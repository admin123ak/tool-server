<?php

namespace App\Controllers;

use App\Models\KeysModel;

// ================= CONFIG LOG =================
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

function writeLog($message) {
    $time = date('Y-m-d H:i:s');
    error_log("[$time] $message");
}

// Helper function for reduce_multiples
if (!function_exists('reduce_multiples')) {
    function reduce_multiples($string, $separator = ',', $unique = true) {
        $parts = explode($separator, $string);
        if ($unique) {
            $parts = array_unique($parts);
        }
        $parts = array_filter($parts, function($value) {
            return $value !== '';
        });
        return implode($separator, $parts);
    }
}

class Connect extends BaseController
{
    protected $model, $game, $uKey, $sDev;
    protected $FIXED_TOKEN = "Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9EVm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E";

    public function __construct()
    {
        include('conn.php');
//=================================================
        $sql1 ="select * from onoff where id=1";
        $result1 = mysqli_query($conn, $sql1);
        $userDetails1 = mysqli_fetch_assoc($result1);
//=================================================
        $this->model = new KeysModel();
//=================================================
        if($userDetails1['status'] == 'on')
        {
            $this->maintenance = true;
        } else {
            $this->maintenance = false;
        }
//=================================================
       $this->staticWords = "Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E";
    }

    public function index()
    {
        // ================= DETECT BROWSER =================
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $isBrowser = preg_match('/(Mozilla|Chrome|Safari|Firefox|Edge|Opera)/i', $userAgent);

        if ($isBrowser && !isset($_SERVER['HTTP_X_API_CLIENT'])) {
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html>
            <html>
            <head>
                <title>Trinity X</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body {
                        background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
                        font-family: "Segoe UI", Arial, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .container {
                        text-align: center;
                        color: white;
                        padding: 40px;
                        border-radius: 20px;
                        background: rgba(255,255,255,0.1);
                        backdrop-filter: blur(10px);
                    }
                    h1 {
                        font-size: 48px;
                        text-shadow: 0 0 20px rgba(0,255,255,0.5);
                        margin-bottom: 20px;
                    }
                    p {
                        font-size: 18px;
                        margin-bottom: 30px;
                        opacity: 0.8;
                    }
                    button {
                        background: linear-gradient(90deg, #00c6ff, #0072ff);
                        border: none;
                        padding: 15px 40px;
                        font-size: 20px;
                        color: white;
                        border-radius: 50px;
                        cursor: pointer;
                        margin-top: 20px;
                        transition: transform 0.3s;
                    }
                    button:hover {
                        transform: scale(1.05);
                    }
                    a {
                        color: #00c6ff;
                        text-decoration: none;
                        display: inline-block;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>TRINITY X</h1>
                    <p>Jfjfj</p>
                    <button onclick="alert(\'Coming Soon!\')">ACTIVATE →</button>
                    <br>
                    <a href="#">Join Community</a>
                </div>
            </body>
            </html>';
            exit;
        }

        header('Content-Type: application/json; charset=utf-8');
        header("Cache-Control: no-store, no-cache, must-revalidate");

        $rawBody = $this->request->getBody();
        if (!empty($rawBody) || $this->request->getPost() || $this->request->getJSON()) {
            return $this->index_post();
        } else {
            return $this->response->setJSON([
                "status" => false,
                "message" => "No data provided"
            ]);
        }
    }

    private function returnError($message, $status = false) {
        $responseData = [
            "status" => $status,
            "message" => $message
        ];
        
        // Support both encrypted and plain text responses
        $clientHeader = $this->request->getHeaderLine('X-API-Client');
        if ($clientHeader === 'NativeApp') {
            $rc4Key = "K9mXp2Lq7Wn4Yt5R";
            $errorJson = json_encode($responseData);
            $rc4Encrypted = $this->rc4($errorJson, $rc4Key);
            $finalResponse = base64_encode($rc4Encrypted);
            return $this->response
                ->setContentType('text/plain')
                ->setBody($finalResponse);
        } else {
            return $this->response->setJSON($responseData);
        }
    }

    public function index_post()
    {
        $isMT = $this->maintenance;
        
        // Get raw input
        $rawInput = $this->request->getBody();
        
        // Check if this is a legacy POST request (from original app)
        $game = $this->request->getPost('game');
        $uKey = $this->request->getPost('user_key');
        $sDev = $this->request->getPost('serial');
        
        // If legacy POST parameters exist, use the old flow
        if ($game && $uKey && $sDev) {
            return $this->handleLegacyRequest($game, $uKey, $sDev, $isMT);
        }
        
        // Otherwise, handle encrypted request
        if (empty($rawInput)) {
            return $this->returnError("No data provided");
        }
        
        // Try to decode base64
        $decodedInput = base64_decode($rawInput);
        if (!$decodedInput) {
            // If not base64, maybe it's plain JSON
            $input = json_decode($rawInput, true);
            if ($input && isset($input['game'])) {
                return $this->handleLegacyRequest($input['game'], $input['user_key'], $input['serial'], $isMT);
            }
            return $this->returnError("Invalid data format");
        }
        
        // Try RC4 decryption
        $rc4Key = "K9mXp2Lq7Wn4Yt5R";
        $rc4Decrypted = $this->rc4($decodedInput, $rc4Key);
        
        if (empty($rc4Decrypted)) {
            return $this->returnError("Decryption failed");
        }
        
        $input = json_decode($rc4Decrypted, true);
        
        if (!$input) {
            return $this->returnError("Invalid JSON format");
        }

        $clientHeader = $this->request->getHeaderLine('X-API-Client');
        
        // Check for encrypted wrapper format
        if (isset($input['x'], $input['y'], $input['z'], $input['s'])) {
            $encryptedData = $input['x'];
            $timestamp = $input['y'];
            $nonce = $input['z'];
            $mac = $input['s'];

            $sessionKey = $this->generateSessionKey($this->FIXED_TOKEN, $timestamp);

            if (abs(time() - intval($timestamp)) > 300) {
                return $this->returnError("Request expired");
            }

            $expectedMac = $this->hmacBase64($sessionKey, $encryptedData . "|" . $timestamp . "|" . $nonce);
            if (!hash_equals($expectedMac, $mac)) {
                return $this->returnError("Invalid MAC");
            }

            $decryptedJson = $this->xxtea_decrypt($encryptedData, $sessionKey);
            $requestData = json_decode($decryptedJson, true);
            if (!$requestData) {
                return $this->returnError("Decryption failed");
            }

            $game = $requestData['game'] ?? null;
            $uKey = $requestData['key'] ?? null;
            $sDev = $requestData['hwid'] ?? null;
            $publicKey = $requestData['publicKey'] ?? null;

            if ($publicKey !== $this->FIXED_TOKEN) {
                return $this->returnError("Invalid public key");
            }
            
            return $this->processRequest($game, $uKey, $sDev, $isMT, $timestamp, $nonce, $sessionKey);
        } 
        // Check for direct parameters
        else if (isset($input['game']) && isset($input['user_key']) && isset($input['serial'])) {
            return $this->handleLegacyRequest($input['game'], $input['user_key'], $input['serial'], $isMT);
        }
        else {
            return $this->returnError("Invalid request format");
        }
    }
    
    private function handleLegacyRequest($game, $uKey, $sDev, $isMT)
    {
        $form_rules = [
            'game' => 'required|alpha_dash',
            'user_key' => 'required|min_length[1]|max_length[36]',
            'serial' => 'required|alpha_dash'
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($form_rules);
        
        $data = ['game' => $game, 'user_key' => $uKey, 'serial' => $sDev];
        
        if (!$validation->run($data)) {
            return $this->response->setJSON([
                'status' => false,
                'reason' => "Bad Parameter",
            ]);
        }

        if ($isMT) {
            include('conn.php');
            $sql1 ="select * from onoff where id=1";
            $result1 = mysqli_query($conn, $sql1);
            $userDetails1 = mysqli_fetch_assoc($result1);
            
            return $this->response->setJSON([
                'status' => true,
                'reason' => $userDetails1['myinput']
            ]);
        } else {
            $time = new \CodeIgniter\I18n\Time;
            $model = $this->model;
            $findKey = $model->getKeysGame(['user_key' => $uKey, 'game' => $game]);

            if ($findKey) {
                if ($findKey->status != 1) {
                    return $this->response->setJSON([
                        'status' => false,
                        'reason' => 'USER BLOCKED'
                    ]);
                } else {
                    $id_keys = $findKey->id_keys;
                    $duration = $findKey->duration;
                    $expired = $findKey->expired_date;
                    $max_dev = $findKey->max_devices;
                    $devices = $findKey->devices;

                    if (!$expired) {
                        $setExpired = $time::now()->addHours($duration);
                        $model->update($id_keys, ['expired_date' => $setExpired]);
                        $status = true;
                    } else {
                        if ($time::now()->isBefore($expired)) {
                            $status = true;
                        } else {
                            return $this->response->setJSON([
                                'status' => false,
                                'reason' => 'EXPIRED KEY'
                            ]);
                        }
                    }

                    if ($status) {
                        include('conn.php');
    
                        $sql2 ="select * from modname where id=1";
                        $result2 = mysqli_query($conn, $sql2);
                        $userDetails2 = mysqli_fetch_assoc($result2);
                        
                        $sql3 ="select * from _ftext where id=1";
                        $result3 = mysqli_query($conn, $sql3);
                        $userDetails3 = mysqli_fetch_assoc($result3);
                        
                        $sql4 = "SELECT expired_date FROM keys_code WHERE user_key='$uKey'";
                        $result4 = mysqli_query($conn, $sql4);
                        $userDetails4 = mysqli_fetch_assoc($result4);
                        
                        $sql = "SELECT * FROM Feature WHERE id=1";
                        $result = mysqli_query($conn, $sql);
                        $ModFeatureStatus = mysqli_fetch_assoc($result);
                        
                        $rngcnt = $time->getTimestamp();
                        
                        $devicesAdd = $this->checkDevicesAdd($sDev, $devices, $max_dev);
                        if ($devicesAdd !== false) {
                            if (is_array($devicesAdd)) {
                                $model->update($id_keys, $devicesAdd);
                            }
                            
                            $real = "$game-$uKey-$sDev-" . $this->staticWords;
                            $expiry = $findKey->expired_date;
                            if ($expiry == null) {
                                $expiry = $time::now()->addDays($duration);
                            }
                            
                            return $this->response->setJSON([
                                'status' => true,
                                'data' => [
                                    'real' => $real,
                                    'token' => md5($real),
                                    'modname' => $userDetails2['modname'],
                                    'mod_status' => $userDetails3['_status'],
                                    'credit' => $userDetails3['_ftext'],
                                    'ESP' => $ModFeatureStatus['ESP'],
                                    'Item' => $ModFeatureStatus['Item'],
                                    'AIM' => $ModFeatureStatus['AIM'],
                                    'SilentAim' => $ModFeatureStatus['SilentAim'],
                                    'BulletTrack' => $ModFeatureStatus['BulletTrack'],
                                    'Floating' => $ModFeatureStatus['Floating'],
                                    'Memory' => $ModFeatureStatus['Memory'],
                                    'Setting' => $ModFeatureStatus['Setting'],
                                    'expired_date' => $userDetails4['expired_date'],
                                    'EXP' => $expiry,
                                    'exdate' => $expiry,
                                    'device'=> $max_dev,
                                    'rng' => $rngcnt
                                ],
                            ]);
                        } else {
                            return $this->response->setJSON([
                                'status' => false,
                                'reason' => 'MAX DEVICE REACHED'
                            ]);
                        }
                    }
                }
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'reason' => 'USER OR GAME NOT REGISTERED'
                ]);
            }
        }
    }
    
    private function processRequest($game, $uKey, $sDev, $isMT, $timestamp, $nonce, $sessionKey)
    {
        if (empty($game) || empty($uKey) || empty($sDev)) {
            return $this->returnError("Bad Parameter");
        }
        
        if (strlen($uKey) > 36 || strlen($uKey) < 1) {
            return $this->returnError("Bad Parameter");
        }
        
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $game)) {
            return $this->returnError("Bad Parameter");
        }

        if ($isMT) {
            include('conn.php');
            $sql1 ="select * from onoff where id=1";
            $result1 = mysqli_query($conn, $sql1);
            $userDetails1 = mysqli_fetch_assoc($result1);

            $responseData = [
                'status' => true,
                'message' => $userDetails1['myinput']
            ];
        } else {
            $time = new \CodeIgniter\I18n\Time;
            $model = $this->model;
            $findKey = $model->getKeysGame(['user_key' => $uKey, 'game' => $game]);

            if ($findKey) {
                if ($findKey->status != 1) {
                    $responseData = ['status' => false, 'message' => 'KEY LOCKED'];
                } else {
                    $id_keys = $findKey->id_keys;
                    $duration = $findKey->duration;
                    $expired = $findKey->expired_date;

                    if (!$expired) {
                        $setExpired = $time::now()->addHours($duration);
                        $model->update($id_keys, ['expired_date' => $setExpired]);
                        $responseData['status'] = true;
                    } else {
                        if ($time::now()->isBefore($expired)) {
                            $responseData['status'] = true;
                        } else {
                            $responseData = [
                                'status' => false,
                                'message' => 'KEY EXPIRED'
                            ];
                        }
                    }

                    if (isset($responseData['status']) && $responseData['status']) {
                        include('conn.php');

                        $sql2 ="select * from modname where id=1";
                        $result2 = mysqli_query($conn, $sql2);
                        $userDetails2 = mysqli_fetch_assoc($result2);

                        $sql3 ="select * from _ftext where id=1";
                        $result3 = mysqli_query($conn, $sql3);
                        $userDetails3 = mysqli_fetch_assoc($result3);

                        $sql4 = "SELECT expired_date FROM keys_code WHERE user_key='$uKey'";
                        $result4 = mysqli_query($conn, $sql4);
                        $userDetails4 = mysqli_fetch_assoc($result4);

                        $sql = "SELECT * FROM Feature WHERE id=1";
                        $result = mysqli_query($conn, $sql);
                        $ModFeatureStatus = mysqli_fetch_assoc($result);

                        $rngcnt = $time->getTimestamp();

                        $devices = $findKey->devices ?? '';
                        $max_dev = $findKey->max_devices ?? 1;
                        
                        $devicesAdd = $this->checkDevicesAdd($sDev, $devices, $max_dev);
                        if ($devicesAdd !== false) {
                            if (is_array($devicesAdd)) {
                                $model->update($id_keys, $devicesAdd);
                            }

                            $loaderFile = dirname(dirname(__DIR__)) . "/hwang/liblamdo.so";
                            $binary = @file_get_contents($loaderFile);
                            $base64Load = "";
                            if($binary) $base64Load = base64_encode($binary);

                            $expireDate = $userDetails4['expired_date'] ?? date("Y-m-d H:i:s", strtotime("+1 day"));

                            $sellerName = $findKey->registrator ?? 'Unknown';
                            $sellerType = 'User';
                            $db = \Config\Database::connect();
                            $seller = $db->table('users')->where('username', $sellerName)->get()->getRow();
                            if ($seller) {
                                $levels = [1 => 'Owner', 2 => 'Admin', 3 => 'Reseller'];
                                $sellerType = $levels[$seller->level] ?? 'User';
                            }

                            $responseData = [
                                "status" => true,
                                "expire_date" => $expireDate,
                                "user" => $uKey,
                                "seller_name" => $sellerName,
                                "seller_type" => $sellerType,
                                "message" => "Login Success",
                                "rng" => time()
                            ];
                            
                            if ($base64Load) {
                                $responseData["load"] = ["load_data" => $base64Load];
                            }
                        } else {
                            $responseData = [
                                'status' => false,
                                'message' => 'Maximum devices reached'
                            ];
                        }
                    }
                }
            } else {
                $responseData = [
                    'status' => false,
                    'message' => 'USER OR GAME NOT REGISTERED'
                ];
            }
        }

        $responseData = $responseData ?? [
            "status" => false,
            "message" => "Server error"
        ];

        $jsonResponse = json_encode($responseData);
        $encryptedResponse = $this->xxtea_encrypt($jsonResponse, $sessionKey);

        $responseMac = $this->hmacBase64($sessionKey, $encryptedResponse . "|" . $timestamp . "|" . $nonce);

        $responseWrapper = [
            "x" => $encryptedResponse,
            "y" => $timestamp,
            "z" => $nonce,
            "s" => $responseMac
        ];
        
        $wrapperJson = json_encode($responseWrapper);
        $rc4Key = "K9mXp2Lq7Wn4Yt5R";
        $rc4Encrypted = $this->rc4($wrapperJson, $rc4Key);
        $finalResponse = base64_encode($rc4Encrypted);
        
        return $this->response
            ->setContentType('text/plain')
            ->setBody($finalResponse);
    }

    // ================= XXTEA IMPLEMENTATION =================
    private function long2str($v, $w) {
        $len = count($v);
        $n = ($len - 1) << 2;
        if ($w) {
            $m = $v[$len - 1];
            if (($m < $n - 3) || ($m > $n)) return false;
            $n = $m;
        }
        $s = [];
        for ($i = 0; $i < $len; $i++) {
            $s[$i] = pack("V", $v[$i]);
        }
        if ($w) {
            return substr(implode('', $s), 0, $n);
        } else {
            return implode('', $s);
        }
    }

    private function str2long($s, $w) {
        $v = array_values(unpack("V*", $s . str_repeat("\0", (4 - strlen($s) % 4) & 3)));
        if ($w) {
            $v[] = strlen($s);
        }
        return $v;
    }

    private function int32($n) {
        while ($n >= 2147483648) $n -= 4294967296;
        while ($n <= -2147483649) $n += 4294967296;
        return (int)$n;
    }

    private function xxtea_encrypt($str, $key) {
        if ($str == "") return "";
        $v = $this->str2long($str, true);
        $k = $this->str2long($key, false);
        if (count($k) < 4) {
            for ($i = count($k); $i < 4; $i++) $k[$i] = 0;
        }
        $n = count($v) - 1;
        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52 / ($n + 1));
        $sum = 0;
        while (0 < $q--) {
            $sum = $this->int32($sum + $delta);
            $e = $sum >> 2 & 3;
            for ($p = 0; $p < $n; $p++) {
                $y = $v[$p + 1];
                $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ ($y << 2)) + (($y >> 3 & 0x1fffffff) ^ ($z << 4)) ^ ($this->int32($sum ^ $y) + $this->int32($k[$p & 3 ^ $e] ^ $z)));
                $z = $v[$p] = $this->int32($v[$p] + $mx);
            }
            $y = $v[0];
            $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ ($y << 2)) + (($y >> 3 & 0x1fffffff) ^ ($z << 4)) ^ ($this->int32($sum ^ $y) + $this->int32($k[$n & 3 ^ $e] ^ $z)));
            $z = $v[$n] = $this->int32($v[$n] + $mx);
        }
        return base64_encode($this->long2str($v, false));
    }

    private function xxtea_decrypt($str, $key) {
        if ($str == "") return "";
        $str = base64_decode($str);
        if (!$str) return "";
        $v = $this->str2long($str, false);
        $k = $this->str2long($key, false);
        if (count($k) < 4) {
            for ($i = count($k); $i < 4; $i++) $k[$i] = 0;
        }
        $n = count($v) - 1;
        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52 / ($n + 1));
        $sum = $this->int32($q * $delta);
        while ($sum != 0) {
            $e = $sum >> 2 & 3;
            for ($p = $n; $p > 0; $p--) {
                $z = $v[$p - 1];
                $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ ($y << 2)) + (($y >> 3 & 0x1fffffff) ^ ($z << 4)) ^ ($this->int32($sum ^ $y) + $this->int32($k[$p & 3 ^ $e] ^ $z)));
                $y = $v[$p] = $this->int32($v[$p] - $mx);
            }
            $z = $v[$n];
            $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ ($y << 2)) + (($y >> 3 & 0x1fffffff) ^ ($z << 4)) ^ ($this->int32($sum ^ $y) + $this->int32($k[0 & 3 ^ $e] ^ $z)));
            $y = $v[0] = $this->int32($v[0] - $mx);
            $sum = $this->int32($sum - $delta);
        }
        return $this->long2str($v, true);
    }

    // ================= RC4 IMPLEMENTATION =================
    private function rc4($data, $key) {
        $S = range(0, 255);
        $j = 0;
        $keyLen = strlen($key);
        
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $S[$i] + ord($key[$i % $keyLen])) % 256;
            $temp = $S[$i];
            $S[$i] = $S[$j];
            $S[$j] = $temp;
        }
        
        $i = 0;
        $k = 0;
        $result = '';
        $dataLen = strlen($data);
        
        for ($n = 0; $n < $dataLen; $n++) {
            $i = ($i + 1) % 256;
            $k = ($k + $S[$i]) % 256;
            $temp = $S[$i];
            $S[$i] = $S[$k];
            $S[$k] = $temp;
            $result .= chr(ord($data[$n]) ^ $S[($S[$i] + $S[$k]) % 256]);
        }
        
        return $result;
    }

    private function generateSessionKey($fixed, $ts) {
        $combined = $fixed . $ts;
        $key = "";
        for($i=0; $i<16; $i++) {
            if($i < strlen($combined)) {
                $key .= $combined[$i];
            } else {
                $key .= chr($i * 17);
            }
        }
        return $key;
    }

    private function hmacBase64($key, $data) {
        return base64_encode(hash_hmac('sha256', $data, $key, true));
    }

    private function checkDevicesAdd($currentDevice, $registeredDevices, $maxDevices) {
        if (empty($currentDevice)) {
            return false;
        }

        $deviceArray = array_filter(array_map('trim', explode(',', $registeredDevices)));
        
        if (in_array($currentDevice, $deviceArray)) {
            return true;
        }

        if (count($deviceArray) < $maxDevices) {
            $deviceArray[] = $currentDevice;
            return [
                'devices' => implode(',', $deviceArray)
            ];
        }

        return false;
    }
}