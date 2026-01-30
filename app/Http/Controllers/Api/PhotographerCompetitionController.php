<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhotographerCompetitionController extends Controller
{
    /**
     * Check if photographer is verified
     */
    protected function checkVerifiedPhotographer()
    {
        $photographer = Photographer::where('user_id', auth()->id())->first();
        
        if (!$photographer || !$photographer->is_verified) {
            abort(403, 'Only verified photographers can create competitions');
        }

        return $photographer;
    }

    /**
     * Get photographer's competitions
     */
    public function index()
    {
        $photographer = $this->checkVerifiedPhotographer();

        $competitions = Competition::where('organizer_id', $photographer->id)
            ->withCount(['submissions', 'votes'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Competition::where('organizer_id', $photographer->id)->count(),
            'active' => Competition::where('organizer_id', $photographer->id)->where('status', 'active')->count(),
            'completed' => Competition::where('organizer_id', $photographer->id)->where('status', 'completed')->count(),
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
     * Store a new photographer-organized competition
     */
    public function store(Request $request)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:competitions,slug',
            'theme' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'hero_image' => 'nullable|url',
            'banner_image' => 'nullable|url',
            'submission_deadline' => 'required|date|after:now',
            'voting_start_at' => 'nullable|date|after_or_equal:submission_deadline',
            'voting_end_at' => 'nullable|date|after:voting_start_at',
            'results_announcement_date' => 'nullable|date',
            'total_prize_pool' => 'required|numeric|min:1000',
            'number_of_winners' => 'required|integer|min:1|max:5',
            'participation_fee' => 'nullable|numeric|min:0',
            'is_paid_competition' => 'boolean',
            'max_submissions_per_user' => 'required|integer|min:1|max:5',
            'allow_public_voting' => 'boolean',
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

        // Set organizer
        $validated['organizer_id'] = $photographer->id;
        $validated['admin_id'] = auth()->id(); // Track who created it
        
        // Photographer competitions default settings
        $validated['status'] = 'draft'; // Must be approved by admin
        $validated['is_public'] = false; // Not public until approved
        $validated['is_featured'] = false;
        $validated['allow_judge_scoring'] = false; // Photographers can't use judge scoring
        $validated['allow_watermark'] = true;
        $validated['require_watermark'] = false;

        $competition = Competition::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Competition created successfully! It will be reviewed by admins before going live.',
            'data' => $competition,
        ], 201);
    }

    /**
     * Show photographer's competition
     */
    public function show($id)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $competition = Competition::where('organizer_id', $photographer->id)
            ->where('id', $id)
            ->with(['admin', 'organizer'])
            ->withCount(['submissions', 'votes'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $competition,
        ]);
    }

    /**
     * Update photographer's competition
     */
    public function update(Request $request, $id)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $competition = Competition::where('organizer_id', $photographer->id)
            ->where('id', $id)
            ->firstOrFail();

        // Can only edit draft competitions
        if ($competition->status !== 'draft') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot edit competition that is already active or completed',
            ], 422);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:competitions,slug,' . $id,
            'theme' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|min:50',
            'hero_image' => 'nullable|url',
            'banner_image' => 'nullable|url',
            'submission_deadline' => 'sometimes|required|date|after:now',
            'voting_start_at' => 'nullable|date|after_or_equal:submission_deadline',
            'voting_end_at' => 'nullable|date|after:voting_start_at',
            'results_announcement_date' => 'nullable|date',
            'total_prize_pool' => 'sometimes|required|numeric|min:1000',
            'number_of_winners' => 'sometimes|required|integer|min:1|max:5',
            'participation_fee' => 'nullable|numeric|min:0',
            'is_paid_competition' => 'boolean',
            'max_submissions_per_user' => 'sometimes|required|integer|min:1|max:5',
            'allow_public_voting' => 'boolean',
        ]);

        $competition->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Competition updated successfully',
            'data' => $competition->fresh(),
        ]);
    }

    /**
     * Delete photographer's competition
     */
    public function destroy($id)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $competition = Competition::where('organizer_id', $photographer->id)
            ->where('id', $id)
            ->firstOrFail();

        // Can only delete draft competitions with no submissions
        if ($competition->status !== 'draft') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete competition that is already active or completed',
            ], 422);
        }

        if ($competition->total_submissions > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete competition with existing submissions',
            ], 422);
        }

        $competition->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Competition deleted successfully',
        ]);
    }
}
