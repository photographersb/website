<?php

namespace Database\Factories;

use App\Models\Sponsor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SponsorFactory extends Factory
{
    protected $model = Sponsor::class;

    public function definition(): array
    {
        $name = $this->faker->company();
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'logo_url' => null,
            'website_url' => $this->faker->url(),
            'email' => $this->faker->companyEmail(),
            'contact_person' => $this->faker->name(),
            'contact_phone' => '01' . $this->faker->numerify('#########'),
            'tier' => $this->faker->randomElement(['gold', 'silver', 'bronze']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function gold(): static
    {
        return $this->state(fn (array $attributes) => [
            'tier' => 'gold',
        ]);
    }

    public function silver(): static
    {
        return $this->state(fn (array $attributes) => [
            'tier' => 'silver',
        ]);
    }

    public function bronze(): static
    {
        return $this->state(fn (array $attributes) => [
            'tier' => 'bronze',
        ]);
    }
}
