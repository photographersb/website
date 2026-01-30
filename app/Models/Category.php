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
}
