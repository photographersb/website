<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log user authentication
     */
    public static function logLogin(User $user): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => "{$user->name} logged in",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log user logout
     */
    public static function logLogout(?int $userId = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => 'logout',
            'description' => 'User logged out',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log model creation
     */
    public static function logCreated(Model $model, ?array $properties = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => Auth::user()->name . ' created ' . class_basename($model),
            'properties' => $properties ?? ['created' => $model->toArray()],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log model update
     */
    public static function logUpdated(Model $model, array $changes): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => Auth::user()->name . ' updated ' . class_basename($model),
            'properties' => [
                'changes' => $changes,
                'full_model' => $model->toArray(),
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log model deletion
     */
    public static function logDeleted(Model $model, ?array $properties = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => Auth::user()->name . ' deleted ' . class_basename($model),
            'properties' => $properties ?? ['deleted' => $model->toArray()],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log booking creation
     */
    public static function logBookingCreated(Model $booking): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'booking_created',
            'model_type' => get_class($booking),
            'model_id' => $booking->id,
            'description' => Auth::user()->name . ' created booking #' . $booking->id,
            'properties' => [
                'photographer_id' => $booking->photographer_id,
                'amount' => $booking->total_amount,
                'event_date' => $booking->event_date,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log booking status change
     */
    public static function logBookingStatusChanged(Model $booking, string $oldStatus, string $newStatus): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'booking_status_changed',
            'model_type' => get_class($booking),
            'model_id' => $booking->id,
            'description' => Auth::user()->name . " changed booking #{$booking->id} status from {$oldStatus} to {$newStatus}",
            'properties' => [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log payment received
     */
    public static function logPaymentReceived(Model $transaction): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'payment_received',
            'model_type' => get_class($transaction),
            'model_id' => $transaction->id,
            'description' => Auth::user()->name . ' received payment of ' . $transaction->amount . ' BDT',
            'properties' => [
                'amount' => $transaction->amount,
                'method' => $transaction->payment_method,
                'reference' => $transaction->reference_id,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log refund processed
     */
    public static function logRefundProcessed(Model $transaction, float $amount): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'refund_processed',
            'model_type' => get_class($transaction),
            'model_id' => $transaction->id,
            'description' => Auth::user()->name . ' processed refund of ' . $amount . ' BDT',
            'properties' => [
                'refund_amount' => $amount,
                'original_amount' => $transaction->amount,
                'reference' => $transaction->reference_id,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log competition winner announcement
     */
    public static function logCompetitionWinner(Model $submission): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'competition_winner_announced',
            'model_type' => get_class($submission),
            'model_id' => $submission->id,
            'description' => Auth::user()->name . ' announced ' . $submission->photographer->user->name . ' as winner in competition',
            'properties' => [
                'photographer_id' => $submission->photographer_id,
                'rank' => $submission->rank,
                'prize_amount' => $submission->prize_amount,
                'competition_id' => $submission->competition_id,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log certificate issued
     */
    public static function logCertificateIssued(Model $submission): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'certificate_issued',
            'model_type' => get_class($submission),
            'model_id' => $submission->id,
            'description' => Auth::user()->name . ' issued certificate to ' . $submission->photographer->user->name,
            'properties' => [
                'photographer_id' => $submission->photographer_id,
                'certificate_id' => $submission->certificate_id,
                'competition_id' => $submission->competition_id,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log user role change
     */
    public static function logRoleChanged(Model $user, string $oldRole, string $newRole): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'role_changed',
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'description' => Auth::user()->name . " changed {$user->name}'s role from {$oldRole} to {$newRole}",
            'properties' => [
                'target_user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $newRole,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log user suspension
     */
    public static function logUserSuspended(Model $user, ?string $reason = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_suspended',
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'description' => Auth::user()->name . ' suspended user ' . $user->name . ($reason ? " - Reason: {$reason}" : ''),
            'properties' => [
                'target_user_id' => $user->id,
                'reason' => $reason,
                'suspended_at' => now(),
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log quote sent
     */
    public static function logQuoteSent(Model $quote): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'quote_sent',
            'model_type' => get_class($quote),
            'model_id' => $quote->id,
            'description' => Auth::user()->name . ' sent quote of ' . $quote->amount . ' BDT',
            'properties' => [
                'photographer_id' => $quote->photographer_id,
                'amount' => $quote->amount,
                'inquiry_id' => $quote->inquiry_id,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log review submitted
     */
    public static function logReviewSubmitted(Model $review): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'review_submitted',
            'model_type' => get_class($review),
            'model_id' => $review->id,
            'description' => Auth::user()->name . ' submitted ' . $review->rating . '-star review',
            'properties' => [
                'rating' => $review->rating,
                'photographer_id' => $review->photographer_id,
                'booking_id' => $review->booking_id,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get activity logs for a specific user
     */
    public static function getUserActivity(int $userId, int $limit = 50)
    {
        return ActivityLog::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity logs for a specific model
     */
    public static function getModelActivity(string $modelType, int $modelId, int $limit = 50)
    {
        return ActivityLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity logs by action type
     */
    public static function getActivityByAction(string $action, int $limit = 50)
    {
        return ActivityLog::where('action', $action)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activities (last 24 hours)
     */
    public static function getRecentActivities(int $limit = 100)
    {
        return ActivityLog::where('created_at', '>=', now()->subDay())
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Export activity logs to CSV
     */
    public static function exportToCSV(string $action = null, ?int $limit = null)
    {
        $query = ActivityLog::query();

        if ($action) {
            $query->where('action', $action);
        }

        if ($limit) {
            $query->limit($limit);
        }

        $activities = $query->latest()->get();

        $fileName = 'activity_logs_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Add header
        fputcsv($handle, ['ID', 'User', 'Action', 'Model', 'Description', 'IP Address', 'Created At']);

        // Add data
        foreach ($activities as $activity) {
            fputcsv($handle, [
                $activity->id,
                $activity->user?->name ?? 'System',
                $activity->action,
                $activity->model_type,
                $activity->description,
                $activity->ip_address,
                $activity->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        return stream_get_contents($handle);
    }
}
