'use strict';
/*
 * fake_llm.js — offline OpenAI-compatible fake provider for tests (loopback only).
 *
 * GET  .../models          -> {data:[{id:'fast'},{id:'tooly'}]}
 * POST .../chat/completions -> SSE (or JSON when stream is not requested)
 *
 * Behavior is scripted by markers in the LAST user message:
 *   plain        -> reasoning + chunked text, reply echoes `marker=`
 *   USE_TOOL     -> tool_call read_file README.md, then final text
 *   USE_WRITE    -> tool_call write_file greeting.txt
 *   USE_BASH     -> tool_call bash `echo hello-from-shell`
 *   TODO         -> tool_call todo_write (2 todos)
 *   ASK_Q        -> tool_call ask_user_question
 *   ESCAPE       -> tool_call write_file ../../evil.txt (jail must reject)
 *   FAIL_ME      -> HTTP 500
 * When any role:'tool' message is present -> final text (ends the tool loop).
 */
const http = require('http');

function startFake(opts) {
  opts = opts || {};
  const marker = opts.marker || 'FAKE';
  const delay = opts.delay == null ? 6 : opts.delay;
  const requireKey = opts.requireKey || '';
  const stats = { requests: 0, lastModel: null };

  const server = http.createServer((req, res) => {
    const chunks = [];
    req.on('data', c => chunks.push(c));
    req.on('end', () => {
      const url = req.url.split('?')[0];
      const auth = req.headers.authorization || '';
      if (requireKey && auth !== 'Bearer ' + requireKey) {
        res.writeHead(401, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: { message: 'invalid api key' } }));
        return;
      }
      if (req.method === 'GET' && /\/models$/.test(url)) {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ object: 'list', data: [{ id: 'fast' }, { id: 'tooly' }] }));
        return;
      }
      if (req.method === 'POST' && /chat\/completions$/.test(url)) {
        let body = {};
        try { body = JSON.parse(Buffer.concat(chunks).toString('utf8')); } catch (e) {}
        stats.requests++;
        stats.lastModel = body.model || null;
        handleChat(body, res, { marker, delay });
        return;
      }
      res.writeHead(404, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ error: { message: 'not found: ' + url } }));
    });
  });

  return new Promise(resolve => {
    server.listen(opts.port || 0, '127.0.0.1', () => {
      const p = server.address().port;
      resolve({
        server, port: p, stats,
        url: 'http://127.0.0.1:' + p + '/v1',
        close: () => new Promise(r => server.close(r))
      });
    });
  });
}

function lastUserText(messages) {
  if (!Array.isArray(messages)) return '';
  for (let i = messages.length - 1; i >= 0; i--) {
    const m = messages[i];
    if (m && m.role === 'user' && typeof m.content === 'string') return m.content;
  }
  return '';
}
function hasToolResult(messages) {
  return Array.isArray(messages) && messages.some(m => m && m.role === 'tool');
}

function handleChat(body, res, cfg) {
  const msgs = body.messages || [];

  // pass 2+: tool results came back -> finish the turn with text
  if (hasToolResult(msgs)) {
    return reply(body, res, cfg, {
      reasoning: 'Tool output received; wrapping up.',
      text: 'TOOLS_DONE marker=' + cfg.marker + ' — the tool ran and I am done.'
    });
  }

  const text = lastUserText(msgs);

  if (/FAIL_ME/.test(text)) {
    res.writeHead(500, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ error: { message: 'provider exploded (as scripted)' } }));
    return;
  }
  if (/USE_WRITE/.test(text)) {
    return reply(body, res, cfg, { toolCall: { id: 'call_w1', name: 'write_file', input: { path: 'greeting.txt', content: 'Hello from the fake AI (marker ' + cfg.marker + ')\n' } } });
  }
  if (/USE_BASH/.test(text)) {
    return reply(body, res, cfg, { toolCall: { id: 'call_b1', name: 'bash', input: { command: 'echo hello-from-shell' } } });
  }
  if (/USE_TOOL/.test(text)) {
    return reply(body, res, cfg, { toolCall: { id: 'call_t1', name: 'read_file', input: { path: 'README.md' } } });
  }
  if (/TODO/.test(text)) {
    return reply(body, res, cfg, { toolCall: { id: 'call_d1', name: 'todo_write', input: { todos: [
      { content: 'Step one', status: 'pending', priority: 'high' },
      { content: 'Step two', status: 'in_progress', priority: 'medium' }
    ] } } });
  }
  if (/ASK_Q/.test(text)) {
    return reply(body, res, cfg, { toolCall: { id: 'call_q1', name: 'ask_user_question', input: {
      question: 'Which route should I take?', header: 'Route',
      options: [{ label: 'Option Alpha', value: 'alpha' }, { label: 'Option Beta', value: 'beta' }],
      multiple: false, custom: true, customPlaceholder: 'Type your own answer...'
    } } });
  }
  if (/ESCAPE/.test(text)) {
    return reply(body, res, cfg, { toolCall: { id: 'call_e1', name: 'write_file', input: { path: '../../evil.txt', content: 'escape attempt' } } });
  }

  // default: reasoning + chunked streamed text
  return reply(body, res, cfg, {
    reasoning: 'Thinking... the user said: "' + text.slice(0, 60) + '". A concise reply is best.',
    text: 'Hello from ' + cfg.marker + '. This is a streamed reply.'
  });
}

function reply(body, res, cfg, payload) {
  const model = body.model || 'unknown';
  const usage = {
    prompt_tokens: 42, completion_tokens: 17,
    prompt_tokens_details: { cached_tokens: 7 }
  };

  // ---- non-stream JSON ----
  if (!body.stream) {
    const msg = { role: 'assistant', content: payload.text || null };
    if (payload.reasoning) msg.reasoning_content = payload.reasoning;
    if (payload.toolCall) {
      const tc = payload.toolCall;
      msg.tool_calls = [{ id: tc.id, type: 'function', function: { name: tc.name, arguments: JSON.stringify(tc.input) } }];
    }
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({
      id: 'cmpl-fake', object: 'chat.completion', model,
      choices: [{ index: 0, message: msg, finish_reason: payload.toolCall ? 'tool_calls' : 'stop' }],
      usage
    }));
    return;
  }

  // ---- SSE ----
  res.writeHead(200, {
    'Content-Type': 'text/plain; charset=utf-8',
    'Cache-Control': 'no-cache'
  });
  const lines = [];
  const base = { id: 'cmpl-fake', object: 'chat.completion.chunk', model };
  const push = obj => lines.push('data: ' + JSON.stringify(obj) + '\n');

  if (payload.reasoning) {
    push(Object.assign({}, base, { choices: [{ index: 0, delta: { reasoning_content: payload.reasoning } }] }));
  }
  if (payload.text) {
    const words = payload.text.split(/(?<=\s)/);
    for (const w of words) {
      push(Object.assign({}, base, { choices: [{ index: 0, delta: { content: w } }] }));
    }
  }
  if (payload.toolCall) {
    const tc = payload.toolCall;
    const args = JSON.stringify(tc.input);
    const mid = Math.ceil(args.length / 2);
    push(Object.assign({}, base, { choices: [{ index: 0, delta: { tool_calls: [{ index: 0, id: tc.id, type: 'function', function: { name: tc.name, arguments: '' } }] } }] }));
    push(Object.assign({}, base, { choices: [{ index: 0, delta: { tool_calls: [{ index: 0, function: { arguments: args.slice(0, mid) } }] } }] }));
    push(Object.assign({}, base, { choices: [{ index: 0, delta: { tool_calls: [{ index: 0, function: { arguments: args.slice(mid) } }] } }] }));
    push(Object.assign({}, base, { choices: [{ index: 0, delta: {}, finish_reason: 'tool_calls' }] }));
  } else {
    push(Object.assign({}, base, { choices: [{ index: 0, delta: {}, finish_reason: 'stop' }] }));
  }
  if (body.stream_options && body.stream_options.include_usage) {
    lines.push('data: ' + JSON.stringify({ id: 'cmpl-fake', object: 'chat.completion.chunk', model, choices: [], usage }) + '\n');
  }
  lines.push('data: [DONE]\n');

  let i = 0;
  const timer = setInterval(() => {
    if (res.writableEnded || res.destroyed) { clearInterval(timer); return; }
    if (i >= lines.length) { clearInterval(timer); res.end(); return; }
    res.write(lines[i++]);
  }, Math.max(1, cfg.delay));
  res.on('close', () => clearInterval(timer));
}

module.exports = { startFake };
