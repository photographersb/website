<?php
// Check what admin dashboard can see and what's missing

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Photographer;

$slug = 'nasim-newaz-Du5tz3';
$photographer = Photographer::where('slug', $slug)->with([
    'user',
    'city',
    'categories',
    'packages',
    'albums',
    'reviews',
    'awards'
])->first();

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║                     ADMIN DASHBOARD VISIBILITY CHECK              ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

echo "🔍 WHAT ADMIN CAN SEE (Via /admin/users > View User > Profile)\n";
echo "═════════════════════════════════════════════════════════════════════\n\n";

echo "✅ CURRENTLY VISIBLE IN ADMIN PANEL:\n";
$visibleFields = [
    'User Name' => $photographer->user?->name,
    'User Email' => $photographer->user?->email,
    'User Phone' => $photographer->user?->phone,
    'User Role' => $photographer->user?->role,
    'City' => $photographer->city?->name,
    'Verified Status' => $photographer->is_verified ? 'YES' : 'NO',
    'Profile Picture' => $photographer->profile_picture ? 'YES' : 'NO',
];

foreach ($visibleFields as $field => $value) {
    if ($value) {
        echo "  ✓ $field: $value\n";
    } else {
        echo "  ✗ $field: [EMPTY]\n";
    }
}

echo "\n❌ PHOTOGRAPHER PROFILE DATA (MISSING FROM ADMIN VIEW):\n";
$missingFields = [
    'Bio/Description' => $photographer->bio,
    'Location' => $photographer->location,
    'Experience Years' => $photographer->experience_years,
    'Specializations' => $photographer->specializations,
    'Service Area Radius' => $photographer->service_area_radius,
    'Starting Price' => $photographer->packages()->where('is_active', true)->min('base_price'),
    'Website URL' => $photographer->website_url,
    'Facebook URL' => $photographer->facebook_url,
    'Instagram URL' => $photographer->instagram_url,
    'Twitter URL' => $photographer->twitter_url,
    'LinkedIn URL' => $photographer->linkedin_url,
    'YouTube URL' => $photographer->youtube_url,
    'Average Rating' => $photographer->average_rating,
    'Rating Count' => $photographer->rating_count,
    'Total Bookings' => $photographer->total_bookings,
    'Completed Bookings' => $photographer->completed_bookings,
    'Featured' => $photographer->is_featured,
    'Portfolio Albums' => $photographer->albums->count(),
    'Active Packages' => $photographer->packages->count(),
    'Categories' => $photographer->categories->count(),
    'Reviews Count' => $photographer->reviews->count(),
    'Awards Count' => $photographer->awards->count(),
];

foreach ($missingFields as $field => $value) {
    if (is_numeric($value)) {
        $status = $value > 0 ? '⚠️ ' : '❌ ';
        echo "  $status $field: $value\n";
    } elseif ($value) {
        echo "  ⚠️ $field: [Has Data but not visible]\n";
    } else {
        echo "  ❌ $field: [Empty]\n";
    }
}

echo "\n\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║                          RECOMMENDATIONS                           ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

echo "🎯 WHAT ADMIN SHOULD BE ABLE TO DO:\n\n";

echo "PHASE 1 - Core Profile Data (Already Implemented):\n";
echo "  ✓ View Photographer Profile in User Modal\n";
echo "  ✓ Edit Bio, Website, Profile Image\n";
echo "  ✓ Toggle Verification Status\n";
echo "  ✓ Delete Photographer Profile\n\n";

echo "PHASE 2 - Extended Profile Management (NEW - RECOMMENDED):\n";
$recommendations = [
    'View/Edit Bio & Location' => 'DONE ✓',
    'View/Edit Experience Years' => 'ADD',
    'View/Edit Specializations' => 'ADD',
    'View/Edit Service Area Radius' => 'ADD',
    'View Portfolio Albums & Photos' => 'ADD',
    'View/Edit Service Packages' => 'ADD',
    'View/Edit Categories' => 'ADD',
    'View/Edit Social Media Links' => 'ADD',
    'Manage Verification Status' => 'DONE ✓',
    'View Trust Score' => 'ADD',
    'View Stats & Achievements' => 'ADD',
    'View Reviews & Ratings' => 'ADD',
    'View Booking History' => 'ADD',
    'View Competition Participations' => 'ADD',
];

foreach ($recommendations as $feature => $status) {
    $icon = $status === 'ADD' ? '⚠️ ' : '✓ ';
    echo "  $icon $feature [$status]\n";
}

echo "\n\nPHASE 3 - Advanced Management (FUTURE):\n";
$future = [
    'Bulk Edit Multiple Photographers' => 'Feature Enhancement',
    'Auto-fill Profile from API/Scraping' => 'Feature Enhancement',
    'Profile Audit Trail/History' => 'Feature Enhancement',
    'Profile Completeness Tracking' => 'Already Exists (10%)',
    'Automated Profile Improvement Suggestions' => 'Feature Enhancement',
];

foreach ($future as $feature => $type) {
    echo "  • $feature ($type)\n";
}

echo "\n\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║                         IMPLEMENTATION PLAN                        ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

echo "Files to Modify:\n";
echo "  1. resources/js/Pages/Admin/Users/Index.vue\n";
echo "     - Expand View Modal to show all profile data\n";
echo "     - Expand Edit Modal to include: experience, specializations,\n";
echo "       service radius, location, categories, social links\n";
echo "     - Add tabs for: Portfolio, Packages, Reviews, Stats\n\n";

echo "  2. app/Http/Controllers/Api/AdminController.php\n";
echo "     - Add methods to get full photographer details\n";
echo "     - Add methods to update extended profile fields\n\n";

echo "API Endpoints Needed:\n";
echo "  • GET /api/v1/admin/photographers/{id} - Full profile with all data\n";
echo "  • PUT /api/v1/admin/photographers/{id} - Update profile fields\n";
echo "  • GET /api/v1/admin/photographers/{id}/albums - Portfolio\n";
echo "  • GET /api/v1/admin/photographers/{id}/packages - Services\n";
echo "  • GET /api/v1/admin/photographers/{id}/reviews - Reviews\n";
echo "  • GET /api/v1/admin/photographers/{id}/stats - Statistics\n\n";

echo "\n";
