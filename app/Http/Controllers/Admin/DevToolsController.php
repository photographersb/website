<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DevToolsController extends Controller
{
    /**
     * Middleware to restrict access
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Only accessible in non-production AND user is super admin
            if (config('app.env') === 'production') {
                abort(404);
            }

            if (!auth()->check() || auth()->user()->role !== 'super_admin') {
                abort(403, 'Access denied. Super Admin only.');
            }

            return $next($request);
        });
    }

    /**
     * Dev tools dashboard
     */
    public function index()
    {
        $info = [
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'git_commit' => $this->getGitCommit(),
            'git_branch' => $this->getGitBranch(),
            'build_version' => config('app.build_version', 'N/A'),
            'vite_enabled' => $this->checkViteEnabled(),
            'storage_writable' => is_writable(storage_path()),
            'cache_writable' => is_writable(storage_path('framework/cache')),
        ];

        return view('admin.dev-tools.index', compact('info'));
    }

    /**
     * Clear all caches
     */
    public function clearCache()
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return back()->with('success', '✅ All caches cleared successfully! (optimize, cache, config, route, view)');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error clearing cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear view cache only
     */
    public function clearViewCache()
    {
        try {
            Artisan::call('view:clear');
            return back()->with('success', '✅ View cache cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error clearing view cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear config cache
     */
    public function clearConfigCache()
    {
        try {
            Artisan::call('config:clear');
            return back()->with('success', '✅ Config cache cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error clearing config cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear route cache
     */
    public function clearRouteCache()
    {
        try {
            Artisan::call('route:clear');
            return back()->with('success', '✅ Route cache cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error clearing route cache: ' . $e->getMessage());
        }
    }

    /**
     * Rebuild assets info
     */
    public function assetsInfo()
    {
        $publicPath = public_path('build');
        $manifestPath = public_path('build/manifest.json');

        $info = [
            'vite_enabled' => $this->checkViteEnabled(),
            'build_exists' => File::exists($publicPath),
            'manifest_exists' => File::exists($manifestPath),
            'manifest_content' => File::exists($manifestPath) ? json_decode(File::get($manifestPath), true) : null,
            'build_time' => File::exists($manifestPath) ? date('Y-m-d H:i:s', File::lastModified($manifestPath)) : 'N/A',
        ];

        return back()->with('info', json_encode($info, JSON_PRETTY_PRINT));
    }

    /**
     * Get git commit hash
     */
    private function getGitCommit(): string
    {
        try {
            if (function_exists('shell_exec')) {
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
     * Get git branch
     */
    private function getGitBranch(): string
    {
        try {
            if (function_exists('shell_exec')) {
                $branch = shell_exec('git rev-parse --abbrev-ref HEAD 2>&1');
                if ($branch && !str_contains($branch, 'not a git repository')) {
                    return trim($branch);
                }
            }
        } catch (\Exception $e) {
            // Silent fail
        }
        
        return 'N/A';
    }

    /**
     * Check if Vite is enabled
     */
    private function checkViteEnabled(): bool
    {
        return File::exists(public_path('build/manifest.json'));
    }
}
