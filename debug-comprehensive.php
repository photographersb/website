<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Competition;
use App\Models\Category;
use App\Models\Sponsor;
use App\Models\Judge;
use Illuminate\Support\Facades\Hash;

echo "=== COMPREHENSIVE DEBUG REPORT ===\n\n";

// 1. Admin user status
$admin = User::where('email', 'mahidulislamnakib@gmail.com')->first();
echo "1. ADMIN USER STATUS:\n";
echo "   Email: " . ($admin ? $admin->email : 'NOT FOUND') . "\n";
echo "   Role: " . ($admin ? $admin->role : 'N/A') . "\n";
echo "   Password check (password123): " . ($admin && Hash::check('password123', $admin->password) ? 'VALID' : 'INVALID') . "\n\n";

// 2. Database data counts
echo "2. DATABASE DATA COUNTS:\n";
echo "   Total Categories: " . Category::count() . "\n";
echo "   Total Sponsors (all): " . Sponsor::count() . "\n";
echo "   Active Sponsors: " . Sponsor::where('status', 'active')->count() . "\n";
echo "   Total Judges (all): " . Judge::count() . "\n";
echo "   Active Judges: " . Judge::active()->count() . "\n";
echo "   Competitions: " . Competition::count() . "\n\n";

// 3. Sample data
echo "3. SAMPLE DATA:\n";

$sponsors = Sponsor::where('status', 'active')->limit(3)->get();
echo "   Active Sponsors:\n";
foreach ($sponsors as $sponsor) {
    echo "     - ID: {$sponsor->id} | Name: {$sponsor->name} | Status: {$sponsor->status}\n";
}

$judges = Judge::active()->limit(3)->get();
echo "   Active Judges:\n";
foreach ($judges as $judge) {
    echo "     - ID: {$judge->id} | Name: {$judge->name} | Is Active: " . ($judge->is_active ? 'yes' : 'no') . "\n";
}

$categories = Category::limit(3)->get();
echo "   Categories:\n";
foreach ($categories as $cat) {
    echo "     - ID: {$cat->id} | Name: {$cat->name} | Status: " . ($cat->status ?? 'N/A') . "\n";
}

echo "\n4. CREATE.VUE FILE STATUS:\n";
$createVueFile = file_get_contents('resources/js/Pages/Admin/Competitions/Create.vue');
echo "   File exists: YES\n";
echo "   Contains 'DEBUG MARKER': " . (strpos($createVueFile, 'DEBUG MARKER') !== false ? 'YES' : 'NO') . "\n";
echo "   Contains 'voting_start_at': " . (strpos($createVueFile, 'voting_start_at') !== false ? 'YES' : 'NO') . "\n";
echo "   Contains 'voting_end_at': " . (strpos($createVueFile, 'voting_end_at') !== false ? 'YES' : 'NO') . "\n";
echo "   Contains 'results_announcement_date': " . (strpos($createVueFile, 'results_announcement_date') !== false ? 'YES' : 'NO') . "\n";
echo "   Contains 'fetchCategories': " . (strpos($createVueFile, 'fetchCategories') !== false ? 'YES' : 'NO') . "\n";
echo "   Contains 'fetchAvailableSponsors': " . (strpos($createVueFile, 'fetchAvailableSponsors') !== false ? 'YES' : 'NO') . "\n";
echo "   Contains 'fetchAvailableJudges': " . (strpos($createVueFile, 'fetchAvailableJudges') !== false ? 'YES' : 'NO') . "\n";

echo "\n5. API ENDPOINTS STATUS:\n";

// Test categories endpoint
$categories = Category::all();
echo "   /api/v1/categories: " . ($categories->count() > 0 ? "OK ({$categories->count()} items)" : "NO DATA") . "\n";

// Test sponsors endpoint
$sponsors = Sponsor::where('status', 'active')->get();
echo "   /api/v1/admin/platform-sponsors: " . ($sponsors->count() > 0 ? "OK ({$sponsors->count()} items)" : "NO DATA") . "\n";

// Test judges endpoint
$judges = Judge::active()->get();
echo "   /api/v1/judges: " . ($judges->count() > 0 ? "OK ({$judges->count()} items)" : "NO DATA") . "\n";

echo "\n6. BUILD STATUS:\n";
echo "   Vite build exists: " . (is_dir('public/build') ? 'YES' : 'NO') . "\n";
echo "   manifest.json exists: " . (file_exists('public/build/manifest.json') ? 'YES' : 'NO') . "\n";
echo "   Create.vue compiled: " . (file_exists('public/build/manifest.json') && json_decode(file_get_contents('public/build/manifest.json'), true)['resources/js/Pages/Admin/Competitions/Create.vue'] ? 'YES' : '?') . "\n";

echo "\n=== END REPORT ===\n";
?>
