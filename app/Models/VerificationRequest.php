<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class VerificationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'request_type',
        'full_name',
        'phone',
        'nid_number',
        'business_name',
        'document_front_path',
        'document_back_path',
        'selfie_path',
        'note',
        'status',
        'submitted_documents',
        'admin_note',
        'reviewed_by_user_id',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'submitted_documents' => 'array'
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    /**
     * Scopes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeRecentFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Helper Methods
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function approve(User $reviewer, ?string $adminNote = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by_user_id' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_note' => $adminNote
        ]);

        // Update user verification status
        $this->user->verification()->updateOrCreate(
            ['user_id' => $this->user_id],
            [
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by_user_id' => $reviewer->id,
                'verification_level' => $this->type === 'business' ? 'full' : 'basic'
            ]
        );
    }

    public function reject(User $reviewer, string $reason, ?string $adminNote = null): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_note' => $adminNote ?: $reason
        ]);

        // Ensure user verification is not marked as verified
        $this->user->verification()->updateOrCreate(
            ['user_id' => $this->user_id],
            ['is_verified' => false]
        );
    }

    /**
     * Get type label
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'phone' => 'Phone Verification',
            'nid' => 'National ID',
            'business' => 'Business Verification',
            default => ucfirst($this->type)
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }
}
