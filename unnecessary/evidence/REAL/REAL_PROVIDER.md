# Real Provider Click-Through Test (labels: R*)

Bismillah. All provider operations below were performed **through the UI in a real
headless Chrome** (CDP): clicks on Settings -> Add Connection -> Custom -> fill ->
`Test Connection` -> `Connect`, then model dropdown pick and `Send` for chat.

- Server: fresh instance, port 18910 (OC_DATA=/tmp/oc_real/data)
- Chrome: headless, CDP port 9446
- Provider A: Ollama Remote — https://monkhood-unstaffed-catalog.ngrok-free.dev/v1 (Ollama proxy, key: sk-ollama-ngrok-secret)
- Provider B: Mission Local — http://localhost:3000/v1 (Mission Barisal, key: )
- Elapsed: 154.7s
- Chat attempts (A/B): 1 each max; retries: none

## Results

| id | test | result | note |
|----|------|--------|------|
| R1.1 | Test Connection (ngrok Ollama) alert says OK | ✅ PASS | alert: OK |
| R1.2 | Provider A saved & listed Connected in settings | ✅ PASS | row text snippet: Anthropic4 modelsNot setConnectDeepSeek2 modelsNot setConnectGoogle3 modelsNot setConnectOllama3 modelsNot setConnectOpenAI5 modelsNot setConnectOpenCode Zen7 modelsNot setConnectOllama Remote2 modelsConnectedDisconnect+ |
| R1.3 | Model jaahas/qwen3.5-uncensored:2b selected from dropdown | ✅ PASS | button label: Qwen3.5 Uncensored 2B |
| R1.4 | Real streaming chat reply from Provider A (contains OLLAMA CONNECTED OK) | ✅ PASS | reply(23 chars): AI

OLLAMA CONNECTED OK |
| R2.1 | Test Connection (Mission localhost:3000) alert says OK | ✅ PASS | alert: OK |
| R2.2 | Provider B saved & listed Connected in settings | ✅ PASS | row text snippet: Anthropic4 modelsNot setConnectDeepSeek2 modelsNot setConnectGoogle3 modelsNot setConnectOllama3 modelsNot setConnectOpenAI5 modelsNot setConnectOpenCode Zen7 modelsNot setConnectOllama Remote2 modelsConnectedDisconnectM |
| R2.3 | Model mission selected from dropdown | ✅ PASS | button label: Mission Pilot |
| R2.4 | Real streaming chat reply from Provider B (Bengali, non-error) | ✅ PASS | reply(175 chars): AI

[phase] সরাসরি উত্তর তৈরি করছি...[phase] উত্তর প্রস্তুত!আমার নাম বারিশালি, আমি শেখা ও সমাজের সঠিক প্রবণতা বুঝতে এবং দুষ্টুমির বিরুদ্ধে সংগ্রাম করতে সক্ষম হওয়ার জন্য তৈরি। |
| R3.1 | providers JSON: both base_urls present, api keys masked | ✅ PASS | {"status":200,"hasA":true,"hasB":true,"keyA":false,"keyB":null,"len":3185} |
| R3.2 | all expected routes fired, every response HTTP 200 | ✅ PASS | counts={"projects_list":1,"status":1,"project_info":1,"providers":3,"conversations":37,"settings_load":1,"test_connection":2,"settings_save":2,"start":5,"ai_chat_stream":2} missing=[] non200=[] |
| R3.3 | two Test-Connection OK dialogs observed | ✅ PASS | dialogs=["alert:OK","alert:OK"] |
| R3.4 | zero JavaScript exceptions in the page | ✅ PASS | none |
| R3.5 | index.php untouched (md5 still 895eb75226cc2801834baaced2873bb1) | ✅ PASS | md5=895eb75226cc2801834baaced2873bb1 |

## Route log (main tab)

| route | count |
|-------|-------|
| ai_chat_stream | 2 |
| conversations | 37 |
| project_info | 1 |
| projects_list | 1 |
| providers | 3 |
| settings_load | 1 |
| settings_save | 2 |
| start | 5 |
| status | 1 |
| test_connection | 2 |

- non-200 responses: none
- dialogs: [{"type":"alert","message":"OK","t":1790239135479},{"type":"alert","message":"OK","t":1790239228005}]
- JS exceptions: none

## Chat replies (real LLM output)

### A — Ollama Remote (jaahas/qwen3.5-uncensored:2b)

Prompt: `Reply with exactly this sentence and nothing else: OLLAMA CONNECTED OK`

> AI
> 
> OLLAMA CONNECTED OK

### B — Mission Local (mission)

Prompt: `তোমার নাম কী? এক লাইনে বাংলায় ছোট উত্তর দাও।`

> AI
> 
> [phase] সরাসরি উত্তর তৈরি করছি...[phase] উত্তর প্রস্তুত!আমার নাম বারিশালি, আমি শেখা ও সমাজের সঠিক প্রবণতা বুঝতে এবং দুষ্টুমির বিরুদ্ধে সংগ্রাম করতে সক্ষম হওয়ার জন্য তৈরি।

## Screenshots

- `REAL/R1_provider_saved.png` — Provider A row Connected in settings
- `REAL/R1_chat.png` — chat streamed from Provider A
- `REAL/R2_provider_saved.png` — Provider B row Connected in settings
- `REAL/R2_chat.png` — chat streamed from Provider B

## Test notes (honest)

- `<select>` option (`Custom Provider`) is chosen by setting `value` + firing `change` —
  native OS menus are not drivable via CDP; every *button* (Test, Connect, Send, model
  option rows, modal open/close) was a real `.click()`.
- Text fields are filled by setting `.value` + `input` event (same state a typed value produces).
- `alert()` dialogs are auto-accepted by the CDP `Page.javascriptDialogOpening` handler and logged.
- **Honest finding (run 1):** Provider B was first saved with a placeholder key
  (`sk-mission-local-secret`); Test Connection passed (GET `/v1/models` is open) but chat
  failed `HTTP 401 invalid session token` — the Mission proxy rejects unknown bearer tokens
  on `POST /v1/chat/completions` (probe: fake key -> 401, no key -> 200). Run 1 falsely
  showed PASS because the assertion only checked reply length; the assertion was tightened
  (error-pattern rejection + expected-token match) and Provider B re-configured with an
  empty API key (its authentic config). Run 2 results are below.
- R3.1 key-masking evidence therefore rests on Provider A's key (`keyA` absent from the
  providers JSON); `keyB` was intentionally never stored.
