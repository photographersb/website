<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="Edit Event" 
      subtitle="Update event details"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

    <!-- Loading State -->
    <div v-if="loading" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center">
      <div class="text-gray-600">Loading event data...</div>
    </div>

    <!-- Form -->
    <div v-else class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="submitForm" class="space-y-6" novalidate>
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
              <input
                v-model="form.title"
                type="text"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
              <select
                v-model="form.event_type"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select event type</option>
                <option value="workshop">Workshop</option>
                <option value="exhibition">Exhibition</option>
                <option value="meetup">Meetup</option>
                <option value="competition">Competition</option>
                <option value="seminar">Seminar</option>
                <option value="other">Other</option>
              </select>
              <p v-if="errors.event_type" class="mt-1 text-sm text-red-600">{{ errors.event_type }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Detailed description of the event..."
              ></textarea>
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Hero Image URL</label>
              <input
                v-model="form.hero_image_url"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="https://example.com/hero-image.jpg"
              />
              <input
                type="file"
                accept="image/*"
                class="upload-input mt-2 block text-sm"
                @change="handleImageUpload('hero_image_url', $event)"
              />
              <div class="mt-2 flex flex-wrap gap-2">
                <button
                  type="button"
                  class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                  @click="openPexelsPicker('hero_image_url', 1600, 900)"
                >
                  Choose from Pexels
                </button>
              </div>
              <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 1600x900 px.</p>
              <p
                v-if="uploadingImages.hero_image_url"
                class="mt-1 text-xs text-gray-500"
              >
                Uploading...
              </p>
              <p
                v-if="form.hero_image_credit_name"
                class="mt-1 text-xs text-gray-500"
              >
                Pexels credit:
                <a
                  :href="form.hero_image_credit_url || 'https://www.pexels.com'"
                  target="_blank"
                  rel="noopener"
                  class="font-semibold text-burgundy underline"
                >
                  {{ form.hero_image_credit_name }}
                </a>
              </p>
              <p class="mt-1 text-sm text-gray-500">Optional hero banner image URL</p>
              <p v-if="errors.hero_image_url" class="mt-1 text-sm text-red-600">{{ errors.hero_image_url }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image URL</label>
              <input
                v-model="form.banner_image"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="https://example.com/banner.jpg"
              />
              <p class="mt-1 text-sm text-gray-500">Optional banner image URL</p>
              <input
                type="file"
                accept="image/*"
                class="upload-input mt-2 block text-sm"
                @change="handleImageUpload('banner_image', $event)"
              />
              <div class="mt-2 flex flex-wrap gap-2">
                <button
                  type="button"
                  class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                  @click="openPexelsPicker('banner_image', 1920, 600)"
                >
                  Choose from Pexels
                </button>
              </div>
              <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 1920x600 px.</p>
              <p
                v-if="uploadingImages.banner_image"
                class="mt-1 text-xs text-gray-500"
              >
                Uploading...
              </p>
              <p
                v-if="form.banner_image_credit_name"
                class="mt-1 text-xs text-gray-500"
              >
                Pexels credit:
                <a
                  :href="form.banner_image_credit_url || 'https://www.pexels.com'"
                  target="_blank"
                  rel="noopener"
                  class="font-semibold text-burgundy underline"
                >
                  {{ form.banner_image_credit_name }}
                </a>
              </p>
              <p v-if="errors.banner_image" class="mt-1 text-sm text-red-600">{{ errors.banner_image }}</p>
            </div>
          </div>
        </div>

        <!-- Date, Time & Location -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Date, Time & Location</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
              <input
                v-model="form.event_date"
                type="date"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.event_date" class="mt-1 text-sm text-red-600">{{ errors.event_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Duration (hours)</label>
              <input
                v-model.number="form.duration_hours"
                type="number"
                min="0.5"
                step="0.5"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="2.5"
              />
              <p class="mt-1 text-sm text-gray-500">Duration in hours (e.g., 2.5 for 2.5 hours)</p>
              <p v-if="errors.duration_hours" class="mt-1 text-sm text-red-600">{{ errors.duration_hours }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
              <select
                v-model="form.city_id"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select location</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name }}
                </option>
              </select>
              <p v-if="cities.length === 0" class="mt-1 text-sm text-warning-700">⚠️ No locations available. Please add locations from Admin → Locations first.</p>
              <p v-if="errors.city_id" class="mt-1 text-sm text-red-600">{{ errors.city_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name *</label>
              <input
                v-model="form.venue_name"
                type="text"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="e.g., ICCB, Hotel InterContinental, Dhaka Club"
              />
              <p v-if="errors.venue_name" class="mt-1 text-sm text-red-600">{{ errors.venue_name }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Venue Full Address *</label>
              <textarea
                v-model="form.venue_address"
                rows="2"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="e.g., 123 Main Street, Building Name, Floor 2, Near Landmark"
              ></textarea>
              <p v-if="errors.venue_address" class="mt-1 text-sm text-red-600">{{ errors.venue_address }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Location Display Name *</label>
              <input
                v-model="form.location"
                type="text"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Optional: Short location label for listings"
              />
              <p class="mt-1 text-sm text-gray-500">Short name for event listings (e.g., "Gulshan 2"). Required when publishing.</p>
              <p v-if="errors.location" class="mt-1 text-sm text-red-600">{{ errors.location }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Additional Address Notes</label>
              <input
                v-model="form.address"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Optional: Additional directions or notes"
              />
              <p class="mt-1 text-sm text-gray-500">Optional additional address information</p>
              <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
            </div>
          </div>
        </div>

        <!-- Attendance & Pricing -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Attendance & Pricing</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Max Attendees</label>
              <input
                v-model.number="form.max_attendees"
                type="number"
                min="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="50"
              />
              <p class="mt-1 text-sm text-gray-500">Leave blank for unlimited</p>
              <p v-if="errors.max_attendees" class="mt-1 text-sm text-red-600">{{ errors.max_attendees }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Price (৳)</label>
              <input
                v-model.number="form.ticket_price"
                type="number"
                min="0"
                step="100"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="500"
              />
              <p class="mt-1 text-sm text-gray-500">Set to 0 for free events</p>
              <p v-if="errors.ticket_price" class="mt-1 text-sm text-red-600">{{ errors.ticket_price }}</p>
            </div>
          </div>
        </div>

        <!-- Requirements -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Requirements & Details</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
              <textarea
                v-model="form.requirements"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="What attendees need to bring or know (e.g., 'Bring your own camera')"
              ></textarea>
              <p v-if="errors.requirements" class="mt-1 text-sm text-red-600">{{ errors.requirements }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Organizer/Photographer *</label>
              <select
                v-model="form.organizer_id"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select photographer</option>
                <option v-for="photographer in photographers" :key="photographer.id" :value="photographer.id">
                  {{ photographer.user?.name || `Photographer #${photographer.id}` }}
                </option>
              </select>
              <p class="mt-1 text-sm text-gray-500">Select the photographer organizing this event</p>
              <p v-if="errors.organizer_id" class="mt-1 text-sm text-red-600">{{ errors.organizer_id }}</p>
            </div>
          </div>
        </div>

        <!-- Status & Settings -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="text-xl font-bold text-gray-900">Status & Settings</h2>
            <span
              v-if="eventState"
              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
              :class="eventState.tone"
            >
              {{ eventState.label }}
            </span>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
              <select
                v-model="form.status"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="draft">Draft (Not visible to public)</option>
                <option value="published">Published (Visible to public)</option>
                <option value="cancelled">Cancelled</option>
              </select>
              <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
            </div>

            <div class="flex items-center">
              <input
                v-model="form.is_featured"
                type="checkbox"
                class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                Featured Event (Show prominently on events page)
              </label>
            </div>

            <div class="flex items-center">
              <input
                v-model="form.is_verified"
                type="checkbox"
                class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                Verified Event (Official/trusted event)
              </label>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4">
          <router-link
            to="/admin/events"
            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-all"
          >
            Cancel
          </router-link>
          <button
            type="button"
            @click="saveDraft"
            :disabled="processing"
            class="px-6 py-3 bg-gray-100 text-gray-800 rounded-lg font-medium hover:bg-gray-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Save Draft
          </button>
          <button
            type="submit"
            :disabled="processing"
            class="px-6 py-3 bg-burgundy text-white rounded-lg font-medium hover:bg-burgundy-dark transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ processing ? 'Updating...' : 'Update Event' }}
          </button>
        </div>
      </form>
    </div>

    <PexelsPickerModal
      :visible="pexelsPickerOpen"
      :target-width="pexelsTarget.width"
      :target-height="pexelsTarget.height"
      @close="closePexelsPicker"
      @select="handlePexelsSelect"
    />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../../api';
import { validateUploadFile } from '../../../utils/imageValidation'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import PexelsPickerModal from '../../../components/PexelsPickerModal.vue'
const router = useRouter();
const processing = ref(false);
const loading = ref(true);
const cities = ref([]);
const photographers = ref([]);
const errors = ref({});
const allowPrefill = ref(false);
const eventTiming = ref({
  start_datetime: null,
  end_datetime: null,
  event_date: null,
  duration_hours: null,
});
const uploadingImages = ref({
  hero_image_url: false,
  banner_image: false,
});

const pexelsPickerOpen = ref(false);
const pexelsTarget = ref({
  field: 'hero_image_url',
  width: 1600,
  height: 900,
});

const form = ref({
  title: '',
  event_type: '',
  description: '',
  hero_image_url: '',
  hero_image_credit_name: '',
  hero_image_credit_url: '',
  banner_image: '',
  banner_image_credit_name: '',
  banner_image_credit_url: '',
  event_date: '',
  duration_hours: null,
  city_id: '',
  venue_name: '',
  venue_address: '',
  location: '',
  address: '',
  max_attendees: null,
  ticket_price: 0,
  requirements: '',
  organizer_id: null,
  status: 'published',
  is_featured: false,
  is_verified: false,
});

const eventState = computed(() => {
  if (!form.value.status) {
    return null;
  }

  if (form.value.status === 'cancelled') {
    return { label: 'Cancelled', tone: 'bg-red-100 text-red-800' };
  }

  const timing = eventTiming.value || {};
  const now = new Date();
  let start = timing.start_datetime ? new Date(timing.start_datetime) : null;
  let end = timing.end_datetime ? new Date(timing.end_datetime) : null;

  if (!start && timing.event_date) {
    start = new Date(timing.event_date);
    if (timing.duration_hours) {
      end = new Date(start.getTime() + Number(timing.duration_hours) * 60 * 60 * 1000);
    } else {
      end = new Date(start);
      end.setHours(23, 59, 59, 999);
    }
  }

  if (!start) {
    return { label: 'Date not set', tone: 'bg-gray-100 text-gray-700' };
  }

  if (end && now > end) {
    return { label: 'Ended', tone: 'bg-gray-100 text-gray-700' };
  }

  if (now < start) {
    return { label: 'Upcoming', tone: 'bg-blue-100 text-blue-800' };
  }

  return { label: 'Ongoing', tone: 'bg-green-100 text-green-800' };
});

const EVENT_TYPE_PRESETS = {
  workshop: {
    description: 'Hands-on training focused on techniques, lighting, and workflow. Includes guided practice and live critique sessions.',
    requirements: 'Bring a camera with a charged battery, at least one lens, and a memory card. A tripod is recommended.',
  },
  photowalk: {
    description: 'A guided outdoor session covering composition, street storytelling, and light hunting across key locations.',
    requirements: 'Comfortable walking shoes, a camera or phone with a full charge, and weather-appropriate clothing.',
  },
  expo: {
    description: 'A showcase of photography brands, gear demos, and creative showcases with networking opportunities.',
    requirements: 'Carry a valid ID for entry and prepare any business cards or portfolios for networking.',
  },
  exhibition: {
    description: 'Curated photography exhibition featuring thematic galleries, artist talks, and community engagement.',
    requirements: 'No special equipment required. Photography inside the venue may be restricted by organizers.',
  },
  seminar: {
    description: 'Expert-led talks on industry trends, business strategy, and creative growth for photographers.',
    requirements: 'Bring a notebook or device for notes. Arrive 15 minutes early for seating.',
  },
  meetup: {
    description: 'Community gathering for photographers to connect, collaborate, and share experiences.',
    requirements: 'No equipment required. Optional: bring a portfolio or recent work to share.',
  },
  webinar: {
    description: 'Online session covering photography techniques, post-processing, or business insights.',
    requirements: 'Stable internet connection, headphones, and a quiet space for participation.',
  },
  competition: {
    description: 'Photography competition with submission guidelines, judging criteria, and award announcements.',
    requirements: 'Prepare your submissions in the required format and adhere to the deadline and theme.',
  },
  other: {
    description: 'Special event tailored for photographers with unique experiences and learning opportunities.',
    requirements: 'Follow the organizer instructions shared in the event announcement.',
  },
};

const applyEventTypePreset = (eventType) => {
  const preset = EVENT_TYPE_PRESETS[eventType];
  if (!preset) return;
  form.value.description = preset.description;
  form.value.requirements = preset.requirements;
};

const formatDateForInput = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

const getEventIdFromPath = () => {
  const segments = window.location.pathname.split('/').filter(Boolean);
  const editIndex = segments.indexOf('edit');
  if (editIndex !== -1 && segments[editIndex + 1]) {
    return segments[editIndex + 1];
  }
  return segments[segments.length - 1];
};

const fetchEvent = async () => {
  try {
    const eventId = getEventIdFromPath();
    const response = await api.get(`/admin/events/${eventId}`);
    
    const event = response.data.data || response.data;
    
    // Populate form with event data
    form.value = {
      title: event.title || '',
      event_type: event.event_type || '',
      description: event.description || '',
      hero_image_url: event.hero_image_url || '',
      hero_image_credit_name: event.hero_image_credit_name || '',
      hero_image_credit_url: event.hero_image_credit_url || '',
      banner_image: event.banner_image || '',
      banner_image_credit_name: event.banner_image_credit_name || '',
      banner_image_credit_url: event.banner_image_credit_url || '',
      event_date: formatDateForInput(event.event_date),
      duration_hours: event.duration_hours || null,
      city_id: event.city_id || '',
      venue_name: event.venue_name || '',
      venue_address: event.venue_address || '',
      location: event.location || '',
      address: event.address || '',
      max_attendees: event.max_attendees || null,
      ticket_price: event.ticket_price || 0,
      requirements: event.requirements || '',
      organizer_id: event.organizer_id || null,
      status: event.status || 'published',
      is_featured: event.is_featured || false,
      is_verified: event.is_verified || false,
    };
    eventTiming.value = {
      start_datetime: event.start_datetime || null,
      end_datetime: event.end_datetime || null,
      event_date: event.event_date || null,
      duration_hours: event.duration_hours || null,
    };
    allowPrefill.value = true;
  } catch (error) {
    console.error('Error fetching event:', error);
    
    if (error.response?.status === 404) {
      alert('Event not found. It may have been deleted.');
    } else {
      alert(error.response?.data?.message || 'Failed to load event data');
    }
    
    router.push('/admin/events');
  } finally {
    loading.value = false;
  }
};

watch(
  () => form.value.event_type,
  (value, oldValue) => {
    if (!allowPrefill.value || !value || value === oldValue) return;
    applyEventTypePreset(value);
  }
);

const fetchCities = async () => {
  try {
    const response = await api.get('/locations', {
      params: {
        type: 'district'
      }
    });
    
    console.log('Cities API Response:', response.data);
    
    if (response.data.status === 'success' && Array.isArray(response.data.data)) {
      cities.value = response.data.data;
      console.log(`Loaded ${cities.value.length} cities`);
    } else {
      console.warn('Unexpected response structure:', response.data);
      cities.value = [];
    }
  } catch (error) {
    console.error('Error fetching cities:', error);
    cities.value = [];
  }
};

const fetchPhotographers = async () => {
  try {
    const response = await api.get('/admin/photographers', {
      params: {
        status: 'active',
        minimal: 1
      }
    });
    
    console.log('Photographers API Response:', response.data);
    
    if (response.data.status === 'success' && Array.isArray(response.data.data)) {
      photographers.value = response.data.data;
      console.log(`Loaded ${photographers.value.length} photographers`);
    } else {
      console.warn('Unexpected response structure:', response.data);
      photographers.value = [];
    }
  } catch (error) {
    console.error('Error fetching photographers:', error);
    photographers.value = [];
  }
};

const submitForm = async () => {
  processing.value = true;
  errors.value = {};

  try {
    const eventId = getEventIdFromPath();
    const response = await api.put(`/admin/events/${eventId}`, form.value);
    
    if (response.data.status === 'success') {
      alert('Event updated successfully!');
      router.push('/admin/events');
    }
  } catch (error) {
    console.error('Error updating event:', error);
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    } else {
      alert(error.response?.data?.message || 'Failed to update event');
    }
  } finally {
    processing.value = false;
  }
};

const handleImageUpload = async (field, event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  if (field === 'hero_image_url') {
    form.value.hero_image_credit_name = '';
    form.value.hero_image_credit_url = '';
  }
  if (field === 'banner_image') {
    form.value.banner_image_credit_name = '';
    form.value.banner_image_credit_url = '';
  }

  const rules = {
    hero_image_url: { width: 1600, height: 900 },
    banner_image: { width: 1920, height: 600 }
  };
  const rule = rules[field] || {};
  const validation = await validateUploadFile(file, {
    label: 'Image',
    maxBytes: 5 * 1024 * 1024,
    allowedTypes: ['image/jpeg', 'image/png'],
    imageWidth: rule.width,
    imageHeight: rule.height
  });

  if (!validation.ok) {
    errors.value[field] = validation.message;
    event.target.value = '';
    return;
  }

  uploadingImages.value[field] = true;
  errors.value[field] = '';

  try {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('folder', 'events');

    const response = await api.post('/admin/media/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data?.status === 'success' && response.data.data?.url) {
      form.value[field] = response.data.data.url;
    } else {
      errors.value[field] = response.data?.message || 'Image upload failed.';
    }
  } catch (error) {
    errors.value[field] = error.response?.data?.message || 'Image upload failed.';
  } finally {
    uploadingImages.value[field] = false;
    event.target.value = '';
  }
};

const openPexelsPicker = (field, width, height) => {
  pexelsTarget.value = { field, width, height };
  pexelsPickerOpen.value = true;
};

const closePexelsPicker = () => {
  pexelsPickerOpen.value = false;
};

const applyPexelsCredit = (field, credit) => {
  if (field === 'hero_image_url') {
    form.value.hero_image_credit_name = credit?.name || '';
    form.value.hero_image_credit_url = credit?.url || '';
  }
  if (field === 'banner_image') {
    form.value.banner_image_credit_name = credit?.name || '';
    form.value.banner_image_credit_url = credit?.url || '';
  }
};

const handlePexelsSelect = async ({ file, credit }) => {
  const field = pexelsTarget.value.field;
  uploadingImages.value[field] = true;
  errors.value[field] = '';
  try {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('folder', 'events');

    const response = await api.post('/admin/media/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    if (response.data?.status === 'success' && response.data.data?.url) {
      form.value[field] = response.data.data.url;
      applyPexelsCredit(field, credit);
    } else {
      errors.value[field] = response.data?.message || 'Image upload failed.';
    }
  } catch (error) {
    errors.value[field] = error.response?.data?.message || 'Image upload failed.';
  } finally {
    uploadingImages.value[field] = false;
    closePexelsPicker();
  }
};

const saveDraft = async () => {
  form.value.status = 'draft';
  await submitForm();
};

onMounted(() => {
  fetchCities();
  fetchPhotographers();
  fetchEvent();
});
</script>
