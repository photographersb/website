<template>
  <div class="admin-users-management">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">👥 User Management</h1>
        <p class="page-subtitle">Manage all platform users and their accounts</p>
      </div>
      <button @click="showAddModal = true" class="btn-primary">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add User
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card stat-blue">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Users</span>
          <span class="stat-value">{{ stats.total }}</span>
        </div>
      </div>

      <div class="stat-card stat-green">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Active Users</span>
          <span class="stat-value">{{ stats.active }}</span>
        </div>
      </div>

      <div class="stat-card stat-purple">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Photographers</span>
          <span class="stat-value">{{ stats.photographers }}</span>
        </div>
      </div>

      <div class="stat-card stat-red">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Suspended</span>
          <span class="stat-value">{{ stats.suspended }}</span>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
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
            placeholder="Search by name or email..." 
            class="search-input"
          />
        </div>

        <select v-model="filters.role" @change="fetchUsers" class="filter-select">
          <option value="">All Roles</option>
          <option value="client">Client</option>
          <option value="photographer">Photographer</option>
          <option value="admin">Admin</option>
          <option value="super_admin">Super Admin</option>
        </select>

        <button @click="exportUsers" class="btn-export">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading users...</p>
      </div>

      <!-- Users Table -->
      <div v-else class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Contact</th>
              <th>Role</th>
              <th>Status</th>
              <th>Verified</th>
              <th>Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="users.length === 0">
              <td colspan="7" class="empty-state">
                <div class="empty-icon">👥</div>
                <p>No users found</p>
              </td>
            </tr>
            <tr v-for="user in users" :key="user.id" class="user-row">
              <td>
                <div class="user-cell">
                  <div class="user-avatar">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="user-name">{{ user.name }}</div>
                    <div class="user-id">ID: {{ user.id }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="contact-cell">
                  <div class="email">{{ user.email }}</div>
                  <div class="phone">{{ user.phone || 'N/A' }}</div>
                </div>
              </td>
              <td>
                <span class="badge" :class="'badge-' + user.role">
                  {{ formatRole(user.role) }}
                </span>
              </td>
              <td>
                <span v-if="user.is_suspended" class="badge badge-danger">Suspended</span>
                <span v-else class="badge badge-success">Active</span>
              </td>
              <td>
                <span v-if="user.email_verified_at" class="verified-badge">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Verified
                </span>
                <span v-else class="unverified-badge">Not Verified</span>
              </td>
              <td>{{ formatDate(user.created_at) }}</td>
              <td>
                <div class="action-buttons">
                  <button @click="viewUser(user)" class="btn-action" title="View Details">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button @click="editUser(user)" class="btn-action" title="Edit User">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button v-if="!user.is_suspended" @click="suspendUser(user)" class="btn-action btn-warning" title="Suspend User">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                  </button>
                  <button v-else @click="unsuspendUser(user)" class="btn-action btn-success" title="Unsuspend User">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button @click="deleteUser(user)" class="btn-action btn-danger" title="Delete User">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="stats.total > 0" class="pagination">
        <div class="pagination-info">
          Showing {{ users.length }} of {{ stats.total }} users
        </div>
      </div>
    </div>

    <!-- View User Modal -->
    <div v-if="showViewModal" class="modal-overlay" @click.self="showViewModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>User Details</h3>
          <button @click="showViewModal = false" class="modal-close">×</button>
        </div>
        <div class="modal-body" v-if="selectedUser">
          <div class="detail-grid">
            <div class="detail-item">
              <span class="detail-label">Name:</span>
              <span class="detail-value">{{ selectedUser.name }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Email:</span>
              <span class="detail-value">{{ selectedUser.email }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Phone:</span>
              <span class="detail-value">{{ selectedUser.phone || 'N/A' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Role:</span>
              <span class="badge" :class="'badge-' + selectedUser.role">{{ formatRole(selectedUser.role) }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Status:</span>
              <span v-if="selectedUser.is_suspended" class="badge badge-danger">Suspended</span>
              <span v-else class="badge badge-success">Active</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Email Verified:</span>
              <span>{{ selectedUser.email_verified_at ? 'Yes' : 'No' }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Joined:</span>
              <span>{{ formatDate(selectedUser.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div v-if="showAddModal || showEditModal" class="modal-overlay" @click.self="closeEditModal">
      <div class="modal">
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit User' : 'Add New User' }}</h3>
          <button @click="closeEditModal" class="modal-close">×</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveUser" class="user-form">
            <div class="form-group">
              <label>Name *</label>
              <input v-model="userForm.name" type="text" required class="form-input" placeholder="Full Name" />
            </div>
            <div class="form-group">
              <label>Email *</label>
              <input v-model="userForm.email" type="email" required class="form-input" placeholder="email@example.com" />
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input v-model="userForm.phone" type="text" class="form-input" placeholder="+880 1234567890" />
            </div>
            <div class="form-group">
              <label>Password {{ showEditModal ? '' : '*' }}</label>
              <input v-model="userForm.password" type="password" :required="!showEditModal" class="form-input" placeholder="Min 8 characters" />
              <small v-if="showEditModal" class="form-hint">Leave blank to keep current password</small>
            </div>
            <div class="form-group">
              <label>Role *</label>
              <select v-model="userForm.role" required class="form-input">
                <option value="client">Client</option>
                <option value="photographer">Photographer</option>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </div>
            <div class="form-actions">
              <button type="button" @click="closeEditModal" class="btn-secondary">Cancel</button>
              <button type="submit" class="btn-primary">{{ showEditModal ? 'Update User' : 'Create User' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Success Toast -->
    <div v-if="showToast" class="toast">
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const users = ref([])
const loading = ref(false)
const showViewModal = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const selectedUser = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

const userForm = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  role: 'client'
})

const filters = ref({
  search: '',
  role: ''
})

const stats = ref({
  total: 0,
  active: 0,
  photographers: 0,
  suspended: 0
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchUsers()
  }, 500)
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const params = new URLSearchParams()
    
    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.role) params.append('role', filters.value.role)

    const response = await fetch(`/api/v1/admin/users?${params}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    const data = await response.json()
    
    if (data.status === 'success') {
      users.value = data.data
      stats.value = {
        total: data.meta?.total || data.data.length,
        active: data.data.filter(u => !u.is_suspended).length,
        photographers: data.data.filter(u => u.role === 'photographer').length,
        suspended: data.data.filter(u => u.is_suspended).length
      }
    }
  } catch (error) {
    console.error('Error fetching users:', error)
    showToastMessage('Error loading users')
  } finally {
    loading.value = false
  }
}

const viewUser = (user) => {
  selectedUser.value = user
  showViewModal.value = true
}

const editUser = (user) => {
  selectedUser.value = user
  userForm.value = {
    name: user.name,
    email: user.email,
    phone: user.phone || '',
    password: '',
    role: user.role
  }
  showEditModal.value = true
}

const closeEditModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  selectedUser.value = null
  userForm.value = {
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'client'
  }
}

const saveUser = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const url = showEditModal.value 
      ? `/api/v1/admin/users/${selectedUser.value.id}` 
      : '/api/v1/admin/users'
    
    const method = showEditModal.value ? 'PUT' : 'POST'
    
    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(userForm.value)
    })
    
    const data = await response.json()
    
    if (data.status === 'success') {
      showToastMessage(data.message)
      closeEditModal()
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error saving user')
    }
  } catch (error) {
    console.error('Error saving user:', error)
    showToastMessage('Error saving user')
  }
}

const deleteUser = async (user) => {
  if (!confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) return
  
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/users/${user.id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    const data = await response.json()
    
    if (data.status === 'success') {
      showToastMessage(data.message)
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error deleting user')
    }
  } catch (error) {
    console.error('Error deleting user:', error)
    showToastMessage('Error deleting user')
  }
}

const suspendUser = async (user) => {
  if (!confirm(`Are you sure you want to suspend ${user.name}?`)) return
  
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/users/${user.id}/suspend`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ reason: 'Suspended by admin' })
    })
    
    const data = await response.json()
    
    if (data.status === 'success') {
      showToastMessage(data.message)
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error suspending user')
    }
  } catch (error) {
    console.error('Error suspending user:', error)
    showToastMessage('Error suspending user')
  }
}

const unsuspendUser = async (user) => {
  if (!confirm(`Are you sure you want to unsuspend ${user.name}?`)) return
  
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/users/${user.id}/unsuspend`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    const data = await response.json()
    
    if (data.status === 'success') {
      showToastMessage(data.message)
      fetchUsers()
    } else {
      showToastMessage(data.message || 'Error unsuspending user')
    }
  } catch (error) {
    console.error('Error unsuspending user:', error)
    showToastMessage('Error unsuspending user')
  }
}

const exportUsers = () => {
  showToastMessage('Export feature coming soon')
}

const formatRole = (role) => {
  const roles = {
    'client': 'Client',
    'photographer': 'Photographer',
    'admin': 'Admin',
    'super_admin': 'Super Admin'
  }
  return roles[role] || role
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
.admin-users-management {
  padding: 2rem;
  min-height: 100vh;
  background: #f9fafb;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.page-subtitle {
  color: #6b7280;
  margin: 0.5rem 0 0 0;
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
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  border-left: 4px solid;
}

.stat-blue { border-color: #3b82f6; }
.stat-green { border-color: #10b981; }
.stat-purple { border-color: #8b5cf6; }
.stat-red { border-color: #ef4444; }

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(59, 130, 246, 0.1);
}

.stat-blue .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-green .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-purple .stat-icon { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.stat-red .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-label {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
}

.content-card {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  padding: 1.5rem;
}

.filters-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 300px;
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
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.search-input:focus {
  outline: none;
  border-color: #6c0b1a;
  box-shadow: 0 0 0 3px rgba(108, 11, 26, 0.1);
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: #6c0b1a;
}

.btn-primary {
  display: flex;
  align-items: center;
  background: #6c0b1a;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: #4a070f;
}

.btn-export {
  display: flex;
  align-items: center;
  background: white;
  color: #6b7280;
  padding: 0.75rem 1.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-export:hover {
  background: #f9fafb;
  border-color: #6c0b1a;
  color: #6c0b1a;
}

.loading-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 3rem;
  height: 3rem;
  border: 3px solid #e5e7eb;
  border-top-color: #6c0b1a;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 1rem;
  background: #f9fafb;
  color: #6b7280;
  font-weight: 600;
  font-size: 0.875rem;
  border-bottom: 2px solid #e5e7eb;
}

.data-table td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.user-row:hover {
  background: #f9fafb;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  background: linear-gradient(135deg, #6c0b1a, #9d1429);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
}

.user-name {
  font-weight: 600;
  color: #1f2937;
}

.user-id {
  font-size: 0.75rem;
  color: #9ca3af;
}

.contact-cell {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.email {
  color: #1f2937;
}

.phone {
  font-size: 0.875rem;
  color: #6b7280;
}

.badge {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
}

.badge-client { background: #dbeafe; color: #1e40af; }
.badge-photographer { background: #fce7f3; color: #be185d; }
.badge-admin { background: #fef3c7; color: #92400e; }
.badge-super_admin { background: #fee2e2; color: #991b1b; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-danger { background: #fee2e2; color: #991b1b; }

.verified-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  color: #059669;
  font-size: 0.875rem;
  font-weight: 500;
}

.unverified-badge {
  color: #9ca3af;
  font-size: 0.875rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  width: 2rem;
  height: 2rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 0.375rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  transition: all 0.2s;
}

.btn-action:hover {
  background: #f9fafb;
  border-color: #6c0b1a;
  color: #6c0b1a;
}

.btn-danger:hover {
  background: #fee2e2;
  border-color: #ef4444;
  color: #ef4444;
}

.btn-success:hover {
  background: #d1fae5;
  border-color: #10b981;
  color: #10b981;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #9ca3af;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.pagination-info {
  color: #6b7280;
  font-size: 0.875rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal {
  background: white;
  border-radius: 1rem;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

.modal-close {
  background: none;
  border: none;
  font-size: 2rem;
  color: #9ca3af;
  cursor: pointer;
  padding: 0;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.375rem;
}

.modal-close:hover {
  background: #f3f4f6;
  color: #6b7280;
}

.modal-body {
  padding: 1.5rem;
}

.detail-grid {
  display: grid;
  gap: 1rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.detail-label {
  font-weight: 600;
  color: #6b7280;
}

.detail-value {
  color: #1f2937;
}

.user-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.form-input {
  padding: 0.75rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #6c0b1a;
  box-shadow: 0 0 0 3px rgba(108, 11, 26, 0.1);
}

.form-hint {
  font-size: 0.75rem;
  color: #6b7280;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
  justify-content: flex-end;
}

.btn-secondary {
  padding: 0.75rem 1.5rem;
  border: 1px solid #e5e7eb;
  background: white;
  color: #6b7280;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #6c0b1a;
  color: #6c0b1a;
}

.btn-warning:hover {
  background: #fef3c7;
  border-color: #f59e0b;
  color: #f59e0b;
}

.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #065f46;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  animation: slideIn 0.3s ease-out;
  z-index: 1001;
}

@keyframes slideIn {
  from {
    transform: translateX(400px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
.mr-2 { margin-right: 0.5rem; }
</style>

<style scoped>
.admin-users-management {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.page-subtitle {
  color: #6b7280;
}

.content-card {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  padding: 2rem;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-box {
  background: #f9fafb;
  padding: 1rem;
  border-radius: 0.5rem;
  display: flex;
  flex-direction: column;
}

.stat-label {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

.text-green { color: #10b981; }
.text-red { color: #ef4444; }

.btn-primary {
  background: #6c0b1a;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
}

.btn-primary:hover {
  background: #4a070f;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 1rem;
  background: #f9fafb;
  color: #6b7280;
  font-weight: 600;
  font-size: 0.875rem;
}

.data-table td {
  padding: 1rem;
  border-top: 1px solid #e5e7eb;
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-client { background: #dbeafe; color: #1e40af; }
.badge-photographer { background: #fce7f3; color: #be185d; }
.badge-admin { background: #fef3c7; color: #92400e; }
.badge-super_admin { background: #fee2e2; color: #991b1b; }
.badge-success { background: #d1fae5; color: #065f46; }

.btn-icon {
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  padding: 0.25rem;
  margin: 0 0.25rem;
}

.btn-icon:hover {
  opacity: 0.7;
}
</style>
