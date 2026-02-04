<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CompetitionSubmission extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'uuid',
        'competition_id',
        'photographer_id',
        'image_path',
        'image_url',
        'thumbnail_url',
        'title',
        'description',
        'location',
        'date_taken',
        'camera_make',
        'camera_model',
        'camera_settings',
        'hashtags',
        'is_watermarked',
        'status',
        'rejection_reason',
        'view_count',
        'vote_count',
        'judge_score',
        'final_score',
        'ranking',
        'is_winner',
        'winner_position',
        'short_url',
        'share_token',
    ];

    protected $casts = [
        'date_taken' => 'date',
        'is_watermarked' => 'boolean',
        'is_winner' => 'boolean',
        'judge_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'view_count' => 'integer',
        'vote_count' => 'integer',
        'ranking' => 'integer',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($submission) {
            if (empty($submission->uuid)) {
                $submission->uuid = (string) Str::uuid();
            }
        });
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CompetitionVote::class, 'submission_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionScore::class, 'submission_id');
    }

    public function shareFrame(): HasOne
    {
        return $this->hasOne(SubmissionShareFrame::class, 'competition_submission_id');
    }
    
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
    
    public function incrementVoteCount()
    {
        $this->increment('vote_count');
    }
    
    public function decrementVoteCount()
    {
        $this->decrement('vote_count');
    }
    
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending_review');
    }
    
    public function scopeForCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }
    
    public function scopeByPhotographer($query, $photographerId)
    {
        return $query->where('photographer_id', $photographerId);
    }
}
