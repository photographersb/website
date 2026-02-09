<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Photographer;

class PlatformStatsController extends Controller
{
    /**
     * Get public platform statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Real-time platform stats (no cache)
            $stats = [
                // Real photographers count
                'photographers' => DB::table('photographers')->count(),
                
                // Real cities covered (cities that have photographers)
                'cities' => DB::table('locations')
                    ->whereIn('type', ['district', 'upazila'])
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('photographers')
                              ->whereColumn('photographers.city_id', 'locations.id');
                    })
                    ->count(),
                
                // Real-time visitors (last 30 days)
                'visitors' => $this->getTotalVisitors(),
                
                // Average rating
                'rating' => $this->getAverageRating(),
                
                // Additional stats
                'total_bookings' => $this->getTotalBookings(),
                'active_events' => $this->getActiveEvents(),
                'competitions' => $this->getTotalCompetitions(),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('Platform stats error: ' . $e->getMessage());
            
            // Return zeros if error - starts from 0
            return response()->json([
                'status' => 'success',
                'data' => [
                    'photographers' => 0,
                    'cities' => 0,
                    'visitors' => 0,
                    'rating' => 0,
                    'total_bookings' => 0,
                    'active_events' => 0,
                    'competitions' => 0,
                ],
            ]);
        }
    }

    /**
     * Get total unique visitors (last 30 days)
     */
    private function getTotalVisitors()
    {
        try {
            $count = DB::table('visitor_logs')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            \Log::info('Visitor count: ' . $count);
            return $count;
        } catch (\Exception $e) {
            \Log::error('getTotalVisitors error: ' . $e->getMessage());
            return 0; // Start from 0 if table doesn't exist
        }
    }

    /**
     * Get platform average rating
     */
    private function getAverageRating()
    {
        try {
            $avgRating = DB::table('reviews')
                ->where('status', 'published')
                ->avg('rating');
            
            return round($avgRating ?? 0, 1);
        } catch (\Exception $e) {
            return 0; // Start from 0
        }
    }

    /**
     * Get total bookings
     */
    private function getTotalBookings()
    {
        try {
            return DB::table('bookings')->count();
        } catch (\Exception $e) {
            return 0; // Start from 0
        }
    }

    /**
     * Get active events count
     */
    private function getActiveEvents()
    {
        try {
            return DB::table('events')
                ->where('status', 'published')
                ->where('event_date', '>=', now())
                ->count();
        } catch (\Exception $e) {
            return 0; // Start from 0
        }
    }

    /**
     * Get total competitions
     */
    private function getTotalCompetitions()
    {
        try {
            return DB::table('competitions')->count();
        } catch (\Exception $e) {
            return 0; // Start from 0
        }
    }

    /**
     * Format number for display (e.g., 10500 → "10.5K")
     */
    public static function formatNumber($number)
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return $number;
    }
}
