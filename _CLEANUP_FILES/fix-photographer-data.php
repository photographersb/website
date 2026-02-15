<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Photographer;
use App\Models\City;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

echo "=== FIXING PHOTOGRAPHER DATA ===\n\n";

// 1. Fix photographers with NULL city_id
echo "1. Assigning cities to photographers with NULL city_id...\n";
$photographersWithoutCity = Photographer::whereNull('city_id')->get();
$cities = City::all();

if ($cities->isEmpty()) {
    echo "   ERROR: No cities found in database!\n";
} else {
    echo "   Found {$photographersWithoutCity->count()} photographers without city\n";
    
    foreach ($photographersWithoutCity as $photographer) {
        // Assign a random city (in production, you'd want to assign based on location)
        $randomCity = $cities->random();
        $photographer->city_id = $randomCity->id;
        $photographer->save();
        
        echo "   - {$photographer->slug}: Assigned to {$randomCity->name}\n";
    }
}

// 2. Assign categories to photographers without categories
echo "\n2. Assigning categories to photographers without categories...\n";
$allCategories = Category::where('is_active', true)->get();

if ($allCategories->isEmpty()) {
    echo "   ERROR: No categories found in database!\n";
} else {
    $photographers = Photographer::all();
    
    foreach ($photographers as $photographer) {
        $currentCategoryCount = $photographer->categories()->count();
        
        if ($currentCategoryCount == 0) {
            // Assign 1-3 random categories
            $numCategories = rand(1, 3);
            $randomCategories = $allCategories->random(min($numCategories, $allCategories->count()));
            
            foreach ($randomCategories as $category) {
                // Check if already exists
                $exists = DB::table('photographer_category')
                    ->where('photographer_id', $photographer->id)
                    ->where('category_id', $category->id)
                    ->exists();
                    
                if (!$exists) {
                    DB::table('photographer_category')->insert([
                        'photographer_id' => $photographer->id,
                        'category_id' => $category->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            $photographer->load('categories');
            $categoryNames = $photographer->categories->pluck('name')->join(', ');
            echo "   - {$photographer->slug}: Assigned {$numCategories} categories ({$categoryNames})\n";
        }
    }
}

// 3. Verify the fixes
echo "\n3. Verification:\n";
$totalPhotographers = Photographer::count();
$photographersWithCity = Photographer::whereNotNull('city_id')->count();
$photographersWithCategories = DB::table('photographer_category')
    ->distinct('photographer_id')
    ->count('photographer_id');

echo "   Total photographers: $totalPhotographers\n";
echo "   With city assigned: $photographersWithCity\n";
echo "   With categories assigned: $photographersWithCategories\n";

if ($photographersWithCity < $totalPhotographers) {
    echo "   ⚠️  WARNING: " . ($totalPhotographers - $photographersWithCity) . " photographers still without city!\n";
} else {
    echo "   ✓ All photographers have cities assigned\n";
}

if ($photographersWithCategories < $totalPhotographers) {
    echo "   ⚠️  WARNING: " . ($totalPhotographers - $photographersWithCategories) . " photographers still without categories!\n";
} else {
    echo "   ✓ All photographers have categories assigned\n";
}

// 4. Test API queries
echo "\n4. Testing API queries:\n";

// Test category filter
$weddingCategory = Category::where('slug', 'wedding-photography')->first();
if ($weddingCategory) {
    $count = Photographer::where('is_verified', true)
        ->whereHas('categories', function ($q) use ($weddingCategory) {
            $q->where('categories.id', $weddingCategory->id);
        })
        ->count();
    echo "   Wedding Photography: $count photographers\n";
}

// Test city filter
$dhaka = City::where('slug', 'dhaka')->first();
if ($dhaka) {
    $count = Photographer::where('is_verified', true)
        ->where('city_id', $dhaka->id)
        ->count();
    echo "   Dhaka: $count photographers\n";
}

echo "\n✓ Done! Photographers should now appear in category and location filters.\n";
