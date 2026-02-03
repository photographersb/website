<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Judge extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'title',
        'bio',
        'profile_image',
        'email',
        'organization',
        'facebook_url',
        'instagram_url',
        'website_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($judge) {
            if (empty($judge->slug)) {
                $judge->slug = Str::slug($judge->name);
                
                // Ensure uniqueness
                $originalSlug = $judge->slug;
                $count = 1;
                
                while (static::where('slug', $judge->slug)->exists()) {
                    $judge->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'competition_judges', 'judge_profile_id')
            ->withPivot(['role', 'bio', 'expertise', 'is_active', 'sort_order', 'assigned_at'])
            ->withTimestamps();
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionScore::class, 'judge_id');
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
