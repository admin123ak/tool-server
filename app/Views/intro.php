<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= BASE_NAME ?? 'SX2.LADOR' ?> // MAINFRAME</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?= link_tag('assets/css/cyberpunk.css') ?>
<style>
  body{ background:#03040a; color:#cfe7ee; font-family:'Share Tech Mono',monospace; overflow-x:hidden; }
  /* Background layers */
  .bg-grid{ position:fixed; inset:0; z-index:0;
    background:
      linear-gradient(rgba(0,245,255,.04) 1px,transparent 1px) 0 0/60px 60px,
      linear-gradient(90deg,rgba(0,245,255,.04) 1px,transparent 1px) 0 0/60px 60px,
      radial-gradient(ellipse at 20% 30%, rgba(0,245,255,.12), transparent 60%),
      radial-gradient(ellipse at 80% 70%, rgba(255,43,214,.10), transparent 60%);
  }
  .bg-3d{ position:fixed; bottom:-20vh; left:-10%; right:-10%; height:80vh; z-index:0; pointer-events:none;
    background:
      repeating-linear-gradient(90deg, rgba(0,245,255,.4) 0 1px, transparent 1px 80px),
      repeating-linear-gradient(0deg, rgba(0,245,255,.4) 0 1px, transparent 1px 80px);
    transform: perspective(800px) rotateX(65deg);
    mask-image: linear-gradient(to top, #000, transparent 80%);
  }
  .scan{ position:fixed; inset:0; pointer-events:none; z-index:1;
    background: repeating-linear-gradient(0deg, rgba(0,245,255,.025) 0 2px, transparent 2px 4px);
  }

  /* TOP STATUS BAR */
  .top-bar{
    position:relative; z-index:10;
    display:flex; justify-content:space-between; align-items:center;
    padding:12px 32px; background:rgba(0,8,16,.9); border-bottom:1px solid rgba(0,245,255,.3);
    font:11px 'Share Tech Mono',monospace; letter-spacing:.2em; color:#7fb3c2;
  }
  .top-bar .live{ display:flex; gap:24px; align-items:center; }
  .top-bar .dot{ display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--cy-green,#39ff14); box-shadow:0 0 8px var(--cy-green,#39ff14); animation: bk 1.2s infinite; margin-right:6px; }
  @keyframes bk{ 50%{opacity:.3} }

  /* HERO LAYOUT — 2/3 + 1/3 NOT symmetric */
  .hero{
    position:relative; z-index:5;
    display:grid; grid-template-columns: 1.7fr 1fr; gap:24px;
    padding:48px 32px; min-height: calc(100vh - 50px);
    align-items:start;
  }

  /* LEFT: BOOT TERMINAL + giant title */
  .hero-left{ position:relative; }
  .boot-screen{
    background:rgba(0,0,0,.6); border:1px solid rgba(0,245,255,.25); padding:18px 22px;
    margin-bottom:32px;
    font:13px/1.85 'Share Tech Mono',monospace;
    box-shadow: inset 0 0 30px rgba(0,245,255,.06);
    position:relative; overflow:hidden;
  }
  .boot-screen::before{
    content:""; position:absolute; left:0; right:0; height:2px;
    background:linear-gradient(90deg, transparent, var(--cy-cyan,#00f5ff), transparent);
    animation: scan 4s linear infinite;
  }
  @keyframes scan{ from{top:-2px} to{top:100%} }
  .boot-screen .head{
    display:flex; align-items:center; justify-content:space-between;
    border-bottom:1px dashed rgba(0,245,255,.3); padding-bottom:8px; margin-bottom:10px;
    font:10px 'Share Tech Mono',monospace; color:var(--cy-cyan,#00f5ff); letter-spacing:.25em;
  }
  .boot-screen .dots{ display:flex; gap:6px; }
  .boot-screen .dots span{ width:10px; height:10px; border-radius:50%; }
  .boot-screen .dots span:nth-child(1){ background:var(--cy-magenta,#ff2bd6); box-shadow:0 0 6px var(--cy-magenta,#ff2bd6); }
  .boot-screen .dots span:nth-child(2){ background:#ffb800; box-shadow:0 0 6px #ffb800; }
  .boot-screen .dots span:nth-child(3){ background:var(--cy-green,#39ff14); box-shadow:0 0 6px var(--cy-green,#39ff14); }
  .boot-screen .ln{ opacity:0; animation: bshow .25s forwards; }
  @keyframes bshow{ to{opacity:1} }
  .boot-screen .gn{ color:var(--cy-green,#39ff14); }
  .boot-screen .cy{ color:var(--cy-cyan,#00f5ff); }
  .boot-screen .mg{ color:var(--cy-magenta,#ff2bd6); }
  .boot-screen .am{ color:#ffb800; }
  .boot-screen .ok{ background:var(--cy-green,#39ff14); color:#000; padding:0 6px; }

  /* GIANT TITLE */
  .giant{
    font:900 80px/1 'Orbitron',sans-serif;
    letter-spacing:-.02em; margin:24px 0;
    position:relative;
  }
  .giant .l1{ display:block;
    background:linear-gradient(90deg,#fff 0%,var(--cy-cyan,#00f5ff) 100%);
    -webkit-background-clip:text; background-clip:text; color:transparent;
    text-shadow: 0 0 40px rgba(0,245,255,.4);
  }
  .giant .l2{ display:block;
    background:linear-gradient(90deg,var(--cy-magenta,#ff2bd6),var(--cy-cyan,#00f5ff));
    -webkit-background-clip:text; background-clip:text; color:transparent;
    position:relative;
  }
  .giant .l2::after{
    content:attr(data-text); position:absolute; left:3px; top:0;
    background:linear-gradient(90deg,var(--cy-magenta,#ff2bd6),#fff);
    -webkit-background-clip:text; background-clip:text; color:transparent;
    mix-blend-mode:screen; opacity:.55; clip-path: inset(50% 0 30% 0);
    animation: gl 3s infinite linear alternate;
  }
  @keyframes gl{
    0%{clip-path:inset(40% 0 50% 0); transform:translate(0,0)}
    25%{clip-path:inset(10% 0 80% 0); transform:translate(-2px,0)}
    50%{clip-path:inset(70% 0 10% 0); transform:translate(2px,0)}
    75%{clip-path:inset(35% 0 55% 0); transform:translate(-1px,0)}
    100%{clip-path:inset(45% 0 45% 0); transform:translate(1px,0)}
  }
  .tagline{
    font:14px/1.6 'Share Tech Mono',monospace; color:#cfe7ee;
    max-width: 560px; margin-bottom:32px; letter-spacing:.05em;
    padding-left:14px; border-left:3px solid var(--cy-cyan,#00f5ff);
  }
  .tagline span{ color:var(--cy-magenta,#ff2bd6); }

  /* CTA buttons */
  .cta-row{ display:flex; gap:14px; flex-wrap:wrap; margin-bottom:36px; }
  .cta{
    padding:16px 32px; font:900 13px 'Orbitron',sans-serif; letter-spacing:.2em;
    text-decoration:none; cursor:pointer; transition: all .2s;
    display:inline-flex; align-items:center; gap:10px;
    clip-path: polygon(14px 0, 100% 0, calc(100% - 14px) 100%, 0 100%);
  }
  .cta.primary{
    background:linear-gradient(135deg, var(--cy-cyan,#00f5ff), var(--cy-magenta,#ff2bd6));
    color:#000; box-shadow:0 0 24px rgba(0,245,255,.4);
  }
  .cta.primary:hover{ filter:brightness(1.1); box-shadow:0 0 40px var(--cy-cyan,#00f5ff); }
  .cta.ghost{
    background:transparent; color:var(--cy-cyan,#00f5ff);
    border:1px solid var(--cy-cyan,#00f5ff); padding:15px 32px;
    box-shadow: inset 0 0 12px rgba(0,245,255,.1);
  }
  .cta.ghost:hover{ background:rgba(0,245,255,.1); }

  /* RIGHT: STATS CARD + DIAGRAM */
  .hero-right{
    background:rgba(0,8,16,.7); border:1px solid rgba(0,245,255,.25);
    padding:24px; position:relative;
    box-shadow: 0 0 30px rgba(0,245,255,.08), inset 0 0 0 1px rgba(0,245,255,.05);
  }
  .hero-right::before, .hero-right::after{
    content:""; position:absolute; width:20px; height:20px;
    border:2px solid var(--cy-cyan,#00f5ff);
    filter: drop-shadow(0 0 4px var(--cy-cyan,#00f5ff));
  }
  .hero-right::before{ left:-2px; top:-2px; border-right:0; border-bottom:0; }
  .hero-right::after{ right:-2px; bottom:-2px; border-left:0; border-top:0;
    border-color: var(--cy-magenta,#ff2bd6); filter: drop-shadow(0 0 4px var(--cy-magenta,#ff2bd6));
  }
  .panel-title{
    font:700 11px 'Share Tech Mono',monospace; letter-spacing:.25em;
    color:var(--cy-cyan,#00f5ff); padding-bottom:10px;
    border-bottom:1px dashed rgba(0,245,255,.3); margin-bottom:16px;
    display:flex; justify-content:space-between;
  }
  .panel-title .x{ color:#7fb3c2; }

  /* Big counter */
  .big-stat{ text-align:center; margin-bottom:18px; }
  .big-stat .n{
    font:900 60px 'Share Tech Mono',monospace; line-height:1;
    background:linear-gradient(180deg, var(--cy-cyan,#00f5ff), var(--cy-magenta,#ff2bd6));
    -webkit-background-clip:text; background-clip:text; color:transparent;
    text-shadow:0 0 30px rgba(0,245,255,.4);
  }
  .big-stat .l{ font:10px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.3em; margin-top:4px; }

  /* Mini stats grid */
  .mini-grid{
    display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:20px;
  }
  .mini-stat{
    border:1px solid rgba(0,245,255,.2); padding:10px;
    background:rgba(0,245,255,.04); position:relative;
  }
  .mini-stat .n{ font:900 22px 'Share Tech Mono',monospace; color:var(--cy-cyan,#00f5ff); }
  .mini-stat.mg .n{ color:var(--cy-magenta,#ff2bd6); }
  .mini-stat.gn .n{ color:var(--cy-green,#39ff14); }
  .mini-stat.am .n{ color:#ffb800; }
  .mini-stat .l{ font:9px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.2em; margin-top:2px; }

  /* SYSTEM diagram */
  .sys-diag{
    border:1px solid rgba(0,245,255,.2); padding:14px; background:rgba(0,0,0,.4);
    font:11px/1.9 'Share Tech Mono',monospace;
  }
  .sys-diag .node{ display:flex; align-items:center; gap:8px; }
  .sys-diag .node .b{ width:8px; height:8px; border-radius:50%; }
  .sys-diag .node.up .b{ background:var(--cy-green,#39ff14); box-shadow:0 0 6px var(--cy-green,#39ff14); }
  .sys-diag .node.wn .b{ background:#ffb800; box-shadow:0 0 6px #ffb800; animation: bk 1.5s infinite; }
  .sys-diag .arrow{ color:#3a5d68; margin-left:14px; }

  /* FEATURE BLOCKS */
  .features{
    position:relative; z-index:5; padding:64px 32px 32px;
    display:grid; grid-template-columns: repeat(4, 1fr); gap:18px;
  }
  .feat{
    background:rgba(0,8,16,.7); border:1px solid rgba(0,245,255,.2);
    padding:24px 20px; position:relative; transition: all .3s;
    clip-path: polygon(0 0, 100% 0, 100% calc(100% - 18px), calc(100% - 18px) 100%, 0 100%);
  }
  .feat:hover{
    border-color:var(--cy-cyan,#00f5ff);
    background:rgba(0,245,255,.06);
    transform:translateY(-4px); box-shadow:0 8px 30px rgba(0,245,255,.15);
  }
  .feat .ico{
    width:48px; height:48px; display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg, var(--cy-cyan,#00f5ff), var(--cy-magenta,#ff2bd6));
    color:#000; margin-bottom:14px;
    clip-path: polygon(50% 0,100% 25%,100% 75%,50% 100%,0 75%,0 25%);
  }
  .feat h3{ font:700 16px 'Orbitron',sans-serif; color:#fff; margin-bottom:6px; letter-spacing:.05em; }
  .feat .id{ font:9px 'Share Tech Mono',monospace; color:var(--cy-cyan,#00f5ff); letter-spacing:.3em; margin-bottom:8px; }
  .feat p{ font:12px/1.6 'Share Tech Mono',monospace; color:#7fb3c2; }

  /* FOOTER LINE */
  .footer-line{
    position:relative; z-index:5; padding:24px 32px;
    border-top:1px solid rgba(0,245,255,.2);
    background:rgba(0,8,16,.8);
    font:10px 'Share Tech Mono',monospace; color:#3a5d68;
    text-align:center; letter-spacing:.3em;
  }
  .footer-line .x{ color:var(--cy-cyan,#00f5ff); }

  /* Responsive */
  @media (max-width: 1024px){
    .hero{ grid-template-columns: 1fr; }
    .giant{ font-size: 56px; }
    .features{ grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 600px){
    .giant{ font-size: 38px; }
    .features{ grid-template-columns: 1fr; }
    .top-bar{ font-size:9px; padding:10px 14px; }
    .top-bar .live{ gap:10px; }
  }
</style>
</head>
<body>

<div class="bg-grid"></div>
<div class="bg-3d"></div>
<div class="scan"></div>

<!-- TOP STATUS BAR -->
<div class="top-bar">
  <div class="live">
    <span><span class="dot"></span>MAINFRAME // ONLINE</span>
    <span>NODE.01 :: ASIA-PACIFIC</span>
    <span id="clock">--:--:--</span>
  </div>
  <div class="live">
    <span style="color:var(--cy-cyan,#00f5ff)"><a href="<?= base_url('login') ?>" style="color:inherit;text-decoration:none">[ ENTER PANEL ▸ ]</a></span>
  </div>
</div>

<!-- HERO -->
<section class="hero">
  <div class="hero-left">

    <!-- BOOT terminal -->
    <div class="boot-screen">
      <div class="head">
        <span>// sys.boot v2.6 -- last login: <?= date('Y-m-d H:i') ?> from MAINFRAME.01</span>
        <div class="dots"><span></span><span></span><span></span></div>
      </div>
      <div id="bootlog"></div>
    </div>

    <!-- HERO TITLE -->
    <h1 class="giant">
      <span class="l1">// LICENSE</span>
      <span class="l2" data-text="CONTROL_GRID">CONTROL_GRID</span>
    </h1>

    <p class="tagline">
      &gt; Encrypted key vault. Realtime loader pipeline. <span>Zero-trust</span> auth.<br>
      &gt; Built for <span>games &amp; software</span> protection. 24/7 uplink. <span>99.97%</span> SLA.
    </p>

    <div class="cta-row">
      <a href="<?= base_url('login') ?>" class="cta primary">
        <i class="fas fa-terminal"></i> ENGAGE LINK
      </a>
      <a href="<?= base_url('Getkey.php') ?>" class="cta ghost">
        <i class="fas fa-key"></i> FREE_KEY
      </a>
      <a href="<?= base_url('register') ?>" class="cta ghost">
        <i class="fas fa-user-plus"></i> SIGNUP
      </a>
    </div>
  </div>

  <!-- RIGHT PANEL: stats card -->
  <aside class="hero-right">
    <div class="panel-title">// SYS.STATUS<span class="x">REAL-TIME</span></div>

    <div class="big-stat">
      <div class="n">99.97%</div>
      <div class="l">UPLINK STABILITY</div>
    </div>

    <div class="mini-grid">
      <div class="mini-stat"><div class="n">0043</div><div class="l">LIC_TOTAL</div></div>
      <div class="mini-stat mg"><div class="n">0013</div><div class="l">USR_ACTIVE</div></div>
      <div class="mini-stat gn"><div class="n">005</div><div class="l">GAME_NODES</div></div>
      <div class="mini-stat am"><div class="n">8421</div><div class="l">THREATS_BLK</div></div>
    </div>

    <div class="panel-title" style="margin-top:6px">// NODE.MAP<span class="x">5 zones</span></div>
    <div class="sys-diag">
      <div class="node up"><span class="b"></span> NODE.01 ASIA-PAC <span class="arrow">▸ 12ms</span></div>
      <div class="node up"><span class="b"></span> NODE.02 EU-WEST <span class="arrow">▸ 89ms</span></div>
      <div class="node up"><span class="b"></span> NODE.03 US-EAST <span class="arrow">▸ 145ms</span></div>
      <div class="node wn"><span class="b"></span> NODE.04 SA-NORTH <span class="arrow">▸ 210ms</span></div>
      <div class="node up"><span class="b"></span> NODE.05 AU-EAST <span class="arrow">▸ 67ms</span></div>
    </div>
  </aside>
</section>

<!-- FEATURES BLOCKS -->
<section class="features">
  <div class="feat">
    <div class="ico"><i class="fas fa-lock"></i></div>
    <div class="id">FEAT // 001</div>
    <h3>QUANTUM CIPHER</h3>
    <p>AES-256 + RC4 + XXTEA layered encryption. Zero-knowledge proof for every handshake.</p>
  </div>
  <div class="feat">
    <div class="ico" style="background:linear-gradient(135deg, var(--cy-magenta,#ff2bd6), #ff5555)"><i class="fas fa-bolt"></i></div>
    <div class="id">FEAT // 002</div>
    <h3>INSTANT FORGE</h3>
    <p>Generate license keys in &lt;120ms. Bulk forge up to 10,000/sec. Hardware-id binding.</p>
  </div>
  <div class="feat">
    <div class="ico" style="background:linear-gradient(135deg, var(--cy-green,#39ff14), var(--cy-cyan,#00f5ff))"><i class="fas fa-eye"></i></div>
    <div class="id">FEAT // 003</div>
    <h3>OMNI MONITOR</h3>
    <p>Real-time loader pipeline. Track every activation, HWID, expiry across 5 global nodes.</p>
  </div>
  <div class="feat">
    <div class="ico" style="background:linear-gradient(135deg, #ffb800, var(--cy-magenta,#ff2bd6))"><i class="fas fa-shield-virus"></i></div>
    <div class="id">FEAT // 004</div>
    <h3>NEURAL GUARD</h3>
    <p>AI threat detector. Auto-bans crackers, replays, bots. 47k+ patterns learned.</p>
  </div>
</section>

<!-- FOOTER -->
<div class="footer-line">
  <span class="x">SX2.LADOR.MAINFRAME</span> // BUILD 2026.06.<?= rand(100,999) ?> // © <?= date('Y') ?> // ALL UPLINKS ENCRYPTED
</div>

<script>
  // boot log animation
  (function(){
    var log = document.getElementById('bootlog');
    var lines = [
      {c:'cy',  t:'> sx2.lador mainframe v2.6.0 // build 2026.06.<?= rand(100, 999) ?>'},
      {c:'gn',  t:'[ok] kernel.unlock ........................ <span class="ok">PASS</span>'},
      {c:'gn',  t:'[ok] mounting /vault/keys ................ 8932 entries'},
      {c:'cy',  t:'[..] initializing neural net link ........ <span class="gn">DONE</span>'},
      {c:'',    t:'[..] handshake with NODE.01 :: 12ms'},
      {c:'gn',  t:'[ok] cipher subsystem :: AES-256/RC4 :: ARMED'},
      {c:'am',  t:'[wn] firewall .................. 47 packets dropped'},
      {c:'cy',  t:'[..] uplink stabilizing .................. <span class="gn">99.97%</span>'},
      {c:'gn',  t:'[ok] ALL SYSTEMS NOMINAL :: entering control grid'},
      {c:'mg',  t:'> awaiting operator handshake ............ <span style="background:var(--cy-cyan,#00f5ff);color:#000;padding:0 4px;animation:bk 1s infinite">▮ READY</span>'},
    ];
    var i = 0;
    function pad(n){ return n<10?'0'+n:n; }
    function ts(){ var d=new Date(); return pad(d.getHours())+':'+pad(d.getMinutes())+':'+pad(d.getSeconds()); }
    var iv = setInterval(function(){
      if(i >= lines.length){
        clearInterval(iv); return;
      }
      var p = lines[i++];
      var el = document.createElement('div');
      el.className = 'ln ' + p.c;
      el.innerHTML = '<span style="color:#3a5d68">'+ts()+'</span> '+p.t;
      log.appendChild(el);
    }, 320);
  })();

  // clock
  (function(){
    var el = document.getElementById('clock');
    function pad(n){ return n<10?'0'+n:n; }
    function tick(){
      var d = new Date();
      el.textContent = pad(d.getHours())+':'+pad(d.getMinutes())+':'+pad(d.getSeconds())+' UTC';
    }
    tick(); setInterval(tick, 1000);
  })();
</script>
</body>
</html>
