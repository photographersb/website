<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotographerStats extends Model
{
    protected $fillable = [
        'photographer_id',
        'profile_views',
        'profile_views_this_month',
        'profile_share_clicks',
        'profile_share_visits',
        'total_points',
        'level',
        'conversion_rate',
        'response_rate',
        'average_response_time',
        'repeat_client_rate',
        'portfolio_completeness',
        'monthly_earnings',
        'growth_metrics',
    ];

    protected $casts = [
        'monthly_earnings' => 'array',
        'growth_metrics' => 'array',
        'conversion_rate' => 'decimal:2',
    ];

    public function photographer()
    {
        return $this->belongsTo(Photographer::class);
    }

    /**
     * Update photographer level based on points
     */
    public function updateLevel()
    {
        $newLevel = $this->calculateLevel($this->total_points);
        if ($newLevel > $this->level) {
            $this->update(['level' => $newLevel]);
            
            // Notify about level up
            \App\Services\PhotographerNotificationService::sendCustomNotification(
                $this->photographer_id,
                "🎉 Level Up! You're now Level {$newLevel}",
                "Congratulations on reaching Level {$newLevel}! Keep up the great work.",
                ['new_level' => $newLevel],
                '/dashboard?tab=achievements',
                'level_up'
            );
        }
    }

    /**
     * Calculate level from points
     */
    private function calculateLevel($points)
    {
        // Level formula: every 100 points = 1 level
        return max(1, floor($points / 100) + 1);
    }

    /**
     * Get points needed for next level
     */
    public function getPointsToNextLevel()
    {
        $nextLevel = $this->level + 1;
        $pointsForNextLevel = ($nextLevel - 1) * 100;
        return max(0, $pointsForNextLevel - $this->total_points);
    }

    /**
     * Track profile view
     */
    public function incrementProfileView()
    {
        $this->increment('profile_views');
        $this->increment('profile_views_this_month');
    }

    /**
     * Calculate conversion rate
     */
    public function updateConversionRate()
    {
        $photographer = $this->photographer;
        $totalViews = $this->profile_views;
        $totalBookings = $photographer->bookings()->count();

        if ($totalViews > 0) {
            $this->conversion_rate = ($totalBookings / $totalViews) * 100;
            $this->save();
        }
    }

    /**
     * Update portfolio completeness
     */
    public function updatePortfolioCompleteness()
    {
        $photographer = $this->photographer;
        $score = 0;

        // Profile picture (10 points)
        if ($photographer->profile_picture) $score += 10;

        // Bio (10 points)
        if ($photographer->bio) $score += 10;

        // Specializations (10 points)
        $specializations = is_array($photographer->specializations) 
            ? $photographer->specializations 
            : ($photographer->specializations ? json_decode($photographer->specializations, true) : []);
        if ($specializations && count($specializations) > 0) $score += 10;

        // Social links (10 points)
        $socialLinks = array_filter([
            $photographer->facebook_url,
            $photographer->instagram_url,
            $photographer->twitter_url,
            $photographer->website_url,
        ]);
        if (count($socialLinks) >= 2) $score += 10;

        // Albums (20 points)
        $albumCount = $photographer->albums()->count();
        if ($albumCount >= 1) $score += 10;
        if ($albumCount >= 3) $score += 10;

        // Packages (20 points)
        $packageCount = $photographer->packages()->count();
        if ($packageCount >= 1) $score += 10;
        if ($packageCount >= 3) $score += 10;

        // Awards (10 points)
        if ($photographer->awards()->count() > 0) $score += 10;

        // Experience (10 points)
        if ($photographer->experience_years >= 2) $score += 10;

        $this->portfolio_completeness = $score;
        $this->save();

        return $score;
    }
}
