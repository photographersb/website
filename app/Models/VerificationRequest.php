<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'request_type',
        'requested_documents',
        'submitted_documents',
        'status',
        'reviewer_id',
        'reviewed_at',
        'reviewer_notes'
    ];

    protected $casts = [
        'requested_documents' => 'array',
        'submitted_documents' => 'array',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function approve(User $reviewer, ?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewer_id' => $reviewer->id,
            'reviewed_at' => now(),
            'reviewer_notes' => $notes
        ]);
    }

    public function reject(User $reviewer, string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewer_id' => $reviewer->id,
            'reviewed_at' => now(),
            'reviewer_notes' => $notes ?: $reason
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
