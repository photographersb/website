<template>
  <div class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex items-center justify-between">
        <!-- Left: Back Button + Title -->
        <div class="flex items-center space-x-4">
          <button 
            v-if="showBack"
            @click="goBack" 
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
          </button>

          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
            <p v-if="subtitle" class="text-sm text-gray-600 mt-0.5">{{ subtitle }}</p>
          </div>
        </div>

        <!-- Right: Dashboard + Notifications + User Menu -->
        <div class="flex items-center space-x-4">
          <!-- Dashboard Link -->
          <router-link 
            v-if="!isDashboard"
            to="/admin/dashboard" 
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
          </router-link>

          <!-- Notifications Dropdown -->
          <div class="relative">
            <button 
              @click="toggleNotifications"
              class="relative inline-flex items-center p-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <!-- Badge for unread count -->
              <span 
                v-if="unreadCount > 0" 
                class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-burgundy rounded-full"
              >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
              </span>
            </button>

            <!-- Notifications Dropdown Panel -->
            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <div 
                v-if="showNotifications"
                class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                @click.stop
              >
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                  <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                  <button 
                    v-if="notifications.length > 0"
                    @click="markAllRead"
                    class="text-xs text-burgundy hover:text-burgundy-dark font-medium"
                  >
                    Mark all read
                  </button>
                </div>

                <!-- Notifications List -->
                <div class="max-h-96 overflow-y-auto">
                  <div v-if="loadingNotifications" class="px-4 py-8 text-center text-gray-500">
                    <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-burgundy" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading...
                  </div>

                  <div v-else-if="notifications.length === 0" class="px-4 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    No notifications
                  </div>

                  <div v-else>
                    <div 
                      v-for="notification in notifications"
                      :key="notification.id"
                      @click="handleNotificationClick(notification)"
                      :class="[
                        'px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors',
                        !notification.read_at ? 'bg-primary-50' : ''
                      ]"
                    >
                      <div class="flex items-start">
                        <div :class="[
                          'flex-shrink-0 w-2 h-2 mt-2 rounded-full',
                          !notification.read_at ? 'bg-burgundy' : 'bg-gray-300'
                        ]"></div>
                        <div class="ml-3 flex-1">
                          <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                          <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                          <p class="text-xs text-gray-400 mt-1">{{ formatTime(notification.created_at) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-gray-200 text-center">
                  <router-link 
                    to="/admin/notifications" 
                    class="text-sm font-medium text-burgundy hover:text-burgundy-dark"
                    @click="showNotifications = false"
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
              @click="toggleUserMenu"
              class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              {{ userName }}
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- User Dropdown -->
            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <div 
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1"
              >
                <router-link 
                  to="/admin/dashboard" 
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false"
                >
                  📊 Dashboard
                </router-link>
                <router-link 
                  to="/admin/settings" 
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false"
                >
                  ⚙️ Settings
                </router-link>
                <hr class="my-1 border-gray-200">
                <button 
                  @click="logout"
                  class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                >
                  🚪 Logout
                </button>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../api';

export default {
  name: 'AdminHeader',
  props: {
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String,
      default: ''
    },
    showBack: {
      type: Boolean,
      default: true
    }
  },
  setup() {
    const router = useRouter();
    const route = useRoute();
    
    const showNotifications = ref(false);
    const showUserMenu = ref(false);
    const notifications = ref([]);
    const loadingNotifications = ref(false);
    const unreadCount = ref(0);

    const userName = computed(() => {
      const user = JSON.parse(localStorage.getItem('user') || '{}');
      return user.name || 'Admin';
    });

    const isDashboard = computed(() => {
      return route.path === '/admin/dashboard';
    });

    const goBack = () => {
      if (window.history.length > 1) {
        router.back();
      } else {
        router.push('/admin/dashboard');
      }
    };

    const toggleNotifications = () => {
      showNotifications.value = !showNotifications.value;
      showUserMenu.value = false;
      if (showNotifications.value && notifications.value.length === 0) {
        fetchNotifications();
      }
    };

    const toggleUserMenu = () => {
      showUserMenu.value = !showUserMenu.value;
      showNotifications.value = false;
    };

    const fetchNotifications = async () => {
      loadingNotifications.value = true;
      try {
        const { data } = await api.get('/notifications?limit=10');
        if (data.status === 'success') {
          notifications.value = data.data;
          unreadCount.value = data.unread_count || 0;
        }
      } catch (error) {
        console.error('Error fetching notifications:', error);
      } finally {
        loadingNotifications.value = false;
      }
    };

    const markAllRead = async () => {
      try {
        await api.post('/notifications/mark-all-read');
        notifications.value.forEach(n => n.read_at = new Date().toISOString());
        unreadCount.value = 0;
      } catch (error) {
        console.error('Error marking notifications as read:', error);
      }
    };

    const handleNotificationClick = async (notification) => {
      // Mark as read
      if (!notification.read_at) {
        try {
          await api.put(`/notifications/${notification.id}/read`);
          notification.read_at = new Date().toISOString();
          unreadCount.value = Math.max(0, unreadCount.value - 1);
        } catch (error) {
          console.error('Error marking notification as read:', error);
        }
      }

      // Navigate if has link
      if (notification.action_url) {
        showNotifications.value = false;
        router.push(notification.action_url);
      }
    };

    const formatTime = (timestamp) => {
      const date = new Date(timestamp);
      const now = new Date();
      const diff = now - date;
      const minutes = Math.floor(diff / 60000);
      const hours = Math.floor(diff / 3600000);
      const days = Math.floor(diff / 86400000);

      if (minutes < 1) return 'Just now';
      if (minutes < 60) return `${minutes}m ago`;
      if (hours < 24) return `${hours}h ago`;
      if (days < 7) return `${days}d ago`;
      return date.toLocaleDateString();
    };

    const logout = () => {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      router.push('/auth');
    };

    // Close dropdowns when clicking outside
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        showNotifications.value = false;
        showUserMenu.value = false;
      }
    };

    onMounted(() => {
      document.addEventListener('click', handleClickOutside);
      // Fetch initial unread count
      fetchNotifications();
    });

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
    });

    return {
      showNotifications,
      showUserMenu,
      notifications,
      loadingNotifications,
      unreadCount,
      userName,
      isDashboard,
      goBack,
      toggleNotifications,
      toggleUserMenu,
      markAllRead,
      handleNotificationClick,
      formatTime,
      logout
    };
  }
}
</script>

<style scoped>
/* Add any additional styles if needed */
</style>
