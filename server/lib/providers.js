'use strict';
/*
 * providers.js — provider catalog, settings merge, test_connection, LLM adapters.
 * Customer brings their own provider: base_url + api_key + models are saved
 * server-side (state.json, mode 0600) and NEVER returned to the browser (E6.2).
 * Node default modules only: global fetch is used for all provider HTTP.
 */
const store = require('./store');
const log = require('./log');

const CATALOG = {
  anthropic: {
    name: 'Anthropic', base: 'https://api.anthropic.com/v1', protocol: 'anthropic', requiresKey: true,
    models: {
      'claude-sonnet-4-20250514': { name: 'Claude Sonnet 4', context_window: 200000 },
      'claude-opus-4-20250514': { name: 'Claude Opus 4', context_window: 200000 },
      'claude-3-7-sonnet-20250219': { name: 'Claude 3.7 Sonnet', context_window: 200000 },
      'claude-3-5-haiku-20241022': { name: 'Claude 3.5 Haiku', context_window: 200000 }
    }
  },
  openai: {
    name: 'OpenAI', base: 'https://api.openai.com/v1', protocol: 'openai', requiresKey: true,
    models: {
      'gpt-4o': { name: 'GPT-4o', context_window: 128000 },
      'gpt-4o-mini': { name: 'GPT-4o mini', context_window: 128000 },
      'gpt-4.1': { name: 'GPT-4.1', context_window: 1047576 },
      'gpt-4.1-mini': { name: 'GPT-4.1 mini', context_window: 1047576 },
      'o3-mini': { name: 'o3-mini', context_window: 200000 }
    }
  },
  google: {
    name: 'Google', base: 'https://generativelanguage.googleapis.com/v1beta/openai',
    protocol: 'openai', requiresKey: true,
    models: {
      'gemini-2.5-pro': { name: 'Gemini 2.5 Pro', context_window: 1048576 },
      'gemini-2.5-flash': { name: 'Gemini 2.5 Flash', context_window: 1048576 },
      'gemini-2.0-flash': { name: 'Gemini 2.0 Flash', context_window: 1048576 }
    }
  },
  deepseek: {
    name: 'DeepSeek', base: 'https://api.deepseek.com/v1', protocol: 'openai', requiresKey: true,
    models: {
      'deepseek-chat': { name: 'DeepSeek Chat', context_window: 65536 },
      'deepseek-reasoner': { name: 'DeepSeek Reasoner', context_window: 65536 }
    }
  },
  ollama: {
    name: 'Ollama', base: 'http://127.0.0.1:11434/v1', protocol: 'openai', requiresKey: false,
    models: {
      'llama3.1': { name: 'Llama 3.1', context_window: 131072 },
      'qwen2.5-coder': { name: 'Qwen 2.5 Coder', context_window: 32768 },
      'codellama': { name: 'Code Llama', context_window: 16384 }
    }
  },
  opencode_zen: {
    name: 'OpenCode Zen', base: 'https://opencode.ai/zen/v1', protocol: 'openai', requiresKey: true,
    models: {
      'deepseek-v4-flash-free': { name: 'DeepSeek V4 Flash (free)', context_window: 131072 },
      'mimo-v2.5-free': { name: 'MiMo V2.5 (free)', context_window: 200000 },
      'minimax-m3-free': { name: 'MiniMax M3 (free)', context_window: 204800 },
      'minimax-m2.5-free': { name: 'MiniMax M2.5 (free)', context_window: 204800 },
      'nemotron-3-ultra-free': { name: 'Nemotron 3 Ultra (free)', context_window: 131072 },
      'north-mini-code-free': { name: 'North Mini Code (free)', context_window: 131072 },
      'qwen3.6-plus-free': { name: 'Qwen 3.6 Plus (free)', context_window: 262144 }
    }
  }
};

const CUSTOM_TYPES = id => id === 'custom' || (id && id.indexOf('custom:') === 0);

function slugName(name) {
  return String(name || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
}

function parseModels(m) {
  if (!m) return {};
  if (typeof m === 'object') return m;
  try {
    const o = JSON.parse(String(m));
    return o && typeof o === 'object' ? o : {};
  } catch (e) { return {}; }
}

function entry(id) {
  return store.settings().providers[id] || null;
}

/* merged view; opts.secrets=true adds api_key (server-side use ONLY) */
function merged(id, opts) {
  opts = opts || {};
  const cat = CATALOG[id] || null;
  const ent = entry(id);
  const custom = CUSTOM_TYPES(id) || (ent && ent.is_custom_type);
  const models = Object.assign({}, (cat && cat.models) || {}, parseModels(ent && ent.models));
  const base = (ent && ent.base_url) || (cat && cat.base) || '';
  const key = (ent && ent.api_key) || '';
  let connected;
  if (custom) connected = !!ent && !!(key || (ent && ent.base_url));
  else if (cat && cat.requiresKey === false) connected = !!ent;
  else connected = !!key;
  const modelKeys = Object.keys(models);
  const out = {
    id,
    name: (ent && ent.name) || (cat && cat.name) || id,
    connected,
    models,
    is_custom_type: !!custom,
    /* UI picks the FIRST connected provider's default_model on load —
       admin can pin the model via env (OC_PROVIDER_<n>_MODEL) or settings */
    default_model: (ent && ent.model) || modelKeys[0] || null
  };
  if (base) out.base_url = base;
  if (opts.secrets) {
    out.api_key = key;
    out.protocol = (cat && cat.protocol) || (ent && ent.protocol) || 'openai';
    out.requiresKey = custom ? false : !!(cat && cat.requiresKey !== false);
    out.base = base;
    out.raw_models = parseModels(ent && ent.models);
  }
  return out;
}

function listProviders(opts) {
  const st = store.settings();
  const ids = new Set([...Object.keys(CATALOG), ...Object.keys(st.providers)]);
  return [...ids].map(id => merged(id, opts));
}

function getProvider(id, opts) {
  if (!id) return null;
  const cat = CATALOG[id];
  const ent = entry(id);
  if (!cat && !ent) return null;
  return merged(id, Object.assign({ secrets: true }, opts));
}

/* Providers as the UI must see them (never secrets). */
function publicProviders() {
  return listProviders({ secrets: false }).map(p => {
    const o = {
      id: p.id, name: p.name, connected: p.connected,
      models: p.models, is_custom_type: p.is_custom_type
    };
    if (p.default_model) o.default_model = p.default_model;
    if (p.base_url) o.base_url = p.base_url;
    return o;
  });
}

/* model_info map for status: {modelId: {context}} (UI keys without provider prefix) */
function modelInfo() {
  const out = {};
  for (const p of listProviders({ secrets: false })) {
    for (const [mid, m] of Object.entries(p.models || {})) {
      const ctxWin = typeof m === 'object' && m ? m.context_window : null;
      if (ctxWin && !out[mid]) out[mid] = { context: ctxWin };
    }
  }
  return out;
}

function saveProviderConfig(q) {
  const st = store.settings();
  let id = String(q.provider_id || '').trim();
  if (!id) return { error: 'provider_id required' };
  const name = q.name != null ? String(q.name).trim() : '';
  if (id === 'custom' && name) id = 'custom:' + slugName(name);
  const prev = st.providers[id] || {};
  const ent = {
    is_custom_type: CUSTOM_TYPES(id) ? true : (prev.is_custom_type || false),
    api_key: q.api_key !== undefined ? String(q.api_key) : (prev.api_key || ''),
    base_url: q.base_url !== undefined ? String(q.base_url).trim() : (prev.base_url || ''),
    name: name || prev.name || '',
    models: q.models !== undefined ? parseModels(q.models) : (prev.models || {}),
    model: q.model || prev.model || ''
  };
  if (CUSTOM_TYPES(id)) ent.is_custom_type = true;
  st.providers[id] = ent;
  store.saveSettings();
  return { success: true, provider_id: id };
}

function deleteProviderConfig(q) {
  const st = store.settings();
  const id = String(q.provider_id || '');
  if (id) delete st.providers[id];
  store.saveSettings();
  return { success: true };
}

function favorites() { return store.settings().favorites.slice(); }
function addFavorite(modelId) {
  const f = store.settings().favorites;
  if (modelId && !f.includes(modelId)) f.push(modelId);
  store.saveSettings();
  return { success: true };
}
function removeFavorite(modelId) {
  const st = store.settings();
  st.favorites = st.favorites.filter(x => x !== modelId);
  store.saveSettings();
  return { success: true };
}
function permissions() { return store.settings().permissions; }
function setPermission(perm, value) {
  const p = store.settings().permissions;
  const v = value == null ? '' : String(value);
  if (v === 'allow_always' || v === 'deny') p[perm] = v;
  else delete p[perm];
  store.saveSettings();
  return { success: true };
}

/* ---------------- URL helpers ---------------- */
function stripSlash(b) { return String(b || '').replace(/\/+$/, ''); }
function urlCandidates(base, tail) {
  const b = stripSlash(base);
  const out = [b + tail];
  if (!/\/v\d+$/.test(b)) out.push(b + '/v1' + tail);
  return [...new Set(out)];
}

/* ---------------- test_connection ---------------- */
async function testConnection(q) {
  const id = String(q.provider_id || '');
  const baseP = getProvider(id) || {};
  const base = stripSlash(q.base_url || baseP.base_url || baseP.base || '');
  const key = q.api_key !== undefined ? String(q.api_key) : (baseP.api_key || '');
  const protocol = baseP.protocol || 'openai';
  if (!base) return { error: 'No base_url configured for provider "' + id + '"' };

  const model = q.model
    || (Object.keys(parseModels(q.models || baseP.models))[0])
    || Object.keys((CATALOG[id] && CATALOG[id].models) || {})[0]
    || 'gpt-4o-mini';

  const headers = { 'Accept': 'application/json' };
  if (protocol === 'anthropic') {
    if (key) headers['x-api-key'] = key;
    headers['anthropic-version'] = '2023-06-01';
  } else if (key) {
    headers['Authorization'] = 'Bearer ' + key;
  }

  let unauthorized = 0;
  let lastHttp = null;
  let netErr = null;

  async function hit(url, opts) {
    try {
      const res = await fetch(url, Object.assign({
        headers, signal: AbortSignal.timeout(15000)
      }, opts || {}));
      return res;
    } catch (e) {
      netErr = e;
      return null;
    }
  }

  // 1) /models listing
  for (const url of urlCandidates(base, '/models')) {
    const res = await hit(url);
    if (!res) continue;
    if (res.ok) return { success: true, endpoint: url };
    if (res.status === 401 || res.status === 403) { unauthorized = res.status; lastHttp = res; continue; }
    if (res.status === 404 || res.status === 405) { lastHttp = res; continue; }
    lastHttp = res;
  }
  // 2) minimal completion
  const chatTail = protocol === 'anthropic' ? '/messages' : '/chat/completions';
  for (const url of urlCandidates(base, chatTail)) {
    let body;
    if (protocol === 'anthropic') {
      body = { model, max_tokens: 8, messages: [{ role: 'user', content: 'ping' }] };
    } else {
      body = { model, messages: [{ role: 'user', content: 'ping' }], max_tokens: 8 };
    }
    const res = await hit(url, {
      method: 'POST',
      headers: Object.assign({ 'Content-Type': 'application/json' }, headers),
      body: JSON.stringify(body)
    });
    if (!res) continue;
    if (res.ok) return { success: true, endpoint: url };
    if (res.status === 401 || res.status === 403) { unauthorized = res.status; lastHttp = res; continue; }
    lastHttp = res;
  }

  if (unauthorized) {
    return { error: 'Unauthorized (HTTP ' + unauthorized + ') — check the API key.' };
  }
  if (lastHttp) {
    let t = '';
    try { t = (await lastText(lastHttp)).slice(0, 240); } catch (e) {}
    return { error: 'HTTP ' + lastHttp.status + (t ? ': ' + t : '') };
  }
  return { error: 'Cannot reach ' + base + (netErr ? ': ' + (netErr.message || netErr) : '') };
}
async function lastText(res) {
  try { return await res.text(); } catch (e) { return ''; }
}

/* ---------------- message conversion ---------------- */
function storedToOpenAI(messages, systemPrompt) {
  const out = [{ role: 'system', content: systemPrompt }];
  for (const m of messages) {
    if (m.role === 'user') {
      out.push({ role: 'user', content: String(m.content || '') });
    } else if (m.role === 'assistant') {
      let text = '';
      const toolCalls = [];
      for (const p of (m.parts || [])) {
        if (p.type === 'text' && p.text) text += p.text;
        if (p.type === 'tool_use') {
          toolCalls.push({
            id: p.id || ('call_' + Math.random().toString(36).slice(2, 10)),
            type: 'function',
            function: { name: p.name, arguments: JSON.stringify(p.input || {}) }
          });
        }
      }
      if (!text && !toolCalls.length && m.content) text = String(m.content);
      if (!text && !toolCalls.length) continue;
      const msg = { role: 'assistant', content: text || null };
      if (toolCalls.length) msg.tool_calls = toolCalls;
      out.push(msg);
    } else if (m.role === 'tool_result') {
      out.push({
        role: 'tool',
        tool_call_id: m.tool_call_id,
        content: String(m.content == null ? '' : m.content)
      });
    }
  }
  return out;
}

function storedToAnthropic(messages, systemPrompt) {
  const turns = [];
  for (const m of messages) {
    if (m.role === 'user') {
      turns.push({ role: 'user', content: [{ type: 'text', text: String(m.content || '') }] });
    } else if (m.role === 'assistant') {
      const blocks = [];
      let text = '';
      for (const p of (m.parts || [])) {
        if (p.type === 'text' && p.text) text += p.text;
        if (p.type === 'tool_use') {
          blocks.push({ type: 'tool_use', id: p.id || ('toolu_' + Math.random().toString(36).slice(2, 10)), name: p.name, input: p.input || {} });
        }
      }
      if (!text && !blocks.length && m.content) text = String(m.content);
      if (text) blocks.unshift({ type: 'text', text });
      if (blocks.length) turns.push({ role: 'assistant', content: blocks });
    } else if (m.role === 'tool_result') {
      turns.push({
        role: 'user',
        content: [{ type: 'tool_result', tool_use_id: m.tool_call_id, content: String(m.content == null ? '' : m.content) }]
      });
    }
  }
  // merge consecutive same-role turns + drop leading assistant
  const merged2 = [];
  for (const t of turns) {
    const last = merged2[merged2.length - 1];
    if (last && last.role === t.role) last.content = last.content.concat(t.content);
    else merged2.push(t);
  }
  while (merged2.length && merged2[0].role !== 'user') merged2.shift();
  return merged2;
}

function toolsForOpenAI(tools) {
  return tools.map(t => ({
    type: 'function',
    function: { name: t.name, description: t.description, parameters: t.schema }
  }));
}
function toolsForAnthropic(tools) {
  return tools.map(t => ({ name: t.name, description: t.description, input_schema: t.schema }));
}

function parseJsonSafe(s, fallback) {
  try { return JSON.parse(s); } catch (e) { return fallback; }
}

/* ---------------- LLM chat (streaming) ---------------- */
/*
 * cfg: {provider (merged+secrets), model, messages (stored neutral), tools,
 *       signal, onText(t), onReasoning(t)}
 * returns { text, reasoning, toolCalls:[{id,name,input}], usage:{input_tokens,output_tokens,cached_tokens} }
 */
async function llmChat(cfg) {
  const prov = cfg.provider;
  const protocol = prov.protocol || 'openai';
  const t0 = Date.now();
  log.info('llm', 'request provider=' + prov.id + ' model=' + cfg.model +
    ' protocol=' + protocol + ' tools=' + ((cfg.tools && cfg.tools.length) || 0));
  try {
    const out = protocol === 'anthropic' ? await anthropicChat(cfg) : await openaiChat(cfg);
    log.info('llm', 'done provider=' + prov.id + ' model=' + cfg.model +
      ' in=' + (Date.now() - t0) + 'ms text=' + (out.text || '').length + 'c' +
      ' tools=' + (out.toolCalls || []).length +
      ' usage=' + JSON.stringify(out.usage || {}));
    return out;
  } catch (e) {
    log.error('llm', 'failed provider=' + prov.id + ' model=' + cfg.model +
      ' in=' + (Date.now() - t0) + 'ms error=' + (e.message || e));
    throw e;
  }
}

async function safeFetch(url, opts) {
  try {
    return await fetch(url, opts);
  } catch (e) {
    const err = new Error('Cannot reach AI endpoint ' + url + ': ' + (e.message || e));
    err.network = true;
    throw err;
  }
}

async function openaiChat(cfg) {
  const prov = cfg.provider;
  const base = stripSlash(prov.base_url || prov.base);
  const key = prov.api_key || '';
  const headers = { 'Content-Type': 'application/json' };
  if (key) headers['Authorization'] = 'Bearer ' + key;

  const msgs = storedToOpenAI(cfg.messages, cfg.systemPrompt);
  const urls = urlCandidates(base, '/chat/completions');

  /* Parser-compat variant: OpenAI spec says tool arguments are a JSON STRING,
     but some gateways reject it ("Value looks like object, but can't find
     closing '}'" — observed on an Ollama proxy). Spec form is ALWAYS tried
     first; this object-form body is only ever sent AFTER an upstream error,
     so compliant providers (OpenAI/Mission/probes) never see it. */
  const hasStringToolArgs = msgs.some(m => Array.isArray(m.tool_calls) &&
    m.tool_calls.some(tc => tc.function && typeof tc.function.arguments === 'string'));
  const objectArgMsgs = !hasStringToolArgs ? null : msgs.map(m => {
    if (!Array.isArray(m.tool_calls)) return m;
    return Object.assign({}, m, {
      tool_calls: m.tool_calls.map(tc => {
        const f = Object.assign({}, tc.function);
        if (typeof f.arguments === 'string') {
          try { f.arguments = JSON.parse(f.arguments); } catch (e) { /* keep */ }
        }
        return Object.assign({}, tc, { function: f });
      })
    });
  });

  const mkBody = (stream, includeStreamOpts, compatObjectArgs) => {
    const useMsgs = (compatObjectArgs && objectArgMsgs) ? objectArgMsgs : msgs;
    const b = { model: cfg.model, messages: useMsgs };
    if (cfg.tools && cfg.tools.length) b.tools = toolsForOpenAI(cfg.tools);
    if (stream) {
      b.stream = true;
      if (includeStreamOpts) b.stream_options = { include_usage: true };
    } else {
      b.max_tokens = 4096;
    }
    return b;
  };

  let res = null;
  let lastErrBody = '';
  for (let i = 0; i < urls.length; i++) {
    const url = urls[i];
    /* Retry ladder:
       1) transient 429/502/503 — same spec body, once (1200ms backoff)
       2) parser-compat — if it STILL fails (or hard 400/422/500) while the
          history carries tool_calls, resend once with arguments as objects
          (upstream parser quirk; see objectArgMsgs above)                */
    let transient = 0;
    let compat = false;
    for (;;) {
      res = await safeFetch(url, {
        method: 'POST', headers, body: JSON.stringify(mkBody(true, true, compat)),
        signal: anySignal([cfg.signal, AbortSignal.timeout(300000)])
      });
      if ((res.status === 429 || res.status === 502 || res.status === 503) && transient < 1 && !compat) {
        transient++;
        log.warn('llm', 'transient HTTP ' + res.status + ' from ' + url +
          ' — retrying once in 1200ms (' + prov.id + '/' + cfg.model + ')');
        await new Promise(r => setTimeout(r, 1200));
        continue;
      }
      if (!compat && objectArgMsgs &&
          [400, 422, 500, 502, 503].includes(res.status) &&
          (transient >= 1 || [400, 422, 500].includes(res.status))) {
        compat = true;
        log.warn('llm', 'compat: upstream HTTP ' + res.status + ' with tool history —' +
          ' resending tool arguments as objects once (' + prov.id + '/' + cfg.model + ')');
        await new Promise(r => setTimeout(r, 900));
        continue;
      }
      break;
    }
    if (res.status === 404 || res.status === 405) { res = null; continue; }
    if (res.status === 400) {
      lastErrBody = await lastText(res);
      if (/stream_options/i.test(lastErrBody)) {
        res = await safeFetch(url, {
          method: 'POST', headers, body: JSON.stringify(mkBody(true, false)),
          signal: anySignal([cfg.signal, AbortSignal.timeout(300000)])
        });
        lastErrBody = '';
      }
      if (res && !res.ok && /stream/i.test(lastErrBody || '')) {
        // provider dislikes streaming: fall back to a single-shot completion
        const ns = await safeFetch(url, {
          method: 'POST', headers, body: JSON.stringify(mkBody(false, false)),
          signal: anySignal([cfg.signal, AbortSignal.timeout(300000)])
        });
        if (ns.ok) {
          const j = await ns.json();
          return nonStreamResult(j, cfg);
        }
      }
    }
    break;
  }
  if (!res) throw new Error('AI endpoint not found at ' + base + ' (404). Check the base URL.');
  if (!res.ok) {
    const t = (lastErrBody || await lastText(res)).slice(0, 300);
    log.warn('llm', 'upstream HTTP ' + res.status + ' base=' + base + ' model=' + cfg.model +
      ' body=' + t.slice(0, 200));
    throw new Error('AI request failed: HTTP ' + res.status + (t ? ' — ' + t : ''));
  }
  log.info('llm', 'stream connected base=' + base + ' model=' + cfg.model + ' status=200');

  // ---- parse SSE ----
  const out = { text: '', reasoning: '', toolCalls: [], usage: { input_tokens: 0, output_tokens: 0, cached_tokens: 0 } };
  const toolMap = new Map();
  const reader = res.body.getReader();
  const dec = new TextDecoder();
  let buf = '';
  const handleData = (payload) => {
    if (payload === '[DONE]') return;
    const d = parseJsonSafe(payload, null);
    if (!d) return;
    if (d.usage) {
      out.usage.input_tokens = d.usage.prompt_tokens || out.usage.input_tokens;
      out.usage.output_tokens = d.usage.completion_tokens || out.usage.output_tokens;
      out.usage.cached_tokens = (d.usage.prompt_tokens_details && d.usage.prompt_tokens_details.cached_tokens) || out.usage.cached_tokens;
    }
    const delta = d.choices && d.choices[0] && d.choices[0].delta;
    if (!delta) return;
    if (delta.reasoning_content) { out.reasoning += delta.reasoning_content; if (cfg.onReasoning) cfg.onReasoning(delta.reasoning_content); }
    else if (delta.reasoning) { out.reasoning += delta.reasoning; if (cfg.onReasoning) cfg.onReasoning(delta.reasoning); }
    if (delta.content) { out.text += delta.content; if (cfg.onText) cfg.onText(delta.content); }
    if (delta.tool_calls) {
      for (const tc of delta.tool_calls) {
        const idx = tc.index == null ? 0 : tc.index;
        let t = toolMap.get(idx);
        if (!t) { t = { id: '', name: '', args: '' }; toolMap.set(idx, t); }
        if (tc.id) t.id = tc.id;
        if (tc.function && tc.function.name) t.name = tc.function.name;
        if (tc.function && tc.function.arguments) t.args += tc.function.arguments;
      }
    }
  };
  for (;;) {
    const { done, value } = await reader.read();
    if (done) break;
    buf += dec.decode(value, { stream: true });
    let nl;
    while ((nl = buf.indexOf('\n')) >= 0) {
      const line = buf.slice(0, nl).replace(/\r$/, '');
      buf = buf.slice(nl + 1);
      if (line.startsWith('data:')) handleData(line.slice(5).trim());
    }
  }
  if (buf.trim().startsWith('data:')) handleData(buf.trim().slice(5).trim());

  for (const [, t] of toolMap) {
    out.toolCalls.push({
      id: t.id || ('call_' + Math.random().toString(36).slice(2, 10)),
      name: t.name,
      input: parseJsonSafe(t.args, {})
    });
  }
  log.info('llm', 'stream parsed provider=' + prov.id + ' model=' + cfg.model +
    ' text=' + out.text.length + 'c reasoning=' + out.reasoning.length + 'c' +
    ' tool_calls=' + out.toolCalls.length);
  return out;
}

function nonStreamResult(j, cfg) {
  const out = { text: '', reasoning: '', toolCalls: [], usage: { input_tokens: 0, output_tokens: 0, cached_tokens: 0 } };
  const msg = (j.choices && j.choices[0] && j.choices[0].message) || {};
  out.text = msg.content || '';
  out.reasoning = msg.reasoning_content || msg.reasoning || '';
  if (j.usage) {
    out.usage.input_tokens = j.usage.prompt_tokens || 0;
    out.usage.output_tokens = j.usage.completion_tokens || 0;
    out.usage.cached_tokens = (j.usage.prompt_tokens_details && j.usage.prompt_tokens_details.cached_tokens) || 0;
  }
  for (const tc of (msg.tool_calls || [])) {
    out.toolCalls.push({
      id: tc.id || ('call_' + Math.random().toString(36).slice(2, 10)),
      name: tc.function && tc.function.name,
      input: parseJsonSafe((tc.function && tc.function.arguments) || '', {})
    });
  }
  if (out.text && cfg.onText) cfg.onText(out.text);
  if (out.reasoning && cfg.onReasoning) cfg.onReasoning(out.reasoning);
  return out;
}

async function anthropicChat(cfg) {
  const prov = cfg.provider;
  const base = stripSlash(prov.base_url || prov.base);
  const key = prov.api_key || '';
  const headers = { 'Content-Type': 'application/json', 'anthropic-version': '2023-06-01' };
  if (key) headers['x-api-key'] = key;

  const body = {
    model: cfg.model,
    max_tokens: 8192,
    system: cfg.systemPrompt,
    messages: storedToAnthropic(cfg.messages, cfg.systemPrompt),
    stream: true
  };
  if (cfg.tools && cfg.tools.length) body.tools = toolsForAnthropic(cfg.tools);

  let res = await safeFetch(base + '/messages', {
    method: 'POST', headers, body: JSON.stringify(body),
    signal: anySignal([cfg.signal, AbortSignal.timeout(300000)])
  });
  if (res.status === 404) {
    res = await safeFetch(base + '/v1/messages', {
      method: 'POST', headers, body: JSON.stringify(body),
      signal: anySignal([cfg.signal, AbortSignal.timeout(300000)])
    });
  }
  if (!res.ok) {
    const t = (await lastText(res)).slice(0, 300);
    throw new Error('AI request failed: HTTP ' + res.status + (t ? ' — ' + t : ''));
  }

  const out = { text: '', reasoning: '', toolCalls: [], usage: { input_tokens: 0, output_tokens: 0, cached_tokens: 0 } };
  const reader = res.body.getReader();
  const dec = new TextDecoder();
  let buf = '';
  let curBlock = null; // {type, id, name, buf}

  const handleData = (payload) => {
    const d = parseJsonSafe(payload, null);
    if (!d) return;
    if (d.type === 'message_start' && d.message && d.message.usage) {
      out.usage.input_tokens = d.message.usage.input_tokens || 0;
    } else if (d.type === 'content_block_start') {
      const cb = d.content_block || {};
      if (cb.type === 'tool_use') curBlock = { type: 'tool_use', id: cb.id, name: cb.name, buf: '' };
      else if (cb.type === 'thinking') curBlock = { type: 'thinking' };
      else if (cb.type === 'text') curBlock = { type: 'text' };
    } else if (d.type === 'content_block_delta') {
      const dl = d.delta || {};
      if (dl.type === 'text_delta' && dl.text) {
        out.text += dl.text;
        if (cfg.onText) cfg.onText(dl.text);
      } else if (dl.type === 'thinking_delta' && dl.thinking) {
        out.reasoning += dl.thinking;
        if (cfg.onReasoning) cfg.onReasoning(dl.thinking);
      } else if (dl.type === 'input_json_delta' && curBlock && curBlock.type === 'tool_use') {
        curBlock.buf += dl.partial_json || '';
      }
    } else if (d.type === 'content_block_stop') {
      if (curBlock && curBlock.type === 'tool_use') {
        out.toolCalls.push({
          id: curBlock.id || ('toolu_' + Math.random().toString(36).slice(2, 10)),
          name: curBlock.name,
          input: parseJsonSafe(curBlock.buf, {})
        });
      }
      curBlock = null;
    } else if (d.type === 'message_delta' && d.usage) {
      out.usage.output_tokens = d.usage.output_tokens || out.usage.output_tokens;
    } else if (d.type === 'error') {
      throw new Error('AI provider error: ' + ((d.error && d.error.message) || 'unknown'));
    }
  };

  for (;;) {
    const { done, value } = await reader.read();
    if (done) break;
    buf += dec.decode(value, { stream: true });
    let nl;
    while ((nl = buf.indexOf('\n')) >= 0) {
      const line = buf.slice(0, nl).replace(/\r$/, '');
      buf = buf.slice(nl + 1);
      if (line.startsWith('data:')) handleData(line.slice(5).trim());
    }
  }
  if (buf.trim().startsWith('data:')) handleData(buf.trim().slice(5).trim());
  return out;
}

function anySignal(list) {
  const arr = list.filter(Boolean);
  if (arr.length === 1) return arr[0];
  if (typeof AbortSignal.any === 'function') return AbortSignal.any(arr);
  // manual fallback
  const ctrl = new AbortController();
  for (const s of arr) {
    if (s.aborted) { ctrl.abort(s.reason); break; }
    s.addEventListener('abort', () => ctrl.abort(s.reason), { once: true });
  }
  return ctrl.signal;
}

module.exports = {
  CATALOG, listProviders, getProvider, publicProviders, modelInfo,
  saveProviderConfig, deleteProviderConfig,
  favorites, addFavorite, removeFavorite, permissions, setPermission,
  testConnection, llmChat, CUSTOM_TYPES, slugName
};
