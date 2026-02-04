<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PHOTOGRAPHER DEBUG ===\n\n";

// Check totals
$totalPhotographers = \App\Models\Photographer::count();
$verifiedPhotographers = \App\Models\Photographer::where('is_verified', true)->count();
$totalCategories = \App\Models\Category::count();
$totalCities = \App\Models\City::count();
$pivotRecords = \Illuminate\Support\Facades\DB::table('photographer_category')->count();

echo "Total Photographers: $totalPhotographers\n";
echo "Verified Photographers: $verifiedPhotographers\n";
echo "Total Categories: $totalCategories\n";
echo "Total Cities: $totalCities\n";
echo "Pivot Records (photographer_category): $pivotRecords\n\n";

// Check sample photographer
echo "=== SAMPLE PHOTOGRAPHER ===\n";
$photographer = \App\Models\Photographer::with(['categories', 'city', 'user'])->first();
if ($photographer) {
    echo "ID: {$photographer->id}\n";
    echo "Name: {$photographer->user->name}\n";
    echo "Slug: {$photographer->slug}\n";
    echo "Verified: " . ($photographer->is_verified ? 'Yes' : 'No') . "\n";
    echo "City ID: " . ($photographer->city_id ?? 'NULL') . "\n";
    echo "City: " . ($photographer->city ? $photographer->city->name : 'NOT ASSIGNED') . "\n";
    echo "Categories via relation: " . $photographer->categories->count() . "\n";
    if ($photographer->categories->count() > 0) {
        foreach ($photographer->categories as $cat) {
            echo "  - {$cat->name} (id: {$cat->id})\n";
        }
    } else {
        echo "  (No categories assigned)\n";
    }
}

// Check pivot table directly
echo "\n=== PIVOT TABLE DATA ===\n";
$pivots = \Illuminate\Support\Facades\DB::table('photographer_category')->take(5)->get();
foreach ($pivots as $pivot) {
    echo "Photographer ID: {$pivot->photographer_id}, Category ID: {$pivot->category_id}\n";
}

// Check if there's a mismatch
echo "\n=== DIAGNOSIS ===\n";
$photographersWithCategories = \Illuminate\Support\Facades\DB::table('photographer_category')
    ->distinct('photographer_id')
    ->count('photographer_id');
echo "Photographers with categories in pivot: $photographersWithCategories\n";

$photographersWithNullCity = \App\Models\Photographer::whereNull('city_id')->count();
echo "Photographers with NULL city_id: $photographersWithNullCity\n";

// Test a query filter
echo "\n=== TEST CATEGORY FILTER ===\n";
$testCategory = \App\Models\Category::first();
if ($testCategory) {
    echo "Testing with category: {$testCategory->name} (slug: {$testCategory->slug})\n";
    
    $query = \App\Models\Photographer::where('is_verified', true)
        ->whereHas('categories', function ($q) use ($testCategory) {
            $q->where('categories.id', $testCategory->id);
        });
    
    $count = $query->count();
    echo "Photographers with this category: $count\n";
    
    if ($count > 0) {
        $results = $query->with('categories')->take(3)->get();
        foreach ($results as $p) {
            echo "  - {$p->slug}: " . $p->categories->pluck('name')->implode(', ') . "\n";
        }
    }
}

// Test a city filter
echo "\n=== TEST CITY FILTER ===\n";
$testCity = \App\Models\City::first();
if ($testCity) {
    echo "Testing with city: {$testCity->name} (slug: {$testCity->slug})\n";
    
    $photographersInCity = \App\Models\Photographer::where('is_verified', true)
        ->where('city_id', $testCity->id)
        ->count();
    
    echo "Verified photographers in this city: $photographersInCity\n";
}

echo "\n=== ROOT CAUSE ANALYSIS ===\n";
if ($photographersWithNullCity == $totalPhotographers) {
    echo "❌ CRITICAL: ALL photographers have NULL city_id!\n";
    echo "   This is why location filters return 0 results.\n\n";
}

if ($pivotRecords == 0) {
    echo "❌ CRITICAL: No photographer-category relationships exist!\n";
    echo "   This is why category filters return 0 results.\n\n";
} elseif ($photographersWithCategories == 0) {
    echo "⚠️  WARNING: Pivot table has records but relationships don't work!\n";
    echo "   Check if the relationship is defined correctly.\n\n";
}

echo "\nDone!\n";
