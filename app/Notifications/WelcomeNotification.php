<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $userType)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Welcome to Photographar! 🎉')
            ->greeting('Welcome ' . $notifiable->name . '!')
            ->line('Thank you for joining Photographar, Bangladesh\'s premier photography marketplace.');

        if ($this->userType === 'photographer') {
            $message->line('As a photographer, you can now:')
                ->line('✓ Create your professional profile')
                ->line('✓ Showcase your portfolio')
                ->line('✓ Receive booking inquiries')
                ->line('✓ Participate in competitions')
                ->line('✓ Organize photography events')
                ->action('Complete Your Profile', url('/photographer/profile'))
                ->line('Complete your profile to start receiving bookings!');
        } else {
            $message->line('As a client, you can now:')
                ->line('✓ Browse top photographers')
                ->line('✓ Book photography services')
                ->line('✓ Attend photography events')
                ->line('✓ Vote in competitions')
                ->action('Explore Photographers', url('/photographers'))
                ->line('Start exploring and find the perfect photographer for your next event!');
        }

        return $message->line('If you have any questions, feel free to contact our support team.')
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
