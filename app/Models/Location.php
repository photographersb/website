<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type', // 'division', 'district', 'upazila'
        'parent_id', // self-referencing FK
        'is_active',
        'seo_title',
        'seo_description',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function photographers(): HasMany
    {
        return $this->hasMany(Photographer::class, 'city_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'city_id');
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'city_id');
    }

    public function competitionSubmissions(): HasMany
    {
        return $this->hasMany(CompetitionSubmission::class, 'district_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSeoTitleAttribute($value)
    {
        if ($value) return $value;
        return "Best Photographers in {$this->name} | Photographer SB";
    }

    public function getSeoDescriptionAttribute($value)
    {
        if ($value) return $value;
        return "Find verified wedding, event, product, fashion, and photo journalist photographers in {$this->name}, Bangladesh.";
    }
}
