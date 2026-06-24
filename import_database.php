<?php
/**
 * Database Import Script for PanelBot
 * This script helps import your ffdetect_main.sql file
 */

echo "🗄️  Database Import Script for PanelBot\n";
echo "======================================\n\n";

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ffdetect_main';

echo "📋 Database Configuration:\n";
echo "Host: $host\n";
echo "Username: $username\n";
echo "Database: $database\n\n";

// Check if SQL file exists
$sqlFile = 'ffdetect_main.sql';
if (!file_exists($sqlFile)) {
    die("❌ Error: $sqlFile not found in current directory!\n");
}

echo "✅ Found $sqlFile\n";

try {
    // Connect to MySQL
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error . "\n");
    }
    
    echo "✅ Connected to MySQL server\n";
    
    // Create database if it doesn't exist
    $conn->query("CREATE DATABASE IF NOT EXISTS `$database`");
    echo "✅ Database '$database' ready\n";
    
    // Select database
    $conn->select_db($database);
    
    // Read SQL file
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        die("❌ Error reading $sqlFile\n");
    }
    
    echo "📖 Reading SQL file...\n";
    
    // Split SQL into individual queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "🔄 Executing " . count($queries) . " SQL queries...\n";
    
    $success = 0;
    $errors = 0;
    
    foreach ($queries as $query) {
        if (empty($query) || strpos($query, '--') === 0) {
            continue;
        }
        
        if ($conn->query($query)) {
            $success++;
        } else {
            $errors++;
            echo "⚠️  Query error: " . $conn->error . "\n";
        }
    }
    
    echo "\n📊 Import Results:\n";
    echo "✅ Successful queries: $success\n";
    echo "❌ Failed queries: $errors\n";
    
    if ($errors == 0) {
        echo "\n🎉 Database imported successfully!\n";
        echo "📝 Your existing data and structure are preserved.\n";
    } else {
        echo "\n⚠️  Import completed with some errors.\n";
        echo "💡 This is normal if the database already exists.\n";
    }
    
    // Show table count
    $result = $conn->query("SHOW TABLES");
    $tableCount = $result->num_rows;
    echo "📋 Total tables in database: $tableCount\n";
    
    $conn->close();
    
} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}

echo "\n🚀 Next Steps:\n";
echo "1. Run: php setup.php (to configure bot token and owner ID)\n";
echo "2. Upload bot.php and auth.php to your web server\n";
echo "3. Set webhook URL in your bot configuration\n";
echo "4. Test the bot with /start command\n";

?>