<?php

namespace Database\Factories;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MentorFactory extends Factory
{
    protected $model = Mentor::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        
        return [
            'user_id' => User::factory()->create()->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'bio' => $this->faker->paragraph(),
            'expertise' => implode(',', $this->faker->words(5)),
            'profile_photo_url' => null,
            'experience_years' => $this->faker->numberBetween(5, 30),
            'hourly_rate' => $this->faker->numberBetween(2000, 10000),
            'availability_status' => 'available',
            'is_active' => true,
            'rating' => $this->faker->randomFloat(1, 4, 5),
            'total_reviews' => $this->faker->numberBetween(0, 100),
            'website_url' => $this->faker->url(),
            'social_media' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'availability_status' => 'available',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'availability_status' => 'unavailable',
        ]);
    }
}
