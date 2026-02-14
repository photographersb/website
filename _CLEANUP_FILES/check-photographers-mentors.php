<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Photographer;
use App\Models\Mentor;

echo "=== PHOTOGRAPHERS ===\n";
echo "Total: " . Photographer::count() . "\n";
echo "Verified: " . Photographer::where('is_verified', true)->count() . "\n\n";

echo "=== MENTORS ===\n";
echo "Total: " . Mentor::count() . "\n";
echo "Active: " . Mentor::where('is_active', true)->count() . "\n\n";

$photographers = Photographer::where('is_verified', true)->take(5)->get(['id', 'user_id']);
if ($photographers->count() > 0) {
    echo "Sample verified photographers:\n";
    foreach ($photographers as $p) {
        echo "  - Photographer #{$p->id} (user_id: {$p->user_id})\n";
    }
} else {
    echo "No verified photographers found.\n";
}

echo "\n";

$mentors = Mentor::where('is_active', true)->take(5)->get(['id', 'name']);
if ($mentors->count() > 0) {
    echo "Sample active mentors:\n";
    foreach ($mentors as $m) {
        echo "  - {$m->name} (ID: {$m->id})\n";
    }
} else {
    echo "No active mentors found.\n";
}
