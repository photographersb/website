# Quick Start Guide - New Features

## 1. Album Creation with Validation

### API Endpoint
```
POST /api/v1/photographer/albums
Authorization: Bearer {token}
```

### Request Body
```json
{
    "name": "Wedding Portfolio",
    "description": "My best wedding photography",
    "category_id": 1,
    "is_public": true
}
```

### Response (Success)
```json
{
    "status": "success",
    "data": {
        "id": 123,
        "name": "Wedding Portfolio",
        ...
    }
}
```

### Response (Validation Error)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": ["Please enter an album title."]
    }
}
```

---

## 2. Package with Sample Images

### API Endpoint
```
POST /api/v1/photographer/packages
Authorization: Bearer {token}
```

### Option A: Single Pexels URL (Now Supported!)
```json
{
    "name": "Wedding Basic",
    "category": "Wedding",
    "price": 15000,
    "duration_unit": "event",
    "duration_value": 1,
    "delivery_days": 7,
    "sample_images": "https://images.pexels.com/photos/123456/photo.jpg"
}
```

### Option B: Multiple URLs
```json
{
    "sample_images": [
        "https://images.pexels.com/photos/1.jpg",
        "https://images.pexels.com/photos/2.jpg",
        "https://images.pexels.com/photos/3.jpg"
    ]
}
```

### Option C: File Uploads
```javascript
// FormData approach
const formData = new FormData();
formData.append('name', 'Wedding Package');
formData.append('uploaded_images[]', file1);
formData.append('uploaded_images[]', file2);
```

---

## 3. Photographer Awards Management

### List All Awards
```
GET /api/v1/photographer/awards
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "title": "Best Wedding Photographer 2024",
            "organization": "Bangladesh Photography Association",
            "year": 2024,
            "type": "award",
            "description": "Won 1st place in wedding category",
            "certificate_url": "/storage/certificates/cert.jpg",
            "display_order": 1
        }
    ]
}
```

### Create Award
```
POST /api/v1/photographer/awards
Authorization: Bearer {token}
```

**Request (JSON):**
```json
{
    "title": "Best Portrait Photographer",
    "organization": "Dhaka Photo Society",
    "year": 2024,
    "type": "award",
    "description": "Awarded for excellence in portrait photography",
    "certificate_url": "https://example.com/certificate.jpg"
}
```

**Request (File Upload - FormData):**
```javascript
const formData = new FormData();
formData.append('title', 'Best Portrait Photographer');
formData.append('organization', 'Dhaka Photo Society');
formData.append('year', 2024);
formData.append('type', 'award');
formData.append('certificate_file', certificateFile); // JPG, PNG, or PDF
```

### Update Award
```
PUT /api/v1/photographer/awards/{id}
Authorization: Bearer {token}
```

### Delete Award
```
DELETE /api/v1/photographer/awards/{id}
Authorization: Bearer {token}
```

### Reorder Awards (Drag & Drop)
```
POST /api/v1/photographer/awards/reorder
Authorization: Bearer {token}

{
    "awards": [
        { "id": 1, "display_order": 0 },
        { "id": 3, "display_order": 1 },
        { "id": 2, "display_order": 2 }
    ]
}
```

### Public View (No Auth Required)
```
GET /api/v1/photographers/{photographerId}/awards
```

---

## 4. Photographer Profile - City, Categories, Hashtags

### Get Available Options

**Cities:**
```
GET /api/v1/cities
```

**Categories:**
```
GET /api/v1/categories
```

**Hashtags:**
```
GET /api/v1/hashtags
```

### Update Profile
```
PATCH /api/v1/photographer/profile
Authorization: Bearer {token}

{
    "city_id": 1,
    "specializations": ["wedding", "portrait", "event"],
    "favorite_hashtags": ["#wedding", "#portrait", "#photography"],
    "categories": [1, 3, 5] // Category IDs for many-to-many
}
```

### Database Relationships Already Working
```php
// In Photographer model
$photographer->city;           // City model
$photographer->categories;     // Collection of Category
$photographer->hashtags;       // Collection of Hashtag
$photographer->specializations; // Array
```

---

## 5. Competition Photo Submission with Error Handling

### Submit Photo
```
POST /api/v1/competitions/{competition}/submissions
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**FormData:**
```javascript
const formData = new FormData();
formData.append('title', 'Sunset over Dhaka');
formData.append('description', 'Beautiful sunset captured from rooftop');
formData.append('image', imageFile); // JPEG, PNG, or WebP
formData.append('location', 'Dhaka, Bangladesh');
formData.append('date_taken', '2024-12-15');
formData.append('camera_make', 'Canon');
formData.append('camera_model', 'EOS R5');
```

### Validation Requirements
- **Format:** JPEG, JPG, PNG, or WebP
- **Max Size:** 10MB
- **Min Dimensions:** 1920x1080 pixels
- **Title:** Required, 3-255 characters

### Success Response
```json
{
    "status": "success",
    "message": "Submission uploaded successfully! It will be reviewed before appearing in the gallery.",
    "data": {
        "id": 456,
        "title": "Sunset over Dhaka",
        "image_url": "/storage/competitions/1/submissions/image.jpg",
        "thumbnail_url": "/storage/competitions/1/submissions/thumb.jpg",
        "status": "pending_review"
    }
}
```

### Error Responses

**Validation Error (422):**
```json
{
    "status": "error",
    "message": "Image dimensions must be at least 1920x1080 pixels."
}
```

**Processing Error (500):**
```json
{
    "status": "error",
    "message": "Failed to process your image. Please ensure it is a valid image file.",
    "details": "Try reducing the file size or using a different format (JPEG or PNG)."
}
```

**No Image Processing Available:**
```json
{
    "status": "error",
    "message": "Image processing is not available on this server. Please contact support or upload smaller images."
}
```

---

## Frontend Implementation Examples

### Vue 3 - Album Creation
```vue
<template>
  <form @submit.prevent="createAlbum">
    <input v-model="form.name" placeholder="Album Title" />
    <span v-if="errors.name" class="text-red-500">{{ errors.name[0] }}</span>
    
    <textarea v-model="form.description" placeholder="Description"></textarea>
    
    <button type="submit">Create Album</button>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const form = ref({ name: '', description: '' });
const errors = ref({});

const createAlbum = async () => {
  try {
    errors.value = {};
    const response = await axios.post('/photographer/albums', form.value);
    alert('Album created!');
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    }
  }
};
</script>
```

### Vue 3 - Awards Management
```vue
<template>
  <div>
    <h2>My Awards & Achievements</h2>
    
    <!-- List Awards -->
    <draggable v-model="awards" @end="reorderAwards">
      <div v-for="award in awards" :key="award.id" class="award-card">
        <h3>{{ award.title }}</h3>
        <p>{{ award.organization }} - {{ award.year }}</p>
        <span :class="`badge-${award.type}`">{{ award.type }}</span>
        
        <button @click="editAward(award)">Edit</button>
        <button @click="deleteAward(award.id)">Delete</button>
      </div>
    </draggable>
    
    <!-- Add Award Button -->
    <button @click="showAddModal = true">+ Add Award</button>
    
    <!-- Add/Edit Modal -->
    <modal v-if="showAddModal" @close="showAddModal = false">
      <form @submit.prevent="saveAward">
        <input v-model="awardForm.title" placeholder="Award Title" required />
        <input v-model="awardForm.organization" placeholder="Organization" />
        <input v-model="awardForm.year" type="number" min="1950" :max="currentYear" required />
        
        <select v-model="awardForm.type" required>
          <option value="award">Award</option>
          <option value="achievement">Achievement</option>
          <option value="recognition">Recognition</option>
          <option value="certification">Certification</option>
        </select>
        
        <textarea v-model="awardForm.description" placeholder="Description"></textarea>
        
        <input type="file" @change="handleCertificateUpload" accept=".jpg,.jpeg,.png,.pdf" />
        
        <button type="submit">Save Award</button>
      </form>
    </modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';

const awards = ref([]);
const showAddModal = ref(false);
const awardForm = ref({
  title: '',
  organization: '',
  year: new Date().getFullYear(),
  type: 'award',
  description: ''
});
const certificateFile = ref(null);

const fetchAwards = async () => {
  const response = await axios.get('/photographer/awards');
  awards.value = response.data.data;
};

const saveAward = async () => {
  const formData = new FormData();
  Object.keys(awardForm.value).forEach(key => {
    formData.append(key, awardForm.value[key]);
  });
  
  if (certificateFile.value) {
    formData.append('certificate_file', certificateFile.value);
  }
  
  if (awardForm.value.id) {
    // Update
    await axios.put(`/photographer/awards/${awardForm.value.id}`, formData);
  } else {
    // Create
    await axios.post('/photographer/awards', formData);
  }
  
  showAddModal.value = false;
  fetchAwards();
};

const deleteAward = async (id) => {
  if (confirm('Delete this award?')) {
    await axios.delete(`/photographer/awards/${id}`);
    fetchAwards();
  }
};

const reorderAwards = async () => {
  const orderedAwards = awards.value.map((award, index) => ({
    id: award.id,
    display_order: index
  }));
  
  await axios.post('/photographer/awards/reorder', { awards: orderedAwards });
};

const handleCertificateUpload = (event) => {
  certificateFile.value = event.target.files[0];
};

onMounted(fetchAwards);
</script>
```

### Vue 3 - Competition Submission
```vue
<template>
  <form @submit.prevent="submitPhoto">
    <input v-model="form.title" placeholder="Photo Title" required />
    <textarea v-model="form.description" placeholder="Description"></textarea>
    
    <input 
      type="file" 
      @change="handleImageSelect" 
      accept="image/jpeg,image/jpg,image/png,image/webp"
      required 
    />
    
    <div v-if="imagePreview">
      <img :src="imagePreview" alt="Preview" style="max-width: 300px;" />
    </div>
    
    <input v-model="form.location" placeholder="Location" />
    <input v-model="form.camera_make" placeholder="Camera Make" />
    <input v-model="form.camera_model" placeholder="Camera Model" />
    
    <button type="submit" :disabled="uploading">
      {{ uploading ? 'Uploading...' : 'Submit Photo' }}
    </button>
    
    <div v-if="error" class="error">
      <p>{{ error.message }}</p>
      <p v-if="error.details" class="text-sm">{{ error.details }}</p>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps(['competitionId']);

const form = ref({
  title: '',
  description: '',
  location: '',
  camera_make: '',
  camera_model: ''
});

const imageFile = ref(null);
const imagePreview = ref(null);
const uploading = ref(false);
const error = ref(null);

const handleImageSelect = (event) => {
  const file = event.target.files[0];
  if (!file) return;
  
  // Validate file size (10MB)
  if (file.size > 10 * 1024 * 1024) {
    error.value = { message: 'Image size must not exceed 10MB.' };
    return;
  }
  
  imageFile.value = file;
  
  // Create preview
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target.result;
  };
  reader.readAsDataURL(file);
};

const submitPhoto = async () => {
  if (!imageFile.value) {
    error.value = { message: 'Please select an image' };
    return;
  }
  
  uploading.value = true;
  error.value = null;
  
  const formData = new FormData();
  Object.keys(form.value).forEach(key => {
    if (form.value[key]) {
      formData.append(key, form.value[key]);
    }
  });
  formData.append('image', imageFile.value);
  
  try {
    const response = await axios.post(
      `/competitions/${props.competitionId}/submissions`,
      formData,
      {
        headers: { 'Content-Type': 'multipart/form-data' }
      }
    );
    
    alert('Photo submitted successfully!');
    // Reset form or redirect
    
  } catch (err) {
    if (err.response?.status === 422) {
      error.value = { message: err.response.data.message };
    } else if (err.response?.status === 500) {
      error.value = {
        message: err.response.data.message,
        details: err.response.data.details
      };
    } else {
      error.value = { message: 'Upload failed. Please try again.' };
    }
  } finally {
    uploading.value = false;
  }
};
</script>
```

---

## Testing Commands

### Test Album Validation
```bash
# Should fail - empty name
curl -X POST http://127.0.0.1:8000/api/v1/photographer/albums \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":""}'

# Should succeed
curl -X POST http://127.0.0.1:8000/api/v1/photographer/albums \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Album","description":"Testing"}'
```

### Test Package with Single URL
```bash
curl -X POST http://127.0.0.1:8000/api/v1/photographer/packages \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Wedding Basic",
    "category":"Wedding",
    "price":15000,
    "duration_unit":"event",
    "duration_value":1,
    "delivery_days":7,
    "sample_images":"https://images.pexels.com/photos/1444442/pexels-photo-1444442.jpeg"
  }'
```

### Test Awards
```bash
# List awards
curl http://127.0.0.1:8000/api/v1/photographer/awards \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create award
curl -X POST http://127.0.0.1:8000/api/v1/photographer/awards \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title":"Best Photographer 2024",
    "organization":"Bangladesh Photo Society",
    "year":2024,
    "type":"award",
    "description":"Awarded for excellence"
  }'
```

---

## Common Issues & Solutions

### Issue: "Photographer profile not found"
**Solution:** Ensure user has `role='photographer'` and related photographer record exists.

### Issue: Package validation fails with single URL
**Solution:** Now fixed! Single URLs are automatically converted to arrays.

### Issue: "Image processing is not available"
**Solution:** Enable GD or ImageMagick PHP extension. Image will still upload but won't be resized.

### Issue: Certificate upload not working
**Solution:** Use `FormData` with `certificate_file` field, not JSON.

### Issue: Awards not showing on public profile
**Solution:** Check the public endpoint: `/api/v1/photographers/{id}/awards`

---

## Support

For issues or questions:
1. Check `storage/logs/laravel.log` for detailed errors
2. Verify authentication token is valid
3. Ensure required fields are provided
4. Check HTTP status codes for error types:
   - 422: Validation errors
   - 403: Authorization errors
   - 404: Resource not found
   - 500: Server errors (check logs)

---

**Last Updated:** January 2025  
**Version:** 1.0  
**Status:** Production Ready
