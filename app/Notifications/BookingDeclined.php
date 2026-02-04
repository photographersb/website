<?php

namespace App\Notifications;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingDeclined extends Notification implements ShouldQueue
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
        $photographerName = $this->booking->photographer->full_name ?? $this->booking->photographer->name;
        $eventDate = $this->booking->event_date?->format('d M Y');

        return (new MailMessage)
            ->subject('Booking Declined')
            ->greeting("Hello {$notifiable->name},")
            ->line("{$photographerName} has declined your booking request for {$eventDate}.")
            ->line("Booking Code: {$this->booking->booking_code}")
            ->line('You can request another photographer or try again later.')
            ->action('Browse Other Photographers', route('homepage') ?? url('/'))
            ->line('We hope to see you book another photographer soon!');
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
            'message' => 'Your booking request was declined',
            'action_url' => route('booking.show', $this->booking),
        ];
    }
}
