<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders {--days=1 : Days before event to send reminder}';

    protected $description = 'Send event reminder notifications to users who RSVP\'d';

    public function handle()
    {
        $days = (int) $this->option('days');
        $targetDate = now()->addDays($days)->startOfDay();
        
        $this->info("Sending reminders for events on {$targetDate->format('Y-m-d')}...");

        // Get upcoming events
        $events = Event::where('status', 'published')
            ->whereDate('event_date', $targetDate->format('Y-m-d'))
            ->with(['rsvps' => function($q) {
                $q->where('rsvp_status', 'going');
            }])
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events found for this date.');
            return 0;
        }

        $totalReminders = 0;

        foreach ($events as $event) {
            $rsvpCount = $event->rsvps->count();
            
            if ($rsvpCount === 0) {
                $this->line("  - {$event->title}: No RSVPs");
                continue;
            }

            foreach ($event->rsvps as $rsvp) {
                try {
                    $rsvp->user->notify(new EventReminderNotification($event, $days));
                    $totalReminders++;
                } catch (\Exception $e) {
                    Log::error("Failed to send reminder for event {$event->id} to user {$rsvp->user_id}: {$e->getMessage()}");
                    $this->error("  - Failed for {$rsvp->user->name}");
                }
            }

            $this->info("  - {$event->title}: Sent {$rsvpCount} reminders");
        }

        $this->info("\n\u2713 Total reminders sent: {$totalReminders}");
        
        Log::info("Event reminders sent", [
            'days_before' => $days,
            'events_count' => $events->count(),
            'reminders_sent' => $totalReminders,
        ]);

        return 0;
    }
}
