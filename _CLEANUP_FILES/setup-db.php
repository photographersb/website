<?php
/**
 * Laravel Database Setup Script
 * Sets up MySQL database and runs migrations
 */

$appPath = '/home/photographersb/htdocs/photographersb.com/public';
chdir($appPath);

// Load Laravel
require_once 'bootstrap/app.php';

$app = app();
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Try to run migrations
try {
    echo "Running migrations...\n";
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    echo "✓ Migrations completed successfully!\n";
} catch (\Throwable $e) {
    echo "✗ Migration error: " . $e->getMessage() . "\n";
    
    // Try to identify if it's a connection error
    if (strpos($e->getMessage(), 'SQLSTATE[HY000]') !== false) {
        echo "\nDatabase connection failed. Attempting to create database...\n";
        
        // Get database name from config
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbHost = config('database.connections.mysql.host');
        
        echo "Target Database: $dbName\n";
        echo "Target User: $dbUser\n";
        echo "Host: $dbHost\n";
        
        // Try connecting as root to create database
        try {
            $pdo = new PDO("mysql:host=$dbHost", 'root', '');
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Create user if not exists
            $pdo->exec("CREATE USER IF NOT EXISTS '$dbUser'@'localhost' IDENTIFIED BY 'password'");
            $pdo->exec("GRANT ALL PRIVILEGES ON `$dbName`.* TO '$dbUser'@'localhost'");
            $pdo->exec("FLUSH PRIVILEGES");
            
            echo "✓ Database and user created\n";
            echo "Please update .env with the new password and try migrations again\n";
        } catch (\PDOException $pde) {
            echo "✗ Could not create database: " . $pde->getMessage() . "\n";
            echo "You may need to manually create the database through CloudPanel\n";
        }
    }
}
?>
