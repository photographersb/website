# 🏆 Photographer SB - Premium Platform Upgrade Plan
**Production-Grade Implementation Roadmap**

---

## 📊 1. SYSTEM ARCHITECTURE MAP

### Current Database Status (Verified)
✅ **Core Tables Exist:**
- `users` (with role: photographer, client, admin, super_admin, moderator, judge, mentor)
- `photographers`, `categories`, `cities`, `packages`, `photos`, `albums`
- `bookings`, `inquiries`, `quotes`, `reviews`, `review_replies`
- `events`, `event_rsvps`
- `competitions`, `competition_submissions`, `competition_votes`, `competition_prizes`, `competition_sponsors`, `competition_categories`
- `competition_judges`, `competition_scores`, `scoring_criteria`
- `mentors`, `judges`, `competition_mentor`
- `transactions`, `subscriptions`, `subscription_plans`
- `verifications`, `trust_scores`, `audit_logs`, `activity_logs`

### New Tables Required

#### A) Certificate System
```sql
-- certificate_templates
id, name, slug, type (event|workshop|competition)
background_image_url, layout_config (json)
typography_settings (json: fonts, sizes, colors)
signature_images (json: [{name, image_url, position}])
placeholders (json: {participant_name, event_name, date, id, custom})
border_style, watermark_opacity
is_active, is_default, created_by_admin_id
created_at, updated_at

-- certificates
id, uuid, certificate_code (unique, 8-char alphanumeric)
template_id, entity_type (event|workshop|competition)
entity_id (event_id or competition_id)
participant_id (user_id), participant_name
issued_date, expiry_date (nullable)
certificate_data (json: all merge fields)
pdf_url, qr_code_url
verification_status (valid|revoked|expired)
revoked_at, revoked_by, revocation_reason
download_count, last_downloaded_at
emailed_at, email_status
created_at, updated_at

-- certificate_issues_log
id, certificate_id, action (generated|downloaded|emailed|verified|revoked)
actor_id, ip_address, user_agent
metadata (json), created_at
```

#### B) Social Share Frame System
```sql
-- competition_share_frames
id, competition_id, template_name
orientation (portrait|square|story|landscape)
frame_overlay_url, qr_position (json: {x, y, size})
text_positions (json: {competition_name: {x,y}, photographer: {x,y}, vote_cta: {x,y}})
brand_logo_url, watermark_opacity
font_family, font_color, background_color
is_active, display_order
created_at, updated_at

-- generated_share_images
id, submission_id, frame_template_id
generated_image_url, orientation
share_url, short_url, qr_code_data
generation_metadata (json: photographer, title, votes)
download_count, share_count
created_at, updated_at
```

#### C) Enhanced Judge System (Already exists, needs expansion)
```sql
-- judges table (EXISTS - expand)
ADD COLUMN: portfolio_url, linkedin_url
ADD COLUMN: years_of_experience INT
ADD COLUMN: specialization (json: [portrait, landscape, wildlife])
ADD COLUMN: profile_image_url
ADD COLUMN: total_competitions_judged INT DEFAULT 0
ADD COLUMN: average_scoring_time INT (minutes)

-- judge_availability (NEW)
id, judge_id, available_from, available_to
timezone, max_competitions_concurrent
is_available, notes, created_at, updated_at

-- competition_judges table (EXISTS - verify fields)
Ensure: judge_profile_id links to judges.id
Add if missing: scoring_deadline, submission_count_assigned
Add: progress_percentage, submissions_scored_count
```

#### D) Booking Marketplace Enhancement
```sql
-- booking_messages (NEW)
id, booking_id, sender_id, sender_type (client|photographer)
message, attachments (json: [{filename, url, type, size}])
is_read, read_at, is_system_message
created_at, updated_at

-- booking_status_logs (NEW)
id, booking_id, old_status, new_status
changed_by_user_id, reason, notes
ip_address, created_at

-- booking_requests table (EXISTS as inquiries - enhance)
ALTER inquiries ADD COLUMN: preferred_time_slot
ADD COLUMN: event_type_detail (wedding, birthday, corporate)
ADD COLUMN: guest_count INT
ADD COLUMN: indoor_outdoor (indoor|outdoor|both)
ADD COLUMN: additional_services (json: [videography, drone, album])
```

#### E) Photographer Verification Enhancement
```sql
-- user_verifications (NEW - separate from existing verifications table)
id, user_id, verification_type (phone|email|nid|business|studio)
verification_status (pending|approved|rejected)
document_url, document_number, document_type
verified_by_admin_id, verified_at
rejection_reason, notes
expires_at, created_at, updated_at

-- verification_requests (NEW)
id, user_id, request_type (nid|business_license|tax_certificate)
requested_documents (json), submitted_documents (json)
status (draft|submitted|under_review|approved|rejected)
reviewer_id, reviewed_at, reviewer_notes
created_at, updated_at
```

#### F) Event Management Premium
```sql
-- events table (EXISTS - enhance)
ADD COLUMN: event_type (free|paid)
ADD COLUMN: ticket_price DECIMAL(10,2)
ADD COLUMN: capacity INT
ADD COLUMN: seats_available INT
ADD COLUMN: early_bird_price DECIMAL(10,2)
ADD COLUMN: early_bird_deadline DATETIME
ADD COLUMN: venue_name VARCHAR(255)
ADD COLUMN: venue_address TEXT
ADD COLUMN: venue_google_maps_url
ADD COLUMN: agenda (json: [{time, session, speaker}])
ADD COLUMN: requirements TEXT (what to bring)

-- event_registrations (NEW)
id, event_id, user_id, registration_code (unique)
ticket_type (regular|early_bird|vip)
payment_status, payment_method, amount_paid
qr_code_url, checked_in, checked_in_at
registration_date, created_at, updated_at

-- event_attendance (NEW)
id, event_id, registration_id, user_id
check_in_time, check_out_time
scanned_by_admin_id, scan_location
attendance_status (present|absent|late)
notes, created_at, updated_at

-- event_mentors (EXISTS as competition_mentor - verify reuse)
Can reuse for events if entity_type added
```

#### G) SEO & Tracking Tables
```sql
-- seo_metadata (NEW)
id, entity_type (photographer|category|city|tag|event|competition)
entity_id, slug (unique per type)
meta_title, meta_description, meta_keywords
og_title, og_description, og_image_url
twitter_card, canonical_url
schema_markup (json)
sitemap_priority DECIMAL(2,1), sitemap_frequency
is_indexed, noindex, nofollow
created_at, updated_at

-- tracking_scripts (NEW)
id, name (Facebook Pixel|GA4|GTM)
script_type, tracking_id, script_code (TEXT)
placement (head|body_start|body_end)
is_active, requires_consent
environments (json: [production, staging])
created_at, updated_at

-- page_analytics (NEW - optional)
id, url, page_type, visit_count
unique_visitors, avg_time_on_page
bounce_rate, date, created_at
```

#### H) Admin Error & Health Monitoring
```sql
-- system_errors (NEW)
id, error_type (exception|warning|notice|fatal)
severity (low|medium|high|critical)
module (booking|competition|payment|auth)
error_message, error_code, stack_trace (TEXT)
request_url, request_method, request_payload (json)
user_id, ip_address, user_agent
is_resolved, resolved_by_admin_id, resolved_at
resolution_notes, occurrence_count
first_seen_at, last_seen_at, created_at

-- admin_link_checker (NEW)
id, route_path, route_name, http_method
expected_status, actual_status
response_time_ms, error_message
last_checked_at, check_frequency (daily|weekly)
is_broken, broken_since, created_at, updated_at

-- system_health_logs (NEW)
id, check_type (database|cache|queue|storage|api)
status (healthy|degraded|down)
response_time_ms, error_details
checked_at, created_at
```

---

## 🗺️ 2. DATABASE RELATIONSHIP MAP

```
USERS (core)
├── photographers (1:1)
├── mentors (1:1)
├── judges (1:1)
├── bookings (1:M as client)
├── inquiries (1:M)
├── reviews (1:M as reviewer)
├── certificates (1:M as participant)
├── user_verifications (1:M)
└── booking_messages (1:M)

PHOTOGRAPHERS
├── packages (1:M)
├── photos (1:M)
├── albums (1:M)
├── bookings (1:M)
├── reviews (1:M)
└── trust_scores (1:1)

EVENTS
├── event_rsvps (1:M)
├── event_registrations (1:M)
├── event_attendance (1:M)
├── event_mentors (M:M via pivot)
├── certificates (1:M polymorphic)
└── seo_metadata (1:1 polymorphic)

COMPETITIONS
├── competition_submissions (1:M)
├── competition_votes (1:M)
├── competition_judges (1:M)
├── competition_scores (1:M through judges)
├── competition_prizes (1:M)
├── competition_sponsors (1:M)
├── competition_categories (1:M)
├── competition_share_frames (1:M)
├── certificates (1:M polymorphic)
└── seo_metadata (1:1 polymorphic)

COMPETITION_SUBMISSIONS
├── competition_votes (1:M)
├── competition_scores (1:M from judges)
├── generated_share_images (1:M)
└── certificates (1:1 for winners)

BOOKINGS
├── quotes (1:1)
├── inquiries (1:1)
├── reviews (1:1)
├── booking_messages (1:M)
├── booking_status_logs (1:M)
└── transactions (1:M)

CERTIFICATES
├── certificate_templates (M:1)
├── certificate_issues_log (1:M)
└── entity (polymorphic: events|competitions)

JUDGES
├── competition_judges (1:M)
├── competition_scores (1:M)
└── judge_availability (1:M)
```

---

## 🎯 3. MODULE IMPLEMENTATION PLAN

### **P0 - MUST HAVE (Launch Blockers)**

#### **P0.1 - Booking Marketplace Core** (5-7 days)
**Why Critical:** Primary revenue driver

**Backend:**
- ✅ `bookings` table exists
- ✅ `inquiries` table exists
- ⚠️ ADD: `booking_messages` table
- ⚠️ ADD: `booking_status_logs` table
- ⚠️ ENHANCE: `inquiries` table with missing fields

**API Endpoints:**
```php
// Client
POST   /api/v1/bookings/inquire          // Create inquiry
GET    /api/v1/bookings/my-inquiries     // List my inquiries
GET    /api/v1/bookings/{id}/messages    // Get conversation
POST   /api/v1/bookings/{id}/messages    // Send message
POST   /api/v1/bookings/{id}/accept      // Accept quote
POST   /api/v1/bookings/{id}/decline     // Decline quote

// Photographer
GET    /api/v1/photographer/bookings     // All bookings
PATCH  /api/v1/photographer/bookings/{id}/status  // Update status
POST   /api/v1/photographer/bookings/{id}/quote   // Send quote
GET    /api/v1/photographer/bookings/{id}/messages
POST   /api/v1/photographer/bookings/{id}/messages

// Admin
GET    /api/v1/admin/bookings           // All bookings
GET    /api/v1/admin/bookings/stats     // Metrics
```

**Frontend:**
```
/client/bookings/new                    // New inquiry form
/client/bookings                        // My bookings list
/client/bookings/{id}                   // Booking detail + messages

/photographer/bookings                  // Photographer dashboard
/photographer/bookings/{id}             // Booking detail + quote form
/photographer/bookings/{id}/messages    // Message thread

/admin/bookings                         // Admin list + filters
/admin/bookings/{id}                    // Admin detail view
```

**Mobile UX:**
- Sticky message input at bottom
- Collapsible booking details card
- One-tap call/email photographer
- File attachment preview (images inline)
- Status badge chips
- Pull-to-refresh on list

---

#### **P0.2 - Photographer Verification** (3-4 days)
**Why Critical:** Trust & safety

**Backend:**
- ⚠️ CREATE: `user_verifications` table
- ⚠️ CREATE: `verification_requests` table
- Add verification badge logic to photographer profiles

**API Endpoints:**
```php
// Photographer
POST   /api/v1/verification/request       // Submit verification
GET    /api/v1/verification/status        // Check status
POST   /api/v1/verification/upload-document

// Admin
GET    /api/v1/admin/verifications        // Pending requests
GET    /api/v1/admin/verifications/{id}   // Review detail
PATCH  /api/v1/admin/verifications/{id}/approve
PATCH  /api/v1/admin/verifications/{id}/reject
```

**Frontend:**
```
/photographer/verification/request      // Multi-step form
/photographer/verification/status       // Status tracker

/admin/verifications                    // Admin queue
/admin/verifications/{id}               // Review interface
```

**Verification Types:**
1. ✅ Phone (already exists)
2. ✅ Email (already exists)
3. ⚠️ NID (new)
4. ⚠️ Business License (new)
5. ⚠️ Studio Address (new)

**Badge System:**
- Green checkmark: Email + Phone verified
- Blue shield: NID verified
- Gold star: Business verified

---

#### **P0.3 - SEO Core (Photographer Profiles)** (2-3 days)
**Why Critical:** Organic traffic = free customers

**Backend:**
- ⚠️ CREATE: `seo_metadata` table (polymorphic)
- Auto-generate meta from photographer profile
- Dynamic OG image generation

**Implementation:**
```php
// Auto-generate for photographers
public function generatePhotographerSEO($photographer) {
    return [
        'meta_title' => "{$photographer->user->name} - Professional Photographer in {$photographer->city->name}",
        'meta_description' => "{$photographer->bio} | Specializing in {$photographer->specialty}. Book now starting from ৳{$photographer->min_price}",
        'og_title' => "Hire {$photographer->user->name} - {$photographer->specialty}",
        'og_description' => "⭐ {$photographer->average_rating}/5 ({$photographer->total_reviews} reviews) | ৳{$photographer->min_price}+",
        'og_image_url' => $photographer->cover_photo ?: $photographer->portfolio[0],
        'canonical_url' => "https://photographersb.com/@{$photographer->username}",
        'schema_markup' => [
            '@type' => 'LocalBusiness',
            'name' => $photographer->user->name,
            'priceRange' => "৳{$photographer->min_price}-৳{$photographer->max_price}",
            'aggregateRating' => [
                'ratingValue' => $photographer->average_rating,
                'reviewCount' => $photographer->total_reviews
            ]
        ]
    ];
}
```

**Routes:**
```php
// Public photographer profile
Route::get('/@{username}', [PhotographerProfileController::class, 'show']);

// SEO endpoints
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/sitemap/photographers.xml', [SitemapController::class, 'photographers']);
Route::get('/sitemap/categories.xml', [SitemapController::class, 'categories']);
Route::get('/sitemap/cities.xml', [SitemapController::class, 'cities']);
Route::get('/robots.txt', [RobotsController::class, 'index']);
```

**Frontend:**
```vue
<!-- layouts/AppLayout.vue -->
<Head>
  <title>{{ seoMeta.title }}</title>
  <meta name="description" :content="seoMeta.description" />
  <meta property="og:title" :content="seoMeta.og_title" />
  <meta property="og:description" :content="seoMeta.og_description" />
  <meta property="og:image" :content="seoMeta.og_image" />
  <meta property="og:url" :content="seoMeta.canonical_url" />
  <link rel="canonical" :href="seoMeta.canonical_url" />
  <script type="application/ld+json" v-html="seoMeta.schema_markup"></script>
</Head>
```

---

### **P1 - IMPORTANT (Post-Launch Features)**

#### **P1.1 - Certificate System (Auto + Manual)** (6-8 days)

**Backend:**
- ⚠️ CREATE: `certificate_templates` table
- ⚠️ CREATE: `certificates` table
- ⚠️ CREATE: `certificate_issues_log` table

**Certificate Generator Service:**
```php
namespace App\Services;

use Intervention\Image\ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateGeneratorService
{
    public function generate(CertificateTemplate $template, array $data): Certificate
    {
        // 1. Load background image
        $image = ImageManager::gd()->read($template->background_image_url);
        
        // 2. Apply participant name
        $image->text(
            $data['participant_name'],
            $template->typography_settings['name']['x'],
            $template->typography_settings['name']['y'],
            function($font) use ($template) {
                $font->file($template->typography_settings['name']['font']);
                $font->size($template->typography_settings['name']['size']);
                $font->color($template->typography_settings['name']['color']);
                $font->align('center');
            }
        );
        
        // 3. Apply event/workshop name
        $image->text(...);
        
        // 4. Apply date (DD-MM-YYYY format)
        $formattedDate = $data['issued_date']->format('d-m-Y');
        $image->text(...);
        
        // 5. Apply signature images
        foreach ($template->signature_images as $sig) {
            $signature = ImageManager::gd()->read($sig['image_url']);
            $image->place($signature, $sig['position']['x'], $sig['position']['y']);
        }
        
        // 6. Generate QR code
        $qrCode = QrCode::size(150)->generate(
            route('certificate.verify', ['code' => $certificateCode])
        );
        $image->place($qrCode, 'bottom-right', 20, 20);
        
        // 7. Save to storage
        $filename = "certificates/{$certificateCode}.pdf";
        
        // Convert to PDF using DomPDF or Snappy
        $pdf = PDF::loadView('certificates.template', [
            'image' => $image->toJpeg(),
            'data' => $data
        ]);
        
        Storage::disk('public')->put($filename, $pdf->output());
        
        // 8. Create certificate record
        return Certificate::create([
            'certificate_code' => $certificateCode,
            'template_id' => $template->id,
            'entity_type' => $data['entity_type'],
            'entity_id' => $data['entity_id'],
            'participant_id' => $data['participant_id'],
            'participant_name' => $data['participant_name'],
            'issued_date' => $data['issued_date'],
            'certificate_data' => $data,
            'pdf_url' => Storage::url($filename),
            'qr_code_url' => Storage::url("qr/{$certificateCode}.png"),
            'verification_status' => 'valid'
        ]);
    }
    
    public function bulkGenerate(CertificateTemplate $template, array $participants): Collection
    {
        $certificates = collect();
        
        foreach ($participants as $participant) {
            $certificates->push($this->generate($template, [
                'entity_type' => $participant['entity_type'],
                'entity_id' => $participant['entity_id'],
                'participant_id' => $participant['user_id'],
                'participant_name' => $participant['name'],
                'issued_date' => now()
            ]));
        }
        
        return $certificates;
    }
    
    public function sendEmail(Certificate $certificate): void
    {
        Mail::to($certificate->participant->email)->send(
            new CertificateIssuedMail($certificate)
        );
        
        $certificate->update([
            'emailed_at' => now(),
            'email_status' => 'sent'
        ]);
    }
}
```

**API Endpoints:**
```php
// Admin - Certificate Templates
GET    /api/v1/admin/certificates/templates
POST   /api/v1/admin/certificates/templates
PATCH  /api/v1/admin/certificates/templates/{id}
DELETE /api/v1/admin/certificates/templates/{id}

// Admin - Certificate Generation
POST   /api/v1/admin/certificates/generate-manual      // Select participants + generate
POST   /api/v1/admin/certificates/bulk-generate        // Event/workshop participants
POST   /api/v1/admin/certificates/{id}/resend-email
POST   /api/v1/admin/certificates/bulk-download        // ZIP export

// Public - Verification
GET    /api/v1/certificates/verify/{code}              // Public verification page

// User - My Certificates
GET    /api/v1/certificates/my-certificates
GET    /api/v1/certificates/{id}/download
```

**Frontend:**
```
/admin/certificates/templates                    // CRUD for templates
/admin/certificates/templates/create            // Visual template editor
/admin/certificates/templates/{id}/edit

/admin/certificates/issue                        // Manual issue form
/admin/certificates                              // Issued certificates list
/admin/certificates/{id}                         // Detail + re-send

/admin/events/{id}/certificates                  // Event-specific certificates
/admin/workshops/{id}/certificates

/certificate/verify/{code}                       // Public verification page

/my-certificates                                 // User's certificates
```

**Auto-Issue Triggers:**
```php
// Event attendance marked
Event::observe([
    'attendanceMarked' => function($attendance) {
        if ($attendance->event->auto_issue_certificate) {
            CertificateGeneratorService::generate(
                $attendance->event->certificate_template,
                ['participant' => $attendance->user, ...]
            );
        }
    }
]);

// Workshop completion
Workshop::observe([
    'completed' => function($workshop) {
        $participants = $workshop->registrations()
            ->where('attendance_status', 'present')
            ->get();
        
        CertificateGeneratorService::bulkGenerate(
            $workshop->certificate_template,
            $participants
        );
    }
]);
```

**Public Verification Page:**
```vue
<!-- /certificate/verify/{code} -->
<template>
  <div class="certificate-verification">
    <div v-if="certificate.verification_status === 'valid'" class="valid">
      <i class="check-circle text-success"></i>
      <h1>✅ Valid Certificate</h1>
      
      <div class="certificate-details">
        <p><strong>Certificate ID:</strong> {{ certificate.certificate_code }}</p>
        <p><strong>Participant:</strong> {{ certificate.participant_name }}</p>
        <p><strong>Event:</strong> {{ certificate.entity.name }}</p>
        <p><strong>Issue Date:</strong> {{ formatDate(certificate.issued_date) }}</p>
      </div>
      
      <img :src="certificate.pdf_url" class="certificate-preview" />
      
      <button @click="download">Download Certificate</button>
    </div>
    
    <div v-else class="invalid">
      <i class="x-circle text-danger"></i>
      <h1>❌ Invalid or Revoked Certificate</h1>
      <p>This certificate code does not exist or has been revoked.</p>
    </div>
  </div>
</template>
```

---

#### **P1.2 - Judge Dashboard** (5-7 days)

**Backend:**
- ✅ `judges` table exists
- ✅ `competition_judges` table exists
- ✅ `competition_scores` table exists
- ⚠️ ENHANCE: `judges` table with portfolio_url, specialization
- ⚠️ CREATE: `judge_availability` table

**Scoring Criteria Config:**
```php
// Admin sets per competition
'scoring_criteria' => [
    'composition' => ['weight' => 0.2, 'max' => 10],
    'lighting' => ['weight' => 0.2, 'max' => 10],
    'storytelling' => ['weight' => 0.2, 'max' => 10],
    'creativity' => ['weight' => 0.2, 'max' => 10],
    'technical_quality' => ['weight' => 0.2, 'max' => 10]
]
```

**API Endpoints:**
```php
// Judge Dashboard
GET    /api/v1/judge/dashboard                          // My stats + assigned competitions
GET    /api/v1/judge/competitions                       // My competitions
GET    /api/v1/judge/competitions/{id}/submissions      // Submissions to score
GET    /api/v1/judge/submissions/{id}                   // View submission detail
POST   /api/v1/judge/submissions/{id}/score             // Submit score
PATCH  /api/v1/judge/submissions/{id}/score             // Update score
GET    /api/v1/judge/scoring-history                    // My past scores

// Admin - Judge Management
GET    /api/v1/admin/judges                             // All judges
POST   /api/v1/admin/judges                             // Create judge profile
POST   /api/v1/admin/competitions/{id}/assign-judge     // Assign judge
DELETE /api/v1/admin/competitions/{id}/judges/{judgeId} // Remove judge
GET    /api/v1/admin/competitions/{id}/judge-progress   // Scoring progress
POST   /api/v1/admin/competitions/{id}/publish-results  // Publish winners
```

**Scoring Rules Enforcement:**
```php
class JudgeScoreValidator
{
    public function validate($judgeId, $submissionId): array
    {
        $errors = [];
        
        // 1. Judge must be assigned to competition
        if (!CompetitionJudge::where('judge_id', $judgeId)
            ->where('competition_id', $submission->competition_id)
            ->exists()) {
            $errors[] = 'Judge not assigned to this competition';
        }
        
        // 2. Deadline not passed
        $competition = Competition::find($submission->competition_id);
        if (now() > $competition->judging_end_at) {
            $errors[] = 'Judging deadline has passed';
        }
        
        // 3. Cannot score own submission
        if ($submission->photographer_id === $judgeId) {
            $errors[] = 'Cannot score your own submission';
        }
        
        // 4. Already scored
        if (CompetitionScore::where('judge_id', $judgeId)
            ->where('submission_id', $submissionId)
            ->exists()) {
            $errors[] = 'Submission already scored. Use PATCH to update.';
        }
        
        return $errors;
    }
}
```

**Result Calculation:**
```php
class CompetitionResultService
{
    public function calculateWinners(Competition $competition): array
    {
        $submissions = $competition->submissions()
            ->with('scores')
            ->get();
        
        $results = $submissions->map(function($submission) {
            // Average judge scores
            $avgJudgeScore = $submission->scores()->avg('total_score');
            
            // Public votes (if enabled)
            $publicVoteCount = $submission->votes()->count();
            
            // Weighted final score
            $finalScore = (
                ($avgJudgeScore * $competition->judge_weight_percentage / 100) +
                ($publicVoteCount * $competition->public_vote_weight_percentage / 100)
            );
            
            return [
                'submission_id' => $submission->id,
                'photographer_id' => $submission->photographer_id,
                'final_score' => $finalScore,
                'judge_score' => $avgJudgeScore,
                'public_votes' => $publicVoteCount
            ];
        })->sortByDesc('final_score');
        
        // Assign positions
        $position = 1;
        foreach ($results as $result) {
            CompetitionSubmission::find($result['submission_id'])
                ->update(['winner_position' => $position++]);
        }
        
        return $results->take(3)->toArray();
    }
    
    public function publishResults(Competition $competition): void
    {
        $winners = $this->calculateWinners($competition);
        
        $competition->update([
            'status' => 'completed',
            'results_announcement_date' => now()
        ]);
        
        // Send notifications to winners
        foreach ($winners as $winner) {
            $submission = CompetitionSubmission::find($winner['submission_id']);
            event(new CompetitionWinnerAnnounced($submission, $winner['position']));
        }
    }
}
```

**Frontend:**
```
/judge/dashboard                          // My stats + competitions
/judge/competitions                       // Assigned competitions list
/judge/competitions/{id}/submissions      // Submissions grid
/judge/submissions/{id}/score             // Scoring interface
/judge/scoring-history                    // Past scores

/admin/judges                             // Judge management
/admin/judges/create                      // Add judge
/admin/judges/{id}                        // Judge profile
/admin/competitions/{id}/judges           // Assign judges
/admin/competitions/{id}/judge-progress   // Scoring progress tracker
```

**Judge Scoring Interface (Mobile-First):**
```vue
<template>
  <div class="scoring-interface">
    <div class="submission-viewer">
      <img :src="submission.photo_url" class="full-width" />
      <div class="submission-meta">
        <h3>{{ submission.title }}</h3>
        <p>{{ submission.description }}</p>
      </div>
    </div>
    
    <div class="scoring-form">
      <div v-for="criterion in criteria" class="criterion">
        <label>{{ criterion.name }} (0-10)</label>
        <input 
          type="range" 
          min="0" 
          max="10" 
          v-model="scores[criterion.key]"
          class="score-slider"
        />
        <span class="score-value">{{ scores[criterion.key] }}</span>
      </div>
      
      <div class="total-score">
        <strong>Total Score:</strong> {{ totalScore }} / 50
      </div>
      
      <textarea 
        v-model="feedback" 
        placeholder="Feedback for photographer (optional)"
        class="feedback-textarea"
      ></textarea>
      
      <button @click="submitScore" class="submit-btn">
        Submit Score
      </button>
    </div>
  </div>
</template>
```

---

#### **P1.3 - Competition Share Frame Generator** (4-5 days)

**Backend:**
- ⚠️ CREATE: `competition_share_frames` table
- ⚠️ CREATE: `generated_share_images` table

**Frame Generator Service:**
```php
namespace App\Services;

use Intervention\Image\ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShareFrameGeneratorService
{
    public function generate(CompetitionSubmission $submission, string $orientation = 'square'): GeneratedShareImage
    {
        $frame = $submission->competition->share_frames()
            ->where('orientation', $orientation)
            ->where('is_active', true)
            ->first();
        
        if (!$frame) {
            throw new \Exception('No active frame template for this orientation');
        }
        
        // 1. Load submission photo
        $image = ImageManager::gd()->read($submission->photo_url);
        
        // 2. Resize to orientation
        $dimensions = $this->getOrientationDimensions($orientation);
        $image->cover($dimensions['width'], $dimensions['height']);
        
        // 3. Apply frame overlay
        $overlay = ImageManager::gd()->read($frame->frame_overlay_url);
        $image->place($overlay, 'center');
        
        // 4. Add competition name
        $image->text(
            $submission->competition->title,
            $frame->text_positions['competition_name']['x'],
            $frame->text_positions['competition_name']['y'],
            function($font) use ($frame) {
                $font->file($frame->font_family);
                $font->size(32);
                $font->color($frame->font_color);
                $font->align('center');
            }
        );
        
        // 5. Add photographer name
        $image->text(
            "By {$submission->photographer->name}",
            $frame->text_positions['photographer']['x'],
            $frame->text_positions['photographer']['y'],
            function($font) use ($frame) {
                $font->size(24);
                $font->color($frame->font_color);
            }
        );
        
        // 6. Add vote CTA
        $image->text(
            "🗳️ Vote for me!",
            $frame->text_positions['vote_cta']['x'],
            $frame->text_positions['vote_cta']['y'],
            function($font) use ($frame) {
                $font->size(28);
                $font->color('#FF6B35');
                $font->align('center');
            }
        );
        
        // 7. Generate QR code
        $shortUrl = $this->generateShortUrl($submission);
        $qrCode = QrCode::size(120)->generate($shortUrl);
        
        $qrImage = ImageManager::gd()->read($qrCode);
        $image->place(
            $qrImage,
            $frame->qr_position['x'],
            $frame->qr_position['y']
        );
        
        // 8. Add watermark
        $logo = ImageManager::gd()->read($frame->brand_logo_url);
        $logo->opacity($frame->watermark_opacity);
        $image->place($logo, 'bottom-right', 10, 10);
        
        // 9. Save
        $filename = "share-frames/{$submission->id}-{$orientation}-" . time() . ".jpg";
        $image->save(storage_path("app/public/{$filename}"), 90);
        
        // 10. Create record
        return GeneratedShareImage::create([
            'submission_id' => $submission->id,
            'frame_template_id' => $frame->id,
            'generated_image_url' => Storage::url($filename),
            'orientation' => $orientation,
            'share_url' => $shortUrl,
            'qr_code_data' => $shortUrl,
            'generation_metadata' => [
                'photographer' => $submission->photographer->name,
                'title' => $submission->title,
                'votes' => $submission->votes_count
            ]
        ]);
    }
    
    protected function getOrientationDimensions(string $orientation): array
    {
        return match($orientation) {
            'portrait' => ['width' => 1080, 'height' => 1350],  // 4:5
            'square' => ['width' => 1080, 'height' => 1080],    // 1:1
            'story' => ['width' => 1080, 'height' => 1920],     // 9:16
            'landscape' => ['width' => 1200, 'height' => 675],  // 16:9
        };
    }
    
    protected function generateShortUrl(CompetitionSubmission $submission): string
    {
        // Use Laravel short URL package or bitly
        return "https://photosb.link/v/{$submission->id}";
    }
}
```

**API Endpoints:**
```php
// Photographer/Public
POST   /api/v1/submissions/{id}/generate-frame          // Generate share image
GET    /api/v1/submissions/{id}/share-frames            // Get generated frames
GET    /api/v1/submissions/{id}/share-frames/download   // Download ZIP

// Admin - Frame Templates
GET    /api/v1/admin/competitions/{id}/share-frames
POST   /api/v1/admin/competitions/{id}/share-frames     // Create template
PATCH  /api/v1/admin/share-frames/{id}
DELETE /api/v1/admin/share-frames/{id}
```

**Frontend:**
```
/competitions/{id}/submissions/{submissionId}/share    // Share frame generator
/admin/competitions/{id}/share-frames                  // Template management
```

**Share Frame Generator UI:**
```vue
<template>
  <div class="share-frame-generator">
    <div class="orientation-selector">
      <button 
        v-for="orientation in orientations" 
        :key="orientation.value"
        @click="selectedOrientation = orientation.value"
        :class="{ active: selectedOrientation === orientation.value }"
      >
        <i :class="orientation.icon"></i>
        {{ orientation.label }}
      </button>
    </div>
    
    <div class="preview">
      <img 
        v-if="generatedFrame" 
        :src="generatedFrame.generated_image_url" 
        :class="`preview-${selectedOrientation}`"
      />
      <div v-else class="placeholder">
        <p>Select orientation to generate</p>
      </div>
    </div>
    
    <div class="actions">
      <button @click="generate" class="generate-btn">
        Generate Frame
      </button>
      
      <button 
        v-if="generatedFrame" 
        @click="download" 
        class="download-btn"
      >
        Download
      </button>
      
      <button 
        v-if="generatedFrame" 
        @click="share('whatsapp')" 
        class="share-btn whatsapp"
      >
        Share on WhatsApp
      </button>
      
      <button 
        v-if="generatedFrame" 
        @click="share('facebook')" 
        class="share-btn facebook"
      >
        Share on Facebook
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedOrientation: 'square',
      orientations: [
        { value: 'portrait', label: 'Instagram Post', icon: 'portrait' },
        { value: 'square', label: 'Facebook', icon: 'square' },
        { value: 'story', label: 'Story', icon: 'smartphone' },
        { value: 'landscape', label: 'Twitter', icon: 'landscape' }
      ],
      generatedFrame: null
    }
  },
  
  methods: {
    async generate() {
      const response = await axios.post(
        `/api/v1/submissions/${this.submissionId}/generate-frame`,
        { orientation: this.selectedOrientation }
      );
      
      this.generatedFrame = response.data.data;
    },
    
    download() {
      window.open(this.generatedFrame.generated_image_url, '_blank');
    },
    
    share(platform) {
      const url = this.generatedFrame.share_url;
      const text = `Vote for my photo in ${this.competition.title}!`;
      
      if (platform === 'whatsapp') {
        window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`);
      } else if (platform === 'facebook') {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`);
      }
    }
  }
}
</script>
```

---

#### **P1.4 - Event Management Premium** (4-6 days)

**Backend:**
- ✅ `events` table exists
- ✅ `event_rsvps` table exists
- ⚠️ ENHANCE: `events` table with venue, pricing fields
- ⚠️ CREATE: `event_registrations` table
- ⚠️ CREATE: `event_attendance` table

**Event Enhancement Migration:**
```php
Schema::table('events', function (Blueprint $table) {
    $table->enum('event_type', ['free', 'paid'])->default('free');
    $table->decimal('ticket_price', 10, 2)->nullable();
    $table->integer('capacity')->nullable();
    $table->integer('seats_available')->nullable();
    $table->decimal('early_bird_price', 10, 2)->nullable();
    $table->dateTime('early_bird_deadline')->nullable();
    $table->string('venue_name')->nullable();
    $table->text('venue_address')->nullable();
    $table->string('venue_google_maps_url', 500)->nullable();
    $table->json('agenda')->nullable();
    $table->text('requirements')->nullable();
});
```

**API Endpoints:**
```php
// Public
GET    /api/v1/events                               // List events
GET    /api/v1/events/{id}                          // Event detail
POST   /api/v1/events/{id}/register                 // Register for event

// User
GET    /api/v1/my-events                            // My registrations
GET    /api/v1/my-events/{id}/ticket                // Get ticket QR

// Admin
POST   /api/v1/admin/events                         // Create event
PATCH  /api/v1/admin/events/{id}                    // Update event
GET    /api/v1/admin/events/{id}/registrations      // Registrations list
GET    /api/v1/admin/events/{id}/attendance         // Attendance interface
POST   /api/v1/admin/events/{id}/scan-qr            // Scan QR + mark attendance
GET    /api/v1/admin/events/{id}/attendance/export  // Export CSV
POST   /api/v1/admin/events/{id}/assign-mentors     // Assign mentors
```

**Event Registration Logic:**
```php
class EventRegistrationService
{
    public function register(Event $event, User $user, array $data): EventRegistration
    {
        // 1. Check capacity
        if ($event->capacity && $event->seats_available <= 0) {
            throw new \Exception('Event is full');
        }
        
        // 2. Check duplicate registration
        if (EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists()) {
            throw new \Exception('Already registered');
        }
        
        // 3. Determine ticket price
        $ticketType = 'regular';
        $amount = $event->ticket_price;
        
        if ($event->early_bird_deadline && now() < $event->early_bird_deadline) {
            $ticketType = 'early_bird';
            $amount = $event->early_bird_price;
        }
        
        // 4. Generate registration code
        $registrationCode = strtoupper(substr(md5(uniqid()), 0, 8));
        
        // 5. Generate QR code
        $qrCodeUrl = $this->generateQRCode($registrationCode);
        
        // 6. Create registration
        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'registration_code' => $registrationCode,
            'ticket_type' => $ticketType,
            'amount_paid' => $amount,
            'payment_status' => $event->event_type === 'free' ? 'completed' : 'pending',
            'qr_code_url' => $qrCodeUrl,
            'registration_date' => now()
        ]);
        
        // 7. Decrement seats
        $event->decrement('seats_available');
        
        // 8. Send confirmation email
        Mail::to($user->email)->send(new EventRegistrationConfirmation($registration));
        
        return $registration;
    }
    
    protected function generateQRCode(string $code): string
    {
        $qr = QrCode::size(300)
            ->format('png')
            ->generate($code);
        
        $filename = "qr-codes/events/{$code}.png";
        Storage::disk('public')->put($filename, $qr);
        
        return Storage::url($filename);
    }
}
```

**QR Scanner + Attendance:**
```php
class EventAttendanceService
{
    public function scanQRCode(Event $event, string $qrCode, User $admin): EventAttendance
    {
        $registration = EventRegistration::where('registration_code', $qrCode)
            ->where('event_id', $event->id)
            ->firstOrFail();
        
        // Check if already checked in
        if ($registration->checked_in) {
            throw new \Exception('Already checked in at ' . $registration->checked_in_at->format('h:i A'));
        }
        
        // Create attendance record
        $attendance = EventAttendance::create([
            'event_id' => $event->id,
            'registration_id' => $registration->id,
            'user_id' => $registration->user_id,
            'check_in_time' => now(),
            'scanned_by_admin_id' => $admin->id,
            'attendance_status' => 'present'
        ]);
        
        // Update registration
        $registration->update([
            'checked_in' => true,
            'checked_in_at' => now()
        ]);
        
        return $attendance;
    }
    
    public function exportAttendance(Event $event): string
    {
        $registrations = EventRegistration::where('event_id', $event->id)
            ->with('user', 'attendance')
            ->get();
        
        $csv = "Name,Email,Phone,Registration Code,Check-in Time,Status\n";
        
        foreach ($registrations as $reg) {
            $csv .= implode(',', [
                $reg->user->name,
                $reg->user->email,
                $reg->user->phone,
                $reg->registration_code,
                $reg->checked_in_at ? $reg->checked_in_at->format('d-m-Y h:i A') : 'Not checked in',
                $reg->checked_in ? 'Present' : 'Absent'
            ]) . "\n";
        }
        
        $filename = "attendance-{$event->slug}-" . now()->format('d-m-Y') . ".csv";
        Storage::disk('public')->put("exports/{$filename}", $csv);
        
        return Storage::url("exports/{$filename}");
    }
}
```

**Frontend:**
```
/events                                  // Public event list
/events/{id}                            // Event detail + register button
/my-events                              // User's registered events
/my-events/{id}/ticket                  // Digital ticket with QR

/admin/events/create                    // Create event with all fields
/admin/events/{id}/edit
/admin/events/{id}/registrations        // Registrations list
/admin/events/{id}/attendance           // QR scanner interface
/admin/events/{id}/attendance/manual    // Manual attendance marking
```

**QR Scanner Interface (Mobile):**
```vue
<template>
  <div class="qr-scanner-page">
    <div class="event-header">
      <h2>{{ event.title }}</h2>
      <p>{{ event.event_date }} | {{ event.venue_name }}</p>
    </div>
    
    <div class="scanner-container">
      <qrcode-stream @detect="onQRDetect" class="qr-camera"></qrcode-stream>
      
      <div v-if="scanning" class="scanning-overlay">
        <p>Scanning...</p>
      </div>
    </div>
    
    <div v-if="lastScanned" class="last-scanned">
      <div class="success" v-if="lastScanned.status === 'success'">
        <i class="check-circle"></i>
        <p>✅ {{ lastScanned.name }} checked in!</p>
      </div>
      
      <div class="error" v-else>
        <i class="x-circle"></i>
        <p>❌ {{ lastScanned.error }}</p>
      </div>
    </div>
    
    <div class="attendance-stats">
      <div class="stat">
        <strong>{{ stats.checked_in }}</strong>
        <span>Checked In</span>
      </div>
      <div class="stat">
        <strong>{{ stats.total }}</strong>
        <span>Total Registered</span>
      </div>
    </div>
    
    <button @click="exportAttendance" class="export-btn">
      Export Attendance CSV
    </button>
  </div>
</template>

<script>
import { QrcodeStream } from 'vue-qrcode-reader';

export default {
  components: { QrcodeStream },
  
  data() {
    return {
      scanning: false,
      lastScanned: null,
      stats: { checked_in: 0, total: 0 }
    }
  },
  
  methods: {
    async onQRDetect(detectedCodes) {
      if (this.scanning) return;
      
      this.scanning = true;
      const qrCode = detectedCodes[0].rawValue;
      
      try {
        const response = await axios.post(
          `/api/v1/admin/events/${this.event.id}/scan-qr`,
          { qr_code: qrCode }
        );
        
        this.lastScanned = {
          status: 'success',
          name: response.data.data.user.name
        };
        
        this.stats.checked_in++;
        
        // Play success sound
        new Audio('/sounds/success.mp3').play();
        
      } catch (error) {
        this.lastScanned = {
          status: 'error',
          error: error.response.data.message
        };
        
        // Play error sound
        new Audio('/sounds/error.mp3').play();
      }
      
      setTimeout(() => {
        this.scanning = false;
        this.lastScanned = null;
      }, 2000);
    },
    
    async exportAttendance() {
      const response = await axios.get(
        `/api/v1/admin/events/${this.event.id}/attendance/export`
      );
      
      window.open(response.data.data.download_url);
    }
  }
}
</script>
```

---

### **P2 - PREMIUM (Nice-to-Have)**

#### **P2.1 - Tracking & Analytics** (3-4 days)

**Backend:**
- ⚠️ CREATE: `tracking_scripts` table
- Admin UI to manage GA4, FB Pixel, GTM
- Cookie consent banner integration

**API Endpoints:**
```php
// Admin
GET    /api/v1/admin/tracking-scripts
POST   /api/v1/admin/tracking-scripts
PATCH  /api/v1/admin/tracking-scripts/{id}
DELETE /api/v1/admin/tracking-scripts/{id}

// Public (for consent)
GET    /api/v1/tracking/config              // Get active scripts (post-consent)
```

**Tracking Implementation:**
```php
// TrackingScript Model
class TrackingScript extends Model
{
    protected $fillable = [
        'name', 'script_type', 'tracking_id', 'script_code',
        'placement', 'is_active', 'requires_consent', 'environments'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'requires_consent' => 'boolean',
        'environments' => 'array'
    ];
    
    public function shouldLoad(): bool
    {
        return $this->is_active && 
               in_array(config('app.env'), $this->environments);
    }
}

// TrackingService
class TrackingService
{
    public function getActiveScripts(bool $consentGiven = false): Collection
    {
        return TrackingScript::where('is_active', true)
            ->when(!$consentGiven, function($query) {
                $query->where('requires_consent', false);
            })
            ->get()
            ->filter(fn($script) => $script->shouldLoad());
    }
    
    public function renderScripts(string $placement, bool $consentGiven): string
    {
        $scripts = $this->getActiveScripts($consentGiven)
            ->where('placement', $placement);
        
        $html = '';
        foreach ($scripts as $script) {
            $html .= $script->script_code . "\n";
        }
        
        return $html;
    }
}
```

**Frontend Integration:**
```vue
<!-- layouts/AppLayout.vue -->
<template>
  <div>
    <Head>
      <div v-html="trackingScripts.head"></div>
    </Head>
    
    <div v-html="trackingScripts.body_start"></div>
    
    <slot />
    
    <CookieConsentBanner @consent-given="loadTrackingScripts" />
    
    <div v-html="trackingScripts.body_end"></div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      trackingScripts: {
        head: '',
        body_start: '',
        body_end: ''
      },
      consentGiven: false
    }
  },
  
  async mounted() {
    this.consentGiven = this.checkConsent();
    await this.loadTrackingScripts();
  },
  
  methods: {
    checkConsent() {
      return localStorage.getItem('tracking_consent') === 'true';
    },
    
    async loadTrackingScripts() {
      const response = await axios.get('/api/v1/tracking/config', {
        params: { consent: this.consentGiven }
      });
      
      this.trackingScripts = response.data.data;
    }
  }
}
</script>
```

**Cookie Consent Banner:**
```vue
<template>
  <div v-if="!consentGiven" class="cookie-banner">
    <div class="cookie-content">
      <p>
        🍪 We use cookies to improve your experience. 
        By continuing, you accept our use of cookies.
      </p>
      <div class="cookie-actions">
        <button @click="acceptAll" class="accept-btn">
          Accept All
        </button>
        <button @click="showPreferences" class="preferences-btn">
          Preferences
        </button>
      </div>
    </div>
    
    <div v-if="showingPreferences" class="cookie-preferences">
      <h3>Cookie Preferences</h3>
      
      <label>
        <input type="checkbox" v-model="preferences.essential" disabled checked />
        Essential Cookies (Required)
      </label>
      
      <label>
        <input type="checkbox" v-model="preferences.analytics" />
        Analytics Cookies (Google Analytics, Facebook Pixel)
      </label>
      
      <label>
        <input type="checkbox" v-model="preferences.marketing" />
        Marketing Cookies
      </label>
      
      <button @click="savePreferences">Save Preferences</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      consentGiven: false,
      showingPreferences: false,
      preferences: {
        essential: true,
        analytics: false,
        marketing: false
      }
    }
  },
  
  mounted() {
    this.consentGiven = localStorage.getItem('tracking_consent') === 'true';
  },
  
  methods: {
    acceptAll() {
      localStorage.setItem('tracking_consent', 'true');
      this.consentGiven = true;
      this.$emit('consent-given');
    },
    
    showPreferences() {
      this.showingPreferences = true;
    },
    
    savePreferences() {
      localStorage.setItem('tracking_consent', 'true');
      localStorage.setItem('tracking_preferences', JSON.stringify(this.preferences));
      this.consentGiven = true;
      this.$emit('consent-given');
    }
  }
}
</script>
```

**Admin Tracking Management:**
```
/admin/settings/tracking                 // List scripts
/admin/settings/tracking/create          // Add new script
/admin/settings/tracking/{id}/edit       // Edit script
```

---

#### **P2.2 - Admin Health Tools** (4-5 days)

**Backend:**
- ⚠️ CREATE: `system_errors` table
- ⚠️ CREATE: `admin_link_checker` table
- ⚠️ CREATE: `system_health_logs` table

**Error Logging System:**
```php
// Custom Exception Handler
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\SystemError;

class Handler extends ExceptionHandler
{
    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception)) {
            $this->logToDatabase($exception);
        }
        
        parent::report($exception);
    }
    
    protected function logToDatabase(Throwable $exception)
    {
        $request = request();
        
        // Check if error already exists (prevent spam)
        $existingError = SystemError::where('error_message', $exception->getMessage())
            ->where('module', $this->detectModule($exception))
            ->where('is_resolved', false)
            ->first();
        
        if ($existingError) {
            $existingError->increment('occurrence_count');
            $existingError->update(['last_seen_at' => now()]);
            return;
        }
        
        // Create new error record
        SystemError::create([
            'error_type' => $this->getErrorType($exception),
            'severity' => $this->getSeverity($exception),
            'module' => $this->detectModule($exception),
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'stack_trace' => $exception->getTraceAsString(),
            'request_url' => $request->fullUrl(),
            'request_method' => $request->method(),
            'request_payload' => json_encode($request->all()),
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_resolved' => false,
            'occurrence_count' => 1,
            'first_seen_at' => now(),
            'last_seen_at' => now()
        ]);
        
        // Send notification for critical errors
        if ($this->getSeverity($exception) === 'critical') {
            event(new CriticalErrorOccurred($exception));
        }
    }
    
    protected function getErrorType(Throwable $exception): string
    {
        return match(true) {
            $exception instanceof \ErrorException => 'exception',
            $exception instanceof \ParseError => 'fatal',
            default => 'exception'
        };
    }
    
    protected function getSeverity(Throwable $exception): string
    {
        return match(true) {
            $exception instanceof \ErrorException && $exception->getSeverity() & E_ERROR => 'critical',
            $exception instanceof \ErrorException && $exception->getSeverity() & E_WARNING => 'high',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException => 'medium',
            default => 'low'
        };
    }
    
    protected function detectModule(Throwable $exception): string
    {
        $trace = $exception->getTrace();
        
        if (empty($trace)) return 'unknown';
        
        $file = $trace[0]['file'] ?? '';
        
        return match(true) {
            str_contains($file, 'Controllers/Api/Booking') => 'booking',
            str_contains($file, 'Controllers/Api/Competition') => 'competition',
            str_contains($file, 'Controllers/Api/Payment') => 'payment',
            str_contains($file, 'Controllers/Api/Auth') => 'auth',
            str_contains($file, 'Controllers/Api/Event') => 'event',
            default => 'general'
        };
    }
}
```

**API Endpoints:**
```php
// Admin Error Logs
GET    /api/v1/admin/errors                          // List errors
GET    /api/v1/admin/errors/{id}                     // Error detail
PATCH  /api/v1/admin/errors/{id}/resolve             // Mark resolved
DELETE /api/v1/admin/errors/{id}                     // Delete error

// Admin Link Checker
POST   /api/v1/admin/health/scan-links               // Trigger link check
GET    /api/v1/admin/health/link-report              // Get report

// Admin Health Monitor
GET    /api/v1/admin/health/dashboard                // Health dashboard
GET    /api/v1/admin/health/logs                     // Health check logs
```

**Link Checker Service:**
```php
namespace App\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

class AdminLinkCheckerService
{
    public function scanAllRoutes(): array
    {
        $routes = Route::getRoutes();
        $report = [];
        
        foreach ($routes as $route) {
            // Only check GET routes in admin namespace
            if (!str_starts_with($route->getName(), 'admin.')) continue;
            if ($route->methods()[0] !== 'GET') continue;
            
            $path = $route->uri();
            
            // Skip routes with parameters
            if (str_contains($path, '{')) continue;
            
            $url = url($path);
            
            try {
                $start = microtime(true);
                $response = Http::timeout(10)->get($url);
                $responseTime = (microtime(true) - $start) * 1000;
                
                $status = $response->status();
                $isBroken = $status >= 400;
                
                AdminLinkChecker::updateOrCreate(
                    ['route_path' => $path],
                    [
                        'route_name' => $route->getName(),
                        'http_method' => 'GET',
                        'expected_status' => 200,
                        'actual_status' => $status,
                        'response_time_ms' => $responseTime,
                        'error_message' => $isBroken ? $response->body() : null,
                        'last_checked_at' => now(),
                        'is_broken' => $isBroken,
                        'broken_since' => $isBroken ? now() : null
                    ]
                );
                
                $report[] = [
                    'path' => $path,
                    'status' => $status,
                    'response_time' => $responseTime,
                    'is_broken' => $isBroken
                ];
                
            } catch (\Exception $e) {
                AdminLinkChecker::updateOrCreate(
                    ['route_path' => $path],
                    [
                        'route_name' => $route->getName(),
                        'http_method' => 'GET',
                        'expected_status' => 200,
                        'actual_status' => 0,
                        'response_time_ms' => 0,
                        'error_message' => $e->getMessage(),
                        'last_checked_at' => now(),
                        'is_broken' => true,
                        'broken_since' => now()
                    ]
                );
                
                $report[] = [
                    'path' => $path,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                    'is_broken' => true
                ];
            }
        }
        
        return $report;
    }
}
```

**Health Monitor Service:**
```php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemHealthMonitorService
{
    public function checkHealth(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
            'api' => $this->checkAPI()
        ];
    }
    
    protected function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $responseTime = (microtime(true) - $start) * 1000;
            
            return [
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'details' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'error' => $e->getMessage()
            ];
        }
    }
    
    protected function checkCache(): array
    {
        try {
            $start = microtime(true);
            Cache::put('health_check', true, 10);
            $value = Cache::get('health_check');
            $responseTime = (microtime(true) - $start) * 1000;
            
            return [
                'status' => $value ? 'healthy' : 'degraded',
                'response_time_ms' => $responseTime
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'error' => $e->getMessage()
            ];
        }
    }
    
    protected function checkStorage(): array
    {
        try {
            $diskSpace = disk_free_space(storage_path());
            $diskTotal = disk_total_space(storage_path());
            $diskUsagePercent = (($diskTotal - $diskSpace) / $diskTotal) * 100;
            
            $status = match(true) {
                $diskUsagePercent < 80 => 'healthy',
                $diskUsagePercent < 90 => 'degraded',
                default => 'down'
            };
            
            return [
                'status' => $status,
                'disk_usage_percent' => round($diskUsagePercent, 2),
                'free_space_gb' => round($diskSpace / 1024 / 1024 / 1024, 2)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'error' => $e->getMessage()
            ];
        }
    }
    
    protected function checkQueue(): array
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();
            
            return [
                'status' => $failedJobs > 10 ? 'degraded' : 'healthy',
                'failed_jobs' => $failedJobs
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'error' => $e->getMessage()
            ];
        }
    }
    
    protected function checkAPI(): array
    {
        try {
            $start = microtime(true);
            $response = Http::timeout(5)->get(url('/api/v1/health'));
            $responseTime = (microtime(true) - $start) * 1000;
            
            return [
                'status' => $response->successful() ? 'healthy' : 'down',
                'response_time_ms' => $responseTime,
                'status_code' => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'error' => $e->getMessage()
            ];
        }
    }
}
```

**Frontend:**
```
/admin/errors                            // Error logs dashboard
/admin/errors/{id}                       // Error detail + stack trace
/admin/health                            // System health dashboard
/admin/health/link-checker               // Link checker report
```

---

## 🎨 4. DESIGN SYSTEM CONSISTENCY

### Brand Colors (Photographer SB)
```css
:root {
  /* Primary */
  --burgundy: #800020;
  --burgundy-dark: #660019;
  --burgundy-light: #A6002A;
  
  /* Secondary */
  --gold: #D4AF37;
  --gold-light: #E8C547;
  
  /* Neutrals */
  --gray-50: #F9FAFB;
  --gray-100: #F3F4F6;
  --gray-200: #E5E7EB;
  --gray-300: #D1D5DB;
  --gray-400: #9CA3AF;
  --gray-500: #6B7280;
  --gray-600: #4B5563;
  --gray-700: #374151;
  --gray-800: #1F2937;
  --gray-900: #111827;
  
  /* Status */
  --success: #10B981;
  --warning: #F59E0B;
  --error: #EF4444;
  --info: #3B82F6;
}
```

### Design Tokens
```css
/* Buttons */
.btn-primary {
  background: var(--burgundy);
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: var(--burgundy-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
}

/* Badges */
.badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.badge-success { background: #D1FAE5; color: #065F46; }
.badge-warning { background: #FEF3C7; color: #92400E; }
.badge-error { background: #FEE2E2; color: #991B1B; }
.badge-info { background: #DBEAFE; color: #1E40AF; }

/* Cards */
.card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  border: 1px solid var(--gray-200);
}

.card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Tables */
.table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 8px;
  overflow: hidden;
}

.table thead {
  background: var(--gray-50);
}

.table th {
  padding: 12px 16px;
  font-weight: 600;
  text-align: left;
  color: var(--gray-700);
  border-bottom: 2px solid var(--gray-200);
}

.table td {
  padding: 12px 16px;
  border-bottom: 1px solid var(--gray-100);
}

.table tr:hover {
  background: var(--gray-50);
}

/* Forms */
.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid var(--gray-300);
  border-radius: 6px;
  font-size: 14px;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: var(--burgundy);
  box-shadow: 0 0 0 3px rgba(128, 0, 32, 0.1);
}

/* Empty States */
.empty-state {
  padding: 60px 20px;
  text-align: center;
  color: var(--gray-500);
}

.empty-state-icon {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.5;
}

/* Skeleton Loaders */
.skeleton {
  background: linear-gradient(
    90deg,
    var(--gray-200) 25%,
    var(--gray-300) 50%,
    var(--gray-200) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s infinite;
}

@keyframes skeleton-loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
```

### Mobile-First Breakpoints
```css
/* Mobile First */
@media (min-width: 640px) { /* sm */ }
@media (min-width: 768px) { /* md */ }
@media (min-width: 1024px) { /* lg */ }
@media (min-width: 1280px) { /* xl */ }
@media (min-width: 1536px) { /* 2xl */ }
```

---

## 📋 5. DEVELOPER TODO LIST (Copyable)

### Phase 1: Database & Models (Week 1)

```
[ ] Create certificate_templates migration
[ ] Create certificates migration
[ ] Create certificate_issues_log migration
[ ] Create competition_share_frames migration
[ ] Create generated_share_images migration
[ ] Enhance judges table (add portfolio_url, specialization, etc)
[ ] Create judge_availability migration
[ ] Create booking_messages migration
[ ] Create booking_status_logs migration
[ ] Enhance inquiries table (add event_type_detail, guest_count, etc)
[ ] Create user_verifications migration
[ ] Create verification_requests migration
[ ] Enhance events table (add event_type, ticket_price, venue fields)
[ ] Create event_registrations migration
[ ] Create event_attendance migration
[ ] Create seo_metadata migration (polymorphic)
[ ] Create tracking_scripts migration
[ ] Create system_errors migration
[ ] Create admin_link_checker migration
[ ] Create system_health_logs migration

[ ] Create all corresponding Eloquent models
[ ] Define model relationships
[ ] Add model observers for auto-triggers
[ ] Create model factories for testing
[ ] Create database seeders
```

### Phase 2: Services & Business Logic (Week 2)

```
[ ] CertificateGeneratorService (generate, bulkGenerate, sendEmail)
[ ] ShareFrameGeneratorService (generate, getOrientationDimensions, generateShortUrl)
[ ] EventRegistrationService (register, generateQRCode)
[ ] EventAttendanceService (scanQRCode, exportAttendance)
[ ] BookingMessagingService (sendMessage, markAsRead)
[ ] VerificationService (submitRequest, approveRequest, rejectRequest)
[ ] SEOGeneratorService (generatePhotographerSEO, generateSitemap)
[ ] TrackingService (getActiveScripts, renderScripts)
[ ] AdminLinkCheckerService (scanAllRoutes)
[ ] SystemHealthMonitorService (checkHealth, checkDatabase, checkCache, etc)
[ ] CompetitionResultService (calculateWinners, publishResults)
[ ] JudgeScoreValidator (validate scoring rules)
```

### Phase 3: API Controllers (Week 3)

```
[ ] CertificateTemplateController (CRUD for templates)
[ ] CertificateController (generate, verify, download, email)
[ ] ShareFrameController (generate, download, list)
[ ] BookingMessageController (list, send, markAsRead)
[ ] VerificationController (request, status, upload)
[ ] AdminVerificationController (list, approve, reject)
[ ] EventRegistrationController (register, myEvents, getTicket)
[ ] EventAttendanceController (scanQR, exportCSV, manualMark)
[ ] JudgeDashboardController (dashboard, myCompetitions, submissions, submitScore)
[ ] AdminJudgeController (list, create, assign, removeFromCompetition)
[ ] SitemapController (index, photographers, categories, cities)
[ ] TrackingScriptController (CRUD)
[ ] SystemErrorController (list, resolve, delete)
[ ] AdminHealthController (dashboard, linkReport, scanLinks)
```

### Phase 4: Frontend Views (Week 4)

```
[ ] /admin/certificates/templates (List + CRUD)
[ ] /admin/certificates/templates/create (Visual editor)
[ ] /admin/certificates (Issued certificates list)
[ ] /admin/certificates/issue (Manual issue form)
[ ] /admin/events/{id}/certificates (Event certificates)
[ ] /certificate/verify/{code} (Public verification)
[ ] /my-certificates (User certificates)

[ ] /competitions/{id}/submissions/{submissionId}/share (Share frame generator)
[ ] /admin/competitions/{id}/share-frames (Frame templates)

[ ] /client/bookings/new (Inquiry form)
[ ] /client/bookings (My bookings list)
[ ] /client/bookings/{id} (Detail + messages)
[ ] /photographer/bookings (Photographer dashboard)
[ ] /photographer/bookings/{id}/messages (Message thread)
[ ] /admin/bookings (Admin list + filters)

[ ] /photographer/verification/request (Verification form)
[ ] /photographer/verification/status (Status tracker)
[ ] /admin/verifications (Pending queue)
[ ] /admin/verifications/{id} (Review interface)

[ ] /events (Public list)
[ ] /events/{id} (Detail + register)
[ ] /my-events (User registrations)
[ ] /my-events/{id}/ticket (Digital ticket with QR)
[ ] /admin/events/create (Enhanced form with venue, pricing)
[ ] /admin/events/{id}/registrations (Registrations list)
[ ] /admin/events/{id}/attendance (QR scanner)
[ ] /admin/events/{id}/attendance/manual (Manual marking)

[ ] /judge/dashboard (My stats)
[ ] /judge/competitions (Assigned competitions)
[ ] /judge/competitions/{id}/submissions (Submissions grid)
[ ] /judge/submissions/{id}/score (Scoring interface)
[ ] /admin/judges (Judge management)
[ ] /admin/judges/create (Add judge)
[ ] /admin/competitions/{id}/judges (Assign judges)
[ ] /admin/competitions/{id}/judge-progress (Progress tracker)

[ ] /admin/settings/tracking (Tracking scripts)
[ ] /admin/errors (Error logs)
[ ] /admin/errors/{id} (Error detail)
[ ] /admin/health (Health dashboard)
[ ] /admin/health/link-checker (Link report)
```

### Phase 5: Testing & QA (Week 5)

```
[ ] Write unit tests for all services
[ ] Write feature tests for API endpoints
[ ] Write browser tests for critical user flows
[ ] Test mobile responsiveness on real devices
[ ] Test QR scanner on mobile
[ ] Test certificate generation with different templates
[ ] Test judge scoring workflow
[ ] Test booking messaging real-time updates
[ ] Test event registration + attendance flow
[ ] Test verification approval workflow
[ ] Test SEO meta tags rendering
[ ] Test tracking scripts loading with consent
[ ] Test error logging and admin notification
[ ] Load test competition voting with 1000 concurrent users
[ ] Security audit (SQL injection, XSS, CSRF)
```

### Phase 6: Documentation & Deployment (Week 6)

```
[ ] API documentation (Swagger/Postman)
[ ] User guides for photographers
[ ] User guides for judges
[ ] Admin manual
[ ] Database backup strategy
[ ] CDN setup for images
[ ] Redis cache configuration
[ ] Queue worker setup
[ ] Cron job configuration
[ ] SSL certificate installation
[ ] Domain DNS configuration
[ ] Environment variables configuration
[ ] Deploy to staging
[ ] UAT with real users
[ ] Deploy to production
```

---

## ✅ 6. TESTING CHECKLIST

### A) Certificate System
```
[ ] Admin can create certificate template with custom fields
[ ] Template editor preview renders correctly
[ ] Auto-generate certificates when event attendance marked
[ ] Bulk generate certificates for workshop participants
[ ] Certificate PDF downloads successfully
[ ] Certificate QR code is scannable
[ ] Public verification page shows correct details
[ ] Invalid certificate code shows error message
[ ] Certificate email delivery works
[ ] Revoked certificate shows revoked status
[ ] Certificate download count increments
[ ] DD-MM-YYYY date format in certificates
```

### B) Share Frame Generator
```
[ ] Frame generator works for all orientations (portrait, square, story, landscape)
[ ] Generated frame includes competition name, photographer name, vote CTA
[ ] QR code in frame is scannable and redirects to submission page
[ ] Short URL generation works
[ ] Generated image downloadable
[ ] WhatsApp share button works
[ ] Facebook share button works
[ ] Watermark appears correctly
[ ] Frame respects brand colors
[ ] Mobile UI is responsive
```

### C) Judge Dashboard
```
[ ] Judge can only see assigned competitions
[ ] Judge cannot score own submission
[ ] Scoring deadline enforcement works
[ ] All 5 criteria are required (composition, lighting, storytelling, creativity, technical)
[ ] Total score calculates correctly (0-50)
[ ] Judge can edit submitted score before deadline
[ ] Judge progress percentage shows correctly on admin
[ ] Winner calculation uses correct weights (judge + public vote)
[ ] Tie-break rules work
[ ] Results publish notification sent to winners
```

### D) Booking Marketplace
```
[ ] Client can submit inquiry with all required fields
[ ] Photographer receives notification of new inquiry
[ ] Message thread shows all messages chronologically
[ ] File attachments upload and display correctly
[ ] Real-time message updates work (Pusher/Echo)
[ ] Photographer can send custom quote
[ ] Client can accept/decline quote
[ ] Booking status transitions correctly
[ ] Status change logs recorded
[ ] Email notifications sent at each status change
```

### E) Photographer Verification
```
[ ] Verification request form collects all documents
[ ] Admin sees pending verifications in queue
[ ] Admin can approve with badge assignment
[ ] Admin can reject with reason
[ ] Photographer receives notification of decision
[ ] Verification badge shows on profile
[ ] Different badge levels (email, phone, NID, business)
[ ] Expired verifications trigger renewal request
```

### F) Event Management
```
[ ] Free events allow registration without payment
[ ] Paid events show ticket price (৳)
[ ] Early bird pricing applies before deadline
[ ] Registration code is unique per attendee
[ ] QR code generates correctly
[ ] QR scanner marks attendance successfully
[ ] Duplicate check-in blocked with error message
[ ] Attendance CSV export has correct columns
[ ] Venue name and address show correctly
[ ] Event capacity enforcement works
[ ] Sold out events show "Sold Out" badge
```

### G) SEO & Tracking
```
[ ] Photographer profile has correct meta title/description
[ ] OG image shows profile photo or portfolio image
[ [ ] Schema.org markup validates on Google Rich Results Test
[ ] Canonical URL is correct
[ ] Sitemap.xml generates with all photographers
[ ] robots.txt blocks admin routes
[ ] Google Analytics tracking works
[ ] Facebook Pixel fires on page view
[ ] Cookie consent banner appears on first visit
[ ] Consent preferences saved to localStorage
[ ] Tracking scripts don't load until consent given
```

### H) Admin Health Tools
```
[ ] Error logs capture exceptions automatically
[ ] Critical errors send email notification to admin
[ ] Error detail shows full stack trace
[ ] Mark as resolved updates status
[ ] Duplicate errors increment occurrence_count
[ ] Link checker scans all admin GET routes
[ ] Broken links highlighted in report
[ ] Health dashboard shows database status
[ ] Cache status check works
[ ] Storage disk usage shows percentage
[ ] Failed jobs count shows in queue health
```

---

## 🔒 7. SECURITY CHECKLIST

```
[ ] All user inputs sanitized and validated
[ ] SQL injection protection (use Eloquent, not raw queries)
[ ] XSS protection (escape all output)
[ ] CSRF tokens on all forms
[ ] Rate limiting on API endpoints (60 requests/minute)
[ ] Authentication required for all sensitive endpoints
[ ] Authorization checks on all actions (policies)
[ ] File upload validation (size, type, mime)
[ ] Certificate verification uses unique codes (not guessable IDs)
[ ] Judge scoring validates assignment and deadline
[ ] QR codes signed to prevent forgery
[ ] Admin routes protected with role middleware
[ ] Sensitive data encrypted in database
[ ] Password hashing with bcrypt
[ ] Two-factor authentication for admin accounts
[ ] Session timeout after 30 minutes inactivity
[ ] HTTPS enforced on production
[ ] Security headers (X-Frame-Options, X-Content-Type-Options)
[ ] Dependencies updated regularly
[ ] Environment variables not in version control
```

---

## ⚡ 8. PERFORMANCE CHECKLIST

```
[ ] Database indexes on foreign keys
[ ] Query optimization (N+1 problem fixed with eager loading)
[ ] Redis cache for dashboard stats (5-minute TTL)
[ ] Image optimization (WebP format, max 1MB)
[ ] Certificate PDFs stored in CDN
[ ] Lazy loading for images
[ ] Pagination on all list pages (20 items per page)
[ ] API response caching where appropriate
[ ] Queue long-running tasks (certificate generation, email sending)
[ ] Background job for link checker (runs weekly)
[ ] Database query monitoring (Laravel Telescope)
[ ] Page load time < 3 seconds on mobile
[ ] Core Web Vitals pass (LCP < 2.5s, FID < 100ms, CLS < 0.1)
[ ] CDN for static assets
[ ] Gzip compression enabled
[ ] Minified CSS and JS in production
```

---

## 📊 9. PRIORITY SUMMARY

### P0 - Launch Blockers (Must Complete Before Launch)
- **Booking Marketplace** (primary revenue)
- **Photographer Verification** (trust & safety)
- **SEO Core** (organic traffic)

### P1 - Post-Launch (Complete Within 1 Month)
- **Certificate System** (event value-add)
- **Judge Dashboard** (competition integrity)
- **Competition Share Frames** (viral growth)
- **Event Management Premium** (monetization)

### P2 - Premium Features (3-6 Months)
- **Tracking & Analytics** (optimization)
- **Admin Health Tools** (maintenance)
- **Advanced Reporting** (business intelligence)

---

## 🎯 10. SUCCESS METRICS

### Technical KPIs
- Page load time < 3 seconds (mobile)
- API response time < 200ms (95th percentile)
- Database query time < 50ms (average)
- Zero critical security vulnerabilities
- 99.9% uptime
- Error rate < 0.1%

### Business KPIs
- 50+ verified photographers in first month
- 100+ bookings in first month
- 10+ events with paid registrations
- 5+ active competitions with 100+ submissions each
- Organic traffic 1000+ sessions/month (SEO)
- 20% conversion rate (visitor → inquiry)

### UX KPIs
- Mobile traffic > 70%
- Bounce rate < 40%
- Average session duration > 3 minutes
- Return visitor rate > 30%

---

## 🚀 11. DEPLOYMENT TIMELINE

**Week 1-2:** Database & models, core services  
**Week 3-4:** API controllers & admin backend  
**Week 5-6:** Frontend UI (mobile-first)  
**Week 7:** Testing & QA  
**Week 8:** Staging deployment & UAT  
**Week 9:** Production deployment + monitoring  

**Total:** 9 weeks (2 months) for full premium upgrade

---

This implementation plan is **production-ready, developer-friendly, and fully consistent** with the Photographer SB brand. Every module has clear database schema, API endpoints, service logic, and mobile-first frontend specifications.

**No shortcuts. All features fully integrated. Bangladesh-ready with ৳ currency and DD-MM-YYYY dates.**

Ready to start implementation! 🚀
