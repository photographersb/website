# QA Testing Quick Reference

## 🚀 Quick Start (5 minutes)

```bash
# Setup test environment
cp .env.example .env.testing
php artisan key:generate --env=testing

# Create test database
mysql -u root -p -e "CREATE DATABASE photographar_test;"

# Run migrations and seed
php artisan migrate --env=testing
php artisan db:seed --env=testing

# Run all tests
php artisan test

# View coverage report
php artisan test --coverage --coverage-html=coverage-report
open coverage-report/index.html
```

## 📋 Test Command Cheatsheet

```bash
# Run all tests
php artisan test

# Run specific suite
php artisan test tests/Feature/RouteHealthCheckTest.php

# Run with verbose output
php artisan test -v

# Run with coverage
php artisan test --coverage

# Run specific test method
php artisan test --filter=test_admin_can_create_competition

# Run tests containing keyword
php artisan test --filter=Competition

# Run in parallel (faster)
php artisan test --parallel

# Stop after first failure
php artisan test --stop-on-failure

# Run with detailed profiling
php artisan test --profile
```

## 🎯 Test Coverage by Feature

| Feature | Test Class | Tests | Status |
|---------|-----------|-------|--------|
| Route Health | RouteHealthCheckTest | 12 | ✅ Ready |
| Competition CRUD | CompetitionAdminCrudTest | 7 | ✅ Ready |
| Submissions & Voting | CompetitionSubmissionVotingTest | 7 | ✅ Ready |
| Event CRUD | EventAdminCrudTest | 8 | ✅ Ready |
| Registration & Attendance | EventRegistrationAttendanceTest | 8 | ✅ Ready |
| Verification Flow | VerificationFlowTest | 7 | ✅ Ready |
| Booking Flow | BookingFlowTest | 8 | ✅ Ready |
| Judge Scoring | JudgeScoringFlowTest | 7 | ✅ Ready |
| **TOTAL** | **8 Suites** | **64** | **✅ Ready** |

## 🏭 Factories At A Glance

```php
// Users
User::factory()->admin()->create();
User::factory()->judge()->create();
User::factory()->photographer()->create();
User::factory()->client()->create();
User::factory()->suspended()->create();
User::factory()->unverified()->create();

// Models
Category::factory()->create();
Location::factory()->create();
Photographer::factory()->verified()->create();
Photographer::factory()->featured()->create();

// Competitions
Competition::factory()->published()->create();
Competition::factory()->draft()->create();
Competition::factory()->upcoming()->create();
Competition::factory()->closed()->create();

// Events
Event::factory()->free()->create();
Event::factory()->paid()->create();
Event::factory()->online()->create();
Event::factory()->upcoming()->create();
Event::factory()->past()->create();

// Requests
VerificationRequest::factory()->pending()->create();
VerificationRequest::factory()->approved()->create();
VerificationRequest::factory()->rejected()->create();

BookingRequest::factory()->create();
BookingRequest::factory()->accepted()->create();
BookingRequest::factory()->rejected()->create();
BookingRequest::factory()->confirmed()->create();

// Votes & Submissions
CompetitionSubmission::factory()->approved()->create();
CompetitionSubmission::factory()->finalist()->create();
CompetitionSubmission::factory()->winner()->create();
CompetitionVote::factory()->create();
```

## 🌱 Seeders

```bash
# Seed Bangladesh divisions (8 cities)
php artisan db:seed --class=Database\\Seeders\\BangladeshDivisionSeeder

# Seed photography categories (12 categories)
php artisan db:seed --class=Database\\Seeders\\PhotographyCategorySeeder

# Seed complete test dataset
php artisan db:seed --class=Database\\Seeders\\TestDataSeeder

# Seed everything
php artisan db:seed
```

## 📊 Coverage Goals

```
Minimum targets:
- Lines: 80%
- Methods: 85%  
- Classes: 90%

Target goals:
- Lines: 90%
- Methods: 95%
- Classes: 98%
```

## 🔍 Debug Common Issues

```bash
# Test fails to run?
composer dump-autoload

# Database connection error?
mysql -u root -p -e "SELECT 1"

# Migration error?
php artisan migrate:reset --env=testing

# Factory not found?
composer dump-autoload && php artisan test

# Tests pass locally but fail in CI?
# Check .env.testing matches CI configuration
```

## 🎓 Test Patterns

### Testing Authenticated Routes
```php
$response = $this->actingAs($user)->get('/admin/dashboard');
```

### Testing API Endpoints
```php
$response = $this->postJson('/api/v1/competitions', $data);
```

### Testing Authorization
```php
$this->assertEquals(403, $response->getStatusCode());
```

### Testing Database Changes
```php
$this->assertDatabaseHas('users', ['email' => 'test@test.com']);
$this->assertDatabaseMissing('users', ['id' => 999]);
```

### Testing with Assertions
```php
$this->assertCount(5, $response->json('data'));
$this->assertArrayHasKey('id', $response->json('data')[0]);
```

## 📈 CI/CD Status

**Main Workflow**: `.github/workflows/tests.yml`
- Runs on: push to main/develop, PR to these branches
- Duration: ~5-10 minutes
- Coverage: Uploaded to Codecov

**Regression Workflow**: `.github/workflows/regression-tests.yml`  
- Runs: Daily at 2 AM UTC
- Coverage: HTML report artifact
- Alerts: Slack notification on failure

## 🚨 Pre-Deployment Checklist

- [ ] All tests pass locally: `php artisan test`
- [ ] Coverage acceptable: `php artisan test --coverage`
- [ ] Code style clean: `./vendor/bin/pint`
- [ ] No migrations pending: `php artisan migrate:status`
- [ ] No uncommitted changes: `git status`
- [ ] CI workflow passing on main branch
- [ ] Regression tests pass (check GitHub Actions)

## 📝 Common Test Methods

```php
// Assertions
$this->assertEquals(200, $response->getStatusCode());
$this->assertTrue($condition);
$this->assertNull($value);
$this->assertNotNull($value);
$this->assertContains($needle, $haystack);
$this->assertArrayHasKey('id', $data);
$this->assertCount(5, $data);

// Database
$this->assertDatabaseHas('users', ['email' => 'test@test.com']);
$this->assertDatabaseMissing('users', ['email' => 'deleted@test.com']);

// JSON API
$response->assertJson(['status' => 'success']);
$response->assertJsonPath('data.0.id', 1);
```

## 🔗 Key Files

- **Tests**: `tests/Feature/*.php`
- **Factories**: `database/factories/*.php`
- **Seeders**: `database/seeders/*.php`
- **Config**: `phpunit.xml`
- **CI Workflows**: `.github/workflows/*.yml`
- **Documentation**: `QA_TESTING_COMPLETE_DOCUMENTATION.md`

## 💡 Pro Tips

1. **Run tests during development** - catch bugs early
2. **Use factories for realistic data** - don't hardcode
3. **Test both success AND failure** - edge cases matter
4. **Keep tests focused** - one test = one concern
5. **Use descriptive names** - `test_admin_can_create_competition` not `test_create`
6. **Clean up after tests** - don't leave data behind
7. **Check coverage reports** - find untested code
8. **Monitor CI builds** - fix failures quickly

## 📞 Need Help?

1. Check test output: `php artisan test -v`
2. Review test class comments
3. Read QA_TESTING_COMPLETE_DOCUMENTATION.md
4. Check Laravel docs: laravel.com/docs/11.x/testing
5. Review factory/seeder code for examples
