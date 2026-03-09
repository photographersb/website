<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityUserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_id',
        'user_id',
        'awarded_by',
        'awarded_reason',
        'awarded_at',
    ];

    protected $casts = [
        'awarded_at' => 'datetime',
    ];

    public function badge()
    {
        return $this->belongsTo(CommunityBadge::class, 'badge_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
