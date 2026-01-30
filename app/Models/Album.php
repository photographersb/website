<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Album extends Model
{
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
}
