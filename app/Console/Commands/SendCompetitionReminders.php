<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Models\User;
use App\Notifications\CompetitionDeadlineReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendCompetitionReminders extends Command
{
    protected $signature = 'competitions:send-reminders';

    protected $description = 'Send deadline reminders for active competitions';

    public function handle()
    {
        $this->info("Checking competitions for deadline reminders...");

        // Get active competitions with approaching deadlines
        $competitions = Competition::where('status', 'active')
            ->where('submission_deadline', '>', now())
            ->where('submission_deadline', '<=', now()->addDays(3))
            ->get();

        if ($competitions->isEmpty()) {
            $this->info('No competitions with approaching deadlines.');
            return 0;
        }

        $totalReminders = 0;

        foreach ($competitions as $competition) {
            $hoursRemaining = now()->diffInHours($competition->submission_deadline);
            
            // Send reminders at specific intervals: 72h, 48h, 24h, 12h, 6h
            if (!$this->shouldSendReminder($hoursRemaining)) {
                continue;
            }

            // Send to all photographers
            $photographers = User::where('role', 'photographer')->get();
            
            $sentCount = 0;
            foreach ($photographers as $photographer) {
                try {
                    $photographer->notify(new CompetitionDeadlineReminder($competition, $hoursRemaining));
                    $sentCount++;
                    $totalReminders++;
                } catch (\Exception $e) {
                    Log::error("Failed to send competition reminder {$competition->id} to user {$photographer->id}: {$e->getMessage()}");
                }
            }

            $this->info("  - {$competition->title} ({$hoursRemaining}h remaining): Sent {$sentCount} reminders");
        }

        $this->info("\n✓ Total reminders sent: {$totalReminders}");
        
        Log::info("Competition reminders sent", [
            'competitions_count' => $competitions->count(),
            'reminders_sent' => $totalReminders,
        ]);

        return 0;
    }

    private function shouldSendReminder(int $hours): bool
    {
        $reminderPoints = [72, 48, 24, 12, 6, 3, 1];
        
        foreach ($reminderPoints as $point) {
            if ($hours >= ($point - 1) && $hours <= ($point + 1)) {
                return true;
            }
        }
        
        return false;
    }
}
