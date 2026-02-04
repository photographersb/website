<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Auth\Access\Response;

class VerificationRequestPolicy
{
    /**
     * Admins can view all verification requests
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin' || $user->role === 'admin';
    }

    /**
     * View a specific verification request (owner or admin)
     */
    public function view(User $user, VerificationRequest $verificationRequest): bool
    {
        return $user->id === $verificationRequest->user_id || $user->role === 'super_admin' || $user->role === 'admin';
    }

    /**
     * Photographers can create verification requests
     */
    public function create(User $user): bool
    {
        return $user->role === 'photographer';
    }

    /**
     * Photographers cannot update their requests
     */
    public function update(User $user, VerificationRequest $verificationRequest): bool
    {
        return false;
    }

    /**
     * Only super-admins can delete verification requests
     */
    public function delete(User $user, VerificationRequest $verificationRequest): bool
    {
        return $user->role === 'super_admin';
    }

    /**
     * Admins can approve requests
     */
    public function approve(User $user, VerificationRequest $verificationRequest): bool
    {
        return ($user->role === 'super_admin' || $user->role === 'admin') && $verificationRequest->isPending();
    }

    /**
     * Admins can reject requests
     */
    public function reject(User $user, VerificationRequest $verificationRequest): bool
    {
        return ($user->role === 'super_admin' || $user->role === 'admin') && $verificationRequest->isPending();
    }

    /**
     * Restore not allowed
     */
    public function restore(User $user, VerificationRequest $verificationRequest): bool
    {
        return false;
    }

    /**
     * Force delete not allowed
     */
    public function forceDelete(User $user, VerificationRequest $verificationRequest): bool
    {
        return false;
    }
}
