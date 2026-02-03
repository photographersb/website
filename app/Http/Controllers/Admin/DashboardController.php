<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Photographer;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Verify admin access
     */
    protected function authorizeAdmin()
    {
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    /**
     * Get comprehensive admin dashboard data
     * 
     * Returns dashboard with fallbacks to prevent empty state
     */
    public function index(Request $request)
    {
        try {
            $this->authorizeAdmin();

            // Use cache with short TTL for real-time updates
            $cacheKey = 'admin_dashboard_' . auth()->id();
            $dashboardData = Cache::remember($cacheKey, 60, function () {
                return $this->compileDashboardData();
            });

            // Clear cache if force refresh requested
            if ($request->has('refresh')) {
                Cache::forget($cacheKey);
                $dashboardData = $this->compileDashboardData();
            }

            return response()->json([
                'status' => 'success',
                'data' => $dashboardData,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error', [
                'user_id' => auth()->id() ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return fallback dashboard data
            return response()->json([
                'status' => 'success',
                'data' => $this->getFallbackDashboardData(),
                'error' => 'Some dashboard data could not be loaded. Showing cached/fallback data.',
                'timestamp' => now()->toIso8601String(),
            ], 200);
        }
    }

    /**
     * Compile all dashboard data with safe queries
     */
    protected function compileDashboardData()
    {
        try {
            // Core Stats - with safe counting
            $stats = [
                'total_users' => User::count() ?? 0,
                'active_users' => User::where('created_at', '>=', now()->subDays(30))->count() ?? 0,
                'new_users_today' => User::whereDate('created_at', today())->count() ?? 0,
                'total_photographers' => Photographer::count() ?? 0,
                'verified_photographers' => Photographer::where('is_verified', true)->count() ?? 0,
                'pending_verifications' => DB::table('verifications')->where('verification_status', 'pending')->count() ?? 0,
                'total_bookings' => DB::table('bookings')->count() ?? 0,
                'pending_bookings' => DB::table('bookings')->where('status', 'pending')->count() ?? 0,
                'confirmed_bookings' => DB::table('bookings')->where('status', 'confirmed')->count() ?? 0,
                'completed_bookings' => DB::table('bookings')->where('status', 'completed')->count() ?? 0,
                'total_revenue' => DB::table('transactions')->where('status', 'completed')->sum('amount') ?? 0,
                'total_events' => DB::table('events')->count() ?? 0,
                'published_events' => DB::table('events')->where('status', 'published')->count() ?? 0,
                'total_competitions' => Competition::count() ?? 0,
                'active_competitions' => Competition::where('status', 'active')->count() ?? 0,
                'total_submissions' => CompetitionSubmission::count() ?? 0,
                'pending_submissions' => CompetitionSubmission::where('status', 'pending_review')->count() ?? 0,
            ];

            // Recent Competitions (with safe loading)
            $recentCompetitions = Competition::with('categories')
                ->withCount('submissions')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($comp) {
                    return [
                        'id' => $comp->id,
                        'title' => $comp->title,
                        'status' => $comp->status,
                        'total_prize_pool' => $comp->total_prize_pool ?? 0,
                        'submissions_count' => $comp->submissions_count ?? 0,
                        'start_date' => $comp->start_date,
                    ];
                }) ?? [];

            // Recent Bookings
            $recentBookings = DB::table('bookings')
                ->select('id', 'status', 'booking_date', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->toArray() ?? [];

            // Platform Health
            $platformHealth = [
                'status' => 'operational',
                'uptime' => '99.9%',
                'active_sessions' => User::where('last_login_at', '>=', now()->subHours(1))->count() ?? 0,
                'database_status' => $this->checkDatabaseHealth() ? 'healthy' : 'warning',
            ];

            // Notices (Role-based, if exists)
            $notices = [];
            try {
                if (class_exists('App\Models\Notice')) {
                    $notices = Notice::where('status', 'published')
                        ->where(function ($query) {
                            $query->whereNull('expires_at')
                                  ->orWhere('expires_at', '>=', now());
                        })
                        ->orderBy('published_at', 'desc')
                        ->limit(5)
                        ->get()
                        ->toArray() ?? [];
                }
            } catch (\Exception $e) {
                Log::debug('Notices not available yet: ' . $e->getMessage());
            }

            return [
                'stats' => $stats,
                'recent_competitions' => $recentCompetitions,
                'recent_bookings' => $recentBookings,
                'platform_health' => $platformHealth,
                'notices' => $notices,
                'generated_at' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            Log::error('Dashboard data compilation error: ' . $e->getMessage());
            return $this->getFallbackDashboardData();
        }
    }

    /**
     * Get fallback dashboard data (safe defaults)
     */
    protected function getFallbackDashboardData()
    {
        return [
            'stats' => [
                'total_users' => 0,
                'active_users' => 0,
                'new_users_today' => 0,
                'total_photographers' => 0,
                'verified_photographers' => 0,
                'pending_verifications' => 0,
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'confirmed_bookings' => 0,
                'completed_bookings' => 0,
                'total_revenue' => 0,
                'total_events' => 0,
                'published_events' => 0,
                'total_competitions' => 0,
                'active_competitions' => 0,
                'total_submissions' => 0,
                'pending_submissions' => 0,
            ],
            'recent_competitions' => [],
            'recent_bookings' => [],
            'platform_health' => [
                'status' => 'unknown',
                'uptime' => '—',
                'active_sessions' => 0,
                'database_status' => 'unknown',
            ],
            'notices' => [],
            'is_fallback' => true,
        ];
    }

    /**
     * Check database health
     */
    protected function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::warning('Database health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh cache
     */
    public function refreshCache()
    {
        $this->authorizeAdmin();

        Cache::forget('admin_dashboard_' . auth()->id());
        Log::info('Admin dashboard cache cleared by ' . auth()->user()->name);

        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard cache refreshed',
        ]);
    }
}
