<?php

namespace Database\Seeders;

use App\Models\Photographer;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PackagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📦 Seeding packages...');

        $packageTemplates = [
            [
                'name' => 'Basic Wedding Package',
                'description' => 'Perfect for small intimate weddings. Includes full day coverage with edited photos.',
                'price' => 15000,
                'duration_hours' => 8,
                'photos_included' => 300,
                'videos_included' => 0,
                'features' => ['8 hours coverage', '300+ edited photos', 'Online gallery', 'Digital delivery'],
            ],
            [
                'name' => 'Premium Wedding Package',
                'description' => 'Complete wedding photography with videography. Perfect for grand celebrations.',
                'price' => 35000,
                'duration_hours' => 12,
                'photos_included' => 600,
                'videos_included' => 1,
                'features' => ['12 hours coverage', '600+ edited photos', 'Cinematic video', 'Same-day highlights', 'Album design', 'Drone shots'],
            ],
            [
                'name' => 'Holud Special',
                'description' => 'Capture the vibrant colors and joy of your Holud ceremony.',
                'price' => 12000,
                'duration_hours' => 6,
                'photos_included' => 200,
                'videos_included' => 0,
                'features' => ['6 hours coverage', '200+ photos', 'Traditional poses', 'Candid moments'],
            ],
            [
                'name' => 'Pre-wedding Shoot',
                'description' => 'Romantic pre-wedding photography session at location of your choice.',
                'price' => 18000,
                'duration_hours' => 4,
                'photos_included' => 150,
                'videos_included' => 0,
                'features' => ['4 hours session', '150+ edited photos', '2 outfit changes', 'Location assistance'],
            ],
            [
                'name' => 'Corporate Event Coverage',
                'description' => 'Professional photography for corporate events, conferences, and seminars.',
                'price' => 20000,
                'duration_hours' => 6,
                'photos_included' => 400,
                'videos_included' => 0,
                'features' => ['6 hours coverage', '400+ photos', 'Group photos', 'Speaker coverage', '48-hour delivery'],
            ],
            [
                'name' => 'Product Photography - Basic',
                'description' => 'Professional product photos for e-commerce and catalogs.',
                'price' => 5000,
                'duration_hours' => 2,
                'photos_included' => 50,
                'videos_included' => 0,
                'features' => ['Up to 50 products', 'White background', 'High-resolution', 'Retouching included'],
            ],
        ];

        $photographers = Photographer::all();
        $totalPackages = 0;

        foreach ($photographers as $photographer) {
            // Each photographer gets 2-4 packages
            $numPackages = rand(2, 4);
            $selectedTemplates = array_rand($packageTemplates, $numPackages);
            
            if (!is_array($selectedTemplates)) {
                $selectedTemplates = [$selectedTemplates];
            }

            foreach ($selectedTemplates as $templateIndex) {
                $template = $packageTemplates[$templateIndex];
                
                // Add some price variation
                $priceVariation = rand(-20, 20) / 100; // -20% to +20%
                $adjustedPrice = $template['price'] * (1 + $priceVariation);

                Package::create([
                    'photographer_id' => $photographer->id,
                    'name' => $template['name'],
                    'description' => $template['description'],
                    'category' => $template['name'], // Use name as category
                    'base_price' => round($adjustedPrice, -2), // Round to nearest 100
                    'duration_unit' => 'hours',
                    'duration_value' => $template['duration_hours'],
                    'includes' => json_encode($template['features']),
                    'is_active' => true,
                    'booking_count' => rand(0, 20),
                    'display_order' => $totalPackages,
                ]);

                $totalPackages++;
            }
        }

        $this->command->info("✅ {$totalPackages} packages created for {$photographers->count()} photographers");
    }
}
