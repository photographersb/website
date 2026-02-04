<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSitemapCheck extends Model
{
    use HasFactory;

    protected $table = 'admin_sitemap_checks';

    protected $fillable = [
        'run_by_user_id',
        'started_at',
        'finished_at',
        'total_links',
        'passed',
        'failed',
        'skipped',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * User who ran this check
     */
    public function runByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'run_by_user_id');
    }

    /**
     * Results for this check
     */
    public function results(): HasMany
    {
        return $this->hasMany(AdminSitemapCheckResult::class, 'check_id');
    }

    /**
     * Get passed results
     */
    public function passedResults()
    {
        return $this->results()->where('result_status', 'passed');
    }

    /**
     * Get failed results
     */
    public function failedResults()
    {
        return $this->results()->where('result_status', 'failed');
    }

    /**
     * Get skipped results
     */
    public function skippedResults()
    {
        return $this->results()->where('result_status', 'skipped');
    }

    /**
     * Calculate duration in seconds
     */
    public function getDurationSeconds(): ?int
    {
        if ($this->finished_at && $this->started_at) {
            return $this->finished_at->diffInSeconds($this->started_at);
        }
        return null;
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRate(): float
    {
        if ($this->total_links === 0) {
            return 0;
        }
        return ($this->passed / $this->total_links) * 100;
    }

    /**
     * Check if scan is complete
     */
    public function isComplete(): bool
    {
        return $this->finished_at !== null;
    }
}
