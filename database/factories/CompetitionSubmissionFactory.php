<?php

namespace Database\Factories;

use App\Models\CompetitionSubmission;
use App\Models\Competition;
use App\Models\Photographer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompetitionSubmissionFactory extends Factory
{
    protected $model = CompetitionSubmission::class;

    public function definition(): array
    {
        $competition = Competition::inRandomOrder()->first() 
            ?? Competition::factory()->create();
        $photographer = Photographer::inRandomOrder()->first() 
            ?? Photographer::factory()->create();
        
        return [
            'uuid' => (string) Str::uuid(),
            'competition_id' => $competition->id,
            'photographer_id' => $photographer->id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'image_url' => null,
            'thumbnail_url' => null,
            'submission_category' => null,
            'status' => 'pending',
            'approval_status' => 'pending',
            'is_finalist' => false,
            'is_winner' => false,
            'winner_position' => null,
            'total_votes' => 0,
            'average_score' => 0,
            'submitted_at' => now(),
            'approved_at' => null,
            'approved_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => 'approved',
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => 'rejected',
            'status' => 'rejected',
        ]);
    }

    public function finalist(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_finalist' => true,
            'approval_status' => 'approved',
        ]);
    }

    public function winner(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_winner' => true,
            'is_finalist' => true,
            'winner_position' => 1,
            'approval_status' => 'approved',
        ]);
    }
}
