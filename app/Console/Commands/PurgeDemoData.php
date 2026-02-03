<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeDemoData extends Command
{
    protected $signature = 'app:purge-demo-data {--force : Force purge without confirmation}';
    protected $description = 'Purge all demo/test data while keeping schema and system tables intact';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('⚠️  This will DELETE all demo data. Continue?')) {
            $this->info('Operation cancelled.');
            return 1;
        }

        $this->info('🗑️  Starting demo data purge...\n');

        try {
            // Disable FK checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $this->line('✓ Foreign key checks disabled');

            // Get count before
            $this->newLine();
            $this->info('📊 Purging tables (in dependency order)...');

            // Order matters: delete child tables before parents
            $tablesToPurge = [
                // Analytics & logs (no dependencies, safe to purge)
                'page_views' => true,
                'visitor_logs' => true,
                'activity_logs' => true,
                'audit_logs' => true,
                'notifications' => true,
                'notice_reads' => true,
                'contact_messages' => true,

                // Competition data (many dependencies, purge children first)
                'competition_votes' => true,
                'competition_scores' => true,
                'competition_submissions' => true,
                'competition_mentor' => true,
                'competition_prizes' => true,
                'scoring_criteria' => true,
                'competition_judges' => true,
                'competition_categories' => true,
                'competition_sponsors' => true,
                'competitions' => true,

                // Events
                'event_payments' => true,
                'event_registrations' => true,
                'event_rsvps' => true,
                'event_tickets' => true,
                'events' => true,

                // Reviews & interactions
                'review_replies' => true,
                'reviews' => true,
                'photographer_favorites' => true,
                'photographer_achievements' => true,

                // Bookings & inquiries
                'bookings' => true,
                'quotes' => true,
                'inquiries' => true,

                // Photographer content
                'photos' => true,
                'albums' => true,
                'packages' => true,
                'photographer_category' => true,
                'photographer_stats' => true,
                'photographer_awards' => true,
                'photographer_notifications' => true,

                // Transactions
                'transactions' => true,

                // Subscriptions
                'subscriptions' => true,
                'subscription_plans' => true,

                // Verification & trust
                'verifications' => true,
                'trust_scores' => true,

                // References & supporting
                'seo_meta' => true,
                'username_history' => true,
                'hashtags' => true,
                'photo_categories' => true,

                // Judges & mentors (demo)
                'judges' => true,
                'mentors' => true,

                // Sponsors, notices, achievements
                'sponsors' => true,
                'notices' => true,
                'notice_role' => true,
                'achievements' => true,
            ];

            $purgedCount = 0;
            $totalRows = 0;

            foreach ($tablesToPurge as $table => $shouldPurge) {
                if (!$shouldPurge) {
                    continue;
                }

                $count = DB::table($table)->count();
                if ($count > 0) {
                    DB::table($table)->truncate();
                    $totalRows += $count;
                    $purgedCount++;
                    $this->line("  ✓ {$table} → {$count} rows purged");
                }
            }

            // Re-enable FK checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->line('✓ Foreign key checks re-enabled');

            $this->newLine();
            $this->info("✅ Purge complete!");
            $this->info("   Truncated: {$purgedCount} tables");
            $this->info("   Rows deleted: {$totalRows}");

            // Clear caches
            $this->line('\n🧹 Clearing application caches...');
            $this->call('cache:clear');
            $this->call('config:cache');

            $this->newLine();
            $this->info('✨ Demo data purge successful! Database is clean.');
            $this->info('   Run: php artisan migrate:fresh --seed');
            $this->info('   to recreate with Bangladesh seed data.');

            return 0;

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('❌ Error during purge: ' . $e->getMessage());
            return 1;
        }
    }
}
