<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\CompetitionJudge;
use App\Models\CompetitionScore;
use App\Models\CompetitionSubmission;
use App\Models\Judge;
use App\Models\Photographer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoJudgePanelSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding judge panel demo data...');

        $competition = Competition::whereIn('status', ['judging', 'active'])
            ->orderByRaw("status = 'judging' desc")
            ->first();

        if (!$competition) {
            $admin = User::whereIn('role', ['admin', 'super_admin'])->first() ?? User::first();
            $competition = Competition::create([
                'admin_id' => $admin?->id,
                'title' => 'Judge Panel Demo Competition',
                'slug' => 'judge-panel-demo',
                'description' => 'Demo competition for judge panel review.',
                'theme' => 'Demo Theme',
                'status' => 'judging',
                'submission_deadline' => now()->subDays(2),
                'judging_start_at' => now()->subDay(),
                'judging_end_at' => now()->addDays(7),
                'results_announcement_date' => now()->addDays(10),
                'is_public' => true,
                'allow_judge_scoring' => true,
                'allow_public_voting' => false,
                'max_submissions_per_user' => 1,
                'total_prize_pool' => 20000,
                'number_of_winners' => 3,
                'is_featured' => true,
                'published_at' => now()->subDay(),
            ]);
        } else {
            $competition->update([
                'status' => 'judging',
                'judging_start_at' => $competition->judging_start_at ?? now()->subDay(),
                'judging_end_at' => now()->addDays(7),
            ]);
        }

        $judgeUsers = User::where('role', 'judge')->get();
        if ($judgeUsers->isEmpty()) {
            $judgeUsers = collect([
                User::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => 'Demo Judge One',
                    'email' => 'demo.judge1@photographar.com',
                    'password' => Hash::make('password'),
                    'role' => 'judge',
                    'email_verified_at' => now(),
                ]),
                User::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => 'Demo Judge Two',
                    'email' => 'demo.judge2@photographar.com',
                    'password' => Hash::make('password'),
                    'role' => 'judge',
                    'email_verified_at' => now(),
                ]),
            ]);
        }

        $superAdmin = User::where('role', 'super_admin')
            ->orWhere('email', 'mahidulislamnakib@gmail.com')
            ->first();

        if ($superAdmin) {
            $judgeUsers = $judgeUsers->push($superAdmin)->unique('id')->values();
        }

        foreach ($judgeUsers as $judgeUser) {
            $judgeProfile = Judge::firstOrCreate(
                ['user_id' => $judgeUser->id],
                [
                    'name' => $judgeUser->name,
                    'slug' => Str::slug($judgeUser->name . '-judge'),
                    'email' => $judgeUser->email,
                    'is_active' => true,
                ]
            );

            CompetitionJudge::firstOrCreate(
                [
                    'competition_id' => $competition->id,
                    'judge_id' => $judgeUser->id,
                ],
                [
                    'judge_profile_id' => $judgeProfile->id,
                    'role' => 'judge',
                    'is_active' => true,
                    'assigned_at' => now(),
                ]
            );
        }

        $targetCount = 15;
        $existingCount = CompetitionSubmission::where('competition_id', $competition->id)->count();
        if ($existingCount < 20) {
            $createCount = max(0, $targetCount - $existingCount);
        } else {
            $createCount = 0;
        }

        $usedPhotographerIds = CompetitionSubmission::where('competition_id', $competition->id)
            ->pluck('photographer_id')
            ->all();

        $availablePhotographers = Photographer::whereNotIn('id', $usedPhotographerIds)->get();

        if ($availablePhotographers->count() < $createCount) {
            $missing = $createCount - $availablePhotographers->count();
            for ($i = 0; $i < $missing; $i++) {
                $user = User::create([
                    'uuid' => (string) Str::uuid(),
                    'name' => 'Demo Photographer ' . Str::upper(Str::random(4)),
                    'email' => 'demo.photographer.' . Str::random(6) . '@photographar.com',
                    'password' => Hash::make('password'),
                    'role' => 'photographer',
                    'email_verified_at' => now(),
                ]);

                $photographer = Photographer::create([
                    'user_id' => $user->id,
                    'slug' => Str::slug($user->name . '-' . Str::random(5)),
                    'bio' => 'Demo photographer profile for judge panel seeding.',
                    'experience_years' => rand(2, 12),
                    'average_rating' => rand(35, 50) / 10,
                    'rating_count' => rand(5, 30),
                    'is_verified' => true,
                    'verification_type' => 'email',
                    'verified_at' => now(),
                    'profile_completeness' => rand(70, 100),
                ]);

                $availablePhotographers->push($photographer);
            }
        }

        $titles = [
            'Golden Hour Drift',
            'Monsoon Light',
            'City Motion',
            'Quiet Morning',
            'Riverline Echo',
            'Market Rhythm',
            'Harbor Haze',
            'Hidden Courtyard',
            'Tea Garden Calm',
            'Coastal Whisper',
        ];

        for ($i = 0; $i < $createCount; $i++) {
            $photographer = $availablePhotographers->get($i);
            if (!$photographer) {
                break;
            }

            $seed = $competition->id * 1000 + $i + 1;
            CompetitionSubmission::create([
                'uuid' => (string) Str::uuid(),
                'competition_id' => $competition->id,
                'photographer_id' => $photographer->id,
                'user_id' => $photographer->user_id,
                'image_path' => 'submissions/demo_' . $seed . '.jpg',
                'image_url' => 'https://picsum.photos/1200/800?random=' . $seed,
                'thumbnail_url' => 'https://picsum.photos/400/300?random=' . $seed,
                'title' => $titles[$i % count($titles)],
                'description' => 'Demo submission for judge panel preview.',
                'camera_make' => ['Canon', 'Nikon', 'Sony', 'Fujifilm'][array_rand(['Canon', 'Nikon', 'Sony', 'Fujifilm'])],
                'camera_model' => ['EOS R6', 'D850', 'A7 IV', 'X-T5'][array_rand(['EOS R6', 'D850', 'A7 IV', 'X-T5'])],
                'camera_settings' => json_encode([
                    'iso' => rand(100, 1600),
                    'shutter_speed' => '1/' . rand(80, 400),
                    'aperture' => 'f/' . (rand(18, 36) / 10),
                ]),
                'status' => 'approved',
                'submitted_at' => now()->subDays(rand(0, 6)),
                'view_count' => rand(5, 120),
                'vote_count' => rand(0, 80),
            ]);
        }

        $submissions = CompetitionSubmission::where('competition_id', $competition->id)
            ->where('status', 'approved')
            ->get();

        foreach ($judgeUsers as $judgeUser) {
            $count = $submissions->count();
            if ($count === 0) {
                continue;
            }

            $scoreCount = max(1, (int) floor($count / 2));
            $selection = $submissions->random($scoreCount);
            if ($selection instanceof CompetitionSubmission) {
                $selection = collect([$selection]);
            }

            foreach ($selection as $submission) {
                CompetitionScore::updateOrCreate(
                    [
                        'competition_id' => $competition->id,
                        'submission_id' => $submission->id,
                        'judge_id' => $judgeUser->id,
                    ],
                    [
                        'composition_score' => rand(6, 10),
                        'technical_score' => rand(6, 10),
                        'creativity_score' => rand(6, 10),
                        'story_score' => rand(6, 10),
                        'impact_score' => rand(6, 10),
                        'feedback' => 'Strong composition and thoughtful lighting.',
                        'status' => 'completed',
                        'scored_at' => now()->subDays(rand(0, 3)),
                    ]
                );
            }
        }

        $this->command->info('Judge panel demo data ready.');
        $this->command->line('Competition: ' . $competition->title);
        $this->command->line('Submissions: ' . CompetitionSubmission::where('competition_id', $competition->id)->count());
        $this->command->line('Judges assigned: ' . $judgeUsers->count());
    }
}
