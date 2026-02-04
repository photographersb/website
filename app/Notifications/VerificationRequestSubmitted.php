<?php

namespace App\Notifications;

use App\Models\VerificationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VerificationRequest $verificationRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verification Request Submitted - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your verification request has been received and is now under review.')
            ->line('Verification Type: **' . $this->verificationRequest->getTypeLabel() . '**')
            ->line('We will review your documents and notify you of the result within 2-3 business days.')
            ->line('Thank you for using ' . config('app.name') . '!')
            ->action('View Request', route('verification.show', $this->verificationRequest));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'verification_request_id' => $this->verificationRequest->id,
            'type' => $this->verificationRequest->type,
            'status' => 'submitted',
            'message' => 'Your verification request has been submitted for review'
        ];
    }
}
