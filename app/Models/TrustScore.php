<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustScore extends Model
{
    protected $fillable = [
        'photographer_id',
        'phone_verified',
        'email_verified',
        'id_verified',
        'review_count',
        'average_rating',
        'booking_completion_rate',
        'response_time_avg',
        'profile_completeness',
        'overall_score',
        'trust_badge',
    ];

    protected $casts = [
        'phone_verified' => 'boolean',
        'email_verified' => 'boolean',
        'id_verified' => 'boolean',
        'average_rating' => 'decimal:2',
        'booking_completion_rate' => 'decimal:2',
        'response_time_avg' => 'decimal:2',
        'profile_completeness' => 'decimal:2',
        'overall_score' => 'decimal:2',
    ];

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }
}
