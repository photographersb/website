<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareFrame extends Model
{
    use HasFactory;
    
    protected $table = 'competition_share_frame_templates';
    
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
