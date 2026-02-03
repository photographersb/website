<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Photographer;
use Illuminate\Support\Str;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::where('email', 'admin@photographar.com')->first();
        
        if (!$admin) {
            $admin = User::create([
                'uuid' => Str::uuid(),
                'name' => 'Admin User',
                'email' => 'admin@photographar.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]);
            $this->command->info('✓ Admin created: admin@photographar.com / admin123');
        } else {
            $admin->update([
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]);
            $this->command->info('✓ Admin updated: admin@photographar.com / admin123');
        }

        // Create Test Photographer
        $photographerUser = User::where('email', 'photographer@test.com')->first();
        
        if (!$photographerUser) {
            $photographerUser = User::create([
                'uuid' => Str::uuid(),
                'name' => 'Test Photographer',
                'email' => 'photographer@test.com',
                'password' => bcrypt('photo123'),
                'role' => 'photographer',
                'email_verified_at' => now()
            ]);

            // Create photographer profile
            Photographer::create([
                'user_id' => $photographerUser->id,
                'slug' => 'test-photographer',
                'bio' => 'Professional photographer with 10+ years experience in nature and wildlife photography',
                'specializations' => json_encode(['Nature', 'Wildlife', 'Landscape']),
                'is_verified' => true,
                'verification_type' => 'phone_email_id',
                'verified_at' => now(),
                'experience_years' => 10,
                'city_id' => 1,
            ]);

            $this->command->info('✓ Photographer created: photographer@test.com / photo123');
        } else {
            $photographerUser->update([
                'password' => bcrypt('photo123'),
                'role' => 'photographer',
                'email_verified_at' => now()
            ]);

            // Update photographer profile if exists
            $photographer = Photographer::where('user_id', $photographerUser->id)->first();
            if ($photographer) {
                $photographer->update([
                    'is_verified' => true,
                    'verification_type' => 'phone_email_id',
                    'verified_at' => now()
                ]);
            } else {
                Photographer::create([
                    'user_id' => $photographerUser->id,
                    'slug' => 'test-photographer',
                    'bio' => 'Professional photographer with 10+ years experience in nature and wildlife photography',
                    'specializations' => json_encode(['Nature', 'Wildlife', 'Landscape']),
                    'is_verified' => true,
                    'verification_type' => 'phone_email_id',
                    'verified_at' => now(),
                    'experience_years' => 10,
                    'city_id' => 1,
                ]);
            }

            $this->command->info('✓ Photographer updated: photographer@test.com / photo123');
        }

        // Create Test Client
        $client = User::where('email', 'client@test.com')->first();
        
        if (!$client) {
            User::create([
                'uuid' => Str::uuid(),
                'name' => 'Test Client',
                'email' => 'client@test.com',
                'password' => bcrypt('client123'),
                'role' => 'client',
                'email_verified_at' => now()
            ]);
            $this->command->info('✓ Client created: client@test.com / client123');
        } else {
            $client->update([
                'password' => bcrypt('client123'),
                'email_verified_at' => now()
            ]);
            $this->command->info('✓ Client updated: client@test.com / client123');
        }

        $this->command->newLine();
        $this->command->info('Test users ready!');
        $this->command->info('Admin:        admin@photographar.com / admin123');
        $this->command->info('Photographer: photographer@test.com / photo123');
        $this->command->info('Client:       client@test.com / client123');
    }
}
