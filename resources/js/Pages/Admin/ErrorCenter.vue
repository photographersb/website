<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Error Center</h1>
        <p class="text-gray-600 mt-1">Monitor, track, and manage system errors in real-time</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="refreshData"
          class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <i class="fas fa-sync-alt"></i> Refresh
        </button>
        <button
          @click="exportErrors"
          class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors"
        >
          <i class="fas fa-download"></i> Export CSV
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Total Errors</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
          <i class="fas fa-exclamation-circle text-4xl text-red-500 opacity-20"></i>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Open Errors</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.open }}</p>
          </div>
          <i class="fas fa-circle-notch text-4xl text-orange-500 opacity-20"></i>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Critical (P0)</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.critical }}</p>
          </div>
          <i class="fas fa-fire text-4xl text-yellow-500 opacity-20"></i>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Muted</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.muted }}</p>
          </div>
          <i class="fas fa-volume-mute text-4xl text-blue-500 opacity-20"></i>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Resolved</p>
            <p class="text-3xl font-bold text-gray-900">{{ stats.resolved }}</p>
          </div>
          <i class="fas fa-check-circle text-4xl text-green-500 opacity-20"></i>
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
            @keyup.enter="applyFilters"
            type="text"
            placeholder="Error message, file, or class..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
          />
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="filters.status"
            @change="applyFilters"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
          >
            <option value="">All Statuses</option>
            <option value="open">Open</option>
            <option value="resolved">Resolved</option>
            <option value="muted">Muted</option>
          </select>
        </div>

        <!-- Severity Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
          <select
            v-model="filters.severity"
            @change="applyFilters"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
          >
            <option value="">All Severities</option>
            <option value="P0">🔴 Critical (P0)</option>
            <option value="P1">🟠 High (P1)</option>
            <option value="P2">🟡 Medium (P2)</option>
            <option value="P3">🔵 Low (P3)</option>
            <option value="P4">⚪ Info (P4)</option>
          </select>
        </div>

        <!-- Environment Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Environment</label>
          <select
            v-model="filters.environment"
            @change="applyFilters"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
          >
            <option value="">All Environments</option>
            <option value="production">Production</option>
            <option value="staging">Staging</option>
            <option value="development">Development</option>
          </select>
        </div>
      </div>

      <!-- Date Range -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
          <input
            v-model="filters.dateFrom"
            @change="applyFilters"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
          <input
            v-model="filters.dateTo"
            @change="applyFilters"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
          />
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-3 pt-2">
        <button
          @click="applyFilters"
          class="px-6 py-2 text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors"
        >
          <i class="fas fa-filter"></i> Apply Filters
        </button>
        <button
          @click="resetFilters"
          class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
        >
          <i class="fas fa-times"></i> Clear Filters
        </button>
      </div>
    </div>

    <!-- Errors Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Severity</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Error Message</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Occurrences</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Last Occurred</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="errors.length === 0" class="hover:bg-gray-50">
              <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2 block opacity-50"></i>
                No errors found. Your system is running smoothly!
              </td>
            </tr>
            <tr v-for="error in errors" :key="error.id" class="hover:bg-gray-50 transition-colors">
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
                  <p class="font-mono text-gray-700">{{ formatFile(error.file) }}</p>
                  <p class="text-gray-500">Line {{ error.line }}</p>
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
                    <i class="fas fa-check"></i> Resolved
                  </span>
                  <span
                    v-else-if="error.is_muted"
                    class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    <i class="fas fa-volume-mute"></i> Muted
                  </span>
                  <span v-else class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    <i class="fas fa-circle"></i> Open
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
                    @click="showDetails(error)"
                    class="px-3 py-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors"
                    title="View full error details"
                  >
                    <i class="fas fa-eye"></i> View
                  </button>

                  <!-- Toggle Resolve -->
                  <button
                    v-if="!error.is_resolved"
                    @click="toggleResolve(error)"
                    class="px-3 py-1 text-xs text-green-600 hover:text-green-700 font-medium transition-colors"
                    title="Mark as resolved"
                  >
                    <i class="fas fa-check"></i> Resolve
                  </button>
                  <button
                    v-else
                    @click="toggleResolve(error)"
                    class="px-3 py-1 text-xs text-orange-600 hover:text-orange-700 font-medium transition-colors"
                    title="Reopen this error"
                  >
                    <i class="fas fa-undo"></i> Reopen
                  </button>

                  <!-- Toggle Mute -->
                  <button
                    v-if="!error.is_muted"
                    @click="toggleMute(error)"
                    class="px-3 py-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors"
                    title="Mute similar errors"
                  >
                    <i class="fas fa-volume-mute"></i> Mute
                  </button>
                  <button
                    v-else
                    @click="toggleMute(error)"
                    class="px-3 py-1 text-xs text-gray-600 hover:text-gray-700 font-medium transition-colors"
                    title="Unmute this error"
                  >
                    <i class="fas fa-volume-up"></i> Unmute
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="px-6 py-4 border-t bg-gray-50 flex items-center justify-between">
        <div class="text-sm text-gray-600">
          Showing {{ (pagination.page - 1) * pagination.per_page + 1 }} to
          {{ Math.min(pagination.page * pagination.per_page, pagination.total) }} of
          {{ pagination.total }} errors
        </div>
        <div class="flex gap-2">
          <button
            @click="pagination.page--"
            :disabled="pagination.page <= 1"
            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Previous
          </button>
          <div class="flex items-center gap-1">
            <span class="text-sm text-gray-600"
              >Page {{ pagination.page }} of {{ pagination.total_pages }}</span
            >
          </div>
          <button
            @click="pagination.page++"
            :disabled="pagination.page >= pagination.total_pages"
            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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
      <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full max-h-96 overflow-y-auto" @click.stop>
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-start justify-between">
            <div>
              <h2 class="text-xl font-bold text-gray-900">Error Details</h2>
              <p class="text-sm text-gray-600 mt-1">{{ selectedError.exception_class }}</p>
            </div>
            <button
              @click="selectedError = null"
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <!-- Message -->
          <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Error Message</h3>
            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg break-words">{{ selectedError.message }}</p>
          </div>

          <!-- Location -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-semibold text-gray-900 mb-2">File</h3>
              <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg font-mono break-all">
                {{ selectedError.file }}
              </p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-900 mb-2">Line</h3>
              <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ selectedError.line }}</p>
            </div>
          </div>

          <!-- Stack Trace (Super Admin Only) -->
          <div v-if="selectedError.trace">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Stack Trace</h3>
            <pre class="text-xs text-gray-700 bg-gray-50 p-3 rounded-lg overflow-x-auto max-h-40">{{
              selectedError.trace
            }}</pre>
          </div>

          <!-- Metadata -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-semibold text-gray-900 mb-2">URL</h3>
              <p class="text-xs text-gray-700 bg-gray-50 p-3 rounded-lg break-all">{{ selectedError.url }}</p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-900 mb-2">IP Address</h3>
              <p class="text-xs text-gray-700 bg-gray-50 p-3 rounded-lg">{{ selectedError.ip }}</p>
            </div>
          </div>

          <!-- Resolution Notes (if resolved) -->
          <div v-if="selectedError.is_resolved && selectedError.notes">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Resolution Notes</h3>
            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ selectedError.notes }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';

export default {
  name: 'ErrorCenter',
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

    // Fetch errors from API
    const fetchErrors = async () => {
      loading.value = true;
      try {
        const params = new URLSearchParams({
          page: pagination.value.page,
          per_page: pagination.value.per_page,
          ...(filters.value.search && { search: filters.value.search }),
          ...(filters.value.status && { status: filters.value.status }),
          ...(filters.value.severity && { severity: filters.value.severity }),
          ...(filters.value.environment && { environment: filters.value.environment }),
          ...(filters.value.dateFrom && { from: filters.value.dateFrom }),
          ...(filters.value.dateTo && { to: filters.value.dateTo }),
        });

        const token = localStorage.getItem('auth_token');
        const response = await fetch(`/api/v1/admin/error-logs?${params}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          }
        });
        const data = await response.json();

        if (data.status === 'success') {
          errors.value = data.data;
          pagination.value = data.pagination;
        }
      } catch (error) {
        console.error('Failed to fetch errors:', error);
      } finally {
        loading.value = false;
      }
    };

    // Fetch statistics
    const fetchStats = async () => {
      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch('/api/v1/admin/error-logs/statistics', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          }
        });
        const data = await response.json();

        if (data.status === 'success') {
          stats.value = data.data;
        }
      } catch (error) {
        console.error('Failed to fetch statistics:', error);
      }
    };

    // Toggle resolve status
    const toggleResolve = async (error) => {
      const endpoint = error.is_resolved ? 'unresolve' : 'resolve';
      const action = error.is_resolved ? 'unresolve' : 'resolve';

      try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch(`/api/v1/admin/error-logs/${error.id}/${endpoint}`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            notes: action === 'resolve' ? `Resolved by admin at ${new Date().toLocaleString()}` : '',
          }),
        });

        const data = await response.json();
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
        const token = localStorage.getItem('auth_token');
        const response = await fetch(`/api/v1/admin/error-logs/${error.id}/${endpoint}`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
        });

        const data = await response.json();
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
        const token = localStorage.getItem('auth_token');
        const response = await fetch(`/api/v1/admin/error-logs/${error.id}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          }
        });
        const data = await response.json();

        if (data.status === 'success') {
          selectedError.value = data.data;
        }
      } catch (error) {
        console.error('Failed to fetch error details:', error);
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
      return new Date(date).toLocaleString();
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
      stats,
      selectedError,
      loading,
      pagination,
      filters,
      fetchErrors,
      fetchStats,
      toggleResolve,
      toggleMute,
      showDetails,
      formatFile,
      formatDate,
      severityClass,
      applyFilters,
      resetFilters,
      exportErrors,
      refreshData,
      watchPagination,
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
</style>
