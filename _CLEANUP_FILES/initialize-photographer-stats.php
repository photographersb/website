<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Photographer;
use App\Models\PhotographerStats;
use App\Services\AchievementService;

echo "🔍 Checking Photographer Stats Status\n";
echo "======================================\n\n";

$totalPhotographers = Photographer::count();
$photographersWithStats = PhotographerStats::count();

echo "Total photographers: {$totalPhotographers}\n";
echo "Photographers with stats: {$photographersWithStats}\n";
echo "Missing stats: " . ($totalPhotographers - $photographersWithStats) . "\n\n";

if ($totalPhotographers > $photographersWithStats) {
    echo "🔧 Initializing stats for photographers without them...\n\n";
    
    $photographers = Photographer::all();
    $initialized = 0;
    
    foreach ($photographers as $photographer) {
        $stats = PhotographerStats::where('photographer_id', $photographer->id)->first();
        
        if (!$stats) {
            echo "  Initializing stats for: {$photographer->user->name} (ID: {$photographer->id})\n";
            AchievementService::initializeStats($photographer->id);
            
            // Track existing data
            AchievementService::trackProfileUpdate($photographer->id);
            
            $bookingCount = $photographer->bookings()->count();
            if ($bookingCount > 0) {
                echo "    - Tracking {$bookingCount} bookings\n";
                AchievementService::trackBookingCreated($photographer->id);
            }
            
            $reviewCount = $photographer->reviews()->count();
            if ($reviewCount > 0) {
                echo "    - Tracking {$reviewCount} reviews\n";
                $fiveStarReviews = $photographer->reviews()->where('rating', 5)->get();
                foreach ($fiveStarReviews as $review) {
                    AchievementService::trackReviewReceived($photographer->id, 5);
                }
            }
            
            $albumCount = $photographer->albums()->count();
            if ($albumCount > 0) {
                echo "    - Tracking {$albumCount} albums\n";
                foreach ($photographer->albums as $album) {
                    AchievementService::trackAlbumCreated($photographer->id);
                }
            }
            
            $packageCount = $photographer->packages()->count();
            if ($packageCount > 0) {
                echo "    - Tracking {$packageCount} packages\n";
                foreach ($photographer->packages as $package) {
                    AchievementService::trackPackageCreated($photographer->id);
                }
            }
            
            $initialized++;
        }
    }
    
    echo "\n✅ Initialized stats for {$initialized} photographers!\n\n";
} else {
    echo "✅ All photographers have stats initialized!\n\n";
}

echo "📊 Final Stats:\n";
echo "===============\n";
$stats = PhotographerStats::all();
foreach ($stats as $stat) {
    $photographer = $stat->photographer;
    echo "• {$photographer->user->name}: Level {$stat->level}, {$stat->total_points} points\n";
}
