'use strict';
/*
 * auth.js — sessions, CSRF, optional access-token login gate.
 * The UI file is never edited: CSRF `C` is injected at render time.
 *   - API/stream requests require a live csrf_token (UI always sends C).
 *   - With OC_ACCESS_TOKEN set, sessions only exist after /login (or ?token=),
 *     so `shell` is unreachable without the token (test E6.3).
 */
const crypto = require('crypto');

const COOKIE = 'oc_sid';
const sessions = new Map(); // sid -> {csrf, created, authed}
const ACCESS_TOKEN = process.env.OC_ACCESS_TOKEN || '';

function parseCookies(req) {
  const out = {};
  const h = req.headers && req.headers.cookie;
  if (!h) return out;
  for (const part of h.split(';')) {
    const i = part.indexOf('=');
    if (i < 0) continue;
    out[part.slice(0, i).trim()] = decodeURIComponent(part.slice(i + 1).trim());
  }
  return out;
}

function getSid(req) { return parseCookies(req)[COOKIE] || null; }
function getSession(sid) { return sid ? sessions.get(sid) || null : null; }

function createSession(authed) {
  const sid = crypto.randomBytes(18).toString('hex');
  const sess = {
    csrf: crypto.randomBytes(18).toString('hex'),
    created: Date.now(),
    authed: ACCESS_TOKEN ? !!authed : true
  };
  sessions.set(sid, sess);
  return { sid, sess };
}

function tokenEqual(a, b) {
  const ba = Buffer.from(String(a || ''));
  const bb = Buffer.from(String(b || ''));
  if (ba.length !== bb.length) return false;
  return crypto.timingSafeEqual(ba, bb);
}

function checkCsrf(token) {
  if (!token) return null;
  for (const s of sessions.values()) {
    if (tokenEqual(s.csrf, token)) return s;
  }
  return null;
}

function setCookie(res, sid) {
  res.setHeader('Set-Cookie',
    `${COOKIE}=${sid}; Path=/; HttpOnly; SameSite=Lax; Max-Age=${7 * 24 * 3600}`);
}

function loginPage(next) {
  const nx = String(next || '/').replace(/[<>"'`]/g, '');
  const localNote = ACCESS_TOKEN
    ? 'Enter the access token configured for this server.'
    : 'Local mode: token gate disabled — enter anything.';
  return `<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1"><title>Login - Code with AI</title>
<style>
body{margin:0;font-family:ui-sans-serif,system-system,-apple-system,Segoe UI,Roboto,sans-serif;background:#0b1220;color:#e5e7eb;display:flex;min-height:100vh;align-items:center;justify-content:center}
.card{background:#121826;border:1px solid #2b3445;border-radius:14px;padding:28px;width:320px;box-shadow:0 8px 24px rgba(0,0,0,.35)}
h1{font-size:16px;margin:0 0 6px}.sub{color:#64748b;font-size:12px;margin-bottom:16px}
label{display:block;font-size:12px;color:#94a3b8;margin-bottom:6px}
input{width:100%;box-sizing:border-box;padding:10px 12px;border-radius:10px;border:1px solid #2b3445;background:#0f1623;color:#e5e7eb;font-size:14px}
button{margin-top:16px;width:100%;padding:10px;border:0;border-radius:10px;background:#3b82f6;color:#fff;font-weight:600;cursor:pointer}
button:hover{background:#2563eb}
</style></head><body>
<form class="card" method="POST" action="/login">
<h1>Code with AI</h1>
<div class="sub">${localNote}</div>
<label>Access token</label>
<input type="password" name="token" autofocus autocomplete="current-password">
<input type="hidden" name="next" value="${nx}">
<button type="submit">Sign in</button>
</form></body></html>`;
}

module.exports = {
  COOKIE, ACCESS_TOKEN, parseCookies, getSid, getSession, createSession,
  tokenEqual, checkCsrf, setCookie, loginPage, sessions
};
