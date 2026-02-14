<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

$navLinks = [
    '/admin/users',
    '/admin/photographers',
    '/admin/verifications',
    '/admin/bookings',
    '/admin/competitions',
    '/admin/mentors',
    '/admin/judges',
    '/admin/events',
    '/admin/reviews',
    '/admin/transactions',
    '/admin/activity-logs',
    '/admin/sponsors',
    '/admin/categories',
    '/admin/cities',
    '/admin/seo',
    '/admin/contact-messages',
    '/admin/notices',
    '/admin/settings',
    '/admin/notifications',
    '/admin/error-center',
    '/admin/audit-logs',
    '/admin/share-frames',
    '/admin/hashtags',
];

echo "=== ADMIN QUICK NAV ROUTE VERIFICATION ===\n\n";

foreach ($navLinks as $link) {
    $route = Route::getRoutes()->match(
        \Illuminate\Http\Request::create($link, 'GET')
    );
    
    if ($route) {
        $name = $route->getName() ?: '(unnamed)';
        $action = $route->getActionName();
        $action = str_replace('App\\Http\\Controllers\\', '', $action);
        echo "✓ $link\n";
        echo "  → $name\n";
        echo "  → $action\n\n";
    } else {
        echo "✗ $link - ROUTE NOT FOUND\n\n";
    }
}
