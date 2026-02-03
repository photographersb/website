<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Photographer;
use App\Models\City;
use App\Models\Category;
use App\Models\PhotoCategory;
use App\Models\Hashtag;
use App\Models\Package;
use App\Models\Album;
use App\Models\Photo;
use App\Models\PhotographerAward;
use App\Models\Inquiry;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Models\CompetitionSubmission;
use App\Models\CompetitionVote;
use App\Models\CompetitionJudge;
use App\Models\Sponsor;
use App\Models\ContactMessage;
use App\Models\Notice;
use App\Models\Judge;
use App\Models\Mentor;

function ensureUser(array $data): User
{
    $user = User::where('email', $data['email'])->first();
    if (!$user) {
        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'username' => $data['username'] ?? Str::slug($data['name']) . '-' . Str::random(6),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password'] ?? 'Password123!'),
            'role' => $data['role'] ?? 'client',
            'email_verified_at' => now(),
        ]);
    } else {
        $user->update([
            'name' => $data['name'],
            'role' => $data['role'] ?? $user->role,
            'email_verified_at' => $user->email_verified_at ?? now(),
            'username' => $user->username ?? ($data['username'] ?? Str::slug($data['name']) . '-' . Str::random(6)),
        ]);
    }

    return $user;
}

function ensurePhotographer(User $user, ?int $cityId = null): Photographer
{
    $photographer = Photographer::where('user_id', $user->id)->first();
    if (!$photographer) {
        $photographer = Photographer::create([
            'user_id' => $user->id,
            'slug' => Str::slug($user->name) . '-' . Str::random(6),
            'bio' => 'Audit photographer profile',
            'location' => 'Dhaka',
            'experience_years' => 5,
            'city_id' => $cityId,
        ]);
    }
    return $photographer;
}

$seed = [
    'generated_at' => now()->toDateTimeString(),
    'errors' => [],
];

try {
    // Core lookup data
    $city = City::firstOrCreate(['slug' => 'dhaka'], [
        'name' => 'Dhaka',
        'division' => 'Dhaka',
    ]);

    $category = Category::firstOrCreate(['slug' => 'wedding'], [
        'name' => 'Wedding',
        'description' => 'Wedding photography',
        'is_active' => true,
    ]);

    $photoCategory = PhotoCategory::firstOrCreate(['slug' => 'portrait'], [
        'name' => 'Portrait',
        'description' => 'Portraits',
        'is_active' => true,
    ]);

    $hashtag = Hashtag::firstOrCreate(['slug' => 'bangladesh'], [
        'name' => 'Bangladesh',
        'is_featured' => true,
        'is_active' => true,
    ]);

    $sponsor = Sponsor::firstOrCreate(['slug' => 'audit-sponsor'], [
        'name' => 'Audit Sponsor',
        'logo' => 'https://example.com/logo.png',
        'website' => 'https://example.com',
        'status' => 'active',
    ]);

    // Users
    $admin = ensureUser([
        'name' => 'Audit Admin',
        'email' => 'admin.audit@example.com',
        'phone' => '01700000001',
        'role' => 'admin',
        'username' => 'audit-admin',
    ]);

    $client = ensureUser([
        'name' => 'Audit Client',
        'email' => 'client.audit@example.com',
        'phone' => '01700000002',
        'role' => 'client',
        'username' => 'audit-client',
    ]);

    $photographerUser = ensureUser([
        'name' => 'Audit Photographer',
        'email' => 'photographer.audit@example.com',
        'phone' => '01700000003',
        'role' => 'photographer',
        'username' => 'audit-photographer',
    ]);

    $judgeUser = ensureUser([
        'name' => 'Audit Judge',
        'email' => 'judge.audit@example.com',
        'phone' => '01700000004',
        'role' => 'judge',
        'username' => 'audit-judge',
    ]);

    $mentorUser = ensureUser([
        'name' => 'Audit Mentor',
        'email' => 'mentor.audit@example.com',
        'phone' => '01700000005',
        'role' => 'photographer',
        'username' => 'audit-mentor',
    ]);

    // Photographer profile
    $photographer = ensurePhotographer($photographerUser, $city->id);

    // Package
    $package = Package::firstOrCreate([
        'photographer_id' => $photographer->id,
        'name' => 'Audit Package',
    ], [
        'description' => 'Audit package description',
        'category' => 'Wedding',
        'base_price' => 5000,
        'duration_unit' => 'hours',
        'duration_value' => 3,
        'includes' => json_encode(['Edited photos', 'USB']),
        'is_active' => true,
    ]);

    // Album & photo
    $album = Album::firstOrCreate([
        'photographer_id' => $photographer->id,
        'slug' => 'audit-album',
    ], [
        'name' => 'Audit Album',
        'description' => 'Audit album description',
        'cover_photo_url' => 'https://example.com/cover.jpg',
        'category_id' => $category->id,
        'is_public' => true,
    ]);

    $photo = Photo::firstOrCreate([
        'album_id' => $album->id,
        'photographer_id' => $photographer->id,
        'image_url' => 'https://example.com/photo.jpg',
    ], [
        'uuid' => (string) Str::uuid(),
        'thumbnail_url' => 'https://example.com/photo-thumb.jpg',
        'title' => 'Audit Photo',
    ]);

    // Awards
    $award = PhotographerAward::firstOrCreate([
        'photographer_id' => $photographer->id,
        'title' => 'Audit Award',
        'year' => (int) date('Y'),
    ], [
        'organization' => 'Audit Org',
        'type' => 'award',
    ]);

    // Inquiry & Booking
    $inquiry = Inquiry::firstOrCreate([
        'client_id' => $client->id,
        'photographer_id' => $photographer->id,
        'event_date' => now()->addDays(15)->toDateString(),
    ], [
        'uuid' => (string) Str::uuid(),
        'event_location' => 'Dhaka',
        'package_id' => $package->id,
        'status' => 'new',
    ]);

    $booking = Booking::firstOrCreate([
        'inquiry_id' => $inquiry->id,
        'client_id' => $client->id,
        'photographer_id' => $photographer->id,
    ], [
        'uuid' => (string) Str::uuid(),
        'package_id' => $package->id,
        'event_date' => now()->addDays(30)->toDateString(),
        'total_amount' => 8000,
        'status' => 'confirmed',
        'payment_status' => 'pending',
        'payment_method' => 'manual',
    ]);

    // Review
    $review = Review::firstOrCreate([
        'booking_id' => $booking->id,
        'reviewer_id' => $client->id,
    ], [
        'uuid' => (string) Str::uuid(),
        'photographer_id' => $photographer->id,
        'rating' => 5,
        'comment' => 'Great experience',
        'status' => 'published',
    ]);

    // Transaction
    $transaction = Transaction::where('user_id', $client->id)
        ->where('reference_id', (string) $booking->id)
        ->where('reference_table', 'bookings')
        ->first();

    if (!$transaction) {
        DB::table('transactions')->insert([
            'uuid' => (string) Str::uuid(),
            'user_id' => $client->id,
            'transaction_type' => 'booking',
            'reference_id' => (string) $booking->id,
            'reference_table' => 'bookings',
            'amount' => 8000,
            'currency' => 'BDT',
            'payment_method' => 'manual',
            'status' => 'completed',
            'net_amount' => 8000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $transaction = Transaction::where('user_id', $client->id)
            ->where('reference_id', (string) $booking->id)
            ->where('reference_table', 'bookings')
            ->first();
    }

    // Event
    $event = Event::firstOrCreate([
        'slug' => 'audit-event',
    ], [
        'uuid' => (string) Str::uuid(),
        'organizer_id' => $photographer->id,
        'title' => 'Audit Event',
        'description' => 'Audit event description',
        'event_type' => 'workshop',
        'event_date' => now()->addDays(7),
        'location' => 'Dhaka',
        'venue_name' => 'Audit Hall',
        'venue_address' => 'Audit Road, Dhaka',
        'city_id' => $city->id,
        'status' => 'published',
        'require_registration' => true,
    ]);

    // Event registration
    $eventRegistration = EventRegistration::firstOrCreate([
        'event_id' => $event->id,
        'user_id' => $client->id,
    ], [
        'qty' => 1,
        'total_amount' => 0,
        'qr_token' => 'AUDIT-QR-' . Str::random(8),
        'status' => 'confirmed',
    ]);

    // Competition
    $competition = Competition::firstOrCreate([
        'slug' => 'audit-competition',
    ], [
        'uuid' => (string) Str::uuid(),
        'admin_id' => $admin->id,
        'organizer_id' => $photographer->id,
        'title' => 'Audit Competition',
        'description' => 'Audit competition description',
        'submission_deadline' => now()->addDays(10),
        'status' => 'active',
        'is_public' => true,
    ]);

    $competitionCategory = CompetitionCategory::firstOrCreate([
        'competition_id' => $competition->id,
        'name' => 'Portrait',
    ], [
        'description' => 'Portrait category',
        'is_active' => true,
    ]);

    $submission = CompetitionSubmission::firstOrCreate([
        'competition_id' => $competition->id,
        'photographer_id' => $photographer->id,
    ], [
        'uuid' => (string) Str::uuid(),
        'category_id' => $category->id,
        'image_path' => 'uploads/competition/audit.jpg',
        'image_url' => 'https://example.com/comp.jpg',
        'thumbnail_url' => 'https://example.com/comp-thumb.jpg',
        'title' => 'Audit Submission',
        'status' => 'approved',
        'certificate_id' => 'CERT-' . Str::random(8),
        'certificate_url' => 'https://example.com/cert.pdf',
        'certificate_generated_at' => now(),
        'award_type' => 'winner',
        'rank' => 1,
    ]);

    $vote = CompetitionVote::firstOrCreate([
        'submission_id' => $submission->id,
        'voter_id' => $client->id,
    ], [
        'competition_id' => $competition->id,
        'vote_value' => 1,
        'voted_at' => now(),
        'ip_address' => '127.0.0.1',
    ]);

    $judge = Judge::firstOrCreate([
        'slug' => 'audit-judge',
    ], [
        'user_id' => $judgeUser->id,
        'name' => $judgeUser->name,
        'email' => $judgeUser->email,
        'is_active' => true,
    ]);

    $mentor = Mentor::firstOrCreate([
        'slug' => 'audit-mentor',
    ], [
        'user_id' => $mentorUser->id,
        'name' => $mentorUser->name,
        'email' => $mentorUser->email,
        'is_active' => true,
        'created_by' => $admin->id,
    ]);

    $competitionJudge = CompetitionJudge::firstOrCreate([
        'competition_id' => $competition->id,
        'judge_id' => $judgeUser->id,
    ], [
        'role' => 'head',
        'is_active' => true,
        'assigned_at' => now(),
    ]);

    $contactMessage = ContactMessage::firstOrCreate([
        'email' => 'audit.contact@example.com',
    ], [
        'name' => 'Audit Contact',
        'subject' => 'Audit Message',
        'message' => 'Audit contact message',
        'type' => 'contact',
        'status' => 'pending',
    ]);

    $notice = Notice::firstOrCreate([
        'title' => 'Audit Notice',
    ], [
        'message' => 'Audit notice body',
        'created_by' => $admin->id,
        'is_active' => true,
    ]);

    // Tokens
    $seed['tokens'] = [
        'admin' => $admin->createToken('audit_admin')->plainTextToken,
        'client' => $client->createToken('audit_client')->plainTextToken,
        'photographer' => $photographerUser->createToken('audit_photographer')->plainTextToken,
        'judge' => $judgeUser->createToken('audit_judge')->plainTextToken,
    ];

    $seed['ids'] = [
        'admin_id' => $admin->id,
        'client_id' => $client->id,
        'photographer_id' => $photographer->id,
        'photographer_user_id' => $photographerUser->id,
        'judge_id' => $judge->id,
        'judge_user_id' => $judgeUser->id,
        'mentor_id' => $mentor->id,
        'city_id' => $city->id,
        'category_id' => $category->id,
        'photo_category_id' => $photoCategory->id,
        'hashtag_id' => $hashtag->id,
        'package_id' => $package->id,
        'album_id' => $album->id,
        'photo_id' => $photo->id,
        'award_id' => $award->id,
        'inquiry_id' => $inquiry->id,
        'booking_id' => $booking->id,
        'review_id' => $review->id,
        'transaction_id' => $transaction->id,
        'event_id' => $event->id,
        'event_slug' => $event->slug,
        'event_registration_id' => $eventRegistration->id,
        'event_qr_token' => $eventRegistration->qr_token,
        'competition_id' => $competition->id,
        'competition_slug' => $competition->slug,
        'competition_category_id' => $competitionCategory->id,
        'submission_id' => $submission->id,
        'certificate_id' => $submission->certificate_id,
        'sponsor_id' => $sponsor->id,
        'contact_message_id' => $contactMessage->id,
        'notice_id' => $notice->id,
    ];

    $settingsTableExists = !empty(DB::select("SHOW TABLES LIKE 'settings'"));
    $seed['settings_table_exists'] = $settingsTableExists;

} catch (Throwable $e) {
    $seed['errors'][] = $e->getMessage();
}

file_put_contents(storage_path('app/audit-seed.json'), json_encode($seed, JSON_PRETTY_PRINT));

// Route audit
$baseUrl = 'http://127.0.0.1:8000';

$routes = app('router')->getRoutes();
$results = [];

foreach ($routes as $route) {
    $methods = array_values(array_diff($route->methods(), ['HEAD']));
    if (empty($methods)) {
        $methods = ['GET'];
    }

    foreach ($methods as $method) {
        $uri = $route->uri();
        $url = $baseUrl . '/' . ltrim($uri, '/');

        $sampleIds = $seed['ids'] ?? [];

        $replacements = [
            '{id}' => $sampleIds['event_id'] ?? 1,
            '{user}' => $sampleIds['client_id'] ?? 1,
            '{photographer}' => $sampleIds['photographer_id'] ?? 1,
            '{photographerId}' => $sampleIds['photographer_id'] ?? 1,
            '{photographer_id}' => $sampleIds['photographer_id'] ?? 1,
            '{albumId}' => $sampleIds['album_id'] ?? 1,
            '{album}' => $sampleIds['album_id'] ?? 1,
            '{photo}' => $sampleIds['photo_id'] ?? 1,
            '{package}' => $sampleIds['package_id'] ?? 1,
            '{category}' => $sampleIds['category_id'] ?? 1,
            '{city}' => $sampleIds['city_id'] ?? 1,
            '{competition}' => $sampleIds['competition_id'] ?? 1,
            '{submission}' => $sampleIds['submission_id'] ?? 1,
            '{booking}' => $sampleIds['booking_id'] ?? 1,
            '{transactionId}' => $sampleIds['transaction_id'] ?? 1,
            '{registration}' => $sampleIds['event_registration_id'] ?? 1,
            '{qrToken}' => $sampleIds['event_qr_token'] ?? 'AUDIT-QR',
            '{mentor}' => $sampleIds['mentor_id'] ?? 1,
            '{judge}' => $sampleIds['judge_id'] ?? 1,
            '{sponsor}' => $sampleIds['sponsor_id'] ?? 1,
            '{notice}' => $sampleIds['notice_id'] ?? 1,
            '{certificate_id}' => $sampleIds['certificate_id'] ?? 'CERT-TEST',
            '{provider}' => 'google',
            '{slug}' => $sampleIds['event_slug'] ?? 'audit-event',
            '{competition_id}' => $sampleIds['competition_id'] ?? 1,
        ];

        foreach ($replacements as $key => $value) {
            if (str_contains($url, $key)) {
                $url = str_replace($key, (string) $value, $url);
            }
        }

        // Special-case usernames
        if (str_contains($url, '@{username}')) {
            $url = str_replace('@{username}', '@' . ($seed['ids']['photographer_user_id'] ?? 1), $url);
        }

        $middleware = $route->gatherMiddleware();

        $token = null;
        $requiresAuth = collect($middleware)->contains(function ($mw) {
            return is_string($mw) && str_contains($mw, 'Authenticate:sanctum');
        });

        if ($requiresAuth) {
            if (collect($middleware)->contains(function ($mw) {
                return is_string($mw) && str_contains($mw, 'CheckRole:admin');
            })) {
                $token = $seed['tokens']['admin'] ?? null;
            } elseif (collect($middleware)->contains(function ($mw) {
                return is_string($mw) && str_contains($mw, 'CheckRole:judge');
            })) {
                $token = $seed['tokens']['judge'] ?? null;
            } else {
                // Default to photographer for protected routes; fallback to client
                $token = $seed['tokens']['photographer'] ?? ($seed['tokens']['client'] ?? null);
            }
        }

        $headers = ['Accept' => 'application/json'];
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        // Build payloads for mutating routes
        $payload = [];
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            if (str_contains($uri, 'auth/register')) {
                $payload = [
                    'name' => 'Route Audit User',
                    'email' => 'route.audit.' . Str::random(6) . '@example.com',
                    'password' => 'Password123!',
                    'password_confirmation' => 'Password123!',
                    'phone' => '017' . rand(10000000, 99999999),
                    'role' => 'client',
                ];
            } elseif (str_contains($uri, 'auth/login')) {
                $payload = [
                    'email' => 'admin.audit@example.com',
                    'password' => 'Password123!',
                ];
            } elseif (str_contains($uri, 'auth/forgot-password')) {
                $payload = ['email' => 'admin.audit@example.com'];
            } elseif (str_contains($uri, 'auth/resend-verification')) {
                $payload = ['email' => 'admin.audit@example.com'];
            } elseif (str_contains($uri, 'categories') && str_contains($uri, 'admin')) {
                $payload = [
                    'name' => 'Audit Category ' . Str::random(4),
                    'slug' => 'audit-category-' . Str::random(6),
                    'description' => 'Audit category',
                    'is_active' => true,
                ];
            } elseif (str_contains($uri, 'cities') && str_contains($uri, 'admin')) {
                $payload = [
                    'name' => 'Audit City ' . Str::random(4),
                    'slug' => 'audit-city-' . Str::random(6),
                    'division' => 'Dhaka',
                ];
            } elseif (str_contains($uri, 'competitions') && str_contains($uri, 'admin') && str_ends_with($uri, 'competitions')) {
                $payload = [
                    'title' => 'Audit Competition API',
                    'description' => 'Audit competition',
                    'submission_deadline' => now()->addDays(12)->toDateTimeString(),
                    'status' => 'active',
                ];
            } elseif (str_contains($uri, 'events') && str_contains($uri, 'admin') && str_ends_with($uri, 'events')) {
                $payload = [
                    'title' => 'Audit Event API',
                    'event_type' => 'workshop',
                    'description' => 'Audit event description',
                    'event_date' => now()->addDays(5)->toDateTimeString(),
                    'city_id' => $sampleIds['city_id'] ?? null,
                    'location' => 'Dhaka',
                    'venue_name' => 'Audit Venue',
                    'venue_address' => 'Audit Road, Dhaka',
                    'organizer_id' => $sampleIds['photographer_id'] ?? null,
                    'status' => 'published',
                ];
            } elseif (str_contains($uri, 'photographer/packages')) {
                $payload = [
                    'name' => 'Audit Package API',
                    'description' => 'Audit package',
                    'base_price' => 3000,
                    'duration_unit' => 'hours',
                    'duration_value' => 2,
                    'is_active' => true,
                ];
            } elseif (str_contains($uri, 'photographer/albums')) {
                $payload = [
                    'name' => 'Audit Album API',
                    'slug' => 'audit-album-api-' . Str::random(4),
                    'description' => 'Audit album',
                    'is_public' => true,
                ];
            } elseif (str_contains($uri, 'photographer/awards')) {
                $payload = [
                    'title' => 'Audit Award API',
                    'organization' => 'Audit Org',
                    'year' => (int) date('Y'),
                    'type' => 'award',
                ];
            } elseif (str_contains($uri, 'bookings/inquiry')) {
                $payload = [
                    'photographer_id' => $sampleIds['photographer_id'] ?? null,
                    'package_id' => $sampleIds['package_id'] ?? null,
                    'event_date' => now()->addDays(20)->toDateString(),
                    'event_location' => 'Dhaka',
                    'budget_min' => 3000,
                    'budget_max' => 8000,
                ];
            } elseif (str_contains($uri, 'reviews') && $method === 'POST') {
                $payload = [
                    'booking_id' => $sampleIds['booking_id'] ?? null,
                    'rating' => 5,
                    'comment' => 'Audit review',
                ];
            } elseif (str_contains($uri, 'contact-messages') && $method === 'POST') {
                $payload = [
                    'name' => 'Audit Contact API',
                    'email' => 'audit.contact.api@example.com',
                    'subject' => 'Audit subject',
                    'message' => 'Audit message',
                    'type' => 'general',
                ];
            } elseif (str_contains($uri, 'notices') && $method === 'POST') {
                $payload = [
                    'title' => 'Audit Notice API',
                    'message' => 'Audit notice message',
                    'is_active' => true,
                ];
            } elseif (str_contains($uri, 'photographer/profile')) {
                $payload = [
                    'bio' => 'Updated bio',
                    'location' => 'Dhaka',
                ];
            } elseif (str_contains($uri, 'payments/initiate')) {
                $payload = [
                    'booking_id' => $sampleIds['booking_id'] ?? null,
                    'amount' => 8000,
                    'payment_method' => 'manual',
                ];
            } elseif (str_contains($uri, 'events') && str_contains($uri, '/rsvp')) {
                $payload = [
                    'event_id' => $sampleIds['event_id'] ?? null,
                ];
            } elseif (str_contains($uri, 'competitions') && str_contains($uri, 'submissions') && $method === 'POST') {
                $payload = [
                    'image_url' => 'https://example.com/comp-api.jpg',
                    'thumbnail_url' => 'https://example.com/comp-api-thumb.jpg',
                    'title' => 'API Submission',
                    'category_id' => $sampleIds['category_id'] ?? null,
                ];
            } elseif (str_contains($uri, 'notification-preferences') && $method === 'PUT') {
                $payload = [
                    'email' => true,
                    'sms' => false,
                    'push' => true,
                ];
            } elseif (str_contains($uri, 'settings/bulk')) {
                $payload = [
                    'settings' => [
                        ['key' => 'audit_key', 'value' => 'audit_value'],
                    ],
                ];
            } elseif (str_contains($uri, 'settings/') && $method === 'PUT') {
                $payload = [
                    'value' => 'audit_value',
                ];
            }
        }

        try {
            $response = Http::withHeaders($headers)->send($method, $url, [
                'json' => $payload,
                'timeout' => 20,
            ]);

            $results[] = [
                'method' => $method,
                'uri' => $uri,
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $middleware,
                'status' => $response->status(),
                'ok' => $response->successful(),
                'error' => $response->failed() ? substr($response->body(), 0, 500) : null,
            ];
        } catch (Throwable $e) {
            $results[] = [
                'method' => $method,
                'uri' => $uri,
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $middleware,
                'status' => 0,
                'ok' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

file_put_contents(storage_path('app/route-audit-results.json'), json_encode($results, JSON_PRETTY_PRINT));

echo "Route audit completed. Results saved to storage/app/route-audit-results.json\n";
