<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WinnerCalculationService
{
    /**
     * Calculate winners for a competition
     * 
     * @param Competition $competition
     * @param array $config Configuration for scoring weights
     * @return array
     */
    public function calculateWinners(Competition $competition, array $config = [])
    {
        // Default configuration: use competition weights when available
        $voteWeight = $config['vote_weight'] ?? $competition->vote_weight ?? 0.4;
        $judgeWeight = $config['judge_weight'] ?? $competition->judge_weight ?? 0.6;
        $numberOfWinners = $config['number_of_winners'] ?? 3; // 1st, 2nd, 3rd
        $honorableMentions = $config['honorable_mentions'] ?? 5; // Top 5-10
        
        // Get all approved submissions with scores
        $submissions = CompetitionSubmission::with(['photographer'])
            ->where('competition_id', $competition->id)
            ->where('status', 'approved')
            ->get();
        
        if ($submissions->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No approved submissions found for this competition'
            ];
        }
        
        // Calculate final scores for each submission
        $scoredSubmissions = $submissions->map(function ($submission) use ($voteWeight, $judgeWeight) {
            $voteCount = $submission->vote_count ?? 0;
            $judgeScore = $submission->judge_score ?? 0;
            
            // Find max vote count for normalization (avoid division by zero)
            $maxVotesInCompetition = CompetitionSubmission::where('competition_id', $submission->competition_id)
                ->where('status', 'approved')
                ->max('vote_count') ?? 0;
            
            // Normalize vote count (0-100 scale)
            // If no votes at all in competition, use 0
            if ($maxVotesInCompetition <= 0 && $voteCount <= 0) {
                $normalizedVotes = 0;
            } else {
                $normalizedVotes = $maxVotesInCompetition > 0 
                    ? min(100, ($voteCount / $maxVotesInCompetition) * 100) 
                    : 0;
            }
            
            // Normalize judge score (0-50 becomes 0-100 scale)
            // Judge scores range from 0 to 50 (5 criteria x 10 points each)
            $normalizedJudgeScore = $judgeScore > 0 
                ? min(100, ($judgeScore / 50) * 100) 
                : 0;
            
            // Calculate weighted final score
            $finalScore = ($normalizedVotes * $voteWeight) + ($normalizedJudgeScore * $judgeWeight);
            
            $submission->final_score = round($finalScore, 2);
            $submission->normalized_votes = round($normalizedVotes, 2);
            $submission->normalized_judge_score = round($normalizedJudgeScore, 2);
            
            return $submission;
        });
        
        // Sort by final score (descending), then by judge score, then by vote count for consistency
        $rankedSubmissions = $scoredSubmissions->sortByDesc(function ($submission) {
            return [
                $submission->final_score,
                $submission->judge_score ?? 0,
                $submission->vote_count ?? 0
            ];
        })->values();
        
        // Assign ranks and awards
        $winners = [];
        $rank = 1;
        $previousScore = null;
        
        foreach ($rankedSubmissions as $index => $submission) {
            // If score differs from previous, update rank (can be > 1 if there were ties)
            if ($previousScore !== null && abs($previousScore - $submission->final_score) > 0.01) {
                $rank = $index + 1; // Use index-based ranking (accounts for ties naturally)
            }
            
            $submission->rank = $rank;
            
            // Determine award type
            if ($rank === 1) {
                $submission->award_type = '1st Place';
                $submission->is_winner = true;
                $submission->prize_amount = $this->getPrizeAmount($competition, 1);
            } elseif ($rank === 2) {
                $submission->award_type = '2nd Place';
                $submission->is_winner = true;
                $submission->prize_amount = $this->getPrizeAmount($competition, 2);
            } elseif ($rank === 3) {
                $submission->award_type = '3rd Place';
                $submission->is_winner = true;
                $submission->prize_amount = $this->getPrizeAmount($competition, 3);
            } elseif ($rank <= ($numberOfWinners + $honorableMentions)) {
                $submission->award_type = 'Honorable Mention';
                $submission->is_winner = false;
                $submission->prize_amount = null;
            } else {
                $submission->award_type = null;
                $submission->is_winner = false;
                $submission->prize_amount = null;
            }
            
            if ($submission->is_winner || $submission->award_type === 'Honorable Mention') {
                $winners[] = $submission;
            }
            
            $previousScore = $submission->final_score;
        }
        
        return [
            'success' => true,
            'winners' => $winners,
            'all_submissions' => $rankedSubmissions,
            'config' => [
                'vote_weight' => $voteWeight,
                'judge_weight' => $judgeWeight,
                'total_submissions' => $submissions->count(),
                'winners_count' => collect($winners)->where('is_winner', true)->count(),
                'honorable_mentions_count' => collect($winners)->where('award_type', 'Honorable Mention')->count()
            ]
        ];
    }
    
    /**
     * Announce winners and save to database
     * 
     * @param Competition $competition
     * @param array $config
     * @return array
     */
    public function announceWinners(Competition $competition, array $config = [])
    {
        DB::beginTransaction();
        
        try {
            $result = $this->calculateWinners($competition, $config);
            
            if (!$result['success']) {
                DB::rollBack();
                return $result;
            }
            
            $winners = $result['winners'];
            $allSubmissions = $result['all_submissions'];
            
            // Reset all submissions
            CompetitionSubmission::where('competition_id', $competition->id)
                ->update([
                    'is_winner' => false,
                    'rank' => null,
                    'award_type' => null,
                    'prize_amount' => null,
                    'winner_announced_at' => null,
                    'winner_notes' => null
                ]);
            
            // Update winners
            foreach ($allSubmissions as $submission) {
                CompetitionSubmission::where('id', $submission->id)
                    ->update([
                        'final_score' => $submission->final_score,
                        'rank' => $submission->rank,
                        'is_winner' => $submission->is_winner,
                        'award_type' => $submission->award_type,
                        'prize_amount' => $submission->prize_amount,
                        'winner_announced_at' => $submission->is_winner || $submission->award_type === 'Honorable Mention' 
                            ? now() 
                            : null
                    ]);
            }
            
            // Update competition status to closed
            $competition->update([
                'status' => 'closed'
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Winners announced successfully',
                'winners' => $winners,
                'config' => $result['config']
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Failed to announce winners: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get prize amount for a position based on competition prize pool
     * 
     * @param Competition $competition
     * @param int $position
     * @return float|null
     */
    private function getPrizeAmount(Competition $competition, int $position): ?float
    {
        $prizePool = $competition->prize_pool ?? 0;
        
        if ($prizePool <= 0) {
            return null;
        }
        
        // Default distribution: 50% for 1st, 30% for 2nd, 20% for 3rd
        $distribution = [
            1 => 0.50, // 50%
            2 => 0.30, // 30%
            3 => 0.20, // 20%
        ];
        
        return isset($distribution[$position]) 
            ? round($prizePool * $distribution[$position], 2) 
            : null;
    }
    
    /**
     * Get winners for a competition
     * 
     * @param Competition $competition
     * @return array
     */
    public function getWinners(Competition $competition)
    {
        $winners = CompetitionSubmission::with(['photographer', 'competition'])
            ->where('competition_id', $competition->id)
            ->where('is_winner', true)
            ->orderBy('winner_position', 'asc')
            ->orderByDesc('vote_count')
            ->get();
        
        if (Schema::hasColumn('competition_submissions', 'award_type')) {
            $honorableMentions = CompetitionSubmission::with(['photographer', 'competition'])
                ->where('competition_id', $competition->id)
                ->where('award_type', 'Honorable Mention')
                ->orderBy('winner_position', 'asc')
                ->orderByDesc('vote_count')
                ->get();
        } else {
            $honorableMentions = collect();
        }
        
        return [
            'winners' => $winners,
            'honorable_mentions' => $honorableMentions,
            'total_winners' => $winners->count(),
            'total_honorable_mentions' => $honorableMentions->count()
        ];
    }
    
    /**
     * Get leaderboard for a competition
     * 
     * @param Competition $competition
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLeaderboard(Competition $competition, int $limit = 20)
    {
        return CompetitionSubmission::with(['photographer'])
            ->where('competition_id', $competition->id)
            ->where('status', 'approved')
            ->orderByDesc('vote_count')
            ->orderByDesc('judge_score')
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }
}
