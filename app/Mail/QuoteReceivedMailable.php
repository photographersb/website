<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteReceivedMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Quote $quote) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'noreply@photographar.com'), 'Photographar'),
            subject: 'New Quote from ' . ($this->quote->photographer->name ?? 'Photographer'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.quote-received',
            with: [
                'quote' => $this->quote,
                'photographer' => $this->quote->photographer,
                'client' => $this->quote->inquiry->client,
                'quoteUrl' => url('/quotes/' . $this->quote->id),
                'expiresAt' => $this->quote->valid_until,
            ],
        );
    }
}
