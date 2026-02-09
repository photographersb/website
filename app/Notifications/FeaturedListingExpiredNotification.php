<?php

namespace App\Notifications;

use App\Models\FeaturedPhotographer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeaturedListingExpiredNotification extends Notification implements ShouldQueue
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
        $category = $this->featured->category ?? 'all categories';

        return (new MailMessage)
            ->subject('Your Featured Photography Listing Has Expired')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your {$this->featured->package_tier} featured photography listing for {$category} has expired.")
            ->line("**Expired on:** " . $this->featured->end_date->format('F j, Y'))
            ->line("**Package:** {$this->featured->package_tier} - ৳" . $this->getPrice())
            ->line('To maintain your featured status and visibility, consider renewing your listing.')
            ->action('Renew Featured Listing', url('/be-featured'))
            ->line('Thank you for using Photographar!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $category = $this->featured->category ?? 'all categories';

        return new DatabaseMessage([
            'title' => 'Featured Listing Expired',
            'message' => "Your {$this->featured->package_tier} featured listing for {$category} has expired.",
            'type' => 'featured_expired',
            'featured_photographer_id' => $this->featured->id,
            'action_url' => '/be-featured',
            'action_label' => 'Renew Now',
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
            'category' => $this->featured->category,
            'expired_date' => $this->featured->end_date,
            'message' => "Your {$this->featured->package_tier} featured listing has expired.",
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
