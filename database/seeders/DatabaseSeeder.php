<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Photographer;
use App\Models\Category;
use App\Models\City;
use App\Models\Album;
use App\Models\Package;
use App\Models\Event;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\CompetitionVote;
use App\Models\CompetitionJudge;
use App\Models\CompetitionScore;
use App\Models\CompetitionCategory;
use App\Models\CompetitionSponsor;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        echo "🇧🇩 Starting Bangladesh-based comprehensive seeding...\n\n";

        // All 64 Districts of Bangladesh
        $bangladeshCities = [
            // Dhaka Division
            ['name' => 'Dhaka', 'slug' => 'dhaka', 'division' => 'Dhaka', 'photographer_count' => 150],
            ['name' => 'Gazipur', 'slug' => 'gazipur', 'division' => 'Dhaka', 'photographer_count' => 35],
            ['name' => 'Narayanganj', 'slug' => 'narayanganj', 'division' => 'Dhaka', 'photographer_count' => 30],
            ['name' => 'Tangail', 'slug' => 'tangail', 'division' => 'Dhaka', 'photographer_count' => 20],
            ['name' => 'Munshiganj', 'slug' => 'munshiganj', 'division' => 'Dhaka', 'photographer_count' => 15],
            ['name' => 'Manikganj', 'slug' => 'manikganj', 'division' => 'Dhaka', 'photographer_count' => 12],
            ['name' => 'Narsingdi', 'slug' => 'narsingdi', 'division' => 'Dhaka', 'photographer_count' => 15],
            ['name' => 'Kishoreganj', 'slug' => 'kishoreganj', 'division' => 'Dhaka', 'photographer_count' => 18],
            ['name' => 'Gopalganj', 'slug' => 'gopalganj', 'division' => 'Dhaka', 'photographer_count' => 10],
            ['name' => 'Faridpur', 'slug' => 'faridpur', 'division' => 'Dhaka', 'photographer_count' => 15],
            ['name' => 'Madaripur', 'slug' => 'madaripur', 'division' => 'Dhaka', 'photographer_count' => 8],
            ['name' => 'Rajbari', 'slug' => 'rajbari', 'division' => 'Dhaka', 'photographer_count' => 10],
            ['name' => 'Shariatpur', 'slug' => 'shariatpur', 'division' => 'Dhaka', 'photographer_count' => 8],
            
            // Chittagong Division
            ['name' => 'Chittagong', 'slug' => 'chittagong', 'division' => 'Chittagong', 'photographer_count' => 80],
            ['name' => 'Cox\'s Bazar', 'slug' => 'coxs-bazar', 'division' => 'Chittagong', 'photographer_count' => 40],
            ['name' => 'Comilla', 'slug' => 'comilla', 'division' => 'Chittagong', 'photographer_count' => 25],
            ['name' => 'Feni', 'slug' => 'feni', 'division' => 'Chittagong', 'photographer_count' => 15],
            ['name' => 'Brahmanbaria', 'slug' => 'brahmanbaria', 'division' => 'Chittagong', 'photographer_count' => 18],
            ['name' => 'Rangamati', 'slug' => 'rangamati', 'division' => 'Chittagong', 'photographer_count' => 20],
            ['name' => 'Bandarban', 'slug' => 'bandarban', 'division' => 'Chittagong', 'photographer_count' => 25],
            ['name' => 'Khagrachhari', 'slug' => 'khagrachhari', 'division' => 'Chittagong', 'photographer_count' => 15],
            ['name' => 'Noakhali', 'slug' => 'noakhali', 'division' => 'Chittagong', 'photographer_count' => 20],
            ['name' => 'Lakshmipur', 'slug' => 'lakshmipur', 'division' => 'Chittagong', 'photographer_count' => 12],
            ['name' => 'Chandpur', 'slug' => 'chandpur', 'division' => 'Chittagong', 'photographer_count' => 15],
            
            // Rajshahi Division
            ['name' => 'Rajshahi', 'slug' => 'rajshahi', 'division' => 'Rajshahi', 'photographer_count' => 35],
            ['name' => 'Bogra', 'slug' => 'bogra', 'division' => 'Rajshahi', 'photographer_count' => 25],
            ['name' => 'Pabna', 'slug' => 'pabna', 'division' => 'Rajshahi', 'photographer_count' => 18],
            ['name' => 'Natore', 'slug' => 'natore', 'division' => 'Rajshahi', 'photographer_count' => 12],
            ['name' => 'Sirajganj', 'slug' => 'sirajganj', 'division' => 'Rajshahi', 'photographer_count' => 15],
            ['name' => 'Naogaon', 'slug' => 'naogaon', 'division' => 'Rajshahi', 'photographer_count' => 12],
            ['name' => 'Joypurhat', 'slug' => 'joypurhat', 'division' => 'Rajshahi', 'photographer_count' => 8],
            ['name' => 'Chapainawabganj', 'slug' => 'chapainawabganj', 'division' => 'Rajshahi', 'photographer_count' => 10],
            
            // Khulna Division
            ['name' => 'Khulna', 'slug' => 'khulna', 'division' => 'Khulna', 'photographer_count' => 40],
            ['name' => 'Jessore', 'slug' => 'jessore', 'division' => 'Khulna', 'photographer_count' => 25],
            ['name' => 'Satkhira', 'slug' => 'satkhira', 'division' => 'Khulna', 'photographer_count' => 18],
            ['name' => 'Bagerhat', 'slug' => 'bagerhat', 'division' => 'Khulna', 'photographer_count' => 15],
            ['name' => 'Jhenaidah', 'slug' => 'jhenaidah', 'division' => 'Khulna', 'photographer_count' => 12],
            ['name' => 'Magura', 'slug' => 'magura', 'division' => 'Khulna', 'photographer_count' => 10],
            ['name' => 'Kushtia', 'slug' => 'kushtia', 'division' => 'Khulna', 'photographer_count' => 18],
            ['name' => 'Chuadanga', 'slug' => 'chuadanga', 'division' => 'Khulna', 'photographer_count' => 8],
            ['name' => 'Meherpur', 'slug' => 'meherpur', 'division' => 'Khulna', 'photographer_count' => 8],
            ['name' => 'Narail', 'slug' => 'narail', 'division' => 'Khulna', 'photographer_count' => 10],
            
            // Barisal Division
            ['name' => 'Barisal', 'slug' => 'barisal', 'division' => 'Barisal', 'photographer_count' => 25],
            ['name' => 'Patuakhali', 'slug' => 'patuakhali', 'division' => 'Barisal', 'photographer_count' => 15],
            ['name' => 'Bhola', 'slug' => 'bhola', 'division' => 'Barisal', 'photographer_count' => 12],
            ['name' => 'Pirojpur', 'slug' => 'pirojpur', 'division' => 'Barisal', 'photographer_count' => 10],
            ['name' => 'Jhalokathi', 'slug' => 'jhalokathi', 'division' => 'Barisal', 'photographer_count' => 8],
            ['name' => 'Barguna', 'slug' => 'barguna', 'division' => 'Barisal', 'photographer_count' => 10],
            
            // Sylhet Division
            ['name' => 'Sylhet', 'slug' => 'sylhet', 'division' => 'Sylhet', 'photographer_count' => 45],
            ['name' => 'Moulvibazar', 'slug' => 'moulvibazar', 'division' => 'Sylhet', 'photographer_count' => 18],
            ['name' => 'Habiganj', 'slug' => 'habiganj', 'division' => 'Sylhet', 'photographer_count' => 15],
            ['name' => 'Sunamganj', 'slug' => 'sunamganj', 'division' => 'Sylhet', 'photographer_count' => 12],
            
            // Rangpur Division
            ['name' => 'Rangpur', 'slug' => 'rangpur', 'division' => 'Rangpur', 'photographer_count' => 30],
            ['name' => 'Dinajpur', 'slug' => 'dinajpur', 'division' => 'Rangpur', 'photographer_count' => 20],
            ['name' => 'Thakurgaon', 'slug' => 'thakurgaon', 'division' => 'Rangpur', 'photographer_count' => 12],
            ['name' => 'Panchagarh', 'slug' => 'panchagarh', 'division' => 'Rangpur', 'photographer_count' => 10],
            ['name' => 'Nilphamari', 'slug' => 'nilphamari', 'division' => 'Rangpur', 'photographer_count' => 12],
            ['name' => 'Lalmonirhat', 'slug' => 'lalmonirhat', 'division' => 'Rangpur', 'photographer_count' => 10],
            ['name' => 'Kurigram', 'slug' => 'kurigram', 'division' => 'Rangpur', 'photographer_count' => 10],
            ['name' => 'Gaibandha', 'slug' => 'gaibandha', 'division' => 'Rangpur', 'photographer_count' => 12],
            
            // Mymensingh Division
            ['name' => 'Mymensingh', 'slug' => 'mymensingh', 'division' => 'Mymensingh', 'photographer_count' => 28],
            ['name' => 'Jamalpur', 'slug' => 'jamalpur', 'division' => 'Mymensingh', 'photographer_count' => 15],
            ['name' => 'Netrokona', 'slug' => 'netrokona', 'division' => 'Mymensingh', 'photographer_count' => 12],
            ['name' => 'Sherpur', 'slug' => 'sherpur', 'division' => 'Mymensingh', 'photographer_count' => 10],
        ];

        $cities = [];
        foreach ($bangladeshCities as $cityData) {
            $cities[] = City::create([
                'name' => $cityData['name'],
                'slug' => $cityData['slug'],
                'division' => $cityData['division'],
                'photographer_count' => $cityData['photographer_count'],
                'display_order' => count($cities) + 1,
            ]);
        }
        echo "✓ Created " . count($cities) . " Bangladesh cities\n";

        // Photography Categories
        $categoryData = [
            ['name' => 'Wedding Photography', 'icon' => '💍'],
            ['name' => 'Event Photography', 'icon' => '🎉'],
            ['name' => 'Portrait Photography', 'icon' => '👤'],
            ['name' => 'Product Photography', 'icon' => '📦'],
            ['name' => 'Food Photography', 'icon' => '🍽️'],
            ['name' => 'Fashion Photography', 'icon' => '👗'],
            ['name' => 'Real Estate Photography', 'icon' => '🏠'],
            ['name' => 'Drone Photography', 'icon' => '🚁'],
            ['name' => 'Wildlife Photography', 'icon' => '🦁'],
            ['name' => 'Studio Photography', 'icon' => '📸'],
        ];

        $categories = [];
        foreach ($categoryData as $index => $cat) {
            $categories[] = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => "Professional {$cat['name']} services across Bangladesh",
                'icon' => $cat['icon'],
                'photographer_count' => rand(15, 50),
                'booking_count' => rand(30, 150),
                'display_order' => $index + 1,
                'is_active' => true,
            ]);
        }
        echo "✓ Created " . count($categories) . " photography categories\n";

        // Create Admin
        $admin = User::create([
            'uuid' => Str::uuid(),
            'name' => 'Admin Rahman',
            'email' => 'admin@photographar.com',
            'phone' => '+8801711111111',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
        echo "✓ Created admin user\n";

        // Bangladesh Photographer Names
        $bangladeshiNames = [
            'Kamal Hossain', 'Rahim Ahmed', 'Shakib Khan', 'Tamim Iqbal',
            'Mushfiqur Rahman', 'Mahmudullah Riyad', 'Sakib Al Hasan', 'Mustafizur Rahman',
            'Imrul Kayes', 'Sabbir Rahman', 'Liton Das', 'Soumya Sarkar',
            'Rubel Hossain', 'Taskin Ahmed', 'Mehidy Hasan', 'Mominul Haque',
            'Nazmul Islam', 'Taijul Islam', 'Abu Hider', 'Khaled Ahmed',
        ];

        $photographers = [];
        foreach ($bangladeshiNames as $index => $name) {
            $city = $cities[array_rand($cities)];
            $slug = Str::slug($name);
            
            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@photographar.com',
                'phone' => '+880171' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
                'role' => 'photographer',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

            $photographer = Photographer::create([
                'user_id' => $user->id,
                'slug' => $slug,
                'profile_picture' => 'profiles/photographer_' . ($index + 1) . '.jpg',
                'bio' => "Professional photographer based in {$city->name}, Bangladesh. Specializing in capturing moments that last a lifetime. " . ($index + 2) . " years of experience.",
                'experience_years' => $index + 2,
                'specializations' => json_encode(array_slice(array_column($categoryData, 'name'), 0, rand(2, 4))),
                'service_area_radius' => rand(10, 100),
                'average_rating' => round(rand(38, 50) / 10, 1),
                'rating_count' => rand(5, 100),
                'is_verified' => $index < 15,
                'verified_at' => $index < 15 ? now()->subDays(rand(30, 365)) : null,
                'profile_completeness' => rand(75, 100),
                'total_bookings' => rand(10, 150),
                'completed_bookings' => rand(8, 130),
                'response_time_avg' => rand(1, 12),
                'facebook_url' => rand(0, 10) < 8 ? "https://facebook.com/" . strtolower(str_replace(' ', '', $name)) : null,
                'instagram_url' => rand(0, 10) < 9 ? "https://instagram.com/" . strtolower(str_replace(' ', '', $name)) : null,
                'twitter_url' => rand(0, 10) < 5 ? "https://twitter.com/" . strtolower(str_replace(' ', '', $name)) : null,
                'linkedin_url' => rand(0, 10) < 6 ? "https://linkedin.com/in/" . strtolower(str_replace(' ', '', $name)) : null,
                'youtube_url' => rand(0, 10) < 4 ? "https://youtube.com/@" . strtolower(str_replace(' ', '', $name)) : null,
                'website_url' => rand(0, 10) < 7 ? "https://" . strtolower(str_replace(' ', '', $name)) . ".com" : null,
            ]);

            // Assign random categories
            $selectedCategories = collect($categories)->random(rand(2, 5));
            $photographer->categories()->attach($selectedCategories->pluck('id')->toArray());

            // Create albums
            for ($i = 1; $i <= rand(2, 5); $i++) {
                Album::create([
                    'photographer_id' => $photographer->id,
                    'name' => $selectedCategories[$i % count($selectedCategories)]->name . " Collection",
                    'slug' => $slug . "-album-$i",
                    'description' => "Stunning collection of " . strtolower($selectedCategories[$i % count($selectedCategories)]->name),
                    'cover_photo_url' => "https://picsum.photos/800/600?random=" . ($index * 10 + $i),
                    'category_id' => $selectedCategories[$i % count($selectedCategories)]->id,
                    'is_public' => true,
                    'photo_count' => rand(15, 50),
                    'view_count' => rand(200, 5000),
                    'display_order' => $i,
                ]);
            }

            // Create packages
            $packageTypes = ['Basic', 'Standard', 'Premium'];
            foreach ($packageTypes as $i => $type) {
                Package::create([
                    'photographer_id' => $photographer->id,
                    'name' => "$type Package",
                    'description' => "$type photography package for your special moments",
                    'category' => $selectedCategories[0]->name,
                    'base_price' => ($i + 1) * rand(5000, 15000),
                    'duration_unit' => 'hours',
                    'duration_value' => ($i + 1) * 4,
                    'includes' => json_encode(['High-res Digital Photos', 'Online Gallery', 'Basic Editing', ($i > 0 ? 'Photo Album' : null), ($i > 1 ? 'Video Highlights' : null)]),
                    'excludes' => json_encode(['Travel Cost', 'Accommodation']),
                    'add_ons' => json_encode(['Extra Hour' => 2000, 'Additional Album' => 3000, 'Drone Shots' => 5000]),
                    'travel_cost_type' => 'per_km',
                    'travel_cost_value' => 50,
                    'advance_booking_days' => 7,
                    'is_active' => true,
                    'display_order' => $i + 1,
                ]);
            }

            $photographers[] = $photographer;
        }
        echo "✓ Created " . count($photographers) . " Bangladesh-based photographers\n";

        // Create Clients
        $clientNames = [
            'Fahim Ahmed', 'Nusrat Jahan', 'Rafiq Uddin', 'Ayesha Siddiqua',
            'Jamal Hossain', 'Farhana Islam', 'Tanvir Rahman', 'Sadia Karim',
            'Imran Khan', 'Nadia Sultana', 'Arif Mahmud', 'Rupa Begum',
            'Habib Ullah', 'Shirin Akter', 'Milon Das',
        ];

        $clients = [];
        foreach ($clientNames as $index => $name) {
            $clients[] = User::create([
                'uuid' => Str::uuid(),
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@gmail.com',
                'phone' => '+880181' . str_pad($index + 1, 7, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
                'role' => 'client',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
        }
        echo "✓ Created " . count($clients) . " client users\n";
        echo "✓ Created " . count($clients) . " client users\n";

        // Create Bookings and Reviews
        $bookingCount = 0;
        $reviewCount = 0;
        foreach ($photographers as $photographer) {
            $numBookings = rand(2, 8);
            for ($i = 0; $i < $numBookings; $i++) {
                $client = $clients[array_rand($clients)];
                $packages = Package::where('photographer_id', $photographer->id)->get();
                if ($packages->isEmpty()) continue;
                
                $package = $packages->random();
                $status = ['pending_payment', 'confirmed', 'completed', 'cancelled'][rand(0, 3)];
                
                // Create inquiry first
                $inquiry = \App\Models\Inquiry::create([
                    'uuid' => Str::uuid(),
                    'client_id' => $client->id,
                    'photographer_id' => $photographer->id,
                    'package_id' => $package->id,
                    'event_date' => now()->addDays(rand(1, 90)),
                    'event_location' => $cities[array_rand($cities)]->name . ', Bangladesh',
                    'requirements' => 'Professional photography services needed',
                    'status' => 'accepted',
                ]);
                
                // Create quote
                $quoteAmount = $package->base_price + rand(0, 10000);
                $quote = \App\Models\Quote::create([
                    'uuid' => Str::uuid(),
                    'inquiry_id' => $inquiry->id,
                    'photographer_id' => $photographer->id,
                    'package_id' => $package->id,
                    'base_price' => $package->base_price,
                    'total_amount' => $quoteAmount,
                    'validity_days' => 7,
                    'deposit_percentage' => 50,
                    'status' => 'accepted',
                    'accepted_at' => now()->subDays(rand(1, 30)),
                ]);
                
                $booking = Booking::create([
                    'uuid' => Str::uuid(),
                    'inquiry_id' => $inquiry->id,
                    'quote_id' => $quote->id,
                    'client_id' => $client->id,
                    'photographer_id' => $photographer->id,
                    'package_id' => $package->id,
                    'event_date' => $inquiry->event_date,
                    'event_start_time' => '10:00:00',
                    'total_amount' => $quoteAmount,
                    'status' => $status,
                    'payment_status' => $status === 'completed' ? 'completed' : 'pending',
                    'confirmed_at' => in_array($status, ['confirmed', 'completed']) ? now()->subDays(rand(1, 30)) : null,
                    'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 15)) : null,
                ]);
                $bookingCount++;

                // Create review for completed bookings
                if ($status === 'completed' && rand(0, 1)) {
                    Review::create([
                        'uuid' => Str::uuid(),
                        'booking_id' => $booking->id,
                        'reviewer_id' => $client->id,
                        'photographer_id' => $photographer->id,
                        'rating' => rand(4, 5),
                        'comment' => ['Amazing work! Highly recommended.', 'Great photographer, very professional.', 'Loved the photos! Worth every penny.', 'Excellent service and quality.'][rand(0, 3)],
                        'status' => 'published',
                        'published_at' => now()->subDays(rand(1, 10)),
                    ]);
                    $reviewCount++;
                }
            }
        }
        echo "✓ Created $bookingCount bookings and $reviewCount reviews\n";

        // Create Events
        $eventData = [
            ['name' => 'Dhaka Photography Festival 2026', 'city' => 'Dhaka', 'type' => 'exhibition'],
            ['name' => 'Bangladesh Wedding Expo', 'city' => 'Dhaka', 'type' => 'exhibition'],
            ['name' => 'Chittagong Photo Walk', 'city' => 'Chittagong', 'type' => 'meetup'],
            ['name' => 'Sylhet Nature Photography Tour', 'city' => 'Sylhet', 'type' => 'seminar'],
            ['name' => 'Portrait Masterclass - Dhaka', 'city' => 'Dhaka', 'type' => 'workshop'],
            ['name' => 'Cox\'s Bazar Sunset Photo Session', 'city' => 'Cox\'s Bazar', 'type' => 'meetup'],
            ['name' => 'Rajshahi Heritage Photography', 'city' => 'Rajshahi', 'type' => 'seminar'],
            ['name' => 'Product Photography Workshop', 'city' => 'Dhaka', 'type' => 'workshop'],
        ];

        $events = [];
        foreach ($eventData as $index => $eventInfo) {
            $organizer = $photographers[array_rand($photographers)];
            $city = collect($cities)->firstWhere('name', $eventInfo['city']) ?? $cities[0];
            
            $event = Event::create([
                'uuid' => Str::uuid(),
                'title' => $eventInfo['name'],
                'slug' => Str::slug($eventInfo['name']),
                'description' => "Join us for an amazing {$eventInfo['type']} event in {$eventInfo['city']}! Learn from professionals and network with fellow photographers.",
                'event_type' => $eventInfo['type'],
                'organizer_id' => $organizer->id,
                'event_date' => now()->addDays(rand(10, 90)),
                'start_time' => '10:00:00',
                'end_time' => '17:00:00',
                'location' => ['Radisson Blu', 'InterContinental', 'Pan Pacific Sonargaon', 'Westin Dhaka'][rand(0, 3)],
                'address' => $city->name . ', Bangladesh',
                'city_id' => $city->id,
                'max_attendees' => rand(50, 200),
                'is_featured' => $index < 3,
                'hero_image_url' => "https://picsum.photos/1200/400?random=" . ($index + 100),
                'status' => 'published',
            ]);
            $events[] = $event;
        }
        echo "✓ Created " . count($events) . " photography events\n";

        // Create Competitions
        $competitionData = [
            ['title' => 'Bangladesh Wildlife Photography Competition 2026', 'category' => 'Wildlife Photography', 'prize' => 50000],
            ['title' => 'Best Wedding Photographer Bangladesh', 'category' => 'Wedding Photography', 'prize' => 100000],
            ['title' => 'Street Photography Contest Dhaka', 'category' => 'Portrait Photography', 'prize' => 30000],
            ['title' => 'Landscape Photography of Bangladesh', 'category' => 'Drone Photography', 'prize' => 75000],
            ['title' => 'Fashion Photography Awards', 'category' => 'Fashion Photography', 'prize' => 60000],
        ];

        $competitions = [];
        foreach ($competitionData as $index => $compData) {
            $category = collect($categories)->firstWhere('name', $compData['category']) ?? $categories[0];
            $adminUser = User::where('role', 'super_admin')->first();
            
            $competition = Competition::create([
                'uuid' => Str::uuid(),
                'admin_id' => $adminUser->id,
                'title' => $compData['title'],
                'slug' => Str::slug($compData['title']),
                'description' => "Showcase your best work and compete with top photographers across Bangladesh. Amazing prizes and recognition await!",
                'submission_deadline' => now()->addDays(30),
                'voting_start_at' => now()->addDays(31),
                'voting_end_at' => now()->addDays(60),
                'total_prize_pool' => $compData['prize'],
                'max_submissions_per_user' => 3,
                'status' => 'active',
                'banner_image' => "https://picsum.photos/1200/400?random=" . ($index + 200),
            ]);
            $competitions[] = $competition;

            // Create competition categories
            $catNames = ['Professional', 'Amateur', 'Mobile Photography'];
            foreach ($catNames as $catIndex => $catName) {
                CompetitionCategory::create([
                    'competition_id' => $competition->id,
                    'name' => $catName,
                    'description' => "$catName category for {$competition->title}",
                    'prize_amount' => $compData['prize'] / 3,
                    'max_submissions_per_user' => 1,
                    'is_active' => true,
                ]);
            }

            // Create sponsors
            $sponsors = [
                ['name' => 'Canon Bangladesh', 'tier' => 'platinum', 'amount' => 50000],
                ['name' => 'Sony Bangladesh', 'tier' => 'gold', 'amount' => 30000],
                ['name' => 'Nikon Bangladesh', 'tier' => 'silver', 'amount' => 20000],
                ['name' => 'DJI Bangladesh', 'tier' => 'bronze', 'amount' => 10000],
            ];

            foreach ($sponsors as $sponsorIndex => $sponsorData) {
                CompetitionSponsor::create([
                    'competition_id' => $competition->id,
                    'name' => $sponsorData['name'],
                    'logo_url' => "https://via.placeholder.com/200x100?text=" . urlencode($sponsorData['name']),
                    'website_url' => 'https://' . Str::slug($sponsorData['name']) . '.com',
                    'description' => 'Leading photography equipment provider in Bangladesh',
                    'tier' => $sponsorData['tier'],
                    'contribution_amount' => $sponsorData['amount'],
                    'display_order' => $sponsorIndex + 1,
                    'is_active' => true,
                ]);
            }
        }
        echo "✓ Created " . count($competitions) . " competitions with categories and sponsors\n";

        // Create Competition Submissions
        $submissionCount = 0;
        foreach ($competitions as $competition) {
            $compCategories = CompetitionCategory::where('competition_id', $competition->id)->get();
            
            $numSubmissions = rand(15, min(20, count($photographers)));  // Ensure we don't exceed photographer count
            $usedPhotographers = [];
            
            for ($i = 0; $i < $numSubmissions; $i++) {
                // Get a photographer that hasn't submitted to this competition yet
                $availablePhotographers = array_filter($photographers, function($p) use ($usedPhotographers) {
                    return !in_array($p->id, $usedPhotographers);
                });
                
                if (empty($availablePhotographers)) break;  // No more photographers available
                
                $photographer = $availablePhotographers[array_rand($availablePhotographers)];
                $usedPhotographers[] = $photographer->id;
                
                $submission = CompetitionSubmission::create([
                    'uuid' => Str::uuid(),
                    'competition_id' => $competition->id,
                    'photographer_id' => $photographer->id,
                    'image_path' => "submissions/photo_" . ($i + $competition->id * 100) . ".jpg",
                    'category_id' => null,  // References global categories, not competition_categories
                    'image_url' => "https://picsum.photos/1200/800?random=" . ($i + $competition->id * 100),
                    'thumbnail_url' => "https://picsum.photos/400/300?random=" . ($i + $competition->id * 100),
                    'title' => ['Golden Hour', 'Monsoon Magic', 'Urban Life', 'Village Beauty', 'Heritage Pride', 'Natural Wonder'][rand(0, 5)] . " " . ($i + 1),
                    'description' => 'Captured in ' . $cities[array_rand($cities)]->name . ', Bangladesh. A moment frozen in time.',
                    'status' => ['approved', 'approved', 'approved', 'pending_review'][rand(0, 3)],
                    'vote_count' => rand(0, 500),
                ]);
                $submissionCount++;

                // Add some votes
                if ($submission->status === 'approved') {
                    $numVotes = rand(5, 50);
                    for ($v = 0; $v < $numVotes; $v++) {
                        try {
                            CompetitionVote::create([
                                'submission_id' => $submission->id,
                                'competition_id' => $competition->id,
                                'voter_id' => $clients[array_rand($clients)]->id,
                                'ip_address' => '103.' . rand(10, 250) . '.' . rand(10, 250) . '.' . rand(10, 250),
                                'voted_at' => now()->subDays(rand(1, 20)),
                            ]);
                        } catch (\Exception $e) {
                            // Skip duplicate votes
                        }
                    }
                }
            }
        }
        echo "✓ Created $submissionCount competition submissions with votes\n";

        // Assign Judges
        $judgeCount = 0;
        foreach ($competitions as $competition) {
            $judgePhotographers = collect($photographers)->random(rand(3, 5));
            foreach ($judgePhotographers as $judge) {
                CompetitionJudge::create([
                    'competition_id' => $competition->id,
                    'judge_id' => $judge->user_id,
                    'assigned_at' => now()->subDays(rand(5, 30)),
                ]);
                $judgeCount++;

                // Add some judge scores
                $submissions = CompetitionSubmission::where('competition_id', $competition->id)
                    ->where('status', 'approved')
                    ->inRandomOrder()
                    ->limit(rand(5, 15))
                    ->get();

                foreach ($submissions as $submission) {
                    CompetitionScore::create([
                        'competition_id' => $competition->id,
                        'submission_id' => $submission->id,
                        'judge_id' => $judge->user_id,
                        'technical_score' => rand(7, 10),
                        'creativity_score' => rand(6, 10),
                        'composition_score' => rand(7, 10),
                        'story_score' => rand(6, 10),
                        'impact_score' => rand(7, 10),
                        'total_score' => rand(35, 50),
                        'feedback' => 'Excellent work! Great composition and lighting.',
                        'status' => 'completed',
                        'scored_at' => now()->subDays(rand(1, 20)),
                    ]);
                }
            }
        }
        echo "✓ Assigned $judgeCount judges and added their scores\n";

        // Create Transactions
        $transactionCount = 0;
        $bookings = Booking::where('status', 'completed')->get();
        foreach ($bookings as $booking) {
            Transaction::create([
                'uuid' => Str::uuid(),
                'user_id' => $booking->client_id,
                'transaction_type' => 'booking',
                'reference_id' => (string) $booking->id,
                'reference_table' => 'bookings',
                'amount' => $booking->total_amount,
                'net_amount' => $booking->total_amount * 0.95,  // 5% platform fee
                'platform_fee' => $booking->total_amount * 0.05,
                'payment_method' => ['bkash', 'nagad', 'card', 'bank_transfer'][rand(0, 3)],
                'gateway_reference' => 'TXN' . strtoupper(Str::random(12)),
                'status' => 'completed',
                'payment_date' => now()->subDays(rand(1, 60)),
            ]);
            $transactionCount++;
        }
        echo "✓ Created $transactionCount payment transactions\n";

        echo "\n🎉 Bangladesh-based seeding completed successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📧 Admin Login:\n";
        echo "   Email: admin@photographar.com\n";
        echo "   Password: password123\n\n";
        echo "📸 Sample Photographer:\n";
        echo "   Email: kamal.hossain@photographar.com\n";
        echo "   Password: password123\n\n";
        echo "👤 Sample Client:\n";
        echo "   Email: fahim.ahmed@gmail.com\n";
        echo "   Password: password123\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📊 Data Summary:\n";
        echo "   • " . count($cities) . " Bangladesh cities\n";
        echo "   • " . count($categories) . " photography categories\n";
        echo "   • " . count($photographers) . " photographers\n";
        echo "   • " . count($clients) . " clients\n";
        echo "   • $bookingCount bookings\n";
        echo "   • $reviewCount reviews\n";
        echo "   • " . count($events) . " events\n";
        echo "   • " . count($competitions) . " competitions\n";
        echo "   • $submissionCount competition submissions\n";
        echo "   • $judgeCount judges assigned\n";
        echo "   • $transactionCount transactions\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }
}
