<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Seeding roles and permissions...');

        // Define roles
        $roles = [
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'moderator' => 'Moderator',
            'photographer' => 'Photographer',
            'organizer' => 'Event Organizer',
            'client' => 'Client',
        ];

        // Roles are managed via enum in users table
        // This seeder just logs what roles are available
        foreach ($roles as $role => $display) {
            $this->command->line("  ✓ {$display} ({$role})");
        }

        $this->command->info('✅ Roles configuration loaded');
    }
}
