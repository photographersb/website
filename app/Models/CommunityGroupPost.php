<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityGroupPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'content',
        'image_url',
        'likes_count',
        'comments_count',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(CommunityGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityGroupPostComment::class, 'post_id');
    }
}
