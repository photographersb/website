<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->bootstrap();

// Mark migrations as complete
$migrations = [
    '2026_01_31_000002_create_seo_meta_table',
    '2026_01_31_000003_add_city_and_hashtags_to_photographers_table',
];

foreach ($migrations as $migration) {
    if (!\DB::table('migrations')->where('migration', $migration)->exists()) {
        $batch = \DB::table('migrations')->max('batch') ?? 0;
        \DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch + 1,
        ]);
        echo "✓ Marked as complete: $migration\n";
    } else {
        echo "~ Already marked: $migration\n";
    }
}

echo "\nDone.\n";
