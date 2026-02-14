# QA Testing System - Complete Documentation

## 📋 Overview

This document provides a comprehensive guide to the automated QA testing system for the Photographer Marketplace Platform (Laravel). The system is designed to prevent regressions, ensure code quality, and validate all major user flows.

### Key Features
- ✅ **Route Health Checks**: Automatic validation of all GET routes
- ✅ **CRUD Tests**: Complete admin module testing
- ✅ **User Flow Tests**: End-to-end testing of key features
- ✅ **CI/CD Integration**: GitHub Actions workflow
- ✅ **Code Quality**: Pint + Laravel Insights
- ✅ **Coverage Reporting**: HTML and XML coverage reports
- ✅ **Daily Regression Testing**: Automated nightly test runs

---

## 📁 Test Structure

```
tests/
├── Feature/
│   ├── RouteHealthCheckTest.php          # All route health checks
│   ├── CompetitionAdminCrudTest.php      # Competition CRUD operations
│   ├── CompetitionSubmissionVotingTest.php # Submission & voting flow
│   ├── EventAdminCrudTest.php            # Event CRUD operations
│   ├── EventRegistrationAttendanceTest.php # Registration & attendance
│   ├── VerificationFlowTest.php          # Photographer verification
│   ├── BookingFlowTest.php               # Booking request flow
│   └── JudgeScoringFlowTest.php          # Judge scoring process
database/
├── factories/
│   ├── UserFactory.php                   # User roles: admin, judge, photographer, client
│   ├── CategoryFactory.php               # Photography categories
│   ├── LocationFactory.php               # Bangladesh divisions
│   ├── PhotographerProfileFactory.php    # Photographer profiles
│   ├── CompetitionFactory.php            # Competitions
│   ├── EventFactory.php                  # Events
│   ├── VerificationRequestFactory.php    # Verification requests
│   ├── CompetitionSubmissionFactory.php  # Submissions
│   ├── CompetitionVoteFactory.php        # Votes
│   ├── BookingRequestFactory.php         # Booking requests
│   ├── SponsorFactory.php                # Sponsors
│   ├── MentorFactory.php                 # Mentors
│   └── EventRegistrationFactory.php      # Event registrations
└── seeders/
    ├── BangladeshDivisionSeeder.php      # 8 Bangladesh divisions
    ├── PhotographyCategorySeeder.php     # 12 photography categories
    └── TestDataSeeder.php                # Demo admin, judges, mentors, sponsors
.github/
└── workflows/
    ├── tests.yml                         # CI tests on push/PR
    └── regression-tests.yml              # Daily regression tests
```

---

## 🏭 Factories (Model Builders)

Each factory creates realistic test data with chainable modifiers.

### UserFactory
```php
// Create specific roles
User::factory()->admin()->create();           // Admin user
User::factory()->judge()->create();           // Judge user
User::factory()->photographer()->create();    // Photographer
User::factory()->client()->create();          // Regular client

// Special states
User::factory()->suspended()->create();       // Suspended user
User::factory()->unverified()->create();      // Unverified email
```

### CompetitionFactory
```php
Competition::factory()->published()->create();    // Published & public
Competition::factory()->draft()->create();        // Draft (private)
Competition::factory()->closed()->create();       // Registration closed
Competition::factory()->upcoming()->create();     // Future competition
```

### EventFactory
```php
Event::factory()->free()->create();              // Free event
Event::factory()->paid()->create();              // Paid event
Event::factory()->online()->create();            // Online event
Event::factory()->upcoming()->create();          // Future event
Event::factory()->past()->create();              // Past event
```

### PhotographerProfileFactory
```php
Photographer::factory()->verified()->create();   // Verified badge
Photographer::factory()->featured()->create();   // Featured profile
```

---

## 🌱 Seeders (Data Population)

### BangladeshDivisionSeeder
Seeds 8 major divisions with coordinates:
- Dhaka, Chittagong, Khulna, Rajshahi, Barisal, Sylhet, Rangpur, Mymensingh

```bash
php artisan db:seed --class=Database\\Seeders\\BangladeshDivisionSeeder
```

### PhotographyCategorySeeder
Seeds 12 photography categories:
- Wedding, Portrait, Landscape, Product, Event, Corporate, Nature, Fashion, Sports, Architecture, Street, Food

```bash
php artisan db:seed --class=Database\\Seeders\\PhotographyCategorySeeder
```

### TestDataSeeder
Creates complete demo dataset:
- 1 Admin user
- 3 Judge users with Judge profiles
- 5 Active mentors
- 13 Sponsors (3 gold, 5 silver, 5 bronze)
- 10 Photographer users with profiles
- 15 Client users

```bash
php artisan db:seed --class=Database\\Seeders\\TestDataSeeder
```

---

## 🧪 Feature Tests

### 1. RouteHealthCheckTest
**Purpose**: Validates all GET routes return correct HTTP status codes

**Tests**:
- ✅ Public routes return 200 (sitemap, home)
- ✅ SEO routes: /@username, /photographer/{id}
- ✅ API public endpoints
- ✅ Admin routes deny unauthenticated users
- ✅ Photographer profile API routes
- ✅ Competition/Event routes with existing records
- ✅ Route collection contains critical routes
- ✅ Auth endpoints configured
- ✅ Throttle rate limiting applied

**Run**:
```bash
php artisan test tests/Feature/RouteHealthCheckTest.php
```

---

### 2. CompetitionAdminCrudTest
**Purpose**: Admin competition management

**Tests**:
- ✅ Create competition with judges/sponsors
- ✅ Update competition details
- ✅ Sync judges pivot table
- ✅ Sync sponsors pivot table
- ✅ Delete competition
- ✅ Authorization: non-admin blocked
- ✅ Input validation

**Example**:
```php
$response = $this->actingAs($admin)
    ->postJson('/api/v1/admin/competitions', [
        'title' => 'Photography Competition 2026',
        'judges' => [$judge->id],
        'sponsors' => [$sponsor->id],
    ]);

$this->assertDatabaseHas('competitions', ['title' => '...']);
```

**Run**:
```bash
php artisan test tests/Feature/CompetitionAdminCrudTest.php
```

---

### 3. CompetitionSubmissionVotingTest
**Purpose**: Photographer submissions and voting flows

**Tests**:
- ✅ Photographer can submit to competition
- ✅ Admin approves/rejects submissions
- ✅ Client can vote (1 vote per user enforced)
- ✅ Vote value validation (1-5 rating)
- ✅ Vote count increments
- ✅ Unapproved submissions can't be voted
- ✅ Average score calculation

**Key Business Rules Tested**:
1. One vote per user per submission (duplicate vote prevented)
2. Only approved submissions receive votes
3. Vote ratings must be 1-5
4. Vote counts update correctly

**Run**:
```bash
php artisan test tests/Feature/CompetitionSubmissionVotingTest.php
```

---

### 4. EventAdminCrudTest
**Purpose**: Admin event management

**Tests**:
- ✅ Create free event
- ✅ Create paid event with currency
- ✅ Update event details
- ✅ Assign mentors to event
- ✅ Enforce registration deadline
- ✅ Delete event
- ✅ Input validation
- ✅ Authorization checks

**Run**:
```bash
php artisan test tests/Feature/EventAdminCrudTest.php
```

---

### 5. EventRegistrationAttendanceTest
**Purpose**: Registration, QR scanning, and attendance tracking

**Tests**:
- ✅ Client registers for free event
- ✅ Prevent duplicate registration
- ✅ QR code generated on registration
- ✅ Admin scans QR and checks in attendee
- ✅ Prevent duplicate check-in
- ✅ Client submits feedback after event
- ✅ Max attendees enforced
- ✅ Registration deadline enforced

**QR Code Flow**:
```
1. Client registers → registration_code generated (UNIQ-CODE)
2. QR code auto-created: public/qr-codes/events/{event_id}/{code}.png
3. Admin scans QR → extracts registration_code
4. Check-in logged with attended_at timestamp
5. Certificate auto-issued (if enabled)
```

**Run**:
```bash
php artisan test tests/Feature/EventRegistrationAttendanceTest.php
```

---

### 6. VerificationFlowTest
**Purpose**: Photographer verification badge system

**Tests**:
- ✅ Photographer submits verification request
- ✅ Admin views pending requests
- ✅ Admin approves request
- ✅ Admin rejects with reason
- ✅ Verification badge appears on profile
- ✅ Prevent duplicate submissions
- ✅ Rejected requests can be resubmitted

**Status Transitions**:
```
pending → approved → verified badge ✓
       → rejected  → can resubmit
```

**Run**:
```bash
php artisan test tests/Feature/VerificationFlowTest.php
```

---

### 7. BookingFlowTest
**Purpose**: End-to-end booking request workflow

**Tests**:
- ✅ Client creates booking request
- ✅ Photographer accepts/rejects
- ✅ Client responds to proposal
- ✅ Messages created for conversation
- ✅ Only photographer can respond to their bookings
- ✅ Only client can respond to proposal
- ✅ Rejected bookings can't be accepted
- ✅ Authorization enforcement

**Status Flow**:
```
pending → photographer accepts → accepted
       → client confirms → confirmed
messages enabled throughout
```

**Run**:
```bash
php artisan test tests/Feature/BookingFlowTest.php
```

---

### 8. JudgeScoringFlowTest
**Purpose**: Judge assignment and submission scoring

**Tests**:
- ✅ Admin assigns judge to competition
- ✅ Judge scores submission (1-10 scale)
- ✅ Judge can only score assigned competitions
- ✅ Score validation (0-10 range)
- ✅ Prevent duplicate scoring
- ✅ Average score calculated from multiple judges
- ✅ Ranking computed from scores

**Scoring Process**:
```
1. Admin assigns judges to competition
2. Judge views assigned competition submissions
3. Judge submits score with criteria breakdown
4. System calculates average from all judges
5. Ranking determined by average score
```

**Run**:
```bash
php artisan test tests/Feature/JudgeScoringFlowTest.php
```

---

## 🚀 Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
# Run only feature tests
php artisan test tests/Feature

# Run specific test class
php artisan test tests/Feature/RouteHealthCheckTest.php

# Run with verbose output
php artisan test --verbose

# Run with coverage report
php artisan test --coverage

# HTML coverage report
php artisan test --coverage --coverage-html=coverage-report
```

### Run with Coverage
```bash
php artisan test tests/Feature --coverage --coverage-html=coverage-report
# Report generated in coverage-report/index.html
```

### Filter Tests by Name
```bash
# Run only admin tests
php artisan test --filter Admin

# Run only tests containing "vote"
php artisan test --filter vote
```

### Parallel Test Execution (Faster)
```bash
php artisan test --parallel
```

---

## 🔄 CI/CD Workflows

### Automated CI Tests (Push/Pull Request)

**Triggers**: On push to `main` or `develop`, or PR to these branches

**File**: `.github/workflows/tests.yml`

**Steps**:
1. Checkout code
2. Setup PHP 8.2 with extensions
3. Install composer dependencies
4. Copy `.env.example` → `.env.testing`
5. Generate app key
6. Run migrations on test database
7. Seed test data (divisions, categories, demo users)
8. Run each test suite individually:
   - Route Health Checks
   - Competition Admin CRUD
   - Competition Submissions & Voting
   - Event Admin CRUD
   - Event Registration & Attendance
   - Verification Flow
   - Booking Flow
   - Judge Scoring
9. Run full test suite with coverage
10. Upload coverage to Codecov
11. Run Pint code style check
12. Run Laravel Insights

**View Results**:
- GitHub Actions tab → click workflow run → scroll to test output

### Daily Regression Testing

**Triggers**: Daily at 2 AM UTC (or manual `workflow_dispatch`)

**File**: `.github/workflows/regression-tests.yml`

**Features**:
- Fresh database migration + seed
- Full test suite execution
- HTML coverage report generation
- Slack notification on failure
- Artifact upload

**Slack Integration** (Optional):
```bash
# Set in GitHub repo settings → Secrets
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...
```

---

## 📊 Coverage Reports

### Generate Local Coverage
```bash
php artisan test --coverage --coverage-html=coverage-report
open coverage-report/index.html  # macOS
# or
start coverage-report/index.html # Windows
```

### Coverage Targets
- **Lines**: Aim for >80%
- **Methods**: Aim for >85%
- **Classes**: Aim for >90%

**View What's NOT Covered**:
1. Open coverage-report/index.html
2. Click on files
3. Red lines = not executed by tests
4. Add tests to improve coverage

---

## 🛠️ Local Setup & Debugging

### Prerequisites
```bash
# PHP 8.2+
php -v

# MySQL 8.0
mysql --version

# Composer
composer --version

# Node.js (for frontend)
node --version
```

### Initial Setup
```bash
# 1. Install dependencies
composer install

# 2. Copy environment file
cp .env.example .env.testing

# 3. Generate key
php artisan key:generate --env=testing

# 4. Create test database
mysql -u root -p -e "CREATE DATABASE photographar_test;"

# 5. Run migrations
php artisan migrate --env=testing

# 6. Seed test data
php artisan db:seed --env=testing

# 7. Run tests
php artisan test
```

### Debug Failing Test
```bash
# 1. Run with verbose output
php artisan test tests/Feature/CompetitionAdminCrudTest.php -v

# 2. Run single test method
php artisan test tests/Feature/CompetitionAdminCrudTest.php 
    --filter=test_admin_can_create_competition

# 3. Run with dd() to debug
# Add to test: dd($response->json());

# 4. Check database state
# Add to test: dd(DB::getQueryLog());
```

---

## 📋 Regression Test Checklist

Use this checklist before major releases:

### User Management
- [ ] Admin can create/update/delete users
- [ ] User roles (admin, judge, photographer, client) work correctly
- [ ] Suspended users cannot login
- [ ] Email verification enforced

### Photographer Features
- [ ] Directory listing with filters works
- [ ] Search filters by city, category, rating
- [ ] Verification request submission works
- [ ] Verification badge appears after approval
- [ ] Profile SEO URLs work: /@username

### Competitions
- [ ] Admin can create competitions with judges/sponsors
- [ ] Photographers can submit entries
- [ ] Submissions require admin approval
- [ ] Voting open only after approval
- [ ] One vote per user enforced
- [ ] Judge scoring calculated
- [ ] Winners determined from scores

### Events
- [ ] Admin can create free/paid events
- [ ] Registration deadline enforced
- [ ] QR codes generated for registrations
- [ ] Admin can scan QR and check in
- [ ] Max attendees limit enforced
- [ ] Certificates auto-issued on attendance

### Bookings
- [ ] Client can create booking requests
- [ ] Photographer receives notification
- [ ] Photographer can accept/reject
- [ ] Client can confirm proposal
- [ ] Messages enabled for communication

### Routes & Performance
- [ ] All public routes return 200
- [ ] Admin routes redirect unauthenticated users
- [ ] Throttle rate limiting applies
- [ ] SEO routes (sitemap, robots.txt) work
- [ ] API endpoints respond within 500ms

---

## 🐛 Common Test Issues & Solutions

### Issue: "Class not found" Error
```
Solution: Run composer autoload
composer dump-autoload
php artisan test
```

### Issue: Database Connection Failed
```
Solution: Verify test database credentials in .env.testing
Ensure MySQL is running:
mysql -u root -p -e "SELECT 1"
```

### Issue: Migration Failed
```
Solution: Manually check for conflicts
php artisan migrate:reset --env=testing
php artisan migrate --env=testing
```

### Issue: Factories Not Found
```
Solution: Ensure UserFactory extends Factory class
Check namespace: namespace Database\Factories;
Run: composer dump-autoload
```

### Issue: Tests Pass Locally But Fail in CI
```
Solution: 
- Check database credentials in CI workflow
- Ensure ENV variables match .github/workflows/tests.yml
- Verify MySQL service is running in CI
- Check PHP version matches (8.2)
```

---

## 📝 Writing New Tests

### Template for Feature Test
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class MyFeatureTest extends TestCase
{
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_something_works()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/endpoint');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('table', ['field' => 'value']);
    }
}
```

### Testing Best Practices
1. Use descriptive test names: `test_admin_can_create_competition`
2. Arrange-Act-Assert pattern
3. One assertion per test (ideally)
4. Use factories for test data
5. Clean up in tearDown if needed
6. Mock external services (email, payment)
7. Test both success and failure cases

---

## 📞 Support & Troubleshooting

### View Test Logs
```bash
# Real-time test output
php artisan test -v

# Capture output to file
php artisan test > test-results.txt 2>&1
```

### Check Database State During Test
```php
// Add to test
$this->actingAs($user)->post('/api/endpoint', []);
dd(User::count());  // Shows count
dd(User::all());    // Shows all records
```

### Debug with Laravel Debugbar
Add to .env during dev:
```
DEBUGBAR_ENABLED=true
```

---

## 📚 Additional Resources

- [PHPUnit Documentation](https://phpunit.de/)
- [Laravel Testing Documentation](https://laravel.com/docs/11.x/testing)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Factory Boy Documentation](https://factoryboy.readthedocs.io/)

---

**Generated**: February 2026  
**Platform**: Photographer Marketplace (Laravel 11)  
**Test Coverage**: 8 Feature Test Suites, 60+ Individual Tests
