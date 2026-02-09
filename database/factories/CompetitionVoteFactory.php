<?php

namespace Database\Factories;

use App\Models\CompetitionVote;
use App\Models\CompetitionSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionVoteFactory extends Factory
{
    protected $model = CompetitionVote::class;

    public function definition(): array
    {
        $submission = CompetitionSubmission::inRandomOrder()->first() 
            ?? CompetitionSubmission::factory()->create();
        
        return [
            'submission_id' => $submission->id,
            'competition_id' => $submission->competition_id,
            'user_id' => User::factory()->create()->id,
            'vote_value' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(),
            'voted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
