<?php
/**
 * API للتحقق من المفتاح وعرض تفاصيله (check.php)
  * المسار: /flu/check.php
   */

   // إعداد الترويسات لتكون JSON
   header('Content-Type: application/json; charset=UTF-8');

   // مسار ملف قاعدة البيانات (استخدام ../ للرجوع للمجلد الرئيسي حيث يوجد الملف)
   $json_file = __DIR__ . '/rjsjsjskakakjfjrjwkakakzkfjf82owoqkakr902q00wosksofktkr.json';

   // قراءة المفتاح من الرابط (GET Request)
   $keyInput = $_GET['key'] ?? '';

   // دالة لجلب البيانات من الـ JSON
   function get_data($file) {
       if (!file_exists($file)) return [];
           return json_decode(file_get_contents($file), true) ?: [];
           }

           $all_keys = get_data($json_file);

           // ==========================================
           // 1. حالة الفشل (المفتاح غير موجود أو فارغ)
           // ==========================================
           if (empty($keyInput) || !isset($all_keys[$keyInput])) {
               http_response_code(401); // إرسال كود الخطأ 401 Unauthorized
                   echo json_encode([
                           "status" => "error",
                                   "message" => "FUCK YOU"
                                       ], JSON_PRETTY_PRINT);
                                           exit;
                                           }

                                           // ==========================================
                                           // 2. حالة النجاح (تجهيز البيانات)
                                           // ==========================================
                                           $key_data = $all_keys[$keyInput];
                                           $now = time();

                                           // حساب تواريخ المفتاح
                                           $expiry_date = $key_data['expiry_date'] ?? 0;
                                           $created_at = $key_data['created_at'] ?? $now;

                                           // حساب الوقت المتبقي
                                           $total_seconds = max(0, $expiry_date - $now);
                                           $days = floor($total_seconds / 86400);
                                           $hours = floor(($total_seconds % 86400) / 3600);
                                           $minutes = floor(($total_seconds % 3600) / 60);
                                           $seconds = $total_seconds % 60;
                                           $formatted_time = "{$days}d {$hours}h {$minutes}m {$seconds}s";

                                           // التحقق مما إذا كان المفتاح نشطاً
                                           $is_active = (($key_data['status'] ?? 'active') === 'active') && ($expiry_date > $now);

                                           // تجهيز قائمة الأجهزة (HWIDs)
                                           $uids_str = $key_data['registered_uids'] ?? '';
                                           // تحويل النص الذي يحتوي على الأجهزة المفصولة بفاصلة إلى مصفوفة
                                           $uids_array = array_filter(explode(',', $uids_str)); 

                                           $hwids_list = [];
                                           $id_counter = 1;
                                           $created_iso = date('Y-m-d\TH:i:s', $created_at);
                                           $ip_address = $_SERVER['REMOTE_ADDR'] ?? "127.0.0.1"; // أخذ الآي بي الفعلي للزائر

                                           foreach ($uids_array as $uid) {
                                               $hwids_list[] = [
                                                       "id" => $id_counter++,
                                                               "hwid" => trim($uid),
                                                                       "pc_name" => "Unknown Device",
                                                                               "ip_address" => $ip_address,
                                                                                       "is_active" => true,
                                                                                               "created_at" => $created_iso,
                                                                                                       "last_used" => date('Y-m-d\TH:i:s') 
                                                                                                           ];
                                                                                                           }

                                                                                                           // بناء استجابة النجاح النهائية (مطابقة للمثال المطلوب)
                                                                                                           $response = [
                                                                                                               "status" => "success",
                                                                                                                   "license_key" => $keyInput,
                                                                                                                       "app_id" => 2,
                                                                                                                           "subscription" => [
                                                                                                                                   "id" => 20162,
                                                                                                                                           "name" => $key_data['vendedor'] ?? "", // يمكنك تغييرها لنوع الاشتراك لو أردت
                                                                                                                                                   "is_active" => $is_active,
                                                                                                                                                           "is_lifetime" => false,
                                                                                                                                                                   "expires_at" => $expiry_date ? date('Y-m-d\TH:i:s.000000', $expiry_date) : null,
                                                                                                                                                                           "time_remaining" => [
                                                                                                                                                                                       "total_seconds" => $total_seconds,
                                                                                                                                                                                                   "days" => $days,
                                                                                                                                                                                                               "hours" => $hours,
                                                                                                                                                                                                                           "minutes" => $minutes,
                                                                                                                                                                                                                                       "seconds" => $seconds,
                                                                                                                                                                                                                                                   "formatted" => $formatted_time
                                                                                                                                                                                                                                                           ],
                                                                                                                                                                                                                                                                   "created_at" => $created_iso
                                                                                                                                                                                                                                                                       ],
                                                                                                                                                                                                                                                                           "hwid_bindings" => [
                                                                                                                                                                                                                                                                                   "total_hwids" => count($hwids_list),
                                                                                                                                                                                                                                                                                           "hwids" => $hwids_list
                                                                                                                                                                                                                                                                                               ]
                                                                                                                                                                                                                                                                                               ];

                                                                                                                                                                                                                                                                                               // إرسال كود 200 (نجاح)
                                                                                                                                                                                                                                                                                               http_response_code(200);

                                                                                                                                                                                                                                                                                               // طباعة البيانات بصيغة JSON
                                                                                                                                                                                                                                                                                               echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                                                                                                                                                                                                                                                                               exit;
                                                                                                                                                                                                                                                                                               ?>
                                                                                                                                                                                                                                                                                               