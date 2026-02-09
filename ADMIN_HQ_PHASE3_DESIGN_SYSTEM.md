# PHASE 3 & 5: Premium Admin HQ Design & Architecture

**Generated:** February 4, 2026  
**Status:** FINAL DESIGN READY FOR IMPLEMENTATION  
**Target:** Transform `/admin/dashboard` into professional "Admin HQ"

---

## 🎨 DESIGN SYSTEM & BRAND GUIDELINES

### Color Palette (Photographer SB Brand)
```
Primary Colors:
- Burgundy: #6c0b1a (primary actions)
- Burgundy Dark: #4a0812 (hover states)
- Burgundy Light: #f3e5e7 (backgrounds)

Accent Colors:
- Orange: #ff9500 (warnings, alerts)
- Green: #10b981 (success, active)
- Red: #ef4444 (errors, critical)
- Blue: #3b82f6 (info, neutral)

Neutrals:
- Gray 900: #111827 (text)
- Gray 600: #4b5563 (secondary text)
- Gray 100: #f3f4f6 (backgrounds)
- White: #ffffff (card backgrounds)
```

### Typography Hierarchy
```
H1: 32px, 700 weight (page titles)
H2: 24px, 700 weight (section titles)
H3: 20px, 600 weight (subsection titles)
H4: 16px, 600 weight (card titles)
Body: 14px, 400 weight (regular text)
Small: 12px, 400 weight (helper text, labels)
```

### Component Standards
```
Border Radius: 8px (all cards, buttons, inputs)
Box Shadow: 0 1px 3px rgba(0,0,0,0.1) (light), 0 10px 25px rgba(0,0,0,0.15) (elevated)
Spacing: 4px grid (4, 8, 12, 16, 24, 32, 48, 64px)
Transitions: 200ms ease-in-out (all interactive elements)
```

---

## 📐 NEW ADMIN HQ LAYOUT ARCHITECTURE

### LAYOUT STRUCTURE
```
┌─────────────────────────────────────────────────────┐
│  ADMIN HEADER (persistent top, sticky)              │  60px
├─────────────────────────────────────────────────────┤
│    │                                                 │
│ S  │ MAIN CONTENT AREA                              │
│ I  │ ┌─────────────────────────────────────────────┐ │
│ D  │ │ Breadcrumb / Page Title                    │ │  8px
│ E  │ ├─────────────────────────────────────────────┤ │
│ B  │ │                                              │ │
│ A  │ │ DASHBOARD / PAGE CONTENT                    │ │  dynamic
│ R  │ │ (Scrollable)                                │ │
│    │ │                                              │ │
│    │ └─────────────────────────────────────────────┘ │
│ (   │                                                 │
│ 24  │                                                 │
│ 0   │                                                 │
│ px  │                                                 │
│ @   │                                                 │
│ md  │                                                 │
│ )   │                                                 │
├─────────────────────────────────────────────────────┤
│  FOOTER (optional, desktop only)                     │
└─────────────────────────────────────────────────────┘
```

---

## 🏗️ ADMIN HQ DASHBOARD LAYOUT (NEW)

### SECTION 1: HEADER SECTION (160px)
```
┌────────────────────────────────────────────────────┐
│                                                     │
│  🏢 Admin HQ                    System: Healthy ✓  │ 16px padding
│  Real-time Platform Management  Last sync: 2m ago  │
│                                                     │
│ [🔄 Refresh]  [Time Range: This Month ▼]          │
│                                                     │
└────────────────────────────────────────────────────┘
```

**Components:**
- Logo + Title with icon
- System health badge (green/yellow/red)
- Last sync timestamp
- Refresh button
- Time range dropdown

---

### SECTION 2: LIVE KPIs (FIRST ROW - 120px)

8 cards, 4 per row (desktop), 1-2 per row (mobile)

```
Card Layout (each):
┌─────────────────────┐
│ Icon (bg color)  │  │
│ Label Text       │  │  Stat Value: Large, bold
│ 42,531          │  │  
│ ↑ 12.5% vs last │  │
└─────────────────────┘
```

**KPI Cards:**
1. **Total Users** (Blue)
   - Value: Count
   - Secondary: Active count
   - Trend: % change

2. **Total Photographers** (Purple)
   - Value: Count
   - Secondary: Verified count
   - Trend: % change

3. **Total Competitions** (Orange)
   - Value: Count
   - Secondary: Active count
   - Trend: % change

4. **Pending Verifications** (Red)
   - Value: Count
   - Badge: Alert if > 5
   - Click: Links to verification page

5. **Today's Revenue** (Green)
   - Value: ৳ Amount
   - Secondary: # transactions
   - Trend: % change

6. **Pending Submissions** (Orange)
   - Value: Count
   - Badge: Alert if > 10
   - Click: Links to submissions

7. **Active Events** (Blue)
   - Value: Count
   - Secondary: Registered users
   - Trend: % change

8. **Pending Bookings** (Purple)
   - Value: Count
   - Secondary: Total value
   - Click: Links to bookings

---

### SECTION 3: ACTION CENTER (88px)

**Title:** "Quick Actions"  
**Layout:** Horizontal scrollable button group (mobile), grid (desktop)

**Quick Action Buttons (6 major):**

| Icon | Label | Route | Permission |
|------|-------|-------|-----------|
| ➕ | Create Event | `/admin/events/create` | admin |
| 🎯 | Create Competition | `/admin/competitions/create` | admin |
| 👤 | Add Judge | `/admin/judges/create` | admin |
| 🎤 | Add Mentor | `/admin/mentors/create` | admin |
| 📢 | Create Notice | `/admin/notices/create` | admin |
| ⚙️ | Settings | `/admin/settings` | admin |

**Dev-only button (if APP_ENV !== production):**
| 🧹 | Clear Cache | POST `/admin/dev/clear-cache` | super_admin |

---

### SECTION 4: OPERATIONS PANEL (400px)

**Title:** "Active Operations"  
**Layout:** 3-column grid (desktop), 1 column (mobile)

#### Widget 1: Latest Submissions (Pending)
```
┌──────────────────────────┐
│ 📋 Latest Submissions     │
│ Pending Review (12)       │
├──────────────────────────┤
│ ┌─ Competition           │
│ │ 📸 Submission 1        │ → Link to detail
│ │ User: John Doe        │
│ │ Time: 2 hours ago     │
│ ├─ Competition           │
│ │ 📸 Submission 2        │
│ │ User: Jane Smith      │
│ │ Time: 4 hours ago     │
│ └─ ...max 5 rows...     │
├──────────────────────────┤
│ [View All 12 →]          │
└──────────────────────────┘
```

#### Widget 2: Verification Requests (Pending)
```
┌──────────────────────────┐
│ ✓ Verifications Pending   │
│ Waiting for Review (8)   │
├──────────────────────────┤
│ ┌─ Photographer 1        │
│ │ 🆔 Portfolio Review   │
│ │ Date: 3 days old      │
│ ├─ Photographer 2        │
│ │ 🆔 Document Review    │
│ │ Date: 5 days old      │
│ └─ ...max 5 rows...     │
├──────────────────────────┤
│ [Review All 8 →]         │
└──────────────────────────┘
```

#### Widget 3: Recent System Activity
```
┌──────────────────────────┐
│ 📊 System Activity        │
│ Last 24 Hours            │
├──────────────────────────┤
│ 🟢 Users Joined (34)      │
│ 🟡 Errors Logged (2)      │
│ 🔵 Bookings (12)          │
│ 🟣 Reviews Created (8)    │
│ 🟠 Payouts (5)            │
│                           │
│ [View Activity Logs →]    │
└──────────────────────────┘
```

---

### SECTION 5: MODULES NAVIGATION GRID (500px+)

**Title:** "Management Modules"  
**Layout:** Responsive 2x6 grid (4 cards per row on desktop, 2 on tablet, 1 on mobile)

**Module Card Template:**
```
┌─────────────────────────┐
│ 🎭 Module Name          │ Icon + Title
│ Description text        │ Light gray text
├─────────────────────────┤
│ Stats:                  │
│ 24 Active  3 Pending   │ Badge counts
│                         │
│ [View] [Create] [More] │ Action buttons
└─────────────────────────┘
```

**Module Cards (12 total):**

1. **Photographers**
   - Icon: 📷
   - Stats: "234 Total | 12 Pending"
   - Buttons: View All | Add New | Pending Verifications

2. **Events**
   - Icon: 📅
   - Stats: "18 Active | 42 Past"
   - Buttons: View All | Create Event | Attendance

3. **Competitions**
   - Icon: 🏆
   - Stats: "6 Active | 84 Submissions"
   - Buttons: View All | Create | Submissions

4. **Judges**
   - Icon: ⚖️
   - Stats: "12 Active | 8 Inactive"
   - Buttons: Manage | Add Judge | Assignments

5. **Mentors**
   - Icon: 🎤
   - Stats: "8 Active | 15 Total"
   - Buttons: Manage | Add | Reorder

6. **Sponsors**
   - Icon: 💼
   - Stats: "5 Active | 12 Competitions"
   - Buttons: Manage | Add | Statistics

7. **Reviews & Feedback**
   - Icon: ⭐
   - Stats: "345 Approved | 12 Pending"
   - Buttons: Moderate | View | Report

8. **Bookings & Payments**
   - Icon: 💳
   - Stats: "₹2.5L Today | 28 Pending"
   - Buttons: Manage | Transactions | Refunds

9. **Messages & Support**
   - Icon: 💬
   - Stats: "5 Unread | 234 Resolved"
   - Buttons: View | Response Center | Reports

10. **SEO & Tracking**
    - Icon: 🔍
    - Stats: "42 Pages | GA4 Connected"
    - Buttons: SEO Settings | Sitemap | Analytics

11. **System Health**
    - Icon: 💚
    - Stats: "✓ All Systems | 0 Critical"
    - Buttons: Health Check | Error Logs | Database

12. **Settings & Config**
    - Icon: ⚙️
    - Stats: "Site Config | Integrations"
    - Buttons: General | Site Links | Integrations

---

### SECTION 6: BOTTOM SECTION - ANALYTICS (400px)

#### Chart 1: Platform Growth (if time range > 1 week)
```
Line Chart: Users/Events/Bookings over time
X-axis: Dates
Y-axis: Count
Legend: 3 lines
```

#### Chart 2: Revenue Breakdown (if applicable)
```
Pie Chart: Revenue by source
- Bookings: 60%
- Event Registration: 25%
- Other: 15%
```

---

## 📱 RESPONSIVE DESIGN BREAKPOINTS

### Mobile (< 640px)
- Sidebar: Hidden (hamburger menu)
- Cards: Single column
- Module grid: 1 column
- Charts: Hidden or simplified

### Tablet (640px - 1024px)
- Sidebar: Collapsible
- Cards: 2 columns
- Module grid: 2 columns
- Charts: Simplified

### Desktop (> 1024px)
- Sidebar: Always visible
- Cards: 4 columns
- Module grid: 4 columns
- Charts: Full size

---

## 🗂️ FILE STRUCTURE FOR NEW ADMIN HQ

```
resources/
├── js/
│   ├── components/
│   │   ├── AdminLayout.vue               NEW - Persistent layout wrapper
│   │   ├── AdminSidebar.vue              NEW - Module navigation sidebar
│   │   ├── AdminHeader.vue               NEW - Top header bar
│   │   ├── AdminFooter.vue               NEW - Footer (optional)
│   │   ├── AdminDashboard.vue            UPDATE - Redesigned with new sections
│   │   ├── AdminQuickNav.vue             DELETE or consolidate
│   │   ├── KPICard.vue                   NEW - Reusable KPI card
│   │   ├── ModuleCard.vue                NEW - Module navigation card
│   │   ├── StatWidget.vue                NEW - Statistics widget
│   │   └── Chart*.vue                    NEW - Chart components
│   │
│   ├── Pages/
│   │   ├── Admin/
│   │   │   ├── Dashboard.vue             Main admin dashboard page
│   │   │   ├── Judges/
│   │   │   │   ├── Index.vue             NEW
│   │   │   │   ├── Create.vue            NEW
│   │   │   │   └── Edit.vue              NEW
│   │   │   ├── Sponsors/
│   │   │   │   ├── Index.vue             NEW
│   │   │   │   ├── Create.vue            NEW
│   │   │   │   └── Edit.vue              NEW
│   │   │   ├── Reviews/
│   │   │   │   ├── Index.vue             NEW
│   │   │   │   └── Detail.vue            NEW
│   │   │   ├── Bookings/
│   │   │   │   ├── Index.vue             NEW
│   │   │   │   └── Detail.vue            NEW
│   │   │   ├── Transactions/
│   │   │   │   ├── Index.vue             NEW
│   │   │   │   └── Detail.vue            NEW
│   │   │   ├── ActivityLogs/
│   │   │   │   └── Index.vue             NEW
│   │   │   ├── Hashtags/
│   │   │   │   ├── Index.vue             NEW
│   │   │   │   ├── Create.vue            NEW
│   │   │   │   └── Edit.vue              NEW
│   │   │   └── [existing pages]
│   │
│   └── utils/
│       └── adminMenu.js                  NEW - Central menu config
│
├── css/
│   └── admin-hq.css                      NEW - Admin-specific styles
│
└── stores/
    └── adminStore.js                     UPDATE - Admin state management

public/
├── images/
│   └── icons/
│       ├── judge.svg                     NEW - Judge icon
│       ├── sponsor.svg                   NEW - Sponsor icon
│       ├── health.svg                    NEW - Health icon
│       └── [other module icons]
```

---

## 🎛️ ADMIN MENU CONFIGURATION (JSON)

**File:** `resources/js/utils/adminMenu.js`

```javascript
export const adminMenuConfig = {
  header: {
    title: "Admin HQ",
    subtitle: "Platform Management",
    logo: "/images/logo.svg"
  },
  
  modules: [
    // Row 1
    {
      id: "photographers",
      title: "Photographers",
      icon: "📷",
      description: "Manage photographer profiles and verifications",
      stats: { label: "Total", count: "photographers_count" },
      color: "blue",
      actions: [
        { label: "View All", route: "/admin/photographers", icon: "👁" },
        { label: "Pending", route: "/admin/verifications", icon: "⏳" },
        { label: "Add New", route: "/admin/photographers/create", icon: "➕", permission: "create" }
      ]
    },
    
    {
      id: "events",
      title: "Events",
      icon: "📅",
      description: "Create and manage events with attendance tracking",
      stats: { label: "Active", count: "active_events" },
      color: "purple",
      actions: [
        { label: "View All", route: "/admin/events", icon: "👁" },
        { label: "Create", route: "/admin/events/create", icon: "➕", permission: "create" },
        { label: "Attendance", route: "/admin/events/attendance", icon: "📍" }
      ]
    },

    {
      id: "competitions",
      title: "Competitions",
      icon: "🏆",
      description: "Manage competitions, judges, and prize distributions",
      stats: { label: "Active", count: "active_competitions" },
      color: "orange",
      actions: [
        { label: "View All", route: "/admin/competitions", icon: "👁" },
        { label: "Create", route: "/admin/competitions/create", icon: "➕", permission: "create" },
        { label: "Submissions", route: "/admin/competitions/submissions", icon: "📋" }
      ]
    },

    {
      id: "judges",
      title: "Judges",
      icon: "⚖️",
      description: "Manage competition judges and assignments",
      stats: { label: "Active", count: "active_judges" },
      color: "red",
      actions: [
        { label: "View All", route: "/admin/judges", icon: "👁" },
        { label: "Add New", route: "/admin/judges/create", icon: "➕", permission: "create" },
        { label: "Assignments", route: "/admin/judges/assignments", icon: "🎯" }
      ]
    },

    // Row 2
    {
      id: "mentors",
      title: "Mentors",
      icon: "🎤",
      description: "Add and manage mentors for the platform",
      stats: { label: "Total", count: "mentors_count" },
      color: "green",
      actions: [
        { label: "View All", route: "/admin/mentors", icon: "👁" },
        { label: "Add New", route: "/admin/mentors/create", icon: "➕", permission: "create" }
      ]
    },

    {
      id: "sponsors",
      title: "Sponsors",
      icon: "💼",
      description: "Manage platform and competition sponsors",
      stats: { label: "Active", count: "active_sponsors" },
      color: "blue",
      actions: [
        { label: "Platform", route: "/admin/sponsors", icon: "🌍" },
        { label: "Competition", route: "/admin/competition-sponsors", icon: "🏆" },
        { label: "Add New", route: "/admin/sponsors/create", icon: "➕", permission: "create" }
      ]
    },

    {
      id: "reviews",
      title: "Reviews",
      icon: "⭐",
      description: "Moderate and manage user reviews",
      stats: { label: "Pending", count: "pending_reviews" },
      color: "yellow",
      actions: [
        { label: "View All", route: "/admin/reviews", icon: "👁" },
        { label: "Pending", route: "/admin/reviews?status=pending", icon: "⏳" },
        { label: "Reported", route: "/admin/reviews?status=reported", icon: "🚩" }
      ]
    },

    {
      id: "bookings",
      title: "Bookings",
      icon: "💳",
      description: "Manage bookings and payments",
      stats: { label: "Pending", count: "pending_bookings" },
      color: "purple",
      actions: [
        { label: "View All", route: "/admin/bookings", icon: "👁" },
        { label: "Transactions", route: "/admin/transactions", icon: "💰" },
        { label: "Pending", route: "/admin/bookings?status=pending", icon: "⏳" }
      ]
    },

    // Row 3
    {
      id: "messages",
      title: "Messages",
      icon: "💬",
      description: "Manage contact messages and support",
      stats: { label: "Unread", count: "unread_messages" },
      color: "blue",
      actions: [
        { label: "View All", route: "/admin/contact-messages", icon: "👁" },
        { label: "Unread", route: "/admin/contact-messages?status=unread", icon: "📬" }
      ]
    },

    {
      id: "seo",
      title: "SEO & Analytics",
      icon: "🔍",
      description: "Manage SEO, sitemap, and tracking",
      stats: { label: "Pages", count: "seo_meta_count" },
      color: "green",
      actions: [
        { label: "SEO Meta", route: "/admin/seo", icon: "🔍" },
        { label: "Sitemap", route: "/admin/system-health/sitemap", icon: "🗺" },
        { label: "Generate", route: "/admin/seo/generate", icon: "⚙️", permission: "create" }
      ]
    },

    {
      id: "system",
      title: "System Health",
      icon: "💚",
      description: "Monitor system health and errors",
      stats: { label: "Errors", count: "error_count" },
      color: "red",
      actions: [
        { label: "Status", route: "/admin/system-health", icon: "📊" },
        { label: "Error Logs", route: "/admin/system-health/errors", icon: "🐛" },
        { label: "Activity", route: "/admin/activity-logs", icon: "📝" }
      ]
    },

    {
      id: "settings",
      title: "Settings",
      icon: "⚙️",
      description: "Configure platform settings and integrations",
      stats: { label: "Config", count: "settings_count" },
      color: "gray",
      actions: [
        { label: "General", route: "/admin/settings", icon: "⚙️" },
        { label: "Site Links", route: "/admin/settings/site-links", icon: "🔗" },
        { label: "Hashtags", route: "/admin/hashtags", icon: "#️⃣" }
      ]
    }
  ]
};
```

---

## 🔐 ADMIN RBAC (Role-Based Access Control)

**Permissions to enforce on each module:**

```javascript
const adminPermissions = {
  photographers: ["view", "create", "edit", "delete", "verify"],
  events: ["view", "create", "edit", "delete"],
  competitions: ["view", "create", "edit", "delete", "manage_judges"],
  judges: ["view", "create", "edit", "delete"],
  mentors: ["view", "create", "edit", "delete"],
  sponsors: ["view", "create", "edit", "delete"],
  reviews: ["view", "moderate", "delete"],
  bookings: ["view", "update_status", "refund"],
  messages: ["view", "respond"],
  seo: ["view", "create", "edit", "generate"],
  system: ["view", "view_errors", "clear_cache"],
  settings: ["view", "edit"]
};
```

---

## 📊 IMPLEMENTATION CHECKLIST

### Phase 3.1: Layout Components (2 days)
- [ ] Create AdminLayout.vue wrapper
- [ ] Create AdminSidebar.vue navigation
- [ ] Create AdminHeader.vue top bar
- [ ] Create KPICard.vue component
- [ ] Create ModuleCard.vue component
- [ ] Set up admin styles in admin-hq.css
- [ ] Configure adminMenu.js
- [ ] Update app.js routing with layout nesting

### Phase 3.2: Dashboard Redesign (2 days)
- [ ] Redesign AdminDashboard.vue
- [ ] Add KPI section (8 cards)
- [ ] Add Quick Actions section
- [ ] Add Operations Panel (3 widgets)
- [ ] Add Modules Grid (12 cards)
- [ ] Add Analytics section
- [ ] Implement responsive design
- [ ] Add loading states

### Phase 5.1: Missing Pages (3 days)
- [ ] Build Judges management page
- [ ] Build Sponsors management page
- [ ] Build Reviews moderation page
- [ ] Build Bookings management page
- [ ] Build Transactions page
- [ ] Build Activity Logs page
- [ ] Build Hashtags management page

### Phase 5.2: Missing Actions (2 days)
- [ ] Add bulk promote to judge/mentor
- [ ] Add set all prizes button
- [ ] Add calculate winners button
- [ ] Add announce winners button
- [ ] Add generate certificates button
- [ ] Add bulk user approval UI

### Phase 5.3: Missing Widgets (1 day)
- [ ] Add bookings KPI card
- [ ] Add transactions KPI card
- [ ] Add reviews KPI card
- [ ] Add scoring statistics widget
- [ ] Add category statistics widget

### Phase 5.4: Testing & Polish (1 day)
- [ ] Test all links
- [ ] Test responsive design
- [ ] Test permission enforcement
- [ ] Fix bugs and edge cases
- [ ] Performance optimization
- [ ] Documentation

---

## 🚀 LAUNCH READINESS

**Go-Live Checklist:**
- [ ] All 12 module cards functional
- [ ] All dashboard links working (0 broken)
- [ ] Zero console errors
- [ ] Mobile responsive tested (3 breakpoints)
- [ ] Performance < 2s load time
- [ ] All features tested by stakeholders
- [ ] Admin documentation complete
- [ ] Backup database before deploy

---

**Status:** Design complete, ready for implementation  
**Next Phase:** Phase 6 - Direct Dashboard Connections  
**Last Updated:** February 4, 2026
