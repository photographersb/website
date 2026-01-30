<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'photographer_id',
        'name',
        'cover_image',
        'sample_images',
        'description',
        'category',
        'base_price',
        'price',
        'duration_unit',
        'duration_value',
        'duration_hours',
        'edited_photos',
        'raw_photos',
        'delivery_days',
        'includes',
        'excludes',
        'add_ons',
        'travel_cost_type',
        'travel_cost_value',
        'advance_booking_days',
        'is_active',
        'view_count',
        'inquiry_count',
        'booking_count',
        'display_order',
    ];

    protected $casts = [
        'sample_images' => 'array',
        'includes' => 'array',
        'excludes' => 'array',
        'add_ons' => 'array',
        'base_price' => 'decimal:2',
        'price' => 'decimal:2',
        'travel_cost_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
