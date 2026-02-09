<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeaturedPhotographer;
use Illuminate\Http\Request;

class FeaturedPhotographerPublicController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int) $request->input('limit', 3);
        if ($limit < 1) {
            $limit = 1;
        }
        if ($limit > 12) {
            $limit = 12;
        }

        $featured = FeaturedPhotographer::active()
            ->with([
                'photographer.user:id,name,username,email',
                'photographer.categories:id,name,slug',
                'photographer.city:id,name,slug',
            ])
            ->orderByDesc('package_tier')
            ->orderBy('start_date')
            ->limit($limit)
            ->get();

        $photographers = $featured->map(function ($item) {
            $photographer = $item->photographer;
            if (!$photographer) {
                return null;
            }

            $photographer->setAttribute('is_featured', true);
            $photographer->setAttribute('featured_package', $item->package_tier);
            $photographer->setAttribute('featured_start_date', $item->start_date);
            $photographer->setAttribute('featured_end_date', $item->end_date);

            return $photographer;
        })->filter()->values();

        return response()->json([
            'status' => 'success',
            'data' => $photographers,
        ]);
    }
}
