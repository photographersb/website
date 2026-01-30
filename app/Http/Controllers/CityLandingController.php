<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Category;
use App\Models\Photographer;
use Illuminate\Http\Request;

class CityLandingController extends Controller
{
    public function showCity($citySlug)
    {
        $city = City::where('slug', $citySlug)->firstOrFail();
        
        $photographers = Photographer::with(['user', 'categories'])
            ->where('city_id', $city->id)
            ->where('is_verified', true)
            ->orderBy('average_rating', 'desc')
            ->paginate(12);

        $stats = [
            'total_photographers' => Photographer::where('city_id', $city->id)->where('is_verified', true)->count(),
            'avg_price' => Photographer::where('city_id', $city->id)->avg('starting_price'),
            'categories' => Category::withCount(['photographers' => function($q) use ($city) {
                $q->where('city_id', $city->id)->where('is_verified', true);
            }])->having('photographers_count', '>', 0)->get(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'city' => $city,
                'photographers' => $photographers,
                'stats' => $stats,
                'meta' => [
                    'title' => "Professional Photographers in {$city->name} | Photographar SB",
                    'description' => "Find and book verified professional photographers in {$city->name}, Bangladesh. Wedding, event, portrait photography and more. Compare packages, read reviews, secure payments.",
                    'keywords' => "photographer {$city->name}, wedding photographer {$city->name}, event photography {$city->name}, professional photography {$city->name}",
                ]
            ]
        ]);
    }

    public function showCategory($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        
        $photographers = Photographer::with(['user', 'categories', 'city'])
            ->whereHas('categories', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->where('is_verified', true)
            ->orderBy('average_rating', 'desc')
            ->paginate(12);

        $stats = [
            'total_photographers' => Photographer::whereHas('categories', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            })->where('is_verified', true)->count(),
            'cities' => City::withCount(['photographers' => function($q) use ($category) {
                $q->whereHas('categories', function($q2) use ($category) {
                    $q2->where('categories.id', $category->id);
                })->where('is_verified', true);
            }])->having('photographers_count', '>', 0)->get(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => $category,
                'photographers' => $photographers,
                'stats' => $stats,
                'meta' => [
                    'title' => "{$category->name} Photographers in Bangladesh | Photographar SB",
                    'description' => "Find professional {$category->name} photographers across Bangladesh. Compare portfolios, packages, and reviews. Book with confidence.",
                    'keywords' => "{$category->name} photographer Bangladesh, {$category->name} photography, professional {$category->name}",
                ]
            ]
        ]);
    }

    public function showCityCategory($citySlug, $categorySlug)
    {
        $city = City::where('slug', $citySlug)->firstOrFail();
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        
        $photographers = Photographer::with(['user', 'categories'])
            ->where('city_id', $city->id)
            ->whereHas('categories', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->where('is_verified', true)
            ->orderBy('average_rating', 'desc')
            ->paginate(12);

        $stats = [
            'total_photographers' => $photographers->total(),
            'avg_rating' => Photographer::where('city_id', $city->id)
                ->whereHas('categories', function($q) use ($category) {
                    $q->where('categories.id', $category->id);
                })
                ->where('is_verified', true)
                ->avg('average_rating'),
            'avg_price' => Photographer::where('city_id', $city->id)
                ->whereHas('categories', function($q) use ($category) {
                    $q->where('categories.id', $category->id);
                })
                ->avg('starting_price'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'city' => $city,
                'category' => $category,
                'photographers' => $photographers,
                'stats' => $stats,
                'meta' => [
                    'title' => "{$category->name} Photographers in {$city->name} | Photographar SB",
                    'description' => "Find verified {$category->name} photographers in {$city->name}, Bangladesh. View portfolios, compare packages, read reviews. Average price: ৳{$stats['avg_price']}. Book now!",
                    'keywords' => "{$category->name} photographer {$city->name}, {$category->name} photography {$city->name}, professional {$category->name} {$city->name}",
                ]
            ]
        ]);
    }
}
