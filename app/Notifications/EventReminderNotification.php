<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Event $event, public int $daysUntilEvent)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->event->load(['organizer.user', 'city']);
        $dateFormatted = $event->event_date->format('l, F j, Y');
        $timeFormatted = $event->start_time ? $event->start_time->format('g:i A') : 'TBA';

        $reminderText = match($this->daysUntilEvent) {
            1 => 'tomorrow',
            7 => 'in 7 days',
            default => "in {$this->daysUntilEvent} days",
        };

        return (new MailMessage)
            ->subject("Reminder: {$event->title} is {$reminderText}! 📅")
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("This is a reminder that **{$event->title}** is happening {$reminderText}.")
            ->line('')
            ->line('**Event Details:**')
            ->line('📅 Date: ' . $dateFormatted)
            ->line('🕐 Time: ' . $timeFormatted)
            ->line('📍 Location: ' . $event->location)
            ->line('🏙️ City: ' . ($event->city->name ?? 'N/A'))
            ->line('👤 Organizer: ' . $event->organizer->user->name)
            ->line('')
            ->line('**Event Type:** ' . ucfirst($event->event_type))
            ->action('View Event Details', url('/events/' . $event->slug))
            ->line('We look forward to seeing you there!')
            ->salutation('Best regards, The Photographar Team');
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
