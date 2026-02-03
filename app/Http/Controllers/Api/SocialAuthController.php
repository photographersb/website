<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Laravel\Socialite\Facades\Socialite;
use App\Http\Traits\ApiResponse;

class SocialAuthController extends Controller
{
    use ApiResponse;
    /**
     * Redirect to the OAuth provider
     */
    public function redirectToProvider(string $provider): JsonResponse
    {
        try {
            $this->validateProvider($provider);
            
            $redirectUrl = Socialite::driver($provider)
                ->stateless()
                ->redirect()
                ->getTargetUrl();
            
            return $this->success([
                'redirect_url' => $redirectUrl
            ], 'Redirect URL generated successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to redirect to provider', 500);
        }
    }

    /**
     * Handle OAuth provider callback
     */
    public function handleProviderCallback(string $provider): JsonResponse
    {
        try {
            $this->validateProvider($provider);
            
            // Get user from provider
            $providerUser = Socialite::driver($provider)->stateless()->user();
            
            // Find or create user
            $user = $this->findOrCreateUser($providerUser, $provider);
            
            // Generate API token
            $token = $user->createToken('social-auth-token')->plainTextToken;
            
            return $this->success([
                'user' => $user,
                'token' => $token,
            ], 'Successfully authenticated with ' . ucfirst($provider));
            
        } catch (\Exception $e) {
            return $this->error('Authentication failed', 500);
        }
    }

    /**
     * Find or create user from provider data
     */
    protected function findOrCreateUser($providerUser, string $provider): User
    {
        return DB::transaction(function () use ($providerUser, $provider) {
            // Check if social account exists
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_id', $providerUser->getId())
                ->first();
            
            if ($socialAccount) {
                // Update token and return existing user
                $socialAccount->update([
                    'token' => $providerUser->token,
                    'refresh_token' => $providerUser->refreshToken ?? null,
                    'expires_at' => $providerUser->expiresIn ? now()->addSeconds($providerUser->expiresIn) : null,
                    'avatar_url' => $providerUser->getAvatar(),
                ]);
                
                return $socialAccount->user;
            }
            
            // Check if user exists with this email
            $user = User::where('email', $providerUser->getEmail())->first();
            
            if ($user) {
                // Link social account to existing user
                $this->createSocialAccount($user, $providerUser, $provider);
                return $user;
            }
            
            // Create new user
            $user = User::create([
                'name' => $providerUser->getName() ?: 'User',
                'email' => $providerUser->getEmail(),
                'email_verified_at' => now(), // Auto-verify from provider
                'password' => Hash::make(Str::random(32)), // Random password
                'role' => 'client', // Default role
                'profile_picture' => $providerUser->getAvatar(),
            ]);
            
            // Create social account
            $this->createSocialAccount($user, $providerUser, $provider);
            
            return $user;
        });
    }

    /**
     * Create social account record
     */
    protected function createSocialAccount(User $user, $providerUser, string $provider): SocialAccount
    {
        return SocialAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $providerUser->getId(),
            'provider_email' => $providerUser->getEmail(),
            'avatar_url' => $providerUser->getAvatar(),
            'token' => $providerUser->token,
            'refresh_token' => $providerUser->refreshToken ?? null,
            'expires_at' => $providerUser->expiresIn ? now()->addSeconds($providerUser->expiresIn) : null,
        ]);
    }

    /**
     * Link social account to existing authenticated user
     */
    public function linkAccount(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'provider' => 'required|in:google,facebook,apple',
                'provider_id' => 'required|string',
                'provider_email' => 'nullable|email',
                'avatar_url' => 'nullable|url',
                'token' => 'required|string',
            ]);
            
            $user = $request->user();
            
            // Check if social account already linked
            $existing = SocialAccount::where('provider', $request->provider)
                ->where('provider_id', $request->provider_id)
                ->first();
            
            if ($existing && $existing->user_id !== $user->id) {
                return $this->validationError(
                    ['social_account' => 'This social account is already linked to another user'],
                    'This social account is already linked to another user'
                );
            }
            
            // Create or update social account
            SocialAccount::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => $request->provider,
                ],
                [
                    'provider_id' => $request->provider_id,
                    'provider_email' => $request->provider_email,
                    'avatar_url' => $request->avatar_url,
                    'token' => $request->token,
                ]
            );
            
            return $this->success([], ucfirst($request->provider) . ' account linked successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to link account', 500);
        }
    }

    /**
     * Unlink social account from user
     */
    public function unlinkAccount(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'provider' => 'required|in:google,facebook,apple',
            ]);
            
            $user = $request->user();
            
            // Check if user has password (can't unlink if no password set)
            if (!$user->password) {
                return $this->validationError(
                    ['password' => 'Cannot unlink. Please set a password first.'],
                    'Cannot unlink. Please set a password first.'
                );
            }
            
            $deleted = SocialAccount::where('user_id', $user->id)
                ->where('provider', $request->provider)
                ->delete();
            
            if (!$deleted) {
                return $this->notFound('Social account not found');
            }
            
            return $this->success([], ucfirst($request->provider) . ' account unlinked successfully');
            
        } catch (\Exception $e) {
            return $this->error('Failed to unlink account', 500);
        }
    }

    /**
     * Get user's linked social accounts
     */
    public function getLinkedAccounts(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $accounts = SocialAccount::where('user_id', $user->id)
            ->select('provider', 'provider_email', 'avatar_url', 'created_at')
            ->get();
        
        return $this->success(['accounts' => $accounts], 'Linked social accounts retrieved successfully');
    }

    /**
     * Validate provider
     */
    protected function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            throw new \InvalidArgumentException('Invalid provider');
        }
    }
}
