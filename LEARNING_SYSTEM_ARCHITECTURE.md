# LEARNING SYSTEM ARCHITECTURE

## Overview
The Learning System introduces a complete education layer for Photographer SB with:
- Public learning discovery (`/learn`) for courses, workshops, and instructors.
- Authenticated learning actions (enrollments, lesson progress, reviews, instructor profile).
- Admin moderation and operations for learning quality and trust.
- Certificate issuance on full course completion via existing certificates module.

## Core Domain
### 1) Courses and Lessons
- `learning_courses`: instructor-owned courses with category, level, pricing, featured, publication status.
- `learning_lessons`: ordered lessons per course with support for video/text/resource content.

### 2) Enrollment and Progress
- `learning_enrollments`: one enrollment per user-course, tracks status, completion %, completed lessons, certificate link.
- `learning_lesson_progress`: per-lesson progress with timestamps and completion markers.

### 3) Instructor and Reviews
- `learning_instructor_profiles`: instructor bio, expertise, approval status, student metrics.
- `learning_course_reviews`: one review per learner-course, moderated visibility.

## API Surface
### Public
- `GET /api/v1/learn/hub`
- `GET /api/v1/learn/courses`
- `GET /api/v1/learn/courses/{course}`
- `GET /api/v1/learn/workshops`
- `GET /api/v1/learn/instructors`
- `GET /api/v1/learn/search`

### Authenticated
- `POST /api/v1/learn/courses/{course}/enroll`
- `GET /api/v1/learn/dashboard`
- `POST /api/v1/learn/courses/{course}/lessons/{lesson}/progress`
- `POST /api/v1/learn/courses/{course}/reviews`
- `GET /api/v1/learn/instructor/profile`
- `POST /api/v1/learn/instructor/profile`

### Admin
- `GET /api/v1/admin/learning/overview`
- `GET /api/v1/admin/learning/courses`
- `POST /api/v1/admin/learning/courses/{course}/status`
- `GET /api/v1/admin/learning/instructors`
- `POST /api/v1/admin/learning/instructors/{profile}/approval`
- `GET /api/v1/admin/learning/reviews`
- `POST /api/v1/admin/learning/reviews/{review}/status`

## Frontend UX
- `/learn`: Learning hub with filtering, featured courses, workshop recommendations, instructor spotlight.
- `/dashboard/learning`: personal learning dashboard with summary and enrollments table.
- `/admin/learning`: admin operations for course status, instructor approvals, and top-course visibility.

## Integrations
### Discovery and Workshops
- Learning hub includes workshop recommendations from `events` filtered by workshop intent.

### Profile Exposure
- Photographer profile payload includes:
  - `learning_stats.active_courses`
  - `learning_stats.completed_courses`
  - `learning_stats.certificates_earned`
  - `learning_stats.instructor_profile` (if approved)

### Certificates
- `LearningService::refreshEnrollmentProgress()` issues completion certificate when completion reaches 100%.
- Certificate source is tracked as `source_type = learning_course`.

## Data Safety and Compatibility
- Additive-only migrations.
- No breaking changes to existing modules.
- Learning endpoints use same `ApiResponse` conventions as existing APIs.
- Non-versioned read-only aliases are provided for `/learn/*` public endpoints to match existing discover/community alias strategy.

## Phase Roadmap (Completed in this delivery)
1. Schema foundation for courses/lessons/enrollments/progress/instructors/reviews.
2. Domain models and relationships.
3. Learning service for enrollment/progress/certificate issuance.
4. Public learning APIs and search/workshop integration.
5. Authenticated learning APIs and dashboard endpoint.
6. Admin moderation APIs for learning operations.
7. `/learn` and `/dashboard/learning` frontend pages.
8. `/admin/learning` admin page and navigation wiring.
9. Profile integration for learning stats and instructor visibility.
