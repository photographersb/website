<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Photographer;
use Illuminate\Http\Request;

class PhotographerSearchController extends Controller
{
    /**
     * Search photographers by location and category combined
     * Route: /photographers/{location_slug}/{category_slug}
     */
    public function byLocationAndCategory(string $locationSlug, string $categorySlug)
    {
        $location = Location::where('slug', $locationSlug)->firstOrFail();
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        $photographers = Photographer::publicVisible()->with(['user', 'city'])
            ->where('city_id', $location->id)
            ->whereJsonContains('specializations', $category->name)
            ->orderByRaw('is_featured DESC, average_rating DESC')
            ->paginate(24);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Photographers', 'url' => '/photographers'],
            ['label' => $location->name, 'url' => '/locations/' . $location->slug],
            ['label' => $category->name, 'url' => '/categories/' . $category->slug],
            ['label' => $category->name . ' in ' . $location->name, 'url' => null],
        ];

        $seoMeta = (object) [
            'meta_title' => "{$category->name} in {$location->name} | Photographer SB",
            'meta_description' => "Find verified {$category->name} photographers in {$location->name}, Bangladesh. Browse portfolios, packages, ratings and book instantly.",
            'canonical_url' => url("/photographers/{$location->slug}/{$category->slug}"),
            'og_title' => "{$category->name} Photographers in {$location->name}",
            'og_description' => "Discover professional {$category->name} services in {$location->name}",
            'og_image' => asset('images/og-photographers.jpg'),
            'og_url' => url("/photographers/{$location->slug}/{$category->slug}"),
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $this->getItemListSchema($photographers, $category->name, $location->name),
        ];

        return view('photographers.search', [
            'photographers' => $photographers,
            'location' => $location,
            'category' => $category,
            'breadcrumbs' => $breadcrumbs,
            'seoMeta' => $seoMeta,
        ]);
    }

    /**
     * Search photographers by location only
     * Route: /photographers/location/{location_slug}
     */
    public function byLocation(string $slug)
    {
        $location = Location::where('slug', $slug)->firstOrFail();

        $photographers = Photographer::publicVisible()->with(['user', 'city'])
            ->where('city_id', $location->id)
            ->orderByRaw('is_featured DESC, average_rating DESC')
            ->paginate(24);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Photographers', 'url' => '/photographers'],
            ['label' => $location->name, 'url' => null],
        ];

        $seoMeta = (object) [
            'meta_title' => "Photographers in {$location->name} | Photographer SB",
            'meta_description' => "Find verified photographers in {$location->name}, Bangladesh. All photography types: wedding, event, portrait, product and more.",
            'canonical_url' => url("/photographers/location/{$location->slug}"),
            'og_title' => "Find Photographers in {$location->name}",
            'og_description' => "Professional photographers in {$location->name} for all your needs",
            'og_image' => asset('images/og-locations.jpg'),
            'og_url' => url("/photographers/location/{$location->slug}"),
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $this->getItemListSchema($photographers, null, $location->name),
        ];

        return view('photographers.location', [
            'photographers' => $photographers,
            'location' => $location,
            'breadcrumbs' => $breadcrumbs,
            'seoMeta' => $seoMeta,
        ]);
    }

    /**
     * Search photographers by category only
     * Route: /photographers/category/{category_slug}
     */
    public function byCategory(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $photographers = Photographer::publicVisible()->with(['user', 'city'])
            ->whereJsonContains('specializations', $category->name)
            ->orderByRaw('is_featured DESC, average_rating DESC')
            ->paginate(24);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Photographers', 'url' => '/photographers'],
            ['label' => $category->name, 'url' => null],
        ];

        $seoMeta = (object) [
            'meta_title' => "{$category->name} Photographers in Bangladesh | Photographer SB",
            'meta_description' => "Find verified {$category->name} professionals across Bangladesh. View portfolios, compare prices and book your photographer today.",
            'canonical_url' => url("/photographers/category/{$category->slug}"),
            'og_title' => "{$category->name} Photographers Bangladesh",
            'og_description' => "Professional {$category->name} services in Bangladesh",
            'og_image' => asset('images/og-categories.jpg'),
            'og_url' => url("/photographers/category/{$category->slug}"),
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $this->getItemListSchema($photographers, $category->name),
        ];

        return view('photographers.category', [
            'photographers' => $photographers,
            'category' => $category,
            'breadcrumbs' => $breadcrumbs,
            'seoMeta' => $seoMeta,
        ]);
    }

    /**
     * Generate ItemList schema for photographers
     */
    private function getItemListSchema($photographers, $category = null, $location = null)
    {
        $items = [];
        foreach ($photographers->items() as $index => $photographer) {
            $items[] = [
                '@type' => 'Person',
                'name' => $photographer->user->name,
                'url' => route('photographer.profile.public', ['username' => $photographer->user->username]),
                'image' => $photographer->profile_picture,
                'jobTitle' => implode(', ', $photographer->specializations ?? []),
                'location' => $photographer->city->name ?? null,
                'rating' => [
                    '@type' => 'AggregateRating',
                    'ratingValue' => $photographer->average_rating,
                    'ratingCount' => $photographer->rating_count,
                ],
            ];
        }

        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $category ? "{$category} Photographers" : ($location ? "Photographers in {$location}" : "Photographers"),
            'numberOfItems' => count($items),
            'itemListElement' => array_values(array_map(function ($item, $pos) {
                $item['position'] = $pos + 1;
                return $item;
            }, $items, array_keys($items))),
        ]);
    }
}
