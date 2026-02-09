<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BangladeshLocationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🗺️  Seeding Bangladesh locations...');

        $divisions = [
            [
                'name' => 'Dhaka',
                'districts' => [
                    'Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Kishoreganj',
                    'Madaripur', 'Manikganj', 'Munshiganj', 'Narayanganj', 'Narsingdi',
                    'Rajbari', 'Shariatpur', 'Tangail'
                ]
            ],
            [
                'name' => 'Chattogram',
                'districts' => [
                    'Chattogram', 'Bandarban', 'Brahmanbaria', 'Chandpur', 'Cumilla',
                    'Cox\'s Bazar', 'Feni', 'Khagrachari', 'Lakshmipur', 'Noakhali', 'Rangamati'
                ]
            ],
            [
                'name' => 'Sylhet',
                'districts' => [
                    'Sylhet', 'Habiganj', 'Moulvibazar', 'Sunamganj'
                ]
            ],
            [
                'name' => 'Khulna',
                'districts' => [
                    'Khulna', 'Bagerhat', 'Chuadanga', 'Jessore', 'Jhenaidah',
                    'Khustia', 'Magura', 'Meherpur', 'Narail', 'Satkhira'
                ]
            ],
            [
                'name' => 'Rajshahi',
                'districts' => [
                    'Rajshahi', 'Bogura', 'Joypurhat', 'Naogaon', 'Natore', 'Nawabganj', 'Pabna', 'Sirajganj'
                ]
            ],
            [
                'name' => 'Barisal',
                'districts' => [
                    'Barisal', 'Barguna', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur'
                ]
            ],
            [
                'name' => 'Rangpur',
                'districts' => [
                    'Rangpur', 'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh', 'Thakurgaon'
                ]
            ],
            [
                'name' => 'Mymensingh',
                'districts' => [
                    'Mymensingh', 'Jamalpur', 'Netrokona', 'Sherpur'
                ]
            ],
        ];

        $sortOrder = 0;
        $now = now();

        foreach ($divisions as $divisionData) {
            $sortOrder++;

            $divisionSlug = Str::slug($divisionData['name']);

            DB::table('locations')->updateOrInsert(
                [
                    'slug' => $divisionSlug,
                    'type' => 'division',
                    'parent_id' => null,
                ],
                [
                    'name' => $divisionData['name'],
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $divisionId = DB::table('locations')
                ->where('slug', $divisionSlug)
                ->where('type', 'division')
                ->value('id');

            $districtSort = 1;
            foreach ($divisionData['districts'] as $districtName) {
                $districtSlug = Str::slug($districtName);
                if (DB::table('locations')->where('slug', $districtSlug)->where('type', 'division')->exists()) {
                    $districtSlug = $districtSlug . '-district';
                }

                DB::table('locations')->updateOrInsert(
                    [
                        'slug' => $districtSlug,
                        'type' => 'district',
                        'parent_id' => $divisionId,
                    ],
                    [
                        'name' => $districtName,
                        'is_active' => true,
                        'sort_order' => $districtSort++,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }

        $this->command->info('✅ Seeded 64 Bangladesh districts successfully');
    }
}
