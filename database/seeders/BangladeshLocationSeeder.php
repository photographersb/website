<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BangladeshLocationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🗺️  Seeding Bangladesh locations...');

        // All 64 Districts of Bangladesh organized by Division
        $districts = [
            // Dhaka Division (13)
            ['name' => 'Dhaka', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'dhaka'],
            ['name' => 'Gazipur', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'gazipur'],
            ['name' => 'Narayanganj', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'narayanganj'],
            ['name' => 'Tangail', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'tangail'],
            ['name' => 'Munshiganj', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'munshiganj'],
            ['name' => 'Manikganj', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'manikganj'],
            ['name' => 'Narsingdi', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'narsingdi'],
            ['name' => 'Kishoreganj', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'kishoreganj'],
            ['name' => 'Gopalganj', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'gopalganj'],
            ['name' => 'Faridpur', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'faridpur'],
            ['name' => 'Madaripur', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'madaripur'],
            ['name' => 'Rajbari', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'rajbari'],
            ['name' => 'Shariatpur', 'division' => 'Dhaka', 'state' => 'BD', 'slug' => 'shariatpur'],

            // Chittagong Division (13)
            ['name' => 'Chittagong', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'chittagong'],
            ['name' => 'Cox\'s Bazar', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'coxs-bazar'],
            ['name' => 'Bandarban', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'bandarban'],
            ['name' => 'Rangamati', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'rangamati'],
            ['name' => 'Feni', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'feni'],
            ['name' => 'Noakhali', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'noakhali'],
            ['name' => 'Comilla', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'comilla'],
            ['name' => 'Chandpur', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'chandpur'],
            ['name' => 'Lakshmipur', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'lakshmipur'],
            ['name' => 'Khagrachhari', 'division' => 'Chittagong', 'state' => 'BD', 'slug' => 'khagrachhari'],

            // Barisal Division (6)
            ['name' => 'Barisal', 'division' => 'Barisal', 'state' => 'BD', 'slug' => 'barisal'],
            ['name' => 'Bhola', 'division' => 'Barisal', 'state' => 'BD', 'slug' => 'bhola'],
            ['name' => 'Pirojpur', 'division' => 'Barisal', 'state' => 'BD', 'slug' => 'pirojpur'],
            ['name' => 'Patuakhali', 'division' => 'Barisal', 'state' => 'BD', 'slug' => 'patuakhali'],
            ['name' => 'Barguna', 'division' => 'Barisal', 'state' => 'BD', 'slug' => 'barguna'],
            ['name' => 'Jhalokati', 'division' => 'Barisal', 'state' => 'BD', 'slug' => 'jhalokati'],

            // Khulna Division (10)
            ['name' => 'Khulna', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'khulna'],
            ['name' => 'Satkhira', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'satkhira'],
            ['name' => 'Jessore', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'jessore'],
            ['name' => 'Jashore', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'jashore'],
            ['name' => 'Magura', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'magura'],
            ['name' => 'Narail', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'narail'],
            ['name' => 'Bagerhat', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'bagerhat'],
            ['name' => 'Chuadanga', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'chuadanga'],
            ['name' => 'Meherpur', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'meherpur'],
            ['name' => 'Kushtia', 'division' => 'Khulna', 'state' => 'BD', 'slug' => 'kushtia'],

            // Rajshahi Division (8)
            ['name' => 'Rajshahi', 'division' => 'Rajshahi', 'state' => 'BD', 'slug' => 'rajshahi'],
            ['name' => 'Bogra', 'division' => 'Rajshahi', 'state' => 'BD', 'slug' => 'bogra'],
            ['name' => 'Naogaon', 'division' => 'Rajshahi', 'state' => 'BD', 'slug' => 'naogaon'],
            ['name' => 'Natore', 'division' => 'Rajshahi', 'state' => 'BD', 'slug' => 'natore'],
            ['name' => 'Nawabganj', 'division' => 'Rajshahi', 'state' => 'BD', 'slug' => 'nawabganj'],
            ['name' => 'Pabna', 'division' => 'Rajshahi', 'state' => 'BD', 'slug' => 'pabna'],
            ['name' => 'Sirajganj', 'division' => 'Rajshahi', 'state' => 'BD', 'slug' => 'sirajganj'],
            ['name' => 'Chapainawabganj', 'division' => 'Rajshahi', 'state' => 'BD', 'slug' => 'chapainawabganj'],

            // Rangpur Division (8)
            ['name' => 'Rangpur', 'division' => 'Rangpur', 'state' => 'BD', 'slug' => 'rangpur'],
            ['name' => 'Dinajpur', 'division' => 'Rangpur', 'state' => 'BD', 'slug' => 'dinajpur'],
            ['name' => 'Nilphamari', 'division' => 'Rangpur', 'state' => 'BD', 'slug' => 'nilphamari'],
            ['name' => 'Kurigram', 'division' => 'Rangpur', 'state' => 'BD', 'slug' => 'kurigram'],
            ['name' => 'Gaibandha', 'division' => 'Rangpur', 'state' => 'BD', 'slug' => 'gaibandha'],
            ['name' => 'Lalmonirhat', 'division' => 'Rangpur', 'state' => 'BD', 'slug' => 'lalmonirhat'],
            ['name' => 'Thakurgaon', 'division' => 'Rangpur', 'state' => 'BD', 'slug' => 'thakurgaon'],
            ['name' => 'Panchagarh', 'division' => 'Rangpur', 'state' => 'BD', 'slug' => 'panchagarh'],

            // Mymensingh Division (4)
            ['name' => 'Mymensingh', 'division' => 'Mymensingh', 'state' => 'BD', 'slug' => 'mymensingh'],
            ['name' => 'Jamalpur', 'division' => 'Mymensingh', 'state' => 'BD', 'slug' => 'jamalpur'],
            ['name' => 'Sherpur', 'division' => 'Mymensingh', 'state' => 'BD', 'slug' => 'sherpur'],
            ['name' => 'Netrokona', 'division' => 'Mymensingh', 'state' => 'BD', 'slug' => 'netrokona'],

            // Sylhet Division (4)
            ['name' => 'Sylhet', 'division' => 'Sylhet', 'state' => 'BD', 'slug' => 'sylhet'],
            ['name' => 'Sunamganj', 'division' => 'Sylhet', 'state' => 'BD', 'slug' => 'sunamganj'],
            ['name' => 'Moulvibazar', 'division' => 'Sylhet', 'state' => 'BD', 'slug' => 'moulvibazar'],
            ['name' => 'Habiganj', 'division' => 'Sylhet', 'state' => 'BD', 'slug' => 'habiganj'],
        ];

        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('cities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Insert districts
        foreach ($districts as $key => $district) {
            DB::table('cities')->insert([
                'name' => $district['name'],
                'slug' => $district['slug'],
                'division' => $district['division'],
                'state' => $district['state'],
                'photographer_count' => 0,
                'display_order' => $key + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✅ Seeded 64 Bangladesh districts successfully");
    }
}
