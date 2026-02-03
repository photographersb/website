<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Mentor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'title',
        'organization',
        'bio',
        'profile_image',
        'email',
        'phone',
        'facebook_url',
        'instagram_url',
        'website_url',
        'country',
        'city',
        'is_active',
        'sort_order',
        'user_id',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mentor) {
            if (empty($mentor->slug)) {
                $mentor->slug = Str::slug($mentor->name);
                
                // Ensure uniqueness
                $originalSlug = $mentor->slug;
                $count = 1;
                
                while (static::where('slug', $mentor->slug)->exists()) {
                    $mentor->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'competition_mentor')
            ->withPivot(['role_type', 'note', 'sort_order'])
            ->withTimestamps()
            ->orderBy('competition_mentor.sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
