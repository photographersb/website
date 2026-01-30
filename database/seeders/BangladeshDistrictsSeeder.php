<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BangladeshDistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            // Dhaka Division
            ['name' => 'Dhaka', 'state' => 'Dhaka Division'],
            ['name' => 'Faridpur', 'state' => 'Dhaka Division'],
            ['name' => 'Gazipur', 'state' => 'Dhaka Division'],
            ['name' => 'Gopalganj', 'state' => 'Dhaka Division'],
            ['name' => 'Kishoreganj', 'state' => 'Dhaka Division'],
            ['name' => 'Madaripur', 'state' => 'Dhaka Division'],
            ['name' => 'Manikganj', 'state' => 'Dhaka Division'],
            ['name' => 'Munshiganj', 'state' => 'Dhaka Division'],
            ['name' => 'Narayanganj', 'state' => 'Dhaka Division'],
            ['name' => 'Narsingdi', 'state' => 'Dhaka Division'],
            ['name' => 'Rajbari', 'state' => 'Dhaka Division'],
            ['name' => 'Shariatpur', 'state' => 'Dhaka Division'],
            ['name' => 'Tangail', 'state' => 'Dhaka Division'],
            
            // Chittagong Division
            ['name' => 'Bandarban', 'state' => 'Chittagong Division'],
            ['name' => 'Brahmanbaria', 'state' => 'Chittagong Division'],
            ['name' => 'Chandpur', 'state' => 'Chittagong Division'],
            ['name' => 'Chattogram', 'state' => 'Chittagong Division'],
            ['name' => 'Comilla', 'state' => 'Chittagong Division'],
            ['name' => 'Cox\'s Bazar', 'state' => 'Chittagong Division'],
            ['name' => 'Feni', 'state' => 'Chittagong Division'],
            ['name' => 'Khagrachari', 'state' => 'Chittagong Division'],
            ['name' => 'Lakshmipur', 'state' => 'Chittagong Division'],
            ['name' => 'Noakhali', 'state' => 'Chittagong Division'],
            ['name' => 'Rangamati', 'state' => 'Chittagong Division'],
            
            // Rajshahi Division
            ['name' => 'Bogura', 'state' => 'Rajshahi Division'],
            ['name' => 'Joypurhat', 'state' => 'Rajshahi Division'],
            ['name' => 'Naogaon', 'state' => 'Rajshahi Division'],
            ['name' => 'Natore', 'state' => 'Rajshahi Division'],
            ['name' => 'Chapainawabganj', 'state' => 'Rajshahi Division'],
            ['name' => 'Pabna', 'state' => 'Rajshahi Division'],
            ['name' => 'Rajshahi', 'state' => 'Rajshahi Division'],
            ['name' => 'Sirajganj', 'state' => 'Rajshahi Division'],
            
            // Khulna Division
            ['name' => 'Bagerhat', 'state' => 'Khulna Division'],
            ['name' => 'Chuadanga', 'state' => 'Khulna Division'],
            ['name' => 'Jessore', 'state' => 'Khulna Division'],
            ['name' => 'Jhenaidah', 'state' => 'Khulna Division'],
            ['name' => 'Khulna', 'state' => 'Khulna Division'],
            ['name' => 'Kushtia', 'state' => 'Khulna Division'],
            ['name' => 'Magura', 'state' => 'Khulna Division'],
            ['name' => 'Meherpur', 'state' => 'Khulna Division'],
            ['name' => 'Narail', 'state' => 'Khulna Division'],
            ['name' => 'Satkhira', 'state' => 'Khulna Division'],
            
            // Barisal Division
            ['name' => 'Barguna', 'state' => 'Barisal Division'],
            ['name' => 'Barisal', 'state' => 'Barisal Division'],
            ['name' => 'Bhola', 'state' => 'Barisal Division'],
            ['name' => 'Jhalokati', 'state' => 'Barisal Division'],
            ['name' => 'Patuakhali', 'state' => 'Barisal Division'],
            ['name' => 'Pirojpur', 'state' => 'Barisal Division'],
            
            // Sylhet Division
            ['name' => 'Habiganj', 'state' => 'Sylhet Division'],
            ['name' => 'Moulvibazar', 'state' => 'Sylhet Division'],
            ['name' => 'Sunamganj', 'state' => 'Sylhet Division'],
            ['name' => 'Sylhet', 'state' => 'Sylhet Division'],
            
            // Rangpur Division
            ['name' => 'Dinajpur', 'state' => 'Rangpur Division'],
            ['name' => 'Gaibandha', 'state' => 'Rangpur Division'],
            ['name' => 'Kurigram', 'state' => 'Rangpur Division'],
            ['name' => 'Lalmonirhat', 'state' => 'Rangpur Division'],
            ['name' => 'Nilphamari', 'state' => 'Rangpur Division'],
            ['name' => 'Panchagarh', 'state' => 'Rangpur Division'],
            ['name' => 'Rangpur', 'state' => 'Rangpur Division'],
            ['name' => 'Thakurgaon', 'state' => 'Rangpur Division'],
            
            // Mymensingh Division
            ['name' => 'Jamalpur', 'state' => 'Mymensingh Division'],
            ['name' => 'Mymensingh', 'state' => 'Mymensingh Division'],
            ['name' => 'Netrokona', 'state' => 'Mymensingh Division'],
            ['name' => 'Sherpur', 'state' => 'Mymensingh Division'],
        ];

        foreach ($districts as $district) {
            \App\Models\City::updateOrCreate(
                ['name' => $district['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($district['name']),
                    'state' => $district['state']
                ]
            );
        }
    }
}
