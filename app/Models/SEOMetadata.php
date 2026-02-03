<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SEOMetadata extends Model
{
    protected $table = 'seo_metadata';

    protected $fillable = [
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image_url',
        'twitter_card',
        'canonical_url',
        'schema_markup',
        'sitemap_priority',
        'sitemap_frequency',
        'is_indexed',
        'noindex',
        'nofollow'
    ];

    protected $casts = [
        'schema_markup' => 'array',
        'is_indexed' => 'boolean',
        'noindex' => 'boolean',
        'nofollow' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getMetaTagsArray(): array
    {
        return [
            'title' => $this->meta_title,
            'description' => $this->meta_description,
            'keywords' => $this->meta_keywords,
            'og:title' => $this->og_title,
            'og:description' => $this->og_description,
            'og:image' => $this->og_image_url,
            'twitter:card' => $this->twitter_card,
            'canonical' => $this->canonical_url,
            'robots' => $this->generateRobotsTag()
        ];
    }

    public function generateRobotsTag(): string
    {
        $parts = [];
        
        if ($this->noindex) {
            $parts[] = 'noindex';
        } else {
            $parts[] = 'index';
        }

        if ($this->nofollow) {
            $parts[] = 'nofollow';
        } else {
            $parts[] = 'follow';
        }

        return implode(', ', $parts);
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
