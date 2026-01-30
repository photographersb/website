<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hashtags()
    {
        return $this->hasMany(Hashtag::class, 'category_id');
    }
}
