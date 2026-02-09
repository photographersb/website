<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Competition;
use App\Models\CompetitionJudge;
use App\Models\CompetitionScore;
use App\Models\CompetitionSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetitionJudgeController extends Controller
{
    use ApiResponse;
    /**
     * Assign judge to competition (Admin only)
     */
    public function assignJudge(Request $request, $competitionId)
    {
        $validated = $request->validate([
            'judge_id' => 'required|exists:judges,id',
            'role' => 'required|in:judge,chief_judge',
            'bio' => 'nullable|string|max:1000',
            'expertise' => 'nullable|string|max:255',
        ]);

        $competition = Competition::findOrFail($competitionId);

        // Check if judge already assigned
        $existing = CompetitionJudge::where('competition_id', $competitionId)
            ->where('judge_id', $validated['judge_id'])
            ->first();

        if ($existing) {
            return $this->error('Judge already assigned to this competition', 400);
        }

        $judge = CompetitionJudge::create([
            'competition_id' => $competitionId,
            'judge_id' => $validated['judge_id'],
            'role' => $validated['role'],
            'bio' => $validated['bio'] ?? null,
            'expertise' => $validated['expertise'] ?? null,
            'assigned_at' => now(),
        ]);

        return $this->created($judge->load('judge'), 'Judge assigned successfully');
    }

    /**
     * Remove judge from competition (Admin only)
     */
    public function removeJudge($competitionId, $judgeId)
    {
        $judge = CompetitionJudge::where('competition_id', $competitionId)
            ->where('id', $judgeId)
            ->firstOrFail();

        $judge->delete();

        return $this->success(null, 'Judge removed successfully');
    }

    /**
     * Get all judges for a competition
     */
    public function getJudges($competitionId)
    {
        $judges = CompetitionJudge::with('judge')
            ->forCompetition($competitionId)
            ->active()
            ->get();

        return $this->success($judges, 'Judges retrieved successfully');
    }

    /**
     * Get submissions assigned to judge for scoring
     */
    public function getAssignedSubmissions(Request $request, $competitionId)
    {
        $judgeId = $request->user()->id;

        // Check if user is a judge for this competition
        $isJudge = CompetitionJudge::where('competition_id', $competitionId)
            ->where('judge_id', $judgeId)
            ->active()
            ->exists();

        if (!$isJudge) {
            return $this->unauthorized('Not authorized as judge for this competition');
        }

        // Get all approved submissions with existing scores
        $submissions = CompetitionSubmission::with([
            'photographer.photographer',
            'files' => function ($query) {
                $query->select(['id', 'submission_id', 'exif_json', 'sort_order'])
                    ->orderBy('sort_order');
            },
            'scores' => function ($query) use ($judgeId) {
                $query->where('judge_id', $judgeId);
            }
        ])
            ->forCompetition($competitionId)
            ->approved()
            ->orderBy('created_at', 'asc')
            ->get();

        // Add scoring status for each submission
        $submissions->each(function ($submission) use ($judgeId) {
            $score = $submission->scores->first();
            $submission->my_score = $score;
            $submission->is_scored = $score && $score->status === 'completed';
        });

        return $this->success($submissions, 'Assigned submissions retrieved successfully');
    }

    /**
     * Submit score for a submission
     */
    public function submitScore(Request $request, $competitionId, $submissionId)
    {
        $judgeId = $request->user()->id;

        // Verify judge assignment
        $isJudge = CompetitionJudge::where('competition_id', $competitionId)
            ->where('judge_id', $judgeId)
            ->active()
            ->exists();

        if (!$isJudge) {
            return $this->unauthorized('Not authorized as judge for this competition');
        }

        // Verify submission
        $submission = CompetitionSubmission::forCompetition($competitionId)
            ->approved()
            ->findOrFail($submissionId);

        // Validate scores
        $validated = $request->validate([
            'composition_score' => 'required|numeric|min:0|max:10',
            'technical_score' => 'required|numeric|min:0|max:10',
            'creativity_score' => 'required|numeric|min:0|max:10',
            'story_score' => 'required|numeric|min:0|max:10',
            'impact_score' => 'required|numeric|min:0|max:10',
            'feedback' => 'nullable|string|max:2000',
            'strengths' => 'nullable|string|max:1000',
            'improvements' => 'nullable|string|max:1000',
        ]);

        // Create or update score
        $score = CompetitionScore::updateOrCreate(
            [
                'competition_id' => $competitionId,
                'submission_id' => $submissionId,
                'judge_id' => $judgeId,
            ],
            $validated
        );

        // Update submission's judge_score (average of all judges)
        $this->updateSubmissionJudgeScore($submissionId);

        return $this->success($score, 'Score submitted successfully');
    }

    /**
     * Get judge's scoring progress
     */
    public function getScoringProgress(Request $request, $competitionId)
    {
        $judgeId = $request->user()->id;

        $totalSubmissions = CompetitionSubmission::forCompetition($competitionId)
            ->approved()
            ->count();

        $scoredSubmissions = CompetitionScore::forCompetition($competitionId)
            ->byJudge($judgeId)
            ->completed()
            ->count();

        $pendingSubmissions = $totalSubmissions - $scoredSubmissions;

        return $this->success([
            'total' => $totalSubmissions,
            'scored' => $scoredSubmissions,
            'pending' => $pendingSubmissions,
            'progress_percentage' => $totalSubmissions > 0 
                ? round(($scoredSubmissions / $totalSubmissions) * 100, 1) 
                : 0
        ], 'Scoring progress retrieved successfully');
    }

    /**
     * Get scoring statistics for admin
     */
    public function getScoringStats($competitionId)
    {
        $judges = CompetitionJudge::forCompetition($competitionId)->active()->count();
        $totalSubmissions = CompetitionSubmission::forCompetition($competitionId)->approved()->count();
        $totalScores = CompetitionScore::forCompetition($competitionId)->completed()->count();
        $expectedScores = $judges * $totalSubmissions;
        $pendingScores = $expectedScores - $totalScores;

        // Get per-judge progress
        $judgeProgress = CompetitionJudge::with('judge')
            ->forCompetition($competitionId)
            ->active()
            ->get()
            ->map(function ($judge) use ($competitionId, $totalSubmissions) {
                $scored = CompetitionScore::forCompetition($competitionId)
                    ->byJudge($judge->judge_id)
                    ->completed()
                    ->count();

                return [
                    'judge_id' => $judge->judge_id,
                    'judge_name' => $judge->judge->name,
                    'role' => $judge->role,
                    'total' => $totalSubmissions,
                    'scored' => $scored,
                    'pending' => $totalSubmissions - $scored,
                    'progress' => $totalSubmissions > 0 
                        ? round(($scored / $totalSubmissions) * 100, 1) 
                        : 0
                ];
            });

        return $this->success([
            'judges_count' => $judges,
            'total_submissions' => $totalSubmissions,
            'total_scores' => $totalScores,
            'expected_scores' => $expectedScores,
            'pending_scores' => $pendingScores,
            'overall_progress' => $expectedScores > 0 
                ? round(($totalScores / $expectedScores) * 100, 1) 
                : 0,
            'judge_progress' => $judgeProgress
        ], 'Scoring statistics retrieved successfully');
    }

    /**
     * Get all judging assignments for current user (Judge Dashboard)
     */
    public function getMyAssignments(Request $request)
    {
        $judgeId = $request->user()->id;

        // Get all competitions where user is assigned as judge
        $competitions = Competition::whereHas('judges', function ($query) use ($judgeId) {
            $query->where('judge_id', $judgeId)->where('is_active', true);
        })
        ->with(['judges' => function ($query) use ($judgeId) {
            $query->where('judge_id', $judgeId);
        }])
        ->withCount([
            'submissions as total_submissions' => function ($query) {
                $query->where('status', 'approved');
            }
        ])
        ->get();

        // Calculate scoring progress for each competition
        $competitions->each(function ($competition) use ($judgeId) {
            $scoredCount = CompetitionScore::where('competition_id', $competition->id)
                ->where('judge_id', $judgeId)
                ->where('status', 'completed')
                ->count();
            
            $competition->scored_count = $scoredCount;
            $competition->pending_count = $competition->total_submissions - $scoredCount;
        });

        // Calculate overall stats
        $totalCompetitions = $competitions->count();
        $totalSubmissions = $competitions->sum('total_submissions');
        $totalScored = $competitions->sum('scored_count');

        return $this->success([
            'competitions' => $competitions,
            'stats' => [
                'total_competitions' => $totalCompetitions,
                'total_submissions' => $totalSubmissions,
                'scored' => $totalScored,
                'pending' => $totalSubmissions - $totalScored,
            ]
        ], 'My assignments retrieved successfully');
    }

    /**
     * Update submission's average judge score
     */
    private function updateSubmissionJudgeScore($submissionId)
    {
        $averageScore = CompetitionScore::forSubmission($submissionId)
            ->completed()
            ->avg('total_score');

        if ($averageScore !== null) {
            CompetitionSubmission::where('id', $submissionId)->update([
                'judge_score' => round($averageScore, 1)
            ]);
        }
    }
}
