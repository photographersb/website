<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n📋 Seeding notification templates and settings...\n";

        // Notification template configurations
        $templates = [
            [
                'key' => 'booking_created',
                'name' => 'New Booking Created',
                'description' => 'Sent when a new booking is created',
                'channels' => json_encode(['mail', 'database', 'sms']),
                'enabled' => true,
            ],
            [
                'key' => 'booking_status_updated',
                'name' => 'Booking Status Updated',
                'description' => 'Sent when booking status changes',
                'channels' => json_encode(['mail', 'database']),
                'enabled' => true,
            ],
            [
                'key' => 'quote_received',
                'name' => 'Quote Received',
                'description' => 'Sent when a photographer sends a quote',
                'channels' => json_encode(['mail', 'database']),
                'enabled' => true,
            ],
            [
                'key' => 'payment_received',
                'name' => 'Payment Received',
                'description' => 'Sent when payment is received',
                'channels' => json_encode(['mail', 'database']),
                'enabled' => true,
            ],
            [
                'key' => 'competition_deadline_reminder',
                'name' => 'Competition Deadline Reminder',
                'description' => 'Sent as reminder for competition deadlines',
                'channels' => json_encode(['mail', 'database']),
                'enabled' => true,
            ],
            [
                'key' => 'event_reminder',
                'name' => 'Event Reminder',
                'description' => 'Sent as reminder for upcoming events',
                'channels' => json_encode(['mail', 'database']),
                'enabled' => true,
            ],
            [
                'key' => 'review_request',
                'name' => 'Review Request',
                'description' => 'Sent to request review after booking completion',
                'channels' => json_encode(['mail', 'database']),
                'enabled' => true,
            ],
            [
                'key' => 'review_reply',
                'name' => 'Review Reply Received',
                'description' => 'Sent when photographer replies to review',
                'channels' => json_encode(['mail', 'database']),
                'enabled' => true,
            ],
            [
                'key' => 'welcome',
                'name' => 'Welcome Notification',
                'description' => 'Sent on user registration',
                'channels' => json_encode(['mail', 'database']),
                'enabled' => true,
            ],
            [
                'key' => 'email_verification',
                'name' => 'Email Verification',
                'description' => 'Sent to verify email address',
                'channels' => json_encode(['mail']),
                'enabled' => true,
            ],
        ];

        // Check if notification_settings table exists
        if (!DB::getSchemaBuilder()->hasTable('notification_settings')) {
            echo "⚠️  notification_settings table doesn't exist, creating template data structure...\n";
            $this->createTemplateData($templates);
            return;
        }

        // Insert templates
        foreach ($templates as $template) {
            DB::table('notification_settings')->updateOrInsert(
                ['key' => $template['key']],
                $template
            );
        }

        echo "✓ " . count($templates) . " notification templates seeded\n";

        // Create user notification preferences if preferences table exists
        if (DB::getSchemaBuilder()->hasTable('user_notification_preferences')) {
            $this->seedUserPreferences();
        }

        echo "✅ Notification templates seeding completed!\n\n";
    }

    /**
     * Seed user notification preferences
     */
    private function seedUserPreferences(): void
    {
        echo "📧 Seeding user notification preferences...\n";

        $users = \App\Models\User::limit(10)->get();

        $defaultPreferences = [
            'booking_created' => ['mail' => true, 'sms' => false, 'database' => true],
            'booking_status_updated' => ['mail' => true, 'sms' => false, 'database' => true],
            'quote_received' => ['mail' => true, 'sms' => false, 'database' => true],
            'payment_received' => ['mail' => true, 'sms' => false, 'database' => true],
            'competition_deadline_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
            'event_reminder' => ['mail' => true, 'sms' => false, 'database' => true],
            'review_request' => ['mail' => true, 'sms' => false, 'database' => true],
            'review_reply' => ['mail' => true, 'sms' => false, 'database' => true],
            'marketing_emails' => false,
            'weekly_digest' => true,
        ];

        foreach ($users as $user) {
            DB::table('user_notification_preferences')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'preferences' => json_encode($defaultPreferences),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        echo "✓ User preferences seeded for " . $users->count() . " users\n";
    }

    /**
     * Create template data structure for reference
     */
    private function createTemplateData(array $templates): void
    {
        echo "📄 Notification template configuration:\n";
        echo "────────────────────────────────────────────────────\n";

        foreach ($templates as $template) {
            $channels = json_decode($template['channels'], true);
            echo "• " . $template['name'] . "\n";
            echo "  Key: {$template['key']}\n";
            echo "  Channels: " . implode(', ', $channels) . "\n";
            echo "  Enabled: " . ($template['enabled'] ? 'Yes' : 'No') . "\n";
            echo "\n";
        }

        echo "Note: To enable these templates in database, create the following migration:\n";
        echo "  php artisan make:migration create_notification_settings_table\n\n";
    }
}
