<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Photographer;
use App\Models\User;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Support\Str;

class SeedShowcasePhotographers extends Command
{
    protected $signature = 'sb:seed-showcase-photographers {--count=50 : Number of showcase photographers to create}';
    protected $description = 'Seed featured photographers across Bangladesh categories and locations';

    public function handle()
    {
        $count = $this->option('count');
        $locations = Location::where('type', 'district')->get();
        $categories = Category::whereIn('name', [
            'Wedding Photography',
            'Portrait Photography',
            'Event Photography',
            'Product Photography',
            'Fashion Photography',
            'Documentary Photography',
            'Photo Journalist',
            'Drone Photography',
        ])->get();

        if ($locations->isEmpty() || $categories->isEmpty()) {
            $this->error('❌ Locations or Categories not found. Run sb:seed-bd-core first.');
            return 1;
        }

        $this->info("Creating $count showcase photographers...");

        $showcaseNames = [
            ['first' => 'Rajib', 'last' => 'Khan', 'bio' => 'Award-winning wedding photographer'],
            ['first' => 'Nadia', 'last' => 'Ahmed', 'bio' => 'Fashion & lifestyle photography specialist'],
            ['first' => 'Karim', 'last' => 'Hassan', 'bio' => 'Documentary photographer & photojournalist'],
            ['first' => 'Sadia', 'last' => 'Nasrin', 'bio' => 'Portrait & studio photography expert'],
            ['first' => 'Arif', 'last' => 'Shaheen', 'bio' => 'Event photographer with 10+ years experience'],
            ['first' => 'Mina', 'last' => 'Begum', 'bio' => 'Product photography for e-commerce'],
            ['first' => 'Fahim', 'last' => 'Rahman', 'bio' => 'Drone & aerial photography'],
            ['first' => 'Zara', 'last' => 'Malik', 'bio' => 'Travel & nature photography'],
            ['first' => 'Omar', 'last' => 'Hossain', 'bio' => 'Corporate & commercial photography'],
            ['first' => 'Asha', 'last' => 'Dey', 'bio' => 'Bridal & engagement specialist'],
        ];

        $specializations = [
            'wedding', 'portrait', 'event', 'product', 'fashion',
            'documentary', 'nature', 'corporate', 'drone', 'cinematography'
        ];

        for ($i = 0; $i < $count; $i++) {
            $nameData = $showcaseNames[$i % count($showcaseNames)];
            $username = Str::slug($nameData['first'] . '-' . $nameData['last'] . '-' . ($i + 1));
            $email = $username . '@photographersb.com';

            // Create user if doesn't exist
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nameData['first'] . ' ' . $nameData['last'],
                    'username' => $username,
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            // Create photographer
            $location = $locations->random();
            $mainCategory = $categories->random();

            Photographer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'city_id' => $location->id,
                    'slug' => $username,
                    'bio' => $nameData['bio'],
                    'experience_years' => rand(2, 15),
                    'specializations' => [
                        $mainCategory->name,
                        $specializations[array_rand($specializations)],
                    ],
                    'is_verified' => true,
                    'verified_at' => now()->subDays(rand(1, 100)),
                    'is_featured' => $i < 10,
                    'featured_until' => $i < 10 ? now()->addMonths(3) : null,
                    'profile_completeness' => rand(70, 100),
                    'average_rating' => number_format(rand(40, 50) / 10, 1),
                    'rating_count' => rand(5, 100),
                    'total_bookings' => rand(5, 50),
                    'completed_bookings' => rand(3, 50),
                    'response_time_avg' => rand(1, 24),
                ]
            );

            if (($i + 1) % 10 === 0) {
                $this->line("  ✓ Created $i / $count photographers");
            }
        }

        $this->info("✅ $count showcase photographers created!");
        return 0;
    }
}
