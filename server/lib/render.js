'use strict';
/*
 * render.js — serve index.php WITHOUT editing the file on disk.
 * Source md5 stays 895eb75226cc2801834baaced2873bb1 (6,490 lines, 0 edits).
 * cPanel-style per-user values are injected at render time:
 *   var-line (A / C / SP / HD / PID), <title>, footer path, create-prefix.
 * After injection zero occurrences of the old account ('alistiyab') may remain
 * — asserted by test E2.4 / E6.2.
 */
const fs = require('fs');
const path = require('path');
const { UI_ROOT, OC_HOME, withinRoots } = require('./jail');

const UI_FILE = path.join(UI_ROOT, 'index.php');
let cache = null;

function source() {
  if (cache === null) cache = fs.readFileSync(UI_FILE, 'utf8');
  return cache;
}

/* Exact byte anchors (verified in evidence/P1). */
const ANCHORS = {
  varLine: "var A='index.live.php?act=ai&path=public_html&project_id=alistiyab_a6093d3b779b',C='',SP='/home/alistiyab/public_html',HD='/home/alistiyab',PID='alistiyab_a6093d3b779b',pv=[]",
  title: '<title>public_html - Code with AI</title>',
  footerPath: '<span style="">/home/alistiyab/public_html</span>',
  createPrefix: '>/home/alistiyab/</span>'
};

function esc(s) {
  return String(s == null ? '' : s).replace(/[&<>"']/g,
    c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

/*
 * query: page URL params { path?, project_id?, session? }
 * csrf : live session token -> injected as UI var C
 */
function render(query, csrf) {
  const store = require('./store');
  const q = query || {};
  let proj = null;

  if (q.project_id) proj = store.getProject(q.project_id);

  if (!proj && q.path) {
    const abs = path.isAbsolute(q.path) ? path.resolve(q.path) : path.resolve(OC_HOME, q.path);
    if (withinRoots(abs)) {
      const pid = q.project_id || ('proj_' + (path.basename(abs).replace(/[^A-Za-z0-9_-]/g, '') || 'default'));
      proj = store.upsertProject({
        project_id: pid,
        name: path.basename(abs) || pid,
        path: abs,
        type: 'custom',
        created: store.now()
      });
    }
  }

  if (!proj) {
    const pid = q.project_id || process.env.OC_DEFAULT_PROJECT || 'default';
    proj = store.getProject(pid) || store.upsertProject({
      project_id: pid, name: pid,
      path: path.join(OC_HOME, pid),
      type: 'custom', created: store.now()
    });
  }

  if (!fs.existsSync(proj.path)) {
    try { fs.mkdirSync(proj.path, { recursive: true }); } catch (e) {}
  }
  // seed a README so file_tree / project_info have content on first run
  const readme = path.join(proj.path, 'README.md');
  if (!fs.existsSync(readme)) {
    try {
      fs.writeFileSync(readme,
        '# ' + (proj.name || proj.project_id) + '\n\n' +
        'Working directory managed by the Code with AI server.\n' +
        'Project path: `' + proj.path + '`\n\n' +
        'Ask the AI to explore, edit or run commands here.\n');
    } catch (e) {}
  }

  const SP = proj.path;
  const HD = (SP === OC_HOME || SP.startsWith(OC_HOME + path.sep)) ? OC_HOME : path.dirname(SP);
  const relPath = SP.startsWith(HD + path.sep) ? SP.slice(HD.length + 1)
    : (SP === HD ? '' : path.basename(SP));
  const dispName = proj.name || path.basename(SP);

  // ?session= picks the conversation this page views
  if (q.session) {
    const conv = store.getConv(String(q.session));
    if (conv && conv.project_id === proj.project_id) store.setActive(proj.project_id, conv.id);
  }

  let html = source();
  const stats = { replacements: {}, remainingOldAccountRefs: -1 };

  const rep = (name, needle, value, expected) => {
    const n = html.split(needle).length - 1;
    if (n > 0) html = html.split(needle).join(value);
    stats.replacements[name] = { found: n, expected };
  };

  rep('varLine', ANCHORS.varLine,
    `var A='index.live.php?act=ai&path=${encodeURIComponent(relPath)}&project_id=${encodeURIComponent(proj.project_id)}',` +
    `C='${csrf}',SP=${JSON.stringify(SP)},HD=${JSON.stringify(HD)},` +
    `PID=${JSON.stringify(proj.project_id)},pv=[]`, 1);

  rep('title', ANCHORS.title, `<title>${esc(dispName)} - Code with AI</title>`, 1);
  rep('footerPath', ANCHORS.footerPath, `<span style="">${esc(SP)}</span>`, 1);
  rep('createPrefix', ANCHORS.createPrefix, `>${esc(HD)}/</span>`, 1);

  stats.remainingOldAccountRefs = (html.match(/alistiyab/g) || []).length;
  return { html, project: proj, stats, SP, HD, rel: relPath };
}

module.exports = { render, source, UI_FILE, ANCHORS };
