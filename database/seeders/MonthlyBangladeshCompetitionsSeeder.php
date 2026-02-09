<?php

namespace Database\Seeders;

use App\Models\Competition;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MonthlyBangladeshCompetitionsSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = 1;
        $now = Carbon::now();
        $tooCloseDays = 14;
        $cutoff = $now->copy()->addDays($tooCloseDays)->startOfDay();

        $competitions = [
            [
                'month' => 2,
                'title' => 'Color of Love',
                'theme' => 'Identity & Love',
                'description' => 'Celebrate February in Bangladesh through colors of love, language, and national identity. Capture stories that honor heritage, unity, and the warmth of the season.',
            ],
            [
                'month' => 3,
                'title' => 'Colors of Bangladesh',
                'theme' => 'Spring & Vibrancy',
                'description' => 'Welcome spring with vivid scenes of Pahela Falgun, blooming fields, and the energy of everyday life. Show the color and rhythm of Bangladesh in March.',
            ],
            [
                'month' => 4,
                'title' => 'Pohela Boishakh: New Year Stories',
                'theme' => 'New Year Traditions',
                'description' => 'Document the Bangla New Year with street processions, traditional attire, folk art, and family gatherings. Tell fresh stories of renewal and tradition.',
            ],
            [
                'month' => 5,
                'title' => 'Rivers and Lives of Bengal',
                'theme' => 'River Life',
                'description' => 'Rivers shape Bangladesh. Capture ferry crossings, river markets, fishing life, and the resilience of communities living by the water.',
            ],
            [
                'month' => 6,
                'title' => 'Rural Bangladesh',
                'theme' => 'Village Stories',
                'description' => 'Before monsoon peaks, rural life is rich with farming, craft, and daily rituals. Show the quiet strength and beauty of village Bangladesh.',
            ],
            [
                'month' => 7,
                'title' => 'Stories of the Monsoon',
                'theme' => 'Monsoon & Resilience',
                'description' => 'July is the season of rain. Capture the drama of storms, flooded roads, umbrellas, boats, and the resilience of people in monsoon life.',
            ],
            [
                'month' => 8,
                'title' => 'Roads and Horizons',
                'theme' => 'Journeys',
                'description' => 'August carries reflection and movement. Show journeys, roads, transport hubs, and quiet horizons that speak of travel, memory, and hope.',
            ],
            [
                'month' => 9,
                'title' => 'Faces of Bangladesh',
                'theme' => 'Portraits',
                'description' => 'Portraits that reveal character, emotion, and story. Celebrate the people of Bangladesh across age, region, and profession.',
            ],
            [
                'month' => 10,
                'title' => 'Festivals of Bengal',
                'theme' => 'Festivals & Culture',
                'description' => 'From Durga Puja to local fairs, October is rich with celebration. Capture rituals, colors, and the shared joy of festival life.',
            ],
            [
                'month' => 11,
                'title' => 'The Working People of Bangladesh',
                'theme' => 'Work & Craft',
                'description' => 'Honor the dignity of labor. Show farmers, workers, artisans, and everyday professions that keep Bangladesh moving.',
            ],
            [
                'month' => 12,
                'title' => 'Joy of Bengal: Images of a Nation',
                'theme' => 'National Pride',
                'description' => 'December brings Victory Day and celebration. Capture pride, unity, and the joyful spirit of the nation.',
            ],
            [
                'month' => 1,
                'title' => 'Light and Silence',
                'theme' => 'Winter Calm',
                'description' => 'January is quiet and misty. Photograph winter light, foggy landscapes, and the calm that settles over Bangladesh.',
            ],
        ];

        $this->command->info('Creating monthly Bangladesh competitions...');

        foreach ($competitions as $competitionData) {
            $month = $competitionData['month'];
            $year = $month >= $now->month ? $now->year : $now->year + 1;
            $startDate = Carbon::create($year, $month, 1, 0, 0, 0, $now->timezone);

            if ($startDate->lessThanOrEqualTo($cutoff)) {
                $startDate->addYear();
            }

            $submissionEnd = $startDate->copy()->addDays(29)->endOfDay();
            $votingStart = $submissionEnd->copy()->addSecond();
            $votingEnd = $votingStart->copy()->addDays(6)->endOfDay();
            $judgingStart = $votingEnd->copy()->addSecond();
            $judgingEnd = $judgingStart->copy()->addDays(6)->endOfDay();
            $resultsDate = $judgingEnd->copy()->addDay()->startOfDay();

            $title = $competitionData['title'];
            $slug = Str::slug($title);

            if (Competition::where('slug', $slug)->exists()) {
                $this->command->warn("Skipped existing competition: {$slug}");
                continue;
            }

            Competition::create([
                'admin_id' => $adminId,
                'title' => $title,
                'slug' => $slug,
                'description' => $competitionData['description'],
                'theme' => $competitionData['theme'],
                'start_date' => $startDate->toDateString(),
                'submission_deadline' => $submissionEnd,
                'end_date' => $submissionEnd->toDateString(),
                'voting_start_at' => $votingStart,
                'voting_end_at' => $votingEnd,
                'judging_start_at' => $judgingStart,
                'judging_end_at' => $judgingEnd,
                'results_announcement_date' => $resultsDate->toDateString(),
                'announcement_date' => $resultsDate->toDateString(),
                'status' => 'active',
                'mode' => 'open',
                'entry_type' => 'single',
                'allow_public_voting' => true,
                'voting_enabled' => true,
                'allow_judge_scoring' => true,
                'allow_watermark' => false,
                'require_watermark' => false,
                'participation_fee' => 3000,
                'is_paid_competition' => true,
                'max_submissions_per_user' => 3,
                'min_submissions_to_proceed' => 10,
                'total_prize_pool' => 0,
                'number_of_winners' => 3,
                'is_public' => true,
                'published_at' => $now,
                'rules' => 'Participation fee: BDT 3000. All participants receive a certificate of participation. Photos must be original and taken by the participant. One submission per entry is recommended.',
                'terms_and_conditions' => 'By submitting, you confirm you own the rights to the image and grant Photographer SB a non-exclusive right to display entries for competition promotion. Entries must follow community guidelines and local laws.',
                'prizes' => [
                    [
                        'position' => 'Participation',
                        'amount' => 0,
                        'award_type' => 'special',
                        'prize_type' => 'certificate',
                        'sort_order' => 99,
                    ],
                ],
            ]);

            $this->command->info("Created: {$title} ({$startDate->format('F Y')})");
        }
    }
}
