<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class PlatformSettingsSeeder extends Seeder
{
    /**
     * Seed platform default settings.
     * These control GA4, Firebase, payments, email, localization, etc.
     */
    public function run(): void
    {
        // Delete existing to ensure fresh state
        DB::table('settings')->truncate();

        $settings = [
            // Platform Info
            ['key' => 'platform_name', 'value' => 'Photographar', 'group' => 'general', 'data_type' => 'string', 'description' => 'Platform name', 'is_public' => true],
            ['key' => 'platform_tagline', 'value' => 'Professional Photography Marketplace', 'group' => 'general', 'data_type' => 'string', 'description' => 'Platform tagline', 'is_public' => true],
            ['key' => 'platform_currency', 'value' => 'BDT', 'group' => 'general', 'data_type' => 'string', 'description' => 'Default currency code', 'is_public' => true],
            ['key' => 'platform_timezone', 'value' => 'Asia/Dhaka', 'group' => 'general', 'data_type' => 'string', 'description' => 'Platform timezone', 'is_public' => false],
            ['key' => 'platform_commission_percentage', 'value' => '10', 'group' => 'general', 'data_type' => 'integer', 'description' => 'Platform commission on transactions (%)', 'is_public' => false],

            // Analytics & Tracking
            ['key' => 'ga4_measurement_id', 'value' => '', 'group' => 'tracking', 'data_type' => 'string', 'description' => 'Google Analytics 4 Measurement ID (G-XXXXXXX)', 'is_public' => false],
            ['key' => 'gtag_id', 'value' => '', 'group' => 'tracking', 'data_type' => 'string', 'description' => 'Google Tag Manager ID (GTM-XXXXXXX)', 'group' => 'tracking', 'data_type' => 'string', 'is_public' => false],
            ['key' => 'fb_pixel_id', 'value' => '', 'group' => 'tracking', 'data_type' => 'string', 'description' => 'Facebook Pixel ID', 'is_public' => false],
            ['key' => 'enable_analytics', 'value' => '1', 'group' => 'tracking', 'data_type' => 'boolean', 'description' => 'Enable analytics tracking', 'is_public' => false],

            // Email Settings
            ['key' => 'mail_from_address', 'value' => 'noreply@photographar.bd', 'group' => 'email', 'data_type' => 'string', 'description' => 'From email address', 'is_public' => false],
            ['key' => 'mail_from_name', 'value' => 'Photographar', 'group' => 'email', 'data_type' => 'string', 'description' => 'From name for emails', 'is_public' => false],
            ['key' => 'send_verification_email', 'value' => '1', 'group' => 'email', 'data_type' => 'boolean', 'description' => 'Send email verification on signup', 'is_public' => false],

            // Payment Settings
            ['key' => 'stripe_public_key', 'value' => '', 'group' => 'payment', 'data_type' => 'string', 'description' => 'Stripe public key', 'is_public' => false],
            ['key' => 'stripe_secret_key', 'value' => '', 'group' => 'payment', 'data_type' => 'string', 'description' => 'Stripe secret key', 'is_public' => false],
            ['key' => 'enable_stripe', 'value' => '0', 'group' => 'payment', 'data_type' => 'boolean', 'description' => 'Enable Stripe payments', 'is_public' => false],

            // File Storage
            ['key' => 'max_upload_size_mb', 'value' => '10', 'group' => 'storage', 'data_type' => 'integer', 'description' => 'Max file upload size (MB)', 'is_public' => false],
            ['key' => 'storage_path', 'value' => 'public', 'group' => 'storage', 'data_type' => 'string', 'description' => 'Storage disk (public, private, s3)', 'is_public' => false],

            // Features (Feature Flags)
            ['key' => 'enable_competitions', 'value' => '1', 'group' => 'features', 'data_type' => 'boolean', 'description' => 'Enable photography competitions', 'is_public' => true],
            ['key' => 'enable_mentorship', 'value' => '1', 'group' => 'features', 'data_type' => 'boolean', 'description' => 'Enable mentorship program', 'is_public' => true],
            ['key' => 'enable_events', 'value' => '1', 'group' => 'features', 'data_type' => 'boolean', 'description' => 'Enable event listings', 'is_public' => true],
            ['key' => 'enable_two_factor_auth', 'value' => '1', 'group' => 'features', 'data_type' => 'boolean', 'description' => 'Enable two-factor authentication', 'is_public' => true],

            // Localization
            ['key' => 'default_language', 'value' => 'en', 'group' => 'localization', 'data_type' => 'string', 'description' => 'Default language (en, bn)', 'is_public' => true],
            ['key' => 'date_format', 'value' => 'DD-MM-YYYY', 'group' => 'localization', 'data_type' => 'string', 'description' => 'Date format display', 'is_public' => true],
            ['key' => 'time_format', 'value' => 'HH:mm', 'group' => 'localization', 'data_type' => 'string', 'description' => 'Time format display', 'is_public' => true],
            ['key' => 'enable_bangla', 'value' => '1', 'group' => 'localization', 'data_type' => 'boolean', 'description' => 'Enable Bangla language support', 'is_public' => true],

            // SEO & Sharing
            ['key' => 'site_title', 'value' => 'Photographar - Find Professional Photographers', 'group' => 'seo', 'data_type' => 'string', 'description' => 'Site title for SEO', 'is_public' => true],
            ['key' => 'site_description', 'value' => 'Connect with professional photographers in Bangladesh', 'group' => 'seo', 'data_type' => 'string', 'description' => 'Site description', 'is_public' => true],
            ['key' => 'og_image', 'value' => '', 'group' => 'seo', 'data_type' => 'string', 'description' => 'Default OG image URL', 'is_public' => true],
            ['key' => 'twitter_handle', 'value' => '', 'group' => 'seo', 'data_type' => 'string', 'description' => 'Twitter handle for sharing', 'is_public' => true],

            // Moderation
            ['key' => 'require_photographer_approval', 'value' => '1', 'group' => 'moderation', 'data_type' => 'boolean', 'description' => 'Photographers require admin approval', 'is_public' => false],
            ['key' => 'min_photographer_rating_to_list', 'value' => '2.0', 'group' => 'moderation', 'data_type' => 'string', 'description' => 'Minimum rating to appear in listings', 'is_public' => false],

            // Notifications
            ['key' => 'enable_email_notifications', 'value' => '1', 'group' => 'notifications', 'data_type' => 'boolean', 'description' => 'Send email notifications', 'is_public' => false],
            ['key' => 'enable_sms_notifications', 'value' => '1', 'group' => 'notifications', 'data_type' => 'boolean', 'description' => 'Send SMS notifications', 'is_public' => false],
        ];

        DB::table('settings')->insert($settings);

        $this->command->info('Platform settings seeded successfully. Total: ' . count($settings));
    }
}
