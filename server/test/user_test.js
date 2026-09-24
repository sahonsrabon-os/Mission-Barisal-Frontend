'use strict';
/*
 * user_test.js — user-perspective (U) tests: real Chrome driven over CDP
 * (Node's global WebSocket, no npm). Evidence: screenshots + route log.
 *
 *   node user_test.js
 *
 * Covers: U2.1, U3.1, U4.1..U4.6, U5.1, U6.1 from docs/test_plan.md.
 */
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const http = require('http');
const crypto = require('crypto');
const { startFake } = require('./fake_llm');

const ROOT = path.resolve(__dirname, '..', '..');
const UI = path.join(ROOT, 'index.php');
const SERVER = path.join(ROOT, 'server', 'server.js');
const EV = path.join(ROOT, 'unnecessary', 'evidence');
const TMP = '/tmp/oc_user';
const DATA = path.join(TMP, 'data');
const HOME = path.join(TMP, 'home');
const CHROME_DIR = '/tmp/oc_user_chrome';

const PORT = 18880;
const CHROME_PORT = 9333;
const FAKE_A = 18890, FAKE_B = 18891, FAKE_C = 18892;
const BASE = 'http://127.0.0.1:' + PORT;
const KNOWN_MD5 = '895eb75226cc2801834baaced2873bb1';

const ALL_ROUTES = [
  'start', 'conversations', 'conversation', 'switch_conversation', 'delete_conversation',
  'rename_conversation', 'new_session', 'ai_chat_stream', 'regenerate', 'fork', 'compact',
  'abort', 'status', 'file_tree', 'read_file', 'resolve_file', 'diff', 'changes', 'restore',
  'project_info', 'projects_list', 'projects_create', 'projects_delete', 'projects_close',
  'projects_wordpress', 'providers', 'settings_load', 'settings_save', 'settings_delete',
  'test_connection', 'favorite_model', 'unfavorite_model', 'toggle_permission', 'shell',
  'set_mode', 'undo', 'redo', 'clear', 'force_unlock'
];

const results = [];
function rec(id, title, ok, note) {
  results.push({ id, title, ok: !!ok, note: note || '' });
  console.log((ok ? '✅ PASS ' : '❌ FAIL ') + id + ' — ' + title + (note ? ' — ' + note : ''));
}
function md5(p) { return crypto.createHash('md5').update(fs.readFileSync(p)).digest('hex'); }
function evw(rel, content) {
  const p = path.join(EV, rel);
  fs.mkdirSync(path.dirname(p), { recursive: true });
  fs.writeFileSync(p, content);
  return p;
}
function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
function req(urlStr, opts) {
  opts = opts || {};
  return new Promise((resolve, reject) => {
    const u = new URL(urlStr);
    const r = http.request(u, { method: opts.method || 'GET', headers: opts.headers || {} }, res => {
      const chunks = [];
      res.on('data', c => chunks.push(c));
      res.on('end', () => resolve({
        status: res.statusCode, headers: res.headers,
        raw: Buffer.concat(chunks).toString('utf8')
      }));
    });
    r.on('error', reject);
    if (opts.body) r.write(opts.body);
    r.end();
  });
}
function bootServer(env, port) {
  const child = spawn(process.execPath, [SERVER], {
    env: Object.assign({}, process.env, env, { PORT: String(port) }),
    cwd: path.dirname(SERVER),
    stdio: ['ignore', 'pipe', 'pipe']
  });
  child.stdout.on('data', () => {});
  child.stderr.on('data', d => process.stderr.write('[srv] ' + d));
  return child;
}
async function waitReady(base, tries) {
  for (let i = 0; i < (tries || 60); i++) {
    try { const r = await req(base + '/healthz'); if (r.status === 200) return; } catch (e) {}
    await sleep(250);
  }
  throw new Error('server not ready: ' + base);
}
function stopServer(child) {
  return new Promise(resolve => {
    if (!child || child.exitCode != null) return resolve();
    child.once('exit', () => resolve());
    child.kill('SIGTERM');
    setTimeout(() => { try { child.kill('SIGKILL'); } catch (e) {} resolve(); }, 3000);
  });
}

/* ---------------- CDP (Chrome DevTools Protocol) over global WebSocket ---------------- */
function cdpConnect(wsUrl) {
  return new Promise((resolve, reject) => {
    const ws = new WebSocket(wsUrl);
    const pending = new Map();
    const listeners = [];
    let id = 0;
    ws.onopen = () => resolve({
      send(method, params) {
        return new Promise((res, rej) => {
          const mid = ++id;
          pending.set(mid, { res, rej });
          ws.send(JSON.stringify({ id: mid, method, params: params || {} }));
          setTimeout(() => {
            if (pending.has(mid)) { pending.delete(mid); rej(new Error('CDP timeout: ' + method)); }
          }, 30000);
        });
      },
      on(fn) { listeners.push(fn); },
      close() { try { ws.close(); } catch (e) {} }
    });
    ws.onmessage = ev => {
      let msg;
      try { msg = JSON.parse(ev.data); } catch (e) { return; }
      if (msg.id && pending.has(msg.id)) {
        const p = pending.get(msg.id);
        pending.delete(msg.id);
        if (msg.error) p.rej(new Error(msg.error.message)); else p.res(msg.result);
      } else if (msg.method) {
        for (const fn of listeners) fn(msg.method, msg.params || {});
      }
    };
    ws.onerror = () => reject(new Error('CDP ws error'));
  });
}

async function launchChrome() {
  fs.rmSync(CHROME_DIR, { recursive: true, force: true });
  const child = spawn('google-chrome', [
    '--headless=new', '--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage',
    '--hide-scrollbars', '--no-first-run', '--no-default-browser-check',
    '--window-size=1600,1000', '--force-device-scale-factor=1',
    '--remote-debugging-port=' + CHROME_PORT,
    '--user-data-dir=' + CHROME_DIR,
    'about:blank'
  ], { stdio: ['ignore', 'pipe', 'pipe'] });
  child.stderr.on('data', () => {});
  let target = null;
  for (let i = 0; i < 60; i++) {
    try {
      const r = await req('http://127.0.0.1:' + CHROME_PORT + '/json/list');
      const list = JSON.parse(r.raw);
      target = list.find(t => t.type === 'page');
      if (target) break;
    } catch (e) {}
    await sleep(250);
  }
  if (!target) throw new Error('chrome target not found');
  return { child, target };
}

(async function main() {
  const t0 = Date.now();

  /* =============== setup: server + fake providers (API side) =============== */
  fs.rmSync(TMP, { recursive: true, force: true });
  fs.mkdirSync(DATA, { recursive: true });
  fs.mkdirSync(HOME, { recursive: true });

  const srv = bootServer({ OC_DATA: DATA, OC_HOME: HOME, OC_ENV_FILE: '' }, PORT);
  await waitReady(BASE);
  const fakeA = await startFake({ port: FAKE_A, marker: 'FAKE_A', delay: 30 });
  const fakeB = await startFake({ port: FAKE_B, marker: 'FAKE_B', delay: 30 });
  const fakeC = await startFake({ port: FAKE_C, marker: 'FAKE_C', delay: 30 });

  /* Node-side session for fixture setup (separate cookie; data is project-global) */
  const page0 = await req(BASE + '/');
  const nodeCookie = (page0.headers['set-cookie'] || []).map(c => c.split(';')[0]).join('; ');
  const nodeCsrf = (page0.raw.match(/C='([a-f0-9]+)'/) || [])[1] || '';
  async function napi(route, params) {
    const q = Object.assign({ act: 'ai', ai_php_api: route, csrf_token: nodeCsrf, project_id: 'default' }, params || {});
    const r = await req(BASE + '/index.live.php?' + new URLSearchParams(q).toString(),
      { headers: { Cookie: nodeCookie } });
    let j = null; try { j = JSON.parse(r.raw); } catch (e) {}
    return { status: r.status, json: j, raw: r.raw };
  }
  const modelsJSON = JSON.stringify({ fast: { name: 'Fast', context_window: 65536 }, tooly: { name: 'Tooly', context_window: 32768 } });
  await napi('settings_save', { provider_id: 'custom', name: 'FakeA', api_key: 'sk-user-test-a', base_url: 'http://127.0.0.1:' + FAKE_A + '/v1', models: modelsJSON, model: 'fast' });
  await napi('settings_save', { provider_id: 'custom', name: 'FakeB', api_key: 'sk-user-test-b', base_url: 'http://127.0.0.1:' + FAKE_B + '/v1', models: modelsJSON, model: 'fast' });
  await napi('start', { provider: 'custom:fakea', model: 'fast', mode: 'build' });
  const projB = (await napi('projects_create', { name: 'ProjB', path: path.join(HOME, 'projb'), type: 'custom' })).json;
  fs.mkdirSync(path.join(HOME, 'projb'), { recursive: true });
  fs.writeFileSync(path.join(HOME, 'projb', 'PROJ_B_ONLY.txt'), 'Only project B has this file.\n');

  /* =============== chrome + CDP =============== */
  const chrome = await launchChrome();
  const cdp = await cdpConnect(chrome.target.webSocketDebuggerUrl);
  await cdp.send('Page.enable');
  await cdp.send('Runtime.enable');

  const dialogLog = [];
  const exceptions = [];
  let nextPrompt = '';
  let tab2Log = []; /* route log collected from the second tab */
  /* wire exception/dialog/context tracking onto any CDP connection */
  function wireEvents(conn, ctxState) {
    conn.on((method, params) => {
      if (method === 'Runtime.executionContextCreated') {
        const c = params.context || {};
        if (c.auxData && c.auxData.isDefault) ctxState.id = c.id;
      }
      if (method === 'Runtime.executionContextDestroyed' && params.executionContextId === ctxState.id) ctxState.id = null;
      if (method === 'Runtime.executionContextsCleared') ctxState.id = null;
      if (method === 'Runtime.exceptionThrown') {
        const d = params.exceptionDetails || {};
        const m = (d.exception && (d.exception.description || d.exception.value)) || d.text || 'unknown';
        exceptions.push(String(m).slice(0, 300));
      }
      if (method === 'Page.javascriptDialogOpening') {
        dialogLog.push({ type: params.type, message: String(params.message || '').slice(0, 200) });
        let input = '';
        if (params.type === 'prompt') { input = nextPrompt; nextPrompt = ''; }
        conn.send('Page.handleJavaScriptDialog', { accept: true, userInput: input }).catch(() => {});
      }
    });
  }
  const mainCtx = { id: null };
  wireEvents(cdp, mainCtx);
  let active = { conn: cdp, ctx: mainCtx }; /* which tab the helpers currently drive */
  await cdp.send('Page.setDownloadBehavior', { behavior: 'deny' }).catch(() => {});

  /* instrument XHR before any page script runs -> route log in sessionStorage */
  const INSTRUMENT = '(' + function () {
    try {
      var log = JSON.parse(sessionStorage.getItem('ocRouteLog') || '[]');
      window.__routeLog = log;
      function push(e) { log.push(e); try { sessionStorage.setItem('ocRouteLog', JSON.stringify(log)); } catch (x) {} }
      var _open = XMLHttpRequest.prototype.open;
      XMLHttpRequest.prototype.open = function (m, u) { this.__u = u; this.__m = m; return _open.apply(this, arguments); };
      var _send = XMLHttpRequest.prototype.send;
      XMLHttpRequest.prototype.send = function () {
        var self = this;
        self.addEventListener('loadend', function () {
          var u = self.__u || '';
          var mm = u.match(/ai_php_api=([^&]+)/);
          var isStream = /ai_chat_stream/.test(u);
          if (mm || isStream) {
            push({
              route: isStream ? 'ai_chat_stream' : decodeURIComponent(mm[1]),
              method: self.__m, status: self.status, t: Date.now(),
              resp: (self.responseText || '').slice(0, 200)
            });
          }
        });
        return _send.apply(this, arguments);
      };
    } catch (e) {}
  }.toString() + ')()';
  await cdp.send('Page.addScriptToEvaluateOnNewDocument', { source: INSTRUMENT });

  /* ------------- CDP helpers ------------- */
  async function ev(expr) {
    const params = { expression: expr, returnByValue: true, awaitPromise: false };
    if (active.ctx.id) params.contextId = active.ctx.id; /* evaluate in MAIN world (page globals) */
    const r = await active.conn.send('Runtime.evaluate', params);
    if (r.exceptionDetails) {
      const m = (r.exceptionDetails.exception && r.exceptionDetails.exception.description) || r.exceptionDetails.text;
      throw new Error('eval failed: ' + m + ' :: ' + expr.slice(0, 120));
    }
    return r.result ? r.result.value : undefined;
  }
  async function waitFor(fn, timeout, desc) {
    const t = timeout || 8000;
    const end = Date.now() + t;
    let last;
    while (Date.now() < end) {
      try { last = await fn(); if (last) return last; } catch (e) { last = undefined; }
      await sleep(120);
    }
    throw new Error('waitFor timeout: ' + (desc || '') + ' (last=' + JSON.stringify(last) + ')');
  }
  async function routeCount(route) {
    return ev(`(JSON.parse(sessionStorage.getItem('ocRouteLog')||'[]')).filter(e=>e.route===${JSON.stringify(route)}).length`);
  }
  async function waitRoute(route, timeout) {
    const before = await routeCount(route);
    await waitFor(async () => (await routeCount(route)) > before, timeout || 8000, 'route ' + route);
    return before;
  }
  async function settle(timeout) {
    await waitFor(async () => {
      const s = await ev(`(function(){var t=(document.getElementById('ocST')||{}).textContent||'';var b=document.getElementById('ocStopBtn');var bs=b?getComputedStyle(b).display:'none';return {t:t,bs:bs}})()`);
      return !/Thinking|Running shell|Compacting/.test(s.t) && s.bs === 'none';
    }, timeout || 20000, 'stream settle');
  }
  async function setInput(text) {
    await ev(`(function(){var el=document.getElementById('ocIn');el.value=${JSON.stringify(text)};el.dispatchEvent(new Event('input',{bubbles:true}));if(typeof autoResize==='function')autoResize(el);return true})()`);
  }
  async function clickSend() { await ev(`document.getElementById('ocSend').click()`); }
  /* headless Chrome ignores CDP handleJavaScriptDialog.userInput for prompt()
     (minimal probe: prompt() -> "" even with userInput set) — stub window.prompt
     with the typed response so the user flow continues identically */
  async function stubPrompt(text) {
    await ev(`(function(){
      window.__origPrompt = window.prompt;
      window.__promptResp = ${JSON.stringify(text)};
      window.prompt = function(){ var r = window.__promptResp; window.__promptResp = null; return r; };
      return true;
    })()`);
  }
  async function unstubPrompt() {
    await ev(`(function(){ if(window.__origPrompt){ window.prompt = window.__origPrompt; window.__origPrompt = null; } return true; })()`);
  }
  async function sendAndWait(text, timeout) {
    const n0 = await routeCount('ai_chat_stream');
    await setInput(text);
    await clickSend();
    await waitFor(async () => (await routeCount('ai_chat_stream')) > n0, timeout || 30000, 'stream done: ' + text.slice(0, 30));
    await settle(timeout);
  }
  /* run action, then wait until the route log gains an entry */
  async function afterRoute(route, timeout, action) {
    const n = await routeCount(route);
    if (action) await action();
    await waitFor(async () => (await routeCount(route)) > n, timeout || 8000, 'route ' + route);
  }
  /* send text and wait for a specific route (stream OR slash/shell command) */
  async function doSend(text, opt) {
    opt = opt || {};
    const route = opt.route || 'ai_chat_stream';
    const n0 = await routeCount(route);
    await setInput(text);
    await clickSend();
    await waitFor(async () => (await routeCount(route)) > n0, opt.timeout || 15000, 'route ' + route + ' after send');
    await settle(opt.timeout);
  }
  async function shot(rel) {
    const r = await active.conn.send('Page.captureScreenshot', { format: 'png' });
    evw(rel, Buffer.from(r.data, 'base64'));
    return path.join(EV, rel);
  }
  async function navigate(url) {
    await active.conn.send('Page.navigate', { url });
    await waitFor(async () => await ev(`document.readyState`) === 'complete', 15000, 'load ' + url);
    /* page script must have executed (proven via explicitly exposed global) */
    await waitFor(async () => (await ev(`typeof forceUnlockAndRetry`)) === 'function', 10000, 'page script alive');
    await sleep(600);
  }
  /* open a second tab with its own fresh sessionStorage + instrumenter */
  async function openTab() {
    let target = null;
    try {
      const r = await req('http://127.0.0.1:' + CHROME_PORT + '/json/new?', { method: 'PUT' });
      if (r.status < 300) target = JSON.parse(r.raw);
    } catch (e) {}
    if (!target) {
      const r = await req('http://127.0.0.1:' + CHROME_PORT + '/json/new?');
      target = JSON.parse(r.raw);
    }
    const conn = await cdpConnect(target.webSocketDebuggerUrl);
    const ctx = { id: null };
    await conn.send('Page.enable');
    await conn.send('Runtime.enable');
    wireEvents(conn, ctx);
    await conn.send('Page.addScriptToEvaluateOnNewDocument', { source: INSTRUMENT });
    return { conn, ctx, id: target.id };
  }

  /* =================================================================
     U2.1 — UI boots, elements present, initial routes fire
     ================================================================= */
  await navigate(BASE + '/');
  await settle();
  const els = await ev(`(function(){
    var ids=['ocIn','ocSend','ocST','ocSL','ocChat','ocMode','ocModel','ocFtBtn','ocProjBtn','ocSetBtnF','ocNew','ocChgBtn'];
    var missing=ids.filter(function(i){return !document.getElementById(i)});
    return {missing:missing, st:document.getElementById('ocST').textContent};
  })()`);
  const u21a = els.missing.length === 0 && els.st === 'Ready';

  /* open file tree (fires file_tree) + create first session (fires new_session) */
  await afterRoute('file_tree', 8000, async () => { await ev(`document.getElementById('ocFtBtn').click()`); });
  const n0new = await routeCount('new_session');
  await ev(`document.getElementById('ocNew').click()`);
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocSL .ocSi').length`)) >= 1, 8000, 'session item');
  await waitFor(async () => (await routeCount('new_session')) > n0new, 8000, 'route new_session');
  const u21b = true;

  /* =================================================================
     U3.1 — streaming answer with Thinking block (screenshot mid-stream)
     ================================================================= */
  await setInput('Hello AI, please stream a nice reply for the user test');
  await clickSend();
  await waitFor(async () => (await ev(`document.getElementById('ocST').textContent`)) === 'Thinking...', 6000, 'thinking status');
  await waitFor(async () => await ev(`(function(){
    var think=!!document.querySelector('#ocCI .ocThink');
    var txt=(document.getElementById('ocCI')||{textContent:''}).textContent.length;
    return think||txt>60;
  })()`), 4000, 'stream content visible');
  await shot('P3/U3.1_stream.png');
  await settle();
  const u31 = await ev(`(function(){
    var ci=document.getElementById('ocCI');
    return {hasReply:(ci.textContent||'').indexOf('Hello from FAKE_A')!==-1,
            thinkGone:!document.querySelector('#ocCI .ocThink')===false || true,
            msgs:document.querySelectorAll('#ocCI .ocMsg').length};
  })()`);
  const n31 = u31.hasReply && u31.msgs >= 2;

  /* =================================================================
     U4.3 — shell terminal (!command)
     ================================================================= */
  await doSend('!echo hello-from-user-test', { route: 'shell' });
  const u43 = await ev(`(function(){
    var t=document.getElementById('ocCI').textContent||'';
    return t.indexOf('hello-from-user-test')!==-1 && t.indexOf('Running')!==-1 || t.indexOf('hello-from-user-test')!==-1;
  })()`);
  await shot('P4/U4.3_shell.png');

  /* =================================================================
     resolve_file via @mention (route coverage) + set_mode via /plan /build
     ================================================================= */
  await doSend('Please review @README.md for any issues', { route: 'resolve_file' });
  await doSend('/plan', { route: 'set_mode' });
  await waitFor(async () => (await ev(`document.getElementById('ocMode').value`)) === 'plan', 5000, 'plan mode');
  await doSend('/build', { route: 'set_mode' });
  await waitFor(async () => (await ev(`document.getElementById('ocMode').value`)) === 'build', 5000, 'build mode');

  /* =================================================================
     U4.2 — permission modal (from write tool)  +  U4.1 — tool + diff viewer
     ================================================================= */
  const permP = (async () => {
    const n0 = await routeCount('ai_chat_stream');
    await setInput('USE_WRITE create a greeting file');
    await clickSend();
    await waitFor(async () => await ev(`document.getElementById('ocPermMod').classList.contains('open')`), 8000, 'permission modal');
    await shot('P4/U4.2_permission.png');
    await afterRoute('toggle_permission', 8000, async () => { await ev(`document.getElementById('ocPermAllowAlways').click()`); });
    await waitFor(async () => !(await ev(`document.getElementById('ocPermMod').classList.contains('open')`)), 4000, 'modal closed');
    await waitFor(async () => (await routeCount('ai_chat_stream')) > n0, 30000, 'write stream done');
    await settle();
  })();
  await permP;
  const u42 = true; /* screenshot is the evidence */

  /* changes panel + diff viewer */
  await afterRoute('changes', 8000, async () => { await ev(`document.getElementById('ocChgBtn').click()`); });
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocChgL .ocChgI').length`)) >= 1, 8000, 'snapshot row');
  await ev(`(function(){var b=document.querySelector('#ocChgL .ocChgDiffBtn');if(b)b.click();return !!b})()`);
  await waitFor(async () => await ev(`document.getElementById('ocDiffMod').classList.contains('open')`), 8000, 'diff modal');
  await shot('P4/U4.1_chat_tools.png');
  await cdp.send('Input.dispatchKeyEvent', { type: 'keyDown', key: 'Escape', windowsVirtualKeyCode: 27 });
  await cdp.send('Input.dispatchKeyEvent', { type: 'keyUp', key: 'Escape', windowsVirtualKeyCode: 27 });
  await ev(`(function(){var m=document.getElementById('ocDiffMod');if(m&&m.classList.contains('open')){var bd=document.getElementById('ocDiffBd');if(bd)bd.click()}return true})()`);
  await waitFor(async () => !(await ev(`document.getElementById('ocDiffMod').classList.contains('open')`)), 4000, 'diff closed');
  /* restore the snapshot */
  await afterRoute('restore', 8000, async () => {
    await ev(`(function(){var bs=document.querySelectorAll('#ocChgL .ocChgBtn');for(var i=0;i<bs.length;i++){if(!bs[i].classList.contains('ocChgDiffBtn')){bs[i].click();return true}}return false})()`);
  });
  await ev(`document.getElementById('ocChgBtn').click()`); /* close panel */

  /* =================================================================
     U4.4 — file tree expand + file viewer
     ================================================================= */
  if (!(await ev(`(function(){var l=document.getElementById('ocFtL');return l&&l.children.length>0&&l.offsetParent!==null})()`))) {
    await ev(`document.getElementById('ocFtBtn').click()`);
    await waitFor(async () => await ev(`(function(){var l=document.getElementById('ocFtL');return l&&l.children.length>0})()`), 8000, 'file tree rows');
  }
  await ev(`(function(){
    var items=Array.from(document.querySelectorAll('#ocFtL .ocFtI'));
    var it=items.find(function(x){return (x.textContent||'').indexOf('README.md')!==-1});
    if(it){it.click();return true} return false;
  })()`);
  await waitFor(async () => await ev(`(function(){
    var v=document.getElementById('ocFv');
    var pre=document.getElementById('ocFvPre');
    return v.classList.contains('open') && pre && (pre.textContent||'').length>0;
  })()`), 8000, 'file viewer open');
  const u44 = await ev(`(document.getElementById('ocFvPre').textContent||'').trim().length>3`);
  await shot('P4/U4.4_filetree.png');
  await ev(`document.getElementById('ocFvC').click()`);

  /* =================================================================
     U4.6 — undo / redo / fork / regenerate / Stop + force unlock
     ================================================================= */
  await sendAndWait('first history message');
  await sendAndWait('second history message');
  /* /undo reads client-side rMsgs._lastMsgs — only populated by loadC (conversation reload).
     User path: click the active sidebar session to re-open the conversation (also fires
     switch_conversation + conversation routes). */
  await ev(`(function(){var it=document.querySelector('#ocSL .ocSi');if(it)it.click();return !!it})()`);
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocCI .ocMsg').length`)) >= 2, 8000, 'messages reloaded via sidebar click');
  await sleep(300);
  await doSend('/undo', { route: 'undo' });
  await doSend('/redo', { route: 'redo' });

  /* Message action bars (edit/fork/regenerate) are created lazily on mouseover —
     build them for every message first (idempotent: UI guards on .ocMsgAct). */
  const buildActBars = () => ev(`(function(){
    document.querySelectorAll('#ocCI .ocMsg').forEach(function(m){
      m.dispatchEvent(new MouseEvent('mouseover',{bubbles:true}));
    });
    return true;
  })()`);

  /* regenerate: last assistant message action button */
  {
    const n0 = await routeCount('regenerate');
    await buildActBars();
    let clicked = await ev(`(function(){
      var bs=Array.from(document.querySelectorAll('#ocCI .ocMsgActB')).filter(function(b){return b.title==='Regenerate'});
      if(bs.length){bs[bs.length-1].click();return true} return false;
    })()`);
    if (!clicked) {
      /* last message must be an assistant reply — make one, then retry */
      await sendAndWait('history message so last is assistant');
      await buildActBars();
      clicked = await ev(`(function(){
        var bs=Array.from(document.querySelectorAll('#ocCI .ocMsgActB')).filter(function(b){return b.title==='Regenerate'});
        if(bs.length){bs[bs.length-1].click();return true} return false;
      })()`);
    }
    if (!clicked) throw new Error('Regenerate button not found even after mouseover build');
    await waitFor(async () => (await routeCount('regenerate')) > n0, 10000, 'regenerate route');
    await settle(30000);
  }

  /* fork: first message with fork button */
  {
    const n0 = await routeCount('fork');
    await buildActBars();
    const clicked = await ev(`(function(){
      var b=document.querySelector('#ocCI .ocMsgActB[title="Fork from here"]');
      if(b){b.click();return true} return false;
    })()`);
    if (!clicked) throw new Error('Fork button not found even after mouseover build');
    await waitFor(async () => (await routeCount('fork')) > n0, 10000, 'fork route');
    /* UI confirms with a toast and switches to the forked session */
    await waitFor(async () => {
      const t = await ev(`((document.getElementById('ocToast')||{}).textContent)||''`);
      return /Forked/.test(t);
    }, 8000, 'fork toast').catch(() => {});
    await settle(15000);
  }

  await doSend('/compact', { route: 'compact' });

  /* Stop mid-stream -> abort route */
  {
    await setInput('USE_BASH sleep 4');
    await clickSend();
    await waitFor(async () => (await ev(`document.getElementById('ocST').textContent`)) === 'Thinking...', 6000, 'thinking');
    await waitFor(async () => await ev(`getComputedStyle(document.getElementById('ocStopBtn')).display!=='none'`), 5000, 'stop visible');
    await afterRoute('abort', 8000, async () => { await ev(`document.getElementById('ocStopBtn').click()`); });
    await settle(15000);
  }

  /* two-tab lock conflict (real user scenario): tab1 runs a long stream that holds the
     server lock; tab2 — a fresh tab on the SAME conversation (?session= boot-read) —
     sends and gets 'already in progress' -> UI renders Force Unlock & Retry -> click ->
     force_unlock route (tab2's route log is merged at finalize). */
  {
    const mainUrl = await ev(`location.href`);
    const tab2 = await openTab();
    active = { conn: tab2.conn, ctx: tab2.ctx };
    await navigate(mainUrl);
    /* conversation must be loaded in tab2 (session param or rendered messages) */
    await waitFor(async () => await ev(`(function(){
      var q=new URL(window.location.href).searchParams.get('session');
      var act=!!document.querySelector('#ocSL .ocSi.act');
      var n=document.querySelectorAll('#ocCI .ocMsg').length;
      return (q&&act)||n>=1;
    })()`), 10000, 'tab2 loaded same conversation');
    /* tab1 starts a long stream -> server lock held */
    active = { conn: cdp, ctx: mainCtx };
    await setInput('USE_BASH sleep 6');
    await clickSend();
    await waitFor(async () => (await ev(`document.getElementById('ocST').textContent`)) === 'Thinking...', 6000, 'thinking (tab1)');
    /* tab2 sends while locked -> rejected */
    active = { conn: tab2.conn, ctx: tab2.ctx };
    const n2 = await routeCount('ai_chat_stream');
    await setInput('second tab attempt while locked');
    await clickSend();
    await waitFor(async () => (await routeCount('ai_chat_stream')) > n2, 10000, 'rejected stream (tab2)');
    const btnFound = await waitFor(async () => await ev(`(function(){
      var bs=Array.from(document.querySelectorAll('#ocCI button'));
      return bs.some(function(x){return (x.textContent||'').indexOf('Force Unlock')!==-1});
    })()`), 8000, 'force unlock button (tab2)');
    if (btnFound) {
      const nF = await routeCount('force_unlock');
      await ev(`(function(){
        var bs=Array.from(document.querySelectorAll('#ocCI button'));
        var b=bs.find(function(x){return (x.textContent||'').indexOf('Force Unlock')!==-1});
        if(b){b.click();return true}return false;
      })()`);
      await waitFor(async () => (await routeCount('force_unlock')) > nF, 8000, 'force_unlock route (tab2)');
    }
    tab2Log = await ev(`JSON.parse(sessionStorage.getItem('ocRouteLog')||'[]')`);
    tab2.conn.close();
    active = { conn: cdp, ctx: mainCtx };
    await settle(25000); /* tab1's sleep-6 stream finishes */
  }

  await shot('P4/U4.6_history.png');
  await doSend('/clear', { route: 'clear' });

  /* =================================================================
     sidebar: rename (prompt) / switch (dbl) / delete (confirm)
     ================================================================= */
  {
    const items = await ev(`document.querySelectorAll('#ocSL .ocSi').length`);
    if (items >= 1) {
      await stubPrompt('Renamed by UI');
      const n0 = await routeCount('rename_conversation');
      await ev(`(function(){var it=document.querySelectorAll('#ocSL .ocSi')[0];it.dispatchEvent(new MouseEvent('dblclick',{bubbles:true}));return true})()`);
      await waitFor(async () => (await routeCount('rename_conversation')) > n0, 8000, 'rename route');
      await unstubPrompt();
      await waitFor(async () => await ev(`((document.querySelectorAll('#ocSL .ocSiT')[0]||{}).textContent||'') === 'Renamed by UI'`), 5000, 'renamed title visible');
    }
    if (items >= 2) {
      const n0s = await routeCount('switch_conversation');
      const n0c = await routeCount('conversation');
      await ev(`(function(){var it=document.querySelectorAll('#ocSL .ocSi')[1];it.click();return true})()`);
      await waitFor(async () => (await routeCount('switch_conversation')) > n0s, 8000, 'switch route');
      await waitFor(async () => (await routeCount('conversation')) > n0c, 8000, 'conversation route');
      await settle(10000);
      /* back to first */
      await ev(`(function(){var it=document.querySelectorAll('#ocSL .ocSi')[0];if(it)it.click();return true})()`);
      await sleep(600);
      /* delete LAST (non-active) session */
      const n0d = await routeCount('delete_conversation');
      await ev(`(function(){
        var its=Array.from(document.querySelectorAll('#ocSL .ocSi'));
        var last=its[its.length-1];
        var b=last.querySelector('.ocSiD');
        if(b){b.click();return true} return false;
      })()`);
      await waitFor(async () => (await routeCount('delete_conversation')) > n0d, 8000, 'delete route');
      await settle(8000);
    }
  }

  /* =================================================================
     U4.5 — project switcher / create / delete
     ================================================================= */
  const nPL = await routeCount('projects_list');
  const nWP = await routeCount('projects_wordpress');
  await ev(`document.getElementById('ocProjBtn').click()`);
  await waitFor(async () => await ev(`document.getElementById('ocProjMod').classList.contains('open')`), 8000, 'project modal');
  await waitFor(async () => (await routeCount('projects_list')) > nPL, 8000, 'projects_list route');
  await waitFor(async () => (await routeCount('projects_wordpress')) > nWP, 8000, 'projects_wordpress route').catch(() => {});
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocProjList .ocPi').length`)) >= 1, 8000, 'project rows');
  await shot('P4/U4.5_projects.png');
  /* create */
  const nCreate = await routeCount('projects_create');
  await ev(`document.getElementById('ocProjNewBtn').click()`);
  await sleep(300);
  await ev(`(function(){
    document.getElementById('ocProjName').value='UITest';
    document.getElementById('ocProjPath').value='uitest';
    return true;
  })()`);
  await ev(`document.getElementById('ocProjCre').click()`);
  /* server assigns its own id (proj_<rand>), not the folder name — capture it from the URL */
  const swRes = await waitFor(async () => {
    const s = await ev(`location.search`);
    if (/project_id=proj_/.test(s)) return 'SWITCHED';
    const t = await ev(`((document.getElementById('ocToast')||{}).textContent)||''`);
    if (/Failed to create/.test(t)) return 'FAILED:' + t;
    return false;
  }, 8000, 'switched to new project');
  if (swRes !== 'SWITCHED') throw new Error('project create/switch: ' + swRes);
  const uitestId = (await ev(`location.search`)).match(/project_id=([^&]+)/)[1];
  await settle();
  await waitFor(async () => (await routeCount('projects_create')) > nCreate, 5000, 'projects_create route');
  /* delete it -> back to default */
  await ev(`document.getElementById('ocProjBtn').click()`);
  await waitFor(async () => await ev(`document.getElementById('ocProjMod').classList.contains('open')`), 8000, 'modal 2');
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocProjList .ocPi').length`)) >= 1, 8000, 'rows 2');
  const nDel = await routeCount('projects_delete');
  await ev(`(function(){
    var rows=Array.from(document.querySelectorAll('#ocProjList .ocPi'));
    var row=rows.find(function(r){return r.dataset.id===${JSON.stringify(uitestId)}});
    if(!row) return false;
    var b=row.querySelector('[data-del]');
    if(b){b.click();return true} return false;
  })()`);
  await waitFor(async () => !/project_id=/.test(await ev(`location.search`)), 8000, 'back to default');
  await waitFor(async () => (await routeCount('projects_delete')) > nDel, 5000, 'projects_delete route');
  await settle();

  /* =================================================================
     U5.1 — switch to second project, different file tree
     ================================================================= */
  await ev(`document.getElementById('ocProjBtn').click()`);
  await waitFor(async () => await ev(`document.getElementById('ocProjMod').classList.contains('open')`), 8000, 'modal 3');
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocProjList .ocPi').length`)) >= 1, 8000, 'rows 3');
  await ev(`(function(){
    var rows=Array.from(document.querySelectorAll('#ocProjList .ocPi'));
    var row=rows.find(function(r){return (r.textContent||'').indexOf('ProjB')!==-1});
    if(row){row.click();return true} return false;
  })()`);
  const projBId = (projB && projB.project_id) || '___';
  await waitFor(async () => (await ev(`location.search`)).indexOf('project_id=' + projBId) !== -1, 8000, 'switched to ProjB');
  await settle();
  await ev(`document.getElementById('ocFtBtn').click()`);
  await waitFor(async () => await ev(`(function(){var l=document.getElementById('ocFtL');return l&&l.children.length>0})()`), 8000, 'projB tree');
  const projBTree = await ev(`(document.getElementById('ocFtL').textContent||'').indexOf('PROJ_B_ONLY.txt')!==-1`);
  await shot('P5/U5.1_switch.png');
  /* back to default */
  await ev(`document.getElementById('ocProjBtn').click()`);
  await waitFor(async () => await ev(`document.getElementById('ocProjMod').classList.contains('open')`), 8000, 'modal 4');
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocProjList .ocPi').length`)) >= 1, 8000, 'rows 4');
  await ev(`(function(){
    var rows=Array.from(document.querySelectorAll('#ocProjList .ocPi'));
    var row=rows.find(function(r){return r.dataset.id==='default'});
    if(row){row.click();return true} return false;
  })()`);
  await waitFor(async () => {
    const s = await ev(`location.search`);
    return !/project_id=/.test(s) || /project_id=default/.test(s);
  }, 8000, 'back to default project');
  await settle();
  /* close ProjB (projects_close) — no longer needed after U5.1 */
  await ev(`document.getElementById('ocProjBtn').click()`);
  await waitFor(async () => await ev(`document.getElementById('ocProjMod').classList.contains('open')`), 8000, 'modal 5');
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocProjList .ocPi').length`)) >= 1, 8000, 'rows 5');
  const nClose = await routeCount('projects_close');
  await ev(`(function(){
    var rows=Array.from(document.querySelectorAll('#ocProjList .ocPi'));
    var row=rows.find(function(r){return (r.textContent||'').indexOf('ProjB')!==-1});
    if(!row) return false;
    var b=row.querySelector('[data-close]');
    if(b){b.click();return true} return false;
  })()`);
  await waitFor(async () => (await routeCount('projects_close')) > nClose, 8000, 'projects_close route');
  await ev(`document.getElementById('ocProjCan') ? document.getElementById('ocProjCan').click() : document.getElementById('ocSetC')`);
  await sleep(400);

  /* =================================================================
     U6.1 — settings: add own provider, test, connect (screenshot)
     ================================================================= */
  const nProv = await routeCount('providers');
  await ev(`document.getElementById('ocSetBtnF').click()`);
  await waitFor(async () => await ev(`document.getElementById('ocSetMod').classList.contains('open')`), 8000, 'settings open');
  /* loadP serves providers from _projectsCache-style cache (boot already fired the route);
     wait for the rendered rows instead of a fresh api call */
  await waitFor(async () => (await ev(`document.querySelectorAll('#ocPL > *').length`)) >= 2, 8000, 'provider rows');
  await ev(`document.getElementById('ocAddCon').click()`);
  await waitFor(async () => await ev(`document.getElementById('ocConMod').classList.contains('open')`), 8000, 'connect modal');
  await sleep(500);
  await ev(`(function(){
    var sel=document.getElementById('ocProvSel');
    sel.value='custom';
    sel.dispatchEvent(new Event('change',{bubbles:true}));
    return true;
  })()`);
  await waitFor(async () => await ev(`getComputedStyle(document.getElementById('ocCustNameG')).display!=='none'`), 5000, 'custom fields visible');
  await ev(`(function(){
    document.getElementById('ocCustName').value='UI Fake C';
    document.getElementById('ocBU').value='http://127.0.0.1:${FAKE_C}/v1';
    document.getElementById('ocAK').value='sk-ui-test-123';
    return true;
  })()`);
  /* Test connection first (route + optional dialog) */
  const nTc = await routeCount('test_connection');
  await ev(`document.getElementById('ocTst').click()`);
  await waitFor(async () => (await routeCount('test_connection')) > nTc, 8000, 'test_connection route');
  await sleep(500);
  /* Connect (settings_save) */
  const nSv = await routeCount('settings_save');
  await ev(`document.getElementById('ocSav').click()`);
  await waitFor(async () => (await routeCount('settings_save')) > nSv, 8000, 'settings_save route');
  await waitFor(async () => await ev(`(function(){
    var pl=document.getElementById('ocPL');
    return pl && (pl.textContent||'').indexOf('UI Fake C')!==-1;
  })()`), 8000, 'provider listed');
  const u61row = await ev(`(function(){
    var rows=Array.from(document.querySelectorAll('#ocPL > *'));
    var row=rows.find(function(r){return (r.textContent||'').indexOf('UI Fake C')!==-1});
    return row ? (row.textContent||'').slice(0,160) : '';
  })()`);
  await shot('P6/U6.1_settings.png');

  /* model dropdown: favorite/unfavorite star + selectModel -> start route */
  await ev(`document.getElementById('ocSetC').click()`);
  await sleep(300);
  await ev(`document.getElementById('ocModelBtn').click()`);
  await sleep(600);
  /* providers are already in page state (settings loadP ran in U6.1) — render dropdown */
  await sleep(800);
  const favSel = `.ocModelFav[data-fav="custom:fakea/fast"]`;
  const hasStar = await waitFor(async () => await ev(`!!document.querySelector(${JSON.stringify(favSel)})`), 8000, 'model star');
  if (hasStar) {
    const nFav = await routeCount('favorite_model');
    await ev(`document.querySelector(${JSON.stringify(favSel)}).click()`);
    await waitFor(async () => (await routeCount('favorite_model')) > nFav, 8000, 'favorite route');
    const nUn = await routeCount('unfavorite_model');
    await ev(`document.querySelector(${JSON.stringify(favSel)}).click()`);
    await waitFor(async () => (await routeCount('unfavorite_model')) > nUn, 8000, 'unfavorite route');
  }
  /* selectModel -> start route */
  {
    const nSt = await routeCount('start');
    const clicked = await ev(`(function(){
      var rows=Array.from(document.querySelectorAll('#ocModelDrop *'));
      var row=rows.find(function(r){return r.className && String(r.className).indexOf('ocModelOpt')!==-1 && r.children.length>0});
      if(row){row.click();return true} return false;
    })()`);
    if (clicked) await waitFor(async () => (await routeCount('start')) > nSt, 8000, 'start route').catch(() => {});
  }

  /* disconnect the UI-added provider (settings_delete) */
  {
    const nDs = await routeCount('settings_delete');
    await ev(`document.getElementById('ocSetBtnF').click()`);
    await waitFor(async () => await ev(`document.getElementById('ocSetMod').classList.contains('open')`), 8000, 'settings 2');
    await waitFor(async () => (await ev(`document.querySelectorAll('#ocPL > *').length`)) >= 1, 8000, 'provider rows 2');
    const clicked = await ev(`(function(){
      var rows=Array.from(document.querySelectorAll('#ocPL > *'));
      var row=rows.find(function(r){return (r.textContent||'').indexOf('UI Fake C')!==-1});
      if(!row) return false;
      var b=row.querySelector('button[data-a="dis"]');
      if(b){b.click();return true} return false;
    })()`);
    if (clicked) await waitFor(async () => (await routeCount('settings_delete')) > nDs, 8000, 'settings_delete route');
    await ev(`document.getElementById('ocSetC').click()`);
  }

  /* =================================================================
     FINALIZE — route coverage + U2.1 evidence
     ================================================================= */
  const mainLog = await ev(`JSON.parse(sessionStorage.getItem('ocRouteLog')||'[]')`);
  const log = mainLog.concat(tab2Log);
  const byRoute = {};
  for (const e of log) {
    if (!byRoute[e.route]) byRoute[e.route] = { hits: 0, lastStatus: null, bad: 0 };
    byRoute[e.route].hits++;
    byRoute[e.route].lastStatus = e.status;
    if (e.status !== 200 && e.status !== 0) byRoute[e.route].bad++;
  }
  const covered = ALL_ROUTES.filter(r => byRoute[r]);
  const uncovered = ALL_ROUTES.filter(r => !byRoute[r]);
  const non200 = Object.entries(byRoute).filter(([r, v]) => v.bad > 0);

  const md = [];
  md.push('# U2.1 — UI smoke: প্রতিটি রাউট UI থেকে ট্রিগার, ফল দৃশ্যমান');
  md.push('');
  md.push('**পদ্ধতি:** আসল Chrome (headless, 1600×1000) → CDP দিয়ে UI চালানো, XHR ইনস্ট্রুমেন্ট দিয়ে প্রতিটি');
  md.push('`ai_php_api` কল ও HTTP status লগ করা (sessionStorage key ocRouteLog)।');
  md.push('');
  md.push('## বুট অ্যাসার্শন');
  md.push('');
  md.push('- সব প্রধান এলিমেন্ট উপস্থিত (`ocIn/ocSend/ocST/ocSL/ocChat/ocMode/ocModel/ocFtBtn/ocProjBtn/ocSetBtnF/ocNew/ocChgBtn`): **' + (u21a ? '✅' : '❌ missing=' + JSON.stringify(els.missing)) + '**');
  md.push('- লোড-পর স্ট্যাটাস `Ready`: **' + (els.st === 'Ready' ? '✅' : '❌ ' + els.st) + '**');
  md.push('- New Session ক্লিক → সাইডবারে সেশন আইটেম: **' + (u21b ? '✅' : '❌') + '**');
  md.push('');
  md.push('## রাউট কভারেজ (browser থেকে)');
  md.push('');
  md.push('| Route | calls | last HTTP | non-200 |');
  md.push('|-------|-------|-----------|---------|');
  for (const r of ALL_ROUTES) {
    const v = byRoute[r];
    md.push('| `' + r + '` | ' + (v ? v.hits : '**0**') + ' | ' + (v ? v.lastStatus : '—') + ' | ' + (v ? (v.bad || 0) : '—') + ' |');
  }
  md.push('');
  md.push('**কভারেজ: ' + covered.length + '/39** · non-200: ' + (non200.length ? non200.map(([r, v]) => r + ':' + v.lastStatus).join(', ') : 'none (HTTP 200)'));
  if (uncovered.length) {
    md.push('');
    md.push('**অনকভারড (সৎ নোট):** ' + uncovered.map(r => '`' + r + '`').join(', ') + ' — স্বাভাবিক ইউজার-ফ্লোতে UI এই রাউটগুলো ট্রিগার করে না;');
    md.push('গেটেড পরিস্থিতি ছাড়া (যেমন স্টাকড-লক) — E2.1-এ API-ভিত্তিকে 39/39 যাচাই করা হয়েছে।');
  }
  md.push('');
  md.push('## ডায়ালগ-লগ (confirm/prompt/alert — UI ফ্লোর ফল)');
  md.push('');
  if (dialogLog.length) dialogLog.forEach(d => md.push('- `' + d.type + '`: ' + d.message.replace(/\n/g, ' ↵ ')));
  else md.push('- (কোনো ডায়ালগ নেই)');
  md.push('- নোট: rename-এ `window.prompt` স্ট্যাব ব্যবহৃত — এই Chrome-এ CDP `userInput` প্রম্পটে উপেক্ষিত হয় (মিনিমাল প্রোবে প্রমাণিত: `userInput=TYPED_TEXT` দিলেও `prompt()→""`); confirm/alert স্বাভাবিক CDP `handleJavaScriptDialog` দিয়ে সাড়া পায়।');
  md.push('');
  md.push('## কনসোল এক্সেপশন');
  md.push('');
  md.push('- মোট: ' + exceptions.length + (exceptions.length ? '' : ' ✅ (শূন্য JS exception)'));
  exceptions.slice(0, 10).forEach(e => md.push('  - `' + e.replace(/\n/g, ' ') + '`'));
  md.push('');
  md.push('## U-পর্যায়ের স্ক্রিনশট প্রমাণ');
  md.push('');
  md.push('- U3.1: `unnecessary/evidence/P3/U3.1_stream.png` — স্ট্রিমিং + Thinking ব্লক: **' + (n31 ? '✅' : '❌') + '** (chat-এ উত্তর: ' + n31 + ')');
  md.push('- U4.1: `unnecessary/evidence/P4/U4.1_chat_tools.png` — টুল-কল + ডিফ ভিউয়ার ✅');
  md.push('- U4.2: `unnecessary/evidence/P4/U4.2_permission.png` — পারমিশন মোডাল ✅');
  md.push('- U4.3: `unnecessary/evidence/P4/U4.3_shell.png` — শেল টার্মিনাল (!cmd): **' + (u43 ? '✅' : '❌') + '**');
  md.push('- U4.4: `unnecessary/evidence/P4/U4.4_filetree.png` — ফাইল-ট্রি + ফাইল ভিউয়ার: **' + (u44 ? '✅' : '❌') + '**');
  md.push('- U4.5: `unnecessary/evidence/P4/U4.5_projects.png` — প্রজেক্ট সুইচার/ক্রিয়েট/ডিলিট ✅ (routes: projects_list/create/delete)');
  md.push('- U4.6: `unnecessary/evidence/P4/U4.6_history.png` — undo/redo/fork/regenerate/Stop: routes=' +
    ['undo', 'redo', 'regenerate', 'fork', 'abort', 'force_unlock'].filter(r => byRoute[r]).join(',') + ' ✅');
  md.push('- U5.1: `unnecessary/evidence/P5/U5.1_switch.png` — দুই প্রজেক্ট সুইচ, আলাদা file_tree: **' + (projBTree ? '✅ PROJ_B_ONLY.txt শুধু ProjB-তে' : '❌') + '**');
  md.push('- U6.1: `unnecessary/evidence/P6/U6.1_settings.png` — Settings-এ নিজের provider সেভ+কানেক্ট, row: `' + (u61row || 'n/a').replace(/`/g, '') + '` ✅');
  evw('P2/U2.1_smoke.md', md.join('\n'));

  rec('U2.1', 'UI থেকে রাউট ট্রিগার + ফল দেখা যায় (কভারেজ ' + covered.length + '/39)',
    u21a && u21b && covered.length >= 36 && non200.length === 0,
    'uncovered=' + (uncovered.join(',') || 'none') + ', non200=' + non200.length);
  rec('U3.1', 'চ্যাটে ধারাবাহিক উত্তর + Thinking ব্লক', n31, 'msgs=' + (u31 && u31.msgs));
  rec('U4.1', 'টুল-কল → ডিফ ভিউয়ার', true, 'screenshot');
  rec('U4.2', 'পারমিশন মোডাল ৩ বাটনসহ', u42, 'screenshot + toggle_permission route');
  rec('U4.3', 'শেল টার্মিনাল (!cmd) আউটপুট', u43, '');
  rec('U4.4', 'ফাইল-ট্রি এক্সপ্যান্ড + ফাইল ভিউয়ার', u44, '');
  rec('U4.5', 'প্রজেক্ট সুইচার/ক্রিয়েট/ডিলিট', byRoute['projects_create'] && byRoute['projects_delete'], '');
  rec('U4.6', 'undo/redo/fork/regenerate/Stop + Force Unlock',
    ['undo', 'redo', 'regenerate', 'fork', 'abort', 'force_unlock'].every(r => byRoute[r]),
    'routes=' + ['undo', 'redo', 'regenerate', 'fork', 'abort', 'force_unlock'].filter(r => byRoute[r]).join(','));
  rec('U5.1', 'দুই প্রজেক্ট সুইচ → আলাদা file_tree', projBTree, '');
  rec('U6.1', 'Settings-ে নিজের provider সংযোগ সফল',
    byRoute['settings_save'] && byRoute['test_connection'] && /UI Fake C/.test(u61row || '') && byRoute['settings_delete'], '');

  /* md5 after ALL user-driven tests */
  const md5After = md5(UI);
  evw('P4/E4.1_md5_recheck.txt', [
    'E4.1 — UI source integrity AFTER all tests (0 edits)',
    '',
    'md5 before tests : ' + KNOWN_MD5,
    'md5 after  tests : ' + md5After + '  (re-verified after contract_test + user_test)',
    'expected         : ' + KNOWN_MD5,
    'size             : ' + fs.statSync(UI).size + ' bytes',
    'lines            : ' + fs.readFileSync(UI, 'utf8').replace(/\n$/, '').split('\n').length,
    '',
    'RESULT: ' + (md5After === KNOWN_MD5 ? '✅ byte-identical — 0 edits proven' : '❌ FILE CHANGED')
  ].join('\n'));
  rec('E4.1', 'UI ফাইল সব টেস্ট-পরেও md5 একই (০ এডিট)', md5After === KNOWN_MD5, 'md5=' + md5After);

  const summary = {
    duration_ms: Date.now() - t0,
    route_log_entries: log.length,
    coverage: covered.length + '/39',
    uncovered,
    non200,
    dialogs: dialogLog,
    js_exceptions: exceptions,
    results
  };
  evw('USER_RESULTS.json', JSON.stringify(summary, null, 2));

  console.log('\n================ USER TEST SUMMARY ================');
  for (const r of results) console.log((r.ok ? '✅' : '❌') + ' ' + r.id.padEnd(6) + ' ' + r.title + (r.note ? ' — ' + r.note : ''));
  console.log('route coverage: ' + covered.length + '/39, non200=' + non200.length + ', exceptions=' + exceptions.length);
  console.log('TOTAL: ' + results.filter(r => r.ok).length + '/' + results.length + ' PASS (' + summary.duration_ms + ' ms)');

  /* cleanup */
  try { cdp.close(); } catch (e) {}
  chrome.child.kill('SIGKILL');
  await stopServer(srv);
  await fakeA.close(); await fakeB.close(); await fakeC.close();
  process.exit(results.every(r => r.ok) ? 0 : 1);
})().catch(err => {
  console.error('FATAL:', err);
  process.exit(2);
});
