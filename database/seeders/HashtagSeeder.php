<?php

namespace Database\Seeders;

use App\Models\Hashtag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HashtagSeeder extends Seeder
{
    public function run(): void
    {
        $hashtags = [
            // Platform Tags
            ['name' => 'photographersb', 'featured' => true],
            ['name' => 'somogrobangladesh', 'featured' => true],
            ['name' => 'thephotographersbd', 'featured' => true],
            
            // General Photography
            ['name' => 'photography', 'featured' => true],
            ['name' => 'photooftheday', 'featured' => true],
            ['name' => 'photographer', 'featured' => true],
            ['name' => 'photo', 'featured' => false],
            ['name' => 'picoftheday', 'featured' => false],
            ['name' => 'picstitch', 'featured' => false],
            ['name' => 'instaart', 'featured' => false],
            
            // Wedding & Events
            ['name' => 'wedding', 'featured' => true],
            ['name' => 'weddingphotography', 'featured' => true],
            ['name' => 'weddingphotographer', 'featured' => false],
            ['name' => 'bride', 'featured' => false],
            ['name' => 'groom', 'featured' => false],
            ['name' => 'engagement', 'featured' => false],
            ['name' => 'prewedding', 'featured' => false],
            ['name' => 'holud', 'featured' => false],
            ['name' => 'reception', 'featured' => false],
            ['name' => 'event', 'featured' => false],
            ['name' => 'eventphotography', 'featured' => false],
            ['name' => 'eventphotographer', 'featured' => false],
            ['name' => 'celebration', 'featured' => false],
            
            // Portrait & Studio
            ['name' => 'portrait', 'featured' => true],
            ['name' => 'portraitphotography', 'featured' => true],
            ['name' => 'portraits', 'featured' => false],
            ['name' => 'model', 'featured' => false],
            ['name' => 'modeling', 'featured' => false],
            ['name' => 'headshot', 'featured' => false],
            ['name' => 'studio', 'featured' => false],
            ['name' => 'studiophotography', 'featured' => false],
            
            // Fashion & Lifestyle
            ['name' => 'fashion', 'featured' => true],
            ['name' => 'fashionphotography', 'featured' => false],
            ['name' => 'fashionphotographer', 'featured' => false],
            ['name' => 'style', 'featured' => false],
            ['name' => 'lifestyle', 'featured' => false],
            ['name' => 'ootd', 'featured' => false],
            ['name' => 'lookoftheday', 'featured' => false],
            
            // Commercial & Product
            ['name' => 'product', 'featured' => true],
            ['name' => 'productphotography', 'featured' => false],
            ['name' => 'ecommerce', 'featured' => false],
            ['name' => 'food', 'featured' => true],
            ['name' => 'foodphotography', 'featured' => false],
            ['name' => 'foodblogger', 'featured' => false],
            ['name' => 'foodie', 'featured' => false],
            ['name' => 'instafood', 'featured' => false],
            ['name' => 'corporate', 'featured' => false],
            ['name' => 'businessphotography', 'featured' => false],
            
            // Nature & Landscape
            ['name' => 'landscape', 'featured' => true],
            ['name' => 'landscapephotography', 'featured' => true],
            ['name' => 'nature', 'featured' => true],
            ['name' => 'naturephotography', 'featured' => false],
            ['name' => 'sunset', 'featured' => false],
            ['name' => 'sunrise', 'featured' => false],
            ['name' => 'mountains', 'featured' => false],
            ['name' => 'mountain', 'featured' => false],
            ['name' => 'forest', 'featured' => false],
            ['name' => 'travel', 'featured' => false],
            ['name' => 'travelphotography', 'featured' => false],
            ['name' => 'wanderlust', 'featured' => false],
            ['name' => 'instatravel', 'featured' => false],
            
            // Wildlife & Animals
            ['name' => 'wildlife', 'featured' => true],
            ['name' => 'wildlifephotography', 'featured' => false],
            ['name' => 'animals', 'featured' => false],
            ['name' => 'animal', 'featured' => false],
            ['name' => 'birds', 'featured' => false],
            ['name' => 'birdwatching', 'featured' => false],
            ['name' => 'birdphotography', 'featured' => false],
            ['name' => 'pet', 'featured' => false],
            ['name' => 'petphotography', 'featured' => false],
            ['name' => 'dog', 'featured' => false],
            ['name' => 'cat', 'featured' => false],
            
            // Documentary & Artistic
            ['name' => 'documentary', 'featured' => true],
            ['name' => 'journalist', 'featured' => false],
            ['name' => 'photojournalist', 'featured' => false],
            ['name' => 'street', 'featured' => true],
            ['name' => 'streetphotography', 'featured' => false],
            ['name' => 'urban', 'featured' => false],
            ['name' => 'urbanphotography', 'featured' => false],
            ['name' => 'city', 'featured' => false],
            ['name' => 'cityscape', 'featured' => false],
            ['name' => 'architecture', 'featured' => false],
            ['name' => 'architecturephotography', 'featured' => false],
            
            // Specialty & Tech
            ['name' => 'drone', 'featured' => true],
            ['name' => 'dronephotography', 'featured' => false],
            ['name' => 'aerial', 'featured' => false],
            ['name' => 'aerialphotography', 'featured' => false],
            ['name' => 'macro', 'featured' => false],
            ['name' => 'macrophotography', 'featured' => false],
            ['name' => 'underwater', 'featured' => false],
            ['name' => 'underwaterphotography', 'featured' => false],
            ['name' => 'sports', 'featured' => false],
            ['name' => 'sportsphotography', 'featured' => false],
            ['name' => 'action', 'featured' => false],
            ['name' => 'cinematography', 'featured' => false],
            ['name' => 'videography', 'featured' => false],
            ['name' => 'film', 'featured' => false],
            
            // Bangladesh & Cities
            ['name' => 'bangladesh', 'featured' => true],
            ['name' => 'bangladeshi', 'featured' => false],
            ['name' => 'dhaka', 'featured' => true],
            ['name' => 'chattogram', 'featured' => false],
            ['name' => 'sylhet', 'featured' => false],
            ['name' => 'khulna', 'featured' => false],
            ['name' => 'rajshahi', 'featured' => false],
            ['name' => 'barisal', 'featured' => false],
            ['name' => 'rangpur', 'featured' => false],
            ['name' => 'mymensingh', 'featured' => false],
            ['name' => 'southasia', 'featured' => false],
            ['name' => 'indoasian', 'featured' => false],
            
            // Aesthetic & Style
            ['name' => 'aesthetic', 'featured' => false],
            ['name' => 'artistic', 'featured' => false],
            ['name' => 'art', 'featured' => false],
            ['name' => 'artphotography', 'featured' => false],
            ['name' => 'fineart', 'featured' => false],
            ['name' => 'minimalist', 'featured' => false],
            ['name' => 'noir', 'featured' => false],
            ['name' => 'bw', 'featured' => false],
            ['name' => 'blackandwhite', 'featured' => false],
            ['name' => 'monochrome', 'featured' => false],
            ['name' => 'color', 'featured' => false],
            ['name' => 'colorful', 'featured' => false],
            ['name' => 'vibrant', 'featured' => false],
            ['name' => 'creative', 'featured' => false],
            ['name' => 'composition', 'featured' => false],
            
            // Interaction & Community
            ['name' => 'instagood', 'featured' => false],
            ['name' => 'instamoment', 'featured' => false],
            ['name' => 'insta', 'featured' => false],
            ['name' => 'instadaily', 'featured' => false],
            ['name' => 'follow', 'featured' => false],
            ['name' => 'followme', 'featured' => false],
            ['name' => 'tagged', 'featured' => false],
            ['name' => 'tag', 'featured' => false],
            ['name' => 'repost', 'featured' => false],
            ['name' => 'share', 'featured' => false],
            ['name' => 'photocontest', 'featured' => false],
            ['name' => 'competition', 'featured' => false],
        ];

        Hashtag::truncate();
        $displayOrder = 1;

        foreach ($hashtags as $tag) {
            Hashtag::firstOrCreate(
                ['slug' => Str::slug($tag['name'])],
                [
                    'name' => $tag['name'],
                    'description' => ucfirst($tag['name']) . ' photography content',
                    'is_featured' => $tag['featured'],
                    'usage_count' => 0,
                ]
            );
            $displayOrder++;
        }

        $this->command->info('✓ Tags seeded: ' . count($hashtags) . ' tags');
    }
}
