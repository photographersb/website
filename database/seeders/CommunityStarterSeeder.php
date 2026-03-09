<?php

namespace Database\Seeders;

use App\Models\CommunityBadge;
use App\Models\CommunityGroup;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommunityStarterSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()
            ->whereIn('role', ['super_admin', 'admin', 'moderator'])
            ->orderBy('id')
            ->first() ?? User::query()->orderBy('id')->first();

        if (!$creator) {
            return;
        }

        $cityMap = [
            'Dhaka Photographers' => 'Dhaka',
            'Sylhet Photographers' => 'Sylhet',
            'Rajshahi Photographers' => 'Rajshahi',
            'Khulna Photographers' => 'Khulna',
            'Chattogram Photographers' => 'Chattogram',
        ];

        $interestGroups = [
            ['name' => 'Street Photography Bangladesh', 'description' => 'A community for documenting urban life, street moments, and candid photography across Bangladesh.'],
            ['name' => 'Wedding Photographers BD', 'description' => 'Discuss wedding workflows, client management, lighting setups, and portfolio growth.'],
            ['name' => 'Travel Photography BD', 'description' => 'Share travel stories, locations, and techniques for landscape and journey storytelling.'],
            ['name' => 'Drone Photography BD', 'description' => 'Explore aerial composition, drone safety, post-processing, and legal best practices.'],
            ['name' => 'Food Photography BD', 'description' => 'Learn food styling, tabletop lighting, commercial composition, and editing tips.'],
        ];

        foreach ($interestGroups as $row) {
            CommunityGroup::query()->firstOrCreate(
                ['slug' => Str::slug($row['name'])],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'type' => 'interest',
                    'created_by' => $creator->id,
                    'status' => 'active',
                    'is_featured' => true,
                ]
            );
        }

        foreach ($cityMap as $clubName => $cityName) {
            $city = Location::query()->where('name', $cityName)->first();

            CommunityGroup::query()->firstOrCreate(
                ['slug' => Str::slug($clubName)],
                [
                    'name' => $clubName,
                    'description' => "Connect photographers in {$cityName} for local meetups, learning, and collaboration.",
                    'type' => 'local_club',
                    'city_id' => $city?->id,
                    'created_by' => $creator->id,
                    'status' => 'active',
                    'is_featured' => true,
                ]
            );
        }

        $badges = [
            ['code' => 'top_contributor', 'name' => 'Top Contributor', 'icon' => 'star', 'description' => 'Consistently contributes high-value content.'],
            ['code' => 'community_mentor', 'name' => 'Community Mentor', 'icon' => 'mentor', 'description' => 'Provides mentorship and guidance.'],
            ['code' => 'event_organizer', 'name' => 'Event Organizer', 'icon' => 'calendar', 'description' => 'Organizes valuable community events.'],
            ['code' => 'competition_judge', 'name' => 'Competition Judge', 'icon' => 'gavel', 'description' => 'Supports competition quality as a judge.'],
            ['code' => 'helpful_photographer', 'name' => 'Helpful Photographer', 'icon' => 'hands-helping', 'description' => 'Frequently helps peers in discussions.'],
        ];

        foreach ($badges as $badge) {
            CommunityBadge::query()->firstOrCreate(
                ['code' => $badge['code']],
                [
                    'name' => $badge['name'],
                    'icon' => $badge['icon'],
                    'description' => $badge['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
