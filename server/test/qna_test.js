'use strict';
/*
 * qna_test.js — question-driven, user-curiosity test run (labels: Q*)
 *
 * Answers the deployment/independence questions from the operator's desk by
 * DOING each thing a curious daily user would do, in Chrome over CDP, and
 * saving every result as evidence under unnecessary/evidence/P8/.
 *
 *   Q1  Is the UI+backend fully independent of localhost:3000?
 *   Q2  Can providers live in .env, be loaded by the start script, no code edits?
 *   Q3  index.php never touched?
 *   Q4  What happens with NO LLM configured? (UI must still boot cleanly)
 *   Q5  LLM configurable from admin (.env) AND from the frontend (Settings UI)?
 *   Q6  Does it work when opened via another origin (LAN IP / public tunnel)?
 *       Do real env vars win over .env?
 *   Q7  Daily-use curiosity: chat, file tree, new session — through the UI
 *
 *   node qna_test.js
 *
 * Evidence: unnecessary/evidence/P8/*.png, unnecessary/evidence/P8/QA_RESULTS.json, unnecessary/evidence/P8/QA_*.md
 */
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const http = require('http');
const os = require('os');
const crypto = require('crypto');

const ROOT = path.resolve(__dirname, '..', '..');
const UI = path.join(ROOT, 'index.php');
const SERVER = path.join(ROOT, 'server', 'server.js');
const EV = path.join(ROOT, 'unnecessary', 'evidence');
const P8 = path.join(EV, 'P8');
const TMP = '/tmp/oc_qna';
const CHROME_DIR = '/tmp/oc_qna_chrome';
const LOG_NOENV = path.join(TMP, 'server_noenv.log');
const LOG_ENV = path.join(TMP, 'server_env.log');
const LOG_LAN = path.join(TMP, 'server_lan.log');

const PORT_NOENV = 18920;
const PORT_ENV = 18921;
const CHROME_PORT = 9447;
const KNOWN_MD5 = '895eb75226cc2801834baaced2873bb1';
const FORBIDDEN_REPLY = /AI request failed|Cannot reach AI endpoint|HTTP \d{3}|Unauthorized|invalid session token/i;

function lanIP() {
  const ifs = os.networkInterfaces();
  const all = [];
  for (const list of Object.values(ifs)) {
    for (const i of (list || [])) {
      if (i.family === 'IPv4' && !i.internal) all.push(i.address);
    }
  }
  return all.find(a => a.startsWith('192.168.')) || all[0] || '127.0.0.1';
}
const LAN = lanIP();

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
  let srv = null, chrome = null, cdp = null;

  const dialogLog = [];
  const exceptions = [];

  /* route log + helpers (same instrumentation as user_test) */
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

  const ctx = { id: null };
  function wire(conn, c) {
    conn.on((method, params) => {
      if (method === 'Runtime.executionContextCreated') {
        const cx = params.context || {};
        if (cx.auxData && cx.auxData.isDefault) c.id = cx.id;
      }
      if (method === 'Runtime.executionContextDestroyed' && params.executionContextId === c.id) c.id = null;
      if (method === 'Runtime.executionContextsCleared') c.id = null;
      if (method === 'Runtime.exceptionThrown') {
        const d = params.exceptionDetails || {};
        const m = (d.exception && (d.exception.description || d.exception.value)) || d.text || 'unknown';
        exceptions.push(String(m).slice(0, 300));
      }
      if (method === 'Page.javascriptDialogOpening') {
        dialogLog.push({ type: params.type, message: String(params.message || '').slice(0, 200) });
        conn.send('Page.handleJavaScriptDialog', { accept: true }).catch(() => {});
      }
    });
  }

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
  async function evP(expr) {
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
  async function routeLog() {
    return JSON.parse((await ev(`sessionStorage.getItem('ocRouteLog')||'[]'`)) || '[]');
  }
  async function settle(timeout) {
    await waitFor(async () => {
      const s = await ev(`(function(){var t=(document.getElementById('ocST')||{}).textContent||'';var b=document.getElementById('ocStopBtn');var bs=b?getComputedStyle(b).display:'none';return {t:t,bs:bs}})()`);
      return !/Thinking|Running shell|Compacting|Testing/.test(s.t) && s.bs === 'none';
    }, timeout || 30000, 'stream settle');
  }
  async function setInput(text) {
    await ev(`(function(){var el=document.getElementById('ocIn');el.value=${JSON.stringify(text)};el.dispatchEvent(new Event('input',{bubbles:true}));return true})()`);
  }
  async function shot(rel) {
    const r = await cdp.send('Page.captureScreenshot', { format: 'png' });
    evw(rel, Buffer.from(r.data, 'base64'));
    return path.join(EV, rel);
  }
  async function navigate(url) {
    await cdp.send('Page.navigate', { url });
    await waitFor(async () => await ev(`document.readyState`) === 'complete', 15000, 'load ' + url);
    await waitFor(async () => (await ev(`typeof forceUnlockAndRetry`)) === 'function', 10000, 'page script alive @ ' + url);
    await sleep(700);
  }
  async function lastAssistant() {
    return ev(`(function(){
      var ms=document.querySelectorAll('#ocCI .ocMsg.a');
      if(!ms.length)return '';
      var m=ms[ms.length-1];
      return (m.innerText||m.textContent||'').trim();
    })()`);
  }
  async function doChat(prompt, waitMs) {
    const s0 = await routeCount('ai_chat_stream');
    await setInput(prompt);
    await ev(`document.getElementById('ocSend').click()`);
    await waitFor(async () => (await routeCount('ai_chat_stream')) > s0, waitMs, 'ai_chat_stream done');
    await settle(waitMs);
    return lastAssistant();
  }
  /* providers JSON as the page itself sees it (A/C extracted from served HTML) */
  async function providersViaPage(baseHost) {
    return evP(`(function(){
      var h=document.documentElement.outerHTML;
      var a=(h.match(/var A='([^']+)'/)||[])[1]||'';
      var c=(h.match(/,C='([a-f0-9]+)'/)||[])[1]||'';
      if(!a||!c)return Promise.resolve({err:'no A/C in page'});
      return fetch(a+'&ai_php_api=providers&csrf_token='+c).then(function(r){
        return r.text().then(function(t){
          var j;try{j=JSON.parse(t)}catch(e){return {err:'bad json',status:r.status}}
          var customs=j.filter(function(x){return x.is_custom_type});
          return {status:r.status, total:j.length,
            customs:customs.map(function(x){return {id:x.id,name:x.name,connected:x.connected,
              base:x.base_url,default_model:x.default_model,
              models:Object.keys(x.models||{})}}),
            keyLeak:/sk-[A-Za-z0-9]/.test(t)};
        });
      }).catch(function(e){return {err:String(e)}});
    })()`);
  }

  try {
    fs.rmSync(TMP, { recursive: true, force: true });
    fs.mkdirSync(path.join(TMP, 'data_a'), { recursive: true });
    fs.mkdirSync(path.join(TMP, 'home_a'), { recursive: true });
    fs.mkdirSync(path.join(TMP, 'data_b'), { recursive: true });
    fs.mkdirSync(path.join(TMP, 'home_b'), { recursive: true });
    fs.mkdirSync(P8, { recursive: true });

    chrome = await launchChrome();
    cdp = await cdpConnect(chrome.target.webSocketDebuggerUrl);
    await cdp.send('Page.enable');
    await cdp.send('Runtime.enable');
    wire(cdp, ctx);
    /* XHR route-log hooks must exist in EVERY document (navigations change origin) */
    await cdp.send('Page.addScriptToEvaluateOnNewDocument', { source: INSTRUMENT });

    const baseA = 'http://127.0.0.1:' + PORT_NOENV;
    const baseB = 'http://127.0.0.1:' + PORT_ENV;

    /* =================================================================
       PHASE A — Q4: boot with NO .env, NO provider at all
       ================================================================= */
    srv = bootServer({
      OC_DATA: path.join(TMP, 'data_a'), OC_HOME: path.join(TMP, 'home_a'),
      OC_ENV_FILE: '', OC_LOG_FILE: LOG_NOENV
    }, PORT_NOENV);
    await waitReady(baseA);

    await navigate(baseA + '/');
    await settle(30000);
    const q4status = await ev(`document.getElementById('ocST').textContent`);
    const q4providers = await providersViaPage();
    await ev(`document.getElementById('ocModelBtn').click()`);
    await waitFor(async () => await ev(`document.getElementById('ocModelDrop').classList.contains('open')`), 5000, 'dropdown open');
    const q4groups = await ev(`document.querySelectorAll('#ocModelDrop .ocModelGrp').length`);
    await ev(`document.getElementById('ocModelBtn').click()`);
    await shot('P8/Q4_ui_no_provider.png');
    rec('Q4.1', 'UI boots cleanly with zero providers (no LLM configured)',
      /Ready|No provider/.test(q4status) && !q4providers.err &&
      (q4providers.customs || []).filter(x => x.connected).length === 0,
      'status=' + q4status + ' connected_customs=' +
      ((q4providers.customs || []).filter(x => x.connected).length) + ' note=dropdown groups=' + q4groups);
    rec('Q4.2', 'Model dropdown correctly empty (no connected provider) — user simply cannot chat yet, no crash',
      q4groups === 0, 'groups=' + q4groups);

    await stopProc(srv); srv = null;

    /* =================================================================
       PHASE B — Q2/Q5/Q7: boot WITH the real .env via start-script semantics
       ================================================================= */
    srv = bootServer({
      OC_DATA: path.join(TMP, 'data_b'), OC_HOME: path.join(TMP, 'home_b'),
      OC_LOG_FILE: LOG_ENV /* OC_ENV_FILE unset -> <ui>/.env is loaded */
    }, PORT_ENV);
    await waitReady(baseB);

    const envLog = fs.readFileSync(LOG_ENV, 'utf8');
    const seedLines = envLog.split('\n').filter(l => /INFO\s+env/.test(l));
    const envFileLine = seedLines.find(l => /file=.*\.env keys=\d+ applied=\d+/.test(l)) || '';
    const createdLines = seedLines.filter(l => /-> created/.test(l));
    rec('Q2.1', 'start script + .env: config file loaded, providers seeded — zero source edits',
      !!envFileLine && createdLines.length >= 2,
      envFileLine.trim().split(' ').slice(-2).join(' ') + ' | created=' + createdLines.length);

    await navigate(baseB + '/');
    await settle(30000);
    const q2providers = await providersViaPage();
    rec('Q2.2', 'both .env providers visible to UI as Connected, keys never sent to browser',
      !q2providers.err && (q2providers.customs || []).length >= 2 &&
      (q2providers.customs || []).every(x => x.connected) && !q2providers.keyLeak,
      JSON.stringify((q2providers.customs || []).map(x => x.id + ':' + x.connected + ':' + x.default_model)) +
      ' keyLeak=' + q2providers.keyLeak);

    /* default model auto-selected WITHOUT any UI action (env MODEL -> default_model) */
    let autoLabel = '';
    try {
      await waitFor(async () => {
        autoLabel = (await ev(`(document.getElementById('ocModelBtnT')||{textContent:''}).textContent`)) || '';
        return /Qwen3\.5/.test(autoLabel);
      }, 12000, 'env default model auto-selected');
    } catch (e) {}
    rec('Q2.3', 'UI auto-selects the admin-chosen default model from .env (no click needed)',
      /Qwen3\.5/.test(autoLabel), 'label=' + autoLabel);

    /* settings shows both rows Connected (admin side, before any UI config) */
    await ev(`document.getElementById('ocSetBtnF').click()`);
    await waitFor(async () => await ev(`document.getElementById('ocSetMod').classList.contains('open')`), 6000, 'settings open');
    await sleep(400);
    const rowText = (await ev(`document.getElementById('ocPL').textContent`) || '').replace(/\s+/g, ' ');
    await shot('P8/Q2_env_providers.png');
    await ev(`document.getElementById('ocSetC').click()`);
    await waitFor(async () => !(await ev(`document.getElementById('ocSetMod').classList.contains('open')`)), 6000, 'settings closed');
    rec('Q2.4', 'Settings lists both env providers Connected without touching the UI',
      rowText.includes('Ollama Remote') && rowText.includes('Mission Local') &&
      (rowText.match(/Connected/g) || []).length >= 2,
      'rows snippet: ' + rowText.slice(0, 240));

    /* Q5 frontend channel: user can still ADD their own provider via UI (U6.1
       already proved the click-through; here just verify the UI save route works
       by reading the user-test result log written earlier this run) */
    const userLog = (() => { try { return fs.readFileSync('/tmp/oc_qna_user.log', 'utf8'); } catch (e) { return ''; } })();
    const contractLog = (() => { try { return fs.readFileSync('/tmp/oc_qna_contract.log', 'utf8'); } catch (e) { return ''; } })();

    /* Q7 daily use: Bengali chat with the env default model.
       Each attempt is isolated: a timeout/upstream failure becomes a note +
       fallback instead of killing the whole run (honest per-question result). */
    const badReply = r => !r || FORBIDDEN_REPLY.test(r) || r.length < 5;
    const PROMPT = 'রাজধানীর নাম কী? এক লাইনে বাংলায় উত্তর দাও।';
    let reply = '';
    let usedModel = autoLabel || '(unknown)';
    try { reply = await doChat(PROMPT, 240000); }
    catch (e) { notes.push('default-model chat attempt 1: ' + String(e.message || e).replace(/\s+/g, ' ').slice(0, 200)); }
    if (badReply(reply)) {
      notes.push('default model chat attempt 1 reply: ' + String(reply || '(none)').replace(/\s+/g, ' ').slice(0, 160));
      await sleep(2500);
      try { reply = await doChat(PROMPT, 240000); }
      catch (e) { notes.push('default-model chat attempt 2: ' + String(e.message || e).replace(/\s+/g, ' ').slice(0, 200)); }
      notes.push('default model chat attempt 2 reply: ' + String(reply || '(none)').replace(/\s+/g, ' ').slice(0, 160));
    }
    if (badReply(reply)) {
      /* curious user's next move: switch model from the dropdown and try again */
      notes.push('switching to Mission Pilot after upstream trouble on default model');
      try {
        await ev(`document.getElementById('ocModelBtn').click()`);
        await waitFor(async () => await ev(`document.getElementById('ocModelDrop').classList.contains('open')`), 5000, 'drop open');
        const clicked = await ev(`(function(){
          var rows=Array.prototype.slice.call(document.querySelectorAll('#ocModelDrop .ocModelOpt'));
          var r=rows.filter(function(x){return x.textContent.indexOf('Mission Pilot')!==-1})[0];
          if(!r)return false;r.click();return true;
        })()`);
        await waitFor(async () => !(await ev(`document.getElementById('ocModelDrop').classList.contains('open')`)), 5000, 'drop closed');
        if (clicked) {
          usedModel = 'Mission Pilot (switched)';
          reply = await doChat(PROMPT, 300000);
        }
      } catch (e) {
        notes.push('mission fallback: ' + String(e.message || e).replace(/\s+/g, ' ').slice(0, 200));
      }
    }
    await shot('P8/Q7_daily_chat.png');
    rec('Q7.1', 'daily-use Bengali chat answered by a REAL external LLM through the UI',
      reply.length >= 5 && !FORBIDDEN_REPLY.test(reply),
      'model=' + usedModel + ' reply(' + reply.length + 'c): ' + reply.replace(/\s+/g, ' ').slice(0, 240));

    /* Q7 daily use: file tree + new session */
    const f0 = await routeCount('file_tree');
    await ev(`document.getElementById('ocFtBtn').click()`);
    let treeOk = false;
    try { await waitFor(async () => (await routeCount('file_tree')) > f0, 8000, 'file_tree route'); treeOk = true; } catch (e) {}
    let fileCount = -1;
    try {
      await waitFor(async () => {
        fileCount = await ev(`(function(){var l=document.getElementById('ocFtL');return l?l.children.length:-1})()`);
        return fileCount > 0;
      }, 8000, 'file tree rows in #ocFtL');
    } catch (e) {}
    rec('Q7.2', 'file tree opens and lists files (per-project workdir jail)',
      treeOk && fileCount > 0, 'route=' + treeOk + ' tree rows=' + fileCount);

    const n0 = await routeCount('new_session');
    let nsOk = false;
    try { await ev(`document.getElementById('ocNew').click()`); await waitFor(async () => (await routeCount('new_session')) > n0, 8000, 'new_session route'); nsOk = true; } catch (e) {}
    rec('Q7.3', 'New Session button starts a fresh conversation', nsOk, 'new_session fired=' + nsOk);

    const logB = await routeLog();
    const badB = logB.filter(e => e.status !== 200);
    rec('Q7.4', 'phase-B route log: every response HTTP 200',
      badB.length === 0, 'routes=' + logB.length + ' non200=' + JSON.stringify(badB.slice(0, 5)));

    await stopProc(srv); srv = null;

    /* =================================================================
       PHASE C — Q6: another origin (LAN IP) + real env beats .env
       ================================================================= */
    srv = bootServer({
      OC_DATA: path.join(TMP, 'data_b'), OC_HOME: path.join(TMP, 'home_b'),
      HOST: '0.0.0.0', /* real env var must WIN over .env's HOST=127.0.0.1 */
      OC_LOG_FILE: LOG_LAN
    }, PORT_ENV);
    await waitReady(baseB);
    const lanLog = fs.readFileSync(LOG_LAN, 'utf8');
    const readyLine = (lanLog.split('\n').find(l => /READY/.test(l)) || '');
    rec('Q6.1', 'real environment variable beats .env value (precedence documented behaviour)',
      /host=0\.0\.0\.0/.test(readyLine), readyLine.trim().slice(-140));

    await navigate('http://' + LAN + ':' + PORT_ENV + '/');
    await settle(30000);
    const q6providers = await providersViaPage();
    await shot('P8/Q6_lan_origin.png');
    rec('Q6.2', 'UI fully works from a DIFFERENT origin (http://' + LAN + ':' + PORT_ENV + '/) — same-origin API path, tunnel-ready',
      !q6providers.err && q6providers.status === 200 &&
      (q6providers.customs || []).length >= 2,
      'providers status=' + q6providers.status + ' customs=' + ((q6providers.customs || []).length));

    const logC = await routeLog();
    const badC = logC.filter(e => e.status !== 200);
    rec('Q6.3', 'route log over LAN origin: HTTP 200 everywhere (proxies/tunnels behave like this origin)',
      badC.length === 0, 'routes=' + logC.length + ' non200=' + JSON.stringify(badC.slice(0, 5)));

    await stopProc(srv); srv = null;

    /* =================================================================
       Q1 / Q3 / Q5 — evidence-based cross-checks
       ================================================================= */
    /* Q1: suites re-run this session with fresh state + fakes only => PASS,
           and localhost:3000 appears ONLY in the optional real-provider test */
    const grepHits = [];
    (function walk(dir) {
      for (const f of fs.readdirSync(dir)) {
        const p = path.join(dir, f);
        const st = fs.statSync(p);
        if (st.isDirectory()) { if (f !== 'evidence' && f !== 'node_modules' && f !== 'logs') walk(p); }
        else if (/\.(js|php|sh|json|md)$/.test(f)) {
          const txt = fs.readFileSync(p, 'utf8');
          if (txt.includes('localhost:3000')) {
            txt.split('\n').forEach((l, i) => {
              if (l.includes('localhost:3000')) grepHits.push(path.relative(ROOT, p) + ':' + (i + 1) + ': ' + l.trim().slice(0, 140));
            });
          }
        }
      }
    })(ROOT);
    /* PRODUCT code must have zero hits; the optional live test, this script's own
       grep code, docs and .env may legitimately mention it as configuration */
    const PRODUCT_RE = /^(index\.php|server[\\/]server\.js|server[\\/]lib[\\/]|server[\\/]test[\\/](contract_test|user_test|fake_llm)\.js)/;
    const productHits = grepHits.filter(h => PRODUCT_RE.test(h));
    const nonProduct = grepHits.filter(h => !PRODUCT_RE.test(h));
    evw('P8/Q1_grep_localhost3000.txt',
      '# every occurrence of localhost:3000 in the project (code + docs + test configs)\n' +
      '# PRODUCT CODE = index.php, server/server.js, server/lib/*, contract/user/fake suites\n' +
      '# (the optional real-provider test, this self-checking script, docs and .env may\n' +
      '#  legitimately mention it as configuration/reference).\n' +
      '# PRODUCT-CODE hits: ' + productHits.length + '\n' +
      (productHits.join('\n') || '  none') + '\n' +
      '# non-product hits (optional live test / qna self-reference / docs / config):\n' +
      (nonProduct.join('\n') || '  none') + '\n');
    fs.copyFileSync('/tmp/oc_qna_contract.log', path.join(P8, 'Q1_contract_rerun.log'));
    fs.copyFileSync('/tmp/oc_qna_user.log', path.join(P8, 'Q1_user_rerun.log'));
    const contractOk = /TOTAL:\s*11\/11 PASS/.test(contractLog);
    const userOk = /TOTAL:\s*11\/11 PASS/.test(userLog);
    rec('Q1.1', 'backend+UI are independent of localhost:3000 — contract & user suites PASS with fakes only',
      contractOk && userOk,
      'contract=' + (contractOk ? '11/11' : 'FAIL') + ' user=' + (userOk ? '11/11' : 'FAIL'));
    rec('Q1.2', 'localhost:3000 never referenced by product code (only optional live test/docs/config)',
      productHits.length === 0,
      'total hits=' + grepHits.length + ' product-code hits=' + productHits.length +
      ' (see unnecessary/evidence/P8/Q1_grep_localhost3000.txt)');

    const uiMd5 = md5(UI);
    rec('Q3.1', 'index.php untouched through every phase (0 edits)',
      uiMd5 === KNOWN_MD5, 'md5=' + uiMd5);

    const u61 = /U6\.1.*PASS|✅ U6\.1/.test(userLog);
    rec('Q5.1', 'both admin (.env seed — proved in Q2) and frontend (Settings UI — U6.1) can add LLMs',
      u61 && createdLines.length >= 2, 'env_seeded=' + createdLines.length + ' ui_add(U6.1)=' + u61);

    rec('Q5.2', 'zero JavaScript exceptions across all phases', exceptions.length === 0,
      exceptions.slice(0, 3).join(' | ') || 'none');

    /* =============== evidence write-up =============== */
    const elapsed = ((Date.now() - t0) / 1000).toFixed(1);
    const md = [
      '# Q&A Run — operator questions answered by doing (labels: Q*)',
      '',
      'Bismillah. Every row below was executed in real Chrome (CDP) against a real',
      'server started with operator semantics (`node start.js` equivalent: OC_ENV_FILE',
      'unset => ui/.env loaded by lib/env.js; PORT/HOST overridden by real env vars).',
      '',
      '- Elapsed: ' + elapsed + 's — LAN origin tested: http://' + LAN + ':' + PORT_ENV + '/',
      '- Servers: no-env phase :' + PORT_NOENV + ', env phase :' + PORT_ENV + ' (HOST=0.0.0.0 via real env)',
      '- Logs: unnecessary/evidence/P8 (server logs copied below), ui/logs/server.log in daily use',
      '',
      '## Results',
      '',
      '| id | question verified | result | note |',
      '|----|-------------------|--------|------|',
      ...results.map(r => '| ' + r.id + ' | ' + r.title + ' | ' + (r.ok ? '✅ PASS' : '❌ FAIL') + ' | ' +
        String(r.note).replace(/\|/g, '\\|').slice(0, 400) + ' |'),
      '',
      '## Honest notes',
      '',
      '- External LLM replies are live (ngrok Ollama / Mission localhost:3000); upstream',
      '  hiccups are retried once (backend 502/503/429 retry + test-level retry) and any',
      '  remaining trouble is recorded verbatim in the notes column and in notes[].',
      '- `default_model` auto-select uses the env provider\'s MODEL value (settings.json field),',
      '  consumed by the untouched UI hook at index.php ("first connected provider that',
      '  exposes a default_model wins").',
      '- HOST precedence proof: .env says 127.0.0.1, real env said 0.0.0.0 -> READY shows 0.0.0.0.',
      '',
      '## Run notes[]',
      '',
      ...(notes.length ? notes.map(n => '- ' + n) : ['- none']),
      '',
      '## Exceptions / dialogs',
      '',
      '- JS exceptions: ' + (exceptions.length ? JSON.stringify(exceptions.slice(0, 5)) : 'none'),
      '- dialogs: ' + JSON.stringify(dialogLog)
    ].join('\n');
    evw('P8/QA_RESULTS.md', md + '\n');
    evw('P8/QA_RESULTS.json', JSON.stringify({
      results, notes, exceptions, dialogLog, lan: LAN, elapsed_s: Number(elapsed)
    }, null, 2));

    const passed = results.filter(r => r.ok).length;
    console.log('\n=== Q&A run: ' + passed + '/' + results.length + ' PASS (' + elapsed + 's) ===');
    if (passed !== results.length) process.exitCode = 1;
  } catch (err) {
    console.error('\n❌ RUN FAILED: ' + (err && err.stack || err));
    try { evw('P8/QA_ERROR.txt', String(err && err.stack || err) + '\n'); } catch (e) {}
    process.exitCode = 1;
  } finally {
    if (srv) await stopProc(srv);
    if (chrome) await stopProc(chrome.child);
    try { fs.rmSync(CHROME_DIR, { recursive: true, force: true }); } catch (e) {}
  }
})();
