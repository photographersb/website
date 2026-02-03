<?php

namespace Database\Seeders;

use App\Models\Judge;
use App\Models\User;
use Illuminate\Database\Seeder;

class JudgeSeeder extends Seeder
{
    public function run(): void
    {
        // Create judge users first
        $judgeUsers = [
            [
                'name' => 'Rafiqul Islam',
                'email' => 'rafiqul.judge@photographar.com',
                'password' => bcrypt('password'),
                'role' => 'judge',
            ],
            [
                'name' => 'Nadia Rahman',
                'email' => 'nadia.judge@photographar.com',
                'password' => bcrypt('password'),
                'role' => 'judge',
            ],
        ];

        $createdUsers = [];
        foreach ($judgeUsers as $userData) {
            $createdUsers[] = User::create($userData);
        }

        $judges = [
            [
                'user_id' => $createdUsers[0]->id,
                'name' => 'Rafiqul Islam',
                'slug' => 'rafiqul-islam',
                'title' => 'Cinematographer & Visual Artist',
                'bio' => 'Award-winning cinematographer with expertise in visual storytelling. Specializes in composition, lighting, and technical excellence. Has judged over 50 photography competitions.',
                'email' => 'rafiqul.judge@photographar.com',
                'organization' => 'Bangladesh Film Institute',
                'facebook_url' => 'https://facebook.com/rafiqulislam',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'user_id' => $createdUsers[1]->id,
                'name' => 'Nadia Rahman',
                'slug' => 'nadia-rahman',
                'title' => 'Fashion & Portrait Photographer',
                'bio' => 'Leading fashion photographer with 20 years experience. Expert in portrait photography, lighting techniques, and post-production. Mentor at multiple photography institutes.',
                'email' => 'nadia.judge@photographar.com',
                'organization' => 'Fashion Photography Guild',
                'instagram_url' => 'https://instagram.com/nadiarahman',
                'website_url' => 'https://nadiarahman.com',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'user_id' => null,
                'name' => 'Professor Ahmed Hassan',
                'slug' => 'ahmed-hassan',
                'title' => 'Photography Educator',
                'bio' => 'Professor of Visual Arts at Dhaka University. Published author on photography theory and critique. Expert in evaluating artistic merit and technical proficiency.',
                'email' => 'ahmed.hassan@example.com',
                'organization' => 'Dhaka University',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'user_id' => null,
                'name' => 'Sarah Begum',
                'slug' => 'sarah-begum',
                'title' => 'Commercial Photographer',
                'bio' => 'Commercial photographer specializing in advertising and product photography. Judge for international photography awards. Known for attention to detail and creativity.',
                'email' => 'sarah@example.com',
                'organization' => 'Pixel Perfect Studio',
                'facebook_url' => 'https://facebook.com/sarahbegum',
                'instagram_url' => 'https://instagram.com/sarahbegum',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'user_id' => null,
                'name' => 'Kamal Hossain',
                'slug' => 'kamal-hossain-judge',
                'title' => 'Landscape Photographer',
                'bio' => 'Landscape and nature photographer. Expert in outdoor photography, natural lighting, and environmental storytelling. Regular contributor to photography magazines.',
                'email' => 'kamal.judge@example.com',
                'organization' => 'Nature Photography Society',
                'website_url' => 'https://kamalhossain.photography',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($judges as $judge) {
            Judge::create($judge);
        }
    }
}
