<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Photographer;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * SEO Hub: List all photography categories
     */
    public function index()
    {
        $categories = Category::withCount(['photographers' => function ($query) {
                $query->publicVisible();
            }])
            ->orderBy('photographers_count', 'desc')
            ->get();

        $seoMeta = (object) [
            'meta_title' => 'Photography Categories | Find Photographers by Specialty | Photographer SB',
            'meta_description' => 'Browse photographers by category: Wedding, Portrait, Event, Commercial, Fashion, Wildlife, Product Photography and more. Find the perfect specialist for your needs.',
            'canonical_url' => url('/categories'),
            'og_title' => 'Photography Categories - Photographer SB',
            'og_description' => 'Explore photographers organized by their photography specialties across Bangladesh.',
            'og_image' => asset('images/og-categories.jpg'),
            'og_url' => url('/categories'),
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $this->getCategoriesSchema($categories),
        ];

        return view('categories.index', [
            'categories' => $categories,
            'seoMeta' => $seoMeta,
        ]);
    }

    /**
     * Show photographers by category
     */
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $photographers = Photographer::publicVisible()->with('user')
            ->where(function ($query) use ($category) {
                $query->whereJsonContains('specializations', $category->name)
                    ->orWhereHas('categories', function ($categoryQuery) use ($category) {
                        $categoryQuery->where('categories.id', $category->id);
                    });
            })
            ->paginate(24);

        $seoMeta = (object) [
            'meta_title' => "{$category->name} Photographers in Bangladesh | Photographer SB",
            'meta_description' => "Find and hire verified {$category->name} photographers across Bangladesh. Browse portfolios, compare packages, read reviews and book instantly.",
            'canonical_url' => url("/categories/{$slug}"),
            'og_title' => "{$category->name} Photographers - Photographer SB",
            'og_description' => "Discover the best {$category->name} photographers in Bangladesh. {$photographers->total()} verified professionals available.",
            'og_image' => $category->image_url ?? asset('images/og-categories.jpg'),
            'og_url' => url("/categories/{$slug}"),
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $this->getCategoryPhotographersSchema($category, $photographers),
        ];

        return view('categories.show', [
            'category' => $category,
            'photographers' => $photographers,
            'seoMeta' => $seoMeta,
        ]);
    }

    /**
     * Generate schema for categories hub page
     */
    protected function getCategoriesSchema($categories): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Photography Categories',
            'description' => 'Browse photographers by their photography specialties',
            'url' => url('/categories'),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $categories->count(),
                'itemListElement' => $categories->map(function ($category, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $category->name,
                        'url' => url("/categories/{$category->slug}"),
                    ];
                })->toArray(),
            ],
        ];
    }

    /**
     * Generate schema for category photographers list
     */
    protected function getCategoryPhotographersSchema($category, $photographers): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => "{$category->name} Photographers",
            'description' => "List of verified {$category->name} photographers in Bangladesh",
            'url' => url("/categories/{$category->slug}"),
            'numberOfItems' => $photographers->total(),
            'itemListElement' => $photographers->take(10)->map(function ($photographer, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'item' => [
                        '@type' => 'Person',
                        'name' => $photographer->user->name,
                        'url' => url("/@{$photographer->user->username}"),
                    ],
                ];
            })->toArray(),
        ];
    }
}
