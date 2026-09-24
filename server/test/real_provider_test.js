'use strict';
/*
 * real_provider_test.js — REAL-provider browser click-through test (labels: R*)
 *
 * Adds two authentic OpenAI-compatible providers entirely through the UI
 * (clicks in real Chrome over CDP), runs Test Connection, Saves, then chats
 * with each provider and captures screenshots as evidence.
 *
 *   Provider A: ngrok Ollama proxy  https://monkhood-unstaffed-catalog.ngrok-free.dev/v1
 *   Provider B: Mission Barisal     http://localhost:3000/v1
 *
 *   node real_provider_test.js
 *
 * Evidence: unnecessary/evidence/REAL/*.png, unnecessary/evidence/REAL/REAL_PROVIDER.md,
 *           unnecessary/evidence/REAL_RESULTS.json
 */
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const http = require('http');
const crypto = require('crypto');

const ROOT = path.resolve(__dirname, '..', '..');
const UI = path.join(ROOT, 'index.php');
const SERVER = path.join(ROOT, 'server', 'server.js');
const EV = path.join(ROOT, 'unnecessary', 'evidence');
const TMP = '/tmp/oc_real';
const DATA = path.join(TMP, 'data');
const HOME = path.join(TMP, 'home');
const CHROME_DIR = '/tmp/oc_real_chrome';

const PORT = 18910;
const CHROME_PORT = 9446;
const BASE = 'http://127.0.0.1:' + PORT;
const KNOWN_MD5 = '895eb75226cc2801834baaced2873bb1';

/* an assistant reply must never be one of these engine/provider error shapes */
const FORBIDDEN_REPLY = /AI request failed|Cannot reach AI endpoint|HTTP \d{3}|Unauthorized|invalid session token/i;
const PROV_A = {
  key: 'A',
  name: 'Ollama Remote',
  base: 'https://monkhood-unstaffed-catalog.ngrok-free.dev/v1',
  apiKey: 'sk-ollama-ngrok-secret', /* ngrok accepts any/no bearer (probed: 200) */
  models: 'jaahas/qwen3.5-uncensored:2b=Qwen3.5 Uncensored 2B, deepseek-r1:1.5b=DeepSeek R1 1.5B',
  needle: 'Qwen3.5',
  modelId: 'jaahas/qwen3.5-uncensored:2b',
  prompt: 'Reply with exactly this sentence and nothing else: OLLAMA CONNECTED OK',
  expect: /OLLAMA CONNECTED OK/i,
  chatShot: 'REAL/R1_chat.png',
  provShot: 'REAL/R1_provider_saved.png',
  /* real tool-loops through ngrok can legitimately take minutes
     (one iteration measured 91s) — budget must cover a full loop */
  chatWait: 300000
};
const PROV_B = {
  key: 'B',
  name: 'Mission Local',
  base: 'http://localhost:3000/v1',
  /* Mission proxy REJECTS unknown bearer tokens on /chat/completions with
     401 "invalid session token" (probed: fake key -> 401, no key -> 200),
     so the authentic config for this provider is an EMPTY api key. */
  apiKey: '',
  models: 'mission=Mission Pilot, doc-king=Doc King',
  needle: 'Mission Pilot',
  modelId: 'mission',
  prompt: 'তোমার নাম কী? এক লাইনে বাংলায় ছোট উত্তর দাও।',
  expect: null, /* Bengali free-form reply — asserted as non-error + non-empty */
  chatShot: 'REAL/R2_chat.png',
  provShot: 'REAL/R2_provider_saved.png',
  chatWait: 300000
};
function replyOk(reply, p) {
  if (!reply || reply.length < 5) return false;
  if (FORBIDDEN_REPLY.test(reply)) return false; /* an error was rendered as the reply */
  if (p.expect && !p.expect.test(reply)) return false;
  return true;
}

const results = [];
let chatAttempts = 1;
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
function stopProc(child) {
  return new Promise(resolve => {
    if (!child || child.exitCode != null) return resolve();
    child.once('exit', () => resolve());
    child.kill('SIGTERM');
    setTimeout(() => { try { child.kill('SIGKILL'); } catch (e) {} resolve(); }, 3000);
  });
}

/* ---------------- CDP over global WebSocket (no npm) ---------------- */
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
    ws.onmessage = e => {
      let msg;
      try { msg = JSON.parse(e.data); } catch (err) { return; }
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
  const notes = [];
  let srv = null, chrome = null;

  try {
    /* =============== setup: fresh server =============== */
    fs.rmSync(TMP, { recursive: true, force: true });
    fs.mkdirSync(DATA, { recursive: true });
    fs.mkdirSync(HOME, { recursive: true });
    srv = bootServer({ OC_DATA: DATA, OC_HOME: HOME, OC_ENV_FILE: '' }, PORT);
    await waitReady(BASE);

    /* =============== chrome + CDP =============== */
    chrome = await launchChrome();
    const cdp = await cdpConnect(chrome.target.webSocketDebuggerUrl);
    await cdp.send('Page.enable');
    await cdp.send('Runtime.enable');

    const dialogLog = [];
    const exceptions = [];
    const ctx = { id: null };
    cdp.on((method, params) => {
      if (method === 'Runtime.executionContextCreated') {
        const c = params.context || {};
        if (c.auxData && c.auxData.isDefault) ctx.id = c.id;
      }
      if (method === 'Runtime.executionContextDestroyed' && params.executionContextId === ctx.id) ctx.id = null;
      if (method === 'Runtime.executionContextsCleared') ctx.id = null;
      if (method === 'Runtime.exceptionThrown') {
        const d = params.exceptionDetails || {};
        const m = (d.exception && (d.exception.description || d.exception.value)) || d.text || 'unknown';
        exceptions.push(String(m).slice(0, 300));
      }
      if (method === 'Page.javascriptDialogOpening') {
        dialogLog.push({ type: params.type, message: String(params.message || '').slice(0, 300), t: Date.now() });
        cdp.send('Page.handleJavaScriptDialog', { accept: true }).catch(() => {});
      }
    });

    /* route log in sessionStorage (instrumented before any page script) */
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

    /* ------------- helpers ------------- */
    async function ev(expr) {
      const params = { expression: expr, returnByValue: true, awaitPromise: false };
      if (ctx.id) params.contextId = ctx.id;
      const r = await cdp.send('Runtime.evaluate', params);
      if (r.exceptionDetails) {
        const m = (r.exceptionDetails.exception && r.exceptionDetails.exception.description) || r.exceptionDetails.text;
        throw new Error('eval failed: ' + m + ' :: ' + String(expr).slice(0, 160));
      }
      return r.result ? r.result.value : undefined;
    }
    async function evP(expr) { /* await promise results */
      const params = { expression: expr, returnByValue: true, awaitPromise: true };
      if (ctx.id) params.contextId = ctx.id;
      const r = await cdp.send('Runtime.evaluate', params);
      if (r.exceptionDetails) {
        const m = (r.exceptionDetails.exception && r.exceptionDetails.exception.description) || r.exceptionDetails.text;
        throw new Error('eval failed: ' + m + ' :: ' + String(expr).slice(0, 160));
      }
      return r.result ? r.result.value : undefined;
    }
    async function waitFor(fn, timeout, desc) {
      const t = timeout || 8000;
      const end = Date.now() + t;
      let last;
      while (Date.now() < end) {
        try { last = await fn(); if (last) return last; } catch (e) { last = undefined; }
        await sleep(150);
      }
      throw new Error('waitFor timeout: ' + (desc || '') + ' (last=' + JSON.stringify(last) + ')');
    }
    async function routeCount(route) {
      return ev(`(JSON.parse(sessionStorage.getItem('ocRouteLog')||'[]')).filter(e=>e.route===${JSON.stringify(route)}).length`);
    }
    async function settle(timeout) {
      await waitFor(async () => {
        const s = await ev(`(function(){var t=(document.getElementById('ocST')||{}).textContent||'';var b=document.getElementById('ocStopBtn');var bs=b?getComputedStyle(b).display:'none';return {t:t,bs:bs}})()`);
        return !/Thinking|Running shell|Compacting|Testing/.test(s.t) && s.bs === 'none';
      }, timeout || 30000, 'stream settle');
    }
    async function setInput(text) {
      await ev(`(function(){var el=document.getElementById('ocIn');el.value=${JSON.stringify(text)};el.dispatchEvent(new Event('input',{bubbles:true}));if(typeof autoResize==='function')autoResize(el);return true})()`);
    }
    async function shot(rel) {
      const r = await cdp.send('Page.captureScreenshot', { format: 'png' });
      evw(rel, Buffer.from(r.data, 'base64'));
      return path.join(EV, rel);
    }
    async function navigate(url) {
      await cdp.send('Page.navigate', { url });
      await waitFor(async () => await ev(`document.readyState`) === 'complete', 15000, 'load ' + url);
      await waitFor(async () => (await ev(`typeof forceUnlockAndRetry`)) === 'function', 10000, 'page script alive');
      await sleep(600);
    }
    async function fill(sel, text) {
      await ev(`(function(){var el=document.getElementById(${JSON.stringify(sel)});if(!el)return false;el.value=${JSON.stringify(text)};el.dispatchEvent(new Event('input',{bubbles:true}));return true})()`);
    }
    async function openSettings() {
      await ev(`document.getElementById('ocSetBtnF').click()`);
      await waitFor(async () => await ev(`document.getElementById('ocSetMod').classList.contains('open')`), 6000, 'settings open');
    }
    async function closeSettings() {
      await ev(`document.getElementById('ocSetC').click()`);
      await waitFor(async () => !(await ev(`document.getElementById('ocSetMod').classList.contains('open')`)), 6000, 'settings closed');
    }
    /* click "+ Add Connection", pick Custom, fill the four fields */
    async function addCustomForm(p) {
      await ev(`document.getElementById('ocAddCon').click()`);
      await waitFor(async () => await ev(`document.getElementById('ocConMod').classList.contains('open')`), 8000, 'connect modal open');
      const shown = await ev(`(function(){
        var s=document.getElementById('ocProvSel');s.value='custom';
        s.dispatchEvent(new Event('change',{bubbles:true}));
        return document.getElementById('ocBUG').style.display;
      })()`);
      if (shown !== 'block') throw new Error('custom fields did not show (ocBUG display=' + shown + ')');
      await fill('ocCustName', p.name);
      await fill('ocBU', p.base);
      await fill('ocAK', p.apiKey);
      await fill('ocCustModels', p.models);
      const vals = await ev(`(function(){
        return {n:document.getElementById('ocCustName').value,b:document.getElementById('ocBU').value,
                k:document.getElementById('ocAK').value,m:document.getElementById('ocCustModels').value};
      })()`);
      if (vals.n !== p.name || vals.b !== p.base || vals.k !== p.apiKey || vals.m !== p.models)
        throw new Error('form fill mismatch: ' + JSON.stringify(vals));
    }
    /* click Test Connection -> wait for the OK alert */
    async function testConnection() {
      const d0 = dialogLog.length;
      const r0 = await routeCount('test_connection');
      await ev(`document.getElementById('ocTst').click()`);
      await waitFor(async () => (await routeCount('test_connection')) > r0, 45000, 'test_connection route');
      await waitFor(async () => dialogLog.length > d0, 45000, 'test alert dialog');
      const msg = dialogLog[dialogLog.length - 1].message;
      await settle(10000);
      return msg;
    }
    /* click Connect -> settings_save + modal closes + provider row appears */
    async function saveConnection(name) {
      const r0 = await routeCount('settings_save');
      await ev(`document.getElementById('ocSav').click()`);
      await waitFor(async () => !(await ev(`document.getElementById('ocConMod').classList.contains('open')`)), 10000, 'connect modal closed after save');
      await waitFor(async () => (await routeCount('settings_save')) > r0, 10000, 'settings_save route');
      await waitFor(async () => {
        const t = await ev(`document.getElementById('ocPL').textContent`);
        return t.includes(name) && t.includes('Connected');
      }, 10000, 'provider row visible: ' + name);
    }
    /* open model dropdown, click the option containing needle, wait for start */
    async function pickModel(needle) {
      await ev(`document.getElementById('ocModelBtn').click()`);
      await waitFor(async () => await ev(`document.getElementById('ocModelDrop').classList.contains('open')`), 6000, 'model dropdown open');
      const s0 = await routeCount('start');
      const found = await ev(`(function(){
        var rows=Array.prototype.slice.call(document.querySelectorAll('#ocModelDrop .ocModelOpt'));
        var r=rows.filter(function(x){return x.textContent.indexOf(${JSON.stringify(needle)})!==-1})[0];
        if(!r)return false;r.click();return true;
      })()`);
      if (!found) throw new Error('model option not found in dropdown: ' + needle);
      await waitFor(async () => !(await ev(`document.getElementById('ocModelDrop').classList.contains('open')`)), 6000, 'dropdown closed after pick');
      await waitFor(async () => (await routeCount('start')) > s0, 10000, 'start route after model pick');
      return ev(`(document.getElementById('ocModelBtnT')||{textContent:''}).textContent`);
    }
    /* send a chat prompt and wait for stream end + settle; return assistant text.
       Transient upstream failures (502/503/429/network) get ONE retry — industry
       practice for external LLMs; every retry is recorded in the evidence. */
    async function chatOnce(prompt, waitMs) {
      const s0 = await routeCount('ai_chat_stream');
      await setInput(prompt);
      await ev(`document.getElementById('ocSend').click()`);
      await waitFor(async () => (await routeCount('ai_chat_stream')) > s0, waitMs, 'ai_chat_stream done');
      await settle(waitMs);
      return ev(`(function(){
        var ms=document.querySelectorAll('#ocCI .ocMsg.a');
        if(!ms.length)return '';
        var m=ms[ms.length-1];
        return (m.innerText||m.textContent||'').trim();
      })()`);
    }
    async function lastAssistantText() {
      try {
        return await ev(`(function(){
          var ms=document.querySelectorAll('#ocCI .ocMsg.a');
          if(!ms.length)return '';
          var m=ms[ms.length-1];
          return (m.innerText||m.textContent||'').trim();
        })()`);
      } catch (e) { return ''; }
    }
    /* if the stream outlived the budget, do what a real user would: press Stop
       (releases the per-conversation generation lock for the NEXT provider) */
    async function stopIfRunning() {
      try {
        const vis = await ev(`(function(){var b=document.getElementById('ocStopBtn');return !!(b&&getComputedStyle(b).display!=='none')})()`);
        if (!vis) return;
        await ev(`document.getElementById('ocStopBtn').click()`);
        await sleep(1500);
        try { await settle(45000); } catch (e) { /* note below via caller */ }
        notes.push('user pressed Stop: stream had not finished within the chat budget');
      } catch (e) {
        notes.push('stop attempt failed: ' + String(e.message || e).slice(0, 120));
      }
    }
    async function chat(prompt, waitMs) {
      chatAttempts = 1;
      let reply = '';
      try {
        reply = await chatOnce(prompt, waitMs);
      } catch (e) {
        notes.push('chat attempt 1 did not finish in ' + waitMs + 'ms: ' +
          String(e.message || e).replace(/\s+/g, ' ').slice(0, 200));
        await stopIfRunning();
        reply = await lastAssistantText();
      }
      if (/HTTP (502|503|429)|Cannot reach AI endpoint|invalid session token/.test(reply || '')) {
        notes.push('chat attempt 1 hit transient upstream — retrying once: ' + String(reply).replace(/\s+/g, ' ').slice(0, 160));
        await sleep(2500);
        chatAttempts = 2;
        try {
          reply = await chatOnce(prompt, waitMs);
        } catch (e) {
          notes.push('chat attempt 2 did not finish in ' + waitMs + 'ms: ' +
            String(e.message || e).replace(/\s+/g, ' ').slice(0, 200));
          await stopIfRunning();
          reply = await lastAssistantText();
        }
      }
      return reply || '';
    }

    /* =================================================================
       R1 — Provider A (ngrok Ollama): add via clicks, Test, Save, chat
       ================================================================= */
    await navigate(BASE + '/');
    await settle(30000);

    await openSettings();
    await addCustomForm(PROV_A);
    const alertA = await testConnection();
    rec('R1.1', 'Test Connection (ngrok Ollama) alert says OK', /^OK/.test(alertA), 'alert: ' + alertA);

    await saveConnection(PROV_A.name);
    await shot(PROV_A.provShot);
    const provRowA = await ev(`document.getElementById('ocPL').textContent`);
    rec('R1.2', 'Provider A saved & listed Connected in settings',
      provRowA.includes(PROV_A.name) && provRowA.includes('Connected') && provRowA.includes('2 models'),
      'row text snippet: ' + provRowA.replace(/\s+/g, ' ').slice(0, 220));

    await closeSettings();
    const labelA = await pickModel(PROV_A.needle);
    rec('R1.3', 'Model ' + PROV_A.modelId + ' selected from dropdown',
      true, 'button label: ' + labelA);

    const replyA = await chat(PROV_A.prompt, PROV_A.chatWait);
    await shot(PROV_A.chatShot);
    rec('R1.4', 'Real streaming chat reply from Provider A (contains OLLAMA CONNECTED OK)',
      replyOk(replyA, PROV_A), 'reply(' + replyA.length + ' chars): ' + replyA.slice(0, 300));

    /* =================================================================
       R2 — Provider B (localhost Mission): add via clicks, Test, Save, chat
       ================================================================= */
    await openSettings();
    await addCustomForm(PROV_B);
    const alertB = await testConnection();
    rec('R2.1', 'Test Connection (Mission localhost:3000) alert says OK', /^OK/.test(alertB), 'alert: ' + alertB);

    await saveConnection(PROV_B.name);
    await shot(PROV_B.provShot);
    const provRowB = await ev(`document.getElementById('ocPL').textContent`);
    rec('R2.2', 'Provider B saved & listed Connected in settings',
      provRowB.includes(PROV_B.name) && provRowB.includes('Connected') && provRowB.includes('2 models'),
      'row text snippet: ' + provRowB.replace(/\s+/g, ' ').slice(0, 220));

    await closeSettings();
    const labelB = await pickModel(PROV_B.needle);
    rec('R2.3', 'Model ' + PROV_B.modelId + ' selected from dropdown',
      true, 'button label: ' + labelB);

    const replyB = await chat(PROV_B.prompt, PROV_B.chatWait);
    await shot(PROV_B.chatShot);
    rec('R2.4', 'Real streaming chat reply from Provider B (Bengali, non-error)',
      replyOk(replyB, PROV_B), 'reply(' + replyB.length + ' chars): ' + replyB.slice(0, 300));

    /* =================================================================
       R3 — contract/security checks over the whole run
       ================================================================= */
    /* both providers exposed to the UI, API keys NOT leaked */
    const leak = await evP(`(function(){
      var h=document.documentElement.outerHTML;
      var a=(h.match(/var A='([^']+)'/)||[])[1]||'';
      var c=(h.match(/,C='([a-f0-9]+)'/)||[])[1]||'';
      if(!a||!c)return Promise.resolve({err:'could not extract A/C from page'});
      return fetch(a+'&ai_php_api=providers&csrf_token='+c).then(function(r){
        return r.text().then(function(t){
          return {status:r.status,
            hasA:t.indexOf(${JSON.stringify(PROV_A.base)})!==-1,
            hasB:t.indexOf(${JSON.stringify(PROV_B.base)})!==-1,
            keyA:${PROV_A.apiKey ? ('t.indexOf(' + JSON.stringify(PROV_A.apiKey) + ')!==-1') : 'null'},
            keyB:${PROV_B.apiKey ? ('t.indexOf(' + JSON.stringify(PROV_B.apiKey) + ')!==-1') : 'null'},
            len:t.length};
        });
      }).catch(function(e){return {err:String(e)}});
    })()`);
    rec('R3.1', 'providers JSON: both base_urls present, api keys masked',
      !leak.err && leak.status === 200 && leak.hasA && leak.hasB && !leak.keyA && !leak.keyB,
      JSON.stringify(leak));

    /* route log integrity */
    const logRaw = await ev(`sessionStorage.getItem('ocRouteLog')||'[]'`);
    const routeLog = JSON.parse(logRaw || '[]');
    const counts = {};
    let non200 = [];
    for (const e of routeLog) {
      counts[e.route] = (counts[e.route] || 0) + 1;
      if (e.status !== 200) non200.push({ route: e.route, status: e.status });
    }
    const needRoutes = ['start', 'settings_load', 'settings_save', 'test_connection', 'providers', 'ai_chat_stream'];
    const missing = needRoutes.filter(r => !counts[r]);
    rec('R3.2', 'all expected routes fired, every response HTTP 200',
      !missing.length && !non200.length,
      'counts=' + JSON.stringify(counts) + ' missing=' + JSON.stringify(missing) + ' non200=' + JSON.stringify(non200));

    /* dialogs: exactly the two Test-Connection OK alerts */
    const okAlerts = dialogLog.filter(d => d.type === 'alert' && /^OK/.test(d.message)).length;
    rec('R3.3', 'two Test-Connection OK dialogs observed',
      okAlerts === 2 && dialogLog.length === 2,
      'dialogs=' + JSON.stringify(dialogLog.map(d => d.type + ':' + d.message)));

    rec('R3.4', 'zero JavaScript exceptions in the page', exceptions.length === 0,
      exceptions.slice(0, 3).join(' | ') || 'none');

    const uiMd5 = md5(UI);
    rec('R3.5', 'index.php untouched (md5 still ' + KNOWN_MD5 + ')', uiMd5 === KNOWN_MD5, 'md5=' + uiMd5);

    /* =============== evidence write-up =============== */
    const elapsed = ((Date.now() - t0) / 1000).toFixed(1);
    const md = [
      '# Real Provider Click-Through Test (labels: R*)',
      '',
      'Bismillah. All provider operations below were performed **through the UI in a real',
      'headless Chrome** (CDP): clicks on Settings -> Add Connection -> Custom -> fill ->',
      '`Test Connection` -> `Connect`, then model dropdown pick and `Send` for chat.',
      '',
      '- Server: fresh instance, port ' + PORT + ' (OC_DATA=' + DATA + ')',
      '- Chrome: headless, CDP port ' + CHROME_PORT,
      '- Provider A: ' + PROV_A.name + ' — ' + PROV_A.base + ' (Ollama proxy, key: ' + PROV_A.apiKey + ')',
      '- Provider B: ' + PROV_B.name + ' — ' + PROV_B.base + ' (Mission Barisal, key: ' + PROV_B.apiKey + ')',
      '- Elapsed: ' + elapsed + 's',
      '- Chat attempts (A/B): ' + chatAttempts + ' each max; retries: ' + (notes.length ? notes.join(' || ') : 'none'),
      '',
      '## Results',
      '',
      '| id | test | result | note |',
      '|----|------|--------|------|',
      ...results.map(r => '| ' + r.id + ' | ' + r.title + ' | ' + (r.ok ? '✅ PASS' : '❌ FAIL') + ' | ' + r.note.replace(/\|/g, '\\|').slice(0, 400) + ' |'),
      '',
      '## Route log (main tab)',
      '',
      '| route | count |',
      '|-------|-------|',
      ...Object.entries(counts).sort().map(([k, v]) => '| ' + k + ' | ' + v + ' |'),
      '',
      '- non-200 responses: ' + (non200.length ? JSON.stringify(non200) : 'none'),
      '- dialogs: ' + JSON.stringify(dialogLog),
      '- JS exceptions: ' + (exceptions.length ? JSON.stringify(exceptions.slice(0, 5)) : 'none'),
      '',
      '## Chat replies (real LLM output)',
      '',
      '### A — ' + PROV_A.name + ' (' + PROV_A.modelId + ')',
      '',
      'Prompt: `' + PROV_A.prompt + '`',
      '',
      '> ' + replyA.replace(/\n/g, '\n> ').slice(0, 1200),
      '',
      '### B — ' + PROV_B.name + ' (' + PROV_B.modelId + ')',
      '',
      'Prompt: `' + PROV_B.prompt + '`',
      '',
      '> ' + replyB.replace(/\n/g, '\n> ').slice(0, 1200),
      '',
      '## Screenshots',
      '',
      '- `REAL/R1_provider_saved.png` — Provider A row Connected in settings',
      '- `REAL/R1_chat.png` — chat streamed from Provider A',
      '- `REAL/R2_provider_saved.png` — Provider B row Connected in settings',
      '- `REAL/R2_chat.png` — chat streamed from Provider B',
      '',
      '## Test notes (honest)',
      '',
      '- `<select>` option (`Custom Provider`) is chosen by setting `value` + firing `change` —',
      '  native OS menus are not drivable via CDP; every *button* (Test, Connect, Send, model',
      '  option rows, modal open/close) was a real `.click()`.',
      '- Text fields are filled by setting `.value` + `input` event (same state a typed value produces).',
      '- `alert()` dialogs are auto-accepted by the CDP `Page.javascriptDialogOpening` handler and logged.',
      '- **Honest finding (run 1):** Provider B was first saved with a placeholder key',
      '  (`sk-mission-local-secret`); Test Connection passed (GET `/v1/models` is open) but chat',
      '  failed `HTTP 401 invalid session token` — the Mission proxy rejects unknown bearer tokens',
      '  on `POST /v1/chat/completions` (probe: fake key -> 401, no key -> 200). Run 1 falsely',
      '  showed PASS because the assertion only checked reply length; the assertion was tightened',
      '  (error-pattern rejection + expected-token match) and Provider B re-configured with an',
      '  empty API key (its authentic config). Run 2 results are below.',
      '- R3.1 key-masking evidence therefore rests on Provider A\'s key (`keyA` absent from the',
      '  providers JSON); `keyB` was intentionally never stored.',
    ].join('\n');
    evw('REAL/REAL_PROVIDER.md', md + '\n');
    evw('REAL_RESULTS.json', JSON.stringify({
      results, counts, non200, dialogLog, exceptions, notes, chatAttempts,
      replies: { A: replyA, B: replyB }, elapsed_s: Number(elapsed)
    }, null, 2));

    const passed = results.filter(r => r.ok).length;
    console.log('\n=== real provider click-through: ' + passed + '/' + results.length + ' PASS (' + elapsed + 's) ===');
    if (passed !== results.length) process.exitCode = 1;
  } catch (err) {
    console.error('\n❌ RUN FAILED: ' + (err && err.stack || err));
    try { evw('REAL/REAL_PROVIDER_ERROR.txt', String(err && err.stack || err) + '\n'); } catch (e) {}
    process.exitCode = 1;
  } finally {
    if (chrome) await stopProc(chrome.child);
    if (srv) await stopProc(srv);
    try { fs.rmSync(CHROME_DIR, { recursive: true, force: true }); } catch (e) {}
  }
})();
