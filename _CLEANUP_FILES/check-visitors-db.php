<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Checking Visitor Logs Database...\n\n";

$count = DB::table('visitor_logs')->count();
echo "Total rows in visitor_logs: {$count}\n\n";

if ($count > 0) {
    echo "Recent entries:\n";
    $recent = DB::table('visitor_logs')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['id', 'session_id', 'ip_address', 'created_at']);
    
    foreach ($recent as $row) {
        echo "  - ID {$row->id}: {$row->ip_address} at {$row->created_at}\n";
    }
    
    echo "\n30 days ago: " . now()->subDays(30)->toDateTimeString() . "\n";
    echo "Today: " . now()->toDateTimeString() . "\n\n";
    
    $last30Days = DB::table('visitor_logs')
        ->where('created_at', '>=', now()->subDays(30))
        ->count();
    
    echo "Visitors in last 30 days: {$last30Days}\n";
}
