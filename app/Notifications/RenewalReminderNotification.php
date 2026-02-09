<?php

namespace App\Notifications;

use App\Models\FeaturedPhotographer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewalReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $featured;

    /**
     * Create a new notification instance.
     */
    public function __construct(FeaturedPhotographer $featured)
    {
        $this->featured = $featured;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = now()->diffInDays($this->featured->end_date);

        return (new MailMessage)
            ->subject('Renew Your Featured Photography Listing - Expires in ' . $daysLeft . ' Days')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your {$this->featured->package_tier} featured photography listing is expiring soon!")
            ->line("**Expires on:** " . $this->featured->end_date->format('F j, Y') . " ({$daysLeft} days left)")
            ->line("**Category:** " . ($this->featured->category ?? 'All Categories'))
            ->line("**Location:** " . ($this->featured->location ?? 'All Locations'))
            ->line("**Current Price:** ৳" . $this->getPrice() . "/month")
            ->line('Don\'t lose your featured visibility. Renew now to maintain your top placement!')
            ->action('Renew Now', url('/be-featured'))
            ->line('If you need any assistance, please contact our support team.')
            ->line('Thank you for choosing Photographar!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $daysLeft = now()->diffInDays($this->featured->end_date);

        return new DatabaseMessage([
            'title' => 'Renewal Reminder - Expires in ' . $daysLeft . ' Days',
            'message' => "Your {$this->featured->package_tier} featured listing expires on {$this->featured->end_date->format('F j, Y')}. Renew now to maintain visibility!",
            'type' => 'featured_renewal_reminder',
            'featured_photographer_id' => $this->featured->id,
            'days_left' => $daysLeft,
            'action_url' => '/be-featured',
            'action_label' => 'Renew Featured',
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'featured_photographer_id' => $this->featured->id,
            'package_tier' => $this->featured->package_tier,
            'expires_at' => $this->featured->end_date,
            'days_left' => now()->diffInDays($this->featured->end_date),
            'message' => "Your featured listing expires in {$this->featured->end_date->diffForHumans()}",
        ];
    }

    /**
     * Get the price for the featured photographer package
     */
    private function getPrice()
    {
        $prices = [
            'Starter' => 999,
            'Professional' => 2499,
            'Enterprise' => 5999,
        ];
        return number_format($prices[$this->featured->package_tier] ?? 999);
    }
}
