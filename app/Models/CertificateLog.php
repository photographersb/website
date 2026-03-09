<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateLog extends Model
{
    use HasFactory;

    protected $table = 'certificate_logs';

    protected $fillable = [
        'certificate_id',
        'user_id',
        'action_type',
        'entity_type',
        'entity_id',
        'message',
        'rule_triggered',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
