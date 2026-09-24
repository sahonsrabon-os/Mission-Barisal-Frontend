# Code with AI — Node Backend (contract-first rebuild)

`index.php` (৬,৪৯০ লাইন, md5 `895eb75226cc2801834baaced2873bb1`) হলো একমাত্র UI-সোর্স ও
কনট্রাক্ট-সোর্স — **এটিতে কোনো এডিট হয়নি** (০ এডিট প্রমাণ: `unnecessary/evidence/P4/E4.1_md5_recheck.txt`)।
এই সার্ভার সেই UI-র ঠিক যা-ই পড়ে সেটাই ফেরত দেয়: **৩৯টি রাউট + ১২টি স্ট্রিম-ইভেন্ট**।

নির্ভরতা: **শুধু Node ডিফল্ট মডিউল** (কোনো npm প্যাকেজ নেই)। Node ≥ 22 (global `WebSocket`
টেস্ট-হারনেসে ব্যবহৃত; সার্ভারের জন্য যেকোনো আধুনিক Node)।

---

## চালানো (প্রস্তাবিত: রুটের `node start.js` / `npm start`)

```bash
cd /path/to/repo
cp .env.example .env    # একবার — প্রোভাইডার/পোর্ট/টোকেন শুধু এখানেই
node start.js           # (বা: npm start, ./start.sh — সবই একই; run_id-UUIDসহ বুট)
node stop.js            # (বা: ./stop.sh) নিরাপদ বন্ধ
```

ম্যানুয়াল (আগের মতো):

```bash
cd server
PORT=8080 OC_DATA=./data OC_HOME=/var/www/html node server.js
```

**Env-প্রাধান্য:** আসল এনভায়রনমেন্ট ভেরিয়েবল > `.env` ফাইল > বিল্ট-ইন ডিফল্ট
(যাচাই: `unnecessary/evidence/P8/QA_RESULTS.json` Q6.1)।

| env | অর্থ | ডিফল্ট |
|-----|------|--------|
| `PORT` | HTTP পোর্ট | `18800` |
| `OC_DATA` | স্টেট/কনভারসেশন/settings রাখার ডিরেক্টরি | `./data` |
| `OC_HOME` | কাস্টমারের হোম রুট (প্রজেক্ট-পাথের জেইল) | `./../workspace` |
| `OC_ROOTS` | অতিরিক্ত অনুমোদিত রুট (`:`-বিভাজিত) | — |
| `OC_ACCESS_TOKEN` | সেট থাকলে সব API/স্ট্রিমে টোকেন-গেট (লগইন) | বন্ধ |
| `OC_SESSION_SECRET` | কুকি সিগনেচার | র‍্যান্ডম |

সার্ভার দুটি ফাইল সার্ভ করে:

- `/` (বা `index.php`) → `lib/render.js` **runtime render** — UI বাইট-এ-বাইট থাকে,
  শুধু render-time ইনজেকশন হয়: অ্যাপ-কনস্ট (`A`,`C`,`SP`,`HD`,`PID`), টাইটেল, ফুটার,
  নতুন-প্রজেক্ট প্রিফিক্স — টোকেন/হার্ডকোড-পাথ/পুরনো `project_id` **সরিয়ে**।
  রেন্ডার সবসময় নতুন salt দেয়, তাই প্রতি রিকোয়েস্টে ইউনিক CSRF (`C`)।
- বাকি সব → `ai_php_api=<route>` ডিসপ্যাচার (`lib/routes.js`)।

## মডিউল

| ফাইল | দায়িত্ব |
|------|---------|
| `server.js` | HTTP এন্ট্রি, রুট-ডিসপ্যাচার, সেশন-কুকি, জেইল-হেলার, render |
| `lib/render.js` | `index.php` render-time ইনজেকশন (সোর্স অক্ষত) |
| `lib/routes.js` | ৩৯টি রাউট-হ্যান্ডলার (ঠিক UI-য়াই পড়ে সেটাই ফেরত) |
| `lib/stream.js` | এজেন্ট ইঞ্জিন — SSE-শৈলী স্ট্রিম, ১২ ইভেন্ট, প্রতি-conv লক (`AbortController`) |
| `lib/tools.js` | read/write/bash/todo/permission টুল (jail + পারমিশন চেক) |
| `lib/providers.js` | কাস্টমারের নিজের AI provider (base_url/api_key/models), test/ফেভারিট/পারমিশন |
| `lib/store.js` | JSON স্টেট (প্রজেক্ট/কনভারসেশন/স্ন্যাপশট/সেশন) |
| `lib/jail.js` | পাথ-জেইল — `realOf`/`within`, `../` ট্র্যাভার্সাল ব্লক |
| `lib/auth.js` | `oc_sid` কুকি + CSRF + ঐচ্ছিক `OC_ACCESS_TOKEN` গেট |
| `lib/diff.js` | স্ন্যাপশট ↔ ওয়ার্কিং ফাইলের ডিফ |
| `lib/env.js` | `.env` লোডার (প্রাধান্য: আসল env > ফাইল) + বুট-সিডিং প্রোভাইডার (`OC_PROVIDER_<n>_*` / `OC_PROVIDERS`) |
| `lib/log.js` | বিস্তারিত লগ: `logs/server.log` (+stdout) — http/llm/env/boot লাইন, secret রিড্যাক্ট |

## কনট্রাক্ট (সংক্ষেপে)

- **রাউট (৩৯):** start, conversations, conversation, switch_conversation,
  delete_conversation, rename_conversation, new_session, ai_chat_stream, regenerate,
  fork, compact, abort, status, file_tree, read_file, resolve_file, diff, changes,
  restore, project_info, projects_list, projects_create, projects_delete,
  projects_close, projects_wordpress, providers, settings_load, settings_save,
  settings_delete, test_connection, favorite_model, unfavorite_model,
  toggle_permission, shell, set_mode, undo, redo, clear, force_unlock
- **স্ট্রিম ইভেন্ট (১২):** `done, error, iteration-start, permission-request, question,
  reasoning-delta, text-delta, title, todos, tool-call, tool-result, usage` — প্রতিটি লাইন
  `data: ` + JSON (`substring(6)` দিয়ে UI পার্স করে), প্রতিটি পেলোডে `conversation_id`
  (সেশন আইসোলেশন; UI শুধু সক্রিয় সেশনের ইভেন্ট রেন্ডার করে)।
- **UI method নিয়ম:** `api(route, data)` — data থাকলে POST (form-urlencoded), না থাকলে GET;
  raw XHR (`conversation`, `file_tree`, `resolve_file`, `changes`) GET।

## নিরাপত্তা

- পাথ-জেইল: প্রতি রিকোয়েস্টে `path` + `project_id` → per-request working dir (cPanel-সদৃশ);
  রুটের বাইরে `../` → ব্যর্থ (`E5.2`)।
- CSRF: প্রতি render-এ নতুন `C`; সব POST/স্ট্রিম যাচাই করে।
- `OC_ACCESS_TOKEN` সেট থাকলে `shell` সহ সব রাউট টোকেন-ছাড়া ব্লক (`E6.3`)।
- API key কখনো রেসপন্সে ফেরত যায় না — শুধু ফিল্ড-নাম/মাস্কড ভ্যালু (`E6.2`)।
- রেন্ডারে কোনো হার্ডকোড পাথ/টোকেন/পুরনো `project_id` নেই (শুধু গ্রাহক-সাইড ভ্যারিয়েবল)।

## টেস্ট

```bash
# E+U কনট্রাক্ট টেস্ট (১১/১১ PASS, ~২ সেকেন্ড) — প্রয়োজন: node
node test/contract_test.js

# ব্যবহারকারী-আঁচ UI টেস্ট (১১/১১ PASS, ~১৭ সেকেন্ড) — প্রয়োজন: google-chrome
node test/user_test.js

# আসল প্রোভাইডার ক্লিক-থ্রু (ngrok Ollama + Mission :3000; R* লেবেল)
# — Settings-এ ক্লিক করে provider যোগ → Test → Connect → মডেল বাছ → চ্যাট
node test/real_provider_test.js

# অপারেটর প্রশ্ন-তদন্ত (Q* লেবেল): .env+start.sh, স্বাধীনতা, ওরিজিন/টানেল, দৈনন্দিন ব্যবহার
node test/qna_test.js

# ফেক OpenAI SSE প্রোভাইডার (লুপব্যাক, টেস্ট নিজেই বুট করে)
# node test/fake_llm.js   (প্রয়োজনে ম্যানুয়ালি)
```

- প্রমাণ: `../unnecessary/evidence/P1..P6/`, `../unnecessary/evidence/CONTRACT_RESULTS.json`,
  `../unnecessary/evidence/USER_RESULTS.json`, স্ক্রিনশট `P3..P6/U*.png`
- পরিকল্পনা ও ফলাফল: `../docs/test_plan.md`

### user_test-এর কিছু টেস্ট-নোট (সৎ নোট)

1. **`window.prompt` স্ট্যাব (rename)** — এই Chrome বিল্ডে CDP
   `Page.handleJavaScriptDialog.userInput` প্রম্পটে উপেক্ষিত হয় (মিনিমাল প্রোব:
   `userInput=TYPED_TEXT` দিলেও `prompt()` → `""`)। তাই rename-ফ্লোতে টেস্ট
   `window.prompt` স্ট্যাব করে ক্যানড রেসপন্স দেয় — confirm/alert স্বাভাবিক CDP-তেই
   সাড়া পায় (ডায়ালগ-লগে প্রমাণ)।
2. **মেসেজ action-বার** UI-তে শুধু `mouseover`-এ তৈরি হয় (লেজি DOM) — টেস্ট আগে
   প্রতিটি `.ocMsg`-এ `mouseover` ডিসপ্যাচ করে regenerate/fork বাটন বানায়।
3. **UI স্ক্রিপ্ট IIFE-তে আবদ্ধ** — তাই টেস্ট কখনো পেজ-ফাংশন/গ্লোবাল ধরে নেয় না;
   শুধু DOM অ্যাকশন + একমাত্র এক্সপোজড গ্লোবাল `window.forceUnlockAndRetry` ব্যবহার করে।
4. **force_unlock দ্বিতীয় ট্যাব থেকে** — আসল ইউজার-সিনারিও: একটি ট্যাবে দীর্ঘ স্ট্রিম
   সার্ভার-লক ধরে রাখে, দ্বিতীয় ট্যাব (একই `?session=`) পাঠালে "already in progress"
   এরর → Force Unlock বাটন → `force_unlock` রাউট।

---

## কনফিগ — `.env` (অ্যাডমিন, কোডে হাত ছাড়া)

`.env` (রুটে, `chmod 600`) — `start.js`/সার্ভার বুটে `lib/env.js` এটি লোড করে:

```bash
PORT=8787
HOST=127.0.0.1                       # LAN/টানেল দরকার হলে 0.0.0.0 (আসল env>এই ফাইল)
# OC_ACCESS_TOKEN=change-me          # সেট করলে UI+shell টোকেন-গেট

# অ্যাডমিনের LLM — বুটেই Settings-এ "Connected" হয়ে যায়
OC_PROVIDER_1_NAME=Ollama Remote
OC_PROVIDER_1_BASE_URL=https://...ngrok-free.dev/v1
OC_PROVIDER_1_API_KEY=               # না লাগলে খালি
OC_PROVIDER_1_MODELS=qwen=Qwen 2B,llama3.1=Llama 3.1
OC_PROVIDER_1_MODEL=qwen             # UI লোডেই এটি অটো-সিলেক্ট হয়

# বিকল্প: OC_PROVIDERS='[{"name":"...","base_url":"...","api_key":"...","models":{...}}]'
# OC_PROVIDERS_FORCE=1                # প্রতি বুটে env মান ফিরিয়ে আনবে (ডিফল্ট: থাকা থাকে)
```

নিয়ম:

- **সিডিং:** এন্ট্রি না থাকলে তৈরি; থাকলে রাখা হয় (গ্রাহক UI-র এডিট জয়ী) — শুধু
  `OC_PROVIDERS_FORCE=1` দিলে env জয়ী।
- **ডিফল্ট মডেল:** `OC_PROVIDER_<n>_MODEL` → সেটিংসে `default_model` → UI নিজেই
  প্রথম connected provider-এর default মডেল বেছে নেয় (ইউটাচড `index.php`-এর হুক)।
- **দুই চ্যানেল:** অ্যাডমিন `.env` থেকে, গ্রাহক Settings UI থেকে (U6.1) — দুটোই একই
  `state.json`-এ; কী কখনো ব্রাউজারে যায় না (E6.2/Q2.2)।

## লগ (বিস্তারিত)

- `logs/server.log` — টাইমস্ট্যাম্পসহ প্রতি-রিকোয়েস্ট লাইন
  (`GET /index.live.php route=... -> 200 3ms`), LLM কল
  (`llm request/done/failed ... usage=... in=1234ms`), transient রিট্রাই ও upstream
  এরর, env সিডিং, boot READY — secret মান রিড্যাক্ট হয়।
- `logs/console.log` — কাঁচা stdout/stderr (crash trace সহ)।
- `OC_LOG_FILE` দিয়ে লগ-ফাইল বদলানো যায়; `''` দিলে ফাইল-লগ বন্ধ (শুধু stdout)।
- টেস্ট-রানও একই ফাইলে লেখে (সমান্তরাল রানে লাইন-ইন্টারলক নয়, append-only)।

## ডিপ্লয়মেন্ট (cPanel / পাবলিক টানেল)

- `index.php`-তে **কোনো `<?php` ব্লক নেই** — এটি কাঁচা JS টেমপ্লেট; Apache/PHP দিয়ে
  সার্ভ করলে API পাথ ও পুরনো হার্ডকোড পাথ অবিকল থাকে → ভাঙবে + লিক হয়। তাই
  **সবসময় Node সার্ভারই UI সার্ভ করবে** (`/` → render inject)।
- **cPanel:** Node.js অ্যাপ হিসেবে `server.js` চালান (cPanel "Setup Node.js App" বা
  terminal), অ্যাপ ডিরেকটরি = ui/; `.env` দিয়ে পোর্ট/টোকেন/প্রোভাইডার; PHP লাগবে না।
- **লোকাল + পাবলিক টানেল:** UI same-origin relative API
  (`index.live.php?act=ai&...`) ব্যবহার করে — তাই localhost, LAN IP, বা পাবলিক
  URL যেকোনো origin-এ একইভাবে চলে (কোনো CORS নেই; কুকি+CSRFও same-origin)।
  উদাহরণ: `cloudflared tunnel --url http://127.0.0.1:8787`
  (বা `ngrok http 8787`) → URL খুললেই পুরো UI। প্রমাণ:
  `unnecessary/evidence/P8/Q6_lan_origin.png` + Q6.2/Q6.3। টানেল URL প্রকাশ্য — অবশ্যই
  `OC_ACCESS_TOKEN` সেট করুন।
