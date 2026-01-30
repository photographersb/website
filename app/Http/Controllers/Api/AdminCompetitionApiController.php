<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;

class AdminCompetitionApiController extends Controller
{
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

        return response()->json([
            'competitions' => $competitions,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'date_filter']),
        ]);
    }

    /**
     * Get single competition
     */
    public function show($id)
    {
        $competition = Competition::with(['admin', 'organizer'])->findOrFail($id);
        
        return response()->json(['competition' => $competition]);
    }

    /**
     * Store a new competition
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:competitions,slug',
            'theme' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            'allow_public_voting' => 'boolean',
            'allow_judge_scoring' => 'boolean',
            'allow_watermark' => 'boolean',
            'require_watermark' => 'boolean',
            'is_public' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,active,judging,completed,cancelled',
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

        $competition = Competition::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Competition created successfully',
            'competition' => $competition,
        ], 201);
    }

    /**
     * Update competition
     */
    public function update(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:competitions,slug,' . $id,
            'theme' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
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
            'allow_public_voting' => 'boolean',
            'allow_judge_scoring' => 'boolean',
            'allow_watermark' => 'boolean',
            'require_watermark' => 'boolean',
            'is_public' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'sometimes|required|in:draft,active,judging,completed,cancelled',
        ]);

        $competition->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Competition updated successfully',
            'competition' => $competition->fresh(),
        ]);
    }

    /**
     * Delete competition
     */
    public function destroy($id)
    {
        $competition = Competition::findOrFail($id);
        
        // Check if competition has submissions
        if ($competition->total_submissions > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete competition with existing submissions. Archive it instead.',
            ], 422);
        }

        $competition->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Competition deleted successfully',
        ]);
    }
}
