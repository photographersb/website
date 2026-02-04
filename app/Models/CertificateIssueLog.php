<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateIssueLog extends Model
{
    protected $fillable = [
        'certificate_id',
        'action',
        'performed_by_user_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function performedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
