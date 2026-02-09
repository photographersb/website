<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sponsor;
use App\Models\Judge;
use App\Models\Mentor;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'phone' => '01700000001',
        ]);

        // Create judges
        $judgeUsers = User::factory(3)->judge()->create();
        foreach ($judgeUsers as $user) {
            Judge::factory()->create(['user_id' => $user->id]);
        }

        // Create mentors
        Mentor::factory(5)->create();

        // Create sponsors
        Sponsor::factory(3)->gold()->create();
        Sponsor::factory(5)->silver()->create();
        Sponsor::factory(5)->bronze()->create();

        // Create test photographers and clients
        User::factory(10)->photographer()->create();
        User::factory(15)->client()->create();

        // Create photographers with profiles
        \App\Models\Photographer::factory(10)->create();
    }
}
