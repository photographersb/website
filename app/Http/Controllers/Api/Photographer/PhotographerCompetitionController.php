<?php

namespace App\Http\Controllers\Api\Photographer;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Photographer;
use App\Models\CompetitionSubmission;
use Illuminate\Http\Request;

class PhotographerCompetitionController extends Controller
{
    /**
     * Check if user is a verified photographer
     */
    private function checkVerifiedPhotographer()
    {
        $photographer = Photographer::where('user_id', auth()->id())->first();

        if (!$photographer) {
            abort(403, 'You must have a photographer profile to participate in competitions');
        }

        return $photographer;
    }

    /**
     * Get photographer's competitions (competitions they've participated in)
     */
    public function index(Request $request)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $query = CompetitionSubmission::where('photographer_id', $photographer->id)
            ->with(['competition.category', 'category'])
            ->select('competition_id')
            ->distinct();

        // Get unique competitions
        $submissions = $query->get();
        $competitionIds = $submissions->pluck('competition_id');

        $competitions = Competition::whereIn('id', $competitionIds)
            ->with(['category'])
            ->orderBy('submission_deadline', 'desc')
            ->paginate(20);

        // Add submission details for each competition
        $competitions->getCollection()->transform(function ($competition) use ($photographer) {
            $submission = CompetitionSubmission::where('competition_id', $competition->id)
                ->where('photographer_id', $photographer->id)
                ->with(['category'])
                ->orderBy('created_at', 'desc')
                ->first();

            $competition->my_submission = $submission;
            return $competition;
        });

        return response()->json([
            'status' => 'success',
            'data' => $competitions->items(),
            'meta' => [
                'current_page' => $competitions->currentPage(),
                'total' => $competitions->total(),
                'per_page' => $competitions->perPage()
            ]
        ]);
    }

    /**
     * Store a new competition (photographers cannot create competitions - only admins)
     */
    public function store(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Only administrators can create competitions'
        ], 403);
    }

    /**
     * Get details of a competition the photographer participated in
     */
    public function show($id)
    {
        $photographer = $this->checkVerifiedPhotographer();

        $competition = Competition::with(['category'])
            ->findOrFail($id);

        // Get photographer's submissions for this competition
        $submissions = CompetitionSubmission::where('competition_id', $id)
            ->where('photographer_id', $photographer->id)
            ->with(['category', 'votes', 'scores'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'competition' => $competition,
                'my_submissions' => $submissions,
                'submission_count' => $submissions->count(),
                'total_votes' => $submissions->sum('public_votes'),
                'can_submit' => now()->lt($competition->submission_deadline) 
                    && $submissions->count() < $competition->max_submissions_per_user
            ]
        ]);
    }

    /**
     * Update a competition (photographers cannot update competitions)
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Only administrators can update competitions'
        ], 403);
    }

    /**
     * Delete a competition (photographers cannot delete competitions)
     */
    public function destroy($id)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Only administrators can delete competitions'
        ], 403);
    }
}
