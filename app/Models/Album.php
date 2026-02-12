<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasSeoMeta;

class Album extends Model
{
    use HasSeoMeta;

    protected $fillable = [
        'photographer_id',
        'name',
        'slug',
        'description',
        'cover_photo_url',
        'category_id',
        'is_public',
        'photo_count',
        'view_count',
        'display_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * SEO helpers for auto-generated meta
     */
    protected function getSeoTitle(): string
    {
        return trim("{$this->name} | Client Gallery | Photographer SB");
    }

    protected function getSeoDescription(): string
    {
        $description = trim(strip_tags($this->description ?? ''));
        if ($description !== '') {
            return $description;
        }

        $photographerName = $this->photographer?->user?->name
            ?? $this->photographer?->business_name
            ?? 'Photographer';

        return "Private gallery shared by {$photographerName}.";
    }

    protected function getSeoCanonicalUrl(string $slug): string
    {
        return url("/client/galleries/{$this->id}");
    }

    protected function getSeoImage(): ?string
    {
        return $this->cover_photo_url
            ?: ($this->photographer?->profile_picture ?? null);
    }
}
