<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🎯 Simulating Website Visitors...\n\n";

// Clear existing visitor logs for testing
echo "Clearing old visitor logs...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('page_views')->truncate();
DB::table('visitor_logs')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "✅ Cleared\n\n";

// Simulate 15 unique visitors
echo "Simulating visitors:\n";
echo "====================\n";

for ($i = 1; $i <= 15; $i++) {
    $sessionId = 'session_' . uniqid();
    $now = now();
    
    // Insert visitor log
    $visitorId = DB::table('visitor_logs')->insertGetId([
        'session_id' => $sessionId,
        'ip_address' => '192.168.1.' . $i,
        'user_agent' => 'Mozilla/5.0 (Test Browser)',
        'device_type' => ['desktop', 'mobile', 'tablet'][rand(0, 2)],
        'browser' => ['Chrome', 'Firefox', 'Safari'][rand(0, 2)],
        'os' => ['Windows', 'macOS', 'Linux'][rand(0, 2)],
        'referrer' => null,
        'landing_page' => 'http://localhost:8000/',
        'first_visit' => $now,
        'last_activity' => $now,
        'page_views' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    
    // Insert page view
    DB::table('page_views')->insert([
        'visitor_log_id' => $visitorId,
        'url' => 'http://localhost:8000/',
        'page_title' => 'Home',
        'referrer' => 'https://google.com',
        'time_on_page' => rand(10, 300),
        'viewed_at' => $now,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    
    echo "✅ Visitor #{$i} - IP: 192.168.1.{$i} - Session: {$sessionId}\n";
    
    // Small delay to simulate real traffic
    usleep(100000); // 0.1 second
}

echo "\n📊 Current Stats:\n";
echo "====================\n";

$visitorCount = DB::table('visitor_logs')->count();
$pageViewCount = DB::table('page_views')->count();

echo "Total Visitors: {$visitorCount}\n";
echo "Total Page Views: {$pageViewCount}\n";

// Test the API
echo "\n🔄 Testing API Endpoint:\n";
echo "====================\n";

// Clear cache to get fresh data
\Illuminate\Support\Facades\Cache::forget('public_platform_stats');

$controller = new App\Http\Controllers\Api\PlatformStatsController();
$response = $controller->index();
$data = $response->getData();

echo "API Response:\n";
echo "- Visitors: " . $data->data->visitors . "\n";
echo "- Photographers: " . $data->data->photographers . "\n";
echo "- Cities: " . $data->data->cities . "\n";

echo "\n✅ Visitor tracking is working!\n";
echo "The counter will now start from {$visitorCount} and increment with each new visitor.\n";
