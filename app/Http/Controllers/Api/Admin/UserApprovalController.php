<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserApprovalController extends Controller
{
    use ApiResponse;
    /**
     * Get pending users
     */
    public function index(Request $request)
    {
        $query = User::with(['photographer'])
            ->whereNotIn('role', ['admin', 'super_admin']);

        // Filter by approval status
        if ($request->has('status')) {
            $query->where('approval_status', $request->status);
        } else {
            $query->where('approval_status', 'pending');
        }

        // Filter by role
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }

    /**
     * Get approval statistics
     */
    public function stats()
    {
        $stats = [
            'pending' => User::where('approval_status', 'pending')
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->count(),
            'approved' => User::where('approval_status', 'approved')
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->count(),
            'rejected' => User::where('approval_status', 'rejected')
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->count(),
            'total' => User::whereNotIn('role', ['admin', 'super_admin'])->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Approve user
     */
    public function approve(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->approval_status === 'approved') {
            return response()->json([
                'message' => 'User is already approved'
            ], 400);
        }

        $user->update([
            'approval_status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        // Send approval email
        try {
            Mail::send('emails.user-approved', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Account Has Been Approved - Photographar SB');
            });
        } catch (\Exception $e) {
            \Log::error('Approval email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'User approved successfully',
            'user' => $user
        ]);
    }

    /**
     * Reject user
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'approval_status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        // Send rejection email
        try {
            Mail::send('emails.user-rejected', [
                'user' => $user,
                'reason' => $validated['reason']
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Account Registration Status - Photographar SB');
            });
        } catch (\Exception $e) {
            \Log::error('Rejection email failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'User rejected successfully',
            'user' => $user
        ]);
    }

    /**
     * Bulk approve
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $approvedCount = 0;

        foreach ($users as $user) {
            if ($user->approval_status !== 'approved') {
                $user->update([
                    'approval_status' => 'approved',
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);

                // Send approval email
                try {
                    Mail::send('emails.user-approved', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Your Account Has Been Approved - Photographar SB');
                    });
                } catch (\Exception $e) {
                    \Log::error('Approval email failed for user ' . $user->id);
                }

                $approvedCount++;
            }
        }

        return response()->json([
            'message' => "{$approvedCount} users approved successfully",
            'approved_count' => $approvedCount
        ]);
    }
}
