<template>
  <Toast
    v-if="toastVisible"
    :message="toastMessage"
    :type="toastType"
    @close="closeToast"
  />
  <div
    v-if="loading"
    class="min-h-screen flex items-center justify-center"
  >
    <div class="text-center">
      <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-2 border-burgundy" />
      <p class="text-gray-600 mt-4">
        Loading event...
      </p>
    </div>
  </div>

  <div
    v-else-if="event"
    class="min-h-screen bg-gray-50"
  >
    <!-- Hero Banner -->
    <div class="relative h-64 sm:h-80 md:h-96 overflow-hidden bg-gradient-to-br from-gray-300 to-gray-400">
      <img
        v-if="heroImage"
        :src="heroImage"
        :alt="event.title"
        class="w-full h-full object-cover"
        decoding="async"
      >
      <div
        v-else
        class="w-full h-full flex items-center justify-center"
      >
        <svg
          class="w-20 h-20 text-gray-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
      </div>
      <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" />
      
      <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 md:p-8">
        <div class="container mx-auto">
          <div class="flex flex-wrap items-center gap-2 mb-2 sm:mb-3">
            <span class="px-2 sm:px-3 py-1 bg-white/90 text-gray-900 rounded-full text-xs sm:text-sm font-semibold uppercase">
              {{ displayTypeLabel || 'Event' }}
            </span>
            <span
              v-if="eventState"
              class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold"
              :class="eventState.tone"
            >
              {{ eventState.label }}
            </span>
            <span
              v-if="event.is_featured"
              class="px-2 sm:px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs sm:text-sm font-semibold"
            >
              ⭐ Featured
            </span>
            <span
              v-if="isPaid"
              class="px-2 sm:px-3 py-1 bg-burgundy text-white rounded-full text-xs sm:text-sm font-semibold"
            >
              Paid Event
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-2">
            {{ event.title }}
          </h1>
          <p class="text-sm sm:text-base md:text-lg text-white/90">
            {{ formatDate(event.start_datetime || event.event_date) }} • {{ event.venue_name || event.venue || event.location_text || event.location || 'TBA' }}
          </p>
          <p
            v-if="heroCredit"
            class="mt-2 text-xs text-white/80"
          >
            Photo by
            <a
              :href="heroCredit.url"
              target="_blank"
              rel="noopener"
              class="font-semibold text-white underline"
            >
              {{ heroCredit.name }}
            </a>
            on Pexels.
          </p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-3 sm:px-4 md:px-6 py-6 sm:py-8 md:py-12">
      <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <!-- Left Column: Event Details -->
        <div class="lg:col-span-2 space-y-6 sm:space-y-8">
          <!-- Stats Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ event.registered_count || 0 }}
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">
                Registrations
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ event.capacity_percent || 0 }}%
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">
                Capacity Used
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ event.max_attendees || '∞' }}
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">
                Capacity
              </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ isPaid ? formatPrice(priceValue, priceCurrency) : 'Free' }}
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">
                Price
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">
              About This Event
            </h2>
            <div class="prose prose-sm sm:prose max-w-none text-gray-700">
              <p class="whitespace-pre-wrap">
                {{ event.description }}
              </p>
            </div>
          </div>

          <!-- Event Details -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-4">
              Event Details
            </h2>
            <div class="space-y-3 sm:space-y-4">
              <!-- Date & Time -->
              <div class="flex items-start">
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Date & Time
                  </p>
                  <p class="font-semibold text-sm sm:text-base">
                    {{ formatDateTimeRange(event.start_datetime || event.event_date, event.end_datetime || event.event_end_date) }}
                  </p>
                </div>
              </div>

              <!-- Location -->
              <div class="flex items-start">
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
                <div class="flex-1">
                  <p class="text-xs sm:text-sm text-gray-600">
                    Location
                  </p>
                  <p
                    v-if="event.venue_name"
                    class="font-semibold text-sm sm:text-base"
                  >
                    {{ event.venue_name }}
                  </p>
                  <p
                    v-else-if="event.venue"
                    class="font-semibold text-sm sm:text-base"
                  >
                    {{ event.venue }}
                  </p>
                  <p
                    v-else-if="event.location_text"
                    class="font-semibold text-sm sm:text-base"
                  >
                    {{ event.location_text }}
                  </p>
                  <p
                    v-else-if="event.location"
                    class="font-semibold text-sm sm:text-base"
                  >
                    {{ event.location }}
                  </p>
                  <p
                    v-if="event.venue_address"
                    class="text-xs sm:text-sm text-gray-700 mt-1"
                  >
                    {{ event.venue_address }}
                  </p>
                  <p
                    v-else-if="event.address"
                    class="text-xs sm:text-sm text-gray-700 mt-1"
                  >
                    {{ event.address }}
                  </p>
                  <p
                    v-if="event.city"
                    class="text-xs sm:text-sm text-gray-600 mt-0.5"
                  >
                    {{ event.city.name }}
                  </p>
                </div>
              </div>

              <!-- Duration -->
              <div
                v-if="event.duration_hours"
                class="flex items-start"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Duration
                  </p>
                  <p class="font-semibold text-sm sm:text-base">
                    {{ event.duration_hours }} hours
                  </p>
                </div>
              </div>

              <!-- Capacity -->
              <div
                v-if="event.max_attendees"
                class="flex items-start"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5"
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
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    Capacity
                  </p>
                  <p class="font-semibold text-sm sm:text-base">
                    {{ event.max_attendees }} attendees
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ event.registered_count || 0 }} registered
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Requirements (if any) -->
          <div
            v-if="event.requirements"
            class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6"
          >
            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">
              Requirements
            </h2>
            <div class="prose prose-sm sm:prose max-w-none text-gray-700">
              <p class="whitespace-pre-wrap">
                {{ event.requirements }}
              </p>
            </div>
          </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="mt-6 lg:mt-0 space-y-4 sm:space-y-6">
          <!-- RSVP Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6 sticky top-4">
            <div class="text-center mb-4 sm:mb-6">
              <div class="text-3xl sm:text-4xl font-bold text-burgundy mb-2">
                {{ isPaid ? formatPrice(priceValue, priceCurrency) : 'Free' }}
              </div>
              <p
                v-if="isPaid"
                class="text-xs sm:text-sm text-gray-600"
              >
                per person
              </p>
            </div>

            <button
              :disabled="isEventFull || isPaid"
              :class="[
                'w-full py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg transition-colors',
                isRsvped
                  ? 'bg-green-600 text-white hover:bg-green-700'
                  : isEventFull
                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    : isPaid
                      ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                      : 'bg-burgundy text-white hover:bg-rose-800'
              ]"
              @click="handleRsvp"
            >
              {{
                isEventFull
                  ? 'Event Full'
                  : isPaid
                    ? 'Tickets Required'
                    : isRsvped
                      ? '✓ Registered'
                      : 'Register Now'
              }}
            </button>

            <div
              v-if="!isEventFull"
              class="mt-3 sm:mt-4 text-center text-xs sm:text-sm text-gray-600"
            >
              {{ event.max_attendees ? `${event.max_attendees - (event.registered_count || 0)} spots left` : 'Unlimited spots' }}
            </div>
          </div>

          <!-- Organizer Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">
              Organized By
            </h3>
            <div class="flex items-center gap-3 mb-3 sm:mb-4">
              <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full object-cover overflow-hidden bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                <img
                  v-if="event.organizer?.profile_image_url"
                  :src="event.organizer?.profile_image_url"
                  :alt="event.organizer?.user?.name"
                  class="w-full h-full object-cover"
                >
                <svg
                  v-else
                  class="w-6 h-6 sm:w-8 sm:h-8 text-gray-500"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div>
                <p class="font-semibold text-sm sm:text-base">
                  {{ event.organizer?.user?.name }}
                </p>
                <p
                  v-if="event.organizer?.specialization"
                  class="text-xs sm:text-sm text-gray-600"
                >
                  {{ event.organizer.specialization }}
                </p>
              </div>
            </div>
            <button
              class="w-full py-2 border-2 border-burgundy text-burgundy rounded-lg hover:bg-burgundy hover:text-white transition-colors text-sm sm:text-base font-medium"
              @click="viewPhotographer"
            >
              View Profile
            </button>
          </div>

          <!-- Share Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">
              Share Event
            </h3>
            <div class="flex gap-2 sm:gap-3">
              <button
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500 transition-colors"
                title="Share on Facebook"
                @click="shareOnFacebook"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-blue-600"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
              </button>
              <button
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-colors"
                title="Share on Twitter"
                @click="shareOnTwitter"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-blue-400"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                </svg>
              </button>
              <button
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                title="Copy link"
                @click="copyLink"
              >
                <svg
                  class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-gray-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div
    v-else
    class="min-h-screen flex items-center justify-center"
  >
    <div class="text-center">
      <h2 class="text-2xl font-bold text-gray-800 mb-2">
        Event Not Found
      </h2>
      <p class="text-gray-600 mb-4">
        The event you're looking for doesn't exist
      </p>
      <button
        class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-rose-800"
        @click="$router.push('/events')"
      >
        Back to Events
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Toast from '../components/ui/Toast.vue';
import { useApiError } from '../composables/useApiError';
import api from '../api';
import {
  formatDate as formatDateValue,
  formatDateTime as formatDateTimeValue,
  formatNumber
} from '../utils/formatters';

const router = useRouter();
const route = useRoute();

const {
  toastMessage,
  toastType,
  toastVisible,
  showToast,
  closeToast,
  handleApiError,
} = useApiError();

// State
const event = ref(null);
const loading = ref(true);
const isRsvped = ref(false);

// Computed
const isEventFull = computed(() => {
  if (!event.value) return false;
  if (typeof event.value.capacity_full === 'boolean') return event.value.capacity_full;
  if (!event.value.max_attendees) return false;
  return (event.value.registered_count || 0) >= event.value.max_attendees;
});

const isPaid = computed(() => {
  if (!event.value) return false;
  if (typeof event.value.is_paid === 'boolean') return event.value.is_paid;
  if (typeof event.value.is_ticketed === 'boolean') return event.value.is_ticketed;
  if (event.value.event_mode) return event.value.event_mode === 'paid';
  if (event.value.event_type) return event.value.event_type === 'paid';

  const numeric = Number(
    event.value.ticket_price ?? event.value.price ?? event.value.base_price ?? 0
  );
  return Number.isFinite(numeric) && numeric > 0;
});

const displayTypeLabel = computed(() => {
  if (!event.value) return '';
  return formatLabel(event.value.type || event.value.event_type || event.value.event_mode || '');
});

const priceValue = computed(() => {
  if (!event.value) return 0;

  if (Array.isArray(event.value.tickets) && event.value.tickets.length > 0) {
    const prices = event.value.tickets
      .map((ticket) => Number(ticket.price))
      .filter((amount) => Number.isFinite(amount));
    if (prices.length) return Math.min(...prices);
  }

  const direct = Number(
    event.value.price ?? event.value.ticket_price ?? event.value.base_price ?? 0
  );
  return Number.isFinite(direct) ? direct : 0;
});

const priceCurrency = computed(() => event.value?.currency || 'BDT');

const heroImage = computed(() => {
  if (!event.value) return null;
  return event.value.hero_image_url || event.value.banner_image || null;
});

const heroCredit = computed(() => {
  if (!event.value) return null;
  if (event.value.hero_image_url && event.value.hero_image_credit_name) {
    return {
      name: event.value.hero_image_credit_name,
      url: event.value.hero_image_credit_url || 'https://www.pexels.com',
    };
  }
  if (!event.value.hero_image_url && event.value.banner_image && event.value.banner_image_credit_name) {
    return {
      name: event.value.banner_image_credit_name,
      url: event.value.banner_image_credit_url || 'https://www.pexels.com',
    };
  }
  return null;
});

const eventState = computed(() => {
  if (!event.value) return null;
  if (event.value.status === 'cancelled') {
    return { label: 'Cancelled', tone: 'bg-red-100 text-red-800' };
  }

  const sourceDate = event.value.start_datetime || event.value.event_date;
  if (!sourceDate) {
    return null;
  }

  const toDateKey = (value) => {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return null;
    return date.toISOString().slice(0, 10);
  };

  const todayKey = toDateKey(new Date());
  const eventKey = toDateKey(sourceDate);
  if (!todayKey || !eventKey) return null;

  if (eventKey === todayKey) {
    return { label: 'Ongoing', tone: 'bg-green-100 text-green-800' };
  }
  if (eventKey > todayKey) {
    return { label: 'Upcoming', tone: 'bg-blue-100 text-blue-800' };
  }
  return { label: 'Ended', tone: 'bg-gray-100 text-gray-700' };
});

// Methods
const fetchEvent = async () => {
  loading.value = true;
  try {
    const slug = route.params.slug;
    const { data } = await api.get(`/events/${slug}`);

    if (data.event || data.data) {
      event.value = data.event || data.data;
      const registration = event.value?.user_registration;
      isRsvped.value = registration && registration.rsvp_status === 'going';
    }
  } catch (error) {
    handleApiError(error, 'Failed to load event');
    event.value = null;
  } finally {
    loading.value = false;
  }
};

const handleRsvp = async () => {
  if (!localStorage.getItem('user')) {
    router.push('/auth');
    return;
  }

  if (isEventFull.value) return;

  try {
    const rsvpStatus = isRsvped.value ? 'not_going' : 'going';
    const { data } = await api.post(`/events/${event.value.id}/rsvp`);

    if (data.status === 'success') {
      isRsvped.value = !isRsvped.value;
      if (isRsvped.value) {
        event.value.registered_count = (event.value.registered_count || 0) + 1;
      } else {
        event.value.registered_count = Math.max((event.value.registered_count || 1) - 1, 0);
      }
    }
  } catch (error) {
    handleApiError(error, 'Failed to update RSVP');
  }
};

const viewPhotographer = () => {
  if (event.value?.organizer?.slug) {
    router.push(`/photographers/${event.value.organizer.slug}`);
  }
};

const shareOnFacebook = () => {
  const url = encodeURIComponent(window.location.href);
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
};

const shareOnTwitter = () => {
  const url = encodeURIComponent(window.location.href);
  const text = encodeURIComponent(event.value?.title || 'Check out this event');
  window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
};

const copyLink = () => {
  navigator.clipboard.writeText(window.location.href);
  showToast('Link copied to clipboard!', 'success');
};

const formatDate = (date) => {
  if (!date) return 'TBA';
  return formatDateValue(date);
};

const formatDateTime = (date) => {
  if (!date) return 'TBA';
  return formatDateTimeValue(date);
};

const formatDateTimeRange = (start, end) => {
  if (!start && !end) return 'TBA';
  if (start && end) {
    return `${formatDateTime(start)} - ${formatDateTime(end)}`;
  }
  return formatDateTime(start || end);
};

const formatPrice = (price, currency = 'BDT') => {
  if (price === null || price === undefined) return `৳0`;
  const amount = Number(price) || 0;
  if (currency === 'BDT') {
    return `৳${formatNumber(amount)}`;
  }
  return `${currency} ${formatNumber(amount)}`;
};

const formatLabel = (value) => {
  if (!value) return '';
  return String(value)
    .replace(/[_-]/g, ' ')
    .replace(/\b\w/g, (char) => char.toUpperCase());
};

// Lifecycle
onMounted(() => {
  fetchEvent();
});
</script>
