<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhotographyCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Wedding & Events
            ['name' => 'Wedding Photography', 'icon' => '💍'],
            ['name' => 'Holud Ceremony', 'icon' => '✨'],
            ['name' => 'Engagement Photography', 'icon' => '💑'],
            ['name' => 'Pre-wedding Photography', 'icon' => '📸'],
            ['name' => 'Reception Photography', 'icon' => '🎉'],
            ['name' => 'Event Photography', 'icon' => '🎊'],
            
            // Portrait & Studio
            ['name' => 'Portrait Photography', 'icon' => '👤'],
            ['name' => 'Studio Photography', 'icon' => '📷'],
            ['name' => 'Headshot Photography', 'icon' => '👁️'],
            ['name' => 'Family Photography', 'icon' => '👨‍👩‍👧‍👦'],
            ['name' => 'Children Photography', 'icon' => '👶'],
            ['name' => 'Maternity Photography', 'icon' => '🤰'],
            
            // Fashion & Lifestyle
            ['name' => 'Fashion Photography', 'icon' => '👗'],
            ['name' => 'Bridal Photography', 'icon' => '💒'],
            ['name' => 'Lifestyle Photography', 'icon' => '🌟'],
            
            // Commercial & Product
            ['name' => 'Product Photography', 'icon' => '📦'],
            ['name' => 'E-commerce Photography', 'icon' => '🛍️'],
            ['name' => 'Food Photography', 'icon' => '🍽️'],
            ['name' => 'Corporate Photography', 'icon' => '🏢'],
            ['name' => 'Real Estate Photography', 'icon' => '🏠'],
            ['name' => 'Automotive Photography', 'icon' => '🚗'],
            
            // Documentary & Artistic
            ['name' => 'Documentary Photography', 'icon' => '📖'],
            ['name' => 'Photo Journalist', 'icon' => '📰'],
            ['name' => 'Street Photography', 'icon' => '🚶'],
            ['name' => 'Travel Photography', 'icon' => '✈️'],
            ['name' => 'Wildlife Photography', 'icon' => '🦁'],
            ['name' => 'Nature Photography', 'icon' => '🌿'],
            ['name' => 'Landscape Photography', 'icon' => '🏔️'],
            ['name' => 'Architecture Photography', 'icon' => '🏛️'],
            
            // Specialty & Tech
            ['name' => 'Drone Photography', 'icon' => '🚁'],
            ['name' => 'Aerial Photography', 'icon' => '🛸'],
            ['name' => 'Macro Photography', 'icon' => '🔬'],
            ['name' => 'Underwater Photography', 'icon' => '🐠'],
            ['name' => 'Sports Photography', 'icon' => '⚽'],
            ['name' => 'Concert Photography', 'icon' => '🎤'],
            ['name' => 'Cinematography', 'icon' => '🎥'],
            ['name' => 'Wedding Videography', 'icon' => '🎬'],
            ['name' => 'Timelapses & Animation', 'icon' => '⏱️'],
            
            // Media & Journalism
            ['name' => 'News Photography', 'icon' => '📺'],
            ['name' => 'Press Photography', 'icon' => '📹'],
            ['name' => 'Broadcast Media', 'icon' => '📡'],
            ['name' => 'Feature Photography', 'icon' => '🎯'],
            ['name' => 'Wire Service Photography', 'icon' => '📻'],
            ['name' => 'Investigative Photography', 'icon' => '🔍'],
            ['name' => 'Social Media Content', 'icon' => '📱'],
            ['name' => 'Digital Media Production', 'icon' => '💻'],
        ];

        // Disable foreign key checks to allow truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $displayOrder = 1;
        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['name'] . ' services in Bangladesh',
                'icon' => $categoryData['icon'],
                'display_order' => $displayOrder++,
                'is_active' => true,
                'photographer_count' => 0,
                'booking_count' => 0,
            ]);
        }

        $this->command->info('✓ Photography categories seeded: ' . count($categories) . ' categories');
    }
}
