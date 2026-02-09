<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $photographerName = $this->booking->photographer->user->name;
        $eventDate = $this->booking->event_date->format('F j, Y');

        return (new MailMessage)
            ->subject("How was your photoshoot with {$photographerName}?")
            ->greeting("Hi {$notifiable->name},")
            ->line("We hope your photoshoot on {$eventDate} with {$photographerName} went well!")
            ->line("Your feedback helps other clients make informed decisions and helps photographers improve their services.")
            ->line("Please take a moment to share your experience:")
            ->action('Leave a Review', url('/photographers/' . $this->booking->photographer->slug . '/review?booking=' . $this->booking->id))
            ->line("Thank you for choosing Photographar!");
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'type' => 'review_request',
            'title' => 'Review Your Experience',
            'message' => "How was your photoshoot with {$this->booking->photographer->user->name}?",
            'photographer_id' => $this->booking->photographer_id,
            'photographer_name' => $this->booking->photographer->user->name,
            'action_url' => '/photographers/' . $this->booking->photographer->slug . '/review?booking=' . $this->booking->id,
        ];
    }
}
