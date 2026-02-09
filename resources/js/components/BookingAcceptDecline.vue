<template>
  <div class="space-y-4">
    <!-- Pending Booking Cards -->
    <div
      v-if="pendingBookings.length > 0"
      class="space-y-4"
    >
      <h3 class="text-lg font-bold text-gray-900 mb-4">
        Pending Booking Requests
      </h3>
      
      <div 
        v-for="booking in pendingBookings" 
        :key="booking.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition"
      >
        <div class="p-6 sm:p-8">
          <!-- Booking Info -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Client Info -->
            <div>
              <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">
                Client
              </h4>
              <div class="flex items-center gap-3">
                <img 
                  :src="booking.client.profile_photo_url || defaultAvatar" 
                  :alt="booking.client.name"
                  class="w-12 h-12 rounded-full object-cover"
                >
                <div>
                  <p class="font-semibold text-gray-900">
                    {{ booking.client.name }}
                  </p>
                  <p class="text-sm text-gray-600">
                    {{ booking.client.email }}
                  </p>
                  <p class="text-sm text-gray-600">
                    {{ booking.client.phone }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Event Details -->
            <div>
              <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">
                Event
              </h4>
              <p class="font-semibold text-gray-900">
                {{ booking.event_type }}
              </p>
              <p class="text-sm text-gray-600 mt-1">
                <span class="font-medium">{{ formatDate(booking.event_date) }}</span>
              </p>
              <p class="text-sm text-gray-600">
                <span class="font-medium">{{ booking.event_duration }}</span> hours
              </p>
              <p class="text-sm text-gray-600 mt-1">
                📍 {{ booking.location }}
              </p>
            </div>

            <!-- Price & Package -->
            <div>
              <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">
                Package & Price
              </h4>
              <p class="font-semibold text-gray-900">
                {{ booking.package_name }}
              </p>
              <p class="text-2xl font-bold text-burgundy mt-2">
                {{ formatCurrency(booking.total_amount) }}
              </p>
              <p class="text-xs text-gray-600 mt-1">
                Total amount due
              </p>
            </div>
          </div>

          <!-- Booking Requirements/Notes -->
          <div
            v-if="booking.requirements"
            class="mb-6 p-4 bg-gray-50 rounded-lg"
          >
            <h4 class="font-semibold text-gray-900 mb-2">
              Client Requirements
            </h4>
            <p class="text-gray-700 text-sm">
              {{ booking.requirements }}
            </p>
          </div>

          <!-- Status Timeline -->
          <div class="mb-6 pb-6 border-b">
            <h4 class="font-semibold text-gray-900 mb-3">
              Booking Timeline
            </h4>
            <div class="space-y-2 text-sm">
              <div class="flex items-center gap-2">
                <span class="text-gray-500">📅 Requested:</span>
                <span class="text-gray-700">{{ formatDateTime(booking.created_at) }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-500">⏰ Expires in:</span>
                <span :class="['text-sm font-medium', getExpirationClass(booking.expires_at)]">
                  {{ getTimeRemaining(booking.expires_at) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3">
            <!-- Accept Button -->
            <button
              :disabled="processingId === booking.id || isExpired(booking.expires_at)"
              class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition font-medium flex items-center justify-center gap-2"
              @click="showAcceptConfirm(booking)"
            >
              <svg
                v-if="processingId !== booking.id"
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
              <span v-if="processingId === booking.id">Processing...</span>
              <span v-else>Accept Booking</span>
            </button>

            <!-- Decline Button -->
            <button
              :disabled="processingId === booking.id"
              class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition font-medium flex items-center justify-center gap-2"
              @click="showDeclineConfirm(booking)"
            >
              <svg
                v-if="processingId !== booking.id"
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
              <span v-if="processingId === booking.id">Processing...</span>
              <span v-else>Decline</span>
            </button>

            <!-- Message Button -->
            <router-link
              :to="{ name: 'booking-messages', params: { bookingId: booking.id } }"
              class="flex-1 px-6 py-3 border-2 border-burgundy text-burgundy rounded-lg hover:bg-burgundy-light transition font-medium flex items-center justify-center gap-2"
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
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
              Message
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="text-center py-12 bg-white rounded-lg"
    >
      <svg
        class="w-16 h-16 text-gray-300 mx-auto mb-4"
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
      <h3 class="text-lg font-semibold text-gray-900 mb-2">
        No Pending Bookings
      </h3>
      <p class="text-gray-600">
        All booking requests have been responded to. You're all set!
      </p>
    </div>

    <!-- Confirmation Modal - Accept -->
    <BookingActionConfirmation 
      v-if="confirmingBooking && confirmAction === 'accept'"
      :booking="confirmingBooking"
      action="accept"
      @confirm="confirmAcceptBooking"
      @cancel="confirmingBooking = null"
    />

    <!-- Confirmation Modal - Decline -->
    <BookingActionConfirmation 
      v-if="confirmingBooking && confirmAction === 'decline'"
      :booking="confirmingBooking"
      action="decline"
      @confirm="confirmDeclineBooking"
      @cancel="confirmingBooking = null"
    />

    <!-- Toast Notification -->
    <div 
      v-if="toastMessage"
      :class="[
        'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transition z-50',
        toastType === 'success' ? 'bg-green-600' : 'bg-red-600'
      ]"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';
import BookingActionConfirmation from './BookingActionConfirmation.vue';
import {
  formatDate as formatDateValue,
  formatDateTime as formatDateTimeValue
} from '../../utils/formatters';

const router = useRouter();

const bookings = ref([]);
const loading = ref(true);
const processingId = ref(null);
const confirmingBooking = ref(null);
const confirmAction = ref(null);
const toastMessage = ref('');
const toastType = ref('success');

const defaultAvatar = 'https://ui-avatars.com/api/?name=User';

const pendingBookings = computed(() => {
  return bookings.value.filter(b => b.status === 'pending');
});

const loadBookings = async () => {
  loading.value = true;
  try {
    const { data } = await api.get('/photographer/bookings?status=pending');
    if (data.status === 'success') {
      bookings.value = data.data;
    }
  } catch (error) {
    console.error('Error loading bookings:', error);
    showToast('Failed to load bookings', 'error');
  } finally {
    loading.value = false;
  }
};

const showAcceptConfirm = (booking) => {
  confirmingBooking.value = booking;
  confirmAction.value = 'accept';
};

const showDeclineConfirm = (booking) => {
  confirmingBooking.value = booking;
  confirmAction.value = 'decline';
};

const confirmAcceptBooking = async (notes) => {
  if (!confirmingBooking.value) return;
  
  processingId.value = confirmingBooking.value.id;
  try {
    const { data } = await api.patch(
      `/api/v1/bookings/${confirmingBooking.value.id}/status`,
      {
        status: 'accepted',
        photographer_notes: notes
      }
    );

    if (data.status === 'success') {
      showToast('Booking accepted successfully!', 'success');
      
      // Remove from list
      bookings.value = bookings.value.filter(b => b.id !== confirmingBooking.value.id);
      
      // Refresh after 2 seconds
      setTimeout(() => {
        router.push({ name: 'photographer-bookings-accepted' });
      }, 2000);
    }
  } catch (error) {
    console.error('Error accepting booking:', error);
    showToast('Failed to accept booking. Please try again.', 'error');
  } finally {
    processingId.value = null;
    confirmingBooking.value = null;
    confirmAction.value = null;
  }
};

const confirmDeclineBooking = async (reason) => {
  if (!confirmingBooking.value) return;
  
  processingId.value = confirmingBooking.value.id;
  try {
    const { data } = await api.patch(
      `/api/v1/bookings/${confirmingBooking.value.id}/status`,
      {
        status: 'declined',
        decline_reason: reason
      }
    );

    if (data.status === 'success') {
      showToast('Booking declined. Client has been notified.', 'success');
      
      // Remove from list
      bookings.value = bookings.value.filter(b => b.id !== confirmingBooking.value.id);
    }
  } catch (error) {
    console.error('Error declining booking:', error);
    showToast('Failed to decline booking. Please try again.', 'error');
  } finally {
    processingId.value = null;
    confirmingBooking.value = null;
    confirmAction.value = null;
  }
};

const showToast = (message, type) => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

const formatDate = (date) => {
  return formatDateValue(date);
};

const formatDateTime = (dateTime) => {
  return formatDateTimeValue(dateTime);
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
};

const getTimeRemaining = (expiresAt) => {
  const now = new Date();
  const expires = new Date(expiresAt);
  const diff = expires - now;
  
  if (diff <= 0) return 'Expired';
  
  const hours = Math.floor(diff / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  
  if (hours > 0) return `${hours}h ${minutes}m`;
  return `${minutes}m`;
};

const getExpirationClass = (expiresAt) => {
  const now = new Date();
  const expires = new Date(expiresAt);
  const diff = expires - now;
  const hours = diff / (1000 * 60 * 60);
  
  if (hours < 0) return 'text-red-600';
  if (hours < 6) return 'text-orange-600';
  return 'text-green-600';
};

const isExpired = (expiresAt) => {
  return new Date() > new Date(expiresAt);
};

onMounted(() => {
  loadBookings();
  // Poll for new bookings every 30 seconds
  const interval = setInterval(loadBookings, 30000);
  return () => clearInterval(interval);
});
</script>

<style scoped>
.bg-burgundy-light {
  @apply bg-[#F5E6E6];
}

.text-burgundy {
  @apply text-[#8B0000];
}

.border-burgundy {
  @apply border-[#8B0000];
}
</style>
