<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check current count
$count = DB::table('locations')->count();
$divisions = DB::table('locations')->where('type', 'division')->count();
$districts = DB::table('locations')->where('type', 'district')->count();

echo "Total locations: $count\n";
echo "Divisions: $divisions\n";
echo "Districts: $districts\n";

// Clear if needed
if ($count < 72) {
    echo "\nClearing locations table to re-seed...\n";
    DB::table('locations')->delete();
    
    // Run the seeder
    Artisan::call('db:seed', ['--class' => 'BangladeshLocationSeeder', '--force' => true]);
    
    // Check again
    $count = DB::table('locations')->count();
    $divisions = DB::table('locations')->where('type', 'division')->count();
    $districts = DB::table('locations')->where('type', 'district')->count();
    
    echo "\nAfter seeding:\n";
    echo "Total locations: $count\n";
    echo "Divisions: $divisions\n";
    echo "Districts: $districts\n";
}
?>
