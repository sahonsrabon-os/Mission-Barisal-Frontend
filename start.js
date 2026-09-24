#!/usr/bin/env node
/* Bismillah — Code with AI backend launcher (Node.js, cross-platform).
 *
 * Why a Node launcher instead of bash:
 *   - identical behaviour on Linux / macOS / Windows (node start.js / npm start)
 *   - every run gets a FRESH RANDOM UUID (OC_RUN_ID) so runs are unambiguous,
 *     and the pid-file + port pre-check mean a re-run NEVER blocks or hangs:
 *     a second invocation reports the running instance and exits immediately.
 *
 * - configuration:  ./.env   (loaded inside the server by server/lib/env.js;
 *                             precedence: real env > .env > defaults)
 * - child server:   spawned DETACHED (parent exits at once), raw stdout/stderr
 *                   appended to logs/console.log
 * - status:         pid -> logs/server.pid, run info -> logs/last-run.json,
 *                   app log (with run_id= in the READY line) -> logs/server.log
 *
 * Stop with:  node stop.js   (or ./stop.sh)
 */
'use strict';

const { spawn } = require('node:child_process');
const crypto = require('node:crypto');
const fs = require('node:fs');
const net = require('node:net');
const path = require('node:path');

const ROOT = __dirname;
const LOGS = path.join(ROOT, 'logs');
const PIDF = path.join(LOGS, 'server.pid');
const RUNF = path.join(LOGS, 'last-run.json');
const CONSOLE = path.join(LOGS, 'console.log');

function alive(pid) {
  try { process.kill(pid, 0); return true; }
  catch (e) { return e.code === 'EPERM'; }
}

/* minimal KEY=VALUE preview of .env (the server does the full, authoritative
   load via server/lib/env.js — this is only for port/host pre-checks) */
function readEnvFile() {
  const out = {};
  try {
    for (const line of fs.readFileSync(path.join(ROOT, '.env'), 'utf8').split(/\r?\n/)) {
      if (line.trim().startsWith('#')) continue;
      const m = /^\s*([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*?)\s*$/.exec(line);
      if (m) out[m[1]] = m[2].replace(/\s+#.*$/, '').trim();
    }
  } catch (e) { /* no .env — defaults below */ }
  return out;
}

function canListen(port, host) {
  return new Promise(res => {
    const s = net.createServer();
    s.once('error', () => res(false));
    s.once('listening', () => s.close(() => res(true)));
    s.listen(port, host);
  });
}

const sleep = ms => new Promise(r => setTimeout(r, ms));

async function main() {
  fs.mkdirSync(LOGS, { recursive: true });

  /* 1) already running? (stale pid file is cleaned, never blocks) */
  if (fs.existsSync(PIDF)) {
    const old = parseInt(fs.readFileSync(PIDF, 'utf8').trim(), 10);
    if (old && alive(old)) {
      console.log('Already running: pid ' + old + ' — run "node stop.js" (or ./stop.sh) first.');
      process.exit(1);
    }
    fs.rmSync(PIDF, { force: true });
  }

  const envf = readEnvFile();
  const port = parseInt(envf.PORT || process.env.PORT || '18800', 10);
  const host = envf.HOST || process.env.HOST || '127.0.0.1';
  if (!fs.existsSync(path.join(ROOT, '.env'))) {
    console.log('NOTE: no .env found — using defaults. Copy .env.example to .env to add providers.');
  }

  /* 2) port pre-check: fail fast instead of hanging behind another process */
  if (!(await canListen(port, host))) {
    console.error('Port ' + port + ' is already in use — refusing to start (no blocking).');
    console.error('Free the port, or change PORT in .env.');
    process.exit(1);
  }

  /* 3) fresh identity for this run */
  const runId = crypto.randomUUID();
  const consoleStart = fs.existsSync(CONSOLE) ? fs.statSync(CONSOLE).size : 0;

  const child = spawn(process.execPath, [path.join(ROOT, 'server', 'server.js')], {
    cwd: ROOT,
    env: Object.assign({}, process.env, { OC_RUN_ID: runId }),
    detached: true,
    stdio: ['ignore', fs.openSync(CONSOLE, 'a'), fs.openSync(CONSOLE, 'a')]
  });
  child.unref();

  fs.writeFileSync(PIDF, String(child.pid) + '\n');
  fs.writeFileSync(RUNF, JSON.stringify({
    pid: child.pid,
    runId: runId,
    startedAt: new Date().toISOString(),
    port: port,
    host: host
  }, null, 2) + '\n');

  /* 4) wait for the server's READY line (poll both logs from pre-spawn offsets) */
  let ready = '';
  const deadline = Date.now() + 20000;
  while (Date.now() < deadline && !ready) {
    await sleep(250);
    if (!alive(child.pid)) break;
    for (const f of [path.join(LOGS, 'server.log'), CONSOLE]) {
      try {
        const st = fs.statSync(f);
        const from = (f === CONSOLE) ? consoleStart : 0;
        if (st.size <= from) continue;
        const txt = fs.readFileSync(f, 'utf8').slice(from);
        const m = new RegExp('READY pid=' + child.pid + '[^\\n]*').exec(txt);
        if (m) { ready = m[0]; break; }
      } catch (e) { /* file may not exist yet */ }
    }
  }

  if (!alive(child.pid)) {
    console.error('FAILED to start — last lines of logs/console.log:');
    try {
      const t = fs.readFileSync(CONSOLE, 'utf8');
      console.error(t.slice(Math.max(0, t.length - 1500)));
    } catch (e) { /* ignore */ }
    fs.rmSync(PIDF, { force: true });
    process.exit(1);
  }

  /* 5) health check against the port/host the server actually reported */
  const pm = /port=(\d+)/.exec(ready);
  const hport = pm ? parseInt(pm[1], 10) : port;
  const hm = /host=([^\s]+)/.exec(ready);
  let hhost = hm ? hm[1] : host;
  if (hhost === '0.0.0.0') hhost = '127.0.0.1';

  let health = false;
  try {
    const r = await fetch('http://' + hhost + ':' + hport + '/healthz',
      { signal: AbortSignal.timeout(3000) });
    health = r.ok;
  } catch (e) { health = false; }

  console.log('started pid=' + child.pid + ' run_id=' + runId);
  if (ready) console.log(ready);
  if (health) {
    console.log('healthz OK -> http://' + hhost + ':' + hport + '/   (log: logs/server.log)');
    process.exit(0);
  }
  if (ready) {
    console.log('healthz FAILED on ' + hhost + ':' + hport + ' — see logs/console.log');
    process.exit(1);
  }
  console.log('WARNING: no READY line within 20s — check logs/console.log');
  process.exit(1);
}

main().catch(e => { console.error(String((e && e.stack) || e)); process.exit(1); });
