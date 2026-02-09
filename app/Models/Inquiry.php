<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Inquiry extends Model
{
    protected $fillable = [
        'uuid',
        'client_id',
        'photographer_id',
        'package_id',
        'event_date',
        'event_location',
        'latitude',
        'longitude',
        'preferred_time_slot',
        'event_type_detail',
        'indoor_outdoor',
        'guest_count',
        'budget_min',
        'budget_max',
        'requirements',
        'additional_services',
        'status',
        'response_message',
        'responded_at',
        'expires_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'responded_at' => 'datetime',
        'expires_at' => 'datetime',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'additional_services' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            if (empty($inquiry->uuid)) {
                $inquiry->uuid = (string) Str::uuid();
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
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
