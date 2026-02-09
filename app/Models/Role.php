<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'key',
        'description',
        'permissions',
        'is_system',
        'icon',
        'color_class',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];
}
