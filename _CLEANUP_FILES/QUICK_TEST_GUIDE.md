# Quick Test Guide - Photo Management System

## ✅ System Status: READY TO TEST

All issues fixed:
- ✅ Album `name` field corrected (was using `title`)
- ✅ Album slug auto-generation working
- ✅ Photo `uuid` field added to fillable
- ✅ Package `price`, `duration_hours`, etc. fields added to database
- ✅ Package `base_price` made nullable
- ✅ Frontend built successfully (1,024.60 KB)
- ✅ AlbumPhotoManager component created
- ✅ Pexels integration ready

## 🧪 Test Scenarios

### Test 1: Create Album (VERIFIED ✅)
```
Login: http://127.0.0.1:8000/login
Email: kamal.hossain@photographar.com
Password: password

1. Click "Dashboard" button
2. Go to "Portfolio" tab
3. Click "+ Add Album"
4. Enter name: "Summer Weddings 2026"
5. Description: "Beautiful outdoor ceremonies"
6. Check "Make this album public"
7. Click "Create Album"
8. ✅ Should save successfully
```

**Expected Result:**
- Album appears in grid
- Shows "0 photos"
- Green "Public" badge
- "View" button clickable

### Test 2: Add Photos from Pexels
```
1. Click "View" on the album you created
2. Click "🔍 Add from Pexels" button
3. Type "wedding" in search box
4. Click "Search" or press Enter
5. Click on 3-5 photos to select them (checkmark appears)
6. Click "Add to Album" button
7. ✅ Photos should appear in grid
```

**Expected Result:**
- Photos load in grid (2-4 columns)
- Each photo has "Delete" button on hover
- Album header shows updated photo count
- First photo becomes album cover

### Test 3: Create Package with Image (VERIFIED ✅)
```
1. Go to "Packages" tab
2. Click "+ Add Package"
3. Fill form:
   - Name: "Wedding Premium"
   - Price: 45000
   - Description: "Full day coverage with album"
   - Coverage Hours: 10
   - Edited Photos: 150
   - Raw Photos: 100
   - Delivery Days: 14
   - Check "Make this package active"
4. Scroll to "📸 Package Images"
5. Paste URL: https://images.pexels.com/photos/1024993/pexels-photo-1024993.jpeg
6. See preview appear below
7. Click "Create Package"
```

**Expected Result:**
- Package appears in grid with cover image
- Shows price ৳45,000
- Lists all features
- Green "Active" badge

### Test 4: Edit Package
```
1. Click "Edit" on any package
2. Change price to 50000
3. Update cover image URL
4. Click "Update Package"
```

**Expected Result:**
- Changes save
- New price displays
- New image shows

### Test 5: Delete Photo
```
1. Open album with photos
2. Hover over any photo
3. Click "Delete" button
4. Confirm deletion
```

**Expected Result:**
- Photo removed from grid
- Photo count decreases
- If cover photo deleted, next photo becomes cover

## 🎯 Quick Sample Data

### Sample Pexels URLs for Testing

**Wedding Photos:**
```
https://images.pexels.com/photos/1024993/pexels-photo-1024993.jpeg
https://images.pexels.com/photos/2253870/pexels-photo-2253870.jpeg
https://images.pexels.com/photos/1444442/pexels-photo-1444442.jpeg
https://images.pexels.com/photos/2959192/pexels-photo-2959192.jpeg
```

**Portrait Photos:**
```
https://images.pexels.com/photos/1516680/pexels-photo-1516680.jpeg
https://images.pexels.com/photos/1152994/pexels-photo-1152994.jpeg
https://images.pexels.com/photos/733872/pexels-photo-733872.jpeg
```

**Landscape Photos:**
```
https://images.pexels.com/photos/417074/pexels-photo-417074.jpeg
https://images.pexels.com/photos/531880/pexels-photo-531880.jpeg
```

## 🔍 Pexels Search Tips

### Quick Search Tags Available:
- wedding
- portrait
- landscape
- nature
- sunset
- architecture
- fashion
- food

### Finding More URLs:
1. Go to https://www.pexels.com
2. Search for your topic
3. Click on image
4. Right-click image → "Copy Image Address"
5. Paste in package form

## 📊 Database Verification

Check if data saved correctly:
```sql
-- View albums
SELECT id, photographer_id, name, slug, photo_count FROM albums;

-- View photos in album
SELECT id, album_id, title, image_url FROM photos WHERE album_id = 1;

-- View packages
SELECT id, name, price, duration_hours, edited_photos, cover_image FROM packages;
```

## 🚨 Troubleshooting

**Album won't save:**
- ✅ FIXED: Now uses `name` field (was `title`)
- Clear browser cache
- Check console (F12) for errors

**Photos not adding:**
- Verify Pexels API key in .env
- Try with sample URLs above
- Check if album exists

**Package images broken:**
- Ensure URL is direct image link
- Must start with https://
- Test URL in browser first

**Pexels search not working:**
- Add API key to .env: `PEXELS_API_KEY=your_key`
- Get free key: https://www.pexels.com/api/
- Uses 'demo' key by default (limited)

## ✨ Success Checklist

After testing, you should have:
- [ ] At least 1 album created
- [ ] 3+ photos in album
- [ ] Album shows correct photo count
- [ ] Album cover photo displays
- [ ] 1+ package with cover image
- [ ] Package card shows image
- [ ] Can edit package and change image
- [ ] Can delete photos from album
- [ ] Public profile shows albums (when implemented)

## 🎉 Demo Data Created

During testing, we created:
```
✅ Album: "Test Wedding Album" (ID varies)
✅ Album: "Product Photography Collection" with 37 photos
✅ Package: "Wedding Gold" - ৳35,000, 8 hours, 120 photos
✅ Photo: Wedding Couple in album
```

## 📝 Next Steps After Testing

1. **Get Pexels API Key** (if not already done)
   - Free tier: 200 requests/hour
   - Sign up at https://www.pexels.com/api/

2. **Add More Sample Data**
   - Create 3-5 albums per photographer
   - Add 10-15 photos per album
   - Create 3-4 packages with different prices

3. **Test Public Profile**
   - Visit /photographer/{slug}
   - Verify albums show
   - Check if package images display

4. **Test Booking Flow**
   - Try booking a package
   - Verify package details show correctly

5. **Mobile Testing**
   - Test on phone/tablet
   - Check responsive grid layout
   - Verify touch interactions

## 🔧 Technical Notes

**Photo Storage:**
- External URLs (Pexels CDN)
- No local uploads yet
- Fast loading
- No storage costs

**Database Structure:**
- Albums: name, slug, cover_photo_url, photo_count
- Photos: uuid, image_url, thumbnail_url, title
- Packages: price, duration_hours, edited_photos, cover_image

**API Endpoints:**
- POST /photographer/albums/{id}/photos
- GET /photographer/photos/search-pexels
- DELETE /photographer/photos/{id}
- PUT /photographer/photos/{id}

**Frontend:**
- Size: 1,024.60 KB (275.26 KB gzipped)
- Component: AlbumPhotoManager.vue
- Framework: Vue 3 with Composition API

---

**System Ready!** Start testing at: http://127.0.0.1:8000/login
**Date:** January 29, 2026
**Build:** Production ready
