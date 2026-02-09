<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    use ApiResponse;

    public function show(Request $request)
    {
        $user = $request->user();

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
        ], 'Profile loaded');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return $this->success([
            'name' => $user->name,
            'phone' => $user->phone,
        ], 'Profile updated');
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'two_factor_enabled' => ['required', 'boolean'],
        ]);

        if ($validated['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->email = $validated['email'];
        $user->two_factor_enabled = $validated['two_factor_enabled'];
        $user->save();

        return $this->success([
            'email' => $user->email,
            'two_factor_enabled' => (bool) $user->two_factor_enabled,
        ], 'Account updated');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->error('Current password is incorrect', 422);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return $this->success(null, 'Password updated');
    }
}
