<?php

/**
 * Create Test Data for Dashboard Testing
 * 
 * This script creates sample data for testing the photographer dashboard:
 * - Event RSVP (register photographer for an event)
 * - Competition submission
 * - Award entry
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\Competition;
use App\Models\CompetitionSubmission;
use App\Models\Award;
use Illuminate\Support\Facades\DB;

echo "=== CREATING TEST DATA FOR DASHBOARD ===\n\n";

// Get test photographer - find first photographer with user
$photographer = \App\Models\Photographer::with('user')->where('id', 4)->first();

if (!$photographer || !$photographer->user) {
    echo "❌ Photographer not found!\n";
    exit(1);
}

$user = $photographer->user;
$photographerId = $photographer->id;
echo "✓ Found photographer: {$user->name} (ID: {$photographerId})\n\n";

// 1. Create/Find Event and Register RSVP
echo "1. EVENT RSVP\n";
try {
    // Find or create an event
    $event = Event::where('status', 'published')->first();
    
    if (!$event) {
        echo "   Creating a sample event...\n";
        $event = Event::create([
            'title' => 'Photography Workshop 2026',
            'slug' => 'photography-workshop-2026',
            'description' => 'Join us for an exciting photography workshop!',
            'event_type' => 'workshop',
            'start_datetime' => now()->addDays(30),
            'end_datetime' => now()->addDays(30)->addHours(3),
            'venue' => 'ABC Convention Center',
            'city_id' => 1,
            'organizer_id' => 1, // Admin or another photographer
            'max_attendees' => 50,
            'ticket_price' => 500,
            'status' => 'published',
            'is_featured' => false,
        ]);
        echo "   ✓ Created event: {$event->title}\n";
    } else {
        echo "   ✓ Found existing event: {$event->title}\n";
    }
    
    // Check if already registered
    $existingRsvp = EventRsvp::where('event_id', $event->id)
        ->where('user_id', $user->id)
        ->first();
    
    if ($existingRsvp) {
        echo "   ℹ Already registered for this event (RSVP ID: {$existingRsvp->id})\n";
    } else {
        // Create RSVP
        $rsvp = EventRsvp::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'rsvp_status' => 'going',
            'responded_at' => now(),
        ]);
        echo "   ✓ Created RSVP (ID: {$rsvp->id})\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Create Competition Submission
echo "2. COMPETITION SUBMISSION\n";
try {
    // Find or create a competition
    $competition = Competition::where('status', 'active')->first();
    
    if (!$competition) {
        echo "   Creating a sample competition...\n";
        $competition = Competition::create([
            'title' => 'Best Landscape 2026',
            'slug' => 'best-landscape-2026',
            'description' => 'Submit your best landscape photography!',
            'theme' => 'Landscape',
            'submission_start_at' => now()->subDays(7),
            'submission_deadline' => now()->addDays(30),
            'voting_start_at' => now()->addDays(31),
            'voting_end_at' => now()->addDays(45),
            'winner_announced_at' => now()->addDays(50),
            'status' => 'active',
            'max_submissions_per_user' => 3,
            'prize_amount' => 10000,
        ]);
        echo "   ✓ Created competition: {$competition->title}\n";
    } else {
        echo "   ✓ Found existing competition: {$competition->title}\n";
    }
    
    // Check if already submitted
    $existingSubmission = CompetitionSubmission::where('competition_id', $competition->id)
        ->where('photographer_id', $photographerId)
        ->first();
    
    if ($existingSubmission) {
        echo "   ℹ Already submitted to this competition (Submission ID: {$existingSubmission->id})\n";
    } else {
        // Create submission
        $submission = CompetitionSubmission::create([
            'competition_id' => $competition->id,
            'photographer_id' => $photographerId,
            'title' => 'Sunset Over Mountains',
            'description' => 'A breathtaking view of sunset over mountain peaks.',
            'image_path' => 'competitions/submissions/sunset-mountains.jpg',
            'image_url' => 'https://images.pexels.com/photos/1366630/pexels-photo-1366630.jpeg',
            'thumbnail_url' => 'https://images.pexels.com/photos/1366630/pexels-photo-1366630.jpeg?auto=compress&cs=tinysrgb&w=400',
            'camera_settings' => json_encode([
                'camera' => 'Canon EOS R5',
                'lens' => '24-70mm f/2.8',
                'iso' => 200,
                'aperture' => 'f/8',
                'shutter_speed' => '1/125',
            ]),
            'status' => 'approved',
        ]);
        echo "   ✓ Created submission (ID: {$submission->id})\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Create Award Entry
echo "3. AWARD ENTRY\n";
try {
    // Check if photographer already has awards
    $existingAward = Award::where('photographer_id', $photographerId)->first();
    
    if ($existingAward) {
        echo "   ℹ Photographer already has awards (Award ID: {$existingAward->id})\n";
    } else {
        // Create award
        $award = Award::create([
            'photographer_id' => $photographerId,
            'title' => 'Best Portrait Photographer 2025',
            'type' => 'award',
            'issuing_organization' => 'Bangladesh Photography Association',
            'year' => 2025,
            'description' => 'Awarded for excellence in portrait photography.',
            'display_order' => 1,
        ]);
        echo "   ✓ Created award (ID: {$award->id})\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Summary
echo "=== SUMMARY ===\n";
echo "✓ Event RSVP: Check Events tab > Registered Events section\n";
echo "✓ Competition Submission: Check Competitions tab\n";
echo "✓ Award Entry: Check Awards tab\n";
echo "\nVisit dashboard at: http://127.0.0.1:8000/dashboard\n";
echo "\n=== TEST DATA CREATION COMPLETE ===\n";
