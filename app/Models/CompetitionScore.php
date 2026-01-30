<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionScore extends Model
{
    protected $fillable = [
        'competition_id',
        'submission_id',
        'judge_id',
        'composition_score',
        'technical_score',
        'creativity_score',
        'story_score',
        'impact_score',
        'total_score',
        'feedback',
        'strengths',
        'improvements',
        'status',
        'scored_at',
    ];

    protected $casts = [
        'composition_score' => 'decimal:1',
        'technical_score' => 'decimal:1',
        'creativity_score' => 'decimal:1',
        'story_score' => 'decimal:1',
        'impact_score' => 'decimal:1',
        'total_score' => 'decimal:1',
        'scored_at' => 'datetime',
    ];

    // Auto-calculate total score
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($score) {
            $score->total_score = collect([
                $score->composition_score,
                $score->technical_score,
                $score->creativity_score,
                $score->story_score,
                $score->impact_score,
            ])->filter()->sum();

            if ($score->total_score > 0 && $score->status === 'pending') {
                $score->status = 'completed';
                $score->scored_at = now();
            }
        });
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubmission::class, 'submission_id');
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    public function scopeForSubmission($query, $submissionId)
    {
        return $query->where('submission_id', $submissionId);
    }

    public function scopeByJudge($query, $judgeId)
    {
        return $query->where('judge_id', $judgeId);
    }

    // Helper: Get average score across all criteria
    public function getAverageScore(): float
    {
        $scores = collect([
            $this->composition_score,
            $this->technical_score,
            $this->creativity_score,
            $this->story_score,
            $this->impact_score,
        ])->filter();

        return $scores->count() > 0 ? round($scores->average(), 1) : 0;
    }
}
