<?php

namespace Database\Seeders;

use App\Models\Hashtag;
use App\Models\PhotoCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HashtagSeeder extends Seeder
{
    public function run()
    {
        // Get photo categories
        $portrait = PhotoCategory::where('slug', 'portrait')->first();
        $landscape = PhotoCategory::where('slug', 'landscape')->first();
        $wedding = PhotoCategory::where('slug', 'wedding')->first();
        $wildlife = PhotoCategory::where('slug', 'wildlife')->first();
        $street = PhotoCategory::where('slug', 'street')->first();

        $hashtags = [
            // General Photography
            ['name' => 'photography', 'description' => 'General photography tag', 'is_featured' => true, 'category_id' => null],
            ['name' => 'photooftheday', 'description' => 'Daily best photos', 'is_featured' => true, 'category_id' => null],
            ['name' => 'photographer', 'description' => 'For photographers', 'is_featured' => true, 'category_id' => null],
            ['name' => 'photo', 'description' => 'Photo related content', 'is_featured' => false, 'category_id' => null],
            ['name' => 'instagood', 'description' => 'Instagram good quality', 'is_featured' => false, 'category_id' => null],
            ['name' => 'picoftheday', 'description' => 'Picture of the day', 'is_featured' => false, 'category_id' => null],
            
            // Portrait Photography
            ['name' => 'portrait', 'description' => 'Portrait photography', 'is_featured' => true, 'category_id' => $portrait?->id],
            ['name' => 'portraitphotography', 'description' => 'Portrait style photos', 'is_featured' => true, 'category_id' => $portrait?->id],
            ['name' => 'portraits', 'description' => 'Portrait shots', 'is_featured' => false, 'category_id' => $portrait?->id],
            ['name' => 'model', 'description' => 'Model photography', 'is_featured' => false, 'category_id' => $portrait?->id],
            ['name' => 'fashion', 'description' => 'Fashion photography', 'is_featured' => false, 'category_id' => $portrait?->id],
            
            // Landscape Photography
            ['name' => 'landscape', 'description' => 'Landscape photography', 'is_featured' => true, 'category_id' => $landscape?->id],
            ['name' => 'landscapephotography', 'description' => 'Landscape style', 'is_featured' => true, 'category_id' => $landscape?->id],
            ['name' => 'nature', 'description' => 'Nature photography', 'is_featured' => true, 'category_id' => $landscape?->id],
            ['name' => 'naturephotography', 'description' => 'Nature shots', 'is_featured' => false, 'category_id' => $landscape?->id],
            ['name' => 'sunset', 'description' => 'Sunset photos', 'is_featured' => false, 'category_id' => $landscape?->id],
            ['name' => 'mountains', 'description' => 'Mountain photography', 'is_featured' => false, 'category_id' => $landscape?->id],
            
            // Wedding Photography
            ['name' => 'wedding', 'description' => 'Wedding photography', 'is_featured' => true, 'category_id' => $wedding?->id],
            ['name' => 'weddingphotography', 'description' => 'Wedding photos', 'is_featured' => true, 'category_id' => $wedding?->id],
            ['name' => 'weddingphotographer', 'description' => 'Wedding photographer tag', 'is_featured' => false, 'category_id' => $wedding?->id],
            ['name' => 'bride', 'description' => 'Bride photos', 'is_featured' => false, 'category_id' => $wedding?->id],
            ['name' => 'groom', 'description' => 'Groom photos', 'is_featured' => false, 'category_id' => $wedding?->id],
            ['name' => 'engagement', 'description' => 'Engagement photography', 'is_featured' => false, 'category_id' => $wedding?->id],
            
            // Wildlife Photography
            ['name' => 'wildlife', 'description' => 'Wildlife photography', 'is_featured' => true, 'category_id' => $wildlife?->id],
            ['name' => 'wildlifephotography', 'description' => 'Wildlife photos', 'is_featured' => true, 'category_id' => $wildlife?->id],
            ['name' => 'animals', 'description' => 'Animal photography', 'is_featured' => false, 'category_id' => $wildlife?->id],
            ['name' => 'birds', 'description' => 'Bird photography', 'is_featured' => false, 'category_id' => $wildlife?->id],
            ['name' => 'birdphotography', 'description' => 'Bird shots', 'is_featured' => false, 'category_id' => $wildlife?->id],
            
            // Street Photography
            ['name' => 'street', 'description' => 'Street photography', 'is_featured' => true, 'category_id' => $street?->id],
            ['name' => 'streetphotography', 'description' => 'Street style photos', 'is_featured' => true, 'category_id' => $street?->id],
            ['name' => 'urban', 'description' => 'Urban photography', 'is_featured' => false, 'category_id' => $street?->id],
            ['name' => 'city', 'description' => 'City photography', 'is_featured' => false, 'category_id' => $street?->id],
            ['name' => 'cityscape', 'description' => 'Cityscape photos', 'is_featured' => false, 'category_id' => $street?->id],
            
            // Bangladesh Specific
            ['name' => 'bangladesh', 'description' => 'Bangladesh photography', 'is_featured' => true, 'category_id' => null],
            ['name' => 'bangladeshphotography', 'description' => 'Photos from Bangladesh', 'is_featured' => true, 'category_id' => null],
            ['name' => 'dhaka', 'description' => 'Dhaka city photography', 'is_featured' => false, 'category_id' => null],
            ['name' => 'bangladeshiphotographer', 'description' => 'Photographers from Bangladesh', 'is_featured' => false, 'category_id' => null],
            
            // Event Photography
            ['name' => 'event', 'description' => 'Event photography', 'is_featured' => false, 'category_id' => null],
            ['name' => 'eventphotography', 'description' => 'Event coverage', 'is_featured' => false, 'category_id' => null],
            ['name' => 'concert', 'description' => 'Concert photography', 'is_featured' => false, 'category_id' => null],
            ['name' => 'festival', 'description' => 'Festival photography', 'is_featured' => false, 'category_id' => null],
        ];

        foreach ($hashtags as $hashtag) {
            Hashtag::updateOrCreate(
                ['slug' => Str::slug($hashtag['name'])],
                [
                    'name' => $hashtag['name'],
                    'description' => $hashtag['description'],
                    'is_featured' => $hashtag['is_featured'],
                    'category_id' => $hashtag['category_id'],
                    'usage_count' => 0,
                ]
            );
        }

        $this->command->info('Hashtags seeded successfully!');
    }
}
