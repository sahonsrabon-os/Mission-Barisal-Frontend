# Code with AI — Node.js Backend (Contract-First Rebuild)

This repository ships the **full UI plus its backend**: the original
`index.php` front-end is kept byte-identical (zero edits), and its server-side
renderer is rebuilt on **Node.js built-in modules only** — no runtime
dependencies, no `npm install` step.

| Fact | Status |
|------|--------|
| `index.php` md5 | `895eb75226cc2801834baaced2873bb1` — 6,490 lines / 310,233 bytes, **0 edits** (asserted by every test run) |
| Contract | 39 routes + 12 stream events — verified byte-for-byte against `index.php` |
| Test results (re-run 2026-09-26) | contract **11/11** · user **11/11** (route coverage 39/39, non-200 = 0) |
| Extra suites | real-provider **13/13** · operator Q&A **18/18** (require external providers / Chrome CDP; history in `docs/test_plan.md`) |
| `<?php` blocks | 0 — the file is plain HTML/JS and must be served by the Node server |

![Two providers Connected in Settings (seeded from .env)](docs/images/env-providers-connected.png)

---

## Features

- **Contract-first**: every route, parameter and stream event is extracted from
  `index.php` and enforced by tests.
- **Node built-ins only** — `child_process`, `http`, `fs`, `crypto`, `net`, `path`.
- **Open-source-compatible providers**: any OpenAI-style endpoint plus
  Ollama/Mission-style gateways.
- **Streaming engine**: tool-call loop, permission modal, todo updates, title
  generation, usage/cost accounting.
- **Jailed workspace**: file/shell access is confined to `OC_ROOTS` inside the
  code itself (path-traversal covered by tests).
- **`.env` configuration**: providers, models, port and tokens without touching
  source code.
- **Transparent logging**: `logs/server.log` (http/llm/env/boot lines, secret
  redaction, `run_id=`) plus raw `logs/console.log`.

![Daily chat in Bengali — real external LLM answering in the UI](docs/images/daily-chat-bangla.png)

---

## Requirements

- **Node.js 18+** (any OS; verified on Node 24, Linux x86_64)
- Internet/network access only if you use a real LLM provider (optional)
- `git` to clone

## Quick start — from clone to running

### Linux / macOS (bash)

```bash
# 1) clone
git clone https://github.com/sahonsrabon-os/Mission-Barisal-Frontend.git
cd Mission-Barisal-Frontend

# 2) configure (once) — providers/port live only here
cp .env.example .env
#   edit .env: PORT, HOST, OC_PROVIDER_* (see .env.example)
chmod 600 .env

# 3) run
node start.js           # same as: npm start  /  ./start.sh
#   each run gets a fresh random run_id; a double start or port
#   conflict is not an error — the launcher shows the running
#   instance and exits.

# 4) open in a browser
#   http://127.0.0.1:8787/     (per PORT/HOST in .env)

# 5) stop
node stop.js            # same as: ./stop.sh
```

### Windows (PowerShell)

```powershell
# 1) clone
git clone https://github.com/sahonsrabon-os/Mission-Barisal-Frontend.git
cd Mission-Barisal-Frontend

# 2) configure (once)
Copy-Item .env.example .env
notepad .env             # edit PORT, HOST, OC_PROVIDER_*

# 3) run
node start.js            # same as: npm start

# 4) open http://127.0.0.1:8787/

# 5) stop
node stop.js
```

> **Honest note:** launcher and server use only Node built-ins, so they are
> platform-neutral — but every test and proof in this repository was actually
> executed on **Linux**. The Windows steps are based on standard Node usage and
> were not re-verified on a Windows machine.

## Configuration (`.env`)

| Key | Meaning | Example |
|-----|---------|---------|
| `PORT` / `HOST` | HTTP port/bind (server default `18800`; sample `.env` sets `8787`) | `PORT=8787`, `HOST=0.0.0.0` (LAN/tunnel) |
| `OC_PROVIDER_<n>_NAME` | provider display name | `Mission Local` |
| `OC_PROVIDER_<n>_BASE_URL` | OpenAI-style base URL | `https://example.com/v1` |
| `OC_PROVIDER_<n>_API_KEY` | bearer key (may stay empty) | `sk-...` |
| `OC_PROVIDER_<n>_MODELS` | `id=Label,id2=Label2` | `mission=Mission Pilot` |
| `OC_PROVIDER_<n>_MODEL` | default model (UI auto-selects it) | `mission` |
| `OC_ACCESS_TOKEN` | when set, gates every API/stream call | — |
| `OC_ROOTS` | extra allowed roots (`:`-separated) | — |
| `OC_LOG_FILE` / `OC_PROVIDERS_FORCE` / `OC_ENV_FILE` | log path / force re-seed / disable `.env` | — |

**Precedence:** real environment variables > `.env` file > built-in defaults
(verified: `unnecessary/evidence/P8/QA_RESULTS.json`, Q6.1). Seeded providers
are created on first boot; entries already edited in the UI stay untouched
unless `OC_PROVIDERS_FORCE=1`.

`.env` is never committed (it is in `.gitignore`); `.env.example` ships as the
sample.

## Logging & runs

- `logs/server.log` — timestamped `http`/`llm`/`env`/`boot` lines, secrets redacted
- `logs/console.log` — raw server stdout/stderr
- `logs/server.pid` + `logs/last-run.json` — live process and the **random UUID
  of each run**
- boot lines carry `run_id=<uuid>` so multiple runs on the same day stay
  separable in the log

```bash
tail -f logs/server.log     # application log
tail -f logs/console.log    # raw output
```

## Testing

All tests run with Node only (UI click-through via Chrome CDP; the real-provider
suite is optional):

```bash
npm test                    # contract + user (fast, controlled fake provider)
npm run test:contract       # 39 routes + 12 events contract      -> 11/11
npm run test:user           # U* UI click-through                 -> 11/11
npm run test:real           # R* real ngrok-Ollama + Mission chat -> 13/13
npm run test:qna            # Q* operator question investigation  -> 18/18
```

`npm test` was re-run on 2026-09-26 (both suites 11/11). The `test:real` and
`test:qna` suites need external providers or Chrome CDP; their recorded results
live in `docs/test_plan.md` and the evidence folder.

![Real provider chat (ngrok Ollama) — click-through test screenshot](docs/images/real-chat-ollama.png)

![Chat with tool calls + diff viewer](docs/images/chat-with-tools.png)

![Shell terminal (!cmd) — token-gated](docs/images/shell-terminal.png)

![UI from a LAN origin — same-origin API behind a tunnel](docs/images/lan-tunnel-ready.png)

## Deployment notes

- **Serve with Node only** — `index.php` contains no `<?php` blocks; PHP-FPM or
  cPanel will download it or render a blank page. On cPanel hosts run the Node
  app (Passenger/LSM) or put a tunnel (ngrok/cloudflared) in front.
- API calls use **same-origin relative paths** (`ai_php_api.php`), so the UI
  works identically from a LAN IP or a public tunnel origin (verified:
  `unnecessary/evidence/P8/Q6_lan_origin.png`).
- When exposing a public tunnel, set `HOST=0.0.0.0` together with
  `OC_ACCESS_TOKEN`, and never share `.env` or logs.

## Project structure

```
.
├── index.php                 # the original UI — untouched contract source (md5 checked by tests)
├── server/
│   ├── server.js             # HTTP server + route dispatch + healthz
│   ├── lib/                  # env, log, providers, stream engine, routes, store, jail, ...
│   ├── test/                 # contract / user / real_provider / qna suites
│   └── README.md             # architecture and route-level documentation
├── start.js / stop.js        # cross-platform launcher (run_id UUID; never blocks)
├── start.sh / stop.sh        # convenience wrappers (same behavior)
├── package.json              # npm start / npm test scripts (no dependencies)
├── .env.example              # configuration sample  (.env itself is never committed)
├── docs/                     # documentation + README screenshots
│   ├── test_plan.md          # full test matrix and honest result history
│   ├── QUESTIONS_AND_ANSWERS.md
│   └── images/
└── unnecessary/              # test evidence and archived analysis material — not product code
    ├── evidence/             # test-result JSON/MD, screenshots, run logs
    ├── extract_contract.py   # contract extraction tool
    └── *.html / *.txt        # archived analysis documents
```

## Documentation

- **[docs/test_plan.md](docs/test_plan.md)** — test plan, result logs, and the
  honest history of found-and-fixed issues
- **[docs/QUESTIONS_AND_ANSWERS.md](docs/QUESTIONS_AND_ANSWERS.md)** — seven
  operator questions, each answered by running it, with result tables (18/18)
- **[server/README.md](server/README.md)** — architecture, the 39 routes,
  configuration tables, troubleshooting

## Integrity statement

Every claim in this repository is verifiable against the proof files recorded
under `unnecessary/evidence/`; no result was invented. `index.php` has never
been modified in any commit (every test run re-checks its md5 — current value:
`895eb75226cc2801834baaced2873bb1`).

---

Built by **Sahon Srabon · Developer Zone · Dhaka, Bangladesh** —
evidence first, then conclusion.
