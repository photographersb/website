# Complete Sitemap - Photographer SB

## PUBLIC PAGES (Guest Access)

### Home & Discovery
- `/` - Homepage
- `/about` - About Photographer SB
- `/contact` - Contact us
- `/blog` - Blog listing
- `/blog/[slug]` - Blog post detail
- `/faq` - FAQ page
- `/terms` - Terms of service
- `/privacy` - Privacy policy
- `/security` - Security & safety page
- `/how-it-works` - Platform guide

### Search & Browse
- `/search` - Main search page
- `/photographers` - All photographers (directory)
- `/photographers?city=[city]` - Photographers by city
- `/photographers?category=[category]` - Photographers by category
- `/photographers?city=[city]&category=[category]` - Photographers by city + category
- `/photographers?ratings=4.5` - Top-rated photographers
- `/photographers/verified` - Verified photographers only
- `/[city]-photographers` - City landing page (e.g., /dhaka-photographers)
- `/[category]-photographers` - Category page (e.g., /wedding-photographers)
- `/[city]-[category]-photographers` - Location + category page (e.g., /dhaka-wedding-photographers)

### Photographer Profile Pages
- `/photographer/[username]` - Photographer profile
- `/photographer/[username]/portfolio` - Photographer portfolio
- `/photographer/[username]/reviews` - Photographer reviews
- `/photographer/[username]/gallery` - Photographer gallery
- `/photographer/[username]/events` - Photographer events
- `/photographer/[username]/book` - Book photographer (inquiry form)

### Studio Profile Pages
- `/studio/[studio-slug]` - Studio profile
- `/studio/[studio-slug]/team` - Studio team members
- `/studio/[studio-slug]/portfolio` - Studio portfolio
- `/studio/[studio-slug]/book` - Book studio

### Events Pages
- `/events` - All events listing
- `/events?category=[category]` - Events by category
- `/events?city=[city]` - Events by city
- `/events/upcoming` - Upcoming events
- `/events/[event-slug]` - Event detail page
- `/events/[event-slug]/gallery` - Event gallery
- `/events/[event-slug]/attendees` - Event attendees list
- `/events/[event-slug]/tickets` - Event ticket page

### Competition Pages
- `/competitions` - All competitions listing
- `/competitions/active` - Currently active competitions
- `/competitions/past` - Past competitions
- `/competitions/[competition-slug]` - Competition detail
- `/competitions/[competition-slug]/gallery` - Competition submissions gallery
- `/competitions/[competition-slug]/leaderboard` - Voting leaderboard
- `/competitions/[competition-slug]/results` - Results/winners page
- `/competitions/[competition-slug]/winners/[winner-id]` - Winner detail
- `/certificates/[certificate-id]` - Digital certificate view

---

## AUTHENTICATION PAGES

### Account Management
- `/login` - Login page
- `/register` - Registration page
- `/register/photographer` - Register as photographer
- `/register/client` - Register as client
- `/forgot-password` - Password reset request
- `/reset-password/[token]` - Password reset form
- `/verify-email` - Email verification
- `/verify-phone` - Phone OTP verification
- `/profile-setup` - Initial profile setup wizard

---

## DASHBOARD PAGES - CLIENT/CUSTOMER

### Client Dashboard Main
- `/dashboard` - Dashboard home
- `/dashboard/profile` - Edit profile
- `/dashboard/settings` - Account settings

### Bookings & Inquiries
- `/dashboard/bookings` - My bookings/inquiries
- `/dashboard/bookings/active` - Active bookings
- `/dashboard/bookings/past` - Past bookings
- `/dashboard/bookings/[booking-id]` - Booking details
- `/dashboard/inquiries` - Sent inquiries
- `/dashboard/inquiries/[inquiry-id]` - Inquiry detail

### Favorites & Collections
- `/dashboard/favorites` - Favorite photographers
- `/dashboard/favorites/[folder-id]` - Favorite collection
- `/dashboard/saved-searches` - Saved searches
- `/dashboard/collections` - My collections
- `/dashboard/collections/create` - Create collection

### Reviews & History
- `/dashboard/reviews` - My reviews
- `/dashboard/reviews/[review-id]` - Review detail
- `/dashboard/history` - Booking history

### Events & Competitions
- `/dashboard/events` - My events (RSVPs)
- `/dashboard/events/saved` - Saved events
- `/dashboard/tickets` - My tickets
- `/dashboard/competitions` - Competitions participating in

---

## DASHBOARD PAGES - PHOTOGRAPHER

### Photographer Dashboard Main
- `/dashboard` - Dashboard home
- `/dashboard/profile` - Edit photographer profile
- `/dashboard/profile/completion` - Profile completion guide
- `/dashboard/settings` - Account settings
- `/dashboard/verification` - Verification status

### Profile & Portfolio
- `/dashboard/portfolio` - Manage portfolio
- `/dashboard/portfolio/albums` - Photo albums
- `/dashboard/portfolio/albums/create` - Create album
- `/dashboard/portfolio/albums/[album-id]` - Edit album
- `/dashboard/portfolio/albums/[album-id]/photos` - Album photos
- `/dashboard/portfolio/upload` - Bulk photo upload
- `/dashboard/portfolio/videos` - Video portfolio

### Pricing & Packages
- `/dashboard/packages` - Manage packages
- `/dashboard/packages/create` - Create package
- `/dashboard/packages/[package-id]` - Edit package
- `/dashboard/pricing` - Pricing settings

### Calendar & Availability
- `/dashboard/availability` - Availability calendar
- `/dashboard/calendar` - Booking calendar
- `/dashboard/holidays` - Manage holidays/blocked dates

### Bookings & Inquiries
- `/dashboard/inquiries` - Incoming inquiries
- `/dashboard/inquiries/[inquiry-id]` - Inquiry detail
- `/dashboard/inquiries/[inquiry-id]/quote` - Send quote
- `/dashboard/bookings` - My bookings
- `/dashboard/bookings/[booking-id]` - Booking detail
- `/dashboard/bookings/[booking-id]/delivery` - Delivery gallery

### Reviews & Ratings
- `/dashboard/reviews` - My reviews
- `/dashboard/reviews/[review-id]/reply` - Reply to review
- `/dashboard/ratings` - Rating statistics

### Analytics & Reports
- `/dashboard/analytics` - Dashboard analytics
- `/dashboard/analytics/profile-views` - Profile views
- `/dashboard/analytics/inquiries` - Inquiry statistics
- `/dashboard/analytics/bookings` - Booking statistics
- `/dashboard/analytics/revenue` - Revenue report
- `/dashboard/analytics/reviews` - Review insights
- `/dashboard/analytics/export` - Export data

### Events Management
- `/dashboard/events` - My events
- `/dashboard/events/create` - Create event
- `/dashboard/events/[event-id]` - Edit event
- `/dashboard/events/[event-id]/attendees` - Event attendees
- `/dashboard/events/[event-id]/gallery` - Event gallery
- `/dashboard/events/[event-id]/analytics` - Event analytics

### Competitions
- `/dashboard/competitions` - My competitions
- `/dashboard/competitions/[competition-id]/submit` - Submit entry
- `/dashboard/competitions/[competition-id]/submissions` - My submissions
- `/dashboard/certificates` - My certificates

### Subscription & Billing
- `/dashboard/subscription` - Current subscription
- `/dashboard/subscription/upgrade` - Upgrade plan
- `/dashboard/billing` - Billing history
- `/dashboard/billing/invoices` - Invoice list
- `/dashboard/billing/invoices/[invoice-id]` - Invoice detail
- `/dashboard/payment-methods` - Manage payment methods
- `/dashboard/featured` - Featured listing management
- `/dashboard/featured/buy` - Buy featured listing
- `/dashboard/boosts` - Manage boosts

---

## DASHBOARD PAGES - STUDIO (Team)

### Studio Dashboard Main
- `/dashboard` - Dashboard home
- `/dashboard/studio/profile` - Edit studio profile
- `/dashboard/studio/settings` - Studio settings
- `/dashboard/studio/verification` - Verification status

### Team Management
- `/dashboard/studio/team` - Team members
- `/dashboard/studio/team/invite` - Invite team member
- `/dashboard/studio/team/[member-id]` - Manage member
- `/dashboard/studio/team/roles` - Role management
- `/dashboard/studio/permissions` - Permissions management

### Portfolio & Packages
- `/dashboard/studio/portfolio` - Studio portfolio
- `/dashboard/studio/portfolio/albums` - Albums
- `/dashboard/studio/portfolio/albums/create` - Create album
- `/dashboard/studio/packages` - Studio packages
- `/dashboard/studio/packages/create` - Create package

### Bookings & Inquiries
- `/dashboard/studio/inquiries` - Studio inquiries
- `/dashboard/studio/bookings` - Studio bookings
- `/dashboard/studio/bookings/assign` - Assign to photographer

### Events
- `/dashboard/studio/events` - Studio events
- `/dashboard/studio/events/create` - Create event
- `/dashboard/studio/events/[event-id]` - Event detail

### Analytics & Reporting
- `/dashboard/studio/analytics` - Studio analytics
- `/dashboard/studio/analytics/revenue` - Revenue report
- `/dashboard/studio/analytics/photographer-performance` - Photographer performance

### Subscription & Billing
- `/dashboard/studio/subscription` - Studio subscription
- `/dashboard/studio/billing` - Billing
- `/dashboard/studio/invoices` - Invoices

---

## ADMIN PAGES

### Admin Dashboard Main
- `/admin` - Admin dashboard home
- `/admin/overview` - Platform overview
- `/admin/audit-logs` - Audit logs

### Users Management
- `/admin/users` - All users list
- `/admin/users?role=photographer` - Filter photographers
- `/admin/users?role=client` - Filter clients
- `/admin/users?role=studio` - Filter studios
- `/admin/users/[user-id]` - User detail
- `/admin/users/[user-id]/edit` - Edit user
- `/admin/users/[user-id]/suspend` - Suspend user
- `/admin/users?status=suspended` - Suspended users
- `/admin/users/bulk-actions` - Bulk operations

### Photographer Management
- `/admin/photographers` - All photographers
- `/admin/photographers/verification-pending` - Pending verification
- `/admin/photographers/[photographer-id]` - Photographer detail
- `/admin/photographers/[photographer-id]/verify` - Verify photographer
- `/admin/photographers/[photographer-id]/suspend` - Suspend photographer
- `/admin/photographers/[photographer-id]/portfolio` - Portfolio review

### Studio Management
- `/admin/studios` - All studios
- `/admin/studios/[studio-id]` - Studio detail
- `/admin/studios/[studio-id]/edit` - Edit studio
- `/admin/studios/[studio-id]/team` - View team

### Content Management
- `/admin/categories` - Manage categories
- `/admin/categories/create` - Create category
- `/admin/categories/[category-id]` - Edit category
- `/admin/tags` - Manage tags
- `/admin/tags/create` - Create tag

### Reviews & Moderation
- `/admin/reviews` - All reviews
- `/admin/reviews/flagged` - Flagged reviews
- `/admin/reviews/[review-id]` - Review detail
- `/admin/reviews/[review-id]/approve` - Approve review
- `/admin/reviews/[review-id]/reject` - Reject review

### Events Management
- `/admin/events` - All events
- `/admin/events/pending` - Pending approval
- `/admin/events/[event-id]` - Event detail
- `/admin/events/[event-id]/edit` - Edit event
- `/admin/events/[event-id]/approve` - Approve event
- `/admin/events/[event-id]/feature` - Feature event
- `/admin/events/[event-id]/submissions` - Event submissions (if competition event)

### Competition Management
- `/admin/competitions` - All competitions
- `/admin/competitions/active` - Active competitions
- `/admin/competitions/create` - Create competition
- `/admin/competitions/[competition-id]` - Competition detail
- `/admin/competitions/[competition-id]/edit` - Edit competition
- `/admin/competitions/[competition-id]/publish` - Publish competition
- `/admin/competitions/[competition-id]/submissions` - Submissions queue
- `/admin/competitions/[competition-id]/submissions/[submission-id]` - Submission detail
- `/admin/competitions/[competition-id]/submissions/[submission-id]/approve` - Approve submission
- `/admin/competitions/[competition-id]/submissions/[submission-id]/reject` - Reject submission
- `/admin/competitions/[competition-id]/voting` - Voting management
- `/admin/competitions/[competition-id]/judging` - Judge assignments
- `/admin/competitions/[competition-id]/results` - Results management
- `/admin/competitions/[competition-id]/fraud-detection` - Fraud detection
- `/admin/competitions/[competition-id]/analytics` - Competition analytics
- `/admin/competitions/[competition-id]/sponsors` - Sponsor management

### Payments & Transactions
- `/admin/payments` - All transactions
- `/admin/payments?status=completed` - Completed payments
- `/admin/payments?status=pending` - Pending payments
- `/admin/payments?status=failed` - Failed payments
- `/admin/payments/[transaction-id]` - Transaction detail
- `/admin/disputes` - Payment disputes
- `/admin/disputes/[dispute-id]` - Dispute detail
- `/admin/refunds` - Refund requests
- `/admin/refunds/[refund-id]` - Refund detail
- `/admin/payouts` - Photographer payouts
- `/admin/revenue-reports` - Revenue reports

### Verification Management
- `/admin/verification` - Verification requests
- `/admin/verification/pending` - Pending verifications
- `/admin/verification/[request-id]` - Verification detail
- `/admin/verification/[request-id]/approve` - Approve verification
- `/admin/verification/[request-id]/reject` - Reject verification
- `/admin/verification/documents` - Document management

### CMS & Content
- `/admin/cms` - CMS management
- `/admin/cms/pages` - Static pages
- `/admin/cms/pages/create` - Create page
- `/admin/cms/pages/[page-id]` - Edit page
- `/admin/cms/blog` - Blog management
- `/admin/cms/blog/create` - Create blog post
- `/admin/cms/blog/[post-id]` - Edit blog post
- `/admin/cms/landing-pages` - Landing pages
- `/admin/cms/email-templates` - Email templates
- `/admin/cms/banners` - Promotional banners

### Notifications & Communications
- `/admin/notifications` - Send notifications
- `/admin/notifications/templates` - Notification templates
- `/admin/campaigns` - Email campaigns
- `/admin/campaigns/create` - Create campaign
- `/admin/announcements` - Platform announcements

### Settings & Configuration
- `/admin/settings` - Platform settings
- `/admin/settings/general` - General settings
- `/admin/settings/payment-gateways` - Payment configuration
- `/admin/settings/email` - Email settings
- `/admin/settings/sms` - SMS settings
- `/admin/settings/subscription-plans` - Manage subscription plans
- `/admin/settings/commission-rates` - Commission rates
- `/admin/settings/feature-flags` - Feature flags
- `/admin/settings/maintenance` - Maintenance mode
- `/admin/settings/api-keys` - API key management
- `/admin/settings/security` - Security settings
- `/admin/settings/seo` - SEO settings

### Analytics & Reports
- `/admin/analytics` - Platform analytics
- `/admin/analytics/dashboard` - Analytics dashboard
- `/admin/analytics/users` - User analytics
- `/admin/analytics/photographers` - Photographer analytics
- `/admin/analytics/bookings` - Booking analytics
- `/admin/analytics/revenue` - Revenue analytics
- `/admin/analytics/events` - Event analytics
- `/admin/analytics/competitions` - Competition analytics
- `/admin/analytics/search` - Search analytics
- `/admin/analytics/custom-report` - Custom reports
- `/admin/analytics/export` - Export analytics

### Support & Help
- `/admin/support` - Support tickets
- `/admin/support/[ticket-id]` - Ticket detail
- `/admin/support/reports` - User reports
- `/admin/support/reports/[report-id]` - Report detail

---

## MODERATOR PAGES (Subset of Admin)

### Moderator Dashboard
- `/moderator` - Moderator home
- `/moderator/dashboard` - Dashboard overview

### Content Moderation
- `/moderator/reviews` - Review moderation
- `/moderator/reviews/[review-id]` - Review detail
- `/moderator/comments` - Comment moderation
- `/moderator/reports` - User reports
- `/moderator/reports/[report-id]` - Report detail

### Photographer Management (Limited)
- `/moderator/photographers` - Photographer list
- `/moderator/photographers/[photographer-id]` - Photographer detail
- `/moderator/photographers/[photographer-id]/flag` - Flag photographer

### Event Moderation
- `/moderator/events` - Event moderation
- `/moderator/events/[event-id]` - Event detail
- `/moderator/events/[event-id]/submissions` - Event submissions

### Competition Moderation
- `/moderator/competitions` - Competition moderation
- `/moderator/competitions/[competition-id]` - Competition detail
- `/moderator/competitions/[competition-id]/submissions` - Submission moderation
- `/moderator/competitions/[competition-id]/fraud-detection` - Fraud monitoring

---

## PAYMENT & CHECKOUT PAGES

- `/checkout` - General checkout page
- `/checkout/subscription` - Subscription checkout
- `/checkout/subscription/[plan-id]` - Plan-specific checkout
- `/checkout/featured-listing` - Featured listing checkout
- `/checkout/boost` - Boost purchase checkout
- `/checkout/event-ticket` - Event ticket checkout
- `/checkout/event/[event-id]/tickets` - Event-specific ticket checkout
- `/checkout/competition-entry` - Competition entry fee checkout
- `/payment/success` - Payment success page
- `/payment/failed` - Payment failed page
- `/payment/pending` - Payment pending page

---

## ERROR PAGES

- `/404` - Page not found
- `/403` - Access denied
- `/500` - Server error
- `/503` - Service unavailable
- `/maintenance` - Maintenance mode

---

## API ROUTES (REST Endpoints)

Complete API routes documented in API documentation file

```
Base: /api/v1

Public Routes:
- GET    /photographers
- GET    /photographers/{id}
- GET    /photographers/{id}/portfolio
- GET    /photographers/{id}/reviews
- GET    /cities
- GET    /categories
- GET    /events
- GET    /competitions
- POST   /auth/register
- POST   /auth/login
- POST   /auth/forgot-password

Protected Routes:
- GET    /dashboard/*
- POST   /inquiries
- POST   /reviews
- POST   /competitions/{id}/submit

Admin Routes:
- GET    /admin/users
- POST   /admin/users
- POST   /admin/competitions/{id}/results
- POST   /admin/events/{id}/approve
```

---

## PAGE COUNT SUMMARY

| Section | Count |
|---------|-------|
| Public Pages | 45+ |
| Auth Pages | 8 |
| Client Dashboard | 20+ |
| Photographer Dashboard | 35+ |
| Studio Dashboard | 20+ |
| Admin Pages | 80+ |
| Moderator Pages | 15 |
| Payment Pages | 8 |
| Error Pages | 6 |
| **TOTAL PAGES** | **237+** |

