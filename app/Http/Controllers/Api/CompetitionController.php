<?php

namespace App\Http\Controllers\Api;

use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\CompetitionVote;
use App\Services\WinnerCalculationService;
use App\Services\CertificateService;
use App\Services\PrizeDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompetitionController extends Controller
{
    /**
     * Get competition stats (cached for 15 minutes)
     */
    public function stats()
    {
        return Cache::remember('competition_stats', 900, function () {
            $stats = [
                'active_competitions' => Competition::where('status', 'active')->count(),
                'total_prize_pool' => Competition::where('status', '!=', 'cancelled')->sum('total_prize_pool'),
                'total_submissions' => Competition::sum('total_submissions'),
                'total_participants' => \App\Models\CompetitionSubmission::distinct('photographer_id')->count(),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats,
            ]);
        });
    }

    /**
     * Get all competitions with filters
     */
    public function index(Request $request)
    {
        $query = Competition::where('is_public', true)
            ->with(['admin', 'organizer']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        } else {
            // Default: show active and judging competitions
            $query->whereIn('status', ['active', 'judging', 'completed']);
        }

        // Filter by paid/free
        if ($request->has('is_paid') && $request->is_paid !== '') {
            $query->where('is_paid_competition', $request->is_paid);
        }

        // Filter by theme
        if ($request->has('theme') && $request->theme != '') {
            $query->where('theme', 'like', '%' . $request->theme . '%');
        }

        // Sorting
        $sort = $request->get('sort', 'deadline');
        switch ($sort) {
            case 'prize':
                $query->orderBy('total_prize_pool', 'desc');
                break;
            case 'submissions':
                $query->orderBy('total_submissions', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'deadline':
            default:
                $query->orderBy('submission_deadline', 'asc');
                break;
        }

        $competitions = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $competitions->items(),
            'meta' => [
                'total' => $competitions->total(),
                'per_page' => $competitions->perPage(),
                'current_page' => $competitions->currentPage(),
                'last_page' => $competitions->lastPage(),
            ],
        ]);
    }

    /**
     * Get competition details with prizes, sponsors, and top submissions
     */
    public function show(Competition $competition)
    {
        // Eager load relationships to avoid N+1 queries
        $competition->load([
            'admin',
            'organizer',
            'prizes' => function ($q) {
                $q->orderBy('rank');
            },
            'sponsors',
            'submissions' => function ($q) {
                $q->where('status', 'approved')
                  ->with(['photographer'])
                  ->orderBy('vote_count', 'desc')
                  ->limit(10);
            },
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $competition,
        ]);
    }

    /**
     * Submit photo to competition
     */
    public function submit(Request $request, Competition $competition)
    {
        // Check if competition is accepting submissions
        if (now() > $competition->submission_deadline) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission deadline has passed',
            ], 422);
        }

        $validated = $request->validate([
            'image_url' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'date_taken' => 'required|date',
            'camera_make' => 'nullable|string',
            'camera_model' => 'nullable|string',
            'hashtags' => 'nullable|string',
        ]);

        // Check submission count
        $submissionCount = CompetitionSubmission::where('competition_id', $competition->id)
            ->where('photographer_id', auth()->id())
            ->count();

        if ($submissionCount >= $competition->max_submissions_per_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have reached the maximum number of submissions',
            ], 422);
        }

        $submission = CompetitionSubmission::create([
            ...$validated,
            'competition_id' => $competition->id,
            'photographer_id' => auth()->id(),
            'status' => 'pending_review',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Photo submitted successfully',
            'data' => $submission,
        ], 201);
    }

    /**
     * Vote on submission (with fraud prevention)
     */
    public function vote(Request $request, CompetitionSubmission $submission)
    {
        $competition = $submission->competition;

        // Check if voting is active
        if (now() < $competition->voting_start_at || now() > $competition->voting_end_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voting is not currently active',
            ], 422);
        }

        // Check daily vote limit
        $voteCountToday = CompetitionVote::where('competition_id', $competition->id)
            ->where('voter_id', auth()->id())
            ->whereDate('voted_at', today())
            ->count();

        if ($voteCountToday >= 50) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have reached your daily voting limit',
            ], 422);
        }

        // Check if already voted on this submission
        $existingVote = CompetitionVote::where('submission_id', $submission->id)
            ->where('voter_id', auth()->id())
            ->first();

        if ($existingVote) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already voted on this submission',
            ], 422);
        }

        // Record vote
        $vote = CompetitionVote::create([
            'submission_id' => $submission->id,
            'voter_id' => auth()->id(),
            'competition_id' => $competition->id,
            'voted_at' => now(),
            'ip_address' => $request->ip(),
            'is_verified' => auth()->check(),
            'is_valid' => true,
        ]);

        // Update submission vote count
        $submission->increment('vote_count');

        return response()->json([
            'status' => 'success',
            'message' => 'Vote recorded',
            'data' => $vote,
        ], 201);
    }

    /**
     * Get competition leaderboard
     */
    public function leaderboard(Competition $competition)
    {
        $submissions = CompetitionSubmission::where('competition_id', $competition->id)
            ->where('status', 'approved')
            ->with(['photographer', 'votes'])
            ->orderBy('vote_count', 'desc')
            ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $submissions->items(),
            'meta' => [
                'total' => $submissions->total(),
                'per_page' => $submissions->perPage(),
            ],
        ]);
    }
    
    /**
     * Calculate winners (preview, does not save)
     */
    public function calculateWinners(Request $request, Competition $competition, WinnerCalculationService $winnerService)
    {
        // Validate configuration
        $config = $request->validate([
            'vote_weight' => 'nullable|numeric|min:0|max:1',
            'judge_weight' => 'nullable|numeric|min:0|max:1',
            'number_of_winners' => 'nullable|integer|min:1|max:10',
            'honorable_mentions' => 'nullable|integer|min:0|max:20'
        ]);
        
        // Calculate winners (preview only)
        $result = $winnerService->calculateWinners($competition, $config);
        
        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message']
            ], 400);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Winner calculation preview',
            'data' => [
                'winners' => $result['winners'],
                'config' => $result['config']
            ]
        ]);
    }
    
    /**
     * Announce winners (saves to database)
     */
    public function announceWinners(Request $request, Competition $competition, WinnerCalculationService $winnerService)
    {
        // Validate configuration
        $config = $request->validate([
            'vote_weight' => 'nullable|numeric|min:0|max:1',
            'judge_weight' => 'nullable|numeric|min:0|max:1',
            'number_of_winners' => 'nullable|integer|min:1|max:10',
            'honorable_mentions' => 'nullable|integer|min:0|max:20',
            'winner_notes' => 'nullable|string|max:1000'
        ]);
        
        // Announce winners and save to database
        $result = $winnerService->announceWinners($competition, $config);
        
        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message']
            ], 400);
        }
        
        // TODO: Send winner notifications via email
        
        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => [
                'winners' => $result['winners'],
                'config' => $result['config']
            ]
        ]);
    }
    
    /**
     * Get winners for a competition
     */
    public function getWinners(Competition $competition, WinnerCalculationService $winnerService)
    {
        $result = $winnerService->getWinners($competition);
        
        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }
    
    /**
     * Get detailed leaderboard with rankings
     */
    public function getLeaderboard(Request $request, Competition $competition, WinnerCalculationService $winnerService)
    {
        $limit = $request->input('limit', 20);
        $leaderboard = $winnerService->getLeaderboard($competition, $limit);
        
        return response()->json([
            'status' => 'success',
            'data' => $leaderboard
        ]);
    }

    // Certificate Generation Methods

    /**
     * Generate certificate for a single winner
     */
    public function generateSingleCertificate(Request $request, Competition $competition, CertificateService $certificateService)
    {
        $request->validate([
            'submission_id' => 'required|exists:competition_submissions,id'
        ]);

        $submission = CompetitionSubmission::find($request->submission_id);

        if ($submission->competition_id !== $competition->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission does not belong to this competition'
            ], 400);
        }

        $result = $certificateService->generateCertificate($submission);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Generate certificates for all winners in a competition
     */
    public function generateAllCertificates(Competition $competition, CertificateService $certificateService)
    {
        $result = $certificateService->generateAllCertificates($competition);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Download certificate by certificate ID
     */
    public function downloadCertificate(string $certificateId, CertificateService $certificateService)
    {
        $result = $certificateService->downloadCertificate($certificateId);

        if (is_array($result) && !$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message']
            ], 404);
        }

        return $result; // Binary file response
    }

    /**
     * Get certificate details
     */
    public function getCertificateDetails(string $certificateId)
    {
        $submission = CompetitionSubmission::where('certificate_id', $certificateId)
            ->with(['photographer', 'competition'])
            ->first();

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'certificate_id' => $submission->certificate_id,
                'certificate_url' => $submission->certificate_url,
                'generated_at' => $submission->certificate_generated_at,
                'photographer_name' => $submission->photographer->name,
                'competition_name' => $submission->competition->title,
                'photo_title' => $submission->title,
                'award_type' => $submission->award_type,
                'rank' => $submission->rank,
                'final_score' => $submission->final_score,
                'download_url' => url('/api/v1/certificates/' . $certificateId . '/download')
            ]
        ]);
    }

    // Prize Distribution Methods

    /**
     * Set prize for a single winner
     */
    public function setPrize(Request $request, Competition $competition, PrizeDistributionService $prizeService)
    {
        $request->validate([
            'submission_id' => 'required|exists:competition_submissions,id',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $submission = CompetitionSubmission::find($request->submission_id);

        if ($submission->competition_id !== $competition->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission does not belong to this competition'
            ], 400);
        }

        $result = $prizeService->setPrize($submission, [
            'amount' => $request->amount,
            'description' => $request->description,
            'notes' => $request->notes
        ]);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Set all prizes for competition winners
     */
    public function setAllPrizes(Request $request, Competition $competition, PrizeDistributionService $prizeService)
    {
        $request->validate([
            'prizes' => 'required|array',
            'prizes.*.rank' => 'required|integer',
            'prizes.*.amount' => 'required|numeric|min:0',
            'prizes.*.description' => 'nullable|string'
        ]);

        $result = $prizeService->setAllPrizes($competition, $request->prizes);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Update prize status
     */
    public function updatePrizeStatus(Request $request, Competition $competition, PrizeDistributionService $prizeService)
    {
        $request->validate([
            'submission_id' => 'required|exists:competition_submissions,id',
            'status' => 'required|in:pending,processing,delivered,claimed',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $submission = CompetitionSubmission::find($request->submission_id);

        if ($submission->competition_id !== $competition->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission does not belong to this competition'
            ], 400);
        }

        $result = $prizeService->updatePrizeStatus($submission, $request->status, [
            'tracking_number' => $request->tracking_number,
            'notes' => $request->notes
        ]);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Get prize distribution report for a competition
     */
    public function getPrizeReport(Competition $competition, PrizeDistributionService $prizeService)
    {
        $result = $prizeService->getPrizeReport($competition);

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }

    /**
     * Get all pending prizes
     */
    public function getPendingPrizes(PrizeDistributionService $prizeService)
    {
        $result = $prizeService->getPendingPrizes();

        return response()->json([
            'status' => 'success',
            'data' => $result['data'],
            'total' => $result['total']
        ]);
    }

    /**
     * Get global prize statistics
     */
    public function getGlobalPrizeStats(PrizeDistributionService $prizeService)
    {
        $result = $prizeService->getGlobalStatistics();

        return response()->json([
            'status' => 'success',
            'data' => $result['data']
        ]);
    }
}
