<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSitemapCheck extends Model
{
    protected $table = 'admin_sitemap_checks';

    protected $fillable = [
        'started_by_user_id',
        'started_at',
        'finished_at',
        'total_links',
        'passed_links',
        'failed_links',
        'skipped_links',
        'status',
        'error_summary'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who started the check
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_user_id');
    }

    /**
     * Get all check results
     */
    public function results(): HasMany
    {
        return $this->hasMany(AdminSitemapCheckResult::class, 'check_id');
    }

    /**
     * Get duration in seconds
     */
    public function getDurationSeconds(): ?float
    {
        if (!$this->finished_at) {
            return null;
        }

        return $this->finished_at->diffInMilliseconds($this->started_at) / 1000;
    }

    /**
     * Get summary percentage passed
     */
    public function getPassedPercentage(): int
    {
        if ($this->total_links === 0) {
            return 0;
        }

        return (int) (($this->passed_links / $this->total_links) * 100);
    }

    /**
     * Mark check as completed
     */
    public function markCompleted(): void
    {
        $this->finished_at = now();
        $this->status = 'completed';
        $this->save();
    }

    /**
     * Mark check as failed
     */
    public function markFailed(string $error): void
    {
        $this->finished_at = now();
        $this->status = 'failed';
        $this->error_summary = $error;
        $this->save();
    }
}
