<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Booking;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\Quote;
use App\Models\Event;
use App\Notifications\BookingCreated;
use App\Notifications\BookingStatusUpdated;
use App\Notifications\CompetitionDeadlineReminder;
use App\Notifications\EventReminderNotification;
use App\Notifications\PaymentReceived;
use App\Notifications\QuoteReceived;
use App\Notifications\ReviewReplyReceived;
use App\Notifications\ReviewRequest;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🔔 Seeding notification templates and sending test notifications...\n";

        // Get test users
        $adminUser = User::where('role', 'admin')->first();
        $photographers = User::where('role', 'photographer')->limit(3)->get();
        $clients = User::where('role', 'client')->limit(3)->get();

        if (!$adminUser || $photographers->isEmpty() || $clients->isEmpty()) {
            echo "⚠️  Skipping notification seeding - not enough test users\n";
            return;
        }

        try {
            DB::beginTransaction();

            // 1. Send Welcome Notifications
            echo "📧 Sending welcome notifications...\n";
            foreach ($photographers as $photographer) {
                $photographer->notify(new WelcomeNotification($photographer));
            }
            foreach ($clients as $client) {
                $client->notify(new WelcomeNotification($client));
            }
            echo "✓ Sent welcome notifications to " . ($photographers->count() + $clients->count()) . " users\n";

            // 2. Create test bookings and send notifications
            echo "📅 Creating test bookings and notifications...\n";
            $bookingsCreated = 0;
            foreach ($clients->take(2) as $client) {
                foreach ($photographers->take(2) as $photographer) {
                    $booking = Booking::create([
                        'client_id' => $client->id,
                        'photographer_id' => $photographer->id,
                        'event_date' => now()->addDays(rand(7, 30)),
                        'total_amount' => rand(5000, 50000),
                        'status' => 'pending_payment',
                        'payment_status' => 'pending',
                    ]);

                    $photographer->user->notify(new BookingCreated($booking));
                    $bookingsCreated++;
                }
            }
            echo "✓ Created $bookingsCreated test bookings and sent notifications\n";

            // 3. Send booking status notifications
            echo "📊 Sending booking status updates...\n";
            $updatedBookings = Booking::limit(2)->get();
            foreach ($updatedBookings as $booking) {
                $booking->client->notify(new BookingStatusUpdated($booking, 'pending_payment', 'confirmed'));
            }
            echo "✓ Sent " . $updatedBookings->count() . " booking status update notifications\n";

            // 4. Send quote notifications
            echo "💬 Sending quote notifications...\n";
            $quotesCreated = 0;
            foreach ($clients->take(2) as $client) {
                foreach ($photographers->take(2) as $photographer) {
                    $quote = Quote::create([
                        'photographer_id' => $photographer->id,
                        'inquiry_id' => 1, // May not exist, but for seeding purposes
                        'amount' => rand(3000, 30000),
                        'valid_until' => now()->addDays(7),
                        'status' => 'pending',
                    ]);

                    $client->notify(new QuoteReceived($quote));
                    $quotesCreated++;
                }
            }
            echo "✓ Sent $quotesCreated quote notifications\n";

            // 5. Send competition reminders
            echo "🏆 Sending competition deadline reminders...\n";
            $competitions = Competition::where('submission_deadline', '>', now())
                ->where('submission_deadline', '<=', now()->addDays(3))
                ->limit(2)
                ->get();

            $remindersSent = 0;
            foreach ($competitions as $competition) {
                foreach ($photographers as $photographer) {
                    if ($photographer->user) {
                        $photographer->user->notify(new CompetitionDeadlineReminder($competition));
                        $remindersSent++;
                    }
                }
            }
            echo "✓ Sent $remindersSent competition deadline reminders\n";

            // 6. Send event reminders
            echo "📢 Sending event reminders...\n";
            $upcomingEvents = Event::where('event_date', '>', now())
                ->where('event_date', '<=', now()->addDays(3))
                ->limit(2)
                ->get();

            $eventRemindersSent = 0;
            foreach ($upcomingEvents as $event) {
                foreach ($photographers as $photographer) {
                    if ($photographer->user) {
                        $photographer->user->notify(new EventReminderNotification($event));
                        $eventRemindersSent++;
                    }
                }
            }
            echo "✓ Sent $eventRemindersSent event reminder notifications\n";

            // 7. Send review request notifications (on completed bookings)
            echo "📝 Sending review request notifications...\n";
            $completedBookings = Booking::where('status', 'completed')->limit(2)->get();
            foreach ($completedBookings as $booking) {
                if ($booking->client) {
                    $booking->client->notify(new ReviewRequest($booking));
                }
            }
            echo "✓ Sent " . $completedBookings->count() . " review request notifications\n";

            // 8. Seed notification preferences (if table exists)
            echo "⚙️  Creating notification preferences...\n";
            $this->createNotificationPreferences();

            DB::commit();

            echo "✅ Notification seeding completed successfully!\n\n";
            echo "Summary:\n";
            echo "  • Welcome notifications sent\n";
            echo "  • Test bookings created with notifications\n";
            echo "  • Booking status updates sent\n";
            echo "  • Quote notifications sent\n";
            echo "  • Competition reminders sent\n";
            echo "  • Event reminders sent\n";
            echo "  • Review requests sent\n";
            echo "  • Notification preferences created\n\n";

        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Error during notification seeding: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Create notification preferences for users
     */
    private function createNotificationPreferences(): void
    {
        $defaultPreferences = [
            'email_on_new_booking' => true,
            'email_on_new_quote' => true,
            'email_on_booking_status' => true,
            'email_on_review' => true,
            'email_on_competition_deadline' => true,
            'email_on_event_reminder' => true,
            'email_on_payment' => true,
            'sms_on_new_booking' => false,
            'sms_on_new_quote' => false,
            'sms_on_payment' => false,
            'push_on_new_booking' => true,
            'push_on_new_quote' => true,
            'push_on_booking_status' => true,
            'marketing_emails' => false,
            'weekly_digest' => true,
        ];

        // Store in user's preferences (assuming there's a preferences field or table)
        $users = User::limit(10)->get();

        foreach ($users as $user) {
            // If preferences column exists
            if ($user->hasCast('preferences')) {
                $user->update(['preferences' => $defaultPreferences]);
            }
        }
    }
}
