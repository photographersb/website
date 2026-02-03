# SEO & Shareable Profile URLs Implementation Guide

## Overview
Complete SEO implementation for photographer profiles with shareable /@username URLs, OpenGraph tags, Schema markup, and social sharing buttons.

---

## 1. DATABASE SETUP

### Migrations Created

#### A. `add_username_to_users_table.php`
Adds unique `username` column to users table with index.

```bash
php artisan migrate
```

**Table Changes:**
- `users.username` - unique, indexed, string

#### B. `create_username_history_table.php`
Tracks username changes for 301 redirects.

**Fields:**
- `user_id` - FK to users
- `old_username` - for redirect tracking
- `new_username` - the new username
- `changed_at` - timestamp of change

#### C. `create_seo_meta_table.php`
Polymorphic table for SEO metadata.

**Fields:**
- `model_type` - User, Photographer, Page, etc.
- `model_id` - related model ID
- `meta_title` - SEO title
- `meta_description` - SEO description
- `og_image` - OpenGraph image URL
- `canonical_url` - canonical URL
- `schema_json` - JSON-LD schema (stored as JSON)
- `robots_index/follow` - robots meta control

---

## 2. MODELS UPDATED

### User Model (`app/Models/User.php`)
**Added:**
- `username` field to fillable
- `usernameHistory()` relation
- `seoMeta()` morphOne relation

### New Models Created

#### UsernameHistory (`app/Models/UsernameHistory.php`)
Tracks old usernames for 301 redirects.

#### SeoMeta (`app/Models/SeoMeta.php`)
Polymorphic model for SEO metadata (already existed, verified).

---

## 3. SERVICES

### UsernameService (`app/Services/UsernameService.php`)

**Methods:**

```php
// Generate unique username from name
generateUsername(string $name): string

// Check if username is available
isAvailable(string $username, ?int $exceptUserId = null): bool

// Check if username is reserved
isReservedUsername(string $username): bool

// Update username and track history
updateUsername(User $user, string $newUsername): bool

// Find user by username (handles redirects)
findByUsername(string $username): ?User

// Get profile URL for user
getProfileUrl(User $user): string
```

**Reserved Usernames:**
- admin, login, register, logout, events, competitions, photographers, settings, api, etc.

**Username Rules:**
- lowercase
- alphanumeric + underscore/dot
- no spaces or special chars
- 30 character max
- unique across system

### SeoService (`app/Services/SeoService.php`)

**Methods:**

```php
// Generate SEO metadata for photographer profile
generatePhotographerSeo(User $user): SeoMeta

// Get SEO meta (auto-generate if missing)
getSeoMeta(User $user): ?SeoMeta

// Clear SEO cache
clearCache(User $user): void

// Render meta tags as HTML
renderMetaTags(SeoMeta $seoMeta): string

// Render schema JSON-LD
renderSchemaJson(SeoMeta $seoMeta): string
```

### SchemaJsonService (`app/Services/SchemaJsonService.php`)

**Methods:**

```php
// Generate Person/LocalBusiness schema
generatePhotographerSchema(User $user, ?Photographer $photographer): array

// Build address schema
buildAddress(Photographer $photographer): array

// Build aggregate rating schema
buildAggregateRating(Photographer $photographer): array

// Build price range schema
buildPriceRange(Photographer $photographer): string

// Get social links for schema
getSocialLinks(?Photographer $photographer): array
```

**Schema Output:**
```json
{
  "@context": "https://schema.org",
  "@type": "Person|LocalBusiness",
  "name": "Photographer Name",
  "image": "profile_photo_url",
  "address": { /* PostalAddress */ },
  "aggregateRating": { /* AggregateRating */ },
  "priceRange": "BDT 5000-50000",
  "sameAs": ["facebook", "instagram", "youtube"],
  "email": "photographer@example.com",
  "telephone": "+880XXXXXXXXX"
}
```

---

## 4. CONTROLLER

### PublicPhotographerController (`app/Http/Controllers/PublicPhotographerController.php`)

**Routes:**

```php
// Show photographer profile by username (SEO-friendly)
GET /@{username}
  → showByUsername(Request $request, string $username)

// Legacy support - redirect to username URL
GET /photographer/{id}
  → showById(Request $request, int $id)

// API endpoints
GET /api/photographers/@{username}/portfolio
GET /api/photographers/@{username}/packages
GET /api/photographers/@{username}/reviews
GET /api/photographers/search
```

**Features:**
- 301 redirects for username changes
- Profile view tracking (in cache)
- Auto-generates SEO metadata
- Returns paginated portfolio, packages, reviews
- Search by name, username, category

---

## 5. ROUTES

### Web Routes (`routes/web.php`)

```php
// Public Photographer Profile Routes (SEO-friendly)
Route::get('/@{username}', [PublicPhotographerController::class, 'showByUsername'])
    ->name('photographer.profile.public');

Route::get('/photographer/{id}', [PublicPhotographerController::class, 'showById'])
    ->name('photographer.profile.legacy');

// Public API Routes for Photographer Profiles
Route::prefix('api/photographers')->group(function () {
    Route::get('/@{username}/portfolio', [PublicPhotographerController::class, 'getPortfolio']);
    Route::get('/@{username}/packages', [PublicPhotographerController::class, 'getPackages']);
    Route::get('/@{username}/reviews', [PublicPhotographerController::class, 'getReviews']);
    Route::get('/search', [PublicPhotographerController::class, 'search']);
});
```

**URL Format:**
- Profile: `https://photographersb.com/@mahidulislamnakib`
- API: `https://photographersb.com/api/photographers/@mahidulislamnakib/portfolio`

---

## 6. VIEWS

### Profile View (`resources/views/photographer/profile.blade.php`)

**Sections:**
1. **Photographer Header** - Image, name, verified badge, rating, location
2. **Bio Section** - Full biography if available
3. **Main Content**
   - Portfolio grid (3-column, paginated)
   - Packages sidebar
   - Contact section
4. **Reviews Section** - Paginated reviews with author info

**Features:**
- Dynamic SEO meta tags
- Share buttons (Facebook, WhatsApp, LinkedIn, Twitter, Copy Link)
- Responsive design
- Profile view tracking

### Share Buttons Component (`resources/views/photographer/partials/share-buttons.blade.php`)

**Buttons:**
1. **Copy Link** - Copies profile URL to clipboard
2. **Facebook** - Share with pre-filled title/description
3. **WhatsApp** - Share to contacts
4. **LinkedIn** - Professional sharing
5. **Twitter/X** - Tweet profile
6. **Email** - Email share option (optional)

**Uses OpenGraph Data:**
- og:title
- og:description
- og:image
- og:url

---

## 7. IMPLEMENTATION CHECKLIST

### Step 1: Database Migrations
```bash
# Run migrations
php artisan migrate

# Generate usernames for existing photographers
php artisan db:seed --class=GeneratePhotographerUsernames
```

### Step 2: Service Registration (optional, auto-discovered)
Services are auto-discovered in Laravel 11. If needed:

```php
// config/app.php
'providers' => [
    // ...
    App\Services\UsernameService::class,
    App\Services\SeoService::class,
    App\Services\SchemaJsonService::class,
],
```

### Step 3: Model Relationships
Verify in `User.php`:
```php
public function seoMeta()
{
    return $this->morphOne(SeoMeta::class, 'model');
}

public function usernameHistory()
{
    return $this->hasMany(UsernameHistory::class);
}
```

### Step 4: Routes
Routes already configured in `routes/web.php`

### Step 5: Views & Components
- Profile view in place
- Share buttons component in place
- Public layout created

### Step 6: SEO Tag Generation
SEO metadata auto-generates when:
1. Photographer profile is first viewed
2. Photographer account is created/updated
3. Manually triggered via service

### Step 7: Test & Verify

#### Test Username Generation
```php
$usernameService = app(\App\Services\UsernameService::class);

// Check availability
$usernameService->isAvailable('mahidulislamnakib'); // true/false

// Generate unique username
$username = $usernameService->generateUsername('Mahidul Islam Nakib');
// Returns: mahidul_islam_nakib (or mahidul_islam_nakib1, etc. if taken)
```

#### Test Profile Access
```
GET https://photographersb.com/@mahidulislamnakib
GET https://photographersb.com/@mahidulislamnakib/portfolio
```

#### Test SEO Output
View page source and verify:
- Meta title/description
- OpenGraph tags
- Twitter card tags
- Canonical URL
- Schema JSON-LD

---

## 8. PHOTOGRAPHER PROFILE DATA FLOW

```
User Visits /@username
    ↓
PublicPhotographerController::showByUsername()
    ↓
UsernameService::findByUsername() - Find user (handles redirects)
    ↓
Check if photographer (is_photographer())
    ↓
SeoService::getSeoMeta() - Get or generate SEO data
    ↓
SchemaJsonService - Generate schema.org markup
    ↓
Render photographer/profile.blade.php
    ↓
HTML with meta tags, OG tags, schema JSON-LD
```

---

## 9. SEO META AUTO-GENERATION

When a photographer profile is accessed:

**Auto-generated Meta Title:**
```
{name} (@{username}) | Photographer SB
```
Example: `Mahidul Islam Nakib (@mahidulislamnakib) | Photographer SB`

**Auto-generated Description:**
```
Hire {name}, a verified photographer in {city}. 
View portfolio, packages, reviews and contact on Photographer SB.
```

**OG Image Priority:**
1. User profile photo
2. First portfolio image
3. Site default image

**Schema Type:**
- `Person` - Individual photographers
- `LocalBusiness` - Studio owners

---

## 10. SOCIAL SHARING PREVIEW

When shared on social media, platforms fetch:

1. **Meta Title** → og:title
2. **Meta Description** → og:description
3. **Profile Image** → og:image
4. **Profile URL** → og:url
5. **Site Name** → og:site_name

Example preview on Facebook/WhatsApp:
```
[Profile Image]
Mahidul Islam Nakib (@mahidulislamnakib) | Photographer SB
Hire Mahidul Islam Nakib, a verified photographer in Dhaka. 
View portfolio, packages, reviews and contact on Photographer SB.
https://photographersb.com/@mahidulislamnakib
```

---

## 11. PERFORMANCE & CACHING

### Cache Keys
- `seo_meta_user_{user_id}` - SEO metadata cache
- `profile_views_{user_id}_{date}` - Daily view count

### Cache Duration
- SEO metadata: 7 days (on change)
- Profile views: 24 hours
- Cleared on photographer profile update

### Recommended Caching
```php
$seoMeta = cache()->remember(
    "seo_meta_user_{$user->id}",
    now()->addDays(7),
    function () use ($user) {
        return $this->seoService->generatePhotographerSeo($user);
    }
);
```

---

## 12. ADMIN DASHBOARD INTEGRATION

### Photographer Settings - SEO Tab

```blade
<div class="seo-settings">
    <h3>SEO Settings (Optional Override)</h3>
    
    <div class="form-group">
        <label>Meta Title</label>
        <input type="text" 
               value="{{ $photographer->seoMeta->meta_title ?? 'Auto-generated' }}"
               placeholder="Auto-generated: {name} (@{username}) | Photographer SB" />
    </div>
    
    <div class="form-group">
        <label>Meta Description</label>
        <textarea placeholder="Auto-generated description">
            {{ $photographer->seoMeta->meta_description ?? '' }}
        </textarea>
    </div>
    
    <div class="form-group">
        <label>OG Image</label>
        <input type="file" accept="image/*" />
        <small>Leave empty to use profile photo</small>
    </div>
    
    <div class="checkbox">
        <input type="checkbox" checked>
        Index in search engines (robots meta)
    </div>
</div>
```

---

## 13. MONITORING & ANALYTICS

### Track Profile Views
```php
// In PublicPhotographerController
$cacheKey = "profile_views_{$user->id}_" . now()->format('Y-m-d');
cache()->increment($cacheKey, 1, 86400);
```

### Get View Statistics
```php
$date = now()->format('Y-m-d');
$views = cache()->get("profile_views_{$user->id}_{$date}", 0);
```

---

## 14. TROUBLESHOOTING

### Username Not Showing
- Run migration: `php artisan migrate`
- Generate usernames: `php artisan db:seed --class=GeneratePhotographerUsernames`

### SEO Tags Not Rendering
- Verify `@yield('meta')` in layout
- Check `SeoMeta` relationship in User model
- Ensure profile is accessed, not direct ID

### 301 Redirects Not Working
- Verify `username_history` table populated
- Check redirect route in controller
- Test with old username in URL

### Schema JSON Not Valid
- Validate at: https://schema.org/docs/schemas.html
- Check JSON structure in `SchemaJsonService`
- Use JSON-LD validator: https://json-ld.org/playground/

---

## 15. FILES CREATED/MODIFIED

### Created Files
1. ✅ `database/migrations/2026_02_01_000001_add_username_to_users_table.php`
2. ✅ `database/migrations/2026_02_01_000002_create_username_history_table.php`
3. ✅ `database/migrations/2026_02_01_000003_create_seo_meta_table.php`
4. ✅ `app/Models/UsernameHistory.php`
5. ✅ `app/Services/UsernameService.php`
6. ✅ `app/Services/SeoService.php`
7. ✅ `app/Services/SchemaJsonService.php`
8. ✅ `app/Http/Controllers/PublicPhotographerController.php`
9. ✅ `resources/views/photographer/profile.blade.php`
10. ✅ `resources/views/photographer/partials/share-buttons.blade.php`
11. ✅ `resources/views/layouts/public.blade.php`
12. ✅ `database/seeders/GeneratePhotographerUsernames.php`

### Modified Files
1. ✅ `app/Models/User.php` - Added username + relations
2. ✅ `routes/web.php` - Added profile routes
3. ✅ `resources/views/app.blade.php` - Added @yield('meta')

---

## 16. NEXT STEPS

1. **Run migrations:**
   ```bash
   php artisan migrate
   php artisan db:seed --class=GeneratePhotographerUsernames
   ```

2. **Test profile access:**
   ```
   https://photographersb.com/@{username}
   ```

3. **Verify SEO tags:**
   - View page source
   - Check meta tags
   - Validate schema at https://schema.org/

4. **Set up caching (optional):**
   - Configure cache driver
   - Add cache middleware

5. **Add admin UI:**
   - Create SEO override form
   - Add to photographer settings

6. **Monitor performance:**
   - Track profile views
   - Monitor page load time
   - Check Lighthouse scores

---

## 17. SECURITY CONSIDERATIONS

✅ **Implemented:**
- SQL injection prevention (ORM)
- XSS prevention (e() helper)
- CSRF protection (native Laravel)
- Username validation (whitelist characters)
- Reserved usernames (system protection)

✅ **Recommendations:**
- Enable rate limiting on profile routes
- Monitor for spam/abuse
- Validate image uploads
- Sanitize user-submitted content

---

## 18. DEPLOYMENT

### 1. Push code and migrations
```bash
git push production main
```

### 2. Run migrations on server
```bash
ssh user@server.com
cd /app
php artisan migrate --force
php artisan db:seed --class=GeneratePhotographerUsernames --force
php artisan cache:clear
php artisan config:cache
```

### 3. Verify
- Test profile URLs: `/@username`
- Check meta tags in page source
- Validate schema JSON
- Test social sharing

### 4. Monitor
- Check application logs
- Monitor page load times
- Track profile view stats
- Monitor SEO rankings (after 2-4 weeks)

---

**Created by:** Principal Laravel Architect + SEO Engineer
**Date:** February 1, 2026
**Status:** ✅ Complete & Ready for Deployment
