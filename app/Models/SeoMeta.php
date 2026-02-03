<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoMeta extends Model
{
    use HasFactory;

    protected $table = 'seo_meta';

    protected $fillable = [
        'model_type',
        'model_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'robots_index',
        'robots_follow',
        'robots_snippet',
        'schema_json',
        'created_by',
        'updated_by',
        'is_auto_generated',
    ];

    protected $casts = [
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
        'is_auto_generated' => 'boolean',
        'schema_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the owning model (polymorphic)
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created the SEO meta
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the SEO meta
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get robots meta tag value
     */
    public function getRobotsMetaTag(): string
    {
        $directives = [];
        $directives[] = $this->robots_index ? 'index' : 'noindex';
        $directives[] = $this->robots_follow ? 'follow' : 'nofollow';

        if ($this->robots_snippet) {
            $directives[] = $this->robots_snippet;
        }

        return implode(', ', $directives);
    }

    /**
     * Generate schema JSON-LD for different entity types
     */
    public static function generateSchema(string $type, array $data): array
    {
        return match ($type) {
            'photographer' => self::generatePhotographerSchema($data),
            'event' => self::generateEventSchema($data),
            'competition' => self::generateCompetitionSchema($data),
            'article' => self::generateArticleSchema($data),
            default => [],
        };
    }

    /**
     * Generate Article schema (for blog posts)
     */
    private static function generateArticleSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'author' => [
                '@type' => 'Person',
                'name' => $data['author_name'] ?? 'Photographer SB',
            ],
            'datePublished' => $data['published_at'] ?? now()->toIso8601String(),
            'dateModified' => $data['updated_at'] ?? now()->toIso8601String(),
        ];
    }

    /**
     * Generate LocalBusiness schema (for photographers)
     */
    private static function generatePhotographerSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => $data['name'] ?? '',
            'description' => $data['bio'] ?? '',
            'image' => $data['avatar'] ?? '',
            'url' => $data['profile_url'] ?? '',
            'telephone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $data['city'] ?? 'Bangladesh',
                'addressCountry' => 'BD',
            ],
            'areaServed' => 'BD',
            'aggregateRating' => isset($data['rating']) ? [
                '@type' => 'AggregateRating',
                'ratingValue' => $data['rating'],
                'reviewCount' => $data['review_count'] ?? 0,
            ] : null,
        ];
    }

    /**
     * Generate Event schema
     */
    private static function generateEventSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'startDate' => $data['start_date'] ?? '',
            'endDate' => $data['end_date'] ?? '',
            'eventStatus' => 'https://schema.org/' . strtoupper($data['status'] ?? 'scheduled') . 'Status',
            'eventAttendanceMode' => 'https://schema.org/' . ($data['is_online'] ? 'OnlineEventAttendanceMode' : 'OfflineEventAttendanceMode'),
            'location' => [
                '@type' => 'Place',
                'name' => $data['location'] ?? 'Bangladesh',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $data['city'] ?? '',
                    'addressCountry' => 'BD',
                ],
            ],
        ];
    }

    /**
     * Generate Competition schema
     */
    private static function generateCompetitionSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'image' => $data['image'] ?? '',
            'startDate' => $data['start_date'] ?? '',
            'endDate' => $data['end_date'] ?? '',
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'BDT',
                'price' => $data['prize_pool'] ?? '0',
                'availability' => 'https://schema.org/PreOrder',
            ],
            'location' => [
                '@type' => 'VirtualLocation',
                'url' => $data['url'] ?? '',
            ],
        ];
    }
}
