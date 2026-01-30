<template>
  <div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Page Header -->
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Notifications</h1>
        <p class="text-gray-600">Stay updated with your bookings, payments, and activity</p>
      </div>
      <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <button
              v-if="unreadCount > 0"
              @click="markAllAsRead"
              class="ml-auto px-4 py-2 text-burgundy hover:text-burgundy-dark transition-colors text-sm font-medium"
            >
              Mark all as read
            </button>
          </div>

          <!-- Filters -->
          <div class="mt-4 flex gap-3">
            <button
              @click="filter = 'all'"
              :class="[
                'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
                filter === 'all'
                  ? 'bg-burgundy text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              All
            </button>
            <button
              @click="filter = 'unread'"
              :class="[
                'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
                filter === 'unread'
                  ? 'bg-burgundy text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Unread {{ unreadCount > 0 ? `(${unreadCount})` : '' }}
            </button>
            <button
              @click="filter = 'read'"
              :class="[
                'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
                filter === 'read'
                  ? 'bg-burgundy text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Read
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="py-12 text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading notifications...</p>
        </div>

        <!-- Notifications List -->
        <div v-else-if="filteredNotifications.length > 0" class="divide-y divide-gray-200">
          <div
            v-for="notification in filteredNotifications"
            :key="notification.id"
            :class="[
              'px-6 py-5 hover:bg-gray-50 transition-colors cursor-pointer',
              !notification.read_at && 'bg-blue-50'
            ]"
            @click="handleNotificationClick(notification)"
          >
            <div class="flex items-start gap-4">
              <!-- Icon -->
              <div
                :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0',
                  getNotificationIcon(notification.type).bg
                ]"
              >
                <svg
                  class="w-5 h-5"
                  :class="getNotificationIcon(notification.type).text"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  v-html="getNotificationIcon(notification.type).path"
                ></svg>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                  <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900">
                      {{ getNotificationTitle(notification) }}
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                      {{ getNotificationMessage(notification) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-2">
                      {{ formatDateTime(notification.created_at) }}
                    </p>
                  </div>

                  <!-- Unread Badge -->
                  <div v-if="!notification.read_at" class="flex-shrink-0">
                    <span class="w-3 h-3 bg-burgundy rounded-full block"></span>
                  </div>
                </div>

                <!-- Action Button -->
                <div class="mt-3" v-if="getNotificationAction(notification)">
                  <router-link
                    :to="getNotificationAction(notification)"
                    class="inline-flex items-center text-burgundy hover:text-burgundy-dark text-sm font-medium"
                  >
                    View Details
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-16 text-center">
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
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
            ></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-1">No notifications</h3>
          <p class="text-gray-600">You're all caught up!</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';

const router = useRouter();
const notifications = ref([]);
const loading = ref(true);
const filter = ref('all');

onMounted(() => {
  loadNotifications();
});

const loadNotifications = async () => {
  loading.value = true;
  try {
    const response = await api.get('/notifications');
    notifications.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load notifications:', error);
  } finally {
    loading.value = false;
  }
};

const filteredNotifications = computed(() => {
  if (filter.value === 'unread') {
    return notifications.value.filter(n => !n.read_at);
  } else if (filter.value === 'read') {
    return notifications.value.filter(n => n.read_at);
  }
  return notifications.value;
});

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.read_at).length;
});

const markAllAsRead = async () => {
  try {
    await api.post('/notifications/mark-all-read');
    notifications.value.forEach(n => {
      n.read_at = new Date().toISOString();
    });
  } catch (error) {
    console.error('Failed to mark all as read:', error);
  }
};

const handleNotificationClick = async (notification) => {
  // Mark as read
  if (!notification.read_at) {
    try {
      await api.post(`/notifications/${notification.id}/read`);
      notification.read_at = new Date().toISOString();
    } catch (error) {
      console.error('Failed to mark as read:', error);
    }
  }

  // Navigate to relevant page
  const action = getNotificationAction(notification);
  if (action) {
    router.push(action);
  }
};

const getNotificationIcon = (type) => {
  const icons = {
    'App\\Notifications\\BookingCreated': {
      bg: 'bg-blue-100',
      text: 'text-blue-600',
      path: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
    },
    'App\\Notifications\\BookingStatusUpdated': {
      bg: 'bg-purple-100',
      text: 'text-purple-600',
      path: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    },
    'App\\Notifications\\PaymentReceived': {
      bg: 'bg-green-100',
      text: 'text-green-600',
      path: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    },
    'App\\Notifications\\ReviewRequest': {
      bg: 'bg-yellow-100',
      text: 'text-yellow-600',
      path: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>',
    },
  };
  return icons[type] || icons['App\\Notifications\\BookingCreated'];
};

const getNotificationTitle = (notification) => {
  const titles = {
    'App\\Notifications\\BookingCreated': 'New Booking Request',
    'App\\Notifications\\BookingStatusUpdated': 'Booking Status Updated',
    'App\\Notifications\\PaymentReceived': 'Payment Received',
    'App\\Notifications\\ReviewRequest': 'Write a Review',
  };
  return titles[notification.type] || 'Notification';
};

const getNotificationMessage = (notification) => {
  const data = notification.data;
  
  if (notification.type === 'App\\Notifications\\BookingCreated') {
    return `Booking for ${data.event_date} at ${data.location}`;
  } else if (notification.type === 'App\\Notifications\\BookingStatusUpdated') {
    return `Your booking with ${data.photographer_name} is now ${data.new_status}`;
  } else if (notification.type === 'App\\Notifications\\PaymentReceived') {
    return `Payment of ৳${Number(data.amount).toLocaleString()} received`;
  } else if (notification.type === 'App\\Notifications\\ReviewRequest') {
    return `Share your experience with ${data.photographer_name}`;
  }
  
  return 'You have a new notification';
};

const getNotificationAction = (notification) => {
  const data = notification.data;
  
  if (notification.type === 'App\\Notifications\\BookingCreated' || 
      notification.type === 'App\\Notifications\\BookingStatusUpdated') {
    return '/bookings';
  } else if (notification.type === 'App\\Notifications\\PaymentReceived') {
    return '/transactions';
  } else if (notification.type === 'App\\Notifications\\ReviewRequest') {
    return `/review/${data.photographer_id}`;
  }
  
  return null;
};

const formatDateTime = (date) => {
  const now = new Date();
  const notifDate = new Date(date);
  const diff = Math.floor((now - notifDate) / 1000); // seconds

  if (diff < 60) return 'Just now';
  if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
  if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`;
  
  return notifDate.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: notifDate.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
  });
};
</script>
