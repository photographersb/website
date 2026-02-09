<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeaturedPhotographer;
use App\Models\FeaturedPhotographerAnalytic;
use Illuminate\Http\Request;

class FeaturedPhotographerAnalyticsController extends Controller
{
    /**
     * Get analytics for a specific featured photographer
     */
    public function show(FeaturedPhotographer $featured, Request $request)
    {
        // Check authorization - photographer can only view their own analytics
        $photographer = $featured->photographer;
        if (auth()->id() != $photographer->user_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $days = $request->input('days', 30);
        $startDate = now()->subDays($days)->toDateString();

        // Get summary statistics
        $summary = FeaturedPhotographerAnalytic::getSummary($featured->id, $days);

        // Get trend data for charts
        $trendData = FeaturedPhotographerAnalytic::getTrendData($featured->id, $days);

        // Get top performing days
        $topDays = FeaturedPhotographerAnalytic::getTopDays($featured->id, 7);

        // Calculate growth rates
        $midPoint = $days / 2;
        $firstHalf = FeaturedPhotographerAnalytic::where('featured_photographer_id', $featured->id)
            ->where('date', '>=', now()->subDays($days))
            ->where('date', '<', now()->subDays($midPoint))
            ->sum('views');

        $secondHalf = FeaturedPhotographerAnalytic::where('featured_photographer_id', $featured->id)
            ->where('date', '>=', now()->subDays($midPoint))
            ->sum('views');

        $viewsGrowth = $firstHalf > 0 ? (($secondHalf - $firstHalf) / $firstHalf) * 100 : 0;

        return response()->json([
            'data' => [
                'featured_photographer' => $featured->load('photographer'),
                'summary' => $summary,
                'trend_data' => $trendData,
                'top_days' => $topDays,
                'growth' => [
                    'views_growth_percent' => round($viewsGrowth, 2),
                ],
                'date_range' => [
                    'start' => $startDate,
                    'end' => now()->toDateString(),
                    'days' => $days,
                ],
            ]
        ]);
    }

    /**
     * Get analytics for all featured photographers (admin)
     */
    public function adminIndex(Request $request)
    {
        $days = $request->input('days', 30);
        $sortBy = $request->input('sort_by', 'views');

        $featured = FeaturedPhotographer::active()
            ->with('photographer')
            ->get()
            ->map(function ($f) use ($days) {
                $summary = FeaturedPhotographerAnalytic::getSummary($f->id, $days);
                return [
                    'featured_photographer' => $f,
                    'summary' => $summary,
                ];
            })
            ->sortByDesc(function ($item) use ($sortBy) {
                return $item['summary'][$sortBy === 'views' ? 'total_views' : $sortBy] ?? 0;
            })
            ->values()
            ->take(50);

        // Calculate platform statistics
        $platformStats = $this->calculatePlatformStats($days);

        return response()->json([
            'data' => $featured,
            'platform_stats' => $platformStats,
            'period' => $days . ' days',
        ]);
    }

    /**
     * Record view event
     */
    public function recordView(FeaturedPhotographer $featured)
    {
        FeaturedPhotographerAnalytic::recordView($featured->id);
        return response()->json(['success' => true]);
    }

    /**
     * Record profile click event
     */
    public function recordProfileClick(FeaturedPhotographer $featured)
    {
        FeaturedPhotographerAnalytic::recordProfileClick($featured->id);
        return response()->json(['success' => true]);
    }

    /**
     * Record portfolio click event
     */
    public function recordPortfolioClick(FeaturedPhotographer $featured)
    {
        FeaturedPhotographerAnalytic::recordPortfolioClick($featured->id);
        return response()->json(['success' => true]);
    }

    /**
     * Record inquiry event
     */
    public function recordInquiry(FeaturedPhotographer $featured)
    {
        FeaturedPhotographerAnalytic::recordInquiry($featured->id);
        return response()->json(['success' => true]);
    }

    /**
     * Record booking request event
     */
    public function recordBooking(FeaturedPhotographer $featured)
    {
        FeaturedPhotographerAnalytic::recordBookingRequest($featured->id);
        return response()->json(['success' => true]);
    }

    /**
     * Calculate platform-wide statistics
     */
    private function calculatePlatformStats($days)
    {
        $startDate = now()->subDays($days);

        $allAnalytics = FeaturedPhotographerAnalytic::where('date', '>=', $startDate)->get();

        return [
            'total_platform_views' => $allAnalytics->sum('views'),
            'total_platform_inquiries' => $allAnalytics->sum('inquiry_messages'),
            'total_platform_bookings' => $allAnalytics->sum('booking_requests'),
            'avg_views_per_featured' => $allAnalytics->count() > 0 
                ? $allAnalytics->sum('views') / FeaturedPhotographer::active()->count()
                : 0,
            'platform_conversion_rate' => $allAnalytics->sum('views') > 0
                ? (($allAnalytics->sum('inquiry_messages') + $allAnalytics->sum('booking_requests')) / $allAnalytics->sum('views')) * 100
                : 0,
        ];
    }

    /**
     * Export analytics as CSV
     */
    public function export(FeaturedPhotographer $featured, Request $request)
    {
        // Check authorization
        $photographer = $featured->photographer;
        if (auth()->id() != $photographer->user_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $days = $request->input('days', 30);
        $analytics = FeaturedPhotographerAnalytic::getTrendData($featured->id, $days);

        $filename = "featured-analytics-{$featured->id}-" . now()->format('Y-m-d') . ".csv";

        $csv = "Date,Views,Profile Clicks,Portfolio Clicks,Inquiries,Bookings\n";
        foreach ($analytics as $day) {
            $csv .= "{$day['date']},{$day['views']},{$day['profile_clicks']},{$day['portfolio_clicks']},{$day['inquiry_messages']},{$day['booking_requests']}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
