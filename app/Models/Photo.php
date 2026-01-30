<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    protected $fillable = [
        'uuid',
        'album_id',
        'photographer_id',
        'title',
        'description',
        'image_url',
        'thumbnail_url',
        'camera_make',
        'camera_model',
        'camera_settings',
        'location',
        'date_taken',
        'display_order',
        'view_count',
        'is_featured',
    ];

    protected $casts = [
        'camera_settings' => 'array',
        'date_taken' => 'date',
        'is_featured' => 'boolean',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }
}
