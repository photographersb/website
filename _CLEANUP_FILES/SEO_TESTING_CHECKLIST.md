# SEO Testing Checklist

**Generated:** 2026-02-03  
**Purpose:** Complete verification checklist for production-grade SEO implementation

---

## 1. Meta Tags Testing

### Homepage (`/`)
- [ ] `<title>` contains: "Photographer SB | Find Professional Photographers in Bangladesh"
- [ ] `<meta name="description">` contains platform value proposition
- [ ] `<meta property="og:title">` matches title
- [ ] `<meta property="og:description">` matches description
- [ ] `<meta property="og:image">` points to valid default OG image
- [ ] `<meta property="og:url">` is `https://photographersb.com/`
- [ ] `<meta name="twitter:card">` is `summary_large_image`
- [ ] `<link rel="canonical">` is `https://photographersb.com/`
- [ ] Organization schema.org JSON-LD present

### Photographer Profiles (`/@username`)
- [ ] Dynamic `<title>`: "{Name} | Professional Photographer in {City}"
- [ ] Dynamic `<meta name="description">` with bio excerpt
- [ ] `og:type` is `profile`
- [ ] `og:image` uses photographer's profile photo
- [ ] Canonical URL is `https://photographersb.com/@{username}`
- [ ] Person schema.org JSON-LD with correct properties:
  - [ ] name, url, image, jobTitle
  - [ ] address (city, country)
  - [ ] sameAs (social links if available)

### Category Pages (`/categories/{slug}`)
- [ ] Dynamic `<title>`: "{Category} Photographers in Bangladesh"
- [ ] Dynamic description with photographer count
- [ ] Canonical URL is `/categories/{slug}`
- [ ] `og:image` from category or fallback
- [ ] ItemList schema.org JSON-LD present
- [ ] numberOfItems matches photographer count

### Location Pages (`/locations/{slug}`)
- [ ] Dynamic `<title>`: "Photographers in {City} | Local Photography Services"
- [ ] Dynamic description with local SEO keywords
- [ ] Canonical URL is `/locations/{slug}`
- [ ] High priority (0.9) in sitemap
- [ ] ItemList schema with address data in photographer items
- [ ] `og:locality` meta tag for local SEO

### Event Pages
- [ ] Dynamic `<title>`: "{Event Name} | Photography Event"
- [ ] Dynamic description from event details
- [ ] `og:type` is `event`
- [ ] `og:image` uses event banner
- [ ] Event schema.org JSON-LD with:
  - [ ] name, description, startDate, endDate
  - [ ] location (name, address)
  - [ ] organizer, performer (if applicable)
  - [ ] eventStatus, eventAttendanceMode

### Competition Pages
- [ ] Dynamic `<title>`: "{Competition Name} | Photography Competition"
- [ ] Dynamic description from competition brief
- [ ] `og:image` uses competition cover
- [ ] CreativeWork schema.org JSON-LD

---

## 2. Sitemap Testing

### Main Sitemap (`/sitemap.xml`)
```bash
curl https://photographersb.com/sitemap.xml
```
- [ ] Returns XML sitemap index
- [ ] Contains links to all segment sitemaps:
  - [ ] `/sitemap/main.xml`
  - [ ] `/sitemap/photographers.xml`
  - [ ] `/sitemap/events.xml`
  - [ ] `/sitemap/competitions.xml`
  - [ ] `/sitemap/categories.xml`
  - [ ] `/sitemap/cities.xml`
- [ ] No 404 errors on segment sitemaps

### Photographers Sitemap (`/sitemap/photographers.xml`)
- [ ] All URLs use `/@{username}` format
- [ ] Priority is 0.9 (high)
- [ ] Contains `<lastmod>` timestamp
- [ ] No duplicate URLs
- [ ] Only approved photographers included

### Categories Sitemap (`/sitemap/categories.xml`)
- [ ] Contains `/categories` hub page
- [ ] Contains `/categories/{slug}` for each category
- [ ] Priority 0.8 for hub, 0.7 for individual pages
- [ ] All slugs are URL-safe

### Cities Sitemap (`/sitemap/cities.xml`)
- [ ] Contains `/locations` hub page
- [ ] Contains `/locations/{slug}` for each city
- [ ] Priority 0.9 for high local SEO value
- [ ] All slugs are URL-safe

---

## 3. Schema.org Validation

### Tools to Use
1. **Google Rich Results Test:** https://search.google.com/test/rich-results
2. **Schema.org Validator:** https://validator.schema.org/
3. **Structured Data Testing Tool:** https://developers.google.com/search/docs/appearance/structured-data

### Test Each Page Type
- [ ] Photographer profile (`/@username`) - Person schema passes
- [ ] Event page - Event schema passes
- [ ] Competition page - CreativeWork schema passes
- [ ] Category hub (`/categories`) - CollectionPage schema passes
- [ ] Category page (`/categories/{slug}`) - ItemList schema passes
- [ ] Location page (`/locations/{slug}`) - ItemList schema passes
- [ ] Homepage - Organization schema passes

### Common Issues to Check
- [ ] No missing required properties
- [ ] All URLs are absolute (not relative)
- [ ] Image URLs are valid and publicly accessible
- [ ] Date formats use ISO 8601 (YYYY-MM-DDTHH:MM:SS)
- [ ] No HTML entities in text fields
- [ ] No trailing commas in JSON-LD

---

## 4. OpenGraph Preview Testing

### Facebook Debugger
**URL:** https://developers.facebook.com/tools/debug/

Test URLs:
- [ ] Homepage: `https://photographersb.com/`
- [ ] Sample photographer: `https://photographersb.com/@mahidul`
- [ ] Sample category: `https://photographersb.com/categories/wedding`
- [ ] Sample location: `https://photographersb.com/locations/dhaka`
- [ ] Sample event: `https://photographersb.com/events/{slug}`

**Verify:**
- [ ] Title displays correctly
- [ ] Description is complete (no truncation)
- [ ] Image loads (minimum 1200x630px recommended)
- [ ] No errors or warnings
- [ ] Click "Scrape Again" to refresh cache

### Twitter Card Validator
**URL:** https://cards-dev.twitter.com/validator

- [ ] Summary large image card displays
- [ ] Twitter handle `@thephotographersbd` shows correctly
- [ ] Image aspect ratio is correct

### WhatsApp OG Preview
- [ ] Send test link in WhatsApp
- [ ] Image thumbnail displays
- [ ] Title and description visible

### LinkedIn Post Inspector
**URL:** https://www.linkedin.com/post-inspector/

- [ ] OG tags render correctly
- [ ] Professional image displays

---

## 5. Canonical URL Testing

### View Source on Pages
```bash
# Check canonical tag in HTML source
view-source:https://photographersb.com/@mahidul
```

- [ ] Homepage has `<link rel="canonical" href="https://photographersb.com/" />`
- [ ] Photographer profiles: `href="https://photographersb.com/@{username}"`
- [ ] Category pages: `href="https://photographersb.com/categories/{slug}"`
- [ ] Location pages: `href="https://photographersb.com/locations/{slug}"`
- [ ] No duplicate canonical tags
- [ ] Canonical URLs are absolute (not relative)
- [ ] HTTPS enforced (no HTTP canonicals)

### Pagination Canonical
- [ ] Paginated pages (e.g., `/categories/wedding?page=2`) have self-referencing canonical OR canonical to page 1 (define strategy)

---

## 6. Robots.txt Testing

### Access Robots.txt
```bash
curl https://photographersb.com/robots.txt
```

**Verify:**
- [ ] File exists and returns 200 OK
- [ ] Disallows admin routes: `Disallow: /admin/`
- [ ] Disallows private routes: `Disallow: /dashboard/`
- [ ] Allows public routes: `Allow: /@`
- [ ] Sitemap reference: `Sitemap: https://photographersb.com/sitemap.xml`
- [ ] No syntax errors

### Test Blocked Routes
- [ ] `/admin/*` returns noindex or 401/403
- [ ] `/dashboard/*` not in sitemap
- [ ] Login/register pages have `noindex` meta tag

---

## 7. Internal Linking Audit

### Categories Hub (`/categories`)
- [ ] Links to all category pages (`/categories/{slug}`)
- [ ] Link to locations hub (`/locations`)
- [ ] Breadcrumbs present

### Category Pages (`/categories/{slug}`)
- [ ] Breadcrumbs: Home › Categories › {Category}
- [ ] Links to photographer profiles (`/@username`)
- [ ] "Related Categories" section with internal links
- [ ] Link back to categories hub

### Locations Hub (`/locations`)
- [ ] Links to all location pages (`/locations/{slug}`)
- [ ] Link to categories hub (`/categories`)

### Location Pages (`/locations/{slug}`)
- [ ] Breadcrumbs: Home › Locations › {City}
- [ ] Links to photographer profiles
- [ ] "Nearby Cities" section with internal links
- [ ] Link back to locations hub

### Photographer Profiles
- [ ] Link to category pages (if specializations match)
- [ ] Link to location page (if city matches)

---

## 8. Mobile SEO Testing

### Mobile-Friendly Test
**URL:** https://search.google.com/test/mobile-friendly

- [ ] All pages pass mobile-friendly test
- [ ] No horizontal scrolling
- [ ] Text readable without zooming
- [ ] Touch elements properly spaced

### Mobile Meta Tags
- [ ] `<meta name="viewport" content="width=device-width, initial-scale=1">`
- [ ] Responsive OG images (minimum 600x315px)

---

## 9. Page Speed Testing

### Google PageSpeed Insights
**URL:** https://pagespeed.web.dev/

Test pages:
- [ ] Homepage
- [ ] Photographer profile
- [ ] Category page
- [ ] Location page

**Targets:**
- [ ] Performance score > 90 (desktop)
- [ ] Performance score > 75 (mobile)
- [ ] First Contentful Paint < 1.8s
- [ ] Largest Contentful Paint < 2.5s

### Image Optimization
- [ ] OG images < 500KB
- [ ] Profile photos lazy-loaded
- [ ] Image dimensions specified in HTML

---

## 10. Search Console Integration

### Google Search Console
**URL:** https://search.google.com/search-console

- [ ] Property verified for `photographersb.com`
- [ ] Submit sitemap.xml
- [ ] Check for crawl errors
- [ ] Monitor index coverage

### Bing Webmaster Tools
**URL:** https://www.bing.com/webmasters

- [ ] Property verified
- [ ] Submit sitemap.xml

---

## 11. Pre-Deployment Commands

### Clear All Caches
```bash
php artisan optimize:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

### Verify Routes
```bash
php artisan route:list | grep -E '(categories|locations|@)'
```

Expected output:
```
GET|HEAD  /@{username} .......................... photographer.profile
GET|HEAD  /categories ........................... categories.index
GET|HEAD  /categories/{slug} .................... categories.show
GET|HEAD  /locations ............................ locations.index
GET|HEAD  /locations/{slug} ..................... locations.show
GET|HEAD  /sitemap.xml .......................... sitemap.index
GET|HEAD  /sitemap/categories.xml ............... sitemap.categories
GET|HEAD  /sitemap/cities.xml ................... sitemap.cities
GET|HEAD  /sitemap/photographers.xml ............ sitemap.photographers
```

### Test SEO Service Methods
```php
// In Tinker: php artisan tinker
$photographer = App\Models\Photographer::first();
$seo = app(App\Services\SeoService::class);
$meta = $seo->generatePhotographerSeo($photographer);
dd($meta);
```

---

## 12. Post-Deployment Verification

### Live URL Tests (Replace with actual domain)
- [ ] https://photographersb.com/ loads
- [ ] https://photographersb.com/@mahidul loads (test with real username)
- [ ] https://photographersb.com/categories loads
- [ ] https://photographersb.com/categories/wedding loads (test with real category)
- [ ] https://photographersb.com/locations loads
- [ ] https://photographersb.com/locations/dhaka loads (test with real city)
- [ ] https://photographersb.com/sitemap.xml loads
- [ ] https://photographersb.com/robots.txt loads

### Google Indexing Check
```
site:photographersb.com
```
- [ ] Homepage indexed
- [ ] Photographer profiles indexed
- [ ] Category pages indexed
- [ ] Location pages indexed

---

## 13. Common Issues & Fixes

### Issue: OG Images Not Displaying
**Symptoms:** Facebook shows generic preview
**Fix:**
1. Verify image URLs are absolute (https://...)
2. Check image is publicly accessible (no auth required)
3. Image size minimum 1200x630px
4. Use Facebook Debugger to scrape again

### Issue: Schema Validation Errors
**Symptoms:** Google Rich Results Test shows errors
**Fix:**
1. Check all required properties present
2. Verify date formats (ISO 8601)
3. Ensure URLs are absolute
4. Remove HTML entities from text fields

### Issue: Sitemap Not Loading
**Symptoms:** 404 on /sitemap.xml
**Fix:**
1. Run `php artisan route:clear`
2. Verify SitemapController routes registered
3. Check web server rewrites (`.htaccess` for Apache)

### Issue: Canonical URL Mismatch
**Symptoms:** Google shows wrong canonical
**Fix:**
1. Verify `url()` helper uses HTTPS
2. Check `APP_URL` in `.env` is correct
3. Ensure no trailing slashes in canonicals

---

## 14. SEO Monitoring (Ongoing)

### Weekly Checks
- [ ] Google Search Console for crawl errors
- [ ] Index coverage report
- [ ] Mobile usability issues
- [ ] Page experience report

### Monthly Reviews
- [ ] Organic traffic trends (Google Analytics)
- [ ] Top landing pages
- [ ] Keyword rankings (use tools like Ahrefs, SEMrush)
- [ ] Backlink profile

---

## Completion Checklist

**Before marking SEO complete:**
- [ ] All meta tags rendering dynamically
- [ ] All schema.org markup validated
- [ ] Sitemap generated and accessible
- [ ] Robots.txt configured
- [ ] OG previews tested on Facebook/Twitter
- [ ] Canonical URLs correct
- [ ] Internal linking structure complete
- [ ] Mobile-friendly test passed
- [ ] PageSpeed scores acceptable
- [ ] Search Console configured
- [ ] @username URLs working
- [ ] Category/location pages live
- [ ] No 404 errors on public pages

---

**Last Updated:** 2026-02-03  
**Version:** 1.0  
**Status:** Ready for Production Testing
