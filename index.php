
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>public_html - Code with AI</title>
<link rel="shortcut icon" href=themes/default/images//favicon.ico />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css" id="hljs-theme">
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.2/marked.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.1.6/purify.min.js"></script>
<style>
 :root{
   --bg:#F9FAFB;--bgW:#F3F4F6;--bgS:#FFFFFF;--bgSs:#F9FAFB;--bgC:#F0F1F4;
   --t1:#111827;--t2:#4B5563;--t3:#6B7280;--t4:#9CA3AF;
   --p:#3B82F6;--pH:#2563EB;--pT:#3B82F6;--s:#8B5CF6;
   --bd:#E5E7EB;--bd2:#D1D5DB;--bd3:#F3F4F6;
   --in:#FFFFFF;--ok:#22C55E;--err:#EF4444;--wr:#F59E0B;
   --sb:260px;--rsb:280px;--hh:52px;--r:14px;--rS:10px;--rP:9999px;
   --sh:0 2px 8px rgba(0,0,0,.06);--shM:0 8px 24px rgba(0,0,0,.1);--shL:0 12px 40px rgba(0,0,0,.14);
 --sans:ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
 --mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;
 --bgW:var(--bgSs);--fm:var(--mono);--ff:var(--sans);--er:var(--err)
  }
  @keyframes ocFadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
  @keyframes ocSlideIn{from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)}}
  @keyframes ocPulse{0%,100%{opacity:1}50%{opacity:.4}}
  @keyframes ocBlink{0%,49%{opacity:1}50%,100%{opacity:0}}
  @keyframes ocRipple{to{transform:scale(2.5);opacity:0}}
  @keyframes ocBarFill{from{width:0}}
  @keyframes ocSweep{0%{background-position:-200% 0}100%{background-position:200% 0}}
  [data-cs="dark"]{
   --bg:#0b1220;--bgW:#121826;--bgS:#121826;--bgSs:#1b2333;--bgC:#0f1623;
   --t1:#E5E7EB;--t2:#94A3B8;--t3:#64748B;--t4:#475569;
   --p:#3b82f6;--pH:#60A5FA;--pT:#60A5FA;--s:#8b5cf6;
   --bd:#2b3445;--bd2:#1e293b;--bd3:#1b2333;
   --in:#1b2333;--ok:#22c55e;--err:#ef4444;--wr:#f59e0b;
   --sh:0 2px 8px rgba(0,0,0,.3);--shM:0 8px 24px rgba(0,0,0,.4);--shL:0 12px 40px rgba(0,0,0,.5);
   --bgW:var(--bgSs);--fm:var(--mono);--ff:var(--sans);--er:var(--err)
}
*{box-sizing:border-box;margin:0;padding:0}
html{height:100%}
body{font-family:var(--sans);font-size:14px;background:var(--bg);color:var(--t1);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--bd);border-radius:99px}
::-webkit-scrollbar-thumb:hover{background:var(--t4)}

#oc{display:flex;flex-direction:column;height:100vh;overflow:hidden;background:var(--bg);container-type:inline-size;container-name:oc}

/* Header */
.ocH{display:flex;align-items:center;height:var(--hh);padding:0 16px;background:var(--bgS);border-bottom:1px solid var(--bd);flex-shrink:0;gap:8px;backdrop-filter:blur(12px);position:relative;z-index:50}
.ocHl,.ocHr{display:flex;align-items:center;gap:6px;flex-shrink:0}
.ocHc{flex:1;display:flex;justify-content:center;align-items:center;gap:10px;overflow:hidden}
.ocHl{min-width:0}
.ocBrand{font-size:13.5px;font-weight:700;letter-spacing:-.01em;color:var(--t1);white-space:nowrap;font-family:var(--mono);background:linear-gradient(135deg,var(--p),var(--s));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;margin-left:2px}
.ocHrDiv{width:1px;height:20px;background:var(--bd);margin:0 4px;flex-shrink:0}
.ocHMore{width:34px;height:34px;border:none;border-radius:var(--rS);background:transparent;color:var(--t2);cursor:pointer;display:none;align-items:center;justify-content:center;flex-shrink:0;position:relative}
.ocHMore:hover{background:var(--bgSs);color:var(--t1)}
.ocHMore.show{display:inline-flex}
/* Colorful header icons */
.ocIb svg{transition:all .2s cubic-bezier(.4,0,.2,1)}
 .ocIb{color:var(--t2)}
 .ocIb:hover{background:var(--bgSs);color:var(--t1)}
 .ocIb:active{transform:scale(.94)}
 /* Colorful header icon hovers */
 #ocProjBtn:hover{color:#facc15}
 #ocFtBtn:hover{color:#60a5fa}
 #ocChgBtn:hover{color:#34d399}
 #ocTodoBtn:hover{color:#c084fc}
 #ocModBtn:hover{color:#fb923c}
 a.ocIb:hover{color:#60a5fa}
 #ocReasonToggle:hover{color:#a78bfa}
 #ocSndBtn:hover{color:#f87171}
 #ocNtfBtn:hover{color:#fbbf24}
 #ocThBtn:hover{color:#fbbf24}
 /* Full-color (filled) icons on hover only */
 #ocProjBtn:hover svg{fill:currentColor}
 #ocSndBtn:hover svg{fill:currentColor}
 #ocNtfBtn:hover svg{fill:currentColor}
 #ocThBtn:hover svg{fill:currentColor}
 /* Header icons grow on hover */
 .ocIb:hover svg{transform:scale(1.25)}
.ocHMoreDrop{position:absolute;right:0;top:100%;margin-top:6px;background:var(--bgS);border:1px solid var(--bd);border-radius:var(--r);box-shadow:var(--shL);z-index:9999;display:none;flex-direction:column;gap:2px;padding:6px;min-width:180px;animation:ocSlideIn .15s ease-out}
.ocHMoreDrop.open{display:flex}
.ocHMoreItem{display:flex;align-items:center;gap:10px;padding:8px 10px;border:none;background:none;color:var(--t2);cursor:pointer;font:500 12.5px/1 var(--sans);border-radius:var(--rS);white-space:nowrap;transition:all .15s;text-align:left}
.ocHMoreItem:hover{background:var(--bgSs);color:var(--t1)}
.ocHMoreItem svg{flex-shrink:0}
.ocIb{width:34px;height:34px;border:none;border-radius:var(--rS);background:transparent;color:var(--t2);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:16px;transition:all .2s cubic-bezier(.4,0,.2,1);position:relative;overflow:hidden}
.ocIb:hover{background:var(--bgSs);color:var(--t1)}
.ocIb:active{transform:scale(.94)}
.ocB{height:34px;padding:0 14px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t1);cursor:pointer;font-weight:500;font-size:12.5px;font-family:var(--sans);display:inline-flex;align-items:center;gap:6px;white-space:nowrap;transition:all .2s cubic-bezier(.4,0,.2,1);text-decoration:none;position:relative;overflow:hidden}
.ocB:hover{background:var(--bgSs);border-color:var(--t4);transform:translateY(-1px);box-shadow:var(--sh)}
.ocB:active{transform:translateY(0)}
.ocBP{background:linear-gradient(135deg,var(--p),var(--s));border-color:transparent;color:#fff;box-shadow:0 4px 14px color-mix(in srgb,var(--p) 35%,transparent)}
.ocBP:hover{background:linear-gradient(135deg,var(--pH),var(--s));box-shadow:0 6px 20px color-mix(in srgb,var(--p) 45%,transparent);transform:translateY(-1px)}
.ocBs{height:30px;padding:0 11px;font-size:11.5px}
.ocSel{line-height:1.3 !important;height:34px;padding:0 28px 0 14px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t1);font:500 12.5px/1 var(--sans);cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%239CA3AF' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;transition:all .2s}
[data-cs="dark"] .ocSel{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' fill='none'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2364748B' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E")}
.ocSel:hover{border-color:var(--t4);background:var(--bgSs)}.ocSel:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px color-mix(in srgb,var(--p) 18%,transparent)}
.ocSel option{background:var(--bgS);color:var(--t1)}

/* Model pill (custom dropdown) */
.ocModelBtn{display:inline-flex;align-items:center;gap:8px;height:34px;padding:0 30px 0 14px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t1);font:500 12.5px/1 var(--sans);cursor:pointer;transition:all .2s;position:relative;max-width:240px}
.ocModelBtn:hover{border-color:var(--t4);background:var(--bgSs);transform:translateY(-1px);box-shadow:var(--sh)}
.ocModelBtn:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px color-mix(in srgb,var(--p) 18%,transparent)}
.ocModelBtnT{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-flex;align-items:center;gap:6px}
.ocModelBtnA{position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--t3);pointer-events:none}
.ocModelDrop{position:fixed;background:var(--bgS);border:1px solid var(--bd);border-radius:var(--r);box-shadow:var(--shL);z-index:9999;display:none;min-width:260px;max-width:360px;max-height:380px;overflow-y:auto;overflow-x:hidden;padding:6px;-webkit-overflow-scrolling:touch;animation:ocSlideIn .15s ease-out}
.ocModelDrop.open{display:block}
.ocModelGrp{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--t3);padding:8px 10px 4px;position:sticky;top:0;background:var(--bgS)}
.ocModelOpt{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 10px;border-radius:var(--rS);cursor:pointer;font-size:13px;color:var(--t1);transition:background .12s}
.ocModelOpt:hover{background:var(--bgSs)}
.ocModelOpt.selected{background:color-mix(in srgb,var(--p) 15%,transparent);color:var(--p);font-weight:500}
.ocModelOptT{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1}
.ocModelFav{flex-shrink:0;width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;color:var(--t4);opacity:.5;border:none;background:none;cursor:pointer;border-radius:4px;transition:all .15s}
.ocModelFav:hover{opacity:1;color:var(--p)}
.ocModelFav.isFav{opacity:1;color:#EF4444}
[data-cs="dark"] .ocModelFav{color:var(--t4);opacity:.4}
[data-cs="dark"] .ocModelFav.isFav{color:#F87171;opacity:1}
.ocModelFav svg{width:15px;height:15px;display:block}
.ocModelBtnT svg{width:13px;height:13px;display:block}

/* Body / 3-column layout */
.ocBd{display:flex;flex:1;overflow:hidden;min-width:0}

/* Sidebar */
.ocSb{width:var(--sb);background:var(--bgS);border-right:1px solid var(--bd);display:flex;flex-direction:column;flex-shrink:0;overflow:hidden;transition:width .25s cubic-bezier(.4,0,.2,1)}
.ocSb.hid{width:0;border-right:none}
.ocSb.resp{position:fixed;left:0;top:var(--hh);height:calc(100vh - var(--hh));z-index:200;box-shadow:var(--shL)}
.ocSbH{padding:14px 16px 10px;font-weight:600;font-size:11px;font-family:var(--sans);letter-spacing:.06em;text-transform:uppercase;color:var(--t3);display:flex;justify-content:space-between;align-items:center}
.ocSbL{flex:1;overflow-y:auto;padding:6px 8px}
.ocSi{display:flex;align-items:center;padding:8px 12px;cursor:pointer;gap:10px;transition:all .15s cubic-bezier(.4,0,.2,1);position:relative;border-radius:var(--rS)}
.ocSi:hover{background:var(--bgSs);color:var(--t1)}
.ocSi.act{background:var(--bgSs);color:var(--t1);border:1px solid color-mix(in srgb,var(--p) 40%,transparent);box-shadow:0 0 0 1px color-mix(in srgb,var(--p) 20%,transparent),var(--sh)}
.ocSi.act::before{content:'';position:absolute;left:-1px;top:8px;bottom:8px;width:3px;border-radius:0 3px 3px 0;background:var(--p)}
.ocSiI{flex-shrink:0;width:6px;height:6px;border-radius:50%;background:var(--t4);opacity:.6}
.ocSi.act .ocSiI{background:var(--ok);opacity:1;box-shadow:0 0 6px var(--ok)}
.ocSi.gen .ocSiI{background:var(--err);opacity:1;box-shadow:0 0 6px var(--err);animation:ocSiPulse 1s ease-in-out infinite}
.ocSi.unread .ocSiI{background:var(--p);opacity:1;box-shadow:0 0 6px var(--p)}
.ocSi.unread .ocSiT{font-weight:600}
@keyframes ocSiPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.6)}}
.ocSiT{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px}
.ocSiB{font-size:10px;background:var(--bd3);color:var(--t3);padding:2px 8px;border-radius:var(--rP);flex-shrink:0;font-weight:500}
.ocSiD{opacity:0;border:none;background:none;color:var(--t3);cursor:pointer;font-size:14px;padding:0 2px;flex-shrink:0;line-height:1;transition:opacity .15s}
.ocSi:hover .ocSiD{opacity:.6}.ocSiD:hover{color:var(--err)!important;opacity:1}
.ocSiN{font-size:10px;color:var(--t3);opacity:.5;font-family:var(--mono);min-width:16px;text-align:right;flex-shrink:0}
.ocSbSearch{padding:6px 8px 12px;position:relative}
.ocSbSearchIc{position:absolute;left:20px;top:42%;transform:translateY(-50%);color:var(--t3);pointer-events:none;transition:color .18s ease;display:flex;align-items:center}
.ocSbSearchIn{width:100%;border:1px solid var(--bd);border-radius:var(--r);padding:8px 12px 8px 32px;font-size:12.5px;background:var(--bgC);color:var(--t1);outline:none;font-family:var(--sans);transition:all .18s ease}
.ocSbSearchIn:hover{border-color:var(--t4)}
.ocSbSearchIn:focus{border-color:var(--p);box-shadow:0 0 0 3px color-mix(in srgb,var(--p) 18%,transparent);background:var(--bgS)}
.ocSbSearch:focus-within .ocSbSearchIc{color:var(--p)}
.ocSbSearchIn::placeholder{color:var(--t3)}

 /* Responsive — moved to end of stylesheet */

/* Main */
.ocMn{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;background:var(--bg)}
.ocC{flex:1;overflow-y:auto;scroll-behavior:smooth}
.ocCi{max-width:760px;margin:0 auto;padding:24px 24px 8px}

/* Context bar */
.ocCtx{padding:16px 24px 0;max-width:760px;margin:0 auto}
.ocCtxI{padding:12px 16px;background:var(--bgSs);border:1px solid var(--bd);border-radius:var(--r);font-size:12px;color:var(--t2);line-height:1.6}
.ocCtxI b{color:var(--t1);font-weight:500}
.ocCtxI code{background:var(--bgC);padding:1px 5px;border-radius:5px;font-family:var(--mono);font-size:11px}

.ocSbNew{padding:12px 12px 6px}
.ocSbNewBtn{width:100%;height:36px;justify-content:center;font-size:13px;font-weight:600}
.ocSbF{display:flex;gap:4px;padding:8px 12px;border-top:1px solid var(--bd);flex-shrink:0}
.ocAttBtn{position:absolute;right:6px;bottom:10px;background:transparent;border:none;color:var(--t3);opacity:.6;width:24px;height:24px;border-radius:var(--rS);transition:all .15s;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ocAttBtn:hover{opacity:1;color:var(--p);background:color-mix(in srgb,var(--p) 12%,transparent)}
.ocSlashBtn{width:36px;height:42px;flex-shrink:0;background:transparent;border:1px solid var(--bd);transition:all .15s}
.ocSlashBtn:hover{border-color:var(--p);color:var(--p);background:color-mix(in srgb,var(--p) 10%,transparent)}
.ocRsbFileL{display:flex;flex-direction:column;gap:2px;max-height:180px;overflow-y:auto}

/* Right Sidebar */
.ocRsb{width:var(--rsb);background:var(--bgS);border-left:1px solid var(--bd);display:flex;flex-direction:column;flex-shrink:0;overflow-y:auto;padding:16px 12px;gap:14px}
.ocRsbCard{background:var(--bgSs);border:1px solid var(--bd);border-radius:var(--r);padding:14px;transition:border-color .2s}
.ocRsbCard:hover{border-color:color-mix(in srgb,var(--p) 25%,var(--bd))}
.ocRsbCardH{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.ocRsbCardT{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;}
.ocRsbBadge{font-size:10px;background:var(--p);color:#fff;padding:1px 8px;border-radius:var(--rP);font-weight:600}
.ocRsbFile{display:flex;align-items:center;gap:8px;padding:6px 4px;border-radius:var(--rS);cursor:pointer;font-size:12.5px;color:var(--t2);transition:all .15s}
.ocRsbFile:hover{background:var(--bgC);color:var(--t1)}
.ocRsbFileI{width:14px;height:14px;color:var(--t3);flex-shrink:0}
.ocRsbFileN{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1}
.ocRsbViewAll{display:block;text-align:center;font-size:11px;color:var(--p);padding:6px;cursor:pointer;border-radius:var(--rS);transition:background .15s;margin-top:4px}
.ocRsbViewAll:hover{background:color-mix(in srgb,var(--p) 12%,transparent)}
.ocRsbBar{height:8px;background:var(--bgC);border-radius:var(--rP);overflow:hidden;margin:10px 0 6px}
.ocRsbBarFill{height:100%;border-radius:var(--rP);background:linear-gradient(90deg,var(--p),var(--s));transition:width .5s cubic-bezier(.4,0,.2,1);animation:ocBarFill .6s ease-out}
.ocRsbBarFill.high{background:linear-gradient(90deg,var(--wr),var(--err))}
.ocRsbBarTxt{font-size:11px;color:var(--t3)}
.ocRsbVarRow{display:flex;align-items:center;justify-content:space-between;margin-top:8px}
.ocRsbVarSelect{background:var(--bgC);border:1px solid var(--bd);border-radius:var(--rS);padding:5px 10px;font-size:12px;color:var(--t1);cursor:pointer;font-family:var(--sans);font-weight:500;transition:all .15s}
.ocRsbVarSelect:hover{border-color:var(--t4)}
.ocRsbDots{display:inline-flex;align-items:center;gap:4px}
.ocRsbDot{width:8px;height:8px;border-radius:50%;background:var(--bd);transition:all .2s}
.ocRsbDot.on{background:var(--p);box-shadow:0 0 6px color-mix(in srgb,var(--p) 60%,transparent)}
.ocRsbEfforts{display:flex;gap:6px;margin-top:8px}
.ocRsbEffort{flex:1;text-align:center;padding:6px 4px;border:1px solid var(--bd);border-radius:var(--rS);font-size:11px;color:var(--t3);cursor:pointer;transition:all .15s}
.ocRsbEffort:hover{border-color:var(--t4);color:var(--t1)}
.ocRsbEffort.act{border-color:var(--p);background:color-mix(in srgb,var(--p) 15%,transparent);color:var(--p);font-weight:600}
.ocRsbToggle{margin-left:auto;background:transparent;border:none;color:var(--t3);cursor:pointer;padding:4px;border-radius:var(--rS);transition:all .15s}
.ocRsbToggle:hover{color:var(--t1);background:var(--bgC)}

/* Empty state */
.ocE{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:var(--t3);gap:16px;padding:48px 32px;text-align:center}
.ocEi{font-size:44px;opacity:.3}.ocEt{font-size:20px;color:var(--t2);font-weight:500}.ocEs{font-size:14px;line-height:1.6;max-width:400px;color:var(--t3)}

/* Messages */
.ocMsg{padding:14px 0;display:flex;gap:12px;animation:ocFadeIn .3s ease-out;position:relative}
.ocMsg.u{flex-direction:row-reverse}
.ocAv{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;box-shadow:var(--sh)}
.ocAv svg{width:16px;height:16px}
.ocMsg.u .ocAv{background:linear-gradient(135deg,var(--p),var(--s));color:#fff}
.ocMsg.a .ocAv{background:var(--bgSs);color:var(--s);border:1px solid var(--bd)}
.ocMb{max-width:88%;min-width:0}
.ocMbb{padding:0;line-height:1.65;font-size:14.5px}
.ocMsg.u .ocMbb{padding:10px 14px;background:linear-gradient(135deg,var(--p),var(--s));color:#fff;border-radius:var(--r);border-bottom-right-radius:4px;font-size:14px;box-shadow:0 2px 12px color-mix(in srgb,var(--p) 28%,transparent)}
[data-cs="dark"] .ocMsg.u .ocMbb{background:linear-gradient(135deg,var(--p),var(--s))}
.ocMsg.u .ocMd,.ocMsg.u .ocMd strong{color:rgba(255,255,255,.97)}
.ocMsg.u .ocMd code{background:rgba(255,255,255,.18);border:none;color:rgba(255,255,255,.92)}

/* Markdown */
.ocMd{line-height:1.75}
.ocMd p{margin:8px 0}.ocMd p:first-child{margin-top:0}.ocMd p:last-child{margin-bottom:0}
.ocMd h1,.ocMd h2,.ocMd h3{margin:20px 0 8px;color:var(--t1);font-weight:600}
.ocMd h1{font-size:21px}.ocMd h2{font-size:17px}.ocMd h3{font-size:15px}
.ocMd ul,.ocMd ol{margin:6px 0;padding-left:24px}.ocMd li{margin:3px 0}
.ocMd strong{color:var(--t1);font-weight:600}
.ocMd a{color:var(--pT);text-decoration:none}.ocMd a:hover{text-decoration:underline}
.ocMd code{background:var(--bgSs);border:1px solid var(--bd);border-radius:6px;padding:2px 6px;font-family:var(--mono);font-size:12.5px;color:var(--pT)}
.ocMd pre{margin:12px 0;border-radius:var(--r);overflow:hidden;border:1px solid var(--bd);box-shadow:var(--sh)}
.ocMd pre code{display:block;padding:14px 18px;background:var(--bgC);border:none;font-size:12.5px;line-height:1.6;overflow-x:auto;color:var(--t1)}
.ocMd blockquote{border-left:3px solid var(--s);padding-left:14px;color:var(--t2);margin:8px 0;background:color-mix(in srgb,var(--s) 6%,transparent);border-radius:0 var(--rS) var(--rS) 0;padding:8px 14px}
.ocMd hr{border:none;border-top:1px solid var(--bd);margin:12px 0}
.ocMd table{border-collapse:collapse;margin:10px 0;width:100%}.ocMd th,.ocMd td{border:1px solid var(--bd);padding:6px 10px;text-align:left;font-size:13px}.ocMd th{background:var(--bgSs);font-weight:600}
.ocCH{display:flex;justify-content:space-between;align-items:center;padding:8px 14px;background:var(--bgSs);font-size:11.5px;color:var(--t3);border-bottom:1px solid var(--bd);font-family:var(--mono)}
.ocCC{cursor:pointer;color:var(--t3);font-size:11px;padding:3px 8px;border-radius:var(--rS);background:none;border:none;font-family:var(--sans);transition:all .15s}.ocCC:hover{color:var(--p);background:color-mix(in srgb,var(--p) 12%,transparent)}

/* Tool calls */
.ocTk{margin:10px 0;border:1px solid var(--bd);border-radius:var(--r);background:var(--bgS);overflow:hidden;font-size:13px;border-left:3px solid var(--p);transition:border-color .2s}
.ocTk.err{border-left-color:var(--err)}
.ocTkH{display:flex;align-items:center;padding:9px 12px;cursor:pointer;gap:8px;font-size:12px;transition:background .15s}.ocTkH:hover{background:var(--bgSs)}
.ocTkN{font:600 11.5px/1 var(--mono);color:var(--pT)}
.ocTkDt{color:var(--t3);font-size:11.5px;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ocTkB{padding:10px 14px;border-top:1px solid var(--bd);max-height:200px;overflow-y:auto;font:12px/1.6 var(--mono);white-space:pre-wrap;word-break:break-all;color:var(--t2);display:none}
.ocTk.open .ocTkB{display:block}

/* Input */
.ocIA{border-top:1px solid var(--bd);padding:12px 24px 16px;background:var(--bgS);flex-shrink:0;backdrop-filter:blur(8px)}
.ocIW{max-width:760px;margin:0 auto;display:flex;gap:10px;align-items:flex-end;position:relative}
.ocIn{flex:1;resize:none;border:1px solid var(--bd);border-radius:var(--r);padding:12px 18px;color:var(--t1);font:14px/1.5 var(--sans);min-height:48px;max-height:160px;outline:none;transition:all .2s;box-shadow:var(--sh);background:var(--in)}
.ocIn:focus{border-color:var(--p);box-shadow:0 0 0 3px color-mix(in srgb,var(--p) 16%,transparent);background:var(--bgS)}.ocIn::placeholder{color:var(--t3)}
.ocSn{width:42px;height:42px;border-radius:var(--r);background:linear-gradient(135deg,var(--p),var(--s));color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;transition:all .2s cubic-bezier(.4,0,.2,1);box-shadow:0 4px 14px color-mix(in srgb,var(--p) 35%,transparent)}
.ocSn:hover{transform:translateY(-2px);box-shadow:0 8px 24px color-mix(in srgb,var(--p) 45%,transparent)}.ocSn:active{transform:translateY(0)}.ocSn:disabled{opacity:.4;cursor:not-allowed;transform:none;box-shadow:none}
.ocStop{width:42px;height:42px;border-radius:var(--r);background:var(--err);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s;box-shadow:0 4px 14px color-mix(in srgb,var(--err) 35%,transparent)}
.ocStop:hover{transform:translateY(-2px)}

/* Slash command chips */
.ocChips{max-width:760px;margin:8px auto 0;display:flex;gap:6px;flex-wrap:wrap}
.ocChip{font-size:11.5px;padding:4px 10px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t3);cursor:pointer;font-family:var(--mono);transition:all .15s;user-select:none}
.ocChip:hover{border-color:var(--p);color:var(--p);background:color-mix(in srgb,var(--p) 10%,transparent);transform:translateY(-1px)}

/* Status */
.ocSt{display:flex;align-items:center;padding:2px 20px;background:var(--bgS);border-top:1px solid var(--bd);font-size:11px;color:var(--t3);gap:14px;flex-shrink:0;min-height:24px}
.ocSd{width:6px;height:6px;border-radius:50%;display:inline-block;margin-right:4px;transition:all .2s}.ocSd.on{background:var(--ok);box-shadow:0 0 6px var(--ok)}.ocSd.off{background:var(--err)}
#ocStat{display:inline-flex;gap:10px;margin-left:10px;font-size:11px;color:var(--t3)}
#ocStat>span{display:inline-flex;align-items:center;gap:2px;cursor:default;transition:color .15s}
#ocStat>span:hover{color:var(--t1)}
#ocStat span[id$="V"]{font-weight:500;color:var(--t2)}
#ocStat:hover{opacity:.8}
#ocStatCache{color:var(--ac);font-weight:500}

.ocCtxR{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--bd3);font-size:13px}
.ocCtxR:last-child{border-bottom:none}
.ocCtxL{color:var(--t2)}
.ocCtxV{font-weight:600;color:var(--t1);font-family:var(--mono)}
.ocCtxS{margin-top:10px;padding-top:10px;border-top:1px solid var(--bd3)}
.ocCtxSt{font:500 11px/1 var(--sans);letter-spacing:.04em;text-transform:uppercase;color:var(--t3);margin-bottom:6px}
.ocCtxBr{display:flex;justify-content:space-between;padding:3px 0;font-size:12px}
.ocCtxBr span:first-child{color:var(--t3)}
.ocCtxBr span:last-child{color:var(--t1);font-weight:500}

.ocSetSrch{margin-bottom:10px}
.ocPl{display:flex;flex-direction:column;gap:4px;max-height:50vh;overflow-y:auto}
.ocPi{display:flex;align-items:center;padding:8px 10px;border:1px solid var(--bd3);border-radius:var(--r);gap:8px;transition:background .1s,border-color .1s}
.ocPi:hover{border-color:var(--bd2);background:var(--bgW)}
.ocPiN{flex:1;font-size:13px;font-weight:500}
.ocPiSub{font-size:10px;color:var(--t3);display:block;margin-top:1px}
.ocPiB{font-size:10px;padding:1px 7px;border-radius:9px;background:var(--ac);color:#fff}
	.ocPiBo{background:var(--bgW);color:var(--t3)}
.ocPi.ocPiHide{display:none}
.ocPiClose,.ocPiTrash{border:none;background:none;cursor:pointer;padding:2px;color:var(--t3);opacity:.5;transition:opacity .15s,color .15s;border-radius:4px;display:flex;align-items:center;justify-content:center}
.ocPiClose:hover{color:var(--t1);opacity:1!important}
.ocPiTrash:hover{color:var(--err);opacity:1!important}
	.ocCu{display:inline-block;width:2px;height:1em;background:var(--ac);animation:ocbl 1s infinite;vertical-align:text-bottom;margin-left:1px}
@keyframes ocbl{0%,50%{opacity:1}51%,100%{opacity:0}}

/* Modal */
.ocMo{display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:10000;align-items:center;justify-content:center}
.ocMo.open{display:flex}
.ocMoB{background:var(--bgS);border:1px solid var(--bd2);border-radius:12px;width:680px;max-height:85vh;overflow-y:auto;box-shadow:0 8px 32px rgba(0,0,0,.12)}
.ocMoH{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--bd3)}
.ocMoH h3{font-size:14px;font-weight:600}
.ocMoC{background:none;border:none;color:var(--t3);cursor:pointer;font-size:20px;padding:4px;line-height:1}.ocMoC:hover{color:var(--t1)}
.ocMoBd{padding:18px}
.ocFg{margin-bottom:12px}.ocFg label{display:block;font-size:12px;font-weight:500;margin-bottom:4px;color:var(--t2)}
.ocFi{width:100%;padding:7px 10px;border:1px solid var(--bd2);border-radius:var(--rS);background:var(--input);color:var(--t1);font:13px/1.5 var(--sans);outline:none}.ocFi:focus{border-color:var(--ac)}
.ocPl{display:grid;grid-template-columns:1fr 1fr;gap:4px}
.ocPi{display:flex;align-items:center;padding:8px 10px;border:1px solid var(--bd3);border-radius:var(--r);gap:8px;transition:background .1s,border-color .1s}
.ocPi:hover{border-color:var(--bd2);background:var(--bgW)}
.ocPiN{flex:1;font-size:13px;font-weight:500}
.ocPiB{font-size:10px;padding:1px 7px;border-radius:9px;background:var(--ac);color:#fff}
.ocPiBo{background:var(--bgW);color:var(--t3)}

/* File tree panel */
.ocFt{position:fixed;right:0;top:var(--hh);height:calc(100vh - var(--hh));width:240px;border-left:1px solid var(--bd2);background:var(--bgW);display:flex;flex-direction:column;flex-shrink:0;overflow:hidden;transform:translateX(100%);transition:transform .15s ease;z-index:100}
.ocFt.open{transform:translateX(0)}
.ocFtH{padding:8px 12px;font:500 11px/1 var(--sans);color:var(--t3);border-bottom:1px solid var(--bd3);display:flex;justify-content:space-between;align-items:center;flex-shrink:0}
.ocFtL{flex:1;overflow-y:auto;padding:2px 0;font-size:12px}
.ocFtI{display:flex;align-items:center;padding:3px 8px;cursor:pointer;color:var(--t2);gap:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ocFtI:hover{background:var(--bd3);color:var(--t1)}
.ocFtI.act{background:var(--bd3);color:var(--t1)}
.ocFtIc{width:14px;text-align:center;flex-shrink:0;font-size:11px;opacity:.6}
.ocFtIn{flex:1;overflow:hidden;text-overflow:ellipsis}
.ocFtS{font-size:10px;color:var(--t3);flex-shrink:0;margin-left:auto;padding-left:4px}

/* File viewer modal */
.ocFv{display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:10001;align-items:center;justify-content:center}
.ocFv.open{display:flex}
.ocFvB{background:var(--bgS);border:1px solid var(--bd2);border-radius:12px;width:80vw;max-width:900px;max-height:80vh;display:flex;flex-direction:column;box-shadow:0 8px 32px rgba(0,0,0,.12)}
.ocFvH{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid var(--bd3);font-size:13px;font-weight:500}
.ocFvC{flex:1;overflow:auto;padding:0}
.ocFvC pre{margin:0;padding:14px;font:12.5px/1.5 var(--mono);white-space:pre-wrap;word-break:break-all;color:var(--t1)}

.ocThink{display:flex;align-items:center;gap:6px;padding:4px 0}
.ocThinkD{width:6px;height:6px;border-radius:50%;background:var(--ac);animation:ocBlink 1.4s infinite both}
.ocThinkD:nth-child(2){animation-delay:.2s}
.ocThinkD:nth-child(3){animation-delay:.4s}
@keyframes ocBlink{0%,80%,100%{opacity:.2}40%{opacity:1}}
.ocThinkT{color:var(--t3);font-size:12px;font-style:italic}

.ocStop{width:36px;height:36px;border-radius:var(--r);background:var(--err);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;transition:background .12s}.ocStop:hover{opacity:.85}
.ocTerm{background:#1a1a1a;color:#e0e0e0;font:12px/1.55 var(--mono);padding:10px 14px;border-radius:var(--r);max-height:250px;overflow-y:auto;white-space:pre-wrap;word-break:break-all;margin:0}
.ocReason{border-left:2px solid var(--ac);padding:4px 10px;margin:4px 0;color:var(--t3);font-size:12.5px;font-style:italic;cursor:pointer;user-select:none}.ocReason:hover{background:var(--bgW)}
.ocReasonB{display:none;padding:6px 10px;font-size:12px;font-style:italic;color:var(--t3);white-space:pre-wrap;line-height:1.5}
.ocReason.open .ocReasonB{display:block}
.ocDiff{font:13px/1.6 var(--fm);border-radius:var(--r);overflow:hidden;border:1px solid var(--bd);max-height:320px;overflow-y:auto}
.ocDiffH{background:var(--bgW);padding:6px 14px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);display:flex;justify-content:space-between;align-items:center;gap:8px}
.ocDiffL{padding:0 14px;white-space:pre;font-size:12px}
.ocDiffL.add{background:rgba(34,197,94,.1);color:#15803d}[data-cs="dark"] .ocDiffL.add{background:rgba(74,222,128,.12);color:#86efac}
.ocDiffL.del{background:rgba(239,68,68,.08);color:#b91c1c}[data-cs="dark"] .ocDiffL.del{background:rgba(248,113,113,.1);color:#fca5a5}
.ocDiffL.ctx{color:var(--t3)}
.ocDiffTB{display:flex;gap:4px}
.ocDiffTB button{font-size:10px;padding:2px 8px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t3);cursor:pointer;transition:background .15s,color .15s}
.ocDiffTB button:hover{background:var(--bgW);border-color:var(--p);color:var(--p)}
.ocDiffTB button.act{background:var(--p);color:#fff;border-color:var(--p)}
.ocDiff.split{display:flex;overflow-x:auto}
.ocDiff.split .ocDiffSide{flex:1;min-width:0;overflow:hidden}
.ocDiff.split .ocDiffSide+.ocDiffSide{border-left:2px solid var(--bd)}
.ocDiff.split .ocDiffLNum{display:inline-block;width:36px;text-align:right;color:var(--t3);opacity:.5;padding-right:8px;user-select:none;font-size:11px}
.ocDiffGrep{font:12px/1.6 var(--fm);border:1px solid var(--bd);border-radius:var(--r);overflow:hidden;margin:8px 0}
.ocDiffGrepH{background:var(--bgW);padding:6px 14px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);display:flex;align-items:center;gap:8px}
.ocDiffGrepH .ocGrepPath{color:var(--pT);font-family:var(--fm);cursor:pointer;text-decoration:none;font-weight:500}
.ocDiffGrepH .ocGrepPath:hover{text-decoration:underline}
.ocDiffGrepL{padding:2px 14px 2px 50px;white-space:pre;font-size:12px;position:relative}
.ocDiffGrepLN{position:absolute;left:0;width:44px;text-align:right;color:var(--t3);opacity:.5;font-size:11px;user-select:none}
.ocCodeColl{position:relative}
.ocCodeColl .ocCodeToggle{position:absolute;top:36px;right:8px;font-size:10px;padding:2px 8px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t3);cursor:pointer;z-index:2;transition:background .15s,color .15s}
.ocCodeColl .ocCodeToggle:hover{background:var(--bgW);color:var(--t1)}
.ocCodeColl.collapsed pre{position:relative}
.ocCodeColl.collapsed pre code{max-height:120px;overflow:hidden;display:block}
.ocCodeColl.collapsed pre::after{content:'';position:absolute;left:0;right:0;bottom:0;height:40px;background:linear-gradient(to top,var(--bgC),transparent);pointer-events:none}
.ocCodeColl.collapsed .ocCodeToggle{top:auto;bottom:8px}
.ocPatchNav{display:flex;align-items:center;gap:6px;font-size:11px;color:var(--t3);padding:4px 8px}
.ocPatchNav button{font-size:11px;padding:2px 6px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t3);cursor:pointer}
.ocPatchNav button:hover{background:var(--bgW);color:var(--t1)}
.ocPatchNav button:disabled{opacity:.4;cursor:default}
.ocFileLink{color:var(--pT);text-decoration:none;font-family:var(--fm);cursor:pointer}
.ocFileLink:hover{text-decoration:underline}
.ocTokenW{font-size:10px;padding:2px 6px;border-radius:var(--rP);font-weight:500;margin-left:6px}
.ocTokenW.amber{background:rgba(245,158,11,.12);color:var(--wr)}
.ocTokenW.red{background:rgba(239,68,68,.1);color:var(--er)}
.ocSlashHint{position:absolute;bottom:100%;left:0;right:0;background:var(--bgS);border:1px solid var(--bd);border-radius:var(--r);box-shadow:0 8px 24px rgba(0,0,0,.08);max-height:220px;overflow-y:auto;display:none;font-size:13px;z-index:100;margin-bottom:8px}
.ocSlashHint.open{display:block}
.ocSHItem{padding:8px 14px;cursor:pointer;display:flex;gap:10px;align-items:center}.ocSHItem:hover,.ocSHItem.act{background:var(--bgW)}
.ocSHCmd{font-weight:600;color:var(--p);font-family:var(--fm);white-space:nowrap;font-size:12px}
.ocSHDesc{font-size:12px}
.ocToast{position:fixed;bottom:20px;right:20px;z-index:20000;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.ocToastI{padding:12px 18px;border-radius:var(--r);font-size:13px;color:#fff;pointer-events:auto;animation:ocToastIn .3s cubic-bezier(.16,1,.3,1);box-shadow:0 10px 32px rgba(0,0,0,.12);max-width:380px;display:flex;align-items:center;gap:10px}
.ocToastI.ok{background:var(--ok)}.ocToastI.err{background:var(--err)}.ocToastI.warn{background:var(--wr)}.ocToastI.info{background:var(--p)}
.ocToastI.hide{animation:ocToastOut .25s ease forwards}
@keyframes ocToastIn{from{opacity:0;transform:translateY(8px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
@keyframes ocToastOut{to{opacity:0;transform:translateX(12px)}}
.ocMsgAct{opacity:0;display:flex;gap:6px;position:absolute;top:-6px;right:0;transition:opacity .2s}
.ocMsg:hover .ocMsgAct{opacity:1}
.ocMsgActB{width:28px;height:28px;border:1px solid var(--bd);border-radius:var(--rS);background:var(--bgS);color:var(--t3);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;transition:background .15s,color .15s,border-color .15s}
.ocMsgActB:hover{background:var(--bgW);color:var(--t1)}
.ocMsg{position:relative}
.ocMsgTs{font-size:11px;color:var(--t4);display:none;position:absolute;bottom:-4px;left:40px}
.ocMsg:hover .ocMsgTs{display:block}
.ocLoad{display:flex;align-items:center;justify-content:center;padding:48px;gap:8px;color:var(--t3);font-size:13px}
.ocLoadD{width:6px;height:6px;border-radius:50%;background:var(--p);animation:ocBlink 1.4s infinite both}
.ocLoadD:nth-child(2){animation-delay:.2s}.ocLoadD:nth-child(3){animation-delay:.4s}
.ocFtSrch{padding:6px 12px;border-bottom:1px solid var(--bd2)}
.ocFtSrch input{width:100%;border:1px solid var(--bd);border-radius:var(--rP);padding:5px 12px;font-size:12px;background:var(--in);color:var(--t1);outline:none;transition:border-color .2s}
.ocFtSrch input:focus{border-color:var(--p);box-shadow:0 0 0 3px rgba(59,130,246,.1)}
.ocChg{position:fixed;bottom:0;right:0;width:380px;max-height:50vh;background:var(--bgS);border:1px solid var(--bd);border-radius:var(--r) var(--r) 0 0;box-shadow:0 -8px 32px rgba(0,0,0,.08);z-index:9999;display:none;flex-direction:column}
.ocChg.open{display:flex}
.ocChgH{padding:14px 18px;border-bottom:1px solid var(--bd2);display:flex;justify-content:space-between;align-items:center;font-weight:500;font-size:14px}
.ocChgL{flex:1;overflow-y:auto;padding:6px 0}
.ocChgI{padding:8px 16px;display:flex;justify-content:space-between;align-items:center;font-size:13px;border-bottom:1px solid var(--bd2)}
.ocChgI:hover{background:var(--bgW)}
.ocChgBtn{font-size:11px;padding:4px 10px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t1);cursor:pointer;transition:background .15s,color .15s}
.ocChgBtn:hover{background:var(--bgW);border-color:var(--t4)}
.ocAtMention{background:var(--bgS);border:1px solid var(--bd);border-radius:var(--r);box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:260px;overflow-y:auto;display:none;font-size:13px;z-index:10001;position:absolute;bottom:100%;left:0;right:0;margin-bottom:8px}
.ocAtMention.open{display:block}
.ocAtItem{padding:8px 14px;cursor:pointer;display:flex;gap:8px;align-items:center;border-radius:0;transition:background .1s}.ocAtItem:hover,.ocAtItem.active{background:var(--bgW)}
.ocAtItem span:first-child{color:var(--p);font-family:var(--fm);font-weight:500}
.ocAtItem small{color:var(--t3);font-size:11px;margin-left:auto;white-space:nowrap}
.ocAtItemIcon{width:20px;text-align:center;font-size:14px;flex-shrink:0}
.ocVariantBadge{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:var(--rP);font-size:11px;font-weight:500;margin-left:6px;cursor:pointer;transition:background .15s,color .15s,border-color .15s;border:1px solid var(--bd);color:var(--t2);background:var(--bgS)}
.ocVariantBadge:hover{border-color:var(--p);color:var(--p)}
.ocVariantBadge.active{background:var(--p);color:#fff;border-color:var(--p)}
.ocVariantDrop{position:absolute;top:100%;right:0;background:var(--bgS);border:1px solid var(--bd);border-radius:var(--r);box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:120px;z-index:200;display:none;margin-top:4px}
.ocVariantDrop.open{display:block}
.ocVariantOpt{padding:8px 14px;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:6px}.ocVariantOpt:hover{background:var(--bgW)}
.ocVariantOpt.active{color:var(--p);font-weight:500}
.ocAtItem small{color:var(--t3);font-size:11px;margin-left:auto;white-space:nowrap}
.ocAtItemIcon{width:20px;text-align:center;font-size:14px;flex-shrink:0}
.ocFaqI{border:1px solid var(--bd2);border-radius:var(--r);margin-bottom:8px;overflow:hidden}
.ocFaqQ{padding:12px 16px;cursor:pointer;font-weight:400;font-size:14px;display:flex;justify-content:space-between;align-items:center;gap:10px;background:var(--bgW);user-select:none}
.ocFaqQ:hover{background:var(--bgW)}
.ocFaqQ::after{content:'\25BC';font-size:10px;color:var(--t4);transition:transform .2s}
.ocFaqI.open .ocFaqQ::after{transform:rotate(180deg)}
.ocFaqA{padding:0 16px;max-height:0;overflow:hidden;transition:max-height .25s ease,padding .25s ease;font-size:13.5px;color:var(--t2);line-height:1.75}
.ocFaqI.open .ocFaqA{max-height:500px;padding:12px 16px}
.ocSCT{width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px}
.ocSCT th{text-align:left;padding:6px 8px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);text-transform:uppercase;letter-spacing:.05em}
.ocSCT td{padding:5px 8px;border-bottom:1px solid var(--bd3)}
.ocSCT kbd{background:var(--bgS);border:1px solid var(--bd);border-radius:4px;padding:2px 6px;font-family:var(--fm);font-size:11px;color:var(--t1);white-space:nowrap}

/* ===== STATUS BAR ===== */
.ocSt{display:flex;align-items:center;padding:4px 24px;background:var(--bgS);border-top:1px solid var(--bd2);font-size:11px;color:var(--t3);gap:16px;flex-shrink:0;min-height:28px}
.ocSd{width:6px;height:6px;border-radius:50%;display:inline-block;margin-right:4px}.ocSd.on{background:var(--ok)}.ocSd.off{background:var(--er)}
#ocStat{display:inline-flex;gap:12px;margin-left:12px;font-size:11px;color:var(--t3)}
#ocStat>span{display:inline-flex;align-items:center;gap:3px;cursor:default;font-weight:500}
#ocStat span[id$="V"]{font-weight:500;color:var(--t2)}
#ocStat:hover{opacity:.8}
#ocStatCache{color:var(--p);font-weight:500}

/* ===== THINKING ===== */
.ocThink{display:flex;align-items:center;gap:8px;padding:6px 0}
.ocThinkD{width:7px;height:7px;border-radius:50%;background:var(--p);animation:ocBlink 1.4s infinite both}
.ocThinkD:nth-child(2){animation-delay:.2s}
.ocThinkD:nth-child(3){animation-delay:.4s}
@keyframes ocBlink{0%,80%,100%{opacity:.2}40%{opacity:1}}
.ocThinkT{color:var(--t3);font-size:13px;font-style:italic}

/* ===== CURSOR BLINK ===== */
.ocCu{display:inline-block;width:2px;height:1.1em;background:var(--p);animation:ocbl 1s infinite;vertical-align:text-bottom;margin-left:2px;border-radius:1px}
@keyframes ocbl{0%,50%{opacity:1}51%,100%{opacity:0}}

/* ===== CONTEXT DETAILS ===== */
.ocCtxR{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--bd2);font-size:13px}
.ocCtxR:last-child{border-bottom:none}
.ocCtxL{color:var(--t2);font-weight:400}
.ocCtxV{font-weight:500;color:var(--t1);font-family:var(--fm)}
.ocCtxS{margin-top:12px;padding-top:12px;border-top:1px solid var(--bd2)}
.ocCtxSt{font:500 11px/1 var(--ff);letter-spacing:.06em;text-transform:uppercase;color:var(--t3);margin-bottom:8px}
.ocCtxBr{display:flex;justify-content:space-between;padding:4px 0;font-size:12px}
.ocCtxBr span:first-child{color:var(--t3)}
.ocCtxBr span:last-child{color:var(--t1);font-weight:500}

/* ===== MODAL ===== */
.ocMo{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:10000;align-items:center;justify-content:center}
.ocMo.open{display:flex}
.ocMoB{background:var(--bgS);border:1px solid var(--bd2);border-radius:16px;width:680px;max-height:85vh;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,.2)}
.ocMoH{display:flex;align-items:center;justify-content:space-between;padding:20px;border-bottom:1px solid var(--bd2)}
.ocMoH h3{font-size:18px;font-weight:500;color:var(--t1);letter-spacing:-.01em}
.ocMoC{background:none;border:none;color:var(--t3);cursor:pointer;font-size:22px;padding:4px;line-height:1;transition:color .15s}.ocMoC:hover{color:var(--t1)}
.ocMoBd{padding:20px}

/* ===== FORM ===== */
.ocFg{margin-bottom:16px}.ocFg label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:var(--t2);letter-spacing:-.01em}
.ocFi{width:100%;padding:9px 12px;border:1px solid var(--bd);border-radius:var(--rS);background:var(--in);color:var(--t1);font:14px/1.5 var(--ff);outline:none;transition:border-color .2s,box-shadow .2s}.ocFi:focus{border-color:var(--p);box-shadow:0 0 0 4px rgba(59,130,246,.1)}

/* ===== PROVIDER LIST ===== */
.ocPl{display:grid;grid-template-columns:1fr 1fr;gap:8px;max-height:50vh;overflow-y:auto;padding-right:4px}
.ocPi{display:flex;align-items:center;padding:12px;border:1px solid var(--bd3);border-radius:var(--rS);gap:10px;transition:background .2s,border-color .2s,box-shadow .2s;cursor:pointer}
.ocPi:hover{border-color:var(--p);background:var(--bgW);box-shadow:0 2px 8px rgba(59,130,246,.08)}
.ocPiN{flex:1;font-size:13px;font-weight:500}
.ocPiSub{font-size:11px;color:var(--t3);display:block;margin-top:2px}
.ocPiB{font-size:10px;padding:3px 8px;border-radius:var(--rP);background:var(--p);color:#fff;font-weight:500}
.ocPiBo{background:var(--bgW);color:var(--t3);border:1px solid var(--bd)}
.ocPi.ocPiHide{display:none}
.ocPiClose,.ocPiTrash{border:none;background:none;cursor:pointer;padding:3px;color:var(--t3);opacity:.5;transition:opacity .15s,color .15s;border-radius:4px;display:flex;align-items:center;justify-content:center}
.ocPiClose:hover{color:var(--t1);opacity:1!important}
.ocPiTrash:hover{color:var(--err);opacity:1!important}

/* ===== FILE TREE PANEL ===== */
.ocFt{position:fixed;right:0;top:var(--hh);height:calc(100vh - var(--hh));width:260px;border-left:1px solid var(--bd2);background:var(--bgS);display:flex;flex-direction:column;flex-shrink:0;overflow:hidden;transform:translateX(100%);transition:transform .25s cubic-bezier(.4,0,.2,1);z-index:100}
.ocFt.open{transform:translateX(0)}
.ocFtH{padding:10px 16px;font:500 12px/1 var(--ff);color:var(--t3);border-bottom:1px solid var(--bd2);display:flex;justify-content:space-between;align-items:center;flex-shrink:0}
.ocFtL{flex:1;overflow-y:auto;padding:6px 0;font-size:13px}
.ocFtI{display:flex;align-items:center;padding:5px 12px;cursor:pointer;color:var(--t2);gap:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;border-radius:0;transition:background .1s,color .1s}.ocFtI:hover{background:var(--bgW);color:var(--t1)}
.ocFtI.act{background:var(--bgW);color:var(--p)}.ocFtI.act .ocFtIc{color:var(--p)}
.ocFtIc{width:14px;text-align:center;flex-shrink:0;font-size:12px;opacity:.6;transition:color .1s}
.ocFtIn{flex:1;overflow:hidden;text-overflow:ellipsis;font-size:13px}
.ocFtS{font-size:11px;color:var(--t3);flex-shrink:0;margin-left:auto;padding-left:6px}

/* ===== FILE VIEWER ===== */
.ocFv{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:10001;align-items:center;justify-content:center}
.ocFv.open{display:flex}
.ocFvB{background:var(--bgS);border:1px solid var(--bd2);border-radius:16px;width:80vw;max-width:900px;max-height:80vh;display:flex;flex-direction:column;box-shadow:0 8px 24px rgba(0,0,0,.2)}
.ocFvH{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--bd2);font-size:14px;font-weight:500}
.ocFvC{flex:1;overflow:auto;padding:0}
.ocFvC pre{margin:0;padding:18px;font:13px/1.65 var(--fm);white-space:pre-wrap;word-break:break-all;color:var(--t1)}

/* ===== FILE TREE SUB ===== */
.ocFtSub{padding-left:16px}

/* ===== ACCESSIBILITY ===== */
.ocIb:focus-visible,.ocB:focus-visible,.ocSn:focus-visible,.ocStop:focus-visible,.ocMoC:focus-visible,.ocMsgActB:focus-visible{outline:2px solid var(--p);outline-offset:2px;border-radius:var(--rS)}
.ocFi:focus-visible,.ocIn:focus-visible{outline:none;border-color:var(--p);box-shadow:0 0 0 4px rgba(59,130,246,.1)}
.ocFtI:focus-visible,.ocSi:focus-visible,.ocSHItem:focus-visible,.ocAtItem:focus-visible,.ocPi:focus-visible{outline:2px solid var(--p);outline-offset:-2px;border-radius:var(--rS)}
 @media(prefers-reduced-motion:reduce){
 *{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}
 .ocFt{transition:none}
 }

/* ===== COMMAND PALETTE ===== */
.ocCmdP{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:20000;align-items:flex-start;justify-content:center;padding-top:15vh}
.ocCmdP.open{display:flex}
.ocCmdB{background:var(--bgS);border:1px solid var(--bd);border-radius:12px;width:460px;max-height:45vh;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.25);display:flex;flex-direction:column}
.ocCmdI{padding:8px 16px;border-bottom:1px solid var(--bd3);display:flex;gap:10px;align-items:center}
.ocCmdI input{flex:1;border:none;background:none;color:var(--t1);font:14px/1.4 var(--sans);outline:none}
.ocCmdI input::placeholder{color:var(--t3)}
.ocCmdL{flex:1;overflow-y:auto;padding:2px 0}
.ocCmdIt{padding:6px 16px;cursor:pointer;display:flex;gap:10px;align-items:flex-start;transition:background .1s}
.ocCmdIt:hover,.ocCmdIt.act{background:var(--bgW)}
.ocCmdItK{font:600 12px/1 var(--mono);color:var(--pT);background:var(--bd3);padding:4px 8px;border-radius:4px;min-width:80px;text-align:center;flex-shrink:0;margin-top:1px}
.ocCmdItK.empty{background:transparent;min-width:0;padding:0}
.ocCmdItN{font-size:14px;color:var(--t1);font-weight:500;line-height:1.4}
.ocCmdItD{font-size:12px;color:var(--t3);margin-top:2px;line-height:1.3}

/* ===== PERMISSION PROMPT ===== */
.ocPerm{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:20001;align-items:center;justify-content:center}
.ocPerm.open{display:flex}
.ocPermB{background:var(--bgS);border:1px solid var(--bd2);border-radius:16px;width:560px;max-height:80vh;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,.25)}
.ocPermH{padding:18px 20px;border-bottom:1px solid var(--bd3);display:flex;align-items:center;gap:10px}
.ocPermHI{width:36px;height:36px;border-radius:var(--rS);background:var(--bgW);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.ocPermHT{font-size:15px;font-weight:600;color:var(--t1)}
.ocPermHS{font-size:12px;color:var(--t3);margin-top:2px}
.ocPermD{padding:16px 20px;font-size:13px;color:var(--t2);line-height:1.6;white-space:pre-wrap;word-break:break-all;background:var(--bgW);border-radius:var(--rS);margin:12px 20px;font-family:var(--mono)}
.ocPermF{padding:16px 20px;display:flex;gap:10px;justify-content:flex-end;border-top:1px solid var(--bd3)}

/* ===== CONTEXT PROGRESS BAR ===== */
.ocCtxBar{height:8px;background:var(--bd3);border-radius:4px;overflow:hidden;width:100px;display:inline-block;vertical-align:middle;margin-left:6px}
.ocCtxPct{font-size:11px;color:var(--t2);font-weight:600;margin-left:5px;vertical-align:middle}
.ocCtxBarFill{display:block;height:100%;border-radius:4px;transition:width .3s ease,background .3s ease;min-width:4px}
.ocCtxBarFill.low{background:var(--ok)}
.ocCtxBarFill.mid{background:var(--wr)}
.ocCtxBarFill.high{background:var(--err)}

/* ===== RENAME INPUT ===== */
.ocRenIn{border:1px solid var(--p);border-radius:var(--rS);padding:2px 6px;font:13px/1 var(--sans);background:var(--in);color:var(--t1);outline:none;width:100%;box-sizing:border-box}

/* ===== MSG HOVER ACTIONS ===== */
.ocMsgAct{opacity:0;display:flex;gap:4px;position:absolute;top:-8px;right:0;transition:opacity .2s}
.ocMsg:hover .ocMsgAct{opacity:1}
.ocMsgActB{width:26px;height:26px;border:1px solid var(--bd);border-radius:var(--rS);background:var(--bgS);color:var(--t3);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;transition:background .15s,color .15s,border-color .15s}
.ocMsgActB:hover{background:var(--bgW);color:var(--t1)}
.ocMsg{position:relative}

/* ===== COMPACT INDICATOR ===== */
.ocCompactBadge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:var(--rP);background:var(--bd3);color:var(--t3);font-size:11px;margin-bottom:8px}

/* ===== SHELL MODE INDICATOR ===== */
.ocShellBadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:var(--rP);background:rgba(59,130,246,.1);color:var(--p);font-size:12px;font-weight:600;font-family:var(--fm);margin-right:6px;border:1px solid rgba(59,130,246,.2)}

/* ===== QUESTION PROMPT ===== */
.ocQuestion{border:1px solid var(--bd);border-radius:var(--r);background:var(--bgS);margin:8px 0;overflow:hidden}
.ocQHeader{padding:10px 14px;border-bottom:1px solid var(--bd3);display:flex;align-items:center;gap:8px;font-weight:500;font-size:13px;color:var(--t1)}
.ocQHeader svg{width:16px;height:16px;color:var(--p);flex-shrink:0}
.ocQBody{padding:14px}
.ocQText{font-size:14px;color:var(--t1);margin-bottom:12px;line-height:1.5}
.ocQOptions{display:flex;flex-direction:column;gap:6px;margin-bottom:12px}
.ocQOpt{padding:8px 14px;border:1px solid var(--bd);border-radius:var(--rS);cursor:pointer;font-size:13px;color:var(--t1);transition:background .15s,border-color .15s;display:flex;align-items:center;gap:8px}
.ocQOpt:hover{border-color:var(--p);background:rgba(59,130,246,.04);color:var(--p)}
.ocQOpt.sel{border-color:var(--p);background:rgba(59,130,246,.08);color:var(--p);font-weight:500}
.ocQCustom{display:flex;gap:8px;margin-top:8px}
.ocQCustom input{flex:1;padding:8px 12px;border:1px solid var(--bd);border-radius:var(--rS);background:var(--in);color:var(--t1);font:13px/1.4 var(--sans);outline:none}
.ocQCustom input:focus{border-color:var(--p);box-shadow:0 0 0 3px rgba(59,130,246,.1)}
.ocQActions{display:flex;gap:8px;justify-content:flex-end}

/* ===== TODO PANEL ===== */
.ocTodoP{position:fixed;right:0;top:var(--hh);height:calc(100vh - var(--hh));width:280px;border-left:1px solid var(--bd2);background:var(--bgS);display:flex;flex-direction:column;flex-shrink:0;overflow:hidden;transform:translateX(100%);transition:transform .25s cubic-bezier(.4,0,.2,1);z-index:100}
.ocTodoP.open{transform:translateX(0)}
.ocTodoPH{padding:10px 16px;font:500 12px/1 var(--ff);color:var(--t3);border-bottom:1px solid var(--bd2);display:flex;justify-content:space-between;align-items:center;flex-shrink:0}
.ocTodoPL{flex:1;overflow-y:auto;padding:6px 0}
.ocTodoI{display:flex;align-items:flex-start;padding:8px 16px;gap:8px;cursor:pointer;transition:background .1s}
.ocTodoI:hover{background:var(--bgW)}
.ocTodoChk{width:16px;height:16px;border:1.5px solid var(--bd);border-radius:4px;flex-shrink:0;margin-top:2px;display:flex;align-items:center;justify-content:center;transition:background .15s,border-color .15s;cursor:pointer}
.ocTodoChk.done{background:var(--ok);border-color:var(--ok)}
.ocTodoChk.done::after{content:'\2713';color:#fff;font-size:10px;font-weight:700}
.ocTodoTxt{flex:1;font-size:13px;color:var(--t2);line-height:1.4}
.ocTodoI.done .ocTodoTxt{text-decoration:line-through;color:var(--t3)}

/* ===== MODIFIED FILES PANEL ===== */
.ocFilesP{position:fixed;right:0;top:var(--hh);height:calc(100vh - var(--hh));width:300px;border-left:1px solid var(--bd2);background:var(--bgS);display:flex;flex-direction:column;flex-shrink:0;overflow:hidden;transform:translateX(100%);transition:transform .25s cubic-bezier(.4,0,.2,1);z-index:100}
.ocFilesP.open{transform:translateX(0)}
.ocFilesPH{padding:10px 16px;font:500 12px/1 var(--ff);color:var(--t3);border-bottom:1px solid var(--bd2);display:flex;justify-content:space-between;align-items:center;flex-shrink:0}
.ocFilesPL{flex:1;overflow-y:auto;padding:4px 0}
.ocFilesI{display:flex;align-items:center;padding:6px 16px;gap:8px;font-size:12px;color:var(--t2);cursor:pointer;transition:background .1s}
.ocFilesI:hover{background:var(--bgW)}
.ocFilesIc{font-size:13px;flex-shrink:0}
.ocFilesIc.add{color:var(--ok)}.ocFilesIc.mod{color:var(--p)}.ocFilesIc.del{color:var(--err)}
.ocFilesN{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-family:var(--fm);font-size:12px;color:var(--t1)}
.ocFilesB{font-size:10px;padding:1px 6px;border-radius:var(--rP);flex-shrink:0;font-weight:500}
.ocFilesB.add{background:rgba(34,197,94,.1);color:#15803d}[data-cs="dark"] .ocFilesB.add{background:rgba(74,222,128,.12);color:#86efac}
.ocFilesB.mod{background:rgba(59,130,246,.1);color:#1d4ed8}[data-cs="dark"] .ocFilesB.mod{background:rgba(96,165,250,.12);color:#93c5fd}
.ocFilesB.del{background:rgba(239,68,68,.1);color:#b91c1c}[data-cs="dark"] .ocFilesB.del{background:rgba(248,113,113,.1);color:#fca5a5}

/* ===== KEYBINDINGS SETTINGS ===== */
.ocKbRow{display:flex;align-items:center;padding:8px 0;border-bottom:1px solid var(--bd3);gap:12px}
.ocKbLabel{flex:1;font-size:13px;color:var(--t1)}
.ocKbKey{display:flex;gap:4px}
.ocKbKey kbd{background:var(--bgW);border:1px solid var(--bd);border-radius:4px;padding:2px 8px;font:11px/1.4 var(--fm);color:var(--t2);min-width:28px;text-align:center}
.ocKbEdit{font-size:11px;color:var(--p);cursor:pointer;padding:2px 8px;border:1px solid var(--p);border-radius:var(--rP);background:none;transition:background .15s,color .15s}
.ocKbEdit:hover{background:var(--p);color:#fff}

/* ===== SUBAGENT/TASK ===== */
.ocTaskP{border:1px solid var(--bd);border-radius:var(--r);margin:8px 0;overflow:hidden;background:var(--bgS)}
.ocTaskPH{padding:8px 12px;display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;color:var(--t2);border-bottom:1px solid var(--bd3)}
.ocTaskPH:hover{background:var(--bgW)}
.ocTaskSt{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.ocTaskSt.running{background:var(--p);animation:ocBlink 1.4s infinite both}
.ocTaskSt.done{background:var(--ok)}.ocTaskSt.err{background:var(--err)}
.ocTaskNm{flex:1;font-weight:500;color:var(--t1);font-size:12px}
.ocTaskR{padding:10px 12px;font-size:12px;color:var(--t2);max-height:200px;overflow-y:auto;display:none}
.ocTaskP.open .ocTaskR{display:block}
.ocTaskSpin{display:inline-block;width:12px;height:12px;border:2px solid var(--bd);border-top-color:var(--p);border-radius:50%;animation:ocspin .6s linear infinite;vertical-align:middle;margin-right:6px}
@keyframes ocspin{to{transform:rotate(360deg)}}

/* ===== BADGE STYLES ===== */
.ocBadge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:var(--rP);font-size:10px;font-weight:500;letter-spacing:.02em}
.ocBadge.on{background:rgba(34,197,94,.1);color:var(--ok)}
.ocBadge.off{background:var(--bd3);color:var(--t3)}

/* ===== SHELL PROMPT STYLE ===== */
.ocShellPrompt{background:#1a1a2e;color:#e0e0e0;font:13px/1.6 var(--fm);padding:10px 14px;border-radius:var(--r);border:1px solid var(--bd);margin:6px 0;white-space:pre-wrap;word-break:break-all}
.ocShellPrompt::before{content:'$ ';color:var(--p);font-weight:700}

/* ===== FORK MODAL ===== */
.ocForkItem{padding:10px 16px;cursor:pointer;border-bottom:1px solid var(--bd3);display:flex;gap:10px;align-items:flex-start;transition:background .1s}
.ocForkItem:hover{background:var(--bgW)}
.ocForkItem.act{background:var(--bgW);border-left:3px solid var(--p)}
.ocForkItemNum{font:600 11px/1 var(--mono);color:var(--p);background:var(--bd3);padding:3px 8px;border-radius:4px;min-width:24px;text-align:center;flex-shrink:0;margin-top:1px}
.ocForkItemText{flex:1;font-size:13px;color:var(--t1);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;line-height:1.4}
.ocForkItemTime{font-size:11px;color:var(--t4);flex-shrink:0;margin-top:2px}

/* ===== SESSION DAY SEPARATOR ===== */
.ocSbSep{padding:8px 14px 4px;font:600 10px/1 var(--ff);color:var(--t4);border-top:1px solid var(--bd3);margin-top:4px;position:sticky;top:0;background:var(--bgS);z-index:1}
.ocSbSep:first-child{border-top:none;margin-top:0}

/* ===== SESSION TIMELINE ===== */
.ocTimeline{position:fixed;left:0;top:var(--hh);bottom:0;width:200px;background:var(--bgS);border-right:1px solid var(--bd2);transform:translateX(-100%);transition:transform .25s cubic-bezier(.4,0,.2,1);z-index:90;overflow-y:auto;padding:8px 0}
.ocTimeline.open{transform:translateX(0)}
.ocTimelineH{padding:8px 14px;font:500 12px/1 var(--ff);letter-spacing:.05em;color:var(--t3);border-bottom:1px solid var(--bd2);position:sticky;top:0;background:var(--bgS);z-index:1}
.ocTimelineI{padding:6px 14px;font-size:12px;color:var(--t2);cursor:pointer;border-bottom:1px solid var(--bd3);transition:background .1s;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ocTimelineI:hover{background:var(--bgW);color:var(--t1)}
.ocTimelineI .ocTimelineN{color:var(--p);font-weight:600;margin-right:4px;font-size:10px}
.ocTimelineToggle{position:fixed;left:0;top:var(--hh);width:16px;height:32px;background:var(--bgS);border:1px solid var(--bd2);border-left:none;border-radius:0 6px 6px 0;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:91;transition:left .25s cubic-bezier(.4,0,.2,1);color:var(--t3)}
.ocTimelineToggle:hover{color:var(--t1)}
.ocTimeline.open~.ocTimelineToggle{left:200px}

/* ===== DIFF VIEWER SIDE-BY-SIDE ===== */
.ocDiffToggle{display:flex;gap:4px;padding:4px 0;margin-bottom:4px}
.ocDiffToggleBtn{font-size:11px;padding:3px 10px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t3);cursor:pointer}
.ocDiffToggleBtn.active{background:var(--p);color:#fff;border-color:var(--p)}
.ocDiffSplit{display:grid;grid-template-columns:1fr 1fr;gap:0;font-size:12px;font-family:var(--fm)}
.ocDiffSplitL,.ocDiffSplitR{overflow-x:auto}
.ocDiffSplitL{border-right:1px solid var(--bd2)}
.ocDiffSplitRow{display:flex;min-height:18px;line-height:18px}
.ocDiffSplitRow.add{background:rgba(34,197,94,.08)}
.ocDiffSplitRow.del{background:rgba(239,68,68,.08)}
.ocDiffSplitRow .ocDiffLN{width:32px;text-align:right;padding:0 6px;color:var(--t4);user-select:none;flex-shrink:0}
.ocDiffSplitRow .ocDiffLC{padding:0 8px;white-space:pre}
.ocDiffHunkNav{display:flex;gap:6px;align-items:center;padding:4px 0;font-size:11px;color:var(--t3)}
.ocDiffHunkBtn{font-size:11px;padding:2px 8px;border:1px solid var(--bd);border-radius:var(--rP);background:var(--bgS);color:var(--t1);cursor:pointer}
.ocDiffHunkBtn:hover{background:var(--bgW)}
.ocDiffHunkBtn:disabled{opacity:.4;cursor:default}

/* ===== GREP RESULTS CLICKABLE ===== */
.ocGrepFile{color:var(--p);cursor:pointer;text-decoration:underline;font-family:var(--fm);font-size:12px}
.ocGrepFile:hover{color:var(--pT)}
.ocGrepLine{color:var(--t3);font-size:11px;margin-left:4px}
.ocGrepMatch{background:rgba(250,204,21,.15);padding:1px 3px;border-radius:2px}

/* ===== FILE LINE RANGE SYNTAX HINT ===== */
.ocFileRangeHint{font-size:11px;color:var(--t3);padding:4px 14px;border-bottom:1px solid var(--bd3)}

/* ===== RESPONSIVE (container queries — respond to #oc width, not viewport) ===== */

/* Large screens: all 3 columns visible (default) */

/* Medium: hide right sidebar, make it a drawer */
@container oc (max-width:1100px){
	.ocRsb{display:none !important}
	.ocRsb.open{display:flex !important;position:fixed;right:0;top:var(--hh);height:calc(100vh - var(--hh));z-index:200;box-shadow:var(--shL);width:var(--rsb);max-width:90vw}
	.ocTogRsb{display:inline-flex !important}
}

/* Narrow: shrink left sidebar + compact header */
@container oc (max-width:900px){
	.ocSb{width:220px}
	.ocH{padding:0 10px;gap:4px}
	.ocHl{gap:2px}
	.ocHr{gap:3px}
	.ocB.ocBs{padding:0 8px;font-size:11px}
	.ocBtnTxt{display:none}
	.ocCi{padding:16px}
	.ocIA{padding:10px 16px 14px}
}

/* Mobile: sidebar becomes drawer */
@container oc (max-width:768px){
	.ocSb{position:fixed;left:0;top:0;height:100vh;z-index:200;width:280px;transform:translateX(-100%);transition:transform .25s ease}
	.ocSb.hid{transform:translateX(-100%)}
	.ocSb:not(.hid){transform:translateX(0);box-shadow:var(--shL)}
	.ocMn{width:100%}
	.ocHc{justify-content:flex-start}
	.ocHr button{padding:6px 8px}
	.ocCi{padding:12px}
	.ocIA{padding:10px 12px 14px}
	.ocIn{font-size:16px;padding:10px 14px}
	.ocFv.open{width:100%;max-width:100%;left:0;right:0}
	.ocMoB{width:95vw;max-width:95vw}
	.ocCmdB{width:95vw;max-width:95vw}
	.ocVariantDrop{left:0;right:auto}
	.ocRsb.open{width:100%;max-width:100%}
	.ocFilesP,.ocTodoP{width:100%}
	.ocChg{width:100%}
	.ocHc{display:none}
	.ocHr .ocBtnTxt{display:none}
}
</style>
</head>
<body>

<div class="ocToast" id="ocToast"></div>

<div class="ocTimeline" id="ocTimeline">
	<div class="ocTimelineH">Timeline</div>
	<div id="ocTimelineL"></div>
</div>
<div class="ocTimelineToggle" id="ocTimelineToggle" title="Toggle message timeline">
	<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
</div>

<div id="oc">
<div class="ocH">
	<div class="ocHl">
		<button class="ocIb" id="ocTogSb" title="Toggle sidebar" aria-label="Toggle sidebar"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
		<select class="ocSel" id="ocProject" title="Switch project" style="margin-left:8px;max-width:200px"><option value="">Loading projects...</option></select>
	</div>
	<div class="ocHc">
		<div class="ocModePill" id="ocModeWrap">
			<select class="ocSel" id="ocMode" title="Mode"><option value="build">Build</option><option value="plan">Plan</option></select>
		</div>
		<div style="position:relative;display:inline-flex;align-items:center" id="ocModelWrap">
			<select class="ocSel" id="ocModel" title="Model" style="display:none"><option value="">Select model</option></select>
			<div class="ocModelBtn" id="ocModelBtn" title="Model" tabindex="0">
				<span class="ocModelBtnT" id="ocModelBtnT">Select model</span>
				<svg class="ocModelBtnA" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
			</div>
			<div class="ocModelDrop" id="ocModelDrop"></div>
			<span class="ocVariantBadge" id="ocVariantBadge" title="Reasoning effort" style="display:none"></span>
			<div class="ocVariantDrop" id="ocVariantDrop"></div>
		</div>
	</div>
	<div class="ocHr">
		<button class="ocIb" id="ocProjBtn" title="Projects" aria-label="Projects"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></button>
		<button class="ocIb" id="ocFtBtn" title="Files" aria-label="Files"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg></button>
		<button class="ocIb" id="ocChgBtn" title="Changes" aria-label="Changes"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></button>
		<button class="ocIb" id="ocTodoBtn" title="Tasks" aria-label="Tasks"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></button>
		<button class="ocIb" id="ocModBtn" title="Modified Files" aria-label="Modified Files"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg></button>
		<button class="ocIb" id="ocCtxBtn" title="Project Context" aria-label="Project Context"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></button>
		<a class="ocIb" href="index.live.php?act=ai" title="Back" aria-label="Back" style="text-decoration:none"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg></a>		<button class="ocIb" id="ocReasonToggle" title="Toggle reasoning blocks" style="display:none" aria-label="Toggle reasoning"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></button>
		<span class="ocHrDiv"></span>
		<button class="ocIb" id="ocSndBtn" title="Toggle sound" aria-pressed="true" aria-label="Toggle sound">
			<svg id="ocSndIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
				<path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
				<path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
			</svg>
		</button>
		<button class="ocIb" id="ocNtfBtn" title="Toggle notifications" aria-pressed="true" aria-label="Toggle notifications" style="position:relative">
			<svg id="ocNtfIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18 s-3-2-3-9"/>
				<path d="M13.73 21a2 2 0 0 1-3.46 0"/>
			</svg>
			<span id="ocNtfDot" style="display:none;position:absolute;top:6px;right:7px;width:7px;height:7px;border-radius:50%;background:#e53e3e;border:1.5px solid var(--bg)"></span>
		</button>
		<button class="ocIb" id="ocThBtn" title="Toggle theme" aria-label="Toggle theme">
			<svg id="ocThIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M21.64 13a1 1 0 0 0-1.05-.14 8.05 8.05 0 0 1-3.37.73 8.15 8.15 0 0 1-8.14-8.1 8.59 8.59 0 0 1 .25-2A1 1 0 0 0 8 2.36 10.14 10.14 0 1 0 22 14.05a1 1 0 0 0-.36-1.05z"/>
			</svg>
		</button>
		<button class="ocIb ocTogRsb" id="ocTogRsb" title="Toggle right panel" aria-label="Toggle right panel" style="display:none"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="15" y1="3" x2="15" y2="21"/></svg></button>
		<button class="ocHMore" id="ocHMore" title="More" aria-label="More"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg></button>
		<div class="ocHMoreDrop" id="ocHMoreDrop"></div>
	</div>
</div>
<div class="ocBd">
	<div class="ocSb" id="ocSb">
		<div class="ocSbNew">
			<button class="ocB ocBP ocSbNewBtn" id="ocNew"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> New Session</button>
		</div>
		<div class="ocSbSearch"><span class="ocSbSearchIc"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span><input type="text" id="ocSbSearchIn" class="ocSbSearchIn" placeholder="Search sessions..." autocomplete="off" aria-label="Search sessions"></div>
		<div class="ocSbL" id="ocSL"></div>
		<div class="ocSbF">
			<button class="ocIb" id="ocKbBtn" title="Keyboard shortcuts" aria-label="Keyboard shortcuts"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h.01M10 8h.01M14 8h.01M18 8h.01M8 12h.01M12 12h.01M16 12h.01M7 16h10"/></svg></button>
			<button class="ocIb" id="ocHlBtn" title="Help" aria-label="Help"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></button>
			<button class="ocIb" id="ocSetBtnF" title="Settings" aria-label="Settings"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></button>
		</div>
	</div>
	<div class="ocMn">
		<div class="ocC" id="ocChat"><div class="ocCi" id="ocCI"></div></div>
		<div class="ocIA">
			<div class="ocIW">
				<div style="position:relative;flex:1;display:flex">
					<textarea class="ocIn" id="ocIn" placeholder="Ask anything or type / for commands..." rows="1" style="padding-right:32px" aria-label="Message input"></textarea>
					<button class="ocAttBtn" id="ocAttBtn" title="Attach files" aria-label="Attach files"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>
				</div>
				<input type="file" id="ocFile" multiple style="display:none">
				<button class="ocIb ocSlashBtn" id="ocSlashBtn" title="Slash commands" aria-label="Slash commands"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="19" x2="15" y2="5"/></svg></button>
				<button class="ocSn" id="ocSend" aria-label="Send"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9" fill="none"/></svg></button>
				<button class="ocStop" id="ocStopBtn" style="display:none" title="Stop (Esc)" aria-label="Stop"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="6" width="12" height="12" rx="2"/></svg></button>
				<div class="ocAtMention" id="ocAt"></div>
				<div class="ocSlashHint" id="ocSlash"></div>
			</div>
			<div class="ocChips" id="ocChips">
				<span class="ocChip" data-cmd="/todo">/todo</span>
				<span class="ocChip" data-cmd="/notify">/notify</span>
				<span class="ocChip" data-cmd="/sound">/sound</span>
				<span class="ocChip" data-cmd="/export">/export</span>
				<span class="ocChip" data-cmd="/help">/help</span>
				<span class="ocChip" data-cmd="/clear">/clear</span>
				<span class="ocChip" data-cmd="/plan">/plan</span>
				<span class="ocChip" data-cmd="/build">/build</span>
				<span class="ocChip" data-cmd="/compact">/compact</span>
				<span class="ocChip" data-cmd="/undo">/undo</span>
			</div>
		</div>
		<div class="ocSt">
			<span><span class="ocSd" id="ocSD"></span><span id="ocST">Ready</span></span>
			<span id="ocStat" style="display:none;cursor:pointer">
				<span id="ocStatCtx" title="Context window"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:2px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg><span id="ocStatCtxV">0</span><span class="ocCtxBar" id="ocCtxBar"><span class="ocCtxBarFill low" style="width:0%"></span></span><span class="ocCtxPct"></span></span>
				<span id="ocStatIn" title="Input tokens"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:2px"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg><span id="ocStatInV">0</span></span>
				<span id="ocStatCache" title="Cached tokens"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:2px"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg><span id="ocStatCacheV">0</span></span>
				<span id="ocStatOut" title="Output tokens"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:2px"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg><span id="ocStatOutV">0</span></span>
				<span id="ocStatTools" title="Tool calls"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:2px"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg><span id="ocStatToolsV">0</span></span>
				<span id="ocStatIter" title="Iterations"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:2px"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg><span id="ocStatIterV">0</span></span>
				<span id="ocStatCost" title="Cost"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:2px"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>$<span id="ocStatCostV">0</span></span>
			</span>
			<span style="flex:1"></span>
			<span style="">/home/alistiyab/public_html</span>
		</div>
	</div>
	<div class="ocRsb" id="ocRsb">
		<div class="ocRsbCard" id="ocRsbMod">
			<div class="ocRsbCardH"><span class="ocRsbCardT">Modified Files</span><span class="ocRsbBadge" id="ocRsbModCount">0</span></div>
			<div class="ocRsbFileL" id="ocRsbModList">
				<div class="ocRsbFile"><svg class="ocRsbFileI" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg><span class="ocRsbFileN">No files modified</span></div>
			</div>
			<div class="ocRsbViewAll" id="ocRsbModView">View All</div>
		</div>
		<div class="ocRsbCard" id="ocRsbCtx">
			<div class="ocRsbCardH"><span class="ocRsbCardT">Context Window</span></div>
			<div class="ocRsbBar"><div class="ocRsbBarFill" id="ocRsbCtxBar" style="width:0%"></div></div>
			<div class="ocRsbBarTxt" id="ocRsbCtxTxt">0% of 128K tokens used</div>
		</div>
		<div class="ocRsbCard" id="ocRsbVar">
			<div class="ocRsbCardH"><span class="ocRsbCardT">Model Variant</span></div>
			<div class="ocRsbVarRow">
				<span style="font-size:12px;color:var(--t2)">Effort</span>
				<select class="ocRsbVarSelect" id="ocRsbVarSel" aria-label="Model variant"><option value="default">Default</option><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>
			</div>
		</div>
	</div>
</div>
</div>

<div class="ocChg" id="ocChg">
	<div class="ocChgH"><span>Changes</span><button class="ocMoC" id="ocChgC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocChgL" id="ocChgL"></div>
</div>

<div class="ocMo" id="ocSetMod">
<div class="ocMoB">
	<div class="ocMoH"><h3>Settings</h3><div style="display:flex;gap:6px;align-items:center"><button class="ocB ocBP ocBs" id="ocAddCon" style="font-size:11px">+ Add Connection</button><button class="ocMoC" id="ocSetC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div></div>
	<div class="ocMoBd">
		<input type="text" class="ocFi" id="ocSetSearch" placeholder="Search providers..." style="margin-bottom:10px">
		<div class="ocPl" id="ocPL"></div>
	</div>
</div>
</div>

<div class="ocMo" id="ocConMod">
<div class="ocMoB" style="width:440px">
	<div class="ocMoH"><h3 id="ocConTitle">Add Connection</h3><button class="ocMoC" id="ocConC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocMoBd">
		<div class="ocFg"><label>Provider</label><select class="ocFi" id="ocProvSel" style="cursor:pointer"></select></div>
		<div class="ocFg" id="ocCustNameG" style="display:none"><label>Provider Name</label><input type="text" class="ocFi" id="ocCustName" placeholder="My Provider"></div>
		<div class="ocFg" id="ocBUG" style="display:none"><label>Base URL</label><input type="text" class="ocFi" id="ocBU" placeholder="https://api.example.com/v1"></div>
		<div class="ocFg" id="ocCustModelsG" style="display:none"><label>Models (comma-separated: id=name, id2=name2)</label><input type="text" class="ocFi" id="ocCustModels" placeholder="gpt-4o=GPT-4o, llama3=Llama 3"></div>
		<div class="ocFg"><label>API Key</label><input type="password" class="ocFi" id="ocAK" placeholder="Enter your API key (optional for some)"></div>
		<div style="display:flex;gap:8px;justify-content:flex-end"><button class="ocB" id="ocCan2">Cancel</button><button class="ocB" id="ocTst">Test</button><button class="ocB ocBP" id="ocSav">Connect</button></div>
	</div>
</div>
</div>

<div class="ocMo" id="ocCtxMod">
<div class="ocMoB" style="width:440px">
	<div class="ocMoH"><h3>Context Details</h3><button class="ocMoC" id="ocCtxC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocMoBd" id="ocCtxBd"></div>
</div>
</div>

<div class="ocMo" id="ocProjMod">
<div class="ocMoB" style="width:600px;max-height:80vh">
	<div class="ocMoH"><h3>Project Management</h3><button class="ocMoC" id="ocProjC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocMoBd">
		<div style="margin-bottom:16px">
			<div style="display:flex;gap:8px;margin-bottom:12px">
				<input type="text" class="ocFi" id="ocProjSearch" placeholder="Search projects..." style="flex:1">
				<button class="ocB ocBP" id="ocProjNewBtn" style="white-space:nowrap">+ New Project</button>
			</div>
		</div>
		<div id="ocProjList" style="max-height:400px;overflow-y:auto"></div>
		<div id="ocProjNewForm" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--bd3)">
			<h4 style="font-size:14px;font-weight:500;margin-bottom:12px">Create New Project</h4>
			<div class="ocFg"><label>Project Name (optional)</label><input type="text" class="ocFi" id="ocProjName" placeholder="My Project"></div>
			<div class="ocFg"><label>Directory Path</label>
				<div style="display:flex;gap:0">
					<span style="background:var(--bgW);border:1px solid var(--bd2);border-right:none;border-radius:var(--rS) 0 0 var(--rS);padding:7px 10px;font-size:12px;color:var(--t3);white-space:nowrap">/home/alistiyab/</span>
					<input type="text" class="ocFi" id="ocProjPath" placeholder="public_html/project" style="border-radius:0 var(--rS) var(--rS) 0">
				</div>
			</div>
			<div id="ocProjWpSection" style="display:none;margin-bottom:12px">
				<label style="font-size:12px;font-weight:400;color:var(--t2);display:block;margin-bottom:4px">Or select a WordPress installation:</label>
				<select class="ocFi" id="ocProjWpSel" style="cursor:pointer"><option value="">Select installation...</option></select>
			</div>
			<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
				<button class="ocB" id="ocProjCan">Cancel</button>
				<button class="ocB ocBP" id="ocProjCre">Create Project</button>
			</div>
		</div>
	</div>
</div>
</div>

<div class="ocMo" id="ocPctxMod">
<div class="ocMoB" style="width:720px;max-width:92vw;max-height:86vh">
	<div class="ocMoH"><h3>Project Context</h3><button class="ocMoC" id="ocPctxC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocMoBd">
		<div style="display:flex;gap:6px;margin-bottom:12px">
			<button class="ocB ocPctxTab" data-pctx-tab="bootstrap" style="flex:1;font-size:12px">Bootstrap</button>
			<button class="ocB ocPctxTab" data-pctx-tab="state" style="flex:1;font-size:12px">Project State</button>
			<button class="ocB ocPctxTab" data-pctx-tab="memory" style="flex:1;font-size:12px">Memory</button>
		</div>
		<div id="ocPctxInfo" style="font-size:12px;color:var(--t3);background:var(--bgSs);border:1px solid var(--bd);border-radius:var(--rS);padding:8px 12px;margin-bottom:12px;line-height:1.6"></div>
		<textarea id="ocPctxTxt" spellcheck="false" style="width:100%;box-sizing:border-box;height:340px;resize:vertical;background:var(--bgW);color:var(--t1);border:1px solid var(--bd2);border-radius:var(--rS);padding:12px;font:12.5px/1.6 var(--fm);outline:none"></textarea>
		<div style="display:flex;gap:8px;justify-content:space-between;margin-top:12px;align-items:center">
			<button class="ocB" id="ocPctxClear" style="border-color:var(--err);color:var(--err)">Clear</button>
			<div style="display:flex;gap:8px;align-items:center">
				<span id="ocPctxSaveSt" style="font-size:12px;color:var(--ok);opacity:0;transition:opacity .2s">Saved</span>
				<button class="ocB" id="ocPctxRefresh">Reload</button>
				<button class="ocB ocBP" id="ocPctxSave">Save</button>
			</div>
		</div>
	</div>
</div>
</div>

<div class="ocFt" id="ocFt">
	<div class="ocFtH"><span>Files</span><button class="ocB ocBs" id="ocFtRef" style="font-size:10px;padding:0 6px;height:20px"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></button></div>
	<div class="ocFtSrch"><input type="text" id="ocFtSearch" placeholder="Search files..."></div><div class="ocFtL" id="ocFtL"></div>
</div>

<div class="ocFv" id="ocFv">
<div class="ocFvB">
	<div class="ocFvH"><span id="ocFvT">file.php</span><button class="ocMoC" id="ocFvC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocFvC" id="ocFvP"><pre id="ocFvPre"></pre></div>
</div>
</div>

<div class="ocMo" id="ocHelpMod">
<div class="ocMoB" style="width:600px;max-height:80vh">
	<div class="ocMoH"><h3>Help & FAQ</h3><button class="ocMoC" id="ocHelpC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocMoBd" id="ocHelpBd" style="max-height:calc(80vh - 60px);overflow-y:auto"></div>
</div>
</div>

<div class="ocCmdP" id="ocCmdP">
<div class="ocCmdB">
	<div class="ocCmdI"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input type="text" id="ocCmdIn" placeholder="Type a command..." autocomplete="off"></div>
	<div class="ocCmdL" id="ocCmdL"></div>
</div>
</div>

<div class="ocPerm" id="ocPermMod">
<div class="ocPermB">
	<div class="ocPermH"><div class="ocPermHI" id="ocPermIcon">&#128274;</div><div><div class="ocPermHT" id="ocPermTitle">Permission Required</div><div class="ocPermHS" id="ocPermSub">tool_call</div></div></div>
	<div class="ocPermD" id="ocPermDetail"></div>
	<div class="ocPermF">
		<button class="ocB" id="ocPermDeny">Deny</button>
		<button class="ocB ocBP" id="ocPermAllowOnce">Allow Once</button>
		<button class="ocB ocBP" id="ocPermAllowAlways" style="background:var(--ok);border-color:var(--ok)">Allow Always</button>
	</div>
</div>
</div>

<div class="ocTodoP" id="ocTodoP">
	<div class="ocTodoPH"><span>Tasks</span><button class="ocMoC" id="ocTodoC" style="font-size:16px;padding:0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocTodoPL" id="ocTodoPL"><div style="padding:20px;text-align:center;color:var(--t3);font-size:12px">No tasks yet</div></div>
</div>

<div class="ocFilesP" id="ocFilesP">
	<div class="ocFilesPH"><span>Modified Files</span><button class="ocMoC" id="ocFilesC" style="font-size:16px;padding:0"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocFilesPL" id="ocFilesPL"><div style="padding:20px;text-align:center;color:var(--t3);font-size:12px">No modified files yet</div></div>
</div>

<div class="ocMo" id="ocKbMod">
<div class="ocMoB" style="width:560px;max-height:80vh">
	<div class="ocMoH"><h3>Keybindings</h3><button class="ocMoC" id="ocKbC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocMoBd" id="ocKbBd" style="max-height:calc(80vh - 60px);overflow-y:auto"></div>
</div>
</div>

<div class="ocMo" id="ocDiffMod">
<div class="ocMoB" style="width:90%;max-width:900px;max-height:85vh">
	<div class="ocMoH"><h3>Diff Viewer</h3><button class="ocMoC" id="ocDiffC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocMoBd" id="ocDiffBd" style="max-height:calc(85vh - 60px);overflow-y:auto"></div>
</div>
</div>

<div class="ocMo" id="ocForkMod">
<div class="ocMoB" style="width:500px;max-height:70vh">
	<div class="ocMoH"><h3>Fork Session</h3><button class="ocMoC" id="ocForkC"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
	<div class="ocMoBd" style="padding:0;max-height:calc(70vh - 60px);overflow:hidden;display:flex;flex-direction:column">
		<div style="padding:8px 14px;border-bottom:1px solid var(--bd3)"><input type="text" id="ocForkSearch" placeholder="Search messages..." style="width:100%;border:1px solid var(--bd);border-radius:var(--rP);padding:6px 10px;font-size:13px;background:var(--in);color:var(--t1);outline:none" onfocus="this.style.borderColor='var(--p)'" onblur="this.style.borderColor='var(--bd)'"></div>
		<div id="ocForkList" style="flex:1;overflow-y:auto;padding:4px 0"></div>
	</div>
</div>
</div>

<script>
(function(){
var A='index.live.php?act=ai&path=public_html&project_id=alistiyab_a6093d3b779b',C='',SP='/home/alistiyab/public_html',HD='/home/alistiyab',PID='alistiyab_a6093d3b779b',pv=[],str=false,cS=null,aC=null,ctx='',selModel='',selMode='',lastStats={},modelCtx={},projects=[],wordpressInstalls=[],pendingFiles=[],_origTitle=document.title,_notifyPending=false,lockActive=false,_intentionalUnload=false,_sessions=[],_todoItems=[],_modifiedFiles=[],_shellHistory=[],_soundEnabled=true,_desktopNotifEnabled=false,_keybindings={},_providersCache=null,_providersLoading=false,_projectsCache=null,_statusLoaded=false,_altNumBuf='',_altNumTimer=null,_streamSid=null,_activeThinkBubble=null,_deletedIds={},_unreadIds={};
// Per-session streaming state: allows multiple sessions to stream independently.
// Keyed by conversation ID. Each entry: { xhr, streaming, thinkBubble, bb, full, firstContent, reasonDiv, reasonText, errorShown, lockActive }

// Sync the global str/lockActive flags and send/stop button visibility
// to match the currently-viewed session's per-session streaming state.
// Call this whenever: switching sessions, starting/stopping a stream,
// or when a stream completes (done/error/load) for a session.
function syncStreamUI(forceSid){
	var sid=forceSid||aC;
	var isStreaming=!!(sid&&_ss[sid]&&_ss[sid].streaming);
	str=isStreaming;
	lockActive=isStreaming;
	var sendBtn=document.getElementById('ocSend');
	var stopBtn=document.getElementById('ocStopBtn');
	if(isStreaming){
		if(sendBtn)sendBtn.style.display='none';
		if(stopBtn)stopBtn.style.display='flex';
	}else{
		if(sendBtn)sendBtn.style.display='flex';
		if(stopBtn)stopBtn.style.display='none';
	}
}
var _ss={};

marked.setOptions({breaks:true,gfm:true});
var R=new marked.Renderer();
R.code=function(code,lang){
	var l=lang||'',h='';
	try{h=hljs.highlightAuto(code).value}catch(e){h=code.replace(/</g,'&lt')}
	var lines=code.split('\n').length;
	var coll=lines>15;
	var wrapCls=coll?'ocCodeColl collapsed':'';
	var toggleBtn=coll?'<button class="ocCodeToggle" data-action="expand">Expand</button>':'';
	return '<div class="'+wrapCls+'"><div class="ocCH"><span>'+l+'</span><button class="ocCC" data-action="copy">Copy</button>'+toggleBtn+'</div><pre><code class="hljs">'+h+'</code></pre></div>';
};
marked.use({renderer:R});
window._cp=function(el){
	var c=el.closest('pre').querySelector('code');
	navigator.clipboard.writeText(c.textContent);
	el.textContent='Copied!';
	setTimeout(function(){el.textContent='Copy'},1200);
};
window._toggleCode=function(btn){
	var wrap=btn.closest('.ocCodeColl');
	if(!wrap)return;
	wrap.classList.toggle('collapsed');
	btn.textContent=wrap.classList.contains('collapsed')?'Expand':'Collapse';
};

function api(a,d,cb){
	var iP=!!d,u=A+'&ai_php_api='+a+'&csrf_token='+C,x=new XMLHttpRequest();
	x.open(iP?'POST':'GET',u,true);
	x.setRequestHeader('Accept','application/json');
	if(iP)x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	x.onload=function(){
		if(x.status===200){
			try{cb(null,JSON.parse(x.responseText))}
			catch(e){cb('Parse error')}
		}else cb('HTTP '+x.status);
	};
	x.onerror=function(){cb('Network error')};
	x.send(iP?bp(d):null);
}

function bp(o){
	var p=[];
	for(var k in o){
		if(o.hasOwnProperty(k))p.push(encodeURIComponent(k)+'='+encodeURIComponent(o[k]));
	}
	return p.join('&');
}

function md(t){
	if(!t)return '';
	try{
		return DOMPurify.sanitize(marked.parse(t));
	}catch(e){
		return '<p>'+esc(t)+'</p>';
	}
}

// Event delegation for code-block Copy/Expand buttons.
// DOMPurify strips inline onclick handlers, so we use data-action attributes
// and handle clicks at the container level. This works for all rendered messages.
document.addEventListener('click',function(e){
	var btn=e.target.closest('[data-action]');
	if(!btn)return;
	var action=btn.getAttribute('data-action');
	if(action==='copy'){
		var pre=btn.closest('.ocCH');
		if(!pre)return;
		var codeEl=pre.parentElement.querySelector('pre code');
		if(!codeEl)return;
		navigator.clipboard.writeText(codeEl.textContent).then(function(){
			btn.textContent='Copied!';
			setTimeout(function(){btn.textContent='Copy'},1200);
		},function(){
			// Fallback for non-secure contexts
			var ta=document.createElement('textarea');
			ta.value=codeEl.textContent;
			ta.style.position='fixed';ta.style.left='-9999px';
			document.body.appendChild(ta);ta.select();
			try{document.execCommand('copy')}catch(e){}
			document.body.removeChild(ta);
			btn.textContent='Copied!';
			setTimeout(function(){btn.textContent='Copy'},1200);
		});
	}else if(action==='expand'){
		var wrap=btn.closest('.ocCodeColl');
		if(!wrap)return;
		wrap.classList.toggle('collapsed');
		btn.textContent=wrap.classList.contains('collapsed')?'Expand':'Collapse';
	}
});
function autoResize(el){
	if(!el)return;
	el.style.height='auto';
	var h=Math.max(48,Math.min(el.scrollHeight,160));
	el.style.height=h+'px';
}
function esc(s){var d=document.createElement('div');d.textContent=s;return d.innerHTML}

function st(t,c){
	document.getElementById('ocST').textContent=t;
	document.getElementById('ocSD').className='ocSd '+(c?'on':'off');
}

var _userSvg='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';

function rMsg(m){
	var ch=document.getElementById('ocCI');
	var d=document.createElement('div');
	d.className='ocMsg '+(m.role==='user'?'u':'a');
	var av=document.createElement('div');
	av.className='ocAv';
	av.innerHTML=m.role==='user'?_userSvg:'AI';
	var bd=document.createElement('div');
	bd.className='ocMb';
	var bb=document.createElement('div');
	bb.className='ocMbb';
	if(m.role==='user'){
		bb.textContent=m.content||'';
	}else if(m.content==='__thinking__'){
		bb.innerHTML='<div class="ocThink"><span class="ocThinkD"></span><span class="ocThinkD"></span><span class="ocThinkD"></span><span class="ocThinkT">Thinking...</span></div>';
		bb.dataset.thinking='1';
	}else{
		var pts=m.parts||[],tx='',ht=false;
		pts.forEach(function(p){
			if(p.type==='text'&&p.text){tx+=p.text;ht=true}
		});
		pts.forEach(function(p){
			if(p.type==='reasoning'&&p.text){
				var rd=document.createElement('div');
				rd.className='ocReason';
				rd.innerHTML='<span>Thoughts</span><div class="ocReasonB">'+esc(p.text)+'</div>';
				rd.addEventListener('click',function(){this.classList.toggle('open')});
				bb.appendChild(rd);
			}
		});
		if(ht)bb.innerHTML+='<div class="ocMd">'+md(tx)+'</div>';
		pts.forEach(function(p){
			if(p.type==='tool_use'){
				var t=document.createElement('div');
				t.className='ocTk';
				if(p.id)t.setAttribute('data-tcid',p.id);
				t.innerHTML='<div class="ocTkH" onclick="this.parentElement.classList.toggle(\'open\')">'
					+'<span class="ocTkN">'+esc(p.name||'tool')+'</span>'
					+'<span class="ocTkDt">'+esc(JSON.stringify(p.input||{}).substring(0,100))+'</span>'
					+'</div><div class="ocTkB">'+esc(JSON.stringify(p.input||{},null,2))+'</div>';
				bb.appendChild(t);
			}
		});
		if(!ht&&pts.length>0&&!pts.some(function(p){return p.type==='tool_use'})){
			if(typeof m.content==='string'&&m.content){
				bb.innerHTML='<div class="ocMd">'+md(m.content)+'</div>';
			}
		}
	}
	bd.appendChild(bb);
	d.appendChild(av);
	d.appendChild(bd);
	ch.appendChild(d);
	document.getElementById('ocChat').scrollTop=1e9;
	return bb;
}

function extractModifiedFilesFromMessages(msgs){
	_modifiedFiles=[];
	if(!msgs||!msgs.length)return;
	msgs.forEach(function(m){
		if(m.role!=='assistant')return;
		(m.parts||[]).forEach(function(p){
			if(p.type!=='tool_use')return;
			var name=p.name||'';
			var inp=p.input||{};
			if(name==='write_file'||name==='edit_file'||name==='replace_in_file'||name==='apply_patch'){
				var fp=inp.path||'';
				if(fp)addModifiedFile(fp,name==='write_file'?'add':'mod',0);
			}
			if(name==='bash'&&inp.command){
				var writeMatch=inp.command.match(/(?:>|\s>>\s|sed\s|awk\s|perl\s|cp\s|mv\s|rm\s)\s*[`"']?([^\s`"';|&]+)[`"']?/);
				if(writeMatch&&writeMatch[1]!=='/dev/null')addModifiedFile(writeMatch[1],'mod',0);
			}
		});
	});
}

function rMsgs(msgs){
	var ch=document.getElementById('ocCI');
	ch.innerHTML='';
	extractModifiedFilesFromMessages(msgs);
	if(!msgs||!msgs.length){
		ch.innerHTML='<div class="ocE"><div class="ocEi"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3L20 7.5V16.5L12 21L4 16.5V7.5L12 3Z"/><path d="M12 12L20 7.5"/><path d="M12 12V21"/><path d="M12 12L4 7.5"/></svg></div>'
			+'<div class="ocEt">Code with AI</div>'
			+'<div class="ocEs">Select a model above, then ask me to code, debug, or refactor.<br>'
			+'I can read, write, edit files and run shell commands.</div></div>';
		rCtx();
		return;
	}
	rCtx();
	var totalIn=0,totalOut=0,totalCost=0,toolCalls=0,iters=0,ctxTokens=0,totalCached=0;
	msgs.forEach(function(m){
		if(m.role!=='assistant')return;
		iters++;
		if(m.usage){
			totalIn+=(m.usage.prompt_tokens||0)+(m.usage.input_tokens||0);
			totalOut+=(m.usage.completion_tokens||0)+(m.usage.output_tokens||0);
			totalCached+=(m.usage.cached_tokens||0)+(m.usage.cache_read_input_tokens||0)+(m.usage.cache_creation_input_tokens||0);
			if(m.usage.prompt_tokens_details&&m.usage.prompt_tokens_details.cached_tokens)totalCached+=m.usage.prompt_tokens_details.cached_tokens;
			if(m.usage.cost)totalCost+=m.usage.cost;
			if(m.usage.cost_details&&m.usage.cost_details.upstream_inference_cost)totalCost+=m.usage.cost_details.upstream_inference_cost;
		}
		(m.parts||[]).forEach(function(p){if(p.type==='tool_use')toolCalls++});
	});
	var mrg=[];
	for(var i=0;i<msgs.length;i++){
		var mm=msgs[i];
		if(mm.role==='tool_result')continue;
		if(mm.role==='assistant'){
			var ml=mrg[mrg.length-1];
			if(ml&&ml.role==='assistant'){
				ml.parts=ml.parts.concat(mm.parts||[]);
				ml.id=mm.id;
				if(mm.model)ml.model=mm.model;
			}else{
				mrg.push({id:mm.id,role:mm.role,content:mm.content,model:mm.model,provider:mm.provider,time:mm.time,usage:mm.usage,parts:(mm.parts||[]).slice()});
			}
		}else{
			mrg.push(mm);
		}
	}
	mrg.forEach(function(m){
		if(m.role==='tool_result')return;
		rMsg(m);
	});
	// Attach stored diffs from tool_result messages to their tool_use elements
	msgs.forEach(function(m){
		if(m.role!=='tool_result'||!m.diff)return;
		var tcId=m.tool_call_id;
		if(!tcId)return;
		var tuse=document.querySelector('.ocTk[data-tcid="'+(window.CSS&&CSS.escape?CSS.escape(tcId):tcId.replace(/["\\]/g,'\\$&'))+'"]');
		if(!tuse)return;
		var body=tuse.querySelector('.ocTkB');
		if(body){
			body.innerHTML=renderDiff(m.diff);
			var diffEl=body.querySelector('.ocDiff');
			if(diffEl)diffEl.setAttribute('data-raw',m.diff);
			var summary=String(m.content||'').substring(0,100);
			var dt=tuse.querySelector('.ocTkDt');
			if(dt)dt.textContent=summary;
			if(m.is_error)tuse.classList.add('err');
		}
	});
	if(totalIn>0||iters>0){
		var totalChars=0;
		msgs.forEach(function(m){
			if(m.content&&typeof m.content==='string')totalChars+=m.content.length;
			(m.parts||[]).forEach(function(p){
				if(p.text)totalChars+=p.text.length;
				if(p.input)totalChars+=JSON.stringify(p.input).length;
			});
		});
		ctxTokens=Math.round(totalChars/4);
		updateStats({usage:{input_tokens:totalIn,output_tokens:totalOut,cached_tokens:totalCached},iterations:iters,context_tokens:ctxTokens,tool_calls:toolCalls,cost:totalCost});
	}else{
		document.getElementById('ocStat').style.display='none';
	}
	if(typeof updateReasonToggle==='function')updateReasonToggle();
}

function rCtx(){
	if(!ctx)return;
	var w=document.getElementById('ocCI');
	var ex=w.querySelector('.ocCtx');
	if(ex)return;
	var d=document.createElement('div');
	d.className='ocCtx';
	d.innerHTML='<div class="ocCtxI">'+ctx+'</div>';
	w.insertBefore(d,w.firstChild);
}

function abortStream(sid){
	var targetId=sid||aC;
	if(targetId&&_ss[targetId]&&_ss[targetId].xhr){
		_ss[targetId].xhr.abort();
	}
	// Clear per-session streaming state
	if(targetId&&_ss[targetId]){
		_ss[targetId].streaming=false;
		_ss[targetId].lockActive=false;
		_ss[targetId].xhr=null;
	}
	// If aborting the currently-viewed session, update visible UI
	if(!sid||sid===aC){
		syncStreamUI();
		_activeThinkBubble=null;
	}
	if(_ss[targetId]){
		_ss[targetId].streaming=false;
		_ss[targetId].lockActive=false;
		_ss[targetId].firstContent=false;
		_ss[targetId].reasonDiv=null;
		_ss[targetId].reasonText='';
		_ss[targetId].bb=null;
		_ss[targetId].full='';
		_ss[targetId].errorShown=false;
		_ss[targetId].xhr=null;
		_ss[targetId].thinkBubble=null;
	}
	if(!sid||sid===(_streamSid||aC)){
		clearGenTab(_streamSid||aC);
		_streamSid=null;
		if(!sid||sid===aC) _activeThinkBubble=null;
	}
}

function loadC(id){
	abortStream();
	aC=id;
	clearGenTab(id);
	clearUnread(id);
	delete _unreadIds[id];
	_lastSeen[id]=Math.floor(Date.now()/1000);
	updateURL(id);
	loadTodos();
	loadPromptHistoryForSession();
	var u=A+'&ai_php_api=conversation&csrf_token='+C+'&conversation_id='+encodeURIComponent(id);
	var x=new XMLHttpRequest();
	x.open('GET',u,true);
	x.setRequestHeader('Accept','application/json');
	x.onload=function(){
		if(x.status===200){
			try{
				var d=JSON.parse(x.responseText);
				rMsgs(d.messages||[]);
				hlS(id);
				if(!d.messages||!d.messages.length){aC=null;updateURL('')}
				else{
					var userMsgs=[];
					var userEls=document.querySelectorAll('.ocMsg.u .ocMbb');
					for(var i=0;i<userEls.length;i++){var t=userEls[i].textContent;if(t)userMsgs.push(t)}
					if(userMsgs.length){promptHistory=userMsgs.slice(-50);promptHistoryIdx=-1;savePromptHistory()}
				}
			}catch(e){}
		}
	};
	x.send();
}

function switchToSessionIdx(idx){
	if(!_sessions||!_sessions[idx])return;
	var s=_sessions[idx];
	var mv=document.getElementById('ocModel').value;
	var pp=mv.split('/');
	var _sp={provider:pp[0],model:pp.slice(1).join('/'),mode:document.getElementById('ocMode').value};
	api('start',_sp,function(){
		aC=s.id;
		api('switch_conversation',{conversation_id:s.id},function(){loadC(s.id)});
	});
}

function loadSL(){
	api('conversations',null,function(e,d){
		if(e||!d)return;
		var ls=document.getElementById('ocSL');
		ls.innerHTML='';
		if(!d.length){
			ls.innerHTML='<div style="padding:20px;color:var(--t3);font-size:12px;text-align:center">No sessions yet</div>';
			return;
		}
		_sessions=d;
		var lastDayKey='';
		d.forEach(function(s,i){
			// Day separator
			if(s.updated_at){
				var dt=new Date(s.updated_at*1000);
				var today=new Date();
				var dayKey=dt.toDateString();
				if(dayKey!==lastDayKey){
					lastDayKey=dayKey;
					var dayLabel;
					if(dt.toDateString()===today.toDateString()){
						dayLabel='Today';
					}else{
						var yesterday=new Date(today);
						yesterday.setDate(yesterday.getDate()-1);
						if(dt.toDateString()===yesterday.toDateString()){
							dayLabel='Yesterday';
						}else{
							dayLabel=dt.toLocaleDateString([],{weekday:'long',month:'short',day:'numeric'});
						}
					}
					var sep=document.createElement('div');
					sep.className='ocSbSep';
					sep.textContent=dayLabel;
					ls.appendChild(sep);
				}
			}
			var it=document.createElement('div');
			it.className='ocSi'+(s.id===aC?' act':'');
			it.dataset.id=s.id;
			var ts='';
			if(s.updated_at){
				var dt2=new Date(s.updated_at*1000);
				ts=dt2.toLocaleDateString()===new Date().toLocaleDateString()
					?dt2.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})
					:dt2.toLocaleDateString([],{month:'short',day:'numeric'});
			}
			var numBadge='<span class="ocSiN">'+(i+1)+'</span>';
			it.innerHTML=numBadge+'<span class="ocSiI"></span>'
				+'<span class="ocSiT">'+esc(s.title||'Untitled')+'</span>'
				+(s.message_count?'<span class="ocSiB">'+s.message_count+'</span>':'')
				+'<button class="ocSiD" data-d="1" title="Delete"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';

			it.addEventListener('click',function(ev){

				let btn = ev.target.closest('button');

				if(btn && btn.dataset && btn.dataset.d){
					ev.stopPropagation();
					if(confirm('Delete this session?')){
						// Abort any in-flight stream for this session (active or background)
						// so the server stops generating and does not resurrect it.
						abortStream(s.id);
						if(!_deletedIds)_deletedIds={};
						_deletedIds[s.id]=true;
						api('delete_conversation',{conversation_id:s.id},function(){
							loadSL();
							if(aC===s.id){aC=null;rMsgs([])}
							delete _deletedIds[s.id];
						});
					}
					return;
				}
				var mv=document.getElementById('ocModel').value;
				var pp=mv.split('/');
			var _sp={provider:pp[0],model:pp.slice(1).join('/'),mode:document.getElementById('ocMode').value};
			api('start',_sp,function(){
				aC=s.id;
				api('switch_conversation',{conversation_id:s.id},function(){
					loadC(s.id);
				});
				});
			});
			ls.appendChild(it);
		});
		syncSessionStates(d);
	});
}

function hlS(id){
	document.querySelectorAll('.ocSi').forEach(function(el){
		el.classList.toggle('act',el.dataset.id===id);
	});
}

function setGenTab(id){
	if(!id)return;
	var el=document.querySelector('#ocSL .ocSi[data-id="'+(window.CSS&&CSS.escape?CSS.escape(id):id.replace(/["\\]/g,'\\$&'))+'"]');
	if(!el)return;
	// 'gen' (red, "thinking") applies to the generating session including when
	// it is the currently-viewed (active) session. CSS ordering (.gen after .act)
	// makes the red dot override the active green dot while generating.
	el.classList.remove('unread');
	el.classList.add('gen');
}


function clearGenTab(id){
	if(!id)return;
	var el=document.querySelector('#ocSL .ocSi[data-id="'+(window.CSS&&CSS.escape?CSS.escape(id):id.replace(/["\\]/g,'\\$&'))+'"]');
	if(el){
		el.classList.remove('gen');
		var gt=el.querySelector('.ocSiGt');
		if(gt)gt.remove();
	}
}

function clearAllGenTabs(){
	document.querySelectorAll('#ocSL .ocSi.gen').forEach(function(el){
		el.classList.remove('gen');
		var gt=el.querySelector('.ocSiGt');
		if(gt)gt.remove();
	});
}

function markUnread(id){
	if(!id)return;
	var el=document.querySelector('#ocSL .ocSi[data-id="'+(window.CSS&&CSS.escape?CSS.escape(id):id.replace(/["\\]/g,'\\$&'))+'"]');
	if(el){
		el.classList.remove('gen');
		el.classList.add('unread');
		var gt=el.querySelector('.ocSiGt');
		if(gt)gt.remove();
	}
}

function clearUnread(id){
	if(!id)return;
	var el=document.querySelector('#ocSL .ocSi[data-id="'+(window.CSS&&CSS.escape?CSS.escape(id):id.replace(/["\\]/g,'\\$&'))+'"]');
	if(el){
		el.classList.remove('unread');
	}
}

// Tracks which sessions the current page has marked as unread (a background
// session finished while the user was viewing another one). Stays set until
// the user opens that session, surviving sidebar re-renders and polls.
var _lastSeen={};

function syncSessionStates(sessions){
	if(!sessions)return;
	sessions.forEach(function(s){
		if(!s||!s.id)return;
		var isActive=(s.id===aC);
		var updated=s.updated_at||0;
		if(isActive){
			// Viewing this session: never "unread". While generating, show red;
			// when idle, clear it so the active session shows its normal state.
			clearUnread(s.id);
			delete _unreadIds[s.id];
			if(s.running){setGenTab(s.id)}else{clearGenTab(s.id)}
		}else if(s.running){
			// AI still thinking in another session -> red dot
			clearUnread(s.id);
			setGenTab(s.id);
		}else{
			clearGenTab(s.id);
			// AI finished in another session -> blue dot + bold title (unread).
			// Marked directly by the done handler (_unreadIds) or detected here
			// when the server reports a completion newer than what was seen.
			var firstSeen=(typeof _lastSeen[s.id]==='undefined');
			if(s.last_status==='completed'&&(_unreadIds[s.id]||(!firstSeen&&updated>(_lastSeen[s.id]||0)))){
				markUnread(s.id);
				_unreadIds[s.id]=true;
			}
		}
		_lastSeen[s.id]=updated;
	});
}

function pollSessionStates(){
	if(document.hidden)return;
	api('conversations',null,function(e,d){
		if(e||!d)return;
		syncSessionStates(d);
	});
}

setInterval(function(){pollSessionStates()},5000);

document.getElementById('ocSbSearchIn').addEventListener('input',function(){
	var q=this.value.toLowerCase().trim();
	var items=document.querySelectorAll('.ocSi');
	items.forEach(function(it){
		var title=it.querySelector('.ocSiT');
		if(!title)return;
		var match=!q||title.textContent.toLowerCase().indexOf(q)!==-1;
		it.style.display=match?'':'none';
	});
});

function updateStats(d){
	if(!d)return;
	lastStats=d;
	var u=d.usage||{};
	document.getElementById('ocStat').style.display='inline-flex';
	document.getElementById('ocStatCtxV').textContent=fmtTok(d.context_tokens||0);
	document.getElementById('ocStatInV').textContent=fmtTok(u.input_tokens||0);
	var cached=u.cached_tokens||0;
	document.getElementById('ocStatCacheV').textContent=cached>0?fmtTok(cached):'0';
	document.getElementById('ocStatOutV').textContent=fmtTok(u.output_tokens||0);
	document.getElementById('ocStatToolsV').textContent=d.tool_calls||0;
	document.getElementById('ocStatIterV').textContent=d.iterations||0;
	var cost=d.cost||0;
	var mv=document.getElementById('ocModel').value;
	var isFreeModel=mv&&mv.indexOf('opencode_zen/')===0;
	var costEl=document.getElementById('ocStatCost');
	if(isFreeModel||cost<=0){
		costEl.style.display='none';
	}else{
		costEl.style.display='inline-flex';
		document.getElementById('ocStatCostV').textContent=cost<0.01&&cost>0?cost.toFixed(6):cost.toFixed(4);
	}
	var ctxEl=document.getElementById('ocStatCtx');
	var barEl=document.getElementById('ocCtxBar');
	var pctLabel=document.querySelector('.ocCtxPct');
	if(barEl){
		var fill=barEl.querySelector('.ocCtxBarFill');
		if(fill){fill.style.width='0%';fill.className='ocCtxBarFill low'}
	}
	if(pctLabel){pctLabel.textContent=''}
	var ctxTok=d.context_tokens||0;
	if(mv&&ctxTok>0){
		var modelId=mv.split('/').slice(1).join('/');
		var limit=modelCtx[modelId]||0;
		if(limit>0){
			var pct=ctxTok/limit;
			if(barEl){
				if(fill){
					fill.style.width=Math.max(3,Math.min(pct*100,100))+'%';
					fill.className='ocCtxBarFill '+(pct>0.9?'high':(pct>0.75?'mid':'low'));
				}
			}
			if(pctLabel){pctLabel.textContent=Math.round(pct*100)+'%'}
		}
	}
}

function newS(){
	// Don't abort an in-flight stream just to open a blank session — the previous
	// session may still be thinking and should keep running independently.
	api('new_session',{},function(e,r){
		if(!e&&r&&r.id){
			aC=r.id;
			updateURL(aC);
			syncStreamUI();
			rMsgs([]);
			loadPromptHistoryForSession();
			loadSL();
			setTimeout(function(){hlS(aC)},100);
			var mv=document.getElementById('ocModel').value;
			var pp=mv.split('/');
		var _sp={provider:pp[0],model:pp.slice(1).join('/'),mode:document.getElementById('ocMode').value};
		api('start',_sp,function(){});
		}
	});
}

function send(){
	var inp=document.getElementById('ocIn');
	var text=inp.value.trim();
	if(!text)return;
	// Only block if the CURRENT session is streaming, not other sessions
	if(_ss[aC]&&_ss[aC].streaming)return;

	if(text.charAt(0)==='!'&&text.length>1){
		var shellCmd=text.substring(1);
		inp.value='';autoResize(inp);
		if(text&&!(_ss[aC]&&_ss[aC].streaming)){promptHistory.push(text);if(promptHistory.length>50)promptHistory.shift();promptHistoryIdx=-1;savePromptHistory()}
		rMsg({role:'user',content:text});
		var shellEl=rMsg({role:'asst',content:'',parts:[]});
		shellEl.innerHTML='<div class="ocShellPrompt">'+esc(shellCmd)+'</div><div class="ocTerm">Running...</div>';
		// Per-session: mark this session as streaming too
		if(!_ss[aC]) _ss[aC]={};
		_ss[aC].streaming=true;
		_ss[aC].lockActive=true;
		syncStreamUI();
		st('Running shell...',true);
		api('shell',{command:shellCmd},function(e,r){
			if(aC&&_ss[aC]){_ss[aC].streaming=false;_ss[aC].lockActive=false}
			syncStreamUI();
			if(!e&&r){
				var output=r.output||'';
				if(r.error)output+='\nError: '+r.error;
				shellEl.innerHTML='<div class="ocShellPrompt">'+esc(shellCmd)+'</div><div class="ocTerm">'+esc(output)+'</div>';
				if(r.returncode!==0)shellEl.querySelector('.ocTerm').style.borderColor='var(--wr)';
			}else{
				shellEl.innerHTML='<div class="ocShellPrompt">'+esc(shellCmd)+'</div><div class="ocTerm" style="color:var(--err)">Error: '+(e||'Unknown error')+'</div>';
			}
			st('Ready',true);
			_playSound('complete');
			_notifyOnComplete('Shell Complete','Command finished');
			loadSL();
		});
		return;
	}

	if(text.charAt(0)==='/'){
		inp.value='';
		autoResize(inp);
		var cmd=text.replace(/\s+/g,' ').split(' ');
		var c=cmd[0].toLowerCase();
		if(c==='/clear'){api('clear',{},function(){rMsgs([]);st('Cleared',true)});return}
		if(c==='/export'){showExportDialog();return}
		if(c==='/copy'){
			var msgs=document.querySelectorAll('#ocCI .ocMsg');
			var mdText='';
			msgs.forEach(function(el){
				var isUser=el.classList.contains('u');
				mdText+=(isUser?'**User:** ':'**Assistant:** ');
				var textEl=el.querySelector('.ocMd');
				if(textEl){
					mdText+=textEl.innerText||'';
				}else if(isUser){
					var userEl=el.querySelector('.ocMbb');
					if(userEl)mdText+=userEl.innerText||'';
				}
				mdText+='\n\n';
			});
			navigator.clipboard.writeText(mdText).then(function(){toast('Copied to clipboard','ok')},function(){toast('Copy failed','err')});
			return;
		}
		if(c==='/plan'){api('set_mode',{mode:'plan'},function(){document.getElementById('ocMode').value='plan';st('Plan mode',true)});return}
		if(c==='/build'){api('set_mode',{mode:'build'},function(){document.getElementById('ocMode').value='build';st('Build mode',true)});return}
		if(c==='/compact'){
			st('Compacting...',true);
			api('compact',{keep_last_n:3},function(e2,r2){
				if(!e2&&r2&&r2.success){
					rMsgs(r2.messages||[]);
					if(r2.summary){toast('Nothing to compact','warn')}else{toast('Conversation compacted','ok')}
					st('Ready',true);
				}else{
					toast('Failed to compact: '+(e2||(r2&&r2.error)||'Unknown error'),'err');
					st('Ready',true);
				}
			});
			return;
		}
		if(c==='/undo'){
			if(!rMsgs._lastMsgs||!rMsgs._lastMsgs.length){toast('Nothing to undo','warn');return}
			var lastUser=null;
			for(var i=rMsgs._lastMsgs.length-1;i>=0;i--){
				if(rMsgs._lastMsgs[i].role==='user'){lastUser=rMsgs._lastMsgs[i];break}
			}
			if(!lastUser||!lastUser.id){toast('Cannot undo: no user message found','warn');return}
			api('undo',{message_id:lastUser.id},function(e,r){
				if(!e&&r&&r.success){
					rMsgs(r.messages||[]);
					toast('Message undone (use /redo to restore)','ok');
					st('Ready',true);
					loadSL();
				}else{
					toast('Failed to undo: '+(e||(r&&r.error)||'Unknown error'),'err');
					st('Ready',true);
				}
			});
			return;
		}
		if(c==='/redo'){
			st('Redoing...',true);
			api('redo',{},function(e,r){
				if(!e&&r&&r.success){
					rMsgs(r.messages||[]);
					toast('Message restored','ok');
					st('Ready',true);
					loadSL();
				}else{
					toast('Nothing to redo: '+(e||(r&&r.error)||'Unknown error'),'err');
					st('Ready',true);
				}
			});
			return;
		}
		if(c==='/fork'){
			showForkModal();
			return;
		}
		if(c==='/rename'){
			var title=prompt('Enter new session title:',curSessionTitle());
			if(title!==null&&title.trim()){
				api('rename_conversation',{conversation_id:aC,title:title.trim()},function(e,r){
					if(!e&&r&&r.success){
						toast('Session renamed','ok');
						loadSL();
					}else{
						toast('Failed to rename','err');
					}
				});
			}
			return;
		}
		if(c==='/help'){
			document.getElementById('ocHlBtn').click();
			return;
		}
		if(c==='/todo'){
			var todoText=cmd.slice(1).join(' ');
			if(!todoText){toast('Usage: /todo <task text>','warn');return}
			addTodo(todoText,'medium','pending');
			toast('Task added','ok');
			return;
		}
		if(c==='/sound'){
			_soundEnabled=!_soundEnabled;
			localStorage.setItem('oc_sound_enabled',_soundEnabled?'1':'0');
			applySndIcon(_soundEnabled);
			toast('Sound Notifications - '+(_soundEnabled?'ON':'OFF'),'ok');
			return;
		}
		if(c==='/notify'){
			if(typeof Notification==='undefined'){toast('Notifications not supported','warn');return}
			if(Notification.permission==='default'){
				Notification.requestPermission(function(p){
					_desktopNotifEnabled=(p==='granted');
					applyNtfIcon(_desktopNotifEnabled);
					toast('Desktop Notifications - '+(_desktopNotifEnabled?'ON':'OFF'),'ok');
				});
			}else if(Notification.permission==='granted'){
				_desktopNotifEnabled=!_desktopNotifEnabled;
				applyNtfIcon(_desktopNotifEnabled);
				toast('Desktop Notifications - '+(_desktopNotifEnabled?'ON':'OFF'),'ok');
			}else{
				_desktopNotifEnabled=false;
				applyNtfIcon(false);
				toast('Notifications blocked in browser settings','warn');
			}
			return;
		}
		if(c==='/keybindings'){
			renderKeybindings();
			document.getElementById('ocKbMod').classList.add('open');
			return;
		}
		if(c==='/timeline'){
			_timelineOpen=!_timelineOpen;
			document.getElementById('ocTimeline').classList.toggle('open',_timelineOpen);
			if(_timelineOpen){renderTimeline();toast('Timeline opened','ok')}
			else{toast('Timeline closed','info')}
			return;
		}
		var bb=rMsg({role:'asst',content:'',parts:[]});
		bb.innerHTML='<div style="color:var(--err);padding:6px">Unknown command: '+esc(c)+'. Try /help</div>';
		return;
	}

	inp.value='';
	autoResize(inp);
	if(text&&!(_ss[aC]&&_ss[aC].streaming)){promptHistory.push(text);if(promptHistory.length>50)promptHistory.shift();promptHistoryIdx=-1;savePromptHistory()}
	if(typeof Notification!=='undefined'&&Notification.permission==='default')Notification.requestPermission();
	rMsg({role:'user',content:text});
	// Per-session: mark this session as streaming
	if(!_ss[aC]) _ss[aC]={};
	_ss[aC].streaming=true;
	_ss[aC].lockActive=true;
	_ss[aC].firstContent=false;
	_ss[aC].errorShown=false;
	_ss[aC].full='';
	_ss[aC].bb=null;
	_ss[aC].reasonDiv=null;
	_ss[aC].reasonText='';
	syncStreamUI();
	st('Thinking...',true);
	var thinkBubble=rMsg({role:'asst',content:'__thinking__',parts:[]});
	_streamSid=aC;
	_ss[aC].thinkBubble=thinkBubble;
	setGenTab(aC);

 	var doSend=function(){
		var bb=null,full='',firstContent=false,reasonDiv=null,reasonText='',errorShown=false;
		// Capture this stream's conversation id at send time. Events only render
		// when the user is viewing THIS session; this keeps multiple concurrent
		// streams (one session thinking while another is worked on) fully isolated
		// — events from a background session must never bleed into the active view.
		var streamSid=aC;
		var sendText=text;
		if(pendingFiles.length>0){
			sendText+='\n\n[Attached files:]';
			pendingFiles.forEach(function(f){
				sendText+='\n\n--- '+f.name+' ---\n'+f.content;
			});
		}
		var params='content='+encodeURIComponent(sendText)+'&csrf_token='+C;
		if(aC)params+='&conversation_id='+encodeURIComponent(aC);
		if(pendingFiles.length>0)params+='&attachments='+encodeURIComponent(JSON.stringify(pendingFiles));
		pendingFiles=[];
		document.getElementById('ocFile').value='';
		var xhr=new XMLHttpRequest();
		xhr.open('POST',A+'&ai_chat_stream=1',true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		// Store XHR in per-session state instead of global cS
		if(!_ss[aC]) _ss[aC]={};
		_ss[aC].xhr=xhr;
		_ss[aC].streaming=true;
		_ss[aC].lockActive=true;
		_ss[aC].firstContent=false;
		_ss[aC].bb=null;
		_ss[aC].full='';
		_ss[aC].reasonDiv=null;
		_ss[aC].reasonText='';
		_ss[aC].errorShown=false;
		cS=xhr;
		var buf='';
		var lineBuf='';
		xhr.onprogress=function(){
			var nt=xhr.responseText.substring(buf.length);
			buf=xhr.responseText;
			lineBuf+=nt;
			var lines=lineBuf.split('\n');
			lineBuf=lines.pop();
			lines.forEach(function(line){
				if(line.indexOf('data: ')!==0)return;
				try{
					var d=JSON.parse(line.substring(6));
					var ev=d._event||'';
				// Session isolation: this stream belongs to one conversation (streamSid).
				// While the user is viewing a *different* session, real-time events
				// must not write into the visible chat, status bar, todo list or modal
				// dialogs. 'done', 'title', 'error' and 'question' carry their own
				// conversation_id (d.conversation_id) and are session-aware, so they
				// always run to keep the per-session state correct.
				if(streamSid!==aC && ev!=='done' && ev!=='title' && ev!=='error' && ev!=='question' && ev!=='permission-request'){return;}
			if(!firstContent&&(ev==='text-delta'||ev==='tool-call'||ev==='tool-result'||ev==='reasoning-delta')){
					firstContent=true;
					if(thinkBubble){
						var msgEl=thinkBubble.closest('.ocMsg');
						if(msgEl&&msgEl.parentNode)msgEl.parentNode.removeChild(msgEl);
						thinkBubble=null;
					}
					if(_activeThinkBubble){
						var msgEl2=_activeThinkBubble.closest('.ocMsg');
						if(msgEl2&&msgEl2.parentNode)msgEl2.parentNode.removeChild(msgEl2);
						_activeThinkBubble=null;
					}
				}
				if(ev==='reasoning-delta'&&d.text){
						reasonText+=d.text;
						if(!bb){bb=rMsg({role:'asst',content:'',parts:[]})}
						if(!reasonDiv){
							reasonDiv=document.createElement('div');
							reasonDiv.className='ocReason open';
							reasonDiv.innerHTML='<span>Thinking...</span><div class="ocReasonB"></div>';
							reasonDiv.addEventListener('click',function(){this.classList.toggle('open')});
							var rds=bb.querySelectorAll('.ocReason');
							if(rds.length)bb.insertBefore(reasonDiv,rds[rds.length-1].nextSibling);
							else bb.insertBefore(reasonDiv,bb.firstChild);
							updateReasonToggle();
						}
						reasonDiv.querySelector('.ocReasonB').textContent=reasonText;
						document.getElementById('ocChat').scrollTop=1e9;
					}
					if(ev==='text-delta'&&d.text){
						if(reasonDiv){reasonDiv.classList.remove('open');reasonDiv.querySelector('span').textContent='Thoughts'}
						full+=d.text;
						if(!bb){bb=rMsg({role:'asst',content:'',parts:[]})}
						var me=bb.querySelector('.ocMd');
						if(!me){
							me=document.createElement('div');
							me.className='ocMd';
							bb.appendChild(me);
						}
						var rendered=md(full);
						if(rendered){
							me.innerHTML=rendered+'<span class="ocCu"></span>';
						}
						document.getElementById('ocChat').scrollTop=1e9;
					}
					if(ev==='tool-call'){
						var td=document.createElement('div');
						td.className='ocTk';
						var inp2=JSON.stringify(d.input||{});
						var cmdPreview=d.name==='bash'?('$ '+(d.input.command||'')).substring(0,100):inp2.substring(0,100);
						td.innerHTML='<div class="ocTkH" onclick="this.parentElement.classList.toggle(\'open\')">'
							+'<span class="ocTkN">'+esc(d.name||'tool')+'</span>'
							+'<span class="ocTkDt">'+esc(cmdPreview)+'</span>'
							+'<span style="color:var(--ac);font-size:10px;margin-left:auto">running...</span>'
							+'</div><div class="ocTkB">'+esc(JSON.stringify(d.input||{},null,2))+'</div>';
						if(!bb){bb=rMsg({role:'asst',content:'',parts:[]})}
						bb.appendChild(td);
						document.getElementById('ocChat').scrollTop=1e9;
						td._toolId=d.id;
						td._toolName=d.name;
					}
					if(ev==='tool-result'){
						var found=null;
						if(bb){
							var kids=bb.querySelectorAll('.ocTk');
							for(var k=0;k<kids.length;k++){
								if(kids[k]._toolId===d.id){found=kids[k];break}
							}
						}
						// Auto-track modified files
						if(d.name==='write_file'||d.name==='edit_file'||d.name==='replace_in_file'||d.name==='apply_patch'){
							var fp=d.input&&d.input.path?d.input.path:'';
							if(fp)addModifiedFile(fp,d.name==='write_file'?'add':'mod',0);
						}
						if(d.name==='bash'&&d.input&&d.input.command){
							var cmdStr=d.input.command;
							var writeMatch=cmdStr.match(/(?:>|\s>>\s|sed\s|awk\s|perl\s|cp\s|mv\s|rm\s)\s*[`"']?([^\s`"';|&]+)[`"']?/);
							if(writeMatch&&writeMatch[1]!=='/dev/null')addModifiedFile(writeMatch[1],'mod',0);
						}
						if(found){
							var isErr=!(!d.is_error);
							if(isErr)found.classList.add('err');
							var summary=String(d.output||'').substring(0,100);
							found.querySelector('.ocTkDt').textContent=summary;
							var runningSpan=found.querySelector('span[style*="running"]');
							if(runningSpan)runningSpan.textContent=isErr?'error':'done';
							var body=found.querySelector('.ocTkB');
							if(body){
								if(d.diff){
									var diffHtml=renderDiff(d.diff);
									body.innerHTML=diffHtml;
									var diffEl=body.querySelector('.ocDiff');
									if(diffEl)diffEl.setAttribute('data-raw',d.diff);
								}else if(found._toolName==='bash'){
									body.innerHTML='<div class="ocTerm">'+esc(d.output||'')+'</div>';
								}else if(found._toolName==='grep'||found._toolName==='glob'||found._toolName==='list_files'||found._toolName==='search_files'){
									body.innerHTML=_formatGrepOutput(d.output||'');
								}else{
									body.textContent=d.output||'';
								}
							}
						}else{
							var td2=document.createElement('div');
							td2.className='ocTk'+(d.is_error?' err':'');
							var outputHtml='';
							if(d.diff){
								outputHtml=renderDiff(d.diff);
							}else if(d.name==='bash'){
								outputHtml='<div class="ocTerm">'+esc(d.output||'')+'</div>';
							}else{
								outputHtml=esc(d.output||'');
							}
							td2.innerHTML='<div class="ocTkH" onclick="this.parentElement.classList.toggle(\'open\')">'
								+'<span class="ocTkN">'+esc(d.name||'tool')+'</span>'
								+'<span class="ocTkDt">'+esc(String(d.output||'').substring(0,100))+'</span>'
								+'</div><div class="ocTkB">'+outputHtml+'</div>';
							if(!bb){bb=rMsg({role:'asst',content:'',parts:[]})}
							bb.appendChild(td2);
						}
						document.getElementById('ocChat').scrollTop=1e9;
					}
					if(ev==='done'){
					var statusEl=document.getElementById('ocST');
					statusEl.textContent='Ready';
					updateStats(d);
					// A 'done' event ends the stream. The server emits 'done' on
					// success (optionally with auto_title), on a pending question
					// (pending_question) or on an error. Only a successful, fully
					// received response surfaces as "unread" in other views.
					if(d.conversation_id){
						var _doneSid=d.conversation_id;
						// Is the user still viewing the session that just finished?
						var viewingDone=(aC===_doneSid)||(!aC&&!_streamSid);
						// If the session was deleted while finishing, don't switch to it.
						if(_deletedIds && _deletedIds[_doneSid]){viewingDone=false}
						if(viewingDone){
							aC=_doneSid;
							updateURL(aC);
							savePromptHistory();
							clearGenTab(_doneSid);
							clearUnread(_doneSid);
							delete _unreadIds[_doneSid];
							var sDone=_ss[_doneSid];
							if(sDone){sDone.streaming=false;sDone.lockActive=false;sDone.xhr=null}
							// Refresh sidebar after response completes (no full re-render to preserve diffs)
							syncStreamUI();
							loadSL();
							hlS(_doneSid);
							// Auto-generated title from server
							if(d.auto_title){loadSL()}
						}else{
							// AI finished in another session: keep the current view and
							// clear the "thinking" dot. Only a fully-completed response is
							// marked unread (blue + bold title); errors and pending
							// questions are left without a blue dot.
							clearGenTab(_doneSid);
							clearUnread(_doneSid);
							delete _unreadIds[_doneSid];
							// If this done belongs to a deleted session, don't mark it.
							if(!(_deletedIds && _deletedIds[_doneSid])){
								if(!d.error && !d.pending_question){
									markUnread(_doneSid);
									_unreadIds[_doneSid]=true;
								}
							}
							loadSL();
						}
					}else{
						clearGenTab(streamSid||aC);
					}
					// Clear per-session streaming state for the conversation that just finished
					var _finSid=d.conversation_id||streamSid;
					if(_ss[_finSid]){_ss[_finSid].streaming=false;_ss[_finSid].lockActive=false;_ss[_finSid].firstContent=false;_ss[_finSid].reasonDiv=null;_ss[_finSid].reasonText='';_ss[_finSid].bb=null;_ss[_finSid].full='';_ss[_finSid].xhr=null;_ss[_finSid].errorShown=false}
					// Clear the global stream pointer if it belonged to this stream
					if(_streamSid===streamSid){_streamSid=null}
					_activeThinkBubble=null;
					// Sync UI: if the finished stream was the current session, the
					// send button should now be shown; if it was a background session
					// the UI already reflects the current session's state.
					syncStreamUI();
					if(!d.pending_question&&!errorShown){
						_playSound('complete');
						_notifyOnComplete('AI Response Ready','Chat completed');
					}
						// Auto-compaction on context overflow
						if(d.context_tokens&&!d.pending_question){
							var mv=document.getElementById('ocModel').value;
							var modelId=mv.split('/').slice(1).join('/');
							var limit=modelCtx[modelId]||0;
							if(limit>0){
								var pct=d.context_tokens/limit;
								if(pct>0.85){
								var doCompact=function(){
									st('Auto-compacting...',true);
									toast('Context at '+Math.round(pct*100)+'% - auto-compacting...','warn');
									api('compact',{keep_last_n:3},function(e3,r3){
										if(!e3&&r3&&r3.success){rMsgs(r3.messages||[]);if(r3.summary){toast('Nothing to compact','warn')}else{toast('Auto-compacted','ok')}st('Ready',true)}
										else{toast('Auto-compaction failed: '+(e3||(r3&&r3.error)||'Unknown'),'err');st('Ready',true)}
									});
								};
									setTimeout(doCompact,500);
								}
							}
						}
					}
					if(ev==='usage'){
						updateStats(d);
					}
					if(ev==='iteration-start'){
						reasonDiv=null;
						reasonText='';
					}
					if(ev==='question'){
						var qData=d;
						showQuestion(qData).then(function(answer){
							var ansText=answer.type==='custom'?answer.answer:(Array.isArray(answer.answer)?answer.answer.map(function(a){return a.value||a.label||a}).join(', '):(answer.answer.value||answer.answer.label||answer.answer));
							var inp=document.getElementById('ocIn');
							inp.value=ansText;
							origSend();
						});
					}
					if(ev==='permission-request'){
						askPermission(d.tool,d.input).then(function(allowed){
							if(!allowed){
								toast(d.tool+' denied. You can allow it in Settings > Permissions.','warn');
							}
						});
					}
					if(ev==='title'&&d.title){
						var sid=d.conversation_id||aC;
						var si=document.querySelector('#ocSL .ocSi[data-id="'+(window.CSS&&CSS.escape?CSS.escape(sid):sid.replace(/["\\]/g,'\\$&'))+'"]');
						if(si){
							var stEl=si.querySelector('.ocSiT');
							if(stEl)stEl.textContent=d.title;
						}
						loadSL();
					}
					if(ev==='todos'&&d.todos){
						applyServerTodos(d.todos);
					}
				if(ev==='error'&&d.message){
					errorShown=true;
					clearGenTab(streamSid||aC);
					var _errSid=d.conversation_id||streamSid;
					if(_ss[_errSid]){_ss[_errSid].streaming=false;_ss[_errSid].lockActive=false;_ss[_errSid].errorShown=true;_ss[_errSid].xhr=null;}
					_activeThinkBubble=null;
					syncStreamUI(_errSid);
						if(!bb){bb=rMsg({role:'asst',content:'',parts:[]})}
						if(d.message.indexOf('already in progress')!==-1){
							bb.innerHTML='<div style="color:var(--err);padding:6px">'
								+'<div style="margin-bottom:8px">'+esc(d.message)+'</div>'
								+'<button class="ocB ocBP" onclick="forceUnlockAndRetry()" style="font-size:12px;padding:6px 16px">Force Unlock & Retry</button>'
								+'</div>';
						}else{
							bb.innerHTML='<div style="color:var(--err);padding:6px">'+esc(d.message)+'</div>';
						}
						st('Error',false);
						_playSound('error');
						_notifyOnComplete('AI Error',d.message);
					}
				}catch(e){}
			});
		};
	xhr.onload=function(){
		var doneSid=streamSid||aC;
		// Clear per-session streaming state
		if(_ss[doneSid]){
			_ss[doneSid].streaming=false;
			_ss[doneSid].lockActive=false;
			_ss[doneSid].xhr=null;
		}
		syncStreamUI(doneSid);
		cS=null;
		clearGenTab(doneSid);
		if(_streamSid===streamSid){_streamSid=null}
		_activeThinkBubble=null;
		if(thinkBubble){
			var msgEl=thinkBubble.closest('.ocMsg');
			if(msgEl&&msgEl.parentNode)msgEl.parentNode.removeChild(msgEl);
			thinkBubble=null;
		}
		if(bb){var c=bb.querySelector('.ocCu');if(c)c.remove()}
		if(errorShown){
			st('Error',false);
		}else{
			st('Ready',true);
		}
		document.getElementById('ocChat').scrollTop=1e9;
		loadSL();
		if(!firstContent&&!errorShown&&aC){loadC(aC)}
	};
		xhr.onerror=function(){
		var errSid=streamSid||aC;
		// Clear per-session streaming state
		if(_ss[errSid]){
			_ss[errSid].streaming=false;
			_ss[errSid].lockActive=false;
			_ss[errSid].xhr=null;
		}
		syncStreamUI(errSid);
		cS=null;
		clearGenTab(errSid);
		if(_streamSid===streamSid){_streamSid=null}
		_activeThinkBubble=null;
			if(bb)bb.innerHTML='<span style="color:var(--err)">Connection error</span>';
			st('Error',false);
			_notifyOnComplete('AI Error','Connection error');
		};
		xhr.send(params);
	};

	var mv=document.getElementById('ocModel').value;
	if(mv){
		var pp=mv.split('/');
		api('start',{provider:pp[0],model:pp.slice(1).join('/'),mode:document.getElementById('ocMode').value},function(){doSend()});
	}else{
		doSend();
	}
}

	function loadP(cb){
	if(_providersCache){
		renderProviders(_providersCache);
		if(cb)cb(null,_providersCache);
		return;
	}
	if(_providersLoading){
		var pollP=setInterval(function(){
			if(_providersCache){
				clearInterval(pollP);
				renderProviders(_providersCache);
				if(cb)cb(null,_providersCache);
			}
		},50);
		return;
	}
	_providersLoading=true;
	api('providers',null,function(e,d){
		_providersLoading=false;
		if(e||!d)return;
		_providersCache=d;
		renderProviders(d);
		if(cb)cb(e,d);
	});
}
function renderModelSelect(d){
		var ms=document.getElementById('ocModel');
		if(!ms)return;
		ms.innerHTML='<option value="">Select model</option>';

		// Favorites optgroup first
		if(favModels.length){
			var favGrp=document.createElement('optgroup');
			favGrp.label='Favorites';
			favModels.forEach(function(fm){
				var parts=fm.split('/');
				var provId=parts[0];
				var modelId=parts.slice(1).join('/');
				var prov=d.find(function(p){return p.id===provId});
				if(!prov||!prov.connected)return;
				var m=prov.models&&prov.models[modelId];
				if(!m)return;
				var mName=typeof m==='object'?m.name:m;
				var o=document.createElement('option');
				o.value=fm;
				o.textContent=mName;
				favGrp.appendChild(o);
			});
			if(favGrp.children.length)ms.appendChild(favGrp);
		}

		var g={};
		d.forEach(function(p){
			if(!p.connected)return;
			var m=p.models||{};
			for(var k in m){
				var v=typeof m[k]==='object'?m[k].name:m[k];
				if(!g[p.name])g[p.name]=[];
				g[p.name].push({id:k,name:v,provider:p.id});
			}
		});
		for(var gn in g){
			var og=document.createElement('optgroup');
			og.label=gn;
			g[gn].forEach(function(m){
				var o=document.createElement('option');
				o.value=m.provider+'/'+m.id;
				o.textContent=m.name;
				og.appendChild(o);
			});
			ms.appendChild(og);
		}

 		if(selModel){
 			ms.value=selModel;
 			if(ms.value!==selModel){
 				var opts=ms.querySelectorAll('option');
 				for(var i=0;i<opts.length;i++){
 					if(opts[i].value===selModel){ms.selectedIndex=i;break}
 				}
 			}
 		}else if(_statusLoaded){
 		// 1) Prefer the default_model defined by the hosting provider (via filter.php).
		//    The first connected provider that exposes a default_model wins.
		var defaultModel=null;
		for(var i=0;i<d.length;i++){
			if(d[i].connected && d[i].default_model){
				defaultModel=d[i].id+'/'+d[i].default_model;
				break;
			}
		}
		// 2) Fall back to the legacy "free model" selection.
		if(!defaultModel){
			var freeModel=null;
			var opts=ms.querySelectorAll('option');
			for(var i=0;i<opts.length;i++){
				if(opts[i].value==='opencode_zen/minimax-m2.5-free'){
					freeModel=opts[i].value;
					break;
				}
			}
			if(!freeModel){
				for(var i=0;i<opts.length;i++){
					if(opts[i].value.indexOf('opencode_zen/')===0){
						freeModel=opts[i].value;
						break;
					}
				}
			}
			if(freeModel) defaultModel=freeModel;
		}
	if(defaultModel){
		ms.value=defaultModel;
		var pp=defaultModel.split('/');
		selModel=defaultModel;
		var _sp={provider:pp[0],model:pp.slice(1).join('/'),mode:document.getElementById('ocMode').value};
		api('start',_sp,function(){});
	}
	}
}
function renderProviders(d){
		pv=d;
		syncModelCtx(d);
		renderModelSelect(d);
		rPL(d);
		renderModelDropdown(d);
		syncModelBtn();
		updateVariantBadge();
	}

var _heartSvg='<svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>';
var _modelOptionsCache=[];
function renderModelDropdown(d){
	var drop=document.getElementById('ocModelDrop');
	if(!drop)return;
	drop.innerHTML='';
	_modelOptionsCache=[];
	var allOpts=[];
	function addGroup(label,items){
		if(!items.length)return;
		var grp=document.createElement('div');
		grp.className='ocModelGrp';
		grp.textContent=label;
		drop.appendChild(grp);
		items.forEach(function(item){
			allOpts.push(item);
			var row=document.createElement('div');
			row.className='ocModelOpt';
			row.dataset.value=item.value;
			var _favRemove='Remove from favorites';
			var _favAdd='Add to favorites';
			row.innerHTML='<span class="ocModelOptT">'+esc(item.name)+'</span><button class="ocModelFav'+(item.isFav?' isFav':'')+'" data-fav="'+item.value+'" title="'+(item.isFav?_favRemove:_favAdd)+'" type="button">'+_heartSvg+'</button>';
			row.addEventListener('click',function(e){
				if(e.target.closest('.ocModelFav'))return;
				selectModel(item.value);
			});
			drop.appendChild(row);
		});
	}
	// Favorites group first
	var favItems=[];
	favModels.forEach(function(fm){
		var parts=fm.split('/');
		var provId=parts[0];
		var modelId=parts.slice(1).join('/');
		var prov=d.find(function(p){return p.id===provId});
		if(!prov||!prov.connected)return;
		var m=prov.models&&prov.models[modelId];
		if(!m)return;
		var mName=typeof m==='object'?m.name:m;
		favItems.push({value:fm,name:mName,isFav:true});
	});
	if(favItems.length)addGroup('Favorites',favItems);
	// Per-provider groups
	var g={};
	d.forEach(function(p){
		if(!p.connected)return;
		var m=p.models||{};
		for(var k in m){
			var v=typeof m[k]==='object'?m[k].name:m[k];
			if(!g[p.name])g[p.name]=[];
			var val=p.id+'/'+k;
			g[p.name].push({value:val,name:v,isFav:favModels.indexOf(val)!==-1});
		}
	});
	for(var gn in g){addGroup(gn,g[gn])}
	_modelOptionsCache=allOpts;
	// Wire heart clicks
	drop.querySelectorAll('.ocModelFav').forEach(function(btn){
		btn.addEventListener('click',function(e){
			e.stopPropagation();
			var mid=btn.getAttribute('data-fav');
			toggleFavModel(mid);
			// Update UI in-place: toggle this row + any duplicate (favorites group)
			renderModelSelect(d);
			renderModelDropdown(d);
			syncModelBtn();
		});
	});
}
function syncModelBtn(){
	var ms=document.getElementById('ocModel');
	var btnT=document.getElementById('ocModelBtnT');
	if(!btnT)return;
	var v=ms.value;
	var label='Select model';
	if(v){
		var sel=ms.querySelector('option[value="'+cssEsc(v)+'"]');
		if(sel)label=sel.textContent;
		// If the selected model is a favourite, show a red heart before the label
		if(favModels.indexOf(v)!==-1){
			btnT.innerHTML='<span style="color:#EF4444;display:inline-flex;align-items:center;margin-right:4px">'+_heartSvg+'</span><span>'+esc(label)+'</span>';
			return;
		}
	}
	btnT.textContent=label;
}
function selectModel(v){
	var ms=document.getElementById('ocModel');
	selModel=v;
	ms.value=v;
	if(ms.value!==v){
		var opts=ms.querySelectorAll('option');
		for(var i=0;i<opts.length;i++){
			if(opts[i].value===v){ms.selectedIndex=i;break}
		}
	}
	syncModelBtn();
	syncModelDropdownSelection();
	document.getElementById('ocModelDrop').classList.remove('open');
	var p=v.split('/');
	if(v){
		var _sp={provider:p[0],model:p.slice(1).join('/'),mode:document.getElementById('ocMode').value};
		api('start',_sp,function(){});
	}
	updateVariantBadge();
}
function syncModelDropdownSelection(){
	var v=document.getElementById('ocModel').value;
	var drop=document.getElementById('ocModelDrop');
	if(!drop)return;
	drop.querySelectorAll('.ocModelOpt').forEach(function(row){
		if(row.dataset.value===v)row.classList.add('selected');
		else row.classList.remove('selected');
	});
}
function cssEsc(s){return String(s).replace(/"/g,'&quot;').replace(/'/g,'&#39;')}

function rPL(d,query){
	var l=document.getElementById('ocPL');
	l.innerHTML='';
	var sorted=d.slice().sort(function(a,b){
		if(a.connected&&!b.connected)return -1;
		if(!a.connected&&b.connected)return 1;
		return(a.name||'').localeCompare(b.name||'');
	});
	var q=(query||'').toLowerCase();
	sorted.forEach(function(p){
		if(p.is_custom_type)return;
		var div=document.createElement('div');
		div.className='ocPi';
		div.dataset.id=p.id;
		div.dataset.name=(p.name||'').toLowerCase();
		if(q&&div.dataset.name.indexOf(q)===-1){
			div.className='ocPi ocPiHide';
		}
		var modelCount=Object.keys(p.models||{}).length;
		var sub=modelCount?modelCount+' model'+(modelCount>1?'s':''):'No models';
		var badge=p.connected?'<span class="ocPiB">Connected</span>':'<span class="ocPiB ocPiBo">Not set</span>';
		var btn=(p.connected
			?'<button class="ocB ocBs" data-a="dis" data-id="'+p.id+'">Disconnect</button>'
			:'<button class="ocB ocBs" data-a="con" data-id="'+p.id+'">Connect</button>');
		div.innerHTML='<span class="ocPiN">'+p.name+'<span class="ocPiSub">'+sub+'</span></span>'+badge+btn;
		l.appendChild(div);
	});

	var customs=d.filter(function(p){return p.is_custom_type});
	customs.forEach(function(p){
		var modelCount=Object.keys(p.models||{}).length;
		var sub=modelCount?modelCount+' model'+(modelCount>1?'s':''):'No models';
		var badge=p.connected?'<span class="ocPiB">Connected</span>':'<span class="ocPiB ocPiBo">Not set</span>';
		var div=document.createElement('div');
		div.className='ocPi';
		div.dataset.id=p.id;
		div.dataset.name=(p.name||'').toLowerCase();
		div.innerHTML='<span class="ocPiN">'+esc(p.name)+'<span class="ocPiSub">'+esc(sub)+'</span></span>'+badge+
			'<button class="ocB ocBs" data-a="dis" data-id="'+p.id+'">Disconnect</button>';
		l.appendChild(div);
	});

	var custDiv=document.createElement('div');
	custDiv.className='ocPi';
	custDiv.id='ocCustAddRow';
	custDiv.style.cssText='cursor:pointer;border-style:dashed';
	custDiv.innerHTML='<span class="ocPiN" style="color:var(--p)">+ Add Custom Provider<span class="ocPiSub">OpenAI-compatible</span></span>';
	l.appendChild(custDiv);
}

function isCustomType(id){
	return id==='custom'||(id&&id.indexOf('custom:')===0);
}

function toggleCustFields(id){
	var show=isCustomType(id);
	document.getElementById('ocBUG').style.display=show?'block':'none';
	document.getElementById('ocCustNameG').style.display=(id==='custom')?'block':'none';
	document.getElementById('ocCustModelsG').style.display=show?'block':'none';
}

var cP=null;
function openConPopup(providerId){
	cP=providerId||null;
	var sel=document.getElementById('ocProvSel');
	sel.innerHTML='';
	var found=false;
	pv.forEach(function(p){
		if(p.no_key)return;
		if(p.is_custom_type)return;
		var o=document.createElement('option');
		o.value=p.id;
		o.textContent=p.name+(p.connected?' (Connected)':'');
		sel.appendChild(o);
		if(p.id===providerId){sel.value=providerId;found=true}
	});
	var customOpt=document.createElement('option');
	customOpt.value='custom';
	customOpt.textContent='Custom Provider (OpenAI Compatible)';
	sel.appendChild(customOpt);
	if(providerId&&isCustomType(providerId)){
		sel.value='custom';
		if(isCustomType(providerId)){
			var p=pv.find(function(x){return x.id===providerId});
			if(p){
				document.getElementById('ocCustName').value=p.name||'';
				var mArr=[];
				for(var k in (p.models||{})){mArr.push(k+'='+(typeof p.models[k]==='object'?p.models[k].name:p.models[k]));}
				document.getElementById('ocCustModels').value=mArr.join(', ');
			}
			document.getElementById('ocCustNameG').style.display='none';
		}
	}else{
		document.getElementById('ocCustName').value='';
		document.getElementById('ocCustModels').value='';
	}
	if(!found&&providerId)sel.value=providerId;
	document.getElementById('ocAK').value='';
	document.getElementById('ocBU').value='';
	toggleCustFields(providerId);
	if(providerId&&isCustomType(providerId)){
		var p=pv.find(function(x){return x.id===providerId});
		if(p&&p.base_url){
			document.getElementById('ocBU').value=p.base_url;
		}
	}
	if(providerId){
		if(providerId==='custom'){
			document.getElementById('ocConTitle').textContent='Add Custom Provider';
		}else{
			var p=pv.find(function(x){return x.id===providerId});
			if(p&&p.connected){
				document.getElementById('ocConTitle').textContent='Edit '+p.name;
			}else{
				document.getElementById('ocConTitle').textContent='Connect '+(p?p.name:providerId);
			}
		}
	}else{
		document.getElementById('ocConTitle').textContent='Add Connection';
	}
	document.getElementById('ocConMod').classList.add('open');
}
document.getElementById('ocPL').addEventListener('click',function(e){
	var b=e.target.closest('button');
	if(!b)return;
	var a=b.dataset.a,id=b.dataset.id;
	if(a==='con'){
		openConPopup(id);
	}else if(a==='dis'){
		if(confirm('Disconnect?'))api('settings_delete',{provider_id:id},function(){_providersCache=null;loadP()});
	}
});
document.getElementById('ocAddCon').addEventListener('click',function(){
	loadP();
	setTimeout(function(){openConPopup(null)},200);
});
document.getElementById('ocProvSel').addEventListener('change',function(){
	var id=this.value;
	cP=id;
	toggleCustFields(id);
	if(id==='custom'){
		document.getElementById('ocCustNameG').style.display='block';
		document.getElementById('ocConTitle').textContent='Add Custom Provider';
	}else{
		var p=pv.find(function(x){return x.id===id});
		if(p){
			document.getElementById('ocConTitle').textContent=p.connected?'Edit '+p.name:'Connect '+p.name;
		}
	}
});
document.getElementById('ocSav').addEventListener('click',function(){
	var selVal=document.getElementById('ocProvSel').value;
	var pid=selVal;
	var d={};
	if(selVal==='custom'){
		var custName=document.getElementById('ocCustName').value.trim();
		if(!custName){alert('Please enter a provider name');return;}
		pid='custom:'+custName.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
		d.name=custName;
	}else if(cP&&isCustomType(cP)){
		pid=cP;
	}
	var k=document.getElementById('ocAK').value.trim();
	var bu=document.getElementById('ocBU').value.trim();
	var md=document.getElementById('ocCustModels').value.trim();
	if(isCustomType(pid)&&!bu){alert('Please enter a Base URL for the custom provider');return;}
	d.provider_id=pid;
	d.api_key=k;
	if(bu)d.base_url=bu;
	if(md&&isCustomType(pid)){
		var models={};
		md.split(',').forEach(function(m){
			var parts=m.trim().split('=');
			if(parts.length===2){
				models[parts[0].trim()]=parts[1].trim();
			}else if(parts.length===1&&parts[0].trim()){
				models[parts[0].trim()]=parts[0].trim();
			}
		});
		d.models=JSON.stringify(models);
	}
	api('settings_save',d,function(e,r){
		if(!e&&r&&r.success){
			document.getElementById('ocConMod').classList.remove('open');
			cP=null;
			_providersCache=null;
			loadP();
		}else alert(e||(r&&r.error)||'Failed');
	});
});
document.getElementById('ocTst').addEventListener('click',function(){
	var selVal=document.getElementById('ocProvSel').value;
	var pid=cP||selVal;
	if(!pid)return;
	var td={provider_id:pid,api_key:document.getElementById('ocAK').value.trim()};
	if(isCustomType(pid)){
		var bu=document.getElementById('ocBU').value.trim();
		if(bu)td.base_url=bu;
		var testPid=pid;
		if(selVal==='custom'){
			var custName=document.getElementById('ocCustName').value.trim();
			if(custName)testPid='custom:'+custName.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
			else testPid='custom:test';
		}
		td.provider_id=testPid;
		var md=document.getElementById('ocCustModels').value.trim();
		if(md){
			var models={};
			md.split(',').forEach(function(m){
				var parts=m.trim().split('=');
				if(parts.length===2){
					models[parts[0].trim()]=parts[1].trim();
				}else if(parts.length===1&&parts[0].trim()){
					models[parts[0].trim()]=parts[0].trim();
				}
			});
			td.models=JSON.stringify(models);
			var firstModel=Object.keys(models)[0];
			if(firstModel)td.model=firstModel;
		}
	}
	st('Testing...',true);
	api('test_connection',td,function(e,r){
		alert(!e&&r&&r.success?'OK':'Failed: '+(e||(r&&r.error)||''));
		st('Ready',true);
	});
});
function closeConPopup(){
	document.getElementById('ocConMod').classList.remove('open');
	cP=null;
}
document.getElementById('ocCan2').addEventListener('click',closeConPopup);
document.getElementById('ocConC').addEventListener('click',closeConPopup);
document.getElementById('ocConMod').addEventListener('click',function(e){
	if(e.target===this)closeConPopup();
});

document.getElementById('ocSetBtnF').addEventListener('click',function(){
	document.getElementById('ocSetMod').classList.add('open');
	loadP();
});
document.getElementById('ocSetC').addEventListener('click',function(){
	document.getElementById('ocSetMod').classList.remove('open');
});
document.getElementById('ocSetMod').addEventListener('click',function(e){
	if(e.target===this)this.classList.remove('open');
});
document.getElementById('ocSetSearch').addEventListener('input',function(){
	if(!pv||!pv.length){
		loadP();
		return;
	}
	rPL(pv,this.value);
});
document.addEventListener('click',function(e){
	var row=e.target.closest('#ocCustAddRow');
	if(row){
		openConPopup('custom');
		return;
	}
	var disBtn=e.target.closest('#ocPL button[data-a="dis"]');
	if(disBtn&&disBtn.dataset.id&&disBtn.dataset.id.indexOf('custom:')===0){
		if(confirm('Disconnect this custom provider?')){
			api('settings_delete',{provider_id:disBtn.dataset.id},function(){_providersCache=null;loadP()});
		}
		return;
	}
});
document.getElementById('ocCtxMod').addEventListener('click',function(e){
	if(e.target===this)this.classList.remove('open');
});
document.getElementById('ocCtxC').addEventListener('click',function(){
	document.getElementById('ocCtxMod').classList.remove('open');
});

function showCtxDetail(){
	var bd=document.getElementById('ocCtxBd');
	var u=lastStats.usage||{};
	var inT=u.input_tokens||0;
	var outT=u.output_tokens||0;
	var cachedT=u.cached_tokens||0;
	var ctxT=lastStats.context_tokens||0;
	var tools=lastStats.tool_calls||0;
	var iters=lastStats.iterations||0;
	var cost=lastStats.cost||0;
	if(!cachedT&&rMsgs._lastMsgs){
		rMsgs._lastMsgs.forEach(function(m){
			if(m.role==='assistant'&&m.usage){
				cachedT+=(m.usage.cached_tokens||0)+(m.usage.cache_read_input_tokens||0)+(m.usage.cache_creation_input_tokens||0);
				if(m.usage.prompt_tokens_details&&m.usage.prompt_tokens_details.cached_tokens)cachedT+=m.usage.prompt_tokens_details.cached_tokens;
				if(!inT)inT+=(m.usage.prompt_tokens||0)+(m.usage.input_tokens||0);
			}
		});
	}
	var mv=document.getElementById('ocModel').value;
	var isFreeModel=mv&&mv.indexOf('opencode_zen/')===0;
	var msgs=0;
	var ch=document.getElementById('ocCI');
	ch.querySelectorAll('.ocMsg').forEach(function(){msgs++});
	var html='';
	html+='<div class="ocCtxR"><span class="ocCtxL">Model</span><span class="ocCtxV">'+(document.getElementById('ocModel').selectedOptions[0]?document.getElementById('ocModel').selectedOptions[0].textContent:'-')+'</span></div>';
	html+='<div class="ocCtxR"><span class="ocCtxL">Provider</span><span class="ocCtxV">'+(selModel?selModel.split('/')[0]:'-')+'</span></div>';
	html+='<div class="ocCtxR"><span class="ocCtxL">Mode</span><span class="ocCtxV">'+document.getElementById('ocMode').value+'</span></div>';
	html+='<div class="ocCtxR"><span class="ocCtxL">Messages in view</span><span class="ocCtxV">'+msgs+'</span></div>';
	html+='<div class="ocCtxS"><div class="ocCtxSt">Tokens</div>';
	html+='<div class="ocCtxBr"><span>Context size</span><span>'+fmtTok(ctxT)+'</span></div>';
	html+='<div class="ocCtxBr"><span>Input tokens</span><span>'+fmtTok(inT)+'</span></div>';
	html+='<div class="ocCtxBr"><span>Cached tokens</span><span>'+(cachedT>0?fmtTok(cachedT)+(inT>0?' ('+Math.round(cachedT/inT*100)+'%)':''):'0')+'</span></div>';
	html+='<div class="ocCtxBr"><span>Output tokens</span><span>'+fmtTok(outT)+'</span></div>';
	html+='<div class="ocCtxBr"><span>Total tokens</span><span>'+fmtTok(inT+outT)+'</span></div>';
	html+='</div>';
	html+='<div class="ocCtxS"><div class="ocCtxSt">Session</div>';
	html+='<div class="ocCtxBr"><span>Iterations</span><span>'+iters+'</span></div>';
	html+='<div class="ocCtxBr"><span>Tool calls</span><span>'+tools+'</span></div>';
	if(!isFreeModel&&cost>0){
		html+='<div class="ocCtxBr"><span>Cost</span><span>$'+(cost<0.01&&cost>0?cost.toFixed(6):cost.toFixed(4))+'</span></div>';
	}
	html+='</div>';
	bd.innerHTML=html;
	document.getElementById('ocCtxMod').classList.add('open');
}

document.getElementById('ocStat').addEventListener('click',function(e){
	e.stopPropagation();
	showCtxDetail();
});
document.getElementById('ocAttBtn').addEventListener('click',function(){
	document.getElementById('ocFile').click();
});
document.getElementById('ocFile').addEventListener('change',function(){
	var files=this.files;
	if(!files.length)return;
	var loaded=0;
	var total=files.length;
	Array.from(files).forEach(function(f){
		if(f.size>1048576){
			loaded++;
			pendingFiles.push({name:f.name,content:'[File too large: '+f.size+' bytes]',size:f.size});
			if(loaded===total){
				var names=pendingFiles.map(function(x){return x.name}).join(', ');
				var inp=document.getElementById('ocIn');
				inp.value+=(inp.value?'\n':'')+'[Attached: '+names+']';
				inp.dispatchEvent(new Event('input'));
			}
			return;
		}
		var reader=new FileReader();
		reader.onload=function(e){
			pendingFiles.push({name:f.name,content:e.target.result,size:f.size});
			loaded++;
			if(loaded===total){
				var names=pendingFiles.map(function(x){return x.name}).join(', ');
				var inp=document.getElementById('ocIn');
				inp.value+=(inp.value?'\n':'')+'[Attached: '+names+']';
				inp.dispatchEvent(new Event('input'));
			}
		};
		reader.readAsText(f);
	});
});
document.getElementById('ocSend').addEventListener('click',function(){send()});
document.getElementById('ocStopBtn').addEventListener('click',function(){
	// Stop the stream of the currently-viewed session (per-session).
	if(aC&&_ss[aC]&&_ss[aC].xhr){_ss[aC].xhr.abort();_ss[aC].xhr=null}
	if(cS){cS.abort();cS=null}
	if(aC&&_ss[aC]){_ss[aC].streaming=false;_ss[aC].lockActive=false}
	syncStreamUI();
	st('Stopped',false);
	clearGenTab(aC);
	if(_streamSid===aC){_streamSid=null}
	_activeThinkBubble=null;
	api('abort',{},function(){});
});

var slashCmds=[
	{cmd:'/plan',desc:'Switch to Plan mode (read-only)'},
	{cmd:'/build',desc:'Switch to Build mode (read+write)'},
	{cmd:'/clear',desc:'Clear current conversation'},
	{cmd:'/export',desc:'Download conversation as Markdown'},
	{cmd:'/copy',desc:'Copy transcript to clipboard'},
	{cmd:'/compact',desc:'Summarize conversation to reduce context'},
	{cmd:'/undo',desc:'Undo last message pair'},
	{cmd:'/redo',desc:'Redo last undone message'},
	{cmd:'/fork',desc:'Fork conversation from last message'},
	{cmd:'/rename',desc:'Rename this session'},
	{cmd:'/todo',desc:'Add a task item (e.g. /todo Fix login bug)'},
	{cmd:'/sound',desc:'Toggle sound notifications on/off'},
	{cmd:'/notify',desc:'Toggle desktop notifications'},
	{cmd:'/keybindings',desc:'Customize keyboard shortcuts'},
	{cmd:'/timeline',desc:'Toggle message timeline navigation'},
	{cmd:'/help',desc:'Show keyboard shortcuts and commands'}
];

document.getElementById('ocIn').addEventListener('input',function(){
	autoResize(this);
	var v=this.value;
	var hint=document.getElementById('ocSlash');
	if(v.charAt(0)==='/'&&v.indexOf(' ')===-1){
		var q=v.toLowerCase();
		var matches=slashCmds.filter(function(c){return c.cmd.indexOf(q)===0});
		if(matches.length){
			hint.innerHTML=matches.map(function(c){return '<div class="ocSHItem" data-cmd="'+c.cmd+'"><span class="ocSHCmd">'+c.cmd+'</span><span class="ocSHDesc">'+c.desc+'</span></div>'}).join('');
			hint.classList.add('open');
			slashHintIdx=-1;
			hint.querySelectorAll('.ocSHItem').forEach(function(el){
				el.addEventListener('click',function(){
					var inp=document.getElementById('ocIn');
					var cmd=this.dataset.cmd;
					hint.classList.remove('open');
					slashHintIdx=-1;
					if(cmd==='/todo'){
						inp.value=cmd+' ';
						inp.focus();
					}else{
						inp.value=cmd;
						inp.focus();
						send();
					}
				});
			});
		}else{hint.classList.remove('open');slashHintIdx=-1}
	}else{hint.classList.remove('open');slashHintIdx=-1}
});
var promptHistory=[];
var promptHistoryIdx=-1;
var promptHistorySaved='';
var slashHintIdx=-1;
var _allPromptHistory={};
(function(){try{var s=localStorage.getItem('oc_prompt_history');if(s){var parsed=JSON.parse(s);if(Array.isArray(parsed)){var old=localStorage.getItem('oc_prompt_history_migrated');if(!old){_allPromptHistory={'__legacy__':parsed};localStorage.setItem('oc_prompt_history',JSON.stringify(_allPromptHistory));localStorage.setItem('oc_prompt_history_migrated','1')}else{_allPromptHistory=parsed}}else{_allPromptHistory=parsed}}}catch(e){}})();
function loadPromptHistoryForSession(){var k=aC||'__legacy__';promptHistory=(_allPromptHistory[k]||[]).slice();promptHistoryIdx=-1;promptHistorySaved=''}
function savePromptHistory(){try{var k=aC||'__legacy__';_allPromptHistory[k]=promptHistory;localStorage.setItem('oc_prompt_history',JSON.stringify(_allPromptHistory))}catch(e){}}
function curSessionTitle(){for(var i=0;i<_sessions.length;i++){if(_sessions[i].id===aC)return _sessions[i].title||'Untitled'}return _origTitle.replace(' - Code with AI','')}

function historyUp(inp){
	if(promptHistory.length===0)return false
	if(promptHistoryIdx===-1){
		promptHistorySaved=inp.value;
		promptHistoryIdx=0;
	}else if(promptHistoryIdx<promptHistory.length-1){
		promptHistoryIdx++;
	}
	inp.value=promptHistory[promptHistory.length-1-promptHistoryIdx];
	autoResize(inp);
	return true;
}

function historyDown(inp){
	if(promptHistoryIdx<=0){
		if(promptHistoryIdx===0){
			promptHistoryIdx=-1;
			inp.value=promptHistorySaved;
			promptHistorySaved='';
			autoResize(inp);
			return true;
		}
		return false;
	}
	promptHistoryIdx--;
	inp.value=promptHistory[promptHistory.length-1-promptHistoryIdx];
	autoResize(inp);
	return true;
}

function highlightSlashItem(hint,items,idx){
	items.forEach(function(el){el.classList.remove('act')});
	if(idx>=0&&idx<items.length){
		items[idx].classList.add('act');
		items[idx].scrollIntoView({block:'nearest'});
	}
}

document.getElementById('ocIn').addEventListener('keydown',function(e){
	var hint=document.getElementById('ocSlash');
	if(hint.classList.contains('open')){
		var items=hint.querySelectorAll('.ocSHItem');
		if(e.key==='ArrowUp'&&!e.ctrlKey&&!e.altKey&&!e.metaKey){
			e.preventDefault();
			if(items.length){
				slashHintIdx=(slashHintIdx<=0)?items.length-1:slashHintIdx-1;
				highlightSlashItem(hint,items,slashHintIdx);
			}
			return;
		}
		if(e.key==='ArrowDown'&&!e.ctrlKey&&!e.altKey&&!e.metaKey){
			e.preventDefault();
			if(items.length){
				slashHintIdx=(slashHintIdx>=items.length-1)?0:slashHintIdx+1;
				highlightSlashItem(hint,items,slashHintIdx);
			}
			return;
		}
		if(e.key==='Enter'&&!e.shiftKey){
			e.preventDefault();
			if(slashHintIdx>=0&&slashHintIdx<items.length){
				items[slashHintIdx].click();
			}else if(items.length===1){
				items[0].click();
			}else{
				hint.classList.remove('open');
				slashHintIdx=-1;
				send();
			}
			return;
		}
		if(e.key==='Escape'){
			hint.classList.remove('open');
			slashHintIdx=-1;
			return;
		}
	}
	if(e.key==='Enter'&&!e.shiftKey){
		var hint2=document.getElementById('ocSlash');
		if(hint2.classList.contains('open')){
			var first=hint2.querySelector('.ocSHItem');
			if(first){
				var typed=this.value.trim().toLowerCase();
				var hintCmd=first.dataset.cmd;
				if(typed===hintCmd){
					hint2.classList.remove('open');
					slashHintIdx=-1;
					e.preventDefault();send();
					return;
				}
				e.preventDefault();first.click();return;
			}
		}
		e.preventDefault();send();
		return;
	}
	if(e.key==='ArrowUp'&&!e.ctrlKey&&!e.altKey&&!e.metaKey){
		var curPos=this.selectionStart;
		var val=this.value;
		var lineStart=val.lastIndexOf('\n',curPos-1)+1;
		if(promptHistoryIdx>=0||val===''||curPos===0||curPos===lineStart){
			if(promptHistory.length>0){
				e.preventDefault();
				historyUp(this);
			}
			return;
		}
	}
	if(e.key==='ArrowDown'&&!e.ctrlKey&&!e.altKey&&!e.metaKey){
		var curPos2=this.selectionStart;
		var val2=this.value;
		var atEnd=curPos2>=val2.length;
		if(promptHistoryIdx>=0&&(val2===''||atEnd)){
			e.preventDefault();
			historyDown(this);
			return;
		}
	}
	if(e.key==='ArrowUp'&&e.ctrlKey){
		e.preventDefault();
		historyUp(this);
		return;
	}
	if(e.key==='ArrowDown'&&e.ctrlKey){
		e.preventDefault();
		historyDown(this);
		return;
	}
});

var _diffViewMode='unified';
function renderDiff(diffText){
	if(!diffText)return '';
	var lines=diffText.split('\n');
	var hunks=[];
	var curHunk=null;
	var lineNum=0;
	lines.forEach(function(l){
		lineNum++;
		var cls='ctx';
		if(l.charAt(0)==='+')cls='add';
		else if(l.charAt(0)==='-')cls='del';
		if(l.match(/^@@/)){
			if(curHunk)hunks.push(curHunk);
			curHunk={header:l,lines:[{text:l,cls:'ctx',num:lineNum}]};
		}else if(curHunk){
			curHunk.lines.push({text:l,cls:cls,num:lineNum});
		}else{
			if(!curHunk)curHunk={header:'',lines:[]};
			curHunk.lines.push({text:l,cls:cls,num:lineNum});
		}
	});
	if(curHunk)hunks.push(curHunk);
	var hunkNav='<span class="ocPatchNav"><button onclick="window._diffPrev(this)" title="Previous hunk">\u25B2</button><span class="ocPatchInfo">Hunk 1/'+hunks.length+'</span><button onclick="window._diffNext(this)" title="Next hunk">\u25BC</button></span>';
	var toggleBtn='<span class="ocDiffTB"><button class="'+(_diffViewMode==='unified'?'act':'')+'" onclick="window._diffToggle(this,\'unified\')">Unified</button><button class="'+(_diffViewMode==='split'?'act':'')+'" onclick="window._diffToggle(this,\'split\')">Split</button></span>';
	var html='<div class="ocDiff" data-hunks="'+hunks.length+'" data-hunk="0">'+(_diffViewMode==='split'?_renderDiffSplit(lines):_renderDiffUnified(lines))+'</div>';
	return html;
}
function _renderDiffUnified(lines){
	var html='<div class="ocDiffH"><span>Diff</span>'+_diffControls()+'</div>';
	var lineNum=0;
	lines.forEach(function(l){
		lineNum++;
		var cls='ctx';
		if(l.charAt(0)==='+')cls='add';
		else if(l.charAt(0)==='-')cls='del';
		html+='<div class="ocDiffL '+cls+'" data-ln="'+lineNum+'">'+esc(l)+'</div>';
	});
	return html;
}
function _renderDiffSplit(lines){
	var leftLines=[],rightLines=[],leftNum=0,rightNum=0;
	lines.forEach(function(l){
		if(l.match(/^@@/)){
			leftLines.push({text:l,cls:'ctx'});
			rightLines.push({text:l,cls:'ctx'});
			return;
		}
		if(l.charAt(0)==='-'){
			leftNum++;
			leftLines.push({text:l,cls:'del',num:leftNum});
		}else if(l.charAt(0)==='+'){
			rightNum++;
			rightLines.push({text:l,cls:'add',num:rightNum});
		}else{
			leftNum++;rightNum++;
			leftLines.push({text:l,cls:'ctx',num:leftNum});
			rightLines.push({text:l,cls:'ctx',num:rightNum});
		}
	});
	var html='<div class="ocDiffH"><span>Diff</span>'+_diffControls()+'</div><div class="ocDiff split"><div class="ocDiffSide">';
	leftLines.forEach(function(l){
		html+='<div class="ocDiffL '+l.cls+'">'+(l.num!==undefined?'<span class="ocDiffLNum">'+l.num+'</span>':'')+esc(l.text)+'</div>';
	});
	html+='</div><div class="ocDiffSide">';
	rightLines.forEach(function(l){
		html+='<div class="ocDiffL '+l.cls+'">'+(l.num!==undefined?'<span class="ocDiffLNum">'+l.num+'</span>':'')+esc(l.text)+'</div>';
	});
	html+='</div></div>';
	return html;
}
function _diffControls(){
	return '<span class="ocDiffTB"><button onclick="window._diffToggle(this,\'unified\')" title="Unified view">Unified</button><button onclick="window._diffToggle(this,\'split\')" title="Split view">Split</button><button onclick="window._diffPrev(this)" title="Previous hunk">\u25B2</button><button onclick="window._diffNext(this)" title="Next hunk">\u25BC</button></span>';
}
window._diffToggle=function(btn,mode){
	_diffViewMode=mode;
	var diffEl=btn.closest('.ocDiff');
	if(!diffEl)return;
	var raw=diffEl.getAttribute('data-raw');
	if(!raw)return;
	diffEl.outerHTML=renderDiff(raw);
};
window._diffPrev=function(btn){
	var diffEl=btn.closest('.ocDiff');
	if(!diffEl)return;
	var hunks=diffEl.querySelectorAll('.ocDiffL.ctx[data-ln]');
	if(hunks.length<2)return;
	var cur=parseInt(diffEl.getAttribute('data-hunk')||'0');
	if(cur<=0)return;
	cur--;
	diffEl.setAttribute('data-hunk',cur);
	hunks[cur].scrollIntoView({behavior:'smooth',block:'center'});
	_updatePatchInfo(diffEl,cur,hunks.length);
};
window._diffNext=function(btn){
	var diffEl=btn.closest('.ocDiff');
	if(!diffEl)return;
	var hunks=diffEl.querySelectorAll('.ocDiffL.ctx[data-ln]');
	if(hunks.length<2)return;
	var cur=parseInt(diffEl.getAttribute('data-hunk')||'0');
	if(cur>=hunks.length-1)return;
	cur++;
	diffEl.setAttribute('data-hunk',cur);
	hunks[cur].scrollIntoView({behavior:'smooth',block:'center'});
	_updatePatchInfo(diffEl,cur,hunks.length);
};
function _updatePatchInfo(diffEl,cur,total){
	var info=diffEl.querySelector('.ocPatchInfo');
	if(info)info.textContent='Hunk '+(cur+1)+'/'+total;
}

function _formatGrepOutput(output){
	if(!output)return '';
	var lines=output.split('\n');
	var files={};
	var currentFile=null;
	lines.forEach(function(line){
		var m=line.match(/^([^:]+):(\d+)(?::(.*))?$/);
		if(m){
			currentFile=m[1];
			if(!files[currentFile])files[currentFile]=[];
			files[currentFile].push({line:parseInt(m[2]),text:m[3]||''});
		}else if(currentFile&&line.trim()){
			files[currentFile][files[currentFile].length-1].text+='\n'+line;
		}
	});
	if(Object.keys(files).length===0)return esc(output);
	var html='';
	for(var f in files){
		html+='<div class="ocDiffGrep"><div class="ocDiffGrepH"><span>\uD83D\uDCC2</span> <a href="#" class="ocGrepPath" onclick="window._openFile(\''+esc(f).replace(/'/g,"\\'")+'\');return false">'+esc(f)+'</a> <span style="opacity:.5">('+files[f].length+' match'+(files[f].length>1?'es':'')+')</span></div>';
		files[f].forEach(function(m){
			html+='<div class="ocDiffGrepL"><span class="ocDiffGrepLN">'+m.line+'</span>'+esc(m.text)+'</div>';
		});
		html+='</div>';
	}
	return html;
}

window._openFile=function(path){
	var inp=document.getElementById('ocIn');
	inp.value='@'+path;
	inp.dispatchEvent(new Event('input',{bubbles:true}));
	inp.focus();
};

	function exportChat(opts){
		opts=opts||{};
		var includeThinking=opts.thinking!==false;
		var includeTools=opts.tools!==false;
		var msgs=document.querySelectorAll('#ocCI .ocMsg');
		var mdText='';
		msgs.forEach(function(el){
			var isUser=el.classList.contains('u');
			mdText+=(isUser?'## User\n\n':'## Assistant\n\n');
			var textEl=el.querySelector('.ocMd');
			if(textEl){
				mdText+=textEl.innerText||'';
			}else if(isUser){
				var userEl=el.querySelector('.ocMbb');
				if(userEl)mdText+=userEl.innerText||'';
			}else{
				var thinkEl=el.querySelector('.ocThinkT');
				if(thinkEl)mdText+='*'+thinkEl.textContent+'*';
			}
		// Include reasoning blocks
		if(includeThinking){
			var reason=el.querySelector('.ocReason');
			if(reason){
				var reasonB=reason.querySelector('.ocReasonB');
				if(reasonB)mdText+='\n\n> **Reasoning:**\n> '+(reasonB.textContent||'').replace(/\n/g,'\n> ')+'\n';
			}
		}
		// Include tool calls
		if(includeTools){
			var tools=el.querySelectorAll('.ocTk');
			tools.forEach(function(t){
				var name=t.querySelector('.ocTkN');
				var body=t.querySelector('.ocTkB');
				mdText+='\n\n**Tool: '+(name?name.textContent:'unknown')+'**\n```\n'+(body?body.innerText:'')+'\n```\n';
			});
		}
		mdText+='\n\n---\n\n';
	});
	var blob=new Blob([mdText],{type:'text/markdown'});
	var url=URL.createObjectURL(blob);
	var a=document.createElement('a');
	a.href=url;a.download=(opts.filename||'chat-'+new Date().toISOString().slice(0,10))+'.md';
	document.body.appendChild(a);a.click();document.body.removeChild(a);
	URL.revokeObjectURL(url);
}

	function copyTranscript(){
		var msgs=document.querySelectorAll('#ocCI .ocMsg');
		var md='';
		msgs.forEach(function(el){
			var isUser=el.classList.contains('u');
			md+=(isUser?'**User:** ':'**Assistant:** ');
			var textEl=el.querySelector('.ocMd');
			if(textEl){
				md+=textEl.innerText||'';
			}else if(isUser){
				var userEl=el.querySelector('.ocMbb');
				if(userEl)md+=userEl.innerText||'';
			}
			md+='\n\n';
		});
		navigator.clipboard.writeText(md).then(function(){toast('Copied to clipboard','ok')},function(){toast('Copy failed','err')});
	}

function showForkModal(){
	var msgs=rMsgs._lastMsgs||[];
	var userMsgs=[];
	var userIdx=0;
	for(var i=0;i<msgs.length;i++){
		if(msgs[i].role==='user'&&msgs[i].id){
			userIdx++;
			userMsgs.push({
				id:msgs[i].id,
				content:(msgs[i].content||'').replace(/\n/g,' ').substring(0,200),
				time:msgs[i].time?new Date(msgs[i].time*1000).toLocaleString([],{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'}):'',
				index:userIdx
			});
		}
	}
	if(!userMsgs.length){toast('No user messages to fork from','warn');return}

	var mod=document.getElementById('ocForkMod');
	var list=document.getElementById('ocForkList');
	var search=document.getElementById('ocForkSearch');

	function renderForkList(filter){
		filter=(filter||'').toLowerCase();
		var html='';
		var filtered=userMsgs.filter(function(m){
			return !filter||m.content.toLowerCase().indexOf(filter)>-1;
		});
		// Add "Full session" option at top
		html+='<div class="ocForkItem" data-fork-all="1"><span class="ocForkItemNum">All</span><div style="flex:1"><div class="ocForkItemText" style="font-weight:500;color:var(--p)">Fork entire session</div><div class="ocForkItemTime">Creates a copy of the full conversation</div></div></div>';
		// Add user messages in reverse (latest first)
		for(var i=filtered.length-1;i>=0;i--){
			var m=filtered[i];
			html+='<div class="ocForkItem" data-fork-id="'+esc(m.id)+'"><span class="ocForkItemNum">#'+m.index+'</span><div style="flex:1;min-width:0"><div class="ocForkItemText">'+esc(m.content)+'</div><div class="ocForkItemTime">'+esc(m.time)+'</div></div></div>';
		}
		if(filtered.length===0){
			html='<div style="padding:20px;text-align:center;color:var(--t3);font-size:13px">No messages match your search</div>';
		}
		list.innerHTML=html;
		list.querySelectorAll('.ocForkItem').forEach(function(el){
			el.addEventListener('click',function(){
				var forkId=this.dataset.forkId||'';
				var forkAll=this.dataset.forkAll==='1';
				document.getElementById('ocForkMod').classList.remove('open');
				if(forkAll){
					// Fork from last user message (full session)
					var lastId=userMsgs[userMsgs.length-1].id;
					doFork(lastId);
				}else if(forkId){
					doFork(forkId);
				}
			});
			el.addEventListener('mouseenter',function(){
				list.querySelectorAll('.ocForkItem').forEach(function(e){e.classList.remove('act')});
				this.classList.add('act');
			});
		});
	}

	function doFork(msgId){
		st('Forking...',true);
		api('fork',{message_id:msgId},function(e,r){
			if(!e&&r&&r.success){
				aC=r.conversation_id;
				updateURL(aC);
				toast('Forked to new session: '+(r.title||'Untitled'),'ok');
				st('Ready',true);
				loadSL();
				loadC(aC);
				if(r.forked_content){
					var inp=document.getElementById('ocIn');
					inp.value=r.forked_content;
					inp.focus();
				}
			}else{
				toast('Failed to fork: '+(e||(r&&r.error)||'Unknown error'),'err');
				st('Ready',true);
			}
		});
	}

	renderForkList('');
	search.value='';
	mod.classList.add('open');
	setTimeout(function(){search.focus()},50);

	// Wire up search
	search.oninput=function(){renderForkList(this.value)};

	// Close handlers
	document.getElementById('ocForkC').onclick=function(){mod.classList.remove('open')};
	mod.onclick=function(e){if(e.target===mod)mod.classList.remove('open')};
}

function showExportDialog(){
	var m=document.getElementById('ocDiffMod')||null;
	// Create export options modal
	var mod=document.createElement('div');
	mod.className='ocMo open';
	mod.id='ocExportMod';
	mod.innerHTML='<div class="ocMoB" style="width:440px"><div class="ocMoH"><h3>Export Options</h3><button class="ocMoC" id="ocExpC">✕</button></div><div class="ocMoBd">'
		+'<div style="margin-bottom:12px"><label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer"><input type="checkbox" id="ocExpThinking" checked> Include reasoning/thinking blocks</label></div>'
		+'<div style="margin-bottom:12px"><label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer"><input type="checkbox" id="ocExpTools" checked> Include tool call details</label></div>'
		+'<div style="margin-bottom:16px"><label style="display:block;font-size:13px;margin-bottom:6px">Filename</label><input type="text" class="ocFi" id="ocExpFname" value="chat-'+new Date().toISOString().slice(0,10)+'" style="width:100%"></div>'
		+'<div style="display:flex;gap:8px;justify-content:flex-end">'
		+'<button class="ocB ocBP" id="ocExpDownload">Download Markdown</button>'
		+'<button class="ocB ocBP" id="ocExpCopy">Copy to Clipboard</button>'
		+'</div></div></div>';
	document.body.appendChild(mod);
	document.getElementById('ocExpC').addEventListener('click',function(){document.body.removeChild(mod)});
	mod.addEventListener('click',function(e){if(e.target===mod)document.body.removeChild(mod)});
	document.getElementById('ocExpDownload').addEventListener('click',function(){
		exportChat({thinking:document.getElementById('ocExpThinking').checked,tools:document.getElementById('ocExpTools').checked,filename:document.getElementById('ocExpFname').value});
		document.body.removeChild(mod);
		toast('Exported as Markdown','ok');
	});
	document.getElementById('ocExpCopy').addEventListener('click',function(){
		var opts={thinking:document.getElementById('ocExpThinking').checked,tools:document.getElementById('ocExpTools').checked};
		var msgs=document.querySelectorAll('#ocCI .ocMsg');
		var md='';
		msgs.forEach(function(el){
			var isUser=el.classList.contains('u');
			md+=(isUser?'## User\n\n':'## Assistant\n\n');
			var textEl=el.querySelector('.ocMd');
			if(textEl){
				md+=textEl.innerText||'';
			}else if(isUser){
				var userEl=el.querySelector('.ocMbb');
				if(userEl)md+=userEl.innerText||'';
			}
			if(opts.thinking){
				var r=el.querySelector('.ocReasonB');
				if(r)md+='\n\n> **Reasoning:** '+r.textContent+'\n';
			}
			if(opts.tools){
				el.querySelectorAll('.ocTk').forEach(function(t){
					md+='\n\n**Tool: '+(t.querySelector('.ocTkN')?t.querySelector('.ocTkN').textContent:'')+'**\n```\n'+(t.querySelector('.ocTkB')?t.querySelector('.ocTkB').innerText:'')+'\n```\n';
				});
			}
			md+='\n\n---\n\n';
		});
		navigator.clipboard.writeText(md).then(function(){
			toast('Transcript copied to clipboard','ok');
		},function(){
			toast('Failed to copy','err');
		});
		document.body.removeChild(mod);
	});
}

modelCtx={
	'gpt-4o':128000,'gpt-4o-mini':128000,'gpt-4-turbo':128000,
	'claude-sonnet-4-20250514':200000,'claude-opus-4-20250514':200000,'claude-3-5-sonnet-20241022':200000,'claude-3-5-haiku-20241022':200000,
	'gemini-2.5-pro':1048576,'gemini-2.5-flash':1048576,'gemini-1.5-pro':2097152,
	'deepseek-chat':64000,'deepseek-coder':64000,
	'llama-3.3-70b-versatile':128000,'llama-3.1-8b-instant':128000,
	'MiniMax-Text-01':1000000,'MiniMax-M1':1000000,
	'big-pickle':32000,'minimax-m2.5-free':32000,'nemotron-3-super-free':32000,'trinity-large-preview-free':32000,
	'claude-sonnet-4':200000,'claude-opus-4-7':200000,'gpt-5.4':128000,'gpt-5.4-nano':128000,
	'gemini-3-flash':1048576,'gemini-3.1-pro':1048576,'glm-5':128000,'minimax-m2.7':1000000,'kimi-k2.6':128000,'qwen3.6-plus':128000
};
function syncModelCtx(providers){
	providers.forEach(function(p){
		if(!p.models)return;
		for(var mid in p.models){
			if(!modelCtx[mid]){
				var m=p.models[mid];
				modelCtx[mid]=(typeof m==='object'&&m.context_window)?m.context_window:128000;
			}
		}
	});
}

function getKeybinding(action){return _keybindings[action]||_defaultKeybindings[action]||''}
function matchKey(e,combo){if(!combo)return false;var parts=combo.split('+');var needCtrl=false,needShift=false,needAlt=false,needMeta=false,key='';parts.forEach(function(p){p=p.trim();if(p==='Ctrl'||p==='Control')needCtrl=true;else if(p==='Shift')needShift=true;else if(p==='Alt')needAlt=true;else if(p==='Meta'||p==='Cmd')needMeta=true;else key=p});return(e.ctrlKey===needCtrl)&&(e.shiftKey===needShift)&&(e.altKey===needAlt)&&(e.metaKey===needMeta)&&(e.key.toLowerCase()===key.toLowerCase())}

document.addEventListener('keydown',function(e){
	if(matchKey(e,getKeybinding('stop'))){
		if((_ss[aC]&&_ss[aC].streaming)||(_ss[aC]&&_ss[aC].xhr)){if(_ss[aC]&&_ss[aC].xhr){_ss[aC].xhr.abort();_ss[aC].xhr=null}_ss[aC].streaming=false;_ss[aC].lockActive=false;syncStreamUI();st('Stopped',false);clearGenTab(aC);if(_streamSid===aC){_streamSid=null}_activeThinkBubble=null;api('abort',{},function(){})}
		var openModals=document.querySelectorAll('.ocMo.open,.ocFv.open,.ocCmdP.open,.ocPerm.open');
		openModals.forEach(function(m){m.classList.remove('open')});
		var slashH=document.getElementById('ocSlash');if(slashH)slashH.classList.remove('open');slashHintIdx=-1;
		return;
	}
	if(matchKey(e,getKeybinding('command_palette'))){e.preventDefault();e.stopPropagation();toggleCmdPalette();return}
	if(((_ss[aC]&&_ss[aC].streaming)||cS||lockActive)&&((e.key==='F5')||(e.ctrlKey&&e.key==='r')||(e.metaKey&&e.key==='r'))){
		e.preventDefault();
		e.stopPropagation();
		if(confirm('A generation is still running in the background. If you refresh the page, the process will be terminated and any in-progress work may be lost.\n\nDo you want to refresh anyway?')){
			api('abort',{},function(){});
			_intentionalUnload=true;
			location.reload();
		}
		return;
	}
	var inp=document.getElementById('ocIn');
	// Panel toggles work even while typing
	if(matchKey(e,getKeybinding('toggle_modified'))){e.preventDefault();var _fp=document.getElementById('ocFilesP');_fp.classList.toggle('open');if(_fp.classList.contains('open'))loadModifiedFiles();return}
	if(matchKey(e,getKeybinding('toggle_todos'))){e.preventDefault();document.getElementById('ocTodoP').classList.toggle('open');return}
	if(matchKey(e,getKeybinding('toggle_files'))){e.preventDefault();document.getElementById('ocFtBtn').click();return}
	if(matchKey(e,getKeybinding('toggle_sidebar'))){e.preventDefault();document.getElementById('ocSb').classList.toggle('hid');return}
	// Alt+1..9 session switching (separate from keybindings system)
	// Supports Alt+10..99: press Alt+1 then Alt+0 within 500ms for session 10.
	if(e.altKey&&!e.shiftKey&&!e.ctrlKey&&!e.metaKey&&e.key>='0'&&e.key<='9'){
		e.preventDefault();
		_altNumBuf+=e.key;
		var idx=parseInt(_altNumBuf,10)-1;
		if(_altNumTimer)clearTimeout(_altNumTimer);
		if(_altNumBuf.length>=2){
			// Two-digit complete — switch to the 2-digit session and reset
			if(_sessions&&_sessions[idx]){switchToSessionIdx(idx)}
			_altNumBuf='';_altNumTimer=null;
		}else{
			// Single digit — switch immediately but allow override if a second digit comes within 500ms
			if(_sessions&&_sessions[idx]){switchToSessionIdx(idx)}
			_altNumTimer=setTimeout(function(){_altNumBuf='';_altNumTimer=null;},500);
		}
		return;
	}
	if(document.activeElement===inp)return;
	// Below only when not typing in input
	if(matchKey(e,getKeybinding('new_session'))){e.preventDefault();newS();return}
	if(matchKey(e,getKeybinding('plan_mode'))){e.preventDefault();document.getElementById('ocMode').value='plan';api('set_mode',{mode:'plan'},function(){});return}
	if(matchKey(e,getKeybinding('build_mode'))){e.preventDefault();document.getElementById('ocMode').value='build';api('set_mode',{mode:'build'},function(){});return}
	if(matchKey(e,getKeybinding('settings'))){e.preventDefault();document.getElementById('ocSetMod').classList.add('open');loadP();return}
	if(matchKey(e,getKeybinding('keybindings'))){e.preventDefault();renderKeybindings();document.getElementById('ocKbMod').classList.add('open');return}
	if(matchKey(e,getKeybinding('help'))){e.preventDefault();document.getElementById('ocHlBtn').click();return}
	if(matchKey(e,getKeybinding('sound'))){e.preventDefault();_soundEnabled=!_soundEnabled;localStorage.setItem('oc_sound_enabled',_soundEnabled?'1':'0');applySndIcon(_soundEnabled);toast('Sound Notifications - '+(_soundEnabled?'ON':'OFF'),'ok');return}
	if(matchKey(e,getKeybinding('notifications'))){e.preventDefault();if(typeof Notification!=='undefined'){if(Notification.permission==='default')Notification.requestPermission(function(p){_desktopNotifEnabled=(p==='granted');applyNtfIcon(_desktopNotifEnabled)});else{_desktopNotifEnabled=!_desktopNotifEnabled;applyNtfIcon(_desktopNotifEnabled);toast('Desktop Notifications - '+(_desktopNotifEnabled?'ON':'OFF'),'ok')}}return}
	if(matchKey(e,getKeybinding('clear'))){e.preventDefault();api('clear',{},function(){rMsgs([]);st('Cleared',true)});return}
	if(matchKey(e,getKeybinding('compact'))){e.preventDefault();st('Compacting...',true);api('compact',{keep_last_n:3},function(e2,r){if(!e2&&r&&r.success){rMsgs(r.messages||[]);if(r.summary){toast('Nothing to compact','warn')}else{toast('Compacted','ok')}st('Ready',true)}else{toast('Failed','err');st('Ready',true)}});return}
	if(matchKey(e,getKeybinding('export'))){e.preventDefault();showExportDialog();return}
	if(matchKey(e,getKeybinding('copy'))){e.preventDefault();copyTranscript();return}
},true);

var ocProject=document.getElementById('ocProject');
function loadSidebarProjects(){
	api('projects_list',null,function(e,d){
		if(e||!d)return;
		if(!ocProject)return;
		ocProject.innerHTML='<option value="__new__">+ Add New Project</option>';
		d.forEach(function(p){
			var o=document.createElement('option');
			o.value=p.project_id;
			o.textContent=p.name||p.path;
			if(p.project_id===PID) o.selected=true;
			ocProject.appendChild(o);
		});
	});
}
if(ocProject){
	loadSidebarProjects();
	ocProject.addEventListener('change',function(){
		var val=this.value;
		if(val==='__new__'){
			this.value=PID;
			openProjectModal();
			setTimeout(function(){
				document.getElementById('ocProjNewForm').style.display='block';
				document.getElementById('ocProjName').value='';
				document.getElementById('ocProjPath').value='';
				if(wordpressInstalls&&wordpressInstalls.length){
					document.getElementById('ocProjWpSection').style.display='block';
				}
			},300);
			return;
		}
		if(val && val!==PID){
			switchToProject(val);
		}
	});
}

document.getElementById('ocModel').addEventListener('change',function(){
	var v=this.value;
	if(!v)return;
	syncModelBtn();
	syncModelDropdownSelection();
	updateVariantBadge();
});
document.getElementById('ocMode').addEventListener('change',function(){
	api('set_mode',{mode:this.value},function(){});
});

// Custom model dropdown toggle
(function(){
	var btn=document.getElementById('ocModelBtn');
	var drop=document.getElementById('ocModelDrop');
	btn.addEventListener('click',function(e){
		e.stopPropagation();
		var isOpen=drop.classList.contains('open');
		if(isOpen){
			drop.classList.remove('open');
			return;
		}
		// Position the fixed dropdown under the button, clamped to viewport
		var r=btn.getBoundingClientRect();
		var availH=window.innerHeight-(r.bottom+8);
		var maxH=Math.max(200,Math.min(420,availH));
		drop.style.left=r.left+'px';
		drop.style.top=(r.bottom+4)+'px';
		drop.style.maxHeight=maxH+'px';
		drop.classList.add('open');
		syncModelDropdownSelection();
		// Scroll selected into view (defer so it doesn't trigger the close-on-scroll guard)
		setTimeout(function(){
			if(!drop.classList.contains('open'))return;
			var sel=drop.querySelector('.ocModelOpt.selected');
			if(sel)sel.scrollIntoView({block:'nearest'});
		},0);
	});
	btn.addEventListener('keydown',function(e){
		if(e.key==='Enter'||e.key===' '){e.preventDefault();btn.click()}
	});
	// Close on outside click (bubbling phase; btn handler stops propagation so btn clicks are safe)
	document.addEventListener('click',function(e){
		if(!drop.classList.contains('open'))return;
		if(drop.contains(e.target)||btn.contains(e.target)||e.target===btn)return;
		drop.classList.remove('open');
	});
	// Close on Escape
	document.addEventListener('keydown',function(e){
		if(e.key==='Escape')drop.classList.remove('open');
	});
	// Close on window resize (layout changes); do NOT close on scroll since the dropdown is fixed
	window.addEventListener('resize',function(){if(drop.classList.contains('open'))drop.classList.remove('open')});

	// Add keybindings section to settings modal
	var _origLoadP=loadP;
	loadP=function(){
		_origLoadP();
		var bd=document.querySelector('#ocSetMod .ocMoBd');
		if(bd&&!document.getElementById('ocKbSection')){
			var sec=document.createElement('div');
			sec.id='ocKbSection';
			sec.style.cssText='margin-top:16px;padding-top:16px;border-top:1px solid var(--bd3)';
			sec.innerHTML='<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px"><span style="font-weight:500;font-size:14px">Keybindings</span><button class="ocB ocBs" id="ocKbCustomBtn">Customize</button></div><div style="font-size:12px;color:var(--t3)">Press keys to perform actions quickly</div>';
			bd.appendChild(sec);
			document.getElementById('ocKbCustomBtn').addEventListener('click',function(){
				document.getElementById('ocSetMod').classList.remove('open');
				renderKeybindings();
				document.getElementById('ocKbMod').classList.add('open');
			});
		}
	};
})();
// ===== Header overflow: collapse icons into "more" dropdown =====
(function(){
	var hr=document.querySelector('.ocHr');
	var moreBtn=document.getElementById('ocHMore');
	var moreDrop=document.getElementById('ocHMoreDrop');
	if(!hr||!moreBtn||!moreDrop)return;
	// Buttons that can be collapsed (exclude toggle sidebar, more btn, and buttons hidden by app logic)
	var collapsible=Array.from(hr.querySelectorAll('button:not(#ocTogRsb):not(#ocHMore)'));
	var divider=hr.querySelector('.ocHrDiv');

	function layoutHeader(){
		var ocW=document.getElementById('oc').offsetWidth;
		// Count visible buttons (buttons with data-app-hidden are hidden by app logic, e.g. inactive ocReasonToggle)
		var visible=collapsible.filter(function(b){return b.getAttribute('data-app-hidden')!=='1'});
		var showCount;
		if(ocW>=1000)showCount=visible.length;
		else if(ocW>=800)showCount=6;
		else if(ocW>=600)showCount=4;
		else showCount=2;

		// Reset: show all buttons that were hidden by overflow
		collapsible.forEach(function(b){if(b.getAttribute('data-overflow-hidden')==='1'){b.style.display='';b.removeAttribute('data-overflow-hidden')}});
		if(divider)divider.style.display='';
		moreDrop.innerHTML='';
		moreBtn.classList.remove('show');

		if(showCount<visible.length){
			moreBtn.classList.add('show');
			var hiddenCount=0;
			for(var i=showCount;i<visible.length;i++){
				var btn=visible[i];
				btn.style.display='none';
				btn.setAttribute('data-overflow-hidden','1');
				hiddenCount++;
				// Create dropdown item
				var item=document.createElement('button');
				item.className='ocHMoreItem';
				item.innerHTML=btn.innerHTML;
				// Inherit icon color from original button
				var svg=item.querySelector('svg');
				if(svg){var cs=getComputedStyle(btn.querySelector('svg'));svg.style.color=cs.color}
				var title=btn.getAttribute('title')||btn.getAttribute('aria-label')||'';
				var label=document.createElement('span');
				label.textContent=title;
				item.appendChild(label);
				(function(b){item.addEventListener('click',function(){b.click();moreDrop.classList.remove('open')})})(btn);
				moreDrop.appendChild(item);
			}
			// Hide divider if neighbors are hidden
			if(divider){
				var prev=divider.previousElementSibling;
				var next=divider.nextElementSibling;
				if((prev&&prev.getAttribute('data-overflow-hidden')==='1')||(next&&next.getAttribute('data-overflow-hidden')==='1')){
					divider.style.display='none';
				}
			}
		}
	}

	moreBtn.addEventListener('click',function(e){
		e.stopPropagation();
		moreDrop.classList.toggle('open');
	});
	document.addEventListener('click',function(e){
		if(!moreDrop.contains(e.target)&&e.target!==moreBtn&&!moreBtn.contains(e.target))moreDrop.classList.remove('open');
	});

	if(typeof ResizeObserver!=='undefined'){
		var ro=new ResizeObserver(function(){layoutHeader()});
		ro.observe(document.getElementById('oc'));
	}else{
		window.addEventListener('resize',layoutHeader);
	}
	layoutHeader();
})();
document.getElementById('ocNew').addEventListener('click',newS);
document.getElementById('ocTogSb').addEventListener('click',function(){
	var sb=document.getElementById('ocSb');
	if(window.innerWidth<=768){
		if(sb.classList.contains('resp')){
			sb.classList.remove('resp');
			sb.classList.add('hid');
		}else{
			sb.classList.remove('hid');
			sb.classList.add('resp');
		}
	}else{
		sb.classList.toggle('hid');
	}
});
document.getElementById('ocTogRsb').addEventListener('click',function(){
	var rsb=document.getElementById('ocRsb');
	if(rsb){rsb.classList.toggle('open')}
});
// Auto-hide sidebar when container is narrow (works inside iframe)
if(typeof ResizeObserver!=='undefined'){
	var _ocEl=document.getElementById('oc');
	var _ro=new ResizeObserver(function(){
		var sb=document.getElementById('ocSb');
		var w=_ocEl.offsetWidth;
		if(w<=768){
			sb.classList.add('hid');
			sb.classList.remove('resp');
		}else if(w<=1100){
			sb.classList.remove('resp');
		}else{
			sb.classList.remove('resp');
			if(sb.classList.contains('hid')&&w>768)sb.classList.remove('hid');
		}
	});
	_ro.observe(_ocEl);
}else{
	window.addEventListener('resize',function(){
		var sb=document.getElementById('ocSb');
		if(window.innerWidth<=768){
			sb.classList.add('hid');
			sb.classList.remove('resp');
		}else{
			sb.classList.remove('resp');
			if(!sb.classList.contains('hid'))sb.classList.remove('hid');
		}
	});
}

// ---- Right sidebar wiring ----
(function(){
	// Slash command chips
	var chips=document.getElementById('ocChips');
	if(chips){
		chips.addEventListener('click',function(e){
			var c=e.target.closest('.ocChip');
			if(!c) return;
			var cmd=c.getAttribute('data-cmd')||'';
			var inp=document.getElementById('ocIn');
			if(inp){
				inp.value=cmd+' ';
				inp.focus();
				inp.dispatchEvent(new Event('input'));
			}
		});
	}
	// Slash button focuses input with /
	var slashBtn=document.getElementById('ocSlashBtn');
	if(slashBtn){
		slashBtn.addEventListener('click',function(){
			var inp=document.getElementById('ocIn');
			if(inp){inp.value='/';inp.focus();inp.dispatchEvent(new Event('input'))}
		});
	}
	// Sidebar footer buttons
	var kbBtn=document.getElementById('ocKbBtn');
	if(kbBtn){kbBtn.addEventListener('click',function(){if(document.getElementById('ocKbMod')){if(typeof renderKeybindings==='function'){renderKeybindings()}document.getElementById('ocKbMod').classList.add('open')}})}
	// Model variant select syncs with existing variant system
	var varSel=document.getElementById('ocRsbVarSel');
	if(varSel){
		varSel.addEventListener('change',function(){
			var v=varSel.value||'default';
			if(typeof selModel==='string'&&selModel){
				if(typeof currentVariant==='object'){currentVariant[selModel]=v}
				if(typeof updateVariantBadge==='function'){updateVariantBadge()}
			}
		});
	}
	// View All in modified files card opens Changes panel
	var modView=document.getElementById('ocRsbModView');
	if(modView){modView.addEventListener('click',function(){var b=document.getElementById('ocChgBtn');if(b)b.click()})}
})();

// ---- Sync right sidebar context bar with existing stat updates ----
(function(){
	var rsbBar=document.getElementById('ocRsbCtxBar');
	var rsbTxt=document.getElementById('ocRsbCtxTxt');
	var rsbModCount=document.getElementById('ocRsbModCount');
	var rsbModList=document.getElementById('ocRsbModList');
	// Poll the existing context stat value to mirror it
	if(rsbBar&&rsbTxt){
		setInterval(function(){
			var ctxEl=document.getElementById('ocStatCtxV');
			var ctxBar=document.getElementById('ocCtxBar');
			if(!ctxEl) return;
			var txt=(ctxEl.textContent||'').trim();
			// Try to parse a percentage or token count
			var pct=0;
			var m=txt.match(/(\d+(\.\d+)?)\s*%?/);
			if(m){pct=parseFloat(m[1]); if(pct<1&&txt.indexOf('%')<0){pct=pct>1000?0:0}}
			// Use ctxBar width if available (more reliable)
			if(ctxBar){
				var fill=ctxBar.querySelector('.ocCtxBarFill');
				if(fill){
					var w=fill.style.width||fill.getAttribute('width')||'';
					var wm=w.match(/(\d+(\.\d+)?)\s*%?/);
					if(wm){pct=parseFloat(wm[1])}
				}
			}
			if(!isFinite(pct)||pct<0)pct=0;
			if(pct>100)pct=100;
			rsbBar.style.width=pct+'%';
			rsbBar.classList.toggle('high',pct>=80);
			rsbTxt.textContent=Math.round(pct)+'% of 128K tokens used';
		},800);
	}
	// Mirror modified files count/list
	if(rsbModCount&&rsbModList){
		setInterval(function(){
			var mods=typeof _modifiedFiles!=='undefined'&&_modifiedFiles?_modifiedFiles:[];
			rsbModCount.textContent=mods.length;
			if(!mods.length){
				rsbModList.innerHTML='<div class="ocRsbFile"><svg class="ocRsbFileI" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg><span class="ocRsbFileN">No files modified</span></div>';
				return;
			}
			rsbModList.innerHTML=mods.slice(0,8).map(function(f){
				var name=typeof f==='string'?f:(f.path||f.name||f);
				name=name.split('/').pop();
				return '<div class="ocRsbFile"><svg class="ocRsbFileI" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg><span class="ocRsbFileN">'+(name+'').replace(/[<>]/g,'')+'</span></div>';
			}).join('');
		},1500);
	}
})();

window.addEventListener('beforeunload',function(e){
	if(_intentionalUnload) return;
	// Check if ANY session is currently streaming (not just the active one)
	var anyStreaming=false;
	for(var k in _ss){if(_ss[k]&&_ss[k].streaming){anyStreaming=true;break;}}
	if(anyStreaming||cS||lockActive){
		api('abort',{},function(){});
		e.preventDefault();
		e.returnValue='';
		return e.returnValue;
	}
});

// Initial state
if(window.innerWidth<=768){
	document.getElementById('ocSb').classList.add('hid');
}

var cs=localStorage.getItem('oc-cs')||'dark';
function applyCS(c){
	document.documentElement.setAttribute('data-cs',c);
	document.getElementById('hljs-theme').href=c==='dark'
		?'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css'
		:'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css';
	localStorage.setItem('oc-cs',c);
	var icon=document.getElementById('ocThIcon');
	if(icon){
		if(c==='dark'){
			icon.innerHTML='<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
			document.getElementById('ocThBtn').title='Switch to light mode';
		}else{
			icon.innerHTML='<path d="M21.64 13a1 1 0 0 0-1.05-.14 8.05 8.05 0 0 1-3.37.73 8.15 8.15 0 0 1-8.14-8.1 8.59 8.59 0 0 1 .25-2A1 1 0 0 0 8 2.36 10.14 10.14 0 1 0 22 14.05a1 1 0 0 0-.36-1.05z"/>';
			document.getElementById('ocThBtn').title='Switch to dark mode';
		}
	}
}
applyCS(cs);
document.getElementById('ocThBtn').addEventListener('click',function(){
	cs=cs==='light'?'dark':'light';
	applyCS(cs);
});

// ---- Sound toggle (wired to existing _soundEnabled) ----
function applySndIcon(on){
	var icon=document.getElementById('ocSndIcon');
	var btn=document.getElementById('ocSndBtn');
	if(!icon||!btn) return;
	if(on){
		icon.innerHTML='<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>';
		btn.title='Mute sound';
		btn.style.opacity='1';
	}else{
		icon.innerHTML='<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/>';
		btn.title='Unmute sound';
		btn.style.opacity='0.45';
	}
	btn.setAttribute('aria-pressed',on?'true':'false');
}
applySndIcon(_soundEnabled);  // will be re-applied after pref load
document.getElementById('ocSndBtn').addEventListener('click',function(){
	_soundEnabled=!_soundEnabled;
	localStorage.setItem('oc_sound_enabled',_soundEnabled?'1':'0');
	applySndIcon(_soundEnabled);
	toast('Sound Notifications - '+(_soundEnabled?'ON':'OFF'),'ok');
});

// ---- Notifications toggle (wired to existing _desktopNotifEnabled) ----
function applyNtfIcon(on){
	var btn=document.getElementById('ocNtfBtn');
	var icon=document.getElementById('ocNtfIcon');
	if(!btn) return;
	btn.title=on?'Disable notifications':'Enable notifications';
	btn.setAttribute('aria-pressed',on?'true':'false');
	btn.style.opacity=on?'1':'0.45';
	if(icon){
		if(on){
			icon.innerHTML='<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18 s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>';
		}else{
			icon.innerHTML='<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18 s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/><line x1="21" y1="4" x2="9" y2="20"/>';
		}
	}
	if(!on){var d=document.getElementById('ocNtfDot');if(d) d.style.display='none';}
}
applyNtfIcon(_desktopNotifEnabled);  // will be re-applied after pref load
document.getElementById('ocNtfBtn').addEventListener('click',function(){
	if(typeof Notification==='undefined'){toast('Notifications not supported','warn');return}
	if(Notification.permission==='default'){
		Notification.requestPermission(function(p){
			_desktopNotifEnabled=(p==='granted');
			applyNtfIcon(_desktopNotifEnabled);
			toast('Desktop Notifications - '+(_desktopNotifEnabled?'ON':'OFF'),_desktopNotifEnabled?'ok':'warn');
		});
	}else if(Notification.permission==='granted'){
		_desktopNotifEnabled=!_desktopNotifEnabled;
		applyNtfIcon(_desktopNotifEnabled);
		toast('Desktop Notifications - '+(_desktopNotifEnabled?'ON':'OFF'),'ok');
	}else{
		_desktopNotifEnabled=false;
		applyNtfIcon(false);
		toast('Notifications blocked in browser settings','warn');
	}
});

api('status',null,function(e,d){
	if(!e&&d){
		_statusLoaded=true;
		if(d.session){
			if(d.session.provider&&d.session.model){
				selModel=d.session.provider+'/'+d.session.model;
				selMode=d.session.mode||'build';
				// Re-apply model selection if dropdown already populated
				var ms=document.getElementById('ocModel');
				if(ms&&ms.options.length>1){
					ms.value=selModel;
					if(ms.value!==selModel){
						var opts=ms.querySelectorAll('option');
						for(var i=0;i<opts.length;i++){
							if(opts[i].value===selModel){ms.selectedIndex=i;break}
						}
					}
					syncModelBtn();
					syncModelDropdownSelection();
					updateVariantBadge();
				}
			}
			if(d.session.mode)document.getElementById('ocMode').value=d.session.mode;
			var urlSession=new URL(window.location).searchParams.get('session');
			if(urlSession){
				aC=urlSession;
				loadC(aC);
			}else if(d.session.active_conversation){
				aC=d.session.active_conversation;
				loadC(aC);
			}
		}
		var hc=(d.providers||[]).some(function(p){return p.connected});
		st(hc?'Ready':'No provider connected',hc);

		// Apply favorite models from status (covers the case where settings_load hasn't returned yet)
		if(d.favorite_models && d.favorite_models.length && !favModels.length){
			favModels=d.favorite_models;
			if(_providersCache){
				renderModelSelect(_providersCache);
				renderModelDropdown(_providersCache);
				syncModelBtn();
				syncModelDropdownSelection();
			}
		}

		// Auto-detect model metadata from models.dev
		if(d.model_info){
			for(var mid in d.model_info){
				if(d.model_info[mid].context){
					modelCtx[mid]=d.model_info[mid].context;
				}
			}
		}
		if(d.lock&&d.lock.locked){
			// Only show stop button if the active session matches the one that's generating.
			if(d.session&&d.session.active_conversation===aC){
				if(!_ss[aC]) _ss[aC]={};
				_ss[aC].streaming=true;
				_ss[aC].lockActive=true;
				syncStreamUI();
				st('Generation in progress...',true);
			}else if(aC){
				// There's a lock but not for the active session — show idle status.
				// Another session is generating; this one should still accept input.
				syncStreamUI();
				st('Ready',true);
			}
		}
		// If the server reports running_conversations, sync per-session streaming state
		// so the UI reflects which sessions are currently generating (for sidebar dots).
		if(d.running_conversations&&d.running_conversations.length){
			for(var i=0;i<d.running_conversations.length;i++){
				var rcid=d.running_conversations[i];
				if(!_ss[rcid]) _ss[rcid]={};
				_ss[rcid].streaming=true;
				_ss[rcid].lockActive=true;
			}
			// Update the active session's UI based on its state
			syncStreamUI();
		}
	}
});

api('project_info',null,function(e,d){
	if(!e&&d){
		var overviewText=(d.overview||'');
		var lines=overviewText.split('\n');
		var typeLine=lines[0]||'';
		var pathLine=lines[1]||'';
		var typeMatch=typeLine.match(/Project type:\s*(.+)/i);
		var pathMatch=pathLine.match(/Project path:\s*(.+)/i);
		var dispType=typeMatch?typeMatch[1].trim():(d.type||'unknown');
		var dispPath=pathMatch?pathMatch.trim():SP;
		if(dispPath&&HD&&dispPath.indexOf(HD)===0) dispPath=dispPath.substring(HD.length+1);
		ctx='<b>Project Type:</b> '+esc(dispType)+'<br>'
			+'<b>Path:</b> <code>'+esc(dispPath||SP)+'</code><br>';
		var restOfOverview=lines.slice(2).join('\n').trim();
		if(restOfOverview){
			ctx+='<br>'+esc(restOfOverview.substring(0,300))+(restOfOverview.length>300?'...':'');
		}
		rCtx();
	}
});

loadP();
loadSL();

var ftOpen=false,ftLoaded=false,ftPath='';
document.getElementById('ocFtBtn').addEventListener('click',function(){
	var panel=document.getElementById('ocFt');
	if(ftOpen==false){
		panel.classList.add('open');
		ftOpen=true;
		if(!ftLoaded){loadFT('/');ftLoaded=true}
	}else{
		panel.classList.remove('open');
		ftOpen=false;
	}
});
document.getElementById('ocFtRef').addEventListener('click',function(){loadFT(ftPath||'/')});
document.getElementById('ocFv').addEventListener('click',function(e){if(e.target===this)this.classList.remove('open')});
document.getElementById('ocFvC').addEventListener('click',function(){document.getElementById('ocFv').classList.remove('open')});

function loadFT(path){
	ftPath=path;
	var u=A+'&ai_php_api=file_tree&csrf_token='+C+'&path='+encodeURIComponent(path)+'&depth=1';
	var x=new XMLHttpRequest();
	x.open('GET',u,true);
	x.setRequestHeader('Accept','application/json');
	x.onload=function(){
		if(x.status===200){
			try{renderFT(JSON.parse(x.responseText),path)}
			catch(e){}
		}
	};
	x.send();
}

function renderFT(data,basePath){
	var el=document.getElementById('ocFtL');
	el.innerHTML='';
	if(data.directories){
		data.directories.sort(function(a,b){return a.name.localeCompare(b.name)});
		data.directories.forEach(function(d){
			if(d.name==='.git')return;
			var item=document.createElement('div');
			item.className='ocFtI';
			item.dataset.path=d.path;
			item.dataset.type='dir';
			item.dataset.loaded='0';
			item.innerHTML='<span class="ocFtIc"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></span><span class="ocFtIn">'+esc(d.name)+'</span>';
			item.addEventListener('click',function(ev){
				ev.stopPropagation();
				var loaded=this.dataset.loaded==='1';
				var open=this.dataset.open==='1';
				if(!loaded){
					this.dataset.loaded='1';
					var sub=document.createElement('div');
					sub.className='ocFtSub';
					sub.style.display='block';
					this.after(sub);
					loadFTSub(d.path,sub,this);
				}else{
					var sub=this.nextElementSibling;
					if(sub&&sub.className==='ocFtSub'){
						sub.style.display=open?'none':'block';
					}
				}
				this.dataset.open=open?'0':'1';
				this.querySelector('.ocFtIc').innerHTML=open?'<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>':'<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';
			});
			el.appendChild(item);
		});
	}
	if(data.files){
		data.files.sort(function(a,b){return a.name.localeCompare(b.name)});
		data.files.forEach(function(f){
			var item=document.createElement('div');
			item.className='ocFtI';
			item.dataset.path=f.path;
			item.dataset.type='file';
			var sz=f.size?fmtSize(f.size):'';
			item.innerHTML='<span class="ocFtIc" style="opacity:.4"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg></span><span class="ocFtIn">'+esc(f.name)+'</span>'
				+(sz?'<span class="ocFtS">'+sz+'</span>':'');
			item.addEventListener('click',function(ev){
				ev.stopPropagation();
				viewFile(f.path,f.name);
			});
			el.appendChild(item);
		});
	}
}

function loadFTSub(path,container,toggle){
	var u=A+'&ai_php_api=file_tree&csrf_token='+C+'&path='+encodeURIComponent(path)+'&depth=1';
	var x=new XMLHttpRequest();
	x.open('GET',u,true);
	x.setRequestHeader('Accept','application/json');
	x.onload=function(){
		if(x.status===200){
			try{
				var data=JSON.parse(x.responseText);
				container.innerHTML='';
				if(data.directories){
					data.directories.sort(function(a,b){return a.name.localeCompare(b.name)});
					data.directories.forEach(function(d){
						var item=document.createElement('div');
						item.className='ocFtI';
						item.dataset.path=d.path;
						item.dataset.type='dir';
						item.dataset.loaded='0';
						item.style.paddingLeft='16px';
			item.innerHTML='<span class="ocFtIc"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></span><span class="ocFtIn">'+esc(d.name)+'</span>';
						item.addEventListener('click',function(ev){
							ev.stopPropagation();
							var loaded=this.dataset.loaded==='1';
							var open=this.dataset.open==='1';
							if(!loaded){
								this.dataset.loaded='1';
								var sub=document.createElement('div');
								sub.className='ocFtSub';
								sub.style.display='block';
								this.after(sub);
								loadFTSub(d.path,sub,this);
							}else{
								var sub=this.nextElementSibling;
								if(sub&&sub.className==='ocFtSub'){
									sub.style.display=open?'none':'block';
								}
							}
							this.dataset.open=open?'0':'1';
this.querySelector('.ocFtIc').innerHTML=open?'<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>':'<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';
						});
						container.appendChild(item);
					});
				}
				if(data.files){
					data.files.sort(function(a,b){return a.name.localeCompare(b.name)});
					data.files.forEach(function(f){
						var item=document.createElement('div');
						item.className='ocFtI';
						item.dataset.path=f.path;
						item.dataset.type='file';
						item.style.paddingLeft='16px';
						var sz=f.size?fmtSize(f.size):'';
			item.innerHTML='<span class="ocFtIc" style="opacity:.4"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg></span><span class="ocFtIn">'+esc(f.name)+'</span>'
							+(sz?'<span class="ocFtS">'+sz+'</span>':'');
						item.addEventListener('click',function(ev){
							ev.stopPropagation();
							viewFile(f.path,f.name);
						});
						container.appendChild(item);
					});
				}
				if(!data.directories&&!data.files){
					container.innerHTML='<div style="padding:4px 16px;color:var(--t3);font-size:11px">Empty</div>';
				}
			}catch(e){}
		}
	};
	x.send();
}

function viewFile(path,name){
	var u=A+'&ai_php_api=read_file&csrf_token='+C+'&path='+encodeURIComponent(path);
	var x=new XMLHttpRequest();
	x.open('GET',u,true);
	x.setRequestHeader('Accept','application/json');
	x.onload=function(){
		if(x.status===200){
			try{
				var d=JSON.parse(x.responseText);
				document.getElementById('ocFvT').textContent=name||path;
				var pre=document.getElementById('ocFvPre');
				if(d.content!==undefined){
					try{pre.innerHTML=hljs.highlightAuto(d.content).value}
					catch(e){pre.textContent=d.content}
				}else{
					pre.textContent=d.error||'Failed to read file';
				}
				document.getElementById('ocFv').classList.add('open');
			}catch(e){}
		}
	};
	x.send();
}

function fmtSize(b){
	if(b<1024)return b+'B';
	if(b<1048576)return (b/1024).toFixed(1)+'K';
	return (b/1048576).toFixed(1)+'M';
}
function fmtTok(n){
	if(n>=1000000)return (n/1000000).toFixed(1)+'M';
	if(n>=1000)return (n/1000).toFixed(1)+'k';
	return n;
}
function updateURL(convId){
	var u=new URL(window.location);
	if(convId)u.searchParams.set('session',convId);
	else u.searchParams.delete('session');
history.replaceState(null,'',u.toString());
}

// Toast notifications
function toast(msg,type,dur){
	var t=document.getElementById('ocToast');
	var d=document.createElement('div');
	d.className='ocToastI '+(type||'info');
	d.textContent=msg;
	t.appendChild(d);
	setTimeout(function(){d.classList.add('hide');setTimeout(function(){d.remove()},300)},dur||3000);
}

// Sound notification using Web Audio API (played on tab focus, not in background)
var _audioCtx=null;
function _playChime(){
	try{
		if(!_audioCtx)_audioCtx=new(window.AudioContext||window.webkitAudioContext)();
		if(_audioCtx.state==='suspended')_audioCtx.resume();
		var t=_audioCtx.currentTime;
		function _note(freq,start,dur,vol){
			var o=_audioCtx.createOscillator(),g=_audioCtx.createGain();
			o.type='sine';o.frequency.value=freq;
			o.connect(g);g.connect(_audioCtx.destination);
			g.gain.setValueAtTime(0,t+start);
			g.gain.linearRampToValueAtTime(vol,t+start+0.015);
			g.gain.setValueAtTime(vol,t+start+dur*0.15);
			g.gain.exponentialRampToValueAtTime(0.001,t+start+dur);
			o.start(t+start);o.stop(t+start+dur+0.01);
		}
		_note(880,0,0.12,0.2);
		_note(660,0.1,0.15,0.16);
	}catch(e){}
}

// Browser notification + sound + title flash when chat completes in background tab
function _notifyOnComplete(title,body){
	if(!document.hidden)return;
	if(_notifyPending)return;
	_notifyPending=true;
	document.title='\u2728 '+(body||'Response ready');
	_playSound('complete');
	if(_desktopNotifEnabled&&typeof Notification!=='undefined'&&Notification.permission==='granted'){
		var n=new Notification(title,{body:body,tag:'ai-chat'});
		n.onclick=function(){window.focus();n.close()};
	}
}

// Reset title when user returns to tab after background notification
document.addEventListener('visibilitychange',function(){
	if(!document.hidden&&_notifyPending){
		_notifyPending=false;
		document.title=_origTitle;
	}
});

function forceUnlockAndRetry(){
	var inp=document.getElementById('ocIn');
	var lastMsg=inp.value.trim();
	api('force_unlock',{},function(e,r){
		if(!e&&r&&r.success){
			toast('Lock cleared successfully','ok');
			st('Ready',true);
			if(aC&&_ss[aC]){_ss[aC].streaming=false;_ss[aC].lockActive=false;_ss[aC].xhr=null}
			syncStreamUI();
			cS=null;
			clearGenTab(aC);
			if(lastMsg){
				inp.value=lastMsg;
				setTimeout(function(){send()},300);
			}
		}else{
			toast('Failed to clear lock: '+(e||(r&&r.message)||'Unknown error'),'err');
		}
	});
}
window.forceUnlockAndRetry=forceUnlockAndRetry;

// @-Mention file resolution + mode switching in send()
var origSend=send;
send=function(){
	var inp=document.getElementById('ocIn');
	var text=inp.value.trim();
	if(!text)return;
	// Only block if the CURRENT session is streaming, not other sessions
	if(_ss[aC]&&_ss[aC].streaming)return;

	// Handle @build/@plan mode mentions
	var modeMentions=text.match(/@(build|plan)\b/gi);
	if(modeMentions){
		modeMentions.forEach(function(m){
			var mode=m.substring(1).toLowerCase();
			if(mode==='build'||mode==='plan'){
				document.getElementById('ocMode').value=mode;
				api('set_mode',{mode:mode},function(){});
				text=text.replace(m,'').trim();
			}
		});
		inp.value=text;
		if(!text)return;
	}

	var mentions=text.match(/@([\w\/.\-]+(?:#\d+(?:-\d+)?)?)/g);
	if(mentions){
		mentions=mentions.filter(function(m){
			var fp=m.substring(1).split('#')[0].toLowerCase();
			return fp!=='build'&&fp!=='plan';
		});
		if(mentions.length>0){
			var files={};
			var lineRanges={};
			var resolved=0;
			var remaining=mentions.length;
			mentions.forEach(function(m){
				var raw=m.substring(1);
				var hashIdx=raw.indexOf('#');
				var fp=hashIdx!==-1?raw.substring(0,hashIdx):raw;
				var lineRange=hashIdx!==-1?raw.substring(hashIdx+1):null;
				if(lineRange)lineRanges[fp]=lineRange;
				var u=A+'&ai_php_api=resolve_file&csrf_token='+C+'&path='+encodeURIComponent(fp);
				var x=new XMLHttpRequest();
				x.open('GET',u,true);
				x.setRequestHeader('Accept','application/json');
				x.onload=function(){
					if(x.status===200){
						try{
							var d=JSON.parse(x.responseText);
							if(d.content){
								files[fp]=d.content;
								resolved++;
							}
						}catch(e){}
					}
					remaining--;
					if(remaining===0){
						if(resolved>0){
							var fileCtx='';
							for(var f in files){
								var content=files[f];
								var range=lineRanges[f];
								if(range){
									var parts=range.split('-');
									var start=parseInt(parts[0])||1;
									var end=parts.length>1?parseInt(parts[1])||start:start;
									var lines=content.split('\n');
									var selected=lines.slice(Math.max(0,start-1),Math.min(end,lines.length));
									content=selected.map(function(l,i){return (start+i)+': '+l}).join('\n');
									fileCtx+='\n\n--- @'+f+'#'+range+' ---\n'+content+'\n';
								}else{
									fileCtx+='\n\n--- @'+f+' ---\n'+content+'\n';
								}
							}
							text+='\n[Attached files:]'+fileCtx;
						}
						inp.value=text;
						origSend();
					}
				};
				x.onerror=function(){
					remaining--;
					if(remaining===0){
						inp.value=text;
						origSend();
					}
				};
				x.send();
			});
			return;
		}
	}
	origSend();
};

// Message actions (edit, regenerate)
document.getElementById('ocCI').addEventListener('mouseover',function(e){
	var msg=e.target.closest('.ocMsg');
	if(!msg||msg.querySelector('.ocMsgAct'))return;
	var isU=msg.classList.contains('u');
	var isLast=!msg.nextElementSibling;
	var act=document.createElement('div');
	act.className='ocMsgAct';
	if(isU){
		var editBtn=document.createElement('button');
		editBtn.className='ocMsgActB';
		editBtn.innerHTML='<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>';
		editBtn.title='Edit & Resend';
		editBtn.addEventListener('click',function(ev){
			ev.stopPropagation();
			var bb=msg.querySelector('.ocMbb');
			if(!bb)return;
			var oldText=bb.textContent||'';
			bb.contentEditable='true';
			bb.focus();
			bb.style.background='var(--input)';
			bb.style.color='var(--t1)';
			bb.style.border='1px solid var(--ac)';
			bb.style.borderRadius='var(--r)';
			bb.style.padding='8px 12px';
			var saveBtn=document.createElement('button');
			saveBtn.textContent='Send';
			saveBtn.className='ocB ocBP';
			saveBtn.style.cssText='position:absolute;bottom:-30px;right:0;font-size:11px;height:24px;z-index:100';
			msg.style.position='relative';
			msg.appendChild(saveBtn);
			saveBtn.addEventListener('click',function(){
				var newText=bb.textContent.trim();
				bb.contentEditable='false';
				bb.style.cssText='';
				saveBtn.remove();
				if(newText&&newText!==oldText){
					var msgId=msg.dataset.msgId||'';
					// Just set the input and send as new prompt - don't delete anything
					var inp=document.getElementById('ocIn');
					inp.value=newText;
					origSend();
					toast('Message sent as new prompt','ok');
				}
			});
		});
		act.appendChild(editBtn);
		var forkBtn=document.createElement('button');
		forkBtn.className='ocMsgActB';
		forkBtn.innerHTML='<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v12"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>';
		forkBtn.title='Fork from here';
		forkBtn.addEventListener('click',function(ev){
			ev.stopPropagation();
			var msgId=msg.dataset.msgId||'';
			// Always verify msgId exists in the loaded messages
			var allMsgs=rMsgs._lastMsgs||[];
			var msgIdx=-1;
			for(var i=0;i<allMsgs.length;i++){
				if(allMsgs[i].id===msgId){msgIdx=i;break}
			}
			// If msgId not found in messages, try to find by content match
			if(msgIdx<0){
				var bbFork=msg.querySelector('.ocMbb');
				var domText=bbFork?(bbFork.textContent||'').trim().substring(0,50):'';
				for(var i=0;i<allMsgs.length;i++){
					if(allMsgs[i].role==='user'){
						var apiContent=(allMsgs[i].content||'').trim().substring(0,50);
						if(domText&&apiContent&&domText.indexOf(apiContent)===0){msgId=allMsgs[i].id;msgIdx=i;break}
					}
				}
			}
			if(!msgId||msgIdx<0){toast('Cannot fork: message ID not found','warn');return}
			st('Forking...',true);
			api('fork',{message_id:msgId},function(e,r){
				if(!e&&r&&r.success){
					aC=r.conversation_id;
					updateURL(aC);
					toast('Forked to new session: '+(r.title||'Untitled'),'ok');
					st('Ready',true);
					loadSL();
					loadC(aC);
					if(r.forked_content){
						var inp=document.getElementById('ocIn');
						inp.value=r.forked_content;
						inp.focus();
					}
				}else{
					toast('Failed to fork: '+(e||(r&&r.error)||'Unknown error'),'err');
					st('Ready',true);
				}
			});
		});
		act.appendChild(forkBtn);
	}
	if(isLast&&!isU){
		var regenBtn=document.createElement('button');
		regenBtn.className='ocMsgActB';
		regenBtn.innerHTML='<svg xmlns="http://www.w3.org/2000/svg" height="12" viewBox="0 -960 960 960" width="12" fill="currentColor"><path d="M396-200q-97 0-166.5-63T160-420q0-94 69.5-157T396-640h252L544-744l56-56 200 200-200 200-56-56 104-104H396q-63 0-109.5 40T240-420q0 60 46.5 100T396-280h284v80H396Z"/></svg>';
		regenBtn.title='Regenerate';
		regenBtn.addEventListener('click',function(ev){
			ev.stopPropagation();
			api('regenerate',{},function(e,r){
				if(!e&&r&&r.success){
					msg.parentNode.removeChild(msg);
					toast('Regenerating...','info');
					var lastUserMsg='';
					document.querySelectorAll('#ocCI .ocMsg.u').forEach(function(m){
						lastUserMsg=m.querySelector('.ocMbb').textContent||'';
					});
					if(lastUserMsg){
						var inp=document.getElementById('ocIn');
						inp.value=lastUserMsg;
						origSend();
						inp.value='';
					}
				}
			});
		});
		act.appendChild(regenBtn);
	}
	if(!isU){
		var forkBtn2=document.createElement('button');
		forkBtn2.className='ocMsgActB';
		forkBtn2.innerHTML='<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v12"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>';
		forkBtn2.title='Fork from here';
		forkBtn2.addEventListener('click',function(ev){
			ev.stopPropagation();
			var prevMsg=msg.previousElementSibling;
			var forkId='';
			while(prevMsg){
				if(prevMsg.classList.contains('u')){
					forkId=prevMsg.dataset.msgId||'';
					// Verify forkId exists in messages, if not try content match
					var allMsgs2=rMsgs._lastMsgs||[];
					var found2=false;
					for(var j=0;j<allMsgs2.length;j++){
						if(allMsgs2[j].id===forkId){found2=true;break}
					}
					if(!found2){
						var bb2=prevMsg.querySelector('.ocMbb');
						var domText2=bb2?(bb2.textContent||'').trim().substring(0,50):'';
						for(var j=0;j<allMsgs2.length;j++){
							if(allMsgs2[j].role==='user'){
								var apiContent2=(allMsgs2[j].content||'').trim().substring(0,50);
								if(domText2&&apiContent2&&domText2.indexOf(apiContent2)===0){forkId=allMsgs2[j].id;found2=true;break}
							}
						}
					}
					if(found2)break;
				}
				prevMsg=prevMsg.previousElementSibling;
			}
			if(!forkId){toast('Cannot fork: no user message found before this','warn');return}
			st('Forking...',true);
			api('fork',{message_id:forkId},function(e,r){
				if(!e&&r&&r.success){
					aC=r.conversation_id;
					updateURL(aC);
					toast('Forked to new session: '+(r.title||'Untitled'),'ok');
					st('Ready',true);
					loadSL();
					loadC(aC);
					if(r.forked_content){
						var inp=document.getElementById('ocIn');
						inp.value=r.forked_content;
						inp.focus();
					}
				}else{
					toast('Failed to fork: '+(e||(r&&r.error)||'Unknown error'),'err');
					st('Ready',true);
				}
			});
		});
		act.appendChild(forkBtn2);
	}
	var copyBtn=document.createElement('button');
	copyBtn.className='ocMsgActB';
	copyBtn.innerHTML='<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>';
	copyBtn.title='Copy';
	copyBtn.addEventListener('click',function(ev){
		ev.stopPropagation();
		var bb=msg.querySelector('.ocMbb');
		if(!bb)return;
		var txt=bb.innerText||bb.textContent;
		var ta=document.createElement('textarea');ta.value=txt;ta.style.cssText='position:fixed;left:-9999px';document.body.appendChild(ta);ta.select();
		try{document.execCommand('copy')}catch(e){}
		document.body.removeChild(ta);
		toast('Copied','ok');
	});
	act.appendChild(copyBtn);
	msg.appendChild(act);
});

// Message timestamps on hover
var _origRMsg=rMsg;
rMsg=function(m){
	var bb=_origRMsg(m);
	var msgEl=bb.closest('.ocMsg');
	if(msgEl&&m.time){
		var ts=document.createElement('span');
		ts.className='ocMsgTs';
		var dt=new Date(m.time*1000);
		ts.textContent=dt.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
		msgEl.appendChild(ts);
	}
	if(m.id)msgEl.dataset.msgId=m.id;
	return bb;
};

// Store last loaded messages for edit lookup
var _origRMsgs=rMsgs;
rMsgs=function(msgs){
	rMsgs._lastMsgs=msgs||[];
	_origRMsgs(msgs);
};

// Conversation loading spinner
var _origLoadC=loadC;
	loadC=function(id){
	aC=id;
	// Determine whether the session we are switching to is currently generating.
	// Each session tracks its own streaming state in _ss[id] so multiple
	// sessions can run independently (one keeps thinking while we view another).
	var thisStreaming=!!(_ss[id] && _ss[id].streaming);
	// Only clear the "thinking" (gen) state if we're NOT switching to the session that's currently generating.
	// If we're visiting the session that's currently AI-thinking, keep its red dot.
	if(!thisStreaming){
		clearGenTab(id);
	}
	clearUnread(id);
	delete _unreadIds[id];
	_lastSeen[id]=Math.floor(Date.now()/1000);
	updateURL(id);
	// Sync the transport UI (stop/send buttons, status) to the session we're viewing.
	// A background stream keeps running in _ss[itsId]; we just stop rendering into it.
	syncStreamUI(id);
	if(thisStreaming){
		st('Thinking...',true);
	}else{
		st('Ready',true);
	}
  	loadTodos();
  	loadPromptHistoryForSession();
  	var ch=document.getElementById('ocCI');
  	ch.innerHTML='<div class="ocLoad"><span class="ocLoadD"></span><span class="ocLoadD"></span><span class="ocLoadD"></span><span>Loading...</span></div>';
  	var u=A+'&ai_php_api=conversation&csrf_token='+C+'&conversation_id='+encodeURIComponent(id);
  	var x=new XMLHttpRequest();
  	x.open('GET',u,true);
  	x.setRequestHeader('Accept','application/json');
  	x.onload=function(){
  		if(x.status===200){
  			try{
  				var d=JSON.parse(x.responseText);
  				rMsgs(d.messages||[]);
  				if(d.todos)applyServerTodos(d.todos);
  				hlS(id);
  				if(!d.messages||!d.messages.length){aC=null;updateURL('')}
  				else{
  					var userMsgs=[];
  					var userEls=document.querySelectorAll('.ocMsg.u .ocMbb');
  					for(var i=0;i<userEls.length;i++){var t=userEls[i].textContent;if(t)userMsgs.push(t)}
  					if(userMsgs.length){promptHistory=userMsgs.slice(-50);promptHistoryIdx=-1;savePromptHistory()}
  				}
  				// If this session is currently generating, re-add the thinking bubble
  				// that was removed when the chat area was re-rendered by rMsgs().
  				if(_ss[id] && _ss[id].streaming){
  					_activeThinkBubble=rMsg({role:'asst',content:'__thinking__',parts:[]});
  					st('Thinking...',true);
  					document.getElementById('ocChat').scrollTop=1e9;
  				}
  			}catch(e){
  				var ch=document.getElementById('ocCI');
  				if(ch)ch.innerHTML='<div style="padding:20px;color:var(--err)">Error loading conversation: '+esc(e.message||'unknown')+'</div>';
  			}
  		}
	};
	x.send();
};

// Auto-title after first AI response
var _origXhrOnload;
var origDoSendPatched=false;

// File tree search
var ftSearchEl=document.getElementById('ocFtSearch');
if(ftSearchEl){
	ftSearchEl.addEventListener('input',function(){
		var q=this.value.toLowerCase();
		var items=document.querySelectorAll('#ocFtL .ocFtI');
		items.forEach(function(it){
			var name=it.querySelector('.ocFtIn');
			if(!name)return;
			it.style.display=(!q||name.textContent.toLowerCase().indexOf(q)!==-1)?'':'none';
		});
	});
}

// Changes panel
var chgOpen=false;
document.getElementById('ocChgBtn').addEventListener('click',function(){
	chgOpen=!chgOpen;
	var panel=document.getElementById('ocChg');
	if(chgOpen){
		api('changes',null,function(e,d){
			if(e||!d){toast('Failed to load changes','err');return}
			var list=document.getElementById('ocChgL');
			list.innerHTML='';
			var snaps=d.snapshots||[];
			if(!snaps.length){
				list.innerHTML='<div style="padding:14px;color:var(--t3);font-size:12px;text-align:center">No changes yet</div>';
			}else{
				snaps.forEach(function(s){
					var item=document.createElement('div');
					item.className='ocChgI';
					var dt=new Date((s.time||0)*1000);
					var ts=dt.toLocaleDateString()===new Date().toLocaleDateString()
						?dt.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})
						:dt.toLocaleDateString([],{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'});
				item.innerHTML='<span>'+esc(s.message||'Snapshot')+' <span style="color:var(--t3);font-size:10px">'+ts+'</span></span>'
					+'<div style="display:flex;gap:4px"><button class="ocChgBtn ocChgDiffBtn" data-id="'+esc(s.id||'')+'" data-msg="'+esc(s.message||'')+'">Diff</button><button class="ocChgBtn" data-id="'+esc(s.id||'')+'">Restore</button></div>';
				list.appendChild(item);
				});
				list.querySelectorAll('.ocChgBtn:not(.ocChgDiffBtn)').forEach(function(btn){
					btn.addEventListener('click',function(){
						if(confirm('Restore this snapshot?')){
							api('restore',{id:this.dataset.id},function(e,r){
								if(!e&&r&&r.restored)toast('Snapshot restored','ok');
								else toast('Failed to restore','err');
							});
						}
					});
				});
				list.querySelectorAll('.ocChgDiffBtn').forEach(function(btn){
					btn.addEventListener('click',function(){
						var snapId=this.dataset.id;
						var snapMsg=this.dataset.msg;
						api('diff',{id:snapId},function(e,r){
							if(!e&&r&&r.diff){
								var modal=document.getElementById('ocDiffMod');
								var body=document.getElementById('ocDiffBd');
								var toggleHtml='<div class="ocDiffToggle"><button class="ocDiffToggleBtn active" id="ocDiffUnifiedBtn">Unified</button><button class="ocDiffToggleBtn" id="ocDiffSplitBtn">Split</button></div>';
								var diffHtml='<pre style="font-size:12px;overflow-x:auto;padding:8px;background:var(--bgS);border-radius:var(--r);line-height:1.6">';
								var lines=r.diff.split('\n');
								lines.forEach(function(line,i){
									var cls='';
									if(line.substring(0,1)==='+')cls='color:var(--ok)';
									else if(line.substring(0,1)==='-')cls='color:var(--err)';
									else if(line.substring(0,2)==='@@')cls='color:var(--p);font-weight:600';
									diffHtml+='<div class="ocDiffHunkLine" data-line="'+(i+1)+'" style="'+cls+'">'+esc(line)+'</div>';
								});
								diffHtml+='</pre>';
								var splitHtml='<div id="ocDiffSplitView" style="display:none"><div class="ocDiffSplit"><div class="ocDiffSplitL"><div style="padding:4px 8px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);font-weight:500">Original</div></div><div class="ocDiffSplitR"><div style="padding:4px 8px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);font-weight:500">Modified</div></div></div></div>';
								body.innerHTML='<div style="margin-bottom:8px;font-size:13px;color:var(--t1);font-weight:500">'+esc(snapMsg||'Snapshot')+'</div>'+toggleHtml+'<div id="ocDiffUnifiedView">'+diffHtml+'</div>'+splitHtml;
								modal.classList.add('open');
								document.getElementById('ocDiffUnifiedBtn').addEventListener('click',function(){
									this.classList.add('active');document.getElementById('ocDiffSplitBtn').classList.remove('active');
									document.getElementById('ocDiffUnifiedView').style.display='block';
									document.getElementById('ocDiffSplitView').style.display='none';
								});
								document.getElementById('ocDiffSplitBtn').addEventListener('click',function(){
									this.classList.add('active');document.getElementById('ocDiffUnifiedBtn').classList.remove('active');
									document.getElementById('ocDiffUnifiedView').style.display='none';
									document.getElementById('ocDiffSplitView').style.display='block';
									renderSplitDiff(r.diff);
								});
								showDiffHunkNav(body,r.diff);
							}else{
								toast('Failed to load diff: '+(e||'Unknown'),'err');
							}
						});
					});
				});
			}
		});
	}
	panel.classList.toggle('open',chgOpen);
});
document.getElementById('ocChgC').addEventListener('click',function(){
	chgOpen=false;
	document.getElementById('ocChg').classList.remove('open');
});

// Auto-title generation after streaming - triggered in 'done' handler

// Command Palette
var cmdPaletteCommands=[
	{cmd:'New Session',key:'Ctrl+Shift+N',action:function(){newS()}},
	{cmd:'Plan Mode',key:'Ctrl+Shift+P',action:function(){document.getElementById('ocMode').value='plan';api('set_mode',{mode:'plan'},function(){toast('Plan mode','ok')})}},
	{cmd:'Build Mode',key:'',action:function(){document.getElementById('ocMode').value='build';api('set_mode',{mode:'build'},function(){toast('Build mode','ok')})}},
	{cmd:'Settings',key:'Ctrl+,',action:function(){document.getElementById('ocSetMod').classList.add('open');loadP()}},
	{cmd:'Toggle Files',key:'',action:function(){document.getElementById('ocFtBtn').click()}},
	{cmd:'Toggle Sidebar',key:'',action:function(){document.getElementById('ocTogSb').click()}},
	{cmd:'Toggle Theme',key:'',action:function(){document.getElementById('ocThBtn').click()}},
	{cmd:'Compact Conversation',key:'/compact',action:function(){
		st('Compacting...',true);
		api('compact',{keep_last_n:3},function(e,r){
			if(!e&&r&&r.success){rMsgs(r.messages||[]);if(r.summary){toast('Nothing to compact','warn')}else{toast('Compacted','ok')}st('Ready',true)}
			else{toast('Failed','err');st('Ready',true)}
		});
	}},
	{cmd:'Clear Conversation',key:'/clear',action:function(){api('clear',{},function(){rMsgs([]);st('Cleared',true)})}},
	{cmd:'Export as Markdown',key:'/export',action:function(){exportChat()}},
	{cmd:'Undo Last Message',key:'/undo',action:function(){
		if(!rMsgs._lastMsgs||!rMsgs._lastMsgs.length){toast('Nothing to undo','warn');return}
		var lu=null;for(var i=rMsgs._lastMsgs.length-1;i>=0;i--){if(rMsgs._lastMsgs[i].role==='user'){lu=rMsgs._lastMsgs[i];break}}
		if(!lu||!lu.id){toast('Cannot undo','warn');return}
		api('undo',{message_id:lu.id},function(e,r){if(!e&&r&&r.success){rMsgs(r.messages||[]);toast('Undone','ok');st('Ready',true);loadSL()}else{toast('Failed','err');st('Ready',true)}});
	}},
	{cmd:'Fork Conversation',key:'/fork',action:function(){
		if(!rMsgs._lastMsgs||!rMsgs._lastMsgs.length){toast('Nothing to fork','warn');return}
		var lu=null;for(var i=rMsgs._lastMsgs.length-1;i>=0;i--){if(rMsgs._lastMsgs[i].role==='user'){lu=rMsgs._lastMsgs[i];break}}
		if(!lu||!lu.id){toast('Cannot fork','warn');return}
		api('fork',{message_id:lu.id},function(e,r){
			if(!e&&r&&r.success){
				aC=r.conversation_id;
				updateURL(aC);
				toast('Forked','ok');
				st('Ready',true);
				loadSL();
				loadC(aC);
				if(r.forked_content){
					var inp=document.getElementById('ocIn');
					inp.value=r.forked_content;
					inp.focus();
				}
			}else{toast('Failed','err');st('Ready',true)}
		});
	}},
	{cmd:'Rename Session',key:'/rename',action:function(){
		var t=prompt('Enter new session title:',curSessionTitle());
		if(t!==null&&t.trim()){api('rename_conversation',{conversation_id:aC,title:t.trim()},function(e,r){if(!e&&r&&r.success){toast('Renamed','ok');loadSL()}else{toast('Failed','err')}})}
	}},
	{cmd:'Help & FAQ',key:'',action:function(){document.getElementById('ocHlBtn').click()}}
];

// Command palette is handled by _origCmdCommands and renderCmdList below
document.getElementById('ocCmdP').addEventListener('click',function(e){if(e.target===this)this.classList.remove('open')});

// Model Favorites
var favModels=[];
function loadFavModels(){
	api('settings_load',null,function(e,d){
		if(!e&&d&&d.favorite_models){
			favModels=d.favorite_models;
			refreshModelFavs();
		}
	});
}
function refreshModelFavs(){
	if(_providersCache){
		renderModelSelect(_providersCache);
		renderModelDropdown(_providersCache);
		syncModelBtn();
		syncModelDropdownSelection();
	}
}
function toggleFavModel(modelId){
	var idx=favModels.indexOf(modelId);
	if(idx===-1){
		favModels.push(modelId);
		api('favorite_model',{model_id:modelId},function(){});
		toast('Added to favorites','ok');
	}else{
		favModels.splice(idx,1);
		api('unfavorite_model',{model_id:modelId},function(){});
		toast('Removed from favorites','info');
	}
}

// Permission System
var permConfig={};
var permResolve=null;
function askPermission(permType,detail){
	var permKey=permType;
	if(permConfig[permKey]==='allow_always')return Promise.resolve(true);
	if(permConfig[permKey]==='deny')return Promise.resolve(false);
	return new Promise(function(resolve){
		var mod=document.getElementById('ocPermMod');
		document.getElementById('ocPermTitle').textContent='Allow '+permType+'?';
		document.getElementById('ocPermSub').textContent=permType;
		var detailText='';
		if(typeof detail==='string')detailText=detail;
		else if(typeof detail==='object')detailText=JSON.stringify(detail,null,2);
		document.getElementById('ocPermDetail').textContent=detailText;
		var iconMap={write_file:'\uD83D\uDCDD',edit_file:'\u270F\uFE0F',bash:'\uD83D\uDCBB',php_eval:'\uD83D\uDCBB',php_lint:'\uD83D\uDCBB',file_download:'\u2B07\uFE0F'};
		document.getElementById('ocPermIcon').textContent=iconMap[permType]||'\uD83D\uDD12';
		mod.classList.add('open');
		var onOnce=function(){mod.classList.remove('open');cleanup();resolve(true)};
		var onAlways=function(){permConfig[permKey]='allow_always';mod.classList.remove('open');cleanup();resolve(true);api('toggle_permission',{permission:permKey,value:'allow_always'},function(){});}
		var onDeny=function(){mod.classList.remove('open');cleanup();resolve(false)};
		var cleanup=function(){
			document.getElementById('ocPermAllowOnce').removeEventListener('click',onOnce);
			document.getElementById('ocPermAllowAlways').removeEventListener('click',onAlways);
			document.getElementById('ocPermDeny').removeEventListener('click',onDeny);
		};
		document.getElementById('ocPermAllowOnce').addEventListener('click',onOnce);
		document.getElementById('ocPermAllowAlways').addEventListener('click',onAlways);
		document.getElementById('ocPermDeny').addEventListener('click',onDeny);
	});
}

// Session Rename (double-click sidebar item)
document.getElementById('ocSL').addEventListener('dblclick',function(e){
	var item=e.target.closest('.ocSi');
	if(!item||!item.dataset.id)return;
	var currentTitle=item.querySelector('.ocSiT');
	if(!currentTitle)return;
	var newTitle=prompt('Rename session:',currentTitle.textContent);
	if(newTitle!==null&&newTitle.trim()){
		api('rename_conversation',{conversation_id:item.dataset.id,title:newTitle.trim()},function(e,r){
			if(!e&&r&&r.success){
				currentTitle.textContent=newTitle.trim();
				toast('Renamed','ok');
			}else{
				toast('Failed to rename','err');
			}
		});
	}
});

// Load favorites on init
loadFavModels();

// Project Management
function loadProjects(force){
	if(_projectsCache&&!force){
		projects=_projectsCache.projects||[];
		wordpressInstalls=_projectsCache.wordpress||[];
		renderProjectList('');
		if(typeof loadSidebarProjects==='function') loadSidebarProjects();
		renderWpInstalls(_projectsCache.wordpress||[]);
		return;
	}
	api('projects_list',null,function(e,d){
		if(e||!d)return;
		projects=d;
		_projectsCache=_projectsCache||{};
		_projectsCache.projects=d;
		renderProjectList('');
		if(typeof loadSidebarProjects==='function') loadSidebarProjects();
	});
	api('projects_wordpress',null,function(e,d){
		if(e||!d)return;
		wordpressInstalls=d;
		_projectsCache=_projectsCache||{};
		_projectsCache.wordpress=d;
		renderWpInstalls(d);
	});
}
function renderWpInstalls(d){
		var sel=document.getElementById('ocProjWpSel');
		if(sel){
			sel.innerHTML='<option value="">Select installation...</option>';
			d.forEach(function(w){
				var o=document.createElement('option');
				o.value=w.insid;
				o.textContent=w.name+' - '+w.path;
				sel.appendChild(o);
			});
		}
}

function renderProjectList(query){
	var list=document.getElementById('ocProjList');
	if(!list)return;
	list.innerHTML='';
	if(!projects.length){
		list.innerHTML='<div style="padding:20px;text-align:center;color:var(--t3);font-size:13px">No projects yet. Create one to get started!</div>';
		return;
	}
	var q=(query||'').toLowerCase();
	var filtered=projects.filter(function(p){
		return !q||(p.name||'').toLowerCase().indexOf(q)!==-1||(p.path||'').toLowerCase().indexOf(q)!==-1;
	});
	if(!filtered.length){
		list.innerHTML='<div style="padding:20px;text-align:center;color:var(--t3);font-size:13px">No projects match your search.</div>';
		return;
	}
	filtered.forEach(function(p){
		var item=document.createElement('div');
		item.className='ocPi';
		item.dataset.id=p.project_id;
		item.style.cursor='pointer';
		item.innerHTML='<span class="ocPiN">'+esc(p.name||'Untitled')+'<span class="ocPiSub">'+esc(p.path||'')+'</span></span>'
			+'<span style="display:flex;align-items:center;gap:4px;flex-shrink:0">'
			+'<button class="ocPiClose" data-close="1" title="Close Project"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></button>'
			+'<button class="ocPiTrash" data-del="1" title="Delete Project"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>'
			+'</span>';
		item.addEventListener('click',function(ev){
			if(ev.target.closest('[data-close]')||ev.target.closest('[data-del]'))return;
			switchToProject(p.project_id);
		});
		item.querySelector('[data-close]').addEventListener('click',function(ev){
			ev.stopPropagation();
			if(confirm('Close project "'+(p.name||p.path)+'"?\n\nThis will remove the project from your project list. No conversations, sessions or context data will be deleted.')){
				api('projects_close',{project_id:p.project_id},function(e,r){
					if(!e&&r&&r.success){
						if(p.project_id===PID){
							var u=new URL(window.location);
							u.searchParams.delete('project_id');
							u.searchParams.delete('insid');
							u.searchParams.delete('path');
							u.searchParams.delete('session');
							window.location.href=u.toString();
						}else{
							loadProjects(true);
							toast('Project closed','ok');
						}
					}else{
						toast(r&&r.error?r.error:'Failed to close project','err');
					}
				});
			}
		});
		item.querySelector('[data-del]').addEventListener('click',function(ev){
			ev.stopPropagation();
			if(confirm('Delete project "'+(p.name||p.path)+'"?\n\nThis will permanently delete all conversations, sessions and context data for this project.')){
				api('projects_delete',{project_id:p.project_id},function(e,r){
					if(!e&&r&&r.success){
						if(p.project_id===PID){
							var u=new URL(window.location);
							u.searchParams.delete('project_id');
							u.searchParams.delete('insid');
							u.searchParams.delete('path');
							u.searchParams.delete('session');
							window.location.href=u.toString();
						}else{
							loadProjects(true);
							toast('Project deleted','ok');
						}
					}else{
						toast(r&&r.error?r.error:'Failed to delete project','err');
					}
				});
			}
		});
		list.appendChild(item);
	});
}

function switchToProject(projectId){
	var u=new URL(window.location);
	u.searchParams.set('project_id',projectId);
	u.searchParams.delete('insid');
	u.searchParams.delete('path');
	u.searchParams.delete('session');
	window.location.href=u.toString();
}

function openProjectModal(){
	document.getElementById('ocProjMod').classList.add('open');
	document.getElementById('ocProjSearch').value='';
	document.getElementById('ocProjNewForm').style.display='none';
	loadProjects();
}

function closeProjectModal(){
	document.getElementById('ocProjMod').classList.remove('open');
}

document.getElementById('ocProjBtn').addEventListener('click',openProjectModal);
document.getElementById('ocProjC').addEventListener('click',closeProjectModal);
document.getElementById('ocProjMod').addEventListener('click',function(e){
	if(e.target===this)closeProjectModal();
});

// ===== Project Context (bootstrap, state, memory) =====
var ocPctxTab='bootstrap';
var ocPctxInfoText={
	bootstrap:'Project description, architecture, coding standards, current milestone and development rules. Injected into every session so the AI never starts from zero. The AI will also update this file when you describe your project.',
	state:'Tracks the last completed task, current milestone/sprint, pending and blocked tasks. Refreshed automatically at the end of every session and when tasks are completed.',
	memory:'Durable decisions, lessons and facts extracted automatically after each session so they persist across conversations.'
};
function openPctxModal(tab){
	ocPctxTab=tab||'bootstrap';
	document.getElementById('ocPctxMod').classList.add('open');
	document.getElementById('ocPctxSaveSt').style.opacity='0';
	renderPctxTabs();
	loadPctx();
}
function closePctxModal(){
	document.getElementById('ocPctxMod').classList.remove('open');
}
function renderPctxTabs(){
	document.querySelectorAll('.ocPctxTab').forEach(function(btn){
		var active=(btn.getAttribute('data-pctx-tab')===ocPctxTab);
		btn.style.background=active?'var(--p)':'';
		btn.style.color=active?'#fff':'';
		btn.style.borderColor=active?'var(--p)':'';
	});
	var info=document.getElementById('ocPctxInfo');
	info.textContent=ocPctxInfoText[ocPctxTab]||'';
}
function loadPctx(){
	var action=ocPctxTab==='bootstrap'?'bootstrap_get':(ocPctxTab==='state'?'state_get':'memory_get');
	api(action,{},function(err,r){
		var ta=document.getElementById('ocPctxTxt');
		if(err||!r){ta.value='';return}
		ta.value=r.content||'';
	});
}
function savePctx(){
	var action=ocPctxTab==='bootstrap'?'bootstrap_save':(ocPctxTab==='state'?'state_save':'memory_save');
	var ta=document.getElementById('ocPctxTxt');
	api(action,{content:ta.value},function(err,r){
		var st=document.getElementById('ocPctxSaveSt');
		if(err||!r||!r.success){
			st.textContent='Failed to save';
			st.style.color='var(--err)';
		}else{
			st.textContent='Saved';
			st.style.color='var(--ok)';
		}
		st.style.opacity='1';
		setTimeout(function(){st.style.opacity='0'},2000);
	});
}
function clearPctx(){
	var label=ocPctxTab==='bootstrap'?'bootstrap':(ocPctxTab==='state'?'project state':'memory');
	if(!confirm('Clear '+label+' context file? This cannot be undone.'))return;
	var action=ocPctxTab==='bootstrap'?'bootstrap_clear':(ocPctxTab==='state'?'state_clear':'memory_clear');
	var ta=document.getElementById('ocPctxTxt');
	var st=document.getElementById('ocPctxSaveSt');
	api(action,{},function(err,r){
		if(err||!r||!r.success){
			st.textContent='Failed to clear';
			st.style.color='var(--err)';
			st.style.opacity='1';
		}else{
			ta.value='';
			st.textContent='Cleared';
			st.style.color='var(--ok)';
			st.style.opacity='1';
		}
		setTimeout(function(){st.style.opacity='0'},2000);
	});
}
document.getElementById('ocCtxBtn').addEventListener('click',function(){openPctxModal('bootstrap')});
document.getElementById('ocPctxC').addEventListener('click',closePctxModal);
document.getElementById('ocPctxMod').addEventListener('click',function(e){
	if(e.target===this)closePctxModal();
});
document.getElementById('ocPctxRefresh').addEventListener('click',loadPctx);
document.getElementById('ocPctxSave').addEventListener('click',savePctx);
document.getElementById('ocPctxClear').addEventListener('click',clearPctx);
document.querySelectorAll('.ocPctxTab').forEach(function(btn){
	btn.addEventListener('click',function(){
		ocPctxTab=this.getAttribute('data-pctx-tab');
		renderPctxTabs();
		loadPctx();
	});
});

// Help FAQ Popup
var faqs=[
{q:'What is the AI Assistant?',a:'The AI Assistant is a coding companion integrated into Softaculous that helps you write, debug, refactor, and understand code in your projects. It can read and write files, run shell commands, and search your codebase.'},
{q:'How do I get started?',a:'1. Create a project by clicking \"Projects\" then \"+ New Project\". You can select an existing WordPress installation or enter a custom directory path.\n2. Choose an AI model from the dropdown in the header (you need to connect a provider in Settings first).\n3. Type your request in the chat input and press Enter.\n4. The AI will respond and can perform actions like editing files or running commands.'},
{q:'What are Build and Plan modes?',a:'Build Mode: The AI has full access to read, write, edit files and run commands. Use this when you want the AI to make changes.\nPlan Mode: The AI operates in read-only mode - it can explore and analyze your code but cannot modify anything. Use this for code reviews, explanations, and planning changes before implementing them.'},
{q:'How do I connect an AI provider?',a:'1. Click the Settings button in the header.\n2. Find the provider you want to use and click \"Connect\".\n3. Enter your API key and click \"Connect\".\n4. You can use the \"Test\" button to verify your connection works.\n\nFree models are available through \"OpenCode Zen (Free)\" which requires no API key.'},
{q:'What AI providers are supported?',a:'OpenAI (GPT-4o, GPT-5), Anthropic (Claude), Google (Gemini), DeepSeek, Groq, Together AI, OpenRouter, Ollama (local), MiniMax, and custom OpenAI-compatible providers.'},
{q:'How do projects work?',a:'A project is linked to a directory on your server. The AI Assistant works within that project directory. Each project has its own chat sessions, conversation history, and file tree. You can switch between projects using the dropdown in the header, next to the sidebar toggle button.'},
{q:'How do sessions work?',a:'Each project can have multiple chat sessions. Click \"+ New Session\" to start a fresh conversation. Previous sessions are listed in the sidebar and can be clicked to resume. You can delete sessions with the \u00d7 button, and rename them by double-clicking.'},
{q:'What can the AI do?',a:'Read and write files, edit existing files with targeted changes, search files by name or content, run shell commands, create directory listings, and more. In Build mode it can make all these changes. In Plan mode it can only read and search.'},
{q:'How do I edit and resend a message?',a:'Hover over any user message and click the pencil icon. Edit the text inline and click \"Send\". The edited message will be sent as a new prompt without deleting any existing messages.'},
{q:'How do I use the command palette?',a:'Press Ctrl+K to open the command palette. Type to search for commands like \"New Session\", \"Plan Mode\", \"Compact\", \"Undo\", \"Fork\", \"Settings\", etc. Use arrow keys to navigate and Enter to execute.'},
{q:'What is conversation compaction?',a:'When your conversation gets long, use /compact to summarize the earlier parts and reduce token usage. This keeps the most recent messages intact while compressing the history. This helps stay within the models context window.'},
{q:'How do I undo a message?',a:'Type /undo or use Ctrl+K \u2192 \"Undo Last Message\" to remove the last user message and all responses after it. This is useful when you want to rephrase your question.'},
{q:'How do I fork a conversation?',a:'Type /fork or use Ctrl+K \u2192 \"Fork Conversation\" to create a new session from the current point. The forked session will contain all messages up to your last user message, allowing you to explore a different direction.'},
{q:'What are the keyboard shortcuts?',a:'All shortcuts can be customized via Settings \u2192 Keybindings. Defaults:\nEscape: Stop streaming / Close modals\nEnter: Send message (Shift+Enter for new line)\nAlt+Shift+N: New session\nAlt+Shift+P: Plan mode\nAlt+Shift+S: Settings\nCtrl+K: Command palette\nAlt+Shift+L: Toggle sidebar\nAlt+Shift+F: Toggle file tree\nAlt+Shift+T: Toggle tasks panel\nAlt+Shift+M: Toggle modified files\nAlt+1\u20269: Switch to session 1\u20139 (press a second digit within 500ms for 10+)\nUp (at start of input): Previous prompt in history\nDown (at end of input): Next prompt in history\nShell mode: Type !command to run shell commands\nQuestion mode: AI can ask you questions with selectable options'},
{q:'What are slash commands?',a:'Type / in the chat input to see available commands:\n/build - Switch to Build mode\n/plan - Switch to Plan mode\n/clear - Clear current conversation\n/compact - Summarize conversation to reduce context\n/undo - Undo last message pair\n/fork - Fork conversation from last message\n/rename - Rename this session\n/export - Download conversation as Markdown\n/todo <text> - Add a task item\n/sound - Toggle sound notifications\n/notify - Toggle desktop notifications\n/keybindings - Customize keyboard shortcuts\n/help - Show keyboard shortcuts\n\nShell mode: Type ! at the start to execute a shell command.\nExample: !ls -la or !git status\n\nYou can also type @build or @plan in your message to switch modes inline.'}
];
document.getElementById('ocHlBtn').addEventListener('click',function(){
	var bd=document.getElementById('ocHelpBd');
	bd.innerHTML='';
	var helpLabels={
		stop:'Stop streaming / Close modals',send:'Send message',newline:'New line',
		new_session:'New session',plan_mode:'Plan mode',build_mode:'Build mode',
		settings:'Settings',command_palette:'Command palette',toggle_sidebar:'Toggle sidebar',
		toggle_files:'Toggle file tree',toggle_todos:'Toggle tasks',
		toggle_modified:'Toggle modified files',keybindings:'Customize keybindings',
		sound:'Sound notifications',notifications:'Desktop notifications',
		clear:'Clear conversation',compact:'Compact conversation',
		export:'Export as Markdown',copy:'Copy transcript',help:'Help & FAQ',
		prev_prompt:'Previous prompt',next_prompt:'Next prompt'
	};
	var tbl=document.createElement('table');
	tbl.className='ocSCT';
	var rows='';
	Object.keys(_defaultKeybindings).forEach(function(k){
		var kb=getKeybinding(k);
		if(!kb||!helpLabels[k])return;
		rows+='<tr><td><kbd>'+esc(kb)+'</kbd></td><td>'+helpLabels[k]+'</td></tr>';
	});
	rows+='<tr><td><kbd>Alt+1\u20269</kbd> / <kbd>Alt+1\u202299</kbd></td><td>Switch to session (press second digit within 500ms for 10+</td></tr>';
	rows+='<tr><td><kbd>!</kbd></td><td>Shell mode prefix</td></tr>';
	tbl.innerHTML='<thead><tr><th>Shortcut</th><th>Action</th></tr></thead><tbody>'+rows+'</tbody>';
	bd.appendChild(tbl);
	var sep=document.createElement('div');
	sep.style.cssText='border-top:1px solid var(--bd2);margin:16px 0';
	bd.appendChild(sep);
	faqs.forEach(function(f){
		var item=document.createElement('div');
		item.className='ocFaqI';
		item.innerHTML='<div class="ocFaqQ">'+esc(f.q)+'</div><div class="ocFaqA">'+md(f.a)+'</div>';
		item.querySelector('.ocFaqQ').addEventListener('click',function(){
			var wasOpen=item.classList.contains('open');
			bd.querySelectorAll('.ocFaqI.open').forEach(function(el){el.classList.remove('open')});
			if(!wasOpen) item.classList.add('open');
		});
		bd.appendChild(item);
	});
	document.getElementById('ocHelpMod').classList.add('open');
});
document.getElementById('ocHelpC').addEventListener('click',function(){
	document.getElementById('ocHelpMod').classList.remove('open');
});
document.getElementById('ocHelpMod').addEventListener('click',function(e){
	if(e.target===this)this.classList.remove('open');
});
document.getElementById('ocProjSearch').addEventListener('input',function(){
	renderProjectList(this.value);
});
document.getElementById('ocProjNewBtn').addEventListener('click',function(){
	document.getElementById('ocProjNewForm').style.display='block';
	document.getElementById('ocProjName').value='';
	document.getElementById('ocProjPath').value='';
	document.getElementById('ocProjWpSel').value='';
	if(wordpressInstalls&&wordpressInstalls.length){
		document.getElementById('ocProjWpSection').style.display='block';
	}else{
		document.getElementById('ocProjWpSection').style.display='none';
	}
});
document.getElementById('ocProjCan').addEventListener('click',function(){
	document.getElementById('ocProjNewForm').style.display='none';
});
document.getElementById('ocProjWpSel').addEventListener('change',function(){
	if(this.value){
		var wp=wordpressInstalls.find(function(w){return w.insid===this.value},this);
		if(wp){
			var relPath=wp.path;
			if(relPath.indexOf(HD)===0 && HD.length>0) relPath=relPath.substring(HD.length+1);
			document.getElementById('ocProjPath').value=relPath;
			if(!document.getElementById('ocProjName').value){
				document.getElementById('ocProjName').value=wp.name;
			}
		}
	}
});
document.getElementById('ocProjCre').addEventListener('click',function(){
	var name=document.getElementById('ocProjName').value.trim();
	var path=document.getElementById('ocProjPath').value.trim();
	var insid=document.getElementById('ocProjWpSel').value;
	var fullPath=path;
	
	if(path.indexOf('/')!==0 && path.indexOf(':')=== -1){
		fullPath=HD+'/'+path;
	}
	var type='custom';
	if(insid){
		type='wordpress';
	}
	api('projects_create',{name:name,path:fullPath,type:type,insid:insid},function(e,r){
		if(!e&&r&&!r.error){
			closeProjectModal();
			if(r.project_id){
				switchToProject(r.project_id);
			}else{
				toast('Project created but failed to switch','warn');
				loadProjects(true);
			}
		}else{
			toast('Failed to create project: '+(r&&r.error||e),'err');
		}
	});
});

// If no softpath, auto-open project modal
if(!SP){
	setTimeout(openProjectModal,500);
}

// ========== Model Variants (Reasoning Effort) ==========
var modelVariants={
	'anthropic/claude-sonnet-4-20250514':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'anthropic/claude-opus-4-20250514':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'anthropic/claude-3-5-sonnet-20241022':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'openai/gpt-4o':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'openai/gpt-4o-mini':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'openai/gpt-4-turbo':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'google/gemini-2.5-pro':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'google/gemini-2.5-flash':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'deepseek/deepseek-chat':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'opencode_zen/deepseek-v4-flash-free':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'opencode_zen/mimo-v2.5-free':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'opencode_zen/minimax-m3-free':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'opencode_zen/nemotron-3-ultra-free':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'opencode_zen/north-mini-code-free':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'opencode_zen/qwen3.6-plus-free':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'ollama/llama3':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'ollama/codellama':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}],
	'ollama/mistral':[{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}]
};
var currentVariant={};

function getVariantsForModel(mv){
	if(!mv)return null;
	if(modelVariants[mv])return modelVariants[mv];
	var pp=mv.split('/');
	var prov=pp[0];
	var reasonProviders=['anthropic','openai','google','deepseek','opencode_zen'];
	if(reasonProviders.indexOf(prov)!==-1){
		return [{id:'default',label:'Default'},{id:'low',label:'Low'},{id:'medium',label:'Medium'},{id:'high',label:'High'}];
	}
	return null;
}

function updateVariantBadge(){
	var badge=document.getElementById('ocVariantBadge');
	var mv=document.getElementById('ocModel').value;
	var variants=getVariantsForModel(mv);
	var rsbVar=document.getElementById('ocRsbVar');
	if(!variants||variants.length<=1){
		badge.style.display='none';
		if(rsbVar){rsbVar.style.display='none'}
		return;
	}
	badge.style.display='inline-flex';
	if(rsbVar){rsbVar.style.display=''}
	var v=currentVariant[mv]||'default';
	var label=v==='default'?'Default':v.charAt(0).toUpperCase()+v.slice(1);
	badge.textContent=label;
	badge.className='ocVariantBadge'+(v!=='default'?' active':'');
	// Sync right sidebar variant select
	var rsbSel=document.getElementById('ocRsbVarSel');
	if(rsbSel){rsbSel.value=v}
}

document.getElementById('ocVariantBadge').addEventListener('click',function(e){
	e.stopPropagation();
	var mv=document.getElementById('ocModel').value;
	var variants=getVariantsForModel(mv);
	if(!variants)return;
	var drop=document.getElementById('ocVariantDrop');
	drop.innerHTML='';
	variants.forEach(function(v){
		var opt=document.createElement('div');
		opt.className='ocVariantOpt'+(v.id===(currentVariant[mv]||'default')?' active':'');
		opt.textContent=v.label;
		opt.addEventListener('click',function(){
		currentVariant[mv]=v.id;
		drop.classList.remove('open');
		updateVariantBadge();
		toast('Reasoning: '+v.label,'ok');
		});
		drop.appendChild(opt);
	});
	drop.classList.toggle('open');
});

document.addEventListener('click',function(e){
	var drop=document.getElementById('ocVariantDrop');
	if(drop&&!drop.contains(e.target)&&e.target.id!=='ocVariantBadge'){
		drop.classList.remove('open');
	}
});

document.getElementById('ocModel').addEventListener('change',function(){
	updateVariantBadge();
});

// Ctrl+Shift+V to cycle variants
document.addEventListener('keydown',function(e){
	if((e.ctrlKey||e.metaKey)&&e.shiftKey&&e.key==='V'){
		var mv=document.getElementById('ocModel').value;
		var variants=getVariantsForModel(mv);
		if(!variants||variants.length<=1)return;
		e.preventDefault();
		var current=currentVariant[mv]||'default';
		var idx=variants.findIndex(function(v){return v.id===current});
		var next=variants[(idx+1)%variants.length];
		currentVariant[mv]=next.id;
		updateVariantBadge();
		toast('Reasoning: '+next.label,'ok');
	}
});

updateVariantBadge();

// ========== Reasoning Toggle (Show/Collapse/Hide all) ==========
var reasonToggleState=localStorage.getItem('ocReasonToggle')||'show';
var reasonToggleBtn=document.getElementById('ocReasonToggle');

function updateReasonToggle(){
	if(!reasonToggleBtn)return;
	var blocks=document.querySelectorAll('.ocReason');
	var labels={show:'Show',collapse:'Collapse',hide:'Hide'};
	var next={show:'collapse',collapse:'hide',hide:'show'};
	if(blocks.length===0){
		// No reasoning blocks: keep button visible but dimmed/inactive
		reasonToggleBtn.style.display='inline-flex';
		reasonToggleBtn.style.opacity='0.4';
		reasonToggleBtn.style.pointerEvents='none';
		reasonToggleBtn.title='No reasoning blocks yet';
		reasonToggleBtn.setAttribute('data-app-hidden','1');
		return;
	}
	// Reasoning blocks exist: activate button
	reasonToggleBtn.removeAttribute('data-app-hidden');
	reasonToggleBtn.style.display='inline-flex';
	reasonToggleBtn.style.pointerEvents='auto';
	reasonToggleBtn.style.opacity=reasonToggleState==='hide'?'0.5':'1';
	blocks.forEach(function(b){
		if(reasonToggleState==='hide'){
			b.style.display='none';
		}else if(reasonToggleState==='collapse'){
			b.style.display='';
			b.classList.remove('open');
		}else{
			b.style.display='';
			b.classList.add('open');
		}
	});
	var txtEl=reasonToggleBtn.querySelector('.ocBtnTxt');
	if(txtEl)txtEl.textContent='Reasoning: '+labels[reasonToggleState];
	reasonToggleBtn.title='Click to '+next[reasonToggleState]+' reasoning';
}

if(reasonToggleBtn){
	reasonToggleBtn.addEventListener('click',function(){
		var blocks=document.querySelectorAll('.ocReason');
		if(!blocks.length)return;
		var next={show:'collapse',collapse:'hide',hide:'show'};
		reasonToggleState=next[reasonToggleState]||'show';
		localStorage.setItem('ocReasonToggle',reasonToggleState);
		updateReasonToggle();
	});
}

var origRDE=window.onReasonDeltaEnd||null;

// Observe new reasoning blocks added to DOM
var reasonObserver=new MutationObserver(function(mutations){
	var needUpdate=false;
	mutations.forEach(function(m){
		m.addedNodes.forEach(function(n){
			if(n.nodeType===1){
				if(n.classList&&n.classList.contains('ocReason')){
					needUpdate=true;
				}else if(n.querySelectorAll&&n.querySelectorAll('.ocReason').length){
					needUpdate=true;
				}
			}
		});
	});
	if(needUpdate)updateReasonToggle();
});
reasonObserver.observe(document.getElementById('ocCI')||document.body,{childList:true,subtree:true});

updateReasonToggle();

// ========== @-Mention Mode Selection ==========
var atItems=[
	{id:'build',label:'@build',desc:'Build mode (full access)',icon:'\uD83D\uDEE0\uFE0F'},
	{id:'plan',label:'@plan',desc:'Plan mode (read-only)',icon:'\uD83D\uDCD6'}
];
var atEl=document.getElementById('ocAt');
var atActive=false;
var atIdx=-1;
var atStart=-1;

function showAt(q){
	var lq=(q||'').toLowerCase();
	var items=atItems.filter(function(it){return it.label.toLowerCase().indexOf(lq)===0||it.desc.toLowerCase().indexOf(lq)!==-1});
	if(!items.length){hideAt();return}
	atEl.innerHTML=items.map(function(it,i){
		return '<div class="ocAtItem" data-idx="'+i+'" data-id="'+it.id+'">'
			+'<span class="ocAtItemIcon">'+it.icon+'</span>'
			+'<span>'+it.label+'</span>'
			+'<small>'+it.desc+'</small></div>';
	}).join('');
	atEl.classList.add('open');
	atActive=true;
	atIdx=-1;
	atEl.querySelectorAll('.ocAtItem').forEach(function(el){
		el.addEventListener('mousedown',function(ev){
			ev.preventDefault();
			pickAt(el.dataset.id);
		});
	});
}

function hideAt(){
	atEl.classList.remove('open');
	atActive=false;
	atIdx=-1;
	atStart=-1;
}

function pickAt(mode){
	var inp=document.getElementById('ocIn');
	if(atStart>=0){
		var before=inp.value.substring(0,atStart);
		var after=inp.value.substring(inp.selectionStart);
		inp.value=before+after;
		inp.selectionStart=inp.selectionEnd=before.length;
		autoResize(inp);
	}
	hideAt();
	document.getElementById('ocMode').value=mode;
	api('set_mode',{mode:mode},function(){
		toast('Switched to '+mode.charAt(0).toUpperCase()+mode.slice(1)+' mode','ok');
	});
	inp.focus();
}

document.getElementById('ocIn').addEventListener('input',function(){
	var v=this.value;
	var pos=this.selectionStart;
	var before=v.substring(0,pos);
	var atI=before.lastIndexOf('@');
	if(atI>=0&&(atI===0||/\s/.test(before[atI-1]))){
		var q=before.substring(atI+1);
		if(q.indexOf(' ')===-1){
			atStart=atI;
			showAt(q);
			return;
		}
	}
	hideAt();
});

document.getElementById('ocIn').addEventListener('keydown',function(e){
	if(!atActive)return;
	if(e.key==='ArrowDown'){
		e.preventDefault();
		var items=atEl.querySelectorAll('.ocAtItem');
		atIdx=Math.min(atIdx+1,items.length-1);
		items.forEach(function(el,i){el.classList.toggle('active',i===atIdx)});
	}else if(e.key==='ArrowUp'){
		e.preventDefault();
		var items=atEl.querySelectorAll('.ocAtItem');
		atIdx=Math.max(atIdx-1,0);
		items.forEach(function(el,i){el.classList.toggle('active',i===atIdx)});
	}else if(e.key==='Enter'||e.key==='Tab'){
		if(atIdx>=0){
			e.preventDefault();
			var items=atEl.querySelectorAll('.ocAtItem');
			if(items[atIdx])pickAt(items[atIdx].dataset.id);
		}else if(atActive){
			e.preventDefault();
			var first=atEl.querySelector('.ocAtItem');
			if(first)pickAt(first.dataset.id);
		}
	}else if(e.key==='Escape'){
		hideAt();
	}
});

document.getElementById('ocIn').addEventListener('blur',function(){
	setTimeout(hideAt,150);
});

// Ensure textarea resizes after paste/drop of large content
document.getElementById('ocIn').addEventListener('paste',function(){
	setTimeout(function(){autoResize(document.getElementById('ocIn'))},0);
});

// Shell Mode is handled directly in send() function above

// ===== FEATURE: Enhanced Sound Notifications =====
function _playSound(type){
	if(!_soundEnabled)return;
	try{
		if(!_audioCtx)_audioCtx=new(window.AudioContext||window.webkitAudioContext)();
		if(_audioCtx.state==='suspended')_audioCtx.resume();
		var t=_audioCtx.currentTime;
		function _note(freq,start,dur,vol){
			var o=_audioCtx.createOscillator(),g=_audioCtx.createGain();
			o.type='sine';o.frequency.value=freq;
			o.connect(g);g.connect(_audioCtx.destination);
			g.gain.setValueAtTime(0,t+start);
			g.gain.linearRampToValueAtTime(vol,t+start+0.015);
			g.gain.setValueAtTime(vol,t+start+dur*0.15);
			g.gain.exponentialRampToValueAtTime(0.001,t+start+dur);
			o.start(t+start);o.stop(t+start+dur+0.01);
		}
		if(type==='complete'){_note(880,0,0.12,0.2);_note(660,0.1,0.15,0.16)}
		else if(type==='error'){_note(330,0,0.2,0.25);_note(220,0.15,0.25,0.2)}
		else if(type==='question'){_note(523,0,0.1,0.15);_note(659,0.08,0.1,0.15);_note(784,0.16,0.12,0.15)}
	}catch(e){}
}

// ===== FEATURE: Desktop Notifications =====
function _desktopNotify(title,body,icon){
	if(!_desktopNotifEnabled)return;
	if(typeof Notification==='undefined')return;
	if(Notification.permission!=='granted')return;
	try{
		var n=new Notification(title,{body:body,icon:icon||'',tag:'ai-chat-'+Date.now()});
		n.onclick=function(){window.focus();n.close()};
	}catch(e){}
}
if(typeof Notification!=='undefined'&&Notification.permission==='default'){
	Notification.requestPermission();
}

// ===== FEATURE: Permission Prompt Wiring =====
var _permCb=null;
function showPermPrompt(title,sub,detail,toolName){
	return new Promise(function(resolve){
		document.getElementById('ocPermTitle').textContent=title||'Permission Required';
		document.getElementById('ocPermSub').textContent=sub||toolName||'';
		document.getElementById('ocPermDetail').textContent=detail||'';
		document.getElementById('ocPermIcon').textContent=toolName==='bash'?'\u{1F4BB}':'\u{1F512}';
		document.getElementById('ocPermMod').classList.add('open');
		_permCb=resolve;
	});
}
document.getElementById('ocPermDeny').addEventListener('click',function(){
	document.getElementById('ocPermMod').classList.remove('open');
	if(_permCb)_permCb('deny');_permCb=null;
});
document.getElementById('ocPermAllowOnce').addEventListener('click',function(){
	document.getElementById('ocPermMod').classList.remove('open');
	if(_permCb)_permCb('allow_once');_permCb=null;
});
document.getElementById('ocPermAllowAlways').addEventListener('click',function(){
	document.getElementById('ocPermMod').classList.remove('open');
	api('toggle_permission',{permission:document.getElementById('ocPermSub').textContent,value:'allow_always'},function(){});
	if(_permCb)_permCb('allow_always');_permCb=null;
});
document.getElementById('ocPermMod').addEventListener('click',function(e){
	if(e.target===this){this.classList.remove('open');if(_permCb)_permCb('deny');_permCb=null}
});

// ===== FEATURE: Multi-step Question/Prompt Flow =====
function showQuestion(qData){
	return new Promise(function(resolve){
		var chat=document.getElementById('ocCI');
		var el=document.createElement('div');
		el.className='ocMsg a';
		var optionsHtml='';
		if(qData.options&&qData.options.length){
			optionsHtml='<div class="ocQOptions">';
			qData.options.forEach(function(opt,i){
				var multi=qData.multiple?'data-multi="1"':'';
				optionsHtml+='<div class="ocQOpt" data-idx="'+i+'" '+multi+'>'+esc(opt.label||opt)+'</div>';
			});
			optionsHtml+='</div>';
		}
		var customHtml='';
		if(qData.custom!==false){
			customHtml='<div class="ocQCustom"><input type="text" id="ocQCustomIn" placeholder="'+(qData.customPlaceholder||'Type your answer...')+'"><button class="ocB ocBP ocBs" id="ocQCustomBtn">Submit</button></div>';
		}
		el.innerHTML='<div class="ocAv" style="background:var(--p);color:#fff"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>'
			+'<div class="ocMb"><div class="ocQuestion"><div class="ocQHeader"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>Question</div><div class="ocQBody"><div class="ocQText">'+(qData.question||qData.header||'')+'</div>'+optionsHtml+customHtml+'</div></div></div>';
		chat.appendChild(el);
		document.getElementById('ocChat').scrollTop=1e9;
		_playSound('question');
		_desktopNotify('AI Question','The AI is asking you a question');
		var selected=[];
		el.querySelectorAll('.ocQOpt').forEach(function(opt){
			opt.addEventListener('click',function(){
				if(qData.multiple){
					this.classList.toggle('sel');
					selected=Array.from(el.querySelectorAll('.ocQOpt.sel')).map(function(o){return qData.options[parseInt(o.dataset.idx)]});
				}else{
					el.querySelectorAll('.ocQOpt').forEach(function(o){o.classList.remove('sel')});
					this.classList.add('sel');
					selected=[qData.options[parseInt(this.dataset.idx)]];
				}
			});
		});
		function submitAnswer(){
			var customIn=document.getElementById('ocQCustomIn');
			var customVal=customIn?customIn.value.trim():'';
			if(selected.length){
				resolve({answer:selected.map(function(s){return s.value||s.label||s}),type:'option'});
			}else if(customVal){
				resolve({answer:customVal,type:'custom'});
			}
			var btns=el.querySelectorAll('.ocQOpt');
			btns.forEach(function(b){b.style.pointerEvents='none';b.style.opacity='0.6'});
			var customArea=el.querySelector('.ocQCustom');
			if(customArea)customArea.style.display='none';
		}
		if(qData.options&&qData.options.length){
			el.querySelectorAll('.ocQOpt').forEach(function(opt){
				if(!qData.multiple){
					opt.addEventListener('click',function(){setTimeout(submitAnswer,150)});
				}
			});
		}
		var customBtn=document.getElementById('ocQCustomBtn');
		if(customBtn)customBtn.addEventListener('click',submitAnswer);
		var customInEl=document.getElementById('ocQCustomIn');
		if(customInEl)customInEl.addEventListener('keydown',function(e){if(e.key==='Enter')submitAnswer()});
	});
}

// ===== FEATURE: Todo List Panel =====
document.getElementById('ocTodoBtn').addEventListener('click',function(){
	var p=document.getElementById('ocTodoP');
	p.classList.toggle('open');
	if(p.classList.contains('open'))renderTodos();
});
document.getElementById('ocTodoC').addEventListener('click',function(){
	document.getElementById('ocTodoP').classList.remove('open');
});

function addTodo(text,priority,status){
	_todoItems.push({text:text,priority:priority||'medium',status:status||'pending',id:Date.now()+Math.random()});
	saveTodos();
	renderTodos();
}

function applyServerTodos(todos){
	if(!todos)return;
	_todoItems=(todos||[]).map(function(t){
		var st=(t.status==='completed'||t.status==='done')?'done':'pending';
		return {text:t.content||t.text||'',priority:t.priority||'medium',status:st,id:Date.now()+Math.random()};
	}).filter(function(t){return t.text});
	saveTodos();
	renderTodos();
}

function saveTodos(){
	try{localStorage.setItem('oc_todos_'+aC,JSON.stringify(_todoItems))}catch(e){}
}

function loadTodos(){
	try{
		var saved=localStorage.getItem('oc_todos_'+aC);
		if(saved){_todoItems=JSON.parse(saved);renderTodos()}
	}catch(e){}
}

function renderTodos(){
	var list=document.getElementById('ocTodoPL');
	if(!_todoItems.length){
		list.innerHTML='<div style="padding:20px;text-align:center;color:var(--t3);font-size:12px">No tasks yet. AI will create tasks as it works.</div>';
		return;
	}
	list.innerHTML=_todoItems.map(function(t,i){
		var done=t.status==='done';
		var priCol=t.priority==='high'?'var(--err)':t.priority==='low'?'var(--t3)':'var(--p)';
		return '<div class="ocTodoI'+(done?' done':'')+'">'
			+'<div class="ocTodoChk'+(done?' done':'')+'" data-idx="'+i+'"></div>'
			+'<div class="ocTodoTxt">'+(t.priority==='high'?'<span style="color:var(--err);font-weight:600">[HIGH] </span>':t.priority==='low'?'<span style="color:var(--t3)">[LOW] </span>':'')+esc(t.text)+'</div>'
			+'</div>';
	}).join('');
	list.querySelectorAll('.ocTodoChk').forEach(function(chk){
		chk.addEventListener('click',function(){
			var idx=parseInt(this.dataset.idx);
			_todoItems[idx].status=_todoItems[idx].status==='done'?'pending':'done';
			saveTodos();
			renderTodos();
		});
	});
}

// ===== FEATURE: Modified Files Panel =====
document.getElementById('ocModBtn').addEventListener('click',function(){
	var p=document.getElementById('ocFilesP');
	p.classList.toggle('open');
	if(p.classList.contains('open'))loadModifiedFiles();
});
document.getElementById('ocFilesC').addEventListener('click',function(){
	document.getElementById('ocFilesP').classList.remove('open');
});

function addModifiedFile(path,action,lines){
	// Skip system paths that aren't real project files
	if(path==='/dev/null'||path==='dev/null')return;
	// Normalize path - don't prepend SP if already absolute
	var fullPath=path.charAt(0)==='/'?path:SP+'/'+path;
	var existing=_modifiedFiles.find(function(f){return f.path===fullPath||f.path===path});
	if(existing){
		existing.action=action;
		if(lines)existing.lines=lines;
	}else{
		_modifiedFiles.push({path:fullPath,action:action,lines:lines||0});
	}
	renderModifiedFiles();
}

function loadModifiedFiles(){
	// Render in-session tracked files (in-memory tracking only)
	renderModifiedFiles();
}

function renderModifiedFiles(){
	var list=document.getElementById('ocFilesPL');
	if(!_modifiedFiles.length){
		list.innerHTML='<div style="padding:20px;text-align:center;color:var(--t3);font-size:12px">No modified files yet.<br><span style="font-size:11px">Files changed by AI will appear here.</span></div>';
		return;
	}
	// Deduplicate by path
	var seen={};
	var unique=[];
	_modifiedFiles.forEach(function(f){
		if(!seen[f.path]){seen[f.path]=true;unique.push(f)}
	});
	list.innerHTML=unique.map(function(f){
		var icon=f.action==='add'?'+':f.action==='delete'?'\u2212':'~';
		var cls=f.action==='add'?'add':f.action==='delete'?'del':'mod';
		var badge=f.action==='add'?'added':f.action==='delete'?'deleted':'modified';
		return '<div class="ocFilesI" data-path="'+esc(f.path)+'">'
			+'<span class="ocFilesIc '+cls+'">'+icon+'</span>'
			+'<span class="ocFilesN" title="'+esc(f.path)+'">'+esc(f.path)+'</span>'
			+'<span class="ocFilesB '+cls+'">'+badge+(f.lines?' ('+f.lines+'L)':'')+'</span>'
			+'</div>';
	}).join('');
	list.querySelectorAll('.ocFilesI').forEach(function(el){
		el.addEventListener('click',function(){
			api('read_file',{path:el.dataset.path},function(e,d){
				if(!e&&d&&d.content!==undefined){
					document.getElementById('ocFvT').textContent=el.dataset.path.replace(SP+'/','');
					document.getElementById('ocFvPre').textContent=d.content;
					document.getElementById('ocFv').classList.add('open');
				}
			});
		});
	});
}
// Diff modal close
document.getElementById('ocDiffC').addEventListener('click',function(){
	document.getElementById('ocDiffMod').classList.remove('open');
});
document.getElementById('ocDiffMod').addEventListener('click',function(e){
	if(e.target===this)this.classList.remove('open');
});

// ===== FEATURE: Configurable Keybindings =====
var _defaultKeybindings={
	new_session:'Alt+Shift+N',
	plan_mode:'Alt+Shift+P',
	build_mode:'Alt+Shift+B',
	settings:'Alt+Shift+S',
	command_palette:'Ctrl+K',
	toggle_sidebar:'Alt+Shift+L',
	toggle_files:'Alt+Shift+F',
	toggle_todos:'Alt+Shift+T',
	toggle_modified:'Alt+Shift+M',
	keybindings:'Alt+Shift+K',
	sound:'Alt+Shift+O',
	notifications:'Alt+Shift+D',
	clear:'Alt+Shift+X',
	compact:'Alt+Shift+C',
	export:'Alt+Shift+E',
	copy:'Alt+Shift+Y',
	help:'Alt+Shift+H',
	stop:'Escape',
	send:'Enter',
	newline:'Shift+Enter',
	prev_prompt:'Up',
	next_prompt:'Down'
};
// Migrate old keys to new defaults if user hasn't customized
(function(){var saved=localStorage.getItem('oc_keybindings');if(saved){try{var s=JSON.parse(saved);var olds=Object.keys(s).sort().join(',');var defs=Object.keys(_defaultKeybindings).sort().join(',');if(olds!==defs){localStorage.removeItem('oc_keybindings')}}catch(e){localStorage.removeItem('oc_keybindings')}}})();
_keybindings=JSON.parse(localStorage.getItem('oc_keybindings')||'null')||Object.assign({},_defaultKeybindings);

function renderKeybindings(){
	var bd=document.getElementById('ocKbBd');
	bd.innerHTML='';
	var labels={
		new_session:'New Session',plan_mode:'Plan Mode',build_mode:'Build Mode',settings:'Settings',command_palette:'Command Palette',
		toggle_sidebar:'Toggle Sidebar',toggle_files:'Toggle Files',toggle_todos:'Toggle Tasks',
		toggle_modified:'Toggle Modified Files',keybindings:'Keybindings',sound:'Sound Notifications',
		notifications:'Desktop Notifications',clear:'Clear Conversation',compact:'Compact Conversation',
		export:'Export as Markdown',copy:'Copy Transcript',help:'Help',stop:'Stop / Close Modals',
		send:'Send Message',newline:'New Line',prev_prompt:'Previous Prompt',next_prompt:'Next Prompt'
	};
	var keys=Object.keys(_defaultKeybindings);
	keys.forEach(function(k){
		var row=document.createElement('div');
		row.className='ocKbRow';
		var kb=_keybindings[k]||_defaultKeybindings[k];
		var parts=kb.split('+');
		row.innerHTML='<div class="ocKbLabel">'+(labels[k]||k)+'</div><div class="ocKbKey">'+parts.map(function(p){return '<kbd>'+esc(p)+'</kbd>'}).join(' + ')+'</div><button class="ocKbEdit" data-key="'+k+'">Edit</button>';
		bd.appendChild(row);
	});
	bd.querySelectorAll('.ocKbEdit').forEach(function(btn){
		btn.addEventListener('click',function(){
			var key=this.dataset.key;
			this.textContent='Press keys...';
			this.style.background='var(--p)';this.style.color='#fff';
			var self=this;
			function handler(e){
				e.preventDefault();e.stopPropagation();
				var combo=[];
				if(e.ctrlKey||e.metaKey)combo.push('Ctrl');
				if(e.shiftKey)combo.push('Shift');
				if(e.altKey)combo.push('Alt');
				var k2=e.key;
				if(k2==='Control'||k2==='Shift'||k2==='Alt'||k2==='Meta')return;
				if(k2===' ')k2='Space';
				combo.push(k2.length===1?k2.toUpperCase():k2);
				_keybindings[key]=combo.join('+');
				localStorage.setItem('oc_keybindings',JSON.stringify(_keybindings));
				self.removeEventListener('keydown',handler);
				renderKeybindings();
			}
			this.addEventListener('keydown',handler);
			this.focus();
		});
	});
	var resetBtn=document.createElement('button');
	resetBtn.className='ocB';
	resetBtn.textContent='Reset to Defaults';
	resetBtn.style.cssText='margin-top:16px';
	resetBtn.addEventListener('click',function(){
		_keybindings=Object.assign({},_defaultKeybindings);
		localStorage.setItem('oc_keybindings',JSON.stringify(_keybindings));
		renderKeybindings();
	});
	bd.appendChild(resetBtn);
}

document.getElementById('ocKbC').addEventListener('click',function(){
	document.getElementById('ocKbMod').classList.remove('open');
});
document.getElementById('ocKbMod').addEventListener('click',function(e){
	if(e.target===this)this.classList.remove('open');
});

// ===== FEATURE: Subagent/Task System =====
function launchTask(taskDesc,taskType){
	var chat=document.getElementById('ocCI');
	var el=document.createElement('div');
	el.className='ocMsg a';
	var taskId=Date.now();
	el.innerHTML='<div class="ocAv" style="background:var(--s);color:#fff"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>'
		+'<div class="ocMb"><div class="ocTaskP"><div class="ocTaskPH"><span class="ocTaskSt running"></span><span class="ocTaskNm">'+esc(taskDesc.substring(0,80))+'</span><span style="color:var(--p);font-size:10px"><span class="ocTaskSpin"></span>Running</span></div><div class="ocTaskR" id="ocTask_'+taskId+'"></div></div></div>';
	chat.appendChild(el);
	document.getElementById('ocChat').scrollTop=1e9;
	return taskId;
}

function completeTask(taskId,result,isErr){
	var el=document.getElementById('ocTask_'+taskId);
	if(!el)return;
	var parent=el.closest('.ocTaskP');
	parent.classList.add('open');
	var statusSpan=parent.querySelector('span:last-child');
	var statusDot=parent.querySelector('.ocTaskSt');
	statusDot.classList.remove('running');
	statusDot.classList.add(isErr?'err':'done');
	statusSpan.innerHTML=isErr?'<span style="color:var(--err)">Error</span>':'<span style="color:var(--ok)">Done</span>';
	el.textContent=result||'Completed';
}

// ===== FEATURE: Plugin System =====
var _plugins=[];
function registerPlugin(plugin){
	if(!plugin||!plugin.id)return;
	_plugins.push(plugin);
	if(plugin.onRegister)plugin.onRegister();
}

// Hook: add slash command for shell mode
var _origSlashCmds=true;
(function(){
	var origSend=send;
})();

// ===== Enhanced event: extract todos and modified files from stream =====
var _origRMsg2=rMsg;
rMsg=function(msg){
	var el=_origRMsg2(msg);
	if(msg.role==='asst'&&msg.content){
		var content=msg.content;
		var todoMatches=content.match(/(?:^|\n)\s*[-*]\s+(.{2,})/g);
		if(todoMatches&&todoMatches.length>0){
			todoMatches.forEach(function(t){
				var text=t.replace(/^\s*[-*]\s+/,'').trim();
				if(text.length>3&&text.length<200&&!_todoItems.find(function(x){return x.text===text})){
					addTodo(text,'medium','pending');
				}
			});
		}
	}
	return el;
};

// ===== Wire up Keybindings button (in settings) =====

// ===== Hook: Track modified files from tool calls =====
var _origStreamOnProgress=null;
(function(){
	var origXHROpen=XMLHttpRequest.prototype.open;
	XMLHttpRequest.prototype.open=function(method,url){
		this._isAIStream=url&&url.indexOf('ai_chat_stream=1')!==-1;
		return origXHROpen.apply(this,arguments);
	};
})();

// ===== Enhanced slash commands =====
var _origSendHandler=document.getElementById('ocSend').onclick;
var _slashCommands={
	shell:{desc:'Execute a shell command (prefix: !)',action:null},
	todo:{desc:'Add a task item',action:function(args){
		if(!args)return toast('Usage: /todo <task text>','warn');
		addTodo(args,'medium','pending');
		toast('Task added','ok');
	}},
	keybindings:{desc:'Customize keyboard shortcuts',action:function(){
		renderKeybindings();
		document.getElementById('ocKbMod').classList.add('open');
	}},
	sound:{desc:'Toggle sound notifications (on/off)',action:function(){
		_soundEnabled=!_soundEnabled;
		toast('Sound Notifications - '+(_soundEnabled?'ON':'OFF'),'ok');
		localStorage.setItem('oc_sound_enabled',_soundEnabled?'1':'0');
		applySndIcon(_soundEnabled);
	}},
	notify:{desc:'Toggle desktop notifications',action:function(){
		if(typeof Notification==='undefined'){toast('Notifications not supported','warn');return}
		if(Notification.permission==='granted'){
			_desktopNotifEnabled=!_desktopNotifEnabled;
			applyNtfIcon(_desktopNotifEnabled);
			toast('Desktop Notifications - '+(_desktopNotifEnabled?'ON':'OFF'),'ok');
		}else if(Notification.permission==='default'){
			Notification.requestPermission(function(p){
				if(p==='granted'){_desktopNotifEnabled=true;toast('Desktop Notifications - ON','ok')}
				else{_desktopNotifEnabled=false;toast('Notification permission denied','warn')}
				applyNtfIcon(_desktopNotifEnabled);
			});
		}else{
			_desktopNotifEnabled=false;
			applyNtfIcon(false);
			toast('Notifications blocked in browser settings','warn');
		}
	}}
};

// Load sound preference
_soundEnabled=localStorage.getItem('oc_sound_enabled')!=='0';
_desktopNotifEnabled=typeof Notification!=='undefined'&&Notification.permission==='granted';
// Re-apply icon states now that preferences are loaded
applySndIcon(_soundEnabled);
applyNtfIcon(_desktopNotifEnabled);

// ===== Enhanced sound on stream complete =====
var _origDoneHandler=null;

// ===== Track modified files from stream =====

// ===== Update slash hints with new commands =====
var _origSlashItems=[
	{cmd:'/build',desc:'Switch to Build mode'},
	{cmd:'/plan',desc:'Switch to Plan mode'},
	{cmd:'/clear',desc:'Clear conversation'},
	{cmd:'/compact',desc:'Summarize to reduce context'},
	{cmd:'/undo',desc:'Undo last message pair'},
	{cmd:'/redo',desc:'Redo last undone message'},
	{cmd:'/fork',desc:'Fork conversation'},
	{cmd:'/rename',desc:'Rename this session'},
	{cmd:'/export',desc:'Download as Markdown'},
	{cmd:'/copy',desc:'Copy transcript to clipboard'},
	{cmd:'/help',desc:'Show keyboard shortcuts'},
	{cmd:'/todo <text>',desc:'Add a task item'},
	{cmd:'/sound',desc:'Toggle sound notifications'},
	{cmd:'/notify',desc:'Toggle desktop notifications'},
	{cmd:'/keybindings',desc:'Customize shortcuts'},
	{cmd:'/timeline',desc:'Toggle message timeline'}
];
// Replace the built-in slash command list
var _origShowSlashHint=null;

// Override command palette commands to include new features
var _origCmdCommands=[
	{kb:'new_session',cmd:'New Session',desc:'Start a new conversation',action:function(){newS()}},
	{kb:'plan_mode',cmd:'Plan Mode',desc:'Switch to read-only plan mode',action:function(){document.getElementById('ocMode').value='plan';api('set_mode',{mode:'plan'},function(){})}},
	{kb:'build_mode',cmd:'Build Mode',desc:'Switch to build mode (read+write)',action:function(){document.getElementById('ocMode').value='build';api('set_mode',{mode:'build'},function(){})}},
	{kb:'toggle_sidebar',cmd:'Toggle Sidebar',desc:'Show/hide session history',action:function(){document.getElementById('ocSb').classList.toggle('hid')}},
	{kb:'toggle_files',cmd:'Toggle Files',desc:'Show/hide file tree panel',action:function(){document.getElementById('ocFtBtn').click()}},
	{kb:'toggle_todos',cmd:'Toggle Tasks',desc:'Show/hide tasks panel',action:function(){document.getElementById('ocTodoP').classList.toggle('open')}},
	{kb:'toggle_modified',cmd:'Toggle Modified Files',desc:'Show/hide modified files panel',action:function(){var _fp2=document.getElementById('ocFilesP');_fp2.classList.toggle('open');if(_fp2.classList.contains('open'))loadModifiedFiles()}},
	{kb:'settings',cmd:'Settings',desc:'Open provider and permission settings',action:function(){document.getElementById('ocSetMod').classList.add('open');loadP()}},
	{kb:'keybindings',cmd:'Keybindings',desc:'View and customize keyboard shortcuts',action:function(){renderKeybindings();document.getElementById('ocKbMod').classList.add('open')}},
	{kb:'sound',cmd:'Sound Notifications',desc:'Toggle audio alerts on/off',action:function(){_soundEnabled=!_soundEnabled;localStorage.setItem('oc_sound_enabled',_soundEnabled?'1':'0');applySndIcon(_soundEnabled);toast('Sound Notifications - '+(_soundEnabled?'ON':'OFF'),'ok')}},
	{kb:'notifications',cmd:'Desktop Notifications',desc:'Toggle desktop notification popups',action:function(){
		if(typeof Notification!=='undefined'){
			if(Notification.permission==='default')Notification.requestPermission(function(p){_desktopNotifEnabled=(p==='granted');applyNtfIcon(_desktopNotifEnabled)});
			else{_desktopNotifEnabled=!_desktopNotifEnabled;applyNtfIcon(_desktopNotifEnabled);toast('Desktop Notifications - '+(_desktopNotifEnabled?'ON':'OFF'),'ok')}
		}
	}},
	{kb:'clear',cmd:'Clear Conversation',desc:'Delete all messages in this session',action:function(){api('clear',{},function(){rMsgs([]);st('Cleared',true)})}},
	{kb:'compact',cmd:'Compact Conversation',desc:'Summarize history to free context space',action:function(){st('Compacting...',true);api('compact',{keep_last_n:3},function(e,r){if(!e&&r&&r.success){rMsgs(r.messages||[]);if(r.summary){toast('Nothing to compact','warn')}else{toast('Compacted','ok')}st('Ready',true)}else{toast('Failed','err');st('Ready',true)}})}},
	{kb:'export',cmd:'Export as Markdown',desc:'Download conversation with export options',action:function(){showExportDialog()}},
	{kb:'copy',cmd:'Copy Transcript',desc:'Copy conversation to clipboard',action:function(){copyTranscript()}},
	{kb:'help',cmd:'Help',desc:'Show keyboard shortcuts and FAQ',action:function(){document.getElementById('ocHlBtn').click()}}
];

// Patch the command palette to include new commands
function toggleCmdPalette(){
	var p=document.getElementById('ocCmdP');
	if(p.classList.contains('open')){p.classList.remove('open');return}
	var inp=document.getElementById('ocCmdIn');
	inp.value='';
	renderCmdList('');
	p.classList.add('open');
	setTimeout(function(){inp.focus()},50);
}
function renderCmdList(q){
	var list=document.getElementById('ocCmdL');
	list.innerHTML='';
	var lq=(q||'').toLowerCase();
	_origCmdCommands.filter(function(c){return !lq||c.cmd.toLowerCase().indexOf(lq)!==-1||(c.desc&&c.desc.toLowerCase().indexOf(lq)!==-1)}).forEach(function(c,i){
		var dynKey=c.kb?getKeybinding(c.kb):'';
		var div=document.createElement('div');
		div.className='ocCmdIt';
		div.innerHTML=(dynKey?'<span class="ocCmdItK">'+esc(dynKey)+'</span>':'<span class="ocCmdItK empty"></span>')+'<div style="flex:1;min-width:0"><div class="ocCmdItN">'+esc(c.cmd)+'</div>'+(c.desc?'<div class="ocCmdItD">'+esc(c.desc)+'</div>':'')+'</div>';
		div.addEventListener('click',function(){
			document.getElementById('ocCmdP').classList.remove('open');
			c.action();
		});
		div.addEventListener('mouseenter',function(){
			list.querySelectorAll('.ocCmdIt').forEach(function(el){el.classList.remove('act')});
			div.classList.add('act');
		});
		list.appendChild(div);
	});
}
document.getElementById('ocCmdIn').addEventListener('input',function(){
	renderCmdList(this.value);
});
document.getElementById('ocCmdIn').addEventListener('keydown',function(e){
	if(e.key==='Escape'){document.getElementById('ocCmdP').classList.remove('open');return}
	if(e.key==='Enter'){
		var act=list.querySelector('.ocCmdIt.act')||list.querySelector('.ocCmdIt');
		if(act)act.click();
		return;
	}
	var items=Array.from(list.querySelectorAll('.ocCmdIt'));
	var idx=items.findIndex(function(el){return el.classList.contains('act')});
	if(e.key==='ArrowDown'){
		e.preventDefault();
		if(idx<items.length-1){items[idx].classList.remove('act');items[idx+1].classList.add('act');items[idx+1].scrollIntoView({block:'nearest'})}
	}else if(e.key==='ArrowUp'){
		e.preventDefault();
		if(idx>0){items[idx].classList.remove('act');items[idx-1].classList.add('act');items[idx-1].scrollIntoView({block:'nearest'})}
	}
});
var list=document.getElementById('ocCmdL');

// ===== FEATURE: Session Timeline =====
var _timelineOpen=false;
function renderTimeline(){
	var list=document.getElementById('ocTimelineL');
	if(!list)return;
	var msgs=document.querySelectorAll('#ocCI .ocMsg');
	var html='';
	var userIdx=0;
	msgs.forEach(function(msg,i){
		if(msg.classList.contains('u')){
			userIdx++;
			var txt=msg.querySelector('.ocMbb');
			var preview=txt?txt.textContent.substring(0,60):'';
			html+='<div class="ocTimelineI" data-idx="'+i+'"><span class="ocTimelineN">#'+userIdx+'</span>'+esc(preview)+'</div>';
		}
	});
	list.innerHTML=html||'<div style="padding:14px;color:var(--t3);font-size:12px;text-align:center">No messages yet</div>';
	list.querySelectorAll('.ocTimelineI').forEach(function(el){
		el.addEventListener('click',function(){
			var idx=parseInt(this.dataset.idx);
			var msgs2=document.querySelectorAll('#ocCI .ocMsg');
			if(msgs2[idx]){
				msgs2[idx].scrollIntoView({behavior:'smooth',block:'center'});
				msgs2[idx].style.transition='background .3s';
				msgs2[idx].style.background='rgba(59,130,246,.08)';
				setTimeout(function(){msgs2[idx].style.background=''},1000);
			}
		});
	});
}
document.getElementById('ocTimelineToggle').addEventListener('click',function(){
	_timelineOpen=!_timelineOpen;
	document.getElementById('ocTimeline').classList.toggle('open',_timelineOpen);
	if(_timelineOpen)renderTimeline();
});
var _origRMsgs2=rMsgs;
rMsgs=function(msgs){
	rMsgs._lastMsgs=msgs||[];
	_origRMsgs2(msgs);
	if(_timelineOpen)renderTimeline();
};

// ===== FEATURE: Inline Diff Viewer (side-by-side toggle) =====
var _diffSplitMode=false;
function showDiffViewer(filePath,oldContent,newContent){
	var modal=document.getElementById('ocDiffMod');
	if(!modal)return;
	var body=document.getElementById('ocDiffBd');
	if(!body)return;
	var oldLines=oldContent.split('\n');
	var newLines=newContent.split('\n');
	var maxLen=Math.max(oldLines.length,newLines.length);
	var unifiedHtml='<div class="ocDiffToggle"><button class="ocDiffToggleBtn active" onclick="window._diffMode(false)">Unified</button><button class="ocDiffToggleBtn" onclick="window._diffMode(true)">Split</button></div>';
	unifiedHtml+='<div id="ocDiffUnified"><pre style="font-size:12px;overflow-x:auto;padding:8px;background:var(--bgS);border-radius:var(--r)">';
	for(var i=0;i<maxLen;i++){
		var o=oldLines[i]||'',n=newLines[i]||'';
		if(o===n){
			unifiedHtml+='  '+esc(n)+'\n';
		}else{
			if(o)unifiedHtml+='<span style="color:var(--err)">- '+esc(o)+'</span>\n';
			if(n)unifiedHtml+='<span style="color:var(--ok)">+ '+esc(n)+'</span>\n';
		}
	}
	unifiedHtml+='</pre></div>';
	var splitHtml='<div id="ocDiffSplit" style="display:none"><div class="ocDiffSplit"><div class="ocDiffSplitL"><div style="padding:4px 8px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);font-weight:500">Original</div>';
	for(var i=0;i<oldLines.length;i++){
		splitHtml+='<div class="ocDiffSplitRow"><span class="ocDiffLN">'+(i+1)+'</span><span class="ocDiffLC">'+esc(oldLines[i])+'</span></div>';
	}
	splitHtml+='</div><div class="ocDiffSplitR"><div style="padding:4px 8px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);font-weight:500">Modified</div>';
	for(var i=0;i<newLines.length;i++){
		splitHtml+='<div class="ocDiffSplitRow"><span class="ocDiffLN">'+(i+1)+'</span><span class="ocDiffLC">'+esc(newLines[i])+'</span></div>';
	}
	splitHtml+='</div></div></div>';
	body.innerHTML='<div style="margin-bottom:8px;font-size:13px;color:var(--t1);font-weight:500">'+esc(filePath)+'</div>'+unifiedHtml+splitHtml;
	modal.classList.add('open');
}
window._diffMode=function(split){
	var u=document.getElementById('ocDiffUnified');
	var s=document.getElementById('ocDiffSplit');
	var btns=document.querySelectorAll('.ocDiffToggleBtn');
	if(split){
		if(u)u.style.display='none';
		if(s)s.style.display='block';
		btns[0].classList.remove('active');btns[1].classList.add('active');
	}else{
		if(u)u.style.display='block';
		if(s)s.style.display='none';
		btns[0].classList.add('active');btns[1].classList.remove('active');
	}
};

// ===== FEATURE: Single-Patch Navigation (hunk-by-hunk) =====
var _diffHunks=[];
var _diffHunkIdx=0;
function parseDiffHunks(diffText){
	var hunks=[];
	var lines=diffText.split('\n');
	var currentHunk=null;
	for(var i=0;i<lines.length;i++){
		if(lines[i].substring(0,2)==='@@'){
			if(currentHunk)hunks.push(currentHunk);
			currentHunk={header:lines[i],lines:[],startLine:i};
		}else if(currentHunk){
			currentHunk.lines.push(lines[i]);
		}
	}
	if(currentHunk)hunks.push(currentHunk);
	return hunks;
}
function showDiffHunkNav(container,diffText){
	_diffHunks=parseDiffHunks(diffText);
	_diffHunkIdx=0;
	if(_diffHunks.length===0)return;
	var nav=document.createElement('div');
	nav.className='ocDiffHunkNav';
	nav.id='ocDiffHunkNav';
	nav.innerHTML='<button class="ocDiffHunkBtn" id="ocDiffHunkPrev">‹ Prev</button><span id="ocDiffHunkPos">Hunk 1/'+_diffHunks.length+'</span><button class="ocDiffHunkBtn" id="ocDiffHunkNext">Next ›</button>';
	container.insertBefore(nav,container.firstChild);
	document.getElementById('ocDiffHunkPrev').addEventListener('click',function(){navHunk(-1)});
	document.getElementById('ocDiffHunkNext').addEventListener('click',function(){navHunk(1)});
	highlightHunk();
}
function navHunk(dir){
	_diffHunkIdx=Math.max(0,Math.min(_diffHunkIdx+dir,_diffHunks.length-1));
	highlightHunk();
}
function highlightHunk(){
	var pos=document.getElementById('ocDiffHunkPos');
	if(pos)pos.textContent='Hunk '+(_diffHunkIdx+1)+'/'+_diffHunks.length;
	var prev=document.getElementById('ocDiffHunkPrev');
	var next=document.getElementById('ocDiffHunkNext');
	if(prev)prev.disabled=_diffHunkIdx===0;
	if(next)next.disabled=_diffHunkIdx===_diffHunks.length-1;
	var pre=document.querySelector('#ocDiffBd pre');
	if(!pre)return;
	var allLines=pre.querySelectorAll('.ocDiffHunkLine');
	allLines.forEach(function(l){l.style.background=''});
	var hunk=_diffHunks[_diffHunkIdx];
	if(!hunk)return;
	var start=hunk.startLine;
	for(var i=0;i<hunk.lines.length;i++){
		var lineEl=pre.querySelector('.ocDiffHunkLine[data-line="'+(start+i+1)+'"]');
		if(lineEl)lineEl.style.background='rgba(59,130,246,.06)';
	}
	var firstEl=pre.querySelector('.ocDiffHunkLine[data-line="'+(start+1)+'"]');
	if(firstEl)firstEl.scrollIntoView({block:'center',behavior:'smooth'});
}

// ===== FEATURE: File Line Range Attachments (@file.php#10-20) =====
var _origApiSend=send;
send=function(){
	var inp=document.getElementById('ocIn');
	var text=inp.value;
	var rangeMatch=text.match(/@([^\s@]+)#(\d+)(?:-(\d+))?/);
	if(rangeMatch){
		var filePath=rangeMatch[1];
		var startLine=parseInt(rangeMatch[2]);
		var endLine=rangeMatch[3]?parseInt(rangeMatch[3]):startLine;
		var relPath=filePath.replace(/^\//,'');
		api('read_file',{path:relPath},function(e,r){
			if(!e&&r&&r.content){
				var lines=r.content.split('\n');
				var excerpt=lines.slice(startLine-1,endLine).join('\n');
				pendingFiles.push({name:relPath+'#'+startLine+(endLine>startLine?'-'+endLine:''),content:excerpt});
				toast('Attached '+relPath+':'+startLine+(endLine>startLine?'-'+endLine:''),'ok');
			}else{
				toast('Could not read '+relPath,'err');
			}
			inp.value=inp.value.replace(/@[^\s@]+#\d+(?:-\d+)?/,'').trim();
			_origApiSend();
		});
	}else{
		_origApiSend();
	}
};

// ===== FEATURE: Grep/Search Results with Clickable File References =====
var _origRMsg3=rMsg;
rMsg=function(msg){
	var el=_origRMsg3(msg);
	if(!el)return el;
	if(msg.role==='asst'){
		var toolUses=(msg.parts||[]).filter(function(p){return p.type==='tool_use'});
		toolUses.forEach(function(p){
			if(p.name==='grep'||p.name==='search'||p.name==='glob'){
				var toolEls=el.querySelectorAll('.ocTk');
				toolEls.forEach(function(tEl){
					var body=tEl.querySelector('.ocTkB');
					if(body){
						var text=body.textContent;
						var html=text.replace(/([^\s:]+\.php|[^\s:]+\.js|[^\s:]+\.ts|[^\s:]+\.css|[^\s:]+\.html|[^\s:]+\.json):(\d+)/g,function(m,file,line){
							return '<span class="ocGrepFile" data-file="'+file+'" data-line="'+line+'">'+file+'<span class="ocGrepLine">:'+line+'</span></span>';
						});
						body.innerHTML=html;
						body.querySelectorAll('.ocGrepFile').forEach(function(fEl){
							fEl.addEventListener('click',function(){
								var f=this.dataset.file;
								var l=parseInt(this.dataset.line);
								api('read_file',{path:f},function(e,r){
									if(!e&&r&&r.content){
										var viewer=document.getElementById('ocFv');
										if(viewer){
											viewer.classList.add('open');
											var pre=document.getElementById('ocFvPre');
											var lines=r.content.split('\n');
											var start=Math.max(0,l-5);
											var end=Math.min(lines.length,l+5);
											pre.innerHTML=lines.slice(start,end).map(function(line,i){
												var ln=start+i+1;
												var cls=ln===l?'background:rgba(250,204,21,.15)':'';
												return '<div style="'+cls+'"><span style="color:var(--t4);width:30px;display:inline-block;text-align:right;padding-right:8px">'+ln+'</span>'+esc(line)+'</div>';
											}).join('');
											document.getElementById('ocFvTitle').textContent=f+':'+l;
										}
									}
								});
							});
						});
					}
				});
			}
		});
	}
	return el;
};

// ===== FEATURE: Timeline slash command =====
// /timeline is already in slashCmds array
var _origSendRef2=send;
// Handle /timeline command
var _origProcessCmd=null;

// ===== FEATURE: Plugin/Extension System =====
var _plugins={};
function registerPlugin(name,handlers){
	_plugins[name]=handlers;
	if(handlers.onMount){
		try{handlers.onMount()}catch(e){console.error('Plugin '+name+' mount error:',e)}
	}
	toast('Plugin loaded: '+name,'ok');
}
function getPlugin(name){return _plugins[name]||null}
function listPlugins(){return Object.keys(_plugins)}

// ===== HELPER: Render Split Diff =====
function renderSplitDiff(diffText){
	var splitView=document.getElementById('ocDiffSplitView');
	if(!splitView)return;
	var leftDiv=splitView.querySelector('.ocDiffSplitL');
	var rightDiv=splitView.querySelector('.ocDiffSplitR');
	if(!leftDiv||!rightDiv)return;
	var lines=diffText.split('\n');
	var leftHtml='',rightHtml='';
	var leftLn=1,rightLn=1;
	lines.forEach(function(line){
		var prefix=line.substring(0,1);
		var content=line.substring(1);
		if(prefix==='@'){
			leftHtml+='<div style="padding:4px 8px;color:var(--p);font-weight:600">'+esc(line)+'</div>';
			rightHtml+='<div style="padding:4px 8px;color:var(--p);font-weight:600">'+esc(line)+'</div>';
			var m=line.match(/\+(\d+)/);
			if(m)rightLn=parseInt(m[1]);
			var m2=line.match(/-(\d+)/);
			if(m2)leftLn=parseInt(m2[1]);
		}else if(prefix==='+'){
			rightHtml+='<div class="ocDiffSplitRow add"><span class="ocDiffLN">'+(rightLn++)+'</span><span class="ocDiffLC">'+esc(content)+'</span></div>';
		}else if(prefix==='-'){
			leftHtml+='<div class="ocDiffSplitRow del"><span class="ocDiffLN">'+(leftLn++)+'</span><span class="ocDiffLC">'+esc(content)+'</span></div>';
		}else{
			leftHtml+='<div class="ocDiffSplitRow"><span class="ocDiffLN">'+(leftLn++)+'</span><span class="ocDiffLC">'+esc(line)+'</span></div>';
			rightHtml+='<div class="ocDiffSplitRow"><span class="ocDiffLN">'+(rightLn++)+'</span><span class="ocDiffLC">'+esc(line)+'</span></div>';
		}
	});
	leftDiv.innerHTML='<div style="padding:4px 8px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);font-weight:500">Original</div>'+leftHtml;
	rightDiv.innerHTML='<div style="padding:4px 8px;font-size:11px;color:var(--t3);border-bottom:1px solid var(--bd2);font-weight:500">Modified</div>'+rightHtml;
}

})();

</script>
</body>
</html>
