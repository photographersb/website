<template>
  <AdminLayout 
    page-title="Activity Logs"
    page-description="System activity and audit logs"
    :show-breadcrumbs="true"
  >
    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search activity..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
        <select
          v-model="actionFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="">
            All Actions
          </option>
          <option value="create">
            Create
          </option>
          <option value="update">
            Update
          </option>
          <option value="delete">
            Delete
          </option>
          <option value="login">
            Login
          </option>
        </select>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <div
          v-if="isLoading"
          class="p-8 text-center"
        >
          Loading activity logs...
        </div>
        <div
          v-else-if="filteredActivities.length === 0"
          class="p-12 text-center text-gray-500"
        >
          No activities found
        </div>
        <table
          v-else
          class="w-full text-sm"
        >
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                User
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Action
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Entity
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Details
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Timestamp
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                IP Address
              </th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="activity in filteredActivities"
              :key="activity.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4">
                {{ activity.user_name }}
              </td>
              <td class="px-6 py-4">
                <span :class="`px-3 py-1 rounded-full text-xs font-medium ${getActionColor(activity.action)}`">
                  {{ activity.action }}
                </span>
              </td>
              <td class="px-6 py-4 text-gray-700">
                {{ activity.entity_type }}
              </td>
              <td
                class="px-6 py-4 text-gray-600 max-w-xs truncate"
                :title="activity.details"
              >
                {{ activity.details }}
              </td>
              <td class="px-6 py-4 text-gray-500 text-xs">
                {{ formatDate(activity.created_at) }}
              </td>
              <td class="px-6 py-4 font-mono text-xs">
                {{ activity.ip_address }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        v-if="filteredActivities.length > 0"
        class="flex justify-center gap-2"
      >
        <button
          class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          @click="currentPage > 1 && (currentPage--)"
        >
          Previous
        </button>
        <span class="px-4 py-2">Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          @click="currentPage < totalPages && (currentPage++)"
        >
          Next
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from '../../AdminLayout.vue';
import { formatDateTimeSeconds } from '../../../utils/formatters';

const activities = ref([]);
const isLoading = ref(false);
const searchQuery = ref('');
const actionFilter = ref('');
const currentPage = ref(1);
const itemsPerPage = 20;
const addAlert = inject('addAlert', null);

const filteredActivities = computed(() => {
  let filtered = activities.value;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(a => a.user_name.toLowerCase().includes(q) || a.details.toLowerCase().includes(q));
  }
  if (actionFilter.value) filtered = filtered.filter(a => a.action === actionFilter.value);
  return filtered.slice((currentPage.value - 1) * itemsPerPage, currentPage.value * itemsPerPage);
});

const totalPages = computed(() => {
  let total = activities.value;
  if (searchQuery.value) total = total.filter(a => a.user_name.toLowerCase().includes(searchQuery.value.toLowerCase()) || a.details.toLowerCase().includes(searchQuery.value.toLowerCase()));
  if (actionFilter.value) total = total.filter(a => a.action === actionFilter.value);
  return Math.ceil(total.length / itemsPerPage);
});

const formatDate = (date) => {
  return formatDateTimeSeconds(date);
};

const getActionColor = (action) => {
  const colors = {
    create: 'bg-green-100 text-green-800',
    update: 'bg-blue-100 text-blue-800',
    delete: 'bg-red-100 text-red-800',
    login: 'bg-purple-100 text-purple-800'
  };
  return colors[action] || 'bg-gray-100 text-gray-800';
};

const fetchActivities = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/activity-logs', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (response.ok) {
      const data = await response.json();
      activities.value = data.data || [];
    }
  } catch (error) {
    console.error('Error:', error);
    if (addAlert) addAlert('Failed to load activity logs', 'error');
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchActivities);
</script>
