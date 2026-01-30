<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Photographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|unique:users',
            'role' => 'required|in:client,photographer,studio_owner',
        ]);

        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => null, // Require email verification
        ]);

        // Create photographer profile if role is photographer
        if ($validated['role'] === 'photographer') {
            Photographer::create([
                'user_id' => $user->id,
                'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
                'bio' => null,
                'experience_years' => 0,
                'is_verified' => false,
                'is_available' => true,
            ]);
        }

        // Send email verification OTP
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Log::error('Email verification failed: ' . $e->getMessage());
            // Continue registration even if email fails
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful. Please check your email to verify your account.',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
            ],
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please verify your email before logging in. Check your inbox for verification link.',
                'code' => 'EMAIL_NOT_VERIFIED',
            ], 403);
        }

        // Check if account is suspended
        if ($user->is_suspended) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been suspended',
            ], 403);
        }

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Create photographer profile if user is photographer but doesn't have one
        if ($user->role === 'photographer' && !$user->photographer) {
            Photographer::create([
                'user_id' => $user->id,
                'slug' => Str::slug($user->name) . '-' . Str::random(6),
                'bio' => null,
                'experience_years' => 0,
                'is_verified' => false,
                'is_available' => true,
            ]);
            // Reload user with photographer relationship
            $user->load('photographer');
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Check if user is also a judge
        $isJudge = $user->isJudge();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => array_merge($user->toArray(), ['is_judge' => $isJudge]),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'photographer') {
            $user->load('photographer');
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    /**
     * Verify email with token (from email link)
     */
    public function verifyEmail(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:users,id',
            'hash' => 'required|string',
        ]);

        $user = User::findOrFail($validated['id']);

        // Verify hash
        if (!hash_equals((string) $validated['hash'], sha1($user->getEmailForVerification()))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification link',
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Email already verified',
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully. You can now log in.',
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already verified',
            ], 400);
        }

        try {
            $user->sendEmailVerificationNotification();
            return response()->json([
                'status' => 'success',
                'message' => 'Verification email sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send verification email',
            ], 500);
        }
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // TODO: Generate reset token and send email

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset link sent to email',
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8|confirmed',
        ]);

        // TODO: Verify reset token

        $user = User::where('email', $validated['email'])->first();
        $user->update(['password' => Hash::make($validated['password'])]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully',
        ]);
    }
}
