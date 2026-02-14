<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Table Columns ===\n\n";

echo "EVENTS TABLE COLUMNS:\n";
$columns = DB::select('SHOW COLUMNS FROM events');
foreach ($columns as $col) {
    if (strpos($col->Field, 'date') !== false || strpos($col->Field, 'start') !== false || strpos($col->Field, 'end') !== false) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
}

echo "\nCOMPETITIONS TABLE DATE COLUMNS:\n";
$columns = DB::select('SHOW COLUMNS FROM competitions');
foreach ($columns as $col) {
    if (strpos($col->Field, 'date') !== false || strpos($col->Field, 'start') !== false || strpos($col->Field, 'end') !== false) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
}
