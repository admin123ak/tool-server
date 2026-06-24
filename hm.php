<?php
/**
 * نظام HEX BLADE v7.0 - النسخة المتطورة
 * نظام إدارة تراخيص متقدم مع واجهة احترافية وتأمين عالي
 * كلمة المرور: h0azaz
 */

// ======================================================
// 1. إعدادات النظام المتقدمة
// ======================================================
error_reporting(0);
ini_set('display_errors', 0);

// إعدادات النظام
define('ADMIN_PASSWORD', 'sx2ladorsannso0999099'); // كلمة سر لوحة التحكم
define('KEYS_FILE', 'keys.json');
define('TEMPLATE_FILE', 'connect.json');
define('LOG_FILE', 'system.log');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 دقيقة
define('SESSION_TIMEOUT', 86400 * 30); // 30 يوم - تم التعديل

// إنشاء ملفات النظام إذا لم تكن موجودة
if (!file_exists(KEYS_FILE)) {
    file_put_contents(KEYS_FILE, json_encode([], JSON_PRETTY_PRINT));
}

if (!file_exists(LOG_FILE)) {
    file_put_contents(LOG_FILE, '');
}

// بدء الجلسة في بداية الكود - تم النقل هنا
session_start([
    'cookie_lifetime' => SESSION_TIMEOUT,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'gc_maxlifetime' => SESSION_TIMEOUT,
    'use_strict_mode' => true
]);

// ======================================================
// 2. دوال النظام المساعدة
// ======================================================

// تسجيل النشاطات
function logActivity($action, $details = '') {
    $logEntry = sprintf(
        "[%s] IP: %s | Action: %s | Details: %s\n",
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
        $action,
        $details
    );
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);
}

// فحص الهجوم (Brute Force Protection)
function checkBruteForce($username) {
    $attemptsFile = 'login_attempts.json';
    $attempts = [];
    
    if (file_exists($attemptsFile)) {
        $attempts = json_decode(file_get_contents($attemptsFile), true);
    }
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $currentTime = time();
    
    if (isset($attempts[$ip])) {
        // إزالة المحاولات القديمة
        foreach ($attempts[$ip] as $key => $attempt) {
            if ($currentTime - $attempt['time'] > LOCKOUT_TIME) {
                unset($attempts[$ip][$key]);
            }
        }
        
        $attempts[$ip] = array_values($attempts[$ip]);
        
        // التحقق إذا تم تجاوز الحد المسموح
        if (count($attempts[$ip]) >= MAX_LOGIN_ATTEMPTS) {
            $lastAttempt = end($attempts[$ip]);
            if ($currentTime - $lastAttempt['time'] < LOCKOUT_TIME) {
                return true; // محظور
            }
        }
    }
    
    return false;
}

// تسجيل محاولة دخول فاشلة
function recordFailedAttempt() {
    $attemptsFile = 'login_attempts.json';
    $attempts = [];
    
    if (file_exists($attemptsFile)) {
        $attempts = json_decode(file_get_contents($attemptsFile), true);
    }
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $currentTime = time();
    
    if (!isset($attempts[$ip])) {
        $attempts[$ip] = [];
    }
    
    $attempts[$ip][] = [
        'time' => $currentTime,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    
    file_put_contents($attemptsFile, json_encode($attempts, JSON_PRETTY_PRINT));
}

// تنظيف محاولات الدخول القديمة
function cleanupOldAttempts() {
    $attemptsFile = 'login_attempts.json';
    if (!file_exists($attemptsFile)) return;
    
    $attempts = json_decode(file_get_contents($attemptsFile), true);
    $currentTime = time();
    
    foreach ($attempts as $ip => &$ipAttempts) {
        foreach ($ipAttempts as $key => $attempt) {
            if ($currentTime - $attempt['time'] > LOCKOUT_TIME * 2) {
                unset($ipAttempts[$key]);
            }
        }
        $ipAttempts = array_values($ipAttempts);
        
        if (empty($ipAttempts)) {
            unset($attempts[$ip]);
        }
    }
    
    file_put_contents($attemptsFile, json_encode($attempts, JSON_PRETTY_PRINT));
}

// ترميز البيانات قبل العرض
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// توليد رمز CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_tokens'][$token] = time();
    
    // تنظيف الرموز القديمة
    foreach ($_SESSION['csrf_tokens'] as $storedToken => $timestamp) {
        if (time() - $timestamp > 3600) { // ساعة واحدة
            unset($_SESSION['csrf_tokens'][$storedToken]);
        }
    }
    
    return $token;
}

// التحقق من رمز CSRF
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_tokens'][$token])) {
        return false;
    }
    
    $timestamp = $_SESSION['csrf_tokens'][$token];
    if (time() - $timestamp > 3600) { // ساعة واحدة
        unset($_SESSION['csrf_tokens'][$token]);
        return false;
    }
    
    unset($_SESSION['csrf_tokens'][$token]);
    return true;
}

// تجديد وقت الجلسة تلقائياً
function renewSession() {
    if (isset($_SESSION['login_time'])) {
        $_SESSION['login_time'] = time();
    }
}

// ======================================================
// 3. معالجة طلبات التطبيق (API)
// ======================================================
$input = file_get_contents('php://input');
$requestData = json_decode($input, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($requestData['key'])) {
    header('Content-Type: application/json; charset=UTF-8');
    
    // تسجيل طلب API
    logActivity('API_REQUEST', 'Key: ' . substr($requestData['key'], 0, 8) . '***');
    
    $key = $requestData['key'];
    $hwid = $requestData['hwid'] ?? 'unknown';
    $keysDB = json_decode(file_get_contents(KEYS_FILE), true);

    if (!isset($keysDB[$key])) {
        http_response_code(401);
        logActivity('API_FAILED', 'Invalid key: ' . substr($key, 0, 8) . '***');
        die(json_encode(["status" => false, "message" => "Auth Failed"]));
    }

    $keyData = &$keysDB[$key];

    if (strtotime($keyData['expiry']) < time()) {
        http_response_code(401);
        logActivity('API_FAILED', 'Expired key: ' . substr($key, 0, 8) . '***');
        die(json_encode(["status" => false, "message" => "Key Expired"]));
    }

    if (!isset($keyData['hwids'])) $keyData['hwids'] = [];
    
    if (!in_array($hwid, $keyData['hwids'])) {
        if (count($keyData['hwids']) < $keyData['max_devices']) {
            $keyData['hwids'][] = $hwid;
            $keyData['last_used'] = date('Y-m-d H:i:s');
            file_put_contents(KEYS_FILE, json_encode($keysDB, JSON_PRETTY_PRINT));
            logActivity('NEW_DEVICE', 'Key: ' . substr($key, 0, 8) . '*** - HWID: ' . substr($hwid, 0, 12) . '***');
        } else {
            http_response_code(401);
            logActivity('DEVICE_LIMIT', 'Key: ' . substr($key, 0, 8) . '***');
            die(json_encode(["status" => false, "message" => "Device Limit Reached"]));
        }
    } else {
        $keyData['last_used'] = date('Y-m-d H:i:s');
        file_put_contents(KEYS_FILE, json_encode($keysDB, JSON_PRETTY_PRINT));
    }

    if (file_exists(TEMPLATE_FILE)) {
        $response = json_decode(file_get_contents(TEMPLATE_FILE), true);
        $response['status'] = true;
        $response['expire_date'] = $keyData['expiry'];
        $response['device_count'] = count($keyData['hwids']);
        $response['max_devices'] = $keyData['max_devices'];
        $response['rng'] = rand(100000, 999999);
        logActivity('API_SUCCESS', 'Key: ' . substr($key, 0, 8) . '***');
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
    }
    exit();
}

// ======================================================
// 4. لوحة التحكم (Dashboard)
// ======================================================

// تنظيف محاولات الدخول القديمة
cleanupOldAttempts();

// معالجة تسجيل الخروج
if (isset($_GET['logout'])) { 
    logActivity('LOGOUT', 'User logged out');
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(), '', 0, '/');
    header("Location: hm.php");
    exit();
}

// معالجة تسجيل الدخول
if (isset($_POST['login_submit'])) {
    // التحقق من الهجوم
    if (checkBruteForce('admin')) {
        $login_error = "تم تجاوز عدد المحاولات المسموح بها. الرجاء الانتظار 15 دقيقة.";
        logActivity('BRUTE_FORCE_BLOCKED', 'IP: ' . $_SERVER['REMOTE_ADDR']);
    } else {
        $entered_password = $_POST['admin_pass'] ?? '';
        
        if (hash_equals(ADMIN_PASSWORD, $entered_password)) {
            $_SESSION['admin_auth'] = true;
            $_SESSION['login_time'] = time();
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $_SESSION['csrf_token'] = generateCSRFToken();
            
            logActivity('LOGIN_SUCCESS', 'Admin logged in from IP: ' . $_SERVER['REMOTE_ADDR']);
            
            // إعادة توجيه آمنة
            header("Location: hm.php");
            exit();
        } else {
            recordFailedAttempt();
            $login_error = "كلمة المرور غير صحيحة!";
            logActivity('LOGIN_FAILED', 'Invalid password attempt from IP: ' . $_SERVER['REMOTE_ADDR']);
        }
    }
}

// التحقق من الجلسة مع تجديد تلقائي
if (isset($_SESSION['admin_auth'])) {
    // تجديد وقت الجلسة تلقائياً
    renewSession();
    
    // التحقق من تغيير IP أو User Agent
    if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR'] || 
        $_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
        logActivity('SESSION_HIJACK_ATTEMPT', 'IP or User-Agent changed');
        session_destroy();
        header("Location: hm.php");
        exit();
    }
}

// عرض صفحة تسجيل الدخول إذا لم يكن مسجل الدخول
if (!isset($_SESSION['admin_auth'])): 
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 HEX BLADE- Log in </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Particle.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --dark-bg: #0f172a;
            --card-bg: rgba(255, 255, 255, 0.1);
            --text-light: #f8fafc;
            --accent-color: #3b82f6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background: var(--dark-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            overflow: hidden;
            position: relative;
        }
        
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 45px 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }
        
        .brand-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-container {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            position: relative;
        }
        
        .logo-main {
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            animation: pulse 2s infinite;
        }
        
        .logo-glow {
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: var(--primary-gradient);
            border-radius: 50%;
            filter: blur(20px);
            opacity: 0.3;
            animation: pulseGlow 2s infinite;
        }
        
        .brand-title {
            font-size: 32px;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        
        .brand-subtitle {
            color: #94a3b8;
            font-size: 15px;
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            color: #cbd5e1;
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: right;
        }
        
        .input-group {
            position: relative;
        }
        
        .password-input {
            width: 100%;
            padding: 18px 50px 18px 20px;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
            text-align: right;
        }
        
        .password-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background: rgba(255, 255, 255, 0.12);
        }
        
        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
        }
        
        .toggle-password {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s;
        }
        
        .toggle-password:hover {
            color: var(--accent-color);
        }
        
        .btn-login {
            width: 100%;
            padding: 18px;
            background: var(--primary-gradient);
            border: none;
            border-radius: 14px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 25px;
            color: #fecaca;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
        }
        
        .alert-danger i {
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .security-info {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .security-stats {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            display: block;
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .footer-text {
            color: #64748b;
            font-size: 13px;
            text-align: center;
            margin-top: 25px;
        }
        
        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
        }
        
        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .login-wrapper {
                padding: 15px;
            }
            
            .login-card {
                padding: 35px 25px;
            }
            
            .brand-title {
                font-size: 28px;
            }
            
            .logo-container {
                width: 80px;
                height: 80px;
            }
            
            .logo-main {
                font-size: 35px;
            }
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .brand-title {
                font-size: 24px;
            }
            
            .security-stats {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Particle Background -->
    <div id="particles-js"></div>
    
    <!-- Login Container -->
    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-header">
                <div class="logo-container">
                    <div class="logo-main">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="logo-glow"></div>
                </div>
                <h1 class="brand-title">HEX BLADE</h1>
                <p class="brand-subtitle">نظام إدارة تراخيص متقدم</p>
            </div>
            
            <?php if (isset($login_error)): ?>
            <div class="alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <div><?php echo sanitizeOutput($login_error); ?></div>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm" autocomplete="off">
                <div class="form-group">
                    <label class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <input type="password" name="admin_pass" class="password-input" 
                               placeholder="••••••••" required autofocus>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="login_submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>تسجيل الدخول</span>
                </button>
            </form>
            
            <div class="security-info">
                <div class="security-stats">
                    <div class="stat-item">
                        <span class="stat-number" id="secureConnections">256-bit</span>
                        <span class="stat-label">تشفير</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="activeSessions">24/7</span>
                        <span class="stat-label">مراقبة</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="protectionLevel">99.9%</span>
                        <span class="stat-label">حماية</span>
                    </div>
                </div>
                
                <div class="footer-text">
                    <i class="fas fa-shield-alt me-2"></i>
                    نظام آمن | إدارة متقدمة | لوحة تحكم احترافية
                    <br>
                    © 2026 HEX BLADE System
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // تهيئة Particle Background
        particlesJS("particles-js", {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: "#3b82f6" },
                shape: { type: "circle" },
                opacity: { value: 0.5, random: true },
                size: { value: 3, random: true },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#3b82f6",
                    opacity: 0.2,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: "none",
                    random: true,
                    straight: false,
                    out_mode: "out",
                    bounce: false
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "repulse" },
                    onclick: { enable: true, mode: "push" }
                }
            },
            retina_detect: true
        });
        
        // تبديل عرض كلمة المرور
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.querySelector('input[name="admin_pass"]');
        const eyeIcon = togglePassword.querySelector('i');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        });
        
        // Focus على حقل كلمة المرور
        document.addEventListener('DOMContentLoaded', function() {
            passwordInput.focus();
        });
        
        // منع إعادة إرسال النموذج
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // تأثيرات الإحصائيات
        function animateStats() {
            const stats = {
                secureConnections: '256-bit',
                activeSessions: '24/7',
                protectionLevel: '99.9%'
            };
            
            Object.keys(stats).forEach((id, index) => {
                const element = document.getElementById(id);
                if (element) {
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(10px)';
                    
                    setTimeout(() => {
                        element.textContent = stats[id];
                        element.style.transition = 'all 0.5s ease';
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }, index * 200);
                }
            });
        }
        
        // تشغيل التأثيرات عند تحميل الصفحة
        setTimeout(animateStats, 1000);
        
        // التأكد من عدم حفظ كلمة المرور في المتصفح
        document.getElementById('loginForm').addEventListener('submit', function() {
            passwordInput.value = passwordInput.value.trim();
        });
        
        // إضافة تأثيرات تفاعلية
        const inputs = document.querySelectorAll('.password-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
<?php exit(); endif;

// ======================================================
// 5. لوحة التحكم الرئيسية
// ======================================================

// توليد رمز CSRF جديد إذا لم يكن موجوداً
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCSRFToken();
}

// تحميل قاعدة بيانات المفاتيح
$keysDB = json_decode(file_get_contents(KEYS_FILE), true) ?: [];

// معالجة عمليات لوحة التحكم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        logActivity('CSRF_FAILED', 'Invalid CSRF token');
        die('رمز التحقق غير صالح. الرجاء تحديث الصفحة والمحاولة مرة أخرى.');
    }
    
    // إنشاء مفتاح جديد
    if (isset($_POST['save_key'])) {
        $name = !empty($_POST['key_name']) ? trim($_POST['key_name']) : 
                'KEY_' . strtoupper(substr(md5(microtime() . random_bytes(8)), 0, 10));
        
        $keysDB[$name] = [
            "expiry" => date('Y-m-d H:i:s', strtotime("+" . intval($_POST['days']) . " days")),
            "max_devices" => min(9999, max(1, intval($_POST['max_devices']))),
            "hwids" => [],
            "created_at" => date('Y-m-d H:i:s'),
            "last_used" => null,
            "note" => trim($_POST['key_note'] ?? '')
        ];
        
        file_put_contents(KEYS_FILE, json_encode($keysDB, JSON_PRETTY_PRINT));
        logActivity('KEY_CREATED', 'Key: ' . substr($name, 0, 8) . '***');
        
        $_SESSION['success_message'] = "تم إنشاء المفتاح <strong>" . sanitizeOutput($name) . "</strong> بنجاح!";
        header("Location: hm.php");
        exit();
    }
    
    // تحديث مفتاح
    if (isset($_POST['update_key'])) {
        $old = $_POST['old_name'];
        $new = trim($_POST['edit_name']);
        
        if (isset($keysDB[$old])) {
            if ($old !== $new) {
                $keysDB[$new] = $keysDB[$old];
                unset($keysDB[$old]);
            }
            
            $hwids = !empty($_POST['edit_hwids']) ? 
                     array_map('trim', explode(',', $_POST['edit_hwids'])) : [];
            
            $keysDB[$new] = [
                "expiry" => $_POST['edit_expiry'],
                "max_devices" => min(9999, max(1, intval($_POST['edit_max']))),
                "hwids" => $hwids,
                "created_at" => $keysDB[$new]['created_at'] ?? date('Y-m-d H:i:s'),
                "last_used" => $keysDB[$new]['last_used'] ?? null,
                "note" => trim($_POST['edit_note'] ?? '')
            ];
            
            file_put_contents(KEYS_FILE, json_encode($keysDB, JSON_PRETTY_PRINT));
            logActivity('KEY_UPDATED', 'Key: ' . substr($new, 0, 8) . '***');
            
            $_SESSION['success_message'] = "تم تحديث المفتاح <strong>" . sanitizeOutput($new) . "</strong> بنجاح!";
            header("Location: hm.php");
            exit();
        }
    }
    
    // نسخ المفاتيح
    if (isset($_POST['bulk_action'])) {
        $action = $_POST['bulk_action'];
        $selected_keys = $_POST['selected_keys'] ?? [];
        
        if (!empty($selected_keys)) {
            foreach ($selected_keys as $key) {
                if (isset($keysDB[$key])) {
                    if ($action === 'delete') {
                        unset($keysDB[$key]);
                        logActivity('KEY_DELETED', 'Key: ' . substr($key, 0, 8) . '***');
                    } elseif ($action === 'extend') {
                        $keysDB[$key]['expiry'] = date('Y-m-d H:i:s', strtotime("+30 days", strtotime($keysDB[$key]['expiry'])));
                        logActivity('KEY_EXTENDED', 'Key: ' . substr($key, 0, 8) . '***');
                    }
                }
            }
            
            file_put_contents(KEYS_FILE, json_encode($keysDB, JSON_PRETTY_PRINT));
            $_SESSION['success_message'] = "تم تنفيذ الإجراء على " . count($selected_keys) . " مفتاح!";
            header("Location: hm.php");
            exit();
        }
    }
}

// حذف مفتاح
if (isset($_GET['del']) && isset($_GET['csrf']) && verifyCSRFToken($_GET['csrf'])) {
    $key_to_delete = $_GET['del'];
    if (isset($keysDB[$key_to_delete])) {
        unset($keysDB[$key_to_delete]);
        file_put_contents(KEYS_FILE, json_encode($keysDB, JSON_PRETTY_PRINT));
        logActivity('KEY_DELETED', 'Key: ' . substr($key_to_delete, 0, 8) . '***');
        $_SESSION['success_message'] = "تم حذف المفتاح بنجاح!";
        header("Location: hm.php");
        exit();
    }
}

// إحصائيات النظام
$total_keys = count($keysDB);
$active_keys = 0;
$expired_keys = 0;
$total_devices = 0;
$near_expiry = 0;

foreach ($keysDB as $key => $data) {
    $expiry_time = strtotime($data['expiry']);
    if ($expiry_time > time()) {
        $active_keys++;
        
        // مفاتيح قريبة من الانتهاء (أقل من 3 أيام)
        if (($expiry_time - time()) < (3 * 24 * 3600)) {
            $near_expiry++;
        }
    } else {
        $expired_keys++;
    }
    $total_devices += count($data['hwids'] ?? []);
}

// استخراج رسالة النجاح من الجلسة
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛡️ HEX BLADE - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --warning-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            --dark-bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --sidebar-bg: #1e293b;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --active-color: #10b981;
            --expired-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* تخصيص شريط التمرير */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5);
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        /* Navbar */
        .navbar {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 15px 0;
            box-shadow: var(--shadow-lg);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand i {
            font-size: 1.5rem;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .btn-logout {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fecaca;
            padding: 8px 20px;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.3);
            transform: translateY(-2px);
        }
        
        /* Main Container */
        .main-container {
            padding: 30px 20px;
        }
        
        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }
        
        .stat-card:nth-child(1)::before { background: var(--primary-gradient); }
        .stat-card:nth-child(2)::before { background: var(--success-gradient); }
        .stat-card:nth-child(3)::before { background: var(--warning-gradient); }
        .stat-card:nth-child(4)::before { background: var(--danger-gradient); }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }
        
        .stat-card:nth-child(1) .stat-icon { background: var(--primary-gradient); }
        .stat-card:nth-child(2) .stat-icon { background: var(--success-gradient); }
        .stat-card:nth-child(3) .stat-icon { background: var(--warning-gradient); }
        .stat-card:nth-child(4) .stat-icon { background: var(--danger-gradient); }
        
        .stat-trend {
            font-size: 14px;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #f8fafc 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Keys Grid */
        .keys-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .key-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .key-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .key-card.active {
            border-left: 4px solid var(--active-color);
        }
        
        .key-card.expired {
            border-left: 4px solid var(--expired-color);
        }
        
        .key-card.warning {
            border-left: 4px solid var(--warning-color);
        }
        
        .key-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .key-name {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--text-primary);
            margin-bottom: 5px;
        }
        
        .key-meta {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
        
        .key-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-active {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }
        
        .status-expired {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }
        
        .status-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }
        
        .key-content {
            margin-bottom: 15px;
        }
        
        .key-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .key-info-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        
        .key-info-value {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-primary);
        }
        
        .progress-container {
            margin: 15px 0;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
        
        .progress {
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        
        .key-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .key-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-action-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-action-icon:hover {
            transform: translateY(-2px);
        }
        
        .btn-copy:hover {
            background: rgba(16, 185, 129, 0.2);
            border-color: #10b981;
            color: #10b981;
        }
        
        .btn-edit:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
            color: #3b82f6;
        }
        
        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
            color: #ef4444;
        }
        
        /* Create Key Card */
        .create-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .create-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-title i {
            color: #667eea;
        }
        
        .form-label {
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 12px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            color: var(--text-primary);
        }
        
        .form-control::placeholder {
            color: #64748b;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(30, 41, 59, 0.3);
            border-radius: 15px;
            border: 2px dashed var(--border-color);
            margin: 20px 0;
        }
        
        .empty-icon {
            font-size: 70px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-text {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .empty-subtext {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1050;
        }
        
        .toast {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-xl);
            color: var(--text-primary);
            padding: 15px 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            max-width: 400px;
            animation: slideInLeft 0.3s ease;
        }
        
        .toast-success {
            border-left: 4px solid #10b981;
        }
        
        .toast-error {
            border-left: 4px solid #ef4444;
        }
        
        .toast-icon {
            font-size: 24px;
        }
        
        .toast-success .toast-icon { color: #10b981; }
        .toast-error .toast-icon { color: #ef4444; }
        
        .toast-content {
            flex: 1;
        }
        
        .toast-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .toast-message {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        
        .toast-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 5px;
            font-size: 18px;
            transition: color 0.3s;
        }
        
        .toast-close:hover {
            color: var(--text-primary);
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            padding: 30px 0;
            text-align: center;
            color: var(--text-secondary);
            border-top: 1px solid var(--border-color);
        }
        
        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        
        .footer-logo {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .footer-text {
            font-size: 0.9rem;
            color: #64748b;
        }
        
        .footer-links {
            display: flex;
            gap: 20px;
        }
        
        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
            font-size: 0.9rem;
        }
        
        .footer-links a:hover {
            color: var(--text-primary);
        }
        
        /* Modal */
        .modal-content {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            color: var(--text-primary);
        }
        
        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 25px;
        }
        
        .modal-title {
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 20px 25px;
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        /* Key Copy Animation */
        .key-copy-animation {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--primary-gradient);
            color: white;
            padding: 20px 40px;
            border-radius: 15px;
            font-weight: 600;
            z-index: 9999;
            animation: copyPulse 0.5s ease;
            box-shadow: var(--shadow-xl);
            display: none;
        }
        
        @keyframes copyPulse {
            0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0; }
            50% { transform: translate(-50%, -50%) scale(1.1); }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        }
        
        /* Animations */
        @keyframes slideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }
            
            .stat-value {
                font-size: 28px;
            }
            
            .keys-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 15px;
            }
            
            .key-card {
                padding: 15px;
            }
        }
        
        @media (max-width: 576px) {
            .main-container {
                padding: 15px;
            }
            
            .create-card {
                padding: 20px;
            }
            
            .keys-grid {
                grid-template-columns: 1fr;
            }
            
            .key-actions {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-gem"></i>
                <span>HEX BLADE</span>
            </a>
            
            <div class="user-menu">
                <div class="user-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <a href="?logout=1" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <span class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                        <?php echo $total_keys > 0 ? round(($active_keys / $total_keys) * 100) : 0; ?>%
                    </span>
                </div>
                <div class="stat-value"><?php echo $total_keys; ?></div>
                <div class="stat-label">إجمالي المفاتيح</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="stat-trend">
                        <i class="fas fa-bolt"></i>
                        نشط
                    </span>
                </div>
                <div class="stat-value"><?php echo $active_keys; ?></div>
                <div class="stat-label">مفاتيح نشطة</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="stat-trend">
                        <i class="fas fa-exclamation-triangle"></i>
                        قريب
                    </span>
                </div>
                <div class="stat-value"><?php echo $near_expiry; ?></div>
                <div class="stat-label">تنتهي قريباً</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <span class="stat-trend">
                        <i class="fas fa-link"></i>
                        متصل
                    </span>
                </div>
                <div class="stat-value"><?php echo $total_devices; ?></div>
                <div class="stat-label">جهاز نشط</div>
            </div>
        </div>

        <!-- Success Message -->
        <?php if ($success_message): ?>
        <div class="toast toast-success" id="successToast">
            <div class="toast-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">تم بنجاح</div>
                <div class="toast-message"><?php echo $success_message; ?></div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('successToast');
                if (toast) toast.remove();
            }, 5000);
        </script>
        <?php endif; ?>

        <!-- Create Key Form -->
        <div class="create-card">
            <h2 class="card-title">
                <i class="fas fa-plus-circle"></i>
                إنشاء مفتاح جديد
            </h2>
            
            <form method="POST" id="createKeyForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">اسم المفتاح (اختياري)</label>
                        <input type="text" name="key_name" class="form-control" 
                               placeholder="سيتم توليد اسم تلقائياً">
                        <div class="form-text">اسم سهل التذكر للمفتاح</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">ملاحظات (اختياري)</label>
                        <input type="text" name="key_note" class="form-control" 
                               placeholder="أضف ملاحظة للمفتاح">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">مدة الصلاحية</label>
                        <select name="days" class="form-select">
                            <option value="1">1 يوم</option>
                            <option value="7">7 أيام</option>
                            <option value="30" selected>30 يوم</option>
                            <option value="90">90 يوم</option>
                            <option value="365">سنة واحدة</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">حد الأجهزة</label>
                        <select name="max_devices" class="form-select">
                            <option value="1">1 جهاز</option>
                            <option value="3" selected>3 أجهزة</option>
                            <option value="5">5 أجهزة</option>
                            <option value="10">10 أجهزة</option>
                            <option value="9999">غير محدود</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" name="save_key" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            إنشاء المفتاح
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Keys Grid -->
        <div class="keys-grid" id="keysGrid">
            <?php foreach($keysDB as $name => $info): 
                $used = count($info['hwids'] ?? []);
                $limit = $info['max_devices'];
                $expiry_time = strtotime($info['expiry']);
                $is_expired = $expiry_time < time();
                $days_left = ceil(($expiry_time - time()) / (60 * 60 * 24));
                
                // تحديد حالة المفتاح
                if ($is_expired) {
                    $status_class = 'status-expired';
                    $status_text = 'منتهي';
                    $card_class = 'expired';
                    $progress_color = 'var(--expired-color)';
                } elseif ($days_left <= 3) {
                    $status_class = 'status-warning';
                    $status_text = 'قريب';
                    $card_class = 'warning';
                    $progress_color = 'var(--warning-color)';
                } else {
                    $status_class = 'status-active';
                    $status_text = 'نشط';
                    $card_class = 'active';
                    $progress_color = 'var(--active-color)';
                }
                
                // حساب نسبة الاستخدام
                $usage_percent = $limit > 0 ? min(100, ($used / $limit) * 100) : 0;
            ?>
            <div class="key-card <?php echo $card_class; ?>" data-key="<?php echo $name; ?>">
                <div class="key-header">
                    <div>
                        <div class="key-name"><?php echo sanitizeOutput($name); ?></div>
                        <?php if (!empty($info['note'])): ?>
                        <div class="key-meta"><?php echo sanitizeOutput($info['note']); ?></div>
                        <?php endif; ?>
                    </div>
                    <span class="key-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                </div>
                
                <div class="key-content">
                    <div class="key-info-row">
                        <span class="key-info-label">تاريخ الإنشاء</span>
                        <span class="key-info-value"><?php echo date('Y-m-d', strtotime($info['created_at'] ?? 'now')); ?></span>
                    </div>
                    
                    <div class="key-info-row">
                        <span class="key-info-label">تاريخ الانتهاء</span>
                        <span class="key-info-value"><?php echo date('Y-m-d', $expiry_time); ?></span>
                    </div>
                    
                    <div class="key-info-row">
                        <span class="key-info-label">الوقت المتبقي</span>
                        <span class="key-info-value">
                            <?php 
                            if ($is_expired) {
                                echo 'منتهي منذ ' . abs($days_left) . ' يوم';
                            } elseif ($days_left == 0) {
                                echo 'ينتهي اليوم';
                            } else {
                                echo $days_left . ' يوم';
                            }
                            ?>
                        </span>
                    </div>
                    
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>الأجهزة: <?php echo $used; ?>/<?php echo $limit == 9999 ? '∞' : $limit; ?></span>
                            <span><?php echo round($usage_percent); ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $usage_percent; ?>%; background: <?php echo $progress_color; ?>"></div>
                        </div>
                    </div>
                </div>
                
                <div class="key-footer">
                    <div class="key-meta">
                        <i class="fas fa-history"></i>
                        آخر استخدام: <?php echo $info['last_used'] ? date('Y-m-d', strtotime($info['last_used'])) : 'لم يتم الاستخدام'; ?>
                    </div>
                    <div class="key-actions">
                        <button class="btn-action-icon btn-copy" onclick="copyKey('<?php echo $name; ?>', this)" title="نسخ المفتاح">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button class="btn-action-icon btn-edit" onclick="editKey('<?php echo $name; ?>')" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action-icon btn-delete" onclick="deleteKey('<?php echo $name; ?>')" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($keysDB)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="empty-text">لا توجد مفاتيح حالياً</div>
                <div class="empty-subtext">ابدأ بإنشاء مفتاحك الأول باستخدام النموذج أعلاه</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div class="footer-text">
                        <strong>HEX BLADE</strong> - نظام إدارة التراخيص المتقدم
                        <br>
                        آمن بمفتاح فريد | إدارة متقدمة للأجهزة | واجهة مستخدم حديثة
                    </div>
                    <div class="footer-links">
                        <a href="#" onclick="showSystemInfo()">
                            <i class="fas fa-info-circle"></i> معلومات النظام
                        </a>
                        <a href="#" onclick="showLogs()">
                            <i class="fas fa-history"></i> السجلات
                        </a>
                        <a href="#" onclick="showHelp()">
                            <i class="fas fa-question-circle"></i> المساعدة
                        </a>
                    </div>
                    <div class="footer-text">
                        آخر تحديث: <?php echo date('Y-m-d H:i:s'); ?>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Key Copy Animation -->
    <div class="key-copy-animation" id="copyAnimation">
        <i class="fas fa-check-circle me-2"></i>
        <span id="copyMessage">تم النسخ!</span>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="editForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="old_name" id="editOldName">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit"></i>
                            تعديل المفتاح
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم المفتاح</label>
                                <input type="text" name="edit_name" id="editName" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">ملاحظات</label>
                                <input type="text" name="edit_note" id="editNote" class="form-control">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الانتهاء</label>
                                <input type="datetime-local" name="edit_expiry" id="editExpiry" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">حد الأجهزة</label>
                                <select name="edit_max" id="editMax" class="form-select">
                                    <option value="1">1 جهاز</option>
                                    <option value="3">3 أجهزة</option>
                                    <option value="5">5 أجهزة</option>
                                    <option value="10">10 أجهزة</option>
                                    <option value="9999">غير محدود</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">بصمات الأجهزة (HWID)</label>
                                <textarea name="edit_hwids" id="editHwids" class="form-control" rows="4" 
                                          placeholder="أدخل بصمات الأجهزة مفصولة بفواصل"></textarea>
                                <div class="form-text">لإعادة تعيين الأجهزة، اترك هذا الحقل فارغاً</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" name="update_key" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- System Info Modal -->
    <div class="modal fade" id="systemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle"></i>
                        معلومات النظام
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="mb-3">إحصائيات النظام</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>المفاتيح النشطة:</span>
                                <strong><?php echo $active_keys; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>المفاتيح المنتهية:</span>
                                <strong><?php echo $expired_keys; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>إجمالي الأجهزة:</span>
                                <strong><?php echo $total_devices; ?></strong>
                            </div>
                            <hr>
                            <h6 class="mb-3">معلومات الجلسة</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>آخر دخول:</span>
                                <strong><?php echo date('Y-m-d H:i:s', $_SESSION['login_time'] ?? time()); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>عنوان IP:</span>
                                <strong><?php echo $_SESSION['user_ip'] ?? 'غير معروف'; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>مدة الجلسة:</span>
                                <strong><?php echo gmdate("H:i:s", time() - ($_SESSION['login_time'] ?? time())); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        // Copy key to clipboard with animation
        function copyKey(key, button) {
            // Add click effect to button
            if (button) {
                button.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    button.style.transform = '';
                }, 200);
            }
            
            // Copy to clipboard
            navigator.clipboard.writeText(key).then(() => {
                // Show copy animation
                const copyAnim = document.getElementById('copyAnimation');
                const copyMsg = document.getElementById('copyMessage');
                
                copyMsg.textContent = `تم نسخ المفتاح: ${key}`;
                copyAnim.style.display = 'block';
                
                // Hide animation after 2 seconds
                setTimeout(() => {
                    copyAnim.style.display = 'none';
                }, 2000);
                
                // Show toast notification
                showToast('تم النسخ', `تم نسخ المفتاح: ${key}`, 'success');
                
            }).catch(err => {
                console.error('فشل النسخ:', err);
                showToast('خطأ', 'فشل نسخ المفتاح', 'error');
            });
        }
        
        // Edit key modal
        function editKey(key) {
            const keysDB = <?php echo json_encode($keysDB); ?>;
            const keyData = keysDB[key];
            
            if (keyData) {
                document.getElementById('editOldName').value = key;
                document.getElementById('editName').value = key;
                document.getElementById('editNote').value = keyData.note || '';
                
                // Format datetime for input
                const expiryDate = new Date(keyData.expiry);
                const formattedExpiry = expiryDate.toISOString().slice(0, 16);
                document.getElementById('editExpiry').value = formattedExpiry;
                
                document.getElementById('editMax').value = keyData.max_devices;
                document.getElementById('editHwids').value = keyData.hwids ? keyData.hwids.join(', ') : '';
                
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            }
        }
        
        // Delete key confirmation
        function deleteKey(key) {
            if (confirm(`هل أنت متأكد من حذف المفتاح "${key}"؟\n\nهذا الإجراء لا يمكن التراجع عنه!`)) {
                const csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';
                window.location.href = `?del=${encodeURIComponent(key)}&csrf=${csrfToken}`;
            }
        }
        
        // Show toast notification
        function showToast(title, message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }
        
        // Show system info
        function showSystemInfo() {
            const modal = new bootstrap.Modal(document.getElementById('systemModal'));
            modal.show();
        }
        
        // Show logs
        function showLogs() {
            showToast('السجلات', 'سيتم إضافة هذه الميزة قريباً', 'info');
        }
        
        // Show help
        function showHelp() {
            alert('HEX BLADE - نظام إدارة التراخيص\n\n' +
                  '1. إنشاء مفتاح: استخدم النموذج لإنشاء مفتاح جديد\n' +
                  '2. عرض المفاتيح: جميع المفاتيح معروضة في بطاقات ملونة\n' +
                  '3. نسخ المفتاح: اضغط على زر النسخ في البطاقة لنسخ المفتاح\n' +
                  '4. تعديل المفتاح: اضغط على زر التعديل لتغيير إعدادات المفتاح\n' +
                  '5. حذف المفتاح: اضغط على زر الحذف لإزالة المفتاح\n\n' +
                  'ألوان البطاقات:\n' +
                  '• أخضر: مفتاح نشط\n' +
                  '• أصفر: مفتاح قريب من الانتهاء\n' +
                  '• أحمر: مفتاح منتهي\n\n' +
                  'كلمة المرور: h0azaz');
        }
        
        // Add click effect to key cards
        document.addEventListener('DOMContentLoaded', function() {
            const keyCards = document.querySelectorAll('.key-card');
            
            keyCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't trigger if clicking on action buttons
                    if (!e.target.closest('.key-actions')) {
                        this.style.transform = 'scale(0.98)';
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 150);
                    }
                });
            });
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + F to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    document.querySelector('input[name="key_name"]').focus();
                }
                
                // Ctrl/Cmd + N to create new key
                if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                    e.preventDefault();
                    document.querySelector('button[name="save_key"]').click();
                }
            });
        });
        
        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Auto-renew session every 30 minutes
        setInterval(function() {
            fetch('hm.php?ping=1', { 
                method: 'GET',
                cache: 'no-cache'
            }).catch(() => {});
        }, 30 * 60 * 1000);
    </script>
</body>
</html>