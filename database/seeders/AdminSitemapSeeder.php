<?php

namespace Database\Seeders;

use App\Models\AdminSitemapCheck;
use App\Models\AdminSitemapCheckResult;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSitemapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $adminUser = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
        
        if (!$adminUser) {
            $this->command->warn('No admin user found. Create an admin user first.');
            return;
        }

        // Create a sample check
        $check = AdminSitemapCheck::create([
            'started_by_user_id' => $adminUser->id,
            'started_at' => now()->subHours(2),
            'finished_at' => now()->subHours(1, 50),
            'total_links' => 12,
            'passed_links' => 9,
            'failed_links' => 2,
            'skipped_links' => 1,
            'status' => 'completed',
            'error_summary' => '2 failed links: Dashboard stat API (500), Settings page (404)',
        ]);

        // Sample passed results
        $passedRoutes = [
            ['route' => 'admin.dashboard', 'url' => '/admin/dashboard', 'method' => 'GET', 'module' => 'Dashboard', 'code' => 200, 'time' => 145],
            ['route' => 'admin.users.index', 'url' => '/admin/users', 'method' => 'GET', 'module' => 'Users', 'code' => 200, 'time' => 234],
            ['route' => 'admin.users.create', 'url' => '/admin/users/create', 'method' => 'GET', 'module' => 'Users', 'code' => 200, 'time' => 89],
            ['route' => 'admin.photographers.index', 'url' => '/admin/photographers', 'method' => 'GET', 'module' => 'Photographers', 'code' => 200, 'time' => 456],
            ['route' => 'admin.bookings.index', 'url' => '/admin/bookings', 'method' => 'GET', 'module' => 'Bookings', 'code' => 302, 'time' => 123],
            ['route' => 'admin.events.index', 'url' => '/admin/events', 'method' => 'GET', 'module' => 'Events', 'code' => 200, 'time' => 234],
            ['route' => 'admin.roles.index', 'url' => '/admin/roles', 'method' => 'GET', 'module' => 'Roles', 'code' => 200, 'time' => 156],
            ['route' => 'admin.sitemap.index', 'url' => '/admin/sitemap', 'method' => 'GET', 'module' => 'System Health', 'code' => 200, 'time' => 567],
            ['route' => 'admin.sponsors.index', 'url' => '/admin/sponsors', 'method' => 'GET', 'module' => 'Sponsors', 'code' => 200, 'time' => 289],
        ];

        foreach ($passedRoutes as $route) {
            AdminSitemapCheckResult::create([
                'check_id' => $check->id,
                'route_name' => $route['route'],
                'url' => $route['url'],
                'method' => $route['method'],
                'module' => $route['module'],
                'status_code' => $route['code'],
                'response_time_ms' => $route['time'],
                'result_status' => 'passed',
                'error_summary' => null,
                'error_details' => null,
                'has_blank_body' => false,
            ]);
        }

        // Sample failed results
        $failedRoutes = [
            [
                'route' => 'admin.api.dashboard-stats',
                'url' => '/admin/api/dashboard-stats',
                'method' => 'GET',
                'module' => 'Dashboard',
                'code' => 500,
                'time' => 0,
                'error' => 'Server Error: /admin/api/dashboard-stats returned 500 (Internal Server Error)',
                'details' => 'SQLSTATE[42S22]: Column not found: 1054 Unknown column \'photographers.average_rating\' in \'order clause\'. Make sure the column exists in the photographers table.',
            ],
            [
                'route' => 'admin.settings.index',
                'url' => '/admin/settings',
                'method' => 'GET',
                'module' => 'Settings',
                'code' => 404,
                'time' => 234,
                'error' => 'Route not found',
                'details' => 'GET /admin/settings returned 404 Not Found. Route may have been renamed or removed.',
            ],
            [
                'route' => 'admin.permissions.create',
                'url' => '/admin/permissions/create',
                'method' => 'GET',
                'module' => 'Roles',
                'code' => 403,
                'time' => 78,
                'error' => 'Forbidden: User does not have permission',
                'details' => 'Access denied. Your role does not have permission to access this resource.',
            ],
        ];

        foreach ($failedRoutes as $route) {
            AdminSitemapCheckResult::create([
                'check_id' => $check->id,
                'route_name' => $route['route'],
                'url' => $route['url'],
                'method' => $route['method'],
                'module' => $route['module'],
                'status_code' => $route['code'],
                'response_time_ms' => $route['time'],
                'result_status' => 'failed',
                'error_summary' => $route['error'],
                'error_details' => $route['details'],
                'has_blank_body' => $route['code'] === 500,
            ]);
        }

        $this->command->info('Admin Sitemap seeder completed with sample check and results.');
        $this->command->info("Check ID: {$check->id}");
        $this->command->info("Total links: 12 | Passed: 9 | Failed: 3 | Skipped: 0");
    }
}
