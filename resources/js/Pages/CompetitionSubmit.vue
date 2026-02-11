<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
      <!-- Header -->
      <div class="mb-8">
        <router-link
          :to="`/competitions/${competition?.slug}`"
          class="text-red-600 hover:text-red-700 inline-flex items-center gap-2 mb-4"
        >
          <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          Back to Competition
        </router-link>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Submit Your Photo
        </h1>
        <p class="text-gray-600">
          {{ competition?.title }}
        </p>
      </div>

      <!-- Submission Info Card -->
      <div
        v-if="competition"
        class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8"
      >
        <div class="flex items-start gap-3">
          <svg
            class="w-6 h-6 text-blue-600 flex-shrink-0 mt-1"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
              clip-rule="evenodd"
            />
          </svg>
          <div class="flex-1">
            <h3 class="font-semibold text-gray-900 mb-2">
              Submission Guidelines
            </h3>
            <ul class="text-sm text-gray-700 space-y-1">
              <li>• Maximum file size: 10MB (larger images are auto-compressed)</li>
              <li>• Accepted formats: JPEG, JPG, PNG</li>
              <li>• Minimum resolution: 1920px (longest side)</li>
              <li>• Maximum {{ competition.max_submissions_per_user }} {{ competition.max_submissions_per_user === 1 ? 'submission' : 'submissions' }} allowed</li>
              <li>• Submissions will be reviewed before appearing in the gallery</li>
              <li>• Deadline: {{ formatDate(competition.submission_deadline) }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Submission Form -->
      <div class="bg-white rounded-lg shadow-md p-8">
        <form
          class="space-y-6"
          @submit.prevent="submitForm"
        >
          <!-- Image Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Photo <span class="text-red-500">*</span>
            </label>
            <div
              class="upload-drop p-8 text-center transition-colors"
              :class="{ 'border-red-400 bg-red-50': dragOver }"
              @dragover.prevent="dragOver = true"
              @dragleave.prevent="dragOver = false"
              @drop.prevent="handleDrop"
            >
              <!-- Preview -->
              <div
                v-if="imagePreview"
                class="mb-4"
              >
                <img
                  :src="imagePreview"
                  alt="Preview"
                  class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg"
                >
                <button
                  type="button"
                  class="mt-4 text-red-600 hover:text-red-700 text-sm font-medium"
                  @click="clearImage"
                >
                  Remove Image
                </button>
              </div>

              <!-- Upload UI -->
              <div v-else>
                <svg
                  class="mx-auto h-16 w-16 text-gray-400 mb-4"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 48 48"
                >
                  <path
                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                <div class="text-gray-600 mb-2">
                  <label
                    for="image-upload"
                    class="cursor-pointer text-red-600 hover:text-red-700 font-medium"
                  >
                    Click to upload
                  </label>
                  or drag and drop
                </div>
                <p class="upload-hint">
                  JPEG, JPG, PNG, WEBP up to 10MB · 1920x1080 px minimum
                </p>
                <input
                  id="image-upload"
                  type="file"
                  accept="image/jpeg,image/jpg,image/png,image/webp"
                  class="hidden"
                  @change="handleImageUpload"
                >
              </div>
            </div>
            <p
              v-if="errors.image"
              class="text-red-500 text-sm mt-1"
            >
              {{ errors.image }}
            </p>
          </div>

          <!-- Pexels Section -->
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">
              Load your image from Pexels
            </h3>
            <p class="text-xs text-gray-600 mb-3">
              Paste a Pexels photo page link or a direct image link.
            </p>
            <p class="text-[11px] text-gray-500 mb-3">
              Example: https://images.pexels.com/photos/123456/pexels-photo-123456.jpeg
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
              <input
                v-model="pexelsQuery"
                type="text"
                placeholder="Paste Pexels photo or image link"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              >
              <button
                type="button"
                class="px-4 py-2 bg-gray-900 text-white rounded-lg font-semibold hover:bg-black transition-colors"
                @click="importPexelsImage"
              >
                Import Image
              </button>
            </div>
          </div>

          <!-- Title -->
          <div>
            <label
              for="title"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Title <span class="text-red-500">*</span>
            </label>
            <input 
              id="title"
              v-model="form.title" 
              type="text"
              placeholder="Enter a compelling title for your photo"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              maxlength="255"
              required
            >
            <p class="text-gray-500 text-sm mt-1">
              {{ form.title.length }}/255
            </p>
            <p
              v-if="errors.title"
              class="text-red-500 text-sm mt-1"
            >
              {{ errors.title }}
            </p>
          </div>

          <!-- Description -->
          <div>
            <label
              for="description"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Description/Story
            </label>
            <textarea 
              id="description"
              v-model="form.description"
              rows="4"
              placeholder="Tell the story behind your photo (optional)"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              maxlength="1000"
            />
            <p class="text-gray-500 text-sm mt-1">
              {{ form.description.length }}/1000
            </p>
            <p
              v-if="errors.description"
              class="text-red-500 text-sm mt-1"
            >
              {{ errors.description }}
            </p>
          </div>

          <!-- Location & Date Taken -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="location"
                class="block text-sm font-medium text-gray-700 mb-2"
              >
                Location
              </label>
              <input 
                id="location"
                v-model="form.location" 
                type="text"
                placeholder="Where was this photo taken?"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              >
            </div>

            <div>
              <label
                for="date_taken"
                class="block text-sm font-medium text-gray-700 mb-2"
              >
                Date Taken
              </label>
              <input 
                id="date_taken"
                v-model="form.date_taken" 
                type="date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              >
            </div>
          </div>

          <!-- Camera Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label
                for="camera_make"
                class="block text-sm font-medium text-gray-700 mb-2"
              >
                Camera Make
              </label>
              <input 
                id="camera_make"
                v-model="form.camera_make" 
                type="text"
                placeholder="e.g., Canon, Nikon, Sony"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              >
            </div>

            <div>
              <label
                for="camera_model"
                class="block text-sm font-medium text-gray-700 mb-2"
              >
                Camera Model
              </label>
              <input 
                id="camera_model"
                v-model="form.camera_model" 
                type="text"
                placeholder="e.g., EOS 5D Mark IV"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              >
            </div>
          </div>

          <!-- Camera Settings -->
          <div>
            <label
              for="camera_settings"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Camera Settings
            </label>
            <input 
              id="camera_settings"
              v-model="form.camera_settings" 
              type="text"
              placeholder="e.g., f/2.8, 1/500s, ISO 400"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
            <p class="text-xs text-gray-500 mt-1">
              EXIF auto-fill works only when the image includes metadata.
            </p>
          </div>

          <!-- Hashtags -->
          <div>
            <label
              for="hashtags"
              class="block text-sm font-medium text-gray-700 mb-2"
            >
              Hashtags
            </label>
            <input 
              id="hashtags"
              v-model="form.hashtags" 
              type="text"
              placeholder="#photography #portrait #nature"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
          </div>

          <!-- Watermark Checkbox -->
          <div class="flex items-start gap-3">
            <input 
              id="is_watermarked"
              v-model="form.is_watermarked" 
              type="checkbox"
              class="mt-1 w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
            >
            <label
              for="is_watermarked"
              class="text-sm text-gray-700"
            >
              This photo contains a watermark
            </label>
          </div>

          <!-- Terms Checkbox - REQUIRED -->
          <div class="bg-red-50 border-2 border-red-300 rounded-lg p-4">
            <div class="flex items-start gap-3">
              <input 
                id="terms"
                v-model="agreeToTerms" 
                type="checkbox"
                class="mt-1 w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-2 focus:ring-red-500 cursor-pointer flex-shrink-0"
                required
              >
              <div class="flex-1">
                <label
                  for="terms"
                  class="text-sm font-medium text-gray-900 cursor-pointer block"
                >
                  I confirm that this is my original work and I have the rights to submit it. I agree to the competition rules and terms. <span class="text-red-600 font-bold">*</span>
                </label>
                <p class="text-xs text-gray-600 mt-1">
                  This checkbox must be checked before you can submit your photo.
                </p>
              </div>
            </div>
            <p
              v-if="errors.agree_to_terms"
              class="text-red-600 text-sm mt-3 font-semibold"
            >
              ⚠️ {{ errors.agree_to_terms }}
            </p>
          </div>

          <!-- Submit Buttons -->
          <div class="flex gap-4 pt-4">
            <button
              type="submit"
              :disabled="submitting"
              class="flex-1 bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="submitting">Submitting...</span>
              <span v-else>Submit Photo</span>
            </button>
            <router-link
              :to="`/competitions/${competition?.slug}`"
              class="px-8 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-colors"
            >
              Cancel
            </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import exifr from 'exifr';
import { validateUploadFile } from '../utils/imageValidation';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import { formatDateTime as formatDateTimeValue } from '../utils/formatters';

const route = useRoute();
const router = useRouter();

const competition = ref(null);
const loading = ref(true);
const submitting = ref(false);
const dragOver = ref(false);
const imagePreview = ref(null);
const imageFile = ref(null);
const agreeToTerms = ref(false);
const pexelsQuery = ref('');

const form = ref({
  title: '',
  description: '',
  location: '',
  date_taken: '',
  camera_make: '',
  camera_model: '',
  camera_settings: '',
  hashtags: '',
  is_watermarked: false
});

const errors = ref({});
const MAX_UPLOAD_BYTES = 10 * 1024 * 1024;
const MIN_IMAGE_WIDTH = 1920;
const MIN_IMAGE_HEIGHT = 1080;
const MAX_COMPRESS_DIMENSION = 4000;

onMounted(async () => {
  await fetchCompetition();
});

const fetchCompetition = async () => {
  try {
    const { data } = await api.get(`/competitions/${route.params.slug}`);
    competition.value = data.data;
    
    // Check if competition is active
    if (competition.value.status !== 'active') {
      alert('This competition is not currently active.');
      router.push(`/competitions/${competition.value.slug}`);
      return;
    }
    
    // Check submission deadline
    const now = new Date();
    const submissionDeadline = new Date(competition.value.submission_deadline);
    
    if (now > submissionDeadline) {
      alert('Submission deadline has passed.');
      router.push(`/competitions/${competition.value.slug}`);
      return;
    }
  } catch (error) {
    console.error('Error fetching competition:', error);
    alert('Competition not found');
    router.push('/competitions');
  } finally {
    loading.value = false;
  }
};

const handleImageUpload = async (event) => {
  const file = event.target.files[0];
  if (file) {
    await processImage(file);
  }
};

const handleDrop = async (event) => {
  dragOver.value = false;
  const file = event.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) {
    await processImage(file);
  }
};

const processImage = async (file) => {
  await populateExifFields(file);

  let fileToUse = file;
  if (file.size > MAX_UPLOAD_BYTES && file.type.startsWith('image/')) {
    const compressed = await compressImage(file, {
      maxBytes: MAX_UPLOAD_BYTES,
      minWidth: MIN_IMAGE_WIDTH,
      minHeight: MIN_IMAGE_HEIGHT,
      maxDimension: MAX_COMPRESS_DIMENSION
    });
    if (compressed) {
      fileToUse = compressed;
    }
  }

  const validation = await validateUploadFile(fileToUse, {
    label: 'Photo',
    maxBytes: MAX_UPLOAD_BYTES,
    allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'],
    minImageWidth: MIN_IMAGE_WIDTH,
    minImageHeight: MIN_IMAGE_HEIGHT
  });

  if (!validation.ok) {
    errors.value.image = validation.message;
    return;
  }
  
  errors.value.image = null;
  imageFile.value = fileToUse;
  
  // Create preview
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target.result;
  };
  reader.readAsDataURL(fileToUse);
};

const clearImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
  errors.value.image = null;
};

const compressImage = async (file, options) => {
  const { maxBytes, minWidth, minHeight, maxDimension } = options;

  const image = await loadImage(file);
  const originalWidth = image.width;
  const originalHeight = image.height;

  let scale = 1;
  const maxSide = Math.max(originalWidth, originalHeight);
  if (maxSide > maxDimension) {
    scale = maxDimension / maxSide;
  }

  const scaledWidth = Math.round(originalWidth * scale);
  const scaledHeight = Math.round(originalHeight * scale);
  if (scaledWidth < minWidth || scaledHeight < minHeight) {
    scale = 1;
  }

  const canvas = document.createElement('canvas');
  canvas.width = Math.round(originalWidth * scale);
  canvas.height = Math.round(originalHeight * scale);

  const ctx = canvas.getContext('2d');
  ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

  let quality = 0.9;
  let blob = await canvasToBlob(canvas, quality);

  while (blob && blob.size > maxBytes && quality > 0.5) {
    quality -= 0.1;
    blob = await canvasToBlob(canvas, quality);
  }

  if (!blob || blob.size > maxBytes) {
    return null;
  }

  const extension = 'jpg';
  return new File([blob], `upload.${extension}`, { type: 'image/jpeg' });
};

const loadImage = (file) => new Promise((resolve, reject) => {
  const img = new Image();
  const url = URL.createObjectURL(file);
  img.onload = () => {
    URL.revokeObjectURL(url);
    resolve(img);
  };
  img.onerror = () => {
    URL.revokeObjectURL(url);
    reject(new Error('Invalid image'));
  };
  img.src = url;
});

const canvasToBlob = (canvas, quality) => new Promise((resolve) => {
  canvas.toBlob((blob) => resolve(blob), 'image/jpeg', quality);
});

const populateExifFields = async (file) => {
  try {
    const exif = await exifr.parse(file, {
      pick: ['Make', 'Model', 'DateTimeOriginal', 'ISO', 'FNumber', 'ExposureTime']
    });

    if (!exif) return;

    if (!form.value.camera_make && exif.Make) {
      form.value.camera_make = String(exif.Make);
    }
    if (!form.value.camera_model && exif.Model) {
      form.value.camera_model = String(exif.Model);
    }
    if (!form.value.date_taken && exif.DateTimeOriginal instanceof Date) {
      form.value.date_taken = formatDateInput(exif.DateTimeOriginal);
    }

    if (!form.value.camera_settings) {
      const parts = [];
      if (exif.FNumber) parts.push(`f/${Number(exif.FNumber).toFixed(1)}`);
      if (exif.ExposureTime) parts.push(`1/${Math.round(1 / exif.ExposureTime)}s`);
      if (exif.ISO) parts.push(`ISO ${exif.ISO}`);
      form.value.camera_settings = parts.join(', ');
    }
  } catch (error) {
    // Ignore EXIF parsing errors
  }
};

const formatDateInput = (date) => {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const importPexelsImage = async () => {
  const query = pexelsQuery.value.trim();
  if (!query) {
    errors.value.image = 'Paste a Pexels photo or image link to import.';
    return;
  }
  if (!/^https?:\/\//i.test(query)) {
    errors.value.image = 'Paste a valid Pexels link.';
    return;
  }

  errors.value.image = null;

  try {
    const response = await api.post(`/pexels/import?url=${encodeURIComponent(query)}`, null, { responseType: 'blob' });
    const blob = response.data;
    if (!blob || !blob.type || !blob.type.startsWith('image/')) {
      throw new Error('Not an image');
    }
    const extension = blob.type.split('/')[1] || 'jpg';
    const file = new File([blob], `pexels-image.${extension}`, { type: blob.type || 'image/jpeg' });
    await processImage(file);
  } catch (error) {
    errors.value.image = 'Unable to import this image. Please paste a valid Pexels link.';
  }
};

const submitForm = async () => {
  if (!imageFile.value) {
    errors.value.image = 'Please select an image';
    return;
  }
  
  if (!agreeToTerms.value) {
    errors.value.agree_to_terms = 'You must agree to the terms and conditions by checking the box above';
    alert('⚠️ Please check the "I agree to the terms" box to continue');
    return;
  }
  
  submitting.value = true;
  errors.value = {};
  
  try {
    const formData = new FormData();
    formData.append('image', imageFile.value);
    formData.append('title', form.value.title);
    formData.append('description', form.value.description);
    formData.append('location', form.value.location);
    formData.append('date_taken', form.value.date_taken);
    formData.append('camera_make', form.value.camera_make);
    formData.append('camera_model', form.value.camera_model);
    formData.append('camera_settings', form.value.camera_settings);
    formData.append('hashtags', form.value.hashtags);
    formData.append('is_watermarked', form.value.is_watermarked ? 1 : 0);
    formData.append('agree_to_terms', agreeToTerms.value ? 1 : 0);
    
    const { data } = await api.post(`/competitions/${competition.value.id}/submissions`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    alert(data.message || 'Submission uploaded successfully!');
    router.push(`/competitions/${competition.value.slug}`);
  } catch (error) {
    console.error('Error submitting:', error);
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
      // Check specifically for terms acceptance errors
      if (errors.value.agree_to_terms) {
        alert('⚠️ TERMS ACCEPTANCE REQUIRED\n\nPlease check the box above that says "I confirm that this is my original work..." to accept the competition terms and conditions before submitting.');
      }
      console.error('Validation errors:', errors.value);
    } else if (error.response?.data?.message) {
      alert('❌ Error: ' + error.response.data.message);
    } else {
      alert('❌ Failed to submit. Please try again.');
    }
  } finally {
    submitting.value = false;
  }
};

const formatDate = (date) => {
  return formatDateTimeValue(date);
};
</script>
