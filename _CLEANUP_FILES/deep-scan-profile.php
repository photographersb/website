<?php
// Deep scan photographer profile for database audit

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Photographer;
use App\Models\PhotographerStats;
use App\Models\CompetitionSubmission;
use Illuminate\Support\Facades\DB;

$slug = 'nasim-newaz-Du5tz3';
$photographer = Photographer::where('slug', $slug)->with([
    'user',
    'city',
    'categories',
    'packages',
    'albums.photos',
    'reviews.reviewer',
    'awards',
    'trustScore'
])->first();

if (!$photographer) {
    echo "❌ Photographer not found with slug: $slug\n";
    exit(1);
}

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║           DEEP SCAN: PHOTOGRAPHER PROFILE DATABASE AUDIT          ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

// 1. Basic Info
echo "📋 BASIC INFORMATION\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  ID: {$photographer->id}\n";
echo "  Slug: {$photographer->slug}\n";
echo "  User: {$photographer->user?->name} ({$photographer->user?->email})\n";
echo "  Phone: " . ($photographer->user?->phone ?? 'N/A') . "\n";
echo "  City: {$photographer->city?->name} (ID: {$photographer->city_id})\n";
echo "  Location: " . ($photographer->location ?? 'N/A') . "\n";
echo "  Business Name: " . ($photographer->business_name ?? 'N/A') . "\n";
echo "\n";

// 2. Profile Data
echo "🎯 PROFILE DATA\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Bio: " . (strlen($photographer->bio ?? '') > 80 ? substr($photographer->bio, 0, 80) . "..." : ($photographer->bio ?? 'N/A')) . "\n";
echo "  Experience Years: {$photographer->experience_years}\n";
echo "  Specializations: " . json_encode($photographer->specializations) . "\n";
echo "  Favorite Hashtags: " . json_encode($photographer->favorite_hashtags) . "\n";
echo "  Service Area Radius: " . ($photographer->service_area_radius ?? 'N/A') . " km\n";
echo "\n";

// 3. Social & Web Links
echo "🔗 SOCIAL & WEB LINKS\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Website: " . ($photographer->website_url ? "✅ " . $photographer->website_url : "❌ Not Set") . "\n";
echo "  Facebook: " . ($photographer->facebook_url ? "✅ " . $photographer->facebook_url : "❌ Not Set") . "\n";
echo "  Instagram: " . ($photographer->instagram_url ? "✅ " . $photographer->instagram_url : "❌ Not Set") . "\n";
echo "  Twitter/X: " . ($photographer->twitter_url ? "✅ " . $photographer->twitter_url : "❌ Not Set") . "\n";
echo "  LinkedIn: " . ($photographer->linkedin_url ? "✅ " . $photographer->linkedin_url : "❌ Not Set") . "\n";
echo "  YouTube: " . ($photographer->youtube_url ? "✅ " . $photographer->youtube_url : "❌ Not Set") . "\n";
echo "\n";

// 4. Verification & Status
echo "✅ VERIFICATION & STATUS\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Verified: " . ($photographer->is_verified ? "✅ YES" : "❌ NO") . "\n";
echo "  Verification Type: " . ($photographer->verification_type ?? 'None') . "\n";
echo "  Verified At: " . ($photographer->verified_at ?? 'N/A') . "\n";
echo "  Featured: " . ($photographer->is_featured ? "✅ YES (until " . ($photographer->featured_until ?? 'Forever') . ")" : "❌ NO") . "\n";
echo "\n";

// 5. Ratings & Reviews
echo "⭐ RATINGS & REVIEWS\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Average Rating: {$photographer->average_rating}/5.0\n";
echo "  Total Reviews: {$photographer->rating_count}\n";
echo "  Reviews in DB: {$photographer->reviews->count()}\n";
if ($photographer->reviews->count() > 0) {
    echo "  Recent Reviews:\n";
    foreach ($photographer->reviews->take(3) as $review) {
        $rating = DB::table('reviews')->where('id', $review->id)->value('rating') ?? 'N/A';
        echo "    - {$review->reviewer?->name}: $rating/5 - " . substr($review->comment ?? '', 0, 50) . "...\n";
    }
}
echo "\n";

// 6. Portfolio Data
echo "🖼️  PORTFOLIO DATA\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Total Albums: {$photographer->albums->count()}\n";
if ($photographer->albums->count() > 0) {
    $totalPhotos = $photographer->albums->sum(fn($a) => $a->photos->count());
    echo "  Total Photos in Albums: {$totalPhotos}\n";
    echo "  Albums:\n";
    foreach ($photographer->albums->take(5) as $album) {
        echo "    - {$album->name}: {$album->photos->count()} photos (Public: " . ($album->is_public ? "YES" : "NO") . ")\n";
    }
}
echo "\n";

// 7. Services & Packages
echo "💼 SERVICES & PACKAGES\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Total Packages: {$photographer->packages->count()}\n";
if ($photographer->packages->count() > 0) {
    echo "  Packages:\n";
    foreach ($photographer->packages->take(5) as $pkg) {
        echo "    - {$pkg->name}: ৳" . number_format($pkg->base_price) . " (" . ($pkg->is_active ? "ACTIVE" : "INACTIVE") . ")\n";
    }
}
echo "  Starting Price: ৳" . number_format($photographer->packages()->where('is_active', true)->min('base_price') ?? 0) . "\n";
echo "\n";

// 8. Categories
echo "📁 CATEGORIES\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Total Categories: {$photographer->categories->count()}\n";
if ($photographer->categories->count() > 0) {
    echo "  Categories:\n";
    foreach ($photographer->categories as $cat) {
        echo "    - {$cat->name}\n";
    }
}
echo "\n";

// 9. Stats & Achievements
$stats = PhotographerStats::where('photographer_id', $photographer->id)->first();
echo "📊 STATISTICS & ACHIEVEMENTS\n";
echo "─────────────────────────────────────────────────────────────────────\n";
if ($stats) {
    echo "  Profile Views: {$stats->profile_views}\n";
    echo "  Response Rate: {$stats->response_rate}%\n";
    echo "  Average Response Time: {$stats->average_response_time}h\n";
    echo "  Portfolio Completeness: {$stats->portfolio_completeness}%\n";
    echo "  Level: " . ($stats->level ?? 'N/A') . "\n";
    echo "  Total Points: " . ($stats->total_points ?? 0) . "\n";
} else {
    echo "  ❌ No stats record found\n";
}
echo "\n";

// 10. Bookings
echo "📅 BOOKINGS\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Total Bookings: {$photographer->total_bookings}\n";
echo "  Completed Bookings: {$photographer->completed_bookings}\n";
echo "\n";

// 11. Awards
echo "🏆 AWARDS & COMPETITIONS\n";
echo "─────────────────────────────────────────────────────────────────────\n";
echo "  Awards: {$photographer->awards->count()}\n";
if ($photographer->awards->count() > 0) {
    foreach ($photographer->awards->take(5) as $award) {
        echo "    - {$award->title} ({$award->year})\n";
    }
}

$competitions = CompetitionSubmission::where('photographer_id', $photographer->id)->distinct('competition_id')->count('competition_id');
echo "  Competitions Participated: {$competitions}\n";

$wins = CompetitionSubmission::where('photographer_id', $photographer->id)->where('is_winner', true)->count();
echo "  Competition Wins: {$wins}\n";
echo "\n";

// 12. Trust Score
echo "🛡️  TRUST SCORE\n";
echo "─────────────────────────────────────────────────────────────────────\n";
if ($photographer->trustScore) {
    echo "  Overall Score: " . ($photographer->trustScore->overall_score ?? 'N/A') . "\n";
    echo "  Verification: " . ($photographer->trustScore->verification_score ?? 'N/A') . "\n";
    echo "  Response: " . ($photographer->trustScore->response_score ?? 'N/A') . "\n";
    echo "  Quality: " . ($photographer->trustScore->quality_score ?? 'N/A') . "\n";
    echo "  Reliability: " . ($photographer->trustScore->reliability_score ?? 'N/A') . "\n";
} else {
    echo "  ❌ No trust score record found\n";
}
echo "\n";

// 13. Database Fields Check
echo "💾 DATABASE FIELDS CHECK\n";
echo "─────────────────────────────────────────────────────────────────────\n";
$allFields = collect([
    'user_id' => $photographer->user_id,
    'city_id' => $photographer->city_id,
    'slug' => $photographer->slug,
    'profile_picture' => $photographer->profile_picture,
    'bio' => !empty($photographer->bio),
    'location' => !empty($photographer->location),
    'experience_years' => $photographer->experience_years,
    'specializations' => !empty($photographer->specializations),
    'favorite_hashtags' => !empty($photographer->favorite_hashtags),
    'service_area_radius' => $photographer->service_area_radius,
    'average_rating' => $photographer->average_rating,
    'rating_count' => $photographer->rating_count,
    'is_verified' => $photographer->is_verified,
    'verification_type' => !empty($photographer->verification_type),
    'verified_at' => !empty($photographer->verified_at),
    'is_featured' => $photographer->is_featured,
    'featured_until' => !empty($photographer->featured_until),
    'profile_completeness' => $stats?->portfolio_completeness,
    'total_bookings' => $photographer->total_bookings,
    'completed_bookings' => $photographer->completed_bookings,
    'response_time_avg' => !empty($photographer->response_time_avg),
    'facebook_url' => !empty($photographer->facebook_url),
    'instagram_url' => !empty($photographer->instagram_url),
    'twitter_url' => !empty($photographer->twitter_url),
    'linkedin_url' => !empty($photographer->linkedin_url),
    'youtube_url' => !empty($photographer->youtube_url),
    'website_url' => !empty($photographer->website_url),
]);

foreach ($allFields as $field => $value) {
    $status = $value ? "✅" : "❌";
    if (is_bool($value)) {
        $value = $value ? "SET" : "EMPTY";
    } elseif (is_numeric($value) && $value === 0) {
        $value = "0";
    }
    echo "  $status $field: $value\n";
}
echo "\n";

// 14. Summary
echo "📈 COMPLETENESS SUMMARY\n";
echo "─────────────────────────────────────────────────────────────────────\n";
$completenessFields = [
    'Basic Info' => $photographer->user ? 1 : 0,
    'Bio' => !empty($photographer->bio) ? 1 : 0,
    'Location' => !empty($photographer->location) ? 1 : 0,
    'Experience' => $photographer->experience_years ? 1 : 0,
    'Categories' => $photographer->categories->count() > 0 ? 1 : 0,
    'Portfolio Albums' => $photographer->albums->count() > 0 ? 1 : 0,
    'Packages/Services' => $photographer->packages->count() > 0 ? 1 : 0,
    'Social Links' => ($photographer->facebook_url || $photographer->instagram_url || $photographer->website_url) ? 1 : 0,
    'Verified Status' => $photographer->is_verified ? 1 : 0,
    'Reviews' => $photographer->reviews->count() > 0 ? 1 : 0,
];

$complete = array_sum($completenessFields);
$total = count($completenessFields);
$percentage = ($complete / $total) * 100;

foreach ($completenessFields as $field => $status) {
    $icon = $status ? "✅" : "❌";
    echo "  $icon $field\n";
}

echo "\nOverall Completeness: $complete/$total ({$percentage}%)\n";
echo "Database Completeness Score: " . ($stats?->portfolio_completeness ?? 0) . "%\n";
echo "\n";

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║                        SCAN COMPLETED                             ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
