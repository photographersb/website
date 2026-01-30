<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $photographerName = $this->quote->photographer->user->name;

        return (new MailMessage)
            ->subject('New Quote Received from ' . $photographerName)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($photographerName . ' has sent you a custom quote.')
            ->line('Amount: ৳' . number_format($this->quote->amount, 2))
            ->line('Valid until: ' . $this->quote->expires_at->format('M d, Y'))
            ->action('View Quote', url('/quotes/' . $this->quote->id))
            ->line('Review the quote details and book if you\'re interested!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'quote_received',
            'quote_id' => $this->quote->id,
            'photographer_id' => $this->quote->photographer_id,
            'photographer_name' => $this->quote->photographer->user->name,
            'amount' => $this->quote->amount,
            'message' => $this->quote->photographer->user->name . ' sent you a quote for ৳' . number_format($this->quote->amount, 0),
        ];
    }
}
