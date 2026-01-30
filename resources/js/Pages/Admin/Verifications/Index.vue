<template>
  <div class="admin-verifications">
    <div class="page-header">
      <div>
        <h1 class="page-title">✅ Verification Management</h1>
        <p class="page-subtitle">Review and approve photographer verification requests</p>
      </div>
      <button @click="exportVerifications" class="btn-export-main">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Export
      </button>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
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
          <span class="stat-label">Approved</span>
          <span class="stat-value">{{ stats.approved }}</span>
        </div>
      </div>

      <div class="stat-card stat-red">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Rejected</span>
          <span class="stat-value">{{ stats.rejected }}</span>
        </div>
      </div>

      <div class="stat-card stat-blue">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Requests</span>
          <span class="stat-value">{{ stats.total }}</span>
        </div>
      </div>
    </div>

    <div class="content-card">
      <!-- Tabs -->
      <div class="verification-tabs">
        <button 
          @click="currentTab = 'pending'" 
          :class="['tab-btn', { active: currentTab === 'pending' }]">
          Pending ({{ stats.pending }})
        </button>
        <button 
          @click="currentTab = 'approved'" 
          :class="['tab-btn', { active: currentTab === 'approved' }]">
          Approved ({{ stats.approved }})
        </button>
        <button 
          @click="currentTab = 'rejected'" 
          :class="['tab-btn', { active: currentTab === 'rejected' }]">
          Rejected ({{ stats.rejected }})
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading verifications...</p>
      </div>

      <!-- Verifications List -->
      <div v-else-if="filteredVerifications.length > 0" class="verifications-list">
        <div v-for="verification in filteredVerifications" :key="verification.id" class="verification-card">
          <div class="verification-header">
            <div class="photographer-info">
              <div class="photographer-avatar">{{ verification.photographer?.business_name?.charAt(0).toUpperCase() || 'P' }}</div>
              <div>
                <div class="photographer-name">{{ verification.photographer?.business_name || 'N/A' }}</div>
                <div class="photographer-email">{{ verification.photographer?.user?.email || 'N/A' }}</div>
              </div>
            </div>
            <span :class="`badge badge-${getStatusColor(verification.status)}`">
              {{ capitalizeFirst(verification.status) }}
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

            <div v-if="verification.notes" class="notes-section">
              <strong>Notes:</strong> {{ verification.notes }}
            </div>

            <div v-if="currentTab === 'pending'" class="verification-actions">
              <button @click="approveVerification(verification)" class="btn-action btn-success">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve
              </button>
              <button @click="viewDetails(verification)" class="btn-action">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Details
              </button>
              <button @click="rejectVerification(verification)" class="btn-action btn-danger">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject
              </button>
            </div>
            <div v-else class="verification-actions">
              <button @click="viewDetails(verification)" class="btn-action">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">📋</div>
        <p class="empty-title">No {{ currentTab }} verifications</p>
        <p class="empty-subtitle">{{ getEmptyMessage() }}</p>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal" class="modal-overlay" @click.self="showViewModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Verification Details</h3>
          <button @click="showViewModal = false" class="modal-close">×</button>
        </div>
        <div class="modal-body" v-if="selectedVerification">
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
              <span :class="`badge badge-${getStatusColor(selectedVerification.status)}`">
                {{ capitalizeFirst(selectedVerification.status) }}
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
    <div v-if="showToast" class="toast">{{ toastMessage }}</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const verifications = ref([])
const loading = ref(false)
const currentTab = ref('pending')
const showViewModal = ref(false)
const selectedVerification = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

const stats = computed(() => ({
  pending: verifications.value.filter(v => v.status === 'pending').length,
  approved: verifications.value.filter(v => v.status === 'approved').length,
  rejected: verifications.value.filter(v => v.status === 'rejected').length,
  total: verifications.value.length
}))

const filteredVerifications = computed(() => {
  return verifications.value.filter(v => v.status === currentTab.value)
})

const fetchVerifications = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('/api/v1/admin/verifications', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      verifications.value = data.data || []
    }
  } catch (error) {
    console.error('Error fetching verifications:', error)
    showToastMessage('Error loading verifications')
  } finally {
    loading.value = false
  }
}

const approveVerification = (verification) => {
  if (confirm(`Approve verification for ${verification.photographer?.business_name}?`)) {
    verification.status = 'approved'
    showToastMessage('Verification approved successfully')
  }
}

const rejectVerification = (verification) => {
  const reason = prompt('Enter rejection reason:')
  if (reason) {
    verification.status = 'rejected'
    verification.rejection_reason = reason
    showToastMessage('Verification rejected')
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
  fetchVerifications()
})
</script>

<style scoped>
.admin-verifications { padding: 2rem; min-height: 100vh; background: #f9fafb; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-title { font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0; }
.page-subtitle { color: #6b7280; margin: 0.5rem 0 0 0; }

.btn-export-main { display: flex; align-items: center; background: #6c0b1a; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600; transition: background 0.2s; }
.btn-export-main:hover { background: #4a070f; }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 1rem; border-left: 4px solid; }
.stat-blue { border-color: #3b82f6; }
.stat-yellow { border-color: #f59e0b; }
.stat-green { border-color: #10b981; }
.stat-red { border-color: #ef4444; }
.stat-icon { width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; }
.stat-blue .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-yellow .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stat-green .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-red .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.stat-content { flex: 1; }
.stat-label { display: block; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem; }
.stat-value { display: block; font-size: 2rem; font-weight: 700; color: #1f2937; }

.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.verification-tabs { display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #e5e7eb; }
.tab-btn { background: none; border: none; padding: 1rem 1.5rem; cursor: pointer; font-weight: 600; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; }
.tab-btn:hover { color: #6c0b1a; }
.tab-btn.active { color: #6c0b1a; border-bottom-color: #6c0b1a; }

.loading-state { text-align: center; padding: 3rem; color: #6b7280; }
.spinner { width: 3rem; height: 3rem; border: 3px solid #e5e7eb; border-top-color: #6c0b1a; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
@keyframes spin { to { transform: rotate(360deg); } }

.verifications-list { display: grid; gap: 1.5rem; }
.verification-card { background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; transition: all 0.2s; }
.verification-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

.verification-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.photographer-info { display: flex; align-items: center; gap: 0.75rem; }
.photographer-avatar { width: 2.5rem; height: 2.5rem; border-radius: 50%; background: linear-gradient(135deg, #6c0b1a, #9d1429); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1rem; }
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
.btn-action:hover { background: #f9fafb; border-color: #6c0b1a; color: #6c0b1a; }
.btn-success { border-color: #10b981; color: #10b981; }
.btn-success:hover { background: #d1fae5; border-color: #10b981; color: #065f46; }
.btn-danger { border-color: #ef4444; color: #ef4444; }
.btn-danger:hover { background: #fee2e2; border-color: #ef4444; color: #991b1b; }

.badge { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-danger { background: #fee2e2; color: #991b1b; }
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
