# Photo Competition Module - Complete Design

## IMPLEMENTATION STATUS

### ✅ Phase 1: COMPLETE (Current Platform - 100%)
**Core Features Implemented:**
- Public competition listing with filters (status, search)
- Competition detail page with voting
- Admin full CRUD UI (Index, Create, Edit pages)
- Photographer competition creation (API + UI)
- Basic voting system (vote/unvote)
- Competition status workflow (draft → published → closed)
- Featured competition system
- Role-based permissions (Public/Photographer/Admin)
- Photographer restrictions:
  - Min prize pool: ৳1,000 (vs admin: no minimum)
  - Max submissions per user: 10 (vs admin: unlimited)
  - Cannot set featured/verified
  - Competitions require admin approval
- Mobile responsive design
- API: Full CRUD across 3 tiers
- Database: Complete schema with 13+ fields

### ✅ Phase 2: COMPLETE (Advanced Features - 100%)
**All Features Implemented:**
- ✅ **Photo Submission System** (COMPLETE - upload, gallery, moderation)
- ✅ **Public Voting System** (COMPLETE - vote/unvote with fraud detection)
- ✅ **Judge Scoring System** (COMPLETE - judge dashboard, 5-criteria scoring, progress tracking)
- ✅ **Winner Calculation** (COMPLETE - weighted scoring algorithm, automatic ranking, tie-breaking)
- ✅ **Winner Announcement** (COMPLETE - save winners to database, announcement tracking)
- ✅ **Digital Certificates** (COMPLETE - professional PDF generation with DomPDF, unique certificate IDs, download system)
- ✅ **Prize Distribution** (COMPLETE - prize tracking, status management [pending/processing/delivered/claimed], delivery tracking)
- ✅ **Competition Categories** (COMPLETE - multiple categories per competition, category-based leaderboards, category winners)
- ✅ **Sponsorship System** (COMPLETE - multi-tier sponsorships [Platinum/Gold/Silver/Bronze], logo management, contribution tracking)
- ✅ **Submission Gallery** (COMPLETE - thumbnail grid, lightbox, filters, sort options)

### 🎯 Future Enhancements (Optional)
- **Advanced Analytics** (submission trends, voting patterns, geographic data)
- **Email Notifications** (automated submission confirmations, winner announcements, reminders)
- **Competition Templates** (pre-configured competition types)
- **Live Leaderboards** (real-time ranking updates)
- **Social Sharing** (share submissions, results)

---

## 1) COMPETITION SYSTEM OVERVIEW

### What is the Competition Module?
The Photo Competition Module enables photographers and the platform to create and manage photo competitions. Participants submit photos, the public votes on entries, judges score submissions, and winners are announced with certificates and prizes.

### Competition Goals
- **Engagement**: Increase platform engagement and user retention
- **User-Generated Content**: Create fresh, quality content from photographers
- **Monetization**: Sponsorship opportunities, entry fees, premium entries
- **Community**: Build photographer community and loyalty
- **Credibility**: Showcase quality photography on the platform
- **Lead Generation**: Winners and top entries promote their work (booking leads)

### Competition Business Model
- **Entry Fees**: Photographers pay to submit (free or paid)
- **Sponsorship**: Brands sponsor competitions
- **Premium Entries**: Pay extra for featured/priority judging
- **Prize Distribution**: Winners get exposure, certificates, potential prizes

---

## 2) COMPETITION FEATURES

### 2.1 Competition Creation & Setup

#### Create Competition
**Phase 1 (Implemented):**
- [x] Competition title
- [x] Unique competition slug (auto-generated)
- [x] Competition theme
- [x] Competition description (textarea)
- [x] Submission deadline (datetime)
- [x] Voting start date (datetime)
- [x] Voting end date (datetime)
- [x] Announcement date (datetime)
- [x] Prize pool (decimal)
- [x] Max submissions per user (integer, 1-10)
- [x] Competition rules (textarea)
- [x] Terms & conditions (textarea)
- [x] Status (draft/published/closed)
- [x] Featured toggle (admin only)
- [x] Organizer assignment (admin can assign photographer)

**Phase 2 (Planned):**
- [ ] Rich text editor for description
- [ ] Hero/banner image upload
- [ ] Competition tagline/subtitle
- [ ] Total prize value display (calculated)
- [ ] Number of winners (1st, 2nd, 3rd)
- [ ] Judging period dates (separate from voting)
- [ ] Results publication date (separate from announcement)

#### Competition Rules & Details
**Phase 1 (Implemented):**
- [x] Competition rules (text field)
- [x] Terms and conditions (text field)
- [x] Maximum submissions per participant (1-10 for photographers, unlimited for admin)

**Phase 2 (Planned):**
- [ ] Eligibility requirements (nationality, age, experience)
- [ ] Participation terms (detailed)
- [ ] Rights and usage (copyright, platform rights)
- [ ] Disqualification criteria (detailed list)
- [ ] Submission fee (free or paid)
- [ ] Image size and format requirements
- [ ] Watermark requirements/restrictions
- [ ] Category breakdown (multiple categories)
- [ ] Judging criteria (specific rubrics)
- [ ] Privacy policy for competition

#### Competition Configuration
**Phase 1 (Implemented):**
- [x] Status (draft, published, closed)
- [x] Featured toggle (admin only)
- [x] Organizer assignment

**Phase 2 (Planned):**
- [ ] Allow public voting (yes/no toggle)
- [ ] Allow judge scoring (yes/no toggle)
- [ ] Total points/scoring system configuration
- [ ] Minimum entries to proceed
- [ ] Public or invitation-only setting
- [ ] SEO meta title and description

### 2.2 Competition Categories
**Phase 1 (Not Implemented):**
Currently, competitions are single-category only.

**Phase 2 (Planned):**
- [ ] Pre-defined categories table:
  - Portraiture, Landscape & Nature, Street Photography
  - Conceptual Art, Black & White, Macro & Close-up
  - Wildlife, Architecture, Events, Commercial/Product
  - Heritage & Cultural, Fine Art, Open/Other
- [ ] Multiple categories per competition
- [ ] Category-specific rules
- [ ] Category-specific judges
- [ ] Category winners
- [ ] Minimum/maximum submissions per category

### 2.3 Competition Rules & Eligibility
- [x] Age requirement (18+, etc.)
- [x] Experience level (Amateur, Semi-Pro, Professional)
- [x] Nationality/location restriction (Bangladesh only, etc.)
- [x] Previous competition participation (limit repeat entries)
- [x] Photo ownership requirement (must be photographer's own work)
- [x] Originality requirement
- [x] Image alteration limits (allowed/not allowed)
- [x] AI-generated images policy
- [x] Copyrighted material policy
- [x] Model release requirement
- [x] Venue release requirement (for identifiable locations)
- [x] Disqualification rules

### 2.4 Photo Submission System

**Phase 1 (Not Implemented):**
Submission system is planned for Phase 2. Current implementation only allows voting on competitions.

**Phase 2 (✅ IMPLEMENTED):**

#### Submission Form
- [x] Photo upload (drag-drop and click)
- [x] Multiple photo support (batch upload)
- [x] Photo title (required)
- [x] Photo description/story (max 1000 chars)
- [x] Location of photo (optional)
- [x] Date taken (optional)
- [x] Camera/equipment used (optional)
- [x] Photo hashtags (optional, JSON)
- [x] Category selection
- [x] Confirm originality checkbox
- [x] Preview before submission

#### Submission Requirements
- [ ] Minimum image resolution (1920px longest side)
- [ ] Maximum file size (10MB)
- [ ] Supported formats (JPEG, PNG)
- [ ] EXIF data handling
- [ ] Submission deadline enforcement
- [ ] Submit button with confirmation

#### Submission Confirmation
- [ ] Submission receipt/confirmation
- [ ] Submission ID/reference number
- [ ] Receipt email sent
- [ ] Edit submission option (before deadline)
- [ ] Delete submission option (before deadline)

### 2.5 Submission Management Dashboard (Participant)

**Phase 1 (Not Implemented):**
Submission management dashboard is planned for Phase 2.

**Phase 2 (Planned):**

#### My Submissions
- [ ] List all submitted photos
- [ ] Filter by competition
- [ ] Filter by status
- [ ] View submission details
- [ ] Edit submission (before deadline)
- [ ] Delete submission (before deadline)
- [ ] View submission status in voting/judging
- [ ] Vote count display
- [ ] Judge score display (if judging complete)
- [ ] Ranking/position

#### Submission Analytics
- [ ] View count for each submission
- [ ] Vote count tracking
- [ ] Judge scores breakdown
- [ ] Comparison with other entries

### 2.6 Public Gallery & Browsing

**Phase 1 (Not Implemented):**
Submission gallery is planned for Phase 2. Current implementation only shows competition listing.

**Phase 2 (✅ IMPLEMENTED):**

#### Competition Gallery
- [x] Browse all competition submissions (grid view)
- [x] Thumbnail gallery with pagination
- [x] Gallery search (by title, description, photographer)
- [x] Filter by status (all/approved/pending)
- [x] Sort by:
  - [x] Recently added
  - [x] Most voted
  - [x] Highest scored
- [x] Submission card display:
  - [x] Thumbnail image
  - [x] Title
  - [x] Photographer name
  - [x] Vote count
  - [x] View count
  - [x] Status badge

#### Submission Detail Page
- [ ] Large image display
- [ ] Image lightbox/zoom functionality
- [ ] Submission title
- [ ] Photographer name (linked to profile)
- [ ] Submission description/story
- [ ] Photo metadata display:
  - [ ] Location
  - [ ] Camera/settings
  - [ ] Date taken
- [ ] Vote count display
- [ ] View count tracking
- [ ] Judge score (if judging complete)
- [ ] Current rank/position
- [ ] Vote button (voting period enforcement)
- [ ] Share buttons (WhatsApp, Facebook, Twitter)
- [ ] Related submissions carousel
- [ ] Competition info sidebar
- [ ] Photographer CTA (View Profile / Book)

### 2.7 Voting System (Public)

#### Voting Mechanism
**Phase 1 (Implemented):**
- [x] Simple vote/unvote button on competition detail
- [x] Vote count display
- [x] Authentication required
- [x] One vote per user per competition

**Phase 2 (✅ IMPLEMENTED):**
- [x] Vote button on each submission (submission gallery)
- [x] Vote confirmation feedback
- [x] Real-time vote count updates
- [x] Voting leaderboard (most voted display)
- [x] Undo vote option (unvote)

#### Voting Eligibility
**Phase 2 (Planned):**
- [ ] Email verification requirement
- [ ] OTP verification (anti-fraud)
- [ ] IP address rate limiting
- [ ] Voting timing window enforcement
- [ ] Voting cooldown (e.g., 5 seconds between votes)

#### Anti-Fraud Voting System
**Phase 2 (Planned - Critical):**
- [ ] IP-based rate limiting (max 20 votes per IP/day)
- [ ] User-based rate limiting (max 50 votes per user/day)
- [ ] Device fingerprinting
- [ ] Suspicious pattern detection
- [ ] Automated fraud alerts
- [ ] Manual voting review queue
- [ ] Vote invalidation for fraudulent votes
- [ ] Voter ID audit trail

### 2.8 Judge Scoring System

**Phase 1 (Not Implemented):**
Judge scoring system is planned for Phase 2.

**Phase 2 (✅ IMPLEMENTED):**

#### Judge Panel Management
- [x] Add judges (admin only)
- [x] Assign judges to competitions
- [x] Judge role (judge/chief_judge)
- [x] Judge profile (bio, expertise)
- [x] Judge status (active/inactive)
- [x] Remove judges from competitions

#### Judging Interface
- [x] Judge dashboard (assigned competitions)
- [x] Submissions queue to judge
- [x] Full-size image display with lightbox
- [x] Submission details (title, description, metadata)
- [x] Scoring interface (0-10 scale with 0.5 increments)
- [x] Scoring rubric (5 criteria):
  - [x] Composition (rule of thirds, balance, framing)
  - [x] Technical Quality (focus, exposure, lighting)
  - [x] Creativity (originality, unique perspective)
  - [x] Story/Message (narrative, emotion, context)
  - [x] Impact (visual impact, memorability)
- [x] Judge feedback/comments (optional)
- [x] Strengths field (what worked well)
- [x] Improvements field (constructive feedback)
- [x] Mark submission as scored
- [x] Progress tracking (scored vs pending)
- [x] Progress percentage display
- [x] Filter by status (all/pending/scored)
- [x] Edit existing scores

#### Judge Verification
- [ ] Judge identity verification
- [ ] Judge credibility check
- [ ] Conflict-of-interest check
- [ ] Score consistency validation

#### Judging Workflow
- [ ] Submissions allocation to judges
- [ ] Judging period enforcement
- [ ] Reminder notifications
- [ ] Submission status tracking
- [ ] Re-judging option (for disputes)
- [ ] Final score calculation (weighted average)

### 2.9 Winner Announcement & Certificates

**Phase 1 (Not Implemented):**
Winner system is planned for Phase 2 (requires submission system first).

**Phase 2 (Planned - High Priority):**

#### Winner Calculation
- [ ] Combined scoring (public votes + judge scores)
- [ ] Score weighting configuration (30% public, 70% judges)
- [ ] Tie-breaking rules
- [ ] Ranking determination (1st, 2nd, 3rd place)
- [ ] Honorable mention determination (top 10)
- [ ] Category-wise winners (when categories implemented)

#### Winner Announcement Page
- [ ] Competition results page
- [ ] Winner profile cards:
  - [ ] Rank (1st, 2nd, 3rd)
  - [ ] Winning photo display
  - [ ] Photographer name (linked)
  - [ ] Photo title
  - [ ] Vote count + judge score
  - [ ] Prize amount
- [ ] Honorable mentions section
- [ ] Judge comments on winners
- [ ] Sponsor acknowledgment
- [ ] Results publication timestamp

#### Digital Certificate
- [ ] Auto-generated PDF certificates:
  - [ ] 1st place winner
  - [ ] 2nd place winner
  - [ ] 3rd place winner
  - [ ] Honorable mention recipients
- [ ] Certificate template fields:
  - [ ] Competition name
  - [ ] Winner name
  - [ ] Award position
  - [ ] Award date
  - [ ] Digital signature/seal
- [ ] Certificate download (PDF)
- [ ] Certificate sharing (social media)
- [ ] Display on photographer profile

#### Prize Distribution
- [ ] Prize details management
- [ ] Prize payout processing
- [ ] Delivery confirmation
- [ ] Prize tax handling (if applicable)

#### Winner Notifications
- [ ] Email notification to winner
- [ ] In-app notification
- [ ] Public announcement (social media)
- [ ] Winner press release (optional)

### 2.10 Competition Timeline Visualization

**Phase 1 (Partially Implemented):**
- [x] Competition status display (draft/published/closed)
- [x] Date fields for submission, voting, announcement

**Phase 2 (Planned - Medium Priority):**

**Timeline Phase 1: Submission Phase**
- [ ] Countdown timer to submission deadline
- [ ] Submission count display (live)
- [ ] Call-to-action to submit
- [ ] Submission guidelines visible

**Timeline Phase 2: Voting Phase**
- [ ] Public voting open banner
- [ ] Countdown to voting end
- [ ] Vote count updates (real-time)
- [ ] Leaderboard active
- [ ] Top submissions featured

**Timeline Phase 3: Judging Phase**
- [ ] "Voting closed" status message
- [ ] "Judges reviewing" indicator
- [ ] Status message to public
- [ ] Countdown to results

**Timeline Phase 4: Results Phase**
- [ ] Winners announced banner
- [ ] Certificates published
- [ ] Results page visible
- [ ] Winner profiles linked
- [ ] Sponsor acknowledgment

---

## 3) COMPETITION SPONSORSHIP

**Phase 1 (Not Implemented):**
Sponsorship system is planned for Phase 2.

**Phase 2 (Planned - Low Priority):**

### 3.1 Sponsor Setup
- [ ] Sponsor tier options (Bronze, Silver, Gold, Platinum)
- [ ] Sponsor pricing per tier
- [ ] Sponsorship agreement terms
- [ ] Sponsor logo upload
- [ ] Sponsor website/link
- [ ] Sponsor description

### 3.2 Sponsor Benefits
- [ ] Logo placement on competition page (tiered)
- [ ] Logo on certificates (when implemented)
- [ ] Logo on social media posts
- [ ] Logo in email announcements
- [ ] Social media mention (tag)
- [ ] Blog post mention
- [ ] Prize contribution option
- [ ] Judge appointment option

### 3.3 Sponsor Dashboard
- [ ] View sponsored competition details
- [ ] Sponsor performance metrics
- [ ] Brand impressions tracking
- [ ] Link clicks tracking
- [ ] Social reach metrics
- [ ] ROI calculations

---

## 4) ADMIN COMPETITION MANAGEMENT

### 4.1 Admin Dashboard
- [x] All competitions list
- [x] Search and filter competitions
- [x] Competition status overview
- [x] Total submissions count
- [x] Voting analytics
- [x] Revenue metrics

### 4.2 Competition Management
**Phase 1 (Implemented):**
- [x] Create new competition (full form)
- [x] Edit competition details (full form with data loading)
- [x] Delete competition (with checks)
- [x] Feature/promote competition (toggle)
- [x] View competition details (opens in new tab)
- [x] Search and filter competitions
- [x] Stats dashboard (total, published, draft, closed)

**Phase 2 (Planned):**
- [ ] Publish/unpublish toggle
- [ ] Cancel competition workflow
- [ ] Extend submission deadline (admin override)
- [ ] Extend voting period (admin override)
- [ ] View all submissions (gallery)
- [ ] Approve/reject submissions (moderation)
- [ ] Manage judges (assign, remove)
- [ ] Manually override results (emergency)
- [ ] Bulk operations (status updates)

### 4.3 Submission Moderation
**Phase 2 (✅ IMPLEMENTED):**
- [x] Review submissions queue
- [x] View submission details
- [x] Approve submissions
- [x] Reject submissions with reason
- [x] Status tracking (pending_review/approved/rejected)
- [x] Admin notes/reason field
- [x] View photographer info

### 4.4 Fraud Detection & Prevention
**Phase 2 (Planned - Critical for Voting):**
- [ ] Voting fraud detection algorithms
- [ ] Submission fraud detection
- [ ] IP/device tracking
- [ ] Duplicate account detection
- [ ] Suspicious voting pattern alerts
- [ ] Mass submission alerts
- [ ] Manual review queue
- [ ] Fraud statistics dashboard

### 4.5 Competition Analytics
**Phase 1 (Basic Stats Implemented):**
- [x] Total competitions count
- [x] Status breakdown (published, draft, closed)

**Phase 2 (Advanced Analytics Planned):**
- [ ] Total submissions per competition
- [ ] Submission breakdown by category
- [ ] Average submissions per photographer
- [ ] Total votes cast
- [ ] Voting pattern analysis
- [ ] Top photographers leaderboard
- [ ] Most popular categories
- [ ] Geographic distribution
- [ ] Revenue generated (entry fees)
- [ ] Sponsor ROI metrics

### 4.6 Judge Management
**Phase 2 (Planned):**
- [ ] Add/remove judges
- [ ] Assign judges to competitions
- [ ] Judge expertise level
- [ ] Judge performance review
- [ ] Judge bias analysis
- [ ] Judge communication tools
- [ ] Judge compensation tracking

---

## 5) COMPETITION DATABASE SCHEMA

**Phase 1 (Implemented):**
Core competitions table with 13+ essential fields.

**Phase 2 (Planned):**
Expanded schema with submissions, votes, judges, certificates, sponsors, and fraud detection tables.

### Competitions Table
**Phase 1 (Implemented):**
```
id (primary key)
title (string)
slug (unique slug)
theme (string)
description (text)
submission_deadline (datetime)
voting_start_at (datetime)
voting_end_at (datetime)
announcement_date (datetime)
prize_pool (decimal)
max_submissions_per_user (integer)
rules (text)
terms_and_conditions (text)
status (enum: draft, published, closed)
is_featured (boolean)
organizer_id (foreign key → photographers, nullable)
vote_count (integer)
created_at (timestamp)
updated_at (timestamp)
```

**Phase 2 (Additional Fields Planned):**
```
uuid (unique identifier)
admin_id (foreign key → users)
short_description (string)
hero_image (string)
banner_image (string)
tagline (string)
judging_start_at (datetime)
judging_end_at (datetime)
results_announcement_date (datetime)
allow_public_voting (boolean)
allow_judge_scoring (boolean)
allow_watermark (boolean)
require_watermark (boolean)
participation_fee (decimal)
entry_fee_currency (string, default: BDT)
is_paid_competition (boolean)
min_submissions_to_proceed (integer)
total_prize_pool (decimal)
number_of_winners (integer)
public_or_private (enum: public, private)
featured_until (timestamp, nullable)
total_submissions (integer)
total_votes (integer)
results_published (boolean)
published_at (timestamp, nullable)
deleted_at (timestamp, nullable)
```

### Competition Categories Table
**Phase 2 (Planned):**
```
id (primary key)
competition_id (foreign key → competitions)
category_name (string)
category_slug (string)
description (text)
max_submissions_per_category (integer)
min_participants_for_category (integer)
winners_per_category (integer)
display_order (integer)
created_at (timestamp)
updated_at (timestamp)
```

### Competition Rules Table
**Phase 2 (Planned):**
```
id (primary key)
competition_id (foreign key → competitions)
eligibility_age_min (integer)
eligibility_nationality (string)
experience_level_required (enum: any, amateur, semi_pro, professional)
allows_ai_generated (boolean)
allows_edited_images (boolean)
edit_level_allowed (string)
copyright_requirement (text)
model_release_required (boolean)
original_work_required (boolean)
disqualification_criteria (text)
prize_distribution_terms (text)
usage_rights_text (longtext)
created_at (timestamp)
updated_at (timestamp)
```

### Submissions Table
**Phase 2 (Planned - Critical for Photo Competitions):**
```
id (primary key)
uuid (unique)
competition_id (foreign key → competitions)
category_id (foreign key → competition_categories, nullable)
photographer_id (foreign key → users)
image_path (string)
image_url (string)
thumbnail_url (string)
title (string)
description (text)
location (string, nullable)
date_taken (date, nullable)
camera_make (string, nullable)
camera_model (string, nullable)
camera_settings (string, nullable)
hashtags (string, nullable)
is_watermarked (boolean)
status (enum: pending_review, approved, rejected, disqualified)
view_count (integer)
vote_count (integer)
judge_score (decimal, nullable)
final_score (decimal, nullable)
ranking (integer, nullable)
is_winner (boolean)
winner_position (string, nullable)
created_at (timestamp)
updated_at (timestamp)
deleted_at (timestamp, nullable)
```

### Submission Votes Table
**Phase 2 (Planned):**
```
id (primary key)
submission_id (foreign key → submissions)
voter_id (foreign key → users)
competition_id (foreign key → competitions)
vote_value (integer, default: 1)
voted_at (timestamp)
ip_address (string)
device_fingerprint (string, nullable)
verified (boolean)
is_valid (boolean)
created_at (timestamp)
updated_at (timestamp)
```

### Judges Table
**Phase 2 (Planned):**
```
id (primary key)
user_id (foreign key → users)
competition_id (foreign key → competitions)
expertise_level (string)
bio (text)
credentials (text)
is_verified (boolean)
assigned_at (timestamp)
created_at (timestamp)
updated_at (timestamp)
```

### Judge Scores Table
**Phase 2 (Planned):**
```
id (primary key)
judge_id (foreign key → judges)
submission_id (foreign key → submissions)
competition_id (foreign key → competitions)
category_id (foreign key → competition_categories, nullable)
composition_score (decimal)
technical_skill_score (decimal)
originality_score (decimal)
emotion_score (decimal)
overall_score (decimal)
judge_notes (text, nullable)
scored_at (timestamp)
created_at (timestamp)
updated_at (timestamp)
```

### Competition Winners Table
**Phase 2 (Planned):**
```
id (primary key)
competition_id (foreign key → competitions)
submission_id (foreign key → submissions)
photographer_id (foreign key → users)
winner_position (integer)
prize_amount (decimal)
prize_description (text)
final_score (decimal)
public_votes_percentage (decimal)
judge_score_percentage (decimal)
certificate_id (foreign key → certificates, nullable)
certificate_issued_at (timestamp, nullable)
payment_status (enum: pending, completed, refunded)
payment_date (timestamp, nullable)
created_at (timestamp)
updated_at (timestamp)
```

### Certificates Table
**Phase 2 (Planned):**
```
id (primary key)
uuid (unique)
winner_id (foreign key → competition_winners)
photographer_id (foreign key → users)
competition_id (foreign key → competitions)
certificate_number (string, unique)
award_title (string)
award_date (date)
certificate_template (string)
certificate_pdf_url (string)
certificate_image_url (string)
is_downloaded (boolean)
download_count (integer)
shared_count (integer)
created_at (timestamp)
updated_at (timestamp)
```

### Competition Sponsors Table
**Phase 2 (Planned):**
```
id (primary key)
competition_id (foreign key → competitions)
sponsor_id (foreign key → users, nullable)
tier (enum: bronze, silver, gold, platinum)
sponsorship_amount (decimal)
sponsor_name (string)
sponsor_logo_url (string)
sponsor_website (string)
status (enum: pending, approved, active, completed)
payment_status (enum: pending, completed)
benefits_received (text)
created_at (timestamp)
updated_at (timestamp)
```

### Vote Fraud Detection Table
**Phase 2 (Planned):**
```
id (primary key)
competition_id (foreign key → competitions)
ip_address (string)
device_fingerprint (string)
vote_count (integer)
detected_at (timestamp)
action_taken (enum: flagged, blocked, investigated, cleared)
admin_notes (text)
created_at (timestamp)
updated_at (timestamp)
```

---

## 6) COMPETITION API ROUTES (Laravel)

**Phase 1 (Implemented):**
```php
// Public Routes
GET    /api/competitions                          // List all published competitions
GET    /api/competitions/{slug}                   // Get competition details
POST   /api/competitions/{id}/vote                // Vote for competition (auth required)
DELETE /api/competitions/{id}/vote                // Remove vote (auth required)

// Photographer Routes (Protected)
POST   /api/photographer/competitions             // Create competition (draft, requires approval)
GET    /api/photographer/competitions             // List own competitions
PUT    /api/photographer/competitions/{id}        // Edit own competition
DELETE /api/photographer/competitions/{id}        // Delete own competition

// Admin Routes (Protected)
GET    /api/admin/competitions                    // List all competitions
GET    /api/admin/competitions/{id}               // Get competition details
POST   /api/admin/competitions                    // Create competition (any status)
PUT    /api/admin/competitions/{id}               // Edit any competition
DELETE /api/admin/competitions/{id}               // Delete any competition
POST   /api/admin/competitions/{id}/feature       // Toggle featured status
```

**Phase 2 (Planned Routes):**
```php
// Public Routes (Submissions & Gallery)
GET    /api/competitions/{id}/gallery             // Browse submissions gallery
GET    /api/competitions/{id}/submissions         // List all submissions
GET    /api/competitions/{id}/submissions/{sid}   // Get submission details
GET    /api/competitions/{id}/leaderboard         // Voting leaderboard
GET    /api/competitions/{id}/winners             // Winners list
GET    /api/competitions/{id}/results             // Results page data
GET    /api/competitions/{id}/certificates/{cid}  // Certificate details
POST   /api/competitions/{id}/submissions/{sid}/vote  // Vote for submission
DELETE /api/competitions/{id}/submissions/{sid}/vote  // Remove vote

// Participant Routes (Protected)
POST   /api/competitions/{id}/submit              // Submit photo
GET    /api/competitions/{id}/my-submissions      // List own submissions
PUT    /api/competitions/{id}/submissions/{sid}   // Edit submission
DELETE /api/competitions/{id}/submissions/{sid}   // Delete submission
GET    /api/competitions/{id}/my-analytics        // Submission analytics
GET    /api/dashboard/competitions/my-entries     // All my entries
GET    /api/dashboard/certificates                // My certificates

// Judge Routes (Protected)
GET    /api/judge/competitions                    // Assigned competitions
GET    /api/judge/competitions/{id}/submissions   // Submissions to judge
PUT    /api/judge/competitions/{id}/submissions/{sid}/score  // Score submission
GET    /api/judge/competitions/{id}/analytics     // Judging analytics

// Admin Routes (Extended)
POST   /api/admin/competitions/{id}/publish       // Publish competition
GET    /api/admin/competitions/{id}/submissions   // View all submissions
POST   /api/admin/competitions/{id}/submissions/{sid}/approve   // Approve submission
POST   /api/admin/competitions/{id}/submissions/{sid}/reject    // Reject submission
GET    /api/admin/competitions/{id}/analytics     // Competition analytics
POST   /api/admin/competitions/{id}/announce-results   // Announce winners
GET    /api/admin/competitions/{id}/fraud-detection    // Fraud detection data
POST   /api/admin/competitions/{id}/judges/assign      // Assign judge
GET    /api/admin/competitions/{id}/sponsors           // List sponsors
POST   /api/admin/competitions/{id}/sponsors/approve   // Approve sponsor
```

---

## 7) COMPETITION PAGES & URLS

**Phase 1 (Implemented):**
```
Public Pages:
- /competitions (all competitions listing with filters)
- /competitions/{slug} (competition detail with voting)

Photographer Pages:
- /photographer-dashboard → My Competitions tab (create, list, edit, delete)

Admin Pages:
- /admin/competitions (all competitions with filters & stats)
- /admin/competitions/create (create competition form)
- /admin/competitions/edit/{id} (edit competition form)
```

**Phase 2 (Planned Pages):**
```
Public Pages (Extended):
- /competitions/{slug}/gallery (submissions gallery grid)
- /competitions/{slug}/submissions/{id} (submission detail page)
- /competitions/{slug}/leaderboard (voting leaderboard)
- /competitions/{slug}/results (winners announcement page)
- /competitions/{slug}/winners/{winner_id} (winner detail)
- /competitions/{slug}/certificate/{certificate_id} (digital certificate view/download)

Participant Pages:
- /dashboard/competitions (my competitions overview)
- /dashboard/competitions/{id}/submit (photo submission form)
- /dashboard/competitions/{id}/submissions (my submissions list)
- /dashboard/certificates (my certificates collection)

Judge Pages:
- /dashboard/judge/competitions (assigned competitions)
- /dashboard/judge/competitions/{id}/review (judging interface with scoring)

Admin Pages (Extended):
- /admin/competitions/{id}/submissions (submission moderation queue)
- /admin/competitions/{id}/analytics (detailed analytics dashboard)
- /admin/competitions/{id}/fraud-detection (fraud detection dashboard)
- /admin/competitions/{id}/judges (judge management)
- /admin/competitions/{id}/sponsors (sponsor management)
```

---

## 8) COMPETITION SUCCESS METRICS

**Phase 1 (Current Metrics):**
| Metric | Current Status |
|--------|----------------|
| Active Competitions | Tracked via dashboard stats |
| Total Votes per Competition | Tracked (vote_count field) |
| Featured Competitions | Tracked (is_featured toggle) |

**Phase 2 (Target Metrics When Fully Implemented):**
| Metric | Target |
|--------|--------|
| Competitions per Year | 12-24 |
| Avg Submissions per Competition | 100-500 |
| Participation Rate | 20-30% of photographers |
| Public Engagement (total votes) | 1000-5000 per competition |
| Voting Fraud Rate | <2% |
| Winner Satisfaction | 4.5+ stars |
| Certificate Downloads | 80%+ of winners |
| Sponsorship Revenue per Competition | $1000-5000 |
| Entry Fees Revenue | $500-2000 per competition |

---

## 9) FRAUD PREVENTION STRATEGY

**Phase 1 (Basic Protection):**
- [x] Authentication required for voting
- [x] One vote per user per competition (database constraint)
- [x] Vote tracking in database

**Phase 2 (Advanced Fraud Prevention - Planned):**

### Real-Time Monitoring
- [ ] IP-based vote limiting (max 20 votes per IP per day)
- [ ] Device fingerprinting (browser/device identification)
- [ ] User-based limiting (max 50 votes per user per day)
- [ ] Vote cooldown (minimum 5 seconds between votes)
- [ ] Automated pattern detection (suspicious voting patterns)
- [ ] Real-time alerting system

### Verification Measures
- [ ] OTP verification before first vote (optional enforcement)
- [ ] Email verification requirement
- [ ] Phone verification for first-time voters
- [ ] Captcha/reCAPTCHA on voting form
- [ ] Account age verification (minimum account age)

### Detection & Response
- [ ] Automatic flagging of suspicious patterns
- [ ] Admin manual review queue
- [ ] Vote invalidation for fraudulent votes
- [ ] Automatic account suspension for repeated fraud
- [ ] Competitor tracking (unusual spikes near deadline)
- [ ] Voting pattern analysis (time-based, geographic)
- [ ] Admin fraud detection dashboard

### Submission Fraud Prevention
- [ ] Originality checker (AI-based image similarity)
- [ ] Photographer identity verification
- [ ] Photo metadata validation (EXIF data check)
- [ ] Manual review queue for flagged submissions
- [ ] Reverse image search integration
- [ ] Watermark detection
- [ ] Submission rate limiting
- [ ] Disqualification workflow with notifications

