<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="closeToast"
    />
    <!-- Hero Section with Parallax Effect -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <!-- Decorative Background Elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white bg-opacity-5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white bg-opacity-5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-white bg-opacity-5 rounded-full blur-2xl" />
      </div>

      <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 max-w-7xl pt-10 pb-16 md:pt-10 md:pb-24 relative z-10">
        <!-- Logo/Brand Section -->
        <div class="text-center mt-5 mb-4 sm:mb-5 md:mb-6 lg:mb-8">
          <div class="inline-flex mb-2 sm:mb-3 px-3 sm:px-4 py-1 sm:py-1.5 bg-white bg-opacity-10 backdrop-blur-sm rounded-full border border-white border-opacity-20">
            <p class="text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-2 justify-center">
              <svg
                class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
              </svg>
              A Project by <a
                href="https://somogrobangladesh.com/"
                target="_blank"
                rel="noopener"
                class="underline hover:text-white/80 transition-colors"
              >Somogro Bangladesh</a>
            </p>
          </div>
        </div>

        <h1 class="text-3xl sm:text-4xl md:text-6xl font-bold mb-4 text-center tracking-tight animate-fade-in">
          Photography Events
        </h1>
        <p class="text-lg md:text-xl text-gray-100 max-w-3xl mx-auto text-center leading-relaxed animate-fade-in-delay mb-10">
          Discover workshops, exhibitions, meetups, and photography events happening near you
        </p>

        <!-- Stats Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mt-10 animate-fade-in-delay-2">
          <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-4 sm:p-6 text-center border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold">
              {{ stats.total_events || 0 }}
            </div>
            <div class="text-sm md:text-base text-gray-200 mt-1">
              Total Events
            </div>
          </div>
          <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-4 sm:p-6 text-center border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold">
              {{ stats.upcoming_events || 0 }}
            </div>
            <div class="text-sm md:text-base text-gray-200 mt-1">
              Upcoming
            </div>
          </div>
          <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-4 sm:p-6 text-center border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold">
              {{ stats.total_cities || 0 }}
            </div>
            <div class="text-sm md:text-base text-gray-200 mt-1">
              Locations
            </div>
          </div>
          <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-4 sm:p-6 text-center border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold">
              {{ stats.total_rsvps || 0 }}
            </div>
            <div class="text-sm md:text-base text-gray-200 mt-1">
              Registrations
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 max-w-7xl py-6 sm:py-8 md:py-12 mt-5">
      <!-- Filters -->
      <div class="sb-ui-card p-4 sm:p-5 md:p-6 mb-6 sm:mb-8">
        <h2 class="text-lg sm:text-xl font-semibold mb-4">
          Filter Events
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
          <!-- Location Filter -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Location</label>
            <select 
              v-model="filters.city" 
              class="sb-ui-select text-sm sm:text-base"
              @change="applyFilters"
            >
              <option value="">
                All Locations
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

          <!-- From Date Filter -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">From Date</label>
            <input
              v-model="filters.from_date"
              type="date"
              class="sb-ui-input text-sm sm:text-base"
              @change="applyFilters"
            >
          </div>

          <!-- To Date Filter -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">To Date</label>
            <input
              v-model="filters.to_date"
              type="date"
              class="sb-ui-input text-sm sm:text-base"
              @change="applyFilters"
            >
          </div>

          <!-- Event Type Filter -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Event Type</label>
            <select 
              v-model="filters.event_type" 
              class="sb-ui-select text-sm sm:text-base"
              @change="applyFilters"
            >
              <option value="">
                All Types
              </option>
              <option value="workshop">
                Workshop
              </option>
              <option value="exhibition">
                Exhibition
              </option>
              <option value="meetup">
                Meetup
              </option>
              <option value="competition">
                Competition
              </option>
              <option value="seminar">
                Seminar
              </option>
              <option value="other">
                Other
              </option>
            </select>
          </div>
        </div>

        <!-- Clear Filters -->
        <button
          v-if="hasActiveFilters"
          class="mt-4 text-sm text-burgundy hover:text-rose-800 font-medium"
          @click="clearFilters"
        >
          Clear All Filters
        </button>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading"
        class="text-center py-12"
        role="status"
        aria-live="polite"
      >
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy" />
        <p class="text-gray-600 mt-4">
          Loading events...
        </p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="events.length === 0"
        class="text-center py-16 sm:py-20 md:py-24"
      >
        <svg
          class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto text-gray-400 mb-4"
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
        <h3 class="text-lg sm:text-xl md:text-2xl font-semibold text-gray-800 mb-2">
          No Events Found
        </h3>
        <p class="text-sm sm:text-base text-gray-600">
          Try adjusting your filters or check back later for new events
        </p>
      </div>

      <!-- Events Grid -->
      <div v-else>
        <div class="flex justify-between items-center mb-4 sm:mb-6">
          <p class="text-sm sm:text-base text-gray-600">
            Showing <span class="font-semibold">{{ filteredEventsCount }}</span> events
          </p>
          <select 
            v-model="filters.sort" 
            class="sb-ui-select !min-h-[44px] px-3 py-1.5 text-xs sm:text-sm"
            @change="applyFilters"
          >
            <option value="date_asc">
              Date: Earliest First
            </option>
            <option value="date_desc">
              Date: Latest First
            </option>
            <option value="featured">
              Featured First
            </option>
          </select>
        </div>

        <div class="space-y-8 sm:space-y-10">
          <section
            v-for="section in eventSections"
            :key="section.key"
          >
            <div class="flex items-center justify-between gap-3 mb-4">
              <div>
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
                  {{ section.title }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-600">
                  {{ section.description }}
                </p>
              </div>
              <span class="sb-ui-badge bg-gray-100 text-gray-700 text-xs sm:text-sm">{{ section.items.length }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7 md:gap-8">
              <div
                v-for="event in section.items"
                :key="`${section.key}-${event.id}`"
                class="sb-ui-card sb-ui-card--interactive hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden group flex flex-col"
                role="button"
                tabindex="0"
                :aria-label="`Open event ${event.title}`"
                @click="viewEvent(event)"
                @keydown.enter="viewEvent(event)"
                @keydown.space.prevent="viewEvent(event)"
              >
            <!-- Event Image -->
            <div class="relative w-full overflow-hidden bg-gradient-to-br from-gray-300 to-gray-400 pt-[56.25%]">
              <picture v-if="getEventImage(event) && !hasImageError(event.id)">
                <source
                  v-if="getWebpSource(getEventImage(event))"
                  :srcset="getWebpSource(getEventImage(event))"
                  type="image/webp"
                >
                <img
                  :src="getEventImage(event)"
                  :alt="event.title"
                  class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                  loading="lazy"
                  decoding="async"
                  width="1280"
                  height="720"
                  sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw"
                  @error="markImageError(event.id)"
                >
              </picture>
              <div
                v-else
                class="absolute inset-0 flex items-center justify-center"
              >
                <svg
                  class="w-12 h-12 text-gray-500"
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
              <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-black/20 to-transparent" />
              <div class="absolute top-3 left-3 sb-ui-badge bg-black/70 text-white text-xs sm:text-sm shadow">
                {{ formatDateBadge(event.start_datetime || event.event_date) || 'TBA' }}
              </div>
              <div
                v-if="getFeaturedBadge(event)"
                class="absolute top-3 right-3 sb-ui-badge text-xs sm:text-sm shadow"
                :class="getFeaturedBadge(event)?.tone"
              >
                {{ getFeaturedBadge(event).label }}
              </div>
              <div
                class="absolute bottom-3 left-3 sb-ui-badge text-xs sm:text-sm shadow"
                :class="getPriceBadge(event).tone"
              >
                {{ getPriceBadge(event).label }}
              </div>
            </div>

            <!-- Event Content -->
            <div class="p-4 sm:p-5">
              <!-- Event Type Badge -->
              <div class="flex items-center gap-2 mb-2">
                <span
                  class="sb-ui-badge text-xs font-semibold uppercase tracking-wide"
                  :class="getEventTypeBadge(event).tone"
                >
                  {{ getEventTypeBadge(event).label }}
                </span>
              </div>

              <!-- Event Title -->
              <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-burgundy transition-colors">
                {{ event.title }}
              </h3>

              <!-- Event Description -->
              <p class="text-xs sm:text-sm text-gray-600 mb-4">
                {{ truncateText(event.description, 120) }}
              </p>

              <!-- Event Details -->
              <div class="space-y-2 mb-4">
                <!-- Date & Time -->
                <div class="flex items-start text-xs sm:text-sm text-gray-700">
                  <svg
                    class="w-4 h-4 mr-2 mt-0.5 text-burgundy"
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
                  <div class="space-y-0.5">
                    <div>
                      <span class="text-gray-500">Date:</span>
                      <span class="ml-1 font-medium">{{ formatDate(event.event_date || event.start_datetime) }}</span>
                    </div>
                    <div>
                      <span class="text-gray-500">Start:</span>
                      <span class="ml-1 font-medium">{{ formatTimeLabel(event.start_time, event.start_datetime) }}</span>
                    </div>
                    <div>
                      <span class="text-gray-500">End:</span>
                      <span class="ml-1 font-medium">{{ formatTimeLabel(event.end_time, event.end_datetime || event.event_end_date) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Location -->
                <div class="flex items-center text-xs sm:text-sm text-gray-700">
                  <svg
                    class="w-4 h-4 mr-2 text-burgundy"
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
                  <button
                    type="button"
                    class="inline-flex items-center gap-1 text-left hover:text-burgundy focus:outline-none focus-visible:ring-2 focus-visible:ring-burgundy rounded"
                    @click.stop="filterByLocation(event)"
                  >
                    <span class="underline-offset-2 hover:underline">
                      {{ event.venue_name || event.venue || event.location_text || event.location || 'TBA' }}
                    </span>
                  </button>
                </div>

                <!-- Organizer -->
                <div class="flex items-center text-xs sm:text-sm text-gray-700">
                  <svg
                    class="w-4 h-4 mr-2 text-burgundy"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                  </svg>
                  <span>{{ event.organizer?.name || event.organizer?.user?.name || 'Unknown' }}</span>
                </div>

                <!-- Price (if ticketed) -->
                <div
                  v-if="isPaidEvent(event)"
                  class="flex items-center text-xs sm:text-sm text-gray-700"
                >
                  <svg
                    class="w-4 h-4 mr-2 text-burgundy"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"
                    />
                  </svg>
                  <span class="font-semibold text-burgundy">৳{{ event.ticket_price || event.price || event.base_price || 0 }}</span>
                </div>
              </div>

              <!-- Registration Status -->
              <div class="flex items-center justify-between">
                <div class="flex items-center text-xs sm:text-sm text-gray-600">
                  <svg
                    class="w-4 h-4 mr-1"
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
                  {{ event.rsvp_count || 0 }} Registrations
                </div>
              </div>

              <div class="mt-4 flex items-center gap-3">
                <button
                  class="flex-1 sb-ui-btn sb-ui-btn--primary text-sm sm:text-base"
                  @click.stop="viewEvent(event)"
                >
                  View Details
                </button>
                <button
                  :class="[
                    'flex-1 sb-ui-btn text-sm sm:text-base',
                    isRsvped(event)
                      ? 'bg-green-600 text-white hover:bg-green-700 border-transparent'
                      : 'sb-ui-btn--secondary'
                  ]"
                  @click.stop="toggleRsvp(event)"
                >
                  {{ isRsvped(event) ? '✓ Registered' : 'Register' }}
                </button>
              </div>
            </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row justify-center items-center gap-2 sm:gap-4 mt-8 sm:mt-10 md:mt-12">
          <button
            v-if="currentPage > 1"
            class="w-full sm:w-auto sb-ui-btn sb-ui-btn--secondary text-sm sm:text-base rounded-full"
            @click="previousPage"
          >
            <span class="hidden sm:inline">← Previous</span>
            <span class="sm:hidden">←</span>
          </button>
          
          <span class="text-sm sm:text-base text-gray-700 bg-white/90 border border-[#eadfd7] rounded-full px-4 sm:px-5 py-2 shadow-sm">
            Page <span class="font-semibold">{{ currentPage }}</span> of <span class="font-semibold">{{ totalPages }}</span>
          </span>
          
          <button
            v-if="currentPage < totalPages"
            class="w-full sm:w-auto sb-ui-btn sb-ui-btn--secondary text-sm sm:text-base rounded-full"
            @click="nextPage"
          >
            <span class="hidden sm:inline">Next →</span>
            <span class="sm:hidden">→</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../api';
import Toast from '../components/ui/Toast.vue';
import { useApiError } from '../composables/useApiError';
import { formatDate as formatDateValue, formatNumber } from '../utils/formatters';

const router = useRouter();
const route = useRoute();
const { toastMessage, toastType, toastVisible, showToast, handleApiError, closeToast } = useApiError();

// State
const events = ref([]);
const cities = ref([]);
const stats = ref({});
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const userRsvps = ref([]);
const imageErrors = ref({});

// Filters
const filters = ref({
  city: '',
  from_date: '',
  to_date: '',
  event_type: '',
  sort: 'date_asc'
});

// Computed
const hasActiveFilters = computed(() => {
  return filters.value.city || filters.value.from_date || 
         filters.value.to_date || filters.value.event_type;
});

const getEventDateValue = (event) => {
  const raw = event?.start_datetime || event?.event_date || event?.created_at;
  const date = raw ? new Date(raw) : null;
  return date && !Number.isNaN(date.getTime()) ? date : null;
};

const isPastEvent = (event) => {
  const eventDate = getEventDateValue(event);
  if (!eventDate) return false;
  const now = new Date();
  now.setHours(0, 0, 0, 0);
  return eventDate < now;
};

const isFeaturedEvent = (event) => {
  return Boolean(event?.is_featured || event?.is_admin_featured || event?.is_sponsored || event?.is_promoted);
};

const featuredEvents = computed(() => events.value.filter((event) => isFeaturedEvent(event)));
const upcomingEvents = computed(() => events.value.filter((event) => !isPastEvent(event) && !isFeaturedEvent(event)));
const pastEvents = computed(() => events.value.filter((event) => isPastEvent(event) && !isFeaturedEvent(event)));

const eventSections = computed(() => {
  const sections = [];
  if (featuredEvents.value.length) {
    sections.push({
      key: 'featured',
      title: 'Featured Events',
      description: 'Curated highlights and premium photography experiences.',
      items: featuredEvents.value,
    });
  }
  if (upcomingEvents.value.length) {
    sections.push({
      key: 'upcoming',
      title: 'Upcoming Events',
      description: 'Open for registration and happening soon.',
      items: upcomingEvents.value,
    });
  }
  if (pastEvents.value.length) {
    sections.push({
      key: 'past',
      title: 'Past Events',
      description: 'Browse previous events and community highlights.',
      items: pastEvents.value,
    });
  }
  return sections;
});

const filteredEventsCount = computed(() => events.value.length);

const normalizeSlug = (value) => {
  return String(value || '')
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '');
};

const getCityById = (id) => cities.value.find(city => String(city.id) === String(id));

const getCityBySlug = (slug) => {
  if (!slug) return null;
  const normalized = normalizeSlug(slug);
  return cities.value.find((city) => city.slug === slug || normalizeSlug(city.name) === normalized);
};

const getEventCitySlug = (event) => {
  if (!event) return '';
  return event.city?.slug
    || event.city_slug
    || normalizeSlug(event.city?.name || event.city_name || event.location_text || event.location || '');
};

const syncLocationFromQuery = () => {
  const locationSlug = String(route.query.location || '').trim();
  if (!locationSlug) return;
  const match = getCityBySlug(locationSlug);
  if (match) {
    filters.value.city = match.id;
  }
};

const updateLocationQuery = (locationSlug) => {
  const nextQuery = { ...route.query };
  if (locationSlug) {
    nextQuery.location = locationSlug;
  } else {
    delete nextQuery.location;
  }
  router.replace({ query: nextQuery });
};

const EVENT_TYPE_MAP = {
  photowalk: { label: 'PHOTOWALK', tone: 'bg-amber-100 text-amber-800' },
  workshop: { label: 'WORKSHOP', tone: 'bg-blue-100 text-blue-800' },
  expo: { label: 'EXPO', tone: 'bg-rose-100 text-rose-800' },
  exhibition: { label: 'EXPO', tone: 'bg-rose-100 text-rose-800' },
  seminar: { label: 'SEMINAR', tone: 'bg-emerald-100 text-emerald-800' },
  webinar: { label: 'WEBINAR', tone: 'bg-blue-100 text-blue-800' },
  meetup: { label: 'MEETUP', tone: 'bg-gray-100 text-gray-700' },
  competition: { label: 'COMPETITION', tone: 'bg-amber-100 text-amber-800' },
  other: { label: 'EVENT', tone: 'bg-gray-100 text-gray-700' }
};

const getEventTypeBadge = (event) => {
  const raw = event?.event_type || event?.type || event?.event_mode || '';
  const key = normalizeSlug(raw);
  if (EVENT_TYPE_MAP[key]) return EVENT_TYPE_MAP[key];
  if (!raw) return { label: 'EVENT', tone: 'bg-gray-100 text-gray-700' };
  return { label: String(raw).replace(/[_-]/g, ' ').toUpperCase(), tone: 'bg-gray-100 text-gray-700' };
};

const isLimitedSeats = (event) => {
  if (!event) return false;
  if (event.is_limited_seats) return true;
  if (!event.max_attendees) return false;
  const remaining = event.max_attendees - (event.registered_count || 0);
  return remaining > 0 && remaining <= 10;
};

const getFeaturedBadge = (event) => {
  if (!event) return null;
  const isSponsored = Boolean(event.is_sponsored || event.sponsored);
  const isPromoted = Boolean(event.is_promoted || event.promoted);
  const isAdminFeatured = Boolean(event.is_admin_featured || event.admin_featured);
  const limited = isLimitedSeats(event);

  if (!isSponsored && !isPromoted && !isAdminFeatured && !limited) return null;

  if (limited) {
    return { label: 'Limited Seats', tone: 'bg-amber-100 text-amber-800' };
  }

  return { label: 'Featured', tone: 'bg-yellow-400 text-yellow-900' };
};

const getPriceBadge = (event) => {
  if (isPaidEvent(event)) {
    const amount = Number(event.ticket_price ?? event.price ?? event.base_price ?? 0);
    if (!Number.isFinite(amount) || amount <= 0) {
      return { label: 'Paid', tone: 'bg-burgundy text-white' };
    }
    const formatted = formatNumber(amount);
    return { label: `৳${formatted}`, tone: 'bg-burgundy text-white' };
  }
  return { label: 'Free', tone: 'bg-green-600 text-white' };
};

const truncateText = (value, maxLength = 120) => {
  if (!value) return '';
  const clean = String(value).replace(/\s+/g, ' ').trim();
  if (clean.length <= maxLength) return clean;
  const trimmed = clean.slice(0, maxLength);
  const safe = trimmed.slice(0, trimmed.lastIndexOf(' ') > 60 ? trimmed.lastIndexOf(' ') : trimmed.length);
  return `${safe}...`;
};

const formatDateBadge = (value) => {
  if (!value) return '';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return '';
  const month = date.toLocaleString('en-US', { month: 'short' });
  return `${month} ${date.getDate()}`;
};

const formatTimeOnly = (value) => {
  if (!value) return '';
  if (typeof value === 'string') {
    const match = value.match(/^(\d{2}):(\d{2})/);
    if (match) return `${match[1]}:${match[2]}`;
  }
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return '';
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${hours}:${minutes}`;
};

const isMidnight = (value) => {
  if (!value || Number.isNaN(value.getTime())) return false;
  return value.getHours() === 0 && value.getMinutes() === 0 && value.getSeconds() === 0;
};

const formatTimeLabel = (timeValue, dateValue) => {
  const timeOnly = formatTimeOnly(timeValue);
  if (timeOnly) return timeOnly;
  if (!dateValue) return 'TBA';
  const raw = String(dateValue);
  if (!raw.includes(':') && raw.length <= 10) return 'TBA';
  const date = new Date(dateValue);
  if (Number.isNaN(date.getTime()) || isMidnight(date)) return 'TBA';
  return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const getWebpSource = (url) => {
  if (!url || typeof url !== 'string') return '';
  if (url.startsWith('data:')) return '';
  const match = url.match(/\.(jpg|jpeg|png)(\?.*)?$/i);
  if (!match) return '';
  return url.replace(/\.(jpg|jpeg|png)(\?.*)?$/i, '.webp$2');
};

const getGalleryImage = (event) => {
  const gallery = event?.gallery_images;
  if (Array.isArray(gallery)) return gallery[0] || '';
  if (typeof gallery === 'string') {
    try {
      const parsed = JSON.parse(gallery);
      return Array.isArray(parsed) ? (parsed[0] || '') : '';
    } catch {
      return '';
    }
  }
  return '';
};

const getEventImage = (event) => {
  return event?.hero_image_url
    || event?.banner_image
    || event?.og_image
    || getGalleryImage(event)
    || '';
};

const hasImageError = (id) => Boolean(imageErrors.value[id]);

const markImageError = (id) => {
  if (!id) return;
  imageErrors.value = { ...imageErrors.value, [id]: true };
};

// Methods
const fetchStats = async () => {
  try {
    const { data } = await api.get('/events/stats');
    if (data.status === 'success') {
      stats.value = data.data;
    }
  } catch (error) {
    handleApiError(error, 'Error fetching stats');
  }
};

const fetchCities = async () => {
  try {
    const { data } = await api.get('/locations');
    if (data.status === 'success') {
      const locations = data.data || [];
      cities.value = locations.filter(location => location.type !== 'division');
      syncLocationFromQuery();
    }
  } catch (error) {
    handleApiError(error, 'Error fetching cities');
  }
};

const fetchEvents = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', currentPage.value);
    
    if (filters.value.city) params.append('city_id', filters.value.city);
    if (!filters.value.city && route.query.location) params.append('location', route.query.location);
    if (filters.value.from_date) params.append('start_date', filters.value.from_date);
    if (filters.value.to_date) params.append('end_date', filters.value.to_date);
    if (filters.value.event_type) params.append('type', filters.value.event_type);
    if (filters.value.sort) params.append('sort', filters.value.sort);

    const { data } = await api.get(`/events?${params}`);

    // Handle custom API response format
    if (data.data) {
      events.value = data.data;
      totalPages.value = data.pagination?.last_page || 1;
      currentPage.value = data.pagination?.current_page || 1;
    }
  } catch (error) {
    handleApiError(error, 'Error fetching events');
    events.value = [];
  } finally {
    loading.value = false;
  }
};

const applyFilters = () => {
  currentPage.value = 1;
  const selectedCity = getCityById(filters.value.city);
  updateLocationQuery(selectedCity?.slug || '');
  fetchEvents();
};

const clearFilters = () => {
  filters.value = {
    city: '',
    from_date: '',
    to_date: '',
    event_type: '',
    sort: 'date_asc'
  };
  updateLocationQuery('');
  applyFilters();
};

const getEventRouteKey = (event) => {
  return event?.slug || event?.id || null;
};

const getEventPath = (event) => {
  const key = getEventRouteKey(event);
  return key ? `/events/${key}` : '/events';
};

const getEventTicketsPath = (event) => {
  const key = getEventRouteKey(event);
  return key ? `/events/${key}/tickets` : '/events';
};

const viewEvent = (event) => {
  router.push(getEventPath(event));
};

const isPaidEvent = (event) => {
  if (!event) return false;
  if (typeof event.is_ticketed === 'boolean') return event.is_ticketed;
  if (typeof event.is_ticketed === 'number') return event.is_ticketed === 1;
  if (typeof event.is_ticketed === 'string') {
    const normalized = event.is_ticketed.trim().toLowerCase();
    return normalized === '1' || normalized === 'true';
  }
  if (event.event_mode) return event.event_mode === 'paid';
  if (event.event_type) return event.event_type === 'paid';

  const numeric = Number(event.ticket_price ?? event.price ?? event.base_price ?? 0);
  return Number.isFinite(numeric) && numeric > 0;
};

const toggleRsvp = async (event) => {
  if (!localStorage.getItem('user')) {
    router.push('/auth');
    return;
  }

  if (isPaidEvent(event)) {
    router.push(getEventTicketsPath(event));
    return;
  }

  try {
    const rsvpStatus = isRsvped(event) ? 'not_going' : 'going';
    const { data } = await api.post(`/events/${event.id}/rsvp`);

    if (data.status === 'success') {
      if (rsvpStatus === 'going') {
        userRsvps.value.push(event.id);
        event.rsvp_count = (event.rsvp_count || 0) + 1;
      } else {
        userRsvps.value = userRsvps.value.filter(id => id !== event.id);
        event.rsvp_count = Math.max((event.rsvp_count || 1) - 1, 0);
      }
    }
  } catch (error) {
    handleApiError(error, 'Failed to update RSVP');
  }
};

const isRsvped = (event) => {
  return userRsvps.value.includes(event.id);
};

const formatDate = (date) => {
  return formatDateValue(date);
};

const filterByLocation = (event) => {
  const slug = getEventCitySlug(event);
  if (!slug) return;

  const match = getCityBySlug(slug);
  filters.value.city = match ? match.id : '';
  currentPage.value = 1;
  updateLocationQuery(slug);
  fetchEvents();
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    fetchEvents();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    fetchEvents();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const setMetaTag = (attribute, key, content) => {
  if (!content) return;
  let tag = document.head.querySelector(`meta[${attribute}="${key}"]`);
  if (!tag) {
    tag = document.createElement('meta');
    tag.setAttribute(attribute, key);
    document.head.appendChild(tag);
  }
  tag.setAttribute('content', content);
};

const updatePageMeta = () => {
  if (typeof window === 'undefined') return;
  const title = 'Photography Events | Photographar SB';
  const description = 'Explore upcoming, featured, and past photography events, workshops, and meetups.';
  const canonical = `${window.location.origin}${route.path}`;

  document.title = title;
  setMetaTag('name', 'description', description);
  setMetaTag('property', 'og:title', title);
  setMetaTag('property', 'og:description', description);
  setMetaTag('property', 'og:url', canonical);
  setMetaTag('property', 'og:type', 'website');
};

// Lifecycle
onMounted(() => {
  const initialize = async () => {
    updatePageMeta();
    fetchStats();
    await fetchCities();
    fetchEvents();
  };
  initialize();
});
</script>
