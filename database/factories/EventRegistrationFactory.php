<?php

namespace Database\Factories;

use App\Models\EventRegistration;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    public function definition(): array
    {
        $event = Event::inRandomOrder()->first() ?? Event::factory()->create();
        $user = User::factory()->create();
        
        return [
            'uuid' => (string) Str::uuid(),
            'event_id' => $event->id,
            'user_id' => $user->id,
            'registration_code' => strtoupper(Str::random(8)),
            'status' => 'confirmed',
            'payment_status' => $event->is_free ? 'completed' : 'pending',
            'registration_date' => now(),
            'check_in_date' => null,
            'attended_at' => null,
            'feedback_submitted' => false,
            'feedback_rating' => null,
            'feedback_comment' => null,
            'meta_data' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function attended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'attended',
            'attended_at' => now(),
            'check_in_date' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function withFeedback(): static
    {
        return $this->state(fn (array $attributes) => [
            'feedback_submitted' => true,
            'feedback_rating' => $this->faker->numberBetween(3, 5),
            'feedback_comment' => $this->faker->sentence(),
        ]);
    }
}
