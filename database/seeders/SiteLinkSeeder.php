<?php

namespace Database\Seeders;

use App\Models\SiteLink;
use Illuminate\Database\Seeder;

class SiteLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing links
        SiteLink::truncate();

        $links = [
            // ========== NAVBAR LINKS ==========
            [
                'section' => 'navbar',
                'title' => 'Home',
                'url' => '/',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 10,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'navbar',
                'title' => 'Find Photographers',
                'url' => '/',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 20,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'navbar',
                'title' => 'Events',
                'url' => '/events',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 30,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'navbar',
                'title' => 'Competitions',
                'url' => '/competitions',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 40,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'navbar',
                'title' => 'Join as Photographer',
                'url' => '/auth',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 50,
                'is_active' => true,
                'visibility' => 'guest_only',
            ],

            // ========== FOOTER COMPANY LINKS ==========
            [
                'section' => 'footer_company',
                'title' => 'About Us',
                'url' => '/about',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 10,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_company',
                'title' => 'Contact',
                'url' => '/contact',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 20,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_company',
                'title' => 'How It Works',
                'url' => '/how-it-works',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 30,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_company',
                'title' => 'Career',
                'url' => '/career',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 40,
                'is_active' => false, // Disabled by default
                'visibility' => 'public',
            ],

            // ========== FOOTER LEGAL LINKS ==========
            [
                'section' => 'footer_legal',
                'title' => 'Privacy Policy',
                'url' => '/privacy',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 10,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_legal',
                'title' => 'Terms of Service',
                'url' => '/terms',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 20,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_legal',
                'title' => 'Refund Policy',
                'url' => '/refunds',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 30,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_legal',
                'title' => 'Cookie Policy',
                'url' => '/privacy',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 40,
                'is_active' => true,
                'visibility' => 'public',
            ],

            // ========== FOOTER USEFUL LINKS ==========
            [
                'section' => 'footer_useful',
                'title' => 'Sitemap',
                'url' => '/sitemap',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 10,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_useful',
                'title' => 'Categories',
                'url' => '/categories',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 20,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_useful',
                'title' => 'Locations',
                'url' => '/locations',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 30,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_useful',
                'title' => 'Help Center',
                'url' => '/help',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 40,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'footer_useful',
                'title' => 'Blog',
                'url' => '/events',
                'route_name' => null,
                'icon' => null,
                'open_in_new_tab' => false,
                'sort_order' => 50,
                'is_active' => true,
                'visibility' => 'public',
            ],

            // ========== SOCIAL MEDIA LINKS ==========
            [
                'section' => 'social',
                'title' => 'Facebook',
                'url' => 'https://www.facebook.com/thephotographersbd',
                'route_name' => null,
                'icon' => 'facebook',
                'open_in_new_tab' => true,
                'sort_order' => 10,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'social',
                'title' => 'Instagram',
                'url' => 'https://www.instagram.com/thephotographersbd',
                'route_name' => null,
                'icon' => 'instagram',
                'open_in_new_tab' => true,
                'sort_order' => 20,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'social',
                'title' => 'WhatsApp',
                'url' => 'https://wa.me/8801767300900',
                'route_name' => null,
                'icon' => 'whatsapp',
                'open_in_new_tab' => true,
                'sort_order' => 30,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'social',
                'title' => 'YouTube',
                'url' => 'https://www.youtube.com/@photographersbd',
                'route_name' => null,
                'icon' => 'youtube',
                'open_in_new_tab' => true,
                'sort_order' => 40,
                'is_active' => false, // Disabled by default
                'visibility' => 'public',
            ],
            [
                'section' => 'social',
                'title' => 'LinkedIn',
                'url' => 'https://www.linkedin.com/company/photographersbd',
                'route_name' => null,
                'icon' => 'linkedin',
                'open_in_new_tab' => true,
                'sort_order' => 50,
                'is_active' => false, // Disabled by default
                'visibility' => 'public',
            ],
            [
                'section' => 'social',
                'title' => 'TikTok',
                'url' => 'https://www.tiktok.com/@photographersbd',
                'route_name' => null,
                'icon' => 'tiktok',
                'open_in_new_tab' => true,
                'sort_order' => 60,
                'is_active' => false, // Disabled by default
                'visibility' => 'public',
            ],

            // ========== CTA LINKS ==========
            [
                'section' => 'cta',
                'title' => 'Become a Photographer',
                'url' => '/auth',
                'route_name' => null,
                'icon' => 'camera',
                'open_in_new_tab' => false,
                'sort_order' => 10,
                'is_active' => true,
                'visibility' => 'guest_only',
            ],
            [
                'section' => 'cta',
                'title' => 'Submit to Competition',
                'url' => '/competitions',
                'route_name' => null,
                'icon' => 'trophy',
                'open_in_new_tab' => false,
                'sort_order' => 20,
                'is_active' => true,
                'visibility' => 'auth_only',
            ],
            [
                'section' => 'cta',
                'title' => 'Register for Event',
                'url' => '/events',
                'route_name' => null,
                'icon' => 'calendar',
                'open_in_new_tab' => false,
                'sort_order' => 30,
                'is_active' => true,
                'visibility' => 'public',
            ],
            [
                'section' => 'cta',
                'title' => 'Become a Sponsor',
                'url' => '/become-sponsor',
                'route_name' => null,
                'icon' => 'star',
                'open_in_new_tab' => false,
                'sort_order' => 40,
                'is_active' => true,
                'visibility' => 'public',
            ],
        ];

        foreach ($links as $link) {
            SiteLink::create($link);
        }

        $this->command->info('✅ Site links seeded successfully: ' . count($links) . ' links created');
    }
}
