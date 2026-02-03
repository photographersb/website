<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticeRole extends Model
{
    protected $table = 'notice_role';
    protected $fillable = ['notice_id', 'role'];
    public $timestamps = true;

    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class);
    }
}
