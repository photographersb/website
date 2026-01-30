<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $oldStatus,
        public string $newStatus
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking->load(['client', 'photographer.user', 'package']);
        
        $statusMessages = [
            'confirmed' => [
                'subject' => 'Booking Confirmed',
                'greeting' => 'Great News!',
                'message' => 'Your booking has been confirmed by the photographer.',
                'action' => 'Complete Payment',
            ],
            'rejected' => [
                'subject' => 'Booking Declined',
                'greeting' => 'Booking Update',
                'message' => 'Unfortunately, the photographer is not available for your requested date.',
                'action' => 'Browse Other Photographers',
            ],
            'completed' => [
                'subject' => 'Service Completed',
                'greeting' => 'Service Complete!',
                'message' => 'The photographer has marked your booking as completed. We hope you had a great experience!',
                'action' => 'Write a Review',
            ],
            'cancelled' => [
                'subject' => 'Booking Cancelled',
                'greeting' => 'Booking Cancelled',
                'message' => 'Your booking has been cancelled.',
                'action' => 'View Bookings',
            ],
        ];

        $statusInfo = $statusMessages[$this->newStatus] ?? [
            'subject' => 'Booking Status Updated',
            'greeting' => 'Update',
            'message' => 'Your booking status has been updated.',
            'action' => 'View Booking',
        ];

        $mail = (new MailMessage)
            ->subject($statusInfo['subject'] . ' - Photographar')
            ->greeting($statusInfo['greeting'])
            ->line('Hello ' . $booking->client->name . '!')
            ->line($statusInfo['message'])
            ->line('**Photographer:** ' . $booking->photographer->user->name)
            ->line('**Event Date:** ' . $booking->event_date->format('F j, Y'))
            ->line('**Location:** ' . $booking->location);

        if ($this->newStatus === 'confirmed') {
            $mail->line('**Next Step:** Please complete the payment to finalize your booking.')
                 ->action('Pay Now', url('/payment/' . $booking->id));
        } elseif ($this->newStatus === 'completed') {
            $mail->line('We would love to hear about your experience!')
                 ->action('Write Review', url('/review/' . $booking->photographer_id));
        } else {
            $mail->action($statusInfo['action'], url('/bookings'));
        }

        return $mail->line('Thank you for using Photographar!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'photographer_name' => $this->booking->photographer->user->name,
            'event_date' => $this->booking->event_date,
        ];
    }
}
