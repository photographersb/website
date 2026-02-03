<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationTestController extends Controller
{
    use ApiResponse;
    /**
     * Create a test notification for the current user
     */
    public function createTestNotification(Request $request)
    {
        $user = Auth::user();
        
        $notificationId = (string) \Illuminate\Support\Str::uuid();
        
        DB::table('notifications')->insert([
            'id' => $notificationId,
            'type' => 'App\Notifications\TestNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'title' => $request->input('title', 'Test Notification'),
                'message' => $request->input('message', 'This is a test notification from admin panel'),
                'url' => $request->input('url', '/admin/dashboard'),
                'icon' => $request->input('icon', '🔔'),
                'type' => $request->input('type', 'info') // info, success, warning, error
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return $this->success([
            'notification_id' => $notificationId,
        ], 'Notification created successfully');
    }
    
    /**
     * Create notification for all admins
     */
    public function createAdminNotification(Request $request)
    {
        $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();
        
        $notifications = [];
        
        foreach ($admins as $admin) {
            $notificationId = (string) \Illuminate\Support\Str::uuid();
            
            DB::table('notifications')->insert([
                'id' => $notificationId,
                'type' => 'App\Notifications\AdminNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'title' => $request->input('title', 'Admin Notice'),
                    'message' => $request->input('message', 'Important admin notification'),
                    'url' => $request->input('url', '/admin/dashboard'),
                    'icon' => $request->input('icon', '⚠️'),
                    'type' => $request->input('type', 'warning')
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $notifications[] = $notificationId;
        }
        
        return $this->success([
            'admin_count' => count($admins),
            'notification_ids' => $notifications,
        ], 'Notification sent to ' . count($admins) . ' admins');
    }
}
