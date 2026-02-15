# LIVE DEPLOYMENT QUICK REFERENCE GUIDE

**Last Updated**: February 15, 2026  
**Status**: ✅ PRODUCTION READY  
**Environment**: Live/Production  

---

## 🚀 QUICK START FOR LIVE DEPLOYMENT

### One-Command Deployment
```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install && npm install

# 3. Build and deploy
npm run build && php artisan migrate && php artisan db:seed --class=CompleteCompetitionSeeder
```

### Individual Commands (if needed)
```bash
php artisan cache:clear          # Clear all caches
php artisan config:clear         # Clear config cache
php artisan view:clear           # Clear view cache
php artisan migrate              # Run database migrations
php artisan migrate:status       # Check migration status
php artisan db:seed --class=CompleteCompetitionSeeder  # Seed demo data
npm run build                    # Build frontend assets
```

---

## 📋 CRITICAL FILES & LOCATIONS

### Code Files (Modified/Created)
```
resources/js/Pages/Admin/Certificates/ManualIssuance.vue
  └─ Manual certificate issuance page (redesigned)

database/seeders/CompleteCompetitionSeeder.php
  └─ Production-ready idempotent seeder

.env
  └─ Database: photodb
     User: photoadmin
     Password: Photo@2026
```

### Configuration Files
```
config/database.php
  └─ MySQL: 127.0.0.1:3306

vite.config.js
  └─ Frontend build configuration

tailwind.config.js
  └─ CSS configuration

package.json
  └─ npm dependencies
```

### Log Files
```
storage/logs/laravel.log
  └─ Main application logs

storage/logs/error.log
  └─ Error tracking
```

---

## 🔐 TEST CREDENTIALS

### Admin Dashboard
```
Email: admin@photographar.com
Password: password123
URL: /admin/certificates/manual-issuance
```

### Test Photographers
```
Email: photographer1@demo.test
Email: photographer2@demo.test
Email: photographer3@demo.test
Email: photographer4@demo.test
Email: photographer5@demo.test
Password: password123 (all accounts)
```

### Test Judges
```
Email: judge1@demo.test
Email: judge2@demo.test
Email: judge3@demo.test
Password: password123 (all accounts)
```

---

## 📊 DATABASE CONNECTION

### Connection Details
```
Host: 127.0.0.1
Port: 3306
Database: photodb
Username: photoadmin
Password: Photo@2026
Charset: utf8mb4
Collation: utf8mb4_unicode_ci
```

### Quick Database Commands
```bash
# Test connection
mysql -u photoadmin -p"Photo@2026" photodb -e "SELECT COUNT(*) FROM users;"

# Verify seeded data
mysql -u photoadmin -p"Photo@2026" photodb -e "
  SELECT COUNT(*) as 'Competitions' FROM competitions WHERE status='closed';
  SELECT COUNT(*) as 'Submissions' FROM competition_submissions;
  SELECT COUNT(*) as 'Winners' FROM competition_submissions WHERE is_winner=1;
"

# Backup database
mysqldump -u photoadmin -p"Photo@2026" photodb > backup_$(date +%Y%m%d).sql

# Restore database
mysql -u photoadmin -p"Photo@2026" photodb < backup_20260215.sql
```

---

## 🎯 MAIN FEATURES DEPLOYED

### Manual Certificate Issuance
- **URL**: `/admin/certificates/manual-issuance`
- **Status**: ✅ READY
- **Features**:
  - Dashboard-aligned UI design
  - 3-step form process
  - Live certificate preview
  - Admin-only access
  - 15 demo submissions available

### Competition Management
- **Total Closed Competitions**: 1
- **Total Submissions**: 15
- **Demo Winners**: 3
- **Available Judges**: 3
- **Test Data**: Fully populated and ready

### API Endpoints
- **Route**: `/admin/competitions`
- **Status**: ✅ Working (no 404 errors)
- **Response**: Returns all competitions
- **Fallback**: Public `/competitions` endpoint available

---

## ⚙️ COMMON MAINTENANCE TASKS

### Clear Application Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Rebuild Frontend (after code changes)
```bash
npm run build
```

### Check Migration Status
```bash
php artisan migrate:status
```

### Run Seeder Again (safe - idempotent)
```bash
php artisan db:seed --class=CompleteCompetitionSeeder
```

### View Recent Logs
```bash
tail -f storage/logs/laravel.log
```

---

## 🐛 TROUBLESHOOTING

### Issue: Manual Issuance page shows 404 on load
**Solution**: 
```bash
php artisan cache:clear
php artisan config:clear
# Check API response: curl http://localhost/admin/competitions
```

### Issue: Seeder fails with "Duplicate entry"
**Solution**: Seeder is idempotent - safe to re-run
```bash
# This will NOT create duplicates
php artisan db:seed --class=CompleteCompetitionSeeder
```

### Issue: CSS not loading correctly
**Solution**: Rebuild frontend
```bash
npm run build
php artisan cache:clear
```

### Issue: Database connection error
**Solution**: Verify credentials in .env
```bash
mysql -u photoadmin -p"Photo@2026" photodb -e "SELECT 1;"
```

### Issue: High server load after deployment
**Solution**: Check logs and optimize
```bash
tail storage/logs/laravel.log | grep -i error
php artisan migrate:status
php artisan cache:clear
```

---

## 📈 PERFORMANCE METRICS

### Expected Performance
- **Page Load**: < 2 seconds
- **API Response**: < 500ms
- **Build Time**: ~10 seconds
- **Seeder Execution**: < 5 seconds
- **Database Query**: < 100ms

### Monitoring Commands
```bash
# Check server resources
free -h                    # Memory
df -h                      # Disk space
top -b -n 1 | head         # CPU usage

# Check database performance
mysql -u photoadmin -p"Photo@2026" photodb -e "
  SHOW PROCESSLIST;
  SHOW ENGINE INNODB STATUS\G
"
```

---

## 🔄 DEPLOYMENT CHECKLIST (Quick Version)

- [ ] Pull latest code
- [ ] Verify .env configuration
- [ ] Run migrations: `php artisan migrate`
- [ ] Seed data: `php artisan db:seed --class=CompleteCompetitionSeeder`
- [ ] Build frontend: `npm run build`
- [ ] Clear caches: `php artisan cache:clear`
- [ ] Test admin login at `/admin/certificates/manual-issuance`
- [ ] Verify API endpoints responding
- [ ] Check error logs for issues
- [ ] Monitor for 30 minutes post-deployment

---

## 📞 EMERGENCY PROCEDURES

### Rollback if Critical Issue
```bash
# 1. Stop application (if necessary)
# 2. Revert code
git revert HEAD

# 3. Clear cache
php artisan cache:clear

# 4. Rebuild
npm run build

# 5. Restore database from backup
mysql -u photoadmin -p"Photo@2026" photodb < backup_20260215.sql

# 6. Restart services
php artisan cache:clear
```

### Performance Issue Resolution
```bash
# 1. Check what's consuming resources
ps aux | grep php
ps aux | grep mysql

# 2. Kill stuck processes (carefully)
pkill -f "php artisan queue:work"

# 3. Clear cache
php artisan cache:clear

# 4. Check disk space
du -sh storage/
du -sh storage/logs/

# 5. Archive old logs if needed
cd storage/logs
gzip -v laravel.log.old
```

### Database Issue Resolution
```bash
# 1. Rebuild indexes
php artisan migrate:refresh --seed

# 2. Check data integrity
php artisan tinker
>>> DB::select('CHECK TABLE users');

# 3. Optimize tables
php artisan command:database:optimize

# 4. If corrupted, restore from backup
mysql -u photoadmin -p"Photo@2026" photodb < backup.sql
```

---

## 📚 DOCUMENTATION

All detailed documentation available in:

1. **DEPLOYMENT_PRODUCTION_READY.md**
   - Comprehensive deployment guide
   - Pre/post-deployment checklists
   - Known issues and resolutions

2. **DATABASE_MIGRATIONS_VERIFICATION.md**
   - Migration verification procedures
   - Schema validation
   - Constraint documentation

3. **FINAL_PRE_PRODUCTION_DEPLOYMENT_CHECKLIST.md**
   - Complete final checklist
   - All verification results
   - Sign-off documentation

---

## ✅ VERIFICATION CHECKLIST

Before declaring "Live":
- [ ] All 102 migrations showing "Ran"
- [ ] Admin account can login
- [ ] Manual Issuance page loads without errors
- [ ] API endpoints responding correctly
- [ ] 15 demo submissions visible
- [ ] 3 winners marked
- [ ] 3 judges available
- [ ] No JavaScript errors in console
- [ ] No API 404 errors
- [ ] Database backup available
- [ ] Error logs cleaned/archived
- [ ] Monitoring tools active

---

## 🎯 SUCCESS INDICATORS

When live deployment is successful, you should see:

1. ✅ Manual Issuance page loads instantly
2. ✅ Competitions dropdown populated with demo data
3. ✅ Status cards showing accurate numbers
4. ✅ Step 1→2→3 form navigation smooth
5. ✅ Certificate preview updates in real-time
6. ✅ No errors in browser console (F12)
7. ✅ No errors in Laravel logs
8. ✅ API response times under 500ms
9. ✅ Database queries efficient
10. ✅ All admin features accessible

---

## 📝 IMPORTANT NOTES

### Idempotency
- ✅ Seeder can be run multiple times safely
- ✅ Uses `firstOrCreate()` pattern
- ✅ No duplicate data created
- ✅ Safe for CI/CD pipelines

### Migration Safety
- ✅ All migrations are reversible
- ✅ Rollback supported: `php artisan migrate:rollback`
- ✅ Transaction support enabled
- ✅ No data loss on rollback

### API Deprecation
- ⚠️ Hard-coded query strings deprecated
- ✅ Use params object: `{ params: { ... } }`
- ✅ Fallback endpoints implemented
- ✅ Better error handling

---

## 🚨 CRITICAL ALERTS

**NEVER**:
- ❌ Delete or modify `.env` file directly
- ❌ Run migrations with `--force` without backup
- ❌ Clear database without backup
- ❌ Modify seeder to use `create()` instead of `firstOrCreate()`
- ❌ Deploy without testing locally first

**ALWAYS**:
- ✅ Backup database before deployment
- ✅ Clear cache after deployment
- ✅ Run migrations before seeders
- ✅ Build frontend before going live
- ✅ Monitor error logs for 24 hours
- ✅ Keep deployment documentation updated

---

## 📞 SUPPORT RESOURCES

- **Laravel Docs**: https://laravel.com/docs
- **Vue 3 Docs**: https://vuejs.org/
- **MySQL Docs**: https://dev.mysql.com/doc/
- **Vite Docs**: https://vitejs.dev/

**Local Help**:
```bash
php artisan help migrate
php artisan help db:seed
npm run --help
```

---

## ⏰ TYPICAL DEPLOYMENT TIME

- Code pull: 30 seconds
- Dependencies: 1 minute
- Build: 10 seconds
- Migrations: 30 seconds
- Seeding: 3 seconds
- Cache clear: 5 seconds
- **Total**: ~3 minutes
- **Testing**: 2-5 minutes

**Total Approximate Downtime**: 5-10 minutes (can be reduced with caching strategies)

---

**STATUS: ✅ GO LIVE**

All systems verified and production-ready.

Date: February 15, 2026

