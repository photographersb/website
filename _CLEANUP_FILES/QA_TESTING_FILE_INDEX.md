# 🗂️ QA Testing System - File Index & Navigation

**Quick Navigation Guide for the Complete QA Testing System**

---

## 📁 TESTS DIRECTORY

### Feature Tests (8 Suites, 64 Tests)

#### 1. [RouteHealthCheckTest.php](tests/Feature/RouteHealthCheckTest.php)
- **Purpose**: Validate all GET routes return correct HTTP status codes
- **Tests**: 12 individual test methods
- **Coverage**: Public routes, SEO routes, API endpoints, admin routes
- **Run**: `php artisan test tests/Feature/RouteHealthCheckTest.php`

#### 2. [CompetitionAdminCrudTest.php](tests/Feature/CompetitionAdminCrudTest.php)
- **Purpose**: Admin competition management (Create/Read/Update/Delete)
- **Tests**: 7 individual test methods
- **Coverage**: Competition creation, judges/sponsors sync, deletion
- **Run**: `php artisan test tests/Feature/CompetitionAdminCrudTest.php`

#### 3. [CompetitionSubmissionVotingTest.php](tests/Feature/CompetitionSubmissionVotingTest.php)
- **Purpose**: Photography submissions and voting workflows
- **Tests**: 7 individual test methods
- **Coverage**: Submission creation, admin approval, voting (one per user), scoring
- **Run**: `php artisan test tests/Feature/CompetitionSubmissionVotingTest.php`

#### 4. [EventAdminCrudTest.php](tests/Feature/EventAdminCrudTest.php)
- **Purpose**: Admin event management (Create/Read/Update/Delete)
- **Tests**: 8 individual test methods
- **Coverage**: Free/paid events, mentor assignment, deadline enforcement
- **Run**: `php artisan test tests/Feature/EventAdminCrudTest.php`

#### 5. [EventRegistrationAttendanceTest.php](tests/Feature/EventRegistrationAttendanceTest.php)
- **Purpose**: Event registration, QR scanning, attendance tracking
- **Tests**: 8 individual test methods
- **Coverage**: Registration, QR generation, check-in, feedback, max attendees
- **Run**: `php artisan test tests/Feature/EventRegistrationAttendanceTest.php`

#### 6. [VerificationFlowTest.php](tests/Feature/VerificationFlowTest.php)
- **Purpose**: Photographer verification badge system
- **Tests**: 7 individual test methods
- **Coverage**: Request submission, admin review, approval/rejection, badge display
- **Run**: `php artisan test tests/Feature/VerificationFlowTest.php`

#### 7. [BookingFlowTest.php](tests/Feature/BookingFlowTest.php)
- **Purpose**: End-to-end booking request workflow
- **Tests**: 8 individual test methods
- **Coverage**: Request creation, photographer response, client acceptance, messaging
- **Run**: `php artisan test tests/Feature/BookingFlowTest.php`

#### 8. [JudgeScoringFlowTest.php](tests/Feature/JudgeScoringFlowTest.php)
- **Purpose**: Judge assignment and submission scoring
- **Tests**: 7 individual test methods
- **Coverage**: Judge assignment, scoring (1-10), average calculation, ranking
- **Run**: `php artisan test tests/Feature/JudgeScoringFlowTest.php`

---

## 📁 FACTORIES DIRECTORY

### Model Factories (13 Total)

#### 1. [UserFactory.php](database/factories/UserFactory.php) (311 lines)
- **Creates**: User model with roles
- **Roles**: admin, judge, photographer, client
- **States**: suspended, unverified
- **Usage**: `User::factory()->admin()->create()`

#### 2. [CategoryFactory.php](database/factories/CategoryFactory.php) (32 lines)
- **Creates**: Category model
- **Features**: Unique names, icons, colors
- **Usage**: `Category::factory()->create()`

#### 3. [LocationFactory.php](database/factories/LocationFactory.php) (32 lines)
- **Creates**: City model (Bangladesh divisions)
- **Features**: Coordinates, division mapping
- **Usage**: `Location::factory()->create()`

#### 4. [PhotographerProfileFactory.php](database/factories/PhotographerProfileFactory.php) (56 lines)
- **Creates**: Photographer profile
- **States**: verified, featured
- **Features**: Rates, reviews, stats
- **Usage**: `Photographer::factory()->verified()->create()`

#### 5. [CompetitionFactory.php](database/factories/CompetitionFactory.php) (78 lines)
- **Creates**: Competition model
- **States**: published, draft, closed, upcoming
- **Features**: Dates, fees, prize pool
- **Usage**: `Competition::factory()->upcoming()->create()`

#### 6. [EventFactory.php](database/factories/EventFactory.php) (77 lines)
- **Creates**: Event model
- **States**: free, paid, online, upcoming, past
- **Features**: Types, venues, attendees
- **Usage**: `Event::factory()->free()->create()`

#### 7. [VerificationRequestFactory.php](database/factories/VerificationRequestFactory.php) (39 lines)
- **Creates**: VerificationRequest model
- **States**: pending, approved, rejected
- **Features**: Types, documents, notes
- **Usage**: `VerificationRequest::factory()->pending()->create()`

#### 8. [CompetitionSubmissionFactory.php](database/factories/CompetitionSubmissionFactory.php) (50 lines)
- **Creates**: CompetitionSubmission model
- **States**: approved, rejected, finalist, winner
- **Features**: Votes, scores, status
- **Usage**: `CompetitionSubmission::factory()->approved()->create()`

#### 9. [CompetitionVoteFactory.php](database/factories/CompetitionVoteFactory.php) (25 lines)
- **Creates**: CompetitionVote model
- **Features**: 1-5 rating, comments
- **Usage**: `CompetitionVote::factory()->create()`

#### 10. [BookingRequestFactory.php](database/factories/BookingRequestFactory.php) (51 lines)
- **Creates**: BookingRequest model
- **States**: accepted, rejected, confirmed
- **Features**: Event types, budgets, hours
- **Usage**: `BookingRequest::factory()->accepted()->create()`

#### 11. [SponsorFactory.php](database/factories/SponsorFactory.php) (42 lines)
- **Creates**: Sponsor model
- **States**: gold, silver, bronze
- **Features**: Contact info, website
- **Usage**: `Sponsor::factory()->gold()->create()`

#### 12. [MentorFactory.php](database/factories/MentorFactory.php) (42 lines)
- **Creates**: Mentor model
- **States**: active, inactive
- **Features**: Expertise, rates, availability
- **Usage**: `Mentor::factory()->active()->create()`

#### 13. [EventRegistrationFactory.php](database/factories/EventRegistrationFactory.php) (38 lines)
- **Creates**: EventRegistration model
- **States**: attended, cancelled, withFeedback
- **Features**: QR codes, check-in, feedback
- **Usage**: `EventRegistration::factory()->attended()->create()`

---

## 📁 SEEDERS DIRECTORY

### Database Seeders (3 Total)

#### 1. [BangladeshDivisionSeeder.php](database/seeders/BangladeshDivisionSeeder.php) (80 lines)
- **Populates**: cities table with Bangladesh divisions
- **Records**: 8 divisions (Dhaka, Chittagong, Khulna, Rajshahi, Barisal, Sylhet, Rangpur, Mymensingh)
- **Features**: Coordinates, is_active flag
- **Run**: `php artisan db:seed --class=Database\\Seeders\\BangladeshDivisionSeeder`

#### 2. [PhotographyCategorySeeder.php](database/seeders/PhotographyCategorySeeder.php) (66 lines)
- **Populates**: categories table with photography types
- **Records**: 12 categories (Wedding, Portrait, Landscape, Product, Event, Corporate, Nature, Fashion, Sports, Architecture, Street, Food)
- **Features**: Icons, colors, descriptions
- **Run**: `php artisan db:seed --class=Database\\Seeders\\PhotographyCategorySeeder`

#### 3. [TestDataSeeder.php](database/seeders/TestDataSeeder.php) (44 lines)
- **Populates**: Complete demo dataset
- **Records**:
  - 1 Admin user (admin@test.com)
  - 3 Judge users with Judge profiles
  - 5 Mentors (active)
  - 13 Sponsors (3 gold, 5 silver, 5 bronze)
  - 10 Photographer profiles
  - 15 Client users
- **Run**: `php artisan db:seed --class=Database\\Seeders\\TestDataSeeder`

---

## 📁 CI/CD WORKFLOWS

### GitHub Actions Workflows (2 Files)

#### 1. [.github/workflows/tests.yml]((.github/workflows/tests.yml)) (102 lines)
- **Purpose**: Automated testing on push/PR
- **Triggers**: Push to main/develop, PR to main/develop
- **Duration**: ~8-10 minutes
- **Actions**:
  - PHP 8.2 setup with MySQL
  - Composer dependency installation (cached)
  - Database migration + seeding
  - Run 8 test suites (individual)
  - Coverage report generation
  - Codecov upload
  - Code quality checks (Pint + Insights)

#### 2. [.github/workflows/regression-tests.yml]((.github/workflows/regression-tests.yml)) (93 lines)
- **Purpose**: Daily regression testing
- **Triggers**: Daily 2 AM UTC or manual trigger
- **Duration**: ~10-12 minutes
- **Actions**:
  - Fresh database + migration
  - Complete seeding
  - Full test suite
  - HTML coverage report
  - Artifact upload
  - Slack notification on failure

---

## 📁 CONFIGURATION FILES

### 1. [phpunit.xml](phpunit.xml) (60 lines)
- **Purpose**: PHPUnit test framework configuration
- **Features**:
  - Test suite definitions (Feature, Unit)
  - MySQL test database setup
  - Coverage report configuration (HTML + XML)
  - Bootstrap configuration
  - Environment variable setup
- **Usage**: Automatically loaded by `php artisan test`

---

## 📁 DOCUMENTATION FILES

### 1. [QA_TESTING_COMPLETE_DOCUMENTATION.md](QA_TESTING_COMPLETE_DOCUMENTATION.md) (450+ lines)
- **Audience**: QA Engineers, Developers
- **Content**:
  - Complete test system overview
  - Detailed test suite documentation
  - Factory usage examples
  - Seeder descriptions
  - CI/CD workflow details
  - Coverage report interpretation
  - Local setup instructions
  - Troubleshooting guide
  - Test writing best practices
  - 100+ pages of reference material

### 2. [QA_TESTING_QUICK_REFERENCE.md](QA_TESTING_QUICK_REFERENCE.md) (250+ lines)
- **Audience**: Developers (quick lookup)
- **Content**:
  - 5-minute quick start
  - Command cheatsheet
  - Test coverage table
  - Factory quick examples
  - Coverage goals
  - Debug tips
  - Test patterns
  - Pre-deployment checklist

### 3. [QA_REGRESSION_CHECKLIST.md](QA_REGRESSION_CHECKLIST.md) (400+ lines)
- **Audience**: QA Engineers, Product Managers
- **Content**:
  - Pre-release testing checklist
  - 12 major test sections
  - 100+ individual test items
  - Pass/fail tracking
  - Issue logging template
  - Sign-off section
  - Coverage summary

### 4. [QA_TESTING_SYSTEM_README.md](QA_TESTING_SYSTEM_README.md) (350+ lines)
- **Audience**: Project managers, Team leads
- **Content**:
  - System overview
  - Quick start guide
  - Directory structure
  - Test suites summary
  - CI/CD overview
  - Coverage targets
  - Troubleshooting
  - Best practices

### 5. [QA_TESTING_IMPLEMENTATION_COMPLETE.md](QA_TESTING_IMPLEMENTATION_COMPLETE.md) (400+ lines)
- **Audience**: Project stakeholders, Architects
- **Content**:
  - Implementation summary
  - Deliverables list
  - Statistics & metrics
  - Key features
  - Getting started guide
  - Quality metrics
  - Deployment checklist
  - Summary statement

---

## 🚀 QUICK COMMAND REFERENCE

### Run Tests
```bash
# All tests
php artisan test

# Specific test file
php artisan test tests/Feature/RouteHealthCheckTest.php

# Specific test method
php artisan test --filter=test_admin_can_create_competition

# All tests matching filter
php artisan test --filter=Competition

# Verbose output
php artisan test -v

# With coverage
php artisan test --coverage --coverage-html=coverage-report

# Parallel execution
php artisan test --parallel

# Stop on first failure
php artisan test --stop-on-failure
```

### Database Setup
```bash
# Run migrations
php artisan migrate --env=testing

# Seed test data
php artisan db:seed --env=testing

# Reset database
php artisan migrate:reset --env=testing

# Fresh migration + seed
php artisan migrate:fresh --env=testing --seed
```

### Development
```bash
# Generate app key
php artisan key:generate --env=testing

# View routes
php artisan route:list

# Clear cache
php artisan cache:clear
```

---

## 📊 FILE STATISTICS

| Type | Count | Files | Lines |
|------|-------|-------|-------|
| Test Files | 8 | `tests/Feature/*.php` | 1,200+ |
| Factories | 13 | `database/factories/*.php` | 600+ |
| Seeders | 3 | `database/seeders/*.php` | 190+ |
| CI Workflows | 2 | `.github/workflows/*.yml` | 200+ |
| Configuration | 1 | `phpunit.xml` | 60 |
| Documentation | 5 | `QA_*.md` | 1,500+ |
| **TOTAL** | **32 Files** | - | **3,750+ Lines** |

---

## 🎯 NAVIGATION GUIDE

### I Want To...

**...run all tests**
→ `php artisan test`
→ See: [QA_TESTING_QUICK_REFERENCE.md](QA_TESTING_QUICK_REFERENCE.md)

**...debug a failing test**
→ `php artisan test tests/Feature/CompetitionAdminCrudTest.php -v`
→ See: [QA_TESTING_COMPLETE_DOCUMENTATION.md](QA_TESTING_COMPLETE_DOCUMENTATION.md#troubleshooting)

**...add a new test**
→ Review: [tests/Feature/RouteHealthCheckTest.php](tests/Feature/RouteHealthCheckTest.php)
→ See: [QA_TESTING_COMPLETE_DOCUMENTATION.md#writing-new-tests](QA_TESTING_COMPLETE_DOCUMENTATION.md)

**...use factories in my code**
→ Review: [database/factories/UserFactory.php](database/factories/UserFactory.php)
→ Example: `User::factory()->admin()->create()`

**...seed test data**
→ Run: `php artisan db:seed --env=testing`
→ See: [database/seeders/TestDataSeeder.php](database/seeders/TestDataSeeder.php)

**...run regression checklist before release**
→ See: [QA_REGRESSION_CHECKLIST.md](QA_REGRESSION_CHECKLIST.md)

**...understand the CI/CD process**
→ See: [.github/workflows/tests.yml](.github/workflows/tests.yml)
→ See: [QA_TESTING_SYSTEM_README.md#cicd-workflows](QA_TESTING_SYSTEM_README.md)

**...learn best practices**
→ See: [QA_TESTING_COMPLETE_DOCUMENTATION.md#best-practices](QA_TESTING_COMPLETE_DOCUMENTATION.md)

**...troubleshoot an error**
→ See: [QA_TESTING_COMPLETE_DOCUMENTATION.md#troubleshooting](QA_TESTING_COMPLETE_DOCUMENTATION.md)

---

## 📞 SUPPORT

### Documentation by Level

**Beginners**:
1. Start: [QA_TESTING_QUICK_REFERENCE.md](QA_TESTING_QUICK_REFERENCE.md)
2. Then: [QA_TESTING_SYSTEM_README.md](QA_TESTING_SYSTEM_README.md)

**Intermediate**:
1. Read: [QA_TESTING_COMPLETE_DOCUMENTATION.md](QA_TESTING_COMPLETE_DOCUMENTATION.md)
2. Review: Specific test files in `tests/Feature/`

**Advanced**:
1. Study: Factory patterns in `database/factories/`
2. Review: CI workflows in `.github/workflows/`
3. Extend: Create custom tests/factories

**Project Managers**:
1. See: [QA_TESTING_IMPLEMENTATION_COMPLETE.md](QA_TESTING_IMPLEMENTATION_COMPLETE.md)
2. Review: [QA_REGRESSION_CHECKLIST.md](QA_REGRESSION_CHECKLIST.md)

---

## ✅ CHECKLIST FOR NEW TEAM MEMBERS

- [ ] Read [QA_TESTING_SYSTEM_README.md](QA_TESTING_SYSTEM_README.md)
- [ ] Run: `php artisan test` locally
- [ ] Review one test suite: [tests/Feature/RouteHealthCheckTest.php](tests/Feature/RouteHealthCheckTest.php)
- [ ] Review one factory: [database/factories/UserFactory.php](database/factories/UserFactory.php)
- [ ] Generate coverage report: `php artisan test --coverage --coverage-html=coverage-report`
- [ ] Understand: CI workflows in `.github/workflows/`
- [ ] Bookmark: [QA_TESTING_QUICK_REFERENCE.md](QA_TESTING_QUICK_REFERENCE.md)

---

**Last Updated**: February 4, 2026  
**Total Files**: 32  
**Total Lines**: 3,750+  
**Status**: ✅ Production Ready
