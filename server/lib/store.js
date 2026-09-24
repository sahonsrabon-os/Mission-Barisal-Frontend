'use strict';
/*
 * store.js — persistent state (single JSON file, debounced atomic writes).
 * Mode 0600: settings.providers[].api_key is server-private (never serialized
 * to API responses; providers.js strips it before any UI-facing output).
 */
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const DATA_DIR = process.env.OC_DATA
  ? path.resolve(process.env.OC_DATA)
  : path.join(__dirname, '..', 'data');
const STATE_FILE = path.join(DATA_DIR, 'state.json');

const defaults = () => ({
  projects: [],
  settings: {
    providers: {},   // provider_id -> {api_key, base_url, name, models, model, is_custom_type}
    favorites: [],   // ["provider/model", ...]
    permissions: {}, // tool -> 'allow_always' | 'deny'
    sessions: {}     // project_id -> {provider, model, mode}
  },
  conversations: {}, // conv_id -> {...}
  snapshots: {},     // snap_id -> {id, project_id, time, message, files:{rel:{before,after}}}
  active: {}         // project_id -> conv_id
});

let state = defaults();
let loaded = false;
let saveTimer = null;

function load() {
  if (loaded) return state;
  loaded = true;
  try {
    fs.mkdirSync(DATA_DIR, { recursive: true });
    const raw = fs.readFileSync(STATE_FILE, 'utf8');
    const s = JSON.parse(raw);
    state = Object.assign(defaults(), s);
    state.settings = Object.assign(defaults().settings, s.settings || {});
  } catch (e) { /* first run */ }
  return state;
}

function saveNow() {
  if (saveTimer) { clearTimeout(saveTimer); saveTimer = null; }
  try {
    fs.mkdirSync(DATA_DIR, { recursive: true });
    const tmp = STATE_FILE + '.' + process.pid + '.tmp';
    fs.writeFileSync(tmp, JSON.stringify(state, null, 1));
    fs.renameSync(tmp, STATE_FILE);
    try { fs.chmodSync(STATE_FILE, 0o600); } catch (e) {}
  } catch (e) {
    console.error('[store] save failed:', e.message);
  }
}

function save() {
  load();
  if (saveTimer) return;
  saveTimer = setTimeout(saveNow, 250);
}

function now() { return Math.floor(Date.now() / 1000); }
function rid(prefix) { return prefix + '_' + crypto.randomBytes(6).toString('hex'); }
function msgId() { return 'm_' + crypto.randomBytes(5).toString('hex'); }

/* ---------------- projects ---------------- */
function listProjects() { load(); return state.projects.slice(); }
function getProject(pid) {
  load();
  return state.projects.find(p => p.project_id === pid) || null;
}
function upsertProject(p) {
  load();
  const i = state.projects.findIndex(x => x.project_id === p.project_id);
  if (i >= 0) state.projects[i] = Object.assign(state.projects[i], p);
  else state.projects.push(p);
  save();
  return p;
}
function removeProject(pid) {
  load();
  state.projects = state.projects.filter(p => p.project_id !== pid);
  for (const [cid, c] of Object.entries(state.conversations)) {
    if (c.project_id === pid) delete state.conversations[cid];
  }
  for (const k of Object.keys(state.active)) {
    if (k === pid) delete state.active[k];
  }
  save();
}
function closeProject(pid) {
  load();
  state.projects = state.projects.filter(p => p.project_id !== pid);
  if (state.active[pid]) delete state.active[pid];
  save();
}

/* ---------------- conversations ---------------- */
function listConversations(pid) {
  load();
  return Object.values(state.conversations)
    .filter(c => c.project_id === pid)
    .sort((a, b) => (b.updated || 0) - (a.updated || 0));
}
function getConv(cid) {
  load();
  return state.conversations[cid] || null;
}
function createConv(pid, title) {
  load();
  const c = {
    id: rid('conv'),
    project_id: pid,
    title: title || 'Untitled',
    created: now(), updated: now(),
    messages: [], todos: [],
    last_status: 'idle'
  };
  state.conversations[c.id] = c;
  save();
  return c;
}
/* create with a UI-supplied id (keeps streamSid === conversation_id matching) */
function createConvWithId(cid, pid) {
  load();
  const c = {
    id: cid,
    project_id: pid,
    title: 'Untitled',
    created: now(), updated: now(),
    messages: [], todos: [],
    last_status: 'idle'
  };
  state.conversations[cid] = c;
  save();
  return c;
}
function touchConv(c) {
  load();
  c.updated = now();
  save();
}
function deleteConv(cid) {
  load();
  delete state.conversations[cid];
  for (const k of Object.keys(state.active)) {
    if (state.active[k] === cid) delete state.active[k];
  }
  save();
}
function getActive(pid) {
  load();
  const cid = state.active[pid];
  return cid ? (state.conversations[cid] || null) : null;
}
function setActive(pid, cid) {
  load();
  if (cid && state.conversations[cid]) state.active[pid] = cid;
  else delete state.active[pid];
  save();
}

/* ---------------- settings ---------------- */
function settings() {
  load();
  if (!state.settings.providers) state.settings.providers = {};
  if (!state.settings.favorites) state.settings.favorites = [];
  if (!state.settings.permissions) state.settings.permissions = {};
  if (!state.settings.sessions) state.settings.sessions = {};
  return state.settings;
}
function saveSettings() { save(); }

module.exports = {
  load, save, saveNow, now, rid, msgId,
  listProjects, getProject, upsertProject, removeProject, closeProject,
  listConversations, getConv, createConv, createConvWithId, touchConv, deleteConv,
  getActive, setActive,
  settings, saveSettings,
  DATA_DIR, STATE_FILE,
  get state() { return load(); }
};
