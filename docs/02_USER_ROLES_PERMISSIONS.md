# User Roles & Permissions Matrix

## USER ROLES OVERVIEW

### 1. GUEST USER (Unauthenticated)
**Access Level**: Public read-only

**Permissions**:
- [x] Browse photographer profiles
- [x] View portfolio galleries
- [x] Search photographers by filters
- [x] View reviews and ratings
- [x] Read blog posts
- [x] View events (listing only)
- [x] View competitions (listing only)
- [x] View competition winners

**Restrictions**:
- [ ] Cannot book or send inquiries
- [ ] Cannot submit reviews
- [ ] Cannot participate in competitions
- [ ] Cannot save favorites
- [ ] Cannot RSVP to events
- [ ] Cannot vote on competition entries

---

### 2. CLIENT / CUSTOMER ACCOUNT
**Access Level**: Authenticated user (B2C)

**Permissions**:
- [x] All guest permissions
- [x] Create and manage account profile
- [x] Send photographer inquiries
- [x] Track inquiry/booking status
- [x] Communicate with photographers (messaging)
- [x] Submit reviews (after booking completed)
- [x] Edit/delete own reviews
- [x] Save favorite photographers
- [x] Save searches
- [x] Manage wishlist
- [x] RSVP to events
- [x] Vote on competition entries (with OTP verification)
- [x] View booking invoice/receipt
- [x] Report photographer (inappropriate content)
- [x] View personal dashboard (bookings, favorites, reviews)

**Restrictions**:
- [ ] Cannot create competitions
- [ ] Cannot create events
- [ ] Cannot upload portfolio
- [ ] Cannot access photographer dashboard
- [ ] Cannot verify other users

---

### 3. PHOTOGRAPHER ACCOUNT (Individual)
**Access Level**: Authenticated professional

**Permissions**:
- [x] All client permissions
- [x] Create and manage photographer profile
- [x] Add/edit portfolio (photos, videos, albums)
- [x] Set packages and pricing
- [x] Set availability calendar
- [x] Accept/reject inquiries
- [x] Track bookings
- [x] Provide booking quotes
- [x] View reviews and ratings
- [x] Reply to reviews
- [x] Report reviews (inappropriate)
- [x] View analytics (bookings, profile views, inquiries)
- [x] Create competitions
- [x] Create events
- [x] Participate in competitions (submit photos)
- [x] Upload portfolio watermark options
- [x] Download certificates (competition winner)
- [x] Access photographer dashboard
- [x] Manage subscription/upgrade plans
- [x] View transaction history
- [x] Upload NID/documents for verification
- [x] Request photographer verification badge
- [x] Message clients
- [x] Set service area by location/radius

**Restrictions**:
- [ ] Cannot moderate other photographers
- [ ] Cannot access admin panel
- [ ] Cannot approve verifications
- [ ] Cannot manage platform categories
- [ ] Cannot view other photographers' analytics

---

### 4. STUDIO ACCOUNT (Team/Multi-Member)
**Access Level**: Authenticated team/organization

**Permissions**:
- [x] All photographer permissions
- [x] Add team members (with role assignments)
- [x] Manage team member access (Owner, Manager, Photographer)
- [x] Centralized studio profile
- [x] Multiple sub-profiles under studio
- [x] Shared portfolio and albums
- [x] Assign photographers to bookings
- [x] Team inbox (shared messages)
- [x] Studio-level analytics
- [x] Bulk upload photos
- [x] Manage studio subscription
- [x] Invite team members to competitions
- [x] Studio event hosting

**Team Roles**:

#### Studio Owner
- [x] Full access to all studio features
- [x] Manage billing
- [x] Add/remove team members
- [x] Access financial reports

#### Studio Manager
- [x] Manage bookings and inquiries
- [x] Manage photographers
- [x] View analytics
- [x] [ ] Cannot manage billing
- [x] [ ] Cannot change studio settings

#### Studio Photographer
- [x] Manage own profile/portfolio
- [x] Track own bookings
- [x] [ ] Cannot manage other photographers
- [x] [ ] Cannot change studio settings

---

### 5. MODERATOR (Platform Team)
**Access Level**: Restricted administrative

**Permissions**:
- [x] View all photographer profiles
- [x] View user accounts
- [x] Moderate reviews (flag/hide inappropriate)
- [x] Moderate competition submissions
- [x] Moderate event listings
- [x] Respond to user reports
- [x] Temporarily suspend photographer listings
- [x] Send platform notifications
- [x] Access moderation dashboard
- [x] View user activity logs
- [x] Handle user complaints

**Restrictions**:
- [ ] Cannot delete users
- [ ] Cannot approve verifications
- [ ] Cannot manage platform settings
- [ ] Cannot access financial data
- [ ] Cannot change user roles
- [ ] Cannot create competitions/events

---

### 6. ADMIN (Platform Administrator)
**Access Level**: Full platform access

**Permissions**:
- [x] All moderator permissions
- [x] Manage all users (create, edit, delete, suspend)
- [x] Approve/reject photographer verifications
- [x] Manage categories and tags
- [x] Create and manage competitions
- [x] Approve competition submissions
- [x] Create and manage events
- [x] View all analytics and reports
- [x] Manage subscription plans and pricing
- [x] Process refunds and disputes
- [x] Access financial dashboard
- [x] Manage platform settings and configurations
- [x] Create CMS pages (blog, landing pages)
- [x] Manage promotional banners
- [x] Access audit logs
- [x] Send platform-wide announcements
- [x] Manage SEO settings
- [x] Export data reports
- [x] View competitor's submissions (admin)
- [x] Manage judges panel for competitions
- [x] Approve/reject judge submissions

---

## PERMISSION MATRIX TABLE

| Feature | Guest | Client | Photographer | Studio | Moderator | Admin |
|---------|-------|--------|---------------|--------|-----------|-------|
| Browse Directory | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Send Inquiry | ✗ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Create Profile | ✗ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Manage Portfolio | ✗ | ✗ | ✓ | ✓ | ✗ | ✗ |
| Submit Review | ✗ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Moderate Reviews | ✗ | ✗ | ✗ | ✗ | ✓ | ✓ |
| Create Competition | ✗ | ✗ | ✓ | ✓ | ✗ | ✓ |
| Submit Competition | ✗ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Vote in Competition | ✗ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Judge Competition | ✗ | ✗ | ✗ | ✗ | ✗ | ✓ |
| Create Event | ✗ | ✗ | ✓ | ✓ | ✗ | ✓ |
| RSVP Event | ✗ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Manage Users | ✗ | ✗ | ✗ | ✗ | ✗ | ✓ |
| Access Admin Panel | ✗ | ✗ | ✗ | ✗ | ✓ | ✓ |
| View Analytics | ✗ | ✗ | ✓ | ✓ | ✗ | ✓ |
| Manage Payments | ✗ | ✗ | ✗ | ✗ | ✗ | ✓ |

---

## PERMISSION CATEGORIES (Technical Implementation)

```
Roles:
- guest
- client
- photographer
- studio_owner
- studio_manager
- studio_photographer
- moderator
- admin
- super_admin

Permissions (Gate/Policy based):
- Directory:
  * view-photographers
  * create-profile
  * edit-profile
  * delete-profile
  * upload-portfolio
  * feature-listing
  
- Bookings:
  * create-inquiry
  * manage-bookings
  * accept-booking
  * reject-booking
  
- Reviews:
  * create-review
  * edit-review
  * delete-review
  * moderate-review
  
- Competitions:
  * create-competition
  * submit-competition
  * vote-competition
  * judge-competition
  * approve-competition
  
- Events:
  * create-event
  * edit-event
  * rsvp-event
  
- Admin:
  * access-admin-panel
  * manage-users
  * approve-verification
  * manage-categories
  * manage-payments
  * view-analytics
```

---

## SUBSCRIPTION TIERS & FEATURES

### Free Tier (Default)
- Basic photographer profile
- Up to 20 portfolio photos
- Limited inquiries per month
- Standard listing position
- Public reviews
- Competition participation (free)

### Premium Tier ($5-10/month)
- Enhanced profile with verification badge
- Unlimited portfolio photos/videos
- Unlimited inquiries
- Featured in search results
- Custom branding options
- Advanced analytics
- Email support

### Pro Tier ($15-20/month)
- All premium features
- Studio team members (up to 5)
- Priority support
- Custom landing page
- API access for integrations
- Detailed performance reports
- Booking auto-response templates

### Enterprise (Custom pricing)
- All Pro features
- Unlimited team members
- White-label options
- Advanced integrations
- Dedicated account manager
- Custom analytics
- SLA guarantee

