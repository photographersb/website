<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photographer;
use App\Models\PhotoCategory;
use App\Models\City;
use App\Models\Competition;
use App\Models\SEOMetadata;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate main sitemap index
     */
    public function index()
    {
        $sitemaps = [
            'photographers' => route('api.sitemap.photographers'),
            'categories' => route('api.sitemap.categories'),
            'cities' => route('api.sitemap.cities'),
            'competitions' => route('api.sitemap.competitions'),
            'static' => route('api.sitemap.static')
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($sitemaps as $name => $url) {
            $xml .= '  <sitemap>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
            $xml .= '  </sitemap>' . PHP_EOL;
        }

        $xml .= '</sitemapindex>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Generate photographers sitemap
     */
    public function photographers()
    {
        $photographers = Photographer::where('is_verified', true)
            ->where('is_active', true)
            ->with('seoMetadata')
            ->get();

        $xml = $this->generateSitemapXML($photographers->map(function ($p) {
            return [
                'loc' => route('api.photographers.show', $p->id),
                'lastmod' => $p->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => $p->seoMetadata?->sitemap_priority ?? '0.8'
            ];
        }));

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Generate categories sitemap
     */
    public function categories()
    {
        $categories = PhotoCategory::where('is_active', true)
            ->with('seoMetadata')
            ->get();

        $xml = $this->generateSitemapXML($categories->map(function ($c) {
            return [
                'loc' => route('api.categories.show', $c->slug),
                'lastmod' => $c->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => $c->seoMetadata?->sitemap_priority ?? '0.6'
            ];
        }));

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Generate cities sitemap
     */
    public function cities()
    {
        $cities = City::where('is_active', true)
            ->with('seoMetadata')
            ->get();

        $xml = $this->generateSitemapXML($cities->map(function ($c) {
            return [
                'loc' => route('api.cities.show', $c->slug),
                'lastmod' => $c->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => $c->seoMetadata?->sitemap_priority ?? '0.6'
            ];
        }));

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Generate competitions sitemap
     */
    public function competitions()
    {
        $competitions = Competition::where('status', 'published')
            ->with('seoMetadata')
            ->get();

        $xml = $this->generateSitemapXML($competitions->map(function ($c) {
            return [
                'loc' => route('api.competitions.show', $c->slug),
                'lastmod' => $c->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => $c->seoMetadata?->sitemap_priority ?? '0.7'
            ];
        }));

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Generate static pages sitemap
     */
    public function static()
    {
        $staticPages = [
            [
                'loc' => route('home'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0'
            ],
            [
                'loc' => route('photographers.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'loc' => url('/categories'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'loc' => url('/locations'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'loc' => url('/photographers/by-category'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ],
            [
                'loc' => url('/photographers/by-location'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ],
            [
                'loc' => route('categories.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'loc' => route('cities.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'loc' => route('competitions.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'loc' => route('about'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6'
            ],
            [
                'loc' => route('contact'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5'
            ]
        ];

        $xml = $this->generateSitemapXML($staticPages);

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /**
     * Helper function to generate sitemap XML
     */
    private function generateSitemapXML(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
