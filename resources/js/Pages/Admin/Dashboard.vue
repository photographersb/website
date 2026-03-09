<template>
  <div class="min-h-screen bg-gray-50">
    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="toastVisible = false"
    />

    <AdminHeader
      title="📊 Admin Command Center"
      subtitle="Real-time platform insights, monitoring, and operations"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <section class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-6 shadow-sm">
        <div class="grid grid-cols-1 xl:grid-cols-[1.4fr,1fr] gap-6">
          <div>
            <p class="text-xs font-bold tracking-[0.24em] text-gray-500 uppercase">Admin HQ</p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-900">Platform command center</h1>
            <p class="mt-2 text-sm sm:text-base text-gray-600 max-w-2xl">
              Monitor growth, operations, health, and risk from one dashboard without leaving admin workflow.
            </p>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
              <button
                type="button"
                class="btn-admin-primary"
                @click="go('/admin/events/create')"
              >
                Create Event
              </button>
              <button
                type="button"
                class="btn-admin-primary"
                @click="go('/admin/competitions/create')"
              >
                Create Competition
              </button>
              <button
                type="button"
                class="btn-admin-secondary"
                @click="go('/admin/verifications')"
              >
                Approve Photographers
              </button>
              <button
                type="button"
                class="btn-admin-secondary"
                @click="go('/admin/notices')"
              >
                Send Platform Notice
              </button>
            </div>
          </div>

          <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 sm:p-5">
            <label class="text-sm font-semibold text-gray-800">Global Admin Search</label>
            <div class="mt-2 flex gap-2">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search users, photographers, events, competitions"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-burgundy"
              >
              <button
                type="button"
                class="btn-admin-outline"
                @click="fetchGlobalSearch"
              >
                Search
              </button>
            </div>

            <div
              v-if="searching"
              class="mt-3 text-sm text-gray-500"
            >
              Searching...
            </div>

            <div
              v-else-if="searchQuery.trim().length >= 2 && globalSearchResults.length === 0"
              class="mt-3 text-sm text-gray-500"
            >
              No results found.
            </div>

            <div
              v-else-if="globalSearchResults.length"
              class="mt-3 space-y-2 max-h-56 overflow-auto"
            >
              <button
                v-for="result in globalSearchResults"
                :key="result.id"
                type="button"
                class="w-full text-left px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50"
                @click="go(result.route)"
              >
                <p class="text-sm font-semibold text-gray-900">{{ result.title }}</p>
                <p class="text-xs text-gray-500">{{ result.type }} • {{ result.subtitle }}</p>
              </button>
            </div>

            <p class="mt-4 text-xs text-gray-500">Current role: <span class="font-semibold uppercase">{{ currentRole }}</span></p>
          </div>
        </div>
      </section>

      <div
        v-if="loading"
        class="text-center py-12"
      >
        <div class="admin-loader animate-spin rounded-full h-12 w-12 mx-auto" />
        <p class="mt-4 text-gray-600">Loading command center...</p>
      </div>

      <template v-else>
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-8 gap-3">
          <button
            v-for="card in metricCards"
            :key="card.key"
            type="button"
            class="text-left bg-white border border-gray-200 rounded-xl p-4 hover:border-burgundy/40 hover:shadow-sm transition"
            @click="go(card.route)"
          >
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ card.label }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ card.value }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ card.meta }}</p>
          </button>
        </section>

        <section class="grid grid-cols-1 2xl:grid-cols-3 gap-6">
          <div class="2xl:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
              <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Live Activity Feed</h2>
                <button
                  type="button"
                  class="btn-admin-outline"
                  @click="loadDashboard"
                >
                  Refresh
                </button>
              </div>

              <div
                v-if="paginatedActivity.length === 0"
                class="py-10 text-center text-gray-500"
              >
                No recent activity found.
              </div>

              <div
                v-else
                class="mt-4 space-y-2"
              >
                <div
                  v-for="item in paginatedActivity"
                  :key="item.id"
                  class="flex items-start justify-between gap-3 p-3 rounded-lg border border-gray-100"
                >
                  <div>
                    <p class="text-sm font-semibold text-gray-900">{{ item.action }}</p>
                    <p class="text-xs text-gray-600">Actor: {{ item.actor || 'System' }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-xs text-gray-500">{{ item.time_ago }}</p>
                    <button
                      type="button"
                      class="text-xs text-burgundy hover:underline"
                      @click="go(item.route || '/admin/activity-logs')"
                    >
                      Open
                    </button>
                  </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                  <button
                    type="button"
                    class="btn-admin-outline"
                    :disabled="activityPage <= 1"
                    @click="activityPage--"
                  >
                    Prev
                  </button>
                  <span class="text-xs text-gray-500">Page {{ activityPage }} / {{ activityTotalPages }}</span>
                  <button
                    type="button"
                    class="btn-admin-outline"
                    :disabled="activityPage >= activityTotalPages"
                    @click="activityPage++"
                  >
                    Next
                  </button>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
              <div class="flex items-center justify-between">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Growth Visualization</h2>
                <button
                  type="button"
                  class="btn-admin-outline"
                  @click="chartsVisible = !chartsVisible"
                >
                  {{ chartsVisible ? 'Hide Charts' : 'Show Charts' }}
                </button>
              </div>

              <div
                ref="chartContainer"
                class="mt-4"
              >
                <div v-if="!chartsVisible" class="text-sm text-gray-500">Charts are lazy-loaded for faster dashboard performance.</div>
                <div
                  v-else
                  class="grid grid-cols-1 lg:grid-cols-2 gap-4"
                >
                  <div class="rounded-lg border border-gray-200 p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">User registrations</h3>
                    <div class="space-y-2">
                      <div
                        v-for="row in userGrowthChart"
                        :key="row.label"
                        class="grid grid-cols-[80px,1fr,44px] items-center gap-2"
                      >
                        <span class="text-xs text-gray-500">{{ row.label }}</span>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                          <div class="h-2 bg-burgundy rounded-full" :style="{ width: row.width }" />
                        </div>
                        <span class="text-xs text-gray-700 text-right">{{ row.value }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="rounded-lg border border-gray-200 p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">Bookings by status</h3>
                    <div class="space-y-2">
                      <div
                        v-for="row in bookingStatusChart"
                        :key="row.label"
                        class="grid grid-cols-[90px,1fr,44px] items-center gap-2"
                      >
                        <span class="text-xs text-gray-500 capitalize">{{ row.label }}</span>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                          <div class="h-2 bg-amber-500 rounded-full" :style="{ width: row.width }" />
                        </div>
                        <span class="text-xs text-gray-700 text-right">{{ row.value }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
              <h2 class="text-lg sm:text-xl font-bold text-gray-900">Navigation Hubs</h2>
              <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                <div
                  v-for="group in navGroups"
                  :key="group.title"
                  class="rounded-lg border border-gray-100 p-3"
                >
                  <h3 class="text-sm font-semibold text-gray-900">{{ group.title }}</h3>
                  <div class="mt-2 flex flex-wrap gap-2">
                    <button
                      v-for="link in group.links"
                      :key="link.route"
                      type="button"
                      class="px-2.5 py-1.5 rounded-md bg-gray-100 text-gray-700 text-xs hover:bg-gray-200"
                      @click="go(link.route)"
                    >
                      {{ link.label }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">System Health</h2>
                <button
                  type="button"
                  class="btn-admin-outline"
                  @click="go('/admin/system-health')"
                >
                  Details
                </button>
              </div>
              <div class="mt-3 space-y-2">
                <div
                  v-for="check in healthChecks"
                  :key="check.key"
                  class="flex items-center justify-between border border-gray-100 rounded-lg px-3 py-2"
                >
                  <div class="flex items-center gap-2">
                    <span :class="['health-dot', `health-dot--${check.status || 'yellow'}`]" />
                    <span class="text-sm font-medium text-gray-900">{{ check.label }}</span>
                  </div>
                  <span class="text-xs text-gray-600">{{ check.message }}</span>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Error Center</h2>
                <button
                  type="button"
                  class="btn-admin-outline"
                  @click="go('/admin/error-center')"
                >
                  Open
                </button>
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Open: {{ errorStats.open }} • Critical: {{ errorStats.critical }} • Resolved: {{ errorStats.resolved }}
              </p>

              <div class="mt-3 space-y-2 max-h-72 overflow-auto">
                <div
                  v-for="err in paginatedErrors"
                  :key="err.id"
                  class="border border-gray-100 rounded-lg p-3"
                >
                  <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold" :class="severityClass(err.severity)">{{ err.severity || 'P3' }}</span>
                    <span class="text-xs text-gray-500">{{ err.timestamp ? formatDate(err.timestamp) : '-' }}</span>
                  </div>
                  <p class="mt-1 text-sm font-medium text-gray-900 line-clamp-2">{{ err.message }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ err.route || '-' }}</p>

                  <div class="mt-2 flex gap-2">
                    <button
                      v-if="!err.is_resolved"
                      type="button"
                      class="btn-admin-secondary"
                      @click="resolveError(err)"
                    >
                      Mark Resolved
                    </button>
                    <button
                      type="button"
                      class="btn-admin-outline"
                      @click="go('/admin/error-center')"
                    >
                      Inspect
                    </button>
                  </div>
                </div>
              </div>

              <div class="pt-2 flex items-center justify-end gap-2">
                <button
                  type="button"
                  class="btn-admin-outline"
                  :disabled="errorPage <= 1"
                  @click="errorPage--"
                >
                  Prev
                </button>
                <span class="text-xs text-gray-500">Page {{ errorPage }} / {{ errorTotalPages }}</span>
                <button
                  type="button"
                  class="btn-admin-outline"
                  :disabled="errorPage >= errorTotalPages"
                  @click="errorPage++"
                >
                  Next
                </button>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Admin Notices</h2>
                <button
                  type="button"
                  class="btn-admin-outline"
                  @click="go('/admin/notices')"
                >
                  Manage
                </button>
              </div>

              <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                <div class="rounded-lg bg-gray-50 p-2">
                  <p class="text-xs text-gray-500">Published</p>
                  <p class="font-semibold">{{ noticesSummary.published }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-2">
                  <p class="text-xs text-gray-500">Draft</p>
                  <p class="font-semibold">{{ noticesSummary.draft }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-2">
                  <p class="text-xs text-gray-500">Scheduled</p>
                  <p class="font-semibold">{{ noticesSummary.scheduled }}</p>
                </div>
              </div>

              <div class="mt-3 space-y-2">
                <div
                  v-for="notice in noticesSummary.recent || []"
                  :key="notice.id"
                  class="rounded-lg border border-gray-100 p-2"
                >
                  <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ notice.title }}</p>
                  <p class="text-xs text-gray-500">{{ notice.priority }} • {{ notice.status }}</p>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5">
              <h2 class="text-lg font-bold text-gray-900">Role Access</h2>
              <div class="mt-3 space-y-2">
                <div
                  v-for="item in roleAccessMatrix"
                  :key="item.role"
                  class="flex items-center justify-between rounded-lg border border-gray-100 px-3 py-2"
                >
                  <span class="text-sm font-medium text-gray-800">{{ item.role }}</span>
                  <span class="text-xs" :class="item.enabled ? 'text-green-600' : 'text-gray-400'">
                    {{ item.enabled ? item.scope : 'No dashboard scope' }}
                  </span>
                </div>
              </div>
              <p class="mt-3 text-xs text-gray-500">Unauthorized admin APIs are already protected by middleware and controller checks.</p>
            </div>
          </div>
        </section>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import Toast from '../../components/ui/Toast.vue';
import {
  formatDate as formatDateValue,
  formatNumber as formatNumberValue,
} from '../../utils/formatters';

const router = useRouter();
const loading = ref(true);
const dashboardData = ref({});
const errorStats = ref({ total: 0, open: 0, critical: 0, resolved: 0 });

const searchQuery = ref('');
const searching = ref(false);
const globalSearchResults = ref([]);
let searchTimeout = null;

const activityPage = ref(1);
const activityPerPage = 8;

const errorPage = ref(1);
const errorPerPage = 4;

const chartsVisible = ref(false);
const chartContainer = ref(null);

const toastVisible = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const showToast = (message, type = 'success') => {
  toastMessage.value = message;
  toastType.value = type;
  toastVisible.value = true;
};

const go = (path) => {
  if (!path) return;
  router.push(path);
};

const stats = computed(() => dashboardData.value.stats || {});
const healthChecks = computed(() => dashboardData.value.system_health_panel || []);
const noticesSummary = computed(() => dashboardData.value.notice_summary || { published: 0, draft: 0, scheduled: 0, recent: [] });
const currentRole = computed(() => dashboardData.value.current_user?.role || 'admin');

const metricCards = computed(() => [
  { key: 'total_photographers', label: 'Total photographers', value: formatNumberValue(stats.value.total_photographers || 0), meta: `${formatNumberValue(stats.value.verified_photographers || 0)} verified`, route: '/admin/photographers' },
  { key: 'verified_photographers', label: 'Verified photographers', value: formatNumberValue(stats.value.verified_photographers || 0), meta: `${formatNumberValue(stats.value.pending_verifications || 0)} pending`, route: '/admin/verifications' },
  { key: 'active_events', label: 'Active events', value: formatNumberValue(stats.value.active_events || stats.value.total_events || 0), meta: `${formatNumberValue(stats.value.upcoming_events || 0)} upcoming`, route: '/admin/events' },
  { key: 'running_competitions', label: 'Running competitions', value: formatNumberValue(stats.value.running_competitions || stats.value.active_competitions || 0), meta: `${formatNumberValue(stats.value.total_submissions || 0)} submissions`, route: '/admin/competitions' },
  { key: 'total_bookings', label: 'Total bookings', value: formatNumberValue(stats.value.total_bookings || 0), meta: `${formatNumberValue(stats.value.pending_bookings || 0)} pending`, route: '/admin/bookings' },
  { key: 'total_inquiries', label: 'Total inquiries', value: formatNumberValue(stats.value.total_inquiries || 0), meta: 'Contact messages', route: '/admin/contact-messages' },
  { key: 'total_users', label: 'Total users', value: formatNumberValue(stats.value.total_users || 0), meta: `${formatNumberValue(stats.value.active_users || 0)} active`, route: '/admin/users' },
  { key: 'total_revenue', label: 'Total revenue', value: `৳${formatNumberValue(stats.value.total_revenue || 0)}`, meta: `৳${formatNumberValue(stats.value.monthly_revenue || 0)} this month`, route: '/admin/transactions' },
]);

const activityFeed = computed(() => dashboardData.value.activity_feed || []);
const activityTotalPages = computed(() => Math.max(1, Math.ceil(activityFeed.value.length / activityPerPage)));
const paginatedActivity = computed(() => {
  const start = (activityPage.value - 1) * activityPerPage;
  return activityFeed.value.slice(start, start + activityPerPage);
});

const errorsPreview = computed(() => dashboardData.value.error_center_preview || []);
const errorTotalPages = computed(() => Math.max(1, Math.ceil(errorsPreview.value.length / errorPerPage)));
const paginatedErrors = computed(() => {
  const start = (errorPage.value - 1) * errorPerPage;
  return errorsPreview.value.slice(start, start + errorPerPage);
});

const userGrowthChart = computed(() => toChartRows(dashboardData.value.user_growth || [], 'month', 'count'));
const bookingStatusChart = computed(() => toChartRows(dashboardData.value.booking_stats || [], 'status', 'count'));

const navGroups = computed(() => [
  {
    title: 'Platform Management',
    links: [
      { label: 'Users', route: '/admin/users' },
      { label: 'Approvals', route: '/admin/approvals' },
      { label: 'Roles', route: '/admin/roles' },
      { label: 'Settings', route: '/admin/settings' },
    ],
  },
  {
    title: 'Photographers & Content',
    links: [
      { label: 'Photographers', route: '/admin/photographers' },
      { label: 'Verifications', route: '/admin/verifications' },
      { label: 'Featured', route: '/admin/featured-photographers' },
      { label: 'Reviews', route: '/admin/reviews' },
    ],
  },
  {
    title: 'Events & Competitions',
    links: [
      { label: 'Events', route: '/admin/events' },
      { label: 'Competitions', route: '/admin/competitions' },
      { label: 'Submissions', route: '/admin/submissions' },
      { label: 'Certificates', route: '/admin/certificates' },
      { label: 'Certificate Automation', route: '/admin/certificates/automation-rules' },
    ],
  },
  {
    title: 'Payments & Analytics',
    links: [
      { label: 'Bookings', route: '/admin/bookings' },
      { label: 'Transactions', route: '/admin/transactions' },
      { label: 'Analytics', route: '/admin/analytics' },
      { label: 'System', route: '/admin/system-health' },
    ],
  },
  {
    title: 'Moderation & System',
    links: [
      { label: 'Error Center', route: '/admin/error-center' },
      { label: 'Activity Logs', route: '/admin/activity-logs' },
      { label: 'Notices', route: '/admin/notices' },
      { label: 'Audit Logs', route: '/admin/audit-logs' },
    ],
  },
]);

const roleAccessMatrix = computed(() => {
  const role = currentRole.value;
  return [
    { role: 'Admins', enabled: ['admin', 'super_admin'].includes(role), scope: 'Full platform control' },
    { role: 'Moderators', enabled: role === 'moderator', scope: 'Moderation tools' },
    { role: 'Judges', enabled: true, scope: 'Judging panel via /judge/dashboard' },
    { role: 'Mentors', enabled: true, scope: 'Mentoring features via /admin/mentors' },
  ];
});

const severityClass = (severity) => {
  const map = {
    P0: 'text-red-700',
    P1: 'text-orange-700',
    P2: 'text-amber-700',
    P3: 'text-blue-700',
    P4: 'text-gray-700',
  };
  return map[severity] || 'text-gray-700';
};

const formatDate = (value) => {
  if (!value) return '-';
  return formatDateValue(value);
};

const toChartRows = (rows, labelKey, valueKey) => {
  const normalized = (rows || []).map((row) => ({
    label: row?.[labelKey] || '-',
    value: Number(row?.[valueKey] || 0),
  }));

  const max = Math.max(1, ...normalized.map((row) => row.value));
  return normalized.slice(-8).map((row) => ({
    ...row,
    width: `${Math.max(6, Math.round((row.value / max) * 100))}%`,
  }));
};

const loadDashboard = async () => {
  try {
    loading.value = true;
    const [dashboardRes, errorStatsRes] = await Promise.all([
      api.get('/admin/dashboard'),
      api.get('/admin/error-logs/statistics').catch(() => ({ data: { data: {} } })),
    ]);

    if (dashboardRes.data?.status === 'success') {
      dashboardData.value = dashboardRes.data.data || {};
    } else {
      showToast('Failed to load dashboard data', 'error');
    }

    errorStats.value = errorStatsRes.data?.data || { total: 0, open: 0, critical: 0, resolved: 0 };
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to load admin dashboard', 'error');
  } finally {
    loading.value = false;
  }
};

const fetchGlobalSearch = async () => {
  const q = searchQuery.value.trim();
  if (q.length < 2) {
    globalSearchResults.value = [];
    return;
  }

  try {
    searching.value = true;
    const { data } = await api.get('/admin/global-search', { params: { q, limit: 5 } });
    globalSearchResults.value = data?.data?.results || [];
  } catch (error) {
    showToast('Global search failed', 'error');
  } finally {
    searching.value = false;
  }
};

const resolveError = async (errorItem) => {
  if (!errorItem?.id) return;
  try {
    await api.post(`/admin/error-logs/${errorItem.id}/resolve`);
    showToast('Issue marked as resolved', 'success');
    await loadDashboard();
  } catch (error) {
    showToast('Failed to resolve issue', 'error');
  }
};

watch(searchQuery, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(fetchGlobalSearch, 350);
});

watch(activityTotalPages, () => {
  if (activityPage.value > activityTotalPages.value) {
    activityPage.value = activityTotalPages.value;
  }
});

watch(errorTotalPages, () => {
  if (errorPage.value > errorTotalPages.value) {
    errorPage.value = errorTotalPages.value;
  }
});

onMounted(async () => {
  await loadDashboard();

  if ('IntersectionObserver' in window && chartContainer.value) {
    const observer = new IntersectionObserver((entries) => {
      if (entries.some((entry) => entry.isIntersecting)) {
        chartsVisible.value = true;
        observer.disconnect();
      }
    }, { threshold: 0.2 });

    observer.observe(chartContainer.value);
  } else {
    chartsVisible.value = true;
  }
});
</script>

<style scoped>
.health-dot {
  width: 0.6rem;
  height: 0.6rem;
  border-radius: 9999px;
  display: inline-flex;
  flex-shrink: 0;
}

.health-dot--green {
  background-color: #16a34a;
}

.health-dot--yellow {
  background-color: #ca8a04;
}

.health-dot--red {
  background-color: #dc2626;
}

@media (max-width: 640px) {
  .btn-admin-primary,
  .btn-admin-secondary,
  .btn-admin-outline {
    font-size: 0.75rem;
    line-height: 1rem;
    padding: 0.45rem 0.65rem;
  }
}
</style>
