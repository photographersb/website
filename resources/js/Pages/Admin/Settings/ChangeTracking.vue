<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">
          Settings Change Tracking
        </h1>
        <p class="text-gray-600 mt-1">
          View and manage all platform configuration changes
        </p>
      </div>
    </div>

    <AdminQuickNav />

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm mb-2">
          Total Changes
        </p>
        <p class="text-3xl font-bold text-gray-900">
          {{ totalChanges }}
        </p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm mb-2">
          This Month
        </p>
        <p class="text-3xl font-bold text-orange-600">
          {{ changesThisMonth }}
        </p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm mb-2">
          Unique Settings
        </p>
        <p class="text-3xl font-bold text-blue-600">
          {{ uniqueSettings }}
        </p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm mb-2">
          Active Admins
        </p>
        <p class="text-3xl font-bold text-green-600">
          {{ activeAdmins }}
        </p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search Setting</label>
          <input 
            v-model="searchQuery"
            type="text"
            placeholder="e.g., mail, cache, feature..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Admin User</label>
          <select 
            v-model="adminFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
          >
            <option value="">
              All Admins
            </option>
            <option
              v-for="admin in admins"
              :key="admin.id"
              :value="admin.id"
            >
              {{ admin.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
          <select 
            v-model="dateRange"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
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
            <option value="year">
              This Year
            </option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium"
            @click="resetFilters"
          >
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Changes Timeline -->
    <div
      v-if="loading"
      class="bg-white rounded-lg shadow p-12 text-center"
    >
      <p class="text-gray-600">
        Loading changes...
      </p>
    </div>

    <div
      v-else-if="filteredChanges.length === 0"
      class="bg-white rounded-lg shadow p-12 text-center"
    >
      <svg
        class="w-16 h-16 text-gray-300 mx-auto mb-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
        />
      </svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">
        No Changes Found
      </h3>
      <p class="text-gray-600">
        Try adjusting your filters to see settings changes
      </p>
    </div>

    <div
      v-else
      class="space-y-4"
    >
      <div 
        v-for="(group, date) in groupedByDate"
        :key="date"
        class="space-y-3"
      >
        <!-- Date Header -->
        <h3 class="text-sm font-semibold text-gray-900 px-4 pt-4">
          {{ formatDateGroup(date) }}
        </h3>

        <!-- Changes for this date -->
        <div 
          v-for="change in group"
          :key="change.id"
          class="bg-white rounded-lg shadow hover:shadow-md transition"
        >
          <div class="p-6">
            <!-- Main Content Row -->
            <div class="flex items-start gap-6">
              <!-- Timeline Dot -->
              <div class="pt-1">
                <div
                  :class="[
                    'w-3 h-3 rounded-full border-2',
                    getChangeIcon(change.action).color
                  ]"
                />
              </div>

              <!-- Change Details -->
              <div class="flex-1">
                <div class="flex items-start justify-between mb-3">
                  <div>
                    <h4 class="text-lg font-semibold text-gray-900">
                      {{ change.setting_name }}
                    </h4>
                    <p class="text-sm text-gray-600 mt-1">
                      {{ change.setting_description }}
                    </p>
                  </div>
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-medium',
                      getActionBadgeClass(change.action)
                    ]"
                  >
                    {{ formatAction(change.action) }}
                  </span>
                </div>

                <!-- Old vs New Values -->
                <div
                  v-if="change.old_value !== undefined"
                  class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4"
                >
                  <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-xs font-semibold text-red-900 mb-2 uppercase">
                      Previous Value
                    </p>
                    <code class="text-sm text-red-800 break-all">{{ formatValue(change.old_value) }}</code>
                  </div>
                  <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-xs font-semibold text-green-900 mb-2 uppercase">
                      New Value
                    </p>
                    <code class="text-sm text-green-800 break-all">{{ formatValue(change.new_value) }}</code>
                  </div>
                </div>

                <!-- Metadata Row -->
                <div class="flex items-center gap-6 text-sm text-gray-600">
                  <div class="flex items-center gap-2">
                    <img 
                      :src="change.admin_avatar || defaultAvatar"
                      :alt="change.admin_name"
                      class="w-6 h-6 rounded-full"
                    >
                    <span class="font-medium">{{ change.admin_name }}</span>
                  </div>
                  <span>📅 {{ formatTime(change.created_at) }}</span>
                  <span
                    v-if="change.ip_address"
                    class="text-xs text-gray-500"
                  >
                    IP: {{ maskIpAddress(change.ip_address) }}
                  </span>
                </div>

                <!-- Notes -->
                <div
                  v-if="change.reason"
                  class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200"
                >
                  <p class="text-xs font-semibold text-blue-900 mb-1">
                    Reason/Notes:
                  </p>
                  <p class="text-sm text-blue-800">
                    {{ change.reason }}
                  </p>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center gap-2">
                <button
                  v-if="canRollback(change)"
                  class="px-3 py-1 text-orange-600 hover:bg-orange-50 rounded transition text-xs font-medium"
                  @click="rollbackChange(change)"
                >
                  Rollback
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div
      v-if="totalPages > 1"
      class="flex items-center justify-between"
    >
      <p class="text-sm text-gray-600">
        Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, totalCount) }} of {{ totalCount }}
      </p>
      <div class="flex gap-2">
        <button
          :disabled="currentPage === 1"
          class="px-4 py-2 border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          @click="previousPage"
        >
          Previous
        </button>
        <button
          :disabled="currentPage === totalPages"
          class="px-4 py-2 border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          @click="nextPage"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Toast -->
    <div 
      v-if="toastMessage"
      :class="[
        'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transition z-50',
        toastType === 'success' ? 'bg-green-600' : 'bg-red-600'
      ]"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../../api';
import AdminQuickNav from '../../../components/AdminQuickNav.vue';
import { formatDate as formatDateValue, formatDateTime } from '../../../utils/formatters';

const changes = ref([]);
const admins = ref([]);
const loading = ref(true);
const currentPage = ref(1);
const perPage = ref(15);
const totalCount = ref(0);

const searchQuery = ref('');
const adminFilter = ref('');
const dateRange = ref('all');
const toastMessage = ref('');
const toastType = ref('success');

const defaultAvatar = 'https://ui-avatars.com/api/?name=Admin';

const totalPages = computed(() => Math.ceil(totalCount.value / perPage.value));

const totalChanges = computed(() => changes.value.length);
const changesThisMonth = computed(() => 
  changes.value.filter(c => {
    const changeDate = new Date(c.created_at);
    const now = new Date();
    return changeDate.getMonth() === now.getMonth() && 
           changeDate.getFullYear() === now.getFullYear();
  }).length
);
const uniqueSettings = computed(() => 
  new Set(changes.value.map(c => c.setting_name)).size
);
const activeAdmins = computed(() => 
  new Set(changes.value.map(c => c.admin_id)).size
);

const filteredChanges = computed(() => {
  let filtered = changes.value;

  // Search filter
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(c => 
      c.setting_name.toLowerCase().includes(q) ||
      c.setting_description?.toLowerCase().includes(q)
    );
  }

  // Admin filter
  if (adminFilter.value) {
    filtered = filtered.filter(c => c.admin_id === parseInt(adminFilter.value));
  }

  // Date range filter
  if (dateRange.value !== 'all') {
    const now = new Date();
    filtered = filtered.filter(c => {
      const changeDate = new Date(c.created_at);
      
      if (dateRange.value === 'today') {
        return changeDate.toDateString() === now.toDateString();
      } else if (dateRange.value === 'week') {
        const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
        return changeDate >= weekAgo;
      } else if (dateRange.value === 'month') {
        return changeDate.getMonth() === now.getMonth() && 
               changeDate.getFullYear() === now.getFullYear();
      } else if (dateRange.value === 'year') {
        return changeDate.getFullYear() === now.getFullYear();
      }
      return true;
    });
  }

  return filtered;
});

const groupedByDate = computed(() => {
  const grouped = {};
  filteredChanges.value.forEach(change => {
    const date = new Date(change.created_at).toISOString().split('T')[0];
    if (!grouped[date]) {
      grouped[date] = [];
    }
    grouped[date].push(change);
  });
  return grouped;
});

const loadChanges = async () => {
  loading.value = true;
  try {
    const { data } = await api.get('/admin/settings/changes', {
      params: {
        page: currentPage.value,
        per_page: perPage.value
      }
    });
    if (data.status === 'success') {
      changes.value = data.data;
      totalCount.value = data.meta?.total || data.data.length;
    }
  } catch (error) {
    console.error('Error loading changes:', error);
    showToast('Failed to load settings changes', 'error');
  }

  // Load admins list for filter
  try {
    const { data } = await api.get('/admin/users?role=admin&per_page=100');
    if (data.status === 'success') {
      admins.value = data.data;
    }
  } catch (error) {
    console.error('Error loading admins:', error);
  }

  loading.value = false;
};

const rollbackChange = async (change) => {
  if (!confirm(`Rollback "${change.setting_name}" to previous value?`)) return;

  try {
    const { data } = await api.post(`/admin/settings/${change.id}/rollback`);
    if (data.status === 'success') {
      showToast('Setting rolled back successfully', 'success');
      loadChanges();
    }
  } catch (error) {
    console.error('Error rolling back change:', error);
    showToast('Failed to rollback change', 'error');
  }
};

const resetFilters = () => {
  searchQuery.value = '';
  adminFilter.value = '';
  dateRange.value = 'all';
  currentPage.value = 1;
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    loadChanges();
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    loadChanges();
  }
};

const showToast = (message, type) => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

const formatTime = (datetime) => {
  return formatDateTime(datetime);
};

const formatDateGroup = (date) => {
  return formatDateValue(date);
};

const formatAction = (action) => {
  const actions = {
    'create': 'Created',
    'update': 'Updated',
    'delete': 'Deleted',
    'enable': 'Enabled',
    'disable': 'Disabled'
  };
  return actions[action] || action;
};

const getActionBadgeClass = (action) => {
  const classes = {
    'create': 'bg-green-100 text-green-800',
    'update': 'bg-blue-100 text-blue-800',
    'delete': 'bg-red-100 text-red-800',
    'enable': 'bg-green-100 text-green-800',
    'disable': 'bg-yellow-100 text-yellow-800'
  };
  return classes[action] || 'bg-gray-100 text-gray-800';
};

const getChangeIcon = (action) => {
  const icons = {
    'create': { color: 'border-green-500 bg-green-100' },
    'update': { color: 'border-blue-500 bg-blue-100' },
    'delete': { color: 'border-red-500 bg-red-100' },
    'enable': { color: 'border-green-500 bg-green-100' },
    'disable': { color: 'border-yellow-500 bg-yellow-100' }
  };
  return icons[action] || { color: 'border-gray-500 bg-gray-100' };
};

const formatValue = (value) => {
  if (value === null || value === undefined) return 'Not set';
  if (typeof value === 'boolean') return value ? 'Yes' : 'No';
  if (typeof value === 'object') return JSON.stringify(value, null, 2);
  return String(value);
};

const maskIpAddress = (ip) => {
  const parts = ip.split('.');
  if (parts.length === 4) {
    return `${parts[0]}.${parts[1]}.${parts[2]}.***`;
  }
  return ip;
};

const canRollback = (change) => {
  // Can only rollback if there's a previous value
  return change.old_value !== undefined && change.action !== 'delete';
};

onMounted(() => {
  loadChanges();
  // Auto-refresh every 30 seconds
  const interval = setInterval(loadChanges, 30000);
  return () => clearInterval(interval);
});
</script>

<style scoped>
/* Smooth transitions */
</style>
