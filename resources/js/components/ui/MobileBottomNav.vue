<template>
  <!-- Mobile Bottom Navigation (visible only on mobile) -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden safe-area-bottom">
    <div
      class="grid h-16"
      :class="showVerification ? 'grid-cols-6' : 'grid-cols-5'"
    >
      <!-- Home -->
      <router-link
        to="/"
        class="flex flex-col items-center justify-center gap-1 transition-colors"
        :class="isActive('/') ? 'text-burgundy' : 'text-gray-500'"
      >
        <svg
          class="w-6 h-6"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
        </svg>
        <span class="text-xs font-medium">Home</span>
      </router-link>

      <!-- Browse Photographers -->
      <router-link
        to="/photographers"
        class="flex flex-col items-center justify-center gap-1 transition-colors"
        :class="isActive('/photographers') ? 'text-burgundy' : 'text-gray-500'"
      >
        <svg
          class="w-6 h-6"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
            clip-rule="evenodd"
          />
        </svg>
        <span class="text-xs font-medium">Browse</span>
      </router-link>

      <!-- Competitions -->
      <router-link
        to="/competitions"
        class="flex flex-col items-center justify-center gap-1 transition-colors"
        :class="isActive('/competitions') ? 'text-burgundy' : 'text-gray-500'"
      >
        <svg
          class="w-6 h-6"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
        <span class="text-xs font-medium">Compete</span>
        <!-- Active Competitions Badge -->
        <span
          v-if="activeCompetitionsCount > 0"
          class="absolute top-1 right-1/4 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"
        >
          {{ activeCompetitionsCount > 9 ? '9+' : activeCompetitionsCount }}
        </span>
      </router-link>

      <!-- Events -->
      <router-link
        to="/events"
        class="flex flex-col items-center justify-center gap-1 transition-colors relative"
        :class="isActive('/events') ? 'text-burgundy' : 'text-gray-500'"
      >
        <svg
          class="w-6 h-6"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
            clip-rule="evenodd"
          />
        </svg>
        <span class="text-xs font-medium">Events</span>
      </router-link>

      <!-- Verification (Photographers only) -->
      <router-link
        v-if="showVerification"
        to="/verification"
        class="flex flex-col items-center justify-center gap-1 transition-colors"
        :class="isActive('/verification') ? 'text-burgundy' : 'text-gray-500'"
      >
        <svg
          class="w-6 h-6"
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
        <span class="text-xs font-medium">Verify</span>
      </router-link>

      <!-- Account / Profile -->
      <router-link
        v-if="isLoggedIn"
        :to="dashboardLink"
        class="flex flex-col items-center justify-center gap-1 transition-colors relative"
        :class="isActive('/dashboard') || isActive('/photographer-dashboard') || isActive('/judge-dashboard') || isActive('/admin') ? 'text-burgundy' : 'text-gray-500'"
      >
        <svg
          class="w-6 h-6"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
            clip-rule="evenodd"
          />
        </svg>
        <span class="text-xs font-medium">Account</span>
        <!-- Notification Badge -->
        <span
          v-if="unreadNotifications > 0"
          class="absolute top-1 right-1/4 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"
        >
          {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
        </span>
      </router-link>

      <!-- Login (if not logged in) -->
      <router-link
        v-else
        to="/auth"
        class="flex flex-col items-center justify-center gap-1 transition-colors"
        :class="isActive('/auth') ? 'text-burgundy' : 'text-gray-500'"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
          />
        </svg>
        <span class="text-xs font-medium">Login</span>
      </router-link>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

// Props that can be passed from parent
const props = defineProps({
  isLoggedIn: {
    type: Boolean,
    default: false,
  },
  userRole: {
    type: String,
    default: null, // 'client', 'photographer', 'judge', 'admin'
  },
  unreadNotifications: {
    type: Number,
    default: 0,
  },
  activeCompetitionsCount: {
    type: Number,
    default: 0,
  },
});

const dashboardLink = computed(() => {
  switch (props.userRole) {
    case 'admin':
      return '/admin';
    case 'photographer':
      return '/photographer-dashboard';
    case 'judge':
      return '/judge-dashboard';
    default:
      return '/dashboard';
  }
});

const showVerification = computed(() => {
  return props.isLoggedIn && props.userRole === 'photographer';
});

const isActive = (path) => {
  if (path === '/') {
    return route.path === '/';
  }
  return route.path.startsWith(path);
};
</script>

<style scoped>
/* iOS safe area support */
.safe-area-bottom {
  padding-bottom: env(safe-area-inset-bottom);
}

/* Add padding to body when bottom nav is present (prevents content from being hidden) */
/* This should be added to your main app layout */
</style>
