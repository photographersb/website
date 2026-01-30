<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdminCompetitionApiController extends Controller
{
    /**
     * Get all competitions (admin view)
     */
    public function index(Request $request)
    {
        $query = Competition::with(['category'])->withCount('submissions');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $competitions = $query->paginate(20);
        
        // Calculate stats
        $allCompetitions = Competition::all();
        $stats = [
            'total' => $allCompetitions->count(),
            'active' => $allCompetitions->where('status', 'active')->count(),
            'upcoming' => $allCompetitions->where('status', 'upcoming')->count(),
            'completed' => $allCompetitions->where('status', 'completed')->count(),
            'totalSubmissions' => DB::table('competition_submissions')->count(),
            'totalParticipants' => DB::table('competition_submissions')->distinct('photographer_id')->count(),
            'totalPrizePool' => $allCompetitions->sum('prize_pool'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $competitions->items(),
            'stats' => $stats,
            'meta' => [
                'total' => $competitions->total(),
                'per_page' => $competitions->perPage(),
                'current_page' => $competitions->currentPage(),
                'last_page' => $competitions->lastPage(),
            ],
        ]);
    }

    /**
     * Get single competition
     */
    public function show($id)
    {
        $competition = Competition::with(['category', 'submissions', 'judges'])
            ->withCount('submissions')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $competition,
        ]);
    }

    /**
     * Create new competition
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:competitions,slug',
            'description' => 'nullable|string',
            'theme' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'submission_start' => 'required|date',
            'submission_deadline' => 'required|date|after:submission_start',
            'voting_start' => 'required|date|after:submission_deadline',
            'voting_end' => 'required|date|after:voting_start',
            'announcement_date' => 'required|date|after:voting_end',
            'total_prize_pool' => 'nullable|numeric|min:0',
            'max_submissions_per_user' => 'required|integer|min:1|max:10',
            'rules' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'status' => 'required|in:draft,published,closed',
            'is_featured' => 'boolean',
            'prizes' => 'nullable|array',
            'prizes.*.rank' => 'required|string',
            'prizes.*.title' => 'required|string',
            'prizes.*.description' => 'nullable|string',
            'prizes.*.cash_amount' => 'nullable|numeric|min:0',
            'prizes.*.physical_prizes' => 'nullable|string',
            'prizes.*.display_order' => 'nullable|integer|min:0',
            'sponsors' => 'nullable|array',
            'sponsors.*.name' => 'required|string',
            'sponsors.*.tier' => 'required|in:platinum,gold,silver,bronze',
            'sponsors.*.logo_url' => 'nullable|url',
            'sponsors.*.website_url' => 'nullable|url',
            'sponsors.*.description' => 'nullable|string',
            'sponsors.*.contribution_amount' => 'nullable|numeric|min:0',
            'sponsors.*.display_order' => 'nullable|integer|min:0',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
            
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Competition::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set admin_id
        $validated['admin_id'] = auth()->id();

        // Map status for database
        $statusMap = [
            'draft' => 'draft',
            'published' => 'active',
            'closed' => 'completed',
        ];
        $validated['status'] = $statusMap[$validated['status']] ?? 'draft';

        // Extract prizes and sponsors before creating competition
        $prizes = $validated['prizes'] ?? [];
        $sponsors = $validated['sponsors'] ?? [];
        unset($validated['prizes'], $validated['sponsors']);

        try {
            $competition = DB::transaction(function () use ($validated, $prizes, $sponsors) {
                $competition = Competition::create($validated);

                // Create prizes
                if (!empty($prizes)) {
                    foreach ($prizes as $prize) {
                        $competition->prizes()->create($prize);
                    }
                }

                // Create sponsors
                if (!empty($sponsors)) {
                    foreach ($sponsors as $sponsor) {
                        $competition->sponsors()->create($sponsor);
                    }
                }

                return $competition;
            });

            Log::info('Competition created successfully', [
                'competition_id' => $competition->id,
                'title' => $competition->title,
                'prizes_count' => count($prizes),
                'sponsors_count' => count($sponsors),
                'admin_id' => auth()->id(),
            ]);

            // Clear competition stats cache
            Cache::forget('competition_stats');

            return response()->json([
                'status' => 'success',
                'message' => 'Competition created successfully',
                'data' => $competition->load(['prizes', 'sponsors']),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create competition', [
                'error' => $e->getMessage(),
                'title' => $validated['title'] ?? null,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create competition. Please try again.',
            ], 500);
        }
    }

    /**
     * Update competition
     */
    public function update(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'submission_start' => 'sometimes|date',
            'submission_deadline' => 'sometimes|date',
            'voting_start' => 'sometimes|date',
            'voting_end' => 'sometimes|date',
            'total_prize_pool' => 'nullable|numeric|min:0',
            'max_submissions_per_user' => 'sometimes|integer|min:1',
            'rules' => 'nullable|string',
            'status' => 'sometimes|in:draft,upcoming,active,judging,completed,cancelled',
        ]);

        try {
            $competition->update($validated);

            Log::info('Competition updated successfully', [
                'competition_id' => $competition->id,
                'title' => $competition->title,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Competition updated successfully',
                'data' => $competition->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update competition', [
                'competition_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update competition. Please try again.',
            ], 500);
        }
    }

    /**
     * Delete competition
     */
    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);

        // Check if competition has submissions
        if ($competition->submissions()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete competition with existing submissions. Consider changing status to cancelled instead.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($competition) {
                // Delete related records
                $competition->prizes()->delete();
                $competition->sponsors()->delete();
                $competition->delete();
            });

            Log::info('Competition deleted successfully', [
                'competition_id' => $id,
                'title' => $competition->title,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Competition deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete competition', [
                'competition_id' => $id,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete competition. Please try again.',
            ], 500);
        }
    }
}
