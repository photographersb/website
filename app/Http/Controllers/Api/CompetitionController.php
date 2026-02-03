<?php

namespace App\Http\Controllers\Api;

use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\CompetitionVote;
use App\Http\Traits\ApiResponse;
use App\Services\WinnerCalculationService;
use App\Services\CertificateService;
use App\Services\PrizeDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompetitionController extends Controller
{
    use ApiResponse;
    /**
     * Get competition stats (cached for 15 minutes)
     */
    public function stats()
    {
        return Cache::remember('competition_stats', 900, function () {
            $stats = [
                'active_competitions' => Competition::where('status', 'active')->count(),
                'total_prize_pool' => Competition::whereIn('status', ['published', 'active'])->sum('total_prize_pool'),
                'total_submissions' => Competition::whereIn('status', ['published', 'active'])->sum('total_submissions'),
                'total_participants' => \App\Models\CompetitionSubmission::whereIn('status', ['published', 'active'])->distinct('photographer_id')->count(),
            ];

            return $this->success($stats, 'Competition stats retrieved successfully');
        });
    }

    /**
     * Get all competitions with filters, sorting, and pagination
     * Public dashboard: shows published competitions only
     * Include featured competitions first
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12);
        $page = $request->get('page', 1);
        
        $cacheKey = 'competitions:list:' . md5(json_encode($request->all()));
        
        return Cache::remember($cacheKey, 1800, function () use ($request, $perPage, $page) {
            $query = Competition::query();
            
            // Show published and active competitions to public
            $query->whereIn('status', ['published', 'active']);

            // Filter by category
            if ($request->has('category') && $request->category != '') {
                $query->where('theme', 'like', '%' . $request->category . '%');
            }

            // Filter by theme/keyword search
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('theme', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by paid/free
            if ($request->has('is_paid') && $request->is_paid !== '') {
                $query->where('is_paid_competition', filter_var($request->is_paid, FILTER_VALIDATE_BOOLEAN));
            }

            // Eager load relationships with counts to prevent N+1
            $query->with([
                'admin:id,name,email',
                'organizer.user:id,name',
                'category:id,name,slug',
            ])
            ->withCount(['submissions', 'votes', 'prizes']);

            // Featured scope ordering
            $featuredScope = $request->get('featured_scope', 'global');
            $now = now();

            if ($featuredScope === 'area' && $request->filled('city_id')) {
                $cityId = $request->city_id;
                $query->orderByRaw(
                    "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) AND EXISTS (SELECT 1 FROM photographers p WHERE p.id = competitions.organizer_id AND p.city_id = ?) THEN 2 WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                    [$now, $cityId, $now]
                );
            } elseif ($featuredScope === 'category' && $request->filled('category')) {
                $category = $request->category;
                $query->orderByRaw(
                    "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) AND theme LIKE ? THEN 2 WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                    [$now, "%{$category}%", $now]
                );
            } else {
                $query->orderByRaw(
                    "CASE WHEN is_featured = 1 AND (featured_until IS NULL OR featured_until >= ?) THEN 1 ELSE 0 END DESC",
                    [$now]
                );
            }

            // Sorting: featured first, then by newest
            $sort = $request->get('sort', 'featured-newest');
            switch ($sort) {
                case 'deadline':
                    $query->orderBy('submission_deadline', 'asc');
                    break;
                case 'prize':
                    $query->orderBy('total_prize_pool', 'desc');
                    break;
                case 'submissions':
                    $query->orderBy('total_submissions', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'featured-newest':
                default:
                    // Featured scope applied, then newest
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $competitions = $query->paginate($perPage, ['*'], 'page', $page);

            return $this->paginated($competitions, 'Competitions retrieved successfully');
        });
    }

    /**
     * Get competition details with prizes, sponsors, and top submissions
     */
    public function show(Competition $competition)
    {
        // Check if competition is published or active (public only)
        if (!in_array($competition->status, ['published', 'active'])) {
            return $this->notFound('This competition is not available');
        }

        $cacheKey = "competition:{$competition->id}:details";
        
        $competition = Cache::remember($cacheKey, 3600, function () use ($competition) {
            // Eager load relationships to avoid N+1 queries
            return $competition->load([
            'admin',
            'organizer.user',
            'prizes' => function ($q) {
                $q->orderBy('rank');
            },
            'sponsors',
            'mentors' => function ($q) {
                $q->where('mentors.is_active', true);
            },
            'judgeProfiles' => function ($q) {
                $q->where('judges.is_active', true);
            },
            'submissions' => function ($q) {
                $q->where('status', 'approved')
                  ->with('photographer:id,name')
                  ->orderBy('vote_count', 'desc')
                  ->limit(10);
            },
            ]);
        });

        return $this->success($competition, 'Competition details retrieved successfully');
    }

    /**
     * Submit photo to competition
     */
    public function submit(Request $request, Competition $competition)
    {
        // Check if competition is accepting submissions
        if (now() > $competition->submission_deadline) {
            return $this->error('Submission deadline has passed', 422);
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
            return $this->error('You have reached the maximum number of submissions', 422);
        }

        $submission = CompetitionSubmission::create([
            ...$validated,
            'competition_id' => $competition->id,
            'photographer_id' => auth()->id(),
            'status' => 'pending_review',
        ]);

        return $this->created($submission, 'Photo submitted successfully');
    }

    /**
     * Vote on submission (with fraud prevention)
     */
    public function vote(Request $request, CompetitionSubmission $submission)
    {
        $competition = $submission->competition;

        // Check if voting is active
        if (now() < $competition->voting_start_at || now() > $competition->voting_end_at) {
            return $this->error('Voting is not currently active', 422);
        }

        // Check daily vote limit
        $voteCountToday = CompetitionVote::where('competition_id', $competition->id)
            ->where('voter_id', auth()->id())
            ->whereDate('voted_at', today())
            ->count();

        if ($voteCountToday >= 50) {
            return $this->error('You have reached your daily voting limit', 422);
        }

        // Check if already voted on this submission
        $existingVote = CompetitionVote::where('submission_id', $submission->id)
            ->where('voter_id', auth()->id())
            ->first();

        if ($existingVote) {
            return $this->error('You have already voted on this submission', 422);
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

        return $this->created($vote, 'Vote recorded');
    }

    /**
     * Get competition leaderboard
     */
    public function leaderboard(Competition $competition)
    {
        // Enforce pagination limits to prevent DoS
        $perPage = min(request()->get('per_page', 50), 100);
        
        $submissions = CompetitionSubmission::where('competition_id', $competition->id)
            ->where('status', 'approved')
            ->with(['photographer', 'votes'])
            ->orderBy('vote_count', 'desc')
            ->paginate($perPage);

        return $this->paginated($submissions, 'Leaderboard retrieved successfully');
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
            return $this->error($result['message'], 400);
        }
        
        return $this->success([
            'winners' => $result['winners'],
            'config' => $result['config']
        ], 'Winner calculation preview');
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
            return $this->error($result['message'], 400);
        }
        
        // TODO: Send winner notifications via email
        
        return $this->success([
            'winners' => $result['winners'],
            'config' => $result['config']
        ], $result['message']);
    }
    
    /**
     * Get winners for a competition
     */
    public function getWinners(Competition $competition, WinnerCalculationService $winnerService)
    {
        $result = $winnerService->getWinners($competition);
        
        return $this->success($result, 'Winners retrieved successfully');
    }
    
    /**
     * Get detailed leaderboard with rankings
     */
    public function getLeaderboard(Request $request, Competition $competition, WinnerCalculationService $winnerService)
    {
        $limit = $request->input('limit', 20);
        $leaderboard = $winnerService->getLeaderboard($competition, $limit);
        
        return $this->success($leaderboard, 'Leaderboard retrieved successfully');
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
            return $this->error('Submission does not belong to this competition', 400);
        }

        $result = $certificateService->generateCertificate($submission);

        return $result['success'] 
            ? $this->success($result, $result['message']) 
            : $this->error($result['message'], 400);
    }

    /**
     * Generate certificates for all winners in a competition
     */
    public function generateAllCertificates(Competition $competition, CertificateService $certificateService)
    {
        $result = $certificateService->generateAllCertificates($competition);

        return $result['success'] 
            ? $this->success($result, $result['message']) 
            : $this->error($result['message'], 400);
    }

    /**
     * Download certificate by certificate ID
     */
    public function downloadCertificate(string $certificateId, CertificateService $certificateService)
    {
        $result = $certificateService->downloadCertificate($certificateId);

        if (is_array($result) && !$result['success']) {
            return $this->notFound($result['message']);
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
            return $this->notFound('Certificate not found');
        }

        return $this->success([
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
        ], 'Certificate details retrieved successfully');
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
            return $this->error('Submission does not belong to this competition', 400);
        }

        $result = $prizeService->setPrize($submission, [
            'amount' => $request->amount,
            'description' => $request->description,
            'notes' => $request->notes
        ]);

        return $result['success'] 
            ? $this->success($result['data'] ?? null, $result['message']) 
            : $this->error($result['message'], 400);
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

        return $result['success'] 
            ? $this->success($result['data'] ?? null, $result['message']) 
            : $this->error($result['message'], 400);
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
            return $this->error('Submission does not belong to this competition', 400);
        }

        $result = $prizeService->updatePrizeStatus($submission, $request->status, [
            'tracking_number' => $request->tracking_number,
            'notes' => $request->notes
        ]);

        return $result['success'] 
            ? $this->success($result['data'] ?? null, $result['message']) 
            : $this->error($result['message'], 400);
    }

    /**
     * Get prize distribution report for a competition
     */
    public function getPrizeReport(Competition $competition, PrizeDistributionService $prizeService)
    {
        $result = $prizeService->getPrizeReport($competition);

        return $this->success($result['data'], 'Prize report retrieved successfully');
    }

    /**
     * Get all pending prizes
     */
    public function getPendingPrizes(PrizeDistributionService $prizeService)
    {
        $result = $prizeService->getPendingPrizes();

        return $this->success([
            'data' => $result['data'],
            'total' => $result['total']
        ], 'Pending prizes retrieved successfully');
    }

    /**
     * Get global prize statistics
     */
    public function getGlobalPrizeStats(PrizeDistributionService $prizeService)
    {
        $result = $prizeService->getGlobalStatistics();

        return $this->success($result['data'], 'Global prize statistics retrieved successfully');
    }
}
