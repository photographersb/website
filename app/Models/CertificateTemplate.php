<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'width',
        'height',
        'background_image',
        'background_color',
        'accent_color',
        'text_color',
        'font_family',
        'font_size',
        'title_font',
        'is_default',
        'template_content',
    ];

    protected $casts = [
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'font_size' => 'integer',
        'is_default' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'template_id');
    }

    public function scopeActive($query)
    {
        return $query;
    }
}
