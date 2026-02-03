<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Competition;
use Illuminate\Support\Facades\DB;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create admin user
        $admin = User::where('email', 'admin@photographar.com')->first();
        
        if (!$admin) {
            $admin = User::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Admin User',
                'email' => 'admin@photographar.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now()
            ]);
        }

        $competitions = [
            [
                'title' => 'Bangladesh Nature Photography Contest 2026',
                'slug' => 'bangladesh-nature-2026',
                'description' => 'Capture the breathtaking beauty of Bangladesh - from the Sundarbans mangroves to the tea gardens of Sylhet, from the Cox\'s Bazar coastline to the rural landscapes. Show us the natural wonders of our country through your lens.',
                'theme' => 'Nature & Wildlife',
                'status' => 'active',
                'submission_deadline' => now()->addDays(30),
                'voting_start_at' => now()->addDays(31),
                'voting_end_at' => now()->addDays(38),
                'judging_start_at' => now()->addDays(39),
                'judging_end_at' => now()->addDays(44),
                'results_announcement_date' => now()->addDays(45),
                'is_paid_competition' => false,
                'participation_fee' => 0,
                'max_submissions_per_user' => 3,
                'total_prize_pool' => 50000,
                'number_of_winners' => 3,
                'is_featured' => true,
                'featured_until' => now()->addDays(30),
            ],
            [
                'title' => 'Urban Life & Street Photography Challenge',
                'slug' => 'urban-life-street-2026',
                'description' => 'Document the vibrant pulse of urban Bangladesh. Capture candid moments, street scenes, architectural marvels, and the everyday life of people in our bustling cities.',
                'theme' => 'Street & Urban',
                'status' => 'active',
                'submission_deadline' => now()->addDays(45),
                'voting_start_at' => now()->addDays(46),
                'voting_end_at' => now()->addDays(53),
                'judging_start_at' => now()->addDays(54),
                'judging_end_at' => now()->addDays(59),
                'results_announcement_date' => now()->addDays(60),
                'is_paid_competition' => true,
                'participation_fee' => 500,
                'max_submissions_per_user' => 5,
                'total_prize_pool' => 75000,
                'number_of_winners' => 5,
                'is_featured' => true,
                'featured_until' => now()->addDays(45),
            ],
            [
                'title' => 'Portrait Masters Competition 2026',
                'slug' => 'portrait-masters-2026',
                'description' => 'Showcase your mastery in portrait photography. Whether it\'s studio portraits, environmental portraits, or candid shots, show us the art of capturing human emotion and character.',
                'theme' => 'Portrait',
                'status' => 'draft',
                'submission_deadline' => now()->addDays(60),
                'voting_start_at' => now()->addDays(61),
                'voting_end_at' => now()->addDays(68),
                'judging_start_at' => now()->addDays(69),
                'judging_end_at' => now()->addDays(74),
                'results_announcement_date' => now()->addDays(75),
                'is_paid_competition' => true,
                'participation_fee' => 1000,
                'max_submissions_per_user' => 3,
                'total_prize_pool' => 100000,
                'number_of_winners' => 3,
                'is_featured' => false,
            ],
            [
                'title' => 'Wedding Photography Excellence Award',
                'slug' => 'wedding-excellence-2026',
                'description' => 'Celebrate the art of wedding photography. Submit your best wedding moments - from intimate ceremonies to grand celebrations, from emotional first looks to joyful receptions.',
                'theme' => 'Wedding',
                'status' => 'active',
                'submission_deadline' => now()->addDays(20),
                'voting_start_at' => now()->addDays(21),
                'voting_end_at' => now()->addDays(28),
                'judging_start_at' => now()->addDays(29),
                'judging_end_at' => now()->addDays(34),
                'results_announcement_date' => now()->addDays(35),
                'is_paid_competition' => true,
                'participation_fee' => 1500,
                'max_submissions_per_user' => 10,
                'total_prize_pool' => 150000,
                'number_of_winners' => 5,
                'is_featured' => true,
                'featured_until' => now()->addDays(20),
            ],
            [
                'title' => 'Product Photography Showcase 2026',
                'slug' => 'product-photography-2026',
                'description' => 'Demonstrate your skills in commercial and product photography. From e-commerce shots to creative advertising images, show us how you make products look their absolute best.',
                'theme' => 'Commercial & Product',
                'status' => 'active',
                'submission_deadline' => now()->addDays(35),
                'voting_start_at' => now()->addDays(36),
                'voting_end_at' => now()->addDays(43),
                'judging_start_at' => now()->addDays(44),
                'judging_end_at' => now()->addDays(49),
                'results_announcement_date' => now()->addDays(50),
                'is_paid_competition' => false,
                'participation_fee' => 0,
                'max_submissions_per_user' => 5,
                'total_prize_pool' => 60000,
                'number_of_winners' => 3,
                'is_featured' => false,
            ],
            [
                'title' => 'Architectural Photography Competition',
                'slug' => 'architecture-2026',
                'description' => 'Capture the beauty of buildings and structures. From historical monuments to modern skyscrapers, from interior design to urban planning - celebrate architecture through photography.',
                'theme' => 'Architecture',
                'status' => 'judging',
                'submission_deadline' => now()->subDays(5),
                'voting_start_at' => now()->subDays(4),
                'voting_end_at' => now()->subDays(1),
                'judging_start_at' => now(),
                'judging_end_at' => now()->addDays(5),
                'results_announcement_date' => now()->addDays(10),
                'is_paid_competition' => true,
                'participation_fee' => 800,
                'max_submissions_per_user' => 3,
                'total_prize_pool' => 80000,
                'number_of_winners' => 3,
                'is_featured' => false,
            ],
            [
                'title' => 'Food Photography Mastery 2026',
                'slug' => 'food-photography-2026',
                'description' => 'Make us hungry with your stunning food photography! From restaurant dishes to home-cooked meals, from street food to fine dining - showcase culinary art through your lens.',
                'theme' => 'Food',
                'status' => 'completed',
                'submission_deadline' => now()->subDays(30),
                'voting_start_at' => now()->subDays(29),
                'voting_end_at' => now()->subDays(22),
                'judging_start_at' => now()->subDays(21),
                'judging_end_at' => now()->subDays(16),
                'results_announcement_date' => now()->subDays(15),
                'is_paid_competition' => false,
                'participation_fee' => 0,
                'max_submissions_per_user' => 5,
                'total_prize_pool' => 40000,
                'number_of_winners' => 3,
                'results_published' => true,
                'is_featured' => false,
            ],
            [
                'title' => 'Black & White Photography Award 2026',
                'slug' => 'black-white-2026',
                'description' => 'Embrace the timeless art of monochrome photography. Show us the power of light, shadow, and composition in black and white. Any subject, any style - just black and white excellence.',
                'theme' => 'Black & White',
                'status' => 'active',
                'submission_deadline' => now()->addDays(25),
                'voting_start_at' => now()->addDays(26),
                'voting_end_at' => now()->addDays(33),
                'judging_start_at' => now()->addDays(34),
                'judging_end_at' => now()->addDays(39),
                'results_announcement_date' => now()->addDays(40),
                'is_paid_competition' => false,
                'participation_fee' => 0,
                'max_submissions_per_user' => 4,
                'total_prize_pool' => 45000,
                'number_of_winners' => 3,
                'is_featured' => true,
                'featured_until' => now()->addDays(25),
            ],
        ];

        $this->command->info('Creating demo competitions...');
        $this->command->newLine();

        foreach ($competitions as $competitionData) {
            $competition = Competition::create([
                'admin_id' => $admin->id,
                'title' => $competitionData['title'],
                'slug' => $competitionData['slug'],
                'description' => $competitionData['description'],
                'theme' => $competitionData['theme'],
                'submission_deadline' => $competitionData['submission_deadline'],
                'voting_start_at' => $competitionData['voting_start_at'] ?? null,
                'voting_end_at' => $competitionData['voting_end_at'] ?? null,
                'judging_start_at' => $competitionData['judging_start_at'] ?? null,
                'judging_end_at' => $competitionData['judging_end_at'] ?? null,
                'results_announcement_date' => $competitionData['results_announcement_date'],
                'status' => $competitionData['status'],
                'is_public' => true,
                'is_paid_competition' => $competitionData['is_paid_competition'],
                'participation_fee' => $competitionData['participation_fee'],
                'max_submissions_per_user' => $competitionData['max_submissions_per_user'],
                'total_prize_pool' => $competitionData['total_prize_pool'],
                'number_of_winners' => $competitionData['number_of_winners'],
                'allow_public_voting' => true,
                'allow_judge_scoring' => true,
                'is_featured' => $competitionData['is_featured'] ?? false,
                'featured_until' => $competitionData['featured_until'] ?? null,
                'results_published' => $competitionData['results_published'] ?? false,
                'published_at' => in_array($competitionData['status'], ['active', 'judging', 'completed']) ? now() : null,
            ]);

            // Add prizes
            $prizeAmounts = $this->distributePrizes($competitionData['total_prize_pool'], $competitionData['number_of_winners']);
            foreach ($prizeAmounts as $index => $amount) {
                DB::table('competition_prizes')->insert([
                    'competition_id' => $competition->id,
                    'rank' => $index + 1,
                    'title' => $this->getPrizeTitle($index + 1),
                    'cash_amount' => $amount,
                    'physical_prizes' => $this->getPhysicalPrize($index + 1),
                    'display_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Add categories for some competitions
            if (rand(0, 1)) {
                $categories = $this->getCategories($competitionData['theme']);
                foreach ($categories as $idx => $category) {
                    DB::table('competition_categories')->insert([
                        'competition_id' => $competition->id,
                        'name' => $category['name'],
                        'description' => $category['description'],
                        'prize_amount' => $category['prize_amount'],
                        'max_submissions_per_user' => 1,
                        'is_active' => true,
                        'submission_count' => rand(0, 50),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Add sponsors
            $sponsors = $this->getSponsors($competition->id);
            foreach ($sponsors as $sponsor) {
                DB::table('competition_sponsors')->insert($sponsor);
            }

            // Update total submissions count
            if (in_array($competitionData['status'], ['judging', 'completed'])) {
                $competition->update(['total_submissions' => rand(50, 200)]);
            } elseif ($competitionData['status'] === 'active') {
                $competition->update(['total_submissions' => rand(10, 80)]);
            }

            $this->command->info('✓ ' . $competition->title);
            $this->command->info('  Status: ' . $competition->status);
            $this->command->info('  Prizes: ' . count($prizeAmounts) . ' added');
            $this->command->newLine();
        }

        $this->command->info('All demo competitions created successfully!');
    }

    private function distributePrizes($totalPool, $numberOfWinners)
    {
        if ($numberOfWinners === 1) {
            return [$totalPool];
        }

        $prizes = [];
        $remaining = $totalPool;

        for ($i = 0; $i < $numberOfWinners; $i++) {
            if ($i === $numberOfWinners - 1) {
                $prizes[] = $remaining;
            } else {
                $percentage = 0.4 - ($i * 0.1); // 40%, 30%, 20%, 10% etc
                $amount = round($totalPool * $percentage);
                $prizes[] = $amount;
                $remaining -= $amount;
            }
        }

        return $prizes;
    }

    private function getPrizeTitle($rank)
    {
        $titles = [
            1 => 'First Prize - Grand Champion',
            2 => 'Second Prize - Runner Up',
            3 => 'Third Prize - Second Runner Up',
            4 => 'Fourth Prize',
            5 => 'Fifth Prize',
        ];

        return $titles[$rank] ?? "{$rank}th Prize";
    }

    private function getPhysicalPrize($rank)
    {
        $prizes = [
            1 => 'Professional Camera Kit + Trophy + Certificate of Excellence',
            2 => 'Premium Camera Lens + Trophy + Certificate',
            3 => 'Photography Equipment Bundle + Trophy + Certificate',
            4 => 'Camera Accessories Pack + Certificate',
            5 => 'Photography Gift Voucher + Certificate',
        ];

        return $prizes[$rank] ?? 'Certificate of Achievement';
    }

    private function getCategories($theme)
    {
        $allCategories = [
            'Nature & Wildlife' => [
                ['name' => 'Wildlife', 'description' => 'Animals in their natural habitat', 'prize_amount' => 15000],
                ['name' => 'Landscape', 'description' => 'Natural scenery and vistas', 'prize_amount' => 15000],
                ['name' => 'Macro Nature', 'description' => 'Close-up nature photography', 'prize_amount' => 10000],
            ],
            'Street & Urban' => [
                ['name' => 'Street Life', 'description' => 'Candid street photography', 'prize_amount' => 20000],
                ['name' => 'Urban Architecture', 'description' => 'City buildings and structures', 'prize_amount' => 15000],
            ],
            'Portrait' => [
                ['name' => 'Studio Portrait', 'description' => 'Controlled studio environment', 'prize_amount' => 25000],
                ['name' => 'Environmental Portrait', 'description' => 'Portraits in natural settings', 'prize_amount' => 20000],
            ],
        ];

        return $allCategories[$theme] ?? [];
    }

    private function getSponsors($competitionId)
    {
        $allSponsors = [
            [
                'competition_id' => $competitionId,
                'name' => 'Canon Bangladesh',
                'tier' => 'platinum',
                'logo_url' => 'https://via.placeholder.com/200x100/0052CC/ffffff?text=Canon',
                'website_url' => 'https://canon.com.bd',
                'description' => 'Leading camera and imaging solutions provider',
                'contribution_amount' => 30000,
                'display_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'competition_id' => $competitionId,
                'name' => 'Nikon Bangladesh',
                'tier' => 'gold',
                'logo_url' => 'https://via.placeholder.com/200x100/FFD700/000000?text=Nikon',
                'website_url' => 'https://nikon.com.bd',
                'description' => 'Professional photography equipment manufacturer',
                'contribution_amount' => 20000,
                'display_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'competition_id' => $competitionId,
                'name' => 'Sony Imaging',
                'tier' => 'silver',
                'logo_url' => 'https://via.placeholder.com/200x100/C0C0C0/000000?text=Sony',
                'website_url' => 'https://sony.com',
                'description' => 'Innovation in digital imaging',
                'contribution_amount' => 10000,
                'display_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Return random 1-3 sponsors
        $count = rand(1, 3);
        return array_slice($allSponsors, 0, $count);
    }
}
