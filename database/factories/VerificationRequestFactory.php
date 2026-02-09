<?php

namespace Database\Factories;

use App\Models\VerificationRequest;
use App\Models\Photographer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationRequestFactory extends Factory
{
    protected $model = VerificationRequest::class;

    public function definition(): array
    {
        $photographer = Photographer::inRandomOrder()->first() 
            ?? Photographer::factory()->create();
        
        return [
            'photographer_id' => $photographer->id,
            'user_id' => $photographer->user_id,
            'status' => 'pending',
            'verification_type' => $this->faker->randomElement(['portfolio', 'credentials', 'experience']),
            'documents' => json_encode([]),
            'notes' => $this->faker->sentence(),
            'submitted_at' => now(),
            'reviewed_at' => null,
            'reviewed_by' => null,
            'rejection_reason' => null,
            'verified_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => User::factory()->admin()->create()->id,
            'verified_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => User::factory()->admin()->create()->id,
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);
    }
}
