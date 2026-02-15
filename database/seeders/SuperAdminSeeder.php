<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👑 Ensuring Super Admin user...');

        $superAdminEmail = 'mahidulislamnakib@gmail.com';
        
        // Check if super admin exists
        $superAdmin = User::where('email', $superAdminEmail)->first();

        if ($superAdmin) {
            // Update to ensure super admin status
            $superAdmin->update([
                'role' => 'super_admin',
                'email_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by_admin_id' => null, // Super admin doesn't need approver
                'is_suspended' => false,
                'suspension_reason' => null,
                'suspended_at' => null,
            ]);
            $this->command->line("  ✓ Super Admin updated (email: {$superAdminEmail})");
        } else {
            // Create new super admin
            $superAdmin = User::create([
                'uuid' => Str::uuid(),
                'name' => 'Mahidul Islam Nakib',
                'email' => $superAdminEmail,
                'phone' => '01700000000',
                'password' => Hash::make('SuperAdmin@2026'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by_admin_id' => null, // Super admin doesn't need approver
                'avatar_url' => null,
                'bio' => 'Principal Architect & System Administrator - Somogro Bangladesh',
                'is_suspended' => false,
                'last_login_at' => now(),
                'two_fa_enabled' => false,
            ]);
            $this->command->line("  ✓ Super Admin created (email: {$superAdminEmail})");
        }

        $this->command->info('✅ Super Admin setup complete');
        $this->command->line("\n⚠️  Default password: SuperAdmin@2026");
        $this->command->line("⚠️  Please change password immediately after login!");
    }
}
