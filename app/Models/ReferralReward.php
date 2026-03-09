<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralReward extends Model
{
    protected $fillable = [
        'user_id',
        'milestone',
        'status',
        'referred_photographers_count',
        'badge_name',
        'achieved_at',
        'metadata',
    ];

    protected $casts = [
        'achieved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
