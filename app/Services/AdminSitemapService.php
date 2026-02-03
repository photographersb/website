<?php

namespace App\Services;

use App\Models\AdminSitemapCheck;
use App\Models\AdminSitemapCheckResult;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminSitemapService
{
    /**
     * Module grouping for admin routes
     */
    private const MODULES = [
        'dashboard' => 'Dashboard',
        'users' => 'Users',
        'roles' => 'Roles',
        'photographers' => 'Photographers',
        'bookings' => 'Bookings',
        'events' => 'Events',
        'competitions' => 'Competitions',
        'sponsors' => 'Sponsors',
        'mentors' => 'Mentors',
        'judges' => 'Judges',
        'notices' => 'Notices',
        'seo' => 'SEO',
        'settings' => 'Settings',
        'system' => 'System Health',
        'logs' => 'Error Logs',
    ];

    /**
     * Routes to exclude from testing
     */
    private const EXCLUDED_ROUTES = [
        'logout',
        'admin.logout',
        'auth.logout',
        'admin.sitemap', // Exclude sitemap routes to prevent testing itself
        'sitemap',
    ];

    /**
     * Get all admin sitemap links
     */
    public function getSitemapLinks(): array
    {
        $routes = Route::getRoutes();
        $adminLinks = [];

        foreach ($routes as $route) {
            // Only GET routes
            if (!in_array('GET', $route->methods) && !in_array('HEAD', $route->methods)) {
                continue;
            }

            $uri = $route->uri;

            // Check if admin API route
            if (!str_contains($uri, '/admin/')) {
                continue;
            }

            // Exclude sitemap routes to prevent recursion
            if (str_contains($uri, '/sitemap')) {
                continue;
            }

            // Skip routes with required parameters (e.g., {id})
            if ($this->hasRequiredParameters($route)) {
                continue;
            }

            $module = $this->getModule($uri);
            $routeName = $route->getName() ?? $uri;

            $adminLinks[] = [
                'module' => $module,
                'link_name' => $this->generateLinkName($uri),
                'method' => 'GET',
                'url' => '/' . $uri,
                'route_name' => $routeName,
                'middleware' => implode(', ', $route->middleware()),
                'controller' => $this->getControllerMethod($route),
                'requires_params' => $this->hasOptionalParameters($route),
            ];
        }

        // Sort by module then by link name
        usort($adminLinks, function ($a, $b) {
            if ($a['module'] !== $b['module']) {
                return strcmp($a['module'], $b['module']);
            }
            return strcmp($a['link_name'], $b['link_name']);
        });

        return $adminLinks;
    }

    /**
     * Run comprehensive link tests
     */
    public function runLinkTests(User $user): AdminSitemapCheck
    {
        $check = AdminSitemapCheck::create([
            'started_by_user_id' => $user->id,
            'started_at' => now(),
            'status' => 'running'
        ]);

        try {
            $links = $this->getSitemapLinks();
            $check->total_links = count($links);
            $check->save();

            foreach ($links as $link) {
                $this->testLink($check, $link);
            }

            // Update counts
            $results = $check->results()->get();
            $check->passed_links = $results->where('result_status', 'passed')->count();
            $check->failed_links = $results->where('result_status', 'failed')->count();
            $check->skipped_links = $results->where('result_status', 'skipped')->count();

            $check->markCompleted();

        } catch (Exception $e) {
            $check->markFailed("Error during sitemap test: " . $e->getMessage());
        }

        return $check;
    }

    /**
     * Test a single link
     */
    private function testLink(AdminSitemapCheck $check, array $link): void
    {
        $startTime = microtime(true);

        try {
            // Get base URL
            $url = config('app.url') . $link['url'];

            // Make HTTP request with auth session
            $response = Http::withCookies(
                auth()->user() ? $this->getSessionCookies() : [],
                'localhost'
            )->timeout(10)->get($url);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            $statusCode = $response->status();
            $hasBlankBody = empty(trim($response->body()));

            // Determine result
            if ($statusCode >= 200 && $statusCode < 300) {
                $resultStatus = 'passed';
                $errorSummary = null;
            } elseif ($statusCode === 302 || $statusCode === 301) {
                $resultStatus = 'passed';
                $errorSummary = "Redirect ({$statusCode})";
            } elseif ($statusCode === 403) {
                $resultStatus = 'failed';
                $errorSummary = 'Access Denied (403)';
            } elseif ($statusCode === 404) {
                $resultStatus = 'failed';
                $errorSummary = 'Not Found (404)';
            } elseif ($statusCode >= 500) {
                $resultStatus = 'failed';
                $errorSummary = "Server Error ({$statusCode})";
            } else {
                $resultStatus = 'failed';
                $errorSummary = "Unexpected Status ({$statusCode})";
            }

            if ($hasBlankBody && $statusCode === 200) {
                $resultStatus = 'failed';
                $errorSummary = 'Blank Response Body (200)';
            }

        } catch (Exception $e) {
            $resultStatus = 'failed';
            $statusCode = null;
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            $errorSummary = $e->getMessage();
        }

        // Store result
        AdminSitemapCheckResult::create([
            'check_id' => $check->id,
            'route_name' => $link['route_name'],
            'url' => $link['url'],
            'method' => $link['method'],
            'module' => $link['module'],
            'status_code' => $statusCode,
            'response_time_ms' => $responseTime,
            'result_status' => $resultStatus,
            'error_summary' => $errorSummary,
            'has_blank_body' => $hasBlankBody ?? false
        ]);
    }

    /**
     * Get module from URI
     */
    private function getModule(string $uri): string
    {
        // Handle API routes: api/v1/admin/bookings -> bookings
        if (str_contains($uri, 'api/') && str_contains($uri, '/admin/')) {
            $afterAdmin = explode('/admin/', $uri)[1] ?? '';
            $parts = explode('/', $afterAdmin);
            $key = $parts[0] ?? 'system';
        } else {
            // Extract first part after 'admin/'
            $parts = explode('/', str_replace('admin/', '', $uri));
            $key = $parts[0] ?? 'system';
        }

        // Clean up key (remove parameters, special chars)
        $key = preg_replace('/\{.*?\}/', '', $key);
        $key = trim($key, '/');
        
        return self::MODULES[$key] ?? ucfirst($key);
    }

    /**
     * Generate friendly link name from URI
     */
    private function generateLinkName(string $uri): string
    {
        // Handle API routes
        if (str_contains($uri, 'api/v1/admin/')) {
            $name = str_replace('api/v1/admin/', '', $uri);
        } else {
            $name = str_replace('admin/', '', $uri);
        }

        // Remove route parameters {id}, {check}, etc.
        $name = preg_replace('/\{[^}]+\}/', '', $name);
        
        // Clean up slashes
        $name = trim($name, '/');
        
        // Replace slashes, underscores, dashes with spaces
        $name = str_replace(['/', '_', '-'], ' ', $name);
        
        // Remove extra spaces
        $name = preg_replace('/\s+/', ' ', $name);
        
        // Capitalize
        $name = ucwords(trim($name));
        
        return $name ?: 'Dashboard';
    }

    /**
     * Get controller@method from route
     */
    private function getControllerMethod($route): string
    {
        $action = $route->getActionName();

        if ($action === 'Closure') {
            return 'Closure';
        }

        return $action ?? 'Unknown';
    }

    /**
     * Check if route is excluded
     */
    private function isExcludedRoute($route): bool
    {
        $name = $route->getName() ?? '';

        foreach (self::EXCLUDED_ROUTES as $excluded) {
            if (str_contains($name, $excluded) || str_contains($route->uri, $excluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if route has required parameters
     */
    private function hasRequiredParameters($route): bool
    {
        $uri = $route->uri;

        // Check for parameters like {id}, {slug}, etc.
        return preg_match('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', $uri) > 0;
    }

    /**
     * Check if route has optional parameters
     */
    private function hasOptionalParameters($route): bool
    {
        $uri = $route->uri;

        // Check for optional parameters like {id?}
        return preg_match('/\{[a-zA-Z_][a-zA-Z0-9_]*\?\}/', $uri) > 0;
    }

    /**
     * Get session cookies for authenticated requests
     */
    private function getSessionCookies(): array
    {
        // In a real scenario, you'd use the current session
        // For API testing, you might use a token instead
        return [];
    }

    /**
     * Get recent checks
     */
    public function getRecentChecks(int $limit = 10): array
    {
        return AdminSitemapCheck::orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get check results with filters
     */
    public function getCheckResults(
        AdminSitemapCheck $check,
        ?string $module = null,
        ?string $status = null,
        ?int $statusCode = null,
        ?string $search = null
    ) {
        $query = $check->results();

        if ($module) {
            $query->where('module', $module);
        }

        if ($status) {
            $query->where('result_status', $status);
        }

        if ($statusCode) {
            $query->where('status_code', $statusCode);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                  ->orWhere('route_name', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('module')->orderBy('url')->paginate(50);
    }
}
