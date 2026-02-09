<template>
  <div class="min-h-screen">
    <AdminHeader 
      title="📊 Analytics Dashboard" 
      subtitle="Comprehensive platform metrics and insights"
    />
    
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">INSIGHT STREAM</p>
          <h1 class="hero-title">Analytics that surface the truth.</h1>
          <p class="hero-subtitle">
            Watch platform performance in real time and pivot fast.
          </p>
          <div class="hero-actions">
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
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Range</span>
            <span class="status-value">{{ dateRange }} days</span>
          </div>
          <div class="status-card">
            <span class="status-label">Users</span>
            <span class="status-value">{{ formatNumber(metrics[0].value || 0) }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Bookings</span>
            <span class="status-value">{{ formatNumber(metrics[2].value || 0) }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Live sync: {{ lastUpdatedLabel }}
        </div>
      </div>

      <AdminQuickNav />

      <!-- Date Range Filter -->
      <div class="content-card">
        <div class="flex flex-wrap gap-4 items-end">
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
          <button
            class="btn-admin-primary"
            @click="loadAnalytics"
            :disabled="loading"
            :class="{ 'opacity-60 cursor-not-allowed': loading }"
          >
            <span v-if="!loading">Refresh Data</span>
            <span v-else>Loading...</span>
          </button>
        </div>
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
            <div class="content-card">
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
            <div class="content-card">
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
            <div class="content-card">
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
            <div class="content-card">
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
                  <div class="flex-1">
                    <p class="text-sm text-gray-900">
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
      <!-- End v-else -->
    </div>
    <!-- End max-w-full container -->

    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />
  </div>
  <!-- End min-h-screen -->
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import Toast from '../../components/ui/Toast.vue';
import api from '../../api.js';
import { formatNumber as formatNumberValue, formatDateTime } from '../../utils/formatters';

const loading = ref(true);
const dateRange = ref('30');

const toast = ref({
  show: false,
  message: '',
  type: 'success'
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
  if (!lastUpdated.value) return 'waiting for data';
  return formatDateTime(lastUpdated.value);
});

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
      params: { days: dateRange.value }
    });

    const payload = response.data?.data || {};
    if (payload) {
      // Update metrics
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
      type: 'error'
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

const formatNumber = (num) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M';
  } else if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K';
  }
  return formatNumberValue(num);
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
.page-hero { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: 1.5rem; padding: 1.75rem 2rem; border-radius: 1.5rem; border: 1px solid rgba(142, 14, 63, 0.2); background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(247, 239, 233, 0.82)), linear-gradient(90deg, rgba(142, 14, 63, 0.06), transparent 45%, rgba(109, 72, 56, 0.08)); box-shadow: 0 25px 55px rgba(24, 12, 8, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.6); backdrop-filter: blur(6px); }
.hero-copy { display: flex; flex-direction: column; gap: 0.85rem; }
.hero-kicker { font-size: 0.7rem; letter-spacing: 0.28em; text-transform: uppercase; color: var(--admin-text-secondary); font-weight: 700; }
.hero-title { font-size: 2rem; line-height: 1.1; color: var(--admin-text-primary); text-shadow: 0 2px 14px rgba(142, 14, 63, 0.18); }
.hero-subtitle { color: var(--admin-text-secondary); max-width: 480px; }
.hero-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.hero-status { display: grid; gap: 0.8rem; }
.status-card { background: rgba(255, 255, 255, 0.85); border: 1px solid rgba(142, 14, 63, 0.2); border-radius: 1rem; padding: 1rem 1.25rem; box-shadow: 0 16px 35px rgba(22, 12, 8, 0.08); display: flex; flex-direction: column; gap: 0.35rem; }
.status-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.2em; color: var(--admin-text-secondary); }
.status-value { font-size: 1.1rem; font-weight: 700; color: var(--admin-text-primary); }
.page-topbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.9rem 1.25rem; background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1.1rem; box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08); backdrop-filter: blur(8px); }
.status-chip { background: rgba(142, 14, 63, 0.12); color: var(--admin-text-primary); padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.chart-grid { display: grid; gap: 0.75rem; height: 100%; }
.chart-row { display: grid; grid-template-columns: 3.5rem 1fr 4rem; gap: 0.75rem; align-items: center; }
.chart-label { font-size: 0.75rem; color: #6b7280; }
.chart-value { font-size: 0.75rem; color: #111827; text-align: right; font-weight: 600; }
.chart-bar { position: relative; height: 0.5rem; background: #f3f4f6; border-radius: 9999px; overflow: hidden; }
.chart-bar__fill { position: absolute; left: 0; top: 0; bottom: 0; border-radius: 9999px; }
.chart-bar__fill--brand { background: linear-gradient(90deg, #8e0e3f, #d65f7a); }
.chart-bar__fill--success { background: linear-gradient(90deg, #16a34a, #4ade80); }
@media (max-width: 1024px) { .page-hero { grid-template-columns: 1fr; } }
</style>
