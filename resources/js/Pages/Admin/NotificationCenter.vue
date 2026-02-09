<template>
  <div class="min-h-screen bg-gray-50">
    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="closeToast"
    />

    <AdminHeader 
      title="🔔 Notification Center" 
      subtitle="Manage Platform Notifications"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />
          
          <!-- Stats Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500">
                    Total Notifications
                  </p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{ stats.total }}
                  </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                  <svg
                    class="w-6 h-6 text-blue-600"
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
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500">
                    Unread
                  </p>
                  <p class="text-2xl font-bold text-orange-600">
                    {{ stats.unread }}
                  </p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                  <svg
                    class="w-6 h-6 text-orange-600"
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
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500">
                    Today
                  </p>
                  <p class="text-2xl font-bold text-green-600">
                    {{ stats.today }}
                  </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                  <svg
                    class="w-6 h-6 text-green-600"
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
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-500">
                    This Week
                  </p>
                  <p class="text-2xl font-bold text-purple-600">
                    {{ stats.week }}
                  </p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                  <svg
                    class="w-6 h-6 text-purple-600"
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
                </div>
              </div>
            </div>
          </div>

          <!-- Filters and Actions -->
          <div class="bg-white rounded-lg shadow mb-6 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
              <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">Filter:</label>
                <select
                  v-model="filters.type"
                  class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                  @change="loadNotifications"
                >
                  <option value="">
                    All Types
                  </option>
                  <option value="booking">
                    Booking
                  </option>
                  <option value="payment">
                    Payment
                  </option>
                  <option value="tip">
                    Tip
                  </option>
                  <option value="review">
                    Review
                  </option>
                  <option value="verification">
                    Verification
                  </option>
                  <option value="competition">
                    Competition
                  </option>
                  <option value="system">
                    System
                  </option>
                </select>

                <select
                  v-model="filters.status"
                  class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                  @change="loadNotifications"
                >
                  <option value="">
                    All Status
                  </option>
                  <option value="unread">
                    Unread Only
                  </option>
                  <option value="read">
                    Read Only
                  </option>
                </select>

                <select
                  v-model="filters.period"
                  class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                  @change="loadNotifications"
                >
                  <option value="all">
                    All Time
                  </option>
                  <option value="today">
                    Today
                  </option>
                  <option value="week">
                    This Week
                  </option>
                  <option value="month">
                    This Month
                  </option>
                </select>
              </div>

              <div class="flex items-center gap-2">
                <button
                  :disabled="stats.unread === 0"
                  class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                  @click="markAllAsRead"
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
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  Mark All Read
                </button>

                <button
                  class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center gap-2"
                  @click="deleteRead"
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
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                  Delete Read
                </button>
              </div>
            </div>
          </div>

          <!-- Loading -->
          <div
            v-if="loading"
            class="text-center py-12"
          >
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto" />
            <p class="mt-4 text-gray-600">
              Loading notifications...
            </p>
          </div>

          <!-- Notifications List -->
          <div
            v-else-if="notifications.length > 0"
            class="space-y-3"
          >
            <div
              v-for="notification in notifications"
              :key="notification.id"
              :class="[
                'bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow cursor-pointer',
                notification.read_at ? 'opacity-75' : 'border-l-4 border-primary-500'
              ]"
            >
              <div class="flex items-start justify-between">
                <div
                  class="flex items-start gap-3 flex-1"
                  @click="viewNotification(notification)"
                >
                  <!-- Icon -->
                  <div :class="getNotificationIconClass(notification.type)">
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                      v-html="getNotificationIcon(notification.type)"
                    />
                  </div>

                  <!-- Content -->
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                      <span :class="getTypeClass(notification.type)">
                        {{ formatType(notification.type) }}
                      </span>
                      <span
                        v-if="!notification.read_at"
                        class="px-2 py-0.5 bg-primary-100 text-primary-700 text-xs font-medium rounded-full"
                      >
                        NEW
                      </span>
                    </div>

                    <h4 class="text-base font-semibold text-gray-900 mb-1">
                      {{ notification.data.title || 'Notification' }}
                    </h4>

                    <p class="text-sm text-gray-600 mb-2">
                      {{ notification.data.message }}
                    </p>

                    <div class="flex items-center gap-4 text-xs text-gray-500">
                      <span class="flex items-center gap-1">
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
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                          />
                        </svg>
                        {{ formatTime(notification.created_at) }}
                      </span>
                      <span
                        v-if="notification.data.user_name"
                        class="flex items-center gap-1"
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
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                          />
                        </svg>
                        {{ notification.data.user_name }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 ml-4">
                  <button
                    v-if="!notification.read_at"
                    title="Mark as read"
                    class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors"
                    @click.stop="markAsRead(notification.id)"
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
                        d="M5 13l4 4L19 7"
                      />
                    </svg>
                  </button>

                  <button
                    title="Delete"
                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors"
                    @click.stop="deleteNotification(notification.id)"
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
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div
            v-else
            class="bg-white rounded-lg shadow p-12 text-center"
          >
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg
                class="w-8 h-8 text-gray-400"
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
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              No Notifications
            </h3>
            <p class="text-gray-500">
              No notifications match your current filters.
            </p>
          </div>
    </div>
  </div>
  <!-- End min-h-screen -->
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../../api';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import Toast from '../../components/ui/Toast.vue';
import { formatDate } from '../../utils/formatters';

const loading = ref(true);
const notifications = ref([]);
const stats = ref({
  total: 0,
  unread: 0,
  today: 0,
  week: 0
});

const filters = ref({
  type: '',
  status: '',
  period: 'all'
});

// Toast state
const toastVisible = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const showToast = (message, type = 'success') => {
  toastMessage.value = message;
  toastType.value = type;
  toastVisible.value = true;
};

const closeToast = () => {
  toastVisible.value = false;
};

const loadNotifications = async () => {
  try {
    loading.value = true;
    const response = await api.get('/admin/notifications', {
      params: filters.value
    });
    
    if (response.data.status === 'success') {
      notifications.value = response.data.data.notifications;
      stats.value = response.data.data.stats;
    }
  } catch (error) {
    console.error('Failed to load notifications:', error);
    showToast(error.response?.data?.message || 'Failed to load notifications', 'error');
  } finally {
    loading.value = false;
  }
};

const markAsRead = async (id) => {
  try {
    const response = await api.post(`/admin/notifications/${id}/mark-read`);
    
    if (response.data.status === 'success') {
      const notification = notifications.value.find(n => n.id === id);
      if (notification) {
        notification.read_at = new Date().toISOString();
        stats.value.unread = Math.max(0, stats.value.unread - 1);
      }
      showToast('Notification marked as read');
    }
  } catch (error) {
    console.error('Failed to mark as read:', error);
    showToast(error.response?.data?.message || 'Failed to mark as read', 'error');
  }
};

const markAllAsRead = async () => {
  try {
    const response = await api.post('/admin/notifications/mark-all-read');
    
    if (response.data.status === 'success') {
      notifications.value.forEach(n => {
        n.read_at = new Date().toISOString();
      });
      stats.value.unread = 0;
      showToast('All notifications marked as read');
    }
  } catch (error) {
    console.error('Failed to mark all as read:', error);
    showToast(error.response?.data?.message || 'Failed to mark all as read', 'error');
  }
};

const deleteNotification = async (id) => {
  if (!confirm('Are you sure you want to delete this notification?')) return;
  
  try {
    const response = await api.delete(`/admin/notifications/${id}`);
    
    if (response.data.status === 'success') {
      const index = notifications.value.findIndex(n => n.id === id);
      if (index !== -1) {
        const notification = notifications.value[index];
        if (!notification.read_at) {
          stats.value.unread = Math.max(0, stats.value.unread - 1);
        }
        notifications.value.splice(index, 1);
        stats.value.total = Math.max(0, stats.value.total - 1);
      }
      showToast('Notification deleted');
    }
  } catch (error) {
    console.error('Failed to delete notification:', error);
    showToast(error.response?.data?.message || 'Failed to delete notification', 'error');
  }
};

const deleteRead = async () => {
  if (!confirm('Are you sure you want to delete all read notifications?')) return;
  
  try {
    const response = await api.delete('/admin/notifications/delete-read');
    
    if (response.data.status === 'success') {
      await loadNotifications();
      showToast('Read notifications deleted');
    }
  } catch (error) {
    console.error('Failed to delete read notifications:', error);
    showToast(error.response?.data?.message || 'Failed to delete read notifications', 'error');
  }
};

const viewNotification = (notification) => {
  if (!notification.read_at) {
    markAsRead(notification.id);
  }
  
  // Navigate to related page if URL is provided
  if (notification.data.url) {
    window.location.href = notification.data.url;
  }
};

const formatType = (type) => {
  return type.charAt(0).toUpperCase() + type.slice(1);
};

const getTypeClass = (type) => {
  const classes = {
    booking: 'px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full',
    payment: 'px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full',
    tip: 'px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full',
    review: 'px-2 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full',
    verification: 'px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full',
    competition: 'px-2 py-1 bg-pink-100 text-pink-700 text-xs font-medium rounded-full',
    system: 'px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full'
  };
  return classes[type] || classes.system;
};

const getNotificationIconClass = (type) => {
  const classes = {
    booking: 'p-2 bg-blue-100 rounded-full',
    payment: 'p-2 bg-green-100 rounded-full',
    tip: 'p-2 bg-yellow-100 rounded-full',
    review: 'p-2 bg-purple-100 rounded-full',
    verification: 'p-2 bg-indigo-100 rounded-full',
    competition: 'p-2 bg-pink-100 rounded-full',
    system: 'p-2 bg-gray-100 rounded-full'
  };
  return classes[type] || classes.system;
};

const getNotificationIcon = (type) => {
  const icons = {
    booking: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
    payment: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />',
    tip: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    review: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />',
    verification: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
    competition: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />',
    system: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
  };
  return icons[type] || icons.system;
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
  
  return formatDate(date);
};

onMounted(() => {
  loadNotifications();
});
</script>

<style scoped>
/* Custom styles if needed */
</style>
