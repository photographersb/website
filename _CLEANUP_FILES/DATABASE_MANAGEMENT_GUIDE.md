# 📚 Database Management Guide

## Quick Reference

### Essential Commands

```bash
# Purge demo/test data only (keeps users & core content)
php artisan app:purge-demo-data --force

# Full database reset with fresh Bangladesh seed
php artisan migrate:fresh --seed

# Check database status
php artisan migrate:status

# Clear all caches
php artisan optimize:clear
```

## 🔄 Database Reset Workflow

### Option 1: Purge Demo Data Only
Use this when you want to clean test data but keep existing users and photographers.

```bash
php artisan app:purge-demo-data --force
```

**What it does**:
- ✅ Removes: competitions, events, votes, judges, mentors, hashtags
- ✅ Keeps: users, photographers, categories, cities, packages, albums, photos
- ✅ Safe: Handles foreign key constraints automatically
- ✅ Fast: Completes in seconds

**When to use**:
- After testing competition features
- Before going live with production photographers
- Cleaning up demo accounts while keeping real users

### Option 2: Complete Database Rebuild
Use this for a fresh start with Bangladesh seed data.

```bash
php artisan migrate:fresh --seed
```

**What it does**:
- 🔴 **WARNING**: Drops ALL tables and data
- ✅ Recreates all 77 database tables
- ✅ Seeds Super Admin (mahidulislamnakib@gmail.com)
- ✅ Loads 63 Bangladesh districts
- ✅ Configures 12 photography categories
- ✅ Optimizes caches

**When to use**:
- Initial deployment to production
- Major database structure changes
- Complete system reset needed

## 📊 Seeded Data Details

### Super Admin Account
```
Email: mahidulislamnakib@gmail.com
Password: SuperAdmin@2026 (CHANGE THIS!)
Role: super_admin
Status: Active, fully verified
```

### Bangladesh Locations (63 Districts)

**Dhaka Division (13)**
- Dhaka, Gazipur, Narayanganj, Tangail, Munshiganj, Manikganj, Narsingdi, Kishoreganj, Gopalganj, Faridpur, Madaripur, Rajbari, Shariatpur

**Chittagong Division (10)**
- Chittagong, Cox's Bazar, Bandarban, Rangamati, Feni, Noakhali, Comilla, Chandpur, Lakshmipur, Khagrachhari

**Barisal Division (6)**
- Barisal, Bhola, Pirojpur, Patuakhali, Barguna, Jhalokati

**Khulna Division (10)**
- Khulna, Satkhira, Jessore, Jashore, Magura, Narail, Bagerhat, Chuadanga, Meherpur, Kushtia

**Rajshahi Division (8)**
- Rajshahi, Bogra, Naogaon, Natore, Nawabganj, Pabna, Sirajganj, Chapainawabganj

**Rangpur Division (8)**
- Rangpur, Dinajpur, Nilphamari, Kurigram, Gaibandha, Lalmonirhat, Thakurgaon, Panchagarh

**Mymensingh Division (4)**
- Mymensingh, Jamalpur, Sherpur, Netrokona

**Sylhet Division (4)**
- Sylhet, Sunamganj, Moulvibazar, Habiganj

### Photography Categories (12)

1. **💍 Wedding Photography** - Traditional wedding photography
2. **🎬 Wedding Cinematography** - Wedding videography and films
3. **🌹 Pre-wedding** - Pre-wedding photo shoots
4. **🎉 Holud Photography** - Traditional Holud ceremony
5. **📷 Event Photography** - Corporate and social events
6. **💼 Corporate Photography** - Business and professional photography
7. **📦 Product Photography** - E-commerce and product shots
8. **🍽️ Food Photography** - Restaurant and food styling
9. **👗 Fashion & Model** - Fashion shoots and modeling
10. **🎥 Studio Photography** - Portrait and passport photos
11. **✈️ Travel & Documentary** - Travel and documentary work
12. **🚁 Drone Photography** - Aerial and drone services

## 🛠️ Maintenance Commands

### Clear Caches
```bash
# Clear all application caches
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear everything at once
php artisan optimize:clear

# Recreate optimized caches
php artisan optimize
```

### Database Inspection
```bash
# Show migration status
php artisan migrate:status

# Show all routes
php artisan route:list

# Database connection test
php artisan db:monitor

# Interactive database shell
php artisan tinker
```

## 🔍 Troubleshooting

### Issue: Foreign Key Constraint Errors
**Solution**: The purge command handles this automatically. If you see FK errors:
```bash
# Manual fix:
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE table_name;
SET FOREIGN_KEY_CHECKS=1;
```

### Issue: Migration Already Exists Error
**Solution**: Use fresh migrate:
```bash
php artisan migrate:fresh --seed
```

### Issue: Duplicate Entry Errors
**Solution**: Seeders are now idempotent. If you see duplicates:
```bash
# Clear and reseed:
php artisan migrate:fresh --seed
```

### Issue: Super Admin Can't Login
**Solution**: Verify user exists:
```bash
php artisan tinker
>>> DB::table('users')->where('role', 'super_admin')->first();
```

Reset password if needed:
```bash
# Will be added as separate command in future
```

## 📋 Seeder Files

All seeders are located in `database/seeders/`:

- **RolesSeeder.php** - Configures system roles
- **SuperAdminSeeder.php** - Creates/updates Super Admin
- **BangladeshLocationSeeder.php** - Loads 63 districts
- **PhotographyCategoriesSeeder.php** - Sets up 12 categories
- **DatabaseSeeder.php** - Orchestrates all seeders

## 🔒 Security Checklist

- [ ] Change Super Admin password immediately
- [ ] Review and update `.env` database credentials
- [ ] Enable backup schedule (`php artisan schedule:work`)
- [ ] Configure `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in production
- [ ] Review security headers in `config/cors.php`

## 💾 Backup Strategy

### Before Any Major Operation
```bash
# Export database
mysqldump -u root -p photographar_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Or use Laravel backup package
php artisan backup:run
```

### Restore from Backup
```bash
mysql -u root -p photographar_db < backup_file.sql
```

## 📝 Custom Seeder Creation

To create additional seeders:

```bash
# Create new seeder
php artisan make:seeder YourSeederName

# Add to DatabaseSeeder.php:
$this->call([
    YourSeederName::class,
]);

# Run specific seeder
php artisan db:seed --class=YourSeederName
```

## 🎯 Production Deployment

### Initial Deployment
```bash
# 1. Upload files to server
# 2. Configure .env file
# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Set up database
php artisan migrate:fresh --seed

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set permissions
chmod -R 755 storage bootstrap/cache
```

### Updates
```bash
# 1. Pull latest code
git pull origin main

# 2. Update dependencies
composer install --no-dev

# 3. Run migrations only (no seed)
php artisan migrate --force

# 4. Clear & rebuild caches
php artisan optimize:clear
php artisan optimize
```

## 📞 Support

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Database errors: Check MySQL error logs
- Cache issues: Run `php artisan optimize:clear`

---

**Last Updated**: February 1, 2026  
**Version**: 1.0.0
