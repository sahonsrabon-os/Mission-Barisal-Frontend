#!/usr/bin/env node
/* Stop the backend started by start.js (cross-platform: Linux/macOS/Windows). */
'use strict';

const fs = require('node:fs');
const path = require('node:path');

const ROOT = __dirname;
const PIDF = path.join(ROOT, 'logs', 'server.pid');
const RUNF = path.join(ROOT, 'logs', 'last-run.json');
const sleep = ms => new Promise(r => setTimeout(r, ms));

function alive(pid) {
  try { process.kill(pid, 0); return true; }
  catch (e) { return e.code === 'EPERM'; }
}

(async () => {
  if (!fs.existsSync(PIDF)) { console.log('not running (no pid file)'); process.exit(0); }
  const pid = parseInt(fs.readFileSync(PIDF, 'utf8').trim(), 10);
  let run = null;
  try { run = JSON.parse(fs.readFileSync(RUNF, 'utf8')); } catch (e) { /* optional */ }

  if (!pid || !alive(pid)) {
    console.log('stale pid file' + (pid ? ' (pid ' + pid + ' was not running)' : ''));
    fs.rmSync(PIDF, { force: true });
    process.exit(0);
  }

  try { process.kill(pid); } catch (e) { console.error('kill failed: ' + e.message); }
  for (let i = 0; i < 20 && alive(pid); i++) await sleep(250);
  if (alive(pid)) { try { process.kill(pid, 'SIGKILL'); } catch (e) { /* ignore */ } await sleep(300); }

  fs.rmSync(PIDF, { force: true });
  console.log('stopped pid=' + pid + (run && run.runId ? ' run_id=' + run.runId : ''));
  process.exit(0);
})().catch(e => { console.error(String((e && e.stack) || e)); process.exit(1); });
