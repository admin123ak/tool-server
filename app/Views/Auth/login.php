<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= BASE_NAME ?? 'SX2' ?> // ACCESS</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?= link_tag('assets/css/cyberpunk.css') ?>
<style>
  body{ background:#03040a; color:#cfe7ee; font-family:'Share Tech Mono',monospace; overflow:hidden; min-height:100vh; }
  /* Background: ASCII rain + grid */
  .bg-grid{
    position:fixed; inset:0; z-index:0;
    background:
      linear-gradient(rgba(0,245,255,.05) 1px,transparent 1px) 0 0/50px 50px,
      linear-gradient(90deg,rgba(0,245,255,.05) 1px,transparent 1px) 0 0/50px 50px,
      radial-gradient(circle at 30% 30%, rgba(0,245,255,.15), transparent 60%),
      radial-gradient(circle at 70% 70%, rgba(255,43,214,.12), transparent 60%);
  }
  .ascii-col{
    position:fixed; top:0; bottom:0; width:120px; pointer-events:none; z-index:1;
    font:11px/14px 'Share Tech Mono',monospace; color:rgba(57,255,20,.4);
    white-space:pre; overflow:hidden;
  }
  .ascii-col.l{ left:20px; }
  .ascii-col.r{ right:20px; }
  .ascii-col span{ display:block; animation: fall linear infinite; }
  @keyframes fall{ from{ transform:translateY(-100%) } to{ transform:translateY(100vh) } }

  /* MAIN LAYOUT — SPLIT 2 PANEL */
  .split{
    position:relative; z-index:5; height:100vh;
    display:grid; grid-template-columns: 1.2fr 1fr; gap:0;
  }
  /* LEFT: TERMINAL log */
  .term-side{
    background:linear-gradient(135deg, rgba(0,8,16,.95), rgba(0,4,12,.95));
    border-right:1px solid rgba(0,245,255,.3);
    padding:32px 40px; overflow:hidden; position:relative;
    display:flex; flex-direction:column;
  }
  .term-side::before{
    content:""; position:absolute; left:0; top:0; bottom:0; width:4px;
    background:linear-gradient(180deg, var(--cy-cyan,#00f5ff), var(--cy-magenta,#ff2bd6));
    box-shadow:0 0 12px var(--cy-cyan,#00f5ff);
  }
  .term-head{
    font:900 28px 'Orbitron',sans-serif;
    background:linear-gradient(90deg,#fff,var(--cy-cyan,#00f5ff));
    -webkit-background-clip:text; background-clip:text; color:transparent;
    letter-spacing:.05em;
  }
  .term-sub{ color:#7fb3c2; font:12px 'Share Tech Mono',monospace; letter-spacing:.25em; margin-top:6px; }
  .term-log{
    margin-top:24px; flex:1;
    font:13px/1.85 'Share Tech Mono',monospace; color:#cfe7ee;
    border:1px solid rgba(0,245,255,.2); padding:18px; background:rgba(0,0,0,.4);
    overflow:hidden; position:relative;
  }
  .term-log::before{
    content:""; position:absolute; left:0; right:0; height:2px;
    background:linear-gradient(90deg, transparent, var(--cy-cyan,#00f5ff), transparent);
    animation: scan 3s linear infinite;
  }
  @keyframes scan{ from{top:0} to{top:100%} }
  .term-log .ln{ opacity:0; animation: showln .25s forwards; }
  @keyframes showln{ to{opacity:1} }
  .term-log .gn{ color:var(--cy-green,#39ff14); }
  .term-log .cy{ color:var(--cy-cyan,#00f5ff); }
  .term-log .mg{ color:var(--cy-magenta,#ff2bd6); }
  .term-log .am{ color:#ffb800; }
  .term-cursor{
    display:inline-block; width:8px; height:14px; background:var(--cy-cyan,#00f5ff);
    animation: bk 1s infinite; vertical-align:middle; margin-left:4px;
  }
  @keyframes bk{ 50%{opacity:0} }

  /* RIGHT: AUTH KEYPAD */
  .auth-side{
    background:linear-gradient(225deg, rgba(0,8,16,.7), rgba(0,4,12,.95));
    display:flex; align-items:center; justify-content:center; padding:40px;
    position:relative;
  }
  /* Decorative corner brackets on screen */
  .auth-side::before,.auth-side::after{
    content:""; position:absolute; width:60px; height:60px;
    border:2px solid var(--cy-cyan,#00f5ff);
    filter: drop-shadow(0 0 6px var(--cy-cyan,#00f5ff));
  }
  .auth-side::before{ top:20px; right:20px; border-left:0; border-bottom:0; }
  .auth-side::after{ bottom:20px; left:20px; border-right:0; border-top:0;
    border-color: var(--cy-magenta,#ff2bd6); filter: drop-shadow(0 0 6px var(--cy-magenta,#ff2bd6));
  }
  .keypad{ width:100%; max-width:440px; position:relative; }

  /* Hex shield logo */
  .shield-hex{
    width:120px; height:120px; margin:0 auto 24px; position:relative;
  }
  .shield-hex .body{
    position:absolute; inset:0;
    clip-path: polygon(50% 0,100% 25%,100% 75%,50% 100%,0 75%,0 25%);
    background: linear-gradient(135deg, var(--cy-cyan,#00f5ff), var(--cy-magenta,#ff2bd6));
    display:flex; align-items:center; justify-content:center;
    box-shadow: 0 0 40px var(--cy-cyan,#00f5ff), 0 0 80px rgba(255,43,214,.4);
  }
  .shield-hex .ring{
    position:absolute; inset:-12px;
    clip-path: polygon(50% 0,100% 25%,100% 75%,50% 100%,0 75%,0 25%);
    background: var(--cy-cyan,#00f5ff);
    z-index:-1;
    animation: pulse-ring 2s infinite;
  }
  @keyframes pulse-ring{
    0%{ opacity:.6; transform:scale(1) }
    100%{ opacity:0; transform:scale(1.3) }
  }
  .shield-hex i{ font-size:48px; color:#000; }

  .auth-title{
    font:900 22px 'Orbitron',sans-serif;
    background:linear-gradient(90deg,var(--cy-cyan,#00f5ff),var(--cy-magenta,#ff2bd6));
    -webkit-background-clip:text; background-clip:text; color:transparent;
    text-align:center; letter-spacing:.1em;
  }
  .auth-subt{ text-align:center; color:#7fb3c2; font:11px 'Share Tech Mono',monospace; letter-spacing:.3em; margin:8px 0 28px; }

  /* INPUT row HUD style */
  .field{
    display:flex; align-items:center; margin-bottom:14px;
    border:1px solid rgba(0,245,255,.3); background:rgba(0,8,16,.7);
    transition: all .2s;
  }
  .field:focus-within{
    border-color: var(--cy-cyan,#00f5ff);
    box-shadow: 0 0 0 1px var(--cy-cyan,#00f5ff), 0 0 16px rgba(0,245,255,.3);
    background:rgba(0,245,255,.04);
  }
  .field .ico{
    width:48px; display:flex; align-items:center; justify-content:center;
    background:rgba(0,245,255,.08); color:var(--cy-cyan,#00f5ff); height:50px;
    border-right:1px solid rgba(0,245,255,.3);
  }
  .field .lbl{
    flex:0 0 110px; padding:0 12px; color:var(--cy-cyan,#00f5ff);
    font:700 10px 'Share Tech Mono',monospace; letter-spacing:.2em;
    border-right:1px dashed rgba(0,245,255,.3);
  }
  .field input{
    flex:1; background:transparent !important; border:0 !important; outline:0;
    color:#fff; padding:0 14px; height:50px;
    font:14px 'Share Tech Mono',monospace; letter-spacing:.1em;
  }
  .field input::placeholder{ color:#3a5d68; letter-spacing:.15em; }
  .field .eye{
    width:48px; display:flex; align-items:center; justify-content:center;
    color:#7fb3c2; cursor:pointer; height:50px; transition: color .2s;
  }
  .field .eye:hover{ color: var(--cy-cyan,#00f5ff); }

  /* BIG BUTTON */
  .auth-btn{
    width:100%; padding:16px;
    background:linear-gradient(135deg,var(--cy-cyan,#00f5ff),var(--cy-magenta,#ff2bd6));
    color:#000; border:0; cursor:pointer;
    font:900 14px 'Orbitron',sans-serif; letter-spacing:.3em;
    box-shadow: 0 0 24px rgba(0,245,255,.4);
    clip-path: polygon(12px 0, 100% 0, calc(100% - 12px) 100%, 0 100%);
    margin-top:18px; transition: all .2s;
  }
  .auth-btn:hover{ filter: brightness(1.1); box-shadow: 0 0 36px var(--cy-cyan,#00f5ff); transform: translateY(-1px); }
  .auth-btn:active{ transform: translateY(0); }

  /* Status row */
  .status-row{
    display:flex; justify-content:space-between; font:10px 'Share Tech Mono',monospace;
    color:#7fb3c2; letter-spacing:.2em; margin-top:24px;
    padding-top:16px; border-top:1px dashed rgba(0,245,255,.2);
  }
  .status-row .dot{
    display:inline-block; width:6px; height:6px; border-radius:50%;
    background:var(--cy-green,#39ff14); box-shadow:0 0 8px var(--cy-green,#39ff14);
    animation: bk 1.4s infinite; margin-right:4px;
  }

  /* Footer links */
  .foot-link{
    text-align:center; margin-top:18px;
    font:11px 'Share Tech Mono',monospace; letter-spacing:.15em;
  }
  .foot-link a{ color:var(--cy-cyan,#00f5ff); text-decoration:none; padding:0 10px; }
  .foot-link a:hover{ text-shadow: 0 0 8px var(--cy-cyan,#00f5ff); }
  .foot-link span{ color:#3a5d68; }

  /* Alerts */
  .alert{
    padding:12px 16px; margin-bottom:18px; border:1px solid;
    font:12px 'Share Tech Mono',monospace; letter-spacing:.08em;
    position:relative; padding-left:36px;
  }
  .alert::before{
    content:"!"; position:absolute; left:12px; top:50%; transform:translateY(-50%);
    width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center;
    font:900 12px monospace;
  }
  .alert-danger{ border-color:var(--cy-magenta,#ff2bd6); color:var(--cy-magenta,#ff2bd6); background:rgba(255,43,214,.06); }
  .alert-danger::before{ background:var(--cy-magenta,#ff2bd6); color:#000; }
  .alert-success{ border-color:var(--cy-green,#39ff14); color:var(--cy-green,#39ff14); background:rgba(57,255,20,.06); }
  .alert-success::before{ background:var(--cy-green,#39ff14); color:#000; content:"✓"; }

  /* Responsive */
  @media (max-width: 900px){
    .split{ grid-template-columns: 1fr; height:auto; min-height:100vh; }
    .term-side{ display:none; }
  }
</style>
</head>
<body>

<div class="bg-grid"></div>
<div class="ascii-col l" id="asciiL"></div>
<div class="ascii-col r" id="asciiR"></div>

<div class="split">

  <!-- ===== LEFT: TERMINAL ===== -->
  <aside class="term-side">
    <div>
      <div class="term-head">SX2.LADOR</div>
      <div class="term-sub">// MAINFRAME // AUTH NODE v2.6</div>
    </div>

    <div class="term-log" id="termlog"></div>

    <div style="margin-top:auto; font:10px 'Share Tech Mono',monospace; color:#3a5d68; letter-spacing:.2em">
      <div>NODE: MAINFRAME.01</div>
      <div>REGION: ASIA-PACIFIC</div>
      <div>BUILD: 2026.06.<?= rand(100, 999) ?></div>
      <div style="color:var(--cy-green,#39ff14); margin-top:8px">● UPLINK STABLE :: 99.97%</div>
    </div>
  </aside>

  <!-- ===== RIGHT: AUTH ===== -->
  <main class="auth-side">
    <div class="keypad">

      <!-- HEX SHIELD LOGO -->
      <div class="shield-hex">
        <div class="ring"></div>
        <div class="body"><i class="fas fa-shield-halved"></i></div>
      </div>

      <div class="auth-title">/ / A C C E S S _ G R A N T_</div>
      <div class="auth-subt">[ AUTHENTICATE TO PROCEED ]</div>

      <!-- FLASH -->
      <?php if (session()->getFlashdata('msgDanger')) : ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('msgDanger') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('msgSuccess')) : ?>
        <div class="alert alert-success"><?= session()->getFlashdata('msgSuccess') ?></div>
      <?php endif; ?>

      <?= form_open() ?>
        <input type="hidden" name="ip" value="<?= $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' ?>">
        <input type="hidden" name="stay_log" value="yes">

        <!-- USERNAME field -->
        <div class="field">
          <div class="ico"><i class="fas fa-user-astronaut"></i></div>
          <div class="lbl">OPER_ID</div>
          <input type="text" name="username" placeholder="enter_callsign" required autocomplete="off">
        </div>
        <?php if ($validation->hasError('username')) : ?>
          <div style="color:var(--cy-magenta,#ff2bd6);font:10px 'Share Tech Mono',monospace;margin:-8px 0 8px 14px">▸ <?= $validation->getError('username') ?></div>
        <?php endif; ?>

        <!-- PASSWORD field -->
        <div class="field">
          <div class="ico"><i class="fas fa-fingerprint"></i></div>
          <div class="lbl">CIPHER</div>
          <input type="password" id="password" name="password" placeholder="enter_passphrase" required>
          <div class="eye" onclick="togglePassword()"><i class="fas fa-eye" id="eyeIcon"></i></div>
        </div>
        <?php if ($validation->hasError('password')) : ?>
          <div style="color:var(--cy-magenta,#ff2bd6);font:10px 'Share Tech Mono',monospace;margin:-8px 0 8px 14px">▸ <?= $validation->getError('password') ?></div>
        <?php endif; ?>

        <!-- BUTTON -->
        <button type="submit" class="auth-btn">▸ ENGAGE LINK ◂</button>

      <?= form_close() ?>

      <!-- Status -->
      <div class="status-row">
        <span><span class="dot"></span>SECURE.TLS</span>
        <span>IP:<?= $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' ?></span>
        <span><span class="dot"></span>FW.ACTIVE</span>
      </div>

      <!-- Bottom links -->
      <div class="foot-link">
        <a href="<?= base_url('register') ?>">REGISTER</a>
        <span>::</span>
        <a href="<?= base_url('Getkey.php') ?>">GET_FREE_KEY</a>
        <span>::</span>
        <a href="<?= base_url() ?>">HOME</a>
      </div>
    </div>
  </main>

</div>

<script>
  function togglePassword(){
    var p=document.getElementById('password'), e=document.getElementById('eyeIcon');
    if(p.type==='password'){ p.type='text'; e.className='fas fa-eye-slash'; }
    else{ p.type='password'; e.className='fas fa-eye'; }
  }

  // Terminal log streaming
  (function(){
    var term = document.getElementById('termlog');
    var lines = [
      {c:'cy', t:'[SYS] MAINFRAME.01 // boot sequence init'},
      {c:'gn', t:'[OK]  cipher subsystem :: AES-256/RC4 ready'},
      {c:'gn', t:'[OK]  /vault/keys mounted // 8932 entries'},
      {c:'',   t:'[..]  scanning auth.endpoints ......'},
      {c:'cy', t:'[INFO] firewall .................. UP'},
      {c:'cy', t:'[INFO] honeypot trap ............. armed'},
      {c:'gn', t:'[OK]  uplink to MAINFRAME stable'},
      {c:'am', t:'[WAIT] awaiting operator handshake'},
      {c:'',   t:'[..]  listening on :443/auth'},
      {c:'mg', t:'[ALERT] 3 unauthorized probes rejected'},
      {c:'cy', t:'[INFO] session token entropy :: 256-bit'},
      {c:'gn', t:'[OK]  ready to accept credentials'},
    ];
    var i=0, prefix='';
    function pad(n){ return n<10?'0'+n:n; }
    function ts(){ var d=new Date(); return pad(d.getHours())+':'+pad(d.getMinutes())+':'+pad(d.getSeconds()); }
    function step(){
      if(i>=lines.length){
        // loop: add random new log
        var pool=[
          {c:'cy', t:'[INFO] heartbeat :: 60s tick'},
          {c:'',   t:'[..]  awaiting input'},
          {c:'gn', t:'[OK]  link stable'},
          {c:'am', t:'[WAIT] timeout in 300s'},
        ];
        var p = pool[Math.floor(Math.random()*pool.length)];
        addLine(p.c, p.t);
        return;
      }
      var p = lines[i++];
      addLine(p.c, p.t);
    }
    function addLine(c, t){
      var el = document.createElement('div');
      el.className = 'ln ' + (c||'');
      el.innerHTML = '<span style="color:#3a5d68">' + ts() + '</span> ' + t;
      term.appendChild(el);
      if(term.childElementCount > 18) term.removeChild(term.firstElementChild);
      term.scrollTop = term.scrollHeight;
    }
    // start
    var iv = setInterval(step, 280);
    setTimeout(function(){ clearInterval(iv); setInterval(step, 1600); }, 4500);
  })();

  // ASCII rain
  (function(){
    var chars = '01ﾊﾐﾋｰｳｼﾅﾓﾆｻﾜﾂｵﾘｱﾎﾃﾏｹﾒｴｶｷﾑﾕﾗｾﾈｽﾀﾇﾍﾔﾜ#$%&*';
    function fill(el){
      var s='';
      for(var i=0;i<60;i++) s += chars[Math.floor(Math.random()*chars.length)];
      el.innerHTML = '';
      var span = document.createElement('span');
      span.textContent = s.split('').join('\n');
      span.style.animationDuration = (8 + Math.random()*8)+'s';
      span.style.animationDelay = '-' + (Math.random()*4)+'s';
      el.appendChild(span);
    }
    fill(document.getElementById('asciiL'));
    fill(document.getElementById('asciiR'));
  })();
</script>
</body>
</html>
