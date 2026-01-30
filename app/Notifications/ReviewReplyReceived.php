<?php

namespace App\Notifications;

use App\Models\ReviewReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewReplyReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reviewReply;

    public function __construct(ReviewReply $reviewReply)
    {
        $this->reviewReply = $reviewReply;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $photographerName = $this->reviewReply->review->photographer->user->name;

        return (new MailMessage)
            ->subject($photographerName . ' Replied to Your Review')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($photographerName . ' has replied to your review.')
            ->action('View Reply', url('/photographer/' . $this->reviewReply->review->photographer->slug . '#review-' . $this->reviewReply->review_id))
            ->line('Thank you for sharing your feedback!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'review_reply_received',
            'review_id' => $this->reviewReply->review_id,
            'photographer_id' => $this->reviewReply->review->photographer_id,
            'photographer_name' => $this->reviewReply->review->photographer->user->name,
            'message' => $this->reviewReply->review->photographer->user->name . ' replied to your review',
        ];
    }
}
