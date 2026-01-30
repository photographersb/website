<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Event</h1>
            <p class="mt-1 text-sm text-gray-600">Update event details</p>
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

    <!-- Loading State -->
    <div v-if="loading" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center">
      <div class="text-gray-600">Loading event data...</div>
    </div>

    <!-- Form -->
    <div v-else class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                type="datetime-local"
                required
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
              <label class="block text-sm font-medium text-gray-700 mb-2">Location/Venue *</label>
              <input
                v-model="form.location"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="123 Main Street, Building Name"
              />
              <p v-if="errors.location" class="mt-1 text-sm text-red-600">{{ errors.location }}</p>
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
              <label class="block text-sm font-medium text-gray-700 mb-2">Organizer/Photographer ID *</label>
              <input
                v-model.number="form.organizer_id"
                type="number"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Photographer ID"
              />
              <p class="mt-1 text-sm text-gray-500">Enter the photographer ID who is organizing this event</p>
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
                class="h-4 w-4 text-burgundy-600 focus:ring-burgundy-500 border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                Featured Event (Show prominently on events page)
              </label>
            </div>

            <div class="flex items-center">
              <input
                v-model="form.is_verified"
                type="checkbox"
                class="h-4 w-4 text-burgundy-600 focus:ring-burgundy-500 border-gray-300 rounded"
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
            type="submit"
            :disabled="processing"
            class="px-6 py-3 bg-burgundy-600 text-white rounded-lg font-medium hover:bg-burgundy-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ processing ? 'Updating...' : 'Update Event' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const processing = ref(false);
const loading = ref(true);
const cities = ref([]);
const errors = ref({});

const form = ref({
  title: '',
  event_type: '',
  description: '',
  banner_image: '',
  event_date: '',
  duration_hours: null,
  city_id: '',
  location: '',
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
      location: event.location || '',
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
    const response = await axios.get('/api/v1/cities');
    cities.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching cities:', error);
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

onMounted(() => {
  fetchCities();
  fetchEvent();
});
</script>
