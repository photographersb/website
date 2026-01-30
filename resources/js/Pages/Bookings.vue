<template>
  <div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Page Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Bookings</h1>
        <p class="text-gray-600">View and manage your photography bookings</p>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-3">
          <button
            @click="filter = 'all'"
            :class="[
              'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
              filter === 'all'
                ? 'bg-burgundy text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            All Bookings
          </button>
          <button
            @click="filter = 'pending'"
            :class="[
              'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
              filter === 'pending'
                ? 'bg-burgundy text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Pending
          </button>
          <button
            @click="filter = 'confirmed'"
            :class="[
              'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
              filter === 'confirmed'
                ? 'bg-burgundy text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Confirmed
          </button>
          <button
            @click="filter = 'completed'"
            :class="[
              'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
              filter === 'completed'
                ? 'bg-burgundy text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Completed
          </button>
          <button
            @click="filter = 'cancelled'"
            :class="[
              'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
              filter === 'cancelled'
                ? 'bg-burgundy text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Cancelled
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy"></div>
      </div>

      <!-- Bookings List -->
      <div v-else-if="filteredBookings.length > 0" class="space-y-4">
        <div
          v-for="booking in filteredBookings"
          :key="booking.id"
          class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow"
        >
          <div class="flex flex-col md:flex-row justify-between gap-4">
            <!-- Booking Info -->
            <div class="flex-1">
              <div class="flex items-start justify-between mb-3">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ booking.photographer?.business_name || booking.photographer?.user?.name || 'Photographer' }}
                  </h3>
                  <p class="text-sm text-gray-600 mt-1">Booking #{{ booking.id }}</p>
                </div>
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    getStatusClass(booking.status)
                  ]"
                >
                  {{ capitalizeFirst(booking.status) }}
                </span>
              </div>

              <div class="space-y-2 text-sm text-gray-700">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span>{{ formatDate(booking.event_date) }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span>{{ booking.event_location }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ booking.event_duration }} hours</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="font-semibold text-burgundy">৳{{ Number(booking.total_price).toLocaleString() }}</span>
                </div>
              </div>

              <div v-if="booking.special_requirements" class="mt-3 p-3 bg-gray-50 rounded text-sm text-gray-700">
                <strong>Special Requirements:</strong> {{ booking.special_requirements }}
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2 md:min-w-[150px]">
              <button
                v-if="booking.status === 'completed' && !booking.review"
                @click="writeReview(booking)"
                class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors text-sm font-medium"
              >
                Write Review
              </button>
              <button
                v-if="booking.status === 'pending'"
                @click="cancelBooking(booking)"
                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
              >
                Cancel Booking
              </button>
              <button
                @click="viewDetails(booking)"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium"
              >
                View Details
              </button>
              <router-link
                :to="`/photographer/${booking.photographer?.slug || booking.photographer_id}`"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium text-center"
              >
                View Photographer
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
        <svg
          class="w-16 h-16 text-gray-400 mx-auto mb-4"
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
        <h3 class="text-lg font-medium text-gray-900 mb-1">No bookings found</h3>
        <p class="text-gray-600 mb-4">You haven't made any bookings yet</p>
        <router-link
          to="/"
          class="inline-block px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
        >
          Find Photographers
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';

const router = useRouter();
const bookings = ref([]);
const loading = ref(true);
const filter = ref('all');

onMounted(() => {
  loadBookings();
});

const loadBookings = async () => {
  loading.value = true;
  try {
    const response = await api.get('/bookings');
    bookings.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load bookings:', error);
    if (error.response?.status === 401) {
      router.push('/auth');
    }
  } finally {
    loading.value = false;
  }
};

const filteredBookings = computed(() => {
  if (filter.value === 'all') {
    return bookings.value;
  }
  return bookings.value.filter(b => b.status === filter.value);
});

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    rejected: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const capitalizeFirst = (str) => {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const writeReview = (booking) => {
  router.push(`/review/${booking.photographer_id}`);
};

const cancelBooking = async (booking) => {
  if (!confirm('Are you sure you want to cancel this booking?')) {
    return;
  }

  try {
    await api.patch(`/bookings/${booking.id}/cancel`);
    alert('Booking cancelled successfully');
    loadBookings();
  } catch (error) {
    console.error('Failed to cancel booking:', error);
    alert('Failed to cancel booking. Please try again.');
  }
};

const viewDetails = (booking) => {
  alert(`Booking Details:\n\nID: ${booking.id}\nPhotographer: ${booking.photographer?.business_name || 'N/A'}\nDate: ${formatDate(booking.event_date)}\nLocation: ${booking.event_location}\nPrice: ৳${Number(booking.total_price).toLocaleString()}\nStatus: ${capitalizeFirst(booking.status)}`);
};
</script>
