<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
      <h1 class="text-3xl font-bold mb-8">
        Photography Events
      </h1>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Location</label>
            <select
              v-model="filters.city"
              class="w-full border rounded px-3 py-2"
            >
              <option value="">
                All Locations
              </option>
              <option
                v-for="city in cities"
                :key="city.id"
                :value="city.slug"
              >
                {{ city.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">From Date</label>
            <input
              v-model="filters.from_date"
              type="date"
              class="w-full border rounded px-3 py-2"
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">To Date</label>
            <input
              v-model="filters.to_date"
              type="date"
              class="w-full border rounded px-3 py-2"
            >
          </div>
        </div>
        <button
          class="mt-4 bg-burgundy text-white px-6 py-2 rounded hover:bg-[#6F112D]"
          @click="fetchEvents"
        >
          Apply Filters
        </button>
      </div>

      <!-- Events List -->
      <div
        v-if="loading"
        class="text-center py-12"
      >
        <p class="text-gray-600">
          Loading events...
        </p>
      </div>

      <div
        v-else-if="events.length === 0"
        class="text-center py-12"
      >
        <p class="text-gray-600">
          No events found
        </p>
      </div>

      <div
        v-else
        class="space-y-6"
      >
        <div
          v-for="event in events"
          :key="event.id"
          class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition cursor-pointer"
          @click="viewEvent(event)"
        >
          <div class="md:flex">
            <!-- Image -->
            <div class="md:w-1/3">
              <img
                :src="event.hero_image_url || event.banner_image || '/images/placeholder.svg'"
                :alt="event.title"
                class="w-full h-48 object-cover"
              >
            </div>

            <!-- Content -->
            <div class="p-6 md:w-2/3">
              <div class="flex justify-between items-start mb-2">
                <h2 class="text-2xl font-bold">
                  {{ event.title }}
                </h2>
                <span
                  v-if="event.is_featured"
                  class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm"
                >
                  ⭐ Featured
                </span>
              </div>

              <p class="text-gray-600 mb-3">
                {{ event.description }}
              </p>

              <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                <div>
                  <p class="text-gray-600">
                    Date
                  </p>
                  <p class="font-semibold">
                    {{ formatDate(event.event_date) }}
                  </p>
                </div>
                <div>
                  <p class="text-gray-600">
                    Location
                  </p>
                  <p class="font-semibold">
                    {{ event.location }}
                  </p>
                </div>
                <div>
                  <p class="text-gray-600">
                    Organizer
                  </p>
                  <p class="font-semibold">
                    {{ event.organizer?.user?.name || 'Unknown' }}
                  </p>
                </div>
                <div v-if="event.is_ticketed">
                  <p class="text-gray-600">
                    Ticket Price
                  </p>
                  <p class="font-semibold">
                    ৳{{ event.ticket_price }}
                  </p>
                </div>
              </div>

              <div class="flex gap-3">
                <button
                  class="flex-1 bg-burgundy text-white py-2 rounded hover:bg-[#6F112D]"
                  @click.stop="viewEvent(event)"
                >
                  View Details
                </button>
                <button
                  :class="`flex-1 py-2 rounded ${
                    isRsvped(event)
                      ? 'bg-green-600 text-white hover:bg-green-700'
                      : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                  }`"
                  @click.stop="toggleRsvp(event)"
                >
                  {{ isRsvped(event) ? '✓ Going' : 'RSVP' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center gap-2 mt-12">
        <button
          v-if="currentPage > 1"
          class="px-4 py-2 border rounded hover:bg-gray-100"
          @click="previousPage"
        >
          Previous
        </button>
        <span class="px-4 py-2">Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          v-if="currentPage < totalPages"
          class="px-4 py-2 border rounded hover:bg-gray-100"
          @click="nextPage"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { formatDate as formatDateValue } from '../utils/formatters';

const router = useRouter();
const events = ref([]);
const cities = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const userRsvps = ref([]);

const filters = ref({
  city: '',
  from_date: '',
  to_date: '',
});

const fetchCities = async () => {
  try {
    const { data } = await api.get('/locations');
    if (data.status === 'success') {
      const locations = data.data || [];
      cities.value = locations.filter(location => location.type !== 'division');
    }
  } catch (error) {
    console.error('Error fetching locations:', error);
  }
};

const fetchEvents = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', currentPage.value);
    
    if (filters.value.city) params.append('city', filters.value.city);
    if (filters.value.from_date) params.append('from_date', filters.value.from_date);
    if (filters.value.to_date) params.append('to_date', filters.value.to_date);

    const { data } = await api.get(`/events?${params}`);

    if (data.status === 'success') {
      events.value = data.data;
      totalPages.value = data.meta?.last_page || 1;
      currentPage.value = data.meta?.current_page || 1;
    }
  } catch (error) {
    console.error('Error fetching events:', error);
    events.value = [];
  } finally {
    loading.value = false;
  }
};

const viewEvent = (event) => {
  router.push(`/events/${event.slug}`);
};

const toggleRsvp = async (event) => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    router.push('/auth');
    return;
  }

  try {
    const rsvpStatus = isRsvped(event) ? 'not_going' : 'going';
    const { data } = await api.post(`/events/${event.slug}/rsvp`);

    if (data.status === 'success') {
      if (rsvpStatus === 'going') {
        userRsvps.value.push(event.id);
      } else {
        userRsvps.value = userRsvps.value.filter(id => id !== event.id);
      }
    }
  } catch (error) {
    console.error('Error updating RSVP:', error);
  }
};

const isRsvped = (event) => {
  return userRsvps.value.includes(event.id);
};

const formatDate = (date) => {
  return formatDateValue(date);
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    fetchEvents();
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    fetchEvents();
  }
};

onMounted(() => {
  fetchCities();
  fetchEvents();
});
</script>
