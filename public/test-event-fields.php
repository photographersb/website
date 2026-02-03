<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Events Table Structure Check\n";
echo "=============================\n\n";

$columns = DB::select('DESCRIBE events');

echo "Looking for venue fields...\n\n";

$venueFieldsFound = [];
foreach ($columns as $column) {
    if (in_array($column->Field, ['venue_name', 'venue_address'])) {
        $venueFieldsFound[] = $column->Field;
        echo "✅ " . $column->Field . " (" . $column->Type . ")\n";
    }
}

echo "\n";

if (count($venueFieldsFound) === 2) {
    echo "✅ SUCCESS: Both venue fields added to events table!\n";
} else {
    echo "❌ ERROR: Venue fields missing!\n";
}

echo "\n";
echo "All events table columns:\n";
echo "-------------------------\n";
foreach ($columns as $column) {
    echo "- " . $column->Field . " (" . $column->Type . ")\n";
}
