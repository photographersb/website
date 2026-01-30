<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    protected $fillable = [
        'inquiry_id',
        'photographer_id',
        'package_id',
        'base_price',
        'add_ons',
        'add_ons_total',
        'travel_cost',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'items',
        'terms',
        'cancellation_policy',
        'deposit_percentage',
        'validity_days',
        'status',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'expires_at',
    ];

    protected $casts = [
        'add_ons' => 'array',
        'items' => 'array',
        'base_price' => 'decimal:2',
        'add_ons_total' => 'decimal:2',
        'travel_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
