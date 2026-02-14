<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Existing Indexes ===\n\n";

// Check photographers table
echo "PHOTOGRAPHERS TABLE:\n";
$indexes = DB::select('SHOW INDEX FROM photographers');
foreach ($indexes as $index) {
    if (strpos($index->Key_name, 'idx_') === 0) {
        echo "  - {$index->Key_name} on {$index->Column_name}\n";
    }
}

echo "\nEVENTS TABLE:\n";
$indexes = DB::select('SHOW INDEX FROM events');
foreach ($indexes as $index) {
    if (strpos($index->Key_name, 'idx_') === 0) {
        echo "  - {$index->Key_name} on {$index->Column_name}\n";
    }
}

echo "\nCOMPETITIONS TABLE:\n";
$indexes = DB::select('SHOW INDEX FROM competitions');
foreach ($indexes as $index) {
    if (strpos($index->Key_name, 'idx_') === 0) {
        echo "  - {$index->Key_name} on {$index->Column_name}\n";
    }
}
