<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Event</h1>
            <p class="mt-1 text-sm text-gray-600">Set up a new photography event</p>
          </div>
          <router-link
            to="/admin/events"
            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
          </router-link>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
              <input
                v-model="form.title"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Photography Workshop 2026"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
              <select
                v-model="form.event_type"
                required
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
              <p class="mt-1 text-sm text-gray-500">Optional hero banner image URL</p>
              <p v-if="errors.hero_image_url" class="mt-1 text-sm text-red-600">{{ errors.hero_image_url[0] }}</p>
            </div>
          </div>
        </div>

        <!-- Date, Time & Location -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Date, Time & Location</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Start Date *</label>
              <input
                v-model="form.event_date"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.event_date" class="mt-1 text-sm text-red-600">{{ errors.event_date[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event End Date</label>
              <input
                v-model="form.event_end_date"
                type="datetime-local"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p class="mt-1 text-sm text-gray-500">Leave blank for single-day event</p>
              <p v-if="errors.event_end_date" class="mt-1 text-sm text-red-600">{{ errors.event_end_date[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
              <input
                v-model="form.start_time"
                type="time"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.start_time" class="mt-1 text-sm text-red-600">{{ errors.start_time[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
              <input
                v-model="form.end_time"
                type="time"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.end_time" class="mt-1 text-sm text-red-600">{{ errors.end_time[0] }}</p>
            </div>

            <div class="flex items-center md:col-span-2">
              <input
                v-model="form.all_day_event"
                type="checkbox"
                class="h-4 w-4 text-burgundy-600 focus:ring-burgundy-500 border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                All Day Event
              </label>
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
              <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
              <select
                v-model="form.city_id"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select city</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name }}
                </option>
              </select>
              <p v-if="errors.city_id" class="mt-1 text-sm text-red-600">{{ errors.city_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name *</label>
              <input
                v-model="form.location"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Community Hall, Art Gallery, etc."
              />
              <p v-if="errors.location" class="mt-1 text-sm text-red-600">{{ errors.location[0] }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Full Address</label>
              <input
                v-model="form.address"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="123 Main Street, Building Name, Floor 2"
              />
              <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address[0] }}</p>
            </div>
          </div>
        </div>

        <!-- Attendance & Pricing -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Attendance & Pricing</h2>

          <div class="space-y-4">
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
                <p v-if="errors.max_attendees" class="mt-1 text-sm text-red-600">{{ errors.max_attendees[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Price (৳)</label>
                <input
                  v-model.number="form.ticket_price"
                  type="number"
                  min="0"
                  step="100"
                  :disabled="!form.is_ticketed"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent disabled:bg-gray-100"
                  placeholder="500"
                />
                <p class="mt-1 text-sm text-gray-500">Set to 0 for free ticketed events</p>
                <p v-if="errors.ticket_price" class="mt-1 text-sm text-red-600">{{ errors.ticket_price[0] }}</p>
              </div>
            </div>

            <div class="flex flex-col gap-2">
              <div class="flex items-center">
                <input
                  v-model="form.require_registration"
                  type="checkbox"
                  class="h-4 w-4 text-burgundy-600 focus:ring-burgundy-500 border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-900">
                  Require Registration (Users must register to attend)
                </label>
              </div>

              <div class="flex items-center">
                <input
                  v-model="form.is_ticketed"
                  type="checkbox"
                  class="h-4 w-4 text-burgundy-600 focus:ring-burgundy-500 border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-900">
                  Ticketed Event (Requires paid tickets)
                </label>
              </div>
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
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select photographer</option>
                <option v-for="photographer in photographers" :key="photographer.id" :value="photographer.id">
                  {{ photographer.user?.name || photographer.business_name || `Photographer #${photographer.id}` }}
                </option>
              </select>
              <p class="mt-1 text-sm text-gray-500">Select the photographer organizing this event</p>
              <p v-if="errors.organizer_id" class="mt-1 text-sm text-red-600">{{ errors.organizer_id[0] }}</p>
            </div>
          </div>
        </div>

        <!-- Status & Settings -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Status & Settings</h2>

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
                class="h-4 w-4 text-burgundy-600 focus:ring-burgundy-500 border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                Featured Event (Show prominently on events page)
              </label>
            </div>

            <div v-if="form.is_featured">
              <label class="block text-sm font-medium text-gray-700 mb-2">Featured Until</label>
              <input
                v-model="form.featured_until"
                type="datetime-local"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p class="mt-1 text-sm text-gray-500">Leave blank to feature indefinitely</p>
              <p v-if="errors.featured_until" class="mt-1 text-sm text-red-600">{{ errors.featured_until[0] }}</p>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4">
          <router-link
            to="/admin/events"
            class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 hover:border-burgundy-600 hover:text-burgundy-600 transition-all shadow-sm"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="processing"
            class="inline-flex items-center justify-center px-6 py-3 bg-burgundy-600 text-white rounded-lg font-semibold hover:bg-burgundy-700 hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
          >
            <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ processing ? 'Creating...' : 'Create Event' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" :class="['toast', toastType]">{{ toastMessage }}</div>
  </div>
</template>

<style scoped>
.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  animation: slideIn 0.3s ease-out;
  z-index: 1000;
  font-weight: 600;
}

.toast.success {
  background: #10b981;
  color: white;
}

.toast.error {
  background: #ef4444;
  color: white;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const processing = ref(false);
const cities = ref([]);
const errors = ref({});

const form = ref({
  title: '',
  event_type: '',
  description: '',
  hero_image_url: '',
  event_date: '',
  event_end_date: '',
  start_time: '',
  end_time: '',
  all_day_event: false,
  duration_hours: null,
  city_id: '',
  location: '',
  address: '',
  latitude: null,
  longitude: null,
  max_attendees: null,
  require_registration: true,
  is_ticketed: false,
  ticket_price: 0,
  requirements: '',
  organizer_id: '',
  status: 'published',
  is_featured: false,
  featured_until: '',
});

const photographers = ref([]);
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const fetchCities = async () => {
  try {
    const response = await axios.get('/api/v1/cities');
    cities.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching cities:', error);
    showToastMessage('Failed to load cities', 'error');
  }
};

const fetchPhotographers = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    const response = await axios.get('/api/v1/admin/photographers?status=active', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    photographers.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching photographers:', error);
    showToastMessage('Failed to load photographers', 'error');
  }
};

const submitForm = async () => {
  processing.value = true;
  errors.value = {};

  try {
    const token = localStorage.getItem('auth_token');
    
    // Prepare form data
    const formData = {
      ...form.value,
      organizer_id: parseInt(form.value.organizer_id),
      city_id: parseInt(form.value.city_id),
      is_featured: form.value.is_featured ? 1 : 0,
      require_registration: form.value.require_registration ? 1 : 0,
      is_ticketed: form.value.is_ticketed ? 1 : 0,
      all_day_event: form.value.all_day_event ? 1 : 0
    };

    const response = await axios.post('/api/v1/admin/events', formData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });
    
    if (response.data.status === 'success') {
      showToastMessage('Event created successfully!', 'success');
      setTimeout(() => {
        router.push('/admin/events');
      }, 1500);
    }
  } catch (error) {
    console.error('Error creating event:', error);
    
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    }
    showToastMessage(error.response?.data?.message || 'Failed to create event', 'error');
  } finally {
    processing.value = false;
  }
};

const showToastMessage = (message, type = 'success') => {
  toastMessage.value = message;
  toastType.value = type;
  showToast.value = true;
  setTimeout(() => {
    showToast.value = false;
  }, 3000);
};

onMounted(() => {
  fetchCities();
  fetchPhotographers();
});
</script>
