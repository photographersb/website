<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Competition;
use Illuminate\Support\Facades\DB;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create admin user
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $admin = User::where('email', 'admin@photographar.com')->first();
            if (!$admin) {
                $admin = User::create([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'name' => 'Admin User',
                    'email' => 'admin@photographar.com',
                    'password' => bcrypt('password123'),
                    'role' => 'admin',
                    'email_verified_at' => now()
                ]);
            }
        }

        // Create dummy competition
        $competition = Competition::create([
            'admin_id' => $admin->id,
            'title' => 'Bangladesh Nature Photography Contest 2026',
            'slug' => 'bangladesh-nature-2026',
            'description' => 'Capture the breathtaking beauty of Bangladesh - from the Sundarbans mangroves to the tea gardens of Sylhet, from the Cox\'s Bazar coastline to the rural landscapes. Show us the natural wonders of our country through your lens.',
            'theme' => 'Nature & Wildlife',
            'submission_deadline' => now()->addDays(30),
            'judging_start_at' => now()->addDays(31),
            'results_announcement_date' => now()->addDays(45),
            'status' => 'active',
            'is_public' => true,
            'is_paid_competition' => false,
            'participation_fee' => 0,
            'max_submissions_per_user' => 3,
            'total_prize_pool' => 50000,
            'allow_public_voting' => true,
        ]);

        // Add prizes
        DB::table('competition_prizes')->insert([
            [
                'competition_id' => $competition->id,
                'rank' => 1,
                'title' => 'First Prize',
                'cash_amount' => 25000,
                'physical_prizes' => 'Professional Camera Kit + Certificate',
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'competition_id' => $competition->id,
                'rank' => 2,
                'title' => 'Second Prize',
                'cash_amount' => 15000,
                'physical_prizes' => 'Camera Lens + Certificate',
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'competition_id' => $competition->id,
                'rank' => 3,
                'title' => 'Third Prize',
                'cash_amount' => 10000,
                'physical_prizes' => 'Photography Equipment Bundle + Certificate',
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Add sponsors
        DB::table('competition_sponsors')->insert([
            [
                'competition_id' => $competition->id,
                'name' => 'Canon Bangladesh',
                'tier' => 'platinum',
                'logo_url' => 'https://via.placeholder.com/200x100/0052CC/ffffff?text=Canon',
                'website_url' => 'https://canon.com.bd',
                'description' => 'Leading camera and imaging solutions provider',
                'contribution_amount' => 30000,
                'display_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'competition_id' => $competition->id,
                'name' => 'Nikon Bangladesh',
                'tier' => 'gold',
                'logo_url' => 'https://via.placeholder.com/200x100/FFD700/000000?text=Nikon',
                'website_url' => 'https://nikon.com.bd',
                'description' => 'Professional photography equipment manufacturer',
                'contribution_amount' => 20000,
                'display_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✓ Competition created: ' . $competition->title);
        $this->command->info('  - ID: ' . $competition->id);
        $this->command->info('  - Slug: ' . $competition->slug);
        $this->command->info('  - Deadline: ' . $competition->submission_deadline->format('F j, Y'));
        $this->command->info('  - Prizes: 3 added');
        $this->command->info('  - Sponsors: 2 added');
    }
}
