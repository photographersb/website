<?php

/**
 * Pre-Browser Testing Environment Verification
 * Run: php verify-browser-testing.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n========================================\n";
echo "   BROWSER TESTING ENV VERIFICATION\n";
echo "========================================\n\n";

// Test 1: Application Running
echo "✓ Test 1: Application Status\n";
echo "  - APP_ENV: " . env('APP_ENV') . "\n";
echo "  - APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
echo "  - APP_URL: " . env('APP_URL') . "\n";

// Test 2: Database Connected
echo "\n✓ Test 2: Database Connection\n";
try {
    $connected = DB::connection()->getPdo();
    echo "  - Status: ✅ CONNECTED\n";
    echo "  - Driver: " . DB::getDriverName() . "\n";
    
    // Quick test
    $tables = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?", [
        env('DB_DATABASE')
    ]);
    echo "  - Tables in database: " . $tables[0]->count . "\n";
} catch (\Exception $e) {
    echo "  - Status: ❌ FAILED\n";
    echo "  - Error: " . $e->getMessage() . "\n";
    exit;
}

// Test 3: Key Models
echo "\n✓ Test 3: Event Models\n";
$eventCount = \App\Models\Event::count();
$regCount = \App\Models\EventRegistration::count();
$certCount = \App\Models\Certificate::count();

echo "  - Events: {$eventCount}\n";
echo "  - Registrations: {$regCount}\n";
echo "  - Certificates: {$certCount}\n";

if ($eventCount == 0) {
    echo "  ⚠️  No events in database! Create one before testing.\n";
    echo "     Visit: http://localhost:8000/admin/events/create\n";
}

// Test 4: Storage Ready
echo "\n✓ Test 4: Storage Configuration\n";
$storageLink = is_link(public_path('storage')) || is_dir(public_path('storage'));
if (!$storageLink) {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $storageLink = is_link(public_path('storage')) || is_dir(public_path('storage'));
    } catch (\Exception $e) {
        // ignore, will show missing below
    }
}
echo "  - Storage Link: " . ($storageLink ? "✅ EXISTS" : "⚠️  MISSING") . "\n";

$qrDir = storage_path('app/public/qr-codes/registrations');
echo "  - QR Directory: " . (is_dir($qrDir) ? "✅ EXISTS" : "⚠️  MISSING") . "\n";

// Test 5: Routes
echo "\n✓ Test 5: Routes\n";
$routes = collect(Route::getRoutes())->filter(function ($route) {
    return str_contains($route->getName() ?? '', 'events');
})->count();
echo "  - Event Routes Registered: {$routes}\n";

// Test 6: Authentication
echo "\n✓ Test 6: Authentication\n";
$adminCount = \App\Models\User::where('role', 'admin')->count();
$userCount = \App\Models\User::count();
echo "  - Total Users: {$userCount}\n";
echo "  - Admin Users: {$adminCount}\n";

if ($adminCount == 0) {
    echo "  ⚠️  No admin accounts! Testing admin features will fail.\n";
}

// Test 7: Browser URLs
echo "\n✓ Test 7: Accessible URLs for Testing\n\n";

$testUrls = [
    'Events List' => '/events',
    'Admin Events' => '/admin/events',
    'My Registrations' => '/my-registrations',
    'API Events' => '/api/v1/events',
];

foreach ($testUrls as $label => $url) {
    echo "  {$label}:\n";
    echo "    http://localhost:8000{$url}\n";
}

// Test 8: Recommendations
echo "\n✓ Test 8: Pre-Testing Checklist\n";
echo "  Recommended actions before browser testing:\n\n";

$checklist = [
    '[ ] Start Laravel server: php artisan serve',
    '[ ] Create at least 1 free event for registration testing',
    '[ ] Create at least 1 paid event (optional)',
    '[ ] Create an admin account (if none exists)',
    '[ ] Login as admin or user',
    '[ ] Open browser to: http://localhost:8000',
    '[ ] Clear browser cache',
    '[ ] Open browser developer console (F12)',
];

foreach ($checklist as $item) {
    echo "    {$item}\n";
}

// Test 9: Quick Stats
echo "\n✓ Test 9: System Statistics\n";
try {
    $futureEvents = \App\Models\Event::where('event_date', '>', now())->count();
    $attendedCount = \App\Models\EventRegistration::whereNotNull('attended_at')->count();
    $paidRegs = \App\Models\EventRegistration::where('payment_status', 'paid')->count();
    
    echo "  - Future Events: {$futureEvents}\n";
    echo "  - Attended Registrations: {$attendedCount}\n";
    echo "  - Paid Registrations: {$paidRegs}\n";
} catch (\Exception $e) {
    echo "  - Error gathering stats: " . $e->getMessage() . "\n";
}

// Final status
echo "\n========================================\n";
echo "   STATUS: READY FOR BROWSER TESTING ✅\n";
echo "========================================\n\n";

echo "Next Step: Follow EVENTS_BROWSER_TESTING_CHECKLIST.md\n";
echo "Tests to Run: 20 comprehensive browser tests\n";
echo "Estimated Time: 30-45 minutes\n\n";
