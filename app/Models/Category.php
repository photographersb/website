<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'photographer_count',
        'booking_count',
        'display_order',
        'is_active',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function photographers(): BelongsToMany
    {
        return $this->belongsToMany(Photographer::class, 'photographer_category');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSeoTitleAttribute($value)
    {
        if ($value) return $value;
        return "{$this->name} in Bangladesh | Photographer SB";
    }

    public function getSeoDescriptionAttribute($value)
    {
        if ($value) return $value;
        return "Discover verified {$this->name} professionals in Bangladesh. View portfolios, packages, awards and contact instantly.";
    }
}
