<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Notice;
use App\Models\User;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class NoticeController extends Controller
{
    /**
     * Get all roles for dropdown
     */
    protected function getAvailableRoles(): array
    {
        return [
            'admin' => 'Admin',
            'super_admin' => 'Super Admin',
            'moderator' => 'Moderator',
            'photographer' => 'Photographer',
            'organizer' => 'Organizer',
            'client' => 'Client/Customer',
        ];
    }

    /**
     * Get user notices (role-based)
     */
    public function getMyNotices(Request $request)
    {
        $user = auth()->user();

        $notices = Notice::where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('publish_at')
                      ->orWhere('publish_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
            })
            ->where(function ($query) use ($user) {
                $query->where('show_to_all_roles', true)
                      ->orWhereHas('roles', function ($q) use ($user) {
                          $q->where('role', $user->role);
                      });
            })
            ->orderBy('priority', 'desc')
            ->orderBy('publish_at', 'desc')
            ->get()
            ->map(function ($notice) use ($user) {
                return [
                    'id' => $notice->id,
                    'title' => $notice->title,
                    'message' => $notice->message,
                    'priority' => $notice->priority,
                    'icon' => $notice->icon,
                    'color' => $notice->color,
                    'is_read' => $notice->isReadBy($user),
                    'published_at' => $notice->publish_at,
                    'expires_at' => $notice->expires_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $notices,
        ]);
    }

    /**
     * Mark notice as read
     */
    public function markAsRead(Request $request, $noticeId)
    {
        $user = auth()->user();
        $notice = Notice::findOrFail($noticeId);

        if (!$notice->isVisibleTo($user)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $notice->markAsReadBy($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Notice marked as read',
        ]);
    }

    /**
     * ADMIN: List all notices
     */
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $query = Notice::with('creator', 'updater', 'roles');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                  ->orWhere('message', 'LIKE', "%$search%");
            });
        }

        $notices = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(function ($notice) {
                return [
                    'id' => $notice->id,
                    'title' => $notice->title,
                    'message' => substr($notice->message, 0, 100) . '...',
                    'priority' => $notice->priority,
                    'status' => $notice->status,
                    'show_to_all_roles' => $notice->show_to_all_roles,
                    'roles' => $notice->roles->pluck('role'),
                    'publish_at' => $notice->publish_at,
                    'expires_at' => $notice->expires_at,
                    'is_active' => $notice->isActive(),
                    'created_by' => $notice->creator->name ?? 'Unknown',
                    'created_at' => $notice->created_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $notices->items(),
            'meta' => [
                'total' => $notices->total(),
                'per_page' => $notices->perPage(),
                'current_page' => $notices->currentPage(),
            ],
        ]);
    }

    /**
     * ADMIN: Create notice
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'in:draft,published',
            'publish_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:publish_at',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'show_to_all_roles' => 'boolean',
            'roles' => 'array',
            'roles.*' => 'string|in:admin,super_admin,moderator,photographer,organizer,client',
        ]);

        try {
            $notice = Notice::create([
                'title' => $validated['title'],
                'message' => $validated['message'],
                'priority' => $validated['priority'],
                'status' => $validated['status'] ?? 'draft',
                'publish_at' => $validated['publish_at'] ?? now(),
                'expires_at' => $validated['expires_at'] ?? null,
                'icon' => $validated['icon'] ?? null,
                'color' => $validated['color'] ?? null,
                'show_to_all_roles' => $validated['show_to_all_roles'] ?? false,
                'created_by' => auth()->id(),
            ]);

            // Attach roles
            if (!$validated['show_to_all_roles'] && !empty($validated['roles'])) {
                $notice->attachRoles($validated['roles']);
            }

            Log::info('Notice created', ['notice_id' => $notice->id, 'created_by' => auth()->id()]);

            return response()->json([
                'status' => 'success',
                'message' => 'Notice created successfully',
                'data' => $notice,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create notice: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create notice',
            ], 500);
        }
    }

    /**
     * ADMIN: Get single notice
     */
    public function show($noticeId)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $notice = Notice::with('creator', 'updater', 'roles')->findOrFail($noticeId);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $notice->id,
                'title' => $notice->title,
                'message' => $notice->message,
                'priority' => $notice->priority,
                'status' => $notice->status,
                'publish_at' => $notice->publish_at,
                'expires_at' => $notice->expires_at,
                'icon' => $notice->icon,
                'color' => $notice->color,
                'show_to_all_roles' => $notice->show_to_all_roles,
                'roles' => $notice->roles->pluck('role'),
                'created_by' => $notice->creator->name ?? 'Unknown',
                'updated_by' => $notice->updater->name ?? null,
                'created_at' => $notice->created_at,
                'updated_at' => $notice->updated_at,
                'read_count' => $notice->reads()->count(),
            ],
        ]);
    }

    /**
     * ADMIN: Update notice
     */
    public function update(Request $request, $noticeId)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $notice = Notice::findOrFail($noticeId);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'message' => 'string',
            'priority' => 'in:low,normal,high,urgent',
            'status' => 'in:draft,published,archived',
            'publish_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:publish_at',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'show_to_all_roles' => 'boolean',
            'roles' => 'array',
            'roles.*' => 'string',
        ]);

        try {
            $notice->update(array_merge(
                array_intersect_key($validated, array_flip(['title', 'message', 'priority', 'status', 'publish_at', 'expires_at', 'icon', 'color', 'show_to_all_roles'])),
                ['updated_by' => auth()->id()]
            ));

            // Update roles if provided
            if (isset($validated['roles'])) {
                $notice->detachRoles();
                if (!$validated['show_to_all_roles']) {
                    $notice->attachRoles($validated['roles']);
                }
            }

            Log::info('Notice updated', ['notice_id' => $notice->id, 'updated_by' => auth()->id()]);

            return response()->json([
                'status' => 'success',
                'message' => 'Notice updated successfully',
                'data' => $notice,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update notice: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update notice',
            ], 500);
        }
    }

    /**
     * ADMIN: Delete notice
     */
    public function destroy($noticeId)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $notice = Notice::findOrFail($noticeId);
        $notice->delete();

        Log::info('Notice deleted', ['notice_id' => $notice->id, 'deleted_by' => auth()->id()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notice deleted successfully',
        ]);
    }

    /**
     * Get available roles for UI
     */
    public function getRoles()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->getAvailableRoles(),
        ]);
    }
}
