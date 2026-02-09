<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="📊 Activity Logs"
      subtitle="Track user activity across the platform"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="content-card">
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
              placeholder="Search activity..."
              class="search-input"
              @input="debounceSearch"
            >
          </div>

          <input
            v-model="filters.model_type"
            type="text"
            placeholder="Model type"
            class="filter-input"
            @input="debounceSearch"
          >
          <input
            v-model="filters.action"
            type="text"
            placeholder="Action"
            class="filter-input"
            @input="debounceSearch"
          >
          <input
            v-model="filters.from_date"
            type="date"
            class="filter-input"
            @change="fetchLogs(1)"
          >
          <input
            v-model="filters.to_date"
            type="date"
            class="filter-input"
            @change="fetchLogs(1)"
          >
        </div>

        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading activity logs...</p>
        </div>

        <div
          v-else-if="logs.length > 0"
          class="logs-list"
        >
          <div
            v-for="log in logs"
            :key="log.id"
            class="log-item"
          >
            <div class="log-main">
              <div class="log-title">
                {{ log.action }} • {{ log.model_type || 'N/A' }}
              </div>
              <div class="log-description">
                {{ log.description || '—' }}
              </div>
            </div>
            <div class="log-meta">
              <span>{{ log.user?.name || 'System' }}</span>
              <span>{{ formatDate(log.created_at) }}</span>
            </div>
          </div>
        </div>

        <div
          v-else-if="!hasFilters"
          class="empty-state"
        >
          <div class="empty-icon">
            🔎
          </div>
          <p class="empty-title">
            Apply a filter to view logs
          </p>
        </div>

        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            📊
          </div>
          <p class="empty-title">
            No activity found
          </p>
        </div>

        <div
          v-if="meta.total > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ logs.length }} of {{ meta.total }} logs
          </div>
          <div class="pagination-controls">
            <button
              :disabled="meta.current_page <= 1"
              class="pagination-btn"
              @click="changePage(meta.current_page - 1)"
            >
              Previous
            </button>
            <span class="pagination-current">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
            <button
              :disabled="meta.current_page >= meta.last_page"
              class="pagination-btn"
              @click="changePage(meta.current_page + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showToast"
      class="toast"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'

const logs = ref([])
const loading = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({
  search: '',
  model_type: '',
  action: '',
  from_date: '',
  to_date: ''
})

const meta = ref({ total: 0, current_page: 1, last_page: 1 })
const hasFilters = computed(() => {
  const current = filters.value
  return Boolean(
    current.search
      || current.model_type
      || current.action
      || current.from_date
      || current.to_date
  )
})

let searchTimeout = null
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchLogs(1), 400)
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, 3000)
}

const fetchLogs = async (page = 1) => {
  if (!hasFilters.value) {
    logs.value = []
    meta.value = { total: 0, current_page: 1, last_page: 1 }
    loading.value = false
    return
  }

  loading.value = true
  try {
    const params = { page }
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.model_type) params.model_type = filters.value.model_type
    if (filters.value.action) params.action = filters.value.action
    if (filters.value.from_date) params.from_date = filters.value.from_date
    if (filters.value.to_date) params.to_date = filters.value.to_date

    const { data } = await api.get('/admin/activity-logs', { params })
    if (data.status === 'success') {
      logs.value = data.data
      meta.value = {
        total: data.meta?.total || data.data.length,
        current_page: data.meta?.current_page || 1,
        last_page: data.meta?.last_page || 1
      }
    }
  } catch (error) {
    console.error('Error fetching activity logs', error)
    showToastMessage('Error loading activity logs')
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  if (!hasFilters.value) return
  if (page >= 1 && page <= meta.value.last_page) fetchLogs(page)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}-${month}-${year}`
}

onMounted(() => {
  fetchLogs()
})
</script>

<style scoped>
.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.filters-bar { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin-bottom: 1.5rem; }
.search-box { position: relative; flex: 1; min-width: 220px; }
.search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; width: 1.25rem; height: 1.25rem; }
.search-input { width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; }
.filter-input { padding: 0.625rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
.logs-list { display: flex; flex-direction: column; gap: 0.75rem; }
.log-item { display: flex; justify-content: space-between; gap: 1rem; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem 1rem; }
.log-title { font-weight: 700; color: #111827; }
.log-description { color: #4b5563; }
.log-meta { display: flex; flex-direction: column; gap: 0.25rem; color: #6b7280; font-size: 0.875rem; text-align: right; }
.empty-state { text-align: center; padding: 2rem 0; }
.empty-icon { font-size: 2rem; }
.loading-state { display: flex; flex-direction: column; align-items: center; padding: 2rem 0; }
.spinner { width: 2rem; height: 2rem; border: 3px solid #e5e7eb; border-top: 3px solid var(--admin-brand-primary); border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
.pagination-btn { 
  padding: 0.4rem 0.75rem; 
  border: 1px solid var(--admin-brand-primary); 
  border-radius: 0.5rem; 
  background: white; 
  color: var(--admin-brand-primary);
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}
.pagination-btn:hover:not(:disabled) { 
  background: var(--admin-brand-primary); 
  color: white; 
}
.pagination-btn:disabled { 
  border-color: #e5e7eb; 
  color: #9ca3af; 
  cursor: not-allowed; 
}
.pagination-current { 
  color: var(--admin-brand-primary); 
  font-weight: 600; 
}
.toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--admin-success-dark); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; z-index: 1001; }
</style>
