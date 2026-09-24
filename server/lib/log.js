'use strict';
/* log.js — detailed, secret-safe logging (stdout + append-only file).
 *
 *   OC_LOG_FILE='<path>'  -> log to that file (plus stdout)
 *   OC_LOG_FILE=''        -> file logging disabled (stdout only)
 *   (unset)               -> default: <ui root>/logs/server.log
 *
 * Every line is ISO-timestamped; values of secret-looking keys
 * (api_key/token/secret/password/authorization) are redacted.
 * Requires env.js to have been loaded first (it may set OC_LOG_FILE).
 */
const fs = require('fs');
const path = require('path');

const UI_ROOT = path.resolve(__dirname, '..', '..');
let file;
if (process.env.OC_LOG_FILE !== undefined) {
  file = process.env.OC_LOG_FILE ? path.resolve(process.env.OC_LOG_FILE) : null;
} else {
  file = path.join(UI_ROOT, 'logs', 'server.log');
}
let stream = null;
if (file) {
  try {
    fs.mkdirSync(path.dirname(file), { recursive: true });
    stream = fs.createWriteStream(file, { flags: 'a' });
    stream.on('error', e => {
      stream = null;
      process.stderr.write('[log] file logging disabled: ' + e.message + '\n');
    });
  } catch (e) {
    stream = null;
  }
}

const SECRET_RE = /(api[_-]?key|token|secret|password|authorization|cookie)/i;
function redact(v) {
  if (!v || typeof v !== 'object') return v;
  const out = Array.isArray(v) ? [] : {};
  for (const [k, val] of Object.entries(v)) {
    out[k] = SECRET_RE.test(k) ? '[redacted]'
      : (val && typeof val === 'object' ? redact(val) : val);
  }
  return out;
}

function emit(level, msg, data) {
  let line = new Date().toISOString() + ' ' + level.toUpperCase().padEnd(5) + ' ' + msg;
  if (data !== undefined && data !== null) {
    try { line += ' ' + JSON.stringify(redact(data)); } catch (e) {}
  }
  try { process.stdout.write(line + '\n'); } catch (e) {}
  if (stream) { try { stream.write(line + '\n'); } catch (e) {} }
}

module.exports = {
  file: () => file,
  info: (msg, data) => emit('info', msg, data),
  warn: (msg, data) => emit('warn', msg, data),
  error: (msg, data) => emit('error', msg, data)
};
