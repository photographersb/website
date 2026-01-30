<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhotoCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Wedding', 'icon' => '💒', 'description' => 'Wedding ceremonies, receptions, and celebrations', 'display_order' => 1],
            ['name' => 'Portrait', 'icon' => '👤', 'description' => 'Individual and group portraits', 'display_order' => 2],
            ['name' => 'Event', 'icon' => '🎉', 'description' => 'Corporate events, parties, and gatherings', 'display_order' => 3],
            ['name' => 'Fashion', 'icon' => '👗', 'description' => 'Fashion shoots and modeling', 'display_order' => 4],
            ['name' => 'Product', 'icon' => '📦', 'description' => 'Product photography for e-commerce', 'display_order' => 5],
            ['name' => 'Food', 'icon' => '🍽️', 'description' => 'Food and restaurant photography', 'display_order' => 6],
            ['name' => 'Landscape', 'icon' => '🏞️', 'description' => 'Natural landscapes and scenery', 'display_order' => 7],
            ['name' => 'Architecture', 'icon' => '🏛️', 'description' => 'Buildings and architectural photography', 'display_order' => 8],
            ['name' => 'Nature', 'icon' => '🌿', 'description' => 'Wildlife and nature photography', 'display_order' => 9],
            ['name' => 'Street', 'icon' => '🚶', 'description' => 'Street and documentary photography', 'display_order' => 10],
            ['name' => 'Sports', 'icon' => '⚽', 'description' => 'Sports and action photography', 'display_order' => 11],
            ['name' => 'Baby', 'icon' => '👶', 'description' => 'Newborn and baby photography', 'display_order' => 12],
            ['name' => 'Maternity', 'icon' => '🤰', 'description' => 'Pregnancy and maternity shoots', 'display_order' => 13],
            ['name' => 'Commercial', 'icon' => '💼', 'description' => 'Commercial and advertising photography', 'display_order' => 14],
        ];

        foreach ($categories as $category) {
            \App\Models\PhotoCategory::updateOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($category['name']),
                    'icon' => $category['icon'],
                    'description' => $category['description'],
                    'display_order' => $category['display_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
