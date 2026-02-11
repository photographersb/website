<template>
  <div class="min-h-screen">
    <AdminHeader
      title="📊 Analytics Dashboard"
      subtitle="Comprehensive platform metrics and insights"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminSectionHeader
        title="Analytics Dashboard"
        subtitle="Comprehensive platform metrics and real-time insights."
        eyebrow="Admin / Analytics"
      >
        <template #actions>
          <button
            class="btn-admin-primary"
            @click="loadAnalytics"
            :disabled="loading"
            :class="{ 'opacity-60 cursor-not-allowed': loading }"
          >
            <span v-if="!loading">Refresh Data</span>
            <span v-else>Loading...</span>
          </button>
          <button class="btn-admin-secondary">
            Export Snapshot
          </button>
        </template>
      </AdminSectionHeader>

      <AdminStatsStrip :stats="statItems" />

      <AdminQuickNav />

      <!-- Date Range Filter -->
      <div class="content-card analytics-card">
        <AdminFilterBar class="analytics-filter-bar">
          <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
            <select
              v-model="dateRange"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-burgundy"
            >
              <option value="7">
                Last 7 Days
              </option>
              <option value="30">
                Last 30 Days
              </option>
              <option value="90">
                Last 3 Months
              </option>
              <option value="365">
                Last Year
              </option>
            </select>
          </div>
          <template #actions>
            <div class="status-chip">
              Live sync: {{ lastUpdatedLabel }}
            </div>
          </template>
        </AdminFilterBar>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading"
        class="flex items-center justify-center py-12"
      >
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy" />
      </div>

      <!-- Analytics Content -->
      <div
        v-else
        class="space-y-6"
      >
        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="metric in metrics"
            :key="metric.label"
            class="admin-card p-6"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-600">{{ metric.label }}</span>
              <div :class="['w-10 h-10 rounded-lg flex items-center justify-center', metric.bgColor]">
                <span class="text-xl">{{ metric.icon }}</span>
              </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">
              {{ formatNumber(metric.value) }}
            </div>
            <div class="flex items-center mt-2">
              <span :class="['text-sm font-medium', metric.change >= 0 ? 'text-green-600' : 'text-red-600']">
                {{ metric.change >= 0 ? '↑' : '↓' }} {{ Math.abs(metric.change) }}%
              </span>
              <span class="text-sm text-gray-500 ml-2">vs previous period</span>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- User Growth Chart -->
          <div class="content-card analytics-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              User Growth
            </h3>
            <div class="h-64">
              <div
                v-if="userGrowthBars.length === 0"
                class="h-full flex items-center justify-center text-gray-400"
              >
                No growth data available
              </div>
              <div
                v-else
                class="chart-grid"
              >
                <div
                  v-for="entry in userGrowthBars"
                  :key="entry.label"
                  class="chart-row"
                >
                  <span class="chart-label">{{ entry.label }}</span>
                  <div class="chart-bar">
                    <span
                      class="chart-bar__fill chart-bar__fill--brand"
                      :style="{ width: entry.percent + '%' }"
                    />
                  </div>
                  <span class="chart-value">{{ formatNumber(entry.value) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Revenue Trends -->
          <div class="content-card analytics-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Revenue Trends
            </h3>
            <div class="h-64">
              <div
                v-if="revenueBars.length === 0"
                class="h-full flex items-center justify-center text-gray-400"
              >
                No revenue data available
              </div>
              <div
                v-else
                class="chart-grid"
              >
                <div
                  v-for="entry in revenueBars"
                  :key="entry.label"
                  class="chart-row"
                >
                  <span class="chart-label">{{ entry.label }}</span>
                  <div class="chart-bar">
                    <span
                      class="chart-bar__fill chart-bar__fill--success"
                      :style="{ width: entry.percent + '%' }"
                    />
                  </div>
                  <span class="chart-value">{{ formatNumber(entry.value) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Top Photographers -->
          <div class="content-card analytics-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Top Photographers
            </h3>
            <div
              v-if="topPhotographers.length === 0"
              class="h-40 flex items-center justify-center text-sm text-gray-400"
            >
              No performance leaders yet.
            </div>
            <div
              v-else
              class="space-y-3"
            >
              <div
                v-for="(photographer, index) in topPhotographers"
                :key="photographer.id"
                class="flex items-center justify-between py-2 border-b border-gray-100"
              >
                <div class="flex items-center space-x-3">
                  <span class="text-sm font-medium text-gray-500 w-6">{{ index + 1 }}</span>
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ photographer.name }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ photographer.bookings }} bookings
                    </p>
                  </div>
                </div>
                <span class="text-sm font-semibold text-green-600">${{ formatNumber(photographer.revenue) }}</span>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="content-card analytics-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Recent Activity
            </h3>
            <div
              v-if="recentActivity.length === 0"
              class="h-40 flex items-center justify-center text-sm text-gray-400"
            >
              No recent events in this range.
            </div>
            <div
              v-else
              class="space-y-3"
            >
              <div
                v-for="activity in recentActivity"
                :key="activity.id"
                class="flex items-start space-x-3 py-2 border-b border-gray-100"
              >
                <div :class="['w-2 h-2 rounded-full mt-1.5', activity.color]" />
                <div>
                  <p class="text-sm font-medium text-gray-900">
                    {{ activity.message }}
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ activity.time }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import AdminSectionHeader from '../../components/admin/ui/AdminSectionHeader.vue';
import AdminStatsStrip from '../../components/admin/ui/AdminStatsStrip.vue';
import AdminFilterBar from '../../components/admin/ui/AdminFilterBar.vue';
import Toast from '../../components/ui/Toast.vue';
import api from '../../api';
import { formatNumber as formatNumberValue, formatDateTime } from '../../utils/formatters';

const loading = ref(true);
const dateRange = ref('30');

const toast = ref({
  show: false,
  message: '',
  type: 'success',
});

const metrics = ref([
  { label: 'Total Users', value: 0, change: 0, icon: '👥', bgColor: 'bg-blue-100' },
  { label: 'Active Photographers', value: 0, change: 0, icon: '📸', bgColor: 'bg-pink-100' },
  { label: 'Total Bookings', value: 0, change: 0, icon: '📅', bgColor: 'bg-green-100' },
  { label: 'Revenue', value: 0, change: 0, icon: '💰', bgColor: 'bg-burgundy-100' },
]);

const topPhotographers = ref([]);
const recentActivity = ref([]);
const userGrowthSeries = ref([]);
const revenueSeries = ref([]);
const lastUpdated = ref(null);
const refreshIntervalMs = 60000;
let refreshTimer = null;

const lastUpdatedLabel = computed(() => {
  if (!lastUpdated.value) {
    return 'Waiting for sync';
  }
  return formatDateTime(lastUpdated.value);
});

const formatNumber = (num) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M';
  }
  if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K';
  }
  return formatNumberValue(num);
};

const formatCurrency = (num) => `$${formatNumberValue(Number(num) || 0)}`;

const statItems = computed(() => [
  {
    label: 'Active Users',
    value: formatNumber(metrics.value[0]?.value || 0),
  },
  {
    label: 'Photographers',
    value: formatNumber(metrics.value[1]?.value || 0),
  },
  {
    label: 'Bookings',
    value: formatNumber(metrics.value[2]?.value || 0),
  },
  {
    label: 'Revenue',
    value: formatCurrency(metrics.value[3]?.value || 0),
  },
  {
    label: 'Sync Status',
    value: lastUpdated.value ? 'Live' : 'Pending',
    meta: lastUpdatedLabel.value,
  },
]);

const buildBars = (series) => {
  const limit = 10;
  const trimmed = series.slice(-limit);
  const maxValue = Math.max(0, ...trimmed.map((item) => Number(item.value) || 0));

  return trimmed.map((item) => {
    const value = Number(item.value) || 0;
    const percent = maxValue > 0 ? Math.round((value / maxValue) * 100) : 0;
    const date = item.date ? new Date(item.date) : null;
    const label = date ? date.toLocaleDateString('en-GB', { month: 'short', day: '2-digit' }) : '-';
    return {
      label,
      value,
      percent,
    };
  });
};

const userGrowthBars = computed(() => buildBars(userGrowthSeries.value));
const revenueBars = computed(() => buildBars(revenueSeries.value));

const loadAnalytics = async (options = {}) => {
  const { silent = false } = options;
  if (!silent) {
    loading.value = true;
  }
  try {
    const response = await api.get('/admin/analytics', {
      params: { days: dateRange.value },
    });

    const payload = response.data?.data || {};
    if (payload) {
      metrics.value[0].value = payload.totalUsers || 0;
      metrics.value[0].change = payload.userGrowth || 0;

      metrics.value[1].value = payload.activePhotographers || 0;
      metrics.value[1].change = payload.photographerGrowth || 0;

      metrics.value[2].value = payload.totalBookings || 0;
      metrics.value[2].change = payload.bookingGrowth || 0;

      metrics.value[3].value = payload.revenue || 0;
      metrics.value[3].change = payload.revenueGrowth || 0;

      topPhotographers.value = payload.topPhotographers || [];
      recentActivity.value = payload.recentActivity || [];
      userGrowthSeries.value = payload.userGrowthSeries || [];
      revenueSeries.value = payload.revenueSeries || [];
      lastUpdated.value = new Date();
    }
  } catch (error) {
    console.error('Analytics load error:', error);
    toast.value = {
      show: true,
      message: 'Unable to load analytics right now. Please try again.',
      type: 'error',
    };
    metrics.value.forEach((metric) => {
      metric.value = 0;
      metric.change = 0;
    });
    topPhotographers.value = [];
    recentActivity.value = [];
  } finally {
    if (!silent) {
      loading.value = false;
    }
  }
};

onMounted(() => {
  loadAnalytics();
  refreshTimer = setInterval(() => {
    loadAnalytics({ silent: true });
  }, refreshIntervalMs);
});

onUnmounted(() => {
  if (refreshTimer) {
    clearInterval(refreshTimer);
  }
});
</script>

<style scoped>
.status-chip {
  background: rgba(142, 14, 63, 0.12);
  color: var(--admin-text-primary);
  padding: 0.4rem 0.8rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

:deep(.analytics-filter-bar .admin-filter-bar__actions) {
  align-self: flex-end;
}

.analytics-card {
  overflow: visible;
  padding: 1.75rem;
}

.analytics-card h3 {
  margin-top: 0;
}

.analytics-card h3,
.analytics-card label {
  line-height: 1.3;
}

@media (max-width: 1024px) {
  .analytics-card {
    padding: 1.25rem;
  }
}

.chart-grid {
  display: grid;
  gap: 0.75rem;
  height: 100%;
}

.chart-row {
  display: grid;
  grid-template-columns: 3.5rem 1fr 4rem;
  gap: 0.75rem;
  align-items: center;
}

.chart-label {
  font-size: 0.75rem;
  color: #6b7280;
}

.chart-value {
  font-size: 0.75rem;
  color: #111827;
  text-align: right;
  font-weight: 600;
}

.chart-bar {
  position: relative;
  height: 0.5rem;
  background: #f3f4f6;
  border-radius: 9999px;
  overflow: hidden;
}

.chart-bar__fill {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  border-radius: 9999px;
}

.chart-bar__fill--brand {
  background: linear-gradient(90deg, #8e0e3f, #d65f7a);
}

.chart-bar__fill--success {
  background: linear-gradient(90deg, #16a34a, #4ade80);
}
</style>
