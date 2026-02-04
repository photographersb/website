<?php

namespace App\Notifications;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected BookingRequest $booking)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $eventDate = $this->booking->event_date?->format('d M Y');
        $photographerName = $this->booking->photographer->full_name ?? $this->booking->photographer->name;

        return (new MailMessage)
            ->subject('Booking Completed - Thank You!')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your booking with {$photographerName} on {$eventDate} has been marked as completed.")
            ->line("Booking Code: {$this->booking->booking_code}")
            ->line('We hope you had a great experience!')
            ->action('View Booking', route('booking.show', $this->booking))
            ->line('Please leave a review to help other clients find great photographers.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'photographer_name' => $this->booking->photographer->full_name ?? $this->booking->photographer->name,
            'event_date' => $this->booking->event_date?->format('d M Y'),
            'message' => 'Booking completed successfully!',
            'action_url' => route('booking.show', $this->booking),
        ];
    }
}
