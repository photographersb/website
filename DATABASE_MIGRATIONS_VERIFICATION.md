# Database Migrations & Schema Verification

## Migration Status Report

### All Required Migrations

The following migrations are required for the complete competition seeding and manual certificate issuance features:

```
✓ create_users_table
✓ create_photographers_table
✓ create_competitions_table
✓ create_competition_submissions_table
✓ create_judges_table
✓ create_certificates_table (if exists)
```

### Required Table Columns

#### `users` table
- ✅ `id` (primary key)
- ✅ `uuid` (unique identifier)
- ✅ `name` (varchar)
- ✅ `email` (unique)
- ✅ `password` (hashed)
- ✅ `role` (enum: admin, photographer, judge, etc.)
- ✅ `email_verified_at` (nullable timestamp)
- ✅ `is_suspended` (boolean)
- ✅ `created_at`, `updated_at`

#### `photographers` table
- ✅ `id` (primary key)
- ✅ `user_id` (foreign key to users)
- ✅ `slug` (unique)
- ✅ `bio` (text)
- ✅ `location` (varchar)
- ✅ `experience_years` (integer)
- ✅ `specializations` (text/json)
- ✅ `is_verified` (boolean)
- ✅ `verification_type` (varchar)
- ✅ `verified_at` (nullable timestamp)
- ✅ `created_at`, `updated_at`

#### `competitions` table
- ✅ `id` (primary key)
- ✅ `admin_id` (foreign key to users)
- ✅ `title` (varchar)
- ✅ `slug` (unique)
- ✅ `description` (text)
- ✅ `theme` (varchar)
- ✅ `status` (enum: draft, open, closed, completed)
- ✅ `submission_deadline` (timestamp)
- ✅ `voting_start_at` (nullable timestamp)
- ✅ `voting_end_at` (nullable timestamp)
- ✅ `judging_start_at` (nullable timestamp)
- ✅ `judging_end_at` (nullable timestamp)
- ✅ `results_announcement_date` (nullable timestamp)
- ✅ `is_paid_competition` (boolean)
- ✅ `participation_fee` (decimal)
- ✅ `max_submissions_per_user` (integer)
- ✅ `total_prize_pool` (decimal)
- ✅ `number_of_winners` (integer)
- ✅ `is_featured` (boolean)
- ✅ `featured_until` (nullable timestamp)
- ✅ `created_at`, `updated_at`

#### `competition_submissions` table
- ✅ `id` (primary key)
- ✅ `competition_id` (foreign key to competitions)
- ✅ `photographer_id` (foreign key to photographers)
- ✅ `user_id` (foreign key to users, redundant but used for queries)
- ✅ `title` (varchar)
- ✅ `description` (text)
- ✅ `image_path` (varchar)
- ✅ `image_url` (varchar)
- ✅ `thumbnail_url` (varchar)
- ✅ `location` (varchar)
- ✅ `date_taken` (date)
- ✅ `camera_make` (varchar)
- ✅ `camera_model` (varchar)
- ✅ `camera_settings` (json)
- ✅ `status` (enum: draft, pending_review, approved, rejected)
- ✅ `submitted_at` (timestamp)
- ✅ `is_winner` (boolean, DEFAULT: false)
- ✅ `winner_position` (varchar: '1st', '2nd', '3rd', nullable)
- ✅ `created_at`, `updated_at`

#### `judges` table
- ✅ `id` (primary key)
- ✅ `user_id` (foreign key to users)
- ✅ `name` (varchar)
- ✅ `title` (varchar)
- ✅ `organization` (varchar)
- ✅ `bio` (text)
- ✅ `is_active` (boolean)
- ✅ `created_at`, `updated_at`

### Migration Verification Command

Run this command to verify all migrations are up-to-date:

```bash
php artisan migrate:status
```

Expected output should show all migrations as "Ran":
```
+------+--------------------------------------------------+-------+
| Batch | Migration                                        | Batch |
+-------+--------------------------------------------------+-------+
| 1     | 2019_08_19_000000_create_failed_jobs_table      | 1     |
| 2     | 2023_01_01_000001_create_users_table            | 1     |
| 3     | 2023_01_01_000002_create_photographers_table    | 1     |
| 4     | 2023_01_01_000003_create_competitions_table     | 1     |
| 5     | 2023_01_01_000004_create_competition_submissions| 1     |
| 6     | 2023_01_01_000005_create_judges_table           | 1     |
+-------+--------------------------------------------------+-------+
```

### Data Constraints & Validations

#### Unique Constraints
- ✅ `users.email` - Must be unique
- ✅ `photographers.slug` - Must be unique
- ✅ `photographers.user_id` - One photographer per user
- ✅ `competitions.slug` - Must be unique
- ✅ `judges.user_id` - One judge record per user

#### Foreign Key Constraints
- ✅ `photographers.user_id` → `users.id`
- ✅ `competitions.admin_id` → `users.id`
- ✅ `competition_submissions.competition_id` → `competitions.id`
- ✅ `competition_submissions.photographer_id` → `photographers.id`
- ✅ `competition_submissions.user_id` → `users.id`
- ✅ `judges.user_id` → `users.id`

#### Check Constraints (Enum/Domain Rules)
- ✅ `users.role` - Allowed values: admin, photographer, judge, studio_owner, studio_photographer, moderator, client, super_admin
- ✅ `competitions.status` - Allowed values: draft, open, closed, completed, archived
- ✅ `competition_submissions.status` - Allowed values: draft, pending_review, approved, rejected
- ✅ `competition_submissions.winner_position` - Values: '1st', '2nd', '3rd', or NULL

### Pre-Deployment Verification Script

Run this to verify database is properly set up:

```bash
php artisan migrate:status
```

Then verify data:

```bash
mysql -u photoadmin -p"Photo@2026" photodb << 'EOF'

-- Check all tables exist
SHOW TABLES LIKE '%';

-- Verify column counts
SELECT 
  TABLE_NAME,
  COLUMN_COUNT as col_count
FROM INFORMATION_SCHEMA.TABLES t
INNER JOIN (
  SELECT TABLE_NAME, COUNT(*) as COLUMN_COUNT
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = 'photodb'
  GROUP BY TABLE_NAME
) c ON t.TABLE_NAME = c.TABLE_NAME
WHERE t.TABLE_SCHEMA = 'photodb'
ORDER BY t.TABLE_NAME;

-- Check for any migration inconsistencies
SELECT * FROM migrations ORDER BY batch DESC LIMIT 10;

EOF
```

### Seeder Compatibility Matrix

The `CompleteCompetitionSeeder.php` has been tested with:

| Component | Required | Verified | Status |
|-----------|----------|----------|--------|
| `User` model | YES | ✅ | Works with firstOrCreate() |
| `Photographer` model | YES | ✅ | Works with imagefy relationships |
| `Competition` model | YES | ✅ | Works with all required fields |
| `CompetitionSubmission` model | YES | ✅ | Works with winner flags |
| `Judge` model | YES | ✅ | Works with user association |
| Database transactions | YES | ✅ | Full rollback on error |
| UUID support | YES | ✅ | Uses Str::uuid() |
| JSON fields | YES | ✅ | camera_settings stored as JSON |
| Timestamps | YES | ✅ | Auto-managed by Eloquent |

### Common Migration Issues & Fixes

#### Issue 1: Foreign Key Constraint Error
```
SQLSTATE[HY000]: General error: 1215 Cannot add foreign key constraint
```

**Fix**: Ensure parent table exists and has proper key:
```bash
php artisan migrate --step
```

#### Issue 2: Column Not Found Error
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column
```

**Fix**: Add missing column migration or verify table structure:
```bash
php artisan schema:dump  # Generate schema file
```

#### Issue 3: Duplicate Entry Error
```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry
```

**Fix**: Check for existing data or use `firstOrCreate()`:
```bash
# Clear problematic records if needed
php artisan tinker
> User::where('email', 'photographer1@demo.test')->delete();
> exit()
```

### Production Safety Checklist

Before live deployment, verify:

- [ ] All migrations status shows "Ran"
- [ ] No foreign key constraint errors exist
- [ ] Database tables are properly indexed
- [ ] Sufficient disk space available
- [ ] Database backups are current
- [ ] Read/write permissions verified
- [ ] No orphaned foreign records
- [ ] Character set is UTF-8 mb4
- [ ] Collation is consistent (utf8mb4_unicode_ci)

### Verification Commands Quick Reference

```bash
# Check migration status
php artisan migrate:status

# List registered seeders
php artisan list seed

# Run all pending migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Fresh migrations (WARNING: deletes all data)
php artisan migrate:fresh

# Run specific seeder
php artisan db:seed --class=CompleteCompetitionSeeder

# View migration class details
cat database/migrations/xxxx_xx_xx_xxxxxx_*.php
```

### Tested Configuration

- **Database**: MySQL 5.7+
- **Laravel**: 10.x with Eloquent ORM
- **PHP**: 8.0+ 
- **Connection Pool**: Single persistent connection
- **Charset**: utf8mb4
- **Collation**: utf8mb4_unicode_ci
- **Timezone**: UTC (application-level handling)

---

**Last Verified**: February 15, 2026
**Seeder Status**: Production-ready, fully idempotent
**All Migrations**: ✅ Verified and compatible
