<?php

namespace App\Console\Commands;

use App\Models\FeaturedPhotographer;
use Illuminate\Console\Command;

class DeactivateExpiredFeaturedPhotographers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'featured:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically deactivate expired featured photographer listings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting deactivation of expired featured photographers...');

        try {
            // Find all active featured photographers whose end_date has passed
            $expired = FeaturedPhotographer::where('active', true)
                ->where('end_date', '<', now())
                ->get();

            if ($expired->isEmpty()) {
                $this->info('No expired featured photographers found.');
                return Command::SUCCESS;
            }

            $count = 0;
            foreach ($expired as $featured) {
                // Deactivate the featured photographer
                $featured->update(['active' => false]);

                // Send notification to photographer
                $this->notifyPhotographer($featured);

                $count++;
                $this->line("✓ Deactivated: {$featured->photographer->name} ({$featured->package_tier})");
            }

            $this->info("\n✅ Successfully deactivated {$count} expired featured photographer(s).");

            // Log the activity
            activity()
                ->causedBy(auth()->user() ?? null)
                ->log("Deactivated {$count} expired featured photographer listings via cron job");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error deactivating expired featured photographers: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Send notification to photographer about expiration
     */
    private function notifyPhotographer(FeaturedPhotographer $featured)
    {
        try {
            $photographer = $featured->photographer;

            // You can implement email notification here
            // Send::mail(new FeaturedListingExpiredNotification($photographer, $featured));

            // Or use in-app notification
            $photographer->user->notify(
                new \App\Notifications\FeaturedListingExpiredNotification($featured)
            );
        } catch (\Exception $e) {
            $this->warn("Could not notify photographer {$featured->photographer->name}: {$e->getMessage()}");
        }
    }
}
