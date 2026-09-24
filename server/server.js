'use strict';
/*
 * server.js — HTTP entry point (Node default modules only).
 *
 *   GET  /                    -> renders index.php WITHOUT editing the file
 *                                 (A / C / SP / HD / PID injected at render)
 *   GET|POST /index.live.php  -> API dispatcher (39 routes + ai_chat_stream)
 *   GET|POST /login,/logout   -> optional access-token gate (protects shell)
 *   /themes/*, /favicon.ico   -> 204 (referenced by UI, not shipped)
 *
 * Security model:
 *   - every API/stream request must carry a live session CSRF token (UI var C)
 *   - with OC_ACCESS_TOKEN set, sessions exist only after login => shell is
 *     unreachable without the token (E6.3)
 *   - all file/shell access is jailed to OC_ROOTS (E5.2)
 */
const env = require('./lib/env');
env.load(); // .env FIRST: PORT/HOST/OC_* provider specs become process.env
const log = require('./lib/log');

const http = require('http');
const { URL } = require('url');

const store = require('./lib/store');
const jail = require('./lib/jail');
const auth = require('./lib/auth');
const renderMod = require('./lib/render');
const { routes } = require('./lib/routes');
const stream = require('./lib/stream');

const PORT = parseInt(process.env.PORT || '18800', 10);
const HOST = process.env.HOST || '127.0.0.1';
const BODY_LIMIT = 25 * 1024 * 1024;

jail.ensureHome();
store.load();

/* admin-managed providers from the environment (.env) — see lib/env.js */
{
  const s = env.summary();
  log.info('env', s.skipped
    ? 'file loading disabled (OC_ENV_FILE=)'
    : 'file=' + s.file + (s.error ? ' error=' + s.error : ' keys=' + s.keys.length + ' applied=' + s.applied));
  for (const a of env.seedProviders()) {
    log.info('env', 'provider ' + (a.id || '?') + ' -> ' + a.action + (a.base ? ' base=' + a.base : ''));
  }
}

function sendJson(res, status, obj, extraHeaders) {
  if (res.headersSent || res.writableEnded) return;
  res.writeHead(status, Object.assign({
    'Content-Type': 'application/json; charset=utf-8',
    'Cache-Control': 'no-store',
    'X-Content-Type-Options': 'nosniff'
  }, extraHeaders || {}));
  res.end(JSON.stringify(obj));
}

function readBody(req) {
  return new Promise((resolve, reject) => {
    const chunks = [];
    let size = 0;
    req.on('data', chunk => {
      size += chunk.length;
      if (size > BODY_LIMIT) {
        reject(Object.assign(new Error('Body too large'), { status: 413 }));
        req.destroy();
        return;
      }
      chunks.push(chunk);
    });
    req.on('end', () => resolve(Buffer.concat(chunks).toString('utf8')));
    req.on('error', reject);
  });
}

function parseForm(str) {
  const out = {};
  try {
    for (const [k, v] of new URLSearchParams(str || '')) out[k] = v;
  } catch (e) {}
  return out;
}

async function handleLogin(req, res) {
  const body = parseForm(await readBody(req));
  const nextRaw = String(body.next || '/');
  const next = nextRaw.startsWith('/') && !nextRaw.startsWith('//') ? nextRaw : '/';
  if (auth.tokenEqual(body.token, auth.ACCESS_TOKEN)) {
    const { sid } = auth.createSession(true);
    auth.setCookie(res, sid);
    res.writeHead(302, { Location: next });
    res.end();
  } else {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(auth.loginPage(next).replace('</form>',
      '<div style="color:#ef4444;font-size:12px;margin-top:10px">Invalid token. Try again.</div></form>'));
  }
}

async function handlePage(req, res, u) {
  const qToken = u.searchParams.get('token');
  let sid = auth.getSid(req);
  let sess = auth.getSession(sid);

  if (auth.ACCESS_TOKEN && qToken && auth.tokenEqual(qToken, auth.ACCESS_TOKEN)) {
    const created = auth.createSession(true);
    sid = created.sid;
    sess = created.sess;
    auth.setCookie(res, sid);
  }
  if (auth.ACCESS_TOKEN && (!sess || !sess.authed)) {
    res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end(auth.loginPage(u.pathname + u.search));
    return;
  }
  if (!sess) {
    const created = auth.createSession(false);
    sid = created.sid;
    sess = created.sess;
    auth.setCookie(res, sid);
  }

  const { html, stats } = renderMod.render(
    Object.fromEntries(u.searchParams.entries()), sess.csrf);

  if (stats.remainingOldAccountRefs > 0) {
    log.warn('render', 'old-account references remain: ' + stats.remainingOldAccountRefs);
  }
  res.writeHead(200, {
    'Content-Type': 'text/html; charset=utf-8',
    'Cache-Control': 'no-store',
    'X-Content-Type-Options': 'nosniff'
  });
  res.end(html);
}

async function handleApi(req, res, u) {
  const search = Object.fromEntries(u.searchParams.entries());
  let body = {};
  if (req.method === 'POST' || req.method === 'PUT') {
    const raw = await readBody(req);
    const ct = String(req.headers['content-type'] || '');
    if (ct.includes('application/json') && raw) {
      try { body = JSON.parse(raw); } catch (e) { body = {}; }
    } else {
      body = parseForm(raw);
    }
  }
  const q = Object.assign({}, search, body);

  // 1) CSRF — live session token (login-gated when ACCESS_TOKEN set)
  const sess = auth.checkCsrf(q.csrf_token);
  if (!sess || (auth.ACCESS_TOKEN && !sess.authed)) {
    sendJson(res, 403, { error: 'invalid csrf token (login required)' });
    return;
  }

  // 2) project + per-request working directory (URL path + project_id)
  const pid = q.project_id ? String(q.project_id) : null;
  const project = pid ? store.getProject(pid) : null;
  if (!project || !jail.withinRoots(project.path)) {
    sendJson(res, 404, { error: 'unknown project: ' + (pid || '(none)') });
    return;
  }

  const ctx = {
    req, res, q, project, workdir: project.path, sess,
    _status: 200,
    status(code) { ctx._status = code; }
  };

  // 3) stream route
  if ('ai_chat_stream' in search || 'ai_chat_stream' in body) {
    stream.handleStream(ctx).catch(err => {
      log.error('stream', 'fatal: ' + (err && err.stack || err));
      try {
        if (!res.writableEnded && !res.destroyed) {
          res.write('data: ' + JSON.stringify({
            _event: 'error', message: String(err.message || err), conversation_id: null
          }) + '\n');
          res.end();
        }
      } catch (e) {}
    });
    return;
  }

  // 4) regular JSON routes
  const route = q.ai_php_api ? String(q.ai_php_api) : null;
  if (!route || !routes[route]) {
    sendJson(res, 404, { error: 'unknown route: ' + (route || '(none)') });
    return;
  }
  try {
    const out = await routes[route](ctx);
    sendJson(res, ctx._status || 200, out == null ? {} : out);
  } catch (err) {
    log.error('route ' + route, String((err && err.stack) || err));
    sendJson(res, 500, { error: String((err && err.message) || err) });
  }
}

const server = http.createServer(async (req, res) => {
  const tReq = Date.now();
  let u;
  try {
    u = new URL(req.url, 'http://' + (req.headers.host || 'localhost'));
  } catch (e) {
    sendJson(res, 400, { error: 'bad request' });
    return;
  }
  const p = u.pathname;
  const routeQ = u.searchParams.get('ai_php_api');
  /* one detailed line per completed response (stream lines appear at stream end) */
  res.on('finish', () => {
    log.info('http', req.method + ' ' + p +
      (routeQ ? ' route=' + routeQ : '') +
      ' -> ' + res.statusCode + ' ' + (Date.now() - tReq) + 'ms');
  });

  try {
    if (p === '/login') {
      if (req.method === 'POST') return await handleLogin(req, res);
      res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
      return res.end(auth.loginPage('/'));
    }
    if (p === '/logout') {
      const sid = auth.getSid(req);
      if (sid) auth.sessions.delete(sid);
      res.writeHead(302, { Location: '/login',
        'Set-Cookie': auth.COOKIE + '=; Path=/; HttpOnly; Max-Age=0' });
      return res.end();
    }
    if (p === '/healthz') {
      return sendJson(res, 200, { ok: true, routes: Object.keys(routes).length + 1 });
    }
    if (p.endsWith('index.live.php')) {
      return await handleApi(req, res, u);
    }
    if (p === '/' || p === '/index.php' || p === '') {
      return await handlePage(req, res, u);
    }
    if (p.startsWith('/themes/') || p.endsWith('/favicon.ico')) {
      res.writeHead(204);
      return res.end();
    }
    sendJson(res, 404, { error: 'Not found: ' + p });
  } catch (err) {
    log.error('http', String((err && err.stack) || err));
    if (!res.headersSent) sendJson(res, err.status || 500, { error: String(err.message || err) });
    else try { res.end(); } catch (e) {}
  }
});

server.listen(PORT, HOST, () => {
  log.info('boot', 'READY pid=' + process.pid + ' host=' + HOST + ' port=' + PORT +
    ' access_token=' + (auth.ACCESS_TOKEN ? 'on' : 'off') +
    ' roots=' + jail.OC_ROOTS.join(',') +
    ' log=' + (log.file() || '(stdout only)') +
    (process.env.OC_RUN_ID ? ' run_id=' + process.env.OC_RUN_ID : ''));
});

for (const sig of ['SIGINT', 'SIGTERM']) {
  process.on(sig, () => {
    try { store.saveNow(); } catch (e) {}
    server.close(() => process.exit(0));
    setTimeout(() => process.exit(0), 800);
  });
}
