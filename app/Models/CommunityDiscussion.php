<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityDiscussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'tags',
        'likes_count',
        'comments_count',
        'shares_count',
        'is_featured',
        'status',
        'last_activity_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityDiscussionComment::class, 'discussion_id');
    }

    public function likes()
    {
        return $this->hasMany(CommunityDiscussionLike::class, 'discussion_id');
    }
}
