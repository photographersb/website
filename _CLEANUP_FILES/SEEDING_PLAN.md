# 🌱 DATABASE SEEDING PLAN
**Photographer SB - Production Ready Setup**
*Last Updated: February 5, 2026*

---

## 🎯 Overview

This document outlines the comprehensive seeding strategy for Photographer SB. The system is designed to start with **ZERO sample data** and only essential configuration to ensure production readiness.

---

## 📋 Seeding Philosophy

### ✅ What We SEED (Essential)
- System roles and permissions
- Super admin account
- Bangladesh geographic data (divisions, districts, cities)
- Photography categories
- Platform settings and configuration
- SEO metadata templates
- Notification templates
- Trending hashtags (empty structure)

### ❌ What We DON'T SEED (User Generated)
- Photographers (users register themselves)
- Photographer portfolios
- Packages (photographers create)
- Albums and photos
- Events and competitions
- Reviews and ratings
- Bookings and transactions
- Sponsors (admin adds manually)

---

## 🗺️ Seeding Flow

```
┌─────────────────────────────────────────────────┐
│  STEP 1: Core System Configuration              │
├─────────────────────────────────────────────────┤
│  ✓ RolesSeeder                                  │
│    - Super Admin                                │
│    - Admin                                      │
│    - Photographer                               │
│    - Judge                                      │
│  ✓ SuperAdminSeeder                             │
│    - mahidulislamnakib@gmail.com               │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│  STEP 2: Bangladesh Geographic Data             │
├─────────────────────────────────────────────────┤
│  ✓ BangladeshLocationSeeder                     │
│    - 8 Divisions                                │
│    - 64 Districts                               │
│    - Major Cities (Dhaka, Chittagong, etc.)     │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│  STEP 3: Photography Configuration              │
├─────────────────────────────────────────────────┤
│  ✓ PhotographyCategoriesSeeder                  │
│    - Wedding Photography                        │
│    - Event Photography                          │
│    - Portrait Photography                       │
│    - Product Photography                        │
│    - Fashion Photography                        │
│    - Food Photography                           │
│    - Corporate Photography                      │
│    - Studio Photography                         │
│    - Travel & Documentary                       │
│    - Holud Photography                          │
│    - Pre-wedding Photography                    │
│    - Wedding Cinematography                     │
│  ✓ HashtagSeeder                                │
│    - Initialize trending hashtags structure     │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│  STEP 4: Platform Settings                      │
├─────────────────────────────────────────────────┤
│  ✓ PlatformSettingsSeeder                       │
│    - Site name, logo, contact info              │
│    - Payment gateway configuration              │
│    - Email settings                             │
│    - Social media links                         │
│  ✓ SeoMetaSeeder                                │
│    - Default meta tags                          │
│    - Open Graph settings                        │
│    - Schema.org markup                          │
│  ✓ NotificationTemplatesSeeder                  │
│    - Welcome email                              │
│    - Booking confirmation                       │
│    - Payment receipt                            │
│    - Profile approval                           │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│  STEP 5: System Optimization                    │
├─────────────────────────────────────────────────┤
│  ✓ cache:clear                                  │
│  ✓ config:cache                                 │
│  ✓ route:cache                                  │
└─────────────────────────────────────────────────┘
```

---

## 🔧 How to Use

### Fresh Installation

```bash
# Option 1: Using the fresh-db.php script (Recommended)
php fresh-db.php

# Option 2: Using artisan command
php artisan migrate:fresh --seed
```

### Reseed Without Migration

```bash
php artisan db:seed
```

### Seed Specific Seeder

```bash
php artisan db:seed --class=PhotographyCategoriesSeeder
```

---

## 🔐 Super Admin Credentials

**IMPORTANT: Change password immediately after first login!**

```
Email: mahidulislamnakib@gmail.com
Password: SuperAdmin@2026
```

**Access Points:**
- Admin Panel: http://127.0.0.1:8000/admin/login
- Frontend: http://127.0.0.1:8000

---

## 📊 Seeder Details

### 1. RolesSeeder
**File:** `database/seeders/RolesSeeder.php`

**Creates:**
- Super Admin (full system access)
- Admin (manage content, users, settings)
- Photographer (create portfolios, receive bookings)
- Judge (evaluate competition submissions)

---

### 2. SuperAdminSeeder
**File:** `database/seeders/SuperAdminSeeder.php`

**Creates:**
- 1 Super Admin user
- Email: mahidulislamnakib@gmail.com
- Password: SuperAdmin@2026
- Full system privileges

---

### 3. BangladeshLocationSeeder
**File:** `database/seeders/BangladeshLocationSeeder.php`

**Creates:**
- 8 Divisions (Dhaka, Chittagong, Rajshahi, Khulna, Barisal, Sylhet, Rangpur, Mymensingh)
- 64 Districts (all Bangladesh districts)
- Major Cities with geographic coordinates

**Purpose:**
- Enable location-based photographer search
- City and district dropdowns
- SEO-optimized location pages

---

### 4. PhotographyCategoriesSeeder
**File:** `database/seeders/PhotographyCategoriesSeeder.php`

**Creates:**
- 12 photography specialization categories
- Each with icon, description, display order
- Active status for filtering

**Purpose:**
- Photographer specialization selection
- Category-based search and filtering
- Homepage category cards

---

### 5. HashtagSeeder
**File:** `database/seeders/HashtagSeeder.php`

**Creates:**
- Empty hashtag structure
- Trending topics framework

**Purpose:**
- Social media integration
- Content discovery
- Trending topics tracking

---

### 6. PlatformSettingsSeeder
**File:** `database/seeders/PlatformSettingsSeeder.php`

**Creates:**
- Site configuration (name, logo, contact)
- Payment gateway settings (bKash, Nagad, Rocket)
- Email SMTP configuration
- Social media links
- Platform fees and commission rates

**Purpose:**
- Centralized platform configuration
- Admin-editable settings

---

### 7. SeoMetaSeeder
**File:** `database/seeders/SeoMetaSeeder.php`

**Creates:**
- Default meta titles and descriptions
- Open Graph tags
- Twitter Card settings
- Schema.org structured data

**Purpose:**
- Search engine optimization
- Social media sharing
- Rich snippets in search results

---

### 8. NotificationTemplatesSeeder
**File:** `database/seeders/NotificationTemplatesSeeder.php`

**Creates:**
- Email templates (welcome, booking, payment)
- SMS templates
- In-app notification templates
- Variable placeholders for dynamic content

**Purpose:**
- Consistent communication
- Admin-editable templates
- Multi-channel notifications

---

### 9. PhotographersSeeder (Sample Data)
**File:** `database/seeders/PhotographersSeeder.php`

**Creates:**
- 8 sample photographer profiles
- Verified accounts with realistic data
- Various specializations and locations
- Test data for development

**Sample Photographers:**
1. Rahim Khan - Wedding & Events (Dhaka)
2. Farida Akter - Fashion & Studio (Dhaka)
3. Kamal Hossain - Corporate & Events (Chittagong)
4. Nasrin Islam - Wedding Films (Dhaka)
5. Rakib Ahmed - Product & Food (Khulna)
6. Sadia Rahman - Traditional/Holud (Dhaka)
7. Tariq Mahmud - Drone Services (Rangpur)
8. Ayesha Begum - Full-Service Events (Rajshahi)

**Login Credentials:** All sample photographers use password: `password`

**Purpose:**
- Test photographer features
- Demo bookings and reviews
- UI/UX testing with realistic data
- Development environment setup

---

## 🚀 Post-Seeding Checklist

### Immediate Actions (First Login)

- [ ] Change super admin password
- [ ] Verify email settings (SMTP configuration)
- [ ] Configure payment gateways (bKash, Nagad, Rocket)
- [ ] Update platform settings (site name, logo, contact info)
- [ ] Set commission rates and platform fees
- [ ] Configure notification preferences

### Content Setup

- [ ] Review and adjust photography categories
- [ ] Add social media links
- [ ] Configure SEO meta tags for main pages
- [ ] Set up email templates branding
- [ ] Create additional admin users if needed

### Testing

- [ ] Test photographer registration flow
- [ ] Verify email sending
- [ ] Test location-based search
- [ ] Check category filtering
- [ ] Verify admin panel access

---

## 📦 Database Tables Populated

### System Tables
✅ `roles` - 4 roles
✅ `users` - 1 super admin + 8 sample photographers
✅ `photographers` - 8 sample profiles (verified, ready for testing)

### Geographic Tables
✅ `divisions` - 8 divisions
✅ `districts` - 64 districts
✅ `cities` - Major cities

### Configuration Tables
✅ `categories` - 12 photography categories
✅ `hashtags` - Empty structure
✅ `platform_settings` - System configuration
✅ `seo_meta` - SEO templates
✅ `notification_templates` - Email/SMS templates

### Empty Tables (User Generated Content)
❌ `events` - Empty
❌ `competitions` - Empty
❌ `albums` - Empty
❌ `photos` - Empty
❌ `packages` - Empty
❌ `bookings` - Empty
❌ `reviews` - Empty
❌ `sponsors` - Empty

---

## 🔄 Re-seeding Strategy

### When to Re-seed

1. **After Database Schema Changes**
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **To Reset All Data**
   ```bash
   php fresh-db.php
   ```

3. **To Update Specific Configuration**
   ```bash
   php artisan db:seed --class=PlatformSettingsSeeder
   ```

### Data Preservation

**IMPORTANT:** Re-seeding will DELETE ALL DATA including:
- All photographers and their portfolios
- All events and competitions
- All bookings and transactions
- All user-generated content

**Only the super admin account will be recreated.**

---

## 🐛 Troubleshooting

### Common Issues

**Issue:** Seeding fails with foreign key constraint error
```bash
# Solution: Fresh migration
php artisan migrate:fresh --seed
```

**Issue:** "Class not found" error
```bash
# Solution: Clear composer autoload
composer dump-autoload
php artisan db:seed
```

**Issue:** Cache-related errors
```bash
# Solution: Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Issue:** Permission denied on fresh-db.php
```bash
# Solution (Linux/Mac):
chmod +x fresh-db.php

# Solution (Windows):
php fresh-db.php
```

---

## 📝 Development vs Production

### Development Seeding (Current Setup)
The default setup now includes sample photographers for testing:

```bash
php artisan migrate:fresh --seed
```

**Includes:**
- Essential configuration (roles, locations, categories)
- 8 sample photographers with verified profiles
- Various specializations across Bangladesh cities
- Ready for immediate testing

### Production Seeding
For production deployment WITHOUT sample data:

**Option 1: Comment out PhotographersSeeder**
1. Edit `database/seeders/DatabaseSeeder.php`
2. Comment out these lines:
```php
// $this->command->line('👥 STEP 3.5: Sample Photographers');
// $this->call(PhotographersSeeder::class);
// $this->command->line('  ✓ Sample photographers created for testing');
```
3. Run: `php artisan migrate:fresh --seed`

**Option 2: Create ProductionSeeder**
```bash
php artisan make:seeder ProductionSeeder
```

Then only include essential seeders without PhotographersSeeder.

---

## 🎓 Best Practices

1. **Always Backup Before Re-seeding**
   ```bash
   php artisan backup:run
   ```

2. **Use Transactions in Seeders**
   ```php
   DB::transaction(function () {
       // Seeding logic
   });
   ```

3. **Make Seeders Idempotent**
   ```php
   Category::firstOrCreate(
       ['slug' => 'wedding'],
       ['name' => 'Wedding Photography']
   );
   ```

4. **Log Seeding Progress**
   ```php
   $this->command->info('✓ Categories seeded');
   ```

5. **Handle Errors Gracefully**
   ```php
   try {
       // Seeding
   } catch (\Exception $e) {
       $this->command->error('Seeding failed');
       throw $e;
   }
   ```

---

## 📞 Support

**For seeding issues, contact:**
- Email: mahidulislamnakib@gmail.com
- Check logs: `storage/logs/laravel.log`
- Database queries: Enable query logging

---

## 📅 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Feb 5, 2026 | Initial production seeding plan |
| | | Removed all sample data |
| | | Focused on essential configuration only |

---

**✅ System is now production-ready with zero sample data!**
