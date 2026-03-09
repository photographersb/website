# Today's Work Scan - 2026-03-09

## Step 1 - What Changed

### Change Volume (top-level)
- public: 321
- resources: 66
- app: 65
- database: 23
- routes: 1
- reports: 6

### Build Artifact Churn
- `public/build/**` + `public/build/manifest.json`: 317 changed paths.
- This should stay isolated from source commits unless intentionally shipping a build snapshot.

### Source Hotspots
- Certificate domain expansion:
- controllers/services/models/jobs for issuance, templates, automation.
- admin certificate UI pages changed heavily.

- New module families introduced (mostly untracked/new files):
- Community (`CommunityController`, discussion/group/report models, moderation).
- Growth (`GrowthController`, referrals/share logs/invites models).
- Learning (`LearningController`, courses/lessons/enrollments models).

- Cross-cutting frontend updates:
- `resources/css/app.css` design system expansion (`sb-ui-*` classes).
- `resources/js/App.vue` navigation updates.
- many public/admin pages migrated to shared UI classes.

- Security/ops file touched:
- `public/quick-login.php` now guarded by local/debug/local-request checks.

## Step 2 - Suggested Commit Split (in order)

### Batch A - Security and Ops Guardrails
- Include:
- `public/quick-login.php`
- any directly related route/controller safety adjustments only.
- Exclude:
- design/UI/migration files.

### Batch B - Certificate Platform Backend
- Include:
- `app/Http/Controllers/Admin/CertificateController.php`
- `app/Http/Controllers/Api/Admin/CertificateController.php`
- `app/Http/Controllers/Api/Admin/CertificateTemplateController.php`
- `app/Jobs/IssueCertificateForAttendanceJob.php`
- `app/Jobs/GenerateCertificateAssetsJob.php`
- `app/Models/Certificate*.php`
- `app/Services/Certificate*.php`
- certificate-focused migrations only.

### Batch C - Certificate Admin Frontend
- Include:
- `resources/js/Pages/Admin/Certificates/**`
- any directly required certificate UI components.

### Batch D - Community Module
- Include:
- `app/Http/Controllers/Api/CommunityController.php`
- `app/Http/Controllers/Api/Admin/CommunityModerationController.php`
- `app/Models/Community*.php`
- community migrations
- `resources/js/Pages/Admin/CommunityModeration.vue`
- `resources/js/Pages/CommunityHub.vue`

### Batch E - Growth Module
- Include:
- `app/Http/Controllers/Api/GrowthController.php`
- `app/Http/Controllers/Api/Admin/GrowthDashboardController.php`
- `app/Models/Referral*.php`, `ShareLog.php`, `GrowthInvite.php`
- growth migrations
- `resources/js/Pages/Admin/GrowthDashboard.vue`
- `resources/js/Pages/TopReferrers.vue`

### Batch F - Learning Module
- Include:
- `app/Http/Controllers/Api/LearningController.php`
- `app/Http/Controllers/Api/Admin/LearningAdminController.php`
- `app/Models/Learning*.php`
- learning migrations
- `resources/js/Pages/LearnHub.vue`
- `resources/js/Pages/LearningDashboard.vue`
- `resources/js/Pages/Admin/LearningManagement.vue`

### Batch G - Shared Design System Migration
- Include:
- `resources/css/app.css`
- `resources/js/App.vue`
- shared UI components (`resources/js/components/ui/**`)
- pages updated only for class migration.

### Batch H - Build Output Snapshot (optional)
- Include only if deployment process requires committed build assets:
- `public/build/**`
- `public/build/manifest.json`

## Step 3 - Immediate Risk Notes
- Large mixed-scope tree risks accidental regressions if committed in one pass.
- New modules (community/growth/learning) add many migrations; verify migration order and foreign keys before merge.
- Design-system pass touches many pages; run visual smoke checks before final release commit.

## Step 4 - Quick Execute Checklist
- [ ] Commit Batch A first (small, high-safety).
- [ ] Run `php -l` on touched PHP files per batch.
- [ ] Run targeted diagnostics per batch.
- [ ] Run `npm run build` after frontend batches.
- [ ] Keep Batch H isolated from logic commits.
