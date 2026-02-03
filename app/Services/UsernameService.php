<?php

namespace App\Services;

use App\Models\User;
use App\Models\UsernameHistory;
use Illuminate\Support\Str;

class UsernameService
{
    /**
     * Reserved system usernames that cannot be used
     */
    protected const RESERVED_USERNAMES = [
        'admin',
        'login',
        'register',
        'logout',
        'events',
        'competitions',
        'photographers',
        'settings',
        'api',
        'profile',
        'dashboard',
        'portfolio',
        'bookings',
        'messages',
        'notifications',
        'payments',
        'reviews',
        'support',
        'help',
        'about',
        'contact',
        'terms',
        'privacy',
        'sitemap',
        'robots',
        'favicon',
        'admin-dashboard',
        'super-admin',
    ];

    /**
     * Generate a unique, URL-safe username
     */
    public function generateUsername(string $name): string
    {
        // Convert name to slug format
        $baseUsername = $this->slugify($name);
        $username = $baseUsername;
        $counter = 1;

        // Check if username exists or is reserved
        while ($this->isUsernameTaken($username) || $this->isReservedUsername($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Convert string to URL-safe slug
     */
    public function slugify(string $text): string
    {
        // Convert to lowercase
        $text = strtolower($text);

        // Replace spaces and hyphens with underscores or dots
        $text = preg_replace('/[\s\-]+/', '_', $text);

        // Keep only alphanumeric, underscore, and dot
        $text = preg_replace('/[^a-z0-9._]/i', '', $text);

        // Remove consecutive underscores/dots
        $text = preg_replace('/[_.]{2,}/', '_', $text);

        // Trim underscores/dots from start/end
        $text = trim($text, '_.');

        // Limit to 30 characters
        $text = substr($text, 0, 30);

        return $text ?: 'user';
    }

    /**
     * Check if username is available (not taken and not reserved)
     */
    public function isAvailable(string $username, ?int $exceptUserId = null): bool
    {
        if ($this->isReservedUsername($username)) {
            return false;
        }

        $query = User::where('username', $username);

        if ($exceptUserId) {
            $query->where('id', '!=', $exceptUserId);
        }

        return !$query->exists();
    }

    /**
     * Check if username is reserved
     */
    public function isReservedUsername(string $username): bool
    {
        return in_array(strtolower($username), array_map('strtolower', self::RESERVED_USERNAMES));
    }

    /**
     * Check if username is taken
     */
    public function isUsernameTaken(string $username): bool
    {
        return User::where('username', $username)->exists();
    }

    /**
     * Update user username and track history
     */
    public function updateUsername(User $user, string $newUsername): bool
    {
        // Validate new username
        if (!$this->isAvailable($newUsername, $user->id)) {
            return false;
        }

        // Record old username in history
        if ($user->username) {
            UsernameHistory::create([
                'user_id' => $user->id,
                'old_username' => $user->username,
                'new_username' => $newUsername,
            ]);
        }

        // Update username
        $user->update(['username' => $newUsername]);

        return true;
    }

    /**
     * Get user by username (handle history redirects)
     */
    public function findByUsername(string $username): ?User
    {
        // Try direct match first
        $user = User::where('username', $username)->first();

        if ($user) {
            return $user;
        }

        // Try username history for 301 redirects
        $history = UsernameHistory::where('old_username', $username)->latest()->first();

        return $history?->user;
    }

    /**
     * Get profile URL for user
     */
    public function getProfileUrl(User $user): string
    {
        $username = $user->username ?? $user->id;
        return route('photographer.profile.public', ['username' => $username]);
    }
}
