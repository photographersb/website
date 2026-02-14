<?php
// Load Laravel environment
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

echo "=== Database Diagnostic Test ===\n\n";

// Get environment variables
echo "DB Configuration from .env:\n";
echo "DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
echo "DB_HOST: " . env('DB_HOST') . "\n";
echo "DB_PORT: " . env('DB_PORT') . "\n";
echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "DB_USERNAME: " . env('DB_USERNAME') . "\n";
echo "DB_PASSWORD: " . (env('DB_PASSWORD') ? '[SET]' : '[EMPTY]') . "\n\n";

// Try PDO connection
echo "Attempting PDO connection...\n";
try {
    $dsn = 'mysql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT') . ';dbname=' . env('DB_DATABASE');
    $pdo = new PDO($dsn, env('DB_USERNAME'), env('DB_PASSWORD'));
    echo "✓ PDO connection successful\n\n";
    
    // Check tables
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
} catch (PDOException $e) {
    echo "✗ PDO connection failed\n";
    echo "Error Code: " . $e->getCode() . "\n";
    echo "Error Message: " . $e->getMessage() . "\n\n";

    // Try system command
    echo "Testing with mysql CLI:\n";
    $cmd = "mysql -u " . env('DB_USERNAME') . " -p" . env('DB_PASSWORD') . " " . env('DB_DATABASE') . " -e 'SELECT 1' 2>&1";
    $output = shell_exec($cmd);
    echo $output;
}

echo "\n=== End Diagnostic ===\n";
?>
