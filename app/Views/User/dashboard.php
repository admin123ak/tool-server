<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= BASE_NAME ?? 'PANEL' ?> // MAINFRAME</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?= link_tag('assets/css/cyberpunk.css') ?>
<style>
  /* ============ MAINFRAME LAYOUT ============ */
  body{
    background:#03040a !important;
    color:#cfe7ee;
    overflow-x:hidden;
    font-family:'Share Tech Mono',monospace;
  }
  /* matrix code rain */
  .matrix-rain{
    position:fixed; inset:0; pointer-events:none; z-index:0;
    opacity:.08;
    background-image:
      repeating-linear-gradient(0deg, transparent 0 14px, rgba(0,245,255,.4) 14px 15px),
      repeating-linear-gradient(90deg, transparent 0 200px, rgba(57,255,20,.06) 200px 220px);
  }
  /* perspective grid floor */
  .grid-floor{
    position:fixed; left:0; right:0; bottom:0; height:55vh; pointer-events:none; z-index:0;
    background:
      linear-gradient(transparent 40%, rgba(0,245,255,.05) 100%),
      repeating-linear-gradient(90deg, rgba(0,245,255,.25) 0 1px, transparent 1px 64px),
      repeating-linear-gradient(0deg, rgba(0,245,255,.25) 0 1px, transparent 1px 64px);
    transform: perspective(700px) rotateX(58deg);
    transform-origin: bottom;
    mask-image: linear-gradient(to top, #000 30%, transparent 90%);
  }
  /* Top system bar - 4 gauges */
  .sys-bar{ position:relative; z-index:5; }
  .gauge{
    width:90px; height:90px; position:relative;
  }
  .gauge .ring{ position:absolute; inset:0; border-radius:50%; border:2px solid rgba(0,245,255,.25); }
  .gauge .needle{
    position:absolute; left:50%; bottom:50%; width:3px; height:35px; background:linear-gradient(180deg,#fff,var(--cy-cyan,#00f5ff));
    transform-origin: bottom center; transform: rotate(-90deg);
    box-shadow: 0 0 8px var(--cy-cyan,#00f5ff);
    transition: transform 1s ease-in-out;
  }
  .gauge .center{
    position:absolute; left:50%; top:50%; width:8px; height:8px; border-radius:50%;
    transform: translate(-50%,-50%); background:var(--cy-cyan,#00f5ff); box-shadow:0 0 10px var(--cy-cyan,#00f5ff);
  }
  .gauge .tick{
    position:absolute; left:50%; top:4px; width:1px; height:6px; background:rgba(0,245,255,.4);
    transform-origin: 50% 41px;
  }
  .gauge .lbl{
    position:absolute; left:0; right:0; bottom:-18px; text-align:center;
    font:700 9px 'Share Tech Mono',monospace; color:var(--cy-cyan,#00f5ff); letter-spacing:.18em;
  }
  .gauge .val{
    position:absolute; left:0; right:0; top:62%; text-align:center;
    font:700 14px 'Share Tech Mono',monospace; color:#fff;
  }

  /* TAB nav */
  .nav-tabs{ display:flex; gap:0; border-bottom:1px solid rgba(0,245,255,.3); position:relative; z-index:5; }
  .nav-tabs a{
    padding:10px 22px; color:#7fb3c2; text-decoration:none;
    font:700 11px 'Share Tech Mono',monospace; letter-spacing:.2em;
    border:1px solid transparent; border-bottom:0;
    background: linear-gradient(180deg, transparent, rgba(0,245,255,.04));
    position:relative;
    clip-path: polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%);
    margin-right:-6px;
  }
  .nav-tabs a.on{
    color:var(--cy-cyan,#00f5ff);
    background: linear-gradient(180deg, rgba(0,245,255,.18), rgba(0,245,255,.05));
    border-color: var(--cy-cyan,#00f5ff);
    text-shadow: 0 0 8px rgba(0,245,255,.6);
  }
  .nav-tabs a:hover{ color:#fff; }

  /* HUD PANEL với corner brackets */
  .hud{
    position:relative; background:rgba(5,8,14,.85); border:1px solid rgba(0,245,255,.22);
    padding:14px; margin-bottom:14px;
    box-shadow: inset 0 0 0 1px rgba(0,245,255,.05), 0 0 18px rgba(0,245,255,.06);
  }
  .hud::before, .hud::after{
    content:""; position:absolute; width:14px; height:14px;
    border:2px solid var(--cy-cyan,#00f5ff);
    filter: drop-shadow(0 0 4px var(--cy-cyan,#00f5ff));
  }
  .hud::before{ left:-2px; top:-2px; border-right:0; border-bottom:0; }
  .hud::after{ right:-2px; bottom:-2px; border-left:0; border-top:0;
    border-color: var(--cy-magenta,#ff2bd6); filter: drop-shadow(0 0 4px var(--cy-magenta,#ff2bd6));
  }
  .hud-title{
    display:flex; justify-content:space-between; align-items:center;
    font:700 10px 'Share Tech Mono',monospace; letter-spacing:.2em;
    color:var(--cy-cyan,#00f5ff); padding-bottom:8px; margin-bottom:10px;
    border-bottom:1px dashed rgba(0,245,255,.3);
  }
  .hud-title .dot{ width:8px; height:8px; border-radius:50%; background:var(--cy-green,#39ff14); box-shadow:0 0 8px var(--cy-green,#39ff14); animation: bk 1.4s infinite; }
  @keyframes bk{ 0%,100%{ opacity:1 } 50%{ opacity:.3 } }

  /* TERMINAL log */
  .term{ font:12px 'Share Tech Mono',monospace; max-height:340px; overflow:hidden; }
  .term .ln{ opacity:0; animation: lnshow .3s forwards; padding:1px 0; }
  .term .ln.gn{ color:var(--cy-green,#39ff14); }
  .term .ln.cy{ color:var(--cy-cyan,#00f5ff); }
  .term .ln.mg{ color:var(--cy-magenta,#ff2bd6); }
  .term .ln.am{ color:#ffb800; }
  .term .ln.dm{ color:#7fb3c2; }
  @keyframes lnshow{ to{ opacity:1 } }

  /* HEX GRID licenses */
  .hex-grid{ display:grid; grid-template-columns: repeat(10, 1fr); gap:2px 4px; padding:6px; }
  .hex{
    position:relative; aspect-ratio: 1/1.15;
    clip-path: polygon(50% 0,100% 25%,100% 75%,50% 100%,0 75%,0 25%);
    background: rgba(0,245,255,.06); border:1px solid rgba(0,245,255,.2);
    transition: all .2s;
  }
  .hex:nth-child(even){ margin-top:14%; }
  .hex.act{ background: rgba(57,255,20,.4); box-shadow: inset 0 0 8px var(--cy-green,#39ff14); border-color: var(--cy-green,#39ff14); }
  .hex.exp{ background: rgba(255,184,0,.35); border-color:#ffb800; }
  .hex.dead{ background: rgba(255,43,214,.25); border-color: var(--cy-magenta,#ff2bd6); }
  .hex:hover{ transform: scale(1.15); z-index:3; cursor:pointer; box-shadow: 0 0 16px var(--cy-cyan,#00f5ff); }

  /* RADAR (profile) */
  .radar{ width:100%; aspect-ratio:1; position:relative; }
  .radar .ring{ position:absolute; inset:0; border-radius:50%; border:1px dashed rgba(0,245,255,.3); }
  .radar .ring:nth-child(2){ inset:15%; } .radar .ring:nth-child(3){ inset:30%; } .radar .ring:nth-child(4){ inset:45%; }
  .radar .sweep{
    position:absolute; inset:0; border-radius:50%;
    background: conic-gradient(from 0deg, transparent 80%, rgba(0,245,255,.4) 95%, transparent 100%);
    animation: sw 3.5s linear infinite;
  }
  @keyframes sw{ to{ transform: rotate(360deg) } }
  .radar .core{
    position:absolute; left:50%; top:50%; width:32px; height:32px; border-radius:50%;
    transform: translate(-50%,-50%);
    background: linear-gradient(135deg, var(--cy-cyan,#00f5ff), var(--cy-magenta,#ff2bd6));
    box-shadow: 0 0 20px var(--cy-cyan,#00f5ff); display:flex; align-items:center; justify-content:center;
    color:#000; font-weight:900;
  }
  .radar .blip{
    position:absolute; width:6px; height:6px; border-radius:50%; background:var(--cy-green,#39ff14);
    box-shadow: 0 0 8px var(--cy-green,#39ff14);
  }

  /* TICKER bottom */
  .ticker{
    position:fixed; left:0; right:0; bottom:0; height:34px; z-index:50;
    background:rgba(0,8,16,.95); border-top:1px solid var(--cy-cyan,#00f5ff);
    overflow:hidden; display:flex; align-items:center;
    box-shadow: 0 -8px 24px rgba(0,245,255,.15);
  }
  .ticker .label{ background:var(--cy-cyan,#00f5ff); color:#000; padding:6px 12px; font:700 10px 'Share Tech Mono',monospace; letter-spacing:.2em; margin-right:12px; }
  .ticker .track{
    flex:1; overflow:hidden; white-space:nowrap;
    font:12px 'Share Tech Mono',monospace; color:#cfe7ee;
  }
  .ticker .track span{ display:inline-block; padding-right:60px; animation: tk 38s linear infinite; }
  @keyframes tk{ from{ transform:translateX(0) } to{ transform:translateX(-50%) } }
  .ticker .sep{ color:var(--cy-magenta,#ff2bd6); padding:0 8px; }

  /* BOOT overlay */
  .boot{ position:fixed; inset:0; background:#000; z-index:9998; color:var(--cy-green,#39ff14); font:13px 'Share Tech Mono',monospace; padding:24px; animation: bootout 0s 2.4s forwards; }
  @keyframes bootout{ to{ opacity:0; visibility:hidden } }
  .boot .ln{ opacity:0; animation: lnshow .2s forwards; }
</style>
</head>
<body class="min-h-screen pb-12">

<!-- Boot screen -->
<div class="boot" id="boot">
  <div class="ln" style="animation-delay:.0s">&gt; SX2.LADOR MAINFRAME v2.6.0 (build 2026.06)</div>
  <div class="ln" style="animation-delay:.15s">&gt; Initializing neural net link...... OK</div>
  <div class="ln" style="animation-delay:.30s">&gt; Mounting /vault/keys ............. OK</div>
  <div class="ln" style="animation-delay:.45s">&gt; Loading session [<?= esc($user->username ?? 'guest') ?>] OK</div>
  <div class="ln" style="animation-delay:.6s">&gt; Quantum cipher handshake ........ OK</div>
  <div class="ln" style="animation-delay:.75s">&gt; Establishing uplink to MAINFRAME ... OK</div>
  <div class="ln" style="animation-delay:.95s" class="cy">&gt; All systems nominal. Entering control grid...</div>
  <div class="ln" style="animation-delay:1.2s; color:#fff">&gt; <span style="background:#fff;color:#000;padding:0 6px;animation: bk 1s infinite">▮ READY</span></div>
</div>

<div class="matrix-rain"></div>
<div class="grid-floor"></div>

<!-- ====== TOP SYSTEM BAR ====== -->
<header class="sys-bar px-6 py-4 border-b" style="border-color: rgba(0,245,255,.3); background:linear-gradient(180deg, rgba(0,8,16,.9), rgba(0,8,16,.5))">
  <div class="flex items-center justify-between gap-8">
    <!-- LOGO -->
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 flex items-center justify-center" style="background:linear-gradient(135deg,var(--cy-cyan,#00f5ff),var(--cy-magenta,#ff2bd6));clip-path:polygon(20% 0,80% 0,100% 50%,80% 100%,20% 100%,0 50%);box-shadow:0 0 20px var(--cy-cyan,#00f5ff)">
        <i class="fas fa-shield-alt text-black text-xl"></i>
      </div>
      <div>
        <div style="font:900 18px 'Orbitron',sans-serif; background:linear-gradient(90deg,#fff,var(--cy-cyan,#00f5ff)); -webkit-background-clip:text; background-clip:text; color:transparent">SX2.LADOR</div>
        <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.2em">// MAINFRAME.NODE.01</div>
      </div>
    </div>

    <!-- 4 GAUGES -->
    <div class="flex items-center gap-7">
      <?php
        $g_cpu = rand(35, 78);
        $g_mem = rand(40, 85);
        $g_net = rand(15, 95);
        $g_upt = 99;
        function gauge_rot($v){ return -90 + ($v / 100) * 180; }
      ?>
      <div class="gauge">
        <div class="ring"></div>
        <?php for($i=0;$i<11;$i++): ?><div class="tick" style="transform: translateX(-50%) rotate(<?= -90 + $i*18 ?>deg)"></div><?php endfor; ?>
        <div class="needle" style="transform: rotate(<?= gauge_rot($g_cpu) ?>deg)"></div>
        <div class="center"></div>
        <div class="val"><?= $g_cpu ?>%</div>
        <div class="lbl">CPU</div>
      </div>
      <div class="gauge">
        <div class="ring"></div>
        <?php for($i=0;$i<11;$i++): ?><div class="tick" style="transform: translateX(-50%) rotate(<?= -90 + $i*18 ?>deg)"></div><?php endfor; ?>
        <div class="needle" style="transform: rotate(<?= gauge_rot($g_mem) ?>deg); background:linear-gradient(180deg,#fff,var(--cy-magenta,#ff2bd6)); box-shadow:0 0 8px var(--cy-magenta,#ff2bd6)"></div>
        <div class="center" style="background:var(--cy-magenta,#ff2bd6); box-shadow:0 0 10px var(--cy-magenta,#ff2bd6)"></div>
        <div class="val"><?= $g_mem ?>%</div>
        <div class="lbl" style="color:var(--cy-magenta,#ff2bd6)">MEM</div>
      </div>
      <div class="gauge">
        <div class="ring"></div>
        <?php for($i=0;$i<11;$i++): ?><div class="tick" style="transform: translateX(-50%) rotate(<?= -90 + $i*18 ?>deg)"></div><?php endfor; ?>
        <div class="needle" style="transform: rotate(<?= gauge_rot($g_net) ?>deg); background:linear-gradient(180deg,#fff,var(--cy-green,#39ff14)); box-shadow:0 0 8px var(--cy-green,#39ff14)"></div>
        <div class="center" style="background:var(--cy-green,#39ff14); box-shadow:0 0 10px var(--cy-green,#39ff14)"></div>
        <div class="val"><?= $g_net ?>%</div>
        <div class="lbl" style="color:var(--cy-green,#39ff14)">NET</div>
      </div>
      <div class="gauge">
        <div class="ring"></div>
        <?php for($i=0;$i<11;$i++): ?><div class="tick" style="transform: translateX(-50%) rotate(<?= -90 + $i*18 ?>deg)"></div><?php endfor; ?>
        <div class="needle" style="transform: rotate(<?= gauge_rot($g_upt) ?>deg); background:linear-gradient(180deg,#fff,#ffb800); box-shadow:0 0 8px #ffb800"></div>
        <div class="center" style="background:#ffb800; box-shadow:0 0 10px #ffb800"></div>
        <div class="val"><?= $g_upt ?>%</div>
        <div class="lbl" style="color:#ffb800">UPTIME</div>
      </div>
    </div>

    <!-- OPERATOR + LOGOUT -->
    <div class="flex items-center gap-4">
      <div class="text-right">
        <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.2em">// OPERATOR</div>
        <div style="font:700 14px 'Orbitron',sans-serif; color:var(--cy-cyan,#00f5ff); text-shadow: 0 0 8px rgba(0,245,255,.6)"><?= strtoupper(esc($user->username ?? 'GUEST')) ?></div>
        <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2">$<?= number_format($user->saldo ?? 0, 2) ?> CREDIT</div>
      </div>
      <a href="<?= site_url('logout') ?>" title="DISCONNECT" style="display:flex;align-items:center;justify-content:center;width:44px;height:44px;border:1px solid var(--cy-magenta,#ff2bd6); color:var(--cy-magenta,#ff2bd6); box-shadow: inset 0 0 12px rgba(255,43,214,.2)">
        <i class="fas fa-power-off"></i>
      </a>
    </div>
  </div>

  <!-- ====== NAV TABS ====== -->
  <nav class="nav-tabs mt-5">
    <a href="<?= site_url('dashboard') ?>" class="on"><i class="fas fa-chart-pie mr-2"></i>DASH</a>
    <a href="<?= site_url('keys') ?>"><i class="fas fa-key mr-2"></i>KEYS</a>
    <a href="<?= site_url('keys/generate') ?>"><i class="fas fa-bolt mr-2"></i>FORGE</a>
    <?php if (isset($user->level) && $user->level == 1) : ?>
    <a href="<?= site_url('admin/manage-users') ?>"><i class="fas fa-users mr-2"></i>USERS</a>
    <a href="<?= site_url('admin/create-referral') ?>"><i class="fas fa-user-plus mr-2"></i>SPAWN</a>
    <?php endif; ?>
    <a href="<?= site_url('settings') ?>"><i class="fas fa-cog mr-2"></i>SYS</a>
  </nav>
</header>

<!-- ====== FLASH ====== -->
<div class="px-6 mt-4 relative z-10">
<?php if (session()->getFlashdata('msgDanger')) : ?>
  <div class="hud" style="border-color:var(--cy-magenta,#ff2bd6); color:var(--cy-magenta,#ff2bd6)">
    <div class="hud-title">// ALERT.MAGENTA<span class="dot" style="background:var(--cy-magenta,#ff2bd6); box-shadow:0 0 8px var(--cy-magenta,#ff2bd6)"></span></div>
    <?= session()->getFlashdata('msgDanger') ?>
  </div>
<?php endif; ?>
<?php if (session()->getFlashdata('msgSuccess')) : ?>
  <div class="hud" style="border-color:var(--cy-green,#39ff14); color:var(--cy-green,#39ff14)">
    <div class="hud-title">// CONFIRMED<span class="dot"></span></div>
    <?= session()->getFlashdata('msgSuccess') ?>
  </div>
<?php endif; ?>
</div>

<!-- ====== 3 COLUMN GRID ====== -->
<main class="px-6 py-5 relative z-10 grid gap-5" style="grid-template-columns: 1fr 1.4fr 1fr">

  <!-- ===== COL 1: LIVE TERMINAL ===== -->
  <section class="hud">
    <div class="hud-title">// SYS.CONSOLE<span class="dot"></span></div>
    <div class="term" id="term"></div>
  </section>

  <!-- ===== COL 2: HEX MAP + STATS ===== -->
  <section>
    <!-- HEX MAP -->
    <div class="hud" style="border-color: var(--cy-cyan,#00f5ff)">
      <div class="hud-title">
        <span>// LICENSE.GRID [<?= str_pad($stats['total_keys'] ?? 0, 4, '0', STR_PAD_LEFT) ?>]</span>
        <span style="font-size:9px; color:#7fb3c2">
          <span style="color:var(--cy-green,#39ff14)">● ACTIVE</span>
          <span style="color:#ffb800; margin-left:8px">● EXPIRING</span>
          <span style="color:var(--cy-magenta,#ff2bd6); margin-left:8px">● DEAD</span>
        </span>
      </div>
      <?php
        $total = (int)($stats['total_keys'] ?? 60);
        $active = (int)($stats['active_keys'] ?? 0);
        $unused = (int)($stats['unused_keys'] ?? 0);
        $cells = 60;
        $on = min($active, $cells);
        $exp = min(8, max(0, $cells - $on));
        $dead = max(0, $cells - $on - $exp);
      ?>
      <div class="hex-grid">
        <?php for($i=0;$i<$on;$i++): ?><div class="hex act" title="ACTIVE"></div><?php endfor; ?>
        <?php for($i=0;$i<$exp;$i++): ?><div class="hex exp" title="EXPIRING"></div><?php endfor; ?>
        <?php for($i=0;$i<$dead;$i++): ?><div class="hex dead" title="DEAD"></div><?php endfor; ?>
      </div>
    </div>

    <!-- QUICK STATS bar -->
    <div class="hud">
      <div class="hud-title">// QUICK.READOUT<span class="dot"></span></div>
      <div class="grid grid-cols-3 gap-3" style="font:'Share Tech Mono',monospace">
        <div style="border-left:3px solid var(--cy-green,#39ff14); padding-left:10px">
          <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.2em">ACTIVE</div>
          <div style="font:900 28px 'Orbitron',sans-serif; color:var(--cy-green,#39ff14); text-shadow:0 0 10px var(--cy-green,#39ff14)"><?= str_pad($stats['active_keys'] ?? 0, 3, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div style="border-left:3px solid var(--cy-cyan,#00f5ff); padding-left:10px">
          <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.2em">STOCK</div>
          <div style="font:900 28px 'Orbitron',sans-serif; color:var(--cy-cyan,#00f5ff); text-shadow:0 0 10px var(--cy-cyan,#00f5ff)"><?= str_pad($stats['unused_keys'] ?? 0, 3, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div style="border-left:3px solid var(--cy-magenta,#ff2bd6); padding-left:10px">
          <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.2em">TOTAL</div>
          <div style="font:900 28px 'Orbitron',sans-serif; color:var(--cy-magenta,#ff2bd6); text-shadow:0 0 10px var(--cy-magenta,#ff2bd6)"><?= str_pad($stats['total_keys'] ?? 0, 3, '0', STR_PAD_LEFT) ?></div>
        </div>
      </div>
    </div>

    <!-- FORGE BUTTON -->
    <a href="<?= site_url('keys/generate') ?>" class="hud" style="display:block; text-align:center; padding:24px; text-decoration:none; background: linear-gradient(135deg, rgba(0,245,255,.15), rgba(255,43,214,.15)); cursor:pointer">
      <div style="font:11px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.3em; margin-bottom:6px">▸ QUICK ACCESS ◂</div>
      <div style="font:900 24px 'Orbitron',sans-serif; background:linear-gradient(90deg,var(--cy-cyan,#00f5ff),var(--cy-magenta,#ff2bd6)); -webkit-background-clip:text; background-clip:text; color:transparent">
        ⚡ FORGE NEW KEYS
      </div>
      <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2; margin-top:4px">&gt; Press to initialize key generator subroutine</div>
    </a>
  </section>

  <!-- ===== COL 3: RADAR + INFO ===== -->
  <section>
    <!-- RADAR profile -->
    <div class="hud">
      <div class="hud-title">// OPERATOR.PROFILE<span class="dot"></span></div>
      <div class="radar mb-3">
        <div class="ring"></div><div class="ring"></div><div class="ring"></div><div class="ring"></div>
        <div class="sweep"></div>
        <div class="blip" style="left:30%; top:25%"></div>
        <div class="blip" style="left:70%; top:60%"></div>
        <div class="blip" style="left:40%; top:78%"></div>
        <div class="core"><?= strtoupper(substr($user->username ?? 'X', 0, 1)) ?></div>
      </div>
      <div style="text-align:center">
        <div style="font:700 18px 'Orbitron',sans-serif; color:var(--cy-cyan,#00f5ff); text-shadow:0 0 8px rgba(0,245,255,.5)"><?= strtoupper(esc($user->username ?? 'GUEST')) ?></div>
        <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.2em">LVL <?= $user->level ?? 0 ?> // <?= ($user->level ?? 0) == 1 ? 'ROOT' : (($user->level ?? 0) == 3 ? 'RESELLER' : 'USER') ?></div>
      </div>
    </div>

    <!-- CREDITS -->
    <div class="hud" style="border-color:#ffb800">
      <div class="hud-title" style="color:#ffb800">// CREDIT.VAULT<span class="dot" style="background:#ffb800; box-shadow:0 0 8px #ffb800"></span></div>
      <div style="text-align:center; padding:10px 0">
        <div style="font:900 36px 'Share Tech Mono',monospace; color:#ffb800; text-shadow:0 0 12px rgba(255,184,0,.6)">$<?= number_format($user->saldo ?? 0, 2) ?></div>
        <div style="font:10px 'Share Tech Mono',monospace; color:#7fb3c2; letter-spacing:.2em; margin-top:4px">AVAILABLE BALANCE</div>
      </div>
      <div style="display:flex; gap:6px; margin-top:8px; font:10px 'Share Tech Mono',monospace">
        <div style="flex:1; padding:6px; text-align:center; border:1px solid rgba(0,245,255,.3); color:var(--cy-cyan,#00f5ff)">EXP: <?= esc($user->exp_date ?? '∞') ?></div>
      </div>
    </div>

    <!-- SYSTEM INFO -->
    <div class="hud">
      <div class="hud-title">// SESSION.INFO<span class="dot"></span></div>
      <div style="font:11px 'Share Tech Mono',monospace; color:#cfe7ee; line-height:1.9">
        <div>&gt; IP_ADDR: <span style="color:var(--cy-cyan,#00f5ff)"><?= $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' ?></span></div>
        <div>&gt; SESSION: <span style="color:var(--cy-green,#39ff14)">ENCRYPTED</span></div>
        <div>&gt; CIPHER: <span style="color:var(--cy-cyan,#00f5ff)">AES-256/RC4</span></div>
        <div>&gt; UPLINK: <span style="color:var(--cy-green,#39ff14)">STABLE 99.97%</span></div>
        <div>&gt; PROTOCOL: <span style="color:#ffb800">XXTEA+RSA</span></div>
      </div>
    </div>
  </section>
</main>

<!-- ====== TICKER ====== -->
<div class="ticker">
  <div class="label">// LIVE.FEED</div>
  <div class="track"><span>
    <i class="fas fa-broadcast-tower" style="color:var(--cy-green,#39ff14)"></i> SYS UP
    <span class="sep">▸</span>
    LIC_ACTIVE: <?= $stats['active_keys'] ?? 0 ?>
    <span class="sep">▸</span>
    STOCK: <?= $stats['unused_keys'] ?? 0 ?>
    <span class="sep">▸</span>
    CREDITS: $<?= number_format($user->saldo ?? 0, 2) ?>
    <span class="sep">▸</span>
    NODE_01 ONLINE
    <span class="sep">▸</span>
    NODE_02 ONLINE
    <span class="sep">▸</span>
    NODE_03 ONLINE
    <span class="sep">▸</span>
    QUANTUM_SHIELD <span style="color:var(--cy-green,#39ff14)">ACTIVE</span>
    <span class="sep">▸</span>
    LAST_KEY: HC-<?= strtoupper(substr(md5(rand()), 0, 4)) ?>-<?= strtoupper(substr(md5(rand()), 0, 4)) ?>
    <span class="sep">▸</span>
    UPTIME 99.97%
    <span class="sep">▸</span>
    THREATS_BLOCKED: <?= rand(1200, 9999) ?>
    <span class="sep">▸</span>
    NEURAL_NET <span style="color:var(--cy-cyan,#00f5ff)">SYNCED</span>
    <span class="sep">▸</span>
    <!-- LẶP để mượt -->
    SYS UP
    <span class="sep">▸</span>
    LIC_ACTIVE: <?= $stats['active_keys'] ?? 0 ?>
    <span class="sep">▸</span>
    STOCK: <?= $stats['unused_keys'] ?? 0 ?>
    <span class="sep">▸</span>
    CREDITS: $<?= number_format($user->saldo ?? 0, 2) ?>
    <span class="sep">▸</span>
    NODE_01 ONLINE
  </span></div>
</div>

<!-- ====== JS: terminal log feed ====== -->
<script>
(function(){
  var term = document.getElementById('term');
  var pool = [
    {c:'gn', t:'[OK] vault.unlock... license_id=<?= $user->id_users ?? 'X' ?>'},
    {c:'cy', t:'[INFO] uplink=mainframe.01 latency=12ms'},
    {c:'dm', t:'[..] scanning license pool ... <?= $stats['total_keys'] ?? 0 ?> entries'},
    {c:'gn', t:'[OK] <?= $stats['active_keys'] ?? 0 ?> licenses active // verified'},
    {c:'cy', t:'[INFO] credit.balance = $<?= number_format($user->saldo ?? 0, 2) ?>'},
    {c:'am', t:'[WARN] 2 licenses expiring in <72h'},
    {c:'dm', t:'[..] sync user.<?= esc($user->username ?? "x") ?> profile'},
    {c:'gn', t:'[OK] cipher.handshake AES-256/RC4'},
    {c:'mg', t:'[ALERT] firewall blocked 47 packets from 91.x.x.x'},
    {c:'dm', t:'[..] node.heartbeat // 0001 0002 0003 OK'},
    {c:'cy', t:'[INFO] cache.refresh complete // 234 keys cached'},
    {c:'gn', t:'[OK] auth.session token rotated'},
    {c:'dm', t:'[..] db.replication // 99.99% in-sync'},
    {c:'cy', t:'[INFO] avg.query_time = 4.2ms'},
    {c:'gn', t:'[OK] backup.snapshot saved // 14:32:01'},
    {c:'mg', t:'[ALERT] anomaly detected // node 02 :: false alarm'},
    {c:'dm', t:'[..] heartbeat: 60s tick'},
    {c:'gn', t:'[OK] all systems nominal'},
  ];
  var i = 0;
  function add(){
    if(term.childElementCount > 24) term.removeChild(term.firstElementChild);
    var p = pool[Math.floor(Math.random()*pool.length)];
    var d = new Date();
    var ts = String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0')+':'+String(d.getSeconds()).padStart(2,'0');
    var el = document.createElement('div');
    el.className = 'ln ' + p.c;
    el.textContent = ts + ' ' + p.t;
    term.appendChild(el);
  }
  // initial fill
  for(var k=0;k<14;k++) add();
  setInterval(add, 1800);
})();
</script>

</body>
</html>
