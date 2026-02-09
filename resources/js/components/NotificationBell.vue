<template>
  <div
    ref="dropdownRef"
    class="relative"
  >
    <!-- Notification Bell Button -->
    <button
      class="relative p-2 text-gray-600 hover:text-burgundy hover:bg-gray-100 rounded-full transition-colors"
      :class="{ 'bg-gray-100 text-burgundy': isOpen }"
      @click="toggleDropdown"
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
          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
        />
      </svg>
      
      <!-- Unread Badge -->
      <span
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
      >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">
            Notifications
          </h3>
          <button
            v-if="unreadCount > 0"
            class="text-sm text-burgundy hover:underline"
            @click="markAllAsRead"
          >
            Mark all read
          </button>
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="p-8 text-center"
        >
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-burgundy mx-auto" />
          <p class="text-sm text-gray-600 mt-2">
            Loading notifications...
          </p>
        </div>

        <!-- Notifications List -->
        <div
          v-else-if="notifications.length > 0"
          class="max-h-96 overflow-y-auto"
        >
          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer border-b border-gray-100 last:border-b-0"
            :class="{ 'bg-blue-50': !notification.is_read }"
            @click="handleNotificationClick(notification)"
          >
            <div class="flex items-start gap-3">
              <!-- Icon based on type -->
              <div class="flex-shrink-0 mt-1">
                <div
                  class="w-8 h-8 rounded-full flex items-center justify-center"
                  :class="getNotificationIconColor(notification.type)"
                >
                  <svg
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    v-html="getNotificationIcon(notification.type)"
                  />
                </div>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">
                  {{ notification.title }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                  {{ notification.message }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                  {{ formatTime(notification.created_at) }}
                </p>
              </div>

              <!-- Unread indicator -->
              <div
                v-if="!notification.is_read"
                class="flex-shrink-0"
              >
                <div class="w-2 h-2 bg-burgundy rounded-full" />
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="p-8 text-center"
        >
          <svg
            class="w-16 h-16 mx-auto text-gray-400 mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
            />
          </svg>
          <p class="text-gray-600">
            No notifications yet
          </p>
          <p class="text-sm text-gray-500 mt-1">
            We'll notify you when something important happens
          </p>
        </div>

        <!-- Footer -->
        <div
          v-if="notifications.length > 0"
          class="px-4 py-3 border-t border-gray-200 text-center"
        >
          <button
            class="text-sm text-burgundy hover:underline font-medium"
            @click="viewAllNotifications"
          >
            View all notifications
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import api from '@/api';
import { formatDate } from '@/utils/formatters';

const isOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);
const dropdownRef = ref(null);

// Fetch notifications
const fetchNotifications = async () => {
  loading.value = true;
  try {
    const { data } = await api.get('/photographer/notifications', {
      params: { per_page: 10 }
    });
    if (data.status === 'success') {
      notifications.value = data.data;
      unreadCount.value = data.meta?.unread_count || 0;
    }
  } catch (error) {
    console.error('Error fetching notifications:', error);
  } finally {
    loading.value = false;
  }
};

// Fetch unread count
const fetchUnreadCount = async () => {
  try {
    const { data } = await api.get('/photographer/notifications/unread-count');
    if (data.status === 'success') {
      unreadCount.value = data.data.unread_count;
    }
  } catch (error) {
    console.error('Error fetching unread count:', error);
  }
};

// Toggle dropdown
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value && notifications.value.length === 0) {
    fetchNotifications();
  }
};

// Handle notification click
const handleNotificationClick = async (notification) => {
  // Mark as read
  if (!notification.is_read) {
    try {
      await api.post(`/photographer/notifications/${notification.id}/read`);
      notification.is_read = true;
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
      console.error('Error marking notification as read:', error);
    }
  }

  // Navigate if action URL exists
  if (notification.action_url) {
    window.location.href = notification.action_url;
  }

  isOpen.value = false;
};

// Mark all as read
const markAllAsRead = async () => {
  try {
    await api.post('/photographer/notifications/mark-all-read');
    notifications.value.forEach(n => {
      n.is_read = true;
    });
    unreadCount.value = 0;
  } catch (error) {
    console.error('Error marking all as read:', error);
  }
};

// View all notifications (navigate to notifications page)
const viewAllNotifications = () => {
  window.location.href = '/dashboard?tab=notifications';
  isOpen.value = false;
};

// Get notification icon based on type
const getNotificationIcon = (type) => {
  const icons = {
    booking_received: '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>',
    booking_confirmed: '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
    booking_cancelled: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
    review_posted: '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>',
    competition_result: '<path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>',
    event_reminder: '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>',
    default: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>',
  };
  return icons[type] || icons.default;
};

// Get notification icon color
const getNotificationIconColor = (type) => {
  const colors = {
    booking_received: 'bg-blue-100 text-blue-600',
    booking_confirmed: 'bg-green-100 text-green-600',
    booking_cancelled: 'bg-red-100 text-red-600',
    review_posted: 'bg-yellow-100 text-yellow-600',
    competition_result: 'bg-purple-100 text-purple-600',
    competition_voting_started: 'bg-indigo-100 text-indigo-600',
    event_reminder: 'bg-orange-100 text-orange-600',
    default: 'bg-gray-100 text-gray-600',
  };
  return colors[type] || colors.default;
};

// Format time
const formatTime = (timestamp) => {
  const date = new Date(timestamp);
  const now = new Date();
  const diff = Math.floor((now - date) / 1000); // seconds

  if (diff < 60) return 'Just now';
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
  if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
  
  return formatDate(date);
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false;
  }
};

// Poll for new notifications every 30 seconds
let pollInterval;
onMounted(() => {
  fetchUnreadCount();
  document.addEventListener('click', handleClickOutside);
  pollInterval = setInterval(fetchUnreadCount, 30000);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  if (pollInterval) {
    clearInterval(pollInterval);
  }
});
</script>
