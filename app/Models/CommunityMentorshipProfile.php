<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityMentorshipProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expertise',
        'years_experience',
        'availability_status',
        'session_types',
        'bio',
        'is_active',
    ];

    protected $casts = [
        'expertise' => 'array',
        'session_types' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
