<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LocationFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        $bangladeshCities = [
            'Dhaka', 'Chittagong', 'Khulna', 'Rajshahi',
            'Barisal', 'Sylhet', 'Rangpur', 'Mymensingh'
        ];
        
        $city = $this->faker->randomElement($bangladeshCities);
        
        return [
            'name' => $city,
            'slug' => Str::slug($city),
            'division' => $city,
            'country' => 'Bangladesh',
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
