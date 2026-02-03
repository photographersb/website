<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SponsorsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🤝 Seeding sponsors...');

        $sponsors = [
            [
                'name' => 'Canon Bangladesh',
                'logo' => 'https://via.placeholder.com/200x100?text=Canon+Bangladesh',
                'website' => 'https://canon.com',
                'description' => 'Leading photography equipment provider in Bangladesh',
                'status' => 'active',
            ],
            [
                'name' => 'Sony Bangladesh',
                'logo' => 'https://via.placeholder.com/200x100?text=Sony+Bangladesh',
                'website' => 'https://sony.com',
                'description' => 'Premium camera and imaging solutions',
                'status' => 'active',
            ],
            [
                'name' => 'Nikon Bangladesh',
                'logo' => 'https://via.placeholder.com/200x100?text=Nikon+Bangladesh',
                'website' => 'https://nikon.com',
                'description' => 'Professional cameras and lenses for creators',
                'status' => 'active',
            ],
            [
                'name' => 'DJI Bangladesh',
                'logo' => 'https://via.placeholder.com/200x100?text=DJI+Bangladesh',
                'website' => 'https://dji.com',
                'description' => 'Drone and aerial photography technology',
                'status' => 'active',
            ],
            [
                'name' => 'Somogro Bangladesh',
                'logo' => 'https://via.placeholder.com/200x100?text=Somogro+Bangladesh',
                'website' => 'https://somogro.com',
                'description' => 'Bangladesh travel and marketplace platform',
                'status' => 'active',
            ],
            [
                'name' => 'Bidesh Gomon',
                'logo' => 'https://via.placeholder.com/200x100?text=Bidesh+Gomon',
                'website' => 'https://bideshgomon.com',
                'description' => 'International travel and tour partner',
                'status' => 'active',
            ],
            [
                'name' => 'Tripnow',
                'logo' => 'https://via.placeholder.com/200x100?text=Tripnow',
                'website' => 'https://tripnow.com',
                'description' => 'Travel booking partner for events and tours',
                'status' => 'active',
            ],
            [
                'name' => 'Zatra360',
                'logo' => 'https://via.placeholder.com/200x100?text=Zatra360',
                'website' => 'https://zatra360.com',
                'description' => '360° travel experiences and event partner',
                'status' => 'active',
            ],
            [
                'name' => 'School Alt',
                'logo' => 'https://via.placeholder.com/200x100?text=School+Alt',
                'website' => 'https://schoolalt.com',
                'description' => 'Education and community engagement partner',
                'status' => 'active',
            ],
        ];

        foreach ($sponsors as $index => $sponsor) {
            DB::table('sponsors')->updateOrInsert(
                ['slug' => Str::slug($sponsor['name'])],
                [
                    'name' => $sponsor['name'],
                    'slug' => Str::slug($sponsor['name']),
                    'logo' => $sponsor['logo'],
                    'website' => $sponsor['website'],
                    'description' => $sponsor['description'],
                    'status' => $sponsor['status'],
                    'display_order' => $index + 1,
                    'start_date' => now()->subMonths(6)->toDateString(),
                    'end_date' => now()->addMonths(6)->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $this->command->line("  ✓ {$sponsor['name']}");
        }

        $this->command->info('✅ Sponsors seeded');
    }
}
