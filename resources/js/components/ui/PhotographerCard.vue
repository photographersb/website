<template>
  <div
    class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 overflow-visible border border-gray-100 cursor-pointer transform hover:-translate-y-1 flex flex-col h-full"
    @click="$emit('click', photographer)"
  >
    <!-- Circular Image Container -->
    <div class="relative h-40 md:h-48 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 flex-shrink-0 flex items-center justify-center">
      <!-- Circular Profile Picture -->
      <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white flex-shrink-0">
        <img
          :src="profileImage"
          :alt="photographer.name || photographer.business_name"
          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
          loading="lazy"
          @error="handleAvatarError"
        >
      </div>
      
      <!-- Verified Badge -->
      <div
        v-if="photographer.is_verified"
        class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1 shadow-lg"
      >
        <svg
          class="w-3 h-3"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        Verified
      </div>
      
      <!-- Available Now Badge -->
      <div
        v-if="photographer.is_available || photographer.response_time_hours < 2"
        class="absolute top-3 right-3 bg-yellow-400 text-gray-900 px-2 py-1 rounded-full text-xs font-bold animate-pulse"
      >
        ⚡ Available
      </div>

      <!-- Favorite Button -->
      <button
        v-if="showFavorite"
        class="absolute top-3 left-3 w-9 h-9 rounded-full flex items-center justify-center shadow-lg transition-all"
        :class="isFavorite ? 'bg-[#7a1f2b] text-white' : 'bg-white text-[#7a1f2b] hover:scale-105'"
        aria-label="Toggle favorite"
        @click.stop="$emit('toggle-favorite', photographer.id)"
      >
        <svg
          class="w-4 h-4"
          :fill="isFavorite ? 'currentColor' : 'none'"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3.172 5.172a4 4 0 015.656 0L12 8.343l3.172-3.171a4 4 0 115.656 5.656L12 21.343l-8.828-8.829a4 4 0 010-5.656z"
          />
        </svg>
      </button>
    </div>

    <!-- Content -->
    <div class="p-4 md:p-5 flex flex-col flex-1">
      <!-- Name & Rating -->
      <div class="text-center mb-3">
        <h3 class="font-bold text-lg md:text-xl text-gray-900 line-clamp-1 group-hover:text-primary-700 transition-colors">
          {{ photographer.user?.name || photographer.business_name || 'Unknown Photographer' }}
        </h3>
      </div>
      
      <!-- Rating -->
      <div class="flex items-center justify-center gap-2 mb-3">
        <div class="flex items-center">
          <svg
            v-for="i in 5"
            :key="i"
            :class="i <= Math.round(averageRating) ? 'text-yellow-400' : 'text-gray-300'"
            class="w-4 h-4"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
        </div>
        <span class="text-sm font-semibold text-gray-700">{{ formatFixed(averageRating, 1, '0.0') }}</span>
        <span class="text-sm text-gray-500">({{ ratingCount }})</span>
      </div>
      
      <!-- Location & Category -->
      <div class="flex flex-col items-center gap-2 mb-3">
        <LocationBadge
          v-if="locationName && locationSlug"
          :location-name="locationName"
          :location-slug="locationSlug"
          size="sm"
          variant="soft"
          @click.stop
        />
        <span
          v-else-if="locationName"
          class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-700"
        >
          <svg
            class="w-3.5 h-3.5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path d="M5.05 3.05a7 7 0 019.9 9.9L10 18l-4.95-5.05a7 7 0 010-9.9z" />
          </svg>
          {{ locationName }}
        </span>
        <CategoryBadge
          v-if="primaryCategory && primaryCategorySlug"
          :category-name="primaryCategory"
          :category-slug="primaryCategorySlug"
          size="sm"
          variant="soft"
          @click.stop
        />
        <span
          v-else-if="primaryCategory"
          class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700"
        >
          <svg
            class="w-3.5 h-3.5"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
          </svg>
          {{ primaryCategory }}
        </span>
      </div>
      
      <!-- Key Info Row -->
      <div
        v-if="responseTime"
        class="bg-blue-50 px-2 py-1 rounded flex items-center justify-center gap-1 mb-3 text-xs"
      >
        <span class="text-blue-700">⏱️ {{ responseTime }}</span>
      </div>
      
      <!-- Bio Preview -->
      <p
        v-if="photographer.bio"
        class="text-sm text-gray-600 mb-3 line-clamp-2 leading-relaxed flex-1 min-h-10 text-center"
      >
        {{ photographer.bio }}
      </p>
      
      <!-- Price & CTA - Sticky at bottom -->
      <div class="flex flex-col items-center gap-2 mt-auto pt-3 border-t border-gray-100">
        <div
          v-if="startingPrice"
          class="text-center"
        >
          <div class="text-xs text-gray-500">
            Starting from
          </div>
          <div class="text-lg md:text-xl font-bold text-primary-700">
            ৳{{ formatNumber(startingPrice) }}
          </div>
        </div>
        <button
          class="w-full px-4 py-2 bg-primary-700 text-white rounded-lg text-sm font-semibold hover:bg-primary-800 transition-all hover:scale-105 shadow-md"
          @click.stop="$emit('book', photographer)"
        >
          {{ ctaText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import CategoryBadge from './CategoryBadge.vue';
import LocationBadge from './LocationBadge.vue';
import { formatFixed, formatNumber } from '../../utils/formatters';

const router = useRouter();

const props = defineProps({
  photographer: {
    type: Object,
    required: true,
  },
  ctaText: {
    type: String,
    default: 'View Profile',
  },
  showFavorite: {
    type: Boolean,
    default: false,
  },
  isFavorite: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['click', 'book', 'toggle-favorite']);

const categoriesCount = computed(() => {
  return props.photographer.categories?.length || 0;
});

const primaryCategorySlug = computed(() => {
  if (props.photographer.categories && props.photographer.categories.length > 0) {
    return props.photographer.categories[0].slug || '';
  }
  return '';
});

const locationSlug = computed(() => {
  if (props.photographer.city?.slug) {
    return props.photographer.city.slug;
  }
  if (props.photographer.district?.slug) {
    return props.photographer.district.slug;
  }
  return '';
});

// Computed properties for safe data access
const averageRating = computed(() => {
  return Number(props.photographer.average_rating || 0);
});

const ratingCount = computed(() => {
  return Number(props.photographer.rating_count || props.photographer.review_count || 0);
});

const responseTime = computed(() => {
  const hours = props.photographer.response_time_hours || props.photographer.average_response_time;
  if (!hours) return null;
  return hours < 24 ? `${hours}h response` : `${Math.round(hours / 24)}d response`;
});

const completedJobs = computed(() => {
  return Number(props.photographer.completed_bookings || props.photographer.completed_jobs || 0);
});

const startingPrice = computed(() => {
  return Number(props.photographer.starting_price || props.photographer.base_price || 0);
});

const primaryCategory = computed(() => {
  if (props.photographer.primary_category) return props.photographer.primary_category;
  if (props.photographer.categories && props.photographer.categories.length > 0) {
    return props.photographer.categories[0].name;
  }
  return null;
});

const fallbackAvatar = '/images/default-avatar.png';

const profileImage = computed(() => {
  const raw = props.photographer.profile_picture_url
    || props.photographer.avatar
    || props.photographer.profile_picture
    || '';
  if (!raw) return fallbackAvatar;
  if (raw.startsWith('http') || raw.startsWith('/') || raw.startsWith('data:')) return raw;
  return `/storage/${raw.replace(/^\/+/, '')}`;
});

const handleAvatarError = (event) => {
  event.target.src = fallbackAvatar;
};

const locationName = computed(() => {
  let location = props.photographer.location || props.photographer.district || props.photographer.city;
  
  // Handle if location is an object (has .name property)
  if (location && typeof location === 'object') {
    return location.name || location.title || 'Bangladesh';
  }
  
  // Handle if location is a string
  if (location && typeof location === 'string') {
    return location;
  }
  
  return 'Bangladesh';
});
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
