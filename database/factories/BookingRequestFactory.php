<?php

namespace Database\Factories;

use App\Models\BookingRequest;
use App\Models\Photographer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingRequestFactory extends Factory
{
    protected $model = BookingRequest::class;

    public function definition(): array
    {
        $photographer = Photographer::inRandomOrder()->first() 
            ?? Photographer::factory()->create();
        $eventDate = $this->faker->dateTimeBetween('+7 days', '+60 days');
        
        return [
            'uuid' => (string) Str::uuid(),
            'photographer_id' => $photographer->id,
            'client_id' => User::factory()->create()->id,
            'event_type' => $this->faker->randomElement(['wedding', 'birthday', 'corporate', 'other']),
            'event_date' => $eventDate,
            'event_location' => $this->faker->address(),
            'number_of_hours' => $this->faker->numberBetween(2, 8),
            'estimated_budget' => $this->faker->numberBetween(10000, 50000),
            'description' => $this->faker->paragraph(),
            'special_requirements' => $this->faker->sentence(),
            'status' => 'pending',
            'photographer_response_at' => null,
            'photographer_response' => null,
            'client_response_at' => null,
            'confirmed_price' => null,
            'confirmed_hours' => null,
            'booking_date' => null,
            'completion_date' => null,
            'payment_status' => 'pending',
            'deposit_amount' => null,
            'total_amount' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'photographer_response' => 'Interested!',
            'photographer_response_at' => now(),
            'confirmed_price' => $this->faker->numberBetween(15000, 60000),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'photographer_response' => 'Not available',
            'photographer_response_at' => now(),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'booking_date' => now(),
            'payment_status' => 'paid',
        ]);
    }
}
