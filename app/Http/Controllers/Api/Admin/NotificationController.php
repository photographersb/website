<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current admin
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Build query
            $query = $user->notifications()->latest();

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

            return response()->json([
                'status' => 'success',
                'data' => [
                    'notifications' => $notifications->values(),
                    'stats' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load notifications: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);
            $notification->markAsRead();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            Auth::user()->unreadNotifications->markAsRead();
            
            return response()->json([
                'status' => 'success',
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark all as read'
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);
            $notification->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }

    /**
     * Delete all read notifications
     */
    public function deleteRead()
    {
        try {
            Auth::user()->notifications()
                ->whereNotNull('read_at')
                ->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Read notifications deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete read notifications'
            ], 500);
        }
    }
}
