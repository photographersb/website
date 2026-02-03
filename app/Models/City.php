<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'state',
        'division',
        'latitude',
        'longitude',
        'photographer_count',
        'display_order',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function photographers(): HasMany
    {
        return $this->hasMany(Photographer::class);
    }
}
