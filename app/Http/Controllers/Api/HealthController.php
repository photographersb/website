<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    use ApiResponse;
    /**
     * GET /api/v1/health
     * Public health check endpoint
     */
    public function check()
    {
        try {
            DB::connection()->getPdo();
            $database = 'ok';
        } catch (\Exception $e) {
            $database = 'error: ' . $e->getMessage();
        }

        $cache = 'ok';
        try {
            Cache::put('health_check', 'ok', 1);
        } catch (\Exception $e) {
            $cache = 'error: ' . $e->getMessage();
        }

        return $this->success([
            'database' => $database,
            'cache' => $cache,
            'uptime' => intval(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']),
        ], 'System is healthy', 200, ['timestamp' => now()->toIso8601String()]);
    }

    /**
     * GET /api/v1/admin/health/detailed
     * Detailed admin health check
     */
    public function admin()
    {
        $this->authorize('isAdmin');

        try {
            DB::connection()->getPdo();
            $database = 'ok';
        } catch (\Exception $e) {
            $database = 'error: ' . $e->getMessage();
        }

        return $this->success([
            'database' => $database,
            'active_users' => \App\Models\User::where('is_suspended', false)->count(),
            'total_photographers' => \App\Models\Photographer::count(),
            'active_competitions' => \App\Models\Competition::where('status', 'active')->count(),
            'pending_approvals' => \App\Models\User::where('approval_status', 'pending')->count(),
            'failed_jobs' => DB::table('failed_jobs')->count() ?? 0,
        ], 'System health status retrieved successfully', 200, ['timestamp' => now()->toIso8601String()]);
    }
}
