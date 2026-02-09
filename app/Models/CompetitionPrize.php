<?php

namespace App\Models;

use App\Models\Observers\CompetitionPrizeObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionPrize extends Model
{
    protected $fillable = [
        'competition_id',
        'sponsor_id',
        'rank',
        'title',
        'award_type',
        'prize_type',
        'description',
        'prize_description',
        'cash_amount',
        'amount',
        'physical_prizes',
        'display_order',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model with observers
     */
    protected static function boot()
    {
        parent::boot();
        static::observe(CompetitionPrizeObserver::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function winners()
    {
        return $this->hasMany(CompetitionPrizeWinner::class, 'competition_prize_id');
    }
}
