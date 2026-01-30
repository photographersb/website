<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Transaction $transaction, public string $recipientRole)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $transaction = $this->transaction->load(['booking.client', 'booking.photographer.user', 'booking.package']);
        $booking = $transaction->booking;

        $paymentMethods = [
            'card' => 'Credit/Debit Card (SSLCommerz)',
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'bank_transfer' => 'Bank Transfer',
        ];

        if ($this->recipientRole === 'client') {
            return (new MailMessage)
                ->subject('Payment Confirmation - Photographar')
                ->greeting('Hello ' . $booking->client->name . '!')
                ->line('Your payment has been received successfully!')
                ->line('**Transaction ID:** ' . $transaction->reference_id)
                ->line('**Amount Paid:** ৳' . number_format((float) $transaction->amount, 2))
                ->line('**Payment Method:** ' . ($paymentMethods[$transaction->payment_method] ?? $transaction->payment_method))
                ->line('**Date:** ' . $transaction->created_at->format('F j, Y g:i A'))
                ->line('---')
                ->line('**Booking Details:**')
                ->line('**Photographer:** ' . $booking->photographer->user->name)
                ->line('**Event Date:** ' . $booking->event_date->format('F j, Y'))
                ->line('**Location:** ' . $booking->location)
                ->line('**Package:** ' . ($booking->package->name ?? 'Custom'))
                ->line('---')
                ->line('**Remaining Balance:** ৳' . number_format($booking->remaining_amount ?? 0))
                ->line('The remaining amount will be paid after the service is completed.')
                ->action('View Receipt', url('/transactions'))
                ->line('Thank you for your payment!');
        }

        return (new MailMessage)
            ->subject('Payment Received for Booking - Photographar')
            ->greeting('Hello ' . $booking->photographer->user->name . '!')
            ->line('You have received a payment for a booking.')
            ->line('**Transaction ID:** ' . $transaction->reference_id)
            ->line('**Amount:** ৳' . number_format((float) $transaction->amount, 2))
            ->line('**Payment Method:** ' . ($paymentMethods[$transaction->payment_method] ?? $transaction->payment_method))
            ->line('---')
            ->line('**Booking Details:**')
            ->line('**Client:** ' . $booking->client->name)
            ->line('**Event Date:** ' . $booking->event_date->format('F j, Y'))
            ->line('**Location:** ' . $booking->location)
            ->line('**Package:** ' . ($booking->package->name ?? 'Custom'))
            ->action('View Booking', url('/dashboard'))
            ->line('Your payout will be processed according to our payment schedule.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'reference_id' => $this->transaction->reference_id,
            'amount' => $this->transaction->amount,
            'payment_method' => $this->transaction->payment_method,
            'booking_id' => $this->transaction->booking->id,
        ];
    }
}
