# Tomorrow TODO - 2026-03-09

## Scan Summary (2026-03-08 snapshot)
- Working tree is heavily dirty:
- `public`: 321 changed files (mostly build artifacts and manifest churn).
- `resources`: 66 changed files (large UI/design-system migration in progress).
- `app`: 65 changed files (certificate/growth/community backend expansion).
- `database`: 23 changed files.
- Latest stabilization commits are clean and focused (`transactions`, `sponsors`, `settings`, `notifications`).
- Current diagnostics:
- Analyzer warnings in `app/Http/Controllers/Api/Admin/NotificationController.php` for `notifications()` method resolution (static analysis/type issue, not syntax/runtime break from lint checks).
- Inline TODO markers still unresolved in backend:
- `app/Http/Controllers/Api/AuthController.php`: OTP provider still placeholder.
- `app/Http/Controllers/Api/CompetitionController.php`: winner notification dispatch TODO.
- `app/Services/FraudDetectionService.php`: IP blacklist check TODO.

## Priority Plan For Tomorrow

### P0 - Repo Hygiene and Release Safety
- [ ] Split generated assets from source changes before any new feature commits:
- Keep `public/build/**` out of stabilization commits unless intentionally shipping a production build artifact update.
- [ ] Create scoped commit groups from current dirty tree:
- `certificate-automation` (controllers/models/jobs/services + related pages)
- `community-growth-learning` (new controllers/models/routes/pages)
- `design-system-pass` (`resources/css/app.css`, shared UI classes, affected pages)
- [ ] Re-run and confirm frontend build after grouping (`npm run build`) and ensure no mixed-scope commit contamination.

### P1 - Stabilization Follow-ups (Backend)
- [ ] `Admin NotificationController` analyzer cleanup:
- Replace direct `Auth::user()->notifications()` callsites with typed guard variables or helper methods to eliminate false-positive compile diagnostics.
- [ ] Transactions endpoint regression check:
- Verify `/admin/transactions`, `/admin/transactions/stats`, `/admin/transactions/export`, `/admin/transactions/{id}` with both `booking_*` and `event_*` identifiers.
- [ ] Close backend TODOs that block production readiness:
- Implement real OTP sender path in `AuthController`.
- Implement winner notification dispatch path in `CompetitionController`.
- Implement IP blacklist check in `FraudDetectionService`.

### P1 - Certificates Module Completion
- [ ] Validate new certificate flows end-to-end:
- Template CRUD
- Manual issuance
- Automation rules
- Download/verify routes
- [ ] Add/adjust API authorization checks for all newly introduced certificate admin endpoints.
- [ ] Add regression tests for issuance + revocation + regeneration paths.

### P2 - Frontend Consistency and UX Safety
- [ ] Finish and isolate the uncommitted `resources/js/Pages/Events.vue` route-fallback patch in a dedicated commit.
- [ ] Validate all pages touched by design-token migration (`sb-ui-*` classes):
- Check responsive layout, focus states, form controls, and button disabled states.
- [ ] Verify social share/SEO patches for competition pages:
- `resources/js/Pages/CompetitionDetail.vue`
- `resources/js/Pages/CompetitionGallery.vue`
- Confirm canonical/meta/schema values and no duplicate head tags.

### P2 - QA Matrix (Smoke)
- [ ] Admin smoke:
- Dashboard, Transactions, Notifications, Certificates, Settings.
- [ ] Public smoke:
- Events list/detail/tickets, competition detail/gallery/submit.
- [ ] Role smoke:
- admin, photographer, client; verify access boundaries still hold.

## Suggested Execution Order (Tomorrow)
1. Repo hygiene split (P0) and commit grouping.
2. Backend TODO closures + admin notification analyzer cleanup.
3. Certificates end-to-end verification and auth hardening.
4. Events.vue isolated commit + frontend smoke pass.
5. Final docs update in `reports/FINAL_PLATFORM_AUDIT_2026-03-08.md` with completed deltas.

## Definition of Done (Tomorrow)
- No mixed-scope commits.
- No new diagnostics in touched files.
- Frontend build passes.
- High-risk TODOs (OTP, winner notification, fraud blacklist) are implemented or explicitly ticketed with blockers.
- Audit report continuation section updated with all new commit hashes.
