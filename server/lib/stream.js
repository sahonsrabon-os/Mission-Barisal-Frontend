'use strict';
/*
 * stream.js — ai_chat_stream engine: 12 SSE events over `data: {json}\\n`.
 * Contract from index.php (line 1945+, handlers 1967-2290):
 *   iteration-start, reasoning-delta, text-delta, tool-call, tool-result,
 *   permission-request, todos, question, title, usage, done, error
 * Every event carries conversation_id (E3.2). `error` uses {message} (UI line
 * 2219), `done` carries usage/context_tokens/tool_calls/iterations/cost for
 * updateStats() plus auto_title / pending_question / error flags.
 */
const store = require('./store');
const providers = require('./providers');
const { TOOLS, TOOL_BY_NAME, buildSystemPrompt, truncHeadTail } = require('./tools');
const { unifiedDiff } = require('./diff');

const running = new Map(); // convId -> AbortController

function isRunning(convId) { return running.has(convId); }
function runningForProject(pid) {
  const ids = [];
  for (const cid of running.keys()) {
    const c = store.getConv(cid);
    if (c && c.project_id === pid) ids.push(cid);
  }
  return ids;
}
function abortConv(convId) {
  const ctrl = running.get(convId);
  if (ctrl) { ctrl.abort(); return true; }
  return false;
}
function abortProject(pid) {
  let n = 0;
  for (const cid of [...running.keys()]) {
    const c = store.getConv(cid);
    if (c && c.project_id === pid) { running.get(cid).abort(); n++; }
  }
  return n;
}

const ID_RE = /^[A-Za-z0-9_.-]{1,80}$/;

function ensureConv(project, q) {
  let conv = null;
  if (q.conversation_id && ID_RE.test(String(q.conversation_id))) {
    conv = store.getConv(String(q.conversation_id));
    if (conv && conv.project_id !== project.project_id) conv = null;
    if (!conv) conv = store.createConvWithId(String(q.conversation_id), project.project_id);
  } else {
    conv = store.getActive(project.project_id);
    if (!conv) conv = store.createConv(project.project_id);
  }
  store.setActive(project.project_id, conv.id);
  return conv;
}

function sessionState(pid) {
  const st = store.settings();
  if (!st.sessions[pid]) st.sessions[pid] = { provider: null, model: null, mode: 'build' };
  return st.sessions[pid];
}

function makeEmitter(res) {
  return obj => {
    if (res.writableEnded || res.destroyed) return false;
    try { res.write('data: ' + JSON.stringify(obj) + '\n'); return true; }
    catch (e) { return false; }
  };
}

function estimateContext(messages) {
  let chars = 0;
  for (const m of messages) {
    chars += String(m.content || '').length;
    for (const p of (m.parts || [])) {
      chars += String(p.text || '').length + JSON.stringify(p.input || {}).length;
    }
  }
  return Math.ceil(chars / 4);
}

function autoTitle(conv) {
  if (conv.title && conv.title !== 'Untitled') return null;
  const firstUser = conv.messages.find(m => m.role === 'user');
  if (!firstUser) return null;
  let t = String(firstUser.content || '').replace(/\\s+/g, ' ').trim();
  if (!t) return null;
  if (t.length > 42) t = t.slice(0, 42).trimEnd() + '…';
  conv.title = t;
  return t;
}

function statsPayload(cid, stats, extra) {
  return Object.assign({
    _event: 'done',
    conversation_id: cid,
    usage: { input_tokens: stats.input, output_tokens: stats.output, cached_tokens: stats.cached },
    context_tokens: stats.ctx,
    tool_calls: stats.toolCalls,
    iterations: stats.iterations,
    cost: stats.cost
  }, extra || {});
}

async function handleStream(ctx) {
  const { req, res, q, project, workdir } = ctx;
  res.writeHead(200, {
    'Content-Type': 'text/plain; charset=utf-8',
    'Cache-Control': 'no-cache, no-transform',
    'Connection': 'keep-alive',
    'X-Accel-Buffering': 'no'
  });
  const emit = makeEmitter(res);
  const content = q.content == null ? '' : String(q.content);

  const conv = ensureConv(project, q);
  const cid = conv.id;
  const ss = sessionState(project.project_id);
  const stats = { input: 0, output: 0, cached: 0, ctx: 0, toolCalls: 0, iterations: 0, cost: 0 };

  const failEarly = (msg) => {
    emit({ _event: 'error', message: msg, conversation_id: cid });
    emit(statsPayload(cid, stats, { error: msg }));
    try { if (!res.writableEnded) res.end(); } catch (e) {}
  };

  if (!content.trim()) return failEarly('Empty message.');
  if (running.has(cid)) return failEarly('A generation is already in progress for this session.');

  const prov = ss.provider ? providers.getProvider(ss.provider) : null;
  const model = ss.model;
  if (!prov || !prov.connected || !model) {
    return failEarly('No AI provider connected. Add or enable one in Settings.');
  }

  const controller = new AbortController();
  running.set(cid, controller);
  const signal = controller.signal;
  res.on('close', () => { if (!res.writableEnded) controller.abort(); });

  conv.last_status = 'running';
  conv.messages.push({
    id: store.msgId(), role: 'user', content, time: store.now(),
    attachments: parseJsonSafe(q.attachments)
  });
  store.touchConv(conv);

  const run = { files: {} };
  const addFileChange = (rel, before, after, tool) => {
    if (!(rel in run.files)) run.files[rel] = { before, after, tool };
    else run.files[rel].after = after;
  };

  const toolCtx = {
    workdir,
    mode: ss.mode || 'build',
    conv,
    addFileChange,
    overview: '',
    onTodos: todos => {
      emit({ _event: 'todos', todos, conversation_id: cid });
      store.touchConv(conv);
    }
  };

  let pendingQuestion = null;
  let finished = false;

  try {
    const isPlan = toolCtx.mode === 'plan';
    let iterations = 0;

    while (!signal.aborted && !finished) {
      iterations++;
      stats.iterations = iterations;
      if (iterations > 12) throw new Error('Stopped after 12 tool iterations.');
      emit({ _event: 'iteration-start', conversation_id: cid, iteration: iterations });

      const llmRes = await providers.llmChat({
        provider: prov,
        model,
        messages: conv.messages,
        systemPrompt: buildSystemPrompt(toolCtx),
        tools: TOOLS,
        signal,
        onText: t => emit({ _event: 'text-delta', text: t, conversation_id: cid }),
        onReasoning: t => emit({ _event: 'reasoning-delta', text: t, conversation_id: cid })
      });

      stats.input += llmRes.usage.input_tokens || 0;
      stats.output += llmRes.usage.output_tokens || 0;
      stats.cached = Math.max(stats.cached, llmRes.usage.cached_tokens || 0);
      stats.ctx = estimateContext(conv.messages);
      emit({
        _event: 'usage', conversation_id: cid,
        usage: { input_tokens: stats.input, output_tokens: stats.output, cached_tokens: stats.cached },
        context_tokens: stats.ctx,
        tool_calls: stats.toolCalls, iterations: stats.iterations, cost: stats.cost
      });

      if (llmRes.toolCalls.length) {
        const parts = [];
        if (llmRes.reasoning) parts.push({ type: 'reasoning', text: llmRes.reasoning });
        if (llmRes.text) parts.push({ type: 'text', text: llmRes.text });
        for (const tc of llmRes.toolCalls) {
          parts.push({ type: 'tool_use', id: tc.id, name: tc.name, input: tc.input });
        }
        conv.messages.push({
          id: store.msgId(), role: 'assistant', content: '', parts, time: store.now(),
          model, provider: ss.provider,
          usage: {
            input_tokens: llmRes.usage.input_tokens,
            output_tokens: llmRes.usage.output_tokens,
            cached_tokens: llmRes.usage.cached_tokens
          }
        });
        store.touchConv(conv);

        for (const tc of llmRes.toolCalls) {
          if (signal.aborted) break;
          const def = TOOL_BY_NAME[tc.name];
          emit({ _event: 'tool-call', id: tc.id, name: tc.name, input: tc.input, conversation_id: cid });

          if (tc.name === 'ask_user_question') {
            pendingQuestion = Object.assign({}, tc.input, { conversation_id: cid });
            // question tool_use is NOT persisted: the answer arrives as the
            // next user message (UI origSend flow).
            conv.messages.pop();
            break;
          }

          let result;
          if (!def) {
            result = { output: 'Unknown tool: ' + tc.name, is_error: true };
          } else if (isPlan && def.mutating) {
            result = { output: 'Blocked: plan mode is read-only. Switch to build mode to make changes.', is_error: true };
          } else {
            const pol = def.perm ? (providers.permissions()[def.perm] || '') : '';
            if (pol === 'deny') {
              result = { output: 'Denied by permission policy (' + def.perm + ').', is_error: true };
            } else {
              if (def.mutating && pol !== 'allow_always') {
                // NOTE: the UI's permission modal has no answer channel back to
                // the server (contract limitation), so the event is emitted for
                // UX + "allow always" persistence, then the tool runs.
                emit({ _event: 'permission-request', tool: def.perm || def.name, input: tc.input, conversation_id: cid });
              }
              result = (await def.exec(tc.input, toolCtx)) || {};
            }
          }

          stats.toolCalls++;
          const ev = {
            _event: 'tool-result',
            id: tc.id, name: tc.name, input: tc.input,
            output: truncHeadTail(result.output == null ? '' : String(result.output), 200 * 1024),
            is_error: !!result.is_error,
            conversation_id: cid
          };
          if (result.diff) ev.diff = result.diff;
          if (result.error) ev.error = result.error;
          emit(ev);

          conv.messages.push({
            id: store.msgId(), role: 'tool_result',
            tool_call_id: tc.id, name: tc.name,
            content: truncHeadTail(result.output == null ? '' : String(result.output), 200 * 1024),
            is_error: !!result.is_error,
            diff: result.diff || undefined,
            time: store.now()
          });
          store.touchConv(conv);
        }
        if (pendingQuestion) break;
        continue;
      }

      // final text answer
      if (llmRes.text || llmRes.reasoning) {
        const parts = [];
        if (llmRes.reasoning) parts.push({ type: 'reasoning', text: llmRes.reasoning });
        if (llmRes.text) parts.push({ type: 'text', text: llmRes.text });
        conv.messages.push({
          id: store.msgId(), role: 'assistant', content: llmRes.text, parts, time: store.now(),
          model, provider: ss.provider,
          usage: {
            input_tokens: llmRes.usage.input_tokens,
            output_tokens: llmRes.usage.output_tokens,
            cached_tokens: llmRes.usage.cached_tokens
          }
        });
      }
      store.touchConv(conv);
      finished = true;
    }

    const aborted = signal.aborted;
    conv.last_status = aborted ? 'stopped' : 'completed';

    let titleAuto = false;
    if (!pendingQuestion && !aborted) {
      const t = autoTitle(conv);
      if (t) {
        emit({ _event: 'title', title: t, conversation_id: cid });
        titleAuto = true;
      }
    }
    store.touchConv(conv);

    if (pendingQuestion) {
      emit(Object.assign({ _event: 'question', conversation_id: cid }, pendingQuestion));
      emit(statsPayload(cid, stats, { pending_question: true }));
    } else {
      emit(statsPayload(cid, stats, {
        auto_title: titleAuto || undefined,
        error: aborted ? 'Stopped.' : undefined
      }));
    }
  } catch (err) {
    const aborted = signal.aborted || (err && err.name === 'AbortError');
    conv.last_status = aborted ? 'stopped' : 'error';
    store.touchConv(conv);
    if (!aborted) {
      const msg = String((err && err.message) || err);
      emit({ _event: 'error', message: msg, conversation_id: cid });
      emit(statsPayload(cid, stats, { error: msg }));
    } else {
      emit(statsPayload(cid, stats, { error: 'Stopped.' }));
    }
  } finally {
    running.delete(cid);
    if (conv.last_status === 'running') conv.last_status = 'completed';
    saveSnapshot(store, project, run);
    store.saveNow();
    try { if (!res.writableEnded) res.end(); } catch (e) {}
  }
}

function saveSnapshot(store, project, run) {
  const keys = Object.keys(run.files);
  if (!keys.length) return;
  const st = store.state;
  if (!st.snapshots) st.snapshots = {};
  const id = store.rid('snap');
  const files = {};
  for (const [rel, f] of Object.entries(run.files)) files[rel] = { before: f.before, after: f.after };
  const firstTool = run.files[keys[0]].tool || 'edit';
  st.snapshots[id] = {
    id,
    project_id: project.project_id,
    time: store.now(),
    message: firstTool + ': ' + keys[0] + (keys.length > 1 ? ' (+' + (keys.length - 1) + ' more)' : ''),
    files
  };
  const mine = Object.values(st.snapshots)
    .filter(s => s.project_id === project.project_id)
    .sort((a, b) => (b.time || 0) - (a.time || 0));
  for (const old of mine.slice(200)) delete st.snapshots[old.id];
}

function parseJsonSafe(s) {
  if (!s) return undefined;
  try { return JSON.parse(String(s)); } catch (e) { return undefined; }
}

module.exports = {
  handleStream, running, isRunning, runningForProject,
  abortConv, abortProject, sessionState
};
