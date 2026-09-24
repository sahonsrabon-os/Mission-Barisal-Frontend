# Q&A Run — operator questions answered by doing (labels: Q*)

Bismillah. Every row below was executed in real Chrome (CDP) against a real
server started the way an operator actually starts it (`.env` + `start.sh`
semantics; OC_ENV_FILE unset => ui/.env loaded).

- Elapsed: 112.0s — LAN origin tested: http://192.168.1.145:18921/
- Servers: no-env phase :18920, env phase :18921 (HOST=0.0.0.0 via real env)
- Logs: evidence/P8 (server logs copied below), ui/logs/server.log in daily use

## Results

| id | question verified | result | note |
|----|-------------------|--------|------|
| Q4.1 | UI boots cleanly with zero providers (no LLM configured) | ✅ PASS | status=No provider connected connected_customs=0 note=dropdown groups=0 |
| Q4.2 | Model dropdown correctly empty (no connected provider) — user simply cannot chat yet, no crash | ✅ PASS | groups=0 |
| Q2.1 | start script + .env: config file loaded, providers seeded — zero source edits | ✅ PASS | keys=12 applied=11" \| created=2 |
| Q2.2 | both .env providers visible to UI as Connected, keys never sent to browser | ✅ PASS | ["custom:ollama-remote:true:jaahas/qwen3.5-uncensored:2b","custom:mission-local:true:mission"] keyLeak=false |
| Q2.3 | UI auto-selects the admin-chosen default model from .env (no click needed) | ✅ PASS | label=Qwen3.5 Uncensored 2B |
| Q2.4 | Settings lists both env providers Connected without touching the UI | ✅ PASS | rows snippet: Anthropic4 modelsNot setConnectDeepSeek2 modelsNot setConnectGoogle3 modelsNot setConnectOllama3 modelsNot setConnectOpenAI5 modelsNot setConnectOpenCode Zen7 modelsNot setConnectOllama Remote2 modelsConnectedDisconnectMission Local2 models |
| Q7.1 | daily-use Bengali chat answered by a REAL external LLM through the UI | ✅ PASS | model=Qwen3.5 Uncensored 2B reply(175c): AI আপনি যাচ্ছিছে একটি প্রশ্নের উত্তর দেওয়ার জন্য। আমি চিঠিপত্র থেকে এই বক্তব্যটি পাওয়া যাচ্ছে: "প্রশ্নে রাজধানীর নাম কী, বাংলায় এক লাইনে উত্তর দিন।" তাহলে উত্তর হলো: ঢাকা |
| Q7.2 | file tree opens and lists files (per-project workdir jail) | ✅ PASS | route=true tree rows=1 |
| Q7.3 | New Session button starts a fresh conversation | ✅ PASS | new_session fired=true |
| Q7.4 | phase-B route log: every response HTTP 200 | ✅ PASS | routes=37 non200=[] |
| Q6.1 | real environment variable beats .env value (precedence documented behaviour) | ✅ PASS | T08:36:28.741Z INFO  boot "READY pid=11355 host=0.0.0.0 port=18921 access_token=off roots=/tmp/oc_qna/home_b log=/tmp/oc_qna/server_lan.log" |
| Q6.2 | UI fully works from a DIFFERENT origin (http://192.168.1.145:18921/) — same-origin API path, tunnel-ready | ✅ PASS | providers status=200 customs=2 |
| Q6.3 | route log over LAN origin: HTTP 200 everywhere (proxies/tunnels behave like this origin) | ✅ PASS | routes=7 non200=[] |
| Q1.1 | backend+UI are independent of localhost:3000 — contract & user suites PASS with fakes only | ✅ PASS | contract=11/11 user=11/11 |
| Q1.2 | localhost:3000 never referenced by product code (only optional live test/docs/config) | ✅ PASS | total hits=15 product-code hits=0 (see evidence/P8/Q1_grep_localhost3000.txt) |
| Q3.1 | index.php untouched through every phase (0 edits) | ✅ PASS | md5=895eb75226cc2801834baaced2873bb1 |
| Q5.1 | both admin (.env seed — proved in Q2) and frontend (Settings UI — U6.1) can add LLMs | ✅ PASS | env_seeded=2 ui_add(U6.1)=true |
| Q5.2 | zero JavaScript exceptions across all phases | ✅ PASS | none |

## Honest notes

- External LLM replies are live (ngrok Ollama / Mission localhost:3000); upstream
  hiccups are retried once (backend 502/503/429 retry + test-level retry) and any
  remaining trouble is recorded verbatim in the notes column and in notes[].
- `default_model` auto-select uses the env provider's MODEL value (settings.json field),
  consumed by the untouched UI hook at index.php ("first connected provider that
  exposes a default_model wins").
- HOST precedence proof: .env says 127.0.0.1, real env said 0.0.0.0 -> READY shows 0.0.0.0.

## Run notes[]

- none

## Exceptions / dialogs

- JS exceptions: none
- dialogs: []
