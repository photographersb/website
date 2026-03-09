<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityMentorshipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_user_id',
        'requester_user_id',
        'preferred_session_type',
        'message',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function mentorUser()
    {
        return $this->belongsTo(User::class, 'mentor_user_id');
    }

    public function requesterUser()
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }
}
