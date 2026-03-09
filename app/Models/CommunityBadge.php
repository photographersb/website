<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'icon',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userBadges()
    {
        return $this->hasMany(CommunityUserBadge::class, 'badge_id');
    }
}
