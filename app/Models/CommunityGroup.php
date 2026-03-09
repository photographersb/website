<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image_url',
        'type',
        'city_id',
        'created_by',
        'members_count',
        'posts_count',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function city()
    {
        return $this->belongsTo(Location::class, 'city_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(CommunityGroupMember::class, 'group_id');
    }

    public function posts()
    {
        return $this->hasMany(CommunityGroupPost::class, 'group_id');
    }
}
