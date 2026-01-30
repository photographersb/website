<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionSponsor extends Model
{
    protected $fillable = [
        'competition_id',
        'name',
        'logo_url',
        'website_url',
        'description',
        'tier',
        'contribution_amount',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'contribution_amount' => 'decimal:2',
        'display_order' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the competition that owns the sponsor
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
