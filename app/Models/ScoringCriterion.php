<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoringCriterion extends Model
{
    protected $table = 'scoring_criteria';

    protected $fillable = [
        'competition_id',
        'title',
        'description',
        'max_score',
        'weight',
        'sort_order',
    ];

    protected $casts = [
        'max_score' => 'integer',
        'weight' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }
}
