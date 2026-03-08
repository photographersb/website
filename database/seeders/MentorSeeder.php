<?php

namespace Database\Seeders;

use App\Models\Mentor;
use Illuminate\Database\Seeder;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        $mentors = [
            [
                'name' => 'Munem Wasif',
                'slug' => 'munem-wasif',
                'title' => 'Wildlife & Documentary Photographer',
                'organization' => 'National Geographic Bangladesh',
                'bio' => 'Award-winning wildlife photographer with 15+ years of experience capturing Bangladesh\'s biodiversity. Featured in National Geographic, BBC Wildlife, and Smithsonian Magazine.',
                'email' => 'munem@example.com',
                'phone' => '+880-1700-000001',
                'facebook_url' => 'https://facebook.com/munemwasif',
                'instagram_url' => 'https://instagram.com/munemwasif',
                'website_url' => 'https://munemwasif.com',
                'country' => 'Bangladesh',
                'city' => 'Dhaka',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Shahidul Alam',
                'slug' => 'shahidul-alam',
                'title' => 'Photojournalist & Social Activist',
                'organization' => 'Drik Picture Library',
                'bio' => 'Internationally acclaimed photographer and social activist. Founder of Drik Picture Library and Pathshala South Asian Media Institute. Recipient of numerous international awards.',
                'email' => 'shahidul@example.com',
                'facebook_url' => 'https://facebook.com/shahidulalam',
                'instagram_url' => 'https://instagram.com/shahidulalam',
                'website_url' => 'https://shahidulalam.com',
                'country' => 'Bangladesh',
                'city' => 'Dhaka',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'GMB Akash',
                'slug' => 'gmb-akash',
                'title' => 'Documentary Photographer',
                'organization' => 'Counter Foto',
                'bio' => 'Renowned documentary photographer focusing on social issues and human rights. Published photographer with works exhibited globally. Mentor to countless aspiring photographers.',
                'email' => 'gmb@example.com',
                'facebook_url' => 'https://facebook.com/gmbakash',
                'instagram_url' => 'https://instagram.com/gmbakash',
                'website_url' => 'https://gmbakash.com',
                'country' => 'Bangladesh',
                'city' => 'Dhaka',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Abir Abdullah',
                'slug' => 'abir-abdullah',
                'title' => 'Photojournalist',
                'organization' => 'EPA Photos',
                'bio' => 'Award-winning photojournalist covering South Asia for EPA. Specialized in conflict, politics, and human interest stories. Work published in major international media outlets.',
                'email' => 'abir@example.com',
                'facebook_url' => 'https://facebook.com/abirabdullah',
                'instagram_url' => 'https://instagram.com/abirabdullah',
                'country' => 'Bangladesh',
                'city' => 'Dhaka',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Taslima Akhter',
                'slug' => 'taslima-akhter',
                'title' => 'Documentary Photographer',
                'organization' => 'AMI (Ami Collective)',
                'bio' => 'Documentary photographer and activist. Known for powerful imagery documenting labor rights and women\'s issues. Her iconic photo "Final Embrace" gained worldwide recognition.',
                'email' => 'taslima@example.com',
                'facebook_url' => 'https://facebook.com/taslimaakhter',
                'instagram_url' => 'https://instagram.com/taslimaakhter',
                'website_url' => 'https://taslimaakhter.com',
                'country' => 'Bangladesh',
                'city' => 'Dhaka',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($mentors as $mentor) {
            $mentor['created_by'] = 1; // Admin user
            Mentor::updateOrCreate(
                ['slug' => $mentor['slug']],
                $mentor
            );
        }
    }
}
