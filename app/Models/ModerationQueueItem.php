<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModerationQueueItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'security_flag_id',
        'user_id',
        'target_type',
        'target_id',
        'queue_type',
        'reason',
        'severity',
        'status',
        'action_taken',
        'reviewed_by_user_id',
        'reviewed_at',
        'metadata',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function securityFlag(): BelongsTo
    {
        return $this->belongsTo(SecurityFlag::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
