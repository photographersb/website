<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        $eventDate = $this->faker->dateTimeBetween('+1 day', '+60 days');
        $type = $this->faker->randomElement(['workshop', 'seminar', 'meetup', 'conference']);
        
        return [
            'uuid' => (string) Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'type' => $type,
            'organizer_id' => User::factory()->create()->id,
            'image_url' => null,
            'banner_url' => null,
            'event_date' => $eventDate,
            'event_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'venue_name' => $this->faker->company(),
            'venue_address' => $this->faker->address(),
            'venue_city' => $this->faker->city(),
            'venue_latitude' => $this->faker->latitude(),
            'venue_longitude' => $this->faker->longitude(),
            'max_attendees' => $this->faker->numberBetween(20, 200),
            'event_fee' => $this->faker->numberBetween(0, 5000),
            'currency' => 'BDT',
            'status' => 'published',
            'is_free' => false,
            'is_online' => false,
            'registration_open' => true,
            'registration_deadline' => $eventDate->modify('-3 days'),
            'has_certificates' => true,
            'certificates_enabled' => true,
            'requires_approval' => false,
            'meta_data' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_free' => true,
            'event_fee' => 0,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_free' => false,
            'event_fee' => $this->faker->numberBetween(500, 5000),
        ]);
    }

    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_online' => true,
            'venue_name' => 'Online',
            'venue_address' => null,
        ]);
    }

    public function upcoming(): static
    {
        $eventDate = $this->faker->dateTimeBetween('+10 days', '+30 days');
        
        return $this->state(fn (array $attributes) => [
            'event_date' => $eventDate,
            'registration_deadline' => $eventDate->modify('-3 days'),
            'status' => 'published',
        ]);
    }

    public function past(): static
    {
        $eventDate = $this->faker->dateTimeBetween('-30 days', '-1 day');
        
        return $this->state(fn (array $attributes) => [
            'event_date' => $eventDate,
            'status' => 'published',
        ]);
    }
}
