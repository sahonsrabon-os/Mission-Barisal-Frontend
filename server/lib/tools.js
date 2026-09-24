'use strict';
/*
 * tools.js — agent tool set (all jailed to the per-request working dir).
 * Tool names match what the UI detects: bash, write_file, edit_file,
 * replace_in_file (modified-files panel + permission keys), plus read-only
 * helpers, todo_write and ask_user_question (special stream events).
 */
const fs = require('fs');
const path = require('path');
const { execFile } = require('child_process');
const { jail, JailError } = require('./jail');
const { unifiedDiff } = require('./diff');

const MAX_LLM_OUTPUT = 100 * 1024;   // tool result sent back to the model
const MAX_STORE_OUTPUT = 200 * 1024; // tool result persisted for the UI
const SKIP_DIRS = new Set(['.git', 'node_modules', '.venv', 'vendor']);

function truncHeadTail(s, cap) {
  s = String(s);
  if (s.length <= cap) return s;
  const head = s.slice(0, Math.floor(cap * 0.7));
  const tail = s.slice(-Math.floor(cap * 0.3));
  return head + '\n… [truncated ' + (s.length - head.length - tail.length) + ' chars] …\n' + tail;
}

function buildSystemPrompt(ctx) {
  const lines = [
    'You are a coding agent working inside the project directory below.',
    'Working directory: ' + ctx.workdir,
    'Mode: ' + ctx.mode + (ctx.mode === 'plan' ? ' (read-only: never modify files, never run shell commands)' : ''),
    '',
    'Use the provided tools to read, search, edit and run commands. Keep replies concise.',
    'File paths for tools are relative to the working directory unless absolute paths inside it are given.',
    'When you need a decision from the user, call ask_user_question. Maintain the task list with todo_write when the plan changes.'
  ];
  if (ctx.overview) lines.push('', 'Project context:', ctx.overview.slice(0, 1200));
  return lines.join('\n');
}

/* ---------- individual executors: (input, ctx) -> {output, is_error?, diff?} ---------- */

async function bashTool(input, ctx) {
  const cmd = String(input.command || '');
  if (!cmd) return { output: '', is_error: true, error: 'command required' };
  return new Promise(resolve => {
    execFile('/bin/bash', ['-c', cmd], {
      cwd: ctx.workdir,
      timeout: 30000,
      maxBuffer: 8 * 1024 * 1024,
      env: process.env
    }, (err, stdout, stderr) => {
      let code = 0;
      if (err) {
        if (err.killed) code = 124;
        else if (typeof err.code === 'number') code = err.code;
        else code = 1;
      }
      let output = String(stdout || '');
      if (stderr) output += (output ? '\n' : '') + String(stderr);
      const out = truncHeadTail(output, MAX_LLM_OUTPUT);
      const r = { output: out, returncode: code };
      if (err && err.killed) r.error = 'Timed out after 30s';
      if (code !== 0 && !output) r.error = r.error || ('Exit code ' + code);
      resolve(r);
    });
  });
}

async function readFileTool(input, ctx) {
  try {
    const abs = jail(ctx.workdir, input.path);
    if (!fs.existsSync(abs) || !fs.statSync(abs).isFile()) {
      return { output: 'File not found: ' + input.path, is_error: true };
    }
    const buf = fs.readFileSync(abs);
    if (buf.includes(0)) return { output: 'Binary file (not shown): ' + input.path, is_error: true };
    return { output: truncHeadTail(buf.toString('utf8'), MAX_LLM_OUTPUT) };
  } catch (e) {
    return { output: String(e.message || e), is_error: true };
  }
}

async function writeFileTool(input, ctx) {
  try {
    const abs = jail(ctx.workdir, input.path);
    const before = fs.existsSync(abs) ? safeRead(abs) : null;
    if (before === 'BIN') return { output: 'Refusing to overwrite binary file: ' + input.path, is_error: true };
    fs.mkdirSync(path.dirname(abs), { recursive: true });
    const content = String(input.content == null ? '' : input.content);
    fs.writeFileSync(abs, content);
    const rel = path.relative(ctx.workdir, abs);
    const diff = before !== null ? unifiedDiff(before, content, rel) : '';
    if (ctx.addFileChange) ctx.addFileChange(rel, before, content, 'write_file');
    return {
      output: 'Wrote ' + content.split('\n').length + ' lines to ' + rel + (diff ? '' : ' (new file)'),
      diff: diff || undefined
    };
  } catch (e) {
    return { output: String(e.message || e), is_error: true };
  }
}

async function editFileTool(input, ctx) {
  try {
    const abs = jail(ctx.workdir, input.path);
    if (!fs.existsSync(abs)) return { output: 'File not found: ' + input.path, is_error: true };
    const before = safeRead(abs);
    if (before === 'BIN') return { output: 'Binary file: ' + input.path, is_error: true };
    const oldStr = String(input.old_string == null ? '' : input.old_string);
    const newStr = String(input.new_string == null ? '' : input.new_string);
    if (!oldStr) return { output: 'old_string required', is_error: true };
    const count = before.split(oldStr).length - 1;
    if (count === 0) return { output: 'old_string not found in ' + input.path, is_error: true };
    if (count > 1) return { output: 'old_string matches ' + count + ' times — it must be unique', is_error: true };
    const after = before.replace(oldStr, newStr);
    fs.writeFileSync(abs, after);
    const rel = path.relative(ctx.workdir, abs);
    if (ctx.addFileChange) ctx.addFileChange(rel, before, after, 'edit_file');
    return { output: 'Edited ' + rel, diff: unifiedDiff(before, after, rel) || undefined };
  } catch (e) {
    return { output: String(e.message || e), is_error: true };
  }
}

async function replaceInFileTool(input, ctx) {
  try {
    const abs = jail(ctx.workdir, input.path);
    if (!fs.existsSync(abs)) return { output: 'File not found: ' + input.path, is_error: true };
    const before = safeRead(abs);
    if (before === 'BIN') return { output: 'Binary file: ' + input.path, is_error: true };
    const find = String(input.find == null ? '' : input.find);
    const replace = String(input.replace == null ? '' : input.replace);
    if (!find) return { output: 'find required', is_error: true };
    const count = before.split(find).length - 1;
    if (!count) return { output: 'No occurrences of find string in ' + input.path, is_error: true };
    const after = before.split(find).join(replace);
    fs.writeFileSync(abs, after);
    const rel = path.relative(ctx.workdir, abs);
    if (ctx.addFileChange) ctx.addFileChange(rel, before, after, 'replace_in_file');
    return {
      output: 'Replaced ' + count + ' occurrence(s) in ' + rel,
      diff: unifiedDiff(before, after, rel) || undefined
    };
  } catch (e) {
    return { output: String(e.message || e), is_error: true };
  }
}

async function listFilesTool(input, ctx) {
  try {
    const abs = jail(ctx.workdir, input.path || '.');
    if (!fs.existsSync(abs)) return { output: 'Not found: ' + input.path, is_error: true };
    const depth = Math.min(3, Math.max(1, parseInt(input.depth, 10) || 1));
    const lines = [];
    walk(abs, '', depth, lines, 0, 400);
    return { output: lines.join('\n') || '(empty directory)' };
  } catch (e) {
    return { output: String(e.message || e), is_error: true };
  }
}

function walk(absBase, relBase, maxDepth, lines, curDepth, budget) {
  if (curDepth >= maxDepth || lines.length >= budget) return;
  let entries;
  try { entries = fs.readdirSync(absBase, { withFileTypes: true }); } catch (e) { return; }
  entries.sort((a, b) => a.name.localeCompare(b.name));
  for (const ent of entries) {
    if (lines.length >= budget) break;
    const rel = relBase ? relBase + '/' + ent.name : ent.name;
    if (ent.isDirectory()) {
      if (SKIP_DIRS.has(ent.name)) continue;
      lines.push(rel + '/');
      walk(path.join(absBase, ent.name), rel, maxDepth, lines, curDepth + 1, budget);
    } else {
      let size = '';
      try { size = ' (' + fs.statSync(path.join(absBase, ent.name)).size + 'B)'; } catch (e) {}
      lines.push(rel + size);
    }
  }
}

async function grepTool(input, ctx) {
  try {
    const pattern = String(input.pattern || '');
    if (!pattern) return { output: 'pattern required', is_error: true };
    const root = jail(ctx.workdir, input.path || '.');
    let re;
    try { re = new RegExp(pattern, 'i'); } catch (e) { re = null; }
    const globRe = input.glob ? globToRegex(String(input.glob)) : null;
    const hits = [];
    scan(root, root, 5000, (rel, abs) => {
      if (hits.length >= 200) return false;
      if (globRe && !globRe.test(rel)) return;
      let txt;
      try {
        const buf = fs.readFileSync(abs);
        if (buf.includes(0)) return;
        txt = buf.toString('utf8');
      } catch (e) { return; }
      const ls = txt.split('\n');
      for (let i = 0; i < ls.length; i++) {
        if (hits.length >= 200) break;
        const line = ls[i];
        const ok = re ? re.test(line) : line.toLowerCase().includes(pattern.toLowerCase());
        if (ok) hits.push(rel + ':' + (i + 1) + ':' + line.slice(0, 300));
      }
    });
    if (!hits.length) return { output: 'No matches.' };
    return { output: hits.join('\n') + (hits.length >= 200 ? '\n… [more matches truncated]' : '') };
  } catch (e) {
    return { output: String(e.message || e), is_error: true };
  }
}

async function globTool(input, ctx) {
  try {
    const pattern = String(input.pattern || '');
    if (!pattern) return { output: 'pattern required', is_error: true };
    const root = jail(ctx.workdir, input.path || '.');
    const re = globToRegex(pattern);
    const hits = [];
    scan(root, root, 20000, (rel, abs) => {
      if (hits.length >= 500) return false;
      if (re.test(rel)) {
        let isDir = false;
        try { isDir = fs.statSync(abs).isDirectory(); } catch (e) {}
        hits.push(rel + (isDir ? '/' : ''));
      }
      return true;
    });
    if (!hits.length) return { output: 'No matches.' };
    return { output: hits.join('\n') };
  } catch (e) {
    return { output: String(e.message || e), is_error: true };
  }
}

function globToRegex(glob) {
  let s = '';
  for (let i = 0; i < glob.length; i++) {
    const c = glob[i];
    if (c === '*') {
      if (glob[i + 1] === '*') { s += '.*'; i++; }
      else s += '[^/]*';
    } else if (c === '?') s += '[^/]';
    else if ('\\^$.|+()[]{}'.includes(c)) s += '\\' + c;
    else s += c;
  }
  return new RegExp('^(.*/)?' + s.replace(/^\.\*/, '.*') + '$');
}

function scan(absRoot, base, budget, cb) {
  const stack = [{ abs: absRoot, rel: '' }];
  let visited = 0;
  while (stack.length && visited < budget) {
    const cur = stack.pop();
    let entries;
    try { entries = fs.readdirSync(cur.abs, { withFileTypes: true }); } catch (e) { continue; }
    for (const ent of entries) {
      if (visited >= budget) break;
      visited++;
      const rel = cur.rel ? cur.rel + '/' + ent.name : ent.name;
      const abs = path.join(cur.abs, ent.name);
      if (ent.isDirectory()) {
        if (SKIP_DIRS.has(ent.name)) continue;
        stack.push({ abs, rel });
      } else if (ent.isFile()) {
        if (cb(rel, abs) === false) return;
      }
    }
  }
}

function safeRead(abs) {
  const buf = fs.readFileSync(abs);
  if (buf.includes(0)) return 'BIN';
  return buf.toString('utf8');
}

async function todoWriteTool(input, ctx) {
  const todos = Array.isArray(input.todos) ? input.todos : [];
  const clean = todos.map(t => ({
    content: String((t && (t.content || t.text)) || '').slice(0, 300),
    status: (t && (t.status === 'completed' || t.status === 'done')) ? 'completed' : 'pending',
    priority: (t && t.priority) || 'medium'
  })).filter(t => t.content);
  if (ctx.conv) ctx.conv.todos = clean;
  if (ctx.onTodos) ctx.onTodos(clean);
  return { output: 'Todos updated (' + clean.length + ').' };
}

async function askUserQuestionTool(input, ctx) {
  /* handled by the engine before exec (emits `question` + done) */
  return { output: '' };
}

const TOOLS = [
  {
    name: 'bash',
    description: 'Execute a shell command inside the project working directory (30s timeout).',
    schema: {
      type: 'object',
      properties: { command: { type: 'string', description: 'The command to run' } },
      required: ['command']
    },
    mutating: true, perm: 'bash', exec: bashTool
  },
  {
    name: 'read_file',
    description: 'Read a text file from the project.',
    schema: {
      type: 'object',
      properties: { path: { type: 'string', description: 'File path relative to the working directory' } },
      required: ['path']
    },
    mutating: false, exec: readFileTool
  },
  {
    name: 'write_file',
    description: 'Create or overwrite a file with new content.',
    schema: {
      type: 'object',
      properties: {
        path: { type: 'string' },
        content: { type: 'string', description: 'Full new file content' }
      },
      required: ['path', 'content']
    },
    mutating: true, perm: 'write_file', exec: writeFileTool
  },
  {
    name: 'edit_file',
    description: 'Replace one unique exact string inside a file.',
    schema: {
      type: 'object',
      properties: {
        path: { type: 'string' },
        old_string: { type: 'string', description: 'Exact text to replace (must be unique in the file)' },
        new_string: { type: 'string', description: 'Replacement text' }
      },
      required: ['path', 'old_string', 'new_string']
    },
    mutating: true, perm: 'edit_file', exec: editFileTool
  },
  {
    name: 'replace_in_file',
    description: 'Replace every occurrence of a string inside a file.',
    schema: {
      type: 'object',
      properties: {
        path: { type: 'string' },
        find: { type: 'string' },
        replace: { type: 'string' }
      },
      required: ['path', 'find', 'replace']
    },
    mutating: true, perm: 'replace_in_file', exec: replaceInFileTool
  },
  {
    name: 'list_files',
    description: 'List directory contents (depth 1-3).',
    schema: {
      type: 'object',
      properties: {
        path: { type: 'string', description: 'Directory relative to the working directory (default: root)' },
        depth: { type: 'integer', minimum: 1, maximum: 3 }
      }
    },
    mutating: false, exec: listFilesTool
  },
  {
    name: 'grep',
    description: 'Search file contents by regex (case-insensitive) inside the project.',
    schema: {
      type: 'object',
      properties: {
        pattern: { type: 'string' },
        path: { type: 'string', description: 'Subdirectory to search (default: root)' },
        glob: { type: 'string', description: 'Optional filename glob like *.js' }
      },
      required: ['pattern']
    },
    mutating: false, exec: grepTool
  },
  {
    name: 'glob',
    description: 'Find files by glob pattern like **/*.js relative to the project.',
    schema: {
      type: 'object',
      properties: { pattern: { type: 'string' }, path: { type: 'string' } },
      required: ['pattern']
    },
    mutating: false, exec: globTool
  },
  {
    name: 'todo_write',
    description: 'Replace the task list for this session. Use when the plan or progress changes.',
    schema: {
      type: 'object',
      properties: {
        todos: {
          type: 'array',
          items: {
            type: 'object',
            properties: {
              content: { type: 'string' },
              status: { type: 'string', enum: ['pending', 'in_progress', 'completed', 'done'] },
              priority: { type: 'string', enum: ['low', 'medium', 'high'] }
            },
            required: ['content']
          }
        }
      },
      required: ['todos']
    },
    mutating: false, perm: null, exec: todoWriteTool
  },
  {
    name: 'ask_user_question',
    description: 'Ask the user a multiple-choice question and pause the turn for their answer.',
    schema: {
      type: 'object',
      properties: {
        question: { type: 'string' },
        header: { type: 'string' },
        options: {
          type: 'array',
          items: {
            type: 'object',
            properties: { label: { type: 'string' }, value: { type: 'string' } },
            required: ['label']
          }
        },
        multiple: { type: 'boolean' },
        custom: { type: 'boolean' },
        customPlaceholder: { type: 'string' }
      },
      required: ['question']
    },
    mutating: false, perm: null, exec: askUserQuestionTool, special: 'question'
  }
];

const TOOL_BY_NAME = Object.fromEntries(TOOLS.map(t => [t.name, t]));

module.exports = { TOOLS, TOOL_BY_NAME, buildSystemPrompt, truncHeadTail, MAX_LLM_OUTPUT, MAX_STORE_OUTPUT, JailError };
