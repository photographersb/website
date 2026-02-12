<?php

namespace App\Services;

use App\Models\SeoMeta;
use App\Models\User;
use App\Models\Photographer;

class SeoService
{
    protected UsernameService $usernameService;
    protected SchemaJsonService $schemaService;

    public function __construct(UsernameService $usernameService, SchemaJsonService $schemaService)
    {
        $this->usernameService = $usernameService;
        $this->schemaService = $schemaService;
    }

    /**
     * Generate SEO metadata for photographer profile
     */
    public function generatePhotographerSeo(User $user): SeoMeta
    {
        $photographer = $user->photographer;
        $profileUrl = $this->usernameService->getProfileUrl($user);
        $city = $photographer?->city?->name ?? 'Bangladesh';
        $avgRating = $photographer?->averageRating ?? 0;
        $reviewCount = $photographer?->reviews()->count() ?? 0;

        $metaTitle = "{$user->name} (@{$user->username}) | Photographer SB";
        $metaDescription = "Hire {$user->name}, a verified photographer in {$city}. View portfolio, packages, reviews and contact on Photographer SB.";
        
        $ogImage = $this->getOgImage($user, $photographer);
        $schema = $this->schemaService->generatePhotographerSchema($user, $photographer);

        // Create or update SEO meta
        return $user->seoMeta()->updateOrCreate(
            [],
            [
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'canonical_url' => $profileUrl,
                'og_image' => $ogImage,
                'schema_json' => $schema,
                'robots_index' => true,
                'robots_follow' => true,
                'is_auto_generated' => true,
            ]
        );
    }

    /**
     * Get OpenGraph image for photographer
     */
    protected function getOgImage(?User $user, ?Photographer $photographer = null): ?string
    {
        // Use profile photo first
        if ($user && $user->profile_photo_url) {
            return $user->profile_photo_url;
        }

        // Try first portfolio image
        if ($photographer) {
            $portfolio = $photographer->albums()->first();
            if ($portfolio && $portfolio->image_url) {
                return $portfolio->image_url;
            }
        }

        // Fallback to site default
        return asset('images/og-cover.jpg');
    }

    /**
     * Get SEO meta for user/photographer
     */
    public function getSeoMeta(User $user): ?SeoMeta
    {
        $seoMeta = $user->seoMeta;

        // Auto-generate if missing
        if (!$seoMeta && $user->isPhotographer()) {
            $seoMeta = $this->generatePhotographerSeo($user);
        }

        return $seoMeta;
    }

    /**
     * Clear SEO cache for user
     */
    public function clearCache(User $user): void
    {
        $cacheKey = "seo_meta_user_{$user->id}";
        cache()->forget($cacheKey);
    }

    /**
     * Render meta tags as HTML
     */
    public function renderMetaTags(SeoMeta $seoMeta): string
    {
        $html = '';

        if ($seoMeta->meta_title) {
            $html .= '<meta name="title" content="' . e($seoMeta->meta_title) . '">' . "\n";
        }

        if ($seoMeta->meta_description) {
            $html .= '<meta name="description" content="' . e($seoMeta->meta_description) . '">' . "\n";
        }

        if ($seoMeta->canonical_url) {
            $html .= '<link rel="canonical" href="' . e($seoMeta->canonical_url) . '">' . "\n";
        }

        // OpenGraph tags
        if ($seoMeta->og_title ?? $seoMeta->meta_title) {
            $html .= '<meta property="og:title" content="' . e($seoMeta->og_title ?? $seoMeta->meta_title) . '">' . "\n";
        }

        if ($seoMeta->og_description ?? $seoMeta->meta_description) {
            $html .= '<meta property="og:description" content="' . e($seoMeta->og_description ?? $seoMeta->meta_description) . '">' . "\n";
        }

        if ($seoMeta->canonical_url) {
            $html .= '<meta property="og:url" content="' . e($seoMeta->canonical_url) . '">' . "\n";
        }

        if ($seoMeta->og_image) {
            $html .= '<meta property="og:image" content="' . e($seoMeta->og_image) . '">' . "\n";
            $html .= '<meta property="og:image:alt" content="' . e($seoMeta->meta_title ?? 'Photographer Profile') . '">' . "\n";
        }

        // Twitter Card tags
        $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        if ($seoMeta->meta_title) {
            $html .= '<meta name="twitter:title" content="' . e($seoMeta->meta_title) . '">' . "\n";
        }
        if ($seoMeta->meta_description) {
            $html .= '<meta name="twitter:description" content="' . e($seoMeta->meta_description) . '">' . "\n";
        }
        if ($seoMeta->og_image) {
            $html .= '<meta name="twitter:image" content="' . e($seoMeta->og_image) . '">' . "\n";
        }

        // Robots meta tag
        $html .= '<meta name="robots" content="' . $seoMeta->getRobotsValue() . '">' . "\n";

        return $html;
    }

    /**
     * Render schema.org JSON-LD
     */
    public function renderSchemaJson(SeoMeta $seoMeta): string
    {
        if (!$seoMeta->schema_json) {
            return '';
        }

        return '<script type="application/ld+json">' . "\n" . json_encode($seoMeta->schema_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n" . '</script>';
    }

    /**
     * Generate SEO metadata for Event
     */
    public function generateEventSeo($event): object
    {
        $metaTitle = "{$event->title} | Photography Event | Photographer SB";
        $metaDescription = substr(strip_tags($event->description), 0, 155) . "...";
        $eventUrl = url("/events/{$event->slug}");
        
        $ogImage = $event->banner_url ?? $event->image_url ?? asset('images/og-events.jpg');
        
        $schema = $this->generateEventSchema($event);

        return (object) [
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $eventUrl,
            'og_title' => $event->title,
            'og_description' => $metaDescription,
            'og_image' => $ogImage,
            'og_url' => $eventUrl,
            'og_type' => 'event',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $schema,
        ];
    }

    /**
     * Generate Event schema
     */
    protected function generateEventSchema($event): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->title,
            'description' => strip_tags($event->description),
            'url' => url("/events/{$event->slug}"),
            'eventStatus' => 'https://schema.org/EventScheduled',
        ];

        if ($event->start_date) {
            $schema['startDate'] = $event->start_date->toIso8601String();
        }

        if ($event->end_date) {
            $schema['endDate'] = $event->end_date->toIso8601String();
        }

        if ($event->location) {
            $schema['location'] = [
                '@type' => 'Place',
                'name' => $event->location,
            ];
        }

        if ($event->banner_url ?? $event->image_url) {
            $schema['image'] = $event->banner_url ?? $event->image_url;
        }

        if ($event->organizer_name) {
            $schema['organizer'] = [
                '@type' => 'Organization',
                'name' => $event->organizer_name,
            ];
        }

        return $schema;
    }

    /**
     * Generate SEO metadata for Competition
     */
    public function generateCompetitionSeo($competition): object
    {
        $metaTitle = "{$competition->title} | Photography Competition | Photographer SB";
        $metaDescription = substr(strip_tags($competition->description), 0, 155) . "...";
        $competitionUrl = url("/competitions/{$competition->slug}");
        
        $ogImage = $competition->cover_image ?? $competition->banner_url ?? asset('images/og-competitions.jpg');
        
        $schema = $this->generateCompetitionSchema($competition);

        return (object) [
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $competitionUrl,
            'og_title' => $competition->title,
            'og_description' => $metaDescription,
            'og_image' => $ogImage,
            'og_url' => $competitionUrl,
            'og_type' => 'article',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $schema,
        ];
    }

    /**
     * Generate Competition schema (CreativeWork)
     */
    protected function generateCompetitionSchema($competition): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $competition->title,
            'description' => strip_tags($competition->description),
            'url' => url("/competitions/{$competition->slug}"),
        ];

        if ($competition->start_date) {
            $schema['datePublished'] = $competition->start_date->toIso8601String();
        }

        if ($competition->end_date) {
            $schema['expires'] = $competition->end_date->toIso8601String();
        }

        if ($competition->cover_image ?? $competition->banner_url) {
            $schema['image'] = $competition->cover_image ?? $competition->banner_url;
        }

        if ($competition->prizes) {
            $schema['award'] = $competition->prizes;
        }

        return $schema;
    }

    /**
     * Generate homepage SEO
     */
    public function generateHomepageSeo(): object
    {
        return (object) [
            'meta_title' => 'Photographer SB - Find & Hire Professional Photographers in Bangladesh',
            'meta_description' => 'Bangladesh\'s largest photography marketplace. Browse 1000+ verified photographers. Book wedding, event, portrait, commercial photographers. Secure payments, verified reviews.',
            'canonical_url' => url('/'),
            'og_title' => 'Photographer SB - Professional Photography Services Bangladesh',
            'og_description' => 'Connect with verified photographers across Bangladesh. Compare portfolios, read reviews, book instantly.',
            'og_image' => asset('images/og-home.jpg'),
            'og_url' => url('/'),
            'og_type' => 'website',
            'robots_index' => true,
            'robots_follow' => true,
            'schema_json' => $this->getOrganizationSchema(),
        ];
    }

    /**
     * Generate Organization schema for homepage
     */
    protected function getOrganizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Photographer SB',
            'description' => 'Bangladesh\'s premier photography marketplace connecting photographers with clients',
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'sameAs' => [
                'https://www.facebook.com/thephotographersbd',
                'https://www.instagram.com/thephotographersbd',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'telephone' => '+880-1767300900',
                'availableLanguage' => ['Bengali', 'English'],
            ],
        ];
    }
}
