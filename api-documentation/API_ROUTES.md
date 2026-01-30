# API Routes Plan - Photographer SB

## BASE URL
```
https://photographersb.com/api/v1
```

## AUTHENTICATION

### Auth Routes (Public)
```
POST   /auth/register
POST   /auth/login
POST   /auth/logout (Protected)
POST   /auth/forgot-password
POST   /auth/reset-password
POST   /auth/verify-email
POST   /auth/verify-phone
POST   /auth/refresh-token
POST   /auth/social-login
GET    /auth/me (Protected)
```

### Request/Response Examples
```
POST /auth/register
{
  "name": "Mahmudul Hasan",
  "email": "mahmudul@example.com",
  "phone": "+8801712345678",
  "password": "SecurePassword123",
  "password_confirmation": "SecurePassword123",
  "role": "photographer"
}

Response:
{
  "user": { ... },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "message": "Registration successful"
}
```

---

## PUBLIC ROUTES (No Auth Required)

### Search & Browse

#### Photographers
```
GET    /photographers
GET    /photographers?city=dhaka&category=wedding&rating=4.5
GET    /photographers/{id}
GET    /photographers/{username}
GET    /photographers/{id}/portfolio
GET    /photographers/{id}/portfolio/albums
GET    /photographers/{id}/portfolio/albums/{album-id}
GET    /photographers/{id}/reviews
GET    /photographers/{id}/availability
GET    /photographers/{id}/packages
GET    /photographers/search (Advanced search with filters)
GET    /photographers/trending
GET    /photographers/top-rated
GET    /photographers/featured
```

#### Studios
```
GET    /studios
GET    /studios/{id}
GET    /studios/{slug}
GET    /studios/{id}/team
GET    /studios/{id}/portfolio
GET    /studios/{id}/packages
```

#### Categories & Tags
```
GET    /categories
GET    /categories/{id}
GET    /categories/{slug}
GET    /categories/{id}/photographers
GET    /tags
GET    /tags/{id}
GET    /tags/{name}/photographers
```

#### Locations
```
GET    /cities
GET    /cities/{id}
GET    /cities/{id}/photographers
GET    /cities/{slug}/photographers
GET    /divisions
GET    /divisions/{id}
```

#### Reviews
```
GET    /photographers/{id}/reviews
GET    /photographers/{id}/reviews?rating=5&sort=helpful
GET    /reviews/{id}
GET    /reviews/trending
```

### Events
```
GET    /events
GET    /events?city=dhaka&category=workshop&date=2025-01
GET    /events/{id}
GET    /events/{slug}
GET    /events/{id}/details
GET    /events/{id}/gallery
GET    /events/{id}/attendees
GET    /events/{id}/attendees?limit=20
GET    /events/{id}/organizer
GET    /events/featured
GET    /events/upcoming
GET    /events/past
GET    /events/by-photographer/{photographer-id}
```

### Competitions
```
GET    /competitions
GET    /competitions?status=active&category=portraiture
GET    /competitions/{id}
GET    /competitions/{slug}
GET    /competitions/{id}/details
GET    /competitions/{id}/submissions
GET    /competitions/{id}/submissions?sort=votes&limit=20
GET    /competitions/{id}/leaderboard
GET    /competitions/{id}/winners
GET    /competitions/{id}/results
GET    /competitions/{id}/gallery
GET    /competitions/active
GET    /competitions/past
GET    /certificates/{id}
GET    /certificates/{id}/download
```

### CMS & Content
```
GET    /blog
GET    /blog?category=tips&page=1
GET    /blog/{slug}
GET    /pages/{slug}
GET    /faq
GET    /faqs?category=booking
GET    /landing-pages/{slug}
GET    /sitemap.xml
GET    /robots.txt
```

### Search & Analytics (Public)
```
GET    /search/photographers
GET    /search/events
GET    /search/competitions
GET    /search/suggestions?q=wedding
GET    /trending/categories
GET    /trending/photographers
GET    /trending/searches
GET    /analytics/cities
GET    /analytics/categories
```

---

## PROTECTED ROUTES (Authentication Required)

### Client/User Routes

#### Account
```
GET    /user/profile
PUT    /user/profile
POST   /user/avatar
PUT    /user/email
PUT    /user/phone
PUT    /user/password
POST   /user/verify-email
POST   /user/verify-phone
DELETE /user/account
POST   /user/enable-2fa
POST   /user/disable-2fa
GET    /user/sessions
DELETE /user/sessions/{session-id}
```

#### Bookings & Inquiries
```
POST   /inquiries
GET    /inquiries
GET    /inquiries/{id}
PUT    /inquiries/{id}
DELETE /inquiries/{id}
GET    /inquiries/{id}/quotes
POST   /inquiries/{id}/accept-quote
POST   /inquiries/{id}/reject-quote

GET    /bookings
GET    /bookings/{id}
POST   /bookings/{id}/cancel
GET    /bookings/{id}/invoice
POST   /bookings/{id}/payment-reminder
GET    /bookings/{id}/delivery
POST   /bookings/{id}/complete
```

#### Favorites & Collections
```
POST   /favorites
GET    /favorites
DELETE /favorites/{id}
POST   /favorites/collections
GET    /favorites/collections
PUT    /favorites/collections/{id}
DELETE /favorites/collections/{id}
POST   /favorites/collections/{id}/add
DELETE /favorites/collections/{id}/remove/{photographer-id}
```

#### Saved Searches
```
POST   /saved-searches
GET    /saved-searches
GET    /saved-searches/{id}
PUT    /saved-searches/{id}
DELETE /saved-searches/{id}
```

#### Reviews
```
POST   /bookings/{booking-id}/reviews
GET    /reviews
GET    /reviews/{id}
PUT    /reviews/{id}
DELETE /reviews/{id}
POST   /reviews/{id}/helpful
POST   /reviews/{id}/unhelpful
```

#### Events
```
GET    /events/my-events
GET    /events/saved
POST   /events/{id}/rsvp
DELETE /events/{id}/rsvp
POST   /events/{id}/rsvp/cancel
GET    /events/{id}/my-rsvp
GET    /tickets
GET    /tickets/{id}
POST   /events/{id}/tickets/purchase
GET    /events/{id}/tickets/purchase/{purchase-id}
```

#### Competitions
```
GET    /competitions/my-competitions
GET    /competitions/{id}/my-submissions
POST   /competitions/{id}/submit
PUT    /competitions/{id}/submissions/{submission-id}
DELETE /competitions/{id}/submissions/{submission-id}
GET    /competitions/{id}/voting
POST   /competitions/{id}/vote
DELETE /competitions/{id}/vote/{submission-id}
GET    /certificates
GET    /certificates/{id}
POST   /certificates/{id}/download
```

---

## PHOTOGRAPHER ROUTES (Protected)

### Profile Management
```
GET    /photographer/profile
PUT    /photographer/profile
POST   /photographer/avatar
POST   /photographer/cover-image
PUT    /photographer/location
PUT    /photographer/specializations
PUT    /photographer/bio

GET    /photographer/verification-status
POST   /photographer/request-verification
GET    /photographer/verification-documents
POST   /photographer/upload-verification-document
```

### Portfolio Management
```
GET    /photographer/albums
POST   /photographer/albums
GET    /photographer/albums/{id}
PUT    /photographer/albums/{id}
DELETE /photographer/albums/{id}
POST   /photographer/albums/{id}/photos
GET    /photographer/albums/{id}/photos
PUT    /photographer/albums/{id}/photos/{photo-id}
DELETE /photographer/albums/{id}/photos/{photo-id}
POST   /photographer/albums/{id}/reorder-photos
POST   /photographer/albums/{id}/bulk-upload

GET    /photographer/videos
POST   /photographer/videos
PUT    /photographer/videos/{id}
DELETE /photographer/videos/{id}
```

### Packages & Pricing
```
GET    /photographer/packages
POST   /photographer/packages
GET    /photographer/packages/{id}
PUT    /photographer/packages/{id}
DELETE /photographer/packages/{id}
POST   /photographer/packages/{id}/clone
POST   /photographer/packages/reorder
```

### Availability & Calendar
```
GET    /photographer/availability
GET    /photographer/availability/{date}
PUT    /photographer/availability
POST   /photographer/availability/bulk-update
POST   /photographer/availability/block-dates
DELETE /photographer/availability/{date}
GET    /photographer/calendar
GET    /photographer/bookings/calendar
```

### Inquiries & Bookings (Photographer Side)
```
GET    /photographer/inquiries
GET    /photographer/inquiries?status=new
GET    /photographer/inquiries/{id}
POST   /photographer/inquiries/{id}/respond
POST   /photographer/inquiries/{id}/send-quote
PUT    /photographer/inquiries/{id}/quote
POST   /photographer/inquiries/{id}/accept
POST   /photographer/inquiries/{id}/reject

GET    /photographer/bookings
GET    /photographer/bookings/{id}
POST   /photographer/bookings/{id}/confirm
POST   /photographer/bookings/{id}/complete
POST   /photographer/bookings/{id}/cancel
POST   /photographer/bookings/{id}/reschedule
POST   /photographer/bookings/{id}/delivery
POST   /photographer/bookings/{id}/delivery-photos
GET    /photographer/bookings/{id}/invoice
POST   /photographer/bookings/{id}/request-payment
```

### Reviews & Ratings (Photographer Side)
```
GET    /photographer/reviews
GET    /photographer/reviews/{id}
POST   /photographer/reviews/{id}/reply
PUT    /photographer/reviews/{id}/reply
DELETE /photographer/reviews/{id}/reply
POST   /photographer/reviews/{id}/report
GET    /photographer/ratings
GET    /photographer/ratings/breakdown
```

### Analytics Dashboard
```
GET    /photographer/analytics/overview
GET    /photographer/analytics/profile-views
GET    /photographer/analytics/inquiries
GET    /photographer/analytics/bookings
GET    /photographer/analytics/revenue
GET    /photographer/analytics/reviews
GET    /photographer/analytics/search-appearance
GET    /photographer/analytics/traffic-source
GET    /photographer/analytics/export?format=csv|pdf
```

### Subscription & Billing
```
GET    /photographer/subscription
GET    /photographer/subscription/current-plan
POST   /photographer/subscription/upgrade
POST   /photographer/subscription/downgrade
POST   /photographer/subscription/cancel
GET    /photographer/billing/history
GET    /photographer/billing/invoices
GET    /photographer/billing/invoices/{id}
GET    /photographer/payment-methods
POST   /photographer/payment-methods
DELETE /photographer/payment-methods/{id}

GET    /photographer/featured/listing
POST   /photographer/featured/purchase
GET    /photographer/featured/history

GET    /photographer/boosts
POST   /photographer/boosts/purchase
GET    /photographer/boosts/analytics/{boost-id}
```

### Events (Photographer as Organizer)
```
GET    /photographer/events
POST   /photographer/events
GET    /photographer/events/{id}
PUT    /photographer/events/{id}
DELETE /photographer/events/{id}
POST   /photographer/events/{id}/publish
POST   /photographer/events/{id}/cancel

GET    /photographer/events/{id}/attendees
POST   /photographer/events/{id}/attendees/export
POST   /photographer/events/{id}/attendees/message
DELETE /photographer/events/{id}/attendees/{user-id}

GET    /photographer/events/{id}/gallery
POST   /photographer/events/{id}/gallery
DELETE /photographer/events/{id}/gallery/{photo-id}
POST   /photographer/events/{id}/gallery/publish

GET    /photographer/events/{id}/tickets
GET    /photographer/events/{id}/tickets/sales
POST   /photographer/events/{id}/tickets/refund/{purchase-id}

GET    /photographer/events/{id}/analytics
```

### Competitions (Photographer as Participant)
```
GET    /photographer/competitions
GET    /photographer/competitions/{id}/submit
POST   /photographer/competitions/{id}/submit
GET    /photographer/competitions/{id}/my-submissions
PUT    /photographer/competitions/{id}/submissions/{submission-id}
DELETE /photographer/competitions/{id}/submissions/{submission-id}

GET    /photographer/competitions/{id}/voting
POST   /photographer/competitions/{id}/vote
DELETE /photographer/competitions/{id}/vote/{submission-id}

GET    /photographer/certificates
GET    /photographer/certificates/{id}
POST   /photographer/certificates/{id}/download
POST   /photographer/certificates/{id}/share
```

### Messaging
```
GET    /photographer/messages
GET    /photographer/conversations
GET    /photographer/conversations/{conversation-id}
POST   /photographer/conversations/{conversation-id}/messages
GET    /photographer/conversations/{conversation-id}/messages
DELETE /photographer/conversations/{conversation-id}/messages/{message-id}
```

### Settings
```
GET    /photographer/settings
PUT    /photographer/settings
GET    /photographer/settings/notifications
PUT    /photographer/settings/notifications
POST   /photographer/settings/auto-reply-template
PUT    /photographer/settings/auto-reply-template
GET    /photographer/settings/blocked-users
POST   /photographer/settings/block-user/{user-id}
DELETE /photographer/settings/unblock-user/{user-id}
```

---

## STUDIO ROUTES (Protected)

### Studio Management
```
GET    /studio/profile
PUT    /studio/profile
POST   /studio/avatar
POST   /studio/cover-image

GET    /studio/team
POST   /studio/team/invite
GET    /studio/team/invitations
POST   /studio/team/invitations/{id}/accept
POST   /studio/team/invitations/{id}/reject
GET    /studio/team/members/{member-id}
PUT    /studio/team/members/{member-id}
DELETE /studio/team/members/{member-id}

GET    /studio/roles
PUT    /studio/roles/{member-id}

GET    /studio/permissions
PUT    /studio/permissions/{member-id}
```

### Studio Portfolio
```
GET    /studio/albums
POST   /studio/albums
GET    /studio/albums/{id}
PUT    /studio/albums/{id}
DELETE /studio/albums/{id}
POST   /studio/albums/{id}/photos
GET    /studio/albums/{id}/photos
PUT    /studio/albums/{id}/photos/{photo-id}
DELETE /studio/albums/{id}/photos/{photo-id}

GET    /studio/videos
POST   /studio/videos
PUT    /studio/videos/{id}
DELETE /studio/videos/{id}
```

### Studio Packages
```
GET    /studio/packages
POST   /studio/packages
GET    /studio/packages/{id}
PUT    /studio/packages/{id}
DELETE /studio/packages/{id}
POST   /studio/packages/clone
POST   /studio/packages/{id}/assign-to-member
```

### Studio Bookings & Inquiries
```
GET    /studio/inquiries
GET    /studio/inquiries/{id}
POST   /studio/inquiries/{id}/assign-to/{photographer-id}
POST   /studio/inquiries/{id}/respond
POST   /studio/inquiries/{id}/send-quote

GET    /studio/bookings
GET    /studio/bookings/{id}
POST   /studio/bookings/{id}/assign-to/{photographer-id}
GET    /studio/bookings/{id}/assign-options
```

### Studio Analytics
```
GET    /studio/analytics/overview
GET    /studio/analytics/revenue
GET    /studio/analytics/photographers
GET    /studio/analytics/bookings
GET    /studio/analytics/inquiries
```

### Studio Subscription
```
GET    /studio/subscription
POST   /studio/subscription/upgrade
GET    /studio/billing/history
```

---

## ADMIN ROUTES (Protected - Admin Only)

### Dashboard
```
GET    /admin/overview
GET    /admin/analytics/dashboard
GET    /admin/recent-activities
GET    /admin/alerts
```

### Users Management
```
GET    /admin/users
GET    /admin/users?role=photographer&status=active
GET    /admin/users/{id}
POST   /admin/users
PUT    /admin/users/{id}
DELETE /admin/users/{id}
POST   /admin/users/{id}/suspend
POST   /admin/users/{id}/unsuspend
POST   /admin/users/{id}/reset-password
POST   /admin/users/{id}/enable-2fa
POST   /admin/users/bulk-action
GET    /admin/users/{id}/activity-log
```

### Photographers Management
```
GET    /admin/photographers
GET    /admin/photographers?verification_status=pending
GET    /admin/photographers/{id}
PUT    /admin/photographers/{id}
DELETE /admin/photographers/{id}
POST   /admin/photographers/{id}/verify
POST   /admin/photographers/{id}/reject-verification
POST   /admin/photographers/{id}/suspend
POST   /admin/photographers/{id}/unsuspend
POST   /admin/photographers/{id}/feature
DELETE /admin/photographers/{id}/feature
GET    /admin/photographers/{id}/verification-documents
POST   /admin/photographers/{id}/request-verification-update
GET    /admin/photographers/verification-pending
GET    /admin/photographers/featured
```

### Studios Management
```
GET    /admin/studios
GET    /admin/studios/{id}
PUT    /admin/studios/{id}
POST   /admin/studios/{id}/verify
POST   /admin/studios/{id}/suspend
GET    /admin/studios/{id}/team
```

### Content Management
```
GET    /admin/categories
POST   /admin/categories
GET    /admin/categories/{id}
PUT    /admin/categories/{id}
DELETE /admin/categories/{id}

GET    /admin/tags
POST   /admin/tags
PUT    /admin/tags/{id}
DELETE /admin/tags/{id}
POST   /admin/tags/merge

GET    /admin/cities
POST   /admin/cities
PUT    /admin/cities/{id}
DELETE /admin/cities/{id}
```

### Review Moderation
```
GET    /admin/reviews
GET    /admin/reviews?status=flagged
GET    /admin/reviews/{id}
POST   /admin/reviews/{id}/approve
POST   /admin/reviews/{id}/reject
POST   /admin/reviews/{id}/hide
POST   /admin/reviews/{id}/unhide
POST   /admin/reviews/{id}/delete
GET    /admin/reviews/reports
POST   /admin/reviews/{id}/report
```

### Events Management
```
GET    /admin/events
GET    /admin/events?status=pending
GET    /admin/events/{id}
PUT    /admin/events/{id}
DELETE /admin/events/{id}
POST   /admin/events/{id}/approve
POST   /admin/events/{id}/reject
POST   /admin/events/{id}/feature
DELETE /admin/events/{id}/feature
POST   /admin/events/{id}/cancel
GET    /admin/events/{id}/attendees
GET    /admin/events/{id}/analytics
```

### Competition Management
```
GET    /admin/competitions
POST   /admin/competitions
GET    /admin/competitions/{id}
PUT    /admin/competitions/{id}
DELETE /admin/competitions/{id}
POST   /admin/competitions/{id}/publish
POST   /admin/competitions/{id}/feature
POST   /admin/competitions/{id}/cancel

GET    /admin/competitions/{id}/submissions
GET    /admin/competitions/{id}/submissions?status=pending
GET    /admin/competitions/{id}/submissions/{submission-id}
POST   /admin/competitions/{id}/submissions/{submission-id}/approve
POST   /admin/competitions/{id}/submissions/{submission-id}/reject
POST   /admin/competitions/{id}/submissions/{submission-id}/disqualify

GET    /admin/competitions/{id}/voting
GET    /admin/competitions/{id}/fraud-detection
POST   /admin/competitions/{id}/fraud-detection/investigate

GET    /admin/competitions/{id}/judging
GET    /admin/competitions/{id}/judges
POST   /admin/competitions/{id}/judges/{judge-id}/assign
DELETE /admin/competitions/{id}/judges/{judge-id}

POST   /admin/competitions/{id}/announce-results
GET    /admin/competitions/{id}/analytics

GET    /admin/competitions/{id}/sponsors
POST   /admin/competitions/{id}/sponsors/{sponsor-id}/approve
```

### Payment & Transactions
```
GET    /admin/transactions
GET    /admin/transactions/{id}
GET    /admin/transactions?status=completed
POST   /admin/transactions/{id}/refund
GET    /admin/disputes
GET    /admin/disputes/{id}
POST   /admin/disputes/{id}/resolve
GET    /admin/payouts
POST   /admin/payouts/{id}/process
GET    /admin/revenue-reports
POST   /admin/revenue-reports/generate
```

### Verification Management
```
GET    /admin/verification-requests
GET    /admin/verification-requests?status=pending
GET    /admin/verification-requests/{id}
POST   /admin/verification-requests/{id}/approve
POST   /admin/verification-requests/{id}/reject
GET    /admin/verification-documents
```

### Subscription Plans
```
GET    /admin/subscription-plans
POST   /admin/subscription-plans
GET    /admin/subscription-plans/{id}
PUT    /admin/subscription-plans/{id}
DELETE /admin/subscription-plans/{id}

GET    /admin/subscriptions
GET    /admin/subscriptions?plan_id={plan-id}
```

### CMS & Content
```
GET    /admin/blog
POST   /admin/blog
GET    /admin/blog/{id}
PUT    /admin/blog/{id}
DELETE /admin/blog/{id}
POST   /admin/blog/{id}/publish
POST   /admin/blog/{id}/schedule

GET    /admin/pages
POST   /admin/pages
GET    /admin/pages/{id}
PUT    /admin/pages/{id}
DELETE /admin/pages/{id}

GET    /admin/email-templates
POST   /admin/email-templates
PUT    /admin/email-templates/{id}
GET    /admin/landing-pages
POST   /admin/landing-pages
PUT    /admin/landing-pages/{id}
```

### Notifications & Communications
```
POST   /admin/notifications/send
GET    /admin/notifications/templates
POST   /admin/notifications/templates
GET    /admin/campaigns
POST   /admin/campaigns
GET    /admin/campaigns/{id}
PUT    /admin/campaigns/{id}
POST   /admin/campaigns/{id}/send
GET    /admin/announcements
POST   /admin/announcements
DELETE /admin/announcements/{id}
```

### Settings & Configuration
```
GET    /admin/settings
PUT    /admin/settings
GET    /admin/settings/payment-gateways
PUT    /admin/settings/payment-gateways
GET    /admin/settings/commission-rates
PUT    /admin/settings/commission-rates
GET    /admin/settings/email-config
PUT    /admin/settings/email-config
GET    /admin/settings/sms-config
PUT    /admin/settings/sms-config
POST   /admin/settings/feature-flags
PUT    /admin/settings/feature-flags/{flag}
GET    /admin/settings/maintenance
PUT    /admin/settings/maintenance
```

### Audit & Logs
```
GET    /admin/audit-logs
GET    /admin/audit-logs?action=login&model=User
GET    /admin/api-logs
GET    /admin/error-logs
```

### Analytics & Reports
```
GET    /admin/analytics/users
GET    /admin/analytics/photographers
GET    /admin/analytics/bookings
GET    /admin/analytics/revenue
GET    /admin/analytics/events
GET    /admin/analytics/competitions
GET    /admin/analytics/search
GET    /admin/analytics/geographic
GET    /admin/reports/generate
POST   /admin/reports/generate
GET    /admin/reports/download/{report-id}
```

### Support
```
GET    /admin/support/tickets
GET    /admin/support/tickets/{id}
POST   /admin/support/tickets/{id}/respond
POST   /admin/support/tickets/{id}/close
GET    /admin/support/reports
GET    /admin/support/reports/{id}
POST   /admin/support/reports/{id}/action
```

---

## MODERATOR ROUTES (Protected - Moderator Only)

### Review Moderation
```
GET    /moderator/reviews
POST   /moderator/reviews/{id}/flag
POST   /moderator/reviews/{id}/approve
POST   /moderator/reviews/{id}/reject
GET    /moderator/reviews/reports
```

### Photographer Management
```
GET    /moderator/photographers
GET    /moderator/photographers/{id}
POST   /moderator/photographers/{id}/flag
POST   /moderator/photographers/{id}/suspend
```

### Event Moderation
```
GET    /moderator/events
GET    /moderator/events/{id}
POST   /moderator/events/{id}/approve
POST   /moderator/events/{id}/reject
```

### Competition Moderation
```
GET    /moderator/competitions
GET    /moderator/competitions/{id}/submissions
POST   /moderator/competitions/{id}/fraud-detection
```

### Reports
```
GET    /moderator/reports
GET    /moderator/reports/{id}
POST   /moderator/reports/{id}/action
```

---

## ERROR RESPONSES

All errors follow this format:

```json
{
  "status": "error",
  "code": 400,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required"],
    "password": ["The password must be at least 8 characters"]
  }
}
```

Common status codes:
- 200: OK
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Unprocessable Entity
- 429: Too Many Requests (Rate Limited)
- 500: Server Error

---

## RATE LIMITING

- Public endpoints: 100 requests per 15 minutes
- Authenticated endpoints: 300 requests per 15 minutes
- Search/List endpoints: 50 requests per minute
- Voting endpoints: 50 votes per day per user
- Login attempts: 5 attempts per 15 minutes per IP

---

## PAGINATION

All list endpoints support:
```
GET /api/v1/photographers?page=1&per_page=20&sort=rating&order=desc
```

Response includes:
```json
{
  "data": [...],
  "meta": {
    "total": 500,
    "per_page": 20,
    "current_page": 1,
    "last_page": 25,
    "from": 1,
    "to": 20
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

