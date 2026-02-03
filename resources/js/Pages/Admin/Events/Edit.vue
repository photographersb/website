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
              <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image URL</label>
              <input
                v-model="form.banner_image"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="https://example.com/banner.jpg"
              />
              <p class="mt-1 text-sm text-gray-500">Optional banner image URL</p>
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
              <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
              <select
                v-model="form.city_id"
                :required="form.status !== 'draft'"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="">Select city</option>
                <option v-for="city in cities" :key="city.id" :value="city.id">
                  {{ city.name }}
                </option>
              </select>
              <router-link to="/admin/cities" class="mt-1 inline-block text-sm text-burgundy hover:text-burgundy-dark">
                Manage cities →
              </router-link>
              <p v-if="cities.length === 0" class="mt-1 text-sm text-warning-700">⚠️ No cities available. Please add cities from Admin → Locations first.</p>
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
                  {{ photographer.user?.name || photographer.business_name || `Photographer #${photographer.id}` }}
                </option>
              </select>
              <router-link to="/admin/photographers" class="mt-1 inline-block text-sm text-burgundy hover:text-burgundy-dark">
                Manage photographers →
              </router-link>
              <p class="mt-1 text-sm text-gray-500">Select the photographer organizing this event</p>
              <p v-if="errors.organizer_id" class="mt-1 text-sm text-red-600">{{ errors.organizer_id }}</p>
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
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

const router = useRouter();
const route = useRoute();
const processing = ref(false);
const loading = ref(true);
const cities = ref([]);
const photographers = ref([]);
const errors = ref({});

const form = ref({
  title: '',
  event_type: '',
  description: '',
  banner_image: '',
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

const formatDateForInput = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const fetchEvent = async () => {
  try {
    const eventId = route.params.id;
    const token = localStorage.getItem('auth_token');
    
    const response = await axios.get(`/api/v1/admin/events/${eventId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    
    const event = response.data.data || response.data;
    
    // Populate form with event data
    form.value = {
      title: event.title || '',
      event_type: event.event_type || '',
      description: event.description || '',
      banner_image: event.banner_image || '',
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

const fetchCities = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.warn('No auth token found in localStorage');
      return;
    }
    
    const response = await axios.get('/api/v1/admin/cities?minimal=1', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
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
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.warn('No auth token found in localStorage');
      return;
    }
    
    const response = await axios.get('/api/v1/admin/photographers?status=active&minimal=1', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
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
    const eventId = route.params.id;
    const token = localStorage.getItem('auth_token');
    
    const response = await axios.put(`/api/v1/admin/events/${eventId}`, form.value, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });
    
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
