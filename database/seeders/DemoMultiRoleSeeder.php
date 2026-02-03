<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Photographer;
use App\Models\Mentor;
use App\Models\Judge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoMultiRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("\n🎨 Creating Demo Multi-Role Users...\n");

        try {
            DB::beginTransaction();

            // 1. Demo Client
            $this->command->line('Creating Client...');
            $client = User::create([
                'name' => 'John Client',
                'email' => 'client@demo.test',
                'password' => Hash::make('password123'),
                'phone' => '+8801711111111',
                'role' => 'client',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);
            $this->command->line("  ✓ Client: {$client->email}");

            // 2. Demo Photographer (will be base for multi-role users)
            $this->command->line('Creating Photographer...');
            $photographer_user = User::create([
                'name' => 'Ahmed Photography',
                'email' => 'photographer@demo.test',
                'password' => Hash::make('password123'),
                'phone' => '+8801722222222',
                'role' => 'photographer',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);

            // Auto-create photographer profile
            Photographer::create([
                'user_id' => $photographer_user->id,
                'slug' => 'ahmed-photography',
                'bio' => 'Specializing in wedding and portrait photography with 10 years of experience.',
                'location' => 'Dhaka, Bangladesh',
                'city_id' => 1, // Dhaka
                'experience_years' => 10,
                'specializations' => 'wedding,portrait,commercial',
                'is_verified' => true,
                'verification_type' => 'phone_email_id',
                'verified_at' => now(),
                'facebook_url' => 'https://facebook.com/ahmed',
                'instagram_url' => 'https://instagram.com/ahmed_photography',
            ]);
            $this->command->line("  ✓ Photographer: {$photographer_user->email}");

            // 3. Photographer + Mentor
            $this->command->line('Creating Photographer + Mentor...');
            $photo_mentor_user = User::create([
                'name' => 'Fatima Professional',
                'email' => 'fatima.pro@demo.test',
                'password' => Hash::make('password123'),
                'phone' => '+8801733333333',
                'role' => 'photographer',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);

            // Create photographer profile
            Photographer::create([
                'user_id' => $photo_mentor_user->id,
                'slug' => 'fatima-professional',
                'bio' => 'Award-winning photographer with expertise in commercial and editorial photography.',
                'location' => 'Dhaka, Bangladesh',
                'city_id' => 1,
                'experience_years' => 15,
                'specializations' => 'commercial,editorial,fashion',
                'is_verified' => true,
                'verification_type' => 'phone_email_id',
                'verified_at' => now(),
                'facebook_url' => 'https://facebook.com/fatima',
                'instagram_url' => 'https://instagram.com/fatima_professional',
            ]);

            // Promote to mentor
            Mentor::create([
                'user_id' => $photo_mentor_user->id,
                'name' => 'Fatima Professional',
                'title' => 'Senior Photography Mentor',
                'organization' => 'Bangladesh Photography Academy',
                'bio' => 'Mentoring aspiring photographers with proven techniques and industry insights. 15+ years experience.',
                'email' => 'fatima.pro@demo.test',
                'phone' => '+8801733333333',
                'country' => 'Bangladesh',
                'city' => 'Dhaka',
                'is_active' => true,
            ]);
            $this->command->line("  ✓ Photographer + Mentor: {$photo_mentor_user->email}");

            // 4. Photographer + Judge
            $this->command->line('Creating Photographer + Judge...');
            $photo_judge_user = User::create([
                'name' => 'Karim Judge',
                'email' => 'karim.judge@demo.test',
                'password' => Hash::make('password123'),
                'phone' => '+8801744444444',
                'role' => 'photographer',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);

            // Create photographer profile
            Photographer::create([
                'user_id' => $photo_judge_user->id,
                'slug' => 'karim-judge',
                'bio' => 'Renowned photographer and international competition judge with 20 years of experience.',
                'location' => 'Dhaka, Bangladesh',
                'city_id' => 1,
                'experience_years' => 20,
                'specializations' => 'landscape,nature,wildlife',
                'is_verified' => true,
                'verification_type' => 'phone_email_id',
                'verified_at' => now(),
                'facebook_url' => 'https://facebook.com/karim',
                'instagram_url' => 'https://instagram.com/karim_judge',
            ]);

            // Promote to judge
            Judge::create([
                'user_id' => $photo_judge_user->id,
                'name' => 'Karim Judge',
                'title' => 'International Photography Judge',
                'organization' => 'International Photography Federation',
                'bio' => 'Judging photography competitions for over 15 years. Specialized in landscape and nature photography.',
                'email' => 'karim.judge@demo.test',
                'is_active' => true,
            ]);
            $this->command->line("  ✓ Photographer + Judge: {$photo_judge_user->email}");

            // 5. Photographer + Mentor + Judge (Triple Role)
            $this->command->line('Creating Photographer + Mentor + Judge (Triple Role)...');
            $triple_user = User::create([
                'name' => 'Nadia Master',
                'email' => 'nadia.master@demo.test',
                'password' => Hash::make('password123'),
                'phone' => '+8801755555555',
                'role' => 'photographer',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);

            // Create photographer profile
            Photographer::create([
                'user_id' => $triple_user->id,
                'slug' => 'nadia-master',
                'bio' => 'Multi-awarded photographer, mentor, and international judge. Pioneer in digital photography.',
                'location' => 'Dhaka, Bangladesh',
                'city_id' => 1,
                'experience_years' => 25,
                'specializations' => 'commercial,editorial,fine-art,digital',
                'is_verified' => true,
                'verification_type' => 'phone_email_id',
                'verified_at' => now(),
                'is_featured' => true,
                'featured_until' => now()->addYear(),
                'facebook_url' => 'https://facebook.com/nadia',
                'instagram_url' => 'https://instagram.com/nadia_master',
            ]);

            // Promote to mentor
            Mentor::create([
                'user_id' => $triple_user->id,
                'name' => 'Nadia Master',
                'title' => 'Master Photography Mentor',
                'organization' => 'International Photography Institute',
                'bio' => 'Mentoring photographers worldwide with focus on technical excellence and artistic vision.',
                'email' => 'nadia.master@demo.test',
                'phone' => '+8801755555555',
                'country' => 'Bangladesh',
                'city' => 'Dhaka',
                'is_active' => true,
            ]);

            // Promote to judge
            Judge::create([
                'user_id' => $triple_user->id,
                'name' => 'Nadia Master',
                'title' => 'Master Photography Judge',
                'organization' => 'World Photography Federation',
                'bio' => 'Chief judge for international photography competitions. 25+ years judging experience.',
                'email' => 'nadia.master@demo.test',
                'is_active' => true,
            ]);
            $this->command->line("  ✓ Photographer + Mentor + Judge: {$triple_user->email}");

            // 6. Studio Owner
            $this->command->line('Creating Studio Owner...');
            $studio_owner = User::create([
                'name' => 'Studio Bangladesh',
                'email' => 'studio.owner@demo.test',
                'password' => Hash::make('password123'),
                'phone' => '+8801766666666',
                'role' => 'studio_owner',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);
            $this->command->line("  ✓ Studio Owner: {$studio_owner->email}");

            // 7. Studio Photographer
            $this->command->line('Creating Studio Photographer...');
            $studio_photographer = User::create([
                'name' => 'Studio Assistant',
                'email' => 'studio.assistant@demo.test',
                'password' => Hash::make('password123'),
                'phone' => '+8801777777777',
                'role' => 'studio_photographer',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);
            $this->command->line("  ✓ Studio Photographer: {$studio_photographer->email}");

            // 8. Moderator
            $this->command->line('Creating Moderator...');
            $moderator = User::create([
                'name' => 'Platform Moderator',
                'email' => 'moderator@demo.test',
                'password' => Hash::make('password123'),
                'phone' => '+8801788888888',
                'role' => 'moderator',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);
            $this->command->line("  ✓ Moderator: {$moderator->email}");

            // 9. Admin
            $this->command->line('Creating Admin...');
            $admin = User::create([
                'name' => 'Demo Admin',
                'email' => 'admin.demo@test',
                'password' => Hash::make('password123'),
                'phone' => '+8801799999999',
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_suspended' => false,
            ]);
            $this->command->line("  ✓ Admin: {$admin->email}");

            DB::commit();

            $this->command->newLine();
            $this->command->info("✅ Demo Multi-Role Users Created Successfully!\n");

            $this->command->line("📝 Demo Account Credentials:");
            $this->command->line("────────────────────────────────────────────────");
            $this->command->line("  Client:");
            $this->command->line("    📧 client@demo.test");
            $this->command->line("    🔑 password123");
            $this->command->newLine();

            $this->command->line("  Photographer:");
            $this->command->line("    📧 photographer@demo.test");
            $this->command->line("    🔑 password123");
            $this->command->newLine();

            $this->command->line("  Photographer + Mentor:");
            $this->command->line("    📧 fatima.pro@demo.test");
            $this->command->line("    🔑 password123");
            $this->command->newLine();

            $this->command->line("  Photographer + Judge:");
            $this->command->line("    📧 karim.judge@demo.test");
            $this->command->line("    🔑 password123");
            $this->command->newLine();

            $this->command->line("  Photographer + Mentor + Judge (Triple Role):");
            $this->command->line("    📧 nadia.master@demo.test");
            $this->command->line("    🔑 password123");
            $this->command->newLine();

            $this->command->line("  Studio Owner:");
            $this->command->line("    📧 studio.owner@demo.test");
            $this->command->line("    🔑 password123");
            $this->command->newLine();

            $this->command->line("  Studio Photographer:");
            $this->command->line("    📧 studio.assistant@demo.test");
            $this->command->line("    🔑 password123");
            $this->command->newLine();

            $this->command->line("  Moderator:");
            $this->command->line("    📧 moderator@demo.test");
            $this->command->line("    🔑 password123");
            $this->command->newLine();

            $this->command->line("  Admin:");
            $this->command->line("    📧 admin.demo@test");
            $this->command->line("    🔑 password123");
            $this->command->line("────────────────────────────────────────────────\n");

            $this->command->info("🎯 Test Cases:");
            $this->command->line("  1. Login as 'photographer@demo.test' → See photographer dashboard");
            $this->command->line("  2. Login as 'fatima.pro@demo.test' → See both photographer & mentor dashboards");
            $this->command->line("  3. Login as 'karim.judge@demo.test' → See photographer & judge dashboards");
            $this->command->line("  4. Login as 'nadia.master@demo.test' → See all 3 dashboards");
            $this->command->line("  5. Login as 'admin.demo@test' → Use Admin Panel to promote/manage users\n");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
            throw $e;
        }
    }
}
