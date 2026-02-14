<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LOCATIONS TABLE ===\n";
$locations = DB::table('locations')->selectRaw('type, COUNT(*) as count')->groupBy('type')->get();
foreach ($locations as $loc) {
    echo $loc->type . ": " . $loc->count . "\n";
}
echo "Total in locations: " . DB::table('locations')->count() . "\n\n";

echo "=== CITIES TABLE ===\n";
echo "Total in cities: " . DB::table('cities')->count() . "\n";
$cities = DB::table('cities')->selectRaw('COUNT(*) as count, SUM(CASE WHEN division IS NOT NULL THEN 1 ELSE 0 END) as with_division')->get();
foreach ($cities as $c) {
    echo "Total records: " . $c->count . ", With division: " . $c->with_division . "\n";
}

echo "\nFirst 10 cities:\n";
$first_cities = DB::table('cities')->select('id', 'name', 'division')->limit(10)->get();
foreach ($first_cities as $c) {
    echo $c->id . ": " . $c->name . " (Division: " . $c->division . ")\n";
}

echo "\nFirst 10 locations:\n";
$first_locations = DB::table('locations')->select('id', 'name', 'type', 'parent_id')->limit(10)->get();
foreach ($first_locations as $l) {
    echo $l->id . ": " . $l->name . " (Type: " . $l->type . ", Parent: " . $l->parent_id . ")\n";
}
?>
