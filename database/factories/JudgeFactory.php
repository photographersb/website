<?php

namespace Database\Factories;

use App\Models\Judge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JudgeFactory extends Factory
{
    protected $model = Judge::class;

    public function definition()
    {
        $categories = ['Photography', 'Composition', 'Lighting', 'Color', 'Subject Matter', 'Technical'];
        
        return [
            'user_id' => User::factory(),
            'expertise_categories' => $this->faker->randomElements($categories, 2),
            'bio' => $this->faker->sentence(20),
            'rating' => $this->faker->randomFloat(1, 3.5, 5),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
