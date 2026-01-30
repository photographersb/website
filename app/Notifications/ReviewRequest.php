<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking->load(['client', 'photographer.user']);

        return (new MailMessage)
            ->subject('Share Your Experience - Photographar')
            ->greeting('Hello ' . $booking->client->name . '!')
            ->line('Thank you for choosing Photographar! We hope you had a wonderful experience.')
            ->line('We would love to hear about your experience with **' . $booking->photographer->user->name . '**.')
            ->line('Your feedback helps other clients make informed decisions and helps photographers improve their services.')
            ->line('**Event Date:** ' . $booking->event_date->format('F j, Y'))
            ->line('**Location:** ' . $booking->location)
            ->action('Write a Review', url('/review/' . $booking->photographer_id))
            ->line('Your review will be publicly visible on the photographer\'s profile.')
            ->line('Thank you for supporting our photography community!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'photographer_id' => $this->booking->photographer_id,
            'photographer_name' => $this->booking->photographer->user->name,
        ];
    }
}
