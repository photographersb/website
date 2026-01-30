<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionPrize extends Model
{
    protected $fillable = [
        'competition_id',
        'rank',
        'title',
        'description',
        'cash_amount',
        'physical_prizes',
        'display_order',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
