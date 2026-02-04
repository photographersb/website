<?php

namespace App\Services;

use App\Models\AdminSitemapCheck;
use App\Models\AdminSitemapCheckResult;
use App\Models\Competition;
use App\Models\Event;
use App\Models\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Testing\TestResponse;

class AdminSitemapTestService
{
    /**
     * Routes to exclude from testing
     */
    private const EXCLUDED_ROUTES = [
        'logout',
        'delete',
        'destroy',
        'verify-email',
        'password.reset',
        'password.email',
        'password.update',
        'password.request',
        'reset',
        'forgot-password',
        'confirm-password',
        'profile.delete',
    ];

    /**
     * Routes that require specific parameters
     */
    private const PARAMETERIZED_ROUTES = [
        'competitions.edit' => 'competitions',
        'competitions.show' => 'competitions',
        'competitions.submissions' => 'competitions',
        'competitions.judging' => 'competitions',
        'events.edit' => 'events',
        'events.show' => 'events',
        'users.edit' => 'users',
        'users.show' => 'users',
    ];

    /**
     * Start a new sitemap check
     */
    public function startCheck(int $userId): AdminSitemapCheck
    {
        return AdminSitemapCheck::create([
            'run_by_user_id' => $userId,
            'started_at' => now(),
            'total_links' => 0,
            'passed' => 0,
            'failed' => 0,
            'skipped' => 0,
        ]);
    }

    /**
     * Get all admin routes
     */
    public function getAdminRoutes(): array
    {
        $routes = [];
        $allRoutes = RouteFacade::getRoutes();

        foreach ($allRoutes as $route) {
            // Only GET routes
            if (!in_array('GET', $route->methods)) {
                continue;
            }

            // Only admin routes
            if (!str_starts_with($route->uri, 'admin/')) {
                continue;
            }

            // Skip excluded routes
            if ($this->shouldExcludeRoute($route)) {
                continue;
            }

            $module = $this->extractModule($route->uri);

            $routes[] = [
                'module' => $module,
                'route_name' => $route->getName() ?? 'unnamed',
                'uri' => $route->uri,
                'controller' => $this->formatController($route),
                'methods' => implode(',', $route->methods),
            ];
        }

        return array_values(array_unique($routes, SORT_REGULAR));
    }

    /**
     * Run all tests for a check
     */
    public function runAllTests(AdminSitemapCheck $check, $user): void
    {
        try {
            $routes = $this->getAdminRoutes();
            $check->update(['total_links' => count($routes)]);

            foreach ($routes as $route) {
                try {
                    $this->testRoute($check, $route, $user);
                } catch (\Exception $e) {
                    Log::error("Error testing route {$route['uri']}: {$e->getMessage()}");
                }
            }

            // Recalculate totals
            $this->updateCheckTotals($check);
            $check->update(['finished_at' => now()]);

        } catch (\Exception $e) {
            Log::error("AdminSitemapTestService error: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Test a single route
     */
    private function testRoute(AdminSitemapCheck $check, array $route, $user): void
    {
        $url = $this->buildUrl($route['uri']);
        $startTime = microtime(true);

        try {
            // Handle parameterized routes
            $parameterizedUrl = $this->resolveParameterizedRoute($route['uri']);
            
            if ($parameterizedUrl === null) {
                // Route needs parameters but none available
                AdminSitemapCheckResult::create([
                    'check_id' => $check->id,
                    'module' => $route['module'],
                    'route_name' => $route['route_name'],
                    'url' => $url,
                    'method' => 'GET',
                    'status_code' => null,
                    'response_time_ms' => 0,
                    'result_status' => 'skipped',
                    'error_summary' => 'No data available for parameterized route',
                ]);
                return;
            }

            if ($parameterizedUrl !== $url) {
                $url = $parameterizedUrl;
            }

            // Make the request
            $response = $this->makeRequest($url, $user);
            $responseTime = (int)((microtime(true) - $startTime) * 1000);

            // Determine result
            $status = $response->getStatusCode();
            $resultStatus = $this->determineResult($status, $response);
            $errorSummary = null;

            if ($resultStatus === 'failed') {
                $errorSummary = $this->getErrorMessage($status, $response);
            }

            AdminSitemapCheckResult::create([
                'check_id' => $check->id,
                'module' => $route['module'],
                'route_name' => $route['route_name'],
                'url' => $url,
                'method' => 'GET',
                'status_code' => $status,
                'response_time_ms' => $responseTime,
                'result_status' => $resultStatus,
                'error_summary' => $errorSummary,
            ]);

        } catch (\Exception $e) {
            $responseTime = (int)((microtime(true) - $startTime) * 1000);
            
            AdminSitemapCheckResult::create([
                'check_id' => $check->id,
                'module' => $route['module'],
                'route_name' => $route['route_name'],
                'url' => $url,
                'method' => 'GET',
                'status_code' => null,
                'response_time_ms' => $responseTime,
                'result_status' => 'failed',
                'error_summary' => substr($e->getMessage(), 0, 500),
            ]);
        }
    }

    /**
     * Resolve parameterized routes with actual IDs
     */
    private function resolveParameterizedRoute(string $uri): ?string
    {
        // Check if route has parameters
        if (!str_contains($uri, '{')) {
            return $uri;
        }

        // Extract parameter names
        preg_match_all('/{([^}]+)}/', $uri, $matches);
        $params = $matches[1] ?? [];

        if (empty($params)) {
            return $uri;
        }

        $resolved = $uri;

        foreach ($params as $param) {
            $value = $this->getParameterValue($param);

            if ($value === null) {
                return null; // Cannot resolve this parameter
            }

            $resolved = str_replace("{{$param}}", $value, $resolved);
        }

        return $resolved;
    }

    /**
     * Get a valid parameter value from database
     */
    private function getParameterValue(string $param): ?string
    {
        return match($param) {
            'competition' => Competition::latest()->first()?->id,
            'event' => Event::latest()->first()?->id,
            'user' => User::where('role', '!=', 'super_admin')->latest()->first()?->id,
            'id' => User::where('role', '!=', 'super_admin')->latest()->first()?->id,
            default => null,
        };
    }

    /**
     * Make HTTP request to route
     */
    private function makeRequest(string $url, $user): TestResponse
    {
        // Remove leading slash if present
        $path = ltrim($url, '/');

        // Extract path without domain
        if (str_starts_with($path, 'http')) {
            $parsed = parse_url($path);
            $path = $parsed['path'] ?? '/';
            $path = ltrim($path, '/');
        }

        // Use Laravel testing utilities
        $response = app('Illuminate\Testing\TestCase')->get('/' . $path);

        // For unauthorized/forbidden, return response
        return $response;
    }

    /**
     * Determine if test passed or failed
     */
    private function determineResult(int $status, $response): string
    {
        // Success codes
        if ($status >= 200 && $status < 300) {
            // Check for blank body
            if ($this->isBlankResponse($response)) {
                return 'failed'; // Blank response considered failure
            }
            return 'passed';
        }

        // Redirects are acceptable (3xx)
        if ($status >= 300 && $status < 400) {
            return 'passed';
        }

        // Client/server errors
        return 'failed';
    }

    /**
     * Check if response is blank
     */
    private function isBlankResponse($response): bool
    {
        $content = $response->getContent();
        
        // Consider blank if less than 100 chars or no HTML structure
        return strlen(trim($content)) < 100 || !str_contains($content, 'html');
    }

    /**
     * Get error message for failed status code
     */
    private function getErrorMessage(int $status, $response): string
    {
        return match($status) {
            404 => 'Route not found (404)',
            403 => 'Access denied (403)',
            401 => 'Unauthorized (401)',
            500 => 'Internal server error (500)',
            503 => 'Service unavailable (503)',
            default => "HTTP {$status}",
        };
    }

    /**
     * Extract module name from URI
     */
    private function extractModule(string $uri): string
    {
        $parts = explode('/', $uri);
        return $parts[1] ?? 'system';
    }

    /**
     * Format controller name
     */
    private function formatController(Route $route): string
    {
        if (!$route->getController()) {
            return 'unknown';
        }

        $controller = $route->getController();
        $class = class_basename($controller);
        $method = $route->getActionMethod();

        return "{$class}@{$method}";
    }

    /**
     * Build full URL from URI
     */
    private function buildUrl(string $uri): string
    {
        return url($uri);
    }

    /**
     * Check if route should be excluded
     */
    private function shouldExcludeRoute(Route $route): bool
    {
        $name = $route->getName() ?? '';
        $uri = $route->uri;
        $methods = $route->methods;

        // Skip destructive methods
        foreach ($methods as $method) {
            if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                return true;
            }
        }

        // Skip excluded route names
        foreach (self::EXCLUDED_ROUTES as $excluded) {
            if (str_contains($name, $excluded) || str_contains($uri, $excluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update check totals from results
     */
    private function updateCheckTotals(AdminSitemapCheck $check): void
    {
        $results = $check->results();

        $check->update([
            'passed' => $results->where('result_status', 'passed')->count(),
            'failed' => $results->where('result_status', 'failed')->count(),
            'skipped' => $results->where('result_status', 'skipped')->count(),
        ]);
    }
}
