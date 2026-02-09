<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== EVENTS TABLE COLUMNS ===\n\n";
$columns = DB::select('SHOW COLUMNS FROM events');
foreach ($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}
