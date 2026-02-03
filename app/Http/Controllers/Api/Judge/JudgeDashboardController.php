<?php

namespace App\Http\Controllers\Api\Judge;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionJudge;
use App\Models\CompetitionScore;
use App\Models\CompetitionSubmission;
use App\Models\Judge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JudgeDashboardController extends Controller
{
    /**
     * Get judge's dashboard stats
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get judge profile
        $judgeProfile = Judge::where('user_id', $user->id)->first();
        
        // Get assigned competitions
        $assignedCompetitions = CompetitionJudge::where('judge_id', $user->id)
            ->orWhere(function ($q) use ($judgeProfile) {
                if ($judgeProfile) {
                    $q->where('judge_profile_id', $judgeProfile->id);
                }
            })
            ->pluck('competition_id');

        $stats = [
            'total_competitions' => $assignedCompetitions->count(),
            'active_competitions' => Competition::whereIn('id', $assignedCompetitions)
                ->where('status', 'active')
                ->count(),
            'pending_scores' => CompetitionSubmission::whereIn('competition_id', $assignedCompetitions)
                ->where('status', 'approved')
                ->whereDoesntHave('scores', function ($q) use ($user) {
                    $q->where('judge_id', $user->id);
                })
                ->count(),
            'completed_scores' => CompetitionScore::where('judge_id', $user->id)
                ->where('status', 'completed')
                ->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $stats,
                'judge_profile' => $judgeProfile,
            ],
        ]);
    }

    /**
     * Get competitions assigned to this judge
     */
    public function myCompetitions(Request $request)
    {
        $user = auth()->user();
        $judgeProfile = Judge::where('user_id', $user->id)->first();
        
        $query = Competition::query()
            ->whereHas('judges', function ($q) use ($user, $judgeProfile) {
                $q->where('judge_id', $user->id)
                  ->orWhere(function ($q2) use ($judgeProfile) {
                      if ($judgeProfile) {
                          $q2->where('judge_profile_id', $judgeProfile->id);
                      }
                  });
            })
            ->with(['prizes', 'scoringCriteria'])
            ->withCount([
                'submissions as total_submissions' => function ($q) {
                    $q->where('status', 'approved');
                },
                'submissions as my_scores' => function ($q) use ($user) {
                    $q->whereHas('scores', function ($q2) use ($user) {
                        $q2->where('judge_id', $user->id);
                    });
                },
            ]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $competitions = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $competitions,
        ]);
    }

    /**
     * Get submissions for a competition
     */
    public function competitionSubmissions(Request $request, Competition $competition)
    {
        $user = auth()->user();
        
        // Verify judge is assigned to this competition
        $this->verifyJudgeAccess($competition, $user);

        $query = CompetitionSubmission::where('competition_id', $competition->id)
            ->where('status', 'approved')
            ->with([
                'photographer.user',
                'scores' => function ($q) use ($user) {
                    $q->where('judge_id', $user->id);
                },
            ]);

        // Filter by scoring status
        if ($request->filled('scored')) {
            if ($request->scored === 'yes') {
                $query->whereHas('scores', function ($q) use ($user) {
                    $q->where('judge_id', $user->id);
                });
            } else {
                $query->whereDoesntHave('scores', function ($q) use ($user) {
                    $q->where('judge_id', $user->id);
                });
            }
        }

        $submissions = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => [
                'competition' => $competition->load('scoringCriteria'),
                'submissions' => $submissions,
            ],
        ]);
    }

    /**
     * Get single submission for scoring
     */
    public function getSubmission(Competition $competition, CompetitionSubmission $submission)
    {
        $user = auth()->user();
        
        // Verify judge is assigned
        $this->verifyJudgeAccess($competition, $user);

        // Check if submission belongs to this competition
        if ($submission->competition_id !== $competition->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found in this competition',
            ], 404);
        }

        $submission->load([
            'photographer.user',
            'scores' => function ($q) use ($user) {
                $q->where('judge_id', $user->id);
            },
        ]);

        $criteria = $competition->scoringCriteria;

        return response()->json([
            'status' => 'success',
            'data' => [
                'submission' => $submission,
                'criteria' => $criteria,
                'competition' => $competition->only(['id', 'title', 'slug', 'judging_end_at']),
            ],
        ]);
    }

    /**
     * Submit or update score for a submission
     */
    public function submitScore(Request $request, Competition $competition, CompetitionSubmission $submission)
    {
        $user = auth()->user();
        
        // Verify judge access
        $this->verifyJudgeAccess($competition, $user);

        // Check if judging period is active
        if (now() > $competition->judging_end_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Judging period has ended',
            ], 422);
        }

        $validated = $request->validate([
            'composition_score' => 'required|numeric|min:0|max:10',
            'technical_score' => 'required|numeric|min:0|max:10',
            'creativity_score' => 'required|numeric|min:0|max:10',
            'story_score' => 'required|numeric|min:0|max:10',
            'impact_score' => 'required|numeric|min:0|max:10',
            'feedback' => 'nullable|string|max:1000',
            'strengths' => 'nullable|string|max:500',
            'improvements' => 'nullable|string|max:500',
        ]);

        // Calculate total
        $validated['total_score'] = 
            $validated['composition_score'] +
            $validated['technical_score'] +
            $validated['creativity_score'] +
            $validated['story_score'] +
            $validated['impact_score'];

        $validated['competition_id'] = $competition->id;
        $validated['submission_id'] = $submission->id;
        $validated['judge_id'] = $user->id;
        $validated['status'] = 'completed';
        $validated['scored_at'] = now();

        // Update or create score
        $score = CompetitionScore::updateOrCreate(
            [
                'submission_id' => $submission->id,
                'judge_id' => $user->id,
            ],
            $validated
        );

        // Update submission's judge_score (average of all judge scores)
        $this->updateSubmissionScore($submission);

        return response()->json([
            'status' => 'success',
            'message' => 'Score submitted successfully',
            'data' => $score,
        ]);
    }

    /**
     * Get scoring history for judge
     */
    public function scoringHistory(Request $request)
    {
        $user = auth()->user();

        $query = CompetitionScore::where('judge_id', $user->id)
            ->with([
                'submission.photographer.user',
                'submission.competition:id,title,slug',
            ])
            ->orderBy('scored_at', 'desc');

        $scores = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $scores,
        ]);
    }

    /**
     * Verify judge has access to competition
     */
    private function verifyJudgeAccess(Competition $competition, $user)
    {
        $judgeProfile = Judge::where('user_id', $user->id)->first();
        
        $hasAccess = CompetitionJudge::where('competition_id', $competition->id)
            ->where(function ($q) use ($user, $judgeProfile) {
                $q->where('judge_id', $user->id)
                  ->orWhere(function ($q2) use ($judgeProfile) {
                      if ($judgeProfile) {
                          $q2->where('judge_profile_id', $judgeProfile->id);
                      }
                  });
            })
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You are not assigned as a judge for this competition');
        }
    }

    /**
     * Update submission's average judge score
     */
    private function updateSubmissionScore(CompetitionSubmission $submission)
    {
        $avgScore = CompetitionScore::where('submission_id', $submission->id)
            ->where('status', 'completed')
            ->avg('total_score');

        $submission->update(['judge_score' => $avgScore]);
    }
}
