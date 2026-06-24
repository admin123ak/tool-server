<?php
// Disable error reporting for production (clean JSON output)
error_reporting(0);
header("Content-Type: application/json");

// ================= DATABASE CONFIG =================
$DB_HOST = 'localhost';
$DB_NAME = 'ffdetect_main';
$DB_USER = 'ffdetect_main';
$DB_PASS = 'ffdetect_main';
// ===================================================

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode(['status' => false, 'reason' => 'DB Connection Failed']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => false, 'reason' => 'Invalid Request']));
}

$game = $_POST['game'] ?? '';
$uKey = $_POST['user_key'] ?? '';
$sDev = $_POST['serial'] ?? ''; // This is the ID sent by the app

if (!$game || !$uKey || !$sDev) {
    echo json_encode(['status' => false, 'reason' => 'Missing Parameters']);
    exit;
}

try {
    // 1. Get Key Info
    $stmt = $pdo->prepare("SELECT * FROM keys_code WHERE user_key = ? AND game = ? LIMIT 1");
    $stmt->execute([$uKey, $game]);
    $keyData = $stmt->fetch();

    if (!$keyData) {
        echo json_encode(['status' => false, 'reason' => 'Invalid Key or Wrong Game']);
        exit;
    }

    if ($keyData['status'] != 1) {
        echo json_encode(['status' => false, 'reason' => 'Key Banned']);
        exit;
    }

    // 2. Load Key Settings
    $id_keys = $keyData['id_keys'];
    $duration = $keyData['duration'];
    $expired = $keyData['expired_date'];
    $max_dev = $keyData['max_devices'];
    $devices_str = $keyData['devices'];
    
    // Convert DB string "ID1,ID2" into array ["ID1", "ID2"]
    $currentDevices = array_filter(explode(",", $devices_str));
    
    // 3. DEVICE CHECK LOGIC (THE FIX IS HERE)
    $isDeviceRegistered = in_array($sDev, $currentDevices);
    $updateNeeded = false;

    // --- [START FIX] Smart ID Bridging ---
    if (!$isDeviceRegistered) {
        // If the ID is NOT in the database, check if it's just the Menu ID (64 chars)
        // trying to access a key that already has a Login ID (32 chars).
        
        $hasLoginId = false;
        foreach ($currentDevices as $savedDevice) {
            if (strlen($savedDevice) === 32) {
                $hasLoginId = true;
                break;
            }
        }

        // Rule: If DB has a Login ID (32 chars) AND incoming is Menu ID (64 chars), allow it!
        if ($hasLoginId && strlen($sDev) === 64) {
            $isDeviceRegistered = true; 
        }
    }
    // --- [END FIX] ---

    // 4. Device Slot Management (Only runs if not "Bridged" above)
    if (!$isDeviceRegistered) {
        if (count($currentDevices) < $max_dev) {
            // Add new device
            $currentDevices[] = $sDev;
            $devices_str = implode(",", $currentDevices);
            $updateNeeded = true;
        } else {
            echo json_encode(['status' => false, 'reason' => 'Max Devices Reached']);
            exit;
        }
    }

    // 5. Expiry Management
    $now = new DateTime();
    $finalExpiry = $expired;

    if (!$expired || $expired == '0000-00-00 00:00:00') {
        // First activation
        $expDate = new DateTime();
        $expDate->modify("+{$duration} hours"); // Or "days" if your duration is in days
        $finalExpiry = $expDate->format('Y-m-d H:i:s');
        $updateNeeded = true;
        
        // IMPORTANT: Update SQL needs to save the new expiry
        $upStmt = $pdo->prepare("UPDATE keys_code SET expired_date = ? WHERE id_keys = ?");
        $upStmt->execute([$finalExpiry, $id_keys]);
    } else {
        // Check if expired
        $expDate = new DateTime($expired);
        if ($now > $expDate) {
            echo json_encode(['status' => false, 'reason' => 'Key Expired']);
            exit;
        }
    }

    // 6. Save Updates (New Device or Expiry)
    if ($updateNeeded) {
        $upStmt = $pdo->prepare("UPDATE keys_code SET devices = ?, expired_date = ? WHERE id_keys = ?");
        $upStmt->execute([$devices_str, $finalExpiry, $id_keys]);
    }

    // 7. Success Response
    echo json_encode([
        'status' => true,
        'expiry' => $finalExpiry
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => false, 'reason' => 'Server Error']);
}
?>