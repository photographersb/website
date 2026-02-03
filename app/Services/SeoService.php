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
        $city = $photographer?->city ?? 'Bangladesh';
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
            $portfolio = $photographer->portfolios()->first();
            if ($portfolio && $portfolio->image_url) {
                return $portfolio->image_url;
            }
        }

        // Fallback to site default
        return asset('images/og-default.jpg');
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
}
