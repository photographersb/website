<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Event;
use App\Models\Location;
use App\Models\Photographer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MonthlyBangladeshEventsSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = 1;
        $organizer = Photographer::first();

        if (!$organizer) {
            $this->command->error('No photographers found. Cannot create events.');
            return;
        }

        $events = [
            [
                'year' => 2026,
                'month' => 7,
                'type' => 'workshop',
                'city' => 'Sylhet',
                'title' => 'Monsoon Landscape Photography Workshop',
            ],
            [
                'year' => 2026,
                'month' => 8,
                'type' => 'photowalk',
                'city' => 'Barishal',
                'title' => 'River Life & Boat Stories Photowalk',
            ],
            [
                'year' => 2026,
                'month' => 9,
                'type' => 'seminar',
                'city' => 'Rajshahi',
                'title' => 'Storytelling Through Documentary Photography',
            ],
            [
                'year' => 2026,
                'month' => 10,
                'type' => 'expo',
                'city' => 'Chattogram',
                'title' => 'Coastal & Maritime Photography Expo',
            ],
            [
                'year' => 2026,
                'month' => 11,
                'type' => 'meetup',
                'city' => 'Rangpur',
                'title' => 'Rural Visual Storytellers Meetup',
            ],
            [
                'year' => 2026,
                'month' => 12,
                'type' => 'workshop',
                'city' => 'Khulna',
                'title' => 'Mangrove & Wildlife Photography Workshop',
            ],
            [
                'year' => 2027,
                'month' => 1,
                'type' => 'photowalk',
                'city' => 'Mymensingh',
                'title' => 'Winter Fog & Morning Light Photowalk',
            ],
            [
                'year' => 2027,
                'month' => 2,
                'type' => 'seminar',
                'city' => 'Cumilla',
                'title' => 'Ethics & Responsibility in Photojournalism',
            ],
            [
                'year' => 2027,
                'month' => 3,
                'type' => 'workshop',
                'city' => 'Bandarban',
                'title' => 'Hill Tracts Travel Photography Workshop',
            ],
            [
                'year' => 2027,
                'month' => 4,
                'type' => 'photowalk',
                'city' => 'Bogura',
                'title' => 'Pohela Boishakh Street Photography Walk',
            ],
            [
                'year' => 2027,
                'month' => 5,
                'type' => 'webinar',
                'city' => null,
                'title' => 'Building a Photography Career in Bangladesh',
                'location_label' => 'Online (Nationwide)',
            ],
            [
                'year' => 2027,
                'month' => 6,
                'type' => 'meetup',
                'city' => 'Patuakhali',
                'title' => 'Climate, Coast & Community: Photographer Meetup',
            ],
        ];

        $this->command->info('Creating monthly Bangladesh events...');

        foreach ($events as $eventData) {
            $eventDate = $this->secondFriday($eventData['year'], $eventData['month']);
            $eventEnd = $eventDate->copy()->addHours(2);

            $locationLabel = $eventData['location_label'] ?? $eventData['city'] ?? 'Online';
            $cityId = $this->resolveCityId($eventData['city']);

            $slug = Str::slug($eventData['title']);
            $slugBase = $slug;
            $suffix = 1;
            while (Event::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $eventData['year'] . '-' . $eventData['month'] . '-' . $suffix;
                $suffix++;
            }

            $monthStart = $eventDate->copy()->startOfMonth();
            $monthEnd = $eventDate->copy()->endOfMonth();
            $existing = Event::where('title', $eventData['title'])
                ->whereBetween('event_date', [$monthStart, $monthEnd])
                ->orderBy('created_at')
                ->get();
            if ($existing->count() > 1) {
                $existing->slice(1)->each->delete();
                $this->command->warn('Removed duplicate events for: ' . $eventData['title'] . ' (' . $eventDate->format('F Y') . ')');
            }
            if ($existing->count() > 0 && $cityId && !$existing->first()->city_id) {
                $existing->first()->update([
                    'city_id' => $cityId,
                    'location' => $locationLabel,
                    'venue_name' => $locationLabel,
                    'venue_address' => $locationLabel,
                ]);
                $this->command->warn('Updated city for existing event: ' . $eventData['title']);
            }
            if ($existing->count() > 0) {
                $this->command->warn('Skipped existing event: ' . $eventData['title'] . ' (' . $eventDate->format('F Y') . ')');
                continue;
            }

            Event::create([
                'organizer_id' => $organizer->id,
                'created_by' => $adminId,
                'title' => $eventData['title'],
                'slug' => $slug,
                'description' => 'Draft event created from the monthly plan. Details will be updated later.',
                'event_type' => $this->normalizeEventType($eventData['type']),
                'type' => $eventData['type'],
                'event_mode' => 'free',
                'event_date' => $eventDate,
                'event_end_date' => $eventEnd,
                'start_time' => $eventDate->format('H:i'),
                'end_time' => $eventEnd->format('H:i'),
                'all_day_event' => false,
                'duration_hours' => 2,
                'city_id' => $cityId,
                'location' => $locationLabel,
                'venue_name' => $locationLabel,
                'venue_address' => $locationLabel,
                'require_registration' => true,
                'is_ticketed' => false,
                'ticket_price' => 0,
                'price' => 0,
                'currency' => 'BDT',
                'status' => 'draft',
                'certificates_enabled' => false,
                'is_featured' => false,
            ]);

            $this->command->info('Created: ' . $eventData['title'] . ' (' . $eventDate->format('F Y') . ')');
        }
    }

    private function secondFriday(int $year, int $month): Carbon
    {
        $date = Carbon::create($year, $month, 1, 10, 0, 0);

        while ($date->dayOfWeek !== Carbon::FRIDAY) {
            $date->addDay();
        }

        return $date->addWeek();
    }

    private function resolveCityId(?string $cityName): ?int
    {
        if (!$cityName) {
            return null;
        }

        $aliases = [
            'barishal' => 'barisal',
            'cumilla' => 'comilla',
            'bogura' => 'bogra',
        ];

        $cityNameLower = strtolower($cityName);
        $lookupName = $aliases[$cityNameLower] ?? $cityNameLower;

        $location = Location::whereRaw('LOWER(name) = ?', [$lookupName])->first();
        if ($location) {
            return $location->id;
        }

        $city = City::whereRaw('LOWER(name) = ?', [$lookupName])->first();
        if ($city) {
            return $city->id;
        }

        $this->command->warn('City not found: ' . $cityName . '. Event will be created without city_id.');
        return null;
    }

    private function normalizeEventType(string $type): string
    {
        return match ($type) {
            'photowalk' => 'exhibition',
            'expo' => 'exhibition',
            'webinar' => 'seminar',
            default => $type,
        };
    }
}
