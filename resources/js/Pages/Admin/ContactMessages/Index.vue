<template>
  <div class="min-h-screen">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="📧 Contact Messages & Inquiries" 
      subtitle="Manage customer inquiries and support requests"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">INBOX CONTROL</p>
          <h1 class="hero-title">Inquiries, triaged and transparent.</h1>
          <p class="hero-subtitle">
            Keep customer conversations moving with clarity and speed.
          </p>
          <div class="hero-actions">
            <button
              class="btn-admin-primary"
              @click="exportMessages"
            >
              Export Messages
            </button>
            <button
              class="btn-admin-secondary"
              @click="fetchMessages"
            >
              Refresh Inbox
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Pending</span>
            <span class="status-value">{{ stats.pending || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Responded</span>
            <span class="status-value">{{ stats.responded || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Total</span>
            <span class="status-value">{{ stats.total || 0 }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Filter by type, status, and response cadence
        </div>
      </div>

      <!-- Quick Navigation -->
      <AdminQuickNav />

      <!-- Stats Cards -->
      <div class="stats-grid">
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
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Pending</span>
            <span class="stat-value">{{ stats.pending || 0 }}</span>
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
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Responded</span>
            <span class="stat-value">{{ stats.responded || 0 }}</span>
          </div>
        </div>

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
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Messages</span>
            <span class="stat-value">{{ stats.total || 0 }}</span>
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
                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Archived</span>
            <span class="stat-value">{{ stats.archived || 0 }}</span>
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
              v-model="searchQuery" 
              type="text"
              placeholder="Search messages by name, email, or subject..." 
              class="search-input" 
              @input="debounceSearch"
            >
          </div>

          <select
            v-model="filterType"
            class="filter-select"
            @change="fetchMessages"
          >
            <option value="">
              All Types
            </option>
            <option value="contact">
              Contact
            </option>
            <option value="sponsorship">
              Sponsorship
            </option>
            <option value="general">
              General Inquiry
            </option>
            <option value="support">
              Support Request
            </option>
          </select>

          <select
            v-model="filterStatus"
            class="filter-select"
            @change="fetchMessages"
          >
            <option value="">
              All Status
            </option>
            <option value="pending">
              Pending
            </option>
            <option value="read">
              Read
            </option>
            <option value="resolved">
              Resolved
            </option>
            <option value="archived">
              Archived
            </option>
          </select>

          
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading messages...</p>
        </div>

        <!-- Messages Grid -->
        <div
          v-else-if="messages.length > 0"
          class="messages-grid"
        >
          <div
            v-for="message in messages"
            :key="message.id"
            class="message-card"
          >
            <div class="message-header">
              <div class="message-type">
                <span
                  :class="getTypeClass(message.type)"
                  class="type-badge"
                >
                  {{ getTypeLabel(message.type) }}
                </span>
                <span
                  :class="getStatusClass(message.status)"
                  class="status-badge"
                >
                  {{ message.status }}
                </span>
              </div>
              <div class="quick-actions">
                <button
                  v-if="message.status === 'pending'"
                  class="quick-action-btn success"
                  title="Mark as Responded"
                  @click.stop="markAsResponded(message.id)"
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
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                </button>
                <button
                  v-if="message.status !== 'archived'"
                  class="quick-action-btn archive"
                  title="Archive"
                  @click.stop="archiveMessage(message.id)"
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
                      d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                    />
                  </svg>
                </button>
              </div>
            </div>

            <h3 class="message-subject">
              {{ message.subject }}
            </h3>
            
            <div class="message-date-row">
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
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
              <span>{{ formatDate(message.created_at) }}</span>
            </div>
            
            <div class="message-contact-info">
              <div class="contact-detail">
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
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  />
                </svg>
                <span>{{ message.name }}</span>
              </div>
              <div class="contact-detail">
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
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  />
                </svg>
                <span>{{ message.email }}</span>
              </div>
              <div
                v-if="message.phone"
                class="contact-detail"
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
                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                  />
                </svg>
                <span>{{ message.phone }}</span>
              </div>
            </div>

            <p class="message-preview">
              {{ truncateMessage(message.message) }}
            </p>

            <div class="card-actions">
              <button
                class="btn-action btn-view"
                @click="viewMessage(message)"
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
                View Full Message
              </button>
              <button
                v-if="message.status === 'pending'"
                class="btn-action btn-respond"
                @click="markAsResponded(message.id)"
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
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                Mark Responded
              </button>
              <button
                v-if="message.status !== 'archived'"
                class="btn-action btn-archive"
                @click="archiveMessage(message.id)"
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
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                  />
                </svg>
                Archive
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            📧
          </div>
          <p class="empty-title">
            No messages found
          </p>
          <p class="empty-subtitle">
            Messages will appear here when users contact you
          </p>
        </div>
      </div>
    </div>

    <!-- View Message Modal -->
    <div
      v-if="viewingMessage"
      class="modal-overlay"
      @click.self="viewingMessage = null"
    >
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>Message Details</h3>
          <button
            class="modal-close"
            @click="viewingMessage = null"
          >
            &times;
          </button>
        </div>
        <div class="modal-body">
          <div class="message-detail-section">
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Type:</span>
                <span
                  :class="getTypeClass(viewingMessage.type)"
                  class="type-badge"
                >
                  {{ getTypeLabel(viewingMessage.type) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span
                  :class="getStatusClass(viewingMessage.status)"
                  class="status-badge"
                >
                  {{ viewingMessage.status }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Received:</span>
                <span class="detail-value">{{ formatDateTime(viewingMessage.created_at) }}</span>
              </div>
            </div>

            <div class="contact-info-section">
              <h4>Contact Information</h4>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Name:</span>
                  <span class="detail-value">{{ viewingMessage.name }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Email:</span>
                  <span class="detail-value">{{ viewingMessage.email }}</span>
                </div>
                <div
                  v-if="viewingMessage.phone"
                  class="detail-item"
                >
                  <span class="detail-label">Phone:</span>
                  <span class="detail-value">{{ viewingMessage.phone }}</span>
                </div>
                <div
                  v-if="viewingMessage.company"
                  class="detail-item"
                >
                  <span class="detail-label">Company:</span>
                  <span class="detail-value">{{ viewingMessage.company }}</span>
                </div>
              </div>
            </div>

            <div class="message-content-section">
              <h4>{{ viewingMessage.subject }}</h4>
              <p>{{ viewingMessage.message }}</p>
            </div>

            <div class="modal-actions">
              <button
                v-if="viewingMessage.status === 'pending'"
                class="btn-respond-full"
                @click="markAsResponded(viewingMessage.id); viewingMessage = null"
              >
                <svg
                  class="w-5 h-5 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                Mark as Responded
              </button>
              <button
                v-if="viewingMessage.status !== 'archived'"
                class="btn-archive-full"
                @click="archiveMessage(viewingMessage.id); viewingMessage = null"
              >
                <svg
                  class="w-5 h-5 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
                  />
                </svg>
                Archive Message
              </button>
            </div>
          </div>
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
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'
import { formatDate as formatDateValue, formatDateTime as formatDateTimeValue } from '../../../utils/formatters'

const messages = ref([])
const loading = ref(false)
const viewingMessage = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

const searchQuery = ref('')
const filterType = ref('')
const filterStatus = ref('')

const stats = ref({
  total: 0,
  pending: 0,
  responded: 0,
  archived: 0
})

let searchTimeout = null

onMounted(() => {
  fetchMessages()
  fetchStats()
})

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchMessages()
  }, 500)
}

const fetchMessages = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (searchQuery.value) params.append('search', searchQuery.value)
    if (filterType.value) params.append('type', filterType.value)
    if (filterStatus.value) params.append('status', filterStatus.value)
    
    const response = await api.get(`/admin/contact-messages?${params}`)
    messages.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching messages:', error)
    showToastMessage('Error loading messages')
  } finally {
    loading.value = false
  }
}

const fetchStats = async () => {
  try {
    const response = await api.get('/admin/contact-messages/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const viewMessage = (message) => {
  viewingMessage.value = message
}

const markAsResponded = async (id) => {
  try {
    await api.put(`/admin/contact-messages/${id}/respond`)
    showToastMessage('Message marked as responded')
    fetchMessages()
    fetchStats()
  } catch (error) {
    console.error('Error updating message:', error)
    showToastMessage('Error updating message status')
  }
}

const archiveMessage = async (id) => {
  if (confirm('Archive this message?')) {
    try {
      await api.put(`/admin/contact-messages/${id}/archive`)
      showToastMessage('Message archived successfully')
      fetchMessages()
      fetchStats()
    } catch (error) {
      console.error('Error archiving message:', error)
      showToastMessage('Error archiving message')
    }
  }
}

const exportMessages = () => {
  showToastMessage('Export feature coming soon')
}

const getTypeClass = (type) => {
  const classes = {
    contact: 'badge-blue',
    sponsorship: 'badge-purple',
    general: 'badge-blue',
    support: 'badge-yellow'
  }
  return classes[type] || 'badge-gray'
}

const getTypeLabel = (type) => {
  const labels = {
    contact: '📬 Contact',
    sponsorship: '💼 Sponsorship',
    general: '📝 General Inquiry',
    support: '🆘 Support Request'
  }
  return labels[type] || type
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'badge-yellow',
    read: 'badge-success',
    resolved: 'badge-success',
    archived: 'badge-gray'
  }
  return classes[status] || 'badge-gray'
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const formatDateTime = (date) => {
  return formatDateTimeValue(date)
}

const truncateMessage = (message) => {
  return message?.length > 120 ? message.substring(0, 120) + '...' : message
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}
</script>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

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
@media (max-width: 1024px) { .page-hero { grid-template-columns: 1fr; } }

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

.stat-blue { border-color: var(--admin-info); }
.stat-green { border-color: var(--admin-success); }
.stat-yellow { border-color: var(--admin-warning); }
.stat-purple { border-color: var(--admin-info); }

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-blue .stat-icon { background: var(--admin-info-light); color: var(--admin-info); }
.stat-green .stat-icon { background: var(--admin-success-light); color: var(--admin-success); }
.stat-yellow .stat-icon { background: var(--admin-warning-light); color: var(--admin-warning); }
.stat-purple .stat-icon { background: var(--admin-info-light); color: var(--admin-info); }

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
search-box {
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
  border-color: var(--admin-brand-primary);
  box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.1);
}

.filters-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
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
  border-color: var(--admin-brand-primary);
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
  margin-left: auto;
}

.btn-export:hover {
  background: #f9fafb;
  border-color: var(--admin-brand-primary);
  color: var(--admin-brand-primary);
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
  border-top-color: var(--admin-brand-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.messages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.message-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s;
}

.message-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.message-type {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  flex: 1;
}

.quick-actions {
  display: flex;
  gap: 0.375rem;
}

.quick-action-btn {
  width: 2rem;
  height: 2rem;
  border-radius: 0.375rem;
  border: 1px solid #e5e7eb;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.message-date-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #9ca3af;
  font-size: 0.75rem;
  margin-bottom: 1rem;
}

.message-date-row svg {
  flex-shrink: 0;
}

.quick-action-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.quick-action-btn.success {
  background: var(--admin-success-light);
  border-color: var(--admin-success);
  color: var(--admin-success-text);
}

.quick-action-btn.success:hover {
  background: var(--admin-success);
  color: white;
}

.quick-action-btn.archive {
  background: var(--admin-warning-light);
  border-color: var(--admin-warning);
  color: var(--admin-warning-text);
}

.quick-action-btn.archive:hover {
  background: var(--admin-warning);
  color: white;
}

.type-badge, .status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-purple { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-blue { background: var(--admin-info-light); color: var(--admin-info-text); }
.badge-yellow { background: var(--admin-warning-light); color: var(--admin-warning-text); }
.badge-success { background: var(--admin-success-light); color: var(--admin-success-text); }
.badge-gray { background: var(--admin-bg-hover); color: var(--admin-text-secondary); }

.message-date {
  color: #9ca3af;
  font-size: 0.75rem;
  white-space: nowrap;
}

.message-subject {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem 0;
}

.message-contact-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1rem;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.contact-detail {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.contact-detail svg {
  color: #9ca3af;
  flex-shrink: 0;
}

.message-preview {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.6;
  margin: 0 0 1rem 0;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn-action {
  flex: 1;
  min-width: max-content;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.375rem;
  padding: 0.625rem 1rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 0.5rem;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-view:hover {
  background: var(--admin-brand-primary);
  border-color: var(--admin-brand-primary);
  color: white;
}

.btn-respond {
  background: #d1fae5;
  border-color: #10b981;
  color: #065f46;
}

.btn-respond:hover {
  background: #10b981;
  color: white;
}

.btn-archive:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #9ca3af;
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.empty-subtitle {
  color: #9ca3af;
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

.modal-large {
  max-width: 900px;
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

.message-detail-section {
  display: flex;
  flex-direction: column;
  gap: 2rem;
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

.contact-info-section h4,
.message-content-section h4 {
  margin: 0 0 1rem 0;
  font-size: 1.125rem;
  color: #1f2937;
  font-weight: 600;
}

.message-content-section p {
  color: #6b7280;
  line-height: 1.8;
  white-space: pre-wrap;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-respond-full,
.btn-archive-full {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-respond-full {
  background: #10b981;
  color: white;
}

.btn-respond-full:hover {
  background: #059669;
}

.btn-archive-full {
  background: #6b7280;
  color: white;
}

.btn-archive-full:hover {
  background: #4b5563;
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
