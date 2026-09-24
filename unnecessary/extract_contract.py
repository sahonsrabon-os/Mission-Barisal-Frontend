#!/usr/bin/env python3
"""
Phase-1 contract extractor (Engineer perspective).
Reads the UI source WITHOUT MODIFYING IT and writes evidence files.
Evidence goes to: evidence/P1/
"""
import hashlib, json, os, re, subprocess, sys

SRC = "/home/sahon/Videos/ui/index.php"
EV = "/home/sahon/Videos/ui/evidence/P1"
CS = os.path.join(EV, "callsites")
EXPECT_MD5 = "895eb75226cc2801834baaced2873bb1"

os.makedirs(CS, exist_ok=True)

raw = open(SRC, "rb").read()
text = raw.decode("utf-8", errors="replace")
lines = text.split("\n")


def w(path, content):
    with open(path, "w", encoding="utf-8") as f:
        f.write(content)
    print("  wrote", os.path.relpath(path, "/home/sahon/Videos/ui"))


def sh(cmd):
    return subprocess.run(cmd, shell=True, capture_output=True, text=True).stdout.strip()


# ---------- E1.1 source integrity ----------
md5 = hashlib.md5(raw).hexdigest()
n_lines = text.count("\n") + (0 if text.endswith("\n") else 1)
size = len(raw)
e11 = f"""E1.1 — UI source integrity (Engineer)
command: md5sum / stat -c%s / wc -l  (read-only)
--------------------------------------------------
md5sum       : {md5}
expected     : {EXPECT_MD5}
MATCH        : {'YES  ✅ source byte-identical (0 edits)' if md5 == EXPECT_MD5 else 'NO  ❌ SOURCE CHANGED'}
size(bytes)  : {size}
lines        : {n_lines}
--------------------------------------------------
NOTE: every phase re-runs this check (see E4.1).
"""
w(os.path.join(EV, "E1.1_source_integrity.txt"), e11)

# ---------- E1.2 php blocks ----------
php_count = len(re.findall(r"<\?php", text))
e12 = f"""E1.2 — Language neutrality (Engineer)
command: grep -c '<?php' index.php
--------------------------------------------------
php blocks   : {php_count}
verdict      : {'0 PHP blocks -> server language is FREE (Node default modules OK)  ✅' if php_count == 0 else 'PHP found -> unexpected  ❌'}
extension    : .php (name only)
actual       : pure HTML + JavaScript
"""
w(os.path.join(EV, "E1.2_php_blocks.txt"), e12)

# ---------- route extraction ----------
api_routes = sorted(set(re.findall(r"api\('([a-z_]+)'", text)))
raw_routes = sorted(set(re.findall(r"ai_php_api=([a-z_]+)", text)))
stream_route = "ai_chat_stream" if "ai_chat_stream=1" in text else None
union = sorted(set(api_routes) | set(raw_routes) | ({stream_route} if stream_route else set()))
overlap = sorted(set(api_routes) & set(raw_routes))

# api() call sites with params
calls = []
for m in re.finditer(r"api\('([a-z_]+)'\s*,\s*(.*?)\s*,\s*function", text, re.S):
    name, data = m.group(1), m.group(2).strip()
    line_no = text[: m.start()].count("\n") + 1
    params = []
    if data.startswith("{"):
        body = data[1:]
        depth = 0
        cur = ""
        for ch in body:
            if ch == "{":
                depth += 1
            elif ch == "}":
                if depth == 0:
                    break
                depth -= 1
            if ch == ":" and depth == 0:
                key = cur.strip()
                if re.fullmatch(r"[A-Za-z_][A-Za-z0-9_]*", key):
                    params.append(key)
                cur = ""
                continue
            cur += ch
        method = "POST"
    elif data == "{}":
        method = "POST"
    else:
        method = "GET"
        params = []
    dyn = data.startswith("bp(") or (data not in ("null", "{}") and not data.startswith("{"))
    calls.append({"route": name, "line": line_no, "method": method,
                  "params": params, "raw_arg": data[:120],
                  "params_source": "dynamic(object)" if (dyn and not params) else "literal"})

# raw XHR routes + stream
raw_sites = []
for m in re.finditer(r"ai_php_api=([a-z_]+)", text):
    ln = text[: m.start()].count("\n") + 1
    raw_sites.append({"route": m.group(1), "line": ln})
stream_m = re.search(r"ai_chat_stream=1", text)
stream_line = text[: stream_m.start()].count("\n") + 1 if stream_m else None

# ---------- E1.3 routes ----------
e13 = f"""E1.3 — Route inventory = 39 (Engineer)
commands: grep -oE "api\\('[a-z_]+'" | sort -u
           grep -oE "ai_php_api=[a-z_]+" | sort -u
           grep "ai_chat_stream=1"
--------------------------------------------------
api() routes          : {len(api_routes)}   -> {', '.join(api_routes)}
raw XHR routes        : {len(raw_routes)}   -> {', '.join(raw_routes)}
overlap (both places) : {len(overlap)}   -> {', '.join(overlap)}
stream route          : 1   -> {stream_route}
UNION (unique total)  : {len(union)}   {'✅ = 39' if len(union) == 39 else '❌ expected 39'}
--------------------------------------------------
unique = api({len(api_routes)}) + raw-not-in-api({len(raw_routes) - len(overlap)}) + stream(1) = {len(api_routes) + len(raw_routes) - len(overlap) + 1}
"""
w(os.path.join(EV, "E1.3_routes.txt"), e13)
w(os.path.join(EV, "routes_list.txt"), "\n".join(union) + "\n")

# ---------- E1.4 stream events ----------
events = sorted(set(re.findall(r"ev==='([a-z-]+)'", text)))
e14 = f"""E1.4 — Stream events = 12 (Engineer)
command: grep -oE "ev==='[a-z-]+'" index.php | sort -u
--------------------------------------------------
count  : {len(events)}   {'✅ = 12' if len(events) == 12 else '❌ expected 12'}
events : {', '.join(events)}
--------------------------------------------------
UI parse rule : line must start with 'data: ' then JSON.parse(line.substring(6))
                -> server MUST emit:  data: {{"_event":"<name>", ...}}\\n
"""
w(os.path.join(EV, "E1.4_stream_events.txt"), e14)

# ---------- E1.6 api() function ----------
m = re.search(r"function api\(a,d,cb\)\{", text)
start = text[: m.start()].count("\n") + 1
block = "\n".join(lines[start - 1: start + 15])
e16 = f"""E1.6 — Request protocol is FIXED (Engineer)
source: index.php line {start}
--------------------------------------------------
{block}
--------------------------------------------------
VERDICT:
  URL pattern      : <A>&ai_php_api=<route>&csrf_token=<C>
  method rule      : data present -> POST, absent -> GET
  content-type     : application/x-www-form-urlencoded
  accept           : application/json
  response         : HTTP 200 + JSON body (else UI shows 'HTTP <code>')
=> every server route must honour exactly this.
"""
w(os.path.join(EV, "E1.6_api_function.txt"), e16)

# ---------- E1.7 XHR points ----------
opens = [(text[: m.start()].count("\n") + 1, m.group(0))
         for m in re.finditer(r"\.open\(", text)]
e17 = f"""E1.7 — XHR construction points (Engineer)
command: grep -c '.open(' index.php
--------------------------------------------------
count : {len(opens)}   {'✅ = 8' if len(opens) == 8 else 'note: counted ' + str(len(opens))}
lines : {', '.join(str(l) for l, _ in opens)}
note  : 1 of them lives inside api() (covers 35 routes); 4 raw routes + 1 stream + extras.
"""
w(os.path.join(EV, "E1.7_xhr_points.txt"), e17)

# ---------- E1.8 call-site dumps (field evidence source) ----------
def dump_around(line_no, before=4, after=40):
    a = max(0, line_no - 1 - before)
    b = min(len(lines), line_no - 1 + after)
    out = []
    for i in range(a, b):
        out.append(f"{i+1}: {lines[i]}")
    return "\n".join(out)


for c in calls:
    body = dump_around(c["line"], 2, 42)
    fields = sorted(set(re.findall(r"\b(?:d|r|data|s|p|qData)\.([A-Za-z_][A-Za-z0-9_]*)", body)))
    c["response_fields_observed"] = fields
    w(os.path.join(CS, f"{c['route']}_L{c['line']}.txt"),
      f"# route: {c['route']}  line: {c['line']}  method: {c['method']}\n"
      f"# params(arg): {c['raw_arg']}\n"
      f"# response field reads seen in callback: {', '.join(fields) if fields else '(none/indirect)'}\n"
      f"# NOTE: best-effort regex scan; verify against this dump.\n"
      f"{'-'*70}\n{body}\n")

for rs in raw_sites:
    w(os.path.join(CS, f"RAW_{rs['route']}_L{rs['line']}.txt"),
      f"# raw XHR route: {rs['route']}  line: {rs['line']}\n{'-'*70}\n{dump_around(rs['line'], 4, 42)}\n")

if stream_line:
    w(os.path.join(CS, f"STREAM_ai_chat_stream_L{stream_line}.txt"),
      f"# stream request builder  line: {stream_line}\n{'-'*70}\n{dump_around(stream_line, 22, 60)}\n")

# ---------- E1.5 contract.json ----------
contract = {
    "meta": {
        "source": SRC, "md5": md5, "expected_md5": EXPECT_MD5,
        "md5_match": md5 == EXPECT_MD5, "bytes": size, "lines": n_lines,
        "php_blocks": php_count, "xhr_points": len(opens),
        "api_fn_line": start,
        "base_url_line": next((i+1 for i, l in enumerate(lines) if l.startswith("var A=")), None),
        "generated_by": "extract_contract.py (read-only)",
    },
    "routes": {
        "total_unique": len(union), "expected": 39,
        "api_fn": api_routes, "raw_xhr": raw_routes, "stream": stream_route,
        "list": union,
    },
    "route_calls": calls,
    "raw_xhr_sites": raw_sites,
    "stream": {"route": stream_route, "line": stream_line,
               "events_expected": events, "events_count": len(events),
               "wire_format": "data: {json}\\n", "event_field": "_event"},
    "protocol": {"url": "<A>&ai_php_api=<route>&csrf_token=<C>",
                 "method": "POST if data else GET",
                 "content_type": "application/x-www-form-urlencoded",
                 "accept": "application/json", "success": "HTTP 200 + JSON"},
}
w(os.path.join(EV, "contract.json"), json.dumps(contract, indent=2, ensure_ascii=False))

# ---------- summary ----------
summary = f"""PHASE 1 — ENGINEER EVIDENCE SUMMARY
====================================
E1.1 md5 match        : {'PASS' if md5 == EXPECT_MD5 else 'FAIL'}  ({md5})
E1.2 php blocks == 0  : {'PASS' if php_count == 0 else 'FAIL'}  ({php_count})
E1.3 routes == 39     : {'PASS' if len(union) == 39 else 'FAIL'}  ({len(union)})
E1.4 events == 12     : {'PASS' if len(events) == 12 else 'FAIL'}  ({len(events)})
E1.5 contract.json    : WRITTEN  ({len(calls)} api calls captured)
E1.6 protocol captured: PASS  (line {start})
E1.7 xhr points       : INFO  ({len(opens)} .open( sites)
E1.8 callsite dumps   : WRITTEN  ({len(calls) + len(raw_sites) + (1 if stream_line else 0)} files)

artifacts: {EV}
"""
w(os.path.join(EV, "SUMMARY.txt"), summary)
print("\n" + summary)
sys.exit(0 if (md5 == EXPECT_MD5 and php_count == 0 and len(union) == 39 and len(events) == 12) else 1)
