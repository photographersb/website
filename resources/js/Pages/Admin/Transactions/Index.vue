<template>
  <div class="min-h-screen">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="💳 Transaction Management" 
      subtitle="Monitor all financial transactions"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <AdminSectionHeader
        title="Transaction Management"
        subtitle="Monitor revenue, refunds, and payment flow health in one place."
        eyebrow="Admin / Transactions"
      >
        <template #actions>
          <button
            class="btn-admin-primary"
            @click="exportTransactions"
          >
            Export
          </button>
          <button
            class="btn-admin-secondary"
            @click="fetchTransactions"
          >
            Refresh List
          </button>
        </template>
      </AdminSectionHeader>

      <AdminStatsStrip :stats="statItems" />

      <!-- Revenue Summary Cards -->
      <div class="revenue-grid">
        <div class="revenue-card revenue-primary">
          <div class="revenue-icon">
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
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="revenue-content">
            <h3>Total Revenue</h3>
            <p class="revenue-amount">
              ৳{{ formatNumber(stats.totalRevenue) }}
            </p>
            <span class="revenue-trend">+{{ stats.growth }}% from last month</span>
          </div>
        </div>

        <div class="revenue-card revenue-secondary">
          <div class="revenue-icon">
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
          <div class="revenue-content">
            <h3>This Month</h3>
            <p class="revenue-amount">
              ৳{{ formatNumber(stats.monthlyRevenue) }}
            </p>
            <span class="revenue-trend">{{ transactions.filter(t => isThisMonth(t.created_at)).length }} transactions</span>
          </div>
        </div>

        <div class="revenue-card revenue-tertiary">
          <div class="revenue-icon">
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
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="revenue-content">
            <h3>Pending</h3>
            <p class="revenue-amount">
              ৳{{ formatNumber(stats.pendingRevenue) }}
            </p>
            <span class="revenue-trend">{{ stats.pendingCount }} transactions</span>
          </div>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="content-card">
        <AdminFilterBar>
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
              placeholder="Search by transaction ID or user..." 
              class="search-input" 
              @input="debounceSearch"
            >
          </div>

          <select
            v-model="filters.status"
            class="filter-select"
            @change="fetchTransactions"
          >
            <option value="">
              All Status
            </option>
            <option value="completed">
              Completed
            </option>
            <option value="pending">
              Pending
            </option>
            <option value="failed">
              Failed
            </option>
          </select>

          <select
            v-model="filters.type"
            class="filter-select"
            @change="fetchTransactions"
          >
            <option value="">
              All Types
            </option>
            <option value="booking">
              Booking Payments
            </option>
            <option value="event_tickets">
              Event Tickets
            </option>
          </select>

          <select
            v-model="filters.method"
            class="filter-select"
            @change="fetchTransactions"
          >
            <option value="">
              All Methods
            </option>
            <option value="card">
              Card / SSLCommerz
            </option>
            <option value="bkash">
              bKash
            </option>
            <option value="nagad">
              Nagad
            </option>
            <option value="rocket">
              Rocket
            </option>
            <option value="manual">
              Manual Transfer
            </option>
          </select>

          <input
            v-model="filters.date"
            type="date"
            class="filter-input"
            @change="fetchTransactions"
          >
        </AdminFilterBar>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading transactions...</p>
        </div>

        <!-- Transactions Table -->
        <div
          v-else-if="transactions.length > 0"
          class="table-container"
        >
          <table class="data-table">
            <thead>
              <tr>
                <th>Transaction ID</th>
                <th>User</th>
                <th>Type</th>
                <th v-if="!filters.type || filters.type === 'booking'">Gateway</th>
                <th v-else-if="filters.type === 'event_tickets'">Event / Method</th>
                <th v-else>Method</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="transaction in transactions"
                :key="transaction.id"
                class="transaction-row"
              >
                <td>
                  <span class="transaction-id">#{{ transaction.transaction_id }}</span>
                </td>
                <td>
                  <div class="user-cell">
                    <div class="user-avatar">
                      {{ transaction.user?.name?.charAt(0).toUpperCase() || 'U' }}
                    </div>
                    <div>
                      <div class="user-name">
                        {{ transaction.user?.name || 'N/A' }}
                      </div>
                      <div class="user-email">
                        {{ transaction.user?.email || 'N/A' }}
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="transaction-type">{{ transaction.type === 'event_tickets' ? 'Event Ticket' : capitalizeFirst(transaction.type) }}</span>
                </td>
                <td>
                  <div v-if="transaction.type === 'event_tickets'">
                    <div class="event-info">
                      <strong>{{ transaction.event?.title || 'Event' }}</strong>
                      <br/>
                      <span class="method-badge" :class="`badge-${getMethodColor(transaction.method)}`">
                        {{ capitalizeFirst(transaction.method) }}
                      </span>
                    </div>
                  </div>
                  <div v-else>
                    <span
                      class="gateway-badge"
                      :class="`badge-${getGatewayColor(transaction.method)}`"
                    >
                      {{ capitalizeFirst(transaction.method) }}
                    </span>
                  </div>
                </td>
                <td>
                  <span class="amount-text">৳{{ formatNumber(transaction.amount) }}</span>
                </td>
                <td>
                  <span :class="`badge badge-${getStatusColor(transaction.status)}`">
                    {{ capitalizeFirst(transaction.status) }}
                  </span>
                </td>
                <td>
                  <span class="date-text">{{ formatDate(transaction.created_at) }}</span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button
                      class="btn-action"
                      title="View Details"
                      @click="transaction.type === 'event_tickets' ? viewEventPayment(transaction) : viewTransaction(transaction)"
                    >
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
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            💳
          </div>
          <p class="empty-title">
            No transactions found
          </p>
          <p class="empty-subtitle">
            Transactions will appear here as they are processed
          </p>
        </div>

        <!-- Pagination -->
        <div
          v-if="transactions.length > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ transactions.length }} transactions
          </div>
        </div>
      </div>

      <!-- View Modal -->
      <div
        v-if="showViewModal"
        class="modal-overlay"
        @click.self="showViewModal = false"
      >
        <div class="modal">
          <div class="modal-header">
            <h3>Transaction Details #{{ selectedTransaction?.id }}</h3>
            <button
              class="modal-close"
              @click="showViewModal = false"
            >
              ×
            </button>
          </div>
          <div
            v-if="selectedTransaction"
            class="modal-body"
          >
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">User:</span>
                <span class="detail-value">{{ selectedTransaction.user?.name }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ selectedTransaction.user?.email }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Amount:</span>
                <span class="detail-value">৳{{ formatNumber(selectedTransaction.amount) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Type:</span>
                <span class="detail-value">{{ capitalizeFirst(selectedTransaction.type) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Payment Method:</span>
                <span :class="`badge badge-${getGatewayColor(selectedTransaction.payment_method)}`">
                  {{ capitalizeFirst(selectedTransaction.payment_method) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span :class="`badge badge-${getStatusColor(selectedTransaction.status)}`">
                  {{ capitalizeFirst(selectedTransaction.status) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Transaction ID:</span>
                <span class="detail-value">#{{ selectedTransaction.id }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ formatDate(selectedTransaction.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Event Payment Modal -->
      <div
        v-if="showEventPaymentModal && selectedEventPayment"
        class="modal-overlay"
        @click.self="showEventPaymentModal = false"
      >
        <div class="modal modal-lg">
          <div class="modal-header">
            <h3>Event Payment Review</h3>
            <button
              class="modal-close"
              @click="showEventPaymentModal = false"
            >
              ×
            </button>
          </div>
          <div class="modal-body payment-modal-body">
            <div class="payment-details-grid">
              <div class="detail-item">
                <span class="detail-label">Event:</span>
                <span class="detail-value">{{ selectedEventPayment.event?.name || 'N/A' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">User:</span>
                <span class="detail-value">{{ selectedEventPayment.user?.name }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ selectedEventPayment.user?.email }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Amount:</span>
                <span class="detail-value">৳{{ formatNumber(selectedEventPayment.amount) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Payment Method:</span>
                <span :class="`badge badge-${getMethodColor(selectedEventPayment.method)}`">
                  {{ capitalizeFirst(selectedEventPayment.method) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span :class="`badge badge-${getStatusColor(selectedEventPayment.status)}`">
                  {{ capitalizeFirst(selectedEventPayment.status) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Transaction ID:</span>
                <span class="detail-value">{{ selectedEventPayment.transaction_id }}</span>
              </div>
              <div class="detail-item" v-if="selectedEventPayment.sender_number">
                <span class="detail-label">Sender Number:</span>
                <span class="detail-value">{{ selectedEventPayment.sender_number }}</span>
              </div>
              <div class="detail-item" v-if="selectedEventPayment.trx_id">
                <span class="detail-label">TRX ID:</span>
                <span class="detail-value">{{ selectedEventPayment.trx_id }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ formatDate(selectedEventPayment.created_at) }}</span>
              </div>
            </div>

            <!-- Screenshot Preview -->
            <div v-if="selectedEventPayment.screenshot_path" class="screenshot-section">
              <h4>Payment Proof</h4>
              <img :src="`/storage/${selectedEventPayment.screenshot_path}`" alt="Payment Proof" class="screenshot-preview"/>
            </div>

            <!-- Admin Note -->
            <div v-if="selectedEventPayment.status === 'pending'" class="admin-note-section">
              <label class="detail-label">Admin Note (Optional)</label>
              <textarea
                v-model="approvalAdminNote"
                placeholder="Add any notes about this payment approval/rejection..."
                class="admin-note-textarea"
              ></textarea>
            </div>

            <!-- Admin Note for Cancellation -->
            <div v-if="selectedEventPayment.status === 'completed'" class="admin-note-section">
              <label class="detail-label">Cancellation Note (Optional)</label>
              <textarea
                v-model="approvalAdminNote"
                placeholder="Reason for cancellation..."
                class="admin-note-textarea"
              ></textarea>
            </div>

            <!-- Approved/Rejected Info -->
            <div v-if="selectedEventPayment.status === 'rejected' || selectedEventPayment.status === 'cancelled'" class="approval-info">
              <p v-if="selectedEventPayment.verified_by_user_id">
                <strong>Verified By:</strong> Admin ID {{ selectedEventPayment.verified_by_user_id }}
              </p>
              <p v-if="selectedEventPayment.verified_at">
                <strong>Verified At:</strong> {{ formatDate(selectedEventPayment.verified_at) }}
              </p>
            </div>
          </div>
          <div class="modal-footer" v-if="selectedEventPayment.status === 'pending'">
            <button
              class="btn btn-secondary"
              @click="showEventPaymentModal = false"
            >
              Close
            </button>
            <button
              class="btn btn-danger"
              :disabled="approvingPayment"
              @click="rejectEventPayment"
            >
              {{ approvingPayment ? 'Processing...' : 'Reject' }}
            </button>
            <button
              class="btn btn-success"
              :disabled="approvingPayment"
              @click="approveEventPayment"
            >
              {{ approvingPayment ? 'Processing...' : 'Approve' }}
            </button>
          </div>
          <!-- Cancel button for completed payments -->
          <div class="modal-footer" v-else-if="selectedEventPayment.status === 'completed'">
            <button
              class="btn btn-secondary"
              @click="showEventPaymentModal = false"
            >
              Close
            </button>
            <button
              class="btn btn-danger"
              :disabled="approvingPayment"
              @click="cancelEventPayment"
            >
              {{ approvingPayment ? 'Processing...' : 'Cancel Payment' }}
            </button>
          </div>
          <!-- Close button for other statuses -->
          <div class="modal-footer" v-else>
            <button
              class="btn btn-secondary"
              @click="showEventPaymentModal = false"
            >
              Close
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
import { ref, computed, onMounted, h } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import AdminSectionHeader from '../../../components/admin/ui/AdminSectionHeader.vue'
import AdminStatsStrip from '../../../components/admin/ui/AdminStatsStrip.vue'
import AdminFilterBar from '../../../components/admin/ui/AdminFilterBar.vue'
import api from '../../../api'

const transactions = ref([])
const loading = ref(false)
const showViewModal = ref(false)
const selectedTransaction = ref(null)
const showEventPaymentModal = ref(false)
const selectedEventPayment = ref(null)
const approvalAdminNote = ref('')
const approvingPayment = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({
  search: '',
  status: '',
  type: '',
  method: '',
  date: ''
})

// Stats now come from backend API
const stats = ref({
  totalRevenue: 0,
  monthlyRevenue: 0,
  pendingRevenue: 0,
  pendingCount: 0,
  growth: 0,
  total: 0,
  completed: 0,
  pending: 0,
  failed: 0,
  refunded: 0,
  total_revenue: 0,
  today_revenue: 0,
  yearly_revenue: 0
})

const RevenueIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
])

const CalendarIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
])

const PendingIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
])

const PendingCountIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
])

const statItems = computed(() => [
  {
    label: 'Total Revenue',
    value: `৳${formatNumber(stats.value.totalRevenue || 0)}`,
    meta: `+${stats.value.growth || 0}% from last month`,
    icon: RevenueIcon,
    tone: 'neutral',
  },
  {
    label: 'This Month',
    value: `৳${formatNumber(stats.value.monthlyRevenue || 0)}`,
    meta: `${transactions.value.filter(t => isThisMonth(t.created_at)).length} transactions`,
    icon: CalendarIcon,
    tone: 'info',
  },
  {
    label: 'Pending Revenue',
    value: `৳${formatNumber(stats.value.pendingRevenue || 0)}`,
    meta: 'Awaiting settlement',
    icon: PendingIcon,
    tone: 'warning',
  },
  {
    label: 'Pending Count',
    value: stats.value.pendingCount || 0,
    meta: 'Transactions in queue',
    icon: PendingCountIcon,
    tone: 'success',
  }
])

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchTransactions()
  }, 500)
}

const isThisMonth = (date) => {
  const now = new Date()
  const transDate = new Date(date)
  return transDate.getMonth() === now.getMonth() && transDate.getFullYear() === now.getFullYear()
}

const fetchTransactions = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.status) params.status = filters.value.status
    if (filters.value.type) params.type = filters.value.type
    if (filters.value.method) params.gateway = filters.value.method
    if (filters.value.date) params.date_from = filters.value.date

    const { data: result } = await api.get('/admin/transactions', { params })
    transactions.value = result.data || []

    // Update stats from backend response
    if (result.meta?.stats) {
      stats.value = {
        totalRevenue: result.meta.stats.total_revenue || 0,
        monthlyRevenue: result.meta.stats.monthly_revenue || 0,
        pendingRevenue: 0,
        pendingCount: result.meta.stats.pending || 0,
        growth: result.meta.stats.growth ?? stats.value.growth,
        ...result.meta.stats
      }
    }
  } catch (error) {
    console.error('Error fetching transactions:', error)
    showToastMessage('Error loading transactions')
  } finally {
    loading.value = false
  }
}

const viewTransaction = (transaction) => {
  selectedTransaction.value = transaction
  showViewModal.value = true
}

const viewEventPayment = (transaction) => {
  if (transaction.type === 'event_tickets') {
    selectedEventPayment.value = transaction
    showEventPaymentModal.value = true
  }
}

const approveEventPayment = async () => {
  approvingPayment.value = true
  try {
    const paymentId = selectedEventPayment.value.id.replace('event_', '')
    await api.post(`/admin/transactions/event-payment/${paymentId}/approve`, {
      admin_note: approvalAdminNote.value
    })
    showToastMessage('Event payment approved successfully')
    showEventPaymentModal.value = false
    approvalAdminNote.value = ''
    fetchTransactions()
  } catch (error) {
    console.error('Error approving payment:', error)
    showToastMessage('Error approving payment')
  } finally {
    approvingPayment.value = false
  }
}

const rejectEventPayment = async () => {
  approvingPayment.value = true
  try {
    const paymentId = selectedEventPayment.value.id.replace('event_', '')
    await api.post(`/admin/transactions/event-payment/${paymentId}/reject`, {
      admin_note: approvalAdminNote.value
    })
    showToastMessage('Event payment rejected successfully')
    showEventPaymentModal.value = false
    approvalAdminNote.value = ''
    fetchTransactions()
  } catch (error) {
    console.error('Error rejecting payment:', error)
    showToastMessage('Error rejecting payment')
  } finally {
    approvingPayment.value = false
  }
}

const cancelEventPayment = async () => {
  approvingPayment.value = true
  try {
    const paymentId = selectedEventPayment.value.id.replace('event_', '')
    await api.post(`/admin/transactions/event-payment/${paymentId}/cancel`, {
      admin_note: approvalAdminNote.value
    })
    showToastMessage('Event payment cancelled and ticket availability restored')
    showEventPaymentModal.value = false
    approvalAdminNote.value = ''
    fetchTransactions()
  } catch (error) {
    console.error('Error cancelling payment:', error)
    showToastMessage('Error cancelling payment')
  } finally {
    approvingPayment.value = false
  }
}

const exportTransactions = () => {
  showToastMessage('Export feature coming soon')
}

const getStatusColor = (status) => {
  const colors = {
    completed: 'success',
    pending: 'warning',
    failed: 'danger',
    rejected: 'danger',
    cancelled: 'warning',
    refunded: 'info'
  }
  return colors[status] || 'gray'
}

const getGatewayColor = (method) => {
  const colors = {
    card: 'blue',
    bkash: 'pink',
    nagad: 'orange',
    bank: 'purple'
  }
  return colors[method] || 'gray'
}

const getMethodColor = (method) => {
  const colors = {
    manual: 'blue',
    bkash: 'pink',
    nagad: 'orange',
    rocket: 'purple',
    card: 'blue'
  }
  return colors[method] || 'gray'
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
  fetchTransactions()
})
</script>

<style scoped>
.admin-transactions { padding: 2rem; min-height: 100vh; background: var(--admin-bg-page); }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-title { font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0; }
.page-subtitle { color: #6b7280; margin: 0.5rem 0 0 0; }

.btn-export-main { display: flex; align-items: center; background: var(--admin-brand-primary); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600; transition: background 0.2s; }
.btn-export-main:hover { background: var(--admin-brand-primary-dark); }

.revenue-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.revenue-card { background: linear-gradient(135deg, var(--admin-brand-primary), var(--admin-brand-primary-dark)); color: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 1.5rem; }
.revenue-secondary { background: linear-gradient(135deg, var(--admin-brand-primary-light), var(--admin-brand-primary)); }
.revenue-tertiary { background: linear-gradient(135deg, var(--admin-brand-primary), var(--admin-brand-primary-dark)); }
.revenue-icon { width: 4rem; height: 4rem; background: rgba(255,255,255,0.2); border-radius: 1rem; display: flex; align-items: center; justify-content: center; }
.revenue-content h3 { font-size: 1rem; margin: 0 0 0.5rem 0; opacity: 0.9; }
.revenue-amount { font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; }
.revenue-trend { font-size: 0.875rem; opacity: 0.8; }

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
.transaction-row:hover { background: #f9fafb; }

.transaction-id { font-weight: 600; color: var(--admin-brand-primary); }
.user-cell { display: flex; align-items: center; gap: 0.75rem; }
.user-avatar { width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--admin-brand-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1rem; }
.user-name { font-weight: 600; color: #1f2937; }
.user-email { font-size: 0.75rem; color: #6b7280; }
.transaction-type { color: #6b7280; }
.amount-text, .date-text { color: #1f2937; font-weight: 500; }

.badge, .gateway-badge { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.badge-success { background: var(--admin-success-light); color: var(--admin-success-text); }
.badge-warning { background: var(--admin-warning-light); color: var(--admin-warning-text); }
.badge-danger { background: var(--admin-danger-light); color: var(--admin-danger-text); }
.badge-info { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-blue { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-pink { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-orange { background: var(--admin-warning-light); color: var(--admin-warning-text); }
.badge-purple { background: var(--admin-info-light); color: var(--admin-info-text); }
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
.modal { background: white; border-radius: 1rem; max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-header h3 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; }
.modal-close { background: none; border: none; font-size: 2rem; color: #9ca3af; cursor: pointer; padding: 0; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; border-radius: 0.375rem; }
.modal-close:hover { background: #f3f4f6; color: #6b7280; }
.modal-body { padding: 1.5rem; }

.detail-grid { display: grid; gap: 1rem; }
.detail-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; }
.detail-label { font-weight: 600; color: #6b7280; }
.detail-value { color: #1f2937; }

.modal-lg { max-width: 800px; }
.payment-modal-body { }
.payment-details-grid { display: grid; gap: 1rem; margin-bottom: 2rem; }
.screenshot-section { margin: 2rem 0; }
.screenshot-section h4 { margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #1f2937; }
.screenshot-preview { max-width: 100%; max-height: 400px; border-radius: 0.5rem; border: 1px solid #e5e7eb; }
.admin-note-section { margin: 2rem 0; }
.admin-note-textarea { width: 100%; min-height: 100px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem; }
.approval-info { background: #f0fdf4; padding: 1rem; border-radius: 0.5rem; color: #166534; }
.approval-info p { margin: 0.5rem 0; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1.5rem; border-top: 1px solid #e5e7eb; }
.btn { padding: 0.5rem 1rem; border: none; border-radius: 0.375rem; cursor: pointer; font-weight: 500; transition: background-color 0.2s; }
.btn:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { background: #e5e7eb; color: #1f2937; }
.btn-secondary:hover:not(:disabled) { background: #d1d5db; }
.btn-success { background: #10b981; color: white; }
.btn-success:hover:not(:disabled) { background: #059669; }
.btn-danger { background: #ef4444; color: white; }
.btn-danger:hover:not(:disabled) { background: #dc2626; }
.method-badge { padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
.badge-blue { background: #dbeafe; color: #1e40af; }
.badge-pink { background: #fbddf3; color: #9d174d; }
.badge-orange { background: #fed7aa; color: #92400e; }
.badge-purple { background: #e9d5ff; color: #6b21a8; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-info { background: #cffafe; color: #164e63; }
.badge-gray { background: #f3f4f6; color: #374151; }

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
