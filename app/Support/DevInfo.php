<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class DevInfo
{
    /**
     * Check if dev mode is enabled
     */
    public static function isDevMode(): bool
    {
        return config('app.env') !== 'production' && config('app.debug') === true;
    }

    /**
     * Get git commit hash (short)
     */
    public static function getGitCommit(): string
    {
        try {
            if (function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
                $commit = shell_exec('git rev-parse --short HEAD 2>&1');
                if ($commit && !str_contains($commit, 'not a git repository')) {
                    return trim($commit);
                }
            }
        } catch (\Exception $e) {
            // Silent fail
        }
        
        return 'N/A';
    }

    /**
     * Get current route name
     */
    public static function getRouteName(): string
    {
        $route = Route::current();
        return $route ? ($route->getName() ?? 'unnamed') : 'unknown';
    }

    /**
     * Get current route action (controller@method)
     */
    public static function getRouteAction(): string
    {
        $route = Route::current();
        if (!$route) {
            return 'N/A';
        }

        $action = $route->getAction();
        
        if (isset($action['controller'])) {
            $controller = $action['controller'];
            
            // Extract class name and method
            if (is_string($controller) && str_contains($controller, '@')) {
                [$class, $method] = explode('@', $controller);
                $shortClass = class_basename($class);
                return "{$shortClass}@{$method}";
            }
            
            if (is_string($controller)) {
                return class_basename($controller);
            }
        }

        return 'Closure';
    }

    /**
     * Get all dev info as array
     */
    public static function getDebugInfo(): array
    {
        return [
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'url' => request()->url(),
            'route' => self::getRouteName(),
            'action' => self::getRouteAction(),
            'commit' => self::getGitCommit(),
            'build' => config('app.build_version', 'N/A'),
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Render debug badge HTML
     */
    public static function renderDebugBadge(): string
    {
        if (!self::isDevMode()) {
            return '';
        }

        $info = self::getDebugInfo();
        
        return view('components.dev-badge', compact('info'))->render();
    }

    /**
     * Render route marker comment
     */
    public static function renderRouteMarker(): string
    {
        if (!self::isDevMode()) {
            return '';
        }

        $route = self::getRouteName();
        $action = self::getRouteAction();
        
        return "<!-- DEBUG-ROUTE: {$route} | ACTION: {$action} -->";
    }
}
