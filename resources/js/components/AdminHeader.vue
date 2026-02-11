<template>
  <header class="admin-header">
    <div class="admin-header__bg" aria-hidden="true" />
    <div class="admin-header__content px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex items-center justify-between">
        <!-- Left: Menu Toggle + Breadcrumb Preview -->
        <div class="flex items-center space-x-4">
          <!-- Mobile Menu Toggle -->
          <button
            class="admin-icon-btn md:hidden"
            @click="$emit('toggle-sidebar')"
          >
            <svg
              class="block h-6 w-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>

          <!-- Back to Dashboard Button (visible when showBack is true) -->
          <router-link
            v-if="showBackComputed"
            to="/admin/dashboard"
            class="btn-admin-secondary"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"
              />
            </svg>
            <span class="hidden sm:inline">Back to Dashboard</span>
            <span class="sm:hidden">Back</span>
          </router-link>

          <!-- Current Page Title (hidden on mobile) -->
          <div class="hidden md:block">
            <h2 class="text-lg font-semibold admin-page-title">
              {{ pageTitle }}
            </h2>
          </div>
        </div>

        <!-- Center: System Status & Quick Stats -->
        <div
          v-if="systemHealth && systemHealth.status"
          class="hidden lg:flex items-center space-x-6"
        >
          <!-- System Health -->
          <div class="flex items-center space-x-2">
            <div
              :class="[
                'w-3 h-3 rounded-full',
                systemHealth.status === 'healthy'
                  ? 'bg-green-500 animate-pulse'
                  : systemHealth.status === 'warning'
                    ? 'bg-yellow-500'
                    : 'bg-red-500'
              ]"
            />
            <span class="text-sm text-gray-600">
              {{ systemHealth.status === 'healthy' ? '✓ System Healthy' : '⚠ System Issues' }}
            </span>
          </div>

          <!-- Database Status -->
          <div class="flex items-center space-x-2 text-xs text-gray-500">
            <svg
              class="w-4 h-4"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M3 12a9 9 0 1018 0 9 9 0 00-18 0z" />
            </svg>
            <span>{{ systemHealth.database_connections }}/{{ systemHealth.max_connections }} DB</span>
          </div>

          <!-- Cache Status -->
          <div class="flex items-center space-x-2 text-xs text-gray-500">
            <svg
              class="w-4 h-4"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                clip-rule="evenodd"
              />
            </svg>
            <span>Cache: {{ systemHealth.cache_status }}</span>
          </div>
        </div>

        <!-- Right: Actions & User Menu -->
        <div class="flex items-center space-x-4">
          <!-- Go to Main Site Button -->
          <router-link
            to="/"
            class="btn-admin-secondary hidden sm:inline-flex"
            title="Go to Main Site"
          >
            <span>🏠</span>
            <span class="hidden lg:inline">Main Site</span>
          </router-link>

          <!-- Refresh Button -->
          <button
            :disabled="isRefreshing"
            class="admin-icon-btn disabled:opacity-50"
            title="Refresh page"
            @click="handleRefresh"
          >
            <svg
              :class="['h-5 w-5', isRefreshing && 'animate-spin']"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
          </button>

          <!-- Notifications -->
          <div class="relative">
            <button
              class="admin-icon-btn relative"
              title="Notifications"
              @click="showNotifications = !showNotifications"
            >
              <svg
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
              </svg>
              <span
                v-if="notificationCount > 0"
                class="absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
              >
                {{ notificationCount > 9 ? '9+' : notificationCount }}
              </span>
            </button>

            <!-- Notifications Dropdown -->
            <transition name="dropdown">
              <div
                v-if="showNotifications"
                class="admin-dropdown absolute right-0 mt-2 w-80 z-50"
              >
                <div class="max-h-96 overflow-y-auto">
                  <div
                    v-if="notifications.length === 0"
                    class="px-4 py-8 text-center text-gray-500"
                  >
                    <p>No notifications</p>
                  </div>

                  <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                    @click="handleNotificationClick(notification)"
                  >
                    <div class="flex items-start space-x-3">
                      <div
                        :class="[
                          'flex-shrink-0 w-2 h-2 rounded-full mt-1.5',
                          notification.read ? 'bg-gray-300' : 'bg-blue-500'
                        ]"
                      />
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">
                          {{ notification.title }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                          {{ notification.message }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                          {{ formatTime(notification.created_at) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                  <router-link
                    to="/admin/notifications"
                    class="text-sm text-amber-700 hover:text-amber-800 font-medium"
                  >
                    View all notifications
                  </router-link>
                </div>
              </div>
            </transition>
          </div>

          <!-- User Menu -->
          <div class="relative">
            <button
              class="admin-user-btn flex items-center space-x-2 p-2"
              @click="showUserMenu = !showUserMenu"
            >
              <div class="admin-avatar w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                {{ user?.name?.charAt(0).toUpperCase() || 'A' }}
              </div>
              <div class="hidden sm:flex flex-col items-end">
                <span class="text-sm font-medium text-gray-900">{{ user?.name }}</span>
                <span class="text-xs text-gray-500">{{ user?.role }}</span>
              </div>
              <svg
                class="h-4 w-4 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 14l-7-7m0 0L5 14m7-7v12"
                />
              </svg>
            </button>

            <!-- User Menu Dropdown -->
            <transition name="dropdown">
              <div
                v-if="showUserMenu"
                class="admin-dropdown absolute right-0 mt-2 w-56 z-50"
              >
                <div class="px-4 py-3 border-b border-gray-200">
                  <p class="text-sm font-semibold text-gray-900">
                    {{ user?.name }}
                  </p>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ user?.email }}
                  </p>
                  <div class="mt-2">
                    <span class="badge badge-primary">
                      {{ user?.role?.toUpperCase() }}
                    </span>
                  </div>
                </div>

                <div class="px-0 py-2">
                  <router-link
                    to="/admin/profile"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                  >
                    👤 My Profile
                  </router-link>
                  <router-link
                    to="/admin/settings/account"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                  >
                    ⚙️ Account Settings
                  </router-link>
                  <router-link
                    to="/admin/activity-logs"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                  >
                    📋 My Activity
                  </router-link>
                </div>

                <div class="border-t border-gray-200 px-0 py-2">
                  <button
                    class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors"
                    @click="handleLogout"
                  >
                    🚪 Logout
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Page Title -->
    <div
      v-if="pageTitle"
      class="md:hidden px-4 py-2 border-t border-gray-200"
    >
      <h2 class="text-base font-semibold admin-page-title">
        {{ pageTitle }}
      </h2>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../api';
import { formatDate } from '../utils/formatters';

const props = defineProps({
  user: {
    type: Object,
    default: null
  },
  systemHealth: {
    type: Object,
    default: null
  },
  sidebarOpen: {
    type: Boolean,
    default: true
  },
  showBack: {
    type: Boolean,
    default: undefined
  }
});

defineEmits(['toggle-sidebar']);

const router = useRouter();
const route = useRoute();

const showBackComputed = computed(() => {
  if (props.showBack !== undefined) {
    return props.showBack;
  }
  const isDashboard = route.path === '/admin/dashboard' || route.name === 'admin-dashboard';
  return !isDashboard;
});

// State
const showNotifications = ref(false);
const showUserMenu = ref(false);
const isRefreshing = ref(false);
const notifications = ref([]);
const notificationCount = ref(0);
const pageTitle = ref('Dashboard');

// Methods
const handleRefresh = async () => {
  isRefreshing.value = true;
  try {
    // Reload current page data
    location.reload();
  } finally {
    isRefreshing.value = false;
  }
};

const fetchNotifications = async () => {
  try {
    const response = await api.get('/admin/notifications');
    const data = response?.data;
    if (!data || typeof data !== 'object') return;
    notifications.value = data.data || [];
    notificationCount.value = data.unread_count || 0;
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
};

const handleNotificationClick = (notification) => {
  // Mark as read and navigate if applicable
  if (notification.action_url) {
    router.push(notification.action_url);
    showNotifications.value = false;
  }
};

const handleLogout = async () => {
  try {
    await api.post('/auth/logout');
  } catch (error) {
    console.error('Logout error:', error);
  }

  localStorage.removeItem('token');
  localStorage.removeItem('user');
  localStorage.removeItem('user_role');
  router.push('/login');
};

const formatTime = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now - date;
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);

  if (diffMins < 1) return 'Just now';
  if (diffMins < 60) return `${diffMins}m ago`;
  if (diffHours < 24) return `${diffHours}h ago`;
  if (diffDays < 7) return `${diffDays}d ago`;

  return formatDate(date);
};

const updatePageTitle = () => {
  // Update title based on current route
  const routeName = router.currentRoute.value.name;
  const meta = router.currentRoute.value.meta;
  pageTitle.value = meta?.title || 'Admin Dashboard';
};

// Close menus when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('[role="menuitem"]')) {
    showNotifications.value = false;
    showUserMenu.value = false;
  }
};

// Lifecycle
onMounted(() => {
  fetchNotifications();
  updatePageTitle();

  // Set up polling for notifications
  const notificationInterval = setInterval(fetchNotifications, 30000); // Every 30 seconds

  // Watch for route changes
  router.afterEach(() => {
    updatePageTitle();
  });

  document.addEventListener('click', handleClickOutside);

  onBeforeUnmount(() => {
    clearInterval(notificationInterval);
    document.removeEventListener('click', handleClickOutside);
  });
});
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
