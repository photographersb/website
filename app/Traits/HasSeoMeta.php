<?php

namespace App\Traits;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeoMeta
{
    /**
     * Get SEO meta for this model
     */
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'model');
    }

    /**
     * Get or create SEO meta
     */
    public function getOrCreateSeoMeta(): SeoMeta
    {
        return $this->seoMeta()->firstOrCreate();
    }

    /**
     * Update SEO meta (auto-generate if empty)
     */
    public function updateSeoMeta(array $data = [], bool $autoGenerate = true): SeoMeta
    {
        $meta = $this->getOrCreateSeoMeta();

        if (!empty($data)) {
            $meta->update($data);
        } elseif ($autoGenerate && $meta->is_auto_generated) {
            $this->generateSeoMeta();
            $meta = $this->seoMeta;
        }

        return $meta;
    }

    /**
     * Auto-generate SEO meta from model data
     */
    public function generateSeoMeta(): void
    {
        $meta = $this->getOrCreateSeoMeta();

        $title = $this->getSeoTitle();
        $description = $this->getSeoDescription();
        $slug = $this->getSeoSlug();

        $updates = [
            'meta_title' => $meta->meta_title ?? $title,
            'meta_description' => $meta->meta_description ?? $description,
            'canonical_url' => $meta->canonical_url ?? $this->getSeoCanonicalUrl($slug),
            'og_title' => $meta->og_title ?? $title,
            'og_description' => $meta->og_description ?? $description,
            'og_image' => $meta->og_image ?? $this->getSeoImage(),
            'is_auto_generated' => true,
        ];

        // Generate schema if needed
        if (!$meta->schema_json) {
            $schema = $this->generateSchema();
            if ($schema) {
                $updates['schema_json'] = $schema;
            }
        }

        $meta->update($updates);
    }

    /**
     * Get SEO title (override in model)
     */
    protected function getSeoTitle(): string
    {
        return $this->title ?? $this->name ?? 'Photographer SB';
    }

    /**
     * Get SEO description (override in model)
     */
    protected function getSeoDescription(): string
    {
        return $this->description ?? $this->bio ?? $this->excerpt ?? '';
    }

    /**
     * Get SEO slug (override in model)
     */
    protected function getSeoSlug(): string
    {
        return $this->slug ?? str($this->getSeoTitle())->slug();
    }

    /**
     * Get canonical URL (override in model)
     */
    protected function getSeoCanonicalUrl(string $slug): string
    {
        $baseUrl = config('app.url');
        $modelType = strtolower(class_basename($this));

        return "$baseUrl/$modelType/$slug";
    }

    /**
     * Get SEO image (override in model)
     */
    protected function getSeoImage(): ?string
    {
        return $this->image ?? $this->avatar ?? $this->photo ?? null;
    }

    /**
     * Generate schema.org JSON-LD (override in model)
     */
    protected function generateSchema(): ?array
    {
        return null;
    }
}
