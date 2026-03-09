<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityModerationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'moderator_user_id',
        'target_user_id',
        'action_type',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_user_id');
    }
}
