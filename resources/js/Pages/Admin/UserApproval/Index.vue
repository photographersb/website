<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="👤 User Approval" 
      subtitle="Review and approve new user registrations"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stat-card stat-yellow">
          <div class="stat-icon">
            ⏳
          </div>
          <div class="stat-content">
            <span class="stat-label">Pending Approval</span>
            <span class="stat-value">{{ stats.pending }}</span>
          </div>
        </div>

        <div class="stat-card stat-green">
          <div class="stat-icon">
            ✓
          </div>
          <div class="stat-content">
            <span class="stat-label">Approved</span>
            <span class="stat-value">{{ stats.approved }}</span>
          </div>
        </div>

        <div class="stat-card stat-red">
          <div class="stat-icon">
            ✕
          </div>
          <div class="stat-content">
            <span class="stat-label">Rejected</span>
            <span class="stat-value">{{ stats.rejected }}</span>
          </div>
        </div>

        <div class="stat-card stat-blue">
          <div class="stat-icon">
            👥
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Users</span>
            <span class="stat-value">{{ stats.total }}</span>
          </div>
        </div>
      </div>

      <!-- Filters & Actions -->
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
              placeholder="Search by name, email, or phone..." 
              class="search-input" 
              @input="fetchUsers"
            >
          </div>

          <select
            v-model="filters.status"
            class="filter-select"
            @change="fetchUsers"
          >
            <option value="pending">
              Pending
            </option>
            <option value="approved">
              Approved
            </option>
            <option value="rejected">
              Rejected
            </option>
            <option value="">
              All Status
            </option>
          </select>

          <select
            v-model="filters.role"
            class="filter-select"
            @change="fetchUsers"
          >
            <option value="">
              All Roles
            </option>
            <option value="client">
              Client
            </option>
            <option value="photographer">
              Photographer
            </option>
            <option value="studio_owner">
              Studio Owner
            </option>
          </select>

          <button 
            v-if="selectedUsers.length > 0" 
            class="btn-approve" 
            @click="bulkApprove"
          >
            ✓ Approve Selected ({{ selectedUsers.length }})
          </button>
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading users...</p>
        </div>

        <!-- Users List -->
        <div
          v-else-if="users.length > 0"
          class="users-container"
        >
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>
                    <input 
                      type="checkbox" 
                      :checked="selectedUsers.length === users.filter(u => u.approval_status === 'pending').length && users.filter(u => u.approval_status === 'pending').length > 0"
                      @change="toggleSelectAll"
                    >
                  </th>
                  <th>User</th>
                  <th>Role</th>
                  <th>Contact</th>
                  <th>Email Verified</th>
                  <th>Registered</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="user in users"
                  :key="user.id"
                >
                  <td>
                    <input 
                      v-if="user.approval_status === 'pending'"
                      v-model="selectedUsers" 
                      type="checkbox"
                      :value="user.id"
                    >
                  </td>
                  <td>
                    <div class="user-cell">
                      <div class="user-avatar">
                        {{ user.name.charAt(0).toUpperCase() }}
                      </div>
                      <div>
                        <div class="user-name">
                          {{ user.name }}
                        </div>
                        <div class="user-uuid">
                          ID: {{ user.uuid }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span :class="`role-badge role-${user.role}`">
                      {{ formatRole(user.role) }}
                    </span>
                  </td>
                  <td>
                    <div class="contact-cell">
                      <a
                        :href="`mailto:${user.email}`"
                        class="contact-link"
                      >{{ user.email }}</a>
                      <a
                        v-if="user.phone"
                        :href="`tel:${user.phone}`"
                        class="contact-link"
                      >{{ user.phone }}</a>
                    </div>
                  </td>
                  <td>
                    <span
                      v-if="user.email_verified_at"
                      class="badge badge-success"
                    >✓ Verified</span>
                    <span
                      v-else
                      class="badge badge-warning"
                    >⏳ Pending</span>
                  </td>
                  <td>{{ formatDate(user.created_at) }}</td>
                  <td>
                    <span :class="`status-badge status-${user.approval_status}`">
                      {{ user.approval_status.charAt(0).toUpperCase() + user.approval_status.slice(1) }}
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button 
                        v-if="user.approval_status === 'pending'"
                        class="btn-action btn-success" 
                        title="Approve" 
                        @click="approveUser(user)"
                      >
                        ✓
                      </button>
                      <button 
                        v-if="user.approval_status === 'pending'"
                        class="btn-action btn-danger" 
                        title="Reject" 
                        @click="openRejectModal(user)"
                      >
                        ✕
                      </button>
                      <button 
                        class="btn-action" 
                        title="View Details" 
                        @click="viewUser(user)"
                      >
                        👁
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            👥
          </div>
          <p class="empty-title">
            No users found
          </p>
          <p class="empty-subtitle">
            Users awaiting approval will appear here
          </p>
        </div>
      </div>

      <!-- Reject Modal -->
      <div
        v-if="showRejectModal"
        class="modal-overlay"
        @click.self="showRejectModal = false"
      >
        <div class="modal modal-sm">
          <div class="modal-header">
            <h3>Reject User Registration</h3>
            <button
              class="modal-close"
              @click="showRejectModal = false"
            >
              ×
            </button>
          </div>
          <div class="modal-body">
            <p class="mb-4">
              You are about to reject <strong>{{ rejectingUser?.name }}</strong>'s registration.
            </p>
          
            <label class="block text-sm font-medium mb-2">Rejection Reason *</label>
            <textarea 
              v-model="rejectReason" 
              rows="4" 
              class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy"
              placeholder="Please provide a clear reason for rejection..."
            />
          </div>
          <div class="modal-footer">
            <button
              class="btn-secondary"
              @click="showRejectModal = false"
            >
              Cancel
            </button>
            <button
              :disabled="!rejectReason.trim()"
              class="btn-danger"
              @click="rejectUser"
            >
              Reject User
            </button>
          </div>
        </div>
      </div>

      <!-- View User Modal -->
      <div
        v-if="showViewModal"
        class="modal-overlay"
        @click.self="showViewModal = false"
      >
        <div class="modal">
          <div class="modal-header">
            <h3>User Details</h3>
            <button
              class="modal-close"
              @click="showViewModal = false"
            >
              ×
            </button>
          </div>
          <div
            v-if="selectedUser"
            class="modal-body"
          >
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ selectedUser.name }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value">
                  <a
                    :href="`mailto:${selectedUser.email}`"
                    class="text-burgundy hover:underline"
                  >
                    {{ selectedUser.email }}
                  </a>
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">
                  <a
                    v-if="selectedUser.phone"
                    :href="`tel:${selectedUser.phone}`"
                    class="text-burgundy hover:underline"
                  >
                    {{ selectedUser.phone }}
                  </a>
                  <span
                    v-else
                    class="text-gray-400"
                  >Not provided</span>
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Role:</span>
                <span class="detail-value">
                  <span :class="`role-badge role-${selectedUser.role}`">
                    {{ formatRole(selectedUser.role) }}
                  </span>
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Email Verified:</span>
                <span class="detail-value">
                  <span
                    v-if="selectedUser.email_verified_at"
                    class="text-success-700"
                  >
                    ✓ {{ formatDate(selectedUser.email_verified_at) }}
                  </span>
                  <span
                    v-else
                    class="text-warning-700"
                  >⏳ Pending</span>
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Registered:</span>
                <span class="detail-value">{{ formatDate(selectedUser.created_at) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Approval Status:</span>
                <span class="detail-value">
                  <span :class="`status-badge status-${selectedUser.approval_status}`">
                    {{ selectedUser.approval_status.charAt(0).toUpperCase() + selectedUser.approval_status.slice(1) }}
                  </span>
                </span>
              </div>
              <div
                v-if="selectedUser.rejection_reason"
                class="detail-item full-width"
              >
                <span class="detail-label">Rejection Reason:</span>
                <span class="detail-value">
                  <div class="alert alert-danger mt-2">
                    {{ selectedUser.rejection_reason }}
                  </div>
                </span>
              </div>
              <div
                v-if="selectedUser.photographer"
                class="detail-item full-width"
              >
                <span class="detail-label">Photographer Profile:</span>
                <span class="detail-value">
                  <div class="alert alert-info mt-2">
                    <p><strong>Slug:</strong> {{ selectedUser.photographer.slug }}</p>
                    <p><strong>Experience:</strong> {{ selectedUser.photographer.experience_years }} years</p>
                    <p><strong>Verified:</strong> {{ selectedUser.photographer.is_verified ? 'Yes' : 'No' }}</p>
                  </div>
                </span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              class="btn-secondary"
              @click="showViewModal = false"
            >
              Close
            </button>
            <button 
              v-if="selectedUser?.approval_status === 'pending'"
              class="btn-success" 
              @click="approveUser(selectedUser)"
            >
              ✓ Approve
            </button>
            <button 
              v-if="selectedUser?.approval_status === 'pending'"
              class="btn-danger" 
              @click="openRejectModal(selectedUser)"
            >
              ✕ Reject
            </button>
          </div>
        </div>
      </div>

      <!-- Toast Notification -->
      <div
        v-if="toast.show"
        :class="`toast toast-${toast.type}`"
      >
        {{ toast.message }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../../api'
import { formatDateTime as formatDateTimeValue } from '../../../utils/formatters'

const users = ref([])
const stats = ref({ pending: 0, approved: 0, rejected: 0, total: 0 })
const loading = ref(false)
const selectedUsers = ref([])

const filters = ref({
  search: '',
  status: 'pending',
  role: ''
})

const showRejectModal = ref(false)
const showViewModal = ref(false)
const rejectingUser = ref(null)
const selectedUser = ref(null)
const rejectReason = ref('')

const toast = ref({ show: false, message: '', type: 'success' })

onMounted(() => {
  fetchUsers()
  fetchStats()
})

const fetchUsers = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.status) params.append('status', filters.value.status)
    if (filters.value.role) params.append('role', filters.value.role)
    if (filters.value.search) params.append('search', filters.value.search)
    
    const response = await api.get(`/admin/pending-users?${params}`)
    users.value = response.data
  } catch (error) {
    console.error('Error fetching users:', error)
    console.error('Error response:', error.response)
    showToast(error.response?.data?.message || 'Failed to load users', 'error')
  } finally {
    loading.value = false
  }
}

const fetchStats = async () => {
  try {
    const response = await api.get('/admin/approval-stats')
    stats.value = response.data
  } catch (error) {
    console.error('Failed to load stats:', error)
  }
}

const approveUser = async (user) => {
  try {
    await api.post(`/admin/users/${user.id}/approve`)
    showToast('User approved successfully', 'success')
    showViewModal.value = false
    await fetchUsers()
    await fetchStats()
  } catch (error) {
    showToast('Failed to approve user', 'error')
  }
}

const openRejectModal = (user) => {
  rejectingUser.value = user
  rejectReason.value = ''
  showViewModal.value = false
  showRejectModal.value = true
}

const rejectUser = async () => {
  if (!rejectReason.value.trim()) return
  
  try {
    await api.post(`/admin/users/${rejectingUser.value.id}/reject`, {
      reason: rejectReason.value
    })
    showToast('User rejected', 'success')
    showRejectModal.value = false
    await fetchUsers()
    await fetchStats()
  } catch (error) {
    showToast('Failed to reject user', 'error')
  }
}

const bulkApprove = async () => {
  if (selectedUsers.value.length === 0) return
  
  try {
    await api.post('/admin/users/bulk-approve', {
      user_ids: selectedUsers.value
    })
    showToast(`${selectedUsers.value.length} users approved successfully`, 'success')
    selectedUsers.value = []
    await fetchUsers()
    await fetchStats()
  } catch (error) {
    showToast('Failed to approve users', 'error')
  }
}

const viewUser = (user) => {
  selectedUser.value = user
  showViewModal.value = true
}

const toggleSelectAll = (event) => {
  if (event.target.checked) {
    selectedUsers.value = users.value
      .filter(u => u.approval_status === 'pending')
      .map(u => u.id)
  } else {
    selectedUsers.value = []
  }
}

const formatRole = (role) => {
  return role.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return formatDateTimeValue(date) || 'N/A'
}

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}
</script>

<style scoped>
.admin-user-approval {
  padding: 2rem;
}

.page-header {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.page-subtitle {
  color: #6b7280;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  font-size: 2.5rem;
}

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-label {
  color: #6b7280;
  font-size: 0.875rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
}

.stat-yellow { border-left: 4px solid var(--admin-warning); }
.stat-green { border-left: 4px solid var(--admin-success); }
.stat-red { border-left: 4px solid var(--admin-danger); }
.stat-blue { border-left: 4px solid var(--admin-info); }

.content-card {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.loading-state,
.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  border: 3px solid #f3f4f6;
  border-top-color: var(--admin-brand-primary);
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 250px;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 1.25rem;
  height: 1.25rem;
  color: #9ca3af;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 3rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table thead {
  background: #f9fafb;
}

.data-table th,
.data-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.data-table th {
  font-weight: 600;
  color: #6b7280;
  font-size: 0.875rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  padding: 0.5rem;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-action:hover {
  background: #f3f4f6;
}

.btn-success {
  color: var(--admin-success);
  border-color: var(--admin-success);
}

.btn-success:hover {
  background: var(--admin-success-light);
}

.btn-danger {
  color: var(--admin-danger);
  border-color: var(--admin-danger);
}

.btn-danger:hover {
  background: var(--admin-danger-light);
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}

.modal {
  background: white;
  border-radius: 1rem;
  max-width: 800px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-sm {
  max-width: 500px;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-close {
  font-size: 1.5rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-secondary,
.btn-success,
.btn-danger {
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
}

.btn-secondary {
  background: var(--admin-bg-hover);
  color: var(--admin-text-secondary);
}

.modal-footer .btn-success {
  background: var(--admin-success);
  color: white;
}

.modal-footer .btn-danger {
  background: var(--admin-danger);
  color: white;
}

.toast {
  position: fixed;
  top: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  color: white;
  font-weight: 500;
  z-index: 100;
  animation: slideIn 0.3s ease-out;
}

.toast-success {
  background: var(--admin-success);
}

.toast-error {
  background: var(--admin-danger);
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.filters-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.btn-approve {
  background: var(--admin-success);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-approve:hover {
  background: var(--admin-success-dark);
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--admin-brand-primary), var(--admin-brand-primary-dark));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.1rem;
}

.user-name {
  font-weight: 600;
  color: #1f2937;
}

.user-uuid {
  font-size: 0.75rem;
  color: #6b7280;
}

.contact-cell {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.contact-link {
  color: var(--admin-brand-primary);
  text-decoration: none;
  font-size: 0.875rem;
}

.contact-link:hover {
  text-decoration: underline;
}

.role-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.role-client {
  background: var(--admin-info-light);
  color: var(--admin-info-text);
}

.role-photographer {
  background: var(--admin-brand-primary-soft);
  color: var(--admin-brand-primary);
}

.role-studio_owner {
  background: var(--admin-warning-light);
  color: var(--admin-warning-text);
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-pending {
  background: var(--admin-warning-light);
  color: var(--admin-warning-text);
}

.status-approved {
  background: var(--admin-success-light);
  color: var(--admin-success-text);
}

.status-rejected {
  background: var(--admin-danger-light);
  color: var(--admin-danger-text);
}

.badge {
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.badge-success {
  background: #d1fae5;
  color: #065f46;
}

.badge-warning {
  background: #fef3c7;
  color: #92400e;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.detail-item.full-width {
  grid-column: 1 / -1;
}

.detail-label {
  font-weight: 600;
  color: #6b7280;
  font-size: 0.875rem;
}

.detail-value {
  color: #1f2937;
  font-size: 0.9375rem;
}
</style>
