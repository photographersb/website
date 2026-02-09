<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationController extends Controller
{
    use ApiResponse;
    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        try {
            $query = $request->user()->notifications()->latest();

            // Apply type filter
            if ($request->filled('type')) {
                $query->where('type', 'like', '%' . $request->type . '%');
            }

            // Apply status filter
            if ($request->filled('status')) {
                if ($request->status === 'unread') {
                    $query->whereNull('read_at');
                } elseif ($request->status === 'read') {
                    $query->whereNotNull('read_at');
                }
            }

            // Apply period filter
            if ($request->filled('period') && $request->period !== 'all') {
                $now = Carbon::now();
                switch ($request->period) {
                    case 'today':
                        $query->whereDate('created_at', $now->toDateString());
                        break;
                    case 'week':
                        $query->where('created_at', '>=', $now->copy()->subWeek());
                        break;
                    case 'month':
                        $query->where('created_at', '>=', $now->copy()->subMonth());
                        break;
                }
            }

            $notifications = $query->get()->map(function ($notification) {
                // Extract type from notification type class name
                $typeClass = $notification->type;
                $typeParts = explode('\\', $typeClass);
                $typeBaseName = end($typeParts);
                
                // Convert notification type to simple type
                if (str_contains($typeBaseName, 'Booking')) {
                    $simpleType = 'booking';
                } elseif (str_contains($typeBaseName, 'Payment') || str_contains($typeBaseName, 'Transaction')) {
                    $simpleType = 'payment';
                } elseif (str_contains($typeBaseName, 'Tip')) {
                    $simpleType = 'tip';
                } elseif (str_contains($typeBaseName, 'Review')) {
                    $simpleType = 'review';
                } elseif (str_contains($typeBaseName, 'Verification') || str_contains($typeBaseName, 'Verified')) {
                    $simpleType = 'verification';
                } elseif (str_contains($typeBaseName, 'Competition')) {
                    $simpleType = 'competition';
                } else {
                    $simpleType = 'system';
                }
                
                return [
                    'id' => $notification->id,
                    'type' => $simpleType,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at
                ];
            });

            // Calculate stats
            $stats = [
                'total' => $notifications->count(),
                'unread' => $notifications->where('read_at', null)->count(),
                'today' => $notifications->where('created_at', '>=', Carbon::today())->count(),
                'week' => $notifications->where('created_at', '>=', Carbon::now()->subWeek())->count(),
            ];

            return $this->success([
                'notifications' => $notifications->values(),
                'stats' => $stats
            ], 'Notifications retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to load notifications: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request)
    {
        $count = $request->user()->unreadNotifications->count();

        return $this->success(['count' => $count], 'Unread notification count retrieved successfully');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return $this->success([], 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->success([], 'All notifications marked as read');
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return $this->success([], 'Notification deleted successfully');
    }

    /**
     * Delete all read notifications
     */
    public function deleteRead(Request $request)
    {
        try {
            $request->user()->notifications()
                ->whereNotNull('read_at')
                ->delete();

            return $this->success([], 'Read notifications deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete read notifications: ' . $e->getMessage(), 500);
        }
    }
}
