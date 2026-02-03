<?php

namespace App\Mail;

use App\Models\CompetitionSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompetitionWinnerMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public CompetitionSubmission $submission) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $awardType = match($this->submission->rank) {
            1 => '🥇 1st Place Winner',
            2 => '🥈 2nd Place Winner',
            3 => '🥉 3rd Place Winner',
            default => 'Honorable Mention',
        };

        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'noreply@photographar.com'), 'Photographar'),
            subject: 'Congratulations! You are a ' . $awardType . ' - ' . $this->submission->competition->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.competition-winner',
            with: [
                'submission' => $this->submission,
                'photographer' => $this->submission->photographer,
                'competition' => $this->submission->competition,
                'rank' => $this->submission->rank,
                'awardType' => $this->submission->award_type,
                'prizeAmount' => $this->submission->prize_amount,
                'certificateUrl' => url('/api/v1/certificates/' . $this->submission->certificate_id . '/download'),
                'leaderboardUrl' => url('/competitions/' . $this->submission->competition->slug . '/winners'),
            ],
        );
    }
}
