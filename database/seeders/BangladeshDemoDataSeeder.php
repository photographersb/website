<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BangladeshDemoDataSeeder extends Seeder
{
    /**
     * Seed realistic Bangladesh demo data for QA/staging environments.
     * This seeder is intentionally idempotent by delegating to update-safe seeders.
     */
    public function run(): void
    {
        $this->command->info('Starting Bangladesh demo data seeding...');

        $this->call([
            BangladeshLocationSeeder::class,
            PhotographyCategoriesSeeder::class,
            HashtagSeeder::class,
            PhotographersSeeder::class,
            MentorSeeder::class,
            JudgeSeeder::class,
            SponsorsSeeder::class,
            EventSeeder::class,
            MonthlyBangladeshEventsSeeder::class,
            CompetitionSeeder::class,
            MonthlyBangladeshCompetitionsSeeder::class,
            NoticeSeeder::class,
        ]);

        $this->command->info('Bangladesh demo data seeding complete.');
    }
}
