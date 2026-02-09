<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = DB::connection()->getSchemaBuilder()->getColumnListing('albums');
echo "Albums table columns:\n";
echo implode("\n", $columns);
echo "\n\n";

// Try without featured
$albums = DB::table('albums')
    ->where('photographer_id', 17)
    ->limit(12)
    ->get();

echo "Found " . count($albums) . " albums\n";
if ($albums->count() > 0) {
    echo json_encode($albums[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
