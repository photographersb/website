<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="📝 Audit Logs" 
      subtitle="Track all admin actions and system events"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="flex justify-end">
        <button
          class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark flex items-center gap-2"
          @click="exportLogs"
        >
          <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
          Export Logs
        </button>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stat-card stat-blue">
          <div class="stat-icon">
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
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Logs</span>
            <span class="stat-value">{{ stats.total }}</span>
          </div>
        </div>

        <div class="stat-card stat-green">
          <div class="stat-icon">
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
          <div class="stat-content">
            <span class="stat-label">Today</span>
            <span class="stat-value">{{ stats.today }}</span>
          </div>
        </div>

        <div class="stat-card stat-yellow">
          <div class="stat-icon">
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
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Active Users</span>
            <span class="stat-value">{{ stats.activeUsers }}</span>
          </div>
        </div>

        <div class="stat-card stat-purple">
          <div class="stat-icon">
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
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Critical Events</span>
            <span class="stat-value">{{ stats.critical }}</span>
          </div>
        </div>
      </div>

      <div class="content-card">
        <!-- Filters Bar -->
        <div class="filters-bar">
          <div class="search-box">
            <svg
              class="search-icon"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
            <input 
              v-model="filters.search" 
              type="text"
              placeholder="Search logs..." 
              class="search-input" 
              @input="debounceSearch"
            >
          </div>

          <select
            v-model="filters.action"
            class="filter-select"
            @change="applyFilters"
          >
            <option value="">
              All Actions
            </option>
            <option value="created">
              Created
            </option>
            <option value="updated">
              Updated
            </option>
            <option value="deleted">
              Deleted
            </option>
            <option value="login">
              Login
            </option>
            <option value="logout">
              Logout
            </option>
          </select>

          <select
            v-model="filters.user"
            class="filter-select"
            @change="applyFilters"
          >
            <option value="">
              All Users
            </option>
            <option value="admin">
              Admins
            </option>
            <option value="photographer">
              Photographers
            </option>
            <option value="client">
              Clients
            </option>
          </select>

          <input
            v-model="filters.date"
            type="date"
            class="filter-input"
            @change="applyFilters"
          >
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading audit logs...</p>
        </div>

        <!-- Logs Timeline -->
        <div
          v-else-if="logs.length > 0"
          class="logs-timeline"
        >
          <div
            v-for="(log, index) in logs"
            :key="log.id"
            class="log-entry"
          >
            <div class="log-timeline-marker">
              <div :class="`log-icon log-icon-${getActionColor(log.action)}`">
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
                    :d="getActionIcon(log.action)"
                  />
                </svg>
              </div>
              <div
                v-if="index < logs.length - 1"
                class="log-timeline-line"
              />
            </div>

            <div class="log-content">
              <div class="log-header">
                <div class="log-info">
                  <span class="log-user">{{ log.user?.name || 'System' }}</span>
                  <span :class="`log-action badge-${getActionColor(log.action)}`">
                    {{ capitalizeFirst(log.action) }}
                  </span>
                </div>
                <span class="log-time">{{ formatTime(log.created_at) }}</span>
              </div>
              <p class="log-description">
                {{ log.description }}
              </p>
              <div
                v-if="log.metadata"
                class="log-metadata"
              >
                <span class="metadata-label">Details:</span>
                <code class="metadata-code">{{ JSON.stringify(log.metadata, null, 2) }}</code>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            📋
          </div>
          <p class="empty-title">
            No audit logs found
          </p>
          <p class="empty-subtitle">
            System activity will be logged here
          </p>
        </div>

        <!-- Pagination -->
        <div
          v-if="pagination.total > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ (pagination.page - 1) * pagination.per_page + 1 }} to
            {{ Math.min(pagination.page * pagination.per_page, pagination.total) }} of
            {{ pagination.total }} logs
          </div>
          <div class="flex items-center gap-2">
            <button
              :disabled="pagination.page <= 1"
              class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              @click="changePage(pagination.page - 1)"
            >
              Previous
            </button>
            <span class="text-sm text-gray-600">Page {{ pagination.page }} of {{ pagination.total_pages }}</span>
            <button
              :disabled="pagination.page >= pagination.total_pages"
              class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              @click="changePage(pagination.page + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Toast -->
      <div
        v-if="showToast"
        class="toast"
      >
        {{ toastMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import { formatDateTime } from '../../../utils/formatters'
import api from '../../../api'

const logs = ref([])
const pagination = ref({
  page: 1,
  per_page: 50,
  total: 0,
  total_pages: 1
})
const loading = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({
  search: '',
  action: '',
  user: '',
  date: ''
})

const stats = computed(() => {
  const now = new Date()
  const todayLogs = logs.value.filter(log => {
    const logDate = new Date(log.created_at)
    return logDate.toDateString() === now.toDateString()
  })
  
  const criticalActions = ['deleted', 'suspended', 'rejected']
  const critical = logs.value.filter(log => criticalActions.includes(log.action)).length
  
  const uniqueUsers = new Set(logs.value.map(log => log.user_id)).size
  
  return {
    total: logs.value.length,
    today: todayLogs.length,
    activeUsers: uniqueUsers,
    critical
  }
})

let searchTimeout = null

const inferActionType = (description) => {
  const text = (description || '').toLowerCase()
  if (text.includes('created')) return 'created'
  if (text.includes('updated')) return 'updated'
  if (text.includes('deleted')) return 'deleted'
  if (text.includes('login')) return 'login'
  if (text.includes('logout')) return 'logout'
  if (text.includes('approved')) return 'approved'
  if (text.includes('rejected')) return 'rejected'
  if (text.includes('suspended')) return 'suspended'
  return 'info'
}

const buildMetadata = (log) => {
  const metadata = {}
  if (log.model_type) metadata.model_type = log.model_type
  if (log.model_id) metadata.model_id = log.model_id
  if (log.new_values) metadata.new_values = log.new_values
  if (log.old_values) metadata.old_values = log.old_values
  return Object.keys(metadata).length > 0 ? metadata : null
}

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}

const applyFilters = () => {
  pagination.value.page = 1
  fetchLogs()
}

const fetchLogs = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.value.page,
      per_page: pagination.value.per_page,
      ...(filters.value.search && { search: filters.value.search }),
      ...(filters.value.action && { action: filters.value.action }),
      ...(filters.value.user && { user: filters.value.user }),
      ...(filters.value.date && { date: filters.value.date }),
    }

    const { data } = await api.get('/admin/audit-logs', { params })
    const items = Array.isArray(data.data) ? data.data : []
    logs.value = items.map((log) => {
      const description = log.action || ''
      const actionType = inferActionType(description)
      return {
        ...log,
        user: log.admin || log.user || null,
        description,
        action: actionType,
        metadata: buildMetadata(log)
      }
    })
    if (data.pagination) {
      pagination.value = {
        ...pagination.value,
        page: data.pagination.current_page,
        per_page: data.pagination.per_page,
        total: data.pagination.total,
        total_pages: data.pagination.last_page
      }
    }
  } catch (error) {
    console.error('Error fetching logs:', error)
    showToastMessage('Error loading audit logs')
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  pagination.value.page = page
  fetchLogs()
}

const exportLogs = () => {
  showToastMessage('Export feature coming soon')
}

const getActionColor = (action) => {
  const colors = {
    created: 'green',
    updated: 'blue',
    deleted: 'red',
    login: 'purple',
    logout: 'gray',
    approved: 'green',
    rejected: 'red',
    suspended: 'orange'
  }
  return colors[action] || 'blue'
}

const getActionIcon = (action) => {
  const icons = {
    created: 'M12 4v16m8-8H4',
    updated: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
    deleted: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    login: 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
    logout: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'
  }
  return icons[action] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
}

const capitalizeFirst = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

const formatTime = (date) => {
  if (!date) return 'N/A'
  return formatDateTime(date)
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

onMounted(() => {
  fetchLogs()
})
</script>

<style scoped>
.admin-audit-logs { padding: 2rem; min-height: 100vh; background: var(--admin-bg-page); }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-title { font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0; }
.page-subtitle { color: #6b7280; margin: 0.5rem 0 0 0; }

.btn-export-main { display: flex; align-items: center; background: var(--admin-brand-primary); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600; transition: background 0.2s; }
.btn-export-main:hover { background: var(--admin-brand-primary-dark); }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 1rem; border-left: 4px solid; }
.stat-blue { border-color: var(--admin-brand-primary); }
.stat-green { border-color: var(--admin-brand-primary); }
.stat-yellow { border-color: var(--admin-brand-primary); }
.stat-purple { border-color: var(--admin-brand-primary); }
.stat-icon { width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; }
.stat-blue .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-green .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-yellow .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-purple .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-content { flex: 1; }
.stat-label { display: block; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem; }
.stat-value { display: block; font-size: 2rem; font-weight: 700; color: #1f2937; }

.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.filters-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.search-box { position: relative; flex: 1; min-width: 300px; }
.search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: #9ca3af; }
.search-input { width: 100%; padding: 0.75rem 1rem 0.75rem 3rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; }
.search-input:focus { outline: none; border-color: var(--admin-brand-primary); box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.12); }
.filter-select, .filter-input { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer; }
.filter-select:focus, .filter-input:focus { outline: none; border-color: var(--admin-brand-primary); }

.loading-state { text-align: center; padding: 3rem; color: #6b7280; }
.spinner { width: 3rem; height: 3rem; border: 3px solid #e5e7eb; border-top-color: var(--admin-brand-primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
@keyframes spin { to { transform: rotate(360deg); } }

.logs-timeline { position: relative; }
.log-entry { display: grid; grid-template-columns: 2.5rem 1fr; gap: 1rem; margin-bottom: 1.5rem; }
.log-timeline-marker { position: relative; display: flex; flex-direction: column; align-items: center; }
.log-icon { width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; z-index: 1; }
.log-icon-green { background: var(--admin-success); }
.log-icon-blue { background: var(--admin-brand-primary); }
.log-icon-red { background: var(--admin-danger); }
.log-icon-purple { background: var(--admin-brand-primary); }
.log-icon-orange { background: var(--admin-warning); }
.log-icon-gray { background: #6b7280; }
.log-timeline-line { width: 2px; height: 100%; background: #e5e7eb; flex: 1; margin-top: 0.5rem; }

.log-content { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; }
.log-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.log-info { display: flex; align-items: center; gap: 0.75rem; }
.log-user { font-weight: 600; color: #1f2937; }
.log-action { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.badge-green { background: var(--admin-success-light); color: var(--admin-success-text); }
.badge-blue { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-red { background: var(--admin-danger-light); color: var(--admin-danger-text); }
.badge-purple { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-orange { background: var(--admin-warning-light); color: var(--admin-warning-text); }
.badge-gray { background: var(--admin-bg-hover); color: var(--admin-text-secondary); }
.log-time { font-size: 0.875rem; color: #6b7280; }
.log-description { color: #1f2937; margin: 0; line-height: 1.5; }
.log-metadata { margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb; }
.metadata-label { font-size: 0.875rem; font-weight: 600; color: #6b7280; }
.metadata-code { display: block; background: white; border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 0.5rem; margin-top: 0.5rem; font-size: 0.75rem; color: #374151; overflow-x: auto; }

.empty-state { text-align: center; padding: 4rem 2rem; color: #9ca3af; }
.empty-icon { font-size: 5rem; margin-bottom: 1rem; opacity: 0.5; }
.empty-title { font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem; }
.empty-subtitle { color: #9ca3af; }

.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; }
.pagination-info { color: #6b7280; font-size: 0.875rem; }

.toast { position: fixed; bottom: 2rem; right: 2rem; background: #065f46; color: white; padding: 1rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); animation: slideIn 0.3s ease-out; z-index: 1001; }
@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
.mr-2 { margin-right: 0.5rem; }
</style>
