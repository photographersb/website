<?php

namespace App\Notifications;

use App\Models\VerificationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRejected extends Notification implements ShouldQueue
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
            ->subject('Verification Request - Review Required - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your verification request requires additional review.')
            ->line('**Reason:** ' . ($this->verificationRequest->admin_note ?? 'Please contact support for details'))
            ->line('Verification Type: **' . $this->verificationRequest->getTypeLabel() . '**')
            ->line('You can submit a new verification request after addressing the concerns.')
            ->action('Submit New Request', route('verification.create'))
            ->line('If you have questions, please contact our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'verification_request_id' => $this->verificationRequest->id,
            'type' => $this->verificationRequest->type,
            'status' => 'rejected',
            'reason' => $this->verificationRequest->admin_note,
            'message' => 'Your verification request was not approved. Please review the feedback.'
        ];
    }
}
