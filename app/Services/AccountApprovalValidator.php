<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class AccountApprovalValidator
{
    /**
     * Account status lifecycle:
     * 1. pending    -> Account created, awaiting admin approval
     * 2. approved   -> Admin approved, user can access paid features
     * 3. rejected   -> Admin rejected, account disabled
     */

    /**
     * Valid transitions for account approval status
     */
    private const VALID_APPROVAL_TRANSITIONS = [
        'pending' => ['approved', 'rejected'],
        'approved' => ['rejected'],
        'rejected' => ['pending'],
    ];

    /**
     * Validate if user account is approved for paid access
     * 
     * @param User|int $user User instance or ID
     * @return array {approved: bool, reason?: string, user_status?: string}
     */
    public static function isApprovedForPaidAccess($user)
    {
        if (is_numeric($user)) {
            $user = User::find($user);
        }

        if (!$user) {
            return [
                'approved' => false,
                'reason' => 'User not found'
            ];
        }

        $status = $user->approval_status ?? 'pending';

        // Check approval status
        $allowedStatuses = ['approved'];

        if (!in_array($status, $allowedStatuses, true)) {
            Log::warning('Access denied: account not approved', [
                'user_id' => $user->id,
                'status' => $status,
                'email' => $user->email,
            ]);

            return [
                'approved' => false,
                'reason' => match($status) {
                    'pending' => 'Your account is pending admin approval. You can use free features only.',
                    'rejected' => 'Your account was rejected. Please contact support.',
                    default => 'Your account does not have access to paid features.',
                },
                'user_status' => $status
            ];
        }

        // Check account suspended
        if ($user->is_suspended) {
            Log::warning('Access denied: account suspended', [
                'user_id' => $user->id,
                'suspended_at' => $user->suspended_at,
            ]);

            return [
                'approved' => false,
                'reason' => 'Your account is currently suspended. Please contact support.',
                'user_status' => 'suspended'
            ];
        }

        // Check account email verified
        if (!$user->email_verified_at) {
            Log::warning('Access denied: email not verified', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return [
                'approved' => false,
                'reason' => 'Please verify your email address to access paid features.',
                'user_status' => 'email_unverified'
            ];
        }

        Log::info('Account approval verified', [
            'user_id' => $user->id,
            'status' => $status,
        ]);

        return [
            'approved' => true,
            'user_status' => $status
        ];
    }

    /**
     * Get detailed approval information for a user
     * 
     * @param User $user
     * @return array
     */
    public static function getApprovalStatus(User $user): array
    {
        $status = $user->approval_status ?? 'pending';
        $validation = self::isApprovedForPaidAccess($user);

        return [
            'user_id' => $user->id,
            'approval_status' => $status,
            'approved_for_paid_access' => $validation['approved'],
            'reason' => $validation['reason'] ?? null,
            'email_verified' => (bool) $user->email_verified_at,
            'account_locked' => (bool) $user->is_suspended,
            'approved_at' => $user->approved_at,
            'approved_by' => $user->approved_by_admin_id,
        ];
    }

    /**
     * Approve a user account
     * 
     * @param User $user
     * @param int $approvedBy Admin user ID
     * @return array {success: bool, error?: string}
     */
    public static function approveAccount(User $user, int $approvedBy): array
    {
        try {
            // Validate transition
            $currentStatus = $user->approval_status ?? 'pending';
            $allowedTransitions = self::VALID_APPROVAL_TRANSITIONS[$currentStatus] ?? [];

            if (!in_array('approved', $allowedTransitions, true)) {
                return [
                    'success' => false,
                    'error' => "Cannot approve account with status: {$currentStatus}"
                ];
            }

            // Verify approver is admin
            $approver = User::find($approvedBy);
            if (!$approver || !$approver->isAdmin()) {
                return [
                    'success' => false,
                    'error' => 'Only admins can approve accounts'
                ];
            }

            // Update user
            $user->update([
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by_admin_id' => $approvedBy,
            ]);

            Log::info('User account approved', [
                'user_id' => $user->id,
                'approved_by' => $approvedBy,
                'email' => $user->email,
            ]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Account approval failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to approve account: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reject a user account
     * 
     * @param User $user
     * @param string $reason Rejection reason
     * @param int $rejectedBy Admin user ID
     * @return array {success: bool, error?: string}
     */
    public static function rejectAccount(User $user, string $reason, int $rejectedBy): array
    {
        try {
            // Validate transition
            $currentStatus = $user->approval_status ?? 'pending';
            $allowedTransitions = self::VALID_APPROVAL_TRANSITIONS[$currentStatus] ?? [];

            if (!in_array('rejected', $allowedTransitions, true)) {
                return [
                    'success' => false,
                    'error' => "Cannot reject account with status: {$currentStatus}"
                ];
            }

            // Verify rejector is admin
            $rejector = User::find($rejectedBy);
            if (!$rejector || !$rejector->isAdmin()) {
                return [
                    'success' => false,
                    'error' => 'Only admins can reject accounts'
                ];
            }

            // Update user
            $user->update([
                'approval_status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            Log::warning('User account rejected', [
                'user_id' => $user->id,
                'reason' => $reason,
                'rejected_by' => $rejectedBy,
                'email' => $user->email,
            ]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Account rejection failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to reject account: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Suspend a user account temporarily
     * 
     * @param User $user
     * @param string $reason Suspension reason
     * @param \DateTime|null $until Expiry time (null = indefinite)
     * @return array {success: bool, error?: string}
     */
    public static function suspendAccount(User $user, string $reason): array
    {
        try {
            $user->update([
                'is_suspended' => true,
                'suspension_reason' => $reason,
                'suspended_at' => now(),
            ]);

            Log::warning('User account suspended', [
                'user_id' => $user->id,
                'reason' => $reason,
                'email' => $user->email,
            ]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Account suspension failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to suspend account: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all users pending approval
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAccountsPendingApproval(int $limit = 50)
    {
        return User::where('approval_status', 'pending')
            ->orWhereNull('approval_status')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get suspended accounts
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSuspendedAccounts(int $limit = 50)
    {
        return User::where('is_suspended', true)
            ->orderBy('suspended_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Unlock a user account (remove suspension)
     * 
     * @param User $user
     * @return array {success: bool, error?: string}
     */
    public static function unlockAccount(User $user): array
    {
        try {
            $user->update([
                'is_suspended' => false,
            ]);

            Log::info('User account unlocked', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Account unlock failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to unlock account: ' . $e->getMessage()
            ];
        }
    }
}
