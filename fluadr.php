<?php
/**
 * Fluorite Root Android Key Management System v7.0 - Pro Edition
 * Integrated with Direct JSON API & Associative DB
 */

session_start();

// --- Settings and paths (same as your first method) ---
$json_file = 'rjsjsjskakakjfjrjwkakakzkfjf82owoqkakr902q00wosksofktkr.json';
$maintenance_mode_file = 'maintenance_mode.txt';
$maintenance_mode = file_exists($maintenance_mode_file) && file_get_contents($maintenance_mode_file) === '1';

// Admin login data
$admin_user = "SanNsosjsisjisudy27727";
$admin_pass = "Sannaaohsisu736";

// Error messages for the application in Italian
$italian_messages = [
    'invalid_key' => 'Chiave non trovata',
    'banned' => 'La tua licenza è stata bannata!',
    'expired' => 'La tua licenza è scaduta',
    'device_limit' => 'Limite massimo di dispositivi raggiunto',
    'maintenance' => 'Il server è in manutenzione'
];

function get_data($file) {
    if (!file_exists($file)) file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
    return json_decode(file_get_contents($file), true) ?: [];
}

function save_data($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

class Utils {
    public static function generateKey($days = 30) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $random_part = '';
        for ($i = 0; $i < 7; $i++) {
            $random_part .= $characters[rand(0, strlen($characters) - 1)];
        }
        return 'FLUORITE-' . $random_part;
    }
}

// ==========================================
// 1. API: Process direct application requests (without encryption)
// ==========================================
$requestBody = file_get_contents('php://input');
$requestData = json_decode($requestBody, true);

// Check if the request is coming from the application (API Request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($requestData['app_id'])) {
    header('Content-Type: application/json; charset=UTF-8');
    
    $keyInput = $requestData['license_key'] ?? '';
    $hwidInput = $requestData['hwid'] ?? '';
    $usernameInput = $requestData['username'] ?? '';
    $pcNameInput = $requestData['pc_name'] ?? '';
    $subTypeInput = $requestData['subscription_type'] ?? 'Root';
    
    $all_keys = get_data($json_file);
    
    // Check Maintenance mode
    if ($maintenance_mode) {
        echo json_encode(["success" => false, "message" => $italian_messages['maintenance']]);
        exit;
    }

    // Check if key exists
    if (!isset($all_keys[$keyInput])) {
        echo json_encode(["success" => false, "message" => $italian_messages['invalid_key']]);
        exit;
    }
    
    $key_data = &$all_keys[$keyInput];
    
    // Check ban status
    if (($key_data['status'] ?? 'active') === 'banned') {
        echo json_encode(["success" => false, "message" => $italian_messages['banned']]);
        exit;
    }

    $now = time();
    
    // Activate key on first use
    if (empty($key_data['expiry_date'])) {
        $duration = intval($key_data['duration_days'] ?? 0) * 86400;
        $key_data['expiry_date'] = $now + $duration;
        $key_data['start_date'] = $now;
        save_data($json_file, $all_keys);
    }
    
    // Check validity
    if ($now > $key_data['expiry_date']) {
        echo json_encode(["success" => false, "message" => $italian_messages['expired']]);
        exit;
    }
    
    // Check devices
    $uids = !empty($key_data['registered_uids']) ? explode(',', $key_data['registered_uids']) : [];
    $device_ok = false;
    
    if (in_array($hwidInput, $uids)) {
        $device_ok = true; 
    } elseif (count($uids) < intval($key_data['max_devices'] ?? 1)) {
        if (!empty($hwidInput)) {
            $uids[] = $hwidInput;
            $key_data['registered_uids'] = implode(',', $uids);
            $key_data['used_devices'] = count($uids);
            save_data($json_file, $all_keys);
        }
        $device_ok = true; 
    } else {
        echo json_encode(["success" => false, "message" => $italian_messages['device_limit']]);
        exit;
    }
    
    // Success response
    if ($device_ok) {
        $expiry_iso = date('Y-m-d\TH:i:s.000000', $key_data['expiry_date']);
        echo json_encode([
            "success" => true,
            "message" => "License authentication successful",
            "token" => "AgpbKApDZVIFD1VLWABkFWlkXjQIUA4/IV9JdzQ+DgcTXg8KWnVkUwN9WkRadWFuYGc=",
            "expiry" => $expiry_iso,
            "subscriptions" => [
                [
                    "id" => 20162,
                    "name" => $subTypeInput,
                    "expires_at" => $expiry_iso,
                    "is_active" => true
                ]
            ],
            "local_ipv4" => "151.38.23.72"
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

// ==========================================
// 2. Dashboard Logic (Control Panel Processing)
// ==========================================
$sn = $_SERVER['SCRIPT_NAME'];
$msg = ""; $mtype = "ok";
if (!isset($_SESSION['night_mode'])) $_SESSION['night_mode'] = true; // default theme

// Process login and logout
if (isset($_POST['login_pass'])) {
    if ($_POST['login_user'] === $admin_user && $_POST['login_pass'] === $admin_pass) {
        $_SESSION['logged_in'] = true;
    } else {
        $msg = "Incorrect login details"; $mtype = "err";
    }
}
if (isset($_GET['logout'])) { session_destroy(); header("Location: $sn"); exit; }

if (isset($_SESSION['logged_in'])) {
    $db = get_data($json_file);
    
    // Enable/disable maintenance
    if (isset($_GET['tm'])) {
        $maintenance_mode = !$maintenance_mode;
        file_put_contents($maintenance_mode_file, $maintenance_mode ? '1' : '0');
        header("Location: $sn"); exit;
    }
    
    // Toggle theme
    if (isset($_GET['tn'])) {
        $_SESSION['night_mode'] = !$_SESSION['night_mode'];
        header("Location: $sn"); exit;
    }

    // Add new key manually
    if (isset($_POST['add_key'])) {
        $nk = trim($_POST['key']) ?: Utils::generateKey((int)($_POST['ad'] ?? 30));
        if (!isset($db[$nk])) {
            $db[$nk] = [
                'duration_days' => intval($_POST['ad'] ?? 30),
                'max_devices' => intval($_POST['max_dev'] ?? 1),
                'used_devices' => 0,
                'registered_uids' => '',
                'status' => 'active',
                'vendedor' => 'Admin',
                'expiry_date' => null, // activated on first use
                'start_date' => null,
                'created_at' => time(),
                'notes' => trim($_POST['note'] ?? '')
            ];
            save_data($json_file, $db);
            $msg = "✓ Key created: $nk";
        } else {
            $msg = "Key already exists"; $mtype = "warn";
        }
    }

    // Auto generate
    if (isset($_POST['auto_gen'])) {
        $d = (int)$_POST['ag_d']; $c = (int)$_POST['ag_c']; $m = (int)($_POST['ag_m'] ?? 1);
        for ($i = 0; $i < $c; $i++) {
            $nk = Utils::generateKey($d);
            $db[$nk] = [
                'duration_days' => $d,
                'max_devices' => $m,
                'used_devices' => 0,
                'registered_uids' => '',
                'status' => 'active',
                'vendedor' => 'AutoGen',
                'expiry_date' => null,
                'start_date' => null,
                'created_at' => time(),
                'notes' => 'Auto'
            ];
        }
        save_data($json_file, $db);
        $msg = "✓ Successfully generated $c keys";
    }

    // Edit key
    if (isset($_POST['edit_key'])) {
        $ek = $_POST['ek_id'];
        if (isset($db[$ek])) {
            // Convert date from input to timestamp
            $db[$ek]['expiry_date'] = !empty($_POST['ek_exp']) ? strtotime($_POST['ek_exp']) : null;
            $db[$ek]['max_devices'] = (int)$_POST['ek_max'];
            $db[$ek]['notes'] = trim($_POST['ek_note'] ?? '');
            save_data($json_file, $db);
            $msg = "✓ Key updated";
        }
    }

    // Quick action buttons
    if (isset($_GET['act'], $_GET['k'])) {
        $ak = $_GET['k'];
        if (isset($db[$ak])) {
            switch ($_GET['act']) {
                case 'add_days': 
                    $d = (int)($_GET['d'] ?? 30);
                    if ($db[$ak]['expiry_date']) {
                        $now = time();
                        $base = $db[$ak]['expiry_date'] < $now ? $now : $db[$ak]['expiry_date'];
                        $db[$ak]['expiry_date'] = $base + ($d * 86400);
                    } else {
                        $db[$ak]['duration_days'] += $d; // if not yet activated, increase duration
                    }
                    break;
                case 'ban':   $db[$ak]['status'] = 'banned'; break;
                case 'unban': $db[$ak]['status'] = 'active'; break;
                case 'reset': 
                    $db[$ak]['registered_uids'] = ''; 
                    $db[$ak]['used_devices'] = 0; 
                    break;
                case 'del':   unset($db[$ak]); break;
            }
            save_data($json_file, $db);
        }
        header("Location: $sn"); exit;
    }
}

// Calculate statistics
$db = get_data($json_file);
$total = count($db);
$active = 0; $banned = 0; $expd = 0; $devs = 0;
$now = time();

foreach ($db as $v) {
    $is_expired = !empty($v['expiry_date']) && $v['expiry_date'] < $now;
    if (($v['status'] ?? 'active') === 'banned') {
        $banned++;
    } elseif ($is_expired) {
        $expd++;
    } else {
        $active++;
    }
    $devs += $v['used_devices'] ?? 0;
}

$night = $_SESSION['night_mode'];

function days_left($e_timestamp) {
    if (!$e_timestamp) return 0;
    $diff = $e_timestamp - time();
    return $diff > 0 ? floor($diff / 86400) : 0;
}

// Draw key card to match your data
function kcard($key, $k, $actions=false) {
    global $now;
    $is_expired = !empty($k['expiry_date']) && $k['expiry_date'] < $now;
    $is_banned = ($k['status'] ?? 'active') === 'banned';
    
    $exp_str = !empty($k['expiry_date']) ? date("Y-m-d", $k['expiry_date']) : 'Not activated';
    $dl = days_left($k['expiry_date'] ?? 0);
    if(empty($k['expiry_date'])) $dl = $k['duration_days'] ?? 0; // show duration days if not activated
    
    $maxd = (int)($k['max_devices'] ?? 1);
    $uidc = (int)($k['used_devices'] ?? 0);
    $note = htmlspecialchars($k['notes'] ?? '');
    $cat = !empty($k['created_at']) ? date("Y-m-d", $k['created_at']) : '';

    if ($is_banned) { $st = 'banned'; $badge = '<span class="kb kb-ban"><i class="fas fa-ban"></i>Forbidden</span>'; }
    elseif ($is_expired) { $st = 'expd'; $badge = '<span class="kb kb-expd"><i class="fas fa-calendar-xmark"></i>Expired</span>'; }
    else { $st = 'active'; $badge = '<span class="kb kb-active"><i class="fas fa-circle-check"></i>active</span>'; }

    $pct = $maxd > 0 ? min(100, round($uidc / $maxd * 100)) : 0;
    $pc = $pct >= 100 ? 'var(--red)' : ($pct >= 70 ? 'var(--amber)' : 'var(--green)');
    
    $uk = urlencode($key); $kjs = addslashes($key); $njs = addslashes($note);
    $exp_for_edit = !empty($k['expiry_date']) ? date("Y-m-d", $k['expiry_date']) : '';

    $act = '';
    if ($actions) {
        $banB = !$is_banned
            ? "<a href='?act=ban&k=$uk' class='iBtn red' title='Ban' onclick='return confirm(\"Ban?\")'><i class='fas fa-ban'></i></a>"
            : "<a href='?act=unban&k=$uk' class='iBtn grn' title='Unban'><i class='fas fa-unlock'></i></a>";
        
        $act = "<div class='kacts'>
          <button class='iBtn' onclick='cp(\"$kjs\",this)' title='Copy'><i class='fas fa-copy'></i></button>
          <button class='iBtn' onclick='openEdit(\"$kjs\",\"$exp_for_edit\",$maxd,\"$njs\")' title='Edit'><i class='fas fa-pen'></i></button>
          <div class='ddw'>
            <button class='iBtn' onclick='tdd(this)' title='Add days'><i class='fas fa-calendar-plus'></i></button>
            <div class='ddm'>
              <a class='ddi' href='?act=add_days&k=$uk&d=7'>+7 days</a>
              <a class='ddi' href='?act=add_days&k=$uk&d=15'>+15 days</a>
              <a class='ddi' href='?act=add_days&k=$uk&d=30'>+30 days</a>
              <a class='ddi' href='?act=add_days&k=$uk&d=60'>+60 days</a>
              <a class='ddi' href='?act=add_days&k=$uk&d=90'>+90 days</a>
            </div>
          </div>
          <a href='?act=reset&k=$uk' class='iBtn amb' title='Reset' onclick='return confirm(\"Reset devices?\")'><i class='fas fa-rotate'></i></a>
          $banB
          <a href='?act=del&k=$uk' class='iBtn red' title='Delete' onclick='return confirm(\"Delete permanently?\")'><i class='fas fa-trash'></i></a>
        </div>";
    }

    return "<div class='kc kc-$st' data-st='$st' data-k='".strtolower($key)."'>
      <div class='kc-bar'></div>
      <div class='kc-main'>
        <div class='kval'>
          <code>$key</code>
          <button class='cpb' onclick='cp(\"$kjs\",this)' title='Copy'><i class='fas fa-copy'></i></button>
          $badge
        </div>
        <div class='kmeta'>
          <span><i class='fas fa-calendar-alt'></i>$exp_str</span>
          <span><i class='fas fa-hourglass-half'></i>{$dl} days</span>
          ".($note ? "<span><i class='fas fa-tag'></i>$note</span>" : "")."
          ".($cat ? "<span><i class='fas fa-clock'></i>$cat</span>" : "")."
        </div>
      </div>
      <div class='kdev'>
        <div class='kdn' style='color:$pc'>$uidc/$maxd</div>
        <div class='kdl'>Devices</div>
        <div class='kpw'><div class='kpf' style='width:{$pct}%;background:$pc'></div></div>
      </div>
      $act
    </div>";
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Fluorite Root Android · Pro Key Panel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Cairo:wght@400;600;700;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
/* CSS professional interface (with all its features, without deleting a single character) */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:    <?php echo $night?'#06090f':'#f0f3fa'; ?>;
  --bg2:   <?php echo $night?'#0c1220':'#ffffff'; ?>;
  --bg3:   <?php echo $night?'#101928':'#f4f7fd'; ?>;
  --bdr:   <?php echo $night?'rgba(255,255,255,.07)':'rgba(0,0,0,.09)'; ?>;
  --txt:   <?php echo $night?'#dde4f5':'#0d1629'; ?>;
  --txt2:  <?php echo $night?'#4d6a99':'#7080a0'; ?>;
  --cyan:  #00e5ff;
  --pur:   #7c3aed;
  --green: #10b981;
  --red:   #ef4444;
  --amber: #f59e0b;
  --blue:  #3b82f6;
  --sh:    <?php echo $night?'0 8px 48px rgba(0,0,0,.7)':'0 8px 40px rgba(0,0,0,.1)'; ?>;
}
html{scroll-behavior:smooth}
body{font-family:'Cairo',sans-serif;background:var(--bg);color:var(--txt);min-height:100vh;overflow-x:hidden;transition:background .4s,color .4s}

body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background:radial-gradient(ellipse 60% 40% at 80% 10%,rgba(0,229,255,<?php echo $night?.06:.03; ?>) 0%,transparent 70%),
             radial-gradient(ellipse 50% 50% at 10% 90%,rgba(124,58,237,<?php echo $night?.05:.025; ?>) 0%,transparent 70%)}
body::after{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:linear-gradient(var(--bdr) 1px,transparent 1px),linear-gradient(90deg,var(--bdr) 1px,transparent 1px);
  background-size:50px 50px}

/* LOGIN */
.lp{position:relative;z-index:10;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
.lc{width:460px;background:var(--bg2);border:1px solid var(--bdr);border-radius:32px;padding:56px 48px;position:relative;overflow:hidden;box-shadow:var(--sh);animation:lcIn .9s cubic-bezier(.16,1,.3,1) both}
@keyframes lcIn{from{opacity:0;transform:translateY(60px) scale(.94)}to{opacity:1;transform:none}}
.lc::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent 0%,var(--cyan) 50%,transparent 100%);animation:glowLine 3s ease-in-out infinite}
@keyframes glowLine{0%,100%{opacity:.4}50%{opacity:1}}
.lc-corner{position:absolute;top:-80px;right:-80px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(0,229,255,.15) 0%,transparent 70%);animation:cornPulse 4s ease infinite}
@keyframes cornPulse{0%,100%{transform:scale(1);opacity:.7}50%{transform:scale(1.2);opacity:1}}
.logo-cluster{display:flex;align-items:center;justify-content:center;margin-bottom:36px;position:relative}
.logo-rings{position:relative;width:100px;height:100px;display:flex;align-items:center;justify-content:center}
.ring{position:absolute;inset:0;border-radius:50%;border:1.5px solid;animation:spin linear infinite}
.ring1{border-color:rgba(0,229,255,.35);animation-duration:12s}
.ring2{inset:10px;border-color:rgba(124,58,237,.3);animation-duration:18s;animation-direction:reverse}
.ring3{inset:20px;border-color:rgba(0,229,255,.15);animation-duration:8s;border-style:dashed}
@keyframes spin{to{transform:rotate(360deg)}}
.logo-core{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,rgba(0,229,255,.2),rgba(124,58,237,.2));display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--cyan);position:relative;z-index:2;box-shadow:0 0 30px rgba(0,229,255,.3),inset 0 0 20px rgba(0,229,255,.1);animation:corePulse 3s ease infinite}
@keyframes corePulse{0%,100%{box-shadow:0 0 30px rgba(0,229,255,.3),inset 0 0 20px rgba(0,229,255,.1)}50%{box-shadow:0 0 50px rgba(0,229,255,.5),inset 0 0 30px rgba(0,229,255,.15)}}
.l-title{text-align:center;margin-bottom:10px}
.l-title h1{font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;letter-spacing:-1.5px;background:linear-gradient(135deg,var(--cyan) 0%,#c4b5fd 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;animation:titleShimmer 4s ease infinite}
@keyframes titleShimmer{0%{filter:brightness(1)}50%{filter:brightness(1.3)}100%{filter:brightness(1)}}
.l-title p{color:var(--txt2);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;margin-top:6px}
.l-divider{display:flex;align-items:center;gap:14px;margin:28px 0;color:var(--txt2);font-size:.7rem;text-transform:uppercase;letter-spacing:1.5px}
.l-divider::before,.l-divider::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,transparent,var(--bdr),transparent)}
.lerr{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#fca5a5;border-radius:12px;padding:12px 18px;font-size:.85rem;font-weight:600;margin-bottom:20px;display:flex;align-items:center;gap:10px;animation:errShake .5s cubic-bezier(.36,.07,.19,.97) both}
@keyframes errShake{10%,90%{transform:translateX(-2px)}20%,80%{transform:translateX(4px)}30%,50%,70%{transform:translateX(-4px)}40%,60%{transform:translateX(4px)}}
.fg{margin-bottom:20px;position:relative}
.fg label{display:block;font-size:.68rem;font-weight:800;color:var(--txt2);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:9px}
.fg input{width:100%;background:var(--bg3);border:1px solid var(--bdr);border-radius:14px;padding:14px 48px 14px 16px;font-family:'JetBrains Mono',monospace;font-size:.95rem;color:var(--txt);outline:none;transition:border-color .25s,box-shadow .25s,background .25s;caret-color:var(--cyan)}
.fg input:focus{border-color:rgba(0,229,255,.5);box-shadow:0 0 0 4px rgba(0,229,255,.08),0 0 20px rgba(0,229,255,.05);background:var(--bg2)}
.fg .fi{position:absolute;left:16px;bottom:15px;color:var(--txt2);transition:color .25s}
.fg input:focus ~ .fi{color:var(--cyan)}
.fg input:focus::placeholder{opacity:0;transform:translateX(8px);transition:all .2s}
.btn-login{width:100%;padding:16px;border:none;border-radius:14px;cursor:pointer;font-family:'Cairo',sans-serif;font-size:1rem;font-weight:900;color:#000;background:linear-gradient(135deg,var(--cyan) 0%,#67e8f9 100%);position:relative;overflow:hidden;letter-spacing:.5px;transition:transform .2s,box-shadow .2s}
.btn-login:hover{transform:translateY(-3px);box-shadow:0 16px 40px rgba(0,229,255,.35)}
.btn-login:active{transform:translateY(0) scale(.98)}
.shine{position:absolute;top:0;left:-100%;width:60%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.35),transparent);transform:skewX(-20deg);animation:shineAnim 3s ease-in-out infinite 1s}
@keyframes shineAnim{0%,100%{left:-100%}40%{left:150%}}
.l-status{text-align:center;margin-top:24px;font-size:.72rem;color:var(--txt2);display:flex;align-items:center;justify-content:center;gap:8px}
.ldot{width:6px;height:6px;border-radius:50%;background:var(--green);box-shadow:0 0 8px var(--green);animation:ldotP 2s ease infinite}
@keyframes ldotP{0%,100%{transform:scale(1)}50%{transform:scale(1.4)}}

/* DASHBOARD */
.dash{position:relative;z-index:10;min-height:100vh}
.tb{position:sticky;top:0;z-index:200;height:62px;display:flex;align-items:center;gap:16px;padding:0 28px;background:<?php echo $night?'rgba(6,9,15,.82)':'rgba(240,243,250,.82)'; ?>;backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-bottom:1px solid var(--bdr);animation:tbIn .5s ease both}
@keyframes tbIn{from{opacity:0;transform:translateY(-100%)}to{opacity:1;transform:none}}
.tb-brand{font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;letter-spacing:-1px;background:linear-gradient(135deg,var(--cyan),#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-left:auto}
.tb-tabs{display:flex;gap:3px}
.tt{padding:7px 15px;border-radius:10px;border:none;cursor:pointer;font-family:'Cairo',sans-serif;font-size:.82rem;font-weight:700;background:transparent;color:var(--txt2);transition:all .2s;display:flex;align-items:center;gap:7px}
.tt:hover{background:var(--bg3);color:var(--txt)}
.tt.on{background:rgba(0,229,255,.1);color:var(--cyan)}
.tt .tc{background:rgba(0,229,255,.15);color:var(--cyan);border-radius:10px;padding:1px 8px;font-size:.68rem}
.tb-r{display:flex;align-items:center;gap:10px;margin-right:auto}
.srv{display:flex;align-items:center;gap:7px;padding:5px 14px;border-radius:20px;border:1px solid var(--bdr);font-size:.75rem;font-weight:700;color:var(--txt2)}
.sdot{width:7px;height:7px;border-radius:50%;animation:sdotP 2s infinite}
.sdot.on{background:var(--green);box-shadow:0 0 10px var(--green)}
.sdot.mn{background:var(--red);box-shadow:0 0 10px var(--red)}
@keyframes sdotP{0%,100%{transform:scale(1)}50%{transform:scale(1.5)}}
.tbI{width:36px;height:36px;border-radius:10px;border:1px solid var(--bdr);background:var(--bg3);color:var(--txt2);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.88rem;transition:all .2s;text-decoration:none}
.tbI:hover{color:var(--cyan);border-color:rgba(0,229,255,.3);background:rgba(0,229,255,.06)}
.wrap{max-width:1380px;margin:0 auto;padding:28px 28px 60px}

/* Alerts & Stats */
.mb{background:linear-gradient(90deg,rgba(239,68,68,.14),transparent);border:1px solid rgba(239,68,68,.28);border-radius:16px;padding:14px 20px;margin-bottom:22px;display:flex;align-items:center;gap:14px;color:#fca5a5;font-weight:700;animation:mbP 2.5s ease infinite}
@keyframes mbP{0%,100%{border-color:rgba(239,68,68,.2)}50%{border-color:rgba(239,68,68,.6)}}
.al{padding:13px 18px;border-radius:13px;font-size:.88rem;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:10px;animation:alIn .35s ease both}
@keyframes alIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:none}}
.al-ok{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:var(--green)}
.al-warn{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:var(--amber)}
.al-err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--red)}

.sg{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:26px}
.sc{background:var(--bg2);border:1px solid var(--bdr);border-radius:20px;padding:22px 18px;position:relative;overflow:hidden;cursor:default;transition:transform .3s,box-shadow .3s;animation:scIn .6s cubic-bezier(.16,1,.3,1) both}
.sc:hover{transform:translateY(-5px);box-shadow:var(--sh)}
@keyframes scIn{from{opacity:0;transform:translateY(24px) scale(.95)}to{opacity:1;transform:none}}
.sc-glow{position:absolute;top:-30px;right:-30px;width:100px;height:100px;border-radius:50%;filter:blur(40px);opacity:.25}
.sc-i{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:.95rem;margin-bottom:16px}
.sc-v{font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;line-height:1;margin-bottom:4px;animation:countUp .8s ease both}
.sc-l{font-size:.68rem;color:var(--txt2);font-weight:800;text-transform:uppercase;letter-spacing:1px}

.c-cyan .sc-glow{background:var(--cyan)}.c-cyan .sc-i{background:rgba(0,229,255,.12);color:var(--cyan)}
.c-grn  .sc-glow{background:var(--green)}.c-grn  .sc-i{background:rgba(16,185,129,.12);color:var(--green)}
.c-red  .sc-glow{background:var(--red)}.c-red  .sc-i{background:rgba(239,68,68,.12);color:var(--red)}
.c-gry  .sc-glow{background:#64748b}.c-gry  .sc-i{background:rgba(100,116,139,.12);color:var(--txt2)}
.c-pur  .sc-glow{background:var(--pur)}.c-pur  .sc-i{background:rgba(124,58,237,.12);color:#a78bfa}

/* Layout Elements */
.pc{background:var(--bg2);border:1px solid var(--bdr);border-radius:22px;overflow:hidden;margin-bottom:22px;box-shadow:var(--sh);animation:pcIn .5s ease both}
@keyframes pcIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
.ph{padding:18px 24px;border-bottom:1px solid var(--bdr);display:flex;align-items:center;justify-content:space-between;gap:12px}
.pt{font-family:'Syne',sans-serif;font-weight:700;font-size:.94rem;display:flex;align-items:center;gap:10px}
.pt-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.pb{padding:22px 24px}
.sec{display:none}.sec.on{display:block;animation:secIn .4s ease both}
@keyframes secIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}

/* Forms & Buttons */
.sb{position:relative}.sb input{padding-right:42px}
.sb .si{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--txt2);font-size:.83rem}
.chips{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:18px}
.chip{padding:5px 15px;border-radius:20px;border:1px solid var(--bdr);background:var(--bg3);color:var(--txt2);font-size:.73rem;font-weight:800;cursor:pointer;transition:all .2s;user-select:none}
.chip:hover,.chip.on{background:rgba(0,229,255,.1);border-color:rgba(0,229,255,.35);color:var(--cyan)}

.fgroup{margin-bottom:0}
.fgroup label{display:block;font-size:.67rem;font-weight:800;color:var(--txt2);text-transform:uppercase;letter-spacing:1px;margin-bottom:7px}
.fc{width:100%;background:var(--bg3);border:1px solid var(--bdr);border-radius:11px;padding:10px 14px;font-family:'Cairo',sans-serif;font-size:.87rem;color:var(--txt);outline:none;transition:border-color .2s,box-shadow .2s}
.fc:focus{border-color:rgba(0,229,255,.45);box-shadow:0 0 0 3px rgba(0,229,255,.08)}
.fc.mono{font-family:'JetBrains Mono',monospace;font-size:.8rem}
.fr{display:grid;gap:14px}
.fr3{grid-template-columns:1fr 1fr 1fr}
.fr4{grid-template-columns:1fr 1fr 1fr 1fr}
.fr5{grid-template-columns:1fr 1fr 1fr 1fr 1fr}

.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:11px;border:none;font-family:'Cairo',sans-serif;font-size:.86rem;font-weight:800;cursor:pointer;transition:transform .2s,opacity .2s,box-shadow .2s;text-decoration:none;white-space:nowrap}
.btn:hover{transform:translateY(-2px);opacity:.9}
.btn:active{transform:scale(.97)}
.btn-p{background:linear-gradient(135deg,var(--cyan),var(--pur));color:#fff}
.btn-r{background:var(--red);color:#fff}
.btn-s{background:var(--bg3);color:var(--txt);border:1px solid var(--bdr)}
.btn-sm{padding:6px 12px;font-size:.78rem;border-radius:9px;gap:5px}

/* Key Cards */
.kl{display:flex;flex-direction:column;gap:10px}
.kc{background:var(--bg3);border:1px solid var(--bdr);border-radius:16px;display:flex;align-items:center;gap:14px;padding:16px 18px;position:relative;overflow:hidden;transition:border-color .25s,box-shadow .25s,transform .25s;animation:kcIn .45s cubic-bezier(.16,1,.3,1) both}
.kc:hover{transform:translateX(-3px);border-color:rgba(0,229,255,.2);box-shadow:0 6px 28px rgba(0,0,0,.18)}
@keyframes kcIn{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:none}}
.kc-banned{border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.04)}
.kc-expd{opacity:.65}
.kc-bar{position:absolute;right:0;top:0;bottom:0;width:3px;border-radius:0 3px 3px 0}
.kc-active .kc-bar{background:linear-gradient(180deg,var(--green),#059669)}
.kc-banned .kc-bar{background:var(--red)}.kc-expd .kc-bar{background:var(--txt2)}
.kc-main{flex:1;min-width:0}
.kval{font-family:'JetBrains Mono',monospace;font-size:.93rem;font-weight:700;color:var(--cyan);display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:7px}
.cpb{background:none;border:none;cursor:pointer;color:var(--txt2);font-size:.8rem;padding:3px 7px;border-radius:7px;transition:all .2s}
.cpb:hover{color:var(--cyan);background:rgba(0,229,255,.1)}
.kmeta{display:flex;align-items:center;gap:12px;font-size:.73rem;color:var(--txt2);flex-wrap:wrap}
.kmeta span{display:flex;align-items:center;gap:5px}
.kb{display:inline-flex;align-items:center;gap:4px;padding:2px 10px;border-radius:20px;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px}
.kb-active{background:rgba(16,185,129,.12);color:var(--green);border:1px solid rgba(16,185,129,.25)}
.kb-ban{background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.25)}
.kb-expd{background:rgba(100,116,139,.12);color:var(--txt2);border:1px solid rgba(100,116,139,.2)}
.kdev{min-width:82px;background:var(--bg2);border:1px solid var(--bdr);border-radius:12px;padding:10px 12px;text-align:center;flex-shrink:0}
.kdn{font-family:'JetBrains Mono',monospace;font-size:1.05rem;font-weight:700}
.kdl{font-size:.62rem;color:var(--txt2);font-weight:700;text-transform:uppercase;margin-bottom:6px}
.kpw{height:3px;background:var(--bdr);border-radius:2px;overflow:hidden}
.kpf{height:100%;border-radius:2px;transition:width .7s ease}
.kacts{display:flex;gap:5px;align-items:center;flex-wrap:wrap;flex-shrink:0}

/* Actions */
.iBtn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1px solid var(--bdr);background:var(--bg2);color:var(--txt2);cursor:pointer;font-size:.8rem;transition:all .2s;text-decoration:none}
.iBtn:hover{transform:translateY(-2px);border-color:rgba(0,229,255,.3);color:var(--cyan);background:rgba(0,229,255,.08)}
.iBtn.red:hover{border-color:rgba(239,68,68,.4);color:var(--red);background:rgba(239,68,68,.08)}
.iBtn.grn:hover{border-color:rgba(16,185,129,.4);color:var(--green);background:rgba(16,185,129,.08)}
.iBtn.amb:hover{border-color:rgba(245,158,11,.4);color:var(--amber);background:rgba(245,158,11,.08)}

.ddw{position:relative}
.ddm{position:absolute;top:calc(100%+6px);left:0;z-index:400;background:var(--bg2);border:1px solid var(--bdr);border-radius:12px;padding:8px;min-width:128px;box-shadow:var(--sh);opacity:0;pointer-events:none;transform:translateY(-8px) scale(.96);transition:all .2s}
.ddm.op{opacity:1;pointer-events:all;transform:none}
.ddi{display:block;padding:7px 13px;border-radius:8px;font-size:.79rem;font-weight:700;color:var(--txt2);text-decoration:none;transition:all .15s}
.ddi:hover{background:var(--bg3);color:var(--cyan)}

/* Toggle & Modals */
.tog-row{display:flex;align-items:center;justify-content:space-between;padding:16px 0;border-bottom:1px solid var(--bdr)}
.tog-row:last-child{border:none;padding-bottom:0}
.tog-info h4{font-size:.88rem;font-weight:700;margin-bottom:3px}
.tog-info p{font-size:.74rem;color:var(--txt2)}
.tog{width:50px;height:26px;border-radius:13px;background:var(--bg3);border:1px solid var(--bdr);position:relative;cursor:pointer;transition:all .3s;flex-shrink:0;display:block}
.tog.on{background:var(--cyan);border-color:var(--cyan)}
.tog::after{content:'';position:absolute;top:3px;right:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:transform .3s;box-shadow:0 2px 8px rgba(0,0,0,.3)}
.tog.on::after{transform:translateX(-24px)}

.modal-bg{position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.7);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:center;padding:24px;opacity:0;pointer-events:none;transition:opacity .3s}
.modal-bg.open{opacity:1;pointer-events:all}
.modal{background:var(--bg2);border:1px solid var(--bdr);border-radius:24px;padding:34px;width:540px;max-width:100%;box-shadow:0 48px 120px rgba(0,0,0,.5);transform:scale(.88) translateY(30px);transition:transform .4s cubic-bezier(.16,1,.3,1)}
.modal-bg.open .modal{transform:scale(1) translateY(0)}
.mt{font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;margin-bottom:26px;display:flex;align-items:center;gap:10px}
.mf{display:flex;gap:10px;justify-content:flex-end;margin-top:22px}

.tw{position:fixed;bottom:24px;left:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.toast{background:var(--bg2);border:1px solid var(--bdr);border-radius:14px;padding:13px 20px;display:flex;align-items:center;gap:10px;box-shadow:var(--sh);font-weight:700;font-size:.84rem;pointer-events:all;transform:translateX(-140%) rotate(-4deg);transition:transform .45s cubic-bezier(.16,1,.3,1),opacity .3s;opacity:0}
.toast.show{transform:none;opacity:1}
.toast.hide{transform:translateX(-140%) rotate(4deg);opacity:0}
.empty{text-align:center;padding:50px 20px;color:var(--txt2)}
.empty i{font-size:2.5rem;opacity:.2;display:block;margin-bottom:14px}

@media(max-width:1000px){.sg{grid-template-columns:repeat(3,1fr)}.fr5,.fr4{grid-template-columns:1fr 1fr}}
@media(max-width:640px){.sg{grid-template-columns:repeat(2,1fr)}.wrap{padding:16px}.fr5,.fr4,.fr3{grid-template-columns:1fr}.kc{flex-wrap:wrap}.kacts{width:100%}.tt span.tlbl{display:none}}
::-webkit-scrollbar{width:5px;height:5px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:var(--bdr);border-radius:3px}
</style>
</head>
<body>

<?php if(empty($_SESSION['logged_in'])): ?>
<div class="lp">
  <div class="lc">
    <div class="lc-corner"></div>
    <div class="logo-cluster">
      <div class="logo-rings">
        <div class="ring ring1"></div>
        <div class="ring ring2"></div>
        <div class="ring ring3"></div>
        <div class="logo-core"><i class="fas fa-shield-halved"></i></div>
      </div>
    </div>
    <div class="l-title">
      <h1>Fluorite Root Android</h1>
      <p>buy seller @Sa_Nso</p>
    </div>
    <div class="l-divider">secure access only</div>
    <?php if($msg): ?>
    <div class="lerr"><i class="fas fa-circle-exclamation"></i><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="fg">
        <label>user name</label>
        <input type="text" name="login_user" placeholder="Admin..." autofocus required>
        <i class="fas fa-user fi"></i>
      </div>
      <div class="fg">
        <label>password</label>
        <input type="password" name="login_pass" placeholder="••••••••••••" required>
        <i class="fas fa-lock fi"></i>
      </div>
      <button type="submit" class="btn-login">
        <span class="shine"></span>
        <i class="fas fa-right-to-bracket"></i>&nbsp; Accessing the control panel
      </button>
    </form>
  </div>
</div>

<?php else: ?>
<div class="dash">

<header class="tb">
  <nav class="tb-tabs">
    <button class="tt on" onclick="showSec('overview',this)"><i class="fas fa-chart-pie"></i><span class="tlbl">Dashboard</span></button>
    <button class="tt"    onclick="showSec('keys',this)"><i class="fas fa-key"></i><span class="tlbl">Keys</span><span class="tc"><?php echo $total; ?></span></button>
    <button class="tt"    onclick="showSec('generate',this)"><i class="fas fa-wand-magic-sparkles"></i><span class="tlbl">Generate</span></button>
    <button class="tt"    onclick="showSec('settings',this)"><i class="fas fa-sliders"></i><span class="tlbl">Settings</span><?php if($maintenance_mode) echo '<span style="background:rgba(239,68,68,.2);color:var(--red);border-radius:8px;padding:1px 7px;font-size:.65rem">●</span>'; ?></button>
  </nav>
  <span class="tb-brand">Fluorite Root Android</span>
  <div class="tb-r">
    <span class="srv"><span class="sdot <?php echo $maintenance_mode?'mn':'on'; ?>"></span><?php echo $maintenance_mode?'Maintenance':'Online'; ?></span>
    <a href="?tn" class="tbI" title="Toggle theme"><i class="fas fa-<?php echo $night?'sun':'moon'; ?>"></i></a>
    <a href="?logout" class="tbI" title="Logout"><i class="fas fa-right-from-bracket"></i></a>
  </div>
</header>

<div class="wrap">

<?php if($maintenance_mode): ?>
<div class="mb">
  <i class="fas fa-triangle-exclamation fa-lg"></i>
  <div style="flex:1"><b>Maintenance mode is enabled</b><div style="font-size:.8rem;opacity:.75;margin-top:2px">All application requests will receive a maintenance message.</div></div>
  <a href="?tm" class="btn btn-r btn-sm"><i class="fas fa-power-off"></i> Disable</a>
</div>
<?php endif; ?>

<?php if($msg): ?>
<div class="al al-<?php echo $mtype==='warn'?'warn':($mtype==='err'?'err':'ok'); ?>">
  <i class="fas fa-<?php echo $mtype==='warn'?'triangle-exclamation':($mtype==='err'?'circle-exclamation':'circle-check'); ?>"></i>
  <?php echo $msg; ?>
</div>
<?php endif; ?>

<div class="sg">
  <div class="sc c-cyan"><div class="sc-glow"></div><div class="sc-i"><i class="fas fa-key"></i></div><div class="sc-v"><?php echo $total; ?></div><div class="sc-l">Total</div></div>
  <div class="sc c-grn"><div class="sc-glow"></div><div class="sc-i"><i class="fas fa-circle-check"></i></div><div class="sc-v"><?php echo $active; ?></div><div class="sc-l">Active</div></div>
  <div class="sc c-red"><div class="sc-glow"></div><div class="sc-i"><i class="fas fa-ban"></i></div><div class="sc-v"><?php echo $banned; ?></div><div class="sc-l">Banned</div></div>
  <div class="sc c-gry"><div class="sc-glow"></div><div class="sc-i"><i class="fas fa-calendar-xmark"></i></div><div class="sc-v"><?php echo $expd; ?></div><div class="sc-l">Expired</div></div>
  <div class="sc c-pur"><div class="sc-glow"></div><div class="sc-i"><i class="fas fa-mobile-screen"></i></div><div class="sc-v"><?php echo $devs; ?></div><div class="sc-l">Devices</div></div>
</div>

<div class="sec on" id="sec-overview">
  <div class="pc">
    <div class="ph">
      <div class="pt"><span class="pt-dot" style="background:var(--cyan)"></span>Recently Added Keys</div>
      <button class="btn btn-s btn-sm" onclick="showSec('keys',document.querySelectorAll('.tt')[1])"><i class="fas fa-list"></i> View All</button>
    </div>
    <div class="pb">
      <?php $r = array_slice(array_reverse($db, true), 0, 6, true); if(empty($r)): ?>
      <div class="empty"><i class="fas fa-key"></i>No keys yet</div>
      <?php else: ?><div class="kl"><?php foreach($r as $key => $v) echo kcard($key, $v); ?></div><?php endif; ?>
    </div>
  </div>
</div>

<div class="sec" id="sec-keys">
  <div class="pc">
    <div class="ph">
      <div class="pt"><span class="pt-dot" style="background:var(--cyan)"></span>Key Management</div>
      <div style="display:flex;gap:10px;align-items:center">
        <div class="sb"><i class="fas fa-search si"></i><input class="fc" id="sIn" placeholder="Search key..." oninput="doFilter()" style="padding-right:40px;width:200px"></div>
      </div>
    </div>
    <div class="pb">
      <div class="chips">
        <span class="chip on" onclick="setF('all',this)">All (<?php echo $total; ?>)</span>
        <span class="chip" onclick="setF('kc-active',this)">Active (<?php echo $active; ?>)</span>
        <span class="chip" onclick="setF('kc-banned',this)">Banned (<?php echo $banned; ?>)</span><span class="chip" onclick="setF('kc-expd',this)">Expired (<?php echo $expd; ?>)</span>
      </div>
      <?php if(empty($db)): ?>
      <div class="empty"><i class="fas fa-key"></i>There are no keys; create your own keys from the Generate tab.</div>
      <?php else: ?>
      <div class="kl" id="kGrid">
        <?php foreach(array_reverse($db, true) as $key => $v) echo kcard($key, $v, true); ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="sec" id="sec-generate">
  <div class="pc">
    <div class="ph"><div class="pt"><span class="pt-dot" style="background:var(--green)"></span>Manual Key Creation</div></div>
    <div class="pb">
      <form method="POST">
        <div class="fr fr5" style="margin-bottom:14px">
          <div class="fgroup"><label>Key (empty = auto)</label><input type="text" name="key" class="fc mono" placeholder="••••••••"></div>
          <div class="fgroup"><label>Days</label><input type="number" name="ad" class="fc" value="30" min="1"></div>
          <div class="fgroup"><label>Number of devices</label><input type="number" name="max_dev" class="fc" value="1" min="1"></div>
          <div class="fgroup" style="grid-column: span 2;"><label>Note / Customer Name</label><input type="text" name="note" class="fc" placeholder="VIP..."></div>
        </div>
        <button type="submit" name="add_key" class="btn btn-p"><i class="fas fa-plus"></i> Create</button>
      </form>
    </div>
  </div>

  <div class="pc">
    <div class="ph"><div class="pt"><span class="pt-dot" style="background:#a78bfa"></span>Bulk Generation</div></div>
    <div class="pb">
      <form method="POST">
        <div class="fr fr4" style="margin-bottom:16px">
          <div class="fgroup"><label>Number of keys</label><input type="number" name="ag_c" class="fc" value="5" min="1"></div>
          <div class="fgroup"><label>Number of days for the key</label><input type="number" name="ag_d" class="fc" value="30" min="1"></div>
          <div class="fgroup"><label>Devices/Switch</label><input type="number" name="ag_m" class="fc" value="1" min="1"></div>
          <div class="fgroup" style="display:flex;align-items:flex-end"><button type="submit" name="auto_gen" class="btn btn-p" style="width:100%"><i class="fas fa-bolt"></i> Generate</button></div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="sec" id="sec-settings">
  <div class="pc">
    <div class="ph"><div class="pt"><span class="pt-dot" style="background:var(--amber)"></span>System Settings</div></div>
    <div class="pb">
      <div class="tog-row">
        <div class="tog-info"><h4><i class="fas fa-wrench" style="color:var(--red);margin-left:6px"></i>Maintenance mode</h4><p>Sends a maintenance message to the application and prevents login</p></div>
        <a href="?tm" class="tog <?php echo $maintenance_mode?'on':''; ?>"></a>
      </div>
      <div class="tog-row">
        <div class="tog-info"><h4><i class="fas fa-moon" style="color:#a78bfa;margin-left:6px"></i>Dark mode</h4><p>Toggle the overall appearance of the control panel</p></div>
        <a href="?tn" class="tog <?php echo $night?'on':''; ?>"></a>
      </div>
    </div>
  </div>
</div>

</div></div><div class="modal-bg" id="editM">
  <div class="modal">
    <div class="mt"><i class="fas fa-pen-to-square" style="color:var(--cyan)"></i>Edit Key</div>
    <form method="POST">
      <input type="hidden" name="ek_id" id="mId">
      <div class="fgroup" style="margin-bottom:14px"><label>Key</label><input type="text" id="mDisp" class="fc mono" readonly style="opacity:.6"></div>
      <div class="fr fr3" style="margin-bottom:14px">
        <div class="fgroup"><label>Expiration date</label><input type="date" name="ek_exp" id="mExp" class="fc"></div>
        <div class="fgroup"><label>Number of devices</label><input type="number" name="ek_max" id="mMax" class="fc" min="1"></div>
        <div class="fgroup"><label>Note</label><input type="text" name="ek_note" id="mNote" class="fc"></div>
      </div>
      <div class="mf">
        <button type="button" class="btn btn-s" onclick="closeEdit()">Cancel</button>
        <button type="submit" name="edit_key" class="btn btn-p"><i class="fas fa-check"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="tw" id="tw"></div>

<?php endif; ?>

<script>
function showSec(id, btn) {
  document.querySelectorAll('.sec').forEach(s => s.classList.remove('on'));
  document.getElementById('sec-' + id).classList.add('on');
  document.querySelectorAll('.tt').forEach(b => b.classList.remove('on'));
  if (btn) btn.classList.add('on');
}
function cp(key, btn) {
  navigator.clipboard.writeText(key).then(() => {
    toast('Key copied ✓', '#10b981');
    if (btn) { const o = btn.innerHTML; btn.innerHTML = '<i class="fas fa-check" style="color:var(--green)"></i>'; setTimeout(() => btn.innerHTML = o, 1400); }
  }).catch(() => toast('Copy failed', '#ef4444'));
}
function toast(msg, color = '#10b981') {
  const w = document.getElementById('tw');
  const t = document.createElement('div');
  t.className = 'toast';
  t.innerHTML = `<i class="fas fa-circle-check" style="color:${color};font-size:1rem"></i><span>${msg}</span>`;
  w.appendChild(t);
  requestAnimationFrame(() => requestAnimationFrame(() => t.classList.add('show')));
  setTimeout(() => { t.classList.remove('show'); t.classList.add('hide'); setTimeout(() => t.remove(), 500); }, 2800);
}
function openEdit(key, exp, max, note) {
  document.getElementById('mId').value = key;
  document.getElementById('mDisp').value = key;
  document.getElementById('mExp').value = exp; // Must be in YYYY-MM-DD format
  document.getElementById('mMax').value = max;
  document.getElementById('mNote').value = note;
  document.getElementById('editM').classList.add('open');
}
function closeEdit() { document.getElementById('editM').classList.remove('open'); }
document.getElementById('editM')?.addEventListener('click', e => { if (e.target.id === 'editM') closeEdit(); });

let curF = 'all';
function setF(f, el) {
  curF = f;
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('on'));
  el.classList.add('on');
  doFilter();
}
function doFilter() {
  const q = (document.getElementById('sIn')?.value || '').toLowerCase().trim();
  document.querySelectorAll('#kGrid .kc').forEach(c => {
    const cls = c.className;
    const mf = curF === 'all' || cls.includes(curF);
    const ms = !q || c.dataset.k.includes(q);
    c.style.display = mf && ms ? '' : 'none';
  });
}
function tdd(btn) {
  const m = btn.nextElementSibling;
  const isOpen = m.classList.contains('op');
  document.querySelectorAll('.ddm').forEach(d => d.classList.remove('op'));
  if (!isOpen) m.classList.add('op');
}
document.addEventListener('click', e => {
  if (!e.target.closest('.ddw')) document.querySelectorAll('.ddm').forEach(d => d.classList.remove('op'));
});
const al = document.querySelector('.al');
if (al) setTimeout(() => { al.style.transition = 'opacity .5s,transform .5s'; al.style.opacity = '0'; al.style.transform = 'translateY(-8px)'; setTimeout(() => al.remove(), 500); }, 4000);
document.querySelectorAll('.kc').forEach((c, i) => c.style.animationDelay = (i * 0.04) + 's');
</script>
</body>
</html>