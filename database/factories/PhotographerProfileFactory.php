<?php

namespace Database\Factories;

use App\Models\Photographer;
use App\Models\User;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PhotographerProfileFactory extends Factory
{
    protected $model = Photographer::class;

    public function definition(): array
    {
        $user = User::factory()->photographer()->create();
        $city = City::inRandomOrder()->first() ?? City::factory()->create();
        
        return [
            'user_id' => $user->id,
            'business_name' => $this->faker->company(),
            'about' => $this->faker->paragraph(),
            'experience_years' => $this->faker->numberBetween(1, 20),
            'specializations' => implode(',', $this->faker->words(3)),
            'city_id' => $city->id,
            'address' => $this->faker->address(),
            'phone' => '01' . $this->faker->numerify('#########'),
            'website' => $this->faker->url(),
            'hourly_rate' => $this->faker->numberBetween(1000, 10000),
            'portfolio_url' => null,
            'certificate_url' => null,
            'verification_status' => 'unverified',
            'verification_verified_at' => null,
            'average_rating' => $this->faker->randomFloat(1, 3, 5),
            'total_reviews' => 0,
            'total_bookings' => 0,
            'response_time_hours' => $this->faker->numberBetween(1, 24),
            'accepts_bookings' => true,
            'is_featured' => false,
            'featured_until' => null,
            'meta_data' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'verified',
            'verification_verified_at' => now(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'featured_until' => now()->addMonths(3),
        ]);
    }
}
