<?php

namespace App\Notifications;

use App\Models\PhotographerTip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TipReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tip;

    public function __construct(PhotographerTip $tip)
    {
        $this->tip = $tip;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $donorName = $this->tip->user ? $this->tip->user->name : 'Anonymous';

        return (new MailMessage)
            ->subject("☕ You received a tip from {$donorName}!")
            ->greeting("Great news!")
            ->line("{$donorName} sent you a tip of **৳" . number_format($this->tip->amount, 0) . "**!")
            ->when($this->tip->message, function ($mail) {
                return $mail->line("**Their message:** \"{$this->tip->message}\"");
            })
            ->line("")
            ->line("This is a great way to build connections with your clients and showcase your value!")
            ->action('View Your Profile', route('photographer.profile.public', $this->tip->photographer->user->username))
            ->line("")
            ->line("Thank you for being an amazing photographer! 📸");
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'You received a tip!',
            'message' => 'You received a tip of ৳' . number_format($this->tip->amount, 0) . ' from ' . ($this->tip->user ? $this->tip->user->name : 'Anonymous'),
            'type' => 'tip_received',
            'tip_id' => $this->tip->id,
            'amount' => $this->tip->amount,
            'donor_name' => $this->tip->user ? $this->tip->user->name : 'Anonymous',
        ];
    }
}
