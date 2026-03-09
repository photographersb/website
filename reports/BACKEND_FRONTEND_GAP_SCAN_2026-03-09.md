# Backend vs Frontend Gap Scan (2026-03-09)

Scope: Deep scan of backend-planned API capabilities in `routes/api.php` vs implemented frontend usage in `resources/js/**`.

## High Priority Gaps

1. Community thread interaction is partial (no comment UI / thread detail page)
- Backend planned:
  - `POST /community/discussions/{discussion}/comments` in `routes/api.php:312`
  - `GET /community/discussions/{discussion}` in `routes/api.php:209`
- Frontend implemented:
  - Like/share only in `resources/js/Pages/CommunityHub.vue:309` and `resources/js/Pages/CommunityHub.vue:315`
  - No API call for discussion detail/comments posting found in `resources/js/Pages/CommunityHub.vue`

2. Community group posting flow not implemented in frontend
- Backend planned:
  - `POST /community/groups/{group}/posts` in `routes/api.php:318`
  - `POST /community/group-posts/{post}/comments` in `routes/api.php:319`
- Frontend implemented:
  - Group creation/join only in `resources/js/Pages/CommunityHub.vue:337` and `resources/js/Pages/CommunityHub.vue:351`
  - No group post/comment API usage in frontend files

3. Community safety/reporting and notification features missing in frontend
- Backend planned:
  - `POST /community/reports` in `routes/api.php:324`
  - `GET /community/notifications` in `routes/api.php:325`
  - `POST /community/notifications/{notification}/read` in `routes/api.php:326`
- Frontend implemented:
  - No community report/notification API usage found in `resources/js/**`

4. Learning progression lifecycle incomplete in frontend
- Backend planned:
  - `POST /learn/courses/{course}/lessons/{lesson}/progress` in `routes/api.php:333`
  - `POST /learn/courses/{course}/reviews` in `routes/api.php:334`
  - `GET /learn/courses/{course}` in `routes/api.php:222`
- Frontend implemented:
  - Hub + list + enroll + dashboard in `resources/js/Pages/LearnHub.vue:109`, `resources/js/Pages/LearnHub.vue:118`, `resources/js/Pages/LearnHub.vue:124`, `resources/js/Pages/LearningDashboard.vue:68`
  - No lesson progress tracking, course review submission, or course detail API usage

5. Growth engine features partially wired (missing referrals and invite flow)
- Backend planned:
  - `GET /growth/my-referrals` in `routes/api.php:305`
  - `POST /growth/invite-email` in `routes/api.php:307`
  - `GET /growth/leaderboard` in `routes/api.php:202`
  - `GET /growth/share-frame` in `routes/api.php:203`
- Frontend implemented:
  - `GET /growth/top-referrers` in `resources/js/Pages/TopReferrers.vue:56`
  - `POST /growth/share-log` in multiple pages, e.g. `resources/js/Pages/CompetitionDetail.vue:1464`
  - No frontend usage for referrals/invite/leaderboard/share-frame endpoints

## Medium Priority Gaps

6. Community mentor management is one-way from user side
- Backend planned:
  - `POST /community/mentors/profile` in `routes/api.php:321`
  - `GET /community/mentors` in `routes/api.php:213`
- Frontend implemented:
  - Request mentorship only in `resources/js/Pages/CommunityHub.vue:357`
  - No frontend form to create/update own mentor profile

7. Learning discovery endpoints are not used directly
- Backend planned:
  - `GET /learn/workshops` in `routes/api.php:223`
  - `GET /learn/instructors` in `routes/api.php:224`
  - `GET /learn/search` in `routes/api.php:225`
- Frontend implemented:
  - Learn hub relies on `/learn/hub` aggregate call in `resources/js/Pages/LearnHub.vue:109`
  - No direct usage of workshops/instructors/search endpoints

8. Discover segmented endpoints underused by frontend
- Backend planned:
  - `GET /discover/personalized` in `routes/api.php:192`
  - `GET /discover/photographers` in `routes/api.php:193`
  - `GET /discover/events` in `routes/api.php:195`
  - `GET /discover/competitions` in `routes/api.php:196`
- Frontend implemented:
  - Uses `/discover/hub`, `/discover/photos`, `/discover/search` in `resources/js/Pages/Discover.vue:549`, `resources/js/Pages/Discover.vue:571`, `resources/js/Pages/Discover.vue:596`
  - No direct call to the segmented endpoints above

9. Admin community moderation missing user-ban control in UI
- Backend planned:
  - `POST /admin/community/moderation/ban-user/{user}` in `routes/api.php:584`
- Frontend implemented:
  - Discussions/reports + feature + resolve in `resources/js/Pages/Admin/CommunityModeration.vue:75`, `resources/js/Pages/Admin/CommunityModeration.vue:80`, `resources/js/Pages/Admin/CommunityModeration.vue:85`, `resources/js/Pages/Admin/CommunityModeration.vue:92`
  - No ban-user call in the admin moderation page

10. Admin learning review moderation not exposed in frontend
- Backend planned:
  - `GET /admin/learning/reviews` in `routes/api.php:589`
  - `POST /admin/learning/reviews/{review}/status` in `routes/api.php:590`
- Frontend implemented:
  - Overview/instructors/course status in `resources/js/Pages/Admin/LearningManagement.vue:79`, `resources/js/Pages/Admin/LearningManagement.vue:80`, `resources/js/Pages/Admin/LearningManagement.vue:90`
  - No review queue or review-status action UI

## Low Priority / Optional Gaps

11. Certificate access endpoint for format-specific download is not explicitly used in current page actions
- Backend planned:
  - `GET /my-certificates/{certificate}/download/{format?}` in `routes/api.php:298`
- Frontend implemented:
  - Certificate list + share in `resources/js/Pages/PhotographerCertificates.vue:92`, `resources/js/Pages/PhotographerCertificates.vue:112`
  - UI uses prebuilt download URLs from payload; endpoint is available but not directly called

## Notes
- This scan identifies capability gaps (backend route exists but frontend action/page does not implement it).
- It does not imply backend defects; many endpoints are stable and callable.
- Existing UI style system should be preserved while closing these gaps incrementally.
