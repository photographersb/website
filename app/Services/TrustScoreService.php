<?php

namespace App\Services;

use App\Models\Photographer;
use App\Models\TrustScore;
use App\Models\Review;

class TrustScoreService
{
    /**
     * Calculate trust score for photographer
     */
    public function calculateTrustScore(Photographer $photographer)
    {
        $score = 0;

        // Verification (max 20 points)
        $verificationPoints = 0;
        $verificationPoints += $photographer->trustScore?->phone_verified ? 5 : 0;
        $verificationPoints += $photographer->trustScore?->email_verified ? 5 : 0;
        $verificationPoints += $photographer->trustScore?->id_verified ? 10 : 0;
        $score += $verificationPoints;

        // Reviews (max 25 points)
        $reviewCount = $photographer->reviews()->where('status', 'published')->count();
        if ($reviewCount > 0) {
            $avgRating = $photographer->average_rating;
            $score += min(25, ($avgRating / 5) * 25);
        }

        // Booking completion rate (max 30 points)
        $completionRate = $photographer->completed_bookings / max(1, $photographer->total_bookings);
        $score += $completionRate * 30;

        // Response time (max 15 points)
        $avgResponseTime = $photographer->response_time_avg;
        if ($avgResponseTime <= 2) {
            $score += 15;
        } elseif ($avgResponseTime <= 6) {
            $score += 10;
        } elseif ($avgResponseTime <= 24) {
            $score += 5;
        }

        // Profile completeness (max 10 points)
        $score += $photographer->profile_completeness;

        // Cap at 100
        $score = min(100, max(0, $score));

        // Determine badge
        $badge = match (true) {
            $score >= 80 => 'elite',
            $score >= 60 => 'trusted',
            $score >= 40 => 'verified',
            default => 'none',
        };

        return [
            'overall_score' => $score,
            'trust_badge' => $badge,
        ];
    }

    /**
     * Update trust score in database
     */
    public function updateTrustScore(Photographer $photographer)
    {
        $calculation = $this->calculateTrustScore($photographer);

        TrustScore::updateOrCreate(
            ['photographer_id' => $photographer->id],
            [
                'overall_score' => $calculation['overall_score'],
                'trust_badge' => $calculation['trust_badge'],
                'review_count' => $photographer->reviews()->where('status', 'published')->count(),
                'average_rating' => $photographer->average_rating,
                'booking_completion_rate' => $photographer->total_bookings > 0
                    ? ($photographer->completed_bookings / $photographer->total_bookings) * 100
                    : 0,
                'response_time_avg' => $photographer->response_time_avg,
                'profile_completeness' => $photographer->profile_completeness,
            ]
        );
    }
}
