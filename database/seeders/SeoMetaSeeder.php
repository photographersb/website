<?php

namespace Database\Seeders;

use App\Models\SeoMeta;
use App\Models\Photographer;
use App\Models\Competition;
use Illuminate\Database\Seeder;

class SeoMetaSeeder extends Seeder
{
    public function run(): void
    {
        // Auto-generate SEO meta for photographers
        $photographers = Photographer::take(5)->get();
        foreach ($photographers as $photographer) {
            $this->createPhotographerSeo($photographer);
        }

        // Auto-generate SEO meta for competitions
        $competitions = Competition::take(5)->get();
        foreach ($competitions as $competition) {
            $this->createCompetitionSeo($competition);
        }

        $this->command->info('✓ SEO Meta seeding completed');
    }

    private function createPhotographerSeo(Photographer $photographer)
    {
        $photographer->generateSeoMeta();
        $this->command->line("  ✓ SEO generated for: {$photographer->user->name}");
    }

    private function createCompetitionSeo(Competition $competition)
    {
        $competition->generateSeoMeta();
        $this->command->line("  ✓ SEO generated for: {$competition->title}");
    }
}
