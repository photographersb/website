<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionShareFrameTemplate extends Model
{
    protected $fillable = [
        'competition_id',
        'name',
        'background_image',
        'background_color',
        'text_color',
        'accent_color',
        'font_family',
        'cta_message',
        'show_competition_name',
        'show_photographer_name',
        'show_submission_title',
        'show_watermark',
        'watermark_position',
        'show_qr_code',
        'qr_position',
        'padding_top',
        'padding_bottom',
        'padding_left',
        'padding_right',
        'image_fit_strategy',
        'add_text_overlay_gradient',
        'is_active',
    ];

    protected $casts = [
        'show_competition_name' => 'boolean',
        'show_photographer_name' => 'boolean',
        'show_submission_title' => 'boolean',
        'show_watermark' => 'boolean',
        'show_qr_code' => 'boolean',
        'add_text_overlay_gradient' => 'boolean',
        'is_active' => 'boolean',
        'padding_top' => 'integer',
        'padding_bottom' => 'integer',
        'padding_left' => 'integer',
        'padding_right' => 'integer',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function generatedFrames(): HasMany
    {
        return $this->hasMany(SubmissionShareFrame::class, 'template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->background_image 
            ? asset('storage/' . $this->background_image) 
            : null;
    }
}
