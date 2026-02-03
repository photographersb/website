<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ManageNotificationPreferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:preferences
                            {action=show : show|set|reset}
                            {user_id? : User ID}
                            {--key= : Preference key}
                            {--value= : Preference value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage user notification preferences';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $userId = $this->argument('user_id');

        switch ($action) {
            case 'show':
                return $this->showPreferences($userId);
            case 'set':
                return $this->setPreference($userId);
            case 'reset':
                return $this->resetPreferences($userId);
            default:
                $this->error('Unknown action: ' . $action);
                return 1;
        }
    }

    /**
     * Show notification preferences
     */
    private function showPreferences(?int $userId = null): int
    {
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User #$userId not found");
                return 1;
            }

            $this->displayUserPreferences($user);
            return 0;
        }

        // Show all users
        $users = User::limit(10)->get();

        if ($users->isEmpty()) {
            $this->warn('No users found');
            return 0;
        }

        foreach ($users as $user) {
            $this->displayUserPreferences($user);
            $this->line('');
        }

        return 0;
    }

    /**
     * Display preferences for a user
     */
    private function displayUserPreferences(User $user): void
    {
        $this->info("User: {$user->name} (#{$user->id})");
        $this->line("Email: {$user->email}");

        $preferences = [
            'booking_created' => ['mail' => true, 'sms' => false, 'database' => true],
            'booking_status_updated' => ['mail' => true, 'sms' => false, 'database' => true],
            'quote_received' => ['mail' => true, 'sms' => false, 'database' => true],
            'payment_received' => ['mail' => true, 'sms' => false, 'database' => true],
            'competition_deadline_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
            'event_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
            'review_request' => ['mail' => true, 'sms' => false, 'database' => true],
            'marketing_emails' => false,
            'weekly_digest' => true,
        ];

        $this->table(['Notification Type', 'Email', 'SMS', 'Database'], array_map(function ($key, $prefs) {
            if (is_array($prefs)) {
                return [
                    $key,
                    $prefs['mail'] ? '✓' : '✗',
                    $prefs['sms'] ? '✓' : '✗',
                    $prefs['database'] ? '✓' : '✗',
                ];
            }
            return [$key, $prefs ? '✓' : '✗', '-', '-'];
        }, array_keys($preferences), array_values($preferences)));
    }

    /**
     * Set a notification preference
     */
    private function setPreference(?int $userId = null): int
    {
        if (!$userId) {
            $this->error('User ID required for set action');
            return 1;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("User #$userId not found");
            return 1;
        }

        $key = $this->option('key');
        $value = $this->option('value');

        if (!$key || !$value) {
            $this->error('--key and --value options required');
            return 1;
        }

        // Parse value (true/false)
        $boolValue = in_array(strtolower($value), ['true', '1', 'yes', 'on']);

        // Get current preferences (if any)
        $preferences = $user->preferences ?? [];

        // Set new value
        $keys = explode('.', $key);
        $target = &$preferences;

        foreach ($keys as $k) {
            if (!isset($target[$k])) {
                $target[$k] = [];
            }
            $target = &$target[$k];
        }

        $target = $boolValue;

        $user->update(['preferences' => $preferences]);

        $this->info("Updated preference: $key = " . ($boolValue ? 'true' : 'false'));

        return 0;
    }

    /**
     * Reset preferences to defaults
     */
    private function resetPreferences(?int $userId = null): int
    {
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User #$userId not found");
                return 1;
            }

            $defaultPreferences = [
                'booking_created' => ['mail' => true, 'sms' => false, 'database' => true],
                'booking_status_updated' => ['mail' => true, 'sms' => false, 'database' => true],
                'quote_received' => ['mail' => true, 'sms' => false, 'database' => true],
                'payment_received' => ['mail' => true, 'sms' => false, 'database' => true],
                'competition_deadline_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
                'event_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
                'review_request' => ['mail' => true, 'sms' => false, 'database' => true],
                'marketing_emails' => false,
                'weekly_digest' => true,
            ];

            $user->update(['preferences' => $defaultPreferences]);

            $this->info("Reset preferences for user: {$user->name}");
            return 0;
        }

        // Reset all users
        $count = User::update(['preferences' => json_encode([])]);
        $this->info("Reset preferences for $count users");

        return 0;
    }
}
