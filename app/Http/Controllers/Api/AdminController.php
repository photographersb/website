<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Photographer;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\AuditLog;
use App\Http\Traits\ApiResponse;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    use ApiResponse;
    /**
     * Check if the authenticated user is an admin
     */
    protected function checkAdminAccess()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    /**
     * Get admin dashboard statistics (cached for 5 minutes)
     */
    public function dashboard()
    {
        // Check if user is admin or super_admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return $this->unauthorized('Unauthorized. Admin access required.');
        }

        try {
            // Cache dashboard data for 5 minutes
            $dashboardData = Cache::remember('admin_dashboard_' . Auth::id(), 300, function () {
                // Overview Statistics
                $stats = [
                    'total_users' => User::count(),
                    'active_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
                    'new_users_today' => User::whereDate('created_at', today())->count(),
                    'total_photographers' => Photographer::count(),
                    'verified_photographers' => Photographer::where('is_verified', true)->count(),
                    'pending_verifications' => $this->safeCount('verifications', 'status', 'pending') ?? 0,
                    'total_bookings' => DB::table('bookings')->count(),
                    'pending_bookings' => DB::table('bookings')->where('status', 'pending')->count(),
                    'confirmed_bookings' => DB::table('bookings')->where('status', 'confirmed')->count(),
                    'completed_bookings' => DB::table('bookings')->where('status', 'completed')->count(),
                    'cancelled_bookings' => DB::table('bookings')->where('status', 'cancelled')->count(),
                    'total_revenue' => DB::table('transactions')->where('status', 'completed')->sum('amount') ?? 0,
                    'monthly_revenue' => DB::table('transactions')->where('status', 'completed')
                        ->where('created_at', '>=', now()->startOfMonth())
                        ->sum('amount') ?? 0,
                    'daily_revenue' => DB::table('transactions')->where('status', 'completed')
                        ->whereDate('created_at', today())
                        ->sum('amount') ?? 0,
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
                    
                    // Visitor Analytics (safe with defaults)
                    'active_visitors' => $this->getSafeVisitorCount() ?? 0,
                    'visitors_today' => $this->getSafeVisitorsToday() ?? 0,
                    'page_views_today' => 0,
                    'unique_visitors_30d' => $this->getSafeUniqueVisitors() ?? 0,
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
                $recentBookings = $this->getSafeRecentBookings() ?? [];

                $recentReviews = $this->getSafeRecentReviews() ?? [];

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

                // Recent Activity Logs (safe with error handling)
                $recentActivityLogs = $this->getSafeActivityLogs() ?? [];

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

                // Recent Competitions (Last 5)
                $recentCompetitions = Competition::with('categories')
                    ->withCount('submissions')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
                
                // Visitor Analytics Breakdown (safe with defaults)
                $visitorAnalytics = [
                    'device_breakdown' => [],
                    'top_pages' => [],
                    'traffic_sources' => [],
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
                    'recent_competitions' => $recentCompetitions,
                    'visitor_analytics' => $visitorAnalytics,
                ];
            });

            return $this->success($dashboardData, 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Dashboard error', ['error' => $e->getMessage()]);
            
            // Return minimal dashboard on error
            return $this->success([
                'stats' => [
                    'total_users' => User::count(),
                    'total_photographers' => Photographer::count(),
                    'total_bookings' => DB::table('bookings')->count(),
                    'total_revenue' => DB::table('transactions')->where('status', 'completed')->sum('amount') ?? 0,
                    'total_events' => DB::table('events')->count(),
                    'total_competitions' => Competition::count(),
                    'total_reviews' => DB::table('reviews')->count(),
                ],
                'revenue_trend' => [],
                'user_growth' => [],
                'booking_stats' => [],
                'recent_bookings' => [],
                'recent_reviews' => [],
                'recent_transactions' => [],
                'payment_gateways' => [],
                'top_photographers' => [],
                'top_photographers_by_bookings' => [],
                'recent_activity_logs' => [],
                'platform_health' => ['system_status' => 'operational'],
                'recent_competitions' => [],
                'visitor_analytics' => [],
            ], 'Dashboard data retrieved (minimal)');
        }
    }

    /**
     * Safe count with error handling
     */
    private function safeCount($table, $column = null, $value = null)
    {
        try {
            $query = DB::table($table);
            if ($column && $value) {
                $query->where($column, $value);
            }
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get visitor count safely
     */
    private function getSafeVisitorCount()
    {
        try {
            return DB::table('visitor_logs')
                ->where('last_activity', '>=', now()->subMinutes(15))
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get today's visitors safely
     */
    private function getSafeVisitorsToday()
    {
        try {
            return DB::table('visitor_logs')
                ->whereDate('created_at', today())
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get unique visitors safely
     */
    private function getSafeUniqueVisitors()
    {
        try {
            return DB::table('visitor_logs')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get recent bookings safely
     */
    private function getSafeRecentBookings()
    {
        try {
            return DB::table('bookings')
                ->join('users as clients', 'bookings.client_id', '=', 'clients.id')
                ->join('photographers', 'bookings.photographer_id', '=', 'photographers.id')
                ->join('users as photographers_users', 'photographers.user_id', '=', 'photographers_users.id')
                ->select('bookings.*', 'clients.name as client_name', 'photographers_users.name as photographer_name')
                ->orderBy('bookings.created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get recent reviews safely
     */
    private function getSafeRecentReviews()
    {
        try {
            return DB::table('reviews')
                ->join('users as reviewers', 'reviews.reviewer_id', '=', 'reviewers.id')
                ->join('photographers', 'reviews.photographer_id', '=', 'photographers.id')
                ->join('users as photographers_users', 'photographers.user_id', '=', 'photographers_users.id')
                ->select('reviews.*', 'reviewers.name as reviewer_name', 'photographers_users.name as photographer_name')
                ->orderBy('reviews.created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get recent activity logs safely
     */
    private function getSafeActivityLogs()
    {
        try {
            return DB::table('activity_logs')
                ->join('users', 'activity_logs.causer_id', '=', 'users.id')
                ->select('activity_logs.*', 'users.name as user_name', 'users.email as user_email')
                ->orderBy('activity_logs.created_at', 'desc')
                ->limit(20)
                ->get();
        } catch (\Exception $e) {
            return [];
        }
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

        // Role filter
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Search filter - MUST use closure for correct SQL precedence
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%");
            });
        }

        // Get stats BEFORE pagination (from filtered query)
        $stats = [
            'total' => $query->count(),
            'active' => (clone $query)->where('is_suspended', false)->count(),
            'photographers' => (clone $query)->where('role', 'photographer')->count(),
            'suspended' => (clone $query)->where('is_suspended', true)->count(),
        ];

        // Pagination with dynamic per_page
        $perPage = $request->input('per_page', 30);
        $users = $query->paginate($perPage);

        return $this->success([
            'users' => $users->items(),
            'stats' => $stats,
        ], 'Users retrieved successfully', 200, [
            'meta' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]
        ]);
    }

    /**
     * Get single user
     */
    public function showUser(User $user)
    {
        $this->checkAdminAccess();

        $user->load(['photographer', 'mentor', 'judge']);

        return $this->success(['user' => $user], 'User retrieved successfully');
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
            'role' => 'required|in:client,photographer,studio_owner,studio_photographer,admin,super_admin,moderator',
            'bio' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'create_photographer_profile' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $validated['password'] = bcrypt($validated['password']);
            $validated['email_verified_at'] = now(); // Auto-verify admin-created users
            
            $user = User::create($validated);

            // Auto-create photographer profile if role is photographer-related OR explicitly requested
            $photographerRoles = ['photographer', 'studio_owner', 'studio_photographer'];
            if (in_array($validated['role'], $photographerRoles) || ($request->boolean('create_photographer_profile'))) {
                Photographer::create([
                    'user_id' => $user->id,
                    'business_name' => $validated['name'],
                    'bio' => $validated['bio'] ?? '',
                    'city_id' => $validated['city_id'] ?? null,
                    'is_verified' => true, // Auto-verify admin-created photographers
                    'verification_status' => 'approved',
                    'verified_at' => now(),
                ]);
                
                $this->auditLog("Photographer profile created for user: {$user->id} - {$user->email}");
            }

            DB::commit();

            $user->load('photographer');
            
            $this->auditLog("User created: {$user->id} - {$user->email} (Role: {$user->role})");

            return $this->created($user, 'User created successfully' . (isset($user->photographer) ? ' with photographer profile' : ''));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user: ' . $e->getMessage());
            
            return $this->error('Failed to create user: ' . $e->getMessage(), 500);
        }
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

        return $this->success($user->fresh(), 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        $this->checkAdminAccess();

        // Prevent deleting super admins
        if ($user->role === 'super_admin') {
            return $this->error('Cannot delete super admin users', 403);
        }

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return $this->error('Cannot delete your own account', 403);
        }

        try {
            $userId = $user->id;
            $userEmail = $user->email;
            $user->delete();

            $this->auditLog("User deleted: {$userId} - {$userEmail}");

            return $this->success([], 'User deleted successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle foreign key constraint violations
            if ($e->getCode() === '23000') {
                return $this->error('Cannot delete user: This user has related data (competitions, bookings, reviews, etc.). Please remove or reassign their data first, or suspend the account instead.', 409);
            }
            
            return $this->error('Database error: ' . $e->getMessage(), 500);
        }
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

        return $this->success([], 'User suspended');
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

        return $this->success([], 'User unsuspended');
    }

    /**
     * Get pending verifications
     */
    public function getVerifications(Request $request)
    {
        $this->checkAdminAccess();

        // Build the query with eager loading
        $query = \App\Models\Verification::with(['user', 'approvedBy']);

        // Apply status filter if provided (pending/approved/rejected)
        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        // Calculate stats before pagination (on full filtered dataset)
        $stats = [
            'pending' => \App\Models\Verification::where('verification_status', 'pending')->count(),
            'approved' => \App\Models\Verification::where('verification_status', 'approved')->count(),
            'rejected' => \App\Models\Verification::where('verification_status', 'rejected')->count(),
            'total' => \App\Models\Verification::count(),
        ];

        // Get per_page from request (default 30, max 100)
        $perPage = min($request->get('per_page', 30), 100);

        // Apply sorting and pagination
        $verifications = $query->latest()->paginate($perPage);

        return $this->success([
            'verifications' => $verifications->items(),
            'stats' => $stats,
        ], 'Verifications retrieved successfully', 200, [
            'meta' => [
                'total' => $verifications->total(),
                'per_page' => $verifications->perPage(),
                'current_page' => $verifications->currentPage(),
                'last_page' => $verifications->lastPage(),
            ]
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
            'approved_by' => Auth::id(),
        ]);

        // Update photographer verification status if all verifications approved
        $photographer = Photographer::where('user_id', $verification->user_id)->first();
        if ($photographer) {
            $photographer->update(['is_verified' => true]);
            
            // Send verification approved notification
            try {
                NotificationService::verificationApproved($photographer);
            } catch (\Exception $e) {
                Log::warning('Failed to send verification approved notification', [
                    'photographer_id' => $photographer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->auditLog("Verification approved: {$verification->id}");

        return $this->success([], 'Verification approved');
    }

    /**
     * Reject verification
     */
    public function rejectVerification(\App\Models\Verification $verification, Request $request)
    {
        $this->checkAdminAccess();

        $reason = $request->get('notes', 'No reason provided');
        
        $verification->update([
            'verification_status' => 'rejected',
            'approved_by' => Auth::id(),
            'admin_notes' => $reason,
        ]);

        // Send rejection notification to photographer
        $photographer = Photographer::where('user_id', $verification->user_id)->first();
        if ($photographer) {
            try {
                NotificationService::verificationRejected($photographer, $reason);
            } catch (\Exception $e) {
                Log::warning('Failed to send verification rejected notification', [
                    'photographer_id' => $photographer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->auditLog("Verification rejected: {$verification->id}");

        return $this->success([], 'Verification rejected');
    }

    /**
     * Approve competition submission
     */
    public function approveCompetitionSubmission(CompetitionSubmission $submission)
    {
        $this->checkAdminAccess();

        $submission->update(['status' => 'approved']);

        $this->auditLog("Competition submission approved: {$submission->id}");

        return $this->success([], 'Submission approved');
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

        return $this->success([], 'Submission rejected');
    }

    /**
     * Get audit logs
     */
    public function auditLogs(Request $request)
    {
        $this->checkAdminAccess();

        $logs = AuditLog::latest()
            ->paginate($request->get('per_page', 50));

        return $this->paginated($logs, 'Audit logs retrieved successfully');
    }

    /**
     * Get all photographers with filters
     */
    public function getPhotographers(Request $request)
    {
        $this->checkAdminAccess();

        $query = Photographer::with(['user', 'city', 'categories']);

        // Filter by verification status
        if ($request->has('verified')) {
            $query->where('is_verified', $request->verified);
        }

        // Support verification filter by text values (verified/pending/unverified)
        if ($request->has('verification')) {
            if ($request->verification === 'verified') {
                $query->where('is_verified', true);
            } elseif (in_array($request->verification, ['pending', 'unverified'])) {
                $query->where('is_verified', false);
            }
        }

        // Filter by city
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('average_rating', '>=', $request->rating);
        }

        // NEW: Filter by status (for form dropdowns - e.g., 'active' photographers)
        if ($request->has('status')) {
            $status = $request->status;
            if ($status === 'active') {
                // Active = verified photographers with completed profiles
                $query->where('is_verified', true)
                      ->where('profile_completeness', '>=', 75);
            } elseif ($status === 'verified') {
                $query->where('is_verified', true);
            } elseif ($status === 'pending') {
                $query->where('is_verified', false);
            }
        }

        // Search by name - MUST use closure for correct SQL precedence
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Check if requesting for simple dropdown (minimal data) - for event forms
        if ($request->get('minimal') === 'true' || $request->get('minimal') === '1') {
            $photographers = $query->select('id', 'user_id', 'business_name')
                ->with('user:id,name,email')
                ->orderBy('id', 'desc')
                ->get();
            return $this->success($photographers, 'Photographers retrieved (minimal)');
        }

        // Get stats BEFORE pagination (from filtered query)
        $stats = [
            'total' => $query->count(),
            'verified' => (clone $query)->where('is_verified', true)->count(),
            'pending' => (clone $query)->where('is_verified', false)->count(),
            'avgRating' => (clone $query)->avg('average_rating') ? round((clone $query)->avg('average_rating'), 1) : 0,
        ];

        // Pagination with dynamic per_page
        $perPage = $request->input('per_page', 20);
        $photographers = $query->paginate($perPage);

        return $this->success([
            'photographers' => $photographers->items(),
            'stats' => $stats,
        ], 'Photographers retrieved successfully', 200, [
            'meta' => [
                'total' => $photographers->total(),
                'per_page' => $photographers->perPage(),
                'current_page' => $photographers->currentPage(),
                'last_page' => $photographers->lastPage(),
            ]
        ]);
    }

    /**
     * Get single photographer
     */
    public function showPhotographer($id)
    {
        $this->checkAdminAccess();

        $photographer = Photographer::with(['user', 'city', 'categories', 'packages', 'albums'])
            ->findOrFail($id);

        return $this->success($photographer, 'Photographer retrieved successfully');
    }

    /**
     * Create new photographer
     */
    public function storePhotographer(Request $request)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'city_id' => 'nullable|exists:cities,id',
            'bio' => 'nullable|string',
            'experience_years' => 'nullable|integer',
            'is_verified' => 'boolean',
        ]);

        $photographer = Photographer::create($validated);

        $this->auditLog("Created photographer profile for user #{$validated['user_id']}");

        return $this->created($photographer, 'Photographer created successfully');
    }

    /**
     * Update photographer
     */
    public function updatePhotographer(Request $request, $id)
    {
        $this->checkAdminAccess();

        $photographer = Photographer::findOrFail($id);

        $validated = $request->validate([
            'city_id' => 'nullable|exists:cities,id',
            'bio' => 'nullable|string',
            'experience_years' => 'nullable|integer',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $photographer->update($validated);

        $this->auditLog("Updated photographer profile #{$id}");

        return $this->success($photographer->fresh(['user', 'city', 'categories']), 'Photographer updated successfully');
    }

    /**
     * Delete photographer
     */
    public function deletePhotographer($id)
    {
        $this->checkAdminAccess();

        $photographer = Photographer::findOrFail($id);
        $userId = $photographer->user_id;
        
        $photographer->delete();

        $this->auditLog("Deleted photographer profile #{$id}");

        return $this->success([], 'Photographer deleted successfully');
    }

    /**
     * Verify photographer
     */
    public function verifyPhotographer($id)
    {
        $this->checkAdminAccess();

        $photographer = Photographer::findOrFail($id);
        $photographer->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $this->auditLog("Verified photographer #{$id}");

        return $this->success($photographer->fresh(), 'Photographer verified successfully');
    }

    /**
     * Feature photographer
     */
    public function featurePhotographer(Request $request, $id)
    {
        $this->checkAdminAccess();

        $photographer = Photographer::findOrFail($id);
        
        $validated = $request->validate([
            'is_featured' => 'required|boolean',
            'featured_until' => 'nullable|date',
        ]);

        $photographer->update($validated);

        $this->auditLog("Featured photographer #{$id}");

        return $this->success($photographer->fresh(), 'Photographer featured status updated');
    }

    /**
     * Promote user to mentor
     */
    public function promoteToMentor(Request $request, $userId)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $user = User::findOrFail($userId);

        DB::beginTransaction();
        try {
            // Check if already a mentor
            $existing = \App\Models\Mentor::where('user_id', $userId)->first();
            if ($existing) {
                return $this->error('User is already promoted to mentor', 400);
            }

            // Create mentor record
            $mentor = \App\Models\Mentor::create([
                'user_id' => $userId,
                'name' => $validated['name'] ?? $user->name,
                'title' => $validated['title'] ?? null,
                'organization' => $validated['organization'] ?? null,
                'bio' => $validated['bio'] ?? $user->bio,
                'email' => $validated['email'] ?? $user->email,
                'phone' => $validated['phone'] ?? $user->phone,
                'country' => $validated['country'] ?? 'Bangladesh',
                'city' => $validated['city'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => 0,
            ]);

            DB::commit();

            $this->auditLog("User #{$userId} ({$user->email}) promoted to mentor");

            return $this->created($mentor->load('user'), 'User promoted to mentor successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to promote to mentor: ' . $e->getMessage());
            
            return $this->error('Failed to promote user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Promote user to judge
     */
    public function promoteToJudge(Request $request, $userId)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'nullable|email',
            'is_active' => 'boolean',
        ]);

        $user = User::findOrFail($userId);

        DB::beginTransaction();
        try {
            // Check if already a judge
            $existing = \App\Models\Judge::where('user_id', $userId)->first();
            if ($existing) {
                return $this->error('User is already promoted to judge', 400);
            }

            // Create judge record
            $judge = \App\Models\Judge::create([
                'user_id' => $userId,
                'name' => $validated['name'] ?? $user->name,
                'title' => $validated['title'] ?? null,
                'organization' => $validated['organization'] ?? null,
                'bio' => $validated['bio'] ?? $user->bio,
                'email' => $validated['email'] ?? $user->email,
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => 0,
            ]);

            DB::commit();

            $this->auditLog("User #{$userId} ({$user->email}) promoted to judge");

            return $this->created($judge->load('user'), 'User promoted to judge successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to promote to judge: ' . $e->getMessage());
            
            return $this->error('Failed to promote user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Log audit activity
     */
    private function auditLog($description)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
