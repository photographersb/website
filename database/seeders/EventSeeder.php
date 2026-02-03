<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventTicket;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $userId = \App\Models\User::where('email', 'admin@example.com')->first()?->id ?? 1;
        $categoryId = \App\Models\Category::first()?->id ?? 1;
        $cityId = \App\Models\City::first()?->id ?? 1;

        $events = [
            [
                'title' => 'Bangladesh Wedding Expo 2025',
                'slug' => 'bangladesh-wedding-expo',
                'description' => 'The largest wedding exhibition in South Asia featuring top photographers, designers, and vendors.',
                'location_text' => 'Dhaka International Convention City',
                'venue' => 'Dhaka International Convention City, Bashundhara',
                'city_id' => $cityId,
                'latitude' => 23.8441,
                'longitude' => 90.4152,
                'start_datetime' => now()->addDays(30),
                'end_datetime' => now()->addDays(32),
                'event_type' => 'paid',
                'base_price' => 500,
                'capacity' => 5000,
                'booking_close_datetime' => now()->addDays(28),
                'status' => 'published',
                'is_featured' => true,
                'category_id' => $categoryId,
                'organizer_id' => $userId,
                'created_by' => $userId,
                'refund_policy' => 'Full refund up to 48 hours before event',
            ],
            [
                'title' => 'Nature Photography Workshop',
                'slug' => 'nature-photography-workshop',
                'description' => 'Learn professional nature photography techniques from industry experts.',
                'location_text' => 'Sundarbans National Park',
                'venue' => 'Sundarbans Research Center',
                'city_id' => $cityId,
                'latitude' => 22.2945,
                'longitude' => 89.1811,
                'start_datetime' => now()->addDays(15),
                'end_datetime' => now()->addDays(17),
                'event_type' => 'paid',
                'base_price' => 3500,
                'capacity' => 30,
                'booking_close_datetime' => now()->addDays(10),
                'status' => 'published',
                'is_featured' => true,
                'category_id' => $categoryId,
                'organizer_id' => $userId,
                'created_by' => $userId,
                'refund_policy' => 'Non-refundable deposits',
            ],
            [
                'title' => 'Photographic Equipment Trade Show',
                'slug' => 'photo-equipment-trade-show',
                'description' => 'Latest camera equipment, lenses, and accessories from leading manufacturers.',
                'location_text' => 'Radisson Blu Dhaka',
                'venue' => 'Radisson Blu Dhaka, Banani',
                'city_id' => $cityId,
                'latitude' => 23.7937,
                'longitude' => 90.4149,
                'start_datetime' => now()->addDays(45),
                'end_datetime' => now()->addDays(46),
                'event_type' => 'free',
                'capacity' => 2000,
                'booking_close_datetime' => now()->addDays(44),
                'status' => 'published',
                'is_featured' => false,
                'category_id' => $categoryId,
                'organizer_id' => $userId,
                'created_by' => $userId,
            ],
            [
                'title' => 'Portrait Photography Masterclass',
                'slug' => 'portrait-photography-masterclass',
                'description' => 'Advanced portrait photography techniques, lighting, and posing guides.',
                'location_text' => 'Studio Lights Dhaka',
                'venue' => 'Studio Lights, Gulshan',
                'city_id' => $cityId,
                'latitude' => 23.8009,
                'longitude' => 90.4182,
                'start_datetime' => now()->addDays(20),
                'end_datetime' => now()->addDays(20),
                'event_type' => 'paid',
                'base_price' => 2500,
                'capacity' => 50,
                'booking_close_datetime' => now()->addDays(18),
                'status' => 'published',
                'is_featured' => true,
                'category_id' => $categoryId,
                'organizer_id' => $userId,
                'created_by' => $userId,
            ],
            [
                'title' => 'Annual Photographers Meet-up',
                'slug' => 'annual-photographers-meetup',
                'description' => 'Networking event for professional photographers to connect and collaborate.',
                'location_text' => 'Cafe Dhaka',
                'venue' => 'Cafe Dhaka, Dhanmondi',
                'city_id' => $cityId,
                'latitude' => 23.7642,
                'longitude' => 90.3675,
                'start_datetime' => now()->addDays(60),
                'end_datetime' => now()->addDays(60),
                'event_type' => 'free',
                'capacity' => 200,
                'booking_close_datetime' => now()->addDays(58),
                'status' => 'published',
                'is_featured' => false,
                'category_id' => $categoryId,
                'organizer_id' => $userId,
                'created_by' => $userId,
            ],
            [
                'title' => 'Digital Photo Editing Bootcamp',
                'slug' => 'digital-photo-editing-bootcamp',
                'description' => 'Intensive course on Lightroom, Photoshop, and modern editing techniques.',
                'location_text' => 'Tech Hub Dhaka',
                'venue' => 'Tech Hub, Mirpur',
                'city_id' => $cityId,
                'latitude' => 23.8056,
                'longitude' => 90.3603,
                'start_datetime' => now()->addDays(25),
                'end_datetime' => now()->addDays(27),
                'event_type' => 'paid',
                'base_price' => 1500,
                'capacity' => 40,
                'booking_close_datetime' => now()->addDays(23),
                'status' => 'published',
                'is_featured' => false,
                'category_id' => $categoryId,
                'organizer_id' => $userId,
                'created_by' => $userId,
            ],
        ];

        foreach ($events as $eventData) {
            $event = Event::create($eventData);

            // Create tickets for paid events
            if ($event->event_type === 'paid') {
                if ($event->slug === 'bangladesh-wedding-expo') {
                    // Multiple ticket types for wedding expo
                    EventTicket::create([
                        'event_id' => $event->id,
                        'title' => 'General Entry',
                        'price' => 500,
                        'quantity' => 3000,
                        'sales_start_datetime' => now(),
                        'sales_end_datetime' => $event->booking_close_datetime,
                        'is_active' => true,
                    ]);

                    EventTicket::create([
                        'event_id' => $event->id,
                        'title' => 'VIP Pass',
                        'price' => 1500,
                        'quantity' => 500,
                        'sales_start_datetime' => now(),
                        'sales_end_datetime' => $event->booking_close_datetime,
                        'is_active' => true,
                    ]);

                    EventTicket::create([
                        'event_id' => $event->id,
                        'title' => '3-Day Pass',
                        'price' => 1000,
                        'quantity' => 1000,
                        'sales_start_datetime' => now(),
                        'sales_end_datetime' => $event->booking_close_datetime,
                        'is_active' => true,
                    ]);
                } else {
                    // Single ticket type for other paid events
                    EventTicket::create([
                        'event_id' => $event->id,
                        'title' => 'Participant',
                        'price' => $event->base_price,
                        'quantity' => $event->capacity ?? 100,
                        'sales_start_datetime' => now(),
                        'sales_end_datetime' => $event->booking_close_datetime,
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('Events seeded successfully!');
    }
}
