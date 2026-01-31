<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Photographer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'profile_picture',
        'bio',
        'experience_years',
        'specializations',
        'service_area_radius',
        'average_rating',
        'rating_count',
        'is_verified',
        'verification_type',
        'verified_at',
        'is_featured',
        'featured_until',
        'profile_completeness',
        'total_bookings',
        'completed_bookings',
        'response_time_avg',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'website_url',
    ];

    protected $casts = [
        'specializations' => 'array',
        'verified_at' => 'datetime',
        'featured_until' => 'datetime',
        'average_rating' => 'decimal:2',
        'response_time_avg' => 'decimal:2',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function trustScore(): HasOne
    {
        return $this->hasOne(TrustScore::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'photographer_category');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(PhotographerFavorite::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'photographer_favorites')
                    ->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
