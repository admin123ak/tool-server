<?php
/**
 * =======================================================================
 * VIP TEAM - FREE KEY GENERATOR (Shortner First, Then Key Generate)
 * =======================================================================
 */

session_start();
require_once 'conn.php';

if (!$conn) {
    die("Database connection error. Please configure conn.php properly.");
}

// Game List
$game_list = [
    'BRMODS'    => 'BR MODS',
    'LKTEAM'    => 'LK TEAM',
    'IMMO'      => 'IMMO BLOODY PRO',
    'PUBG'      => 'TYRANTQ STREAMER',
    'IMMORTAL'  => 'IMMORTAL PANEL'
];

$default_game = 'LKTEAM';
$secret_salt = "VIP_TEAM_SECURE_2026";
$vplink_api_token = "2d92e4a0bb8e62d8bdb71175afaec35c757ae96d";

function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ============================================================
// CREATE SHORT LINK USING VPLINK API
// ============================================================
function createShortLink($url, $api_token) {
    $api_url = "https://vplink.in/api?api=" . $api_token . "&url=" . urlencode($url);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0');
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response && $http_code == 200) {
        $res_data = json_decode($response, true);
        if (isset($res_data['status']) && $res_data['status'] === 'success' && !empty($res_data['shortenedUrl'])) {
            return $res_data['shortenedUrl'];
        }
    }
    return false;
}

// ============================================================
// CLAIM API - FINAL KEY ACTIVATION (After Shortner)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'claim_api') {
    header('Content-Type: application/json');
    
    $key = isset($_POST['key']) ? trim($_POST['key']) : '';
    $game = isset($_POST['game']) ? trim($_POST['game']) : '';
    $time = isset($_POST['time']) ? (int)$_POST['time'] : 0;
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $sig = isset($_POST['signature']) ? trim($_POST['signature']) : '';
    
    if (empty($key) || empty($game) || empty($time) || empty($token) || empty($sig)) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters.']);
        exit;
    }
    
    $expected_token = md5($key . $game . $time . $secret_salt);
    if ($token !== $expected_token) {
        echo json_encode(['success' => false, 'error' => 'Invalid signature.']);
        exit;
    }
    
    $dynamic_salt = md5($token . "VIP_TEAM_BACKEND_SECRET");
    $expected_sig = md5(md5($key . $token) . $dynamic_salt);
    if ($sig !== $expected_sig) {
        echo json_encode(['success' => false, 'error' => 'Challenge failed.']);
        exit;
    }
    
    $safe_key = mysqli_real_escape_string($conn, $key);
    $query = mysqli_query($conn, "SELECT * FROM keys_code WHERE user_key = '$safe_key' LIMIT 1");
    
    if (mysqli_num_rows($query) > 0) {
        $key_data = mysqli_fetch_assoc($query);
        $cookie_name = 'claimed_key_' . md5($key);
        
        if ($key_data['status'] == 1) {
            if (isset($_COOKIE[$cookie_name]) || isset($_SESSION['claimed_' . md5($key)])) {
                echo json_encode([
                    'success' => true, 
                    'key' => $key, 
                    'game' => isset($game_list[$key_data['game']]) ? $game_list[$key_data['game']] : $key_data['game']
                ]);
                exit;
            } else {
                echo json_encode(['success' => false, 'error' => 'Key already claimed.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Key is banned.']);
            exit;
        }
    }
    
    // Insert new key into database
    $duration = 6;
    $status = 1;
    $registrator = 'Free_Getkey';
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    
    $stmt = mysqli_prepare($conn, "INSERT INTO keys_code (game, user_key, duration, max_devices, status, registrator, created_at, updated_at) VALUES (?, ?, ?, 1, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssiisss", $game, $key, $duration, $status, $registrator, $created_at, $updated_at);
    
    if (mysqli_stmt_execute($stmt)) {
        $cookie_name = 'claimed_key_' . md5($key);
        setcookie($cookie_name, '1', time() + 86400, '/');
        $_SESSION['claimed_' . md5($key)] = true;
        echo json_encode([
            'success' => true, 
            'key' => $key, 
            'game' => isset($game_list[$game]) ? $game_list[$game] : $game
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
        exit;
    }
}

// ============================================================
// GENERATE KEY - CREATE SHORT LINK FIRST
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    $selected_game = isset($_POST['game']) && array_key_exists($_POST['game'], $game_list) ? $_POST['game'] : $default_game;
    
    // Generate unique key
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    do {
        $random_string = '';
        for ($i = 0; $i < 8; $i++) {
            $random_string .= $characters[rand(0, strlen($characters) - 1)];
        }
        $generated_key = "VIP-6H-" . $random_string;
        
        $safe_key = mysqli_real_escape_string($conn, $generated_key);
        $check_query = mysqli_query($conn, "SELECT id_keys FROM keys_code WHERE user_key = '$safe_key' LIMIT 1");
    } while (mysqli_num_rows($check_query) > 0);

    $now_ts = time();
    $token = md5($generated_key . $selected_game . $now_ts . $secret_salt);
    
    $_SESSION['claim_init'] = true;
    $_SESSION['pending_key'] = $generated_key;
    $_SESSION['pending_game'] = $selected_game;
    $_SESSION['pending_time'] = $now_ts;
    $_SESSION['pending_token'] = $token;
    
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $claim_url = $protocol . $domain . $script_name . "?claim=1&key=" . urlencode($generated_key) . "&game=" . urlencode($selected_game) . "&time=" . $now_ts . "&token=" . urlencode($token);
    
    // Create short link using VPLink API
    $short_link = createShortLink($claim_url, $vplink_api_token);
    
    if (!$short_link) {
        $short_link = $claim_url;
    }
    
    // Store short link in session
    $_SESSION['short_link'] = $short_link;
    
    // Return JSON response with short link
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'short_link' => $short_link,
            'key' => $generated_key
        ]);
        exit;
    } else {
        header("Location: " . $_SERVER['SCRIPT_NAME'] . "?shortlink=1");
        exit;
    }
}

// ============================================================
// CLAIM VERIFICATION (After Shortner Click)
// ============================================================
$success_mode = false;
$claim_error = '';
$claimed_key = '';
$claimed_game = '';

$js_challenge_salt = '';
$verify_key = '';
$verify_game = '';
$verify_time = 0;
$verify_token = '';

if (isset($_GET['claim']) && $_GET['claim'] === '1' && isset($_GET['key']) && isset($_GET['game']) && isset($_GET['time']) && isset($_GET['token'])) {
    $verify_key = trim($_GET['key']);
    $verify_game = trim($_GET['game']);
    $verify_time = (int)$_GET['time'];
    $verify_token = trim($_GET['token']);
    $safe_key = mysqli_real_escape_string($conn, $verify_key);
    
    $expected_token = md5($verify_key . $verify_game . $verify_time . $secret_salt);
    
    if ($verify_token !== $expected_token) {
        $claim_error = "Invalid verification signature.";
    } else {
        $query = mysqli_query($conn, "SELECT * FROM keys_code WHERE user_key = '$safe_key' LIMIT 1");
        
        if (mysqli_num_rows($query) > 0) {
            $key_data = mysqli_fetch_assoc($query);
            $cookie_name = 'claimed_key_' . md5($verify_key);
            
            if (isset($_COOKIE[$cookie_name]) || isset($_SESSION['claimed_' . md5($verify_key)])) {
                $success_mode = true;
                $claimed_key = $key_data['user_key'];
                $claimed_game = isset($game_list[$key_data['game']]) ? $game_list[$key_data['game']] : $key_data['game'];
            } else {
                $claim_error = "This key has already been claimed.";
            }
        } else {
            // Key not in database yet - will be created after JS challenge
            $js_challenge_salt = md5($verify_token . "VIP_TEAM_BACKEND_SECRET");
        }
    }
}

// Check if we came from short link
$show_shortlink = isset($_GET['shortlink']) && isset($_SESSION['short_link']);
$short_link_url = $show_shortlink ? $_SESSION['short_link'] : '';
if ($show_shortlink) {
    unset($_SESSION['short_link']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP TEAM - Free License Key Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.19.0/js/md5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0a0c15 0%, #12172b 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .glass-card {
            background: rgba(18, 22, 40, 0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(108, 122, 195, 0.25);
            border-radius: 2rem;
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            border-color: rgba(108, 122, 195, 0.5);
            box-shadow: 0 20px 40px -20px rgba(108, 122, 195, 0.2);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #6a7bc2 0%, #7c8fcf 50%, #8a9be0 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
            box-shadow: 0 10px 25px -5px rgba(108, 122, 195, 0.4);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid rgba(108, 122, 195, 0.5);
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            background: rgba(108, 122, 195, 0.1);
            border-color: #7c8fcf;
        }
        .key-box {
            background: rgba(10, 12, 25, 0.9);
            border: 1px solid rgba(108, 122, 195, 0.4);
            border-radius: 1rem;
            transition: all 0.3s;
        }
        .key-box:hover {
            border-color: #7c8fcf;
            box-shadow: 0 0 20px rgba(108, 122, 195, 0.2);
        }
        .feature-icon {
            background: rgba(108, 122, 195, 0.15);
            border-radius: 1rem;
            transition: all 0.3s;
        }
        .feature-icon:hover {
            background: rgba(108, 122, 195, 0.25);
            transform: translateY(-3px);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fade {
            animation: fadeIn 0.4s ease-out;
        }
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: rgba(18, 22, 40, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(108, 122, 195, 0.4);
            border-radius: 50px;
            padding: 12px 24px;
            transition: all 0.3s;
            opacity: 0;
            z-index: 1000;
        }
        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        .progress-ring-circle {
            transition: stroke-dashoffset 0.3s;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">

    <div class="w-full max-w-md animate-fade">
        
        <!-- SHORT LINK CARD - User clicks here first -->
        <?php if ($show_shortlink && !empty($short_link_url)): ?>
        <div class="glass-card rounded-2xl p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-link text-2xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Complete Verification</h2>
            <p class="text-gray-400 text-sm mb-5">Click below to verify and claim your key</p>
            
            <a href="<?php echo esc($short_link_url); ?>" target="_blank" class="btn-gradient w-full py-3.5 rounded-xl font-semibold text-white flex items-center justify-center gap-2 mb-4">
                <i class="fas fa-external-link-alt"></i> Click Here to Continue
            </a>
            
            <div class="border-t border-gray-700/50 pt-4 mt-2">
                <p class="text-gray-500 text-[11px]">
                    <i class="fas fa-shield-alt mr-1 text-blue-400"></i> 
                    Complete the short link verification first
                </p>
                <p class="text-gray-600 text-[10px] mt-2">
                    After clicking, you will be redirected to claim your key
                </p>
            </div>
        </div>
        
        <!-- JS CHALLENGE CARD - After short link click -->
        <?php elseif (!empty($js_challenge_salt) && !$success_mode && empty($claim_error)): ?>
        <div class="glass-card rounded-2xl p-6 text-center" id="challengeCard">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-shield-alt text-2xl text-white"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Verifying Your Request</h2>
            <p class="text-gray-400 text-xs mb-5">Please wait while we process your key...</p>
            
            <div class="relative w-20 h-20 mx-auto mb-4">
                <svg class="w-full h-full" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="44" fill="none" stroke="rgba(108,122,195,0.1)" stroke-width="5"/>
                    <circle id="progressCircle" cx="50" cy="50" r="44" fill="none" stroke="url(#gradSoft)" stroke-width="5" stroke-linecap="round" stroke-dasharray="276.46" stroke-dashoffset="276.46" class="progress-ring-circle"/>
                    <defs>
                        <linearGradient id="gradSoft" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#6a7bc2"/>
                            <stop offset="100%" stop-color="#8a9be0"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div id="progressText" class="absolute inset-0 flex items-center justify-center text-white font-semibold text-base">0%</div>
            </div>
            
            <div id="challengeStatus" class="text-blue-400 text-xs font-medium">Initializing security check...</div>
        </div>
        
        <div class="glass-card rounded-2xl p-6 text-center hidden" id="successCard">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-check-circle text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white mb-1">Key Generated Successfully!</h2>
            <p class="text-gray-400 text-sm mb-5">Your trial key is ready to use</p>
            
            <div class="key-box p-4 mb-5">
                <div class="flex items-center justify-between gap-3">
                    <code id="keyText" class="text-white font-mono text-base font-bold tracking-wider break-all">------</code>
                    <button onclick="copyKey()" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-gray-300 transition text-sm">
                        <i class="far fa-copy"></i> Copy
                    </button>
                </div>
            </div>
            
            <div class="flex flex-wrap justify-center gap-2 mb-5">
                <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold"><i class="fas fa-hourglass-half mr-1"></i> 6 Hours</span>
                <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 text-xs font-semibold"><i class="fas fa-mobile-alt mr-1"></i> 1 Device</span>
                <span id="gameTag" class="px-3 py-1 rounded-full bg-green-500/20 text-green-300 text-xs font-semibold">Game</span>
            </div>
            
            <a href="<?php echo esc($_SERVER['SCRIPT_NAME']); ?>" class="btn-gradient w-full py-3 rounded-xl font-semibold text-white flex items-center justify-center gap-2">
                <i class="fas fa-plus-circle"></i> Generate Another Key
            </a>
        </div>
        
        <!-- SUCCESS CARD - Already claimed key -->
        <?php elseif ($success_mode): ?>
        <div class="glass-card rounded-2xl p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-check-circle text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white mb-1">Your License Key</h2>
            <p class="text-gray-400 text-sm mb-5">Key activated for your session</p>
            
            <div class="key-box p-4 mb-5">
                <div class="flex items-center justify-between gap-3">
                    <code id="keyText" class="text-white font-mono text-base font-bold tracking-wider break-all"><?php echo esc($claimed_key); ?></code>
                    <button onclick="copyKey()" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-gray-300 transition text-sm">
                        <i class="far fa-copy"></i> Copy
                    </button>
                </div>
            </div>
            
            <div class="flex flex-wrap justify-center gap-2 mb-5">
                <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold"><i class="fas fa-hourglass-half mr-1"></i> 6 Hours</span>
                <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 text-xs font-semibold"><i class="fas fa-mobile-alt mr-1"></i> 1 Device</span>
                <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-300 text-xs font-semibold"><?php echo esc($claimed_game); ?></span>
            </div>
            
            <a href="<?php echo esc($_SERVER['SCRIPT_NAME']); ?>" class="btn-gradient w-full py-3 rounded-xl font-semibold text-white flex items-center justify-center gap-2">
                <i class="fas fa-plus-circle"></i> Generate Another Key
            </a>
        </div>
        
        <!-- ERROR CARD -->
        <?php elseif (!empty($claim_error)): ?>
        <div class="glass-card rounded-2xl p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-exclamation-triangle text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Verification Failed</h2>
            <p class="text-red-400 text-sm mb-6"><?php echo esc($claim_error); ?></p>
            <a href="<?php echo esc($_SERVER['SCRIPT_NAME']); ?>" class="btn-gradient w-full py-3 rounded-xl font-semibold text-white flex items-center justify-center gap-2">
                <i class="fas fa-redo-alt"></i> Try Again
            </a>
        </div>
        
        <!-- GENERATE FORM - Initial Page -->
        <?php else: ?>
        <div class="glass-card rounded-2xl p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-[#6a7bc2] to-[#8a9be0] flex items-center justify-center shadow-lg">
                    <i class="fas fa-key text-2xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Free Trial Key</h1>
                <p class="text-gray-400 text-sm mt-1">Get 6-hour premium access</p>
            </div>
            
            <!-- Features Grid -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="feature-icon p-3 text-center">
                    <i class="fas fa-clock text-blue-400 text-lg mb-1 block"></i>
                    <span class="text-white text-xs font-medium">6 Hours Access</span>
                </div>
                <div class="feature-icon p-3 text-center">
                    <i class="fas fa-infinity text-purple-400 text-lg mb-1 block"></i>
                    <span class="text-white text-xs font-medium">Full Features</span>
                </div>
                <div class="feature-icon p-3 text-center">
                    <i class="fas fa-mobile-alt text-green-400 text-lg mb-1 block"></i>
                    <span class="text-white text-xs font-medium">1 Device Only</span>
                </div>
                <div class="feature-icon p-3 text-center">
                    <i class="fas fa-shield-alt text-cyan-400 text-lg mb-1 block"></i>
                    <span class="text-white text-xs font-medium">Secure & Safe</span>
                </div>
            </div>
            
            <form id="generateForm" method="POST">
                <input type="hidden" name="action" value="generate">
                <div class="mb-5">
                    <label class="block text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Select Game</label>
                    <select name="game" id="gameSelect" class="w-full bg-gray-900/80 border border-gray-700 rounded-xl py-3 px-4 text-white text-sm focus:outline-none focus:border-[#6a7bc2] transition">
                        <?php foreach ($game_list as $code => $name): ?>
                            <option value="<?php echo esc($code); ?>"><?php echo esc($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" id="generateBtn" class="btn-gradient w-full py-3.5 rounded-xl font-semibold text-white text-base flex items-center justify-center gap-2">
                    <i class="fas fa-bolt"></i> Generate Key
                </button>
            </form>
            
            <div id="loadingSpinner" class="hidden text-center mt-4">
                <div class="inline-block w-6 h-6 border-2 border-blue-400 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-gray-400 text-xs mt-2">Creating secure link...</p>
            </div>
            
            <p class="text-center text-gray-500 text-[10px] mt-6">
                <i class="fas fa-shield-alt text-[10px] mr-1"></i> Verification required for security
            </p>
        </div>
        <?php endif; ?>
        
    </div>
    
    <div id="toast" class="toast">
        <i class="fas fa-check-circle text-green-400 mr-2"></i> Copied to clipboard!
    </div>

    <script>
        function copyKey() {
            const keyText = document.getElementById('keyText').innerText;
            navigator.clipboard.writeText(keyText).then(() => {
                const toast = document.getElementById('toast');
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 2000);
            });
        }
        
        // AJAX Form Submission - Generate Short Link First
        document.getElementById('generateForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btn = document.getElementById('generateBtn');
            const spinner = document.getElementById('loadingSpinner');
            
            btn.disabled = true;
            btn.classList.add('opacity-50');
            spinner.classList.remove('hidden');
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show short link card
                    const shortLinkHtml = `
                        <div class="glass-card rounded-2xl p-6 text-center animate-fade">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                                <i class="fas fa-link text-2xl text-white"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-white mb-2">Complete Verification</h2>
                            <p class="text-gray-400 text-sm mb-5">Click below to verify and claim your key</p>
                            
                            <a href="${data.short_link}" target="_blank" class="btn-gradient w-full py-3.5 rounded-xl font-semibold text-white flex items-center justify-center gap-2 mb-4">
                                <i class="fas fa-external-link-alt"></i> Click Here to Continue
                            </a>
                            
                            <div class="border-t border-gray-700/50 pt-4 mt-2">
                                <p class="text-gray-500 text-[11px]">
                                    <i class="fas fa-shield-alt mr-1 text-blue-400"></i> 
                                    Complete the short link verification first
                                </p>
                                <p class="text-gray-600 text-[10px] mt-2">
                                    After clicking, you will be redirected to claim your key
                                </p>
                            </div>
                        </div>
                    `;
                    document.querySelector('.w-full.max-w-md').innerHTML = shortLinkHtml;
                } else {
                    alert(data.error || 'Failed to generate key. Please try again.');
                    location.reload();
                }
            } catch (error) {
                alert('Network error. Please try again.');
                location.reload();
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-50');
                spinner.classList.add('hidden');
            }
        });
        
        <?php if (!empty($js_challenge_salt) && !$success_mode && empty($claim_error)): ?>
        document.addEventListener("DOMContentLoaded", function() {
            const circle = document.getElementById('progressCircle');
            const radius = 44;
            const circumference = radius * 2 * Math.PI;
            circle.style.strokeDasharray = circumference + ' ' + circumference;
            circle.style.strokeDashoffset = circumference;
            
            function setProgress(percent) {
                const offset = circumference - (percent / 100 * circumference);
                circle.style.strokeDashoffset = offset;
                document.getElementById('progressText').innerText = Math.round(percent) + '%';
            }
            
            const challengeKey = "<?php echo esc($verify_key); ?>";
            const challengeToken = "<?php echo esc($verify_token); ?>";
            const challengeSalt = "<?php echo esc($js_challenge_salt); ?>";
            
            let percent = 0;
            const totalDuration = 3800;
            const intervalTime = 35;
            const increment = 100 / (totalDuration / intervalTime);
            const statusTexts = ["Securing gateway...", "Running validation...", "Checking environment...", "Finalizing license..."];
            
            const interval = setInterval(function() {
                percent += increment;
                if (percent >= 100) {
                    percent = 100;
                    clearInterval(interval);
                    
                    const challengeResponse = md5(md5(challengeKey + challengeToken) + challengeSalt);
                    
                    const formData = new FormData();
                    formData.append('action', 'claim_api');
                    formData.append('key', challengeKey);
                    formData.append('game', "<?php echo esc($verify_game); ?>");
                    formData.append('time', "<?php echo $verify_time; ?>");
                    formData.append('token', challengeToken);
                    formData.append('signature', challengeResponse);
                    
                    document.getElementById('challengeStatus').innerText = "Processing...";
                    
                    fetch('', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('challengeCard').style.display = 'none';
                            document.getElementById('keyText').innerText = data.key;
                            document.getElementById('gameTag').innerText = data.game;
                            document.getElementById('successCard').style.display = 'block';
                            copyKey();
                        } else {
                            renderError(data.error || "Verification failed.");
                        }
                    })
                    .catch(() => renderError("Network error. Please refresh."));
                }
                setProgress(percent);
                const textIndex = Math.min(Math.floor(percent / 25), statusTexts.length - 1);
                document.getElementById('challengeStatus').innerText = statusTexts[textIndex];
            }, intervalTime);
            
            function renderError(msg) {
                const card = document.getElementById('challengeCard');
                card.innerHTML = `<div class="text-center"><div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg"><i class="fas fa-exclamation-triangle text-3xl text-white"></i></div><h2 class="text-2xl font-bold text-white mb-2">Security Error</h2><p class="text-red-400 text-sm mb-6">${msg}</p><a href="<?php echo esc($_SERVER['SCRIPT_NAME']); ?>" class="btn-gradient inline-block w-full py-3 rounded-xl font-semibold text-white text-center">Start Over</a></div>`;
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>