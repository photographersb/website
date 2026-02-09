<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Photographer;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * SEO Hub: List all locations/cities
     */
    public function index()
    {
        $locations = Location::withCount('photographers')
            ->where('is_active', true)
            ->whereIn('type', ['district', 'upazila'])
            ->orderBy('photographers_count', 'desc')
            ->get();

        $seoMeta = (object) [
            'meta_title' => 'Photographers by Location | Find Local Photographers | Photographer SB',
            'meta_description' => 'Find photographers near you. Browse by city and district across Bangladesh: Dhaka, Chittagong, Sylhet, Rajshahi, Khulna and more. Local photographers for your events.',
            'canonical_url' => url('/locations'),
            'og_title' => 'Find Photographers by Location - Photographer SB',
            'og_description' => 'Discover photographers in your city. Search by location across all divisions and districts of Bangladesh.',
            'og_image' => asset('images/og-locations.jpg'),
            'og_url' => url('/locations'),
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $this->getLocationsSchema($locations),
        ];

        return view('locations.index', [
            'locations' => $locations,
            'seoMeta' => $seoMeta,
        ]);
    }

    /**
     * Show photographers by location
     */
    public function show(string $slug)
    {
        $location = Location::where('slug', $slug)->firstOrFail();
        
        $photographers = Photographer::with(['user', 'city'])
            ->where(function ($query) use ($location) {
                $query->where('city_id', $location->id)
                    ->orWhere('location', 'like', '%' . $location->name . '%');
            })
            ->where('is_verified', true)
            ->paginate(24);

        $seoMeta = (object) [
            'meta_title' => "Photographers in {$location->name} | Local Photography Services | Photographer SB",
            'meta_description' => "Hire verified photographers in {$location->name}, Bangladesh. View portfolios, packages, and reviews. Book local photography services instantly.",
            'canonical_url' => url("/locations/{$slug}"),
            'og_title' => "Photographers in {$location->name} - Photographer SB",
            'og_description' => "Find the best photographers in {$location->name}. {$photographers->total()} verified professionals ready to capture your moments.",
            'og_image' => $location->image_url ?? asset('images/og-locations.jpg'),
            'og_url' => url("/locations/{$slug}"),
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $this->getLocationPhotographersSchema($location, $photographers),
        ];

        return view('locations.show', [
            'city' => $location,
            'photographers' => $photographers,
            'seoMeta' => $seoMeta,
        ]);
    }

    /**
     * Generate schema for locations hub page
     */
    protected function getLocationsSchema($locations): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Photographers by Location',
            'description' => 'Find photographers in your city or district across Bangladesh',
            'url' => url('/locations'),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $locations->count(),
                'itemListElement' => $locations->map(function ($location, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $location->name,
                        'url' => url("/locations/{$location->slug}"),
                    ];
                })->toArray(),
            ],
        ];
    }

    /**
     * Generate schema for location photographers list
     */
    protected function getLocationPhotographersSchema($city, $photographers): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => "Photographers in {$city->name}",
            'description' => "List of verified photographers in {$city->name}, Bangladesh",
            'url' => url("/locations/{$city->slug}"),
            'numberOfItems' => $photographers->total(),
            'itemListElement' => $photographers->take(10)->map(function ($photographer, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'item' => [
                        '@type' => 'Person',
                        'name' => $photographer->user->name,
                        'url' => url("/photographer/{$photographer->slug}"),
                        'address' => [
                            '@type' => 'PostalAddress',
                            'addressLocality' => $photographer->city?->name ?? $photographer->location ?? '',
                            'addressCountry' => 'BD',
                        ],
                    ],
                ];
            })->toArray(),
        ];
    }
}
