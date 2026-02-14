<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Get the Laravel app instance
$app = app();

use App\Models\Competition;
use App\Models\User;
use App\Models\Photographer;
use Carbon\Carbon;

echo "==== SETTING UP COMPETITIONS ====\n\n";

// 1. Remove active competitions
echo "1. Removing active competitions...\n";
$deleted = Competition::where('status', 'active')->delete();
echo "   Deleted: $deleted competitions\n\n";

// 2. Get admin user
$admin = User::where('role', 'admin')->first();
$adminId = $admin ? $admin->id : 37;
echo "2. Using admin ID: $adminId\n\n";

// 3. Get photographers for organizers
$photographers = Photographer::limit(10)->get();
echo "3. Found " . $photographers->count() . " photographers\n\n";

// 4. Sponsors mapping (from existing sponsors)
$sponsors = [
    1 => "Tripnow Limited",
    7 => "Somogro Bangladesh",
    8 => "Zatra360",
    9 => "School Alternative",
    10 => "Bidesh Gomon"
];

// 4. Bangladesh locations
$locations = [
    "Dhaka", "Chittagong", "Khulna", "Rajshahi", "Sylhet", "Barisal", "Rangpur", "Mymensingh"
];

// 5. Create 10 competitions (one for each month starting March)
echo "4. Creating 10 Bangladesh-based competitions...\n\n";

$months = [
    3 => "March",
    4 => "April",
    5 => "May",
    6 => "June",
    7 => "July",
    8 => "August",
    9 => "September",
    10 => "October",
    11 => "November",
    12 => "December"
];

$competitionData = [
    [
        "title" => "Dhaka Street Moments 2025",
        "theme" => "Street Photography",
        "description" => "Capture the vibrant streets of Dhaka. Showcase urban life, culture, and people in their daily moments.",
        "location" => "Dhaka",
        "month" => 3,
        "sponsor_id" => 7,
        "prize_pool" => 5500
    ],
    [
        "title" => "Sundarbans Wildlife Challenge",
        "theme" => "Nature & Wildlife",
        "description" => "Document the magnificent wildlife and natural beauty of the Sundarbans. Focus on flora, fauna, and environmental conservation.",
        "location" => "Khulna",
        "month" => 4,
        "sponsor_id" => 1,
        "prize_pool" => 5800
    ],
    [
        "title" => "Chittagong Coast Chronicles",
        "theme" => "Travel & Culture",
        "description" => "Explore and photograph the beaches, ports, and cultural heritage of Chittagong.",
        "location" => "Chittagong",
        "month" => 5,
        "sponsor_id" => 8,
        "prize_pool" => 5600
    ],
    [
        "title" => "Sylhet Tea Gardens",
        "theme" => "Nature & Wildlife",
        "description" => "Beautiful shots of Sylhet's lush tea plantations and misty landscapes.",
        "location" => "Sylhet",
        "month" => 6,
        "sponsor_id" => 9,
        "prize_pool" => 5400
    ],
    [
        "title" => "Rajshahi Heritage Lens",
        "theme" => "Architecture & Urban",
        "description" => "Capture historical monuments, mosques, temples, and architectural gems of Rajshahi.",
        "location" => "Rajshahi",
        "month" => 7,
        "sponsor_id" => 10,
        "prize_pool" => 5700
    ],
    [
        "title" => "Barisal River Life",
        "theme" => "Documentary Photography",
        "description" => "Document the lives of people along the rivers of Barisal. Show daily activities, traditions, and riverside culture.",
        "location" => "Barisal",
        "month" => 8,
        "sponsor_id" => 7,
        "prize_pool" => 5300
    ],
    [
        "title" => "Rangpur Winter Frames",
        "theme" => "Portrait Photography",
        "description" => "Focus on people, portraits, and human emotions during winter in Rangpur.",
        "location" => "Rangpur",
        "month" => 9,
        "sponsor_id" => 1,
        "prize_pool" => 5600
    ],
    [
        "title" => "Mymensingh Agricultural Life",
        "theme" => "Fashion & Style",
        "description" => "Showcase traditional and modern lifestyles of agricultural communities in Mymensingh.",
        "location" => "Mymensingh",
        "month" => 10,
        "sponsor_id" => 8,
        "prize_pool" => 5500
    ],
    [
        "title" => "Dhaka Festival Colors",
        "theme" => "Food & Culinary",
        "description" => "Capture food, festivals, colors, and celebrations across Dhaka during cultural events.",
        "location" => "Dhaka",
        "month" => 11,
        "sponsor_id" => 9,
        "prize_pool" => 5800
    ],
    [
        "title" => "Bangladesh Year End Journey",
        "theme" => "Sports & Action",
        "description" => "Action-packed photography of sports events and activities throughout Bangladesh.",
        "location" => "Chittagong",
        "month" => 12,
        "sponsor_id" => 10,
        "prize_pool" => 5900
    ]
];

$created = 0;
$organizerIndex = 0;
$sponsorIds = [1, 7, 8, 9, 10];
$sponsorIndex = 0;

foreach ($competitionData as $data) {
    $month = $data['month'];
    $year = 2025;
    
    // Get photographer organizer (rotate through available photographers)
    $organizerId = $photographers[$organizerIndex % count($photographers)]->id;
    $organizerIndex++;
    
    // Get sponsor ID (rotate through sponsors)
    $sponsorId = $sponsorIds[$sponsorIndex % count($sponsorIds)];
    $sponsorIndex++;
    
    $submissionDeadline = Carbon::createFromDate($year, $month, 25);
    $votingStart = Carbon::createFromDate($year, $month, 26);
    $votingEnd = Carbon::createFromDate($year, $month, 28);
    $judgingStart = Carbon::createFromDate($year, $month + 1, 1);
    $judgingEnd = Carbon::createFromDate($year, $month + 1, 5);
    $resultsAnnouncement = Carbon::createFromDate($year, $month + 1, 10);

    $competition = Competition::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'admin_id' => $adminId,
        'organizer_id' => $organizerId,
        'title' => $data['title'],
        'slug' => \Illuminate\Support\Str::slug($data['title']),
        'description' => $data['description'],
        'theme' => $data['theme'],
        'hero_image' => '/images/competitions/default-hero.jpg',
        'banner_image' => '/images/competitions/default-banner.jpg',
        'submission_deadline' => $submissionDeadline,
        'voting_start_at' => $votingStart,
        'voting_end_at' => $votingEnd,
        'judging_start_at' => $judgingStart,
        'judging_end_at' => $judgingEnd,
        'results_announcement_date' => $resultsAnnouncement,
        'status' => 'active',
        'allow_public_voting' => true,
        'allow_judge_scoring' => true,
        'allow_watermark' => false,
        'require_watermark' => false,
        'participation_fee' => 0,
        'is_paid_competition' => false,
        'max_submissions_per_user' => 5,
        'min_submissions_to_proceed' => 1,
        'total_prize_pool' => $data['prize_pool'],
        'number_of_winners' => 5,
        'is_public' => true,
        'is_featured' => true,
        'featured_until' => Carbon::now()->addMonths(3),
        'published_at' => Carbon::now(),
    ]);

    echo "   ✓ {$data['title']}\n";
    echo "      Prize: ৳{$data['prize_pool']} | Sponsor: {$sponsors[$sponsorId]} | Theme: {$data['theme']}\n\n";
    $created++;
}

echo "✓ SETUP COMPLETE!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  Deleted: 5 active competitions\n";
echo "  Created: $created new Bangladesh-based competitions\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  Prize Pools: 5,300 - 5,900 BDT each\n";
echo "  Timeline: March - December 2025\n";
echo "  Sponsors: Tripnow, Somogro, Zatra360, School Alt, Bidesh Gomon\n";
echo "  Locations: Dhaka, Chittagong, Khulna, Rajshahi, Sylhet, Barisal, Rangpur, Mymensingh\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

?>
