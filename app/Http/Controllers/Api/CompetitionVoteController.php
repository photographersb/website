<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\CompetitionVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetitionVoteController extends Controller
{
    /**
     * Vote for a submission
     */
    public function vote(Request $request, $competitionId, $submissionId)
    {
        $user = $request->user();
        
        // Get competition and submission
        $competition = Competition::findOrFail($competitionId);
        $submission = CompetitionSubmission::forCompetition($competitionId)
            ->findOrFail($submissionId);
        
        // Check if submission is approved
        if ($submission->status !== 'approved') {
            return response()->json([
                'status' => 'error',
                'message' => 'Can only vote on approved submissions'
            ], 403);
        }
        
        // Check if competition is in voting phase
        if ($competition->status !== 'published' && $competition->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Competition is not accepting votes at this time'
            ], 403);
        }
        
        // Check voting deadline
        if ($competition->voting_deadline && now()->isAfter($competition->voting_deadline)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voting deadline has passed'
            ], 403);
        }
        
        // Prevent voting on own submission
        if ($submission->photographer_id === $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot vote on your own submission'
            ], 403);
        }
        
        // Check if user already voted
        $existingVote = CompetitionVote::where('submission_id', $submissionId)
            ->where('voter_id', $user->id)
            ->first();
        
        if ($existingVote) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already voted on this submission'
            ], 400);
        }
        
        // Create vote
        DB::transaction(function () use ($submission, $user, $competitionId, $submissionId, $request) {
            CompetitionVote::create([
                'submission_id' => $submissionId,
                'voter_id' => $user->id,
                'competition_id' => $competitionId,
                'vote_value' => 1,
                'voted_at' => now(),
                'ip_address' => $request->ip(),
                'is_verified' => true,
                'is_valid' => true
            ]);
            
            // Increment vote count
            $submission->incrementVoteCount();
        });
        
        return response()->json([
            'status' => 'success',
            'message' => 'Vote recorded successfully',
            'data' => [
                'vote_count' => $submission->fresh()->vote_count
            ]
        ]);
    }
    
    /**
     * Remove vote from a submission
     */
    public function unvote(Request $request, $competitionId, $submissionId)
    {
        $user = $request->user();
        
        $vote = CompetitionVote::where('submission_id', $submissionId)
            ->where('voter_id', $user->id)
            ->first();
        
        if (!$vote) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vote not found'
            ], 404);
        }
        
        $submission = CompetitionSubmission::findOrFail($submissionId);
        
        DB::transaction(function () use ($vote, $submission) {
            $vote->delete();
            $submission->decrementVoteCount();
        });
        
        return response()->json([
            'status' => 'success',
            'message' => 'Vote removed successfully',
            'data' => [
                'vote_count' => $submission->fresh()->vote_count
            ]
        ]);
    }
    
    /**
     * Check if user has voted on a submission
     */
    public function checkVote(Request $request, $competitionId, $submissionId)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'has_voted' => false
                ]
            ]);
        }
        
        $hasVoted = CompetitionVote::where('submission_id', $submissionId)
            ->where('voter_id', $user->id)
            ->exists();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'has_voted' => $hasVoted
            ]
        ]);
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
        
        return response()->json([
            'status' => 'success',
            'data' => $votes
        ]);
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
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_votes' => $totalVotes,
                'unique_voters' => $uniqueVoters,
                'top_submissions' => $topSubmissions
            ]
        ]);
    }
}
