<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\CompetitionJudge;
use App\Models\Judge;
use App\Models\User;

class JudgePolicy
{
    /**
     * Determine if user is a judge
     */
    public function isJudge(User $user): bool
    {
        return $user->role === 'judge';
    }

    /**
     * Determine if judge can score submissions for a competition
     */
    public function scoreCompetition(User $user, Competition $competition): bool
    {
        if ($user->role !== 'judge') {
            return false;
        }

        // Check if user is assigned as judge to this competition
        $judgeProfile = Judge::where('user_id', $user->id)->first();
        
        return CompetitionJudge::where('competition_id', $competition->id)
            ->where(function ($q) use ($user, $judgeProfile) {
                $q->where('judge_id', $user->id)
                  ->orWhere(function ($q2) use ($judgeProfile) {
                      if ($judgeProfile) {
                          $q2->where('judge_profile_id', $judgeProfile->id);
                      }
                  });
            })
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Determine if judging period is active
     */
    public function canScoreNow(User $user, Competition $competition): bool
    {
        if (!$this->scoreCompetition($user, $competition)) {
            return false;
        }

        $now = now();
        
        // Check if judging period is active
        if ($competition->judging_start_at && $now < $competition->judging_start_at) {
            return false;
        }

        if ($competition->judging_end_at && $now > $competition->judging_end_at) {
            return false;
        }

        return true;
    }
}
