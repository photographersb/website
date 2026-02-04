<?php

namespace App\Notifications;

use App\Models\VerificationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationApproved extends Notification implements ShouldQueue
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
            ->subject('Verification Approved - ' . config('app.name'))
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line('Your verification request has been **approved**.')
            ->line('Verification Type: **' . $this->verificationRequest->getTypeLabel() . '**')
            ->line('Your profile now displays a verification badge, which helps build trust with clients.')
            ->line('You can now take full advantage of premium photographer features.')
            ->action('View Your Profile', route('photographer.show', $notifiable->username))
            ->line('Thank you for choosing ' . config('app.name') . '!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'verification_request_id' => $this->verificationRequest->id,
            'type' => $this->verificationRequest->type,
            'status' => 'approved',
            'message' => 'Congratulations! Your verification has been approved.'
        ];
    }
}
