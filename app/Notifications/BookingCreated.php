<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Booking $booking, public string $recipientRole)
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking->load(['client', 'photographer.user', 'package']);
        
        if ($this->recipientRole === 'client') {
            return (new MailMessage)
                ->subject('Booking Request Submitted - Photographar')
                ->greeting('Hello ' . $booking->client->name . '!')
                ->line('Your booking request has been successfully submitted.')
                ->line('**Photographer:** ' . $booking->photographer->user->name)
                ->line('**Event Date:** ' . $booking->event_date->format('F j, Y'))
                ->line('**Location:** ' . $booking->location)
                ->line('**Package:** ' . ($booking->package->name ?? 'Custom'))
                ->line('**Price:** ৳' . number_format($booking->package->price ?? 0))
                ->line('The photographer will review your request and respond shortly.')
                ->action('View Booking', url('/bookings'))
                ->line('Thank you for choosing Photographar - Across Somogro Bangladesh!');
        }
        
        // For photographer
        return (new MailMessage)
            ->subject('New Booking Request - Photographar')
            ->greeting('Hello ' . $booking->photographer->user->name . '!')
            ->line('You have received a new booking request.')
            ->line('**Client:** ' . $booking->client->name)
            ->line('**Event Date:** ' . $booking->event_date->format('F j, Y'))
            ->line('**Location:** ' . $booking->location)
            ->line('**Package:** ' . ($booking->package->name ?? 'Custom'))
            ->line('**Message:** ' . $booking->message)
            ->action('View & Respond', url('/dashboard'))
            ->line('Please review and respond to this booking request.');
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
            'event_date' => $this->booking->event_date,
            'location' => $this->booking->location,
            'recipient_role' => $this->recipientRole,
        ];
    }
}
