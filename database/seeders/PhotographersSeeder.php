<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Photographer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PhotographersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👨‍🎨 Seeding photographers...');

        $photographers = [
            [
                'name' => 'Rahim Khan',
                'email' => 'rahim@example.com',
                'phone' => '01711111111',
                'city_id' => 1, // Dhaka
                'business_name' => 'Rahim Photography',
                'bio' => 'Professional wedding and event photographer based in Dhaka with 10+ years experience.',
                'specialties' => [1, 2, 3], // Wedding, Cinematography, Pre-wedding
            ],
            [
                'name' => 'Farida Akter',
                'email' => 'farida@example.com',
                'phone' => '01722222222',
                'city_id' => 1, // Dhaka
                'business_name' => 'Farida Studio',
                'bio' => 'Specialized in fashion and model photography. Creating stunning visuals since 2015.',
                'specialties' => [9, 10], // Fashion, Studio
            ],
            [
                'name' => 'Kamal Hossain',
                'email' => 'kamal@example.com',
                'phone' => '01733333333',
                'city_id' => 14, // Chittagong
                'business_name' => 'Kamal Captures',
                'bio' => 'Corporate and event photographer serving Chittagong and surrounding areas.',
                'specialties' => [5, 6], // Event, Corporate
            ],
            [
                'name' => 'Nasrin Islam',
                'email' => 'nasrin@example.com',
                'phone' => '01744444444',
                'city_id' => 1, // Dhaka
                'business_name' => 'Nasrin Wedding Films',
                'bio' => 'Award-winning cinematographer specializing in wedding films and documentaries.',
                'specialties' => [2, 11], // Cinematography, Travel
            ],
            [
                'name' => 'Rakib Ahmed',
                'email' => 'rakib@example.com',
                'phone' => '01755555555',
                'city_id' => 40, // Khulna
                'business_name' => 'Rakib Product Photography',
                'bio' => 'Expert in product and food photography for e-commerce and restaurants.',
                'specialties' => [7, 8], // Product, Food
            ],
            [
                'name' => 'Sadia Rahman',
                'email' => 'sadia@example.com',
                'phone' => '01766666666',
                'city_id' => 1, // Dhaka
                'business_name' => 'Sadia Holud Shots',
                'bio' => 'Specializing in traditional Bengali wedding ceremonies, especially Holud events.',
                'specialties' => [4, 1], // Holud, Wedding
            ],
            [
                'name' => 'Tariq Mahmud',
                'email' => 'tariq@example.com',
                'phone' => '01777777777',
                'city_id' => 55, // Rangpur
                'business_name' => 'Tariq Drone Services',
                'bio' => 'Professional drone photography and videography for events and real estate.',
                'specialties' => [12, 11], // Drone, Travel
            ],
            [
                'name' => 'Ayesha Begum',
                'email' => 'ayesha@example.com',
                'phone' => '01788888888',
                'city_id' => 50, // Rajshahi
                'business_name' => 'Ayesha Events',
                'bio' => 'Full-service event photography covering weddings, corporate events, and parties.',
                'specialties' => [5, 1, 6], // Event, Wedding, Corporate
            ],
        ];

        foreach ($photographers as $photographerData) {
            // Check if user already exists
            $user = User::where('email', $photographerData['email'])->first();
            
            if (!$user) {
                // Create user
                $user = User::create([
                    'uuid' => Str::uuid(),
                    'name' => $photographerData['name'],
                    'email' => $photographerData['email'],
                    'phone' => $photographerData['phone'],
                    'password' => Hash::make('password'),
                    'role' => 'photographer',
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'is_verified' => true,
                    'last_login_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            // Check if photographer profile exists
            $photographer = Photographer::where('user_id', $user->id)->first();
            
            if (!$photographer) {
                // Create photographer profile
                $photographer = Photographer::create([
                    'user_id' => $user->id,
                    'slug' => Str::slug($photographerData['business_name']),
                    'bio' => $photographerData['bio'],
                    'experience_years' => rand(3, 15),
                    'average_rating' => rand(40, 50) / 10, // 4.0 to 5.0
                    'rating_count' => rand(5, 50),
                    'total_bookings' => rand(10, 100),
                    'completed_bookings' => rand(5, 80),
                    'is_verified' => true,
                    'verification_type' => 'phone_email_id',
                    'verified_at' => now(),
                    'is_featured' => rand(0, 10) < 3, // 30% featured
                    'profile_completeness' => rand(70, 100),
                    'response_time_avg' => rand(2, 24), // 2-24 hours
                ]);

                // Attach categories
                DB::table('photographer_category')->insert(
                    array_map(fn($catId) => [
                        'photographer_id' => $photographer->id,
                        'category_id' => $catId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], $photographerData['specialties'])
                );

                $this->command->line("  ✓ {$photographerData['name']} ({$photographerData['business_name']})");
            } else {
                $this->command->line("  ⏭ {$photographerData['name']} (already exists)");
            }
        }

        $this->command->info('✅ ' . count($photographers) . ' photographers seeded');
    }
}
