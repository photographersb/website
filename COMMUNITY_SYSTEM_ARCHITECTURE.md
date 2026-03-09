# Photographer SB Community System Architecture

## 1) Community System Architecture

### Core Layers
- **Presentation Layer (Vue SPA)**
  - `/community` hub page with sections for discussions, groups, mentorship, featured posts, leaderboard.
  - Mobile-first cards/forms for posting and interaction.
- **API Layer (Laravel)**
  - `CommunityController` for public and authenticated community flows.
  - `Admin\CommunityModerationController` for moderator/admin controls.
- **Domain Layer (Services + Models)**
  - `CommunityService` for spam checks, badge sync, notification generation.
  - Eloquent models for discussions, groups, mentorship, badges, reports, moderation actions, notifications.
- **Data Layer (MySQL)**
  - Dedicated community tables with indexed query paths and constrained references to `users`/`locations`.

### Integration Points
- Photographer profile enrichment through `community_badges` in profile API payload.
- Discovery integration with `featured_community_posts` in discovery hub response.
- Existing auth and role middleware reused (`auth:sanctum`, role checks, throttles).

## 2) Database Schema

### Discussions
- `community_discussions`
- `community_discussion_comments`
- `community_discussion_likes`

### Groups
- `community_groups`
- `community_group_members`
- `community_group_posts`
- `community_group_post_comments`
- `community_group_post_likes`

### Mentorship
- `community_mentorship_profiles`
- `community_mentorship_requests`

### Recognition
- `community_badges`
- `community_user_badges`

### Trust & Safety
- `community_reports`
- `community_moderation_actions`
- `community_notifications`

## 3) Discussion System Design

### Supported Capabilities
- Create discussion (`title`, `content`, `category`, `tags`, `user_id`).
- Browse/filter/search discussions.
- Comment on discussions (thread-ready with optional `parent_id`).
- Like discussions (toggle).
- Track discussion shares.

### Safety Controls
- Rate limits on create/comment/like/share endpoints.
- Duplicate-content spam checks over short rolling windows.
- Report flow for abusive/inappropriate content.

## 4) Group System Implementation

### Group Features
- Create interest groups and city-based local clubs.
- Join groups with role-aware membership (`admin`, `moderator`, `member`).
- Publish group posts and comments.
- Track member/post counters for ranking and discovery.

### Initial Group Experience
- API supports examples such as street/wedding/travel/drone/food communities via standard `name` and `type`.
- Local clubs represented with `type=local_club` and optional `city_id`.

## 5) Mentorship System

### Mentor Side
- Mentor profile fields:
  - `expertise`
  - `years_experience`
  - `availability_status`
  - `session_types`
  - `bio`
- Active/inactive profile state for availability control.

### Mentee Side
- Send mentorship requests with message + preferred session type.
- Mentor receives notification on new requests.

## 6) Moderation Tools

### Member-Level Moderation
- Users can report discussion/comment/group-post content.

### Admin/Moderator Tools
- View report queue.
- Resolve/dismiss reports.
- Feature/unfeature discussions.
- Ban/suspend abusive users.
- Persist moderation audit logs in `community_moderation_actions`.

## 7) Notification System

### Notification Triggers
- Reply on discussion.
- Mention in community comment.
- Group post reply activity.
- Mentorship requests.
- Discussion like activity.

### Notification Endpoints
- Fetch paginated notifications.
- Mark notification as read.

## 8) Implementation Roadmap

### Phase A (Now Implemented)
- Community schema and models.
- Core community APIs.
- `/community` hub page (mobile-first).
- Profile badge integration + discovery featured community integration.

### Phase B (Next)
- Rich thread UI for nested comments and in-page discussion details.
- Group post feed UX improvements and media upload integration.
- Real-time notifications (WebSocket / Echo).

### Phase C
- Advanced moderation console (bulk actions, keyword filters, trust score coupling).
- Reputation weighting (helpful votes, accepted answers).
- Club event scheduling and attendance syncing with events module.

### Phase D
- Recommendation engine for mentors/groups/topics.
- Gamification refinement with seasonal leaderboards.
- Analytics dashboard for community health KPIs.

---

## API Summary (Implemented)

### Public
- `GET /api/v1/community/hub`
- `GET /api/v1/community/discussions`
- `GET /api/v1/community/discussions/{discussion}`
- `GET /api/v1/community/groups`
- `GET /api/v1/community/groups/{group}`
- `GET /api/v1/community/clubs`
- `GET /api/v1/community/mentors`
- `GET /api/v1/community/search?q=`
- `GET /api/v1/community/leaderboard`

### Authenticated
- `POST /api/v1/community/discussions`
- `POST /api/v1/community/discussions/{discussion}/comments`
- `POST /api/v1/community/discussions/{discussion}/like`
- `POST /api/v1/community/discussions/{discussion}/share`
- `POST /api/v1/community/groups`
- `POST /api/v1/community/groups/{group}/join`
- `POST /api/v1/community/groups/{group}/posts`
- `POST /api/v1/community/group-posts/{post}/comments`
- `POST /api/v1/community/mentors/profile`
- `POST /api/v1/community/mentors/{mentorUser}/request`
- `POST /api/v1/community/reports`
- `GET /api/v1/community/notifications`
- `POST /api/v1/community/notifications/{notification}/read`

### Admin/Moderator
- `GET /api/v1/admin/community/moderation/reports`
- `POST /api/v1/admin/community/moderation/reports/{report}/resolve`
- `POST /api/v1/admin/community/moderation/feature/discussions/{discussion}`
- `POST /api/v1/admin/community/moderation/ban-user/{user}`
