<template>
  <div class="min-h-screen">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="✅ Verification Management" 
      subtitle="Review and approve photographer verification requests"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">TRUST REVIEW</p>
          <h1 class="hero-title">Verification flow, zero friction.</h1>
          <p class="hero-subtitle">
            Approve, reject, and track identity checks with clarity.
          </p>
          <div class="hero-actions">
            <button
              class="btn-admin-primary"
              @click="exportVerifications"
            >
              Export
            </button>
            <button
              class="btn-admin-secondary"
              @click="fetchVerifications"
            >
              Refresh List
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Pending</span>
            <span class="status-value">{{ stats.pending || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Approved</span>
            <span class="status-value">{{ stats.approved || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Rejected</span>
            <span class="status-value">{{ stats.rejected || 0 }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Total requests: {{ stats.total || 0 }}
        </div>
      </div>

      <!-- Stats Grid -->
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
            <span class="stat-value">{{ stats.pending }}</span>
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
            <span class="stat-label">Approved</span>
            <span class="stat-value">{{ stats.approved }}</span>
          </div>
        </div>

        <div class="stat-card stat-red">
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
                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Rejected</span>
            <span class="stat-value">{{ stats.rejected }}</span>
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
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Requests</span>
            <span class="stat-value">{{ stats.total }}</span>
          </div>
        </div>
      </div>

      <div class="content-card">
        <!-- P0 Verification Requests -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
              Pending Verification Requests (P0)
            </h3>
            <button
              class="btn-action"
              @click="fetchPendingRequests"
            >
              Refresh
            </button>
          </div>

          <div
            v-if="pendingLoading"
            class="loading-state"
          >
            <div class="spinner" />
            <p>Loading pending requests...</p>
          </div>

          <div
            v-else-if="pendingRequests.length"
            class="verifications-list"
          >
            <div
              v-for="request in pendingRequests"
              :key="request.id"
              class="verification-card"
            >
              <div class="verification-header">
                <div class="photographer-info">
                  <div class="photographer-avatar">
                    {{ request.user?.name?.charAt(0).toUpperCase() || 'U' }}
                  </div>
                  <div>
                    <div class="photographer-name">
                      {{ request.user?.name || 'N/A' }}
                    </div>
                    <div class="photographer-email">
                      {{ request.user?.email || 'N/A' }}
                    </div>
                  </div>
                </div>
                <span class="badge badge-warning">Pending</span>
              </div>

              <div class="verification-body">
                <div class="info-grid">
                  <div class="info-item">
                    <span class="info-label">Request Type:</span>
                    <span class="info-value">{{ capitalizeFirst(request.request_type) }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Submitted:</span>
                    <span class="info-value">{{ formatDate(request.created_at) }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Documents:</span>
                    <span class="info-value">{{ request.submitted_documents?.length || 0 }} files</span>
                  </div>
                </div>

                <div
                  v-if="request.submitted_documents?.length"
                  class="notes-section"
                >
                  <strong>Documents:</strong>
                  <div class="mt-2 space-y-1">
                    <div
                      v-for="(doc, idx) in request.submitted_documents"
                      :key="idx"
                    >
                      <a
                        :href="`/storage/${doc.path}`"
                        target="_blank"
                        rel="noopener"
                        class="text-sm text-burgundy hover:underline"
                      >
                        {{ doc.filename || 'Document' }}
                      </a>
                    </div>
                  </div>
                </div>

                <div class="verification-actions">
                  <button
                    class="btn-action btn-success"
                    @click="approveRequest(request)"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                      />
                    </svg>
                    Approve
                  </button>
                  <button
                    class="btn-action btn-danger"
                    @click="rejectRequest(request)"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                      />
                    </svg>
                    Reject
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div
            v-else
            class="empty-state"
          >
            <div class="empty-icon">
              ✅
            </div>
            <p class="empty-title">
              No pending requests
            </p>
            <p class="empty-subtitle">
              All verification requests have been reviewed.
            </p>
          </div>
        </div>

        <!-- Tabs -->
        <div class="verification-tabs">
          <button 
            :class="['tab-btn', { active: currentTab === 'pending' }]" 
            @click="currentTab = 'pending'; fetchVerifications()"
          >
            Pending ({{ stats.pending }})
          </button>
          <button 
            :class="['tab-btn', { active: currentTab === 'approved' }]" 
            @click="currentTab = 'approved'; fetchVerifications()"
          >
            Approved ({{ stats.approved }})
          </button>
          <button 
            :class="['tab-btn', { active: currentTab === 'rejected' }]" 
            @click="currentTab = 'rejected'; fetchVerifications()"
          >
            Rejected ({{ stats.rejected }})
          </button>
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading verifications...</p>
        </div>

        <!-- Verifications List -->
        <div
          v-else-if="filteredVerifications.length > 0"
          class="verifications-list"
        >
          <div
            v-for="verification in filteredVerifications"
            :key="verification.id"
            class="verification-card"
          >
            <div class="verification-header">
              <div class="photographer-info">
                <div class="photographer-avatar">
                  {{ verification.photographer?.business_name?.charAt(0).toUpperCase() || 'P' }}
                </div>
                <div>
                  <div class="photographer-name">
                    {{ verification.photographer?.business_name || 'N/A' }}
                  </div>
                  <div class="photographer-email">
                    {{ verification.photographer?.user?.email || 'N/A' }}
                  </div>
                </div>
              </div>
              <span :class="`badge badge-${getStatusColor(verification.verification_status)}`">
                {{ capitalizeFirst(verification.verification_status) }}
              </span>
            </div>

            <div class="verification-body">
              <div class="info-grid">
                <div class="info-item">
                  <span class="info-label">Business Name:</span>
                  <span class="info-value">{{ verification.photographer?.business_name || 'N/A' }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Experience:</span>
                  <span class="info-value">{{ verification.photographer?.years_experience || 0 }} years</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Submitted:</span>
                  <span class="info-value">{{ formatDate(verification.created_at) }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Documents:</span>
                  <span class="info-value">{{ verification.documents?.length || 0 }} files</span>
                </div>
              </div>

              <div
                v-if="verification.notes"
                class="notes-section"
              >
                <strong>Notes:</strong> {{ verification.notes }}
              </div>

              <div
                v-if="currentTab === 'pending'"
                class="verification-actions"
              >
                <button
                  class="btn-action btn-success"
                  @click="approveVerification(verification)"
                >
                  <svg
                    class="w-4 h-4 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  Approve
                </button>
                <button
                  class="btn-action"
                  @click="viewDetails(verification)"
                >
                  <svg
                    class="w-4 h-4 mr-2"
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
                  View Details
                </button>
                <button
                  class="btn-action btn-danger"
                  @click="rejectVerification(verification)"
                >
                  <svg
                    class="w-4 h-4 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                  Reject
                </button>
              </div>
              <div
                v-else
                class="verification-actions"
              >
                <button
                  class="btn-action"
                  @click="viewDetails(verification)"
                >
                  <svg
                    class="w-4 h-4 mr-2"
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
                  View Details
                </button>
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
            No {{ currentTab }} verifications
          </p>
          <p class="empty-subtitle">
            {{ getEmptyMessage() }}
          </p>
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
            <h3>Verification Details</h3>
            <button
              class="modal-close"
              @click="showViewModal = false"
            >
              ×
            </button>
          </div>
          <div
            v-if="selectedVerification"
            class="modal-body"
          >
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Business Name:</span>
                <span class="detail-value">{{ selectedVerification.photographer?.business_name }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ selectedVerification.photographer?.user?.email }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ selectedVerification.photographer?.phone }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Experience:</span>
                <span class="detail-value">{{ selectedVerification.photographer?.years_experience }} years</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span :class="`badge badge-${getStatusColor(selectedVerification.verification_status)}`">
                  {{ capitalizeFirst(selectedVerification.verification_status) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Submitted:</span>
                <span class="detail-value">{{ formatDate(selectedVerification.created_at) }}</span>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'

const verifications = ref([])
const loading = ref(false)
const currentTab = ref('pending')
const pendingRequests = ref([])
const pendingLoading = ref(false)
const showViewModal = ref(false)
const selectedVerification = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

// Stats from backend (not computed from client data)
const stats = ref({
  pending: 0,
  approved: 0,
  rejected: 0,
  total: 0
})

// Use all verifications from backend (already filtered by status parameter)
const filteredVerifications = computed(() => {
  return verifications.value
})

const fetchVerifications = async () => {
  loading.value = true
  try {
    // Send status parameter to get filtered results from backend
    const { data } = await api.get('/admin/verifications', {
      params: {
        status: currentTab.value
      }
    })

    if (data.status === 'success') {
      verifications.value = data.data?.verifications || []
      if (data.data?.stats) {
        stats.value = data.data.stats
      }
    }
  } catch (error) {
    console.error('Error fetching verifications:', error)
    showToastMessage('Error loading verifications')
  } finally {
    loading.value = false
  }
}

const fetchPendingRequests = async () => {
  pendingLoading.value = true
  try {
    const { data } = await api.get('/verifications/pending')
    const payload = data.data || {}
    pendingRequests.value = payload.data || payload || []
  } catch (error) {
    console.error('Error fetching pending requests:', error)
    showToastMessage('Error loading pending requests')
  } finally {
    pendingLoading.value = false
  }
}

const approveVerification = async (verification) => {
  if (!confirm(`Approve verification for ${verification.photographer?.business_name}?`)) return
  
  try {
    const { data } = await api.post(`/admin/verifications/${verification.id}/approve`)
    
    if (data.status === 'success') {
      showToastMessage(data.message || 'Verification approved successfully')
      fetchVerifications() // Refresh the list
    } else {
      showToastMessage(data.message || 'Error approving verification')
    }
  } catch (error) {
    console.error('Error approving verification:', error)
    showToastMessage('Error approving verification')
  }
}

const rejectVerification = async (verification) => {
  const reason = prompt('Enter rejection reason:')
  if (!reason) return
  
  try {
    const { data } = await api.post(`/admin/verifications/${verification.id}/reject`, {
      notes: reason
    })
    
    if (data.status === 'success') {
      showToastMessage(data.message || 'Verification rejected')
      fetchVerifications() // Refresh the list
    } else {
      showToastMessage(data.message || 'Error rejecting verification')
    }
  } catch (error) {
    console.error('Error rejecting verification:', error)
    showToastMessage('Error rejecting verification')
  }
}

const approveRequest = async (request) => {
  if (!confirm(`Approve verification request for ${request.user?.name || 'user'}?`)) return

  try {
    const { data } = await api.post(`/verifications/${request.id}/approve`)

    if (data.status === 'success') {
      showToastMessage(data.message || 'Request approved successfully')
      fetchPendingRequests()
    } else {
      showToastMessage(data.message || 'Error approving request')
    }
  } catch (error) {
    console.error('Error approving request:', error)
    showToastMessage('Error approving request')
  }
}

const rejectRequest = async (request) => {
  const reason = prompt('Enter rejection reason:')
  if (!reason) return

  try {
    const { data } = await api.post(`/verifications/${request.id}/reject`, {
      reason
    })

    if (data.status === 'success') {
      showToastMessage(data.message || 'Request rejected')
      fetchPendingRequests()
    } else {
      showToastMessage(data.message || 'Error rejecting request')
    }
  } catch (error) {
    console.error('Error rejecting request:', error)
    showToastMessage('Error rejecting request')
  }
}

const viewDetails = (verification) => {
  selectedVerification.value = verification
  showViewModal.value = true
}

const exportVerifications = () => {
  showToastMessage('Export feature coming soon')
}

const getEmptyMessage = () => {
  const messages = {
    pending: 'All verification requests have been processed',
    approved: 'No approved verifications yet',
    rejected: 'No rejected verifications yet'
  }
  return messages[currentTab.value] || 'No verifications found'
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    approved: 'success',
    rejected: 'danger'
  }
  return colors[status] || 'gray'
}

const capitalizeFirst = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
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
  fetchVerifications()
  fetchPendingRequests()
})
</script>

<style scoped>
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
.admin-verifications { padding: 2rem; min-height: 100vh; background: var(--admin-bg-page); }
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
.stat-red { border-color: var(--admin-brand-primary); }
.stat-icon { width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; }
.stat-blue .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-yellow .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-green .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-red .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-content { flex: 1; }
.stat-label { display: block; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem; }
.stat-value { display: block; font-size: 2rem; font-weight: 700; color: #1f2937; }

.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.verification-tabs { display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #e5e7eb; }
.tab-btn { background: none; border: none; padding: 1rem 1.5rem; cursor: pointer; font-weight: 600; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; }
.tab-btn:hover { color: var(--admin-brand-primary); }
.tab-btn.active { color: var(--admin-brand-primary); border-bottom-color: var(--admin-brand-primary); }

.loading-state { text-align: center; padding: 3rem; color: #6b7280; }
.spinner { width: 3rem; height: 3rem; border: 3px solid #e5e7eb; border-top-color: var(--admin-brand-primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
@keyframes spin { to { transform: rotate(360deg); } }

.verifications-list { display: grid; gap: 1.5rem; }
.verification-card { background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; transition: all 0.2s; }
.verification-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

.verification-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.photographer-info { display: flex; align-items: center; gap: 0.75rem; }
.photographer-avatar { width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--admin-brand-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1rem; }
.photographer-name { font-weight: 600; color: #1f2937; }
.photographer-email { font-size: 0.875rem; color: #6b7280; }

.verification-body { }
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
.info-item { display: flex; flex-direction: column; gap: 0.25rem; }
.info-label { font-size: 0.875rem; color: #6b7280; font-weight: 600; }
.info-value { color: #1f2937; }

.notes-section { background: #f9fafb; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; color: #1f2937; }

.verification-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.btn-action { display: flex; align-items: center; padding: 0.5rem 1rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.375rem; cursor: pointer; font-weight: 500; color: #6b7280; transition: all 0.2s; }
.btn-action:hover { background: #f9fafb; border-color: var(--admin-brand-primary); color: var(--admin-brand-primary); }
.btn-success { border-color: var(--admin-success); color: var(--admin-success); }
.btn-success:hover { background: var(--admin-success-light); border-color: var(--admin-success); color: var(--admin-success-text); }
.btn-danger { border-color: var(--admin-danger); color: var(--admin-danger); }
.btn-danger:hover { background: var(--admin-danger-light); border-color: var(--admin-danger); color: var(--admin-danger-text); }

.badge { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.badge-success { background: var(--admin-success-light); color: var(--admin-success-text); }
.badge-warning { background: var(--admin-warning-light); color: var(--admin-warning-text); }
.badge-danger { background: var(--admin-danger-light); color: var(--admin-danger-text); }
.badge-gray { background: #f3f4f6; color: #6b7280; }

.empty-state { text-align: center; padding: 4rem 2rem; color: #9ca3af; }
.empty-icon { font-size: 5rem; margin-bottom: 1rem; opacity: 0.5; }
.empty-title { font-size: 1.25rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem; }
.empty-subtitle { color: #9ca3af; }

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
