<?php

namespace App\Notifications;

use App\Models\FeaturedPhotographer;
use App\Models\FeaturedPhotographerUpgrade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageUpgradedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $featured;
    protected $upgrade;

    public function __construct(FeaturedPhotographer $featured, FeaturedPhotographerUpgrade $upgrade)
    {
        $this->featured = $featured;
        $this->upgrade = $upgrade;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $packageBenefits = [
            'Starter' => ['5 featured listings', 'Basic analytics', 'Email support'],
            'Professional' => ['15 featured listings', 'Advanced analytics', 'Priority support', 'Custom categories'],
            'Enterprise' => ['Unlimited listings', 'Real-time analytics', '24/7 premium support', 'API access', 'Custom features'],
        ];

        $benefits = $packageBenefits[$this->upgrade->to_package] ?? [];

        $mailMessage = (new MailMessage)
            ->greeting("🎉 Package Upgraded Successfully!")
            ->line("Congratulations! Your featured photographer package has been upgraded.")
            ->line("")
            ->line("**Upgrade Details:**")
            ->line("From: " . $this->upgrade->from_package . " → To: " . $this->upgrade->to_package)
            ->line("Amount Paid: ৳" . number_format($this->upgrade->total_amount, 2))
            ->line("Discount Applied: ৳" . number_format($this->upgrade->discount_amount, 2))
            ->line("")
            ->line("**Your New Package Includes:**");

        foreach ($benefits as $benefit) {
            $mailMessage->line("✓ " . $benefit);
        }

        $mailMessage
            ->line("")
            ->line("Package Active Until: " . $this->featured->end_date->format('F j, Y'))
            ->line("Remaining Days: " . now()->diffInDays($this->featured->end_date))
            ->line("")
            ->action('View Your Featured Listing', route('featured-photographer.show', $this->featured->id))
            ->line("")
            ->line("Thank you for upgrading! Your profile will receive more visibility and engagement with your new tier.")
            ->line("")
            ->line("Need help? Contact our support team.")
            ->salutation('Best regards, Photographar Team');

        return $mailMessage;
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Package Upgraded to ' . $this->upgrade->to_package,
            'message' => 'Your featured photographer package has been successfully upgraded from ' . $this->upgrade->from_package . ' to ' . $this->upgrade->to_package,
            'type' => 'upgrade_success',
            'featured_photographer_id' => $this->featured->id,
            'upgrade_id' => $this->upgrade->id,
            'amount' => $this->upgrade->total_amount,
            'new_package' => $this->upgrade->to_package,
            'action_url' => route('featured-photographer.show', $this->featured->id),
        ];
    }
}
