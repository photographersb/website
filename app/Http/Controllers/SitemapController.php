<?php

namespace App\Http\Controllers;

use App\Models\Photographer;
use App\Models\Event;
use App\Models\Competition;
use App\Models\City;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        $sitemaps = [
            '/sitemap/main.xml',
            '/sitemap/photographers.xml',
            '/sitemap/events.xml',
            '/sitemap/competitions.xml',
            '/sitemap/cities.xml',
            '/sitemap/categories.xml',
        ];
        
        foreach ($sitemaps as $map) {
            $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . url($map) . '</loc>';
            $sitemap .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
            $sitemap .= '</sitemap>';
        }
        
        $sitemap .= '</sitemapindex>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function main()
    {
        $urls = [
            ['loc' => url('/'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => url('/photographers'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => url('/categories'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => url('/locations'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => url('/photographers/by-category'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => url('/photographers/by-location'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => url('/events'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => url('/competitions'), 'changefreq' => 'daily', 'priority' => '0.8'],
            ['loc' => url('/about'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => url('/contact'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => url('/faq'), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        return $this->generateXML($urls);
    }

    public function photographers()
    {
        $photographers = Photographer::with('user')
            ->where('is_verified', true)
            ->select('slug', 'updated_at')
            ->get();

        $urls = $photographers->map(function ($photographer) {
            return [
                'loc' => url("/photographer/{$photographer->slug}"),
                'lastmod' => $photographer->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        })->toArray();

        return $this->generateXML($urls);
    }

    public function events()
    {
        $events = Event::where('status', 'published')
            ->select('slug', 'updated_at')
            ->get();

        $urls = $events->map(function ($event) {
            return [
                'loc' => url("/events/{$event->slug}"),
                'lastmod' => $event->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        })->toArray();

        return $this->generateXML($urls);
    }

    public function competitions()
    {
        $competitions = Competition::where('is_public', true)
            ->select('slug', 'updated_at')
            ->get();

        $urls = $competitions->map(function ($competition) {
            return [
                'loc' => url("/competitions/{$competition->slug}"),
                'lastmod' => $competition->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.7',
            ];
        })->toArray();

        return $this->generateXML($urls);
    }

    public function cities()
    {
        $cities = City::select('slug')->get();

        $urls = $cities->map(function ($city) {
            return [
                'loc' => url('/photographers/by-location') . '?city=' . $city->slug,
                'changefreq' => 'weekly',
                'priority' => '0.9', // High priority for local SEO
            ];
        })->toArray();

        return $this->generateXML($urls);
    }

    public function categories()
    {
        $categories = Category::select('slug')->get();

        $urls = [];
        
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => url('/photographers/by-category') . '?category=' . $category->slug,
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }

        return $this->generateXML($urls);
    }

    private function generateXML(array $urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            
            if (isset($url['lastmod'])) {
                $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            }
            
            if (isset($url['changefreq'])) {
                $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            }
            
            if (isset($url['priority'])) {
                $xml .= '<priority>' . $url['priority'] . '</priority>';
            }
            
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
