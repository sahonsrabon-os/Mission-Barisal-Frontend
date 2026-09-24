'use strict';
/*
 * jail.js — per-request working directory resolution + path jail.
 * Reproduces cPanel's per-user environment: URL `path` + `project_id`
 * select the project; every file/shell access is confined to OC_ROOTS.
 */
const fs = require('fs');
const path = require('path');

class JailError extends Error {
  constructor(msg) { super(msg); this.jail = true; }
}

const UI_ROOT = path.resolve(__dirname, '..', '..');
const OC_HOME = process.env.OC_HOME
  ? path.resolve(process.env.OC_HOME)
  : path.join(UI_ROOT, 'workspace');
const OC_ROOTS = (process.env.OC_ROOTS || OC_HOME)
  .split(path.delimiter === ';' ? ';' : ':')
  .map(s => s.trim()).filter(Boolean).map(p => path.resolve(p));

function ensureHome() {
  try { fs.mkdirSync(OC_HOME, { recursive: true }); } catch (e) {}
}

/* realpath of the nearest existing ancestor + unresolved tail */
function realOf(p) {
  let cur = path.resolve(p);
  let tail = '';
  for (;;) {
    try {
      const real = fs.realpathSync(cur);
      return tail ? path.join(real, tail) : real;
    } catch (e) {
      const parent = path.dirname(cur);
      if (parent === cur) return path.resolve(p);
      tail = tail ? path.join(path.basename(cur), tail) : path.basename(cur);
      cur = parent;
    }
  }
}

function within(root, target) {
  const r = realOf(root);
  const t = realOf(target);
  return t === r || t.startsWith(r + path.sep);
}

function withinRoots(target) {
  return OC_ROOTS.some(r => within(r, target));
}

/* Resolve a user-supplied path inside `root`; throws JailError on escape. */
function jail(root, userPath) {
  const abs = path.resolve(root, userPath == null || userPath === '' ? '.' : String(userPath));
  if (!within(root, abs)) {
    throw new JailError('Path escapes the project working directory: ' + userPath);
  }
  return abs;
}

module.exports = { JailError, jail, within, withinRoots, realOf, ensureHome, OC_HOME, OC_ROOTS, UI_ROOT };
