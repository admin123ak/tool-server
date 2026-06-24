<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

include 'init.php';

$privatekey = <<<EOD
-----BEGIN PRIVATE KEY-----
MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBALIKibCXZ+X62xve
Xp802PIpT3U6fF1qf8fNniw8R19oic3/Bb2NArLOUq2q0EImlFRYd5ORQzx1MWE9
uV988m31iP3LNSwlvmGGjh7NTUpIHE8Pclbmc6m3A1CK7wciCZ2WBp96fkJ0mLSP
rr8qa4NChVu+ngyyfiziivn6614fAgMBAAECgYAq0Nbw9VBCodOsfYsSzWI4xk95
+SEsU+67zYpyx+JAIwM021X1kiIqfuyqIBBqQB3etNG41q+tK9++q1nXiVRBT0g7
iCnK3V1fQuIJeqJuJL6/gjCbtGjnppnd7nyhsKbH6lc5IywpstV4W89lKQ91+hzF
DfBNAoNOpdiYmbXfPQJBAOft8pVReLO07dKzvmvp/4ogBAs5GbYr4JCe+Lo6M/on
c2ZLq0EhFYl0GVw4oHoefcsmKtNVk4X0rQn4evo1MO0CQQDEhNm+ZDuC/axzsicQ
T8uxjZuwZiMDMh/hT0JHe7gdT0TU0sRM5ktE1bn4Fq8M4G1fWjOlAFrJPNZYqWRF
DAW7AkAFQVoG0iLHB6l/5bzB2zqEGbedvXx8qT/cZw3BwoHdADnYLozB3AsN40iT
02Cng7tb+BBuW3kNRv8Iw82dj8j9AkBzTIrAZOhxs0nh242FyXt2IeJNfa9fKaKA
u9LkQ9dDAwYcY8ieYaOZsFfbEdwEjww8nJyeW3XoidQs0r2ssSAPAkAN9cJjr7+N
nbtplERb30DephywpQmN5veQel8dOiA6g0neWKaSmWFOZhT2dOwdOhSYMJcqIOfH
VGpNKpLbW/PK
-----END PRIVATE KEY-----
EOD;

// استخدمنا كلاس Utils من ملف dogin-apk.php لتوحيد عملية التشفير وفك التشفير
class Utils {
    public static function toBase64($data) { return base64_encode($data); }
    public static function fromBase64($data) { return base64_decode($data); }
    public static function toJson($data) { return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); }
    public static function fromJson($data) { return json_decode($data, true); }
    public static function sha256($data) { return strtoupper(hash('sha256', $data)); }
    
    public static function profileEncrypt($data, $hash) {
        $out = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $out .= $data[$i] ^ $hash[$i % strlen($hash)];
        }
        return self::toBase64($out);
    }

    // استبدال الاعتماد على $crypter بدالة أصلية
    public static function decryptRSA($enc_data_b64, $private_key) {
        $data = base64_decode($enc_data_b64);
        if (openssl_private_decrypt($data, $decrypted, $private_key, OPENSSL_PKCS1_PADDING)) {
            return $decrypted;
        }
        return false;
    }

    public static function signRSA($data, $private_key) {
        if (openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256)) {
            return base64_encode($signature);
        }
        return "";
    }
}

function failResponse($msg) {
    die(buildResponse([
        "Status"        => "Failed",
        "MessageString" => $msg,
        "Username"      => "",
        "SubscriptionLeft" => "",
        "Validade"      => "",
        "Vendedor"      => "",
        "RegisterDate"  => "",
        "Dias"          => ""
    ]));
}

function buildResponse($array) {
    global $privatekey;
    
    $json = Utils::toJson($array);
    $hash = Utils::sha256($json);
    $enc  = Utils::profileEncrypt($json, $hash);
    $sign = Utils::signRSA($json, $privatekey);

    $final = [
        "Data" => $enc,
        "Sign" => $sign,
        "Hash" => $hash
    ];

    return Utils::toBase64(Utils::toJson($final));
}

if (!isset($_POST['token']) && !isset($_POST['tokserver_hk'])) {
    failResponse("Invalid request");
}

$tokenParam = isset($_POST['token']) ? $_POST['token'] : $_POST['tokserver_hk'];
$tokenRaw = Utils::fromBase64($tokenParam);
$tokArr   = Utils::fromJson($tokenRaw);

if (!is_array($tokArr)) failResponse("Invalid token");

if (isset($tokArr['Data'], $tokArr['Hash'])) {
    $encData = $tokArr['Data'];
    $hashCli = $tokArr['Hash'];
} elseif (isset($tokArr['Dados_hk'], $tokArr['Hash_hk'])) {
    $encData = $tokArr['Dados_hk'];
    $hashCli = $tokArr['Hash_hk'];
} else {
    failResponse("Invalid token format");
}

// فك التشفير بالطريقة المأخوذة من dogin-apk.php بدلاً من $crypter
$plain = Utils::decryptRSA($encData, $privatekey);

if ($plain === false) failResponse("Decrypt failed");

if (Utils::sha256($plain) !== strtoupper($hashCli)) failResponse("Hash invalido");

$data = Utils::fromJson($plain);

if (!is_array($data)) failResponse("Dados invalidos");

if (!empty($maintenance)) {
    failResponse("Servidor em manutencao");
}

$uname = $data['uname'] ?? null;
if ($uname == null || preg_match("([a-zA-Z0-9]+)", $uname) === 0) {
    failResponse("Usuario invalido.");
}

$cs = $data['cs'] ?? null;
if (!$cs) failResponse("Dados invalidos");

if (!isset($con) || !is_object($con) || !method_exists($con, 'real_escape_string')) {
    if (isset($conn) && is_object($conn) && method_exists($conn, 'real_escape_string')) {
        $con = $conn;
    } else {
        failResponse("DB connection missing");
    }
}

$uEsc = $con->real_escape_string($uname);
$csEsc = $con->real_escape_string($cs);
$where = "WHERE `user_key` = '$uEsc' AND `game` = 'phonk'";

$q = $con->query("SELECT * FROM `keys_code` $where");
if ($q === false) {
    failResponse("DB query failed");
}
$user = $q->fetch_assoc();
if (!is_array($user)) {
    failResponse("Usuario nao encontrado.");
}

// === ACTIVACIÓN AUTOMÁTICA EN PRIMER LOGIN ===
$justActivated = false;
if (empty($user['expired_date'])) {
    $dias = (int)$user['duration'];
    if ($dias <= 0) failResponse("Licenca invalida.");

    $start = date("Y-m-d H:i:s");
    $end   = date("Y-m-d H:i:s", strtotime("+{$dias} hours"));

    $updateQuery = "
        UPDATE `keys_code`
        SET created_at = '$start',
            expired_date = '$end',
            updated_at = '$start',
            status = 1
        $where
    ";
    
    $result = $con->query($updateQuery);
    if ($result === false) {
        failResponse("Falha ao ativar conta: " . $con->error);
    }

    $user['created_at'] = $start;
    $user['expired_date'] = $end;
    $user['status'] = 1;
    $justActivated = true;
}

// === DEVICE MANAGEMENT ===
$maxDevices = (int)($user['max_devices'] ?? 1);
$currentDevices = !empty($user['devices']) ? json_decode($user['devices'], true) : [];
if (!is_array($currentDevices)) $currentDevices = [];

if (!in_array($cs, $currentDevices)) {
    if (count($currentDevices) >= $maxDevices) {
        failResponse("Limite de dispositivos atingido.");
    }
    $currentDevices[] = $cs;
    $devicesJson = json_encode($currentDevices, JSON_UNESCAPED_UNICODE);
    $devicesEsc = $con->real_escape_string($devicesJson);
    $con->query("UPDATE `keys_code` SET `devices` = '$devicesEsc', `updated_at` = NOW() $where");
    $user['devices'] = $devicesJson;
}

// === VALIDACIÓN DE EXPIRACIÓN ===
$endDate = $user['expired_date'] ?? null;

if (!$justActivated && isset($user['status']) && (int)$user['status'] === 0) {
    failResponse("Usuario inativo ou banido.");
}

if (!empty($endDate)) {
    $endTs = strtotime($endDate);
    $nowTs = time();
    
    if ($endTs <= $nowTs) {
        $con->query("UPDATE `keys_code` SET `status` = 0, `updated_at` = NOW() $where");
        failResponse("Sua licenca expirou.");
    }
} elseif (!$justActivated) {
    failResponse("Data de expiracao nao definida.");
}

if (empty($endDate)) {
    $endDate = $user['expired_date'] ?? date("Y-m-d H:i:s");
}

$endTs = strtotime($endDate);
$nowTs = time();
$daysLeft = (int)round(($endTs - $nowTs) / 86400, 0);
if ($daysLeft < 0) {
    $daysLeft = 0;
}

if ($daysLeft === 0) {
    failResponse("Your Token is Expired");
}

$response = [
    "Status"           => "Success",
    "MessageString"    => "",
    "Usuario"          => $user['user_key'] ?? '',
    "Username"         => $user['user_key'] ?? '',
    "SubscriptionLeft" => $endDate,
    "Validade"         => $endDate,
    "Vendedor"         => $user['registrator'] ?? 'System',
    "RegisterDate"     => $user['created_at'] ?? date("Y-m-d H:i:s"),
    "Dias"             => "$daysLeft dias restantes"
];

if (isset($data['load']) && (int)$data['load'] === 1) {
    $loaderPath = "Loaders/PUBG.kmods";
    $loaderData = "";
    
    if (function_exists('isFileExist') && function_exists('readFileData')) {
        $loaderData = isFileExist($loaderPath) ? readFileData($loaderPath) : "";
    } elseif (file_exists($loaderPath)) {
        $loaderData = file_get_contents($loaderPath);
    }
    
    $response = [
        "Status"           => "Success",
        "MessageString"    => "",
        "Loader"           => Utils::toBase64($loaderData),
        "SubscriptionLeft" => $endDate,
        "Val"              => $endDate,
        "User"             => $user['user_key'] ?? '',
        "Vendedor"         => $user['registrator'] ?? 'System',
        "Dias"             => "Voce tem $daysLeft dias restantes",
        "Version"          => "1.2"
    ];
}

echo buildResponse($response);
?>
