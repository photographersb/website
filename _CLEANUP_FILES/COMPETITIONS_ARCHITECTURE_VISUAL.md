# 🗺️ COMPETITIONS SYSTEM: VISUAL ARCHITECTURE & DEPENDENCIES

**Visual Guide to Understanding the Complete System**

---

## 1️⃣ SYSTEM ARCHITECTURE OVERVIEW

```
┌─────────────────────────────────────────────────────────────────┐
│                     PUBLIC INTERFACES (Vue/API)                  │
├──────────────────────┬──────────────────────┬──────────────────┤
│  Public Web Routes   │  Authenticated API   │  Admin Dashboard │
│  /competitions       │  /api/v1/            │  /admin/         │
│  GET details         │  POST submissions    │  POST create     │
│  GET leaderboard     │  POST votes          │  PUT update      │
│  GET winners         │  GET my-submissions  │  DELETE remove   │
│  GET submissions     │  GET my-votes        │  GET stats       │
└──────────────────────┴──────────────────────┴──────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     API LAYER (Laravel Routes)                   │
├──────────────────────┬──────────────────────┬──────────────────┤
│  CompetitionCtrll    │  Submission.Ctrll    │  Admin API Ctrll │
│  • index()           │  • store()           │  • index()       │
│  • show()            │  • update()          │  • store()       │
│  • getWinners()      │  • destroy()         │  • update()      │
│  • getLeaderboard()  │  • mySubmissions()   │  • delete()      │
│  • submitEntry()     │  • approve()         │  • calculate()   │
│  • vote()            │  • reject()          │  • winners()     │
└──────────────────────┴──────────────────────┴──────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    BUSINESS LOGIC (Eloquent Models)             │
├──────────────────────────────────────────────────────────────────┤
│  Competition                                                      │
│    ├─ submissions:     HasMany CompetitionSubmission            │
│    ├─ votes:          HasMany CompetitionVote                  │
│    ├─ scores:         HasMany CompetitionScore  ⚠️ P0-1        │
│    ├─ prizes:         HasMany CompetitionPrize                 │
│    ├─ categories:     HasMany CompetitionCategory              │
│    ├─ sponsors:       HasMany CompetitionSponsor               │
│    ├─ judges:         BelongsToMany User                       │
│    └─ seoMeta:        MorphOne SeoMeta                         │
│                                                                  │
│  CompetitionSubmission                                           │
│    ├─ competition:    BelongsTo Competition                    │
│    ├─ photographer:   BelongsTo User                           │
│    ├─ votes:          HasMany CompetitionVote                  │
│    ├─ scores:         HasMany CompetitionScore  ⚠️ P0-1        │
│    └─ category:       BelongsTo CompetitionCategory            │
│                                                                  │
│  CompetitionVote                                                 │
│    ├─ submission:     BelongsTo CompetitionSubmission          │
│    ├─ competition:    BelongsTo Competition                    │
│    └─ user:           BelongsTo User (voter)                   │
│                                                                  │
│  CompetitionScore  ⚠️ MISSING RELATIONSHIPS (P0-1)              │
│    ├─ competition:    BelongsTo Competition        [ADD THIS]  │
│    ├─ submission:     BelongsTo CompetitionSubmission [ADD]    │
│    ├─ judge:          BelongsTo User               [ADD THIS]  │
│    └─ criterion:      BelongsTo ScoringCriterion   [ADD THIS]  │
│                                                                  │
│  CompetitionPrize  ⚠️ AUTO-CALC BROKEN (P0-3)                   │
│    ├─ competition:    BelongsTo Competition                    │
│    └─ [observer]:     CompetitionPrizeObserver    [ADD THIS]  │
│                                                                  │
│  CompetitionCategory                                             │
│    ├─ competition:    BelongsTo Competition                    │
│    └─ submissions:    HasMany CompetitionSubmission            │
│                                                                  │
│  CompetitionSponsor                                              │
│    ├─ competition:    BelongsTo Competition                    │
│    ├─ sponsor:        BelongsTo Sponsor                        │
│    └─ logo:           String                                   │
│                                                                  │
│  CompetitionJudge (Pivot)                                        │
│    ├─ competition_id: FK                                        │
│    ├─ user_id:       FK                                        │
│    ├─ bio:           String                                    │
│    └─ expertise:     String                                    │
└──────────────────────────────────────────────────────────────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    DATABASE LAYER (MySQL Tables)                │
├──────────┬──────────┬──────────┬──────────┬──────────┬──────────┤
│ compet.  │ compet.  │ compet.  │ compet.  │ compet.  │ compet.  │
│submissions
│votes     │ scores   │ prizes   │ categs   │ sponsors │ judges   │
│          │          │          │          │          │ (pivot)  │
├──────────┼──────────┼──────────┼──────────┼──────────┼──────────┤
│ 25+ cols │ 8 cols   │10+ cols  │7 cols    │5 cols    │5 cols    │
│FK: comp  │FK: sub   │FK: comp  │FK: comp  │FK: comp  │FK: comp  │
│FK: user  │FK: vote  │FK: judge │FK: org   │FK: spon  │FK: judge │
│votes:int │score:dec │amount:$  │order:int │url:str   │role:str  │
│Indexed   │Indexed   │Indexed   │Indexed   │Indexed   │Indexed   │
└──────────┴──────────┴──────────┴──────────┴──────────┴──────────┘
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                  STORAGE LAYER (File System)                     │
├──────────────────────────────────────────────────────────────────┤
│  /storage/app/public/submissions/                                │
│    ├─ {competition_id}/                                          │
│    │   ├─ {submission_id}.jpg     (Original image)              │
│    │   └─ {submission_id}_thumb.jpg (Thumbnail) ⚠️ P0-2        │
│    └─ watermark/                                                 │
│        └─ {submission_id}_watermark.jpg                         │
│                                                                  │
│  /storage/app/certificates/                                      │
│    └─ {winner_id}.pdf              (Generated certificates)     │
│                                                                  │
│  /storage/app/share-frames/                                      │
│    └─ {submission_id}_share.jpg    (Social share image)         │
└──────────────────────────────────────────────────────────────────┘
```

---

## 2️⃣ DATA FLOW: USER SUBMITTING ENTRY

```
┌─────────────────────────────────────────────────────────────────┐
│ STEP 1: USER NAVIGATES TO SUBMISSION FORM                        │
└─────────────────────────────────────────────────────────────────┘
         ▼
    Frontend Vue Component
    GET /api/v1/competitions/{id}  ← Get competition details
         ▼
    CompetitionController::show()
         ▼
    Display form with:
    • Competition deadline
    • Required fields
    • Category options
    • File upload limits

┌─────────────────────────────────────────────────────────────────┐
│ STEP 2: USER UPLOADS IMAGE & SUBMITS                             │
└─────────────────────────────────────────────────────────────────┘
         ▼
    Frontend sends:
    POST /api/v1/competitions/{id}/submissions
    {
      "image": <File>,
      "title": "My Photo",
      "category_id": 1,
      "description": "..."
    }
         ▼
    CompetitionSubmissionController::store()
         ▼
    VALIDATION
    ✓ User authenticated
    ✓ Image mime type (jpg/png)
    ✓ Image size (< 10MB)
    ✓ Deadline not passed
    ✓ User not over submission limit
         ▼
    IMAGE PROCESSING ⚠️ P0-2: NO FALLBACK
    try {
      • Generate thumbnail (requires GD extension)
      • Generate watermark
      • Store original image
      • Store metadata
    } catch {
      ❌ CRASHES without GD
      (FIX: Add fallback)
    }
         ▼
    DATABASE INSERTION
    INSERT INTO competition_submissions (
      competition_id,
      photographer_id,
      category_id,
      title,
      image_path,
      thumbnail_path,
      status: 'pending_review',
      created_at
    )
         ▼
    RESPONSE TO FRONTEND
    {
      "status": "success",
      "submission": {
        "id": 42,
        "status": "pending_review",
        "message": "Awaiting admin approval"
      }
    }

┌─────────────────────────────────────────────────────────────────┐
│ STEP 3: ADMIN REVIEWS & APPROVES                                 │
└─────────────────────────────────────────────────────────────────┘
         ▼
    Admin Dashboard:
    GET /api/v1/admin/competitions/{id}/submissions/pending
         ▼
    AdminCompetitionApiController::pendingSubmissions() ❌ MISSING
    (Returns all submissions with status='pending_review')
         ▼
    Admin clicks "Approve"
    POST /api/v1/admin/submissions/{id}/approve
         ▼
    AdminSubmissionController::approve() ❌ MISSING
    UPDATE competition_submissions SET status='approved'
    SEND Notification to photographer
         ▼
    Submission now visible to public voters

┌─────────────────────────────────────────────────────────────────┐
│ STEP 4: PUBLIC VOTING BEGINS                                     │
└─────────────────────────────────────────────────────────────────┘
         ▼
    GET /api/v1/competitions/{id}/submissions
    Returns: All approved submissions (paginated)
         ▼
    User votes:
    POST /api/v1/competitions/{id}/submissions/{id}/vote
    {
      "vote": true
    }
         ▼
    CompetitionVoteController::vote()
    • Check user authenticated
    • Check rate limit (60/hour)
    • Check not already voted
    • Check submission exists & approved
    ✓ All pass
         ▼
    INSERT INTO competition_votes (
      submission_id,
      user_id,
      ip_address,
      device_fingerprint,
      created_at
    )
         ▼
    Vote count visible in real-time
    GET /api/v1/competitions/{id}/leaderboard
    Returns: Submissions ranked by vote_count DESC
         ▼

┌─────────────────────────────────────────────────────────────────┐
│ STEP 5: JUDGE SCORING (If judges exist)                          │
└─────────────────────────────────────────────────────────────────┘
         ▼
    Judge logs in
    GET /api/v1/competitions/{id}/scoring  ❌ MISSING
         ▼
    Judge Dashboard displays:
    • List of submissions to score
    • Scoring criteria
    • Current average scores
         ▼
    Judge scores submission #1
    POST /api/v1/competitions/{id}/submissions/{id}/score
    {
      "criterion_1": 8.5,
      "criterion_2": 9.0,
      "criterion_3": 7.5,
      "comments": "..."
    }
         ▼
    CompetitionScoreController::store() ❌ BROKEN (P0-1)
    INSERT INTO competition_scores (
      competition_id,           ← MISSING relationship
      submission_id,            ← Missing relationship
      judge_id,                 ← MISSING relationship
      criterion_id,             ← MISSING relationship
      score,
      created_at
    )
         ▼
    UPDATE competition_submissions SET
      average_score = (
        SELECT AVG(score) FROM competition_scores
        WHERE submission_id = {id}
      )

┌─────────────────────────────────────────────────────────────────┐
│ STEP 6: ADMIN CALCULATES WINNERS                                 │
└─────────────────────────────────────────────────────────────────┘
         ▼
    Admin clicks "Calculate Winners"
    POST /api/v1/admin/competitions/{id}/calculate-winners
         ▼
    AdminCompetitionApiController::calculateWinners()
    
    SELECT submissions WHERE approved=1
    ORDER BY average_score DESC (or vote_count DESC)
    LIMIT top_3
         ▼
    INSERT INTO competition_winners (
      competition_id,
      submission_id,
      position: 1|2|3,
      prize_amount: FROM competition_prizes,
      created_at
    )
         ▼
    UPDATE competition_submissions SET
      winner_position = {position},
      winner_announced = 1
         ▼
    Generate certificates (async job)
         ▼
    Admin announces winners:
    POST /api/v1/admin/competitions/{id}/announce-winners
         ▼
    UPDATE competitions SET
      status = 'completed',
      winners_announced = 1,
      announced_at = NOW()
         ▼
    Send notifications to winners

┌─────────────────────────────────────────────────────────────────┐
│ STEP 7: PUBLIC VIEWS RESULTS                                     │
└─────────────────────────────────────────────────────────────────┘
         ▼
    GET /api/v1/competitions/{id}/winners
    Returns: Winning submissions with:
    • Position (1st, 2nd, 3rd)
    • Prize amounts
    • Certificate links
    • Winner details
         ▼
    Public page displays:
    🥇 1st Place: $5000
    🥈 2nd Place: $3000
    🥉 3rd Place: $1000
         ▼
```

---

## 3️⃣ P0 BLOCKERS: DEPENDENCY CHAIN

```
P0-1: CompetitionScore Relationships
├─ Blocks: Judge scoring functionality
├─ Affects: Judge dashboard, score display, leaderboard
├─ Dependency: Requires CompetitionScore model update
├─ Time: 30 minutes
└─ Status: ⏳ WAITING

     ↓ (after P0-1)

P0-2: Image Processing Error Handler
├─ Blocks: Submission upload without GD
├─ Affects: User uploads, submission gallery
├─ Dependency: Requires CompetitionSubmissionController update
├─ Time: 1.5 hours
└─ Status: ⏳ WAITING

     ↓ (independent)

P0-3: Prize Pool Auto-Calculate
├─ Blocks: Admin prize management
├─ Affects: Prize display, admin experience
├─ Dependency: Requires CompetitionPrizeObserver + AppServiceProvider
├─ Time: 1 hour
└─ Status: ⏳ WAITING

     ↓ (independent)

P0-4: Admin Route Verification
├─ Blocks: Admin CRUD operations
├─ Affects: Competition creation/edit/delete
├─ Dependency: Requires route verification + testing
├─ Time: 1 hour
└─ Status: ⏳ WAITING

     ↓ (after P0-4)

P0-5: Dashboard Count Sync
├─ Blocks: Admin dashboard accuracy
├─ Affects: Admin UI stats display
├─ Dependency: Requires AdminCompetitionApiController fix
├─ Time: 1 hour
└─ Status: ⏳ WAITING

┌─────────────────────────────────────────────────────────────────┐
│ Once ALL P0 fixes complete → REGRESSION TESTING SUITE (7 hours) │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4️⃣ SYSTEM HEALTH CHECKLIST

```
✅ = Working  |  ⚠️ = Partial  |  ❌ = Missing  |  🔴 = Broken

DATABASE LAYER
  ✅ All 10 tables created
  ✅ All columns defined
  ✅ Foreign keys enforced
  ✅ Indexes present
  ✅ Timestamps enabled
  Status: 🟢 READY

MODEL LAYER
  ✅ Competition model
  ✅ CompetitionSubmission model
  ⚠️ CompetitionScore model (P0-1 missing relationships)
  ✅ CompetitionVote model
  ⚠️ CompetitionPrize model (P0-3 auto-calc missing)
  ✅ CompetitionCategory model
  ✅ CompetitionSponsor model
  ✅ CompetitionJudge model
  Status: 🟡 MOSTLY READY

API LAYER
  ✅ 30+ routes defined
  ✅ Authentication middleware
  ✅ Validation in place
  ❌ Some endpoints possibly missing (P0-4)
  Status: 🟡 MOSTLY READY

FILE UPLOAD
  ✅ Storage structure created
  ⚠️ GD error handling missing (P0-2)
  ✅ File validation works
  Status: 🟡 PARTIALLY READY

ADMIN FEATURES
  ⚠️ CRUD works (API only)
  ⚠️ Form UI unknown
  ❌ Moderation queue missing (P1-1)
  ❌ Judge management form missing
  ❌ Sponsor assignment unknown
  Status: 🔴 NEEDS WORK

PUBLIC FEATURES
  ✅ Competition listing
  ✅ Detail pages
  ✅ Submission gallery
  ✅ Voting system
  ✅ Leaderboard
  ✅ Winners display
  Status: 🟢 READY

NOTIFICATIONS
  ❌ Email on rejection
  ❌ Email on approval
  ❌ Email on winner announcement
  Status: 🔴 MISSING (P2)

ANALYTICS
  ❌ Admin dashboard metrics
  ❌ Vote fraud detection
  ❌ Performance analytics
  Status: 🔴 MISSING (P2)

OVERALL: 🟡 70% READY FOR LAUNCH
```

---

## 5️⃣ FILE DEPENDENCY MAP

```
Entry Points
    ▼
  routes/api.php (30+ competition routes)
    ├─ → CompetitionController.php ✅
    ├─ → CompetitionSubmissionController.php ⚠️ P0-2
    ├─ → CompetitionVoteController.php ✅
    ├─ → AdminCompetitionApiController.php ⚠️ P0-4, P0-5
    └─ → CompetitionCategoryController.php ✅
    
Models
    ▼
  app/Models/
    ├─ Competition.php ✅
    ├─ CompetitionSubmission.php ✅
    ├─ CompetitionScore.php 🔴 P0-1
    ├─ CompetitionVote.php ✅
    ├─ CompetitionPrize.php ⚠️ P0-3
    ├─ CompetitionCategory.php ✅
    ├─ CompetitionSponsor.php ✅
    ├─ CompetitionJudge.php ✅
    └─ Observers/
        └─ CompetitionPrizeObserver.php ❌ MISSING (P0-3)

Database
    ▼
  database/migrations/
    ├─ create_competitions_table.php ✅
    ├─ create_competition_submissions_table.php ✅
    ├─ create_competition_votes_table.php ✅
    ├─ create_competition_scores_table.php ✅
    ├─ create_competition_prizes_table.php ✅
    ├─ create_competition_categories_table.php ✅
    ├─ create_competition_sponsors_table.php ✅
    └─ create_competition_judges_table.php ✅

Storage
    ▼
  storage/app/public/submissions/ ✅ (with P0-2 fix)
  storage/app/certificates/ ✅
  storage/app/share-frames/ ✅

Configuration
    ▼
  app/Providers/
    └─ AppServiceProvider.php ⚠️ (needs P0-3 observer registration)
    
Tests (if any)
    ▼
  tests/Feature/CompetitionTest.php ❌ MISSING
  tests/Unit/CompetitionModelTest.php ❌ MISSING
```

---

## 6️⃣ QUICK REFERENCE: WHERE TO FIND THINGS

```
🔍 Need to find...                          📂 Look here...

Competition listing endpoint               routes/api.php:92
Submission storage                         app/Http/Controllers/Api/CompetitionSubmissionController.php:58
Vote throttling                           app/Http/Middleware/ThrottleVotes.php
Winner calculation                        AdminCompetitionApiController::calculateWinners()
Database schema                           database/migrations/
Model relationships                       app/Models/Competition.php
Admin authorization                       app/Policies/CompetitionPolicy.php
Image validation rules                    CompetitionSubmissionController::store()
Pivot table data                          app/Models/Pivots/CompetitionJudge.php
SEO metadata                              app/Traits/HasSeoMeta.php
Vote fraud detection                      app/Services/VoteFraudDetector.php
Prize assignment                          CompetitionWinnerService.php
Certificate generation                    app/Services/CertificateGenerator.php
Email notifications                       app/Notifications/
API response formatting                   app/Http/Resources/CompetitionResource.php
```

---

**Status:** ARCHITECTURE DOCUMENTED ✅  
**Ready for:** Implementation Phase  
**Next Action:** Follow [P0_FIXES_QUICK_START.md](P0_FIXES_QUICK_START.md)
