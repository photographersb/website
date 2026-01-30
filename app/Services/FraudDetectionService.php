<?php

namespace App\Services;

use App\Models\CompetitionVote;
use App\Models\CompetitionSubmission;

class FraudDetectionService
{
    /**
     * Detect suspicious voting patterns
     */
    public function detectVoteFraud(CompetitionVote $vote)
    {
        $fraudScore = 0;
        $isValid = true;

        // Check IP reputation
        if ($this->isBlacklistedIP($vote->ip_address)) {
            $fraudScore += 30;
            $isValid = false;
        }

        // Check device fingerprint
        if ($this->isDuplicateDevice($vote->device_fingerprint)) {
            $fraudScore += 20;
        }

        // Check voting velocity (too many votes in short time)
        $recentVotes = CompetitionVote::where('voter_id', $vote->voter_id)
            ->whereDate('voted_at', today())
            ->count();

        if ($recentVotes > 50) {
            $fraudScore += 25;
            $isValid = false;
        }

        // Check for voting on same submission from multiple IPs
        $votersOnSubmission = CompetitionVote::where('submission_id', $vote->submission_id)
            ->distinct('ip_address')
            ->count('ip_address');

        if ($votersOnSubmission > 10) {
            $fraudScore += 15;
        }

        $vote->update([
            'is_valid' => $isValid,
            'fraud_score' => $fraudScore,
        ]);

        return $isValid;
    }

    /**
     * Check if IP is blacklisted
     */
    private function isBlacklistedIP($ipAddress)
    {
        // TODO: Implement IP blacklist check
        return false;
    }

    /**
     * Check for duplicate device fingerprints
     */
    private function isDuplicateDevice($fingerprint)
    {
        $count = CompetitionVote::where('device_fingerprint', $fingerprint)->count();
        return $count > 5;
    }
}
