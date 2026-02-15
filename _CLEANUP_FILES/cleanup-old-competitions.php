<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\Competition;

echo "=== CLEANING UP OLD COMPETITIONS ===\n\n";

// Old competitions to delete
$oldCompIds = [12, 15, 16];

foreach ($oldCompIds as $id) {
    $comp = Competition::find($id);
    if ($comp) {
        echo "Deleting: $comp->title (ID: $id, Prize: ৳$comp->total_prize_pool)\n";
        $comp->delete();
    }
}

echo "\n=== CLEANUP COMPLETE ===\n";
echo "\nRemaining Competitions:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$remaining = Competition::all();
$totalPrize = 0;

foreach ($remaining as $c) {
    echo $c->id . " | " . $c->title . " | Status: " . $c->status . " | Prize: " . $c->total_prize_pool . "\n";
    $totalPrize += $c->total_prize_pool;
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\nStats:\n";
echo "  Total Competitions: " . $remaining->count() . "\n";
echo "  Active: " . Competition::where('status', 'active')->count() . "\n";
echo "  Total Prize Pool: ৳" . number_format($totalPrize, 2) . "\n";

?>
