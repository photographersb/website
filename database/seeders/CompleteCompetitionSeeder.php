<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Photographer;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\Judge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Production-ready seeder for complete competition with submissions, judging, and winners.
 * Fully idempotent - safe to run multiple times without duplicate errors.
 * 
 * Usage: php artisan db:seed --class=CompleteCompetitionSeeder
 */
class CompleteCompetitionSeeder extends Seeder
{
    private const DEMO_SENTINEL = 'demo-complete-competition-2026';
    
    public function run(): void
    {
        $this->command->info("\n🏆 Production-Ready Complete Competition Seeder\n");
        $this->command->info("Creating/Updating complete competition with submissions, judges & winners...\n");

        try {
            DB::beginTransaction();

            // 1. Ensure or get admin user (idempotent)
            $admin = $this->ensureAdmin();
            $this->command->line("✓ Admin user ready (ID: {$admin->id})");

            // 2. Check for existing demo competition to prevent duplicates
            $existingCompetition = Competition::where('title', 'Demo Complete Competition 2026')->first();
            
            if ($existingCompetition) {
                $this->command->line("\n📋 Demo competition already exists (ID: {$existingCompetition->id})");
                $this->command->line("Using existing competition to update submissions...\n");
                $competition = $existingCompetition;
                $isNewCompetition = false;
            } else {
                $competition = $this->createCompetition($admin);
                $this->command->line("✓ Created competition: {$competition->title}");
                $isNewCompetition = true;
            }

            // 3. Ensure photographers (idempotent - uses firstOrCreate)
            $photographers = $this->ensurePhotographers();
            $this->command->line("✓ Photographers ready (" . count($photographers) . " total)");

            // 4. Ensure submissions for photographers (scoped to competition)
            $submissions = $this->ensureSubmissions($competition, $photographers);
            $this->command->line("✓ Submissions ready (" . count($submissions) . " total for this competition)");

            // 5. Ensure judges (idempotent - uses firstOrCreate)
            $judgeUsers = $this->ensureJudges();
            $this->command->line("✓ Judges ready (" . count($judgeUsers) . " total)");

            // 6. Mark winners (idempotent - updates existing winners if any)
            $winners = $this->markWinners($competition);
            $this->command->line("✓ Winners marked (" . count($winners) . " submissions)");

            DB::commit();

            $this->command->info("\n✅ Competition seeding completed successfully!\n");
            $this->command->line("├─ Competition ID: {$competition->id}");
            $this->command->line("├─ Competition Slug: {$competition->slug}");
            $this->command->line("├─ Status: " . strtoupper($competition->status));
            $this->command->line("├─ Submissions: " . count($submissions));
            $this->command->line("├─ Winners: " . count($winners));
            $this->command->line("├─ Admin: {$admin->email}");
            $this->command->line("└─ Judges: " . collect($judgeUsers)->pluck('email')->join(', '));
            $this->command->line("\n");

            if ($isNewCompetition) {
                $this->command->line("🎯 Next steps for testing:");
                $this->command->line("   1. Visit: /admin/certificates/manual-issuance");
                $this->command->line("   2. Select the '{$competition->title}' competition");
                $this->command->line("   3. Choose a submission and issue a certificate");
                $this->command->line("\n");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("\n❌ Error during seeding:\n");
            $this->command->error($e->getMessage());
            $this->command->error("\nStack trace:");
            $this->command->error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Ensure admin user exists or create it (idempotent)
     */
    private function ensureAdmin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@photographar.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }

    /**
     * Create a new complete competition
     */
    private function createCompetition(User $admin): Competition
    {
        return Competition::create([
            'admin_id' => $admin->id,
            'title' => 'Demo Complete Competition 2026',
            'slug' => 'demo-complete-' . now()->timestamp,
            'description' => 'A fully-populated demo competition with submissions, judging, and winners.',
            'theme' => 'All Categories',
            'status' => 'closed',
            'submission_deadline' => now()->subDays(10),
            'voting_start_at' => now()->subDays(9),
            'voting_end_at' => now()->subDays(2),
            'judging_start_at' => now()->subDays(1),
            'judging_end_at' => now(),
            'results_announcement_date' => now()->addDays(1),
            'is_paid_competition' => false,
            'participation_fee' => 0,
            'max_submissions_per_user' => 5,
            'total_prize_pool' => 50000,
            'number_of_winners' => 3,
            'is_featured' => true,
            'featured_until' => now()->addDays(30),
        ]);
    }

    /**
     * Ensure 5 photographers exist (idempotent using firstOrCreate)
     */
    private function ensurePhotographers(): array
    {
        $photographers = [];

        for ($i = 1; $i <= 5; $i++) {
            // Create or get user (idempotent)
            $user = User::firstOrCreate(
                ['email' => "photographer{$i}@demo.test"],
                [
                    'uuid' => Str::uuid(),
                    'name' => "Demo Photographer {$i}",
                    'password' => Hash::make('password123'),
                    'role' => 'photographer',
                    'email_verified_at' => now(),
                    'is_suspended' => false,
                ]
            );

            // Create or get photographer profile (idempotent)
            $photographer = Photographer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'slug' => 'demo-photographer-' . $i,
                    'bio' => "Professional photographer specializing in various categories. Photo {$i}",
                    'location' => 'Dhaka, Bangladesh',
                    'experience_years' => rand(2, 15),
                    'specializations' => 'landscape,portrait,commercial',
                    'is_verified' => true,
                    'verification_type' => 'phone_email_id',
                    'verified_at' => now(),
                ]
            );

            $photographers[] = $photographer;
        }

        return $photographers;
    }

    /**
     * Ensure submissions exist for competition (idempotent - checks by photographer + competition)
     */
    private function ensureSubmissions(Competition $competition, array $photographers): array
    {
        $submissions = [];
        $submissionTitles = [
            'Sunrise at Cox\'s Bazar',
            'Street Life in Dhaka',
            'Mountain Echo',
            'Urban Landscape',
            'Monsoon Memories',
        ];

        $submissionDescriptions = [
            'Capturing the first light of dawn over the sandy beaches',
            'Life in the bustling streets of Bangladesh capital',
            'Serene mountain landscape during golden hour',
            'Modern architecture meets traditional culture',
            'The beauty of rainy season in the countryside',
        ];

        foreach ($photographers as $index => $photographer) {
            for ($j = 1; $j <= 3; $j++) {
                $titleIndex = ($index * 3 + $j - 1) % count($submissionTitles);
                $descIndex = $titleIndex;

                // Check if submission already exists for this photographer+competition combo
                $existingSubmission = CompetitionSubmission::where('competition_id', $competition->id)
                    ->where('photographer_id', $photographer->id)
                    ->where('user_id', $photographer->user_id)
                    ->where('title', 'like', $submissionTitles[$titleIndex] . '%')
                    ->first();

                if ($existingSubmission) {
                    $submissions[] = $existingSubmission;
                    continue;
                }

                $submission = CompetitionSubmission::create([
                    'competition_id' => $competition->id,
                    'photographer_id' => $photographer->id,
                    'user_id' => $photographer->user_id,
                    'title' => $submissionTitles[$titleIndex] . " (Submission {$j})",
                    'description' => $submissionDescriptions[$descIndex],
                    'image_path' => 'submissions/' . $competition->id . '/submission-' . Str::random(8) . '.jpg',
                    'image_url' => 'https://via.placeholder.com/800x600?text=' . urlencode($submissionTitles[$titleIndex]),
                    'thumbnail_url' => 'https://via.placeholder.com/400x300?text=' . urlencode($submissionTitles[$titleIndex]),
                    'location' => 'Bangladesh',
                    'date_taken' => now()->subDays(rand(30, 365)),
                    'camera_make' => 'Canon',
                    'camera_model' => '5D Mark IV',
                    'camera_settings' => json_encode(['iso' => 400, 'aperture' => 5.6, 'shutter' => '1/500']),
                    'status' => 'approved',
                    'submitted_at' => now()->subDays(rand(1, 10)),
                ]);

                $submissions[] = $submission;
            }
        }

        return $submissions;
    }

    /**
     * Ensure judges exist (idempotent using firstOrCreate)
     */
    private function ensureJudges(): array
    {
        $judgeUsers = [];

        for ($i = 1; $i <= 3; $i++) {
            // Create or get user (idempotent)
            $user = User::firstOrCreate(
                ['email' => "judge{$i}@demo.test"],
                [
                    'uuid' => Str::uuid(),
                    'name' => "Demo Judge {$i}",
                    'password' => Hash::make('password123'),
                    'role' => 'judge',
                    'email_verified_at' => now(),
                ]
            );

            // Create or get judge profile (idempotent)
            Judge::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'title' => "Senior Photography Judge {$i}",
                    'organization' => 'Bangladesh Photography Association',
                    'bio' => 'Experienced judge with 15+ years in photography evaluation',
                    'is_active' => true,
                ]
            );

            $judgeUsers[] = $user;
        }

        return $judgeUsers;
    }

    /**
     * Mark winners on submissions (idempotent - only marks non-winners)
     */
    private function markWinners(Competition $competition): array
    {
        // Check if winners already exist for this competition
        $existingWinners = CompetitionSubmission::where('competition_id', $competition->id)
            ->where('is_winner', true)
            ->get();

        if ($existingWinners->count() > 0) {
            return $existingWinners->all();
        }

        // Get all submissions for this competition
        $allSubmissions = CompetitionSubmission::where('competition_id', $competition->id)
            ->get();

        if ($allSubmissions->count() < 3) {
            $this->command->warn("⚠️  Warning: Not enough submissions to mark 3 winners. Found: {$allSubmissions->count()}");
            return [];
        }

        // Mark top 3 as winners
        $positions = ['1st', '2nd', '3rd'];
        $topSubmissions = $allSubmissions->shuffle()->slice(0, 3);
        $winners = [];

        foreach ($topSubmissions as $index => $submission) {
            $submission->update([
                'is_winner' => true,
                'winner_position' => $positions[$index] ?? null,
            ]);
            $winners[] = $submission;
        }

        return $winners;
    }
}
