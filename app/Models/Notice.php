<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'priority',
        'status',
        'publish_at',
        'expires_at',
        'icon',
        'color',
        'show_to_all_roles',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'expires_at' => 'datetime',
        'show_to_all_roles' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created the notice
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the notice
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the roles this notice targets
     */
    public function roles()
    {
        return $this->hasMany(NoticeRole::class);
    }

    /**
     * Get read receipts for this notice
     */
    public function reads(): HasMany
    {
        return $this->hasMany(NoticeRead::class);
    }

    /**
     * Check if notice is currently active
     */
    public function isActive(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->publish_at && $this->publish_at > now()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can see this notice
     */
    public function isVisibleTo(User $user): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->show_to_all_roles) {
            return true;
        }

        $targetRoles = $this->roles()->pluck('role')->toArray();
        return in_array($user->role, $targetRoles);
    }

    /**
     * Attach roles to notice
     */
    public function attachRoles(array $roles): void
    {
        foreach ($roles as $role) {
            NoticeRole::firstOrCreate([
                'notice_id' => $this->id,
                'role' => $role,
            ]);
        }
    }

    /**
     * Detach roles from notice
     */
    public function detachRoles(array $roles = []): void
    {
        if (empty($roles)) {
            NoticeRole::where('notice_id', $this->id)->delete();
        } else {
            NoticeRole::where('notice_id', $this->id)
                ->whereIn('role', $roles)
                ->delete();
        }
    }

    /**
     * Mark notice as read by user
     */
    public function markAsReadBy(User $user): void
    {
        NoticeRead::firstOrCreate([
            'notice_id' => $this->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Check if user has read this notice
     */
    public function isReadBy(User $user): bool
    {
        return NoticeRead::where('notice_id', $this->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
