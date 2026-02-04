<?php

namespace App\Notifications;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
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
        $otherParty = $this->booking->client_user_id === $notifiable->id
            ? $this->booking->photographer->full_name ?? $this->booking->photographer->name
            : $this->booking->client->full_name ?? $this->booking->client->name;
        $eventDate = $this->booking->event_date?->format('d M Y');

        return (new MailMessage)
            ->subject('Booking Cancelled')
            ->greeting("Hello {$notifiable->name},")
            ->line("The booking for {$eventDate} has been cancelled.")
            ->line("Booking Code: {$this->booking->booking_code}")
            ->line('If you have any questions, please contact us.')
            ->action('View Details', route('booking.show', $this->booking))
            ->line('Thank you for using Photographer SB.');
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
            'event_date' => $this->booking->event_date?->format('d M Y'),
            'message' => 'Booking has been cancelled',
            'action_url' => route('booking.show', $this->booking),
        ];
    }
}
