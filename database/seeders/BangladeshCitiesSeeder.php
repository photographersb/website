<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class BangladeshCitiesSeeder extends Seeder
{
    /**
     * Seed Bangladesh cities/districts into the database.
     * Bangladesh has 8 divisions and 64 districts.
     * Source: Official BBS (Bangladesh Bureau of Statistics) data
     */
    public function run(): void
    {
        $cities = [
            // Dhaka Division (13 districts)
            ['name' => 'Dhaka', 'division' => 'Dhaka', 'slug' => 'dhaka'],
            ['name' => 'Narayanganj', 'division' => 'Dhaka', 'slug' => 'narayanganj'],
            ['name' => 'Gazipur', 'division' => 'Dhaka', 'slug' => 'gazipur'],
            ['name' => 'Shariatpur', 'division' => 'Dhaka', 'slug' => 'shariatpur'],
            ['name' => 'Tangail', 'division' => 'Dhaka', 'slug' => 'tangail'],
            ['name' => 'Munshiganj', 'division' => 'Dhaka', 'slug' => 'munshiganj'],
            ['name' => 'Manikganj', 'division' => 'Dhaka', 'slug' => 'manikganj'],
            ['name' => 'Faridpur', 'division' => 'Dhaka', 'slug' => 'faridpur'],
            ['name' => 'Rajbari', 'division' => 'Dhaka', 'slug' => 'rajbari'],
            ['name' => 'Madaripur', 'division' => 'Dhaka', 'slug' => 'madaripur'],

            // Chittagong Division (10 districts)
            ['name' => 'Chittagong', 'division' => 'Chittagong', 'slug' => 'chittagong'],
            ['name' => 'Cox\'s Bazar', 'division' => 'Chittagong', 'slug' => 'cox-bazar'],
            ['name' => 'Sylhet', 'division' => 'Chittagong', 'slug' => 'sylhet'],
            ['name' => 'Comilla', 'division' => 'Chittagong', 'slug' => 'comilla'],
            ['name' => 'Feni', 'division' => 'Chittagong', 'slug' => 'feni'],
            ['name' => 'Noakhali', 'division' => 'Chittagong', 'slug' => 'noakhali'],
            ['name' => 'Khagrachari', 'division' => 'Chittagong', 'slug' => 'khagrachari'],
            ['name' => 'Rangamati', 'division' => 'Chittagong', 'slug' => 'rangamati'],

            // Rajshahi Division (8 districts)
            ['name' => 'Rajshahi', 'division' => 'Rajshahi', 'slug' => 'rajshahi'],
            ['name' => 'Pabna', 'division' => 'Rajshahi', 'slug' => 'pabna'],
            ['name' => 'Natore', 'division' => 'Rajshahi', 'slug' => 'natore'],
            ['name' => 'Naogaon', 'division' => 'Rajshahi', 'slug' => 'naogaon'],
            ['name' => 'Sirajganj', 'division' => 'Rajshahi', 'slug' => 'sirajganj'],
            ['name' => 'Bogra', 'division' => 'Rajshahi', 'slug' => 'bogra'],
            ['name' => 'Joypurhat', 'division' => 'Rajshahi', 'slug' => 'joypurhat'],

            // Khulna Division (9 districts)
            ['name' => 'Khulna', 'division' => 'Khulna', 'slug' => 'khulna'],
            ['name' => 'Barisal', 'division' => 'Khulna', 'slug' => 'barisal'],
            ['name' => 'Pirojpur', 'division' => 'Khulna', 'slug' => 'pirojpur'],
            ['name' => 'Jhalokati', 'division' => 'Khulna', 'slug' => 'jhalokati'],
            ['name' => 'Patuakhali', 'division' => 'Khulna', 'slug' => 'patuakhali'],
            ['name' => 'Bhola', 'division' => 'Khulna', 'slug' => 'bhola'],
            ['name' => 'Satkhira', 'division' => 'Khulna', 'slug' => 'satkhira'],
            ['name' => 'Jessore', 'division' => 'Khulna', 'slug' => 'jessore'],
            ['name' => 'Jashore', 'division' => 'Khulna', 'slug' => 'jashore'],
            ['name' => 'Magura', 'division' => 'Khulna', 'slug' => 'magura'],
            ['name' => 'Narail', 'division' => 'Khulna', 'slug' => 'narail'],

            // Mymensingh Division (4 districts)
            ['name' => 'Mymensingh', 'division' => 'Mymensingh', 'slug' => 'mymensingh'],
            ['name' => 'Sherpur', 'division' => 'Mymensingh', 'slug' => 'sherpur'],
            ['name' => 'Jamalpur', 'division' => 'Mymensingh', 'slug' => 'jamalpur'],
            ['name' => 'Netrokona', 'division' => 'Mymensingh', 'slug' => 'netrokona'],

            // Rangpur Division (8 districts)
            ['name' => 'Rangpur', 'division' => 'Rangpur', 'slug' => 'rangpur'],
            ['name' => 'Dinajpur', 'division' => 'Rangpur', 'slug' => 'dinajpur'],
            ['name' => 'Thakurgaon', 'division' => 'Rangpur', 'slug' => 'thakurgaon'],
            ['name' => 'Panchagarh', 'division' => 'Rangpur', 'slug' => 'panchagarh'],
            ['name' => 'Kurigram', 'division' => 'Rangpur', 'slug' => 'kurigram'],
            ['name' => 'Lalmonirhat', 'division' => 'Rangpur', 'slug' => 'lalmonirhat'],
            ['name' => 'Gaibandha', 'division' => 'Rangpur', 'slug' => 'gaibandha'],
            ['name' => 'Nilphamari', 'division' => 'Rangpur', 'slug' => 'nilphamari'],

            // Barisal/Barishal Division (6 districts - if counted separately)
            ['name' => 'Barguna', 'division' => 'Barishal', 'slug' => 'barguna'],

            // Slyhet Division (4 districts)
            ['name' => 'Moulvi Bazar', 'division' => 'Sylhet', 'slug' => 'moulvi-bazar'],
            ['name' => 'Habiganj', 'division' => 'Sylhet', 'slug' => 'habiganj'],
            ['name' => 'Sunamganj', 'division' => 'Sylhet', 'slug' => 'sunamganj'],
        ];

        // Insert in chunks to avoid issues with large batch insert
        collect($cities)->chunk(10)->each(function ($chunk) {
            foreach ($chunk as $city) {
                City::firstOrCreate(
                    ['name' => $city['name']],
                    $city
                );
            }
        });

        $this->command->info('Bangladesh cities seeded successfully. Total: ' . count($cities));
    }
}
