'use strict';
/*
 * diff.js — minimal unified diff (no npm).
 */
const MAX_LINES = 4000;

function splitLines(s) {
  if (s === '' || s == null) return [];
  const a = String(s).split('\n');
  if (a.length && a[a.length - 1] === '') a.pop();
  return a;
}

function lcsOps(a, b) {
  // classic LCS DP; guard for huge inputs with a coarse fallback
  if (a.length * b.length > 4_000_000) {
    const ops = [];
    for (let i = 0; i < Math.min(a.length, b.length); i++) {
      if (a[i] === b[i]) ops.push(['=', i, a[i]]);
      else { ops.push(['-', i, a[i]]); ops.push(['+', i, b[i]]); }
    }
    for (let i = Math.min(a.length, b.length); i < a.length; i++) ops.push(['-', i, a[i]]);
    for (let i = Math.min(a.length, b.length); i < b.length; i++) ops.push(['+', i, b[i]]);
    return ops;
  }
  const n = a.length, m = b.length;
  const dp = Array.from({ length: n + 1 }, () => new Int32Array(m + 1));
  for (let i = n - 1; i >= 0; i--) {
    for (let j = m - 1; j >= 0; j--) {
      dp[i][j] = a[i] === b[j] ? dp[i + 1][j + 1] + 1 : Math.max(dp[i + 1][j], dp[i][j + 1]);
    }
  }
  const ops = [];
  let i = 0, j = 0;
  while (i < n && j < m) {
    if (a[i] === b[j]) { ops.push(['=', i, a[i]]); i++; j++; }
    else if (dp[i + 1][j] >= dp[i][j + 1]) { ops.push(['-', i, a[i]]); i++; }
    else { ops.push(['+', j, b[j]]); j++; }
  }
  while (i < n) { ops.push(['-', i, a[i]]); i++; }
  while (j < m) { ops.push(['+', j, b[j]]); j++; }
  return ops;
}

function unifiedDiff(before, after, label) {
  const A = splitLines(before).slice(0, MAX_LINES);
  const B = splitLines(after).slice(0, MAX_LINES);
  const ops = lcsOps(A, B);
  const ctx = 3;
  const changed = [];
  ops.forEach((o, idx) => { if (o[0] !== '=') changed.push(idx); });
  if (!changed.length) return '';
  const keep = new Set();
  for (const idx of changed) {
    for (let k = Math.max(0, idx - ctx); k <= Math.min(ops.length - 1, idx + ctx); k++) keep.add(k);
  }
  const out = [`--- a/${label}`, `+++ b/${label}`];
  let hunk = null;
  const flush = () => {
    if (!hunk) return;
    out.push(`@@ -${hunk.aStart},${hunk.aCount} +${hunk.bStart},${hunk.bCount} @@`);
    out.push(...hunk.lines);
    hunk = null;
  };
  let aLine = 1, bLine = 1;
  ops.forEach((o, idx) => {
    if (!keep.has(idx)) {
      if (o[0] === '=') { aLine++; bLine++; }
      else if (o[0] === '-') aLine++;
      else bLine++;
      return;
    }
    if (!hunk) hunk = { aStart: aLine, bStart: bLine, aCount: 0, bCount: 0, lines: [] };
    if (o[0] === '=') { hunk.lines.push(' ' + o[2]); hunk.aCount++; hunk.bCount++; aLine++; bLine++; }
    else if (o[0] === '-') { hunk.lines.push('-' + o[2]); hunk.aCount++; aLine++; }
    else { hunk.lines.push('+' + o[2]); hunk.bCount++; bLine++; }
  });
  flush();
  return out.join('\n');
}

module.exports = { unifiedDiff };
