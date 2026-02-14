# Event Module - Complete Design

## IMPLEMENTATION STATUS

### ✅ Phase 1: COMPLETE (Current Platform - 99%)
**Core Features Implemented:**
- Public event listing with filters (city, date, type, sort)
- Event detail page with RSVP
- Admin full CRUD (create, edit, delete, feature, bulk operations)
- Photographer restricted CRUD (create draft, edit own, delete own)
- Role-based access control (Public/Photographer/Admin)
- Basic RSVP system (going/not_going)
- Event types (workshop, exhibition, meetup, competition, seminar, other)
- Status workflow (draft → published → cancelled)
- Featured event system
- Mobile responsive design
- API: 18 endpoints across 3 tiers

### 🔨 Phase 2: PLANNED (Advanced Features - 50%)
**Features for Future Enhancement:**
- Multi-day events with schedules
- Ticketing system (purchase, QR codes, refunds)
- Event gallery management
- Attendee management (check-in, export, messaging)
- Sponsorship system
- Advanced analytics
- Geographic coordinates & map integration
- Rich notifications (reminders, follow-ups)
- Event categories with icons
- Related events algorithm
- Advanced RSVP (going/maybe/not_going)

---

## 1) EVENT SYSTEM OVERVIEW

### What is the Event Module?
The Event Module is a dedicated system for photographers to create, promote, and manage events (photoshoots, workshops, exhibitions, meetings). Clients can discover events, RSVP, and view gallery photos from past events.

### Event Types
- **Photoshoots**: Fashion shoot, concept photoshoot (open call for models)
- **Workshops**: Photography tutorial, editing workshop, equipment demo
- **Exhibitions**: Photography exhibition, gallery opening, photo display
- **Meetups**: Photographer meetup, camera club meeting, networking event
- **Competitions**: Photo competition events (linked to Competition module)
- **Classes**: Training sessions, mentoring programs
- **Trips**: Photography tours, location trips, travel expeditions

### Why Events Matter for Photographer SB
- Additional revenue stream (ticket sales, sponsorships)
- Community engagement and brand loyalty
- Lead generation (attendees become clients)
- Portfolio building opportunity
- Exposure and media coverage
- Networking for photographers

---

## 2) EVENT FEATURES

### 2.1 Event Creation & Management

#### Create Event
**Phase 1 (Implemented):**
- [x] Event title
- [x] Event type selection (workshop, exhibition, meetup, competition, seminar, other)
- [x] Event description (textarea)
- [x] Event date and time (datetime-local)
- [x] Duration in hours
- [x] Venue/location details (text field)
- [x] City selection (dropdown)
- [x] Capacity/attendee limit (max_attendees)
- [x] Ticket price (৳0 for free events)
- [x] Requirements field
- [x] Organizer ID assignment
- [x] Status (draft/published/cancelled)
- [x] Featured toggle
- [x] Verified toggle

**Phase 2 (Planned):**
- [ ] Rich text editor for description
- [ ] Event banner upload (currently URL only)
- [ ] Multi-day event support (start/end dates)
- [ ] Start time/end time separate fields
- [ ] Geographic coordinates (latitude/longitude)
- [ ] Map integration for venue
- [ ] Gallery/multiple photos upload
- [ ] Outdoor vs indoor toggle
- [ ] Advanced ticketing system

#### Event Details
**Phase 1 (Implemented):**
- [x] Event slug/URL (auto-generated from title)
- [x] Unique event ID
- [x] Event status (draft, published, cancelled)
- [x] Featured flag (is_featured)
- [x] Verified flag (is_verified)
- [x] View counter
- [x] RSVP counter

**Phase 2 (Planned):**
- [ ] Featured image upload
- [ ] Banner image upload
- [ ] SEO meta title and description
- [ ] Event hashtags
- [ ] Completed status (auto-set after event date)

### 2.2 Event Categories & Tags
**Phase 1 (Implemented):**
- [x] Event type enum: workshop, exhibition, meetup, competition, seminar, other
- [x] Single type selection per event
- [x] Type-based filtering
- [x] Type badge display

**Phase 2 (Planned):**
- [ ] Separate event_categories table with icons
- [ ] Multiple category selection
- [ ] Custom tags/hashtags system
- [ ] Trending tags tracking
- [ ] Tag-based search

### 2.3 Event Listing & Discovery

#### Event Listing Page
**Phase 1 (Implemented):**
- [x] Filter by city
- [x] Filter by event type
- [x] Filter by date range (from/to)
- [x] Sort by date (ascending/descending)
- [x] Sort by featured (featured first)
- [x] Search events (planned)
- [x] Featured events highlighted
- [x] Stats bar (total events, upcoming, cities, RSVPs)
- [x] Pagination

**Phase 2 (Planned):**
- [ ] Filter by organizer
- [ ] Filter by this week/this month presets
- [ ] Sort by popularity (RSVP count)
- [ ] Sort by distance (requires geolocation)
- [ ] Keyword search implementation
- [ ] Featured events carousel
- [ ] Upcoming banner section

#### Event Card Display
- [x] Event banner image
- [x] Event title
- [x] Event date and time
- [x] Location (city, area)
- [x] Organizer name + photo
- [x] RSVP count (number of attendees)
- [x] Event type badge
- [x] Featured badge (if promoted)
- [x] Quick action button (View Details / RSVP)

#### Event Detail Page
- [x] Large hero image
- [x] Event title and date
- [x] Breadcrumb navigation
- [x] Event description
- [x] Event date, time, duration
- [x] Location with embedded Google Map
- [x] Organizer profile card with action button
- [x] RSVP count display
- [x] RSVP/Attend button (prominent CTA)
- [x] Capacity information
- [x] Event gallery (photos from event)
- [x] Ticket information (if ticketed)
- [x] Related events (from same organizer or category)
- [x] Share buttons (WhatsApp, Facebook, Twitter)
- [x] Event location on map
- [x] Event organizer details
- [x] Event schedule/agenda (if multi-day)

### 2.4 Event Schedule & Timeline
- [x] Event start date/time
- [x] Event end time (duration indication)
- [x] All-day event toggle
- [x] Multi-day event support
- [x] Event agenda/schedule (if applicable)
- [x] Session-wise timing (for workshops)
- [x] Timeline phases:
  - Upcoming (registrations open)
  - Ongoing (event is happening)
  - Completed (event finished)
  - Cancelled

### 2.5 Organizer Profile & Information
- [x] Organizer name and photo
- [x] Organizer link to profile
- [x] Organizer contact information
- [x] Organizer social media links
- [x] Organizer bio (short)
- [x] Organizer rating/reviews (if applicable)
- [x] Organizer event history
- [x] Follow organizer button

### 2.6 RSVP & Attendee Management

#### RSVP System
**Phase 1 (Implemented):**
- [x] RSVP button (prominent CTA)
- [x] One-click RSVP (toggle going/not going)
- [x] Authentication check (redirects to /auth)
- [x] Capacity check before RSVP
- [x] RSVP count display
- [x] RSVP status indicator
- [x] Spots remaining display

**Phase 2 (Planned):**
- [ ] Three-option RSVP (Going/Maybe/Not Going)
- [ ] RSVP confirmation email
- [ ] RSVP cancellation with confirmation
- [ ] Share event with friends
- [ ] RSVP reminder notifications

#### Attendee List
- [x] Show attendee count
- [x] Attendee profile list (first 10, then "See all")
- [x] Each attendee card with photo
- [x] Attendee names (without surname if privacy enabled)
- [x] Privacy toggle (hide attendee list)

#### Organizer Attendee Dashboard
- [x] List all attendees
- [x] Export attendee list (CSV)
- [x] Attendee filtering
- [x] Send message to attendees
- [x] Remove attendee
- [x] Attendee check-in/check-out (optional, for physical events)

### 2.7 Ticketing System (Optional)

#### Ticket Creation
- [x] Free event vs ticketed
- [x] Ticket types (e.g., Early Bird, Regular, VIP)
- [x] Ticket pricing per type
- [x] Ticket quantity limit
- [x] Ticket sales start/end date
- [x] Ticket benefits description
- [x] Early bird discount

#### Ticket Purchase Flow
- [x] Ticket type selection
- [x] Quantity selection
- [x] Add to cart
- [x] Checkout with payment
- [x] Payment gateway integration (SSLCommerz, bKash)
- [x] Ticket confirmation email
- [x] Digital ticket (QR code)
- [x] Ticket PDF download

#### Ticket Management
- [x] View purchased tickets
- [x] Ticket barcode/QR code display
- [x] Ticket forwarding to friends
- [x] Refund/cancellation (within policy)
- [x] Ticket validity period

### 2.8 Event Gallery & Photos

#### Event Gallery Management (Organizer)
- [x] Upload event photos post-event
- [x] Batch photo upload
- [x] Photo organization (albums within event)
- [x] Photo captions
- [x] Featured photo selection
- [x] Photo order/reordering
- [x] Delete photo
- [x] Gallery privacy settings
- [x] Tag attendees in photos (optional)

#### Event Gallery View (Public)
- [x] Gallery grid/carousel display
- [x] Lightbox photo viewer
- [x] Photo count display
- [x] Photo download option (enable/disable by organizer)
- [x] Share photo option
- [x] Photo like/react (if enabled)

### 2.9 Event Sponsorship & Banner Ads

#### Sponsorship Opportunities
- [x] Sponsor tier options (Bronze, Silver, Gold, Platinum)
- [x] Sponsor pricing per tier
- [x] Sponsor benefits per tier:
  - Logo placement on event page
  - Logo in event gallery
  - Social media mention
  - Email mention
  - Website mention
  - Booth space (for physical events)
- [x] Sponsor management dashboard
- [x] Sponsor logo upload
- [x] Sponsor website link

#### Promotional Banners
- [x] Banner ad slots on event page
- [x] Banner sizes and specifications
- [x] Banner upload
- [x] Banner click tracking
- [x] Banner rotation (if multiple)

### 2.10 Event Notifications & Reminders

#### Automated Notifications
- [x] Event created notification to followers
- [x] Event published notification
- [x] RSVP confirmation to attendee
- [x] Event reminder (3 days before)
- [x] Event reminder (1 day before)
- [x] Event reminder (2 hours before)
- [x] Event started notification
- [x] Event completed notification
- [x] New photo uploaded notification
- [x] Attendee welcome email
- [x] Thank you email (post-event)

#### Notification Channels
- [x] In-app notifications
- [x] Email notifications
- [x] SMS notifications (optional)
- [x] WhatsApp notifications (optional)
- [x] Push notifications (if PWA)
- [x] Notification preference toggle

---

## 3) EVENT ADMIN CONTROLS

### 3.1 Admin Event Management Dashboard
**Phase 1 (Implemented):**
- [x] All events list with filters (status, type, city, search)
- [x] Event details view (opens in new tab)
- [x] Create event (full form)
- [x] Edit event (full form with data loading)
- [x] Delete event (with RSVP checks)
- [x] Toggle featured status
- [x] Status management (draft/published/cancelled)
- [x] Stats cards (total, published, draft, RSVPs)
- [x] Pagination
- [x] Debounced search

**Phase 2 (Planned):**
- [ ] Approve/reject queue for photographer events
- [ ] Flag/report system
- [ ] Bulk status updates
- [ ] Event visibility toggle
- [ ] Suspend event feature
- [ ] Archive old events

### 3.2 Event Moderation
- [x] Event content review queue
- [x] Approve new events
- [x] Review event gallery photos
- [x] Approve event sponsorships
- [x] Handle event reports
- [x] Suspend event (temporarily)
- [x] Archive completed events

### 3.3 Event Analytics
- [x] Total events count
- [x] Active events
- [x] Completed events
- [x] Total RSVPs across events
- [x] Top events by attendance
- [x] Event revenue (from tickets)
- [x] Attendance rate
- [x] Popular event categories
- [x] Event trends over time

---

## 4) EVENT ORGANIZER DASHBOARD

### 4.1 Dashboard Overview
- [x] Event list (My Events)
- [x] Filter by status (Upcoming, Ongoing, Completed, Cancelled)
- [x] Event quick stats:
  - Total views
  - Total RSVPs
  - Ticket sales (if ticketed)
  - Revenue

### 4.2 Event Management
**Phase 1 (API Implemented, UI Pending):**
- [x] Create event (API: POST /photographer/events)
  - Events start as 'draft' (requires admin approval)
  - Max attendees: 500 (vs admin: unlimited)
  - Max price: ৳50,000 (vs admin: unlimited)
  - Max duration: 24 hours (vs admin: unlimited)
  - Cannot set featured or verified
- [x] Edit own events (API: PUT /photographer/events/{id})
  - Editing published events reverts to draft
  - Awaits re-approval from admin
- [x] Delete own events (API: DELETE /photographer/events/{id})
  - Cannot delete published events with RSVPs
- [x] Cancel event (API: POST /photographer/events/{id}/cancel)
- [x] View own events (API: GET /photographer/events)

**Phase 2 (UI Dashboard):**
- [ ] Photographer event dashboard tab in PhotographerDashboard.vue
- [ ] Create event form for photographers
- [ ] Edit event form
- [ ] Event list view with stats
- [ ] Duplicate event feature
- [ ] View event link button

### 4.3 Attendee Management
- [x] View attendee list
- [x] Export attendees (CSV, Excel)
- [x] Send message to all attendees
- [x] Send message to subset of attendees
- [x] Check-in attendees (for physical events)
- [x] Remove attendee
- [x] Tag attendees (VIP, volunteer, etc.)

### 4.4 Ticket Management (If Ticketed)
- [x] View ticket sales
- [x] Ticket revenue breakdown
- [x] Ticket sales chart
- [x] Export ticket list
- [x] Refund ticket
- [x] Ticket analytics

### 4.5 Gallery Management
- [x] Upload event photos
- [x] Organize photos into albums
- [x] Edit photo captions
- [x] Delete photo
- [x] Publish gallery (make public)
- [x] Gallery view count
- [x] Photo download tracking

### 4.6 Event Analytics
- [x] Total views
- [x] Total RSVPs
- [x] RSVP trends (daily)
- [x] Attendee geographic distribution
- [x] Device type breakdown
- [x] Traffic source (direct, search, referral)
- [x] Revenue (if ticketed)
- [x] Data export (CSV, PDF)

### 4.7 Event Communications
- [x] In-app messaging with attendees
- [x] Send announcement to attendees
- [x] Send reminder notifications
- [x] Event Q&A section (optional)
- [x] Comments on event page

---

## 5) PARTICIPANT/CLIENT DASHBOARD

### 5.1 My Events
- [x] Events I'm attending (RSVP'd)
- [x] Events I'm interested in
- [x] Filter by status (Upcoming, Past, Cancelled)
- [x] Event reminders
- [x] Cancel RSVP option

### 5.2 Saved Events
- [x] Save event to wishlist
- [x] Saved events list
- [x] Remove from saved
- [x] Share saved events

### 5.3 My Tickets (If Ticketed)
- [x] View purchased tickets
- [x] Digital ticket (QR code)
- [x] Ticket PDF
- [x] Forward ticket to friend
- [x] Request refund
- [x] Ticket status

### 5.4 Past Events & Galleries
- [x] Events I attended (history)
- [x] View event photos
- [x] Like/react to photos
- [x] Download photos (if allowed)
- [x] Share photos
- [x] Review event (optional)

---

## 6) EVENT PAGES & URLS

```
Public Pages:
- /events (all events listing)
- /events?category=workshop (events by category)
- /events?city=dhaka (events by location)
- /events/[event-slug] (event detail page)
- /events/[event-slug]/gallery (event gallery)
- /events/[event-slug]/attendees (public attendee list)

Organizer Pages:
- /dashboard/events (my events)
- /dashboard/events/create (create event)
- /dashboard/events/[event-id]/edit (edit event)
- /dashboard/events/[event-id]/attendees (attendee management)
- /dashboard/events/[event-id]/gallery (gallery management)
- /dashboard/events/[event-id]/analytics (event analytics)
- /dashboard/events/[event-id]/tickets (ticket management)

Participant Pages:
- /dashboard/events (my events)
- /dashboard/events/saved (saved events)
- /dashboard/tickets (my tickets)

Admin Pages:
- /admin/events (all events)
- /admin/events/[event-id] (event details)
- /admin/events/[event-id]/edit (edit event)
```

---

## 7) EVENT DATABASE SCHEMA

### Events Table
```
id (primary key)
uuid (unique identifier)
organizer_id (foreign key → users)
category_id (foreign key → event_categories)
title (string)
slug (unique slug)
description (longtext, rich text)
short_description (string)
cover_image (image path)
banner_image (image path)
event_date (datetime)
event_end_date (datetime, for multi-day events)
start_time (time)
end_time (time)
all_day_event (boolean)
duration_minutes (integer)
location (string, venue name)
address (string, full address)
latitude (decimal)
longitude (decimal)
city_id (foreign key → cities)
is_ticketed (boolean)
ticket_price (decimal)
is_paid (boolean)
max_attendees (integer, null for unlimited)
require_registration (boolean)
status (enum: draft, published, completed, cancelled)
is_featured (boolean)
featured_until (timestamp, nullable)
view_count (integer)
rsvp_count (integer)
gallery_published (boolean)
is_verified (boolean)
featured_at (timestamp, nullable)
canceled_at (timestamp, nullable)
created_at (timestamp)
updated_at (timestamp)
deleted_at (timestamp, nullable - soft delete)
```

### Event Categories Table
```
id (primary key)
name (string)
slug (string)
description (text)
icon (string, icon class or image)
display_order (integer)
is_active (boolean)
created_at (timestamp)
updated_at (timestamp)
```

### Event Organizers Table (Extended User Info)
```
id (primary key)
user_id (foreign key → users)
total_events_organized (integer)
total_attendees (integer)
average_rating (decimal)
is_verified (boolean)
created_at (timestamp)
updated_at (timestamp)
```

### Event RSVPs Table
```
id (primary key)
event_id (foreign key → events)
user_id (foreign key → users)
rsvp_status (enum: going, maybe, not_going)
responded_at (timestamp)
check_in_at (timestamp, nullable - for physical events)
ticket_purchased (boolean)
special_requirements (text, nullable)
created_at (timestamp)
updated_at (timestamp)
deleted_at (timestamp, nullable - cancel RSVP)
```

### Event Tickets Table
```
id (primary key)
event_id (foreign key → events)
ticket_type (string) - Early Bird, Regular, VIP
price (decimal)
quantity_total (integer)
quantity_sold (integer)
quantity_available (calculated)
sale_start_at (timestamp)
sale_end_at (timestamp)
benefits (text)
is_active (boolean)
created_at (timestamp)
updated_at (timestamp)
```

### Ticket Purchases Table
```
id (primary key)
uuid (unique)
ticket_id (foreign key → event_tickets)
event_id (foreign key → events)
user_id (foreign key → users)
quantity (integer)
total_price (decimal)
payment_status (enum: pending, completed, refunded, failed)
qr_code (string, unique QR code)
ticket_pdf_url (string)
refund_requested (boolean)
refund_reason (text, nullable)
refund_approved_at (timestamp, nullable)
is_checked_in (boolean)
checked_in_at (timestamp, nullable)
created_at (timestamp)
updated_at (timestamp)
```

### Event Gallery Photos Table
```
id (primary key)
event_id (foreign key → events)
uploader_id (foreign key → users)
image_path (string)
image_url (string)
thumbnail_url (string)
caption (string, nullable)
order (integer)
is_featured (boolean)
view_count (integer)
like_count (integer)
can_download (boolean)
created_at (timestamp)
updated_at (timestamp)
```

### Event Sponsors Table
```
id (primary key)
event_id (foreign key → events)
sponsor_id (foreign key → users)
tier (enum: bronze, silver, gold, platinum)
price (decimal)
sponsor_name (string)
sponsor_logo_url (string)
sponsor_website (string)
sponsor_description (text)
status (enum: pending, approved, active, completed)
payment_status (enum: pending, completed, refunded)
approved_at (timestamp, nullable)
created_at (timestamp)
updated_at (timestamp)
```

### Event Hashtags Table
```
id (primary key)
event_id (foreign key → events)
hashtag (string)
created_at (timestamp)
```

### Event Reports Table (Moderation)
```
id (primary key)
event_id (foreign key → events)
reported_by (foreign key → users)
reason (string)
description (text)
status (enum: pending, investigating, resolved, dismissed)
admin_notes (text, nullable)
resolved_at (timestamp, nullable)
created_at (timestamp)
updated_at (timestamp)
```

---

## 8) EVENT API ROUTES (Laravel)

```
// Public Routes
GET    /api/events
GET    /api/events/{slug}
GET    /api/events/{id}/gallery
GET    /api/events/{id}/attendees
GET    /api/events/category/{category_id}
GET    /api/events/city/{city_id}
POST   /api/events/{id}/rsvp
DELETE /api/events/{id}/rsvp
GET    /api/events/{id}/tickets
POST   /api/events/{id}/tickets/purchase

// Organizer Routes (Protected)
POST   /api/events
PUT    /api/events/{id}
DELETE /api/events/{id}
POST   /api/events/{id}/publish
POST   /api/events/{id}/cancel
GET    /api/events/{id}/attendees
POST   /api/events/{id}/attendees/export
POST   /api/events/{id}/attendees/message
DELETE /api/events/{id}/attendees/{attendee_id}
GET    /api/events/{id}/analytics
POST   /api/events/{id}/gallery
DELETE /api/events/{id}/gallery/{photo_id}
GET    /api/events/{id}/tickets/sales
POST   /api/events/{id}/tickets/refund/{purchase_id}

// Admin Routes (Protected)
GET    /api/admin/events
GET    /api/admin/events/{id}
PUT    /api/admin/events/{id}
DELETE /api/admin/events/{id}
POST   /api/admin/events/{id}/approve
POST   /api/admin/events/{id}/feature
GET    /api/admin/events/analytics
POST   /api/admin/events/{id}/sponsor/approve
```

---

## 9) EVENT INTEGRATION WITH OTHER MODULES

### Competition Module Integration
- Events can host competitions
- Competition submission during event
- Public voting at event
- Event page links to competition

### Directory Module Integration
- Photographer event history on profile
- Events organized by photographer visible
- Event link in photographer dashboard
- Attendees can view photographer profile from event

### Booking Module Integration
- Event attendees can see "book photographer" CTA
- Event photographers can accept bookings from attendees
- Post-event booking offer in email

### Payment Module Integration
- Ticket sales payment processing
- Sponsorship fees payment
- Payout to organizer (after fee)
- Refund handling

### Notification Module Integration
- Event reminders
- RSVP confirmations
- Attendee notifications
- Sponsor notifications

---

## 10) EVENT SUCCESS METRICS

| Metric | Target |
|--------|--------|
| Monthly Events Created | 50-100 |
| Avg Attendees per Event | 20-50 |
| Event Conversion Rate (view → RSVP) | 15-20% |
| Ticket Revenue per Event | $50-500 |
| Sponsorship Revenue per Event | $100-1000 |
| Attendee Satisfaction Rating | 4.5+ stars |
| Event Gallery Views | 500-2000 per event |

