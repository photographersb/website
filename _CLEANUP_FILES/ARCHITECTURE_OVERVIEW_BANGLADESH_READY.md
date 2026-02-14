# Photographer SB - Architecture Overview

## System Architecture Diagram

```
┌────────────────────────────────────────────────────────────────┐
│                    PHOTOGRAPHER SB PLATFORM                    │
│                   Bangladesh 100% Ready                         │
└────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                          PUBLIC LAYER (SEO)                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Homepage                                                               │
│      ↓                                                                   │
│  ┌──────────────────┐  ┌──────────────────┐                            │
│  │ /categories      │  │ /locations       │                            │
│  │ (Category Hub)   │  │ (Location Hub)   │                            │
│  └────────┬─────────┘  └────────┬─────────┘                            │
│           │                      │                                      │
│    ┌──────▼──────┐       ┌──────▼──────┐                               │
│    │ CATEGORIES  │       │  LOCATIONS  │                               │
│    │ (39 types)  │       │  (72 items) │                               │
│    └──────┬──────┘       └──────┬──────┘                               │
│           │                      │                                      │
│           └──────────┬───────────┘                                      │
│                      ▼                                                  │
│    ┌─────────────────────────────────┐                                │
│    │ PHOTOGRAPHER SEARCH RESULTS     │                                │
│    │ (High-Value SEO Routes)         │                                │
│    ├─────────────────────────────────┤                                │
│    │ • /photographers/location/{slug} │                                │
│    │ • /photographers/category/{slug} │                                │
│    │ • /photographers/{loc}/{cat}    │ ← HIGHEST VALUE               │
│    └────────────┬────────────────────┘                                │
│                 │                                                      │
│                 ▼                                                      │
│    ┌─────────────────────────────────┐                                │
│    │  PHOTOGRAPHER PROFILES          │                                │
│    │  (/@username - SEO Friendly)    │                                │
│    ├─────────────────────────────────┤                                │
│    │ • Profile Picture               │                                │
│    │ • Bio & Specializations         │                                │
│    │ • Ratings & Reviews             │                                │
│    │ • Portfolio                     │                                │
│    │ • SOCIAL SHARE BUTTONS ← ✨    │                                │
│    │   - WhatsApp                    │                                │
│    │   - Facebook                    │                                │
│    │   - Telegram                    │                                │
│    │   - Copy Link                   │                                │
│    └─────────────────────────────────┘                                │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                     DATABASE LAYER (Single Source)                       │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌─────────────────────┐  ┌──────────────────┐  ┌─────────────────┐   │
│  │   LOCATIONS Table   │  │  CATEGORIES Tbl  │  │  HASHTAGS Tbl   │   │
│  ├─────────────────────┤  ├──────────────────┤  ├─────────────────┤   │
│  │ id (PK)             │  │ id (PK)          │  │ id (PK)         │   │
│  │ name                │  │ name             │  │ name            │   │
│  │ slug (UNIQUE) ✓     │  │ slug (UNIQUE) ✓  │  │ slug (UNIQUE) ✓ │   │
│  │ type (division/     │  │ description      │  │ description     │   │
│  │        district)    │  │ seo_title ← NEW  │  │ is_featured     │   │
│  │ parent_id (self-FK) │  │ seo_description  │  │ usage_count     │   │
│  │ is_active           │  │ seo_keywords     │  │ timestamps      │   │
│  │ seo_title           │  │ icon             │  └─────────────────┘   │
│  │ seo_description     │  │ timestamps       │                         │
│  │ sort_order          │  └──────────────────┘   ┌──────────────────┐ │
│  │ timestamps          │                         │  PHOTOGRAPHERS   │ │
│  └──────┬──────────────┘                         ├──────────────────┤ │
│         │                                        │ id, user_id      │ │
│         │ FK (1:Many)                            │ city_id ← FK     │ │
│         │                                        │ slug             │ │
│  ┌──────▼──────────────────┐                     │ bio              │ │
│  │  PHOTOGRAPHER_CATEGORY  │                     │ specializations  │ │
│  │  (Junction Table)       │                     │ is_verified      │ │
│  ├─────────────────────────┤                     │ is_featured      │ │
│  │ photographer_id (FK)    │                     │ average_rating   │ │
│  │ category_id (FK)        │                     │ rating_count     │ │
│  └─────────────────────────┘                     │ timestamps       │ │
│                                                  └──────────────────┘ │
│  ┌────────────────────────────────────────────────────────────────┐  │
│  │          SEO_META Table (Polymorphic) ← NEW                     │  │
│  ├────────────────────────────────────────────────────────────────┤  │
│  │ id, model_type, model_id                                       │  │
│  │ meta_title, meta_description, meta_keywords                    │  │
│  │ og_title, og_description, og_image, og_url                     │  │
│  │ canonical_url, robots_index, robots_follow                     │  │
│  │ schema_json, is_auto_generated                                 │  │
│  │ created_by, updated_by, timestamps                             │  │
│  └────────────────────────────────────────────────────────────────┘  │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                    CONTROLLER LAYER (Logic)                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  CategoryController                                                     │
│  ├─ index() → Lists all categories with ItemList schema               │
│  └─ show(slug) → Category details + photographers                      │
│                                                                          │
│  LocationController                                                     │
│  ├─ index() → Lists all locations with ItemList schema                │
│  └─ show(slug) → Location details + photographers                      │
│                                                                          │
│  PhotographerSearchController ← NEW                                     │
│  ├─ byLocationAndCategory(loc_slug, cat_slug)                          │
│  │  Route: /photographers/{location}/{category}                        │
│  │  Returns: ItemList schema + photographers                           │
│  │  SEO VALUE: ★★★★★ (Most specific query)                            │
│  │                                                                      │
│  ├─ byLocation(slug)                                                   │
│  │  Route: /photographers/location/{slug}                              │
│  │  Returns: All photographers in location                             │
│  │  SEO VALUE: ★★★★☆                                                  │
│  │                                                                      │
│  └─ byCategory(slug)                                                   │
│     Route: /photographers/category/{slug}                              │
│     Returns: All photographers with category                           │
│     SEO VALUE: ★★★★☆                                                  │
│                                                                          │
│  PublicPhotographerController (Enhanced)                                │
│  ├─ showByUsername(username)                                           │
│  │  Route: /@{username}                                                │
│  │  Returns: Profile + OG tags + social share buttons                  │
│  │  SEO VALUE: ★★★★★ (Individual profile)                             │
│  │                                                                      │
│  └─ Includes: Share buttons, OG preview, schema markup                 │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                     SEEDER LAYER (Data Population)                       │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  LocationSeeder.php                                                     │
│  └─ Seeds: 8 Divisions + 64 Districts (72 total)                        │
│     Fields: name, slug, type, parent_id, is_active, sort_order         │
│                                                                          │
│  PhotographyCategorySeeder.php                                          │
│  └─ Seeds: 39 Photography Categories                                    │
│     Includes: Wedding, Portrait, Product, Drone, Journalist, etc.      │
│                                                                          │
│  HashtagSeeder.php                                                      │
│  └─ Seeds: 150+ Tags                                                    │
│     Categories: Platform, Photography, Location, Style, Engagement     │
│                                                                          │
│  SeedShowcasePhotographers.php                                          │
│  └─ Seeds: 50 Featured Photographers (customizable)                     │
│     Distribution: Across all locations & categories                     │
│     Data: Names, bios, ratings, experience, bookings                    │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                       COMMAND LAYER (Automation)                         │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  php artisan sb:seed-bd-core                                            │
│  ├─ Seeds all core data (locations, categories, tags)                   │
│  ├─ Validates database integrity                                        │
│  ├─ Reports totals & orphans                                            │
│  └─ Output: Statistics + Next Steps                                     │
│                                                                          │
│  php artisan sb:seed-showcase-photographers --count=50                  │
│  ├─ Creates featured photographers                                      │
│  ├─ Distributes across locations & categories                           │
│  ├─ Sets realistic data (ratings, bookings)                             │
│  └─ Reports progress every 10 created                                   │
│                                                                          │
│  php artisan sb:validate-photographer-db [--fix]                        │
│  ├─ Checks photographers without location                               │
│  ├─ Checks photographers without user                                   │
│  ├─ Detects duplicate slugs                                             │
│  ├─ Validates all foreign keys                                          │
│  ├─ Reports statistics                                                  │
│  └─ [--fix] Auto-corrects orphans                                       │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                      COMPONENT LAYER (Views)                             │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  <x-seo-head :seoMeta="$seoMeta" />                                     │
│  ├─ Renders all meta tags: title, description, robots, canonical       │
│  ├─ OG tags: og:title, og:description, og:image, og:url                │
│  ├─ Twitter Card tags                                                   │
│  └─ Schema.org JSON-LD script                                           │
│                                                                          │
│  <x-breadcrumbs :breadcrumbs="$breadcrumbs" />                          │
│  ├─ Visual breadcrumb navigation                                        │
│  ├─ BreadcrumbList schema.org markup                                    │
│  ├─ Improves CTR in Google SERPs                                        │
│  └─ Mobile responsive                                                   │
│                                                                          │
│  <x-social-share-buttons :photographer="$photographer" />              │
│  ├─ Copy Link (with clipboard feedback)                                 │
│  ├─ WhatsApp Share (auto message)                                       │
│  ├─ Facebook Share (OG preview)                                         │
│  ├─ Telegram Share (optional)                                           │
│  └─ LinkedIn Share (optional)                                           │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                      SEO OPTIMIZATION LAYER                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Schema Markup (Structured Data)                                        │
│  ├─ ItemList: All result pages (categories, locations, searches)        │
│  ├─ Person: Individual photographer profiles                            │
│  ├─ AggregateRating: Reviews & ratings on profiles                      │
│  └─ BreadcrumbList: Navigation trail on every page                      │
│                                                                          │
│  Canonical URLs (Duplicate Prevention)                                  │
│  ├─ /categories → canonicals to /categories                             │
│  ├─ /locations/{slug} → unique per location                             │
│  ├─ /photographers/location/{slug} → unique per location                │
│  ├─ /photographers/category/{slug} → unique per category                │
│  ├─ /photographers/{loc}/{cat} → unique per combination                 │
│  └─ /@username → unique per profile                                     │
│                                                                          │
│  Mobile Optimization                                                    │
│  ├─ Responsive grid layout                                              │
│  ├─ Touch-friendly buttons                                              │
│  ├─ Fast pagination                                                     │
│  ├─ Lazy-load images                                                    │
│  └─ AMP-compatible markup                                               │
│                                                                          │
│  Social Sharing (OG Tags)                                               │
│  ├─ og:type = profile (for /@username)                                  │
│  ├─ og:image = profile picture + fallback                               │
│  ├─ og:title = Photographer name + category                             │
│  ├─ og:description = Bio or templated description                       │
│  ├─ og:url = Canonical URL                                              │
│  └─ Verified in Facebook Debugger & WhatsApp                            │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                   SINGLE SOURCE OF TRUTH (Core Principle)                │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ONE Locations Table                                                    │
│  ├─ Unique slug enforcement                                             │
│  ├─ Foreign keys: photographer.city_id, event.city_id, etc.            │
│  └─ Auto-fix: Missing city_id → Assigned default location               │
│                                                                          │
│  ONE Categories Table                                                   │
│  ├─ 39 Photography Types (inclusive list)                               │
│  ├─ Unique slug enforcement                                             │
│  └─ Many-to-many with photographers (no duplication)                     │
│                                                                          │
│  ONE Hashtags Table                                                     │
│  ├─ 150+ Unique tags                                                    │
│  ├─ No category-specific duplication                                    │
│  └─ Featured system for promotion                                       │
│                                                                          │
│  Enforcement: Database Constraints                                      │
│  ├─ UNIQUE(slug) on all core tables                                     │
│  ├─ FOREIGN KEY constraints (cascading deletes)                         │
│  ├─ NOT NULL constraints where required                                 │
│  └─ Indexed queries for fast lookups                                    │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                         ROUTE PRIORITY HIERARCHY                         │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  TIER 1: HIGHEST SEO VALUE                                              │
│  ├─ /photographers/{location}/{category}  ← Most specific intent        │
│  └─ /@username                            ← Individual authority        │
│                                                                          │
│  TIER 2: HIGH SEO VALUE                                                 │
│  ├─ /locations/{slug}                     ← Location hub                │
│  ├─ /categories/{slug}                    ← Category hub                │
│  ├─ /photographers/location/{slug}        ← Filtered by location        │
│  └─ /photographers/category/{slug}        ← Filtered by category        │
│                                                                          │
│  TIER 3: FOUNDATION SEO VALUE                                           │
│  ├─ /locations                            ← Master location list        │
│  └─ /categories                           ← Master category list        │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                      DEPLOYMENT PIPELINE                                 │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  1. Migration Phase                                                     │
│     php artisan migrate                                                 │
│     ├─ Creates locations table (8 div + 64 dist)                        │
│     ├─ Adds SEO fields to categories                                    │
│     └─ Creates seo_meta table (polymorphic)                             │
│                                                                          │
│  2. Seed Phase                                                          │
│     php artisan sb:seed-bd-core                                         │
│     ├─ Populates locations (72 total)                                   │
│     ├─ Populates categories (39 total)                                  │
│     ├─ Populates tags (150+ total)                                      │
│     └─ Validates & reports                                              │
│                                                                          │
│  3. Showcase Phase (Optional)                                           │
│     php artisan sb:seed-showcase-photographers --count=50               │
│     ├─ Creates featured photographers                                   │
│     ├─ Distributes across locations                                     │
│     └─ Sets realistic data                                              │
│                                                                          │
│  4. Validation Phase                                                    │
│     php artisan sb:validate-photographer-db [--fix]                     │
│     ├─ Checks all integrity constraints                                 │
│     ├─ Detects orphans                                                  │
│     └─ Auto-fixes if needed                                             │
│                                                                          │
│  5. Cache Phase                                                         │
│     php artisan cache:clear                                             │
│     php artisan route:cache                                             │
│     php artisan config:cache                                            │
│                                                                          │
│  6. Index Phase                                                         │
│     Submit sitemap.xml to Google Search Console                         │
│     Monitor index coverage & ranking                                    │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Example: Search to Profile

```
User Search:
  "Wedding Photographers in Dhaka"
         ↓
Route Matching:
  /photographers/dhaka/wedding-photography
         ↓
PhotographerSearchController::byLocationAndCategory()
         ↓
Database Queries:
  1. SELECT * FROM locations WHERE slug = 'dhaka'
  2. SELECT * FROM categories WHERE slug = 'wedding-photography'
  3. SELECT * FROM photographers 
     WHERE city_id = {location_id}
     AND JSON_CONTAINS(specializations, {category_name})
     ORDER BY is_featured DESC, average_rating DESC
         ↓
Generate SEO Data:
  - Title: "Wedding Photography in Dhaka | Photographer SB"
  - Description: "Find verified wedding photographers in Dhaka..."
  - Schema: ItemList + Person schemas
  - Breadcrumbs: Home > Categories > Wedding > Dhaka
         ↓
Render View (photographers/search.blade.php):
  1. Apply SEO meta tags (<x-seo-head>)
  2. Display breadcrumbs (<x-breadcrumbs>)
  3. Show photographer grid (24 per page)
  4. Include pagination (rel next/prev)
         ↓
Click on Photographer:
  Photographer Profile (/@username)
         ↓
Generate OG Preview:
  - og:title: "Rajib Khan - Wedding Photographer"
  - og:image: profile_picture
  - og:description: photographer.bio
  - og:url: https://photographersb.com/@rajib-khan
         ↓
Share on WhatsApp:
  Message: "Check out Rajib Khan on Photographer SB 📸
            Location: Dhaka
            Category: Wedding Photography
            Profile: https://photographersb.com/@rajib-khan"
         ↓
Friend clicks link:
  Same profile loads with OG preview visible ✅
```

---

## Index Coverage Map

```
Google Search Console Sitemap:
├─ /sitemap.xml (main index)
│  ├─ /sitemap/main.xml
│  │  ├─ /categories
│  │  ├─ /locations
│  │  └─ Search filters
│  ├─ /sitemap/categories.xml
│  │  └─ /categories/{slug} × 39
│  ├─ /sitemap/locations.xml
│  │  └─ /locations/{slug} × 72
│  ├─ /sitemap/photographers.xml
│  │  ├─ /@{username} × 50+
│  │  ├─ /photographers/location/{slug} × 72
│  │  ├─ /photographers/category/{slug} × 39
│  │  └─ /photographers/{loc}/{cat} × ~2,800
│  │      (72 districts × 39 categories)
│  └─ Pagination: rel="next" / rel="prev"

Total Indexed Pages:
  - Static hubs: 2 (categories, locations)
  - Category pages: 39
  - Location pages: 72
  - Combined searches: ~2,800
  - Photographer profiles: 50+
  Total: ~3,000 unique URLs (more with pagination)
```

---

**Architecture Status**: ✅ PRODUCTION READY
