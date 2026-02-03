<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'badge_color',
        'required_count',
        'points',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Badge colors
    const COLOR_GRAY = 'gray';
    const COLOR_BRONZE = 'bronze';
    const COLOR_SILVER = 'silver';
    const COLOR_GOLD = 'gold';
    const COLOR_PLATINUM = 'platinum';

    // Categories
    const CATEGORY_BOOKINGS = 'bookings';
    const CATEGORY_REVIEWS = 'reviews';
    const CATEGORY_PORTFOLIO = 'portfolio';
    const CATEGORY_COMPETITIONS = 'competitions';
    const CATEGORY_COMMUNITY = 'community';

    public function photographerAchievements()
    {
        return $this->hasMany(PhotographerAchievement::class);
    }

    public function photographer_achievements()
    {
        return $this->photographerAchievements();
    }
}
