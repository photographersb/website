<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionVote extends Model
{
    protected $fillable = [
        'submission_id',
        'voter_id',
        'competition_id',
        'vote_value',
        'voted_at',
        'ip_address',
        'device_fingerprint',
        'is_verified',
        'is_valid',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_valid' => 'boolean',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubmission::class);
    }

    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voter_id');
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
