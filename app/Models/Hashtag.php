<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'description',
        'usage_count',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(PhotoCategory::class, 'category_id');
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
