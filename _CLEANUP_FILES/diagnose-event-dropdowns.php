<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\City;
use App\Models\Photographer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== EVENT DROPDOWNS DIAGNOSTIC ===\n\n";

// 1. Check Cities Table
echo "1. CITIES TABLE CHECK:\n";
if (Schema::hasTable('cities')) {
    $citiesCount = City::count();
    echo "   ✓ Cities table exists\n";
    echo "   Total cities: $citiesCount\n";
    
    if ($citiesCount > 0) {
        $hasIsActive = Schema::hasColumn('cities', 'is_active');
        if ($hasIsActive) {
            $activeCities = City::where('is_active', true)->count();
            echo "   Active cities: $activeCities\n";
            $sampleCities = City::where('is_active', true)->limit(5)->get(['id', 'name', 'slug']);
        } else {
            echo "   ⚠️  cities table missing 'is_active' column\n";
            $sampleCities = City::limit(5)->get(['id', 'name', 'slug']);
        }
        
        echo "   Sample cities:\n";
        foreach ($sampleCities as $city) {
            echo "      - ID: {$city->id}, Name: {$city->name}, Slug: {$city->slug}\n";
        }
    } else {
        echo "   ⚠️  No cities found in database!\n";
    }
} else {
    echo "   ❌ Cities table does NOT exist!\n";
}

// 2. Check Locations table (user mentioned)
echo "\n2. LOCATIONS TABLE CHECK:\n";
if (Schema::hasTable('locations')) {
    $locationsCount = DB::table('locations')->count();
    echo "   ✓ Locations table exists\n";
    echo "   Total locations: $locationsCount\n";
    
    if ($locationsCount > 0) {
        $hasIsActive = Schema::hasColumn('locations', 'is_active');
        if ($hasIsActive) {
            $activeLocations = DB::table('locations')->where('is_active', true)->count();
            echo "   Active locations: $activeLocations\n";
            $sampleLocations = DB::table('locations')->where('is_active', true)->limit(5)->get(['id', 'name', 'slug']);
        } else {
            $sampleLocations = DB::table('locations')->limit(5)->get(['id', 'name', 'slug']);
        }
        
        echo "   Sample locations:\n";
        foreach ($sampleLocations as $location) {
            echo "      - ID: {$location->id}, Name: {$location->name}, Slug: {$location->slug}\n";
        }
    } else {
        echo "   ⚠️  No locations found in database!\n";
    }
} else {
    echo "   ℹ️  Locations table does NOT exist (using cities instead)\n";
}

// 3. Check Photographers
echo "\n3. PHOTOGRAPHERS TABLE CHECK:\n";
if (Schema::hasTable('photographers')) {
    $photographersCount = Photographer::count();
    echo "   ✓ Photographers table exists\n";
    echo "   Total photographers: $photographersCount\n";
    
    if ($photographersCount > 0) {
        $activePhotographers = Photographer::where('is_verified', true)->count();
        echo "   Verified photographers: $activePhotographers\n";
        
        $sample = Photographer::with('user')->limit(5)->get();
        echo "   Sample photographers:\n";
        foreach ($sample as $photographer) {
            $userName = $photographer->user ? $photographer->user->name : 'No user';
            $businessName = $photographer->business_name ?? 'No business name';
            echo "      - ID: {$photographer->id}, User: {$userName}, Business: {$businessName}, Verified: " . ($photographer->is_verified ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "   ⚠️  No photographers found in database!\n";
    }
} else {
    echo "   ❌ Photographers table does NOT exist!\n";
}

// 4. Check Events Table Structure
echo "\n4. EVENTS TABLE STRUCTURE:\n";
if (Schema::hasTable('events')) {
    echo "   ✓ Events table exists\n";
    
    $columns = Schema::getColumnListing('events');
    
    $requiredColumns = [
        'city_id' => false,
        'venue_name' => false,
        'venue_address' => false,
        'event_type' => false,
        'ticket_price' => false,
        'organizer_id' => false,
        'location' => false,
    ];
    
    foreach ($requiredColumns as $col => $exists) {
        $requiredColumns[$col] = in_array($col, $columns);
    }
    
    echo "   Required columns:\n";
    foreach ($requiredColumns as $col => $exists) {
        $status = $exists ? '✓' : '❌';
        echo "      $status $col\n";
    }
    
    $eventsCount = DB::table('events')->count();
    echo "   Total events: $eventsCount\n";
} else {
    echo "   ❌ Events table does NOT exist!\n";
}

// 5. Check Mentors Table
echo "\n5. MENTORS TABLE CHECK:\n";
if (Schema::hasTable('mentors')) {
    $mentorsCount = DB::table('mentors')->count();
    echo "   ✓ Mentors table exists\n";
    echo "   Total mentors: $mentorsCount\n";
    
    if ($mentorsCount > 0) {
        $activeMentors = DB::table('mentors')->where('is_active', true)->count();
        echo "   Active mentors: $activeMentors\n";
    }
} else {
    echo "   ℹ️  Mentors table does NOT exist (feature not implemented yet)\n";
}

// 6. Check event_mentors pivot
echo "\n6. EVENT_MENTORS PIVOT TABLE:\n";
if (Schema::hasTable('event_mentors')) {
    echo "   ✓ event_mentors pivot table exists\n";
} else {
    echo "   ℹ️  event_mentors pivot table does NOT exist (needs creation)\n";
}

echo "\n=== ROOT CAUSE SUMMARY ===\n\n";

// Identify issues
$issues = [];

if (!Schema::hasTable('cities') || City::count() === 0) {
    $issues[] = "❌ City dropdown empty: No cities in database or table missing";
}

if (!Schema::hasTable('photographers') || Photographer::count() === 0) {
    $issues[] = "❌ Photographer dropdown empty: No photographers in database or table missing";
}

if (Schema::hasTable('events')) {
    $columns = Schema::getColumnListing('events');
    if (!in_array('venue_address', $columns)) {
        $issues[] = "⚠️  venue_address column missing from events table";
    }
}

if (count($issues) > 0) {
    echo "ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "  $issue\n";
    }
} else {
    echo "✓ All checks passed! Dropdowns should work.\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Ensure cities table has active data\n";
echo "2. Ensure photographers table has verified photographers\n";
echo "3. Run migrations to add missing columns\n";
echo "4. Create mentors table and pivot if needed\n";
echo "5. Clear caches: php artisan optimize:clear\n";
echo "6. Rebuild frontend: npm run build\n";

