<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BangladeshDivisionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cities')->insertOrIgnore([
            [
                'name' => 'Dhaka',
                'slug' => 'dhaka',
                'division' => 'Dhaka',
                'latitude' => 23.8103,
                'longitude' => 90.4125,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chittagong',
                'slug' => 'chittagong',
                'division' => 'Chittagong',
                'latitude' => 22.3569,
                'longitude' => 91.7832,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khulna',
                'slug' => 'khulna',
                'division' => 'Khulna',
                'latitude' => 22.8456,
                'longitude' => 89.5403,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rajshahi',
                'slug' => 'rajshahi',
                'division' => 'Rajshahi',
                'latitude' => 24.3745,
                'longitude' => 88.6042,
                'display_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barisal',
                'slug' => 'barisal',
                'division' => 'Barisal',
                'latitude' => 22.7010,
                'longitude' => 90.3635,
                'display_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sylhet',
                'slug' => 'sylhet',
                'division' => 'Sylhet',
                'latitude' => 24.8949,
                'longitude' => 91.8687,
                'display_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rangpur',
                'slug' => 'rangpur',
                'division' => 'Rangpur',
                'latitude' => 25.7439,
                'longitude' => 89.2752,
                'display_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mymensingh',
                'slug' => 'mymensingh',
                'division' => 'Mymensingh',
                'latitude' => 24.7465,
                'longitude' => 90.4081,
                'display_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
