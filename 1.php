<?php
// ==========================================================
// API FOR LONELY VIP - DATABASE KEY VERIFICATION
// ==========================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// ==========================================================
// إعدادات قاعدة البيانات
// ==========================================================
$db_host = "localhost";
$db_user = "sxladoro_sannso";
$db_pass = "sxladoro_sannso";
$db_name = "sxladoro_sannso";

// ==========================================================
// قراءة البيانات (POST أو GET)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registrar = $_POST['registrar'] ?? '';
    $cuser = $_POST['cuser'] ?? '';
    $csenha = $_POST['csenha'] ?? '';
    $ckey = $_POST['ckey'] ?? '';
    $cuid = $_POST['cuid'] ?? '';
} else {
    $registrar = $_GET['registrar'] ?? '';
    $cuser = $_GET['cuser'] ?? '';
    $csenha = $_GET['csenha'] ?? '';
    $ckey = $_GET['ckey'] ?? '';
    $cuid = $_GET['cuid'] ?? '';
}

// ==========================================================
// التحقق من registrar
// ==========================================================
if ($registrar !== 'ClientLogar') {
    echo json_encode(["status" => "falha", "mensagem" => "Invalid registrar"]);
    exit;
}

if (empty($cuser) || empty($csenha) || empty($cuid)) {
    echo json_encode(["status" => "falha", "mensagem" => "Missing parameters"]);
    exit;
}

// ==========================================================
// الاتصال بقاعدة البيانات
// ==========================================================
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    echo json_encode(["status" => "falha", "mensagem" => "Database connection error"]);
    exit;
}

// ==========================================================
// البحث عن المفتاح (cuser) في جدول keys_code
// ==========================================================
$sql = "SELECT id_keys, status, duration, expired_date, max_devices, devices 
        FROM keys_code WHERE user_key = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $cuser);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

// إذا لم يجد المفتاح
if (mysqli_stmt_num_rows($stmt) == 0) {
    echo json_encode(["status" => "falha", "mensagem" => "Key Not Found"]);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_result($stmt, $id_keys, $status, $duration, $expired_date, $max_devices, $devices);
mysqli_stmt_fetch($stmt);

// ==========================================================
// التحقق من حالة المفتاح (banned)
// ==========================================================
if ($status != 1) {
    echo json_encode(["status" => "falha", "mensagem" => "Key is Banned"]);
    mysqli_close($conn);
    exit;
}

// ==========================================================
// التحقق من صلاحية المفتاح (تاريخ الانتهاء)
// ==========================================================
$current_time = time();

if (empty($expired_date) || strpos($expired_date, '0000') !== false) {
    // أول تفعيل: حساب تاريخ الانتهاء
    $new_expiry = date('Y-m-d H:i:s', strtotime("+$duration hours"));
    $update_sql = "UPDATE keys_code SET expired_date = '$new_expiry' WHERE id_keys = $id_keys";
    mysqli_query($conn, $update_sql);
    $expired_date = $new_expiry;
}

$exp_ts = strtotime($expired_date);
if ($current_time > $exp_ts) {
    echo json_encode(["status" => "falha", "mensagem" => "Key Expired"]);
    mysqli_close($conn);
    exit;
}

// ==========================================================
// التحقق من عدد الأجهزة (Device Limit by HWID = cuid)
// ==========================================================
$dev_list = array_filter(explode(",", (string)$devices));
$max_dev = (int)$max_devices;

if (!in_array($cuid, $dev_list)) {
    if (count($dev_list) < $max_dev) {
        // إضافة الجهاز الجديد
        $dev_list[] = $cuid;
        $new_dev_str = implode(",", $dev_list);
        $update_sql = "UPDATE keys_code SET devices = '$new_dev_str' WHERE id_keys = $id_keys";
        mysqli_query($conn, $update_sql);
    } else {
        echo json_encode(["status" => "falha", "mensagem" => "Device Limit Reached"]);
        mysqli_close($conn);
        exit;
    }
}

// ==========================================================
// ✅ النجاح: إرجاع نفس الرد الذي يتوقعه التطبيق
// ==========================================================
$response = [
    "status" => "sucesso",
    "mensagem" => "Acesso concedido!",
    "expira" => $expired_date,
    "idapp" => "1",
    "versao" => "AimbotMobile",
    "versaoFF" => "FFmax_x86",
    "IsEmulador" => "0"
];

echo json_encode($response);

mysqli_close($conn);
?>