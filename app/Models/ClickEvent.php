<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClickEvent extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'page_url',
        'route_name',
        'element_tag',
        'element_id',
        'element_classes',
        'element_text',
        'element_name',
        'element_type',
        'input_value',
        'click_x',
        'click_y',
        'occurred_at',
        'user_agent',
        'ip_address',
    ];
}
