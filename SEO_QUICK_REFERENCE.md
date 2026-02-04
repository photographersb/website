# SEO Quick Reference Guide

**Last Updated:** 2026-02-03  
**Status:** Production Ready ✅

---

## 🚀 Quick Start Commands

### Clear All Caches
```bash
php artisan optimize:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

### Verify Routes Work
```bash
php artisan route:list | grep -E '(categories|locations|@)'
```

### Test Sitemap Locally
```bash
curl http://localhost/sitemap.xml
curl http://localhost/sitemap/photographers.xml
curl http://localhost/sitemap/categories.xml
curl http://localhost/sitemap/cities.xml
```

---

## 📍 All SEO URLs

### Public Pages
| Page Type | URL Format | Example |
|-----------|-----------|---------|
| Homepage | `/` | `https://photographersb.com/` |
| Photographer Profile | `/@{username}` | `https://photographersb.com/@mahidul` |
| Categories Hub | `/categories` | `https://photographersb.com/categories` |
| Category Page | `/categories/{slug}` | `https://photographersb.com/categories/wedding` |
| Locations Hub | `/locations` | `https://photographersb.com/locations` |
| Location Page | `/locations/{slug}` | `https://photographersb.com/locations/dhaka` |

### SEO Tools
| Tool | URL | Purpose |
|------|-----|---------|
| Sitemap Index | `/sitemap.xml` | Main sitemap |
| Photographers Sitemap | `/sitemap/photographers.xml` | All @username URLs |
| Categories Sitemap | `/sitemap/categories.xml` | Category pages |
| Cities Sitemap | `/sitemap/cities.xml` | Location pages |
| Robots.txt | `/robots.txt` | Crawl rules |

---

## 🧩 Using the SEO Component

### In Blade Views

#### Pass Full Meta Object (Recommended)
```blade
@section('head')
    <x-seo :meta="$seoMeta" />
@endsection
```

#### Pass Individual Props
```blade
@section('head')
    <x-seo 
        title="Custom Page Title | Photographer SB"
        description="Custom meta description for this page"
        canonical="https://photographersb.com/custom-page"
        image="https://photographersb.com/images/og-image.jpg"
        type="website"
        :schema="$schemaJson"
    />
@endsection
```

#### Noindex Page (For Testing/Admin)
```blade
@section('head')
    <x-seo 
        :meta="$seoMeta" 
        :noindex="true"
    />
@endsection
```

---

## 🔧 SeoService Methods

### In Controllers

#### Homepage SEO
```php
$seoMeta = app(SeoService::class)->generateHomepageSeo();
return view('home', compact('seoMeta'));
```

#### Photographer Profile SEO
```php
$photographer = Photographer::findOrFail($id);
$seoMeta = app(SeoService::class)->generatePhotographerSeo($photographer);
return view('photographer.show', compact('photographer', 'seoMeta'));
```

#### Event SEO
```php
$event = Event::findOrFail($id);
$seoMeta = app(SeoService::class)->generateEventSeo($event);
$schemaJson = app(SeoService::class)->generateEventSchema($event);
return view('events.show', compact('event', 'seoMeta', 'schemaJson'));
```

#### Competition SEO
```php
$competition = Competition::findOrFail($id);
$seoMeta = app(SeoService::class)->generateCompetitionSeo($competition);
$schemaJson = app(SeoService::class)->generateCompetitionSchema($competition);
return view('competitions.show', compact('competition', 'seoMeta', 'schemaJson'));
```

#### Organization Schema (Add to Homepage)
```php
$schemaJson = app(SeoService::class)->getOrganizationSchema();
return view('home', compact('seoMeta', 'schemaJson'));
```

---

## 📊 Schema Types Reference

### Person Schema (Photographers)
**File:** `SeoService::generatePhotographerSchema($photographer)`
```json
{
  "@type": "Person",
  "name": "Photographer Name",
  "jobTitle": "Professional Photographer",
  "url": "https://photographersb.com/@username"
}
```

### Event Schema (Events)
**File:** `SeoService::generateEventSchema($event)`
```json
{
  "@type": "Event",
  "name": "Event Name",
  "startDate": "2026-03-01T10:00:00",
  "endDate": "2026-03-01T18:00:00"
}
```

### CreativeWork Schema (Competitions)
**File:** `SeoService::generateCompetitionSchema($competition)`
```json
{
  "@type": "CreativeWork",
  "name": "Competition Name"
}
```

### ItemList Schema (Listings)
**File:** `CategoryController::getCategoryPhotographersSchema()`
```json
{
  "@type": "ItemList",
  "numberOfItems": 24,
  "itemListElement": [...]
}
```

### CollectionPage Schema (Hub Pages)
**File:** `CategoryController::getCategoriesSchema()`
```json
{
  "@type": "CollectionPage",
  "name": "All Photography Categories"
}
```

### Organization Schema (Homepage)
**File:** `SeoService::getOrganizationSchema()`
```json
{
  "@type": "Organization",
  "name": "Photographer SB",
  "url": "https://photographersb.com"
}
```

---

## 🎨 OG Image Priority Rules

### Photographer Profiles
1. Profile photo (`$photographer->user->profile_photo_url`)
2. First portfolio image
3. Default platform OG image

### Events
1. Event banner (`$event->banner`)
2. Default event OG image

### Competitions
1. Competition cover (`$competition->cover`)
2. Default competition OG image

### Categories/Locations
1. Category/location featured image
2. Default platform OG image

**Technical Requirements:**
- Minimum size: 1200x630px
- Format: JPG or PNG
- Max file size: 500KB
- Must be absolute URL (https://)

---

## 🧪 Testing Tools

### Schema Validation
- **Google Rich Results Test:** https://search.google.com/test/rich-results
- **Schema.org Validator:** https://validator.schema.org/

### OG Preview Testing
- **Facebook Debugger:** https://developers.facebook.com/tools/debug/
- **Twitter Card Validator:** https://cards-dev.twitter.com/validator
- **LinkedIn Post Inspector:** https://www.linkedin.com/post-inspector/

### Performance Testing
- **Google PageSpeed Insights:** https://pagespeed.web.dev/
- **Mobile-Friendly Test:** https://search.google.com/test/mobile-friendly

### Search Console
- **Google Search Console:** https://search.google.com/search-console
- **Bing Webmaster Tools:** https://www.bing.com/webmasters

---

## 📁 File Locations

### Controllers
- `app/Http/Controllers/CategoryController.php` - Category SEO pages
- `app/Http/Controllers/LocationController.php` - Location SEO pages
- `app/Http/Controllers/SitemapController.php` - Sitemap generation
- `app/Http/Controllers/PublicPhotographerController.php` - @username routing

### Services
- `app/Services/SeoService.php` - All SEO metadata generation
- `app/Services/SchemaJsonService.php` - Schema.org helper

### Views
- `resources/views/components/seo.blade.php` - SEO component
- `resources/views/categories/index.blade.php` - Categories hub
- `resources/views/categories/show.blade.php` - Category page
- `resources/views/locations/index.blade.php` - Locations hub
- `resources/views/locations/show.blade.php` - Location page

### Configuration
- `routes/web.php` - SEO routes (lines 53-60)
- `public/robots.txt` - Crawl rules

---

## 🐛 Common Issues & Fixes

### ❌ Sitemap 404 Error
```bash
php artisan route:clear
php artisan optimize
```

### ❌ OG Image Not Showing
- Use `url()` helper for absolute URLs
- Check image is publicly accessible
- Use Facebook Debugger to re-scrape

### ❌ Schema Validation Errors
- Ensure dates use ISO 8601 format (YYYY-MM-DDTHH:MM:SS)
- All URLs must be absolute (https://)
- Remove HTML entities from text fields

### ❌ @Username URL 404
- Verify photographer has username in database
- Check route: `/@{username}` exists in routes/web.php
- Clear route cache

---

## 📈 Success Metrics

### Technical SEO KPIs
- ✅ 100% dynamic meta tags
- ✅ 6 schema types implemented
- ✅ 0 sitemap errors
- ✅ 100% mobile-friendly pages
- ✅ PageSpeed score > 90

### Business KPIs (Track Monthly)
- Organic traffic growth
- Indexed pages (Search Console)
- Click-through rate from SERPs
- Top landing pages
- Keyword rankings

---

## 🔄 Regular Maintenance

### Weekly
- Check Google Search Console for errors
- Monitor crawl stats
- Review sitemap submission status

### Monthly
- Update meta descriptions for underperforming pages
- Optimize slow pages (PageSpeed)
- Review and update OG images

### Quarterly
- Full SEO audit (technical, on-page, content)
- Competitor analysis
- Backlink profile review

---

## 📚 Documentation Files

1. **SEO_IMPLEMENTATION_COMPLETE.md** - Full implementation details (this is the main doc)
2. **SEO_TESTING_CHECKLIST.md** - Complete testing guide (500+ lines)
3. **SEO_QUICK_REFERENCE.md** (this file) - Quick commands and URLs

---

## ✅ Pre-Deployment Checklist

- [ ] Run cache clear commands
- [ ] Verify all routes work (`php artisan route:list`)
- [ ] Test sitemap generation (`curl /sitemap.xml`)
- [ ] Verify robots.txt accessible
- [ ] Update `APP_URL` in `.env` to production domain
- [ ] Test @username URLs locally
- [ ] Validate schema with Google Rich Results Test
- [ ] Check OG preview with Facebook Debugger

---

## 🚀 Post-Deployment Tasks

### Day 1
- [ ] Test all live URLs
- [ ] Submit sitemap to Google Search Console
- [ ] Submit sitemap to Bing Webmaster Tools
- [ ] Verify OG previews on social media

### Week 1
- [ ] Monitor crawl errors
- [ ] Check index coverage
- [ ] Review mobile usability

### Month 1
- [ ] Track organic traffic (Google Analytics)
- [ ] Monitor keyword rankings
- [ ] Analyze top landing pages

---

**Need Help?** Refer to full documentation in `SEO_IMPLEMENTATION_COMPLETE.md` and `SEO_TESTING_CHECKLIST.md`.

**Status:** Production Ready ✅  
**Last Tested:** 2026-02-03
