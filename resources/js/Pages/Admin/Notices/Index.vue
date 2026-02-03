<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="📢 Admin Notices"
      subtitle="Create and manage platform notices for users"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="content-card">
        <div class="filters-bar">
          <div class="search-box">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              v-model="filters.search"
              @input="debounceSearch"
              type="text"
              placeholder="Search notices..."
              class="search-input"
            />
          </div>

          <select v-model="filters.status" @change="fetchNotices" class="filter-select">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
          </select>

          <select v-model="filters.priority" @change="fetchNotices" class="filter-select">
            <option value="">All Priority</option>
            <option value="low">Low</option>
            <option value="normal">Normal</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>

          <button @click="openCreate" class="btn-add">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Notice
          </button>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading notices...</p>
        </div>

        <div v-else-if="notices.length > 0" class="notices-grid">
          <div v-for="notice in notices" :key="notice.id" class="notice-card">
            <div class="notice-header">
              <div class="notice-title">
                <span class="priority-dot" :class="priorityClass(notice.priority)"></span>
                <h3>{{ notice.title }}</h3>
              </div>
              <span class="status-badge" :class="statusClass(notice.status)">{{ notice.status }}</span>
            </div>

            <p class="notice-message">{{ notice.message }}</p>

            <div class="notice-meta">
              <span>Priority: {{ notice.priority }}</span>
              <span>Published: {{ formatDate(notice.publish_at) }}</span>
            </div>

            <div class="notice-meta">
              <span>Created by: {{ notice.created_by }}</span>
              <span v-if="notice.roles?.length">Roles: {{ notice.roles.join(', ') }}</span>
              <span v-else>Roles: All</span>
            </div>

            <div class="card-actions">
              <button @click="openEdit(notice.id)" class="btn-action btn-edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
              </button>
              <button @click="deleteNotice(notice.id)" class="btn-action btn-delete">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
              </button>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">📢</div>
          <p class="empty-title">No notices found</p>
          <p class="empty-subtitle">Create your first notice</p>
        </div>

        <div v-if="meta.total > 0" class="pagination">
          <div class="pagination-info">
            Showing {{ notices.length }} of {{ meta.total }} notices
          </div>
          <div class="pagination-controls">
            <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="pagination-btn">Previous</button>
            <span class="pagination-current">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
            <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="pagination-btn">Next</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>{{ editingId ? 'Edit Notice' : 'Create Notice' }}</h3>
          <button @click="closeModal" class="modal-close">×</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveNotice" class="edit-form">
            <div class="form-row">
              <div class="form-group">
                <label>Title *</label>
                <input v-model="form.title" type="text" class="form-input" required />
              </div>
              <div class="form-group">
                <label>Priority *</label>
                <select v-model="form.priority" class="form-input" required>
                  <option value="low">Low</option>
                  <option value="normal">Normal</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>Message *</label>
              <textarea v-model="form.message" class="form-input" rows="5" required></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Status</label>
                <select v-model="form.status" class="form-input">
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                </select>
              </div>
              <div class="form-group">
                <label>Icon</label>
                <input v-model="form.icon" type="text" class="form-input" placeholder="e.g. 📢" />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Publish At</label>
                <input v-model="form.publish_at" type="datetime-local" class="form-input" />
              </div>
              <div class="form-group">
                <label>Expires At</label>
                <input v-model="form.expires_at" type="datetime-local" class="form-input" />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Color</label>
                <input v-model="form.color" type="text" class="form-input" placeholder="e.g. indigo" />
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input v-model="form.show_to_all_roles" type="checkbox" />
                  <span>Show to all roles</span>
                </label>
              </div>
            </div>

            <div v-if="!form.show_to_all_roles" class="form-group">
              <label>Target Roles</label>
              <div class="roles-grid">
                <label v-for="(label, role) in roles" :key="role" class="checkbox-label">
                  <input type="checkbox" :value="role" v-model="form.roles" />
                  <span>{{ label }}</span>
                </label>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeModal" class="btn-cancel">Cancel</button>
              <button type="submit" class="btn-save" :disabled="saving">
                {{ saving ? 'Saving...' : (editingId ? 'Update' : 'Create') }}
              </button>
            </div>
          </form>
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

const notices = ref([])
const roles = ref({})
const loading = ref(false)
const showModal = ref(false)
const editingId = ref(null)
const saving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({
  search: '',
  status: '',
  priority: ''
})

const meta = ref({
  total: 0,
  current_page: 1,
  last_page: 1
})

const form = ref({
  title: '',
  message: '',
  priority: 'normal',
  status: 'draft',
  publish_at: '',
  expires_at: '',
  icon: '',
  color: '',
  show_to_all_roles: true,
  roles: []
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchNotices(), 400)
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, 3000)
}

const fetchRoles = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('/api/v1/admin/notices/roles/available', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') roles.value = data.data
  } catch (error) {
    console.error('Error loading roles', error)
  }
}

const fetchNotices = async (page = 1) => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const params = new URLSearchParams()
    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.status) params.append('status', filters.value.status)
    if (filters.value.priority) params.append('priority', filters.value.priority)
    params.append('page', page)

    const response = await fetch(`/api/v1/admin/notices?${params}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      notices.value = data.data
      meta.value = {
        total: data.meta?.total || data.data.length,
        current_page: data.meta?.current_page || 1,
        last_page: data.meta?.last_page || 1
      }
    }
  } catch (error) {
    console.error('Error fetching notices', error)
    showToastMessage('Error loading notices')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  form.value = {
    title: '',
    message: '',
    priority: 'normal',
    status: 'draft',
    publish_at: '',
    expires_at: '',
    icon: '',
    color: '',
    show_to_all_roles: true,
    roles: []
  }
  showModal.value = true
}

const openEdit = async (id) => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/notices/${id}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      const notice = data.data
      editingId.value = id
      form.value = {
        title: notice.title,
        message: notice.message,
        priority: notice.priority,
        status: notice.status,
        publish_at: toInputDateTime(notice.publish_at),
        expires_at: toInputDateTime(notice.expires_at),
        icon: notice.icon || '',
        color: notice.color || '',
        show_to_all_roles: notice.show_to_all_roles,
        roles: notice.roles || []
      }
      showModal.value = true
    }
  } catch (error) {
    console.error('Error loading notice', error)
    showToastMessage('Error loading notice')
  }
}

const saveNotice = async () => {
  saving.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const payload = {
      ...form.value,
      publish_at: fromInputDateTime(form.value.publish_at),
      expires_at: fromInputDateTime(form.value.expires_at)
    }
    const url = editingId.value ? `/api/v1/admin/notices/${editingId.value}` : '/api/v1/admin/notices'
    const method = editingId.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    })

    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage(editingId.value ? 'Notice updated' : 'Notice created')
      showModal.value = false
      fetchNotices(meta.value.current_page)
    } else {
      showToastMessage(data.message || 'Error saving notice')
    }
  } catch (error) {
    console.error('Error saving notice', error)
    showToastMessage('Error saving notice')
  } finally {
    saving.value = false
  }
}

const deleteNotice = async (id) => {
  if (!confirm('Delete this notice?')) return
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/notices/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage('Notice deleted')
      fetchNotices(meta.value.current_page)
    } else {
      showToastMessage(data.message || 'Error deleting notice')
    }
  } catch (error) {
    console.error('Error deleting notice', error)
    showToastMessage('Error deleting notice')
  }
}

const closeModal = () => {
  showModal.value = false
}

const changePage = (page) => {
  if (page >= 1 && page <= meta.value.last_page) fetchNotices(page)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const toInputDateTime = (date) => {
  if (!date) return ''
  const d = new Date(date)
  const pad = (n) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const fromInputDateTime = (value) => {
  if (!value) return null
  return new Date(value).toISOString()
}

const statusClass = (status) => {
  return status === 'published' ? 'status-published' : 'status-draft'
}

const priorityClass = (priority) => {
  return {
    low: 'priority-low',
    normal: 'priority-normal',
    high: 'priority-high',
    urgent: 'priority-urgent'
  }[priority] || 'priority-normal'
}

onMounted(() => {
  fetchRoles()
  fetchNotices()
})
</script>

<style scoped>
.content-card {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  padding: 1.5rem;
}

.filters-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1.5rem;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 220px;
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  width: 1.25rem;
  height: 1.25rem;
}

.search-input {
  width: 100%;
  padding: 0.625rem 0.75rem 0.625rem 2.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
}

.filter-select {
  padding: 0.625rem 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background: white;
}

.btn-add {
  display: inline-flex;
  align-items: center;
  background: var(--admin-brand-primary);
  color: white;
  padding: 0.625rem 1rem;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

.notices-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.notice-card {
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1rem;
  background: #fff;
}

.notice-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
}

.notice-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.notice-title h3 {
  font-weight: 700;
  color: #111827;
}

.priority-dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 9999px;
}

.priority-low { background: var(--admin-text-muted); }
.priority-normal { background: var(--admin-info); }
.priority-high { background: var(--admin-warning); }
.priority-urgent { background: var(--admin-danger); }

.status-badge {
  padding: 0.2rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: capitalize;
}

.status-published { background: var(--admin-success-light); color: var(--admin-success-text); }
.status-draft { background: var(--admin-bg-hover); color: var(--admin-text-secondary); }

.notice-message {
  color: #4b5563;
  margin: 0.75rem 0;
}

.notice-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.4rem 0.75rem;
  border-radius: 0.5rem;
  border: 1px solid #e5e7eb;
  background: white;
  cursor: pointer;
}

.btn-edit { color: var(--admin-brand-primary); }
.btn-delete { color: var(--admin-danger); }

.empty-state {
  text-align: center;
  padding: 2rem 0;
}

.empty-icon { font-size: 2rem; }
.empty-title { font-weight: 700; }
.empty-subtitle { color: #6b7280; }

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem 0;
}

.spinner {
  width: 2rem;
  height: 2rem;
  border: 3px solid #e5e7eb;
  border-top: 3px solid var(--admin-brand-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 1rem;
  width: 100%;
  max-width: 720px;
  overflow: hidden;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-body { padding: 1.5rem; }

.edit-form { display: flex; flex-direction: column; gap: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.5rem; }
.form-input { padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; }
.checkbox-label { display: flex; align-items: center; gap: 0.5rem; }
.roles-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.5rem; }

.modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; }
.btn-cancel { padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white; }
.btn-save { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; background: var(--admin-brand-primary); color: white; }

.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: var(--admin-success-dark);
  color: white;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  z-index: 1001;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
}

.pagination-btn {
  padding: 0.4rem 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background: white;
}
</style>
