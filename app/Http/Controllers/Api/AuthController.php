<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Photographer;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;
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
            'accept_terms' => 'accepted',
        ]);

        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => null, // Require email verification
            'terms_accepted_at' => now(),
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
            Log::error('Email verification failed: ' . $e->getMessage());
            // Continue registration even if email fails
        }

        return $this->created([
            'user_id' => $user->id,
            'email' => $user->email,
        ], 'Registration successful. Please check your email to verify your account.');
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

        if (!Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return $this->error('Please verify your email before logging in. Check your inbox for verification link.', 403);
        }

        // Super admin ALWAYS bypasses approval checks
        if ($user->role !== 'super_admin') {
            // Check if account is approved
            if ($user->approval_status === 'pending') {
                return $this->error('Your account is pending admin approval. You will receive an email once approved.', 403);
            }

            if ($user->approval_status === 'rejected') {
                return $this->error('Your account registration was rejected. Reason: ' . ($user->rejection_reason ?? 'Not specified'), 403);
            }
        }

        // Check if account is suspended
        if ($user->is_suspended) {
            return $this->error('Your account has been suspended', 403);
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

        // Check if user is also a judge
        $isJudge = $user->isJudge();

        return $this->success([
            'user' => array_merge($user->toArray(), ['is_judge' => $isJudge]),
        ], 'Login successful');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success([], 'Logged out successfully');
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

        return $this->success($user, 'User data retrieved successfully');
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
            return $this->error('Invalid verification link', 403);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success([], 'Email already verified');
        }

        $user->markEmailAsVerified();

        return $this->success([], 'Email verified successfully. You can now log in.');
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
            return $this->error('Email already verified', 400);
        }

        try {
            $user->sendEmailVerificationNotification();
            return $this->success([], 'Verification email sent successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to send verification email', 500);
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

        return $this->success([], 'Password reset link sent to email');
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

        return $this->success([], 'Password reset successfully');
    }

    /**
     * Send OTP to phone number (Bangladesh format: +8801XXXXXXXXX)
     */
    public function sendPhoneOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|regex:/^\+88\d{10}$/', // Bangladesh phone format
        ]);

        $phone = $validated['phone'];

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache with 5-minute expiry
        \Illuminate\Support\Facades\Cache::put("phone_otp_{$phone}", $otp, now()->addMinutes(5));

        // TODO: Send OTP via Twilio
        // For now, log to console in development
        if (app()->environment('local')) {
            Log::info("Phone OTP for {$phone}: {$otp}");
        }

        return $this->success([
            'phone' => $phone,
            'expires_in_seconds' => 300
        ], 'OTP sent to your phone number');
    }

    /**
     * Verify phone OTP
     */
    public function verifyPhoneOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|regex:/^\+88\d{10}$/',
            'otp' => 'required|numeric|digits:6',
        ]);

        $phone = $validated['phone'];
        $otp = $validated['otp'];

        // Get stored OTP from cache
        $storedOtp = \Illuminate\Support\Facades\Cache::get("phone_otp_{$phone}");

        // Check if OTP exists and matches
        if (!$storedOtp || $storedOtp !== $otp) {
            return $this->validationError(['otp' => ['Invalid or expired OTP']], 'Invalid or expired OTP');
        }

        // Find or update user
        $user = null;
        if (Auth::check()) {
            // If user is logged in, mark their phone as verified
            $user = Auth::user();
            $user->update([
                'phone_verified_at' => now(),
            ]);
        } else {
            // Find user by phone
            $user = User::where('phone', $phone)->first();
            if ($user) {
                $user->update([
                    'phone_verified_at' => now(),
                ]);
            }
        }

        // Delete used OTP
        \Illuminate\Support\Facades\Cache::forget("phone_otp_{$phone}");

        return $this->success([
            'phone' => $phone,
            'verified_at' => now()->toDateTimeString(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_verified_at' => $user->phone_verified_at
            ] : null
        ], 'Phone number verified successfully');
    }

    /**
     * Resend OTP to phone
     */
    public function resendPhoneOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|regex:/^\+88\d{10}$/',
        ]);

        $phone = $validated['phone'];

        // Check if user already requested OTP recently (rate limit: 1 per minute)
        $lastAttempt = \Illuminate\Support\Facades\Cache::get("phone_otp_attempt_{$phone}");
        if ($lastAttempt) {
            return $this->error('Please wait before requesting another OTP', 429);
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP and attempt tracker
        \Illuminate\Support\Facades\Cache::put("phone_otp_{$phone}", $otp, now()->addMinutes(5));
        \Illuminate\Support\Facades\Cache::put("phone_otp_attempt_{$phone}", true, now()->addMinute());

        if (app()->environment('local')) {
            Log::info("Resent Phone OTP for {$phone}: {$otp}");
        }

        return $this->success([
            'phone' => $phone,
            'expires_in_seconds' => 300
        ], 'OTP resent to your phone number');
    }
}
