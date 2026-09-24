'use strict';
/*
 * contract_test.js — engineer-perspective tests for Phases 2-6 of docs/test_plan.md.
 * Runs offline: fake LLM providers on loopback, temp OC_DATA/OC_HOME.
 * Every test writes its evidence file under unnecessary/evidence/P<N>/ per the plan.
 *
 *   node contract_test.js
 */
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const http = require('http');
const crypto = require('crypto');
const { startFake } = require('./fake_llm');

const ROOT = path.resolve(__dirname, '..', '..');       // .../ui
const UI = path.join(ROOT, 'index.php');
const SERVER = path.join(ROOT, 'server', 'server.js');
const EV = path.join(ROOT, 'unnecessary', 'evidence');
const TMP = '/tmp/oc_contract';
const DATA = path.join(TMP, 'data');
const HOME = path.join(TMP, 'home');

const PORT = 18861;
const TOKEN_PORT = 18863;
const FAKE_A_PORT = 18871;
const FAKE_B_PORT = 18872;
const DEAD_PORT = 18899; // nothing listens here (connection-refused case)
const BASE = 'http://127.0.0.1:' + PORT;
const TOKEN_BASE = 'http://127.0.0.1:' + TOKEN_PORT;
const ACCESS_TOKEN = 'gate-secret-123';
const SECRET_KEY = 'sk-supersecret-TESTKEY-9d4f2';
const KNOWN_MD5 = '895eb75226cc2801834baaced2873bb1';

const EXPECT_EVENTS = [
  'iteration-start', 'reasoning-delta', 'text-delta', 'tool-call', 'tool-result',
  'permission-request', 'todos', 'question', 'title', 'usage', 'done', 'error'
];

/* UI's api() method rule: data -> POST(form) else GET; raw XHR routes always GET. */
const UI_METHOD = {
  status: 'GET', conversations: 'GET', providers: 'GET', settings_load: 'GET',
  project_info: 'GET', projects_list: 'GET', projects_wordpress: 'GET',
  changes: 'GET', // api('changes',null,...)
  conversation: 'GET', file_tree: 'GET', resolve_file: 'GET', // raw XHR
  start: 'POST', new_session: 'POST', switch_conversation: 'POST',
  delete_conversation: 'POST', rename_conversation: 'POST', regenerate: 'POST',
  fork: 'POST', compact: 'POST', abort: 'POST', force_unlock: 'POST',
  read_file: 'POST', diff: 'POST', restore: 'POST', projects_create: 'POST',
  projects_delete: 'POST', projects_close: 'POST', settings_save: 'POST',
  settings_delete: 'POST', test_connection: 'POST', favorite_model: 'POST',
  unfavorite_model: 'POST', toggle_permission: 'POST', shell: 'POST',
  set_mode: 'POST', undo: 'POST', redo: 'POST', clear: 'POST',
  ai_chat_stream: 'POST'
};

const results = [];
function md5(p) { return crypto.createHash('md5').update(fs.readFileSync(p)).digest('hex'); }
function evw(rel, content) {
  const p = path.join(EV, rel);
  fs.mkdirSync(path.dirname(p), { recursive: true });
  fs.writeFileSync(p, content);
  return p;
}
function rec(id, view, title, ok, evidence, note) {
  results.push({ id, view, title, ok: !!ok, evidence: evidence || '', note: note || '' });
  console.log((ok ? '✅ PASS ' : '❌ FAIL ') + id + ' — ' + title + (note ? ' — ' + note : ''));
}
function req(urlStr, opts) {
  opts = opts || {};
  return new Promise((resolve, reject) => {
    const u = new URL(urlStr);
    const r = http.request(u, {
      method: opts.method || 'GET',
      headers: opts.headers || {}
    }, res => {
      const chunks = [];
      res.on('data', c => chunks.push(c));
      res.on('end', () => resolve({
        status: res.statusCode,
        headers: res.headers,
        raw: Buffer.concat(chunks).toString('utf8')
      }));
    });
    r.on('error', reject);
    if (opts.body) r.write(opts.body);
    r.end();
  });
}
function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

function bootServer(env, port) {
  const child = spawn(process.execPath, [SERVER], {
    env: Object.assign({}, process.env, env, { PORT: String(port) }),
    cwd: path.dirname(SERVER),
    stdio: ['ignore', 'pipe', 'pipe']
  });
  child.stdout.on('data', () => {});
  child.stderr.on('data', d => process.stderr.write('[srv:' + port + '] ' + d));
  return child;
}
async function waitReady(base, tries) {
  for (let i = 0; i < (tries || 60); i++) {
    try {
      const r = await req(base + '/healthz');
      if (r.status === 200) return true;
    } catch (e) {}
    await sleep(250);
  }
  throw new Error('server at ' + base + ' did not become ready');
}
function stopServer(child) {
  return new Promise(resolve => {
    if (!child || child.exitCode != null) return resolve();
    child.once('exit', () => resolve());
    child.kill('SIGTERM');
    setTimeout(() => { try { child.kill('SIGKILL'); } catch (e) {} resolve(); }, 3000);
  });
}

(async function main() {
  const t0 = Date.now();
  fs.rmSync(TMP, { recursive: true, force: true });
  fs.mkdirSync(DATA, { recursive: true });
  fs.mkdirSync(HOME, { recursive: true });

  const md5Before = md5(UI);

  /* ================= boot main server + fakes ================= */
  const srv = bootServer({ OC_DATA: DATA, OC_HOME: HOME, OC_ENV_FILE: '' }, PORT);
  await waitReady(BASE);
  const fakeA = await startFake({ port: FAKE_A_PORT, marker: 'FAKE_A' });
  const fakeB = await startFake({ port: FAKE_B_PORT, marker: 'FAKE_B' });

  /* page render -> cookie + csrf(C) + project id */
  const page = await req(BASE + '/');
  const cookie = (page.headers['set-cookie'] || []).map(c => c.split(';')[0]).join('; ');
  const csrf = (page.raw.match(/C='([a-f0-9]+)'/) || [])[1] || '';
  const PID = (page.raw.match(/project_id=([A-Za-z0-9_.-]+)/) || [])[1] || '';
  if (!csrf || !PID) throw new Error('could not extract csrf/project from rendered page');

  let snapId = null;
  let firstUserMsgId = null;
  let permVerify = false;

  async function api(route, params, method, projectId) {
    params = params || {};
    method = method || UI_METHOD[route] || 'GET';
    const query = {
      act: 'ai', ai_php_api: route, csrf_token: csrf, project_id: projectId || PID
    };
    const headers = { Cookie: cookie };
    let url = BASE + '/index.live.php?' + new URLSearchParams(query).toString();
    if (method === 'POST') {
      headers['Content-Type'] = 'application/x-www-form-urlencoded';
      const r = await req(url, { method: 'POST', headers, body: new URLSearchParams(params).toString() });
      return wrap(r);
    }
    const all = Object.assign({}, query, params);
    url = BASE + '/index.live.php?' + new URLSearchParams(all).toString();
    const r = await req(url, { method: 'GET', headers });
    return wrap(r);
  }
  function wrap(r) {
    let json = null;
    try { json = JSON.parse(r.raw); } catch (e) {}
    return { status: r.status, json, raw: r.raw, ct: r.headers['content-type'] || '' };
  }
  async function stream(params) {
    const body = new URLSearchParams(Object.assign(
      { ai_chat_stream: '1', csrf_token: csrf, project_id: PID }, params)).toString();
    const r = await req(BASE + '/index.live.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', Cookie: cookie },
      body
    });
    return r;
  }
  function parseSSE(raw) {
    const lines = raw.split('\n').filter(l => l.length);
    const events = [];
    const bad = [];
    for (const line of lines) {
      if (line.indexOf('data: ') !== 0) { bad.push('PREFIX: ' + line.slice(0, 100)); continue; }
      try { events.push(JSON.parse(line.slice(6))); }
      catch (e) { bad.push('JSON_FAIL: ' + line.slice(0, 100)); }
    }
    return { events, bad, prefixOk: bad.length === 0, lineCount: lines.length };
  }
  async function runScenario(name, content, convId) {
    const r = await stream({ content, conversation_id: convId });
    const p = parseSSE(r.raw);
    evw('P3/E3.1_stream_' + name + '.raw.txt', r.raw);
    return { name, status: r.status, ct: r.headers['content-type'] || '', convId, raw: r.raw, ...p };
  }

  /* ================= provider setup (feeds E6.1) ================= */
  const modelsJSON = JSON.stringify({
    fast: { name: 'Fast', context_window: 65536 },
    tooly: { name: 'Tooly', context_window: 32768 }
  });
  const saveA = await api('settings_save', {
    provider_id: 'custom', name: 'FakeA', api_key: SECRET_KEY,
    base_url: fakeA.url, models: modelsJSON, model: 'fast'
  });
  const saveB = await api('settings_save', {
    provider_id: 'custom', name: 'FakeB', api_key: SECRET_KEY,
    base_url: fakeB.url, models: modelsJSON, model: 'fast'
  });
  const tcA = await api('test_connection', { provider_id: 'custom:fakea', api_key: SECRET_KEY, base_url: fakeA.url });
  const tcB = await api('test_connection', { provider_id: 'custom:fakeb', api_key: SECRET_KEY, base_url: fakeB.url });
  const tcDead = await api('test_connection', { provider_id: 'custom:fakea', api_key: SECRET_KEY, base_url: 'http://127.0.0.1:' + DEAD_PORT + '/v1' });
  await api('start', { provider: 'custom:fakea', model: 'fast', mode: 'build' });

  /* conversation fixtures: convMain (2 warmups -> messages) + convThrow */
  const n1 = await api('new_session', {});
  const convMain = n1.json && n1.json.id;
  const scenarios = [];
  scenarios.push(await runScenario('S1_warmup', 'Say hello to the contract', convMain));      // E3 + messages
  scenarios.push(await runScenario('S1b_warmup2', 'A second warmup message', convMain));      // 2 user/assistant pairs
  const n2 = await api('new_session', {});
  const convThrow = n2.json && n2.json.id;
  await api('switch_conversation', { conversation_id: convMain }, 'POST');

  /* second project (E5.1) + fixture projects for close/delete rows */
  const mkProj = async (name, p) => (await api('projects_create', { name, path: p, type: 'custom' })).json;
  const projB = await mkProj('ProjB', 'projb');
  const projClose = await mkProj('ProjClose', 'projclose');
  const projDel = await mkProj('ProjDel', 'projdel');
  fs.mkdirSync(path.join(HOME, 'projb'), { recursive: true });
  fs.writeFileSync(path.join(HOME, 'projb', 'PROJ_B_ONLY.txt'), 'Only project B has this file.\n');

  /* ============ PHASE 3 — stream scenarios (E3.1/E3.2) ============ */
  const S2 = 'c_' + crypto.randomBytes(4).toString('hex');
  const nS2 = await api('new_session', {}); const convS2 = nS2.json.id;
  scenarios.push(await runScenario('S2_tool', 'USE_TOOL read the readme', convS2));
  await api('switch_conversation', { conversation_id: convMain }, 'POST');

  const nS3 = await api('new_session', {}); const convS3 = nS3.json.id;
  scenarios.push(await runScenario('S3_write', 'USE_WRITE create greeting', convS3));
  await api('switch_conversation', { conversation_id: convMain }, 'POST');

  const nS4 = await api('new_session', {}); const convS4 = nS4.json.id;
  scenarios.push(await runScenario('S4_todos', 'TODO update the plan', convS4));
  await api('switch_conversation', { conversation_id: convMain }, 'POST');

  const nS5 = await api('new_session', {}); const convS5 = nS5.json.id;
  scenarios.push(await runScenario('S5_question', 'ASK_Q which route', convS5));
  await api('switch_conversation', { conversation_id: convMain }, 'POST');

  const nS6 = await api('new_session', {}); const convS6 = nS6.json.id;
  scenarios.push(await runScenario('S6_fail', 'FAIL_ME now', convS6));
  await api('switch_conversation', { conversation_id: convMain }, 'POST');

  /* --- E3.1: 12/12 events, data: prefix, parseable --- */
  const union = new Set();
  let allPrefixOk = true;
  const perScenario = [];
  for (const s of scenarios) {
    const names = s.events.map(e => e._event);
    names.forEach(n => union.add(n));
    if (!s.prefixOk) allPrefixOk = false;
    perScenario.push({
      scenario: s.name, status: s.status, contentType: s.ct, lines: s.lineCount,
      prefix_ok: s.prefixOk, bad_lines: s.bad,
      events: s.events.map((e, i) => ({ i, event: e._event, keys: Object.keys(e) }))
    });
  }
  const missing = EXPECT_EVENTS.filter(e => !union.has(e));
  evw('P3/E3.1_events.json', JSON.stringify({
    expected_events: EXPECT_EVENTS,
    observed_union: [...union].sort(),
    missing,
    all_lines_data_prefix: allPrefixOk,
    total_events: scenarios.reduce((a, s) => a + s.events.length, 0),
    scenarios: perScenario
  }, null, 2));
  rec('E3.1', 'Engineer', '১২/১২ ইভেন্টের নাম ও `data: ` প্রিফিক্স সঠিক',
    EXPECT_EVENTS.length === 12 && missing.length === 0 && allPrefixOk,
    'unnecessary/evidence/P3/E3.1_events.json',
    'observed union=' + [...union].sort().join(',') + '; missing=' + (missing.join(',') || 'none'));

  /* --- E3.2: conversation_id on every event --- */
  const e32lines = [];
  let e32ok = true;
  for (const s of scenarios) {
    const rows = s.events.map((e, i) => {
      const ok = typeof e.conversation_id === 'string' && e.conversation_id.length > 0 && e.conversation_id === s.convId;
      if (!ok) e32ok = false;
      return '  line#' + i + ' event=' + e._event + ' conversation_id=' + JSON.stringify(e.conversation_id) + ' expected=' + s.convId + ' => ' + (ok ? 'OK' : 'MISMATCH');
    });
    e32lines.push('scenario ' + s.name + ' (expected conv ' + s.convId + ', ' + s.events.length + ' events):');
    e32lines.push(...rows, '');
  }
  e32lines.push('TOTAL events checked: ' + scenarios.reduce((a, s) => a + s.events.length, 0));
  e32lines.push('RESULT: ' + (e32ok ? 'every event carried the correct conversation_id' : 'MISMATCHES FOUND'));
  evw('P3/E3.2_conv_ids.txt', e32lines.join('\n'));
  rec('E3.2', 'Engineer', 'প্রতিটি ইভেন্টে `conversation_id` (সেশন আইসোলেশন)',
    e32ok, 'unnecessary/evidence/P3/E3.2_conv_ids.txt');

  /* event-level detail assertions (part of E3.1 evidence, kept honest) */
  const evOf = n => scenarios.flatMap(s => s.events).filter(e => e._event === n);
  const details = {
    title: (evOf('title')[0] || {}).title || null,
    done_auto_title: (scenarios[0].events.find(e => e._event === 'done') || {}).auto_title === true,
    s5_pending_question: ((scenarios[5].events.find(e => e._event === 'done') || {}).pending_question) === true,
    s6_error_message: ((scenarios[6].events.find(e => e._event === 'error') || {}).message) || '',
    usage_sample: evOf('usage')[0] || null,
    question_sample: evOf('question')[0] || null,
    todos_count: (evOf('todos')[0] || { todos: [] }).todos.length,
    permission_tool: (evOf('permission-request')[0] || {}).tool || null,
    tool_results: evOf('tool-result').map(t => ({ name: t.name, is_error: !!t.is_error, has_diff: !!t.diff }))
  };
  evw('P3/E3.1_event_details.json', JSON.stringify(details, null, 2));

  /* ============ PHASE 2 — contract matrix (E2.1/E2.2/E2.3) ============ */
  const rows = [];
  const M = [
    ['status', {}, 'GET'],
    ['start', { provider: 'custom:fakea', model: 'fast', mode: 'build' }, 'POST'],
    ['conversations', {}, 'GET'],
    ['new_session', {}, 'POST'],
    ['conversation', () => ({ conversation_id: convMain }), 'GET'],
    ['switch_conversation', () => ({ conversation_id: convMain }), 'POST'],
    ['rename_conversation', { conversation_id: () => convMain, title: 'Renamed session' }, 'POST'],
    ['delete_conversation', { conversation_id: () => convThrow }, 'POST'],
    ['regenerate', {}, 'POST'],
    ['fork', () => ({ message_id: firstUserMsgId }), 'POST'],
    ['compact', { keep_last_n: 3 }, 'POST'],
    ['compact', { keep_last_n: 1 }, 'POST'],
    ['undo', () => ({ message_id: firstUserMsgId }), 'POST'],
    ['redo', {}, 'POST'],
    ['abort', {}, 'POST'],
    ['file_tree', { path: '.' }, 'GET'],
    ['read_file', { path: 'README.md' }, 'POST'],
    ['resolve_file', { path: 'README.md' }, 'GET'],
    ['changes', {}, 'GET'],
    ['diff', () => ({ id: snapId }), 'POST'],
    ['restore', () => ({ id: snapId }), 'POST'],
    ['project_info', {}, 'GET'],
    ['projects_list', {}, 'GET'],
    ['projects_create', { name: 'MatrixTmp', path: 'matrixtmp' }, 'POST'],
    ['projects_close', { project_id: () => projClose.project_id }, 'POST'],
    ['projects_delete', { project_id: () => projDel.project_id }, 'POST'],
    ['projects_wordpress', {}, 'GET'],
    ['providers', {}, 'GET'],
    ['settings_load', {}, 'GET'],
    ['settings_save', { provider_id: 'custom', name: 'TempProbe', base_url: 'http://127.0.0.1:' + DEAD_PORT + '/v1' }, 'POST'],
    ['settings_delete', { provider_id: 'custom:tempprobe' }, 'POST'],
    ['test_connection', { provider_id: 'custom:fakea', api_key: SECRET_KEY, base_url: () => fakeA.url }, 'POST'],
    ['favorite_model', { model_id: 'custom:fakea/fast' }, 'POST'],
    ['unfavorite_model', { model_id: 'custom:fakea/fast' }, 'POST'],
    ['toggle_permission', { permission: 'bash', value: 'allow_always' }, 'POST'],
    ['shell', { command: 'echo contract-shell-ok' }, 'POST'],
    ['set_mode', { mode: 'plan' }, 'POST'],
    ['clear', {}, 'POST'],
    ['force_unlock', {}, 'POST']
  ];

  function resolveParams(p) {
    if (typeof p === 'function') return p();
    const out = {};
    for (const [k, v] of Object.entries(p)) out[k] = typeof v === 'function' ? v() : v;
    return out;
  }

  const postHook = {
    new_session: async () => { await api('switch_conversation', { conversation_id: convMain }, 'POST'); },
    conversation: (row) => {
      const msgs = (row.json && row.json.messages) || [];
      const u = msgs.find(m => m.role === 'user');
      firstUserMsgId = u && u.id;
    },
    fork: async (row) => {
      const fid = row.json && row.json.conversation_id;
      await api('switch_conversation', { conversation_id: convMain }, 'POST');
      if (fid) await api('delete_conversation', { conversation_id: fid }, 'POST');
    },
    changes: (row) => { snapId = ((row.json && row.json.snapshots) || [])[0] ? row.json.snapshots[0].id : null; },
    toggle_permission: async () => {
      const sl = await api('settings_load', {}, 'GET');
      permVerify = !!(sl.json && sl.json.permissions && sl.json.permissions.bash === 'allow_always');
      await api('toggle_permission', { permission: 'bash' }, 'POST');
    },
    set_mode: async () => { await api('set_mode', { mode: 'build' }, 'POST'); }
  };

  for (const [route, params, method] of M) {
    const p = resolveParams(params);
    const r = await api(route, p, method);
    rows.push({
      route, method, params: p, status: r.status,
      content_type: r.ct, json: r.json,
      json_ok: r.ct.indexOf('application/json') === 0 && r.json !== null,
      body: r.raw.slice(0, 1200)
    });
    if (postHook[route]) await postHook[route]({ json: r.json, raw: r.raw });
  }

  /* ai_chat_stream (39th route) — SSE, not JSON */
  const streamRow = await stream({ content: 'matrix route check' });
  const sse = parseSSE(streamRow.raw);
  const streamDone = sse.events.find(e => e._event === 'done');
    rows.push({
    route: 'ai_chat_stream', method: 'POST', params: { content: 'matrix route check' },
    status: streamRow.status, content_type: streamRow.headers['content-type'] || '',
    json: null,
    json_ok: false, sse_protocol_ok: streamRow.status === 200 && sse.prefixOk && !!streamDone,
    body: streamRow.raw.slice(0, 800)
  });

  /* --- E2.1: 39/39 respond 200 (+JSON for JSON routes, SSE for stream) --- */
  const distinct = new Set(rows.map(r => r.route));
  const bad200 = rows.filter(r => r.status !== 200);
  const badJson = rows.filter(r => r.route !== 'ai_chat_stream' && !r.json_ok);
  const e21 = {
    expected_routes: 39,
    distinct_routes_seen: distinct.size,
    rows: rows.map(r => ({
      route: r.route, method: r.method, status: r.status,
      content_type: r.content_type, json_ok: r.json_ok, sse_protocol_ok: r.sse_protocol_ok,
      body_sample: r.body
    })),
    all_200: bad200.length === 0,
    all_json: badJson.length === 0,
    failures: { not_200: bad200.map(r => r.route), not_json: badJson.map(r => r.route) }
  };
  evw('P2/E2.1_routes.json', JSON.stringify(e21, null, 2));
  rec('E2.1', 'Engineer', '৩৯/৩৯ রাউট উত্তর দেয়, HTTP 200 + JSON',
    distinct.size === 39 && bad200.length === 0 && badJson.length === 0 && rows.length >= 39,
    'unnecessary/evidence/P2/E2.1_routes.json',
    'distinct=' + distinct.size + ', rows=' + rows.length + ', not200=' + (bad200.map(r => r.route).join(',') || 'none'));

  /* --- E2.2: required-field schema per route --- */
  const convListResp = await api('conversations', {}, 'GET');   // post-matrix verification
  const providersResp = await api('providers', {}, 'GET');
  const has = (o, k) => !!o && Object.prototype.hasOwnProperty.call(o, k);

  function schemaChecks(route, row) {
    const j = row.json;
    const c = [];
    const A = (desc, cond) => c.push([desc, !!cond]);
    switch (route) {
      case 'start': A('success===true', j && j.success === true); A('session object', j && !!j.session); break;
      case 'status': {
        A('session object', j && !!j.session);
        if (j && j.session) {
          A('session.provider', has(j.session, 'provider'));
          A('session.model', has(j.session, 'model'));
          A('session.mode', has(j.session, 'mode'));
          A('session.active_conversation', has(j.session, 'active_conversation'));
          A('session.provider===custom:fakea', j.session.provider === 'custom:fakea');
          A('session.model===fast', j.session.model === 'fast');
        }
        A('providers[]', j && Array.isArray(j.providers));
        A('providers[0]{id,name,connected,models}', j && j.providers[0] && has(j.providers[0], 'id') && has(j.providers[0], 'name') && has(j.providers[0], 'connected') && has(j.providers[0], 'models'));
        A('custom:fakea connected===true', j && (j.providers || []).some(p => p.id === 'custom:fakea' && p.connected === true));
        A('favorite_models[]', j && Array.isArray(j.favorite_models));
        A('model_info object w/ fast', j && j.model_info && typeof j.model_info === 'object' && !!j.model_info.fast);
        A('lock{locked}', j && j.lock && has(j.lock, 'locked'));
        A('running_conversations[]', j && Array.isArray(j.running_conversations));
        break;
      }
      case 'conversations':
        A('array', Array.isArray(j));
        if (Array.isArray(j) && j[0]) {
          const it = j[0];
          A('item{id,title,updated_at,message_count,running,last_status}',
            has(it, 'id') && has(it, 'title') && has(it, 'updated_at') &&
            has(it, 'message_count') && has(it, 'running') && has(it, 'last_status'));
          A('types (updated_at number, running bool, message_count number)',
            typeof it.updated_at === 'number' && typeof it.running === 'boolean' && typeof it.message_count === 'number');
        }
        /* rename/delete happen later in the matrix -> verify against the fresh post-matrix fetch */
        A('renamed title visible in list (post-matrix)', (convListResp.json || []).some(x => x.title === 'Renamed session'));
        A('deleted conv absent (post-matrix)', !(convListResp.json || []).some(x => x.id === convThrow));
        A('convMain present (post-matrix)', (convListResp.json || []).some(x => x.id === convMain));
        break;
      case 'conversation':
        A('messages[]', j && Array.isArray(j.messages));
        A('todos[]', j && Array.isArray(j.todos));
        A('messages non-empty', j && Array.isArray(j.messages) && j.messages.length > 0);
        A('message{role,content,id}', j && j.messages[0] && has(j.messages[0], 'role') && has(j.messages[0], 'content') && has(j.messages[0], 'id'));
        break;
      case 'switch_conversation': A('success===true', j && j.success === true); break;
      case 'delete_conversation': A('success===true', j && j.success === true); break;
      case 'rename_conversation': A('success===true', j && j.success === true); A('title echoed', j && j.title === 'Renamed session'); break;
      case 'new_session': A('id string', j && typeof j.id === 'string' && j.id.length > 0); break;
      case 'regenerate': A('success===true', j && j.success === true); break;
      case 'fork':
        A('success===true', j && j.success === true);
        A('conversation_id string', j && typeof j.conversation_id === 'string');
        A('forked_content present', j && has(j, 'forked_content'));
        A('title string (fork suffix)', j && typeof j.title === 'string' && j.title.indexOf('(fork)') !== -1);
        break;
      case 'compact':
        A('success===true', j && j.success === true);
        A('messages[]', j && Array.isArray(j.messages));
        A(has(row.params, 'keep_last_n') && row.params.keep_last_n === 3
          ? 'summary present on no-op path (UI: "Nothing to compact")'
          : 'summary ABSENT on real compaction',
          row.params.keep_last_n === 3 ? !!(j && j.summary) : !(j && j.summary));
        break;
      case 'undo':
        A('success===true', j && j.success === true);
        A('messages[]', j && Array.isArray(j.messages));
        break;
      case 'redo':
        A('success===true', j && j.success === true);
        A('messages[]', j && Array.isArray(j.messages));
        A('messages restored (non-empty)', j && Array.isArray(j.messages) && j.messages.length > 0);
        break;
      case 'abort': A('success===true', j && j.success === true); break;
      case 'force_unlock':
        A('success===true', j && j.success === true);
        A('message string (UI failure path reads r.message)', j && typeof j.message === 'string');
        break;
      case 'file_tree':
        A('directories[]', j && Array.isArray(j.directories));
        A('files[]', j && Array.isArray(j.files));
        A('files[].name+path+size', j && j.files[0] && has(j.files[0], 'name') && has(j.files[0], 'path') && has(j.files[0], 'size'));
        A('README.md listed', j && (j.files || []).some(f => f.name === 'README.md'));
        break;
      case 'read_file':
      case 'resolve_file':
        A('content string', j && typeof j.content === 'string');
        A('content has README heading', j && typeof j.content === 'string' && j.content.indexOf('# ') === 0);
        break;
      case 'changes':
        A('snapshots[]', j && Array.isArray(j.snapshots));
        A('snapshot present (from S3 write)', j && Array.isArray(j.snapshots) && j.snapshots.length >= 1);
        A('item{id,message,time}', j && j.snapshots[0] && has(j.snapshots[0], 'id') && has(j.snapshots[0], 'message') && has(j.snapshots[0], 'time'));
        break;
      case 'diff':
        A('diff string non-empty', j && typeof j.diff === 'string' && j.diff.length > 0);
        A('diff mentions greeting.txt', j && typeof j.diff === 'string' && j.diff.indexOf('greeting.txt') !== -1);
        break;
      case 'restore':
        A('restored===true', j && j.restored === true);
        A('diff string', j && typeof j.diff === 'string');
        break;
      case 'project_info':
        A('overview string', j && typeof j.overview === 'string');
        A('overview line1 Project type:', j && /^Project type:/m.test(j.overview));
        A('overview line2 Project path:', j && /^Project path:/m.test(j.overview));
        A('type string', j && typeof j.type === 'string');
        break;
      case 'projects_list':
        A('array', Array.isArray(j));
        A('item{project_id,name,path}', j && j[0] && has(j[0], 'project_id') && has(j[0], 'name') && has(j[0], 'path'));
        A('contains ProjB', Array.isArray(j) && j.some(p => p.name === 'ProjB'));
        break;
      case 'projects_create':
        A('project_id string', j && typeof j.project_id === 'string');
        A('no error field', j && !j.error);
        break;
      case 'projects_close': A('success===true', j && j.success === true); break;
      case 'projects_delete': A('success===true', j && j.success === true); break;
      case 'projects_wordpress':
        A('array', Array.isArray(j));
        if (Array.isArray(j) && j[0]) A('item{insid,name,path}', has(j[0], 'insid') && has(j[0], 'name') && has(j[0], 'path'));
        else A('empty list acceptable (no WP in jail)', true);
        break;
      case 'providers':
        A('array', Array.isArray(j));
        A('item{id,name,connected,models,is_custom_type}', j && j[0] && has(j[0], 'id') && has(j[0], 'name') && has(j[0], 'connected') && has(j[0], 'models') && has(j[0], 'is_custom_type'));
        A('custom:fakea+custom:fakeb present & connected',
          Array.isArray(j) && ['custom:fakea', 'custom:fakeb'].every(id => j.some(p => p.id === id && p.connected === true)));
        A('builtin anthropic present (catalog)', Array.isArray(j) && j.some(p => p.id === 'anthropic'));
        break;
      case 'settings_load':
        A('favorite_models[]', j && Array.isArray(j.favorite_models));
        A('permissions object', j && j.permissions && typeof j.permissions === 'object');
        break;
      case 'settings_save':
        A('success===true', j && j.success === true);
        A('provider_id derived (custom:tempprobe)', j && j.provider_id === 'custom:tempprobe');
        break;
      case 'settings_delete': A('success===true', j && j.success === true); break;
      case 'test_connection':
        A('success===true', j && j.success === true);
        break;
      case 'favorite_model': A('success===true', j && j.success === true); break;
      case 'unfavorite_model': A('success===true', j && j.success === true); break;
      case 'toggle_permission': A('success===true', j && j.success === true); break;
      case 'shell':
        A('output string', j && typeof j.output === 'string');
        A('output contains contract-shell-ok', j && typeof j.output === 'string' && j.output.indexOf('contract-shell-ok') !== -1);
        A('returncode===0', j && j.returncode === 0);
        break;
      case 'set_mode': A('success===true', j && j.success === true); A('mode echoed', j && (j.mode === 'plan' || j.mode === 'build')); break;
      case 'clear': A('success===true', j && j.success === true); A('messages[] empty', j && Array.isArray(j.messages) && j.messages.length === 0); break;
      case 'ai_chat_stream':
        A('HTTP 200 + text/event stream CT', row.status === 200 && row.content_type.indexOf('text/plain') === 0);
        A('SSE lines all `data: ` + JSON', !!row.sse_protocol_ok);
        break;
      default: A('route has schema entry', false);
    }
    return c;
  }

  const md = ['# E2.2 — Required-field schema report', '',
    'সব রাউটের UI-পড়া বাধ্যতামূলক ফিল্ড যাচাই (সোর্স: `unnecessary/evidence/P1/callsites/*`)।', '',
    '| Route | Checks | Failed | Result |', '|-------|--------|--------|--------|'];
  let allSchemaOk = true;
  const schemaDetail = [];
  for (const row of rows) {
    const checks = schemaChecks(row.route, row);
    const failed = checks.filter(c => !c[1]);
    if (failed.length) allSchemaOk = false;
    md.push('| `' + row.route + '`' + (row.params && row.params.keep_last_n ? '(keep=' + row.params.keep_last_n + ')' : '') +
      ' | ' + checks.length + ' | ' + (failed.map(f => f[0]).join('; ') || '—') + ' | ' +
      (failed.length ? '❌' : '✅') + ' |');
    schemaDetail.push({ route: row.route, params: row.params, checks: checks.map(c => ({ desc: c[0], pass: c[1] })) });
  }
  /* post-matrix verifications */
  const favSL = await api('settings_load', {}, 'GET');
  const favHas = Array.isArray(favSL.json && favSL.json.favorite_models) && favSL.json.favorite_models.length > 0;
  md.push('', '## Post-matrix verifications', '',
    '- toggle_permission allow_always verified via settings_load: **' + (permVerify ? '✅' : '❌') + '**',
    '- favorites cleaned after unfavorite: **' + (!favHas ? '✅' : '❌') + '**',
    '- conversations list after renames/deletes checked inside E2.2 rows above.', '');
  if (!permVerify || favHas) allSchemaOk = false;
  md.push('## Result: ' + (allSchemaOk ? '✅ ALL PASS' : '❌ FAILURES PRESENT'), '');
  evw('P2/E2.2_schema_report.md', md.join('\n'));
  evw('P2/E2.2_schema_detail.json', JSON.stringify({ all_pass: allSchemaOk, rows: schemaDetail, post_matrix: { permVerify, favEmptyAfterUnfav: !favHas } }, null, 2));
  rec('E2.2', 'Engineer', 'প্রতিটি রাউটের বাধ্যতামূলক ফিল্ড উপস্থিত (স্কিমা যাচাই)',
    allSchemaOk, 'unnecessary/evidence/P2/E2.2_schema_report.md');

  /* --- E2.3: method rules (data -> POST, else GET; raw XHR GET) --- */
  const methLines = ['E2.3 — GET/POST method rules (mirrors UI api(): data ? POST form : GET)', '',
    'route | UI method | server status | content-type | note'];
  let methOk = true;
  for (const row of rows) {
    const expect = UI_METHOD[row.route];
    const okMethod = row.method === expect;
    if (!okMethod || row.status !== 200) methOk = false;
    methLines.push(row.route + ' | ' + row.method + ' | ' + row.status + ' | ' + row.content_type +
      (okMethod ? '' : ' | MISMATCH expected ' + expect));
  }
  /* explicit protocol probes */
  const probes = [];
  async function probe(label, ok, detail) { probes.push({ label, ok, detail }); if (!ok) methOk = false; }

  // GET on a data-less route
  const g1 = await api('status', {}, 'GET');
  await probe('GET works for data-less route (status)', g1.status === 200 && g1.json !== null, 'HTTP ' + g1.status);
  // POST form on data route (as api() does: csrf in query, data in body)
  const p1 = await api('rename_conversation', { conversation_id: convMain, title: 'Renamed session' }, 'POST');
  await probe('POST form-urlencoded for data route (rename_conversation)', p1.status === 200 && p1.json && p1.json.success === true, 'HTTP ' + p1.status);
  // JSON content-type also accepted
  const q = new URLSearchParams({ act: 'ai', ai_php_api: 'status', csrf_token: csrf, project_id: PID }).toString();
  const p2 = await req(BASE + '/index.live.php?' + q, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Cookie: cookie },
    body: JSON.stringify({ extra: 1 })
  });
  await probe('JSON body accepted (defensive)', p2.status === 200, 'HTTP ' + p2.status);
  // missing/wrong csrf -> 403
  const p3 = await req(BASE + '/index.live.php?ai_php_api=status&csrf_token=deadbeef&project_id=' + PID, { headers: { Cookie: cookie } });
  await probe('invalid csrf_token -> HTTP 403', p3.status === 403, 'HTTP ' + p3.status);
  const p4 = await req(BASE + '/index.live.php?ai_php_api=shell&command=id&project_id=' + PID, { headers: { Cookie: cookie } });
  await probe('no csrf -> HTTP 403 (shell)', p4.status === 403, 'HTTP ' + p4.status);
  // GET with data params also served (UI never sends this; leniency documented)
  const p5 = await api('rename_conversation', { conversation_id: convMain, title: 'Renamed session' }, 'GET');
  await probe('GET-with-data also served (lenient, UI uses POST)', p5.status === 200, 'HTTP ' + p5.status);

  methLines.push('', '## Protocol probes');
  for (const pr of probes) methLines.push((pr.ok ? '✅' : '❌') + ' ' + pr.label + ' — ' + pr.detail);
  methLines.push('', '## Result: ' + (methOk ? '✅ ALL PASS' : '❌ FAILURES'));
  evw('P2/E2.3_method_rules.txt', methLines.join('\n'));
  evw('P2/E2.3_probes.json', JSON.stringify(probes, null, 2));
  rec('E2.3', 'Engineer', 'GET/POST নিয়ম মেলে (ডেটা থাকলে POST)',
    methOk, 'unnecessary/evidence/P2/E2.3_method_rules.txt');

  /* ============ PHASE 5 — working dir + jail (E5.1/E5.2) ============ */
  const ftDefault = await api('file_tree', { path: '.' }, 'GET', PID);
  const ftB = await api('file_tree', { path: '.' }, 'GET', projB.project_id);
  const rfB1 = await api('read_file', { path: 'PROJ_B_ONLY.txt' }, 'POST', PID);
  const rfB2 = await api('read_file', { path: 'README.md' }, 'POST', projB.project_id);
  const piB = await api('project_info', {}, 'GET', projB.project_id);
  const shPwd = await api('shell', { command: 'pwd' }, 'POST', PID);
  const shPwdB = await api('shell', { command: 'pwd' }, 'POST', projB.project_id);

  const dFiles = (ftDefault.json && ftDefault.json.files || []).map(f => f.name);
  const bFiles = (ftB.json && ftB.json.files || []).map(f => f.name);
  const e51lines = [
    'E5.1 — per-request working directory (path/project_id drive the env)',
    '',
    'default project file_tree files: ' + JSON.stringify(dFiles),
    'projB      project file_tree files: ' + JSON.stringify(bFiles),
    'read_file PROJ_B_ONLY.txt @ default -> ' + JSON.stringify(rfB1.json),
    'read_file README.md        @ projB  -> ' + JSON.stringify(rfB2.json),
    'project_info @ projB overview first lines: ' + JSON.stringify((piB.json.overview || '').split('\n').slice(0, 2)),
    'shell pwd @ default: ' + JSON.stringify((shPwd.json.output || '').trim()),
    'shell pwd @ projB  : ' + JSON.stringify((shPwdB.json.output || '').trim()),
    ''
  ];
  const e51ok =
    dFiles.includes('README.md') && !dFiles.includes('PROJ_B_ONLY.txt') &&
    bFiles.includes('PROJ_B_ONLY.txt') && !bFiles.includes('README.md') &&
    rfB1.json && rfB1.json.error && !rfB1.json.content &&
    rfB2.json && rfB2.json.error &&
    (shPwd.json.output || '').trim() === path.join(HOME, 'default') &&
    (shPwdB.json.output || '').trim() === path.join(HOME, 'projb') &&
    (piB.json.overview || '').indexOf(path.join(HOME, 'projb')) !== -1;
  e51lines.push('RESULT: ' + (e51ok ? '✅ each project_id/path gets its own working dir' : '❌ MISMATCH'));
  evw('P5/E5.1_workdir.txt', e51lines.join('\n'));
  rec('E5.1', 'Engineer', 'path/project_id বদলালে file_tree ওই ডিরেক্টরি দেখায়',
    e51ok, 'unnecessary/evidence/P5/E5.1_workdir.txt');

  /* E5.2 traversal */
  const trav = [];
  async function tcase(label, r, expect) {
    const row = { label, status: r.status, body: r.raw.slice(0, 300) };
    row.ok = expect === '403' ? r.status === 403
      : expect === 'error200' ? (r.status === 200 && r.json && typeof r.json.error === 'string')
        : false;
    trav.push(row);
    return row;
  }
  await tcase('file_tree path=../../etc', await api('file_tree', { path: '../../etc' }, 'GET'), '403');
  await tcase('file_tree path=/etc (absolute outside jail)', await api('file_tree', { path: '/etc' }, 'GET'), '403');
  await tcase('read_file path=../../../etc/passwd', await api('read_file', { path: '../../../etc/passwd' }, 'POST'), '403');
  await tcase('read_file path=/etc/passwd', await api('read_file', { path: '/etc/passwd' }, 'POST'), '403');
  await tcase('resolve_file path=../../etc/hosts', await api('resolve_file', { path: '../../etc/hosts' }, 'GET'), '403');
  await tcase('projects_create path=/etc/evil', await api('projects_create', { name: 'Evil', path: '/etc/evil' }, 'POST'), 'error200');
  await tcase('projects_create path=../../evil (escapes OC_HOME)', await api('projects_create', { name: 'Evil2', path: '../../evil' }, 'POST'), 'error200');

  /* tool-level jail: ESCAPE stream must NOT create the file */
  const nEsc = await api('new_session', {});
  const esc = await runScenario('S7_escape', 'ESCAPE write outside jail', nEsc.json.id);
  const escToolResult = esc.events.find(e => e._event === 'tool-result');
  await api('switch_conversation', { conversation_id: convMain }, 'POST');
  const evil1 = fs.existsSync('/tmp/oc_contract/evil.txt');
  const evil2 = fs.existsSync(path.join(HOME, 'evil.txt'));
  const escOk = !!escToolResult && escToolResult.is_error === true && !evil1 && !evil2;
  trav.push({
    label: 'tool write_file path=../../evil.txt via stream (S7)',
    tool_result: escToolResult ? { name: escToolResult.name, is_error: escToolResult.is_error, output: (escToolResult.output || '').slice(0, 200) } : null,
    file_created_outside: evil1 || evil2,
    ok: escOk
  });

  const travOk = trav.every(t => t.ok);
  evw('P5/E5.2_traversal.txt',
    'E5.2 — path-traversal jail (OC_ROOTS=' + HOME + ')\n\n' +
    trav.map(t => (t.ok ? '✅' : '❌') + ' ' + t.label + '\n   status=' + t.status + ' body=' + t.body).join('\n') +
    '\n\nRESULT: ' + (travOk ? '✅ all traversal attempts blocked' : '❌ ESCAPES FOUND'));
  rec('E5.2', 'Engineer', 'রুটের বাইরে (../) যাওয়া যায় না — path-traversal জেইল',
    travOk, 'unnecessary/evidence/P5/E5.2_traversal.txt',
    trav.filter(t => !t.ok).map(t => t.label).join('; '));

  /* ============ PHASE 6 — providers + security (E6.1/E6.2/E6.3) ============ */
  /* E6.1: switch providers, both stream with distinct markers */
  await api('start', { provider: 'custom:fakeb', model: 'fast', mode: 'build' });
  const sB = await runScenario('S8_providerB', 'Reply with greeting from B', convMain);
  await api('start', { provider: 'custom:fakea', model: 'fast', mode: 'build' });
  const sA = await runScenario('S9_providerA', 'Reply with greeting from A', convMain);

  const e61 = [
    'E6.1 — customer switches between two custom providers (own base_url + api_key)',
    '',
    'FakeA url=' + fakeA.url + ' marker=FAKE_A  test_connection=' + JSON.stringify(tcA.json),
    'FakeB url=' + fakeB.url + ' marker=FAKE_B  test_connection=' + JSON.stringify(tcB.json),
    'Dead  url=http://127.0.0.1:' + DEAD_PORT + '/v1 test_connection=' + JSON.stringify(tcDead.json),
    '',
    'settings_save A -> ' + JSON.stringify(saveA.json),
    'settings_save B -> ' + JSON.stringify(saveB.json),
    '',
    'stream while provider=custom:fakeb: status=' + sB.status + ' contains FAKE_B=' + sB.raw.includes('FAKE_B') + ' contains FAKE_A=' + sB.raw.includes('FAKE_A'),
    'stream while provider=custom:fakea: status=' + sA.status + ' contains FAKE_A=' + sA.raw.includes('FAKE_A') + ' contains FAKE_B=' + sA.raw.includes('FAKE_B'),
    ''
  ];
  const e61ok = tcA.json && tcA.json.success === true &&
    tcB.json && tcB.json.success === true &&
    tcDead.json && typeof tcDead.json.error === 'string' &&
    sB.status === 200 && sB.raw.includes('FAKE_B') && !sB.raw.includes('FAKE_A') &&
    sA.status === 200 && sA.raw.includes('FAKE_A') && !sA.raw.includes('FAKE_B');
  e61.push('RESULT: ' + (e61ok ? '✅ both providers connect + streams route to the selected base_url' : '❌ provider switch failed'));
  evw('P6/E6.1_provider_switch.txt', e61.join('\n'));
  rec('E6.1', 'Engineer', 'দুই আলাদা base_url-এ test_connection + স্ট্রিম চলে',
    e61ok, 'unnecessary/evidence/P6/E6.1_provider_switch.txt');

  /* E6.2: secret never leaves the server */
  const sweep = {};
  const sweepRoutes = ['status', 'providers', 'settings_load', 'conversations', 'projects_list', 'conversation'];
  for (const r of sweepRoutes) {
    const resp = await api(r, r === 'conversation' ? { conversation_id: convMain } : {}, UI_METHOD[r]);
    sweep[r] = resp.raw;
  }
  const saveSweep = await api('settings_save', { provider_id: 'custom', name: 'TempProbe2', base_url: fakeA.url }, 'POST');
  sweep['settings_save'] = saveSweep.raw;
  const tcSweep = await api('test_connection', { provider_id: 'custom:fakea', api_key: SECRET_KEY, base_url: fakeA.url }, 'POST');
  sweep['test_connection'] = tcSweep.raw;
  await api('settings_delete', { provider_id: 'custom:tempprobe2' }, 'POST');
  sweep['page_render'] = page.raw;

  const leakHits = [];
  for (const [k, v] of Object.entries(sweep)) {
    if (/TESTKEY/.test(v)) leakHits.push(k + ':TESTKEY');
    if (/sk-supersecret/.test(v)) leakHits.push(k + ':sk-supersecret');
    /* field-name check applies to JSON API responses only: the rendered page
       legitimately contains `api_key:` — that is how the UI SUBMITS keys.
       The leak criterion is the key VALUE, checked everywhere above/below. */
    if (k !== 'page_render' && /["']?api_key["']?\s*:/.test(v)) leakHits.push(k + ':api_key-field');
    if (/alistiyab/.test(v)) leakHits.push(k + ':old-account-path');
    if (/\/home\/alistiyab/.test(v)) leakHits.push(k + ':old-home-path');
  }
  /* positive control: the key IS stored server-side (0600) */
  const stateRaw = fs.readFileSync(path.join(DATA, 'state.json'), 'utf8');
  const stateMode = (fs.statSync(path.join(DATA, 'state.json')).mode & 0o777).toString(8);
  const stored = stateRaw.includes('TESTKEY');
  const e62 = [
    'E6.2 — secrets never returned to the browser (sweep of every client-visible surface)',
    '',
    'swept surfaces: ' + Object.keys(sweep).join(', '),
    'leak hits: ' + (leakHits.join('; ') || 'NONE'),
    'note: rendered page is scanned for key VALUES (TESTKEY/sk-...) and old paths;',
    '      the `api_key` FIELD NAME may appear in UI source (it is how the UI submits keys).',
    '',
    'rendered page: alistiyab occurrences=' + ((page.raw.match(/alistiyab/g) || []).length) +
      ', old path /home/alistiyab occurrences=' + ((page.raw.match(/\/home\/alistiyab/g) || []).length) +
      ', injected HD=' + HOME + ' present=' + page.raw.includes(HOME),
    'rendered page var A: ' + ((page.raw.match(/var A='[^']*'/) || [''])[0]),
    '',
    'positive control — state.json stores the key server-side: ' + stored + ', mode=' + stateMode + ' (want 600)',
    'providers response for custom:fakea: ' + JSON.stringify(((JSON.parse(sweep.providers) || []).find(p => p.id === 'custom:fakea') || {})),
    '',
    'RESULT: ' + (leakHits.length === 0 && stored && stateMode === '600' ? '✅ no key/path leaks; key stored 0600 server-side' : '❌ LEAK OR STORE-PROBLEM')
  ];
  evw('P6/E6.2_key_leak.txt', e62.join('\n'));
  rec('E6.2', 'Engineer', 'API key/পুরনো পাথ কখনো UI/JS response-এ ফেরত আসে না',
    leakHits.length === 0 && stored && stateMode === '600',
    'unnecessary/evidence/P6/E6.2_key_leak.txt',
    'leaks=' + (leakHits.join(',') || 'none'));

  /* E6.3: shell blocked without login/token */
  const tokSrv = bootServer({
    OC_DATA: path.join(TMP, 'data_tok'), OC_HOME: path.join(TMP, 'home_tok'),
    OC_ACCESS_TOKEN: ACCESS_TOKEN, OC_ENV_FILE: ''
  }, TOKEN_PORT);
  await waitReady(TOKEN_BASE);
  const l63 = [];
  let l63ok = true;
  const L = (label, ok, detail) => { l63ok = l63ok && ok; l63.push((ok ? '✅' : '❌') + ' ' + label + ' — ' + detail); };

  const g1t = await req(TOKEN_BASE + '/');
  L('GET / without token serves login page (not the UI)',
    g1t.status === 200 && g1t.raw.includes('Access token') && !g1t.raw.includes("var A='"),
    'HTTP ' + g1t.status + ', has login form=' + g1t.raw.includes('Access token') + ', has UI=' + g1t.raw.includes("var A='"));

  const shNoTok = await req(TOKEN_BASE + '/index.live.php?ai_php_api=shell&command=id&csrf_token=fake&project_id=default',
    { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: '' });
  L('shell without login -> HTTP 403', shNoTok.status === 403, 'HTTP ' + shNoTok.status + ' ' + shNoTok.raw.slice(0, 120));

  const badLogin = await req(TOKEN_BASE + '/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ token: 'wrong-token', next: '/' }).toString()
  });
  L('POST /login wrong token -> re-serves login with error',
    badLogin.status === 200 && badLogin.raw.includes('Invalid token'), 'HTTP ' + badLogin.status);

  const goodLogin = await req(TOKEN_BASE + '/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ token: ACCESS_TOKEN, next: '/' }).toString()
  });
  const tokCookie = (goodLogin.headers['set-cookie'] || []).map(c => c.split(';')[0]).join('; ');
  L('POST /login correct token -> 302 + session cookie',
    goodLogin.status === 302 && tokCookie.includes('oc_sid='), 'HTTP ' + goodLogin.status + ' cookie=' + (tokCookie.slice(0, 24) + '…'));

  const pageT = await req(TOKEN_BASE + '/', { headers: { Cookie: tokCookie } });
  const tokCsrf = (pageT.raw.match(/C='([a-f0-9]+)'/) || [])[1] || '';
  const tokPid = (pageT.raw.match(/project_id=([A-Za-z0-9_.-]+)/) || [])[1] || '';
  L('GET / with session cookie serves the real UI + CSRF',
    pageT.status === 200 && !!tokCsrf, 'HTTP ' + pageT.status + ', csrf=' + (tokCsrf ? 'yes' : 'no'));

  const shOk = await req(TOKEN_BASE + '/index.live.php?' +
    new URLSearchParams({ ai_php_api: 'shell', csrf_token: tokCsrf, project_id: tokPid }).toString(), {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', Cookie: tokCookie },
    body: new URLSearchParams({ command: 'echo guarded-ok' }).toString()
  });
  L('shell WITH login works (output present)',
    shOk.status === 200 && shOk.raw.includes('guarded-ok'), 'HTTP ' + shOk.status + ' ' + shOk.raw.slice(0, 160));

  l63.push('', 'RESULT: ' + (l63ok ? '✅ shell is unreachable without the access token' : '❌ AUTH HOLE'));
  evw('P6/E6.3_shell_auth.txt', 'E6.3 — shell route behind OC_ACCESS_TOKEN login gate\n\n' + l63.join('\n'));
  rec('E6.3', 'Engineer', 'shell রাউট লগইন/টোকেন ছাড়া ব্লকড',
    l63ok, 'unnecessary/evidence/P6/E6.3_shell_auth.txt');

  /* ============ PHASE 4 — E4.1 md5 recheck ============ */
  const md5After = md5(UI);
  const stat = fs.statSync(UI);
  const lines = fs.readFileSync(UI, 'utf8').replace(/\n$/, '').split('\n').length;
  const e41ok = md5After === KNOWN_MD5 && md5After === md5Before;
  evw('P4/E4.1_md5_recheck.txt', [
    'E4.1 — UI source integrity AFTER all tests (0 edits)',
    '',
    'md5 before tests : ' + md5Before,
    'md5 after  tests : ' + md5After,
    'expected         : ' + KNOWN_MD5,
    'size             : ' + stat.size + ' bytes',
    'lines            : ' + lines,
    '',
    'RESULT: ' + (e41ok ? '✅ byte-identical — 0 edits proven' : '❌ FILE CHANGED')
  ].join('\n'));
  rec('E4.1', 'Engineer', 'UI ফাইল টেস্ট-পরেও md5 একই (০ এডিট প্রমাণ)',
    e41ok, 'unnecessary/evidence/P4/E4.1_md5_recheck.txt', 'md5=' + md5After);

  /* cleanup */
  await stopServer(tokSrv);
  await stopServer(srv);
  await fakeA.close();
  await fakeB.close();

  /* summary */
  const summary = {
    started: new Date(t0).toISOString(),
    duration_ms: Date.now() - t0,
    results,
    pass: results.filter(r => r.ok).length,
    fail: results.filter(r => !r.ok).length
  };
  evw('CONTRACT_RESULTS.json', JSON.stringify(summary, null, 2));
  console.log('\n================ CONTRACT TEST SUMMARY ================');
  for (const r of results) console.log((r.ok ? '✅' : '❌') + ' ' + r.id.padEnd(6) + ' ' + r.title + (r.note ? ' — ' + r.note : ''));
  console.log('TOTAL: ' + summary.pass + '/' + results.length + ' PASS  (' + summary.duration_ms + ' ms)');
  console.log('index.php md5: ' + md5(UI) + (md5(UI) === KNOWN_MD5 ? ' (unchanged)' : ' (CHANGED!)'));
  process.exit(summary.fail ? 1 : 0);
})().catch(err => {
  console.error('FATAL:', err);
  process.exit(2);
});
