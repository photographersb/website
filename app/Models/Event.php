<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'uuid',
        'organizer_id',
        'title',
        'slug',
        'description',
        'event_type',
        'theme',
        'hero_image_url',
        'event_date',
        'event_end_date',
        'start_time',
        'end_time',
        'all_day_event',
        'location',
        'address',
        'latitude',
        'longitude',
        'city_id',
        'max_attendees',
        'require_registration',
        'is_ticketed',
        'ticket_price',
        'status',
        'is_featured',
        'featured_until',
        'view_count',
        'rsvp_count',
        'gallery_published',
        'is_verified',
        'requirements',
        'duration_hours',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_end_date' => 'datetime',
        'all_day_event' => 'boolean',
        'require_registration' => 'boolean',
        'is_ticketed' => 'boolean',
        'ticket_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
        'gallery_published' => 'boolean',
        'is_verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class, 'organizer_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->uuid)) {
                $event->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
