<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Certificate Templates Table Structure:\n";
echo "=====================================\n\n";

$columns = DB::select('SHOW COLUMNS FROM certificate_templates');
foreach ($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}
