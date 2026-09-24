# 🧪 টেস্ট পরিকল্পনা ও প্রমাণ-সংগ্রহ ডকুমেন্ট
**প্রকল্প:** Code with AI — contract-first ব্যাকএন্ড পুনর্নির্মাণ
**উৎস UI:** `/home/sahon/Videos/ui/index.php` (৬,৪৯০ লাইন / ৩১০,২৩৩ বাইট / md5 `895eb75226cc2801834baaced2873bb1`)
**তৈরি:** ২৪ সেপ্টেম্বর ২০২৬ · **শুরু:** বিসমিল্লাহ হির রহিমানির রহিম

---

## ০ · এই পরিকল্পনার নীতি

1. **দুই দৃষ্টিকোণ থেকে টেস্ট** — প্রতিটি পর্যায়ে:
   - **E (Engineer):** কনট্রাক্ট/স্কিমা/প্রোটোকল যাচাই — কোড ও JSON প্রমাণ।
   - **U (User):** ব্যবহারকারী আঁচ থেকে UI খুলে, ক্লিক করে, দেখে — দৃশ্যমান ফলাফল/স্ক্রিনশট প্রমাণ।
2. **প্রতিটি টেস্টের নিজস্ব প্রমাণ-ফাইল** — `unnecessary/evidence/` ডিরেক্টরিতে, টেস্ট-আইডি দিয়ে নামকরণ। প্রমাণ ছাড়া টেস্ট = অসম্পন্ন।
3. **উৎস ফাইল অক্ষত** — `index.php` কোনো টেস্টেই লেখা/পরিবর্তন হবে না (প্রতিটি পর্যায়ে md5 পুনরায় যাচাই)।
4. **সৎ ফলাফল** — পাস/ফেল যা-ই হোক লেখা হবে; ব্লক যদি অপেক্ষা করে, সেটা `BLOCKED` লেখা হবে, চাপা পড়বে না।

---

## ১ · পর্যায়ভিত্তিক টেস্ট-ম্যাট্রিক্স

### পর্যায় ১ — স্থিরতা ও কনট্রাক্ট (এখনই সম্পাদনযোগ্য)

| ID | দৃষ্টি | টেস্ট | পদ্ধতি | প্রমাণ-ফাইল | অবস্থা |
|----|--------|-------|--------|--------------|--------|
| E1.1 | Engineer | UI ফাইল byte-identical (md5/size/lines) | `md5sum`, `stat`, `wc -l` | `unnecessary/evidence/P1/E1.1_source_integrity.txt` | ✅ PASS |
| E1.2 | Engineer | PHP ব্লক = ০ (ভাষা-নিরপেক্ষতা) | `grep -c '<?php'` | `unnecessary/evidence/P1/E1.2_php_blocks.txt` | ✅ PASS |
| E1.3 | Engineer | রাউট মোট = ৩৯ (union) | api() ∪ raw ∪ stream | `unnecessary/evidence/P1/E1.3_routes.txt` | ✅ PASS |
| E1.4 | Engineer | স্ট্রিম ইভেন্ট = ১২ | `grep "ev==='"` | `unnecessary/evidence/P1/E1.4_stream_events.txt` | ✅ PASS |
| E1.5 | Engineer | প্রতি রাউটের প্যারামিটার কী সংগ্রহ | regex পার্স | `unnecessary/evidence/P1/contract.json` | ✅ PASS |
| E1.6 | Engineer | রিকোয়েস্ট প্রোটোকল স্থির (GET/POST, CT, JSON) | `api()` ফাংশন ডাম্প | `unnecessary/evidence/P1/E1.6_api_function.txt` | ✅ PASS |
| E1.7 | Engineer | XHR পয়েন্ট = ৮ | `grep -c '.open('` | `unnecessary/evidence/P1/E1.7_xhr_points.txt` | ✅ PASS |
| E1.8 | Engineer | প্রতি রাউটের callback কোড-ডাম্প (ফিল্ড যাচাইয়ের উৎস) | callsite কাটা | `unnecessary/evidence/P1/callsites/*.txt` | ✅ PASS |
| U1.1 | User | UI ব্রাউজারে খুলে লেআউট রেন্ডার হয় | headless স্ক্রিনশট | `unnecessary/evidence/P1/U1.1_ui_render.png` | ✅ PASS |
| U1.2 | User | সার্ভার ছাড়া UI-তে DOM ক্র্যাশ নেই (শুধু XHR এরর) | কনসোল লগ | `unnecessary/evidence/P1/U1.2_console.txt` | ✅ PASS |

**পর্যায় ১ ফলাফল (২৪ সেপ্ট ২০২৬):** **১০/১০ PASS**
- E1.1 md5 = `895eb75226cc2801834baaced2873bb1` ✅ (টেস্ট-পরেও একই — ০ এডিট প্রমাণ)
- E1.3 রাউট = ৩৯ ✅ (api ৩৫ + raw অতিরিক্ত ৩ + stream ১, `read_file` দুই জায়গায়)
- E1.4 ইভেন্ট = ১২ ✅
- E1.5 `contract.json` — ৭৩টি api() call-site, প্রতিটির method+params+response-field
- E1.8 — ৮০টি callsite ডাম্প (`unnecessary/evidence/P1/callsites/`)
- U1.1 স্ক্রিনশট — পূর্ণ ৩-কলাম UI রেন্ডার, সব স্ল্যাশ-কমান্ড দৃশ্যমান ✅
- U1.2 কনসোল — DOM 314,672 বাইট, ৭টি মুখ্য id উপস্থিত, **০টি JS ক্র্যাশ**, শুধু ১টি নির্দোষ password-form সতর্কতা ✅
- প্রমাণ মোট: ৯৫টি ফাইল (`unnecessary/evidence/P1/`)

**পর্যায় ১-তে পাওয়া কাজ-তালিকা (E6-তে যা মোছা হবে):** UI-র নিচে-ডানে হার্ড-কোডেড `/home/alistiyab/public_html` দেখাচ্ছে — এটি অন্য অ্যাকাউন্টের পাথ, কনফিগে বদলাতে হবে।

### পর্যায় ২ — কনট্রাক্ট টেস্ট (সার্ভার নির্মাণের পর)

| ID | দৃষ্টি | টেস্ট | প্রমাণ-ফাইল | অবস্থা |
|----|--------|-------|--------------|--------|
| E2.1 | Engineer | ৩৯/৩৯ রাউট উত্তর দেয়, HTTP 200 + JSON | `unnecessary/evidence/P2/E2.1_routes.json` | ✅ PASS |
| E2.2 | Engineer | প্রতিটি রাউটের **বাধ্যতামূলক** ফিল্ড উপস্থিত (স্কিমা যাচাই) | `unnecessary/evidence/P2/E2.2_schema_report.md` | ✅ PASS |
| E2.3 | Engineer | GET/POST নিয়ম মেলে (ডেটা থাকলে POST) | `unnecessary/evidence/P2/E2.3_method_rules.txt` | ✅ PASS |
| U2.1 | User | UI থেকে প্রতিটি রাউট ট্রিগার করে ফল দেখা যায় | `unnecessary/evidence/P2/U2.1_smoke.md` | ✅ PASS |

### পর্যায় ৩ — স্ট্রিম (SSE-শৈলী)

| ID | দৃষ্টি | টেস্ট | প্রমাণ-ফাইল | অবস্থা |
|----|--------|-------|--------------|--------|
| E3.1 | Engineer | ১২/১২ ইভেন্টের নাম ও `data: ` প্রিফিক্স সঠিক | `unnecessary/evidence/P3/E3.1_events.json` | ✅ PASS |
| E3.2 | Engineer | প্রতিটি ইভেন্টে `conversation_id` (সেশন আইসোলেশন) | `unnecessary/evidence/P3/E3.2_conv_ids.txt` | ✅ PASS |
| U3.1 | User | চ্যাটে ধারাবাহিক উত্তর + Thinking ব্লক দেখা যায় | `unnecessary/evidence/P3/U3.1_stream.png` | ✅ PASS |

### পর্যায় ৪ — ফিচার ধোঁয়া পরীক্ষা (ব্যবহারকারী-আঁচ)

| ID | দৃষ্টি | টেস্ট | প্রমাণ-ফাইল | অবস্থা |
|----|--------|-------|--------------|--------|
| U4.1 | User | চ্যাট → টুল চিপ → ডিফ ভিউয়ার | `unnecessary/evidence/P4/U4.1_chat_tools.png` | ✅ PASS |
| U4.2 | User | পারমিশন মোডাল (Allow once/Always/Deny) | `unnecessary/evidence/P4/U4.2_permission.png` | ✅ PASS |
| U4.3 | User | শেল টার্মিনাল (`!` কমান্ড) | `unnecessary/evidence/P4/U4.3_shell.png` | ✅ PASS |
| U4.4 | User | ফাইল-ট্রি এক্সপ্যান্ড + ফাইল ভিউয়ার | `unnecessary/evidence/P4/U4.4_filetree.png` | ✅ PASS |
| U4.5 | User | প্রজেক্ট সুইচার/ক্রিয়েট/ডিলিট | `unnecessary/evidence/P4/U4.5_projects.png` | ✅ PASS |
| U4.6 | User | undo/redo/fork/regenerate/Stop | `unnecessary/evidence/P4/U4.6_history.png` | ✅ PASS |
| E4.1 | Engineer | UI ফাইল টেস্ট-পরেও md5 একই (০ এডিট প্রমাণ) | `unnecessary/evidence/P4/E4.1_md5_recheck.txt` | ✅ PASS |

### পর্যায় ৫ — ওয়ার্কিং-ডিরেক্টরি (individual environment)

| ID | দৃষ্টি | টেস্ট | প্রমাণ-ফাইল | অবস্থা |
|----|--------|-------|--------------|--------|
| E5.1 | Engineer | `path`/`project_id` বদলালে file_tree ওই ডিরেক্টরি দেখায় | `unnecessary/evidence/P5/E5.1_workdir.txt` | ✅ PASS |
| E5.2 | Engineer | রুটের বাইরে (`../`) যাওয়া যায় না — path-traversal জেইল | `unnecessary/evidence/P5/E5.2_traversal.txt` | ✅ PASS |
| U5.1 | User | দুই প্রজেক্ট সুইচ করে ফাইল-ট্রি আলাদা দেখা যায় | `unnecessary/evidence/P5/U5.1_switch.png` | ✅ PASS |

### পর্যায় ৬ — প্রোভাইডার সুইচ ও নিরাপত্তা

| ID | দৃষ্টি | টেস্ট | প্রমাণ-ফাইল | অবস্থা |
|----|--------|-------|--------------|--------|
| E6.1 | Engineer | দুই আলাদা `base_url`-এ `test_connection` + স্ট্রিম চলে | `unnecessary/evidence/P6/E6.1_provider_switch.txt` | ✅ PASS |
| E6.2 | Engineer | API key কখনো UI/JS response-এ ফেরত আসে না | `unnecessary/evidence/P6/E6.2_key_leak.txt` | ✅ PASS |
| E6.3 | Engineer | `shell` রাউট পাবলি অ্যাক্সেসে লগইন/টোকেন ছাড়া ব্লকড | `unnecessary/evidence/P6/E6.3_shell_auth.txt` | ✅ PASS |
| U6.1 | User | Settings-এ নিজের provider বসিয়ে সংযোগ সফল | `unnecessary/evidence/P6/U6.1_settings.png` | ✅ PASS |

---

## পর্যায় ৭ — আসল প্রোভাইডার (R*) ও অপারেটর প্রশ্ন (Q*)

স্বতন্ত্র লেবেল: **R\*** = দুইটি প্রকৃত বহিরাগত LLM (ngrok Ollama + Mission `localhost:3000`)
UI-তে **ক্লিক করে** যোগ, টেস্ট, সেভ ও চ্যাট; **Q\*** = অপারেটরের প্রশ্ন-তদন্ট (`.env`, স্বাধীনতা,
ওরিজিন/টানেল, দৈনন্দিন ব্যবহার)। দুটোই আলাদা: নির্ণায়ক ফল আগের E\*/U\*-তে (নিয়ন্ত্রিত ফেক),
এগুলো বহিরাগত-সত্য ও স্বাধীনতার প্রমাণ।

| ID | টেস্ট | প্রমাণ-ফাইল | অবস্থা |
|----|-------|--------------|--------|
| R1.1–R1.3 | Provider A (ngrok) ক্লিক-থ্রু: Test=OK, Saved=Connected, মডেল বাছাই | `unnecessary/evidence/REAL/R1_provider_saved.png` | ✅ |
| R1.4 | Provider A আসল স্ট্রিমিং চ্যাট (OLLAMA CONNECTED OK টোকেন) | `unnecessary/evidence/REAL/R1_chat.png` | ✅ (২৩ অক্ষর, run-৫) |
| R2.1–R2.3 | Provider B (Mission) ক্লিক-থ্রু: Test=OK, Saved=Connected, মডেল বাছাই | `unnecessary/evidence/REAL/R2_provider_saved.png` | ✅ |
| R2.4 | Provider B আসল চ্যাট (বাংলা উত্তর, অ-এরর) | `unnecessary/evidence/REAL/R2_chat.png` | ✅ (১৭৫ অক্ষর, run-৫) |
| R3.1 | providers JSON: দুই base_url, কী মাস্কড (খালি-কী → `keyB:null` — সঠিক) | `unnecessary/evidence/REAL_RESULTS.json` | ✅ |
| R3.2–R3.5 | সব রাউট 200, ২টি OK-ডায়ালগ, ০ JS-এক্সেপশন, md5 অক্ষত | `unnecessary/evidence/REAL_RESULTS.json` | ✅ (৩৭ conversations সহ সব non200=[]) |
| Q1.1–Q1.2 | `localhost:3000`-স্বাধীনতা: স্যুট ১১/১১+১১/১১ (ফেক), প্রোডাক্ট-কোডে হিট ০ | `unnecessary/evidence/P8/Q1_*` | ✅ |
| Q2.1–Q2.4 | `.env`+`start.sh` সিডিং, UI-তে Connected, কী-লিক নেই, অটো ডিফল্ট-মডেল | `unnecessary/evidence/P8/Q2_*.png` | ✅ |
| Q3.1 | সব ধাপে `index.php` md5 একই | `unnecessary/evidence/P8/QA_RESULTS.json` | ✅ |
| Q4.1–Q4.2 | LLM ছাড়া UI পরিষ্কার বুট; ড্রপডাউন খালি, বাকি UI চলে | `unnecessary/evidence/P8/Q4_*.png` | ✅ |
| Q5.1–Q5.2 | দুই চ্যানেল (.env+Settings UI); ০ JS-এক্সেপশন | `unnecessary/evidence/P8/QA_RESULTS.json` | ✅ |
| Q6.1–Q6.3 | env>env-file প্রাধান্য; LAN-ওরিজিন ফুল-ওয়ার্কিং; রাউট সব 200 | `unnecessary/evidence/P8/Q6_*.png` | ✅ |
| Q7.1–Q7.4 | দৈনন্দিন ব্যবহার: বাংলা চ্যাট (আসল qwen), ফাইল-ট্রি, নতুন সেশন, non200=0 | `unnecessary/evidence/P8/Q7_*.png` | ✅ |

**পর্যায় ৭ ফলাফল (চূড়ান্ত):** R\* click-through **১৩/১৩ PASS** (১৫৪.৭s, run-৫) ·
Q\* অপারেটর প্রশ্ন **১৮/১৮ PASS** (১১২.০s, run-৩) — উভয়ের প্রমাণ `unnecessary/evidence/REAL/` ও `unnecessary/evidence/P8/`।

**পর্যায় ৭-তে পাওয়া ও সমাধানকৃত সমস্যাগুলো (সৎ ইতিহাস):**

1. **R1.4 রুট-কজ (এখন সমাধান):** run-2/3-এ Provider A দিত `HTTP 503 "Value looks like
   object, but can't find closing '}'"` — ম্যাট্রিক্স-প্রোবে প্রমাণিত: ngrok Ollama-প্রক্সির
   পার্সার OpenAI-স্পেক-সন্মত `tool_calls.arguments` **স্ট্রিং** গ্রহণ করে না (অবজেক্ট হলে 200;
   `tools`/`content:null`/মডেল — কোনোটাই কারণ নয়; Mission দুই ফর্মেই নেয়) —
   `unnecessary/evidence/REAL/R1.4_upstream_probe.txt`। **সমাধান:** ইঞ্জিনে রিট্রাই-ল্যাডার —
   transient 429/502/503 একবার (স্পেক-বডি) → টুল-ইতিহাস থাকা সত্ত্বেও স্থায়ী 4xx/5xx হলে
   একবার object-ফর্ম রিট্রাই (লগ-সহ); স্পেক-ফর্ম সবসময় আগে, তাই সঠিক প্রোভাইডার ভ্যারিয়েন্ট দেখে না।
2. **run-১-এর R2.4 ভুল-PASS (সৎ নোট):** `length>=5` অ্যাসার্টশন Mission-এর 401-এরর-টেক্সট
   ধরতে পেরেছিল — অ্যাসার্টশন শক্ত করা হয়েছে (এরর-প্যাটার্ন নিষিদ্ধ + প্রত্যাশিত টোকেন);
   পুরনো লগ `unnecessary/evidence/REAL/run1_LOG.txt`-এ রাখা।
3. **টেস্ট-হারনেস বাগ ×২ (নিজের দোষ, ঠিক):** Q&A-তে XHR-লগ `INSTRUMENT` ইনজেক্টই হয়নি
   (চ্যাট সত্যিই সফল হয়েছিল — ৯১s/৭৬ অক্ষর — শুধু কাউন্টার 0); Q1.2-র প্রেডিকেট নিজের
   গ্রেপ-কোডের সুইচ-স্ট্রিং নিজেকেই ধরছিল — প্রোডাক্ট-কোড/অ-প্রোডাক্ট ভাগ করে ঠিক।
4. **R\* টেস্ট-আইসোলেশন:** দীর্ঘ আসল টুল-লুপ (এক ইটারেশনে ৯১s পর্যবেক্ষিত) টাইমআউটে
   পুরো রান পড়ে দিলে — প্রতিটি চ্যাট try/catch-এ আইসোলেট, বাজেট ভাঙলে ইউজারের মতো Stop
   চেপে লক ছাড়ানো + আংশিক উত্তর সৎভাবে রেকর্ড।

**অবস্থা চিহ্ন:** ⏳ চলমান · ✅ পাস · ❌ ফেল · 🔒 ব্লকড (ঐতিহাসিক — সার্ভার নির্মাণের অপেক্ষায়; চূড়ান্ত রানে সব ✅, নিচের ফলাফল লগ দেখুন)

---

## ২ · সাফল্যের চূড়ান্ত শর্ত

- [x] ৩৯/৩৯ রাউট + ১২/১২ ইভেন্ট যাচাইকৃত (E-phase) — contract_test E2.1/E3.1 প্রমাণ
- [x] সব **বাধ্যতামূলক** `■` ফিল্ড উপস্থিত (E2.2) — `unnecessary/evidence/P2/E2.2_schema_report.md`
- [x] সব U-phase ফিচার দৃশ্যমানভাবে কাজ করে — user_test ১১/১১, রাউট-কভারেজ ৩৯/৩৯, ৯টি স্ক্রিনশট
- [x] **md5 শেষ পর্যন্ত একই** — `895eb75226cc2801834baaced2873bb1` (E4.1, contract+user টেস্ট-পরেও)
- [x] secret (পাথ/project_id/টোকেন) বাদ + `shell` অথেন্টিকেশন (E6.2/E6.3)

---

## ৩ · ফলাফল লগ

| তারিখ | কোন পর্যায় | ফল | নোট |
|--------|-------------|-----|------|
| ২৪ সেপ্ট ২০২৬ | পরিকল্পনা তৈরি | ✅ | বিসমিল্লাহ দিয়ে শুরু |
| ২৪ সেপ্ট ২০২৬ | **পর্যায় ১ (E1.1–E1.8 + U1.1–U1.2)** | ✅ **১০/১০ PASS** | md5 অক্ষত; ৩৯ রাউট/১২ ইভেন্ট নিশ্চিত; ৮০ callsite + contract.json প্রমাণ; UI রেন্ডার + ০ JS ক্র্যাশ। ৯৫টি প্রমাণ-ফাইল। |
| ২৪ সেপ্ট ২০২৬ | **পর্যায় ২–৬ (contract_test — E+U অটোমেটেড)** | ✅ **১১/১১ PASS** (~২.১ সে.) | E2.1 ৩৯/৩৯ রাউট 200+JSON · E2.2 স্কিমা-রিপোর্ট · E2.3 GET/POST নিয়ম · E3.1 ১২/১২ ইভেন্ট `data: ` প্রিফিক্স · E3.2 প্রতি ইভেন্টে conv-id · E4.1 md5 অক্ষত · E5.1 workdir-per-project · E5.2 ট্র্যাভার্সাল জেইল · E6.1 দুই provider · E6.2 key-leak ০ · E6.3 token-gate · U2.1+U3.1 smoke। প্রমাণ: `unnecessary/evidence/P2..P6/` + `CONTRACT_RESULTS.json`। |
| ২৪ সেপ্ট ২০২৬ | **পর্যায় ২–৬ (user_test — আসল Chrome, ব্যবহারকারী-আঁচ)** | ✅ **১১/১১ PASS** (১৭.০ সে.) | রাউট-কভারেজ **৩৯/৩৯**, non-200=0, JS এক্সেপশন=০ · U4.6 undo/redo/regenerate/fork/abort/force_unlock সব রাউট ফায়ার (force_unlock দ্বিতীয় ট্যাব থেকে) · U5.1 দুই প্রজেক্ট · U6.1 settings_save/test_connection/settings_delete · ৯টি স্ক্রিনশট/মার্কডাউন (`P2..P6/U*`) + `USER_RESULTS.json` + ডায়ালগ-লগ (৭টি confirm/alert)। |
| ২৪ সেপ্ট ২০২৬ | **চূড়ান্ত শর্ত** | ✅ সব পূরণ | md5 শেষ পর্যন্ত `895eb75226cc2801834baaced2873bb1` (০ এডিট) · ৩৯ রাউট/১২ ইভেন্ট দুই দৃষ্টিতে যাচাইকৃত · secret/shell-auth শুচি। **টেস্ট-নোট:** (১) rename-এ `window.prompt` স্ট্যাব — এই Chrome-এ CDP `userInput` উপেক্ষিত (মিনিমাল প্রোবে প্রমাণিত); (২) মেসেজ action-বার শুধু `mouseover`-এ তৈরি হয় — টেস্ট সেটিই অনুসরণ করে; (৩) UI স্ক্রিপ্ট IIFE-তে আবদ্ধ, তাই টেস্ট শুধু DOM + `window.forceUnlockAndRetry` ব্যবহার করে। |
| ২৪ সেপ্ট ২০২৬ | `.env`+`start.sh`+লগ সুবিধা (env.js/log.js) যোগ | ✅ | `chmod 600 .env` (২ প্রোভাইডার), `.env.example`, `start.sh`/`stop.sh`, `logs/server.log` (http/llm/env/boot লাইন, secret রিড্যাক্ট)+`logs/console.log`; প্রাধান্য: আসল env > `.env` > ডিফল্ট। টেস্ট-হারনেস `OC_ENV_FILE=''` দিয়ে হেরমেটিক। |
| ২৪ সেপ্ট ২০২৬ | **রিগ্রেশন: contract + user (নতুন কোডেই)** | ✅ **১১/১১ + ১১/১১** | env+log+retry যোগের পর দুই স্যুটই অক্ষত (`unnecessary/evidence/P8/Q1_*_rerun.log`)। |
| ২৪ সেপ্ট ২০২৬ | **R\* real click-through run-১** | ⚠️ ১৩/১৩ দেখালেও ১টি ভুল-PASS | R2.4 `length>=5` অ্যাসার্টশন Mission-401-এরর-টেক্সট ধরে ফেলেছিল — অ্যাসার্টশন শক্ত, পুরনো লগ সংরক্ষিত (`unnecessary/evidence/REAL/run1_LOG.txt`)। |
| ২৪ সেপ্ট ২০২৬ | **R\* run-২** | ❌ **১১/১৩** | R1.4 ngrok `HTTP 503 "Value looks like object..."`, R3.1 খালি-কী `indexOf('')` প্রেডিকেট-বাগ। |
| ২৪ সেপ্ট ২০২৬ | **R\* run-৩ + রুট-কজ প্রোব** | ❌ **১২/১৩** (R3.1 ঠিক) | R1.4 বারবার একই 503 → ম্যাট্রিক্স-প্রোবে ট্রিগার শনাক্ত: প্রক্সি স্পেক-স্ট্রিং `tool_calls.arguments` রিজেক্ট করে (অবজেক্ট→200); `unnecessary/evidence/REAL/R1.4_upstream_probe.txt`। |
| ২৪ সেপ্ট ২০২৬ | **ইঞ্জিন compat-ফলব্যাক + টেস্ট আইসোলেশন প্যাচ** | ✅ | transient-রিট্রাই → স্থায়ী 4xx/5xx-এ object-ফর্ম একবার (স্পেক আগে); R\*/Q\* চ্যাট try/catch + Stop-ফলব্যাক + ৩০০s বাজেট। |
| ২৪ সেপ্ট ২০২৬ | **R\* run-৪** | ❌ টাইমআউট | compat কাজ করায় টুল-লুপ দীর্ঘ হয়ে ১৮০s বাজেট ছাড়ল — হারনেসের দোষ (উপরের প্যাচ দ্বারা সমাধান)। |
| ২৪ সেপ্ট ২০২৬ | **R\* run-৫ (চূড়ান্ত)** | ✅ **১৩/১৩ PASS** (১৫৪.৭s) | R1.4: ngrok আসল স্ট্রিম উত্তর `OLLAMA CONNECTED OK` (compat-ফলব্যাকের পর টুল-লুপ সম্পূর্ণ) · R2.4: Mission বাংলা উত্তর ১৭৫ অক্ষর · R3.1: `keyB=null` (খালি-কী সঠিক), কী মাস্কড · R3.2: সব রাউট 200 (৩৭ conversations) · R3.4: ০ JS-এক্সেপশন · R3.5: md5 অক্ষত। প্রমাণ: `unnecessary/evidence/REAL_RESULTS.json` + `unnecessary/evidence/REAL/REAL_PROVIDER.md` + `R1_chat.png`/`R2_chat.png`। |
| ২৪ সেপ্ট ২০২৬ | **Q\* অপারেটর প্রশ্ন-তদন্ত run-১/২** | ❌ ১৩/১৩-পর্যন্ত→১৭/১৮ | run-১: `INSTRUMENT` ইনজেক্ট বাদ (চ্যাট সত্যিই সফল); run-২: Q4.1 অ্যাসার্টশন + Q1.2 প্রেডিকেট বাগ — দুটোই ঠিক। |
| ২৪ সেপ্ট ২০২৬ | **Q\* অপারেটর প্রশ্ন-তদন্ট run-৩ (চূড়ান্ত)** | ✅ **১৮/১৮ PASS** (১১২.০s) | `.env` সিড+কী-নিরাপত্তা+অটো-ডিফল্ট-মডেল · LLM-ছাড়া বুট · env>env-file প্রাধান্য · LAN-ওরিজিন ফুল-ওয়ার্কিং · বাংলা চ্যাটে আসল qwen উত্তর · ফাইল-ট্রি/নতুন সেশন · প্রোডাক্ট-কোডে 3000-হিট ০ · md5 অক্ষত · ০ JS-এক্সেপশন। প্রমাণ: `unnecessary/evidence/P8/` (QA_RESULTS.{json,md} + ৪ স্ক্রিনশট + গ্রেপ/রান-লগ)। |
