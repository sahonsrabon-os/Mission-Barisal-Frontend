'use strict';
/*
 * routes.js — the 39 contract routes (35 via api() + 3 raw XHR + stream).
 * Every route: HTTP 200 + JSON (unless a jail/auth violation => 403).
 * Response shapes are derived field-by-field from index.php call sites
 * (evidence/P1/callsites/*).
 */
const fs = require('fs');
const path = require('path');
const { execFile } = require('child_process');
const store = require('./store');
const jail = require('./jail');
const { unifiedDiff } = require('./diff');
const providers = require('./providers');
const stream = require('./stream');

function activeConv(c) {
  return store.getActive(c.project.project_id);
}
function requireConv(c) {
  if (c.q.conversation_id) {
    const conv = store.getConv(String(c.q.conversation_id));
    if (conv && conv.project_id === c.project.project_id) return conv;
  }
  return activeConv(c);
}
function convListItem(cv, cid) {
  return {
    id: cv.id,
    title: cv.title || 'Untitled',
    updated_at: cv.updated || 0,
    message_count: (cv.messages || []).length,
    running: stream.isRunning(cv.id),
    last_status: cv.last_status || 'idle'
  };
}

/* ---------- session-scoped model state ---------- */
function sess(c) { return stream.sessionState(c.project.project_id); }

function defaultSession(c) {
  const s = sess(c);
  if (!s.provider || !s.model) {
    // lazy default: first connected provider (opencode_zen preferred, as UI expects)
    const list = providers.publicProviders().filter(p => p.connected);
    const pick = list.find(p => p.id === 'opencode_zen') || list[0];
    if (pick && pick.connected) {
      s.provider = pick.id;
      const models = Object.keys(pick.models || {});
      s.model = pick.default_model || models[0] || null;
      store.saveSettings();
    }
  }
  return s;
}

/* ---------- helpers ---------- */
function jail404(c, userPath) {
  try {
    return jail.jail(c.workdir, userPath);
  } catch (e) {
    c.status(403);
    return null;
  }
}

function readProjectFile(c, userPath) {
  const abs = jail404(c, userPath);
  if (abs === null) return { __403: true };
  if (!fs.existsSync(abs) || !fs.statSync(abs).isFile()) {
    return { error: 'File not found' };
  }
  try {
    const buf = fs.readFileSync(abs);
    if (buf.includes(0)) return { error: 'Binary file' };
    return { content: buf.toString('utf8') };
  } catch (e) {
    return { error: String(e.message || e) };
  }
}

function fileTree(c) {
  const raw = c.q.path == null || c.q.path === '' ? '.' : String(c.q.path);
  let abs;
  try {
    if (raw === '/' || raw === '') abs = c.workdir;
    else abs = jail.jail(c.workdir, raw);
  } catch (e) {
    c.status(403);
    return { error: 'Path escapes the project working directory', directories: [], files: [] };
  }
  if (!fs.existsSync(abs)) return { directories: [], files: [] };
  let entries;
  try { entries = fs.readdirSync(abs, { withFileTypes: true }); }
  catch (e) { return { directories: [], files: [] }; }
  const directories = [];
  const files = [];
  for (const ent of entries) {
    const p = path.join(abs, ent.name);
    if (ent.isDirectory()) directories.push({ name: ent.name, path: p });
    else if (ent.isFile()) {
      let size = 0;
      try { size = fs.statSync(p).size; } catch (e) {}
      files.push({ name: ent.name, path: p, size });
    }
  }
  return { directories, files };
}

function detectType(dir) {
  try {
    if (fs.existsSync(path.join(dir, 'wp-config.php'))) return 'wordpress';
    if (fs.existsSync(path.join(dir, 'package.json'))) return 'node.js';
    if (fs.existsSync(path.join(dir, 'composer.json'))) return 'php';
    if (fs.existsSync(path.join(dir, 'requirements.txt')) || fs.existsSync(path.join(dir, 'pyproject.toml'))) return 'python';
  } catch (e) {}
  return null;
}

function readReadme(dir) {
  for (const name of ['README.md', 'README.txt', 'README']) {
    try {
      const p = path.join(dir, name);
      if (fs.existsSync(p)) {
        const buf = fs.readFileSync(p);
        if (!buf.includes(0)) return buf.toString('utf8').slice(0, 500);
      }
    } catch (e) {}
  }
  return '';
}

function findMessage(c, messageId) {
  const order = [];
  const act = activeConv(c);
  if (act) order.push(act);
  for (const cv of store.listConversations(c.project.project_id)) {
    if (!order.includes(cv)) order.push(cv);
  }
  for (const cv of order) {
    const idx = (cv.messages || []).findIndex(m => m.id === messageId);
    if (idx >= 0) return { conv: cv, idx };
  }
  return null;
}

/* ================= routes ================= */
const routes = {

  /* ---- session / model ---- */
  start(c) {
    const s = sess(c);
    if (c.q.provider) s.provider = String(c.q.provider);
    if (c.q.model) s.model = String(c.q.model);
    if (c.q.mode) s.mode = String(c.q.mode) === 'plan' ? 'plan' : 'build';
    store.saveSettings();
    return { success: true, session: Object.assign({}, s) };
  },
  set_mode(c) {
    const s = sess(c);
    s.mode = String(c.q.mode) === 'plan' ? 'plan' : 'build';
    store.saveSettings();
    return { success: true, mode: s.mode };
  },
  status(c) {
    const s = defaultSession(c);
    const conv = activeConv(c);
    return {
      session: {
        provider: s.provider || null,
        model: s.model || null,
        mode: s.mode || 'build',
        active_conversation: conv ? conv.id : null
      },
      providers: providers.publicProviders(),
      favorite_models: providers.favorites(),
      model_info: providers.modelInfo(),
      lock: { locked: conv ? stream.isRunning(conv.id) : false },
      running_conversations: stream.runningForProject(c.project.project_id)
    };
  },

  /* ---- conversations ---- */
  conversations(c) {
    return store.listConversations(c.project.project_id).map(cv => convListItem(cv));
  },
  conversation(c) {
    const conv = requireConv(c);
    if (!conv) { c.status(404); return { error: 'Conversation not found', messages: [], todos: [] }; }
    // viewing a conversation makes it the server-side active one
    store.setActive(conv.project_id, conv.id);
    return { messages: conv.messages || [], todos: conv.todos || [] };
  },
  switch_conversation(c) {
    const conv = requireConv(c);
    if (!conv) { c.status(404); return { error: 'Conversation not found' }; }
    store.setActive(conv.project_id, conv.id);
    return { success: true, conversation_id: conv.id };
  },
  new_session(c) {
    const conv = store.createConv(c.project.project_id);
    store.setActive(conv.project_id, conv.id);
    return { id: conv.id, title: conv.title, output: '' };
  },
  delete_conversation(c) {
    const conv = requireConv(c);
    if (conv) {
      stream.abortConv(conv.id);
      store.deleteConv(conv.id);
    }
    return { success: true };
  },
  rename_conversation(c) {
    const conv = requireConv(c);
    if (!conv) { c.status(404); return { error: 'Conversation not found' }; }
    conv.title = String(c.q.title || 'Untitled').slice(0, 120);
    store.touchConv(conv);
    return { success: true, title: conv.title };
  },
  clear(c) {
    const conv = requireConv(c);
    if (conv) {
      conv.messages = [];
      conv.redo = [];
      store.touchConv(conv);
    }
    return { success: true, messages: [] };
  },
  compact(c) {
    const conv = requireConv(c);
    if (!conv) return { success: true, messages: [] };
    const n = parseInt(c.q.keep_last_n, 10) || 10;
    // NOTE (contract quirk): UI toasts "Nothing to compact" when `summary` is
    // truthy — so summary is ONLY set on the no-op path.
    if ((conv.messages || []).length <= n) {
      return { success: true, messages: conv.messages, summary: 'nothing to compact' };
    }
    const keep = conv.messages.slice(-n);
    const dropped = conv.messages.length - keep.length;
    keep.unshift({
      id: store.msgId(), role: 'assistant', content: '',
      parts: [{ type: 'text', text: '(Conversation compacted: ' + dropped + ' earlier messages summarized out. Continue from the recent context below.)' }],
      time: store.now()
    });
    conv.messages = keep;
    conv.redo = [];
    store.touchConv(conv);
    return { success: true, messages: conv.messages };
  },
  undo(c) {
    let hit = null;
    if (c.q.message_id) hit = findMessage(c, String(c.q.message_id));
    if (!hit) {
      const act = activeConv(c);
      if (act && act.messages.length) hit = { conv: act, idx: 0 };
    }
    if (!hit) return { success: false, error: 'Nothing to undo', messages: [] };
    const removed = hit.conv.messages.splice(hit.idx);
    hit.conv.redo = removed.slice(-50);
    store.touchConv(hit.conv);
    return { success: true, messages: hit.conv.messages };
  },
  redo(c) {
    let conv = activeConv(c);
    if (!conv || !(conv.redo || []).length) {
      conv = store.listConversations(c.project.project_id).find(cv => (cv.redo || []).length) || null;
    }
    if (!conv || !(conv.redo || []).length) {
      return { success: false, error: 'Nothing to redo', messages: [] };
    }
    conv.messages = (conv.messages || []).concat(conv.redo);
    conv.redo = [];
    store.touchConv(conv);
    return { success: true, messages: conv.messages };
  },
  regenerate(c) {
    // UI re-sends the last user text as a NEW message, so drop the trailing
    // assistant turn + the last user turn to keep history clean.
    const conv = requireConv(c);
    if (conv && conv.messages.length) {
      const m = conv.messages;
      while (m.length && m[m.length - 1].role === 'tool_result') m.pop();
      if (m.length && m[m.length - 1].role === 'assistant') m.pop();
      if (m.length && m[m.length - 1].role === 'user') m.pop();
      store.touchConv(conv);
    }
    return { success: true };
  },
  fork(c) {
    const hit = c.q.message_id ? findMessage(c, String(c.q.message_id)) : null;
    if (!hit) { c.status(404); return { success: false, error: 'Message not found', forked_content: '' }; }
    const prefix = hit.conv.messages.slice(0, hit.idx + 1);
    const nf = store.createConv(c.project.project_id, (hit.conv.title || 'Untitled') + ' (fork)');
    nf.messages = JSON.parse(JSON.stringify(prefix));
    nf.updated = store.now();
    store.touchConv(nf);
    store.setActive(nf.project_id, nf.id);
    return { success: true, conversation_id: nf.id, title: nf.title, forked_content: '' };
  },
  abort(c) {
    const conv = c.q.conversation_id ? store.getConv(String(c.q.conversation_id)) : activeConv(c);
    if (conv) stream.abortConv(conv.id);
    else stream.abortProject(c.project.project_id);
    return { success: true };
  },
  force_unlock(c) {
    const n = stream.abortProject(c.project.project_id);
    return { success: true, message: n ? ('Cleared ' + n + ' running generation(s).') : 'Lock cleared' };
  },

  /* ---- shell (auth-gated by global CSRF/login) ---- */
  shell(c) {
    return new Promise(resolve => {
      const cmd = String(c.q.command || '');
      if (!cmd) return resolve({ output: '', returncode: 0 });
      execFile('/bin/bash', ['-c', cmd], {
        cwd: c.workdir, timeout: 30000, maxBuffer: 8 * 1024 * 1024, env: process.env
      }, (err, stdout, stderr) => {
        let code = 0;
        if (err) {
          if (err.killed) code = 124;
          else if (typeof err.code === 'number') code = err.code;
          else code = 1;
        }
        let output = String(stdout || '');
        if (stderr) output += (output ? '\n' : '') + String(stderr);
        const r = { output: output.slice(0, 500000), returncode: code };
        if (err && err.killed) r.error = 'Timed out after 30s';
        else if (err && !output) r.error = String(err.message || err);
        resolve(r);
      });
    });
  },

  /* ---- files ---- */
  file_tree(c) { return fileTree(c); },
  read_file(c) {
    const r = readProjectFile(c, c.q.path);
    if (r.__403) return { error: 'Path escapes the project working directory' };
    return r;
  },
  resolve_file(c) {
    const r = readProjectFile(c, c.q.path);
    if (r.__403) return { error: 'Path escapes the project working directory' };
    return r;
  },

  /* ---- snapshots ---- */
  changes(c) {
    const st = store.state;
    const snaps = Object.values(st.snapshots || {})
      .filter(s => s.project_id === c.project.project_id)
      .sort((a, b) => (b.time || 0) - (a.time || 0))
      .map(s => ({ id: s.id, message: s.message, time: s.time }));
    return { snapshots: snaps };
  },
  diff(c) {
    const st = store.state;
    const snap = (st.snapshots || {})[String(c.q.id || '')];
    if (!snap) { c.status(404); return { error: 'Snapshot not found' }; }
    const chunks = [];
    for (const [rel, f] of Object.entries(snap.files || {})) {
      const d = unifiedDiff(f.before == null ? '' : f.before, f.after == null ? '' : f.after, rel);
      if (d) chunks.push(d);
    }
    return { diff: chunks.join('\n') || '(no differences)' };
  },
  restore(c) {
    const st = store.state;
    const snap = (st.snapshots || {})[String(c.q.id || '')];
    if (!snap) { c.status(404); return { error: 'Snapshot not found' }; }
    const chunks = [];
    for (const [rel, f] of Object.entries(snap.files || {})) {
      let abs;
      try { abs = jail.jail(c.workdir, rel); }
      catch (e) { chunks.push('--- refused (outside jail): ' + rel); continue; }
      const now = fs.existsSync(abs) ? (() => { try { return fs.readFileSync(abs).toString('utf8'); } catch (e) { return ''; } })() : '';
      const before = f.before == null ? '' : f.before;
      const d = unifiedDiff(now, before, rel);
      if (d) chunks.push(d);
      try {
        if (f.before === null) { if (fs.existsSync(abs)) fs.unlinkSync(abs); }
        else { fs.mkdirSync(path.dirname(abs), { recursive: true }); fs.writeFileSync(abs, f.before); }
      } catch (e) {
        chunks.push('!! restore failed for ' + rel + ': ' + e.message);
      }
    }
    return { restored: true, diff: chunks.join('\n') || '(no differences)' };
  },

  /* ---- projects ---- */
  project_info(c) {
    const proj = c.project;
    const type = detectType(proj.path) || proj.type || 'generic';
    const readme = readReadme(proj.path);
    const overview = 'Project type: ' + type + '\nProject path: ' + proj.path + (readme ? '\n\n' + readme : '');
    return { overview, type, project_id: proj.project_id, name: proj.name };
  },
  projects_list(c) {
    return store.listProjects().map(p => ({
      project_id: p.project_id, name: p.name, path: p.path,
      type: p.type || 'custom', insid: p.insid || null, created: p.created || 0
    }));
  },
  projects_wordpress(c) {
    const found = [];
    try {
      for (const ent of fs.readdirSync(jail.OC_HOME, { withFileTypes: true })) {
        if (!ent.isDirectory()) continue;
        const dir = path.join(jail.OC_HOME, ent.name);
        if (fs.existsSync(path.join(dir, 'wp-config.php'))) {
          found.push({ insid: 'wp_' + ent.name, name: ent.name, path: dir });
        }
        // one nested level (e.g. public_html style layouts)
        try {
          for (const sub of fs.readdirSync(dir, { withFileTypes: true })) {
            if (!sub.isDirectory()) continue;
            const sdir = path.join(dir, sub.name);
            if (fs.existsSync(path.join(sdir, 'wp-config.php'))) {
              found.push({ insid: 'wp_' + ent.name + '_' + sub.name, name: sub.name, path: sdir });
            }
          }
        } catch (e) {}
      }
    } catch (e) {}
    return found;
  },
  projects_create(c) {
    const rawPath = String(c.q.path || '').trim();
    if (!rawPath) return { error: 'Project path is required' };
    const abs = path.isAbsolute(rawPath) ? path.resolve(rawPath) : path.resolve(jail.OC_HOME, rawPath);
    if (!jail.withinRoots(abs)) {
      return { error: 'Path is outside the allowed project roots: ' + abs };
    }
    try { fs.mkdirSync(abs, { recursive: true }); }
    catch (e) { return { error: 'Cannot create directory: ' + e.message }; }
    const pid = store.rid('proj');
    const proj = store.upsertProject({
      project_id: pid,
      name: String(c.q.name || path.basename(abs) || pid),
      path: abs,
      type: String(c.q.type || 'custom'),
      insid: c.q.insid ? String(c.q.insid) : null,
      created: store.now()
    });
    return { project_id: proj.project_id, name: proj.name, path: proj.path };
  },
  projects_delete(c) {
    const pid = String(c.q.project_id || '');
    if (!pid) return { error: 'project_id required' };
    if (!store.getProject(pid)) { c.status(404); return { error: 'Project not found' }; }
    store.removeProject(pid); // deletes conversations/sessions for it; keeps source files
    return { success: true };
  },
  projects_close(c) {
    const pid = String(c.q.project_id || '');
    if (!pid) return { error: 'project_id required' };
    if (!store.getProject(pid)) { c.status(404); return { error: 'Project not found' }; }
    store.closeProject(pid);
    return { success: true };
  },

  /* ---- providers / settings (customer's own AI provider) ---- */
  providers(c) { return providers.publicProviders(); },
  settings_load(c) {
    return { favorite_models: providers.favorites(), permissions: providers.permissions() };
  },
  settings_save(c) {
    const r = providers.saveProviderConfig(c.q);
    if (r.error) { c.status(200); return { error: r.error }; }
    return { success: true, provider_id: r.provider_id };
  },
  settings_delete(c) {
    providers.deleteProviderConfig(c.q);
    return { success: true };
  },
  test_connection(c) {
    return providers.testConnection(c.q);
  },
  favorite_model(c) { return providers.addFavorite(String(c.q.model_id || '')); },
  unfavorite_model(c) { return providers.removeFavorite(String(c.q.model_id || '')); },
  toggle_permission(c) {
    return providers.setPermission(String(c.q.permission || ''), c.q.value);
  }
};

module.exports = { routes, activeConv };
