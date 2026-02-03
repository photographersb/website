<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="🎓 Mentors"
      subtitle="Manage mentor profiles and visibility"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="content-card">
        <div class="filters-bar">
          <div class="search-box">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input v-model="filters.search" @input="debounceSearch" type="text" placeholder="Search mentors..." class="search-input" />
          </div>

          <select v-model="filters.status" @change="fetchMentors" class="filter-select">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>

          <button @click="$router.push('/admin/mentors/create')" class="btn-add">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Mentor
          </button>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading mentors...</p>
        </div>

        <div v-else-if="mentors.length > 0" class="grid-list">
          <div v-for="mentor in mentors" :key="mentor.id" class="item-card">
            <div class="item-header">
              <div class="item-title">
                <h3>{{ mentor.name }}</h3>
                <span class="subtext">{{ mentor.title || '—' }}</span>
              </div>
              <span class="status-badge" :class="mentor.is_active ? 'status-active' : 'status-inactive'">
                {{ mentor.is_active ? 'active' : 'inactive' }}
              </span>
            </div>

            <p class="item-description">{{ mentor.organization || 'No organization' }}</p>
            <p class="item-description">{{ mentor.bio || 'No bio' }}</p>

            <div class="meta-row">
              <span>Email: {{ mentor.email || 'N/A' }}</span>
              <span>Phone: {{ mentor.phone || 'N/A' }}</span>
            </div>

            <div class="card-actions">
              <button @click="$router.push(`/admin/mentors/${mentor.id}`)" class="btn-action btn-view">View</button>
              <button @click="$router.push(`/admin/mentors/${mentor.id}/edit`)" class="btn-action btn-edit">Edit</button>
              <button @click="toggleStatus(mentor)" class="btn-action btn-toggle">
                {{ mentor.is_active ? 'Deactivate' : 'Activate' }}
              </button>
              <button @click="deleteMentor(mentor.id)" class="btn-action btn-delete">Delete</button>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">🎓</div>
          <p class="empty-title">No mentors found</p>
          <p class="empty-subtitle">Add your first mentor</p>
        </div>

        <div v-if="meta.total > 0" class="pagination">
          <div class="pagination-info">Showing {{ mentors.length }} of {{ meta.total }} mentors</div>
          <div class="pagination-controls">
            <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="pagination-btn">Previous</button>
            <span class="pagination-current">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
            <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="pagination-btn">Next</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showToast" class="toast">{{ toastMessage }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

const mentors = ref([])
const loading = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({ search: '', status: '' })
const meta = ref({ total: 0, current_page: 1, last_page: 1 })
const stats = ref({ total: 0, active: 0, inactive: 0 })

let searchTimeout = null
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchMentors(), 400)
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, 3000)
}

const fetchMentors = async (page = 1) => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const params = new URLSearchParams()
    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.status) params.append('status', filters.value.status)
    params.append('page', page)

    const response = await fetch(`/api/v1/admin/mentors?${params}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      mentors.value = data.data.data || data.data
      meta.value = {
        total: data.data.total || mentors.value.length,
        current_page: data.data.current_page || 1,
        last_page: data.data.last_page || 1
      }
      if (data.stats) {
        stats.value = data.stats
      }
    }
  } catch (error) {
    console.error('Error fetching mentors', error)
    showToastMessage('Error loading mentors')
  } finally {
    loading.value = false
  }
}

const toggleStatus = async (mentor) => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/mentors/${mentor.id}/toggle-status`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (response.ok && data.status === 'success') {
      mentor.is_active = data.data.is_active
      showToastMessage('Status updated')
    }
  } catch (error) {
    console.error('Error toggling status', error)
    showToastMessage('Error updating status')
  }
}

const deleteMentor = async (id) => {
  if (!confirm('Delete this mentor?')) return
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/mentors/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage('Mentor deleted')
      fetchMentors(meta.value.current_page)
    }
  } catch (error) {
    console.error('Error deleting mentor', error)
    showToastMessage('Error deleting mentor')
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= meta.value.last_page) fetchMentors(page)
}

onMounted(() => {
  fetchMentors()
})
</script>

<style scoped>
.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.filters-bar { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin-bottom: 1.5rem; }
.search-box { position: relative; flex: 1; min-width: 220px; }
.search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; width: 1.25rem; height: 1.25rem; }
.search-input { width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; }
.filter-select { padding: 0.625rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
.btn-add { display: inline-flex; align-items: center; background: var(--admin-brand-primary); color: white; padding: 0.625rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 600; }
.grid-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem; }
.item-card { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; background: #fff; }
.item-header { display: flex; justify-content: space-between; gap: 0.75rem; }
.item-title h3 { font-weight: 700; color: #111827; }
.subtext { color: #6b7280; font-size: 0.875rem; }
.item-description { color: #4b5563; margin: 0.5rem 0; }
.meta-row { display: flex; flex-wrap: wrap; gap: 0.75rem; color: #6b7280; font-size: 0.875rem; }
.status-badge { padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
.status-active { background: var(--admin-success-light); color: var(--admin-success-text); }
.status-inactive { background: var(--admin-bg-hover); color: var(--admin-text-secondary); }
.card-actions { display: flex; gap: 0.5rem; margin-top: 1rem; }
.btn-action { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.4rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; background: white; cursor: pointer; }
.btn-view { color: #6366f1; }
.btn-edit { color: var(--admin-brand-primary); }
.btn-toggle { color: var(--admin-info); }
.btn-delete { color: var(--admin-danger); }
.empty-state { text-align: center; padding: 2rem 0; }
.empty-icon { font-size: 2rem; }
.empty-title { font-size: 1.125rem; font-weight: 600; color: #111827; margin-top: 0.5rem; }
.empty-subtitle { color: #6b7280; }
.loading-state { display: flex; flex-direction: column; align-items: center; padding: 2rem 0; }
.spinner { width: 2rem; height: 2rem; border: 3px solid #e5e7eb; border-top: 3px solid var(--admin-brand-primary); border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--admin-success-dark); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; z-index: 1001; }
.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
.pagination-info { color: #6b7280; font-size: 0.875rem; }
.pagination-controls { display: flex; align-items: center; gap: 0.5rem; }
.pagination-btn { padding: 0.4rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; cursor: pointer; }
.pagination-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.pagination-current { color: #6b7280; font-size: 0.875rem; }
</style>
