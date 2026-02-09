<template>
  <AdminLayout 
    page-title="Notifications"
    page-description="View and manage system notifications"
    :show-breadcrumbs="true"
  >
    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search notifications..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
        <select
          v-model="typeFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="">
            All Types
          </option>
          <option value="alert">
            Alert
          </option>
          <option value="warning">
            Warning
          </option>
          <option value="success">
            Success
          </option>
          <option value="info">
            Info
          </option>
        </select>
        <select
          v-model="readFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="">
            All Status
          </option>
          <option value="unread">
            Unread
          </option>
          <option value="read">
            Read
          </option>
        </select>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div
          v-if="isLoading"
          class="p-8 text-center"
        >
          Loading notifications...
        </div>
        <div
          v-else-if="filteredNotifications.length === 0"
          class="p-12 text-center text-gray-500"
        >
          No notifications found
        </div>
        <div
          v-else
          class="divide-y"
        >
          <div
            v-for="notification in filteredNotifications"
            :key="notification.id"
            class="p-4 hover:bg-gray-50 cursor-pointer border-l-4"
            :class="getNotificationBorderColor(notification.type)"
            @click="markAsRead(notification)"
          >
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <p class="font-medium text-gray-900">
                  {{ notification.title }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                  {{ notification.message }}
                </p>
              </div>
              <div class="text-right ml-4">
                <span :class="`px-2 py-1 rounded text-xs font-medium ${getNotificationTypeColor(notification.type)}`">
                  {{ notification.type }}
                </span>
                <p class="text-xs text-gray-500 mt-2">
                  {{ formatDate(notification.created_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from '../../AdminLayout.vue';
import { formatDate as formatDateValue } from '../../../utils/formatters';

const notifications = ref([]);
const isLoading = ref(false);
const searchQuery = ref('');
const typeFilter = ref('');
const readFilter = ref('');
const addAlert = inject('addAlert', null);

const filteredNotifications = computed(() => {
  let filtered = notifications.value;
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(n => n.title.toLowerCase().includes(q) || n.message.toLowerCase().includes(q));
  }
  
  if (typeFilter.value) filtered = filtered.filter(n => n.type === typeFilter.value);
  
  if (readFilter.value === 'unread') {
    filtered = filtered.filter(n => !n.read_at);
  } else if (readFilter.value === 'read') {
    filtered = filtered.filter(n => n.read_at);
  }
  
  return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const getNotificationTypeColor = (type) => {
  const colors = {
    alert: 'bg-red-100 text-red-800',
    warning: 'bg-yellow-100 text-yellow-800',
    success: 'bg-green-100 text-green-800',
    info: 'bg-blue-100 text-blue-800',
  };
  return colors[type] || 'bg-gray-100 text-gray-800';
};

const getNotificationBorderColor = (type) => {
  const colors = {
    alert: 'border-red-500',
    warning: 'border-yellow-500',
    success: 'border-green-500',
    info: 'border-blue-500',
  };
  return colors[type] || 'border-gray-300';
};

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  const now = new Date();
  const diff = now - d;
  
  if (diff < 60000) return 'just now';
  if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
  if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
  if (diff < 604800000) return Math.floor(diff / 86400000) + 'd ago';
  
  return formatDateValue(d);
};

const fetchNotifications = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/notifications', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (response.ok) {
      const data = await response.json();
      notifications.value = data.data || [];
    }
  } catch (error) {
    console.error('Error:', error);
    if (addAlert) addAlert('Failed to load notifications', 'error');
  } finally {
    isLoading.value = false;
  }
};

const markAsRead = async (notification) => {
  if (notification.read_at) return;
  
  try {
    const response = await fetch(`/api/v1/admin/notifications/${notification.id}/read`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (response.ok) {
      notification.read_at = new Date().toISOString();
    }
  } catch (error) {
    console.error('Error marking notification as read:', error);
  }
};

onMounted(fetchNotifications);
</script>
