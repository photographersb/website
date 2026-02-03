<?php

namespace Database\Seeders;

use App\Models\Photographer;
use App\Models\Album;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AlbumsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📸 Seeding albums...');

        $albumTemplates = [
            ['name' => 'Wedding Portfolio', 'description' => 'Our best wedding photography shots showcasing various ceremonies.'],
            ['name' => 'Pre-wedding Sessions', 'description' => 'Romantic pre-wedding photo sessions at beautiful locations.'],
            ['name' => 'Corporate Events', 'description' => 'Professional coverage of corporate events and conferences.'],
            ['name' => 'Holud Ceremonies', 'description' => 'Vibrant and colorful Holud ceremony photography.'],
            ['name' => 'Fashion Shoots', 'description' => 'High-fashion photography and model portfolios.'],
            ['name' => 'Product Showcase', 'description' => 'Commercial product photography for brands.'],
            ['name' => 'Food Photography', 'description' => 'Delicious food photography for restaurants and cafes.'],
            ['name' => 'Cinematic Highlights', 'description' => 'Video thumbnails and cinematic wedding moments.'],
        ];

        $photographers = Photographer::all();
        $totalAlbums = 0;

        foreach ($photographers as $photographer) {
            // Each photographer gets 2-5 albums
            $numAlbums = rand(2, 5);
            $selectedTemplates = array_rand($albumTemplates, min($numAlbums, count($albumTemplates)));
            
            if (!is_array($selectedTemplates)) {
                $selectedTemplates = [$selectedTemplates];
            }

            foreach ($selectedTemplates as $templateIndex) {
                $template = $albumTemplates[$templateIndex];
                
                Album::create([
                    'photographer_id' => $photographer->id,
                    'category_id' => rand(1, 12),
                    'name' => $template['name'],
                    'slug' => Str::slug($photographer->id . '-' . $template['name']),
                    'description' => $template['description'],
                    'cover_photo_url' => null,
                    'photo_count' => rand(15, 50),
                    'is_public' => true,
                    'view_count' => rand(50, 500),
                    'display_order' => $totalAlbums,
                    'created_at' => now()->subDays(rand(1, 180)),
                ]);

                $totalAlbums++;
            }
        }

        $this->command->info("✅ {$totalAlbums} albums created for {$photographers->count()} photographers");
    }
}
