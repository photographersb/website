<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionJudge extends Model
{
    protected $fillable = [
        'competition_id',
        'judge_id',
        'judge_profile_id',
        'role',
        'bio',
        'expertise',
        'is_active',
        'sort_order',
        'assigned_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'assigned_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    public function judgeProfile(): BelongsTo
    {
        return $this->belongsTo(Judge::class, 'judge_profile_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionScore::class, 'judge_id', 'judge_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    public function scopeChiefJudges($query)
    {
        return $query->where('role', 'chief_judge');
    }
}
