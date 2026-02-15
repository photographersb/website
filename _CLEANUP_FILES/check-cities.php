<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\City;

echo "=== CITIES IN DATABASE ===\n\n";
$cities = City::take(10)->get(['id', 'name', 'slug']);
foreach($cities as $city) {
    echo "{$city->id}: {$city->name} ({$city->slug})\n";
}

echo "\nTotal: " . City::count() . " cities\n";
