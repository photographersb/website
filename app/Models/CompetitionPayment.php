<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionPayment extends Model
{
    protected $fillable = [
        'competition_id',
        'submission_id',
        'user_id',
        'method',
        'sender_number',
        'trx_id',
        'amount',
        'screenshot_path',
        'status',
        'admin_note',
        'verified_by_user_id',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubmission::class, 'submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
