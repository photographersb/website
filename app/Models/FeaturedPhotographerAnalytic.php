<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedPhotographerAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'featured_photographer_id',
        'date',
        'views',
        'profile_clicks',
        'portfolio_clicks',
        'inquiry_messages',
        'booking_requests',
        'conversion_rate',
    ];

    protected $casts = [
        'date' => 'date',
        'views' => 'integer',
        'profile_clicks' => 'integer',
        'portfolio_clicks' => 'integer',
        'inquiry_messages' => 'integer',
        'booking_requests' => 'integer',
        'conversion_rate' => 'float',
    ];

    /**
     * Relationship: belongs to FeaturedPhotographer
     */
    public function featuredPhotographer()
    {
        return $this->belongsTo(FeaturedPhotographer::class);
    }

    /**
     * Record a view
     */
    public static function recordView($featuredPhotographerId)
    {
        $today = now()->toDateString();
        
        self::updateOrCreate(
            ['featured_photographer_id' => $featuredPhotographerId, 'date' => $today],
            ['views' => \DB::raw('views + 1')]
        );
    }

    /**
     * Record a profile click
     */
    public static function recordProfileClick($featuredPhotographerId)
    {
        $today = now()->toDateString();
        
        self::updateOrCreate(
            ['featured_photographer_id' => $featuredPhotographerId, 'date' => $today],
            ['profile_clicks' => \DB::raw('profile_clicks + 1')]
        );
    }

    /**
     * Record a portfolio click
     */
    public static function recordPortfolioClick($featuredPhotographerId)
    {
        $today = now()->toDateString();
        
        self::updateOrCreate(
            ['featured_photographer_id' => $featuredPhotographerId, 'date' => $today],
            ['portfolio_clicks' => \DB::raw('portfolio_clicks + 1')]
        );
    }

    /**
     * Record an inquiry message
     */
    public static function recordInquiry($featuredPhotographerId)
    {
        $today = now()->toDateString();
        
        self::updateOrCreate(
            ['featured_photographer_id' => $featuredPhotographerId, 'date' => $today],
            ['inquiry_messages' => \DB::raw('inquiry_messages + 1')]
        );
    }

    /**
     * Record a booking request
     */
    public static function recordBookingRequest($featuredPhotographerId)
    {
        $today = now()->toDateString();
        
        self::updateOrCreate(
            ['featured_photographer_id' => $featuredPhotographerId, 'date' => $today],
            ['booking_requests' => \DB::raw('booking_requests + 1')]
        );
    }

    /**
     * Calculate conversion rate
     */
    public function calculateConversionRate()
    {
        if ($this->views == 0) {
            return 0;
        }

        $conversions = $this->inquiry_messages + $this->booking_requests;
        return ($conversions / $this->views) * 100;
    }

    /**
     * Scope: By date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get summary statistics for a featured photographer
     */
    public static function getSummary($featuredPhotographerId, $days = 30)
    {
        $startDate = now()->subDays($days);

        $analytics = self::where('featured_photographer_id', $featuredPhotographerId)
            ->where('date', '>=', $startDate)
            ->get();

        return [
            'total_views' => $analytics->sum('views'),
            'total_profile_clicks' => $analytics->sum('profile_clicks'),
            'total_portfolio_clicks' => $analytics->sum('portfolio_clicks'),
            'total_inquiries' => $analytics->sum('inquiry_messages'),
            'total_bookings' => $analytics->sum('booking_requests'),
            'avg_daily_views' => $analytics->count() > 0 ? $analytics->sum('views') / $analytics->count() : 0,
            'conversion_rate' => $analytics->count() > 0 
                ? (($analytics->sum('inquiry_messages') + $analytics->sum('booking_requests')) / max($analytics->sum('views'), 1)) * 100
                : 0,
        ];
    }

    /**
     * Get daily trend data
     */
    public static function getTrendData($featuredPhotographerId, $days = 30)
    {
        $startDate = now()->subDays($days);

        return self::where('featured_photographer_id', $featuredPhotographerId)
            ->where('date', '>=', $startDate)
            ->orderBy('date')
            ->get(['date', 'views', 'profile_clicks', 'portfolio_clicks', 'inquiry_messages', 'booking_requests'])
            ->toArray();
    }

    /**
     * Get top performing days
     */
    public static function getTopDays($featuredPhotographerId, $limit = 7)
    {
        return self::where('featured_photographer_id', $featuredPhotographerId)
            ->orderByDesc('views')
            ->limit($limit)
            ->get(['date', 'views', 'profile_clicks', 'inquiry_messages']);
    }
}
