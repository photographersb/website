<template>
  <div class="min-h-screen">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="Event Management" 
      subtitle="Manage all photography events on the platform"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">EVENT CONTROL</p>
          <h1 class="hero-title">Events pipeline, always visible.</h1>
          <p class="hero-subtitle">
            Track publishing, attendance, and registrations in one view.
          </p>
          <div class="hero-actions">
            <router-link
              to="/admin/events/create"
              class="btn-admin-primary"
            >
              Create Event
            </router-link>
            <button
              class="btn-admin-secondary"
              @click="fetchEvents()"
            >
              Refresh List
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Total Events</span>
            <span class="status-value">{{ stats.total || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Published</span>
            <span class="status-value">{{ stats.published || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Draft</span>
            <span class="status-value">{{ stats.draft || 0 }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Total RSVPs: {{ stats.total_rsvps || 0 }}
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="filters.status"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-burgundy focus:border-burgundy"
              @change="fetchEvents"
            >
              <option value="">
                All Status
              </option>
              <option value="draft">
                Draft
              </option>
              <option value="published">
                Published
              </option>
              <option value="cancelled">
                Cancelled
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
            <select
              v-model="filters.event_type"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-burgundy focus:border-burgundy"
              @change="fetchEvents"
            >
              <option value="">
                All Types
              </option>
              <option value="workshop">
                Workshop
              </option>
              <option value="seminar">
                Seminar
              </option>
              <option value="photowalk">
                Photowalk
              </option>
              <option value="expo">
                Expo
              </option>
              <option value="meetup">
                Meetup
              </option>
              <option value="webinar">
                Webinar
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
            <select
              v-model="filters.city_id"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-burgundy focus:border-burgundy"
              @change="fetchEvents"
            >
              <option value="">
                All Cities
              </option>
              <option
                v-for="city in cities"
                :key="city.id"
                :value="city.id"
              >
                {{ city.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search events..."
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-burgundy focus:border-burgundy"
              @input="debounceSearch"
            >
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-primary-100 text-primary-700 rounded-md p-3">
              <svg
                class="w-6 h-6 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">
                Total Events
              </p>
              <p class="text-2xl font-semibold text-gray-900">
                {{ stats.total || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-success-100 text-success-700 rounded-md p-3">
              <svg
                class="w-6 h-6 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">
                Published
              </p>
              <p class="text-2xl font-semibold text-gray-900">
                {{ stats.published || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-warning-100 text-warning-700 rounded-md p-3">
              <svg
                class="w-6 h-6 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">
                Draft
              </p>
              <p class="text-2xl font-semibold text-gray-900">
                {{ stats.draft || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-primary-100 text-primary-700 rounded-md p-3">
              <svg
                class="w-6 h-6 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">
                Total RSVPs
              </p>
              <p class="text-2xl font-semibold text-gray-900">
                {{ stats.total_rsvps || 0 }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Events Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Event
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Type
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Organizer
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                RSVPs
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td
                colspan="7"
                class="px-6 py-12 text-center text-gray-500"
              >
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-burgundy" />
                <p class="mt-2">
                  Loading events...
                </p>
              </td>
            </tr>
            <tr v-else-if="events.length === 0">
              <td
                colspan="7"
                class="px-12 py-16"
              >
                <div class="flex flex-col items-center justify-center">
                  <svg
                    class="w-16 h-16 text-gray-400 mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                  </svg>
                  <h3 class="text-lg font-semibold text-gray-900 mb-1">
                    No events yet
                  </h3>
                  <p class="text-gray-600 mb-6">
                    Get started by creating your first photography event
                  </p>
                  <router-link 
                    to="/admin/events/create"
                    class="inline-flex items-center px-4 py-2 bg-burgundy text-white rounded-md hover:bg-burgundy-dark transition-colors"
                  >
                    <svg
                      class="w-5 h-5 mr-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                      aria-hidden="true"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                      />
                    </svg>
                    Create Event
                  </router-link>
                </div>
              </td>
            </tr>
            <tr
              v-for="event in events"
              v-else
              :key="event.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12">
                    <img
                      v-if="event.hero_image_url"
                      :src="event.hero_image_url"
                      :alt="event.title"
                      class="h-12 w-12 rounded-lg object-cover"
                    >
                    <div
                      v-else
                      class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center"
                    >
                      <svg
                        class="w-6 h-6 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      <router-link
                        :to="`/admin/events/edit/${event.slug || event.id}`"
                        class="text-gray-900 hover:text-burgundy-dark"
                      >
                        {{ event.title }}
                      </router-link>
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ event.location }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="badge badge-primary">
                  {{ formatEventType(event.type || event.event_type) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ event.organizer?.user?.name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(event.event_date || event.start_datetime || event.start_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ event.rsvp_count || 0 }}
                <span v-if="event.max_attendees"> / {{ event.max_attendees }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(event.status)">
                  {{ event.status }}
                </span>
                <span
                  v-if="event.is_featured"
                  class="ml-1 text-primary-600"
                  title="Featured"
                >⭐</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-2">
                  <button
                    class="text-burgundy hover:text-burgundy-dark"
                    title="View"
                    aria-label="View event details"
                    @click="viewEvent(event)"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      aria-hidden="true"
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                      />
                    </svg>
                  </button>
                  <router-link
                    :to="`/admin/events/${event.id}/check-in`"
                    class="text-burgundy hover:text-burgundy-dark"
                    title="Check-in"
                    aria-label="Mark attendance for event"
                    aria-hidden="true"
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
                        d="M5 13l4 4L19 7"
                      />
                    </svg>
                    </router-link>
                    <router-link
                      :to="`/admin/events/edit/${event.slug || event.id}`"
                      class="text-burgundy hover:text-burgundy-dark"
                      title="Edit"
                      aria-label="Edit event details"
                      aria-hidden="true"
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
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                    </router-link>
                  <button
                    :class="event.is_featured ? 'text-primary-600 hover:text-primary-800' : 'text-gray-400 hover:text-gray-600'"
                    :title="event.is_featured ? 'Unfeature' : 'Feature'"
                    :aria-label="event.is_featured ? 'Remove from featured events' : 'Add to featured events'"
                    @click="toggleFeatured(event)"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      aria-hidden="true"
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                  </button>
                  <button
                    class="text-red-600 hover:text-red-900"
                    title="Delete"
                    aria-label="Delete event permanently"
                    aria-hidden="true"
                    @click="deleteEvent(event)"
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
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
              @click="previousPage"
            >
              Previous
            </button>
            <button
              :disabled="currentPage === lastPage"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
              @click="nextPage"
            >
              Next
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing page <span class="font-medium">{{ currentPage }}</span> of <span class="font-medium">{{ lastPage }}</span>
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <button
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                  @click="previousPage"
                >
                  Previous
                </button>
                <button
                  :disabled="currentPage === lastPage"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                  @click="nextPage"
                >
                  Next
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../api';
import AdminHeader from '../../../components/AdminHeader.vue';
import AdminQuickNav from '../../../components/AdminQuickNav.vue';

const events = ref([]);
const cities = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
const stats = ref({});

const filters = ref({
  status: '',
  event_type: '',
  city_id: '',
  search: ''
});

let searchTimeout = null;

const fetchCities = async () => {
  try {
    const { data } = await api.get('/locations?type=district');
    if (data.status === 'success') {
      cities.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching cities:', error);
  }
};

const fetchStats = async () => {
  try {
    // Get first page without filters to get an overview
    const { data } = await api.get('/admin/events?per_page=1');
    if (data.status === 'success' && data.meta) {
      // Calculate stats based on total from meta
      stats.value = {
        total: data.meta.total || 0,
        published: 0,  // Would need a dedicated endpoint to get accurate count
        draft: 0,      // Would need a dedicated endpoint to get accurate count
        total_rsvps: 0 // Would need a dedicated endpoint to get accurate count
      };
    }
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const fetchEvents = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', currentPage.value);
    
    if (filters.value.status) params.append('status', filters.value.status);
    if (filters.value.event_type) params.append('event_type', filters.value.event_type);
    if (filters.value.city_id) params.append('city_id', filters.value.city_id);
    if (filters.value.search) params.append('search', filters.value.search);

    const { data } = await api.get(`/admin/events?${params}`);

    if (data.status === 'success') {
      events.value = data.data;
      // Safely handle meta with fallbacks
      if (data.meta) {
        lastPage.value = data.meta.last_page || 1;
        currentPage.value = data.meta.current_page || 1;
      } else {
        console.warn('No meta data in response:', data);
        lastPage.value = 1;
        currentPage.value = 1;
      }
    } else {
      console.error('API returned non-success status:', data);
      alert('Failed to load events: ' + (data.message || 'Unknown error'));
    }
  } catch (error) {
    console.error('Error fetching events:', error);
    alert('Failed to load events: ' + (error.response?.data?.message || error.message || 'Unknown error'));
  } finally {
    loading.value = false;
  }
};

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchEvents();
  }, 500);
};

const viewEvent = (event) => {
  window.open(`/events/${event.slug}`, '_blank');
};

const toggleFeatured = async (event) => {
  try {
    const { data } = await api.post(`/admin/events/${event.id}/toggle-featured`);
    if (data.status === 'success') {
      event.is_featured = !event.is_featured;
    }
  } catch (error) {
    console.error('Error toggling featured:', error);
    alert('Failed to toggle featured status');
  }
};

const deleteEvent = async (event) => {
  if (!confirm(`Are you sure you want to delete "${event.title}"?`)) return;

  try {
    const { data } = await api.delete(`/admin/events/${event.id}`);
    if (data.status === 'success') {
      fetchEvents();
      fetchStats();
    }
  } catch (error) {
    console.error('Error deleting event:', error);
    alert(error.response?.data?.message || 'Failed to delete event');
  }
};

const formatDate = (date) => {
  if (!date) return '-';
  const d = new Date(date);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const year = d.getFullYear();
  return `${day}-${month}-${year}`;
};

const formatEventType = (type) => {
  if (!type) return 'Other';
  return type.charAt(0).toUpperCase() + type.slice(1);
};

const getStatusClass = (status) => {
  const classes = {
    draft: 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800',
    published: 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800',
    cancelled: 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800'
  };
  return classes[status] || classes.draft;
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    fetchEvents();
  }
};

const nextPage = () => {
  if (currentPage.value < lastPage.value) {
    currentPage.value++;
    fetchEvents();
  }
};

onMounted(() => {
  fetchCities();
  fetchStats();
  fetchEvents();
});
</script>

<style scoped>
.page-hero { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: 1.5rem; padding: 1.75rem 2rem; border-radius: 1.5rem; border: 1px solid rgba(142, 14, 63, 0.2); background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(247, 239, 233, 0.82)), linear-gradient(90deg, rgba(142, 14, 63, 0.06), transparent 45%, rgba(109, 72, 56, 0.08)); box-shadow: 0 25px 55px rgba(24, 12, 8, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.6); backdrop-filter: blur(6px); }
.hero-copy { display: flex; flex-direction: column; gap: 0.85rem; }
.hero-kicker { font-size: 0.7rem; letter-spacing: 0.28em; text-transform: uppercase; color: var(--admin-text-secondary); font-weight: 700; }
.hero-title { font-size: 2rem; line-height: 1.1; color: var(--admin-text-primary); text-shadow: 0 2px 14px rgba(142, 14, 63, 0.18); }
.hero-subtitle { color: var(--admin-text-secondary); max-width: 480px; }
.hero-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.hero-status { display: grid; gap: 0.8rem; }
.status-card { background: rgba(255, 255, 255, 0.85); border: 1px solid rgba(142, 14, 63, 0.2); border-radius: 1rem; padding: 1rem 1.25rem; box-shadow: 0 16px 35px rgba(22, 12, 8, 0.08); display: flex; flex-direction: column; gap: 0.35rem; }
.status-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.2em; color: var(--admin-text-secondary); }
.status-value { font-size: 1.1rem; font-weight: 700; color: var(--admin-text-primary); }
.page-topbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.9rem 1.25rem; background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1.1rem; box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08); backdrop-filter: blur(8px); }
.status-chip { background: rgba(142, 14, 63, 0.12); color: var(--admin-text-primary); padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
@media (max-width: 1024px) { .page-hero { grid-template-columns: 1fr; } }
</style>
