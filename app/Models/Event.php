<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'category_id',
        'organizer_id',
        'description',
        'location',
        'location_text',
        'city_id',
        'venue',
        'venue_name',
        'venue_address',
        'address',
        'latitude',
        'longitude',
        'event_date',
        'event_end_date',
        'start_datetime',
        'end_datetime',
        'start_time',
        'end_time',
        'all_day_event',
        'duration_hours',
        'theme',
        'hero_image_url',
        'event_type',
        'base_price',
        'ticket_price',
        'price',
        'capacity',
        'max_attendees',
        'require_registration',
        'is_ticketed',
        'registration_deadline',
        'certificates_enabled',
        'certificate_template_id',
        'booking_close_datetime',
        'refund_policy',
        'banner_image',
        'status',
        'is_featured',
        'featured_until',
        'requirements',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_end_date' => 'datetime',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'registration_deadline' => 'datetime',
        'featured_until' => 'datetime',
        'booking_close_datetime' => 'datetime',
        'all_day_event' => 'boolean',
        'is_featured' => 'boolean',
        'is_ticketed' => 'boolean',
        'require_registration' => 'boolean',
        'certificates_enabled' => 'boolean',
        'base_price' => 'decimal:2',
        'ticket_price' => 'decimal:2',
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'duration_hours' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->uuid)) {
                $event->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function organizer()
    {
        return $this->belongsTo(Photographer::class, 'organizer_id');
    }

    public function photographer()
    {
        return $this->belongsTo(Photographer::class, 'organizer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tickets()
    {
        return $this->hasMany(EventTicket::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function rsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class, 'event_mentors', 'event_id', 'mentor_id')
                    ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasManyThrough(EventPayment::class, EventRsvp::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_datetime', '<', now());
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    // Accessors
    public function getAvailableSeatsAttribute()
    {
        if (!$this->capacity) {
            return null;
        }

        return $this->capacity - $this->registrations()
            ->where('rsvp_status', 'going')
            ->count();
    }

    public function getConfirmedAttendeeCountAttribute()
    {
        return $this->registrations()
            ->where('rsvp_status', 'going')
            ->count();
    }

    public function getAttendedCountAttribute()
    {
        return $this->registrations()
            ->whereNotNull('check_in_at')
            ->count();
    }

    public function getIsFreeAttribute()
    {
        return $this->event_type === 'free';
    }

    public function getIsPaidAttribute()
    {
        return $this->event_type === 'paid';
    }

    // Methods
    public static function generateSlug($title)
    {
        $slug = Str::slug($title);
        $count = 1;
        $originalSlug = $slug;

        while (self::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function hasCapacityFor($qty = 1)
    {
        if (!$this->capacity) {
            return true;
        }

        return $this->available_seats >= 1;
    }

    public function isBookingOpen()
    {
        if ($this->booking_close_datetime) {
            return now() < $this->booking_close_datetime;
        }

        if (!$this->start_datetime) {
            return true; // If no start date, booking is open by default
        }

        return now() < $this->start_datetime->subDays(1);
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function getUserRegistration($userId)
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->first();
    }

    public function isSoldOut()
    {
        return $this->capacity && $this->confirmed_attendee_count >= $this->capacity;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
