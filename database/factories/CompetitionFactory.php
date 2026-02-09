<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Category;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        $startDate = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $endDate = $this->faker->dateTimeBetween($startDate, '+60 days');
        
        return [
            'uuid' => (string) Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory()->create()->id,
            'city_id' => City::inRandomOrder()->first()?->id ?? City::factory()->create()->id,
            'image_url' => null,
            'banner_url' => null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'submission_start' => $startDate->modify('-10 days'),
            'submission_end' => $startDate->modify('-1 day'),
            'voting_start' => $startDate,
            'voting_end' => $endDate,
            'status' => 'published',
            'visibility' => 'public',
            'competition_type' => $this->faker->randomElement(['photo', 'video']),
            'max_participants' => $this->faker->numberBetween(10, 100),
            'entry_fee' => $this->faker->numberBetween(0, 5000),
            'total_prize_pool' => $this->faker->numberBetween(10000, 100000),
            'rules' => $this->faker->paragraph(),
            'registration_open' => true,
            'voting_open' => true,
            'certificates_enabled' => true,
            'requires_approval' => true,
            'max_submissions_per_user' => 5,
            'judging_type' => 'scores',
            'has_shortlist' => true,
            'announcement_date' => $endDate->modify('+7 days'),
            'meta_data' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'visibility' => 'public',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'visibility' => 'private',
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'registration_open' => false,
            'voting_open' => false,
        ]);
    }

    public function upcoming(): static
    {
        $startDate = $this->faker->dateTimeBetween('+10 days', '+30 days');
        $endDate = $this->faker->dateTimeBetween($startDate, '+60 days');
        
        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'submission_start' => $startDate->modify('-10 days'),
            'submission_end' => $startDate->modify('-1 day'),
            'status' => 'published',
        ]);
    }
}
