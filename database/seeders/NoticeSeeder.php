<?php

namespace Database\Seeders;

use App\Models\Notice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->first();

        if (!$admin) {
            $this->command->warn('No admin user found. Skipping notice seeding.');
            return;
        }

        $notices = [
            [
                'title' => 'Welcome to Photographer SB Admin',
                'message' => 'Welcome! This is your admin dashboard. Use the menu to manage users, photographers, competitions, and more.',
                'priority' => 'normal',
                'status' => 'published',
                'publish_at' => now(),
                'expires_at' => now()->addMonths(1),
                'icon' => 'bell',
                'color' => 'blue',
                'show_to_all_roles' => true,
                'created_by' => $admin->id,
                'roles' => ['admin', 'super_admin'],
            ],
            [
                'title' => 'Pending Verifications',
                'message' => 'You have pending photographer verifications. Please review them in the Verifications section.',
                'priority' => 'high',
                'status' => 'published',
                'publish_at' => now(),
                'expires_at' => now()->addDays(7),
                'icon' => 'check-circle',
                'color' => 'amber',
                'show_to_all_roles' => false,
                'created_by' => $admin->id,
                'roles' => ['admin', 'super_admin'],
            ],
            [
                'title' => 'System Maintenance',
                'message' => 'System maintenance is scheduled for tonight at 11 PM. Platform will be briefly unavailable.',
                'priority' => 'urgent',
                'status' => 'draft',
                'publish_at' => now()->addDays(1),
                'expires_at' => now()->addDays(2),
                'icon' => 'wrench',
                'color' => 'red',
                'show_to_all_roles' => true,
                'created_by' => $admin->id,
                'roles' => ['admin', 'super_admin', 'photographer', 'organizer', 'client'],
            ],
            [
                'title' => 'New Photography Competition Live',
                'message' => 'A new photography competition "Nature Wonders 2026" is now live! Photographers can start submitting their entries.',
                'priority' => 'normal',
                'status' => 'published',
                'publish_at' => now(),
                'expires_at' => now()->addMonths(1),
                'icon' => 'trophy',
                'color' => 'green',
                'show_to_all_roles' => false,
                'created_by' => $admin->id,
                'roles' => ['photographer'],
            ],
            [
                'title' => 'Event Management Tips',
                'message' => 'Check out our new guide on how to effectively manage and organize events on Photographer SB.',
                'priority' => 'low',
                'status' => 'published',
                'publish_at' => now(),
                'expires_at' => now()->addMonths(3),
                'icon' => 'lightbulb',
                'color' => 'purple',
                'show_to_all_roles' => false,
                'created_by' => $admin->id,
                'roles' => ['organizer'],
            ],
        ];

        foreach ($notices as $noticeData) {
            $roles = $noticeData['roles'];
            unset($noticeData['roles']);

            $notice = Notice::updateOrCreate(
                ['title' => $noticeData['title']],
                $noticeData
            );

            if (!$noticeData['show_to_all_roles']) {
                $notice->attachRoles($roles);
            }
        }

        $this->command->info('✓ Notice seeding completed');
    }
}
