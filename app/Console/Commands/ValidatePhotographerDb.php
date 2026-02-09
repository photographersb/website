<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Photographer;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ValidatePhotographerDb extends Command
{
    protected $signature = 'sb:validate-photographer-db {--fix : Auto-fix issues}';
    protected $description = 'Validate photographer database integrity and identify orphan records';

    public function handle()
    {
        $this->info('🔍 Validating Photographer Database...');
        $this->line('');

        $issues = [];

        // Check 1: Photographers without location
        $orphanLocations = Photographer::whereNull('city_id')->count();
        if ($orphanLocations > 0) {
            $issues[] = "❌ $orphanLocations photographers without location (city_id)";
            if ($this->option('fix')) {
                $this->fixPhotographersWithoutLocation();
                $this->info("   ✓ Fixed photographers without location");
            }
        }

        // Check 2: Photographers without user
        $orphanUsers = Photographer::whereNull('user_id')->count();
        if ($orphanUsers > 0) {
            $issues[] = "❌ $orphanUsers photographers without user association";
        }

        // Check 3: Duplicate slugs
        $duplicateSlugs = DB::table('photographers')
            ->select('slug', DB::raw('count(*) as cnt'))
            ->groupBy('slug')
            ->having('cnt', '>', 1)
            ->count();
        if ($duplicateSlugs > 0) {
            $issues[] = "❌ $duplicateSlugs duplicate photographer slugs";
        }

        // Check 4: Locations referenced but not existing
        $invalidLocations = Photographer::select('city_id')
            ->distinct()
            ->whereNotNull('city_id')
            ->whereNotIn('city_id', function ($query) {
                $query->select('id')->from('locations');
            })
            ->count();
        if ($invalidLocations > 0) {
            $issues[] = "❌ $invalidLocations photographers reference non-existent locations";
        }

        // Check 5: Database statistics
        $totalPhotographers = Photographer::count();
        $verifiedPhotographers = Photographer::where('is_verified', true)->count();
        $featuredPhotographers = Photographer::where('is_featured', true)->count();
        $locationCount = Location::count();
        $categoryCount = Category::count();

        $this->line('📊 Database Statistics:');
        $this->line("   Total Photographers: $totalPhotographers");
        $this->line("   Verified: $verifiedPhotographers");
        $this->line("   Featured: $featuredPhotographers");
        $this->line("   Locations: $locationCount");
        $this->line("   Categories: $categoryCount");

        if (empty($issues)) {
            $this->info('');
            $this->info('✅ Database is healthy!');
            return 0;
        }

        $this->error('');
        $this->error('Issues Found:');
        foreach ($issues as $issue) {
            $this->line($issue);
        }

        if ($this->option('fix')) {
            $this->info('');
            $this->info('✓ Auto-fix completed');
        } else {
            $this->line('');
            $this->line('Run with --fix to auto-fix issues');
        }

        return 0;
    }

    protected function fixPhotographersWithoutLocation()
    {
        $defaultLocation = Location::where('type', 'district')
            ->first();

        if (!$defaultLocation) {
            $this->warn('No default location available');
            return;
        }

        Photographer::whereNull('city_id')
            ->update(['city_id' => $defaultLocation->id]);
    }
}
