<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Competition;
use App\Models\Location;
use App\Models\Photographer;

class SitemapHealthController extends Controller
{
    public function index()
    {
        $photographerCount = Photographer::where('is_verified', true)
            ->where('is_active', true)
            ->count();

        $categoryCount = Category::where('is_active', true)->count();

        $cityCount = Location::where('is_active', true)
            ->whereIn('type', ['district', 'upazila'])
            ->count();

        $competitionCount = Competition::where('status', 'published')->count();

        $staticCount = 11;
        $totalUrls = $photographerCount + $categoryCount + $cityCount + $competitionCount + $staticCount;
        $generatedAt = now()->toDateTimeString();

        $sitemaps = [
            [
                'id' => 1,
                'name' => 'Main Sitemap',
                'type' => 'Index',
                'url' => route('sitemap.index'),
                'urls' => $totalUrls,
                'generated_at' => $generatedAt,
                'status' => $totalUrls > 0 ? 'healthy' : 'warning',
            ],
            [
                'id' => 2,
                'name' => 'Photographers',
                'type' => 'Content',
                'url' => route('api.sitemap.photographers'),
                'urls' => $photographerCount,
                'generated_at' => $generatedAt,
                'status' => $photographerCount > 0 ? 'healthy' : 'warning',
            ],
            [
                'id' => 3,
                'name' => 'Categories',
                'type' => 'Content',
                'url' => route('api.sitemap.categories'),
                'urls' => $categoryCount,
                'generated_at' => $generatedAt,
                'status' => $categoryCount > 0 ? 'healthy' : 'warning',
            ],
            [
                'id' => 4,
                'name' => 'Cities',
                'type' => 'Content',
                'url' => route('api.sitemap.cities'),
                'urls' => $cityCount,
                'generated_at' => $generatedAt,
                'status' => $cityCount > 0 ? 'healthy' : 'warning',
            ],
            [
                'id' => 5,
                'name' => 'Competitions',
                'type' => 'Content',
                'url' => route('api.sitemap.competitions'),
                'urls' => $competitionCount,
                'generated_at' => $generatedAt,
                'status' => $competitionCount > 0 ? 'healthy' : 'warning',
            ],
            [
                'id' => 6,
                'name' => 'Static Pages',
                'type' => 'Content',
                'url' => route('api.sitemap.static'),
                'urls' => $staticCount,
                'generated_at' => $generatedAt,
                'status' => $staticCount > 0 ? 'healthy' : 'warning',
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'sitemaps' => $sitemaps,
                'stats' => [
                    'total_urls' => $totalUrls,
                    'last_generated' => $generatedAt,
                ],
            ],
        ]);
    }
}
