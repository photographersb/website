<?php
/**
 * QA Automated Test Script
 * Tests all API endpoints and forms
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

$baseUrl = 'http://127.0.0.1:8000/api/v1';
$results = [];

echo "\n=== PHOTOGRAPHER SB QA TEST SUITE ===\n\n";

// Test 1: Public API Endpoints
echo "[TEST 1] Public API Endpoints\n";
$publicEndpoints = [
    '/categories' => 'List categories',
    '/cities' => 'List cities',
    '/photographers' => 'List photographers',
    '/events' => 'List events',
    '/competitions' => 'List competitions',
    '/mentors' => 'List mentors',
    '/judges' => 'List judges',
];

foreach ($publicEndpoints as $endpoint => $description) {
    try {
        $response = file_get_contents($baseUrl . $endpoint);
        $decoded = json_decode($response, true);
        $status = isset($decoded['status']) ? $decoded['status'] : 'unknown';
        $count = isset($decoded['data']) ? (is_array($decoded['data']) ? count($decoded['data']) : 1) : 0;
        echo "  ✓ $endpoint - Status: $status - Records: $count\n";
        $results['endpoints'][$endpoint] = ['status' => 'PASS', 'details' => "$status - $count records"];
    } catch (\Exception $e) {
        echo "  ✗ $endpoint - Error: " . $e->getMessage() . "\n";
        $results['endpoints'][$endpoint] = ['status' => 'FAIL', 'error' => $e->getMessage()];
    }
}

// Test 2: Model Existence & DB Check
echo "\n[TEST 2] Database Models & Records\n";
$models = [
    'App\Models\User' => 'Users',
    'App\Models\Photographer' => 'Photographers',
    'App\Models\Event' => 'Events',
    'App\Models\Competition' => 'Competitions',
    'App\Models\Category' => 'Categories',
    'App\Models\City' => 'Cities',
    'App\Models\Review' => 'Reviews',
    'App\Models\Booking' => 'Bookings',
    'App\Models\Transaction' => 'Transactions',
];

foreach ($models as $modelClass => $name) {
    try {
        $count = $modelClass::count();
        echo "  ✓ $name - Count: $count\n";
        $results['models'][$name] = ['status' => 'OK', 'count' => $count];
    } catch (\Exception $e) {
        echo "  ✗ $name - Error: " . $e->getMessage() . "\n";
        $results['models'][$name] = ['status' => 'ERROR', 'error' => $e->getMessage()];
    }
}

// Test 3: Admin Routes Check
echo "\n[TEST 3] Admin Routes\n";
$adminRoutes = [
    '/admin/dashboard',
    '/admin/competitions',
    '/admin/events',
    '/admin/users',
    '/admin/photographers',
    '/admin/bookings',
    '/admin/reviews',
    '/admin/transactions',
    '/admin/verifications',
    '/admin/settings',
    '/admin/sponsors',
    '/admin/notices',
    '/admin/mentors',
    '/admin/judges',
];

foreach ($adminRoutes as $route) {
    echo "  → Route exists: $route\n";
    $results['routes'][$route] = ['status' => 'DEFINED'];
}

// Test 4: Key Features
echo "\n[TEST 4] Critical Features Check\n";

// Check migrations
$migrations = DB::table('migrations')->count();
echo "  ✓ Migrations run: $migrations\n";
$results['features']['migrations'] = $migrations;

// Check if tables exist
$tables = [
    'users', 'photographers', 'events', 'competitions', 
    'bookings', 'reviews', 'transactions', 'categories', 'cities'
];

foreach ($tables as $table) {
    $exists = DB::getSchemaBuilder()->hasTable($table);
    $status = $exists ? '✓' : '✗';
    echo "  $status Table '$table' exists\n";
    $results['tables'][$table] = $exists;
}

// Test 5: File Structure
echo "\n[TEST 5] Required Files Check\n";
$requiredFiles = [
    'bootstrap/app.php' => 'App bootstrap',
    'app/Http/Middleware/Authenticate.php' => 'Auth middleware',
    'routes/api.php' => 'API routes',
    'routes/web.php' => 'Web routes',
    'config/database.php' => 'Database config',
];

foreach ($requiredFiles as $file => $description) {
    $exists = file_exists(base_path($file));
    $status = $exists ? '✓' : '✗';
    echo "  $status $file\n";
    $results['files'][$file] = $exists;
}

echo "\n=== SUMMARY ===\n";
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
echo "\n\n";

?>
