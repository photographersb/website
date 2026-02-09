<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Header -->
    <AdminHeader 
      title="🚨 Error Center" 
      subtitle="Monitor, track, and manage system errors in real-time"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />
      
      <!-- Action Buttons -->
      <div class="flex items-center justify-end gap-3">
        <button
          class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          @click="refreshData"
        >
          <i class="fas fa-sync-alt" /> Refresh
        </button>
        <button
          class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors"
          @click="exportErrors"
        >
          <i class="fas fa-download" /> Export CSV
        </button>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">
                Total Errors
              </p>
              <p class="text-3xl font-bold text-gray-900">
                {{ stats.total }}
              </p>
            </div>
            <i class="fas fa-exclamation-circle text-4xl text-red-500 opacity-20" />
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">
                Open Errors
              </p>
              <p class="text-3xl font-bold text-gray-900">
                {{ stats.open }}
              </p>
            </div>
            <i class="fas fa-circle-notch text-4xl text-orange-500 opacity-20" />
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">
                Critical (P0)
              </p>
              <p class="text-3xl font-bold text-gray-900">
                {{ stats.critical }}
              </p>
            </div>
            <i class="fas fa-fire text-4xl text-yellow-500 opacity-20" />
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">
                Muted
              </p>
              <p class="text-3xl font-bold text-gray-900">
                {{ stats.muted }}
              </p>
            </div>
            <i class="fas fa-volume-mute text-4xl text-blue-500 opacity-20" />
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">
                Resolved
              </p>
              <p class="text-3xl font-bold text-gray-900">
                {{ stats.resolved }}
              </p>
            </div>
            <i class="fas fa-check-circle text-4xl text-green-500 opacity-20" />
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Error message, file, or class..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              @keyup.enter="applyFilters"
            >
          </div>

          <!-- Status Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="filters.status"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              @change="applyFilters"
            >
              <option value="">
                All Statuses
              </option>
              <option value="open">
                Open
              </option>
              <option value="resolved">
                Resolved
              </option>
              <option value="muted">
                Muted
              </option>
            </select>
          </div>

          <!-- Severity Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
            <select
              v-model="filters.severity"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              @change="applyFilters"
            >
              <option value="">
                All Severities
              </option>
              <option value="P0">
                🔴 Critical (P0)
              </option>
              <option value="P1">
                🟠 High (P1)
              </option>
              <option value="P2">
                🟡 Medium (P2)
              </option>
              <option value="P3">
                🔵 Low (P3)
              </option>
              <option value="P4">
                ⚪ Info (P4)
              </option>
            </select>
          </div>

          <!-- Environment Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Environment</label>
            <select
              v-model="filters.environment"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              @change="applyFilters"
            >
              <option value="">
                All Environments
              </option>
              <option value="production">
                Production
              </option>
              <option value="staging">
                Staging
              </option>
              <option value="development">
                Development
              </option>
            </select>
          </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
            <input
              v-model="filters.dateFrom"
              type="date"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              @change="applyFilters"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
            <input
              v-model="filters.dateTo"
              type="date"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              @change="applyFilters"
            >
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-2">
          <button
            class="px-6 py-2 text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors"
            @click="applyFilters"
          >
            <i class="fas fa-filter" /> Apply Filters
          </button>
          <button
            class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            @click="resetFilters"
          >
            <i class="fas fa-times" /> Clear Filters
          </button>
        </div>
      </div>

      <div
        v-if="loadError || statsError"
        class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        role="alert"
      >
        {{ loadError || statsError }}
      </div>

      <!-- Errors Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Severity
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Error Message
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Location
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Occurrences
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Last Occurred
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-if="!hasFilters"
                class="hover:bg-gray-50"
              >
                <td
                  colspan="7"
                  class="px-6 py-8 text-center text-gray-500"
                >
                  <i class="fas fa-filter text-3xl mb-2 block opacity-50" />
                  Apply a filter to view errors.
                </td>
              </tr>
              <tr
                v-else-if="safeErrors.length === 0"
                class="hover:bg-gray-50"
              >
                <td
                  colspan="7"
                  class="px-6 py-8 text-center text-gray-500"
                >
                  <i class="fas fa-inbox text-3xl mb-2 block opacity-50" />
                  No errors found. Your system is running smoothly!
                </td>
              </tr>
              <tr
                v-for="error in safeErrors"
                :key="error.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <!-- Severity -->
                <td class="px-6 py-4">
                  <span
                    :class="severityClass(error.severity)"
                    class="px-3 py-1 rounded-full text-xs font-medium"
                  >
                    {{ error.severity }}
                  </span>
                </td>

                <!-- Message -->
                <td class="px-6 py-4">
                  <div class="text-sm">
                    <p class="font-medium text-gray-900 truncate max-w-xs">
                      {{ error.message || 'Unknown Error' }}
                    </p>
                    <p class="text-gray-600 text-xs truncate">
                      {{ error.exception_class }}
                    </p>
                  </div>
                </td>

                <!-- Location -->
                <td class="px-6 py-4 text-sm text-gray-600">
                  <div class="text-xs">
                    <p class="font-mono text-gray-700">
                      {{ formatFile(error.file) }}
                    </p>
                    <p class="text-gray-500">
                      Line {{ error.line }}
                    </p>
                  </div>
                </td>

                <!-- Occurrences -->
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                  {{ error.occurrence_count }}x
                </td>

                <!-- Status -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <span
                      v-if="error.is_resolved"
                      class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                    >
                      <i class="fas fa-check" /> Resolved
                    </span>
                    <span
                      v-else-if="error.is_muted"
                      class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      <i class="fas fa-volume-mute" /> Muted
                    </span>
                    <span
                      v-else
                      class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800"
                    >
                      <i class="fas fa-circle" /> Open
                    </span>
                  </div>
                </td>

                <!-- Last Occurred -->
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ formatDate(error.last_occurrence_at) }}
                </td>

                <!-- Actions -->
                <td class="px-6 py-4">
                  <div class="flex gap-2">
                    <!-- View Details -->
                    <button
                      class="px-3 py-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors"
                      title="View full error details"
                      @click="showDetails(error)"
                    >
                      <i class="fas fa-eye" /> View
                    </button>

                    <!-- Toggle Resolve -->
                    <button
                      v-if="!error.is_resolved"
                      class="px-3 py-1 text-xs text-green-600 hover:text-green-700 font-medium transition-colors"
                      title="Mark as resolved"
                      @click="toggleResolve(error)"
                    >
                      <i class="fas fa-check" /> Resolve
                    </button>
                    <button
                      v-else
                      class="px-3 py-1 text-xs text-orange-600 hover:text-orange-700 font-medium transition-colors"
                      title="Reopen this error"
                      @click="toggleResolve(error)"
                    >
                      <i class="fas fa-undo" /> Reopen
                    </button>

                    <!-- Toggle Mute -->
                    <button
                      v-if="!error.is_muted"
                      class="px-3 py-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors"
                      title="Mute similar errors"
                      @click="toggleMute(error)"
                    >
                      <i class="fas fa-volume-mute" /> Mute
                    </button>
                    <button
                      v-else
                      class="px-3 py-1 text-xs text-gray-600 hover:text-gray-700 font-medium transition-colors"
                      title="Unmute this error"
                      @click="toggleMute(error)"
                    >
                      <i class="fas fa-volume-up" /> Unmute
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="pagination.total > 0"
          class="px-6 py-4 border-t bg-gray-50 flex items-center justify-between"
        >
          <div class="text-sm text-gray-600">
            Showing {{ (pagination.page - 1) * pagination.per_page + 1 }} to
            {{ Math.min(pagination.page * pagination.per_page, pagination.total) }} of
            {{ pagination.total }} errors
          </div>
          <div class="flex gap-2">
            <button
              :disabled="pagination.page <= 1"
              class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              @click="pagination.page--"
            >
              Previous
            </button>
            <div class="flex items-center gap-1">
              <span class="text-sm text-gray-600">Page {{ pagination.page }} of {{ pagination.total_pages }}</span>
            </div>
            <button
              :disabled="pagination.page >= pagination.total_pages"
              class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              @click="pagination.page++"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Error Details Modal -->
      <div
        v-if="selectedError"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        @click="selectedError = null"
      >
        <div
          class="bg-white rounded-lg shadow-lg max-w-3xl w-full max-h-96 overflow-y-auto"
          @click.stop
        >
          <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between">
              <div>
                <h2 class="text-xl font-bold text-gray-900">
                  Error Details
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                  {{ selectedError.exception_class }}
                </p>
              </div>
              <button
                class="text-gray-400 hover:text-gray-600 transition-colors"
                @click="selectedError = null"
              >
                <i class="fas fa-times text-xl" />
              </button>
            </div>
          </div>

          <div class="p-6 space-y-4">
            <!-- Message -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 mb-2">
                Error Message
              </h3>
              <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg break-words">
                {{ selectedError.message }}
              </p>
            </div>

            <!-- Location -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-2">
                  File
                </h3>
                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg font-mono break-all">
                  {{ selectedError.file }}
                </p>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-2">
                  Line
                </h3>
                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">
                  {{ selectedError.line }}
                </p>
              </div>
            </div>

            <!-- Stack Trace (Super Admin Only) -->
            <div v-if="selectedError.trace">
              <h3 class="text-sm font-semibold text-gray-900 mb-2">
                Stack Trace
              </h3>
              <pre class="text-xs text-gray-700 bg-gray-50 p-3 rounded-lg overflow-x-auto max-h-40">{{
              selectedError.trace
              }}</pre>
            </div>

            <div class="detail-grid">
              <div class="detail-card">
                <h3 class="detail-title">
                  Request
                </h3>
                <div class="detail-list">
                  <div>
                    <span class="detail-label">URL</span>
                    <p class="detail-value break-all">
                      {{ selectedError.url || 'N/A' }}
                    </p>
                  </div>
                  <div class="detail-row">
                    <div>
                      <span class="detail-label">Method</span>
                      <p class="detail-value">
                        {{ selectedError.method || 'N/A' }}
                      </p>
                    </div>
                    <div>
                      <span class="detail-label">Status</span>
                      <p class="detail-value">
                        {{ selectedError.status_code || 'N/A' }}
                      </p>
                    </div>
                    <div>
                      <span class="detail-label">Route</span>
                      <p class="detail-value">
                        {{ selectedError.route_name || 'N/A' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="detail-card">
                <h3 class="detail-title">
                  Network
                </h3>
                <div class="detail-list">
                  <div>
                    <span class="detail-label">IP Address</span>
                    <p class="detail-value">
                      {{ selectedError.ip || 'N/A' }}
                    </p>
                  </div>
                  <div>
                    <span class="detail-label">User Agent</span>
                    <p class="detail-value break-all">
                      {{ selectedError.user_agent || 'Unknown' }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="detail-card">
                <h3 class="detail-title">
                  Location
                </h3>
                <div class="detail-list">
                  <div>
                    <span class="detail-label">City</span>
                    <p class="detail-value">
                      {{ selectedError.geo_city || 'Unknown' }}
                    </p>
                  </div>
                  <div class="detail-row">
                    <div>
                      <span class="detail-label">Region</span>
                      <p class="detail-value">
                        {{ selectedError.geo_region || 'Unknown' }}
                      </p>
                    </div>
                    <div>
                      <span class="detail-label">Country</span>
                      <p class="detail-value">
                        {{ selectedError.geo_country || 'Unknown' }}
                      </p>
                    </div>
                  </div>
                  <div>
                    <span class="detail-label">ISP / Timezone</span>
                    <p class="detail-value">
                      {{ selectedError.geo_isp || 'Unknown' }}
                      <span v-if="selectedError.geo_timezone"> • {{ selectedError.geo_timezone }}</span>
                    </p>
                  </div>
                </div>
              </div>
              <div class="detail-card">
                <h3 class="detail-title">
                  Actions
                </h3>
                <div class="detail-actions">
                  <button
                    v-if="selectedError.ip && !selectedError.ip_blocked"
                    class="detail-btn detail-btn--danger"
                    :disabled="ipActionLoading"
                    @click="blockIp(selectedError)"
                  >
                    Block IP
                  </button>
                  <button
                    v-else-if="selectedError.ip && selectedError.ip_blocked"
                    class="detail-btn detail-btn--neutral"
                    :disabled="ipActionLoading"
                    @click="unblockIp(selectedError)"
                  >
                    Unblock IP
                  </button>
                  <button
                    v-if="selectedError.status_code === 429"
                    class="detail-btn detail-btn--warning"
                    :disabled="ipActionLoading"
                    @click="unlockThrottle(selectedError)"
                  >
                    Unlock Rate Limit
                  </button>
                </div>
                <p class="detail-hint">
                  Blocked IPs are enforced globally. Unlock clears the rate limit for this IP.
                </p>
              </div>
            </div>

            <!-- Resolution Notes (if resolved) -->
            <div v-if="selectedError.is_resolved && selectedError.notes">
              <h3 class="text-sm font-semibold text-gray-900 mb-2">
                Resolution Notes
              </h3>
              <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">
                {{ selectedError.notes }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import { formatDateTime } from '../../utils/formatters';
import api from '../../api';

export default {
  name: 'ErrorCenter',
  components: {
    AdminHeader,
    AdminQuickNav
  },
  setup() {
    const errors = ref([]);
    const stats = ref({
      total: 0,
      open: 0,
      resolved: 0,
      critical: 0,
      muted: 0,
    });
    const selectedError = ref(null);
    const loading = ref(false);
    const loadError = ref('');
    const statsError = ref('');
    const ipActionLoading = ref(false);

    const pagination = ref({
      page: 1,
      per_page: 25,
      total: 0,
      total_pages: 1,
    });

    const filters = ref({
      search: '',
      status: '',
      severity: '',
      environment: '',
      dateFrom: '',
      dateTo: '',
    });

    const safeErrors = computed(() => (Array.isArray(errors.value) ? errors.value.filter(Boolean) : []));
    const hasFilters = computed(() => Boolean(
      filters.value.search
        || filters.value.status
        || filters.value.severity
        || filters.value.environment
        || filters.value.dateFrom
        || filters.value.dateTo
    ));

    // Fetch errors from API
    const fetchErrors = async () => {
      if (!hasFilters.value) {
        errors.value = [];
        pagination.value = {
          ...pagination.value,
          page: 1,
          total: 0,
          total_pages: 1,
        };
        loading.value = false;
        return;
      }

      loading.value = true;
      try {
        loadError.value = '';
        const params = {
          page: pagination.value.page,
          per_page: pagination.value.per_page,
          ...(filters.value.search && { search: filters.value.search }),
          ...(filters.value.status && { status: filters.value.status }),
          ...(filters.value.severity && { severity: filters.value.severity }),
          ...(filters.value.environment && { environment: filters.value.environment }),
          ...(filters.value.dateFrom && { from: filters.value.dateFrom }),
          ...(filters.value.dateTo && { to: filters.value.dateTo }),
        };

        const { data } = await api.get('/admin/error-logs', { params });

        if (data.status === 'success') {
          const payload = data.data || {};
          const errorItems = Array.isArray(payload.data)
            ? payload.data
            : Array.isArray(payload)
              ? payload
              : [];
          errors.value = errorItems;

          if (typeof payload.current_page === 'number') {
            pagination.value = {
              ...pagination.value,
              page: payload.current_page,
              per_page: payload.per_page,
              total: payload.total,
              total_pages: payload.last_page,
            };
          } else if (data.pagination && typeof data.pagination.current_page === 'number') {
            pagination.value = {
              ...pagination.value,
              page: data.pagination.current_page,
              per_page: data.pagination.per_page,
              total: data.pagination.total,
              total_pages: data.pagination.last_page,
            };
          } else {
            pagination.value = {
              ...pagination.value,
              total: errorItems.length,
              total_pages: errorItems.length > 0 ? 1 : 0
            };
          }
        }
      } catch (error) {
        console.error('Failed to fetch errors:', error);
        loadError.value = 'Unable to load error logs. Please adjust filters or try again.';
      } finally {
        loading.value = false;
      }
    };

    // Fetch statistics
    const fetchStats = async () => {
      if (!hasFilters.value) {
        stats.value = {
          total: 0,
          open: 0,
          resolved: 0,
          critical: 0,
          muted: 0,
        };
        statsError.value = '';
        return;
      }

      try {
        statsError.value = '';
        const params = {
          ...(filters.value.severity && { severity: filters.value.severity }),
          ...(filters.value.environment && { environment: filters.value.environment }),
          ...(filters.value.dateFrom && { from: filters.value.dateFrom }),
          ...(filters.value.dateTo && { to: filters.value.dateTo }),
        };
        const { data } = await api.get('/admin/error-logs/statistics', { params });

        if (data.status === 'success') {
          const summary = data.data || {};
          stats.value = {
            total: summary.total || 0,
            open: summary.open || 0,
            resolved: summary.resolved || 0,
            critical: summary.critical || 0,
            muted: summary.muted || 0,
          };
        }
      } catch (error) {
        console.error('Failed to fetch statistics:', error);
        statsError.value = 'Unable to load error statistics. Please try again.';
      }
    };

    // Toggle resolve status
    const toggleResolve = async (error) => {
      const endpoint = error.is_resolved ? 'unresolve' : 'resolve';
      const action = error.is_resolved ? 'unresolve' : 'resolve';

      try {
        const { data } = await api.post(`/admin/error-logs/${error.id}/${endpoint}`, {
          notes: action === 'resolve' ? `Resolved by admin at ${formatDateTime(new Date())}` : '',
        });
        if (data.status === 'success') {
          fetchErrors();
          fetchStats();
        }
      } catch (error) {
        console.error('Failed to toggle resolve:', error);
      }
    };

    // Toggle mute status
    const toggleMute = async (error) => {
      const endpoint = error.is_muted ? 'unmute' : 'mute';

      try {
        const { data } = await api.post(`/admin/error-logs/${error.id}/${endpoint}`);
        if (data.status === 'success') {
          fetchErrors();
          fetchStats();
        }
      } catch (error) {
        console.error('Failed to toggle mute:', error);
      }
    };

    // Show error details
    const showDetails = async (error) => {
      try {
        const { data } = await api.get(`/admin/error-logs/${error.id}`);

        if (data.status === 'success') {
          selectedError.value = data.data;
        }
      } catch (error) {
        console.error('Failed to fetch error details:', error);
      }
    };

    const blockIp = async (error) => {
      if (!error?.ip) return;
      ipActionLoading.value = true;
      try {
        const { data } = await api.post(`/admin/error-logs/${error.id}/block-ip`);
        if (data.status === 'success' && selectedError.value) {
          selectedError.value.ip_blocked = true;
        }
      } catch (err) {
        console.error('Failed to block IP:', err);
      } finally {
        ipActionLoading.value = false;
      }
    };

    const unblockIp = async (error) => {
      if (!error?.ip) return;
      ipActionLoading.value = true;
      try {
        const { data } = await api.post(`/admin/error-logs/${error.id}/unblock-ip`);
        if (data.status === 'success' && selectedError.value) {
          selectedError.value.ip_blocked = false;
        }
      } catch (err) {
        console.error('Failed to unblock IP:', err);
      } finally {
        ipActionLoading.value = false;
      }
    };

    const unlockThrottle = async (error) => {
      if (!error?.ip) return;
      ipActionLoading.value = true;
      try {
        const { data } = await api.post(`/admin/error-logs/${error.id}/unlock-throttle`);
        if (data.status === 'success') {
          await fetchErrors();
        }
      } catch (err) {
        console.error('Failed to unlock throttle:', err);
      } finally {
        ipActionLoading.value = false;
      }
    };

    // Format file path
    const formatFile = (file) => {
      if (!file) return 'Unknown';
      const parts = file.split('\\');
      return parts.slice(-3).join('/');
    };

    // Format date
    const formatDate = (date) => {
      if (!date) return 'N/A';
      return formatDateTime(date);
    };

    // Get severity class
    const severityClass = (severity) => {
      const classes = {
        P0: 'bg-red-100 text-red-800',
        P1: 'bg-orange-100 text-orange-800',
        P2: 'bg-yellow-100 text-yellow-800',
        P3: 'bg-blue-100 text-blue-800',
        P4: 'bg-gray-100 text-gray-800',
      };
      return classes[severity] || 'bg-gray-100 text-gray-800';
    };

    // Apply filters
    const applyFilters = () => {
      pagination.value.page = 1;
      fetchErrors();
      fetchStats();
    };

    // Reset filters
    const resetFilters = () => {
      filters.value = {
        search: '',
        status: '',
        severity: '',
        environment: '',
        dateFrom: '',
        dateTo: '',
      };
      pagination.value.page = 1;
      fetchErrors();
      fetchStats();
    };

    // Export errors as CSV
    const exportErrors = async () => {
      try {
        const params = new URLSearchParams({
          ...(filters.value.search && { search: filters.value.search }),
          ...(filters.value.status && { status: filters.value.status }),
          ...(filters.value.severity && { severity: filters.value.severity }),
          ...(filters.value.environment && { environment: filters.value.environment }),
          ...(filters.value.dateFrom && { from: filters.value.dateFrom }),
          ...(filters.value.dateTo && { to: filters.value.dateTo }),
        });

        window.location.href = `/api/v1/admin/error-logs/export?${params}`;
      } catch (error) {
        console.error('Failed to export errors:', error);
      }
    };

    // Refresh data
    const refreshData = () => {
      fetchErrors();
      fetchStats();
    };

    // Initialize
    onMounted(() => {
      fetchErrors();
      fetchStats();

      // Auto-refresh every 30 seconds
      setInterval(() => {
        fetchStats();
      }, 30000);
    });

    // Watch pagination changes
    const watchPagination = () => {
      fetchErrors();
    };

    return {
      errors,
      safeErrors,
      stats,
      loadError,
      statsError,
      hasFilters,
      selectedError,
      loading,
      pagination,
      filters,
      fetchErrors,
      fetchStats,
      toggleResolve,
      toggleMute,
      showDetails,
      blockIp,
      unblockIp,
      unlockThrottle,
      ipActionLoading,
      formatFile,
      formatDate,
      severityClass,
      applyFilters,
      resetFilters,
      exportErrors,
      refreshData,
      watchPagination,
      AdminHeader,
    };
  },
  watch: {
    'pagination.page'() {
      this.fetchErrors();
    },
  },
};
</script>

<style scoped>
table {
  table-layout: auto;
}

tr:hover {
  background-color: rgba(249, 250, 251, 0.5);
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem;
}

.detail-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  background: #f9fafb;
  padding: 1rem;
}

.detail-title {
  font-size: 0.875rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.75rem;
}

.detail-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.detail-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 0.5rem;
}

.detail-label {
  display: block;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.detail-value {
  font-size: 0.8rem;
  color: #111827;
  background: #ffffff;
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: 1px solid #e5e7eb;
}

.detail-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.detail-btn {
  padding: 0.4rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  transition: all 0.2s;
}

.detail-btn--danger {
  color: #ffffff;
  background: #dc2626;
}

.detail-btn--danger:hover {
  background: #b91c1c;
}

.detail-btn--neutral {
  color: #374151;
  background: #e5e7eb;
}

.detail-btn--neutral:hover {
  background: #d1d5db;
}

.detail-btn--warning {
  color: #ffffff;
  background: #d97706;
}

.detail-btn--warning:hover {
  background: #b45309;
}

.detail-hint {
  margin-top: 0.75rem;
  font-size: 0.7rem;
  color: #6b7280;
}
</style>