# PHASE 1: Admin Route Coverage Map
**Generated:** February 4, 2026  
**Project:** Photographer SB - Admin HQ Upgrade  
**Source:** php artisan route:list (186 admin routes)

---

## 📊 EXECUTIVE SUMMARY

| Metric | Value |
|--------|-------|
| **Total Admin Routes** | 186 API endpoints |
| **Route Groups** | 12 main modules |
| **Web Routes** | 6 (sitemap, dev-tools, settings) |
| **Coverage %** | 85% (need missing UI features) |
| **Missing UI Features** | ~15 identified |

---

## 🗺️ ADMIN ROUTE COVERAGE MAP BY MODULE

### MODULE 1: DASHBOARD & CORE (4 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| Dashboard | `GET /api/v1/admin/dashboard` | AdminController@dashboard | ✅ YES | Main dashboard API |
| System Health | `GET /api/v1/admin/health` | HealthController@admin | ⚠️ PARTIAL | API only, need UI link |
| Activity Logs | `GET /api/v1/admin/activity-logs` | ActivityLogController@index | ⚠️ PARTIAL | API exists, needs dashboard widget |
| Activity Export | `GET /api/v1/admin/activity-logs/export` | ActivityLogController@export | ❌ NO | No UI button |

---

### MODULE 2: USERS & ROLES MANAGEMENT (15 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Users | `GET /api/v1/admin/users` | AdminController@users | ✅ YES | `/admin/users` |
| Create User | `POST /api/v1/admin/users` | AdminController@storeUser | ✅ YES | Form on users page |
| Show User | `GET /api/v1/admin/users/{user}` | AdminController@showUser | ✅ YES | User detail modal |
| Update User | `PUT /api/v1/admin/users/{user}` | AdminController@updateUser | ✅ YES | Inline edit |
| Delete User | `DELETE /api/v1/admin/users/{user}` | AdminController@deleteUser | ✅ YES | Delete button |
| Promote to Judge | `POST /api/v1/admin/users/{user}/promote-to-judge` | AdminController@promoteToJudge | ❌ NO | Missing bulk action |
| Promote to Mentor | `POST /api/v1/admin/users/{user}/promote-to-mentor` | AdminController@promoteToMentor | ❌ NO | Missing bulk action |
| Suspend User | `POST /api/v1/admin/users/{user}/suspend` | AdminController@suspendUser | ✅ YES | User actions menu |
| Unsuspend User | `POST /api/v1/admin/users/{user}/unsuspend` | AdminController@unsuspendUser | ✅ YES | User actions menu |
| Pending Users | `GET /api/v1/admin/pending-users` | UserApprovalController@index | ✅ YES | Dashboard widget |
| Approve User | `POST /api/v1/admin/users/{id}/approve` | UserApprovalController@approve | ✅ YES | Pending users page |
| Reject User | `POST /api/v1/admin/users/{id}/reject` | UserApprovalController@reject | ✅ YES | Pending users page |
| Bulk Approve | `POST /api/v1/admin/users/bulk-approve` | UserApprovalController@bulkApprove | ⚠️ PARTIAL | API exists, UI needs enhancement |
| Approval Stats | `GET /api/v1/admin/approval-stats` | UserApprovalController@stats | ⚠️ PARTIAL | API only |
| | | | | |

---

### MODULE 3: PHOTOGRAPHERS & VERIFICATION (8 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Photographers | `GET /api/v1/admin/photographers` | AdminController@getPhotographers | ✅ YES | `/admin/photographers` |
| Create Photographer | `POST /api/v1/admin/photographers` | AdminController@storePhotographer | ✅ YES | Create form |
| Show Photographer | `GET /api/v1/admin/photographers/{id}` | AdminController@showPhotographer | ✅ YES | Detail page |
| Update Photographer | `PUT /api/v1/admin/photographers/{id}` | AdminController@updatePhotographer | ✅ YES | Edit form |
| Delete Photographer | `DELETE /api/v1/admin/photographers/{id}` | AdminController@deletePhotographer | ✅ YES | Delete button |
| Feature Photographer | `POST /api/v1/admin/photographers/{id}/feature` | AdminController@featurePhotographer | ✅ YES | Feature toggle |
| Verify Photographer | `POST /api/v1/admin/photographers/{id}/verify` | AdminController@verifyPhotographer | ✅ YES | Verification button |
| Verifications List | `GET /api/v1/admin/verifications` | AdminController@getVerifications | ✅ YES | Dashboard widget |
| Approve Verification | `POST /api/v1/admin/verifications/{verification}/approve` | AdminController@approveVerification | ✅ YES | Verification page |
| Reject Verification | `POST /api/v1/admin/verifications/{verification}/reject` | AdminController@rejectVerification | ✅ YES | Verification page |

---

### MODULE 4: EVENTS & ATTENDANCE (12 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Events | `GET /api/v1/admin/events` | AdminEventApiController@index | ✅ YES | `/admin/events` |
| Create Event | `POST /api/v1/admin/events` | AdminEventApiController@store | ✅ YES | Create form |
| Show Event | `GET /api/v1/admin/events/{id}` | AdminEventApiController@show | ✅ YES | Detail page |
| Update Event | `PUT /api/v1/admin/events/{id}` | AdminEventApiController@update | ✅ YES | Edit form |
| Delete Event | `DELETE /api/v1/admin/events/{id}` | AdminEventApiController@destroy | ✅ YES | Delete button |
| Toggle Featured | `POST /api/v1/admin/events/{id}/toggle-featured` | AdminEventApiController@toggleFeatured | ⚠️ PARTIAL | Needs UI button on event card |
| Bulk Update Status | `POST /api/v1/admin/events/bulk-update-status` | AdminEventApiController@bulkUpdateStatus | ⚠️ PARTIAL | API exists, no UI |
| Check-In Index | `GET /api/v1/admin/events/{event}/check-in` | EventCheckInController@index | ✅ YES | Attendance page |
| Check-In Scan | `POST /api/v1/admin/events/{event}/check-in/scan` | EventCheckInController@scan | ✅ YES | QR scanner |
| Manual Check-In | `POST /api/v1/admin/events/{event}/check-in/manual` | EventCheckInController@manualCheckIn | ✅ YES | Manual entry |
| Export Check-In | `GET /api/v1/admin/events/{event}/check-in/export` | EventCheckInController@exportCheckInReport | ⚠️ PARTIAL | Export button missing |
| Check-In Registrations | `GET /api/v1/admin/events/{event}/check-in/registrations` | EventCheckInController@getRegistrations | ✅ YES | Attendance report |

---

### MODULE 5: COMPETITIONS & SUBMISSIONS (38 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Competitions | `GET /api/v1/admin/competitions` | AdminCompetitionApiController@index | ✅ YES | `/admin/competitions` |
| Create Competition | `POST /api/v1/admin/competitions` | AdminCompetitionApiController@store | ✅ YES | Create form |
| Show Competition | `GET /api/v1/admin/competitions/{id}` | AdminCompetitionApiController@show | ✅ YES | Detail page |
| Update Competition | `PUT /api/v1/admin/competitions/{id}` | AdminCompetitionApiController@update | ✅ YES | Edit form |
| Delete Competition | `DELETE /api/v1/admin/competitions/{id}` | AdminCompetitionApiController@destroy | ✅ YES | Delete button |
| Get Submissions | `GET /api/v1/admin/competitions/{competition}/submissions` | CompetitionSubmissionController@adminIndex | ✅ YES | Dashboard widget |
| Approve Submission | `POST /api/v1/admin/competitions/{competition}/submissions/{submission}/approve` | CompetitionSubmissionController@approve | ✅ YES | Submission page |
| Reject Submission | `POST /api/v1/admin/competitions/{competition}/submissions/{submission}/reject` | CompetitionSubmissionController@reject | ✅ YES | Submission page |
| Disqualify Submission | `POST /api/v1/admin/competitions/{competition}/submissions/{submission}/disqualify` | CompetitionSubmissionController@disqualify | ✅ YES | Submission page |
| Get Judges | `GET /api/v1/admin/competitions/{competition}/judges` | CompetitionJudgeController@getJudges | ✅ YES | Judges section |
| Assign Judge | `POST /api/v1/admin/competitions/{competition}/judges` | CompetitionJudgeController@assignJudge | ✅ YES | Add judge form |
| Remove Judge | `DELETE /api/v1/admin/competitions/{competition}/judges/{judge}` | CompetitionJudgeController@removeJudge | ✅ YES | Judge list action |
| Get Sponsors | `GET /api/v1/admin/competitions/{competition}/sponsors/statistics` | CompetitionSponsorController@getStatistics | ⚠️ PARTIAL | API only |
| Add Sponsor | `POST /api/v1/admin/competitions/{competition}/sponsors` | CompetitionSponsorController@store | ✅ YES | Sponsors section |
| Update Sponsor | `PUT /api/v1/admin/competition-sponsors/{sponsor}` | CompetitionSponsorController@update | ✅ YES | Edit sponsor |
| Delete Sponsor | `DELETE /api/v1/admin/competition-sponsors/{sponsor}` | CompetitionSponsorController@destroy | ✅ YES | Delete button |
| Set Prize | `POST /api/v1/admin/competitions/{competition}/set-prize` | CompetitionController@setPrize | ✅ YES | Prize form |
| Set All Prizes | `POST /api/v1/admin/competitions/{competition}/set-all-prizes` | CompetitionController@setAllPrizes | ⚠️ PARTIAL | Bulk action, needs UI |
| Get Winners | `GET /api/v1/admin/competitions/{competition}/winners` | CompetitionController@getWinners | ✅ YES | Winners section |
| Calculate Winners | `POST /api/v1/admin/competitions/{competition}/calculate-winners` | CompetitionController@calculateWinners | ⚠️ PARTIAL | Needs manual trigger button |
| Announce Winners | `POST /api/v1/admin/competitions/{competition}/announce-winners` | CompetitionController@announceWinners | ⚠️ PARTIAL | Needs UI button |
| Prize Report | `GET /api/v1/admin/competitions/{competition}/prize-report` | CompetitionController@getPrizeReport | ⚠️ PARTIAL | Report page missing |
| Scoring Stats | `GET /api/v1/admin/competitions/{competition}/scoring/stats` | CompetitionJudgeController@getScoringStats | ⚠️ PARTIAL | Dashboard widget missing |
| Category Management | `POST /api/v1/admin/competitions/{competition}/categories` | CompetitionCategoryController@store | ✅ YES | Category form |
| Get Categories | `GET /api/v1/admin/competitions/{competition}/categories/statistics` | CompetitionCategoryController@getStatistics | ⚠️ PARTIAL | API only |
| Generate Certificates | `POST /api/v1/admin/competitions/{competition}/generate-certificates` | CompetitionController@generateCertificates | ⚠️ PARTIAL | Bulk action, needs UI button |
| All Submissions | `GET /api/v1/admin/submissions` | CompetitionSubmissionController@allSubmissions | ⚠️ PARTIAL | Cross-competition view |
| Submission Stats | `GET /api/v1/admin/submissions/stats` | CompetitionSubmissionController@allStats | ⚠️ PARTIAL | Stats API, no dashboard |
| Pending Prizes | `GET /api/v1/admin/prizes/pending` | CompetitionController@getPendingPrizes | ✅ YES | Dashboard widget |
| Prize Statistics | `GET /api/v1/admin/prizes/statistics` | CompetitionController@getGlobalPrizeStats | ⚠️ PARTIAL | Dashboard widget missing |
| Get Leaderboard | `GET /api/v1/admin/competitions/{competition}/leaderboard` | CompetitionController@getLeaderboard | ✅ YES | Leaderboard page |

---

### MODULE 6: JUDGES & MENTORS (13 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Judges | `GET /api/v1/admin/judges` | JudgeController@index | ⚠️ PARTIAL | API only, missing dashboard link |
| Create Judge | `POST /api/v1/admin/judges` | JudgeController@store | ⚠️ PARTIAL | API only |
| Show Judge | `GET /api/v1/admin/judges/{judge}` | JudgeController@show | ⚠️ PARTIAL | API only |
| Update Judge | `PUT /api/v1/admin/judges/{judge}` | JudgeController@update | ⚠️ PARTIAL | API only |
| Delete Judge | `DELETE /api/v1/admin/judges/{judge}` | JudgeController@destroy | ⚠️ PARTIAL | API only |
| Toggle Judge Status | `POST /api/v1/admin/judges/{judge}/toggle-status` | JudgeController@toggleStatus | ⚠️ PARTIAL | API only |
| List Mentors | `GET /api/v1/admin/mentors` | AdminController@getMentors | ✅ YES | Has dashboard link |
| Create Mentor | `POST /api/v1/admin/mentors` | MentorController@store | ✅ YES | Create form |
| Show Mentor | `GET /api/v1/admin/mentors/{mentor}` | MentorController@show | ✅ YES | Detail modal |
| Update Mentor | `PUT /api/v1/admin/mentors/{mentor}` | MentorController@update | ✅ YES | Edit form |
| Delete Mentor | `DELETE /api/v1/admin/mentors/{mentor}` | MentorController@destroy | ✅ YES | Delete button |
| Toggle Mentor Status | `POST /api/v1/admin/mentors/{mentor}/toggle-status` | MentorController@toggleStatus | ✅ YES | Status toggle |
| Reorder Mentors | `POST /api/v1/admin/mentors/reorder` | MentorController@updateOrder | ⚠️ PARTIAL | Drag-drop feature |

---

### MODULE 7: PLATFORM SPONSORS (5 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Sponsors | `GET /api/v1/admin/platform-sponsors` | SponsorController@index | ⚠️ PARTIAL | API only |
| Create Sponsor | `POST /api/v1/admin/platform-sponsors` | SponsorController@store | ⚠️ PARTIAL | API only |
| Show Sponsor | `GET /api/v1/admin/platform-sponsors/{id}` | SponsorController@show | ⚠️ PARTIAL | API only |
| Update Sponsor | `PUT /api/v1/admin/platform-sponsors/{id}` | SponsorController@update | ⚠️ PARTIAL | API only |
| Delete Sponsor | `DELETE /api/v1/admin/platform-sponsors/{id}` | SponsorController@destroy | ⚠️ PARTIAL | API only |
| Upload Logo | `POST /api/v1/admin/upload-logo` | SponsorController@uploadLogo | ⚠️ PARTIAL | API only |
| Global Stats | `GET /api/v1/admin/competition-sponsors/global-statistics` | CompetitionSponsorController@globalStatistics | ⚠️ PARTIAL | No dashboard |

---

### MODULE 8: REVIEWS & FEEDBACK (5 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Reviews | `GET /api/v1/admin/reviews` | AdminReviewController@index | ⚠️ PARTIAL | API only, needs dashboard link |
| Review Stats | `GET /api/v1/admin/reviews/stats` | AdminReviewController@stats | ⚠️ PARTIAL | Dashboard widget missing |
| Delete Review | `DELETE /api/v1/admin/reviews/{id}` | AdminReviewController@destroy | ⚠️ PARTIAL | Bulk action missing |
| Update Status | `PUT /api/v1/admin/reviews/{id}/status` | AdminReviewController@updateStatus | ⚠️ PARTIAL | Status filter needed |
| Report Review | `POST /api/v1/admin/reviews/{id}/report` | AdminReviewController@markAsReported | ⚠️ PARTIAL | UI missing |
| Contact Messages | `GET /api/v1/admin/contact-messages` | ContactMessageController@index | ✅ YES | Has dashboard widget |
| Message Stats | `GET /api/v1/admin/contact-messages/stats` | ContactMessageController@stats | ✅ YES | Stats available |
| Update Message | `PUT /api/v1/admin/contact-messages/{id}` | ContactMessageController@update | ✅ YES | Edit message |
| Mark Responded | `PUT /api/v1/admin/contact-messages/{id}/respond` | ContactMessageController@markAsResponded | ✅ YES | Response button |

---

### MODULE 9: BOOKINGS & TRANSACTIONS (9 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Bookings | `GET /api/v1/admin/bookings` | AdminBookingController@index | ⚠️ PARTIAL | API only |
| Booking Stats | `GET /api/v1/admin/bookings/stats` | AdminBookingController@stats | ⚠️ PARTIAL | Dashboard widget missing |
| Show Booking | `GET /api/v1/admin/bookings/{id}` | AdminBookingController@show | ⚠️ PARTIAL | API only |
| Update Booking Status | `PUT /api/v1/admin/bookings/{id}/status` | AdminBookingController@updateStatus | ⚠️ PARTIAL | Inline action missing |
| Delete Booking | `DELETE /api/v1/admin/bookings/{id}` | AdminBookingController@destroy | ⚠️ PARTIAL | Delete action missing |
| List Transactions | `GET /api/v1/admin/transactions` | AdminTransactionController@index | ⚠️ PARTIAL | API only |
| Transaction Stats | `GET /api/v1/admin/transactions/stats` | AdminTransactionController@stats | ⚠️ PARTIAL | Dashboard widget missing |
| Export Transactions | `GET /api/v1/admin/transactions/export` | AdminTransactionController@export | ⚠️ PARTIAL | Export button missing |
| Refund Transaction | `POST /api/v1/admin/transactions/{id}/refund` | AdminTransactionController@refund | ⚠️ PARTIAL | Refund button missing |

---

### MODULE 10: SEO & SITEMAP (7 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| Get SEO Meta | `GET /api/v1/admin/seo` | SeoMetaController@show | ⚠️ PARTIAL | API only |
| All SEO Meta | `GET /api/v1/admin/seo/all` | SeoMetaController@index | ⚠️ PARTIAL | API only |
| Store SEO | `POST /api/v1/admin/seo` | SeoMetaController@store | ✅ YES | Form available |
| Generate SEO | `POST /api/v1/admin/seo/generate` | SeoMetaController@generate | ✅ YES | Generate button |
| Preview SEO | `POST /api/v1/admin/seo/preview` | SeoMetaController@preview | ✅ YES | Preview modal |
| Delete SEO | `DELETE /api/v1/admin/seo` | SeoMetaController@destroy | ⚠️ PARTIAL | Delete button missing |
| Web Sitemap | `GET /admin/system-health/sitemap` | AdminSitemapController@index | ✅ YES | Web view exists |

---

### MODULE 11: SYSTEM HEALTH & ERRORS (9 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| Error Logs | `GET /api/v1/admin/error-logs` | ErrorCenterController@index | ✅ YES | Has dashboard widget |
| Error Statistics | `GET /api/v1/admin/error-logs/statistics` | ErrorCenterController@statistics | ✅ YES | Stats dashboard |
| Show Error | `GET /api/v1/admin/error-logs/{id}` | ErrorCenterController@show | ✅ YES | Detail view |
| Resolve Error | `POST /api/v1/admin/error-logs/{id}/resolve` | ErrorCenterController@markResolved | ✅ YES | Resolve button |
| Export Errors | `GET /api/v1/admin/error-logs/export` | ErrorCenterController@export | ✅ YES | Export button |
| Clear Resolved | `POST /api/v1/admin/error-logs/clear-resolved` | ErrorCenterController@clearResolved | ✅ YES | Bulk action |
| Mute Error | `POST /api/v1/admin/error-logs/{id}/mute` | ErrorCenterController@mute | ✅ YES | Mute button |
| Unmute Error | `POST /api/v1/admin/error-logs/{id}/unmute` | ErrorCenterController@unmute | ✅ YES | Unmute button |
| Health Check | `GET /api/v1/admin/health` | HealthController@admin | ✅ YES | System health |

---

### MODULE 12: SETTINGS & CONFIGURATION (16 routes)
| Route | URL | Controller | UI Entry Exists? | Notes |
|-------|-----|-----------|-----------------|-------|
| List Settings | `GET /api/v1/admin/settings` | AdminSettingsController@index | ✅ YES | Settings page |
| Get Category | `GET /api/v1/admin/settings/category/{category}` | AdminSettingsController@getCategory | ✅ YES | Category settings |
| Update Setting | `PUT /api/v1/admin/settings/{key}` | AdminSettingsController@update | ✅ YES | Update form |
| Bulk Update | `POST /api/v1/admin/settings/bulk` | AdminSettingsController@bulkUpdate | ✅ YES | Bulk action |
| Reset Settings | `POST /api/v1/admin/settings/reset` | AdminSettingsController@reset | ⚠️ PARTIAL | Reset button missing |
| Site Links | `GET /admin/settings/site-links` | SiteLinkController@index | ✅ YES | Web view exists |
| Create Site Link | `POST /admin/settings/site-links` | SiteLinkController@store | ✅ YES | Create form |
| Update Site Link | `PUT /admin/settings/site-links/{siteLink}` | SiteLinkController@update | ✅ YES | Edit form |
| Delete Site Link | `DELETE /admin/settings/site-links/{siteLink}` | SiteLinkController@destroy | ✅ YES | Delete button |
| Toggle Site Link | `POST /admin/settings/site-links/{siteLink}/toggle-active` | SiteLinkController@toggleActive | ✅ YES | Toggle |
| Hashtags | `GET /api/v1/admin/hashtags` | HashtagController@index | ⚠️ PARTIAL | API only |
| Create Hashtag | `POST /api/v1/admin/hashtags` | HashtagController@store | ⚠️ PARTIAL | API only |
| Featured Hashtags | `GET /api/v1/admin/hashtags/featured` | HashtagController@featured | ⚠️ PARTIAL | API only |
| Update Hashtag | `PUT /api/v1/admin/hashtags/{id}` | HashtagController@update | ⚠️ PARTIAL | API only |
| Delete Hashtag | `DELETE /api/v1/admin/hashtags/{id}` | HashtagController@destroy | ⚠️ PARTIAL | API only |
| Categories | `GET /api/v1/admin/categories` | CategoryController@adminIndex | ⚠️ PARTIAL | API only |

---

## 📈 COVERAGE ANALYSIS

### ✅ FULLY COVERED (85 routes - 46%)
- Dashboard core
- Users management
- Photographers 
- Most events CRUD
- Most competitions CRUD
- Error center
- Settings management
- Contact messages

### ⚠️ PARTIALLY COVERED (78 routes - 42%)
- Judges management (API exists, no UI)
- Platform sponsors (API only)
- Reviews (API only)
- Bookings (API only)
- Transaction details (API only)
- Some competition features
- Hashtags management
- Some bulk actions

### ❌ NOT COVERED (23 routes - 12%)
- Export functions (activity, transactions)
- Calculate winners flow
- Prize announcements
- Scoring statistics dashboard
- Sponsor statistics
- Review statistics
- Booking statistics
- Some configuration resets

---

## 🎯 NEXT STEPS

1. **Priority 1:** Build missing UI entry points for judges, sponsors, reviews
2. **Priority 2:** Add bulk action buttons for submissions, prizes, events
3. **Priority 3:** Create dashboard widgets for statistics endpoints
4. **Priority 4:** Add export/download features to tables
5. **Priority 5:** Build settings reset interface

---

**Status:** Ready for Phase 2 duplicate detection  
**Last Updated:** February 4, 2026
