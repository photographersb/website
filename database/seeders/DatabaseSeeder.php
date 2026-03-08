<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with PRODUCTION-READY essentials
     * NO sample/fake data - Only essential configuration
     */
    public function run(): void
    {
        $this->command->info("\n╔═══════════════════════════════════════════════════════╗");
        $this->command->info("║  🇧🇩 PHOTOGRAPHER SB - PRODUCTION SETUP                ║");
        $this->command->info("║     Essential Data Only - February 5, 2026            ║");
        $this->command->info("╚═══════════════════════════════════════════════════════╝\n");

        try {
            // ========================================
            // STEP 1: Core System Configuration
            // ========================================
            $this->command->line('📌 STEP 1: Core System Configuration');
            
            $this->call(RolesSeeder::class);
            $this->command->line('  ✓ Roles & permissions configured');
            
            $this->call(SuperAdminSeeder::class);
            $this->command->line('  ✓ Super admin created: mahidulislamnakib@gmail.com');
            
            $this->command->newLine();

            // ========================================
            // STEP 2: Geographic Data
            // ========================================
            $this->command->line('🗺️  STEP 2: Bangladesh Geographic Data');
            
            $this->call(BangladeshLocationSeeder::class);
            $this->command->line('  ✓ 8 divisions, 64 districts, major cities loaded');
            
            $this->command->newLine();

            // ========================================
            // STEP 3: Photography Configuration
            // ========================================
            $this->command->line('📸 STEP 3: Photography Configuration');
            
            $this->call(PhotographyCategoriesSeeder::class);
            $this->command->line('  ✓ 12 photography categories configured');
            
            $this->call(HashtagSeeder::class);
            $this->command->line('  ✓ Trending hashtags initialized');

            $this->call(CommunityStarterSeeder::class);
            $this->command->line('  ✓ Community starter groups, clubs, and badges seeded');

            if ((bool) env('ENABLE_DEMO_SEEDING', false)) {
                $this->call(BangladeshDemoDataSeeder::class);
                $this->command->line('  ✓ Bangladesh demo data seeded (ENABLE_DEMO_SEEDING=true)');
            }
            
            $this->command->newLine();

            // ========================================
            // STEP 3.5: Sample Photographers (Optional)
            // ========================================
            $this->command->line('👥 STEP 3.5: Sample Photographers');
            
            $this->call(PhotographersSeeder::class);
            $this->command->line('  ✓ Sample photographers created for testing');
            
            $this->command->newLine();

            // ========================================
            // STEP 4: Platform Settings
            // ========================================
            $this->command->line('⚙️  STEP 4: Platform Settings');
            
            $this->call(PlatformSettingsSeeder::class);
            $this->command->line('  ✓ Platform configuration loaded');
            
            $this->call(SeoMetaSeeder::class);
            $this->command->line('  ✓ SEO metadata configured');
            
            $this->call(NotificationTemplatesSeeder::class);
            $this->command->line('  ✓ Notification templates created');
            
            $this->command->newLine();

            // ========================================
            // STEP 5: System Optimization
            // ========================================
            $this->command->line('🧹 STEP 5: System Optimization');
            
            Artisan::call('cache:clear');
            $this->command->line('  ✓ Cache cleared');
            
            Artisan::call('config:cache');
            $this->command->line('  ✓ Configuration cached');
            
            Artisan::call('route:cache');
            $this->command->line('  ✓ Routes cached');

            $this->command->newLine();
            
            // ========================================
            // COMPLETION SUMMARY
            // ========================================
            $this->command->info('╔═══════════════════════════════════════════════════════╗');
            $this->command->info('║  ✅ PRODUCTION SETUP COMPLETE                         ║');
            $this->command->info('╚═══════════════════════════════════════════════════════╝');

            $this->command->line("\n📊 Database Summary:");
            $this->command->line("  ✓ Roles: Super Admin, Admin, Photographer, Judge");
            $this->command->line("  ✓ Super Admin: mahidulislamnakib@gmail.com");
            $this->command->line("  ✓ Bangladesh: 8 divisions, 64 districts");
            $this->command->line("  ✓ Photography: 12 categories configured");
            $this->command->line("  ✓ Platform: Settings & templates ready");
            $this->command->line("  ✓ Sample Photographers: 8 test profiles");
            $this->command->line("  ✓ Sample Data: Development-ready");

            $this->command->line("\n🔐 Super Admin Credentials:");
            $this->command->line("  Email: mahidulislamnakib@gmail.com");
            $this->command->line("  Password: SuperAdmin@2026");
            $this->command->line("  ⚠️  IMPORTANT: Change password immediately after first login!");

            $this->command->line("\n🌐 Next Steps:");
            $this->command->line("  1. Login at: http://127.0.0.1:8000/admin/login");
            $this->command->line("  2. Change super admin password");
            $this->command->line("  3. Configure platform settings");
            $this->command->line("  4. Create admin users as needed");
            $this->command->line("  5. Start approving photographer registrations\n");

        } catch (\Exception $e) {
            $this->command->error("❌ Seeding failed: " . $e->getMessage());
            $this->command->error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}
