<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVerification extends Model
{
    protected $fillable = [
        'user_id',
        'verification_type',
        'verification_status',
        'document_url',
        'document_number',
        'document_type',
        'verified_by_admin_id',
        'verified_at',
        'rejection_reason',
        'notes',
        'expires_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }

    public function approve(User $admin, ?string $notes = null): void
    {
        $this->update([
            'verification_status' => 'approved',
            'verified_by_admin_id' => $admin->id,
            'verified_at' => now(),
            'notes' => $notes
        ]);
    }

    public function reject(User $admin, string $reason, ?string $notes = null): void
    {
        $this->update([
            'verification_status' => 'rejected',
            'verified_by_admin_id' => $admin->id,
            'verified_at' => now(),
            'rejection_reason' => $reason,
            'notes' => $notes
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now() > $this->expires_at;
    }
}
