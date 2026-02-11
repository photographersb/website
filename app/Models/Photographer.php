<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasSeoMeta;

class Photographer extends Model
{
    use HasFactory, HasSeoMeta;

    protected $fillable = [
        'user_id',
        'city_id',
        'slug',
        'share_code',
        'profile_picture',
        'bio',
        'location',
        'experience_years',
        'specializations',
        'favorite_hashtags',
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
        'pexels_url',
        'bkash_number',
        'nagad_number',
        'rocket_number',
        'phone_number',
        'accept_tips',
        'tip_phone_number',
        'tip_message',
        'is_available',
        'response_time_preference',
        'booking_lead_time',
    ];

    protected $casts = [
        'specializations' => 'array',
        'favorite_hashtags' => 'array',
        'verified_at' => 'datetime',
        'featured_until' => 'datetime',
        'average_rating' => 'decimal:2',
        'response_time_avg' => 'decimal:2',
    ];

    /**
     * Append computed attributes to array/JSON output
     */
    protected $appends = ['profile_picture_url'];

    /**
     * Get profile picture URL with fallback
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        return $this->profile_picture;
    }

    /**
     * Profile Picture Accessor - Add storage URL prefix
     */
    public function getProfilePictureAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        
        // If already a full URL, return as is
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/storage/')) {
            return $value;
        }
        
        // Prepend /storage/ for relative paths
        return '/storage/' . ltrim($value, '/');
    }

    /**
     * Social Media Accessors - Extract usernames from URLs
     */
    public function getFacebookUsernameAttribute(): ?string
    {
        return $this->extractUsernameFromUrl($this->facebook_url, 'facebook.com');
    }

    public function getInstagramUsernameAttribute(): ?string
    {
        return $this->extractUsernameFromUrl($this->instagram_url, 'instagram.com');
    }

    public function getTwitterUsernameAttribute(): ?string
    {
        return $this->extractUsernameFromUrl($this->twitter_url, 'twitter.com', 'x.com');
    }

    public function getLinkedinUsernameAttribute(): ?string
    {
        return $this->extractUsernameFromUrl($this->linkedin_url, 'linkedin.com');
    }

    public function getYoutubeChannelAttribute(): ?string
    {
        return $this->extractUsernameFromUrl($this->youtube_url, 'youtube.com');
    }

    public function getSocialMediaAttribute(): array
    {
        return [
            'facebook' => [
                'url' => $this->facebook_url,
                'username' => $this->facebook_username,
                'platform' => 'Facebook',
            ],
            'instagram' => [
                'url' => $this->instagram_url,
                'username' => $this->instagram_username,
                'platform' => 'Instagram',
            ],
            'twitter' => [
                'url' => $this->twitter_url,
                'username' => $this->twitter_username,
                'platform' => 'Twitter/X',
            ],
            'linkedin' => [
                'url' => $this->linkedin_url,
                'username' => $this->linkedin_username,
                'platform' => 'LinkedIn',
            ],
            'youtube' => [
                'url' => $this->youtube_url,
                'username' => $this->youtube_channel,
                'platform' => 'YouTube',
            ],
            'website' => [
                'url' => $this->website_url,
                'platform' => 'Website',
            ],
            'pexels' => [
                'url' => $this->pexels_url,
                'platform' => 'Pexels',
            ],
        ];
    }

    /**
     * Helper method to extract username from social media URLs
     */
    private function extractUsernameFromUrl(?string $url, ...$domains): ?string
    {
        if (!$url || !is_string($url)) {
            return null;
        }

        try {
            // Remove protocol if present
            $url = preg_replace('#^https?://#', '', $url);
            $url = preg_replace('#^www\.#', '', $url);

            // Try each domain pattern
            foreach ($domains as $domain) {
                $domain = preg_replace('#^https?://#', '', $domain);
                $domain = preg_replace('#^www\.#', '', $domain);

                if (strpos($url, $domain) !== false) {
                    // Extract path after domain
                    $parts = explode($domain, $url);
                    $path = trim($parts[1] ?? '', '/');

                    // Handle special cases
                    $path = preg_replace('#[?&].*$#', '', $path); // Remove query params
                    $path = rtrim($path, '/');

                    // Extract username from path
                    $segments = array_filter(explode('/', $path));
                    return reset($segments) ?: null;
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get all active social media links (non-null)
     */
    public function getActiveSocialMediaAttribute(): array
    {
        $social = $this->social_media;
        return array_filter($social, fn($item) => !empty($item['url']));
    }

    /**
     * Get business name (from user's name if not set)
     */
    public function getBusinessNameAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(Location::class);
    }

    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(Hashtag::class, 'photographer_hashtag');
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

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class)->orderBy('display_order')->orderBy('year', 'desc');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(PhotographerNotification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function onboardingChecklist(): HasOne
    {
        return $this->hasOne(PhotographerOnboardingChecklist::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
