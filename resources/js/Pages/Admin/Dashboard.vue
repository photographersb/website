<template>
  <div class="admin-shell">
    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="closeToast"
    />

    <AdminHeader 
      title="📊 Admin Dashboard" 
      subtitle="Platform Overview & Analytics"
    />

    <div class="admin-shell__content px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <section class="dashboard-hero">
        <div class="hero-copy">
          <p class="hero-kicker">
            COMMAND CENTER
          </p>
          <h1 class="hero-title">
            Admin dashboard, tuned for clarity.
          </h1>
          <p class="hero-subtitle">
            Track platform growth, revenue, and operational health from one cockpit.
          </p>
          <div class="hero-actions">
            <router-link
              to="/admin/analytics"
              class="btn-admin-primary"
            >
              Open Analytics
            </router-link>
            <router-link
              to="/admin/approvals"
              class="btn-admin-secondary"
            >
              Review Approvals
            </router-link>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">System Health</span>
            <span class="status-value">
              {{ stats.system_status || 'Healthy' }}
            </span>
          </div>
          <div class="status-card">
            <span class="status-label">Active Competitions</span>
            <span class="status-value">
              {{ stats.active_competitions || 0 }}
            </span>
          </div>
          <div class="status-card">
            <span class="status-label">Pending Verifications</span>
            <span class="status-value">
              {{ stats.pending_verifications || 0 }}
            </span>
          </div>
        </div>
      </section>

      <div class="dashboard-topbar">
        <div class="topbar-left">
          <div class="status-chip">
            Updated just now
          </div>
        </div>
        <div class="topbar-actions">
          <router-link
            to="/admin/system-health"
            class="btn-admin-outline"
          >
            System Health
          </router-link>
          <router-link
            to="/admin/activity-logs"
            class="btn-admin-outline"
          >
            Activity Logs
          </router-link>
          <router-link
            to="/judge/dashboard"
            class="btn-admin-outline"
          >
            Judge Portal
          </router-link>
        </div>
      </div>

      <!-- Loading -->
      <div
        v-if="loading"
        class="text-center py-12"
      >
        <div class="admin-loader animate-spin rounded-full h-12 w-12 mx-auto" />
        <p class="mt-4 text-gray-600">
          Loading dashboard...
        </p>
      </div>

      <!-- Content -->
      <div
        v-else
        class="space-y-6"
      >
            <!-- Main Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Users -->
              <div class="admin-kpi">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="admin-kpi__label">
                      Total Users
                    </p>
                    <p class="admin-kpi__value">
                      {{ stats.total_users || 0 }}
                    </p>
                    <p class="admin-kpi__meta admin-kpi__meta--success">
                      {{ stats.active_users || 0 }} active
                    </p>
                  </div>
                  <div class="admin-kpi__icon admin-kpi__icon--brand">
                    <svg
                      class="w-8 h-8"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                      />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Photographers -->
              <div class="admin-kpi">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="admin-kpi__label">
                      Photographers
                    </p>
                    <p class="admin-kpi__value">
                      {{ stats.total_photographers || 0 }}
                    </p>
                    <p class="admin-kpi__meta admin-kpi__meta--success">
                      {{ stats.verified_photographers || 0 }} verified
                    </p>
                  </div>
                  <div class="admin-kpi__icon admin-kpi__icon--info">
                    <svg
                      class="w-8 h-8"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Bookings -->
              <div class="admin-kpi">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="admin-kpi__label">
                      Total Bookings
                    </p>
                    <p class="admin-kpi__value">
                      {{ stats.total_bookings || 0 }}
                    </p>
                    <p class="admin-kpi__meta admin-kpi__meta--warning">
                      {{ stats.pending_bookings || 0 }} pending
                    </p>
                  </div>
                  <div class="admin-kpi__icon admin-kpi__icon--warning">
                    <svg
                      class="w-8 h-8"
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

              <!-- Revenue -->
              <div class="admin-kpi">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="admin-kpi__label">
                      Total Revenue
                    </p>
                    <p class="admin-kpi__value">
                      ৳{{ formatNumber(stats.total_revenue || 0) }}
                    </p>
                    <p class="admin-kpi__meta admin-kpi__meta--success">
                      ৳{{ formatNumber(stats.monthly_revenue || 0) }} this month
                    </p>
                  </div>
                  <div class="admin-kpi__icon admin-kpi__icon--success">
                    <svg
                      class="w-8 h-8"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tips & Featured Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Tips Stats -->
              <div class="admin-highlight admin-highlight--gold">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-white/80">
                      Photographer Tips
                    </p>
                    <p class="mt-2 text-3xl font-bold">
                      ৳{{ formatNumber(stats.total_tips || 0) }}
                    </p>
                    <p class="mt-1 text-sm text-white/80">
                      {{ stats.total_tip_count || 0 }} tips received
                    </p>
                  </div>
                  <div class="p-3 bg-white/20 rounded-full">
                    <svg
                      class="w-8 h-8"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Featured Photographers Revenue -->
              <div class="admin-highlight admin-highlight--rose">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-white/80">
                      Featured Revenue
                    </p>
                    <p class="mt-2 text-3xl font-bold">
                      ৳{{ formatNumber(stats.featured_revenue || 0) }}
                    </p>
                    <p class="mt-1 text-sm text-white/80">
                      {{ stats.active_featured_photographers || 0 }} active
                    </p>
                    <router-link
                      to="/admin/featured-photographers"
                      class="inline-flex items-center gap-1 text-sm font-semibold text-white/90 hover:text-white mt-2"
                    >
                      Manage featured
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
                          d="M9 5l7 7-7 7"
                        />
                      </svg>
                    </router-link>
                  </div>
                  <div class="p-3 bg-white/20 rounded-full">
                    <svg
                      class="w-8 h-8"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Package Upgrades Revenue -->
              <div class="admin-highlight admin-highlight--teal">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-white/80">
                      Package Upgrades
                    </p>
                    <p class="mt-2 text-3xl font-bold">
                      ৳{{ formatNumber(stats.upgrade_revenue || 0) }}
                    </p>
                    <p class="mt-1 text-sm text-white/80">
                      {{ stats.total_upgrades || 0 }} upgrades
                    </p>
                  </div>
                  <div class="p-3 bg-white/20 rounded-full">
                    <svg
                      class="w-8 h-8"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                      />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Tips Table -->
            <div class="admin-card">
              <div class="admin-card__header px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">
                  💰 Recent Tips
                </h2>
                <button class="btn-admin-outline text-sm">
                  View All
                </button>
              </div>
              <div
                v-if="recentTips.length === 0"
                class="p-6 text-center text-gray-500"
              >
                No tips received yet
              </div>
              <div
                v-else
                class="overflow-x-auto"
              >
                <table class="admin-table min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Photographer
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Tipper
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Amount
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Payment Method
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Message
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Date
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Status
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                      v-for="tip in recentTips"
                      :key="tip.id"
                    >
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="h-10 w-10 flex-shrink-0">
                            <img
                              v-if="tip.photographer?.profile_picture"
                              :src="tip.photographer.profile_picture"
                              class="h-10 w-10 rounded-full object-cover"
                              :alt="tip.photographer.user?.name"
                            >
                            <div
                              v-else
                              class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold"
                            >
                              {{ tip.photographer?.user?.name?.charAt(0) || '?' }}
                            </div>
                          </div>
                          <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">
                              {{ tip.photographer?.user?.name || 'Unknown' }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ tip.tipper_name || 'Anonymous' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                        ৳{{ formatNumber(tip.amount) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="badge badge-info">
                          {{ tip.payment_method }}
                        </span>
                      </td>
                      <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                        {{ tip.message || '-' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(tip.created_at) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          :class="getStatusClass(tip.status)"
                        >
                          {{ tip.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Top Photographers & Quick Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Top Photographers by Tips -->
              <div class="admin-card">
                <div class="admin-card__header px-6 py-4">
                  <h2 class="text-lg font-semibold text-gray-900">
                    🏆 Top Photographers by Tips
                  </h2>
                </div>
                <div class="p-6">
                  <div
                    v-if="topPhotographersByTips.length === 0"
                    class="text-center text-gray-500 py-4"
                  >
                    No tips data available
                  </div>
                  <div
                    v-else
                    class="space-y-4"
                  >
                    <div
                      v-for="(photographer, index) in topPhotographersByTips"
                      :key="photographer.id"
                      class="flex items-center"
                    >
                      <div class="flex-shrink-0 w-8 text-center">
                        <span
                          class="text-lg font-bold"
                          :class="index === 0 ? 'text-yellow-500' : index === 1 ? 'text-gray-400' : index === 2 ? 'text-orange-600' : 'text-gray-600'"
                        >
                          {{ index + 1 }}
                        </span>
                      </div>
                      <div class="ml-4 flex-shrink-0">
                        <img
                          v-if="photographer.profile_picture"
                          :src="photographer.profile_picture"
                          class="h-10 w-10 rounded-full object-cover"
                          :alt="photographer.user?.name"
                        >
                        <div
                          v-else
                          class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold"
                        >
                          {{ photographer.user?.name?.charAt(0) || '?' }}
                        </div>
                      </div>
                      <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">
                          {{ photographer.user?.name || 'Unknown' }}
                        </p>
                        <p class="text-xs text-gray-500">
                          {{ photographer.tip_count }} tips
                        </p>
                      </div>
                      <div class="text-sm font-semibold text-green-600">
                        ৳{{ formatNumber(photographer.total_tips) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- System Health -->
              <div class="admin-card">
                <div class="admin-card__header px-6 py-4">
                  <h2 class="text-lg font-semibold text-gray-900">
                    ⚡ System Health
                  </h2>
                </div>
                <div class="p-6 space-y-4">
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Active Competitions</span>
                    <span class="text-lg font-bold text-amber-700">{{ stats.active_competitions || 0 }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Active Events</span>
                    <span class="text-lg font-bold text-red-700">{{ stats.total_events || 0 }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Reviews</span>
                    <span class="text-lg font-bold text-amber-700">{{ stats.total_reviews || 0 }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Average Rating</span>
                    <span class="text-lg font-bold text-orange-600">{{ formatFixed(stats.avg_rating, 1, '0.0') }} ⭐</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Pending Verifications</span>
                    <span class="text-lg font-bold text-red-600">{{ stats.pending_verifications || 0 }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Profile Share Leaders -->
            <div class="admin-card">
              <div class="admin-card__header px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">
                  🔗 Profile Share Leaders
                </h2>
              </div>
              <div class="p-6">
                <div
                  v-if="profileShareLeaders.length === 0"
                  class="text-center text-gray-500 py-4"
                >
                  No profile share activity yet
                </div>
                <div
                  v-else
                  class="space-y-4"
                >
                  <div
                    v-for="(leader, index) in profileShareLeaders"
                    :key="leader.id"
                    class="flex items-center justify-between"
                  >
                    <div class="flex items-center gap-3">
                      <div class="w-6 text-center text-sm font-semibold text-gray-500">
                        {{ index + 1 }}
                      </div>
                      <div class="h-10 w-10 flex-shrink-0">
                        <img
                          v-if="leader.profile_picture"
                          :src="leader.profile_picture"
                          class="h-10 w-10 rounded-full object-cover"
                          :alt="leader.user?.name"
                        >
                        <div
                          v-else
                          class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold"
                        >
                          {{ leader.user?.name?.charAt(0) || '?' }}
                        </div>
                      </div>
                      <div>
                        <p class="text-sm font-medium text-gray-900">
                          {{ leader.user?.name || 'Unknown' }}
                        </p>
                        <p class="text-xs text-gray-500">
                          /photographer/{{ leader.slug || 'profile' }}
                        </p>
                      </div>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-semibold text-gray-900">
                        {{ leader.share_visits }} visits
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ leader.share_clicks }} clicks
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <!-- End grid lg:grid-cols-2 -->
      </div>
      <!-- End v-else space-y-6 -->
    </div>
    <!-- End max-w-full Container -->
  </div>
  <!-- End min-h-screen -->
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../../api';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import Toast from '../../components/ui/Toast.vue';
import {
  formatDate as formatDateValue,
  formatFixed,
  formatNumber as formatNumberValue
} from '../../utils/formatters';

const loading = ref(true);
const dashboardData = ref({});

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

const stats = computed(() => dashboardData.value.stats || {});
const recentTips = computed(() => dashboardData.value.recent_tips || []);
const topPhotographersByTips = computed(() => dashboardData.value.top_photographers_by_tips || []);
const profileShareLeaders = computed(() => dashboardData.value.profile_share_leaderboard || []);

const formatNumber = (num) => {
  return formatNumberValue(num);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  return formatDateValue(dateString);
};

const getStatusClass = (status) => {
  const classes = {
    completed: 'status-pill status-pill--success',
    pending: 'status-pill status-pill--warning',
    failed: 'status-pill status-pill--danger',
    cancelled: 'status-pill status-pill--neutral'
  };
  return classes[status] || 'status-pill status-pill--neutral';
};

const loadDashboard = async () => {
  try {
    loading.value = true;
    const response = await api.get('/admin/dashboard');
    
    if (response.data.status === 'success') {
      dashboardData.value = response.data.data;
    } else {
      showToast('Failed to load dashboard data', 'error');
    }
  } catch (error) {
    console.error('Dashboard load error:', error);
    showToast(error.response?.data?.message || 'Failed to load dashboard', 'error');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadDashboard();
});
</script>

<style scoped>
.dashboard-hero {
  display: grid;
  grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
  gap: 1.5rem;
  padding: 1.75rem 2rem;
  border-radius: 1.5rem;
  border: 1px solid rgba(142, 14, 63, 0.2);
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(247, 239, 233, 0.82)),
    linear-gradient(90deg, rgba(142, 14, 63, 0.06), transparent 45%, rgba(109, 72, 56, 0.08));
  box-shadow: 0 25px 55px rgba(24, 12, 8, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(6px);
}

.hero-copy {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.hero-kicker {
  font-size: 0.7rem;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: var(--admin-text-secondary);
  font-weight: 700;
}

.hero-title {
  font-size: 2.1rem;
  line-height: 1.1;
  color: var(--admin-text-primary);
  text-shadow: 0 2px 14px rgba(142, 14, 63, 0.18);
}

.hero-subtitle {
  color: var(--admin-text-secondary);
  max-width: 480px;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.hero-status {
  display: grid;
  gap: 0.8rem;
}

.status-card {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(142, 14, 63, 0.2);
  border-radius: 1rem;
  padding: 1rem 1.25rem;
  box-shadow: 0 16px 35px rgba(22, 12, 8, 0.08);
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.status-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: var(--admin-text-secondary);
}

.status-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--admin-text-primary);
}

.dashboard-topbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(140, 108, 95, 0.2);
  border-radius: 1.1rem;
  box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08);
  backdrop-filter: blur(8px);
}

.topbar-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.status-chip {
  background: rgba(142, 14, 63, 0.12);
  color: var(--admin-text-primary);
  padding: 0.4rem 0.8rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

@media (max-width: 1024px) {
  .dashboard-hero {
    grid-template-columns: 1fr;
  }
}
</style>
