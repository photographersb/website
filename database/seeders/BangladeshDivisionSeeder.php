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
                'country' => 'Bangladesh',
                'latitude' => 23.8103,
                'longitude' => 90.4125,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chittagong',
                'slug' => 'chittagong',
                'division' => 'Chittagong',
                'country' => 'Bangladesh',
                'latitude' => 22.3569,
                'longitude' => 91.7832,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khulna',
                'slug' => 'khulna',
                'division' => 'Khulna',
                'country' => 'Bangladesh',
                'latitude' => 22.8456,
                'longitude' => 89.5403,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rajshahi',
                'slug' => 'rajshahi',
                'division' => 'Rajshahi',
                'country' => 'Bangladesh',
                'latitude' => 24.3745,
                'longitude' => 88.6042,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barisal',
                'slug' => 'barisal',
                'division' => 'Barisal',
                'country' => 'Bangladesh',
                'latitude' => 22.7010,
                'longitude' => 90.3635,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sylhet',
                'slug' => 'sylhet',
                'division' => 'Sylhet',
                'country' => 'Bangladesh',
                'latitude' => 24.8949,
                'longitude' => 91.8687,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rangpur',
                'slug' => 'rangpur',
                'division' => 'Rangpur',
                'country' => 'Bangladesh',
                'latitude' => 25.7439,
                'longitude' => 89.2752,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mymensingh',
                'slug' => 'mymensingh',
                'division' => 'Mymensingh',
                'country' => 'Bangladesh',
                'latitude' => 24.7465,
                'longitude' => 90.4081,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
