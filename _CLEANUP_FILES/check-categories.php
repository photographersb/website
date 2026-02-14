<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CATEGORIES TABLE ===\n";
echo "Total categories: " . DB::table('categories')->count() . "\n";
echo "Active categories: " . DB::table('categories')->where('is_active', true)->count() . "\n";
echo "Inactive categories: " . DB::table('categories')->where('is_active', false)->count() . "\n\n";

echo "Sample categories:\n";
$categories = DB::table('categories')->select('id', 'name', 'is_active', 'display_order')->orderBy('display_order')->get();
foreach ($categories as $cat) {
    echo $cat->id . ": " . $cat->name . " (Active: " . ($cat->is_active ? 'Yes' : 'No') . ")\n";
}
?>
