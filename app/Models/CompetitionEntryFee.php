<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionEntryFee extends Model
{
    protected $fillable = [
        'competition_id',
        'user_type',
        'fee_amount',
        'currency',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
