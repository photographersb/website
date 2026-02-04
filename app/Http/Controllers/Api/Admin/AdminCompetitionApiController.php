<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompetitionStoreRequest;
use App\Http\Requests\CompetitionUpdateRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Competition;
use App\Models\CompetitionJudge;
use App\Models\Judge;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdminCompetitionApiController extends Controller
{
    use ApiResponse;
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        // Check admin authorization for all methods in this controller
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return $this->unauthorized('Unauthenticated');
            }

            $user = Auth::user();
            // Allow admin, super_admin, and moderator roles
            if (!in_array($user->role ?? null, ['admin', 'super_admin', 'moderator'])) {
                return $this->unauthorized('Unauthorized. Admin access required.');
            }

            return $next($request);
        });
    }

    /**
     * Get all competitions (admin view)
     */
    public function index(Request $request)
    {
        $query = Competition::with(['prizes', 'sponsorRecords', 'category'])
            ->withCount('submissions');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', filter_var($request->featured, FILTER_VALIDATE_BOOLEAN));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('theme', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Create a clone for stats calculation BEFORE pagination
        $statsQuery = clone $query;
        
        // Get IDs from the filtered query (same filters as the list)
        $filteredIds = (clone $statsQuery)->pluck('id');

        // Paginate after getting stats base IDs
        $competitions = $query->paginate($request->get('per_page', 20));

        // Calculate stats using the SAME filtered query
        $stats = [
            'total' => $filteredIds->count(),
            'active' => Competition::whereIn('id', $filteredIds)->active()->count(),
            'upcoming' => Competition::whereIn('id', $filteredIds)->upcoming()->count(),
            'completed' => Competition::whereIn('id', $filteredIds)->completed()->count(),
            'draft' => Competition::whereIn('id', $filteredIds)->where('status', 'draft')->count(),
            'archived' => Competition::whereIn('id', $filteredIds)->where('status', 'archived')->count(),
            'featured' => Competition::whereIn('id', $filteredIds)->where('is_featured', true)->count(),
            'totalSubmissions' => DB::table('competition_submissions')
                ->whereIn('competition_id', $filteredIds)
                ->count(),
            'totalParticipants' => DB::table('competition_submissions')
                ->whereIn('competition_id', $filteredIds)
                ->distinct('photographer_id')
                ->count('photographer_id'),
            'totalPrizePool' => DB::table('competition_prizes')
                ->whereIn('competition_id', $filteredIds)
                ->sum('cash_amount'),
            'pendingSubmissions' => DB::table('competition_submissions')
                ->whereIn('competition_id', $filteredIds)
                ->where('status', 'pending')
                ->count(),
        ];

        $lists = [
            'active' => Competition::active()
                ->orderBy('submission_deadline')
                ->limit(6)
                ->get(['id', 'title', 'status', 'submission_deadline', 'published_at']),
            'upcoming' => Competition::upcoming()
                ->orderBy('published_at')
                ->limit(6)
                ->get(['id', 'title', 'status', 'submission_deadline', 'published_at']),
            'completed' => Competition::completed()
                ->orderByDesc('submission_deadline')
                ->limit(6)
                ->get(['id', 'title', 'status', 'submission_deadline', 'published_at']),
        ];

        return $this->success($competitions->items(), 'Competitions retrieved successfully', 200, [
            'stats' => $stats,
            'lists' => $lists,
            'total' => $competitions->total(),
            'per_page' => $competitions->perPage(),
            'current_page' => $competitions->currentPage(),
            'last_page' => $competitions->lastPage(),
        ]);
    }

    /**
     * Get single competition
     */
    public function show($id)
    {
        $competition = Competition::with(['prizes', 'sponsorRecords', 'category', 'judges.judge', 'judges.judgeProfile'])
            ->withCount('submissions')
            ->findOrFail($id);

        return $this->success($competition, 'Competition retrieved successfully');
    }

    /**
     * Create new competition
     */
    public function store(CompetitionStoreRequest $request)
    {
        $validated = $request->validated();

        // Extract nested data
        $prizes = $validated['prizes'] ?? [];
        $sponsorIds = $validated['sponsor_ids'] ?? [];
        $judgeIds = $validated['judge_ids'] ?? [];
        unset($validated['prizes'], $validated['sponsor_ids'], $validated['judge_ids']);

        $validated['admin_id'] = Auth::id();

        try {
            $competition = DB::transaction(function () use ($validated, $prizes, $sponsorIds, $judgeIds) {
                $competition = Competition::create($validated);

                // Create prizes
                if (!empty($prizes)) {
                    foreach ($prizes as $prize) {
                        $rank = $prize['position'] ?? 'Prize';
                        $title = $prize['title'] ?? $rank . ' Place';
                        
                        $competition->prizes()->create([
                            'rank' => $rank,
                            'title' => $title,
                            'cash_amount' => $prize['amount'] ?? 0,
                            'description' => $prize['description'] ?? null,
                        ]);
                    }
                }

                // Sync sponsors from platform sponsors
                if (!empty($sponsorIds)) {
                    $sponsors = Sponsor::whereIn('id', $sponsorIds)->get()->keyBy('id');
                    $syncData = [];
                    foreach ($sponsorIds as $index => $sponsorId) {
                        $sponsor = $sponsors->get($sponsorId);
                        if ($sponsor) {
                            $syncData[$sponsorId] = [
                                'name' => $sponsor->name,
                                'logo_url' => $sponsor->logo,
                                'website_url' => $sponsor->website,
                                'description' => $sponsor->description,
                                'tier' => 'bronze',
                                'contribution_amount' => null,
                                'display_order' => $index,
                                'is_active' => true,
                            ];
                        }
                    }
                    if (!empty($syncData)) {
                        $competition->sponsorRecords()->sync($syncData);
                    }
                }

                // Assign judges
                if (!empty($judgeIds)) {
                    // Fetch judges and get their user_ids
                    $judgeProfiles = Judge::whereIn('id', $judgeIds)->get();
                    
                    foreach ($judgeProfiles as $index => $profile) {
                        $competition->judges()->create([
                            'judge_id' => $profile->user_id,
                            'judge_profile_id' => $profile->id,
                            'role' => 'judge',
                            'bio' => $profile->bio,
                            'expertise' => null,
                            'is_active' => true,
                            'sort_order' => $index,
                            'assigned_at' => now(),
                        ]);
                    }
                }

                return $competition;
            });

            Log::info('Competition created successfully', [
                'competition_id' => $competition->id,
                'title' => $competition->title,
                'slug' => $competition->slug,
                'admin_id' => Auth::id(),
            ]);

            Cache::forget('competition_stats');

            return $this->created($competition->load(['prizes', 'sponsorRecords', 'category', 'judges.judge', 'judges.judgeProfile']), 'Competition created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create competition', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return $this->error('Failed to create competition: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update competition
     */
    public function update(CompetitionUpdateRequest $request, $id)
    {
        $competition = Competition::findOrFail($id);
        $validated = $request->validated();

        // Extract nested data
        $prizes = $validated['prizes'] ?? null;
        $sponsorIds = $validated['sponsor_ids'] ?? null;
        $judgeIds = $validated['judge_ids'] ?? null;
        unset($validated['prizes'], $validated['sponsor_ids'], $validated['judge_ids']);

        try {
            $competition = DB::transaction(function () use ($competition, $validated, $prizes, $sponsorIds, $judgeIds) {
                $competition->update($validated);

                // Update prizes if provided
                if ($prizes !== null) {
                    $competition->prizes()->delete();
                    foreach ($prizes as $prize) {
                        $rank = $prize['position'] ?? 'Prize';
                        $title = $prize['title'] ?? $rank . ' Place';
                        
                        $competition->prizes()->create([
                            'rank' => $rank,
                            'title' => $title,
                            'cash_amount' => $prize['amount'] ?? 0,
                            'description' => $prize['description'] ?? null,
                        ]);
                    }
                }

                // Sync sponsors if provided
                if ($sponsorIds !== null) {
                    $sponsors = Sponsor::whereIn('id', $sponsorIds)->get()->keyBy('id');
                    $syncData = [];
                    foreach ($sponsorIds as $index => $sponsorId) {
                        $sponsor = $sponsors->get($sponsorId);
                        if ($sponsor) {
                            $syncData[$sponsorId] = [
                                'name' => $sponsor->name,
                                'logo_url' => $sponsor->logo,
                                'website_url' => $sponsor->website,
                                'description' => $sponsor->description,
                                'tier' => 'bronze',
                                'contribution_amount' => null,
                                'display_order' => $index,
                                'is_active' => true,
                            ];
                        }
                    }
                    $competition->sponsorRecords()->sync($syncData);
                }

                // Update judges if provided
                if ($judgeIds !== null) {
                    $competition->judges()->delete();
                    // Fetch judges and get their user_ids
                    $judgeProfiles = Judge::whereIn('id', $judgeIds)->get();
                    
                    foreach ($judgeProfiles as $index => $profile) {
                        $competition->judges()->create([
                            'judge_id' => $profile->user_id,
                            'judge_profile_id' => $profile->id,
                            'role' => 'judge',
                            'bio' => $profile->bio,
                            'expertise' => null,
                            'is_active' => true,
                            'sort_order' => $index,
                            'assigned_at' => now(),
                        ]);
                    }
                }

                return $competition;
            });

            Log::info('Competition updated successfully', [
                'competition_id' => $competition->id,
                'title' => $competition->title,
                'admin_id' => Auth::id(),
            ]);

            Cache::forget('competition_stats');

            return $this->success($competition->fresh()->load(['prizes', 'sponsorRecords', 'category', 'judges.judge', 'judges.judgeProfile']), 'Competition updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update competition', [
                'competition_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return $this->error('Failed to update competition: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete competition
     */
    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);

        // If competition has submissions, archive instead of deleting
        if ($competition->submissions()->count() > 0) {
            $competition->update([
                'status' => 'archived',
                'is_public' => false,
                'is_featured' => false,
            ]);

            Log::info('Competition archived instead of deleted', [
                'competition_id' => $id,
                'title' => $competition->title,
                'admin_id' => Auth::id(),
            ]);

            Cache::forget('competition_stats');

            return $this->success($competition->fresh(), 'Competition has submissions, so it was archived instead of deleted.');
        }

        try {
            DB::transaction(function () use ($competition) {
                $competition->prizes()->delete();
                $competition->sponsorRecords()->detach();
                $competition->judges()->delete();
                $competition->delete();
            });

            Log::info('Competition deleted successfully', [
                'competition_id' => $id,
                'title' => $competition->title,
                'admin_id' => Auth::id(),
            ]);

            Cache::forget('competition_stats');

            return $this->success([], 'Competition deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete competition', [
                'competition_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return $this->error('Failed to delete competition: ' . $e->getMessage(), 500);
        }
    }
}
