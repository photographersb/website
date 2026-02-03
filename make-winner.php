<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get photographer by slug
$photographer = \App\Models\Photographer::where('slug', 'ayman-shuvro-iyNK8s')->first();

if (!$photographer) {
    die("Photographer not found!\n");
}

echo "Found photographer: {$photographer->user->name} (ID: {$photographer->id})\n\n";

// Get competitions
$competitions = \App\Models\Competition::all();
echo "Available Competitions:\n";
foreach ($competitions as $comp) {
    echo "  [{$comp->id}] {$comp->title} - Status: {$comp->status}\n";
}
echo "\n";

// Get photographer's submissions
$submissions = \App\Models\CompetitionSubmission::where('photographer_id', $photographer->id)->get();

if ($submissions->isEmpty()) {
    echo "No submissions found. Creating a demo submission...\n";
    
    // Get first competition
    $competition = $competitions->first();
    if (!$competition) {
        die("No competitions found!\n");
    }
    
    // Create a demo submission
    $submission = \App\Models\CompetitionSubmission::create([
        'competition_id' => $competition->id,
        'photographer_id' => $photographer->id,
        'title' => 'Winning Photograph',
        'description' => 'A stunning capture that won the competition',
        'image_path' => 'submissions/demo-winner.jpg',
        'image_url' => '/storage/submissions/demo-winner.jpg',
        'thumbnail_url' => '/storage/submissions/demo-winner.jpg',
        'status' => 'approved'
    ]);
    
    echo "Created submission ID: {$submission->id}\n";
} else {
    echo "Existing Submissions:\n";
    foreach ($submissions as $sub) {
        echo "  [{$sub->id}] {$sub->title} - Competition: {$sub->competition->title}\n";
    }
    $submission = $submissions->first();
}

// Make it a winner
if (!isset($submission)) {
    die("No submission available!\n");
}

// Update submission to winner status
$submission->update([
    'position' => 1,
    'is_winner' => true,
    'prize_amount' => 10000
]);

echo "\n✅ SUCCESS! Made submission #{$submission->id} a WINNER!\n";
echo "   Competition: {$submission->competition->title}\n";
echo "   Position: 1st Place\n";
echo "   Prize: ৳10,000\n";
echo "\nView profile at: http://127.0.0.1:8000/photographer/{$photographer->slug}\n";
