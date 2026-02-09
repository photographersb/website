<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\RequestReviewNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RequestBookingReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:request-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send review request notifications for recently completed bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for completed bookings needing review requests...');

        // Get bookings completed 1-3 days ago that haven't been reviewed yet
        $completedBookings = Booking::with(['client', 'photographer.user'])
            ->where('status', 'completed')
            ->whereBetween('completed_at', [
                Carbon::now()->subDays(3),
                Carbon::now()->subDays(1)
            ])
            ->whereNull('review_requested_at')
            ->where('allow_review', false)
            ->get();

        $count = 0;

        foreach ($completedBookings as $booking) {
            try {
                // Send to client only (they leave reviews for photographers)
                if ($booking->client) {
                    $booking->client->notify(new RequestReviewNotification($booking));
                }

                // Mark as review requested and enable review
                $booking->update([
                    'review_requested_at' => now(),
                    'allow_review' => true,
                ]);
                
                $count++;
                $this->info("Sent review request for booking #{$booking->id}");
            } catch (\Exception $e) {
                $this->error("Failed to send review request for booking #{$booking->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$count} review requests.");

        return 0;
    }
}
