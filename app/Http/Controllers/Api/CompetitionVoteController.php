<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\CompetitionVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CompetitionVoteController extends Controller
{
    use ApiResponse;
    /**
     * Vote for a submission (with distributed lock to prevent race conditions)
     */
    public function vote(Request $request, Competition $competition, CompetitionSubmission $submission)
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('Login required to vote', 401);
        }
        
        // Distributed lock to prevent duplicate votes from rapid clicking
        $lockKey = "vote:lock:{$user->id}:{$submission->id}";
        $lock = Cache::lock($lockKey, 5);
        
        if (!$lock->get()) {
            return $this->error('Please wait before voting again', 429);
        }
        
        try {
            if ($submission->competition_id !== $competition->id) {
                return $this->error('Submission does not belong to this competition', 404);
            }
        
        // Check if submission is approved
        if ($submission->status !== 'approved') {
            return $this->error('Can only vote on approved submissions', 403);
        }
        
        // Check if competition is in voting phase
        if ($competition->status !== 'published' && $competition->status !== 'active') {
            return $this->error('Competition is not accepting votes at this time', 403);
        }

        if (!$competition->voting_enabled && !$competition->allow_public_voting) {
            return $this->error('Public voting is disabled for this competition', 403);
        }

        $votingStart = $competition->voting_start_date ?? $competition->voting_start_at;
        $votingEnd = $competition->voting_end_date ?? $competition->voting_end_at;

        if ($votingStart && now()->isBefore($votingStart)) {
            return $this->error('Voting has not started yet', 403);
        }

        if ($votingEnd && now()->isAfter($votingEnd)) {
            return $this->error('Voting deadline has passed', 403);
        }
        
        // Check voting deadline
        // Legacy deadline field check (if present)
        if (!empty($competition->voting_deadline) && now()->isAfter($competition->voting_deadline)) {
            return $this->error('Voting deadline has passed', 403);
        }
        
        // Prevent voting on own submission
        if ($submission->photographer_id === $user->id) {
            return $this->error('Cannot vote on your own submission', 403);
        }
        
        // Check if user already voted
        $existingVote = CompetitionVote::where('submission_id', $submission->id)
            ->where(function ($query) use ($user) {
                $query->where('voter_id', $user->id)
                    ->orWhere('voter_user_id', $user->id);
            })
            ->first();
        
        if ($existingVote) {
            return $this->error('You have already voted on this submission', 400);
        }
        
        // Create vote
        DB::transaction(function () use ($submission, $user, $competition, $request) {
            CompetitionVote::create([
            'submission_id' => $submission->id,
                'voter_id' => $user->id,
                'voter_user_id' => $user->id,
            'competition_id' => $competition->id,
                'vote_value' => 1,
                'voted_at' => now(),
                'ip_address' => $request->ip(),
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'is_verified' => true,
                'is_valid' => true
            ]);
            
            // Increment vote count
            $submission->incrementVoteCount();
        });
        
        // Clear cache
        Cache::forget("competition:{$competition->id}:details");
        
        return $this->success([
            'vote_count' => $submission->fresh()->vote_count
        ], 'Vote recorded successfully');
        } finally {
            $lock->release();
        }
    }
    
    /**
     * Remove vote from a submission
     */
    public function unvote(Request $request, Competition $competition, CompetitionSubmission $submission)
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('Login required to remove vote', 401);
        }

        if ($submission->competition_id !== $competition->id) {
            return $this->error('Submission does not belong to this competition', 404);
        }
        
        $vote = CompetitionVote::where('submission_id', $submission->id)
            ->where('voter_id', $user->id)
            ->first();
        
        if (!$vote) {
            return $this->notFound('Vote not found');
        }
        
        DB::transaction(function () use ($vote, $submission) {
            $vote->delete();
            $submission->decrementVoteCount();
        });
        
        return $this->success([
            'vote_count' => $submission->fresh()->vote_count
        ], 'Vote removed successfully');
    }
    
    /**
     * Check if user has voted on a submission
     */
    public function checkVote(Request $request, Competition $competition, CompetitionSubmission $submission)
    {
        $user = $request->user();
        
        if (!$user) {
            return $this->success([
                'has_voted' => false
            ], 'Vote check retrieved successfully');
        }
        
        if ($submission->competition_id !== $competition->id) {
            return $this->success([
                'has_voted' => false
            ], 'Vote check retrieved successfully');
        }

        $hasVoted = CompetitionVote::where('submission_id', $submission->id)
            ->where('voter_id', $user->id)
            ->exists();
        
        return $this->success([
            'has_voted' => $hasVoted
        ], 'Vote check retrieved successfully');
    }
    
    /**
     * Get user's votes for a competition
     */
    public function myVotes(Request $request, $competitionId)
    {
        $user = $request->user();
        
        $votes = CompetitionVote::with('submission')
            ->where('competition_id', $competitionId)
            ->where('voter_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return $this->success($votes, 'My votes retrieved successfully');
    }
    
    /**
     * Get vote statistics for a competition
     */
    public function stats($competitionId)
    {
        $totalVotes = CompetitionVote::where('competition_id', $competitionId)->count();
        $uniqueVoters = CompetitionVote::where('competition_id', $competitionId)
            ->distinct('voter_id')
            ->count('voter_id');
        
        $topSubmissions = CompetitionSubmission::forCompetition($competitionId)
            ->approved()
            ->orderBy('vote_count', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'vote_count', 'thumbnail_url']);
        
        return $this->success([
            'total_votes' => $totalVotes,
            'unique_voters' => $uniqueVoters,
            'top_submissions' => $topSubmissions
        ], 'Vote statistics retrieved successfully');
    }
}
