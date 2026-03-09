<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityDiscussionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'user_id',
        'parent_id',
        'content',
        'status',
    ];

    public function discussion()
    {
        return $this->belongsTo(CommunityDiscussion::class, 'discussion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
