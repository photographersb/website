<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Competition;
use App\Models\Judge;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCompetitionApiController extends Controller
{
    use ApiResponse;

    protected function resolveJudgeUserIds(array $judgeIds): ?array
    {
        if (empty($judgeIds)) {
            return [];
        }

        $idList = collect($judgeIds)->filter()->values();
        if ($idList->isEmpty()) {
            return [];
        }

        $profileMap = Judge::whereIn('id', $idList)->pluck('user_id', 'id');
        $mappedIds = $idList->map(function ($id) use ($profileMap) {
            return $profileMap->get($id, $id);
        })->unique()->values();

        $validUserIds = User::whereIn('id', $mappedIds)->pluck('id')->all();

        if (count($validUserIds) !== $mappedIds->count()) {
            return null;
        }

        return $validUserIds;
    }
    /**
     * Get all competitions for admin
     */
    public function index(Request $request)
    {
        $query = Competition::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('theme', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date_filter')) {
            $today = now();
            switch ($request->date_filter) {
                case 'upcoming':
                    $query->where('submission_deadline', '>', $today);
                    break;
                case 'active':
                    $query->where('status', 'active');
                    break;
                case 'completed':
                    $query->where('status', 'completed');
                    break;
            }
        }

        $competitions = $query->latest()->paginate(15);

        $stats = [
            'total' => Competition::count(),
            'active' => Competition::where('status', 'active')->count(),
            'upcoming' => Competition::where('submission_deadline', '>', now())->count(),
            'completed' => Competition::where('status', 'completed')->count(),
        ];

        return $this->success([
            'competitions' => $competitions->items(),
            'filters' => $request->only(['search', 'status', 'date_filter']),
        ], 'Competitions retrieved successfully', 200, [
            'stats' => $stats,
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
        $competition = Competition::with(['admin', 'organizer'])->findOrFail($id);
        
        return $this->success($competition, 'Competition retrieved successfully');
    }

    /**
     * Store a new competition
     */
    public function store(Request $request)
    {
        // Check authorization - only admins can create competitions
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return $this->unauthorized('This action is unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:competitions,slug',
            'theme' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'hero_image' => 'nullable|url',
            'banner_image' => 'nullable|url',
            'submission_deadline' => 'required|date|after:now',
            'voting_start_at' => 'nullable|date|after_or_equal:submission_deadline',
            'voting_end_at' => 'nullable|date|after:voting_start_at',
            'judging_start_at' => 'nullable|date',
            'judging_end_at' => 'nullable|date|after:judging_start_at',
            'results_announcement_date' => 'nullable|date',
            'total_prize_pool' => 'required|numeric|min:0',
            'number_of_winners' => 'required|integer|min:1|max:10',
            'participation_fee' => 'nullable|numeric|min:0',
            'is_paid_competition' => 'boolean',
            'max_submissions_per_user' => 'required|integer|min:1|max:10',
            'rules' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'allow_public_voting' => 'boolean',
            'allow_judge_scoring' => 'boolean',
            'allow_watermark' => 'boolean',
            'require_watermark' => 'boolean',
            'is_public' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,active,judging,completed,cancelled',
            'prizes' => 'nullable|array',
            'prizes.*.rank' => 'nullable|string|max:50',
            'prizes.*.type' => 'nullable|string|max:50',
            'prizes.*.amount' => 'nullable|numeric|min:0',
            'prizes.*.description' => 'nullable|string|max:255',
            'sponsor_ids' => 'nullable|array',
            'sponsor_ids.*' => 'exists:sponsors,id',
            'judge_ids' => 'nullable|array',
            'judge_ids.*' => 'integer',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['title']);
            
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Competition::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set admin as creator
        $validated['admin_id'] = auth()->id();
        $validated['organizer_id'] = $request->organizer_id ?? null;

        // Store sponsors and judges for later attachment
        $sponsorIds = $validated['sponsor_ids'] ?? [];
        $judgeIds = $validated['judge_ids'] ?? [];
        $resolvedJudgeIds = $this->resolveJudgeUserIds($judgeIds);
        if ($resolvedJudgeIds === null) {
            return $this->validationError(['judge_ids' => 'Invalid judge selection.'], 'Validation failed');
        }
        
        // Remove these from validated to avoid mass assignment errors
        unset($validated['sponsor_ids']);
        unset($validated['judge_ids']);

        try {
            \DB::beginTransaction();

            $competition = Competition::create($validated);

            // Attach sponsors if provided
            if (!empty($sponsorIds)) {
                $competition->sponsorRecords()->attach($sponsorIds);
            }

            // Attach judges if provided
            if (!empty($resolvedJudgeIds)) {
                $competition->judgeUsers()->attach($resolvedJudgeIds);
            }

            \DB::commit();

            return $this->created($competition->load(['sponsorRecords', 'judgeUsers']), 'Competition created successfully');
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->validationError([], 'Error creating competition: ' . $e->getMessage());
        }
    }

    /**
     * Update competition
     */
    public function update(Request $request, $id)
    {
        // Check authorization - only admins can update competitions
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return $this->unauthorized('This action is unauthorized.');
        }

        $competition = Competition::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:competitions,slug,' . $id,
            'theme' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'hero_image' => 'nullable|url',
            'banner_image' => 'nullable|url',
            'submission_deadline' => 'sometimes|required|date',
            'voting_start_at' => 'nullable|date',
            'voting_end_at' => 'nullable|date|after:voting_start_at',
            'judging_start_at' => 'nullable|date',
            'judging_end_at' => 'nullable|date|after:judging_start_at',
            'results_announcement_date' => 'nullable|date',
            'total_prize_pool' => 'sometimes|required|numeric|min:0',
            'number_of_winners' => 'sometimes|required|integer|min:1|max:10',
            'participation_fee' => 'nullable|numeric|min:0',
            'is_paid_competition' => 'boolean',
            'max_submissions_per_user' => 'sometimes|required|integer|min:1|max:10',
            'rules' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'allow_public_voting' => 'boolean',
            'allow_judge_scoring' => 'boolean',
            'allow_watermark' => 'boolean',
            'require_watermark' => 'boolean',
            'is_public' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'sometimes|required|in:draft,active,judging,completed,cancelled',
            'prizes' => 'nullable|array',
            'prizes.*.rank' => 'nullable|string|max:50',
            'prizes.*.type' => 'nullable|string|max:50',
            'prizes.*.amount' => 'nullable|numeric|min:0',
            'prizes.*.description' => 'nullable|string|max:255',
            'sponsor_ids' => 'nullable|array',
            'sponsor_ids.*' => 'exists:sponsors,id',
            'judge_ids' => 'nullable|array',
            'judge_ids.*' => 'integer',
        ]);

        // Store sponsors and judges for later attachment
        $sponsorIds = $validated['sponsor_ids'] ?? null;
        $judgeIds = $validated['judge_ids'] ?? null;
        $resolvedJudgeIds = null;
        if ($judgeIds !== null) {
            $resolvedJudgeIds = $this->resolveJudgeUserIds($judgeIds);
            if ($resolvedJudgeIds === null) {
                return $this->validationError(['judge_ids' => 'Invalid judge selection.'], 'Validation failed');
            }
        }
        
        // Remove these from validated to avoid mass assignment errors
        unset($validated['sponsor_ids']);
        unset($validated['judge_ids']);

        try {
            \DB::beginTransaction();

            $competition->update($validated);

            // Update sponsors if provided
            if ($sponsorIds !== null) {
                $competition->sponsorRecords()->sync($sponsorIds);
            }

            // Update judges if provided
            if ($resolvedJudgeIds !== null) {
                $competition->judgeUsers()->sync($resolvedJudgeIds);
            }

            \DB::commit();

            return $this->success($competition->fresh()->load(['sponsorRecords', 'judgeUsers']), 'Competition updated successfully');
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->validationError([], 'Error updating competition: ' . $e->getMessage());
        }
    }

    /**
     * Delete competition
     */
    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);
        
        // Check if competition has submissions
        if ($competition->total_submissions > 0) {
            return $this->validationError(['competition' => 'Cannot delete competition with existing submissions. Archive it instead.'], 'Validation failed');
        }

        $competition->delete();

        return $this->success([], 'Competition deleted successfully');
    }
}
