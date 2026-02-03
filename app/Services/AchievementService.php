<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Photographer;
use App\Models\PhotographerAchievement;
use App\Models\PhotographerStats;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    /**
     * Initialize stats for a photographer
     */
    public static function initializeStats($photographerId)
    {
        $stats = PhotographerStats::firstOrCreate(
            ['photographer_id' => $photographerId],
            [
                'profile_views' => 0,
                'profile_views_this_month' => 0,
                'total_points' => 0,
                'level' => 1,
                'conversion_rate' => 0,
                'response_rate' => 0,
                'average_response_time' => 0,
                'repeat_client_rate' => 0,
                'portfolio_completeness' => 0,
                'monthly_earnings' => [],
                'growth_metrics' => [],
            ]
        );

        return $stats;
    }

    /**
     * Track booking created
     */
    public static function trackBookingCreated($photographerId, $bookingAmount = 0)
    {
        self::initializeStats($photographerId);
        
        $photographer = Photographer::find($photographerId);
        $bookingCount = $photographer->bookings()->count();

        // Track booking milestones
        self::updateProgress($photographerId, 'first_booking', 1);
        self::updateProgress($photographerId, 'booking_milestone_10', $bookingCount);
        self::updateProgress($photographerId, 'booking_milestone_50', $bookingCount);
        self::updateProgress($photographerId, 'booking_milestone_100', $bookingCount);

        // Track earnings
        if ($bookingAmount > 0) {
            $totalEarnings = $photographer->bookings()
                ->where('status', 'completed')
                ->sum('total_amount');
            
            self::updateProgress($photographerId, 'first_payment', 1);
            self::updateProgress($photographerId, 'earnings_10k', $totalEarnings);
            self::updateProgress($photographerId, 'earnings_100k', $totalEarnings);
        }

        // Update conversion rate
        $stats = PhotographerStats::where('photographer_id', $photographerId)->first();
        if ($stats) {
            $stats->updateConversionRate();
        }
    }

    /**
     * Track review received
     */
    public static function trackReviewReceived($photographerId, $rating)
    {
        self::initializeStats($photographerId);
        
        $photographer = Photographer::find($photographerId);

        // Track five-star reviews
        if ($rating == 5) {
            $fiveStarCount = $photographer->reviews()->where('rating', 5)->count();
            self::updateProgress($photographerId, 'first_five_star', 1);
            self::updateProgress($photographerId, 'five_star_streak_10', $fiveStarCount);
        }

        // Track perfect score (5.0 average with 20+ reviews)
        $reviewCount = $photographer->reviews()->count();
        $averageRating = $photographer->reviews()->avg('rating');
        if ($reviewCount >= 20 && $averageRating == 5.0) {
            self::updateProgress($photographerId, 'perfect_score', $reviewCount);
        }
    }

    /**
     * Track competition submission
     */
    public static function trackCompetitionSubmission($photographerId)
    {
        self::initializeStats($photographerId);
        self::updateProgress($photographerId, 'first_competition', 1);
    }

    /**
     * Track competition win
     */
    public static function trackCompetitionWin($photographerId)
    {
        self::initializeStats($photographerId);
        
        $photographer = Photographer::find($photographerId);
        $winCount = $photographer->competition_submissions()
            ->where('is_winner', true)
            ->count();

        self::updateProgress($photographerId, 'competition_winner', 1);
        self::updateProgress($photographerId, 'competition_champion', $winCount);
    }

    /**
     * Track profile view
     */
    public static function trackProfileView($photographerId)
    {
        $stats = self::initializeStats($photographerId);
        $stats->incrementProfileView();

        // Track view milestones
        self::updateProgress($photographerId, 'profile_views_100', $stats->profile_views);
        self::updateProgress($photographerId, 'profile_views_1000', $stats->profile_views);

        // Update conversion rate
        $stats->updateConversionRate();
    }

    /**
     * Track album created
     */
    public static function trackAlbumCreated($photographerId)
    {
        self::initializeStats($photographerId);
        
        $photographer = Photographer::find($photographerId);
        $albumCount = $photographer->albums()->count();
        $totalPhotos = $photographer->albums()->withCount('photos')->get()->sum('photos_count');

        self::updateProgress($photographerId, 'first_album', 1);
        self::updateProgress($photographerId, 'portfolio_master', $totalPhotos);

        // Update portfolio completeness
        $stats = PhotographerStats::where('photographer_id', $photographerId)->first();
        if ($stats) {
            $stats->updatePortfolioCompleteness();
        }
    }

    /**
     * Track package created
     */
    public static function trackPackageCreated($photographerId)
    {
        self::initializeStats($photographerId);
        
        $photographer = Photographer::find($photographerId);
        $packageCount = $photographer->packages()->count();

        self::updateProgress($photographerId, 'first_package', 1);
        self::updateProgress($photographerId, 'package_pro', $packageCount);

        // Update portfolio completeness
        $stats = PhotographerStats::where('photographer_id', $photographerId)->first();
        if ($stats) {
            $stats->updatePortfolioCompleteness();
        }
    }

    /**
     * Track profile completion
     */
    public static function trackProfileUpdate($photographerId)
    {
        $stats = self::initializeStats($photographerId);
        $completeness = $stats->updatePortfolioCompleteness();

        self::updateProgress($photographerId, 'first_profile_complete', $completeness >= 30 ? 1 : 0);
        self::updateProgress($photographerId, 'complete_profile', $completeness);
    }

    /**
     * Track event attendance
     */
    public static function trackEventAttendance($photographerId)
    {
        self::initializeStats($photographerId);
        self::updateProgress($photographerId, 'event_participant', 1);
    }

    /**
     * Track repeat client
     */
    public static function trackRepeatClient($photographerId)
    {
        self::initializeStats($photographerId);
        
        $photographer = Photographer::find($photographerId);
        
        // Count clients who have booked more than once
        $repeatClients = $photographer->bookings()
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        self::updateProgress($photographerId, 'repeat_clients', $repeatClients);
    }

    /**
     * Update response time stats
     */
    public static function updateResponseTimeStats($photographerId, $responseTimeInHours)
    {
        $stats = self::initializeStats($photographerId);
        
        // Update average response time
        $stats->average_response_time = $responseTimeInHours;
        $stats->save();

        // Track fast responder achievement (under 2 hours)
        if ($responseTimeInHours < 2) {
            self::updateProgress($photographerId, 'fast_responder', 1);
        }
    }

    /**
     * Mark photographer as verified
     */
    public static function markVerified($photographerId)
    {
        self::initializeStats($photographerId);
        self::updateProgress($photographerId, 'verified_photographer', 1);
    }

    /**
     * Update achievement progress
     */
    private static function updateProgress($photographerId, $achievementKey, $newProgress)
    {
        $achievement = Achievement::where('key', $achievementKey)
            ->where('is_active', true)
            ->first();

        if (!$achievement) {
            return;
        }

        $photographerAchievement = PhotographerAchievement::firstOrCreate(
            [
                'photographer_id' => $photographerId,
                'achievement_id' => $achievement->id,
            ],
            [
                'progress' => 0,
                'is_unlocked' => false,
            ]
        );

        // Update progress if new value is higher
        if ($newProgress > $photographerAchievement->progress) {
            $photographerAchievement->progress = $newProgress;
            $photographerAchievement->save();

            // Check if achievement should be unlocked
            $photographerAchievement->checkAndUnlock();
        }
    }

    /**
     * Get photographer's achievement stats
     */
    public static function getAchievementStats($photographerId)
    {
        $stats = self::initializeStats($photographerId);
        
        $totalAchievements = Achievement::where('is_active', true)->count();
        $unlockedAchievements = PhotographerAchievement::where('photographer_id', $photographerId)
            ->where('is_unlocked', true)
            ->count();

        $achievements = Achievement::where('is_active', true)
            ->with(['photographer_achievements' => function ($query) use ($photographerId) {
                $query->where('photographer_id', $photographerId);
            }])
            ->get()
            ->map(function ($achievement) {
                $photographerAchievement = $achievement->photographer_achievements->first();
                return [
                    'id' => $achievement->id,
                    'key' => $achievement->key,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'icon' => $achievement->icon,
                    'badge_color' => $achievement->badge_color,
                    'required_count' => $achievement->required_count,
                    'points' => $achievement->points,
                    'category' => $achievement->category,
                    'progress' => $photographerAchievement ? $photographerAchievement->progress : 0,
                    'is_unlocked' => $photographerAchievement ? $photographerAchievement->is_unlocked : false,
                    'unlocked_at' => $photographerAchievement ? $photographerAchievement->unlocked_at : null,
                    'progress_percentage' => $achievement->required_count > 0 
                        ? min(100, round(($photographerAchievement ? $photographerAchievement->progress : 0) / $achievement->required_count * 100, 1))
                        : 0,
                ];
            })
            ->groupBy('category');

        return [
            'stats' => $stats,
            'total_achievements' => $totalAchievements,
            'unlocked_achievements' => $unlockedAchievements,
            'completion_percentage' => $totalAchievements > 0 ? round(($unlockedAchievements / $totalAchievements) * 100, 1) : 0,
            'achievements_by_category' => $achievements,
            'points_to_next_level' => $stats->getPointsToNextLevel(),
        ];
    }
}
