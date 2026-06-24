<?php
/**
 * NEXT MENU - ULTRA PREMIUM DARK EDITION 2026
 * Professional Admin Dashboard
 */

session_start();
define('ADMIN_PASSWORD', 'VIPTEAM976BHUU');
define('DATA_FILE', 'keys_data.json');

if (!file_exists(DATA_FILE)) {
    file_put_contents(DATA_FILE, json_encode(['keys' => [], 'devices' => []], JSON_PRETTY_PRINT));
}

function loadData() { return json_decode(file_get_contents(DATA_FILE), true); }
function saveData($data) { file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT)); }

// ACTIONS
if (isset($_SESSION['admin_logged'])) {
    $data = loadData();
    if (isset($_POST['add_manual_key'])) {
        $mKey = trim($_POST['manual_key']);
        if(!empty($mKey)) {
            $data['keys'][$mKey] = [
                'days' => intval($_POST['days']),
                'max_devices' => intval($_POST['devices']),
                'created' => time(),
                'expires' => time() + (intval($_POST['days']) * 86400),
                'devices' => []
            ];
            saveData($data);
        }
        header('Location: ' . $_SERVER['PHP_SELF']); exit;
    }
    if (isset($_GET['delete'])) {
        unset($data['keys'][$_GET['delete']]);
        saveData($data);
        header('Location: ' . $_SERVER['PHP_SELF']); exit;
    }
    if (isset($_GET['reset'])) {
        $data['keys'][$_GET['reset']]['devices'] = [];
        saveData($data);
        header('Location: ' . $_SERVER['PHP_SELF']); exit;
    }
}

// LOGIN AJAX
if (isset($_POST['ajax_login'])) {
    header('Content-Type: application/json');
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_logged'] = true;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Access Denied!']);
    }
    exit;
}

if (isset($_GET['logout'])) { session_destroy(); header("Location: ".$_SERVER['PHP_SELF']); exit; }
$isLoggedIn = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXT MENU | Premium Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00d2ff;
            --secondary: #3a7bd5;
            --dark-bg: #0f1219;
            --card-bg: #161b22;
            --text: #ffffff;
            --accent: #00f2fe;
            --danger: #ff4b2b;
        }

        body {
            margin: 0; font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg); color: var(--text);
            display: flex; justify-content: center; min-height: 100vh;
            background: radial-gradient(circle at top right, #1a2a44, #0f1219);
        }

        .container { width: 100%; max-width: 450px; padding: 20px; }

        /* PREMIUM GLASS CARD */
        .glass-card {
            background: rgba(22, 27, 34, 0.8); backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 30px;
            padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); margin-bottom: 25px;
        }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .logo-text { font-size: 22px; font-weight: 700; background: linear-gradient(to right, #00d2ff, #3a7bd5); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* INPUTS */
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { font-size: 12px; color: #8b949e; margin-bottom: 8px; display: block; text-transform: uppercase; letter-spacing: 1px; }
        
        input, select {
            width: 100%; padding: 14px 18px; border-radius: 15px; border: 1px solid #30363d;
            background: #0d1117; color: #fff; font-size: 14px; outline: none; transition: 0.3s; box-sizing: border-box;
        }
        input:focus { border-color: var(--primary); box-shadow: 0 0 10px rgba(0, 210, 255, 0.2); }

        .btn-premium {
            width: 100%; padding: 15px; border-radius: 15px; border: none;
            background: linear-gradient(45deg, #00d2ff, #3a7bd5); color: white;
            font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 16px;
            box-shadow: 0 10px 20px rgba(58, 123, 213, 0.3);
        }
        .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(58, 123, 213, 0.4); }

        /* KEY LIST ITEM */
        .key-box {
            background: #1c2128; border-radius: 20px; padding: 20px; margin-bottom: 15px;
            border-left: 4px solid var(--primary); transition: 0.3s;
        }
        .key-box:hover { background: #222831; }
        .key-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .key-name { font-weight: 700; color: var(--accent); font-family: monospace; font-size: 15px; }
        
        .badge { font-size: 11px; padding: 4px 10px; border-radius: 8px; background: rgba(0, 210, 255, 0.1); color: var(--primary); }

        .key-actions { display: flex; gap: 10px; margin-top: 15px; }
        .btn-action {
            flex: 1; padding: 10px; border-radius: 12px; text-align: center;
            text-decoration: none; font-size: 12px; font-weight: 600; transition: 0.2s;
        }
        .btn-reset { background: rgba(58, 123, 213, 0.1); color: #3a7bd5; border: 1px solid rgba(58, 123, 213, 0.2); }
        .btn-delete { background: rgba(255, 75, 43, 0.1); color: var(--danger); border: 1px solid rgba(255, 75, 43, 0.2); }
        .btn-action:hover { opacity: 0.8; }

        .logout { color: var(--danger); text-decoration: none; font-size: 13px; font-weight: 600; }

        /* LOGIN STYLE */
        .login-box { text-align: center; margin-top: 100px; }
    </style>
</head>
<body>

<div class="container">

<?php if (!$isLoggedIn): ?>
    <div class="login-box glass-card">
        <img src="https://moh.x10.network/nextmenuicon.png" width="90" style="filter: drop-shadow(0 0 15px var(--primary));">
        <h2 style="margin-top:20px;">NEXT MENU <span style="font-weight:300;">PRO</span></h2>
        <p style="color:#8b949e; font-size:13px;">Secure Authentication System</p>
        <form id="loginForm">
            <div class="input-group">
                <input type="password" id="password" placeholder="Access Key" required>
            </div>
            <button type="submit" class="btn-premium">AUTHENTICATE</button>
        </form>
        <p id="msg" style="color:var(--danger); font-size:12px; margin-top:15px;"></p>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e){
            e.preventDefault();
            const fd = new FormData();
            fd.append('ajax_login', '1');
            fd.append('password', document.getElementById('password').value);
            const res = await fetch('', {method:'POST', body:fd});
            const data = await res.json();
            if(data.success) location.reload(); else document.getElementById('msg').innerText = data.message;
        });
    </script>

<?php else: ?>
    <div class="header">
        <span class="logo-text">NEXT MENU ADMIN</span>
        <a href="?logout=1" class="logout"><i class="fa fa-power-off"></i> LOGOUT</a>
    </div>

    <div class="glass-card">
        <h3 style="margin-top:0; font-size:18px;"><i class="fa fa-plus-circle" style="color:var(--primary);"></i> Add Manual Key</h3>
        <form method="POST">
            <div class="input-group">
                <label>License Name</label>
                <input type="text" name="manual_key" placeholder="Example: VIP-USER-99" required>
            </div>
            <div style="display:flex; gap:15px;">
                <div class="input-group" style="flex:1;">
                    <label>Duration (Days)</label>
                    <input type="number" name="days" value="30">
                </div>
                <div class="input-group" style="flex:1;">
                    <label>Device Limit</label>
                    <input type="number" name="devices" value="1">
                </div>
            </div>
            <button type="submit" name="add_manual_key" class="btn-premium">CREATE LICENSE</button>
        </form>
    </div>

    <h3 style="padding-left:10px; font-size:16px; color:#8b949e;">Active Licenses (<?php $d=loadData(); echo count($d['keys']); ?>)</h3>
    
    <?php foreach($d['keys'] as $code => $k): ?>
        <div class="key-box">
            <div class="key-header">
                <span class="key-name"><?php echo $code; ?></span>
                <span class="badge"><?php echo $k['days']; ?> Days</span>
            </div>
            <div style="font-size:12px; color:#8b949e;">
                <i class="fa fa-mobile-alt"></i> Devices: <span style="color:#fff;"><?php echo count($k['devices']); ?> / <?php echo $k['max_devices']; ?></span>
            </div>
            <div class="key-actions">
                <a href="?reset=<?php echo urlencode($code); ?>" class="btn-action btn-reset"><i class="fa fa-sync"></i> Reset HWID</a>
                <a href="?delete=<?php echo urlencode($code); ?>" class="btn-action btn-delete" onclick="return confirm('Remove this license?')"><i class="fa fa-trash"></i> Delete</a>
            </div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

</div>
</body>
</html>
