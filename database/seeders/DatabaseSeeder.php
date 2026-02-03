<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with clean Bangladesh data
     */
    public function run(): void
    {
        $this->command->info("\n╔═══════════════════════════════════════════════════════╗");
        $this->command->info("║  🇧🇩 PHOTOGRAPHER SB - COMPREHENSIVE BANGLADESH SEED   ║");
        $this->command->info("║     February 1, 2026                                  ║");
        $this->command->info("╚═══════════════════════════════════════════════════════╝\n");

        try {
            // Step 1: Roles & Permissions
            $this->call(RolesSeeder::class);
            $this->command->newLine();

            // Step 2: Super Admin User
            $this->call(SuperAdminSeeder::class);
            $this->command->newLine();

            // Step 3: Bangladesh Locations
            $this->call(BangladeshLocationSeeder::class);
            $this->command->newLine();

            // Step 4: Photography Categories
            $this->call(PhotographyCategoriesSeeder::class);
            $this->command->newLine();

            // Step 5: Sponsors
            $this->call(SponsorsSeeder::class);
            $this->command->newLine();

            // Step 6: Photographers & Content
            $this->call(PhotographersSeeder::class);
            $this->command->newLine();

            // Step 7: Packages
            $this->call(PackagesSeeder::class);
            $this->command->newLine();

            // Step 8: Albums
            $this->call(AlbumsSeeder::class);
            $this->command->newLine();

            // Step 9: Clear caches
            $this->command->info('🧹 Optimizing application...');
            Artisan::call('cache:clear');
            Artisan::call('config:cache');
            $this->command->line('  ✓ Caches cleared');
            $this->command->line('  ✓ Config cached');

            $this->command->newLine();
            $this->command->info('╔═══════════════════════════════════════════════════════╗');
            $this->command->info('║  ✅ SEEDING COMPLETE - SYSTEM READY                   ║');
            $this->command->info('╚═══════════════════════════════════════════════════════╝');

            $this->command->line("\n📊 Database Summary:");
            $this->command->line("  ✓ Roles configured");
            $this->command->line("  ✓ Super Admin: mahidulislamnakib@gmail.com");
            $this->command->line("  ✓ 63 Bangladesh districts loaded");
            $this->command->line("  ✓ 12 Photography categories configured");
            $this->command->line("  ✓ Sponsors seeded");
            $this->command->line("  ✓ 8 Sample photographers created");
            $this->command->line("  ✓ Packages and albums seeded");
            $this->command->line("  ✓ Application optimized");

            $this->command->line("\n🔐 Super Admin Credentials:");
            $this->command->line("  Email: mahidulislamnakib@gmail.com");
            $this->command->line("  Password: SuperAdmin@2026");
            $this->command->line("  ⚠️  Change password immediately after first login!\n");

        } catch (\Exception $e) {
            $this->command->error("❌ Seeding failed: " . $e->getMessage());
            throw $e;
        }
    }
}
