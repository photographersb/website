<template>
  <div v-if="loading" class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-2 border-burgundy"></div>
      <p class="text-gray-600 mt-4">Loading event...</p>
    </div>
  </div>

  <div v-else-if="event" class="min-h-screen bg-gray-50">
    <!-- Hero Banner -->
    <div class="relative h-64 sm:h-80 md:h-96 overflow-hidden">
      <img
        :src="event.hero_image_url || 'https://via.placeholder.com/1200x600?text=Event'"
        :alt="event.title"
        class="w-full h-full object-cover"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
      
      <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 md:p-8">
        <div class="container mx-auto">
          <div class="flex flex-wrap items-center gap-2 mb-2 sm:mb-3">
            <span class="px-2 sm:px-3 py-1 bg-white/90 text-gray-900 rounded-full text-xs sm:text-sm font-semibold uppercase">
              {{ event.event_type || 'Event' }}
            </span>
            <span v-if="event.is_featured" class="px-2 sm:px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs sm:text-sm font-semibold">
              ⭐ Featured
            </span>
            <span v-if="event.is_ticketed" class="px-2 sm:px-3 py-1 bg-burgundy text-white rounded-full text-xs sm:text-sm font-semibold">
              Ticketed Event
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-2">
            {{ event.title }}
          </h1>
          <p class="text-sm sm:text-base md:text-lg text-white/90">
            {{ formatDate(event.event_date) }} • {{ event.location }}
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
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">{{ event.rsvp_count || 0 }}</div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">Registrations</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">{{ event.view_count || 0 }}</div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">Views</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">{{ event.max_attendees || '∞' }}</div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">Capacity</div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 text-center">
              <div class="text-2xl sm:text-3xl font-bold text-burgundy">
                {{ event.is_ticketed ? `৳${event.ticket_price}` : 'Free' }}
              </div>
              <div class="text-xs sm:text-sm text-gray-600 mt-1">Price</div>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">About This Event</h2>
            <div class="prose prose-sm sm:prose max-w-none text-gray-700">
              <p class="whitespace-pre-wrap">{{ event.description }}</p>
            </div>
          </div>

          <!-- Event Details -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-4">Event Details</h2>
            <div class="space-y-3 sm:space-y-4">
              <!-- Date & Time -->
              <div class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">Date & Time</p>
                  <p class="font-semibold text-sm sm:text-base">{{ formatDateTime(event.event_date) }}</p>
                </div>
              </div>

              <!-- Location -->
              <div class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div class="flex-1">
                  <p class="text-xs sm:text-sm text-gray-600">Location</p>
                  <p v-if="event.venue_name" class="font-semibold text-sm sm:text-base">{{ event.venue_name }}</p>
                  <p v-else-if="event.location" class="font-semibold text-sm sm:text-base">{{ event.location }}</p>
                  <p v-if="event.venue_address" class="text-xs sm:text-sm text-gray-700 mt-1">{{ event.venue_address }}</p>
                  <p v-else-if="event.address" class="text-xs sm:text-sm text-gray-700 mt-1">{{ event.address }}</p>
                  <p v-if="event.city" class="text-xs sm:text-sm text-gray-600 mt-0.5">{{ event.city.name }}</p>
                </div>
              </div>

              <!-- Duration -->
              <div v-if="event.duration_hours" class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">Duration</p>
                  <p class="font-semibold text-sm sm:text-base">{{ event.duration_hours }} hours</p>
                </div>
              </div>

              <!-- Capacity -->
              <div v-if="event.max_attendees" class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-burgundy mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <div>
                  <p class="text-xs sm:text-sm text-gray-600">Capacity</p>
                  <p class="font-semibold text-sm sm:text-base">{{ event.max_attendees }} attendees</p>
                  <p class="text-xs text-gray-500">{{ event.rsvp_count || 0 }} registered</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Requirements (if any) -->
          <div v-if="event.requirements" class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h2 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4">Requirements</h2>
            <div class="prose prose-sm sm:prose max-w-none text-gray-700">
              <p class="whitespace-pre-wrap">{{ event.requirements }}</p>
            </div>
          </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="mt-6 lg:mt-0 space-y-4 sm:space-y-6">
          <!-- RSVP Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6 sticky top-4">
            <div class="text-center mb-4 sm:mb-6">
              <div class="text-3xl sm:text-4xl font-bold text-burgundy mb-2">
                {{ event.is_ticketed ? `৳${event.ticket_price}` : 'Free' }}
              </div>
              <p v-if="event.is_ticketed" class="text-xs sm:text-sm text-gray-600">per person</p>
            </div>

            <button
              @click="handleRsvp"
              :disabled="isEventFull"
              :class="`w-full py-3 sm:py-4 rounded-lg font-semibold text-base sm:text-lg transition-colors ${
                isRsvped
                  ? 'bg-green-600 text-white hover:bg-green-700'
                  : isEventFull
                  ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                  : 'bg-burgundy text-white hover:bg-rose-800'
              }`"
            >
              {{ isEventFull ? 'Event Full' : isRsvped ? '✓ Registered' : 'Register Now' }}
            </button>

            <div v-if="!isEventFull" class="mt-3 sm:mt-4 text-center text-xs sm:text-sm text-gray-600">
              {{ event.max_attendees ? `${event.max_attendees - (event.rsvp_count || 0)} spots left` : 'Unlimited spots' }}
            </div>
          </div>

          <!-- Organizer Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">Organized By</h3>
            <div class="flex items-center gap-3 mb-3 sm:mb-4">
              <img
                :src="event.organizer?.profile_image_url || 'https://via.placeholder.com/80'"
                :alt="event.organizer?.user?.name"
                class="w-12 h-12 sm:w-16 sm:h-16 rounded-full object-cover"
              />
              <div>
                <p class="font-semibold text-sm sm:text-base">{{ event.organizer?.user?.name }}</p>
                <p v-if="event.organizer?.specialization" class="text-xs sm:text-sm text-gray-600">
                  {{ event.organizer.specialization }}
                </p>
              </div>
            </div>
            <button
              @click="viewPhotographer"
              class="w-full py-2 border-2 border-burgundy text-burgundy rounded-lg hover:bg-burgundy hover:text-white transition-colors text-sm sm:text-base font-medium"
            >
              View Profile
            </button>
          </div>

          <!-- Share Card -->
          <div class="bg-white rounded-lg shadow-md p-4 sm:p-5 md:p-6">
            <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">Share Event</h3>
            <div class="flex gap-2 sm:gap-3">
              <button
                @click="shareOnFacebook"
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500 transition-colors"
                title="Share on Facebook"
              >
                <svg class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
              </button>
              <button
                @click="shareOnTwitter"
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-colors"
                title="Share on Twitter"
              >
                <svg class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                </svg>
              </button>
              <button
                @click="copyLink"
                class="flex-1 p-2 sm:p-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                title="Copy link"
              >
                <svg class="w-5 h-5 sm:w-6 sm:h-6 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <h2 class="text-2xl font-bold text-gray-800 mb-2">Event Not Found</h2>
      <p class="text-gray-600 mb-4">The event you're looking for doesn't exist</p>
      <button
        @click="$router.push('/events')"
        class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-rose-800"
      >
        Back to Events
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../api';

const router = useRouter();
const route = useRoute();

// State
const event = ref(null);
const loading = ref(true);
const isRsvped = ref(false);

// Computed
const isEventFull = computed(() => {
  if (!event.value || !event.value.max_attendees) return false;
  return (event.value.rsvp_count || 0) >= event.value.max_attendees;
});

// Methods
const fetchEvent = async () => {
  loading.value = true;
  try {
    const slug = route.params.slug;
    const { data } = await api.get(`/events/${slug}`);

    if (data.event || data.data) {
      event.value = data.event || data.data;
      // Check if user has already registered
      isRsvped.value = data.user_registration && data.user_registration.rsvp_status === 'going';
    }
  } catch (error) {
    console.error('Error fetching event:', error);
    event.value = null;
  } finally {
    loading.value = false;
  }
};

const handleRsvp = async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    router.push('/auth');
    return;
  }

  if (isEventFull.value) return;

  try {
    const rsvpStatus = isRsvped.value ? 'not_going' : 'going';
    const { data } = await api.post(`/events/${event.value.slug}/rsvp`);

    if (data.status === 'success') {
      isRsvped.value = !isRsvped.value;
      if (isRsvped.value) {
        event.value.rsvp_count = (event.value.rsvp_count || 0) + 1;
      } else {
        event.value.rsvp_count = Math.max((event.value.rsvp_count || 1) - 1, 0);
      }
    }
  } catch (error) {
    console.error('Error updating RSVP:', error);
    alert(error.response?.data?.message || 'Failed to update RSVP');
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
  alert('Link copied to clipboard!');
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const formatDateTime = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

// Lifecycle
onMounted(() => {
  fetchEvent();
});
</script>
