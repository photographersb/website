<?php

namespace App\Notifications;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingAccepted extends Notification implements ShouldQueue
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
            ->subject('Booking Accepted!')
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("{$photographerName} has accepted your booking for {$eventDate}.")
            ->line("Booking Code: {$this->booking->booking_code}")
            ->line("Budget: ৳{$this->booking->budget_min} - ৳{$this->booking->budget_max}")
            ->action('View Booking Details', route('booking.show', $this->booking))
            ->line('You can now communicate with the photographer through the booking page.');
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
            'message' => 'Your booking has been accepted!',
            'action_url' => route('booking.show', $this->booking),
        ];
    }
}
