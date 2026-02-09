<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\BookingReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for upcoming bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for bookings requiring reminders...');

        // Get confirmed bookings happening within next 24-48 hours
        $upcomingBookings = Booking::with(['client', 'photographer.user'])
            ->where('status', 'confirmed')
            ->whereBetween('event_date', [
                Carbon::now()->addHours(24),
                Carbon::now()->addHours(48)
            ])
            ->whereNull('reminder_sent_at')
            ->get();

        $count = 0;

        foreach ($upcomingBookings as $booking) {
            try {
                // Send to client
                if ($booking->client) {
                    $booking->client->notify(new BookingReminderNotification($booking, 'client'));
                }

                // Send to photographer
                if ($booking->photographer && $booking->photographer->user) {
                    $booking->photographer->user->notify(new BookingReminderNotification($booking, 'photographer'));
                }

                // Mark as reminded
                $booking->update(['reminder_sent_at' => now()]);
                
                $count++;
                $this->info("Sent reminders for booking #{$booking->id}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for booking #{$booking->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$count} booking reminders.");

        return 0;
    }
}
