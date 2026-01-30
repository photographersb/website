<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Review extends Model
{
    protected $fillable = [
        'uuid',
        'booking_id',
        'reviewer_id',
        'photographer_id',
        'rating',
        'professionalism_score',
        'quality_score',
        'communication_score',
        'value_score',
        'delivery_score',
        'title',
        'comment',
        'is_anonymous',
        'is_verified_purchase',
        'photo_urls',
        'helpful_count',
        'unhelpful_count',
        'status',
        'flag_reason',
        'moderation_notes',
        'approved_at',
        'published_at',
    ];

    protected $casts = [
        'photo_urls' => 'array',
        'is_anonymous' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
        'rating' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($review) {
            if (empty($review->uuid)) {
                $review->uuid = (string) Str::uuid();
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ReviewReply::class);
    }
}
