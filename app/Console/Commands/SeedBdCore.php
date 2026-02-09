<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\LocationSeeder;
use Database\Seeders\PhotographyCategorySeeder;
use Database\Seeders\HashtagSeeder;
use Database\Seeders\SuperAdminSeeder;
use App\Models\Location;
use App\Models\Category;
use App\Models\Hashtag;
use App\Models\Photographer;
use App\Models\User;

class SeedBdCore extends Command
{
    protected $signature = 'sb:seed-bd-core {--fresh : Clear existing data first}';
    protected $description = 'Seed comprehensive Bangladesh data: locations, categories, tags, and showcase data';

    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('🔄 Clearing existing data...');
            
            // Preserve super admin
            $superAdmin = User::where('email', 'mahidulislamnakib@gmail.com')->first();
            
            Location::truncate();
            Category::truncate();
            Hashtag::truncate();
            
            $this->info('✓ Data cleared');
        }

        $this->info('');
        $this->info('═══════════════════════════════════════');
        $this->info('  PHOTOGRAPHER SB - Bangladesh Seeder');
        $this->info('═══════════════════════════════════════');
        $this->info('');

        // Seed Locations (Bangladesh geographic structure)
        $this->info('📍 Seeding Bangladesh locations...');
        $this->call('db:seed', ['--class' => LocationSeeder::class]);

        // Seed Photography Categories
        $this->info('📂 Seeding photography categories...');
        $this->call('db:seed', ['--class' => PhotographyCategorySeeder::class]);

        // Seed Tags/Hashtags
        $this->info('🏷️ Seeding tags and hashtags...');
        $this->call('db:seed', ['--class' => HashtagSeeder::class]);

        // Validation & Reporting
        $this->info('');
        $this->info('✓ Validation Report:');
        $this->line('  Locations:  ' . Location::count());
        $this->line('  Categories: ' . Category::count());
        $this->line('  Tags:       ' . Hashtag::count());
        $this->line('  Photographers: ' . Photographer::count());
        $this->line('  Active Users: ' . User::where('is_active', true)->count());

        // Check integrity
        $orphanPhotographers = Photographer::where('city_id', null)->count();
        if ($orphanPhotographers > 0) {
            $this->warn('⚠️ Found ' . $orphanPhotographers . ' photographers without location');
        }

        $this->info('');
        $this->info('✅ Bangladesh Core Seeding Complete!');
        $this->line('Next steps:');
        $this->line('  - Seed showcase photographers: php artisan sb:seed-showcase-photographers');
        $this->line('  - Build SEO cache: php artisan cache:clear');
        $this->line('  - View routes: php artisan route:list');
    }
}
