<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionPrizeWinner extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'competition_prize_id',
        'submission_id',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function prize(): BelongsTo
    {
        return $this->belongsTo(CompetitionPrize::class, 'competition_prize_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubmission::class, 'submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
