<?php

namespace App\Console\Commands;

use App\Models\FeaturedPhotographer;
use Illuminate\Console\Command;

class SendRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'featured:send-renewal-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send renewal reminders 3 days before featured listing expires';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for upcoming featured photographer expirations...');

        try {
            // Find all active featured photographers expiring in 3 days
            $expiringIn3Days = FeaturedPhotographer::where('active', true)
                ->whereBetween('end_date', [
                    now()->addDays(3)->startOfDay(),
                    now()->addDays(3)->endOfDay(),
                ])
                ->get();

            if ($expiringIn3Days->isEmpty()) {
                $this->info('No featured photographers expiring in 3 days.');
                return Command::SUCCESS;
            }

            $count = 0;
            foreach ($expiringIn3Days as $featured) {
                $this->sendRenewalReminder($featured);
                $count++;
                $this->line("✓ Reminder sent to: {$featured->photographer->name}");
            }

            $this->info("\n✅ Successfully sent {$count} renewal reminder(s).");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error sending renewal reminders: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Send renewal reminder to photographer
     */
    private function sendRenewalReminder(FeaturedPhotographer $featured)
    {
        try {
            $photographer = $featured->photographer;

            $photographer->user->notify(
                new \App\Notifications\RenewalReminderNotification($featured)
            );
        } catch (\Exception $e) {
            $this->warn("Could not notify photographer {$featured->photographer->name}: {$e->getMessage()}");
        }
    }
}
