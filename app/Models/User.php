<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'email_verified_at',
        'phone_verified_at',
        'profile_photo_url',
        'bio',
        'is_suspended',
        'suspension_reason',
        'suspended_at',
        'last_login_at',
        'last_login_ip',
        'two_factor_enabled',
        'two_factor_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'suspended_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function photographer()
    {
        return $this->hasOne(Photographer::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function judgeAssignments()
    {
        return $this->hasMany(CompetitionJudge::class, 'judge_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_suspended', false);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isPhotographer()
    {
        return in_array($this->role, ['photographer', 'studio_owner', 'studio_photographer']);
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin', 'moderator']);
    }

    public function isJudge()
    {
        return $this->judgeAssignments()->where('is_active', true)->exists();
    }

    public function isSuspended()
    {
        return $this->is_suspended === true;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
}
