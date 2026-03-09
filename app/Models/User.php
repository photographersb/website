<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'username',
        'email',
        'phone',
        'referral_code',
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
        'approval_status',
        'rejection_reason',
        'approved_at',
        'approved_by_admin_id',
        'terms_accepted_at',
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
        'approval_status' => 'string',
        'approved_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
    ];

    /**
     * Boot function to auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function photographer()
    {
        return $this->hasOne(Photographer::class);
    }

    public function mentor()
    {
        return $this->hasOne(\App\Models\Mentor::class);
    }

    public function judge()
    {
        return $this->hasOne(\App\Models\Judge::class);
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

    public function competitionScores()
    {
        return $this->hasMany(CompetitionScore::class, 'judge_id');
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function usernameHistory()
    {
        return $this->hasMany(UsernameHistory::class);
    }

    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class, 'model');
    }

    public function bookingMessages()
    {
        return $this->hasMany(BookingMessage::class, 'sender_id');
    }

    public function favoritePhotographers()
    {
        return $this->belongsToMany(Photographer::class, 'photographer_favorites')
            ->withTimestamps();
    }

    public function verifications()
    {
        return $this->hasMany(UserVerification::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'user_id');
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_user_id');
    }

    public function referralReceived()
    {
        return $this->hasOne(Referral::class, 'referred_user_id');
    }

    public function referralRewards()
    {
        return $this->hasMany(ReferralReward::class);
    }

    public function shareLogs()
    {
        return $this->hasMany(ShareLog::class);
    }

    public function verificationRequests()
    {
        return $this->hasMany(VerificationRequest::class);
    }

    public function communityDiscussions()
    {
        return $this->hasMany(CommunityDiscussion::class);
    }

    public function communityDiscussionComments()
    {
        return $this->hasMany(CommunityDiscussionComment::class);
    }

    public function communityGroupsCreated()
    {
        return $this->hasMany(CommunityGroup::class, 'created_by');
    }

    public function communityGroupMemberships()
    {
        return $this->hasMany(CommunityGroupMember::class, 'user_id');
    }

    public function communityMentorshipProfile()
    {
        return $this->hasOne(CommunityMentorshipProfile::class, 'user_id');
    }

    public function communityMentorshipRequestsReceived()
    {
        return $this->hasMany(CommunityMentorshipRequest::class, 'mentor_user_id');
    }

    public function communityMentorshipRequestsSent()
    {
        return $this->hasMany(CommunityMentorshipRequest::class, 'requester_user_id');
    }

    public function communityBadges()
    {
        return $this->hasMany(CommunityUserBadge::class, 'user_id');
    }

    public function communityNotifications()
    {
        return $this->hasMany(CommunityNotification::class, 'user_id');
    }

    public function learningInstructorProfile()
    {
        return $this->hasOne(LearningInstructorProfile::class, 'user_id');
    }

    public function learningCourses()
    {
        return $this->hasMany(LearningCourse::class, 'instructor_user_id');
    }

    public function learningEnrollments()
    {
        return $this->hasMany(LearningEnrollment::class, 'user_id');
    }

    public function learningLessonProgress()
    {
        return $this->hasMany(LearningLessonProgress::class, 'user_id');
    }

    public function learningCourseReviews()
    {
        return $this->hasMany(LearningCourseReview::class, 'user_id');
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

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isJudge()
    {
        return $this->judge()->exists() || $this->judgeAssignments()->where('is_active', true)->exists();
    }

    public function isMentor()
    {
        return $this->mentor()->exists();
    }

    public function hasMultipleRoles()
    {
        $rolesCount = 0;
        if ($this->photographer) $rolesCount++;
        if ($this->mentor) $rolesCount++;
        if ($this->judge) $rolesCount++;
        return $rolesCount > 1;
    }

    public function getAvailableRoles()
    {
        $roles = ['base_role' => $this->role];
        if ($this->photographer) $roles['photographer'] = true;
        if ($this->mentor) $roles['mentor'] = true;
        if ($this->judge) $roles['judge'] = true;
        return $roles;
    }

    public function isSuspended()
    {
        return $this->is_suspended === true;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function approveAsAdmin($adminId): void
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by_admin_id' => $adminId,
            'rejection_reason' => null,
        ]);
    }

    public function rejectAsAdmin($adminId, string $reason): void
    {
        $this->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by_admin_id' => $adminId,
        ]);
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
}
