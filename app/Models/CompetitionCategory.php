<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionCategory extends Model
{
    protected $fillable = [
        'competition_id',
        'name',
        'description',
        'prize_amount',
        'max_submissions_per_user',
        'is_active',
        'submission_count'
    ];

    protected $casts = [
        'prize_amount' => 'decimal:2',
        'max_submissions_per_user' => 'integer',
        'is_active' => 'boolean',
        'submission_count' => 'integer'
    ];

    /**
     * Get the competition that owns the category
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the submissions for this category
     */
    public function submissions()
    {
        return $this->hasMany(CompetitionSubmission::class, 'category_id');
    }

    /**
     * Get approved submissions for this category
     */
    public function approvedSubmissions()
    {
        return $this->hasMany(CompetitionSubmission::class, 'category_id')
            ->where('status', 'approved');
    }

    /**
     * Increment submission count
     */
    public function incrementSubmissionCount()
    {
        $this->increment('submission_count');
    }

    /**
     * Decrement submission count
     */
    public function decrementSubmissionCount()
    {
        $this->decrement('submission_count');
    }
}
