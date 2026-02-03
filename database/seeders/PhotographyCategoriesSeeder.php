<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhotographyCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📸 Seeding photography categories...');

        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categories = [
            [
                'name' => 'Wedding Photography',
                'slug' => 'wedding-photography',
                'description' => 'Professional wedding photography services',
                'icon' => '💍',
            ],
            [
                'name' => 'Wedding Cinematography',
                'slug' => 'wedding-cinematography',
                'description' => 'Wedding videography and cinematic services',
                'icon' => '🎬',
            ],
            [
                'name' => 'Pre-wedding',
                'slug' => 'pre-wedding',
                'description' => 'Pre-wedding shoots and engagement photography',
                'icon' => '🌹',
            ],
            [
                'name' => 'Holud Photography',
                'slug' => 'holud-photography',
                'description' => 'Traditional Bengali Holud ceremony photography',
                'icon' => '🎉',
            ],
            [
                'name' => 'Event Photography',
                'slug' => 'event-photography',
                'description' => 'General event coverage and documentation',
                'icon' => '📷',
            ],
            [
                'name' => 'Corporate Photography',
                'slug' => 'corporate-photography',
                'description' => 'Corporate events, team photos, and business photography',
                'icon' => '💼',
            ],
            [
                'name' => 'Product Photography',
                'slug' => 'product-photography',
                'description' => 'Product shoots for e-commerce and catalogs',
                'icon' => '📦',
            ],
            [
                'name' => 'Food Photography',
                'slug' => 'food-photography',
                'description' => 'Food styling and restaurant photography',
                'icon' => '🍽️',
            ],
            [
                'name' => 'Fashion & Model Photography',
                'slug' => 'fashion-model-photography',
                'description' => 'Fashion shoots and model portfolio photography',
                'icon' => '👗',
            ],
            [
                'name' => 'Studio Photography',
                'slug' => 'studio-photography',
                'description' => 'Studio portraits and passport photos',
                'icon' => '🎥',
            ],
            [
                'name' => 'Travel & Documentary',
                'slug' => 'travel-documentary',
                'description' => 'Travel photography and documentary services',
                'icon' => '✈️',
            ],
            [
                'name' => 'Drone Photography',
                'slug' => 'drone-photography',
                'description' => 'Aerial and drone photography services',
                'icon' => '🚁',
            ],
            [
                'name' => 'Portrait Photography',
                'slug' => 'portrait-photography',
                'description' => 'Individual and family portrait sessions',
                'icon' => '👤',
            ],
            [
                'name' => 'Maternity Photography',
                'slug' => 'maternity-photography',
                'description' => 'Maternity and pregnancy photography',
                'icon' => '🤰',
            ],
            [
                'name' => 'Newborn Photography',
                'slug' => 'newborn-photography',
                'description' => 'Newborn and infant photography services',
                'icon' => '👶',
            ],
            [
                'name' => 'Kids Photography',
                'slug' => 'kids-photography',
                'description' => 'Children and family photography',
                'icon' => '👧',
            ],
            [
                'name' => 'Birthday & Celebrations',
                'slug' => 'birthday-celebrations',
                'description' => 'Birthday parties and celebration photography',
                'icon' => '🎂',
            ],
            [
                'name' => 'Real Estate Photography',
                'slug' => 'real-estate-photography',
                'description' => 'Property and real estate photography',
                'icon' => '🏠',
            ],
            [
                'name' => 'Architecture Photography',
                'slug' => 'architecture-photography',
                'description' => 'Architectural and interior photography',
                'icon' => '🏗️',
            ],
            [
                'name' => 'Wildlife & Nature',
                'slug' => 'wildlife-nature',
                'description' => 'Wildlife, nature, and landscape photography',
                'icon' => '🦁',
            ],
            [
                'name' => 'Landscape Photography',
                'slug' => 'landscape-photography',
                'description' => 'Scenic and landscape photography',
                'icon' => '🏞️',
            ],
            [
                'name' => 'Street Photography',
                'slug' => 'street-photography',
                'description' => 'Street and candid photography',
                'icon' => '🛣️',
            ],
            [
                'name' => 'Macro Photography',
                'slug' => 'macro-photography',
                'description' => 'Close-up and macro photography',
                'icon' => '🔍',
            ],
            [
                'name' => 'Sports Photography',
                'slug' => 'sports-photography',
                'description' => 'Sports events and action photography',
                'icon' => '⚽',
            ],
            [
                'name' => 'Concert & Live Events',
                'slug' => 'concert-live-events',
                'description' => 'Concert, music festival, and live performance photography',
                'icon' => '🎤',
            ],
            [
                'name' => 'Religious Ceremonies',
                'slug' => 'religious-ceremonies',
                'description' => 'Religious and cultural ceremony photography',
                'icon' => '🙏',
            ],
            [
                'name' => 'Graduation Photography',
                'slug' => 'graduation-photography',
                'description' => 'Graduation and academic ceremony photography',
                'icon' => '🎓',
            ],
            [
                'name' => 'Engagement Photography',
                'slug' => 'engagement-photography',
                'description' => 'Engagement and proposal photography',
                'icon' => '💎',
            ],
            [
                'name' => 'Bridal & Jewelry',
                'slug' => 'bridal-jewelry',
                'description' => 'Bridal wear and jewelry photography',
                'icon' => '👑',
            ],
            [
                'name' => 'Commercial Photography',
                'slug' => 'commercial-photography',
                'description' => 'Commercial and advertising photography',
                'icon' => '📢',
            ],
            [
                'name' => 'Pet Photography',
                'slug' => 'pet-photography',
                'description' => 'Pet and animal photography',
                'icon' => '🐾',
            ],
            [
                'name' => 'Social Media Content',
                'slug' => 'social-media-content',
                'description' => 'Social media content creation and influencer photography',
                'icon' => '📱',
            ],
            [
                'name' => 'Underwater Photography',
                'slug' => 'underwater-photography',
                'description' => 'Underwater and diving photography',
                'icon' => '🌊',
            ],
            [
                'name' => 'Night Photography',
                'slug' => 'night-photography',
                'description' => 'Night and low-light photography',
                'icon' => '🌙',
            ],
            [
                'name' => 'Film Photography',
                'slug' => 'film-photography',
                'description' => 'Traditional film and analog photography',
                'icon' => '🎞️',
            ],
            [
                'name' => 'Black & White Photography',
                'slug' => 'black-white-photography',
                'description' => 'Black and white and monochrome photography',
                'icon' => '⚫',
            ],
            [
                'name' => 'Fine Art Photography',
                'slug' => 'fine-art-photography',
                'description' => 'Fine art and conceptual photography',
                'icon' => '🎨',
            ],
            [
                'name' => 'Vintage & Retro',
                'slug' => 'vintage-retro',
                'description' => 'Vintage and retro style photography',
                'icon' => '📼',
            ],
            [
                'name' => 'Infra-Red Photography',
                'slug' => 'infra-red-photography',
                'description' => 'Infrared and thermal photography',
                'icon' => '🌡️',
            ],
            [
                'name' => 'High Speed Photography',
                'slug' => 'high-speed-photography',
                'description' => 'High-speed and slow-motion photography',
                'icon' => '⚡',
            ],
            [
                'name' => 'Panoramic Photography',
                'slug' => 'panoramic-photography',
                'description' => 'Panoramic and wide-angle photography',
                'icon' => '🖼️',
            ],
            [
                'name' => 'Time Lapse & Sequences',
                'slug' => 'time-lapse-sequences',
                'description' => 'Time lapse, HDR, and sequence photography',
                'icon' => '⏱️',
            ],
            [
                'name' => 'Medical & Scientific',
                'slug' => 'medical-scientific',
                'description' => 'Medical, scientific, and technical photography',
                'icon' => '🔬',
            ],
            [
                'name' => 'Automotive Photography',
                'slug' => 'automotive-photography',
                'description' => 'Car and automotive photography',
                'icon' => '🚗',
            ],
            [
                'name' => 'Aviation Photography',
                'slug' => 'aviation-photography',
                'description' => 'Aircraft and aviation photography',
                'icon' => '✈️',
            ],
            [
                'name' => 'Motorcycle Photography',
                'slug' => 'motorcycle-photography',
                'description' => 'Motorcycle and bike photography',
                'icon' => '🏍️',
            ],
            [
                'name' => 'Custom & Personalization',
                'slug' => 'custom-personalization',
                'description' => 'Custom and specialized photography services',
                'icon' => '⭐',
            ],
        ];

        foreach ($categories as $key => $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => $category['description'],
                'icon' => $category['icon'] ?? null,
                'is_active' => true,
                'display_order' => $key + 1,
                'photographer_count' => 0,
                'booking_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->line("  ✓ {$category['name']}");
        }

        $this->command->info('✅ ' . count($categories) . ' photography categories seeded');
    }
}
