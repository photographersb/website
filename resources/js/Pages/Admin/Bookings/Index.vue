<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="📋 Booking Management" 
      subtitle="Monitor and manage all platform bookings"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <!-- Export Button -->
      <div class="flex justify-end">
        <button @click="exportBookings" class="btn-export-main">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export Data
        </button>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid">
      <div class="stat-card stat-blue">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Bookings</span>
          <span class="stat-value">{{ stats.total }}</span>
        </div>
      </div>

      <div class="stat-card stat-yellow">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Pending</span>
          <span class="stat-value">{{ stats.pending }}</span>
        </div>
      </div>

      <div class="stat-card stat-green">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Confirmed</span>
          <span class="stat-value">{{ stats.confirmed }}</span>
        </div>
      </div>

      <div class="stat-card stat-purple">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Revenue</span>
          <span class="stat-value">৳{{ formatNumber(stats.revenue) }}</span>
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
            placeholder="Search by client or photographer name..." 
            class="search-input"
          />
        </div>

        <select v-model="filters.status" @change="fetchBookings" class="filter-select">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>

        <input v-model="filters.date" @change="fetchBookings" type="date" class="filter-input" />
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading bookings...</p>
      </div>

      <!-- Bookings Table -->
      <div v-else-if="bookings.length > 0" class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Client</th>
              <th>Photographer</th>
              <th>Event Type</th>
              <th>Event Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in bookings" :key="booking.id" class="booking-row">
              <td>
                <span class="booking-id">#{{ booking.id }}</span>
              </td>
              <td>
                <div class="user-cell">
                  <div class="user-avatar">{{ booking.client?.name?.charAt(0).toUpperCase() || 'C' }}</div>
                  <div>
                    <div class="user-name">{{ booking.client?.name || 'N/A' }}</div>
                    <div class="user-email">{{ booking.client?.email || 'N/A' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="user-cell">
                  <div class="user-avatar photographer-avatar">{{ booking.photographer?.user?.name?.charAt(0).toUpperCase() || 'P' }}</div>
                  <div>
                    <div class="user-name">{{ booking.photographer?.user?.name || 'N/A' }}</div>
                    <div class="user-email">{{ booking.photographer?.business_name || 'N/A' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="event-type">{{ booking.event_type || 'General' }}</span>
              </td>
              <td>
                <span class="date-text">{{ formatDate(booking.event_date) }}</span>
              </td>
              <td>
                <span class="amount-text">৳{{ formatNumber(booking.total_amount) }}</span>
              </td>
              <td>
                <span :class="`badge badge-${getStatusColor(booking.status)}`">
                  {{ capitalizeFirst(booking.status) }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button @click="viewBooking(booking)" class="btn-action" title="View Details">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">📋</div>
        <p class="empty-title">No bookings found</p>
        <p class="empty-subtitle">Bookings will appear here as they are created</p>
      </div>

      <!-- Pagination -->
      <div v-if="bookings.length > 0" class="pagination">
        <div class="pagination-info">
          Showing {{ bookings.length }} bookings
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal" class="modal-overlay" @click.self="showViewModal = false">
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>Booking Details #{{ selectedBooking?.id }}</h3>
          <button @click="showViewModal = false" class="modal-close">×</button>
        </div>
        <div class="modal-body" v-if="selectedBooking">
          <div class="detail-sections">
            <div class="detail-section">
              <h4>Client Information</h4>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Name:</span>
                  <span class="detail-value">{{ selectedBooking.client?.name }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Email:</span>
                  <span class="detail-value">{{ selectedBooking.client?.email }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Phone:</span>
                  <span class="detail-value">{{ selectedBooking.client?.phone || 'N/A' }}</span>
                </div>
              </div>
            </div>

            <div class="detail-section">
              <h4>Photographer Information</h4>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Name:</span>
                  <span class="detail-value">{{ selectedBooking.photographer?.user?.name }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Business:</span>
                  <span class="detail-value">{{ selectedBooking.photographer?.business_name }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Email:</span>
                  <span class="detail-value">{{ selectedBooking.photographer?.user?.email }}</span>
                </div>
              </div>
            </div>

            <div class="detail-section">
              <h4>Booking Details</h4>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Event Type:</span>
                  <span class="detail-value">{{ selectedBooking.event_type }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Event Date:</span>
                  <span class="detail-value">{{ formatDate(selectedBooking.event_date) }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Location:</span>
                  <span class="detail-value">{{ selectedBooking.location || 'N/A' }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Total Amount:</span>
                  <span class="detail-value">৳{{ formatNumber(selectedBooking.total_amount) }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Status:</span>
                  <span :class="`badge badge-${getStatusColor(selectedBooking.status)}`">
                    {{ capitalizeFirst(selectedBooking.status) }}
                  </span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Created:</span>
                  <span class="detail-value">{{ formatDate(selectedBooking.created_at) }}</span>
                </div>
              </div>
            </div>

            <div v-if="selectedBooking.notes" class="detail-section">
              <h4>Notes</h4>
              <p class="detail-notes">{{ selectedBooking.notes }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Toast -->
    <div v-if="showToast" class="toast">{{ toastMessage }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

const bookings = ref([])
const loading = ref(false)
const showViewModal = ref(false)
const selectedBooking = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({
  search: '',
  status: '',
  date: ''
})

// Stats from backend (not computed from client data)
const stats = ref({
  total: 0,
  pending: 0,
  confirmed: 0,
  in_progress: 0,
  completed: 0,
  cancelled: 0,
  revenue: 0,
  monthly_revenue: 0
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchBookings()
  }, 500)
}

const fetchBookings = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const params = new URLSearchParams()
    
    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.status) params.append('status', filters.value.status)
    if (filters.value.date) params.append('event_date', filters.value.date)
    
    const response = await fetch(`/api/v1/admin/bookings?${params}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    const data = await response.json()
    if (response.ok) {
      bookings.value = data.data || []
      // Update stats from backend response
      if (data.stats) {
        stats.value = data.stats
      }
    }
  } catch (error) {
    console.error('Error fetching bookings:', error)
    showToastMessage('Error loading bookings')
  } finally {
    loading.value = false
  }
}

const viewBooking = (booking) => {
  selectedBooking.value = booking
  showViewModal.value = true
}

const exportBookings = () => {
  showToastMessage('Export feature coming soon')
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    confirmed: 'success',
    in_progress: 'info',
    completed: 'purple',
    cancelled: 'danger'
  }
  return colors[status] || 'gray'
}

const capitalizeFirst = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1).replace('_', ' ')
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-BD').format(num || 0)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}-${month}-${year}`
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

onMounted(() => {
  fetchBookings()
})
</script>

<style scoped>
.admin-bookings { padding: 2rem; min-height: 100vh; background: var(--admin-bg-page); }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-title { font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0; }
.page-subtitle { color: #6b7280; margin: 0.5rem 0 0 0; }

.btn-export-main { display: flex; align-items: center; background: var(--admin-brand-primary); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600; transition: background 0.2s; }
.btn-export-main:hover { background: var(--admin-brand-primary-dark); }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 1rem; border-left: 4px solid; }
.stat-blue { border-color: var(--admin-brand-primary); }
.stat-yellow { border-color: var(--admin-brand-primary); }
.stat-green { border-color: var(--admin-brand-primary); }
.stat-purple { border-color: var(--admin-brand-primary); }
.stat-icon { width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; }
.stat-blue .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-yellow .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-green .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-purple .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-content { display: flex; flex-direction: column; }
.stat-label { color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem; }
.stat-value { font-size: 2rem; font-weight: 700; color: #1f2937; }

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

.table-container { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 1rem; background: #f9fafb; color: #6b7280; font-weight: 600; font-size: 0.875rem; border-bottom: 2px solid #e5e7eb; }
.data-table td { padding: 1rem; border-bottom: 1px solid #f3f4f6; }
.booking-row:hover { background: #f9fafb; }

.booking-id { font-weight: 600; color: var(--admin-brand-primary); }
.user-cell { display: flex; align-items: center; gap: 0.75rem; }
.user-avatar { width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--admin-brand-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1rem; }
.photographer-avatar { background: var(--admin-brand-primary); }
.user-name { font-weight: 600; color: #1f2937; }
.user-email { font-size: 0.75rem; color: #6b7280; }
.event-type { color: #6b7280; }
.date-text, .amount-text { color: #1f2937; font-weight: 500; }

.badge { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.badge-warning { background: var(--admin-warning-light); color: var(--admin-warning-text); }
.badge-success { background: var(--admin-success-light); color: var(--admin-success-text); }
.badge-info { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-purple { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-danger { background: var(--admin-danger-light); color: var(--admin-danger-text); }
.badge-gray { background: #f3f4f6; color: #6b7280; }

.action-buttons { display: flex; gap: 0.5rem; }
.btn-action { width: 2rem; height: 2rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.375rem; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; transition: all 0.2s; }
.btn-action:hover { background: #f9fafb; border-color: var(--admin-brand-primary); color: var(--admin-brand-primary); }

.empty-state { text-align: center; padding: 4rem 2rem; color: #9ca3af; }
.empty-icon { font-size: 5rem; margin-bottom: 1rem; opacity: 0.5; }
.empty-title { font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem; }
.empty-subtitle { color: #9ca3af; }

.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; }
.pagination-info { color: #6b7280; font-size: 0.875rem; }

.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1rem; }
.modal { background: white; border-radius: 1rem; max-width: 800px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
.modal-large { max-width: 900px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-header h3 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; }
.modal-close { background: none; border: none; font-size: 2rem; color: #9ca3af; cursor: pointer; padding: 0; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; border-radius: 0.375rem; }
.modal-close:hover { background: #f3f4f6; color: #6b7280; }
.modal-body { padding: 1.5rem; }

.detail-sections { display: flex; flex-direction: column; gap: 2rem; }
.detail-section { }
.detail-section h4 { margin: 0 0 1rem 0; font-size: 1.125rem; color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }
.detail-grid { display: grid; gap: 1rem; }
.detail-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; }
.detail-label { font-weight: 600; color: #6b7280; }
.detail-value { color: #1f2937; }
.detail-notes { color: #6b7280; line-height: 1.6; background: #f9fafb; padding: 1rem; border-radius: 0.5rem; }

.toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--admin-success); color: white; padding: 1rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); animation: slideIn 0.3s ease-out; z-index: 1001; }
@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
.mr-2 { margin-right: 0.5rem; }
</style>
