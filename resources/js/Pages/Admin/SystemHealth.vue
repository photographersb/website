<template>
  <div class="admin-shell">
    <AdminHeader />
    
    <div class="admin-shell__content px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Page Header -->
      <div class="mb-6 flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold admin-page-title">
            System Health
          </h1>
          <p class="admin-page-subtitle mt-1">
            Platform performance and infrastructure monitoring
          </p>
        </div>
        <button
          class="btn-admin-secondary flex items-center gap-2"
          :disabled="loading"
          @click="loadSystemHealth"
        >
          <svg
            :class="['w-4 h-4', loading ? 'animate-spin' : '']"
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
          {{ loading ? 'Loading...' : 'Refresh' }}
        </button>
      </div>
      
      <AdminQuickNav />

      <div
        v-if="loadError"
        class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        role="alert"
      >
        {{ loadError }}
      </div>

        <!-- Loading State -->
        <div
          v-if="loading && !healthData.system_status && !loadError"
          class="flex items-center justify-center py-12"
        >
          <div class="admin-loader animate-spin rounded-full h-12 w-12"></div>
        </div>

        <div
          v-else-if="loadError"
          class="bg-white rounded-lg shadow-sm border border-red-200 p-6 mb-6"
        >
          <div class="flex flex-col gap-3">
            <div class="flex items-center gap-3">
              <span class="text-2xl">⚠️</span>
              <div>
                <p class="text-sm font-semibold text-gray-900">
                  System health data unavailable
                </p>
                <p class="text-xs text-red-700">
                  {{ loadError }}
                </p>
              </div>
            </div>
            <button
              class="btn-admin-secondary w-fit"
              :disabled="loading"
              @click="loadSystemHealth"
            >
              Retry
            </button>
          </div>
        </div>

        <!-- Overall Status -->
        <div
          v-else
          class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6"
        >
          <div class="admin-kpi">
            <div class="flex items-center justify-between">
              <div>
                <p class="admin-kpi__label">
                  System Status
                </p>
                <div class="mt-2">
                  <span
                    :class="[
                      'status-pill',
                      healthData.system_status === 'healthy' ? 'status-pill--success' : 'status-pill--danger'
                    ]"
                  >
                    {{ healthData.system_status === 'healthy' ? 'Healthy' : 'Issues Detected' }}
                  </span>
                </div>
              </div>
              <div class="text-3xl">
                {{ healthData.system_status === 'healthy' ? '✅' : '⚠️' }}
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
              All systems operational
            </p>
          </div>

          <div class="admin-kpi">
            <div class="flex items-center justify-between">
              <div>
                <p class="admin-kpi__label">
                  Uptime
                </p>
                <p class="admin-kpi__value">
                  {{ healthData.uptime || '99.9%' }}
                </p>
              </div>
              <div class="text-3xl">
                📈
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
              Last 30 days
            </p>
          </div>

          <div class="admin-kpi">
            <div class="flex items-center justify-between">
              <div>
                <p class="admin-kpi__label">
                  Response Time
                </p>
                <p class="admin-kpi__value">
                  {{ healthData.avg_response_time || '150ms' }}
                </p>
              </div>
              <div class="text-3xl">
                ⚡
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
              Average request time
            </p>
          </div>
        </div>

        <!-- Health Metrics Grid -->
        <div
          v-if="healthData.database"
          class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6"
        >
          <!-- Database -->
          <div class="admin-card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Database
            </h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Connection</span>
                <span
                  :class="[
                    'status-pill',
                    healthData.database.status === 'active' ? 'status-pill--success' : 'status-pill--danger'
                  ]"
                >
                  {{ healthData.database.status === 'active' ? 'Active' : 'Error' }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Size</span>
                <span class="text-sm font-medium text-gray-900">{{ healthData.database.size }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Queries/sec</span>
                <span class="text-sm font-medium text-gray-900">{{ healthData.database.queries_per_sec }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Last Backup</span>
                <span class="text-sm font-medium text-gray-900">{{ healthData.database.last_backup }}</span>
              </div>
            </div>
          </div>

          <!-- Cache -->
          <div class="admin-card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Cache
            </h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Status</span>
                <span
                  :class="[
                    'status-pill',
                    healthData.cache.status === 'active' ? 'status-pill--success' : 'status-pill--danger'
                  ]"
                >
                  {{ healthData.cache.status === 'active' ? 'Connected' : 'Error' }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Driver</span>
                <span class="text-sm font-medium text-gray-900">{{ healthData.cache.driver }}</span>
              </div>
            </div>
          </div>

          <!-- Queue -->
          <div class="admin-card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Job Queue
            </h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Pending Jobs</span>
                <span class="text-sm font-medium text-gray-900">{{ healthData.queue.pending_jobs }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Failed Jobs</span>
                <span :class="['text-sm font-medium', healthData.queue.failed_jobs > 0 ? 'text-red-600' : 'text-gray-900']">
                  {{ healthData.queue.failed_jobs }}
                </span>
              </div>
            </div>
          </div>

          <!-- Storage -->
          <div class="admin-card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Storage
            </h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Disk Usage</span>
                <span class="text-sm font-medium text-gray-900">{{ healthData.storage.used }} / {{ healthData.storage.total }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-amber-700 h-2 rounded-full"
                  :style="{ width: healthData.storage.percent + '%' }"
                />
              </div>
              <p class="text-xs text-gray-500">
                {{ healthData.storage.percent }}% used
              </p>
            </div>
          </div>
        </div>

        <!-- Recent Events -->
        <div
          v-if="healthData.recent_events"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
        >
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Recent System Events
          </h3>
          <div class="space-y-3">
            <div
              v-for="(event, index) in healthData.recent_events"
              :key="index"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <span class="text-2xl">{{ event.icon }}</span>
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ event.message }}
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ event.time }}
                  </p>
                </div>
              </div>
              <span :class="['px-2 py-1 text-xs font-medium rounded', event.badge]">
                {{ event.type }}
              </span>
            </div>
          </div>
        </div>

    </div>

    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import Toast from '../../components/ui/Toast.vue';
import api from '../../api.js';

const loading = ref(false);
const loadError = ref('');

const healthData = ref({
  system_status: '',
  uptime: '',
  avg_response_time: '',
  database: null,
  cache: null,
  queue: null,
  storage: null,
  sessions: null,
  recent_events: [],
});

const toast = ref({
  show: false,
  message: '',
  type: 'success'
});

const loadSystemHealth = async () => {
  loading.value = true;
  try {
    const response = await api.get('/admin/system-health');
    
    if (response.data?.data) {
      healthData.value = response.data.data;
      loadError.value = '';
    }
  } catch (error) {
    console.error('System health load error:', error);
    loadError.value = 'Unable to load system health data. Please try again.';
    toast.value = {
      show: true,
      message: loadError.value,
      type: 'error'
    };
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadSystemHealth();
});
</script>
