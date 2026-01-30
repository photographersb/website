<?php

namespace App\Notifications;

use App\Models\Competition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompetitionDeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Competition $competition, public int $hoursRemaining)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $competition = $this->competition;
        $deadlineFormatted = $competition->submission_deadline->format('F j, Y \\a\\t g:i A');
        
        $urgency = match(true) {
            $this->hoursRemaining <= 24 => '⏰ FINAL DAY',
            $this->hoursRemaining <= 72 => '⚠️ Last 3 Days',
            default => 'Deadline Approaching',
        };

        $message = (new MailMessage)
            ->subject("{$urgency}: {$competition->title} - Submit Now! 🏆")
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("**{$urgency}** - The submission deadline for **{$competition->title}** is approaching!")
            ->line('')
            ->line('⏰ **Deadline:** ' . $deadlineFormatted)
            ->line('⏳ **Time Remaining:** ' . $this->formatTimeRemaining($this->hoursRemaining))
            ->line('')
            ->line('**Competition Details:**')
            ->line('🏆 Theme: ' . ($competition->theme ?? 'Open Theme'))
            ->line('💰 Total Prize Pool: ৳' . number_format($competition->total_prize_pool ?? 0));

        if ($competition->max_submissions_per_user > 1) {
            $message->line('📸 Submissions Allowed: ' . $competition->max_submissions_per_user);
        }

        return $message->line('')
            ->action('Submit Your Photos Now', url('/competitions/' . $competition->slug))
            ->line('Don\'t miss your chance to win amazing prizes!')
            ->salutation('Good luck, The Photographar Team');
    }

    private function formatTimeRemaining(int $hours): string
    {
        if ($hours < 24) {
            return $hours . ' hours';
        }
        
        $days = floor($hours / 24);
        $remainingHours = $hours % 24;
        
        if ($remainingHours === 0) {
            return $days . ' day' . ($days > 1 ? 's' : '');
        }
        
        return $days . ' day' . ($days > 1 ? 's' : '') . ' and ' . $remainingHours . ' hours';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
