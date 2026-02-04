<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SubmissionShareFrame extends Model
{
    protected $fillable = [
        'competition_submission_id',
        'template_id',
        'story_frame_path',
        'post_frame_path',
        'portrait_frame_path',
        'landscape_frame_path',
        'qr_code_path',
        'generation_count',
        'last_generated_at',
        'original_width',
        'original_height',
        'original_orientation',
    ];

    protected $casts = [
        'last_generated_at' => 'datetime',
        'generation_count' => 'integer',
        'original_width' => 'integer',
        'original_height' => 'integer',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubmission::class, 'competition_submission_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CompetitionShareFrameTemplate::class, 'template_id');
    }

    /**
     * Get URLs for all generated frames
     */
    public function getStoryFrameUrlAttribute(): ?string
    {
        return $this->story_frame_path ? Storage::url($this->story_frame_path) : null;
    }

    public function getPostFrameUrlAttribute(): ?string
    {
        return $this->post_frame_path ? Storage::url($this->post_frame_path) : null;
    }

    public function getPortraitFrameUrlAttribute(): ?string
    {
        return $this->portrait_frame_path ? Storage::url($this->portrait_frame_path) : null;
    }

    public function getLandscapeFrameUrlAttribute(): ?string
    {
        return $this->landscape_frame_path ? Storage::url($this->landscape_frame_path) : null;
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->qr_code_path ? Storage::url($this->qr_code_path) : null;
    }

    /**
     * Check if frames exist
     */
    public function hasFrames(): bool
    {
        return $this->story_frame_path || $this->post_frame_path || $this->portrait_frame_path || $this->landscape_frame_path;
    }

    /**
     * Increment generation count
     */
    public function incrementGeneration(): void
    {
        $this->increment('generation_count');
        $this->update(['last_generated_at' => now()]);
    }
}
