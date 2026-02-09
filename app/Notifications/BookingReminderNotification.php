<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $recipientType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $recipientType)
    {
        $this->booking = $booking;
        $this->recipientType = $recipientType;
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
        $isClient = $this->recipientType === 'client';
        $otherParty = $isClient 
            ? $this->booking->photographer->user->name 
            : $this->booking->client->name;

        $eventDate = $this->booking->event_date->format('F j, Y');
        $eventTime = $this->booking->event_time 
            ? $this->booking->event_time->format('g:i A') 
            : 'TBD';

        $subject = $isClient
            ? "Reminder: Your photoshoot with {$otherParty} is tomorrow!"
            : "Reminder: Photoshoot with {$otherParty} is tomorrow!";

        $greeting = $isClient
            ? "Hi {$notifiable->name},"
            : "Hi {$notifiable->name},";

        $message = $isClient
            ? "This is a friendly reminder that your photoshoot with {$otherParty} is scheduled for tomorrow, {$eventDate} at {$eventTime}."
            : "This is a reminder that you have a photoshoot scheduled with {$otherParty} tomorrow, {$eventDate} at {$eventTime}.";

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->line("**Event Details:**")
            ->line("📅 Date: {$eventDate}")
            ->line("🕐 Time: {$eventTime}");

        if ($this->booking->venue_address) {
            $mailMessage->line("📍 Location: {$this->booking->venue_address}");
        }

        if ($this->booking->event_type) {
            $mailMessage->line("📷 Event Type: {$this->booking->event_type}");
        }

        $mailMessage->action('View Booking Details', url('/bookings/' . $this->booking->id));

        if ($isClient) {
            $mailMessage->line('Please arrive on time and bring any props or outfits as discussed.');
        } else {
            $mailMessage->line('Please ensure your equipment is ready and arrive on time.');
        }

        return $mailMessage->line('Thank you for using Photographar!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $isClient = $this->recipientType === 'client';
        $otherParty = $isClient 
            ? $this->booking->photographer->user->name 
            : $this->booking->client->name;

        return [
            'booking_id' => $this->booking->id,
            'type' => 'booking_reminder',
            'recipient_type' => $this->recipientType,
            'title' => 'Booking Reminder',
            'message' => $isClient
                ? "Your photoshoot with {$otherParty} is tomorrow!"
                : "Photoshoot with {$otherParty} is tomorrow!",
            'event_date' => $this->booking->event_date->toDateString(),
            'event_time' => $this->booking->event_time 
                ? $this->booking->event_time->format('H:i') 
                : null,
            'action_url' => '/bookings/' . $this->booking->id,
        ];
    }
}
