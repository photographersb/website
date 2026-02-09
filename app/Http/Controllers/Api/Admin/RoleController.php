<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    private function availablePermissions(): array
    {
        return [
            ['id' => 'manage_users', 'label' => 'Manage Users'],
            ['id' => 'manage_photographers', 'label' => 'Manage Photographers'],
            ['id' => 'verify_photographers', 'label' => 'Verify Photographers'],
            ['id' => 'manage_events', 'label' => 'Manage Events'],
            ['id' => 'manage_competitions', 'label' => 'Manage Competitions'],
            ['id' => 'manage_reviews', 'label' => 'Manage Reviews'],
            ['id' => 'manage_bookings', 'label' => 'Manage Bookings'],
            ['id' => 'view_analytics', 'label' => 'View Analytics'],
            ['id' => 'manage_settings', 'label' => 'Manage Settings'],
            ['id' => 'manage_roles', 'label' => 'Manage Roles'],
        ];
    }

    public function index()
    {
        $roles = Role::orderByDesc('is_system')->orderBy('name')->get();

        $roles = $roles->map(function (Role $role) {
            $userCount = User::where('role', $role->key)->count();

            return [
                'id' => $role->id,
                'name' => $role->name,
                'key' => $role->key,
                'description' => $role->description,
                'permissions' => $role->permissions ?? [],
                'isSystem' => $role->is_system,
                'icon' => $role->icon ?? '🧩',
                'colorClass' => $role->color_class ?? 'bg-gray-100',
                'userCount' => $userCount,
            ];
        });

        return response()->json([
            'roles' => $roles,
            'permissions' => $this->availablePermissions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'array',
            'permissions.*' => 'string|max:255',
        ]);

        $baseKey = Str::slug($validated['name'], '_');
        $key = $baseKey;
        $counter = 1;
        while (Role::where('key', $key)->exists()) {
            $key = $baseKey . '_' . $counter;
            $counter++;
        }

        $role = Role::create([
            'name' => $validated['name'],
            'key' => $key,
            'description' => $validated['description'] ?? null,
            'permissions' => $validated['permissions'] ?? [],
            'is_system' => false,
            'icon' => '🧩',
            'color_class' => 'bg-gray-100',
        ]);

        return response()->json([
            'role' => $role,
            'message' => 'Role created successfully',
        ], 201);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:1000',
            'permissions' => 'array',
            'permissions.*' => 'string|max:255',
        ]);

        $role->update([
            'name' => $validated['name'] ?? $role->name,
            'description' => array_key_exists('description', $validated) ? $validated['description'] : $role->description,
            'permissions' => $validated['permissions'] ?? $role->permissions,
        ]);

        return response()->json([
            'role' => $role,
            'message' => 'Role updated successfully',
        ]);
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return response()->json(['message' => 'System roles cannot be deleted.'], 409);
        }

        $userCount = User::where('role', $role->key)->count();
        if ($userCount > 0) {
            return response()->json(['message' => 'Role cannot be deleted while assigned to users.'], 409);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
