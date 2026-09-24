'use strict';
/* env.js — .env loading + admin-managed LLM provider seeding.
 *
 * Load this FIRST in server.js, before anything else reads process.env:
 *
 *   const env = require('./lib/env');
 *   env.load();          // process.env now includes .env values
 *   ...requires...
 *   env.seedProviders(); // boot-time provider seeding (see below)
 *
 * Precedence:  real process.env  >  .env file  >  built-in defaults
 *   OC_ENV_FILE=''     -> file loading disabled (used by hermetic tests)
 *   OC_ENV_FILE=<path> -> explicit file (missing -> recorded, no crash)
 *   (unset)            -> <ui root>/.env
 *
 * Provider specs (admin gives the LLM from the environment):
 *   OC_PROVIDER_<n>_NAME      display name                (required unless ID)
 *   OC_PROVIDER_<n>_ID        optional explicit id        (default custom:<slug>)
 *   OC_PROVIDER_<n>_BASE_URL  OpenAI-compatible base URL
 *   OC_PROVIDER_<n>_API_KEY   optional (empty = no Authorization header)
 *   OC_PROVIDER_<n>_MODELS    "id=Name, id2=Name2"  or  '{"id":{"name":"..."}}'
 *   OC_PROVIDER_<n>_MODEL     default model id (UI auto-selects it)
 *   OC_PROVIDERS='[{...}]'    JSON array form (same fields)
 *   OC_PROVIDERS_FORCE=1      re-apply env values over existing entries
 *
 * Seeding rules:
 *   - entry missing  -> created
 *   - entry exists   -> kept as-is (customer UI edits win) unless FORCE=1
 *   - OC_ENV_FILE='' -> seeding skipped entirely
 * Never logs secret values, only key names.
 */
const fs = require('fs');
const path = require('path');

const UI_ROOT = path.resolve(__dirname, '..', '..');
let summaryState = { file: null, keys: [], applied: 0, skipped: false, error: null };

function parseFile(text) {
  const out = {};
  for (const raw of String(text).split(/\r?\n/)) {
    const line = raw.trim();
    if (!line || line.startsWith('#')) continue;
    const m = line.match(/^(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/);
    if (!m) continue;
    let v = m[2].trim();
    if (v.length > 1 &&
        ((v.startsWith('"') && v.endsWith('"')) || (v.startsWith("'") && v.endsWith("'")))) {
      v = v.slice(1, -1);
    } else {
      const hash = v.indexOf(' #'); // unquoted inline comment
      if (hash >= 0) v = v.slice(0, hash).trim();
    }
    out[m[1]] = v;
  }
  return out;
}

function load() {
  if (summaryState.file !== null || summaryState.skipped) return summaryState;
  const explicit = process.env.OC_ENV_FILE;
  let file;
  if (explicit !== undefined) {
    if (explicit === '') {
      summaryState = { file: null, keys: [], applied: 0, skipped: true, error: null };
      return summaryState;
    }
    file = path.resolve(explicit);
  } else {
    file = path.join(UI_ROOT, '.env');
  }
  try {
    const parsed = parseFile(fs.readFileSync(file, 'utf8'));
    let applied = 0;
    for (const [k, v] of Object.entries(parsed)) {
      if (process.env[k] === undefined) { process.env[k] = v; applied++; }
      summaryState.keys.push(k);
    }
    summaryState = { file, keys: summaryState.keys, applied, skipped: false, error: null };
  } catch (e) {
    summaryState = {
      file, keys: [], applied: 0, skipped: false,
      error: e.code === 'ENOENT' ? 'file not found' : String(e.message || e)
    };
  }
  return summaryState;
}

function summary() { return summaryState; }

/* ---- provider specs ---- */
function parseModelsSpec(s) {
  if (!s) return {};
  if (typeof s === 'object') return s;
  const t = String(s).trim();
  if (t.startsWith('{')) {
    try { const o = JSON.parse(t); return o && typeof o === 'object' ? o : {}; }
    catch (e) { return {}; }
  }
  const out = {};
  for (const part of t.split(',')) {
    const p = part.split('=');
    if (p.length === 2 && p[0].trim()) out[p[0].trim()] = p[1].trim();
    else if (p.length === 1 && p[0].trim()) out[p[0].trim()] = p[0].trim();
  }
  return out;
}

function specs() {
  const out = [];
  const raw = process.env.OC_PROVIDERS;
  if (raw) {
    try {
      const arr = JSON.parse(raw);
      if (Array.isArray(arr)) out.push(...arr.filter(x => x && typeof x === 'object'));
    } catch (e) { /* recorded as warn by caller via summary */ }
  }
  const groups = {};
  for (const [k, v] of Object.entries(process.env)) {
    const m = k.match(/^OC_PROVIDER_(\d+)_([A-Z_]+)$/);
    if (m) { (groups[m[1]] = groups[m[1]] || {})[m[2]] = v; }
  }
  for (const n of Object.keys(groups).sort((a, b) => Number(a) - Number(b))) {
    const g = groups[n];
    out.push({
      id: g.ID || '',
      name: g.NAME || '',
      base_url: g.BASE_URL || '',
      api_key: g.API_KEY || '',
      models: g.MODELS || '',
      model: g.MODEL || ''
    });
  }
  return out.filter(s => s && (s.base_url || s.id));
}

/* ---- boot-time seeding into the settings store ---- */
function seedProviders() {
  const info = load();
  if (info.skipped) return [];
  const store = require('./store');
  const providers = require('./providers');
  const force = /^(1|true|yes)$/i.test(process.env.OC_PROVIDERS_FORCE || '');
  const actions = [];
  for (const s of specs()) {
    const slug = String(s.name || '').toLowerCase()
      .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    const id = s.id || (slug ? 'custom:' + slug : '');
    if (!id) { actions.push({ action: 'skipped (no name/id)' }); continue; }
    const st = store.settings();
    const exists = !!st.providers[id];
    if (exists && !force) {
      actions.push({ id, action: 'exists (kept)', base: st.providers[id].base_url || '' });
      continue;
    }
    const q = { provider_id: id };
    if (s.name) q.name = s.name;
    if (s.base_url) q.base_url = s.base_url;
    if (s.api_key !== undefined && s.api_key !== '') q.api_key = s.api_key;
    if (s.models) q.models = parseModelsSpec(s.models);
    if (s.model) q.model = s.model;
    try {
      const r = providers.saveProviderConfig(q);
      actions.push({ id: (r && r.provider_id) || id, action: exists ? 'updated (force)' : 'created', base: s.base_url || '' });
    } catch (e) {
      actions.push({ id, action: 'error: ' + (e.message || e) });
    }
  }
  return actions;
}

module.exports = { load, summary, specs, parseModelsSpec, seedProviders };
