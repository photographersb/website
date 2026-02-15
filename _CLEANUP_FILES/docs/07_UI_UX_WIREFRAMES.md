# UI/UX Plan & Wireframe Descriptions - Photographer SB

## DESIGN PRINCIPLES

### Mobile-First Philosophy
- 85% of traffic expected from mobile
- All pages must be fully functional on mobile (< 320px width)
- Touch-friendly interface (48px minimum button size)
- Minimal horizontal scrolling
- Fast loading (images optimized, lazy loading)

### Design System
- **Framework**: Tailwind CSS
- **Color Palette**:
  - Primary: #E63946 (Photo Red - vibrant, photography-focused)
  - Secondary: #457B9D (Professional Blue)
  - Success: #06A77D (Trust Green)
  - Warning: #F4A261 (Alert Orange)
  - Dark: #1D3557 (Main text)
  - Light: #F1FAEE (Background)
- **Typography**: Inter, Poppins (modern, clean)
- **Icons**: Font Awesome 6
- **Grid**: 12-column responsive grid

### Interaction Patterns
- Immediate feedback on actions
- Loading states visible
- Error messages clear and actionable
- Success confirmations
- Smooth transitions and animations
- Accessibility (WCAG 2.1 AA compliant)

---

## KEY PAGES WIREFRAMES

## PAGE 1: HOMEPAGE

### Desktop Layout
```
┌─────────────────────────────────────────────────────────┐
│ HEADER: Logo | Search | Photographer Login | Sign Up    │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ HERO SECTION: Full-width banner image                   │
│ • Overlay with text: "Find Your Perfect Photographer"  │
│ • Tagline: "Discover trusted photographers in          │
│    Bangladesh"                                           │
│ • CTA Buttons: "Browse Now" | "Register as Photographer"│
│                                                          │
├─────────────────────────────────────────────────────────┤
│ SEARCH BAR SECTION (Prominent, sticky)                  │
│ • What: [Dropdown: All Categories | Wedding | Event]   │
│ • Where: [City Autocomplete: Dhaka v]                  │
│ • Budget: [Slider: ৳1000 - ৳100000]                    │
│ • [SEARCH BUTTON]                                       │
│                                                          │
├─────────────────────────────────────────────────────────┤
│ FEATURED PHOTOGRAPHERS (3-column grid)                  │
│ ┌──────────┐  ┌──────────┐  ┌──────────┐               │
│ │ Photo    │  │ Photo    │  │ Photo    │               │
│ │ Name     │  │ Name     │  │ Name     │               │
│ │ ★★★★★   │  │ ★★★★★   │  │ ★★★★★   │               │
│ │ Category │  │ Category │  │ Category │               │
│ │ Featured │  │ Featured │  │ Featured │               │
│ └──────────┘  └──────────┘  └──────────┘               │
│                                                          │
├─────────────────────────────────────────────────────────┤
│ UPCOMING EVENTS (Horizontal scroll)                     │
│ › [Event Card] [Event Card] [Event Card] ›             │
│                                                          │
├─────────────────────────────────────────────────────────┤
│ ACTIVE COMPETITION (Hero)                              │
│ • Hero image from competition                           │
│ • Title, deadline, submission count                     │
│ • [Submit Now] button (primary CTA)                    │
│                                                          │
├─────────────────────────────────────────────────────────┤
│ HOW IT WORKS (3-column info boxes)                      │
│ [Browse] → [Inquire] → [Book] → [Enjoy]               │
│                                                          │
├─────────────────────────────────────────────────────────┤
│ CATEGORIES (Grid)                                       │
│ [Wedding] [Event] [Portrait] [Product]                │
│ [Nature] [Commercial] [Others]                         │
│                                                          │
├─────────────────────────────────────────────────────────┤
│ TRUST SECTION                                           │
│ • 500+ verified photographers                           │
│ • 4.8★ average rating                                   │
│ • 10,000+ bookings completed                           │
│                                                          │
├─────────────────────────────────────────────────────────┤
│ TESTIMONIALS (Carousel)                                │
│ • Client photos + review quotes                        │
│ • Star ratings                                          │
│ • Navigation dots                                       │
│                                                          │
├─────────────────────────────────────────────────────────┤
│ FOOTER                                                  │
│ • Links: About | Contact | Blog | Privacy              │
│ • Social: Instagram | Facebook | Twitter               │
│ • Newsletter signup                                     │
└─────────────────────────────────────────────────────────┘
```

### Mobile Layout
```
┌───────────────────────┐
│ Logo | ≡ Menu | User  │ (Top bar, sticky)
├───────────────────────┤
│                       │
│ [Search Bar]          │
│ What? [Dropdown v]    │
│ Where? [Input]        │
│ Budget? [Slider]      │
│ [SEARCH BUTTON]       │
│                       │
├───────────────────────┤
│ FEATURED               │
│ (Vertical scroll)      │
│ ┌─────────────────┐   │
│ │ Photo           │   │
│ │ Name ★★★★★      │   │
│ │ Category        │   │
│ │ [View Profile]  │   │
│ └─────────────────┘   │
│ ┌─────────────────┐   │
│ │ Photo           │   │
│ │ Name ★★★★★      │   │
│ │ Category        │   │
│ │ [View Profile]  │   │
│ └─────────────────┘   │
│ ...                   │
├───────────────────────┤
│ EVENTS (Vertical)     │
│ ┌─────────────────┐   │
│ │ Date: Jan 15    │   │
│ │ Event Title     │   │
│ │ Location        │   │
│ │ 25 going        │   │
│ │ [View Details]  │   │
│ └─────────────────┘   │
│ ...                   │
├───────────────────────┤
│ COMPETITION HERO      │
│ [Large image]         │
│ Title: "Heritage..."  │
│ Deadline: 2 days      │
│ [SUBMIT NOW]          │
├───────────────────────┤
│ CATEGORIES            │
│ (Horizontal scroll)    │
│ [Wedding] [Event]...  │
├───────────────────────┤
│ FOOTER                │
│ About | Contact |...  │
│ Subscribe: [Email]    │
│ [SUBSCRIBE]           │
└───────────────────────┘
```

### Components Used
- Header with sticky search
- Hero banner with CTA
- Search widget (sticky on desktop, prominent on mobile)
- Photographer card component (image, name, rating, category, CTA)
- Event card component (date, title, location, RSVP count)
- Competition hero component
- Category grid/list
- Testimonial carousel
- Footer with links and newsletter

### CTAs Placement
- Primary: "Browse Now" (hero) | "Search Button" (search bar) | "Submit Now" (competition) | "View Profile" (photographer cards)
- Secondary: "Register as Photographer" | "Learn More" (sections)

### Mobile-First Notes
- Full-screen hero image with overlay text
- Single-column layout for photographer cards (vertical scroll)
- Touch-friendly buttons (48px height)
- Sticky header with search bar
- Simplified navigation (hamburger menu on mobile)
- Lazy-loaded images below fold
- Minimal text, maximum images

---

## PAGE 2: PHOTOGRAPHER SEARCH RESULTS

### Desktop Layout
```
┌──────────────────────────────────────────────────────────┐
│ HEADER: Logo | Search | Login | Sign Up                 │
├─────────────────────────────────┬──────────────────────┤
│                                 │                      │
│ FILTERS (Left Sidebar, 25%)     │ RESULTS (75%)        │
│                                 │                      │
│ City: [Dhaka v]                 │ Sort: [Best Match v] │
│ [ ] Mirpur                      │ Found: 245 results   │
│ [ ] Dhanmondi                   │                      │
│ [ ] Gulshan                      │ ┌──────────────┐   │
│                                 │ │ Photo        │   │
│ Category:                       │ │ Name ★4.8    │   │
│ [ ] Wedding                     │ │ Wedding      │   │
│ [ ] Event                       │ │ 850 reviews  │   │
│ [ ] Portrait                    │ │ Featured     │   │
│ [ ] Product                     │ │ [View] [Like]│   │
│ [ ] Nature                      │ └──────────────┘   │
│                                 │                      │
│ Budget:                         │ ┌──────────────┐   │
│ ৳ [Min] - ৳ [Max] Slider        │ │ Photo        │   │
│                                 │ │ Name ★4.6    │   │
│ Rating:                         │ │ Event Photo  │   │
│ [○○○○○] 5+ stars               │ │ 620 reviews  │   │
│ [○○○○○] 4.5+ stars             │ │ [View] [Like]│   │
│ [Verified Only] ☑               │ └──────────────┘   │
│                                 │                      │
│ Availability:                   │ ... (pagination)     │
│ [Available Now]                 │                      │
│ [Available This Month]          │                      │
│                                 │                      │
│ [CLEAR FILTERS]                │                      │
│                                 │                      │
└─────────────────────────────────┴──────────────────────┘
```

### Mobile Layout
```
┌───────────────────────────────┐
│ Logo | [Filters] | [Sort]     │
├───────────────────────────────┤
│ City: [Dhaka v]               │
│ Category: [All v]             │
│ Budget: ৳1K-৳100K Slider      │
│ Rating: [4.5+ ★]              │
│ [ ] Verified Only             │
│ [APPLY FILTERS] [CLEAR]       │
├───────────────────────────────┤
│ Found: 245 results            │
│                               │
│ ┌─────────────────────────┐   │
│ │ Photo                   │   │
│ │ Name ★4.8 (850)         │   │
│ │ Wedding Photography     │   │
│ │ Featured Photographer   │   │
│ │ [View Profile]          │   │
│ │ ♡ Save                  │   │
│ └─────────────────────────┘   │
│                               │
│ ┌─────────────────────────┐   │
│ │ Photo                   │   │
│ │ Name ★4.6 (620)         │   │
│ │ Event Photography       │   │
│ │ [View Profile]          │   │
│ │ ♡ Save                  │   │
│ └─────────────────────────┘   │
│                               │
│ ... (Load More)               │
│                               │
├───────────────────────────────┤
│ [Previous] [1] [2] [3] [Next] │
│                               │
│ FOOTER                        │
└───────────────────────────────┘
```

### Photographer Card Component (Detailed)
```
Desktop (250px width):
┌─────────────────────────────┐
│ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ │ (Portfolio preview: 3 photos hover)
│                             │
│ Profile Photo (circular)    │
│ Name (18px bold)            │
│ ★★★★★ 4.8 (850 reviews)    │
│ Category tags: [Wedding]    │
│ Location: Dhaka, Mirpur     │
│ ৳12000-৳50000 per event     │
│ "Available now"             │
│                             │
│ [View Profile] [Get Quote]  │
│ ♡ Featured Badge            │
└─────────────────────────────┘

Mobile (full-width):
┌──────────────────────────────┐
│ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ │
│                              │
│ Profile Photo (50px circle)  │
│ Name | ★4.8 | 850 reviews    │
│ Category: Wedding            │
│ Location: Dhaka              │
│ Price: ৳12K-৳50K             │
│ Available now                │
│                              │
│ [View Profile] [Get Quote]   │
│ ♡ Save                       │
└──────────────────────────────┘
```

### CTAs Placement
- **Primary**: "View Profile" (navigates to photographer detail), "Get Quote" (opens inquiry form)
- **Secondary**: "Save" (heart icon - saves to favorites)
- **Sorting**: Top dropdown for sort options
- **Filtering**: Left sidebar (desktop) or collapsible filter panel (mobile)

### Mobile-First Notes
- Filters accessible via bottom sheet or collapsible menu
- Single-column photographer card layout
- Sticky "Apply Filters" button at bottom
- Horizontal scrollable filter pills (tags)
- Lazy-loaded photographer cards
- Infinite scroll or "Load More" button

---

## PAGE 3: PHOTOGRAPHER PROFILE

### Desktop Layout
```
┌──────────────────────────────────────────────────────────┐
│ HEADER: Logo | Search | Login | Sign Up                 │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ COVER IMAGE (Full-width, 300px height)                  │
│                                                          │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ Profile Photo │ Name                    [♡] [Share] │ │
│ │ (150px round) │ Photographer             [Get Quote]│ │
│ │               │ Location: Dhaka                      │ │
│ │               │ Experience: 8 years                 │ │
│ │               │ ★★★★★ 4.8 (850 reviews)             │ │
│ │               │ "Specializes in wedding and events" │ │
│ │               │                                     │ │
│ │               │ [☑ Verified] [📞] [💬] [WhatsApp]   │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ NAVIGATION TABS (Sticky)                               │
│ [Portfolio] [Packages] [Reviews] [Gallery] [Events]    │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ PORTFOLIO SECTION (Default tab)                        │
│                                                          │
│ Featured Album Grid (3 columns):                        │
│ ┌──────────┐  ┌──────────┐  ┌──────────┐             │
│ │ Weddings │  │ Events   │  │ Portraits│             │
│ │ 45 photos│  │ 32 photos│  │ 28 photos│             │
│ │ [View]   │  │ [View]   │  │ [View]   │             │
│ └──────────┘  └──────────┘  └──────────┘             │
│                                                          │
│ ┌──────────┐  ┌──────────┐  ┌──────────┐             │
│ │ Products │  │ Promo    │  │ Boudoir  │             │
│ │ 15 photos│  │ 22 photos│  │ 18 photos│             │
│ │ [View]   │  │ [View]   │  │ [View]   │             │
│ └──────────┘  └──────────┘  └──────────┘             │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ PACKAGES SECTION                                         │
│                                                          │
│ ┌────────────────┐  ┌────────────────┐                  │
│ │ ENGAGEMENT     │  │ WEDDING        │                  │
│ │ PHOTOSHOOT     │  │ PHOTOGRAPHY    │                  │
│ │ ৳12000         │  │ ৳50000         │                  │
│ │ • 2 hours      │  │ • 8 hours      │                  │
│ │ • 200 photos   │  │ • 500 photos   │                  │
│ │ • 1 assistant  │  │ • 2 assistants │                  │
│ │ [Get Quote]    │  │ [Get Quote]    │                  │
│ └────────────────┘  └────────────────┘                  │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ REVIEWS SECTION (First 5 reviews shown)                │
│                                                          │
│ Rating Distribution:                                    │
│ ★★★★★ 340  ████░░░░░░  80%                           │
│ ★★★★☆ 60   ███░░░░░░░░  14%                          │
│ ★★★☆☆ 30   ██░░░░░░░░░  7%                           │
│ ★★☆☆☆ 15   █░░░░░░░░░░  4%                           │
│ ★☆☆☆☆ 5    ░░░░░░░░░░░  1%                           │
│                                                          │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ Review by: Mahmudul (Verified Purchase)            │ │
│ │ ★★★★★ "Amazing work! Highly recommended"            │ │
│ │ "The photos turned out beautiful. Very              │ │
│ │ professional and punctual. Will book again!"        │ │
│ │ 2 months ago  [Helpful] [Report]                    │ │
│ │                                                    │ │
│ │ Reply from Photographer:                           │ │
│ │ "Thank you so much! Hope to work with you again."  │ │
│ └─────────────────────────────────────────────────────┘ │
│ ...more reviews...                                      │
│ [Load More Reviews] [See All Reviews]                 │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ RELATED PHOTOGRAPHERS                                   │
│ ┌──────────┐  ┌──────────┐  ┌──────────┐             │
│ │ Photo    │  │ Photo    │  │ Photo    │             │
│ │ Name     │  │ Name     │  │ Name     │             │
│ │ Category │  │ Category │  │ Category │             │
│ └──────────┘  └──────────┘  └──────────┘             │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

### Mobile Layout
```
┌──────────────────────────────┐
│ HEADER (Sticky): ← | Logo    │
├──────────────────────────────┤
│                              │
│ COVER (150px height)         │
│ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓      │
│                              │
│ Profile Photo (100px circle) │
│ Name                         │
│ Photographer                 │
│ Location                     │
│ ★★★★★ 4.8 (850 reviews)     │
│ "Specializes in weddings..." │
│                              │
│ [☑ Verified]                 │
│ [📞 Call] [💬 Chat]          │
│ [WhatsApp Direct]            │
│ [♡ Save] [Share]             │
│                              │
├──────────────────────────────┤
│ TABS (Horizontal scroll)      │
│ [Portfolio] [Packages]...     │
│                              │
├──────────────────────────────┤
│ PORTFOLIO (Gallery view)      │
│ ┌──────────────┐             │
│ │ Album Cover  │             │
│ │ Weddings     │             │
│ │ 45 photos    │             │
│ │ [View]       │             │
│ └──────────────┘             │
│ ┌──────────────┐             │
│ │ Album Cover  │             │
│ │ Events       │             │
│ │ 32 photos    │             │
│ │ [View]       │             │
│ └──────────────┘             │
│ ...                          │
│                              │
├──────────────────────────────┤
│ PACKAGES                      │
│ ┌──────────────────────────┐ │
│ │ Engagement Photoshoot    │ │
│ │ ৳12000                   │ │
│ │ • 2 hours                │ │
│ │ • 200 photos             │ │
│ │ • 1 assistant            │ │
│ │ [Get Quote]              │ │
│ └──────────────────────────┘ │
│ ┌──────────────────────────┐ │
│ │ Wedding Photography      │ │
│ │ ৳50000                   │ │
│ │ • 8 hours                │ │
│ │ • 500 photos             │ │
│ │ • 2 assistants           │ │
│ │ [Get Quote]              │ │
│ └──────────────────────────┘ │
│ ...                          │
│                              │
├──────────────────────────────┤
│ REVIEWS (First 3 shown)       │
│ ★★★★★ 4.8 (850 reviews)     │
│                              │
│ ┌──────────────────────────┐ │
│ │ Review by: Mahmudul      │ │
│ │ ★★★★★                    │ │
│ │ "Amazing work!           │ │
│ │  Highly recommended"     │ │
│ │ 2 months ago             │ │
│ └──────────────────────────┘ │
│ [See All Reviews]            │
│                              │
├──────────────────────────────┤
│ [Get Quote] (Sticky CTA)     │
│                              │
│ FOOTER                       │
└──────────────────────────────┘
```

### Key Components
- Cover photo with profile info overlay
- Star rating with review count
- Verification badges and trust indicators
- Action buttons (Call, Chat, WhatsApp, Save, Share)
- Tab navigation for sections
- Album grid with lightbox
- Package cards with pricing
- Review cards with photographer responses
- Related photographers section
- Sticky "Get Quote" CTA button (mobile)

### CTAs Placement
- **Primary**: "Get Quote" (fixed at bottom on mobile, prominent on desktop)
- **Secondary**: "Call", "Chat", "WhatsApp"
- **Tertiary**: "Save", "Share", "View All Reviews"

### Mobile-First Notes
- Sticky header with back button
- Fixed "Get Quote" button at bottom
- Horizontal scrolling tabs
- Single-column layout for all content
- Click to expand full review text
- Simplified package display
- Click-to-call button prominent

---

## PAGE 4: BOOKING / INQUIRY FORM

### Desktop Layout
```
┌──────────────────────────────────────────────────────────┐
│ HEADER: Logo | Back                                     │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ ┌────────────────────────────┬──────────────────────┐  │
│ │ BOOKING INQUIRY (Left 60%) │ PHOTOGRAPHER INFO    │  │
│ │                            │ (Right 40%, Sticky) │  │
│ │ Get a Quote from           │ ┌──────────────────┐ │  │
│ │ [Photographer Name]        │ │ Photo            │ │  │
│ │                            │ │ [Name]           │ │  │
│ │ STEP 1: EVENT DETAILS      │ │ ★4.8 (850 reviews)
│ │                            │ │ Wedding Photog   │ │  │
│ │ Event Type:                │ │ Location: Dhaka  │ │  │
│ │ [Wedding ▼]                │ │                  │ │  │
│ │                            │ │ Response Time:   │ │  │
│ │ Event Date:                │ │ Within 1 hour    │ │  │
│ │ [Select Date ▼]            │ │                  │ │  │
│ │                            │ │ Get Quote Button │ │  │
│ │ Number of Guests:          │ │ (highlighted)    │ │  │
│ │ [Input: 150]               │ │                  │ │  │
│ │                            │ │ Available:       │ │  │
│ │ Budget (Optional):         │ │ [Calendar]       │ │  │
│ │ ৳[Min] - ৳[Max]            │ │                  │ │  │
│ │ [Slider]                   │ │ Packages:        │ │  │
│ │                            │ │ • Wedding (৳50K) │ │  │
│ │ Venue Location:            │ │ • Engagement     │ │  │
│ │ [Address or Map]           │ │   (৳12K)         │ │  │
│ │                            │ │ • Promo (৳8K)    │ │  │
│ │ Special Requirements:      │ │                  │ │  │
│ │ [Textarea: 5 rows]         │ │ Contact:         │ │  │
│ │ "Share your vision,        │ │ [☑ Verified]     │ │  │
│ │  specific shots needed,    │ │ [📞]             │ │  │
│ │  or unique requirements"   │ │ [💬]             │ │  │
│ │                            │ │ [WhatsApp]       │ │  │
│ │ [STEP 1 COMPLETE]          │ └──────────────────┘ │  │
│ │                            │                      │  │
│ │ STEP 2: YOUR DETAILS       │                      │  │
│ │                            │                      │  │
│ │ Your Name:                 │                      │  │
│ │ [Input: Full Name]         │                      │  │
│ │                            │                      │  │
│ │ Email:                     │                      │  │
│ │ [Input: Email]             │                      │  │
│ │                            │                      │  │
│ │ Phone:                     │                      │  │
│ │ [+880 ___________]         │                      │  │
│ │ [ ] Verify Phone (OTP)     │                      │  │
│ │                            │                      │  │
│ │ [STEP 2 COMPLETE]          │                      │  │
│ │                            │                      │  │
│ │ STEP 3: CONFIRMATION       │                      │  │
│ │                            │                      │  │
│ │ [ ] I agree to terms       │                      │  │
│ │ [SEND INQUIRY]             │                      │  │
│ │                            │                      │  │
│ └────────────────────────────┴──────────────────────┘  │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

### Mobile Layout
```
┌──────────────────────────────┐
│ ← Back | Booking Inquiry     │
├──────────────────────────────┤
│ Photographer Info            │
│ ┌──────────────────────────┐ │
│ │ Photo (small)            │ │
│ │ Name                     │ │
│ │ ★4.8 (850 reviews)       │ │
│ │ Weddings                 │ │
│ │ [View Profile]           │ │
│ └──────────────────────────┘ │
│                              │
├──────────────────────────────┤
│ STEP 1: EVENT DETAILS        │
│                              │
│ Event Type:                  │
│ [Wedding ▼]                  │
│                              │
│ Event Date:                  │
│ [Select Date]                │
│ (Calendar picker opens)      │
│                              │
│ Number of Guests:            │
│ [Input: _____]               │
│                              │
│ Budget (Optional):           │
│ ৳[___] - ৳[___]               │
│ [Slider]                     │
│                              │
│ Venue Location:              │
│ [Address: ________]          │
│ [Or: Open Map]               │
│                              │
│ Special Requirements:        │
│ [Textarea with placeholder]  │
│ [5 lines]                    │
│                              │
├──────────────────────────────┤
│ STEP 2: YOUR DETAILS         │
│                              │
│ Your Name:                   │
│ [Input: _________]           │
│                              │
│ Email:                       │
│ [Input: _________]           │
│                              │
│ Phone:                       │
│ [+880 _________]             │
│ [ ] Verify with OTP          │
│                              │
├──────────────────────────────┤
│ [ ] I agree to terms         │
│                              │
│ [SEND INQUIRY]               │
│ (Width: 100%, 48px height)   │
│                              │
│ FOOTER                       │
└──────────────────────────────┘
```

### Form Components
- **Event Type Dropdown**: Predefined options (Wedding, Event, Engagement, Photoshoot, etc.)
- **Date Picker**: Calendar with availability visualization (booked dates grayed out)
- **Budget Slider**: Mobile-friendly range slider with currency labels
- **Map Integration**: Click to open map for location selection
- **Textarea**: Rich text or simple text input for requirements
- **Phone OTP**: Inline verification button
- **Photographer Info Card**: Sticky on desktop, prominent on mobile
- **Agreement Checkbox**: Links to terms

### CTAs Placement
- **Primary**: "SEND INQUIRY" button (large, full-width on mobile)
- **Secondary**: "View Profile" (photographer card), "Verify with OTP" (phone field)

### Mobile-First Notes
- Single-column form layout
- Full-width inputs
- Calendar picker (native mobile date picker if available)
- OTP verification inline
- Step-by-step guidance
- Photographer info accessible at top
- Fixed "Send Inquiry" button at bottom
- Form auto-fills user data if logged in

---

## PAGE 5: EVENT LISTING & DETAIL

### Event Listing Page
```
DESKTOP:
┌──────────────────────────────────────────────────────────┐
│ HEADER: Logo | Search | Login | Sign Up                 │
├──────────────────────────────────────────────────────────┤
│ FILTERS (Left Sidebar)        │ EVENT GRID (Right)       │
│ Category: [Workshops ▼]       │ ┌──────────┐ ┌────────┐ │
│ City: [Dhaka ▼]               │ │ Event 1  │ │Event 2 │ │
│ Date: [This Month ▼]          │ │ 2025 Jan │ │2025 Jan│ │
│ [ ] Upcoming Only             │ │ [Details]│ │Details │ │
│ [APPLY]                       │ └──────────┘ └────────┘ │
│                               │ ┌──────────┐ ┌────────┐ │
│                               │ │ Event 3  │ │Event 4 │ │
│                               │ │ 2025 Jan │ │2025 Jan│ │
│                               │ │ [Details]│ │Details │ │
│                               │ └──────────┘ └────────┘ │
│                               │ [Load More Events]       │
└──────────────────────────────────────────────────────────┘

MOBILE:
┌──────────────────────────────┐
│ Logo | [Filters] [Sort]      │
├──────────────────────────────┤
│ Category: [Workshops ▼]      │
│ City: [Dhaka ▼]              │
│ Date: [This Month ▼]         │
│ [ ] Upcoming Only            │
│ [APPLY FILTERS]              │
├──────────────────────────────┤
│ ┌──────────────────────────┐ │
│ │ Event Banner             │ │
│ │ Event Title              │ │
│ │ Jan 15, 2 PM | Dhaka     │ │
│ │ 25 going                 │ │
│ │ [View Details]           │ │
│ │ [RSVP Now]               │ │
│ └──────────────────────────┘ │
│ ┌──────────────────────────┐ │
│ │ Event Banner             │ │
│ │ Event Title              │ │
│ │ Jan 18, 6 PM | Mirpur    │ │
│ │ 42 going                 │ │
│ │ [View Details]           │ │
│ │ [RSVP Now]               │ │
│ └──────────────────────────┘ │
│ ... (Infinite scroll)        │
│                              │
│ FOOTER                       │
└──────────────────────────────┘
```

### Event Detail Page
```
DESKTOP:
┌──────────────────────────────────────────────────────────┐
│ HEADER: Logo | Search | Login | Sign Up                 │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ EVENT HERO (Full-width, 400px height)                   │
│ [Large event image with overlay]                        │
│ "Photography Workshop - Basic to Advanced"              │
│ Category Badge: [Workshop]                              │
│ Date: Jan 15, 2025 | Time: 2 PM - 5 PM                │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────┬──────────────────────┐  │
│ │ EVENT INFO (Left 60%)       │ RSVP WIDGET (Right)  │  │
│ │                             │ (Sticky)             │  │
│ │ DESCRIPTION                 │ ┌─────────────────┐  │  │
│ │ Lorem ipsum dolor sit amet,│ │ 25 people going │  │  │
│ │ consectetur adipiscing     │ │ @ Gulshan Community│ │  │
│ │ elit...                    │ │ Center           │  │  │
│ │                             │ │ Date: Jan 15     │  │  │
│ │ EVENT DETAILS               │ │ Time: 2 PM - 5 PM│  │  │
│ │ Location: Gulshan Community │ │                  │  │  │
│ │ Center, Dhaka               │ │ [ ] Going        │  │  │
│ │ Address: 45 Kamal Ataturk   │ │ [ ] Maybe        │  │  │
│ │ Avenue, Gulshan             │ │ [ ] Not Going    │  │  │
│ │                             │ │                  │  │  │
│ │ Date & Time:                │ │ [RSVP NOW]       │  │  │
│ │ Jan 15, 2025 | 2 PM - 5 PM │ │ (Primary button) │  │  │
│ │                             │ │                  │  │  │
│ │ Duration: 3 hours           │ │ [Share Event]    │  │  │
│ │                             │ │ [Save Event]     │  │  │
│ │ ORGANIZER                   │ │ [Report Event]   │  │  │
│ │ ┌─────────────────────────┐ │ └─────────────────┘  │  │
│ │ │ Photo (50px)            │ │                      │  │
│ │ │ Name                    │ │ ATTENDEES (First 10) │  │
│ │ │ Photographer            │ │ ┌────┐ ┌────┐ ┌──┐  │  │
│ │ │ [View Profile]          │ │ │ 👤 │ │ 👤 │ │+20│ │  │
│ │ │ [Follow]                │ │ └────┘ └────┘ └──┘  │  │
│ │ └─────────────────────────┘ │                      │  │
│ │                             │ SEE ALL ATTENDEES    │  │
│ │ AGENDA (if multi-session)   │                      │  │
│ │ 2:00 - 2:30  Welcome & Intro│                      │  │
│ │ 2:30 - 3:30  Photography    │                      │  │
│ │               Basics         │                      │  │
│ │ 3:30 - 4:30  Q&A            │                      │  │
│ │ 4:30 - 5:00  Networking     │                      │  │
│ │                             │                      │  │
│ │ TAGS                        │                      │  │
│ │ #Photography #Workshop      │                      │  │
│ │ #Beginners #Hands-on        │                      │  │
│ │                             │                      │  │
│ │ MAP                         │                      │  │
│ │ [Embedded Google Map]       │                      │  │
│ │                             │                      │  │
│ │ SPONSORS (if applicable)    │                      │  │
│ │ [Sponsor Logo] [Sponsor Logo]                      │  │
│ │                             │                      │  │
│ │ [See Past Event Gallery]    │                      │  │
│ └─────────────────────────────┴──────────────────────┘  │
│                                                          │
│ RELATED EVENTS                                          │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐                │
│ │ Event    │ │ Event    │ │ Event    │                │
│ │ Image    │ │ Image    │ │ Image    │                │
│ │ Title    │ │ Title    │ │ Title    │                │
│ │ [RSVP]   │ │ [RSVP]   │ │ [RSVP]   │                │
│ └──────────┘ └──────────┘ └──────────┘                │
│                                                          │
│ EVENT GALLERY (Past event photos)                       │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐                │
│ │ Photo    │ │ Photo    │ │ Photo    │                │
│ │ [View]   │ │ [View]   │ │ [View]   │                │
│ └──────────┘ └──────────┘ └──────────┘                │
│                                                          │
│ FOOTER                                                  │
└──────────────────────────────────────────────────────────┘

MOBILE:
┌──────────────────────────────┐
│ ← Back | Share | Save        │
├──────────────────────────────┤
│ [Event Hero Image (200px)]   │
│ [Workshop Badge]             │
│                              │
│ Photography Workshop -       │
│ Basic to Advanced            │
│                              │
│ Jan 15, 2025 | 2 PM - 5 PM  │
│ Location: Gulshan Community │
│ Center                       │
│                              │
│ 25 people going              │
│                              │
├──────────────────────────────┤
│ [RSVP NOW] (Width: 100%)     │
│ (Sticky at bottom if needed) │
│                              │
├──────────────────────────────┤
│ DESCRIPTION                  │
│ Lorem ipsum dolor sit...     │
│                              │
├──────────────────────────────┤
│ EVENT DETAILS                │
│ 📍 Gulshan Community Center  │
│ 45 Kamal Ataturk Ave         │
│ [Open Map]                   │
│                              │
│ 📅 Jan 15, 2025              │
│ 🕐 2 PM - 5 PM              │
│ ⏱️ 3 hours                  │
│                              │
├──────────────────────────────┤
│ ORGANIZER                    │
│ [Photo] Name                 │
│ Photographer                 │
│ [View Profile] [Follow]      │
│                              │
├──────────────────────────────┤
│ ATTENDEES                    │
│ 25 people going              │
│ [👤] [👤] [👤] [+22]         │
│ [See All Attendees]          │
│                              │
├──────────────────────────────┤
│ AGENDA (if applicable)       │
│ 2:00 - Welcome               │
│ 2:30 - Photography Basics    │
│ 3:30 - Q&A                   │
│ 4:30 - Networking            │
│                              │
├──────────────────────────────┤
│ MAP                          │
│ [Embedded Map - 200px]       │
│                              │
├──────────────────────────────┤
│ EVENT GALLERY                │
│ [Photo] [Photo] [Photo]      │
│ [See More]                   │
│                              │
│ FOOTER                       │
└──────────────────────────────┘
```

### Event Card Component
```
Desktop (300px):
┌──────────────────────────┐
│ Event Image (200px x 150)│
│                          │
│ Workshop Badge           │
│ "Photography Workshop"   │
│ Jan 15, 2 PM | Gulshan  │
│ 25 going                 │
│ [View Details] [RSVP]    │
└──────────────────────────┘

Mobile (full-width):
┌────────────────────────────┐
│ Event Image (350px x 200)  │
│ Category: [Workshop]       │
│ "Photography Workshop"     │
│ Jan 15 | 2 PM | Gulshan    │
│ 25 people going            │
│ [View Details] [RSVP Now]  │
└────────────────────────────┘
```

### CTAs Placement
- **Primary**: "RSVP NOW" button (prominently displayed, sticky on mobile)
- **Secondary**: "View Details", "See All Attendees", "View Gallery"
- **Tertiary**: "Share", "Save", "Report"

---

## PAGE 6: COMPETITION - SUBMIT PHOTO

### Submission Form
```
MOBILE:
┌──────────────────────────────┐
│ ← Back | Competition: "Heritage Photo Challenge" │
├──────────────────────────────┤
│ Submission Deadline: 5 days │
│ Submission: 23/50            │
│                              │
├──────────────────────────────┤
│ UPLOAD PHOTO                 │
│                              │
│ ┌──────────────────────────┐ │
│ │ ▧▨▥ Drag & Drop or      │ │
│ │    Tap to Upload         │ │
│ │ [CHOOSE FILE]            │ │
│ └──────────────────────────┘ │
│                              │
│ Supported: JPG, PNG (Max 10MB)│
│ Min: 1920px (longest side)    │
│                              │
├──────────────────────────────┤
│ Photo Title:                 │
│ [Input: ____________]        │
│                              │
│ Category:                    │
│ [Portraiture ▼]              │
│                              │
│ Photo Description:           │
│ [Textarea: Describe your...] │
│ [5 lines]                    │
│                              │
│ Location (optional):         │
│ [Dhaka, Bangladesh]          │
│                              │
│ Date Taken (optional):       │
│ [Select Date]                │
│                              │
│ Equipment Used (optional):   │
│ Camera: [Canon EOS 5D]       │
│ Lens: [50mm]                 │
│ Settings: [ISO 400, f/2.8]   │
│                              │
│ Hashtags (optional):         │
│ #Heritage #Culture #Portrait │
│                              │
│ [ ] Watermark this photo     │
│ [ ] Apply watermark:         │
│     [Input: Optional text]   │
│                              │
├──────────────────────────────┤
│ COMPETITION TERMS            │
│ [ ] I confirm this is my     │
│     original work            │
│ [ ] I have necessary model   │
│     releases                 │
│ [ ] I agree to competition   │
│     terms                    │
│                              │
│ [TERMS & CONDITIONS] (Link)  │
│                              │
├──────────────────────────────┤
│ [PREVIEW SUBMISSION]         │
│ [SUBMIT PHOTO] (Primary CTA) │
│                              │
│ FOOTER                       │
└──────────────────────────────┘

DESKTOP:
┌──────────────────────────────────────────────────────────┐
│ HEADER: Logo | Competition: Heritage Photo Challenge    │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ ┌────────────────────────────┬──────────────────────┐   │
│ │ SUBMISSION FORM (60%)      │ COMPETITION INFO     │   │
│ │                            │ (40%, Sticky)        │   │
│ │ Upload Photo:              │ ┌─────────────────┐  │   │
│ │ ┌─────────────────────────┐│ │ Heritage Photo  │  │   │
│ │ │ Drag & Drop or Click to ││ │ Challenge       │  │   │
│ │ │ Upload                  ││ │                 │  │   │
│ │ │ [CHOOSE FILE BUTTON]    ││ │ Deadline:       │  │   │
│ │ └─────────────────────────┘│ │ Jan 30, 2025    │  │   │
│ │                            │ │ (5 days left)   │  │   │
│ │ Photo Title:               │ │                 │  │   │
│ │ [Input: ____________]      │ │ Submissions:    │  │   │
│ │                            │ │ 23/50           │  │   │
│ │ Category:                  │ │                 │  │   │
│ │ [Portraiture ▼]            │ │ Theme:          │  │   │
│ │                            │ │ Bangladesh      │  │   │
│ │ Photo Description:         │ │ Heritage        │  │   │
│ │ [Large Textarea]           │ │                 │  │   │
│ │ [5 lines - Describe your...│ │ Participation   │  │   │
│ │                            │ │ Fee: Free       │  │   │
│ │ Location:                  │ │                 │  │   │
│ │ [Autocomplete: Dhaka]      │ │ Prize Pool:     │  │   │
│ │                            │ │ ৳50,000         │  │   │
│ │ Date Taken:                │ │                 │  │   │
│ │ [Date Picker]              │ │ Winners:        │  │   │
│ │                            │ │ 1st: ৳25,000    │  │   │
│ │ Equipment (optional):      │ │ 2nd: ৳15,000    │  │   │
│ │ Camera: [Canon EOS 5D]     │ │ 3rd: ৳10,000    │  │   │
│ │ Lens: [50mm]               │ │                 │  │   │
│ │ Settings: [ISO 400, f/2.8] │ │ [Read More...]  │  │   │
│ │                            │ └─────────────────┘  │   │
│ │ Hashtags:                  │                      │   │
│ │ #Heritage #Culture #Portrait│ RULES               │   │
│ │                            │ ┌─────────────────┐  │   │
│ │ Watermark:                 │ │ • Original work │  │   │
│ │ [ ] Apply watermark        │ │ • No AI images  │  │   │
│ │ [Optional text: ________]  │ │ • Model release │  │   │
│ │                            │ │ • Professional  │  │   │
│ │ ┌─────────────────────────┐│ │   standard      │  │   │
│ │ │ TERMS AGREEMENT         ││ └─────────────────┘  │   │
│ │ ├─────────────────────────┤│                      │   │
│ │ │ [ ] Original work       ││ PAST WINNERS       │   │
│ │ │ [ ] Model releases      ││ ┌─────────────────┐ │   │
│ │ │ [ ] Agree to terms      ││ │ 1st: Mahmudul   │ │   │
│ │ │ [View Terms]            ││ │ [Photo]         │ │   │
│ │ └─────────────────────────┘│ │ [View Profile]  │ │   │
│ │                            │ │                 │ │   │
│ │ [PREVIEW] [SUBMIT PHOTO]   │ │ 2nd: Fatima     │ │   │
│ │                            │ │ [Photo]         │ │   │
│ │                            │ │ [View Profile]  │ │   │
│ │                            │ └─────────────────┘ │   │
│ └────────────────────────────┴──────────────────────┘   │
│                                                          │
│ FOOTER                                                  │
└──────────────────────────────────────────────────────────┘
```

### Form Components
- **Photo Upload**: Drag-drop or click upload with preview
- **Title Input**: Simple text field
- **Category Dropdown**: Pre-defined categories
- **Description Textarea**: Rich text or plain text
- **Location Autocomplete**: Dynamic autocomplete for BD locations
- **Date Picker**: Calendar widget
- **Equipment Fields**: Optional text inputs
- **Hashtag Input**: Comma-separated or tag input
- **Watermark Checkbox**: Toggle with text input
- **Terms Checkboxes**: Required agreement checkboxes
- **Competition Info Card**: Sticky on desktop, prominent on mobile

### CTAs Placement
- **Primary**: "SUBMIT PHOTO" button (large, full-width on mobile)
- **Secondary**: "PREVIEW SUBMISSION"
- **Tertiary**: "View Terms", "Read More"

### Mobile-First Notes
- Single-column form
- Touch-friendly file upload
- Native date picker
- Full-width inputs
- Sticky submit button at bottom
- Competition info scrollable or collapsible
- Progress indicator (23/50 submissions)
- Deadline countdown timer

---

## PAGE 7: COMPETITION - VOTING PAGE

### Voting/Leaderboard View
```
MOBILE:
┌──────────────────────────────┐
│ ← Competitions | Sort: ⭐ TOP │
├──────────────────────────────┤
│ Heritage Photo Challenge     │
│ Voting ends: 2 days          │
│ Total votes: 15,320          │
│                              │
├──────────────────────────────┤
│ [LEADERBOARD] [GALLERY] [ALL]│
│                              │
│ LEADERBOARD VIEW:            │
│ #1                           │
│ ┌──────────────────────────┐ │
│ │ 👑 # 1                   │ │
│ │ [Photo]                  │ │
│ │ 1,250 votes ⭐           │ │
│ │ "Beautiful Heritage"     │ │
│ │ by Mahmudul              │ │
│ │ [👍 Vote] [View Profile] │ │
│ └──────────────────────────┘ │
│                              │
│ #2                           │
│ ┌──────────────────────────┐ │
│ │ # 2                      │ │
│ │ [Photo]                  │ │
│ │ 1,180 votes ⭐           │ │
│ │ "Temple Gate"            │ │
│ │ by Fatima Khan           │ │
│ │ [👍 Vote] [View Profile] │ │
│ └──────────────────────────┘ │
│                              │
│ #3                           │
│ ┌──────────────────────────┐ │
│ │ # 3                      │ │
│ │ [Photo]                  │ │
│ │ 1,050 votes ⭐           │ │
│ │ "Old Market"             │ │
│ │ by Ahmed Hassan          │ │
│ │ [👍 Vote] [View Profile] │ │
│ └──────────────────────────┘ │
│ ...                          │
│ [Load More]                  │
│                              │
│ GALLERY VIEW:                │
│ ┌──┐ ┌──┐ ┌──┐              │
│ │ 📷│ │📷│ │📷│              │
│ │ 1 │ │ 2│ │ 3│              │
│ └──┘ └──┘ └──┘              │
│ ┌──┐ ┌──┐ ┌──┐              │
│ │📷│ │📷│ │📷│              │
│ │ 4│ │ 5│ │ 6│              │
│ └──┘ └──┘ └──┘              │
│ (Click to open)              │
│ (Vote on modal)              │
│                              │
│ FOOTER                       │
└──────────────────────────────┘

DESKTOP (Leaderboard):
┌──────────────────────────────────────────────────────────┐
│ HEADER: Logo | Competitions                             │
├──────────────────────────────────────────────────────────┤
│                                                          │
│ Heritage Photo Challenge                                │
│ Voting ends in: 2 days                                  │
│ Total votes: 15,320                                     │
│                                                          │
│ [LEADERBOARD] [GALLERY] [ALL ENTRIES]                  │
│                                                          │
│ ┌──────────────────────────────────────────────────────┐│
│ │ Rank │ Photo │ Title          │ Votes │ Actions     ││
│ ├──────────────────────────────────────────────────────┤│
│ │ 👑 1 │       │ Beautiful      │ 1250  │ [👍] [View] ││
│ │      │[Image]│ Heritage       │ ⭐    │ [Details]   ││
│ │      │       │ by Mahmudul    │       │             ││
│ ├──────────────────────────────────────────────────────┤│
│ │  2   │       │ Temple Gate    │ 1180  │ [👍] [View] ││
│ │      │[Image]│ by Fatima Khan │ ⭐    │ [Details]   ││
│ ├──────────────────────────────────────────────────────┤│
│ │  3   │       │ Old Market     │ 1050  │ [👍] [View] ││
│ │      │[Image]│ by Ahmed Hassan│ ⭐    │ [Details]   ││
│ ├──────────────────────────────────────────────────────┤│
│ │  4   │       │ Rickshaw     │ 950   │ [👍] [View] ││
│ │      │[Image]│ by Yasmin    │ ⭐    │ [Details]   ││
│ ├──────────────────────────────────────────────────────┤│
│ │  5   │       │ Street Vendor  │ 850   │ [👍] [View] ││
│ │      │[Image]│ by Rajib Khan  │ ⭐    │ [Details]   ││
│ │...                                                    ││
│ │                                                      ││
│ │ [Previous] [1] [2] [3] [Next]                       ││
│ └──────────────────────────────────────────────────────┘│
│                                                          │
│ GALLERY VIEW (3-column grid with modal on click)       │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐                │
│ │ Photo    │ │ Photo    │ │ Photo    │                │
│ │ 1,250 ⭐ │ │ 1,180 ⭐ │ │ 1,050 ⭐ │                │
│ │ [Vote]   │ │ [Vote]   │ │ [Vote]   │                │
│ └──────────┘ └──────────┘ └──────────┘                │
│ ...                                                     │
│                                                          │
│ FOOTER                                                  │
└──────────────────────────────────────────────────────────┘
```

### Voting Modal
```
┌───────────────────────────────────┐
│ ✕ Close                           │
├───────────────────────────────────┤
│                                   │
│ [Large Photo Display (600px)]     │
│                                   │
│ "Beautiful Heritage"              │
│ by Mahmudul                       │
│                                   │
│ Location: Old City, Dhaka         │
│ Category: Heritage & Cultural     │
│ 1,250 votes ⭐ | Rank #1          │
│                                   │
│ Description:                      │
│ "A glimpse of our heritage..."    │
│                                   │
│ [View Full Details]               │
│ [View Photographer Profile]       │
│                                   │
│ ┌─────────────────────────────┐   │
│ │ [❤️ Vote for this photo] × 1 │   │
│ │ (You have 50 votes left today)  │
│ └─────────────────────────────┘   │
│                                   │
│ [◀ Previous] [Next ▶]             │
│                                   │
└───────────────────────────────────┘
```

### CTAs Placement
- **Primary**: "👍 Vote" button (vote counter on each entry)
- **Secondary**: "View Details", "View Profile", "Previous/Next"
- **Filtering**: "LEADERBOARD", "GALLERY", "ALL ENTRIES"

### Mobile-First Notes
- Infinite scroll for leaderboard
- Gallery grid view with modal voting
- Vote counter per entry
- Large photo display in voting modal
- Horizontal swipe to vote on next/previous
- Vote limit indicator
- Anti-fraud OTP verification on first vote

---

## ADDITIONAL KEY PAGES

### Photographer Dashboard
- **Layout**: Sidebar navigation (desktop) / Tab navigation (mobile)
- **Main Widget**: Profile completion %, inquiry count, booking count, revenue
- **Quick Actions**: Upload photos, manage availability, view inquiries
- **Charts**: Profile views, inquiry trends, booking conversion

### Client Dashboard
- **Layout**: Tab-based (My Bookings | Favorites | Saved Searches | Reviews)
- **Cards**: Active bookings, saved photographers, recent inquiries
- **CTA**: "Book a Photographer" prominent

### Admin Panel
- **Layout**: Sidebar navigation (desktop only)
- **Dashboard**: Platform stats, recent activities, alerts
- **Main Sections**: Users, Events, Competitions, Payments, Content
- **Tables**: Sortable, filterable, with bulk actions

---

## DESIGN SYSTEM SPECIFICATIONS

### Colors
- Primary Red: #E63946
- Secondary Blue: #457B9D
- Success Green: #06A77D
- Warning Orange: #F4A261
- Dark Text: #1D3557
- Light Background: #F1FAEE
- Gray: #A8DADC

### Typography
- Headings: Poppins (bold, 24px, 32px, 40px)
- Body: Inter (regular, 14px, 16px)
- Captions: Inter (12px, gray)

### Button Sizes
- Desktop: 40-48px height
- Mobile: 48px height (minimum)
- Width: Full-width on mobile, fixed width on desktop

### Spacing
- Padding: 16px (mobile), 24px (desktop)
- Margins: 16px (mobile), 24px (desktop)
- Gap: 12px (small), 16px (medium), 24px (large)

### Border Radius
- Cards: 8px
- Buttons: 6px
- Inputs: 6px
- Images: 8px (corners), 50% (circles)

### Icons
- Size: 16px (small), 20px (medium), 24px (large)
- Weight: Regular or Bold
- Color: Match text color or primary

### Images
- Aspect Ratios: 1:1 (profile), 16:9 (hero), 3:2 (cards), 4:3 (gallery)
- Optimization: WebP with fallback, lazy loading, responsive srcset
- Compression: 80% quality JPG, 70% quality WebP

