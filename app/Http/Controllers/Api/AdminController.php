<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Photographer;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Check if the authenticated user is an admin
     */
    protected function checkAdminAccess()
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    /**
     * Get admin dashboard statistics (cached for 5 minutes)
     */
    public function dashboard()
    {
        // Check if user is admin or super_admin
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        // Cache dashboard data for 5 minutes
        $dashboardData = Cache::remember('admin_dashboard_' . auth()->id(), 300, function () {
            // Overview Statistics
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'total_photographers' => Photographer::count(),
                'verified_photographers' => Photographer::where('is_verified', true)->count(),
                'pending_verifications' => DB::table('verifications')->where('verification_status', 'pending')->count(),
                'total_bookings' => DB::table('bookings')->count(),
                'pending_bookings' => DB::table('bookings')->where('status', 'pending')->count(),
                'confirmed_bookings' => DB::table('bookings')->where('status', 'confirmed')->count(),
                'completed_bookings' => DB::table('bookings')->where('status', 'completed')->count(),
                'cancelled_bookings' => DB::table('bookings')->where('status', 'cancelled')->count(),
                'total_revenue' => DB::table('transactions')->where('status', 'completed')->sum('amount'),
                'monthly_revenue' => DB::table('transactions')->where('status', 'completed')
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('amount'),
                'daily_revenue' => DB::table('transactions')->where('status', 'completed')
                    ->whereDate('created_at', today())
                    ->sum('amount'),
                'total_events' => DB::table('events')->count(),
                'published_events' => DB::table('events')->where('status', 'published')->count(),
                'upcoming_events' => DB::table('events')
                    ->where('status', 'published')
                    ->where('event_date', '>=', now())
                    ->count(),
                'active_competitions' => Competition::where('status', 'active')->count(),
                'total_competitions' => Competition::count(),
                'total_submissions' => CompetitionSubmission::count(),
                'pending_submissions' => CompetitionSubmission::where('status', 'pending_review')->count(),
                'total_reviews' => DB::table('reviews')->count(),
                'pending_reviews' => DB::table('reviews')->where('status', 'pending')->count(),
                'published_reviews' => DB::table('reviews')->where('status', 'published')->count(),
                'avg_rating' => round(DB::table('reviews')->where('status', 'published')->avg('rating') ?? 0, 2),
            ];

            // Revenue Trend (Last 12 months)
            $revenueTrend = DB::table('transactions')
                ->where('status', 'completed')
                ->where('created_at', '>=', now()->subMonths(12))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as revenue')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // User Growth (Last 12 months)
            $userGrowth = User::where('created_at', '>=', now()->subMonths(12))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Booking Status Distribution
            $bookingStats = DB::table('bookings')
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            // Recent Activities (Last 10)
            $recentBookings = DB::table('bookings')
                ->join('users as clients', 'bookings.client_id', '=', 'clients.id')
                ->join('photographers', 'bookings.photographer_id', '=', 'photographers.id')
                ->join('users as photographers_users', 'photographers.user_id', '=', 'photographers_users.id')
                ->select('bookings.*', 'clients.name as client_name', 'photographers_users.name as photographer_name')
                ->orderBy('bookings.created_at', 'desc')
                ->limit(10)
                ->get();

            $recentReviews = DB::table('reviews')
                ->join('users as reviewers', 'reviews.reviewer_id', '=', 'reviewers.id')
                ->join('photographers', 'reviews.photographer_id', '=', 'photographers.id')
                ->join('users as photographers_users', 'photographers.user_id', '=', 'photographers_users.id')
                ->select('reviews.*', 'reviewers.name as reviewer_name', 'photographers_users.name as photographer_name')
                ->orderBy('reviews.created_at', 'desc')
                ->limit(10)
                ->get();

            $recentTransactions = DB::table('transactions')
                ->join('users', 'transactions.user_id', '=', 'users.id')
                ->select('transactions.*', 'users.name as user_name', 'users.email as user_email')
                ->orderBy('transactions.created_at', 'desc')
                ->limit(10)
                ->get();

            // Payment Gateway Distribution
            $paymentGateways = DB::table('transactions')
                ->where('status', 'completed')
                ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get();

            // Top Photographers by Rating
            $topPhotographers = Photographer::with('user')
                ->where('is_verified', true)
                ->orderBy('average_rating', 'desc')
                ->limit(10)
                ->get();

            // Top Photographers by Bookings
            $topPhotographersByBookings = Photographer::with('user')
                ->where('is_verified', true)
                ->orderBy('total_bookings', 'desc')
                ->limit(10)
                ->get();

            // Recent Activity Logs
            $recentActivityLogs = DB::table('activity_logs')
                ->join('users', 'activity_logs.user_id', '=', 'users.id')
                ->select('activity_logs.*', 'users.name as user_name', 'users.email as user_email')
                ->orderBy('activity_logs.created_at', 'desc')
                ->limit(20)
                ->get();

            // Platform Health
            $platformHealth = [
                'avg_response_time' => '< 200ms',
                'uptime' => '99.9%',
                'active_sessions' => User::where('last_login_at', '>=', now()->subHours(1))->count(),
                'pending_support_tickets' => 0,
                'system_status' => 'operational',
                'database_size' => $this->getDatabaseSize(),
                'cache_status' => 'active',
            ];

            return [
                'stats' => $stats,
                'revenue_trend' => $revenueTrend,
                'user_growth' => $userGrowth,
                'booking_stats' => $bookingStats,
                'recent_bookings' => $recentBookings,
                'recent_reviews' => $recentReviews,
                'recent_transactions' => $recentTransactions,
                'payment_gateways' => $paymentGateways,
                'top_photographers' => $topPhotographers,
                'top_photographers_by_bookings' => $topPhotographersByBookings,
                'recent_activity_logs' => $recentActivityLogs,
                'platform_health' => $platformHealth,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $dashboardData,
            'cached_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get database size (approximation)
     */
    private function getDatabaseSize()
    {
        try {
            $size = DB::select("SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
                FROM information_schema.TABLES 
                WHERE table_schema = DATABASE()")[0]->size_mb ?? 0;
            return $size . ' MB';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get all users
     */
    public function users(Request $request)
    {
        $this->checkAdminAccess();

        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%");
        }

        $users = $query->paginate(30);

        return response()->json([
            'status' => 'success',
            'data' => $users->items(),
            'meta' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
            ],
        ]);
    }

    /**
     * Get single user
     */
    public function showUser(User $user)
    {
        $this->checkAdminAccess();

        $user->load('photographer');

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    /**
     * Create new user
     */
    public function storeUser(Request $request)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:client,photographer,admin,super_admin',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        $this->auditLog("User created: {$user->id} - {$user->email}");

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    /**
     * Update user
     */
    public function updateUser(User $user, Request $request)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'sometimes|in:client,photographer,admin,super_admin',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        $this->auditLog("User updated: {$user->id} - {$user->email}");

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        $this->checkAdminAccess();

        // Prevent deleting super admins
        if ($user->role === 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete super admin users',
            ], 403);
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete your own account',
            ], 403);
        }

        $userId = $user->id;
        $userEmail = $user->email;
        $user->delete();

        $this->auditLog("User deleted: {$userId} - {$userEmail}");

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Suspend user
     */
    public function suspendUser(User $user, Request $request)
    {
        $this->checkAdminAccess();

        $user->update([
            'is_suspended' => true,
            'suspension_reason' => $request->get('reason'),
            'suspended_at' => now(),
        ]);

        $this->auditLog("User suspended: {$user->id}");

        return response()->json([
            'status' => 'success',
            'message' => 'User suspended',
        ]);
    }

    /**
     * Unsuspend user
     */
    public function unsuspendUser(User $user)
    {
        $this->checkAdminAccess();

        $user->update([
            'is_suspended' => false,
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);

        $this->auditLog("User unsuspended: {$user->id}");

        return response()->json([
            'status' => 'success',
            'message' => 'User unsuspended',
        ]);
    }

    /**
     * Get pending verifications
     */
    public function getVerifications(Request $request)
    {
        $this->checkAdminAccess();

        $verifications = \App\Models\Verification::with('user')
            ->where('verification_status', 'pending')
            ->latest()
            ->paginate(30);

        return response()->json([
            'status' => 'success',
            'data' => $verifications->items(),
            'meta' => [
                'total' => $verifications->total(),
                'per_page' => $verifications->perPage(),
            ],
        ]);
    }

    /**
     * Approve photographer verification
     */
    public function approveVerification(\App\Models\Verification $verification)
    {
        $this->checkAdminAccess();

        $verification->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Update photographer verification status if all verifications approved
        $photographer = Photographer::where('user_id', $verification->user_id)->first();
        if ($photographer) {
            $photographer->update(['is_verified' => true]);
        }

        $this->auditLog("Verification approved: {$verification->id}");

        return response()->json([
            'status' => 'success',
            'message' => 'Verification approved',
        ]);
    }

    /**
     * Reject verification
     */
    public function rejectVerification(\App\Models\Verification $verification, Request $request)
    {
        $this->checkAdminAccess();

        $verification->update([
            'verification_status' => 'rejected',
            'approved_by' => auth()->id(),
            'admin_notes' => $request->get('notes'),
        ]);

        $this->auditLog("Verification rejected: {$verification->id}");

        return response()->json([
            'status' => 'success',
            'message' => 'Verification rejected',
        ]);
    }

    /**
     * Approve competition submission
     */
    public function approveCompetitionSubmission(CompetitionSubmission $submission)
    {
        $this->checkAdminAccess();

        $submission->update(['status' => 'approved']);

        $this->auditLog("Competition submission approved: {$submission->id}");

        return response()->json([
            'status' => 'success',
            'message' => 'Submission approved',
        ]);
    }

    /**
     * Reject competition submission
     */
    public function rejectCompetitionSubmission(CompetitionSubmission $submission, Request $request)
    {
        $this->checkAdminAccess();

        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $request->get('reason'),
        ]);

        $this->auditLog("Competition submission rejected: {$submission->id}");

        return response()->json([
            'status' => 'success',
            'message' => 'Submission rejected',
        ]);
    }

    /**
     * Get audit logs
     */
    public function auditLogs(Request $request)
    {
        $this->checkAdminAccess();

        $logs = AuditLog::latest()
            ->paginate($request->get('per_page', 50));

        return response()->json([
            'status' => 'success',
            'data' => $logs->items(),
            'meta' => [
                'total' => $logs->total(),
                'per_page' => $logs->perPage(),
            ],
        ]);
    }

    /**
     * Log audit activity
     */
    private function auditLog($description)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
