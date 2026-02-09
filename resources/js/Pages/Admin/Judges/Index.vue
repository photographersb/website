<template>
  <div class="min-h-screen">
    <AdminHeader
      title="⚖️ Judges"
      subtitle="Manage competition judges"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">JUDGE COUNCIL</p>
          <h1 class="hero-title">Judging panel, always in sync.</h1>
          <p class="hero-subtitle">
            Track judge profiles, assignments, and performance at a glance.
          </p>
          <div class="hero-actions">
            <button
              class="btn-admin-primary"
              @click="router.push('/admin/judges/create')"
            >
              Add Judge
            </button>
            <button
              class="btn-admin-secondary"
              @click="fetchJudges(meta.current_page || 1)"
            >
              Refresh List
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Total Judges</span>
            <span class="status-value">{{ stats.total || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Active</span>
            <span class="status-value">{{ stats.active || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Linked Accounts</span>
            <span class="status-value">{{ stats.linked || 0 }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Showing {{ judges.length }} of {{ meta.total || 0 }} judges
        </div>
      </div>

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
              placeholder="Search judges..."
              class="search-input"
              @input="debounceSearch"
            >
          </div>

          <select
            v-model="filters.status"
            class="filter-select"
            @change="fetchJudges"
          >
            <option value="">
              All Status
            </option>
            <option value="active">
              Active
            </option>
            <option value="inactive">
              Inactive
            </option>
          </select>

        </div>

        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading judges...</p>
        </div>

        <div
          v-else-if="judges.length > 0"
          class="grid-list"
        >
          <div
            v-for="judge in judges"
            :key="judge.id"
            class="item-card"
          >
            <div class="item-header">
              <div class="item-title">
                <h3>{{ judge.name }}</h3>
                <span class="subtext">{{ judge.title || '—' }}</span>
              </div>
              <span
                class="status-badge"
                :class="judge.is_active ? 'status-active' : 'status-inactive'"
              >
                {{ judge.is_active ? 'active' : 'inactive' }}
              </span>
            </div>

            <p class="item-description">
              {{ judge.organization || 'No organization' }}
            </p>
            <p class="item-description">
              {{ judge.bio || 'No bio' }}
            </p>

            <div class="meta-row">
              <span>Email: {{ judge.email || 'N/A' }}</span>
              <span>Linked User: {{ judge.user?.email || '—' }}</span>
            </div>

            <div class="card-actions">
              <button
                class="btn-action btn-view"
                @click="router.push(`/admin/judges/${judge.id}`)"
              >
                View
              </button>
              <button
                class="btn-action btn-edit"
                @click="router.push(`/admin/judges/${judge.id}/edit`)"
              >
                Edit
              </button>
              <button
                class="btn-action btn-toggle"
                @click="toggleStatus(judge)"
              >
                {{ judge.is_active ? 'Deactivate' : 'Activate' }}
              </button>
              <button
                class="btn-action btn-delete"
                @click="deleteJudge(judge.id)"
              >
                Delete
              </button>
            </div>
          </div>
        </div>

        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            ⚖️
          </div>
          <p class="empty-title">
            No judges found
          </p>
          <p class="empty-subtitle">
            Add your first judge
          </p>
        </div>

        <div
          v-if="meta.total > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ judges.length }} of {{ meta.total }} judges
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'

const router = useRouter()
const judges = ref([])
const loading = ref(false)
const saving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const availableUsers = ref([])

const filters = ref({ search: '', status: '' })
const meta = ref({ total: 0, current_page: 1, last_page: 1 })
const stats = ref({ total: 0, active: 0, inactive: 0, linked: 0 })

let searchTimeout = null
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchJudges(), 400)
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, 3000)
}

const fetchJudges = async (page = 1) => {
  loading.value = true
  try {
    const params = { page }
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.status) params.status = filters.value.status

    const { data } = await api.get('/admin/judges', { params })
    if (data.status === 'success') {
      judges.value = data.data.data || data.data
      meta.value = {
        total: data.data.total || judges.value.length,
        current_page: data.data.current_page || 1,
        last_page: data.data.last_page || 1
      }
      // Use stats from backend
      if (data.stats) {
        stats.value = data.stats
      }
      await fetchAvailableUsers()
    }
  } catch (error) {
    console.error('Error fetching judges', error)
    showToastMessage('Error loading judges')
  } finally {
    loading.value = false
  }
}

  const fetchAvailableUsers = async () => {
    try {
      const { data } = await api.get('/admin/users', {
        params: { per_page: 500 }
      })
      if (data.status === 'success') {
        const allUsers = data.data.data || data.data || []
        const judgeUserIds = judges.value.map(j => j.user_id).filter(Boolean)
        availableUsers.value = allUsers.filter(u => !judgeUserIds.includes(u.id))
      }
    } catch (error) {
      console.error('Error fetching available users', error)
    }
  }

const toggleStatus = async (judge) => {
  try {
    const { data } = await api.post(`/admin/judges/${judge.id}/toggle-status`)
    if (data.status === 'success') {
      judge.is_active = data.data.is_active
      showToastMessage('Status updated')
    }
  } catch (error) {
    console.error('Error toggling status', error)
    showToastMessage('Error updating status')
  }
}

const deleteJudge = async (id) => {
  if (!confirm('Delete this judge?')) return
  try {
    const { data } = await api.delete(`/admin/judges/${id}`)
    if (data.status === 'success') {
      showToastMessage('Judge deleted')
      fetchJudges(meta.value.current_page)
    }
  } catch (error) {
    console.error('Error deleting judge', error)
    showToastMessage('Error deleting judge')
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= meta.value.last_page) fetchJudges(page)
}

onMounted(() => {
  fetchJudges()
})
</script>

<style scoped>
.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.filters-bar { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin-bottom: 1.5rem; }
.search-box { position: relative; flex: 1; min-width: 220px; }
.search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; width: 1.25rem; height: 1.25rem; }
.search-input { width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; }
.filter-select { padding: 0.625rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
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
.loading-state { display: flex; flex-direction: column; align-items: center; padding: 2rem 0; }
.spinner { width: 2rem; height: 2rem; border: 3px solid #e5e7eb; border-top: 3px solid var(--admin-brand-primary); border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal { background: white; border-radius: 1rem; width: 100%; max-width: 720px; overflow: hidden; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-body { padding: 1.5rem; }
.edit-form { display: flex; flex-direction: column; gap: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.5rem; }
.form-input { padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; }
.checkbox-label { display: flex; align-items: center; gap: 0.5rem; }
.modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; }
.btn-cancel { padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white; }
.btn-save { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; background: var(--admin-brand-primary); color: white; }
.toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--admin-success-dark); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; z-index: 1001; }
.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
.pagination-btn { padding: 0.4rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
@media (max-width: 1024px) { .page-hero { grid-template-columns: 1fr; } }
</style>
