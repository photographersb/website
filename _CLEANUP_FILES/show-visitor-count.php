<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📊 LIVE VISITOR COUNTER\n";
echo "========================\n\n";

// Get current count
$count = DB::table('visitor_logs')
    ->where('created_at', '>=', now()->subDays(30))
    ->count();

// Format number like the frontend does
function formatNumber($num) {
    if ($num < 1000) {
        return $num;
    }
    if ($num >= 1000 && $num < 1000000) {
        $thousands = $num / 1000;
        return ($thousands % 1 === 0 ? $thousands : number_format($thousands, 1)) . 'K';
    }
    if ($num >= 1000000) {
        $millions = $num / 1000000;
        return ($millions % 1 === 0 ? $millions : number_format($millions, 1)) . 'M';
    }
    return $num;
}

$formatted = formatNumber($count);

// Display like the website
echo "╔════════════════════════════╗\n";
echo "║                            ║\n";
echo "║         $formatted+        ║\n";
echo "║                            ║\n";
echo "║     TOTAL VISITORS         ║\n";
echo "║                            ║\n";
echo "╚════════════════════════════╝\n\n";

echo "Details:\n";
echo "- Exact count: $count visitors\n";
echo "- Time window: Last 30 days\n";
echo "- Last updated: " . now()->format('Y-m-d H:i:s') . "\n\n";

// Recent visitors
echo "Recent visitors:\n";
$recent = DB::table('visitor_logs')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get(['id', 'ip_address', 'device_type', 'browser', 'created_at']);

foreach ($recent as $visitor) {
    $time = \Carbon\Carbon::parse($visitor->created_at)->diffForHumans();
    echo "  • {$visitor->ip_address} ({$visitor->device_type}, {$visitor->browser}) - $time\n";
}

echo "\n✅ Counter is live and tracking!\n";
