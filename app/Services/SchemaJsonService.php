<?php

namespace App\Services;

use App\Models\User;
use App\Models\Photographer;
use Illuminate\Support\Collection;

class SchemaJsonService
{
    /**
     * Generate schema.org Person or LocalBusiness schema for photographer
     */
    public function generatePhotographerSchema(User $user, ?Photographer $photographer = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $this->getSchemaType($photographer),
            'name' => $user->name,
            'url' => route('photographer.profile.public', ['username' => $user->username]),
        ];

        // Add profile image
        if ($user->profile_photo_url) {
            $schema['image'] = $user->profile_photo_url;
        }

        // Add description/bio
        if ($user->bio) {
            $schema['description'] = $user->bio;
        }

        // Add address if available
        if ($photographer) {
            if ($photographer->city || $photographer->district) {
                $schema['address'] = $this->buildAddress($photographer);
            }

            // Add aggregate rating if reviews exist
            if ($photographer->reviews()->exists()) {
                $schema['aggregateRating'] = $this->buildAggregateRating($photographer);
            }

            // Add price range if packages exist
            if ($photographer->packages()->exists()) {
                $schema['priceRange'] = $this->buildPriceRange($photographer);
            }

            // Add service area
            if ($photographer->service_areas) {
                $schema['areaServed'] = $photographer->service_areas;
            }

            // Add categories
            if ($photographer->specializations) {
                $schema['knowsAbout'] = $photographer->specializations;
            }
        }

        // Add social profiles
        $sameAs = $this->getSocialLinks($photographer);
        if (!empty($sameAs)) {
            $schema['sameAs'] = $sameAs;
        }

        // Add email if available
        if ($user->email) {
            $schema['email'] = $user->email;
        }

        // Add telephone if available
        if ($user->phone) {
            $schema['telephone'] = $user->phone;
        }

        // Add verification badge
        if ($user->is_verified ?? false) {
            $schema['isVerified'] = true;
        }

        return $schema;
    }

    /**
     * Determine schema type based on photographer profile
     */
    protected function getSchemaType(?Photographer $photographer): string
    {
        if (!$photographer) {
            return 'Person';
        }

        // Use LocalBusiness if studio owner
        if ($photographer->photographer_type === 'studio' || $photographer->studio_id) {
            return 'LocalBusiness';
        }

        return 'Person';
    }

    /**
     * Build address schema
     */
    protected function buildAddress(Photographer $photographer): array
    {
        return [
            '@type' => 'PostalAddress',
            'streetAddress' => $photographer->address ?? '',
            'addressLocality' => $photographer->city ?? '',
            'addressRegion' => $photographer->district ?? '',
            'addressCountry' => 'BD',
        ];
    }

    /**
     * Build aggregate rating schema
     */
    protected function buildAggregateRating(Photographer $photographer): array
    {
        $reviews = $photographer->reviews;
        $avgRating = $reviews->avg('rating') ?? 0;
        $reviewCount = $reviews->count();

        return [
            '@type' => 'AggregateRating',
            'ratingValue' => round($avgRating, 1),
            'reviewCount' => $reviewCount,
            'bestRating' => '5',
            'worstRating' => '1',
        ];
    }

    /**
     * Build price range schema
     */
    protected function buildPriceRange(Photographer $photographer): string
    {
        $packages = $photographer->packages()
            ->select('price')
            ->get()
            ->pluck('price')
            ->filter()
            ->sort();

        if ($packages->isEmpty()) {
            return '';
        }

        $minPrice = $packages->first();
        $maxPrice = $packages->last();

        if ($minPrice === $maxPrice) {
            return "BDT {$minPrice}";
        }

        return "BDT {$minPrice}-{$maxPrice}";
    }

    /**
     * Get social links for schema
     */
    protected function getSocialLinks(?Photographer $photographer): array
    {
        if (!$photographer) {
            return [];
        }

        $links = [];

        if ($photographer->facebook_url) {
            $links[] = $photographer->facebook_url;
        }

        if ($photographer->instagram_url) {
            $links[] = $photographer->instagram_url;
        }

        if ($photographer->youtube_url) {
            $links[] = $photographer->youtube_url;
        }

        if ($photographer->website_url) {
            $links[] = $photographer->website_url;
        }

        return $links;
    }

    /**
     * Generate Person schema for reviewer
     */
    public function generateReviewerSchema(User $user): array
    {
        return [
            '@type' => 'Person',
            'name' => $user->name,
            'image' => $user->profile_photo_url,
        ];
    }

    /**
     * Generate Review schema for individual review
     */
    public function generateReviewSchema($review): array
    {
        return [
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $review->rating,
            ],
            'author' => $this->generateReviewerSchema($review->reviewer),
            'reviewBody' => $review->comment,
            'datePublished' => $review->created_at->toIso8601String(),
        ];
    }
}
