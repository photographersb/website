<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompetitionSponsor extends Model
{
    protected $fillable = [
        'competition_id',
        'sponsor_id',
        'name',
        'logo_url',
        'logo_credit_name',
        'logo_credit_url',
        'website_url',
        'description',
        'tier',
        'contribution_amount',
        'sponsored_amount',
        'display_order',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'contribution_amount' => 'decimal:2',
        'sponsored_amount' => 'decimal:2',
        'display_order' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the competition that owns the sponsor
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function getLogoUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/storage/'])) {
            return $value;
        }

        return Storage::url($value);
    }
}
