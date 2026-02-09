<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Verification Documents
        </h1>
        <p class="text-gray-600 mt-2">
          Review and manage photographer verification documents
        </p>
      </div>
      
      <AdminQuickNav />

      <!-- Filters -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select
            v-model="selectedStatus"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">
              All Status
            </option>
            <option value="pending">
              Pending Review
            </option>
            <option value="approved">
              Approved
            </option>
            <option value="rejected">
              Rejected
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
          <select
            v-model="selectedType"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">
              All Types
            </option>
            <option value="nid">
              National ID
            </option>
            <option value="business_license">
              Business License
            </option>
            <option value="tax_certificate">
              Tax Certificate
            </option>
            <option value="studio_address">
              Studio Address
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by name or email..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
        </div>
      </div>

      <!-- Documents Grid -->
      <div
        v-if="!loading"
        class="grid grid-cols-1 lg:grid-cols-2 gap-6"
      >
        <div
          v-for="doc in filteredDocuments"
          :key="doc.id"
          class="bg-white rounded-lg shadow-md p-6 border-l-4"
          :class="getStatusBorderColor(doc.status)"
        >
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">
                {{ doc.photographerName }}
              </h3>
              <p class="text-sm text-gray-600">
                {{ doc.email }}
              </p>
            </div>
            <span :class="['px-3 py-1 rounded-full text-xs font-semibold', getStatusBadgeClass(doc.status)]">
              {{ doc.status }}
            </span>
          </div>

          <div class="space-y-3 mb-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600">Document Type:</span>
              <span class="font-medium">{{ getDocumentTypeLabel(doc.type) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600">Submitted:</span>
              <span class="text-sm">{{ formatDate(doc.submittedAt) }}</span>
            </div>
            <div
              v-if="doc.status === 'pending'"
              class="flex items-center justify-between"
            >
              <span class="text-sm text-gray-600">Days Pending:</span>
              <span class="font-medium text-orange-600">{{ doc.daysPending }}</span>
            </div>
          </div>

          <!-- Document Preview -->
          <div class="mb-4 bg-gray-100 rounded-lg p-4">
            <div class="flex items-center justify-center h-32 bg-gray-200 rounded">
              <div class="text-center">
                <svg
                  class="w-12 h-12 mx-auto text-gray-400 mb-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
                <p class="text-sm text-gray-600">
                  {{ doc.documentFile }}
                </p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2">
            <button
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
              @click="viewDocument(doc.id)"
            >
              View
            </button>
            <button
              v-if="doc.status === 'pending'"
              class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
              @click="approveDocument(doc.id)"
            >
              Approve
            </button>
            <button
              v-if="doc.status === 'pending'"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
              @click="rejectDocument(doc.id)"
            >
              Reject
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="!loading && filteredDocuments.length === 0"
        class="text-center py-12"
      >
        <svg
          class="w-16 h-16 text-gray-400 mx-auto mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          />
        </svg>
        <p class="text-gray-600 text-lg">
          No documents found
        </p>
      </div>

      <!-- Pagination -->
      <div
        v-if="!loading && pagination.total > 0"
        class="mt-6 bg-white rounded-lg shadow px-6 py-4 flex items-center justify-between"
      >
        <div class="text-sm text-gray-600">
          Showing {{ (pagination.page - 1) * pagination.per_page + 1 }} to
          {{ Math.min(pagination.page * pagination.per_page, pagination.total) }} of
          {{ pagination.total }} documents
        </div>
        <div class="flex items-center gap-2">
          <button
            :disabled="pagination.page <= 1"
            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="changePage(pagination.page - 1)"
          >
            Previous
          </button>
          <span class="text-sm text-gray-600">Page {{ pagination.page }} of {{ pagination.total_pages }}</span>
          <button
            :disabled="pagination.page >= pagination.total_pages"
            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="changePage(pagination.page + 1)"
          >
            Next
          </button>
        </div>
      </div>

      <!-- Loading Spinner -->
      <div
        v-if="loading"
        class="flex justify-center py-12"
      >
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
      </div>
    </div>
    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '../../api'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDate as formatDateValue } from '../../utils/formatters'

const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedStatus = ref('')
const selectedType = ref('')
const searchQuery = ref('')

const documents = ref([])
const pagination = ref({
  page: 1,
  per_page: 15,
  total: 0,
  total_pages: 1
})

const filteredDocuments = computed(() => documents.value)

const normalizeStatus = (status) => (status === 'submitted' ? 'pending' : status)

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return classes[normalizeStatus(status)] || 'bg-gray-100 text-gray-800'
}

const getStatusBorderColor = (status) => {
  const classes = {
    pending: 'border-yellow-400',
    approved: 'border-green-400',
    rejected: 'border-red-400'
  }
  return classes[normalizeStatus(status)] || 'border-gray-400'
}

const getDocumentTypeLabel = (type) => {
  const labels = {
    nid: 'National ID',
    business_license: 'Business License',
    tax_certificate: 'Tax Certificate',
    studio_address: 'Studio Address',
    phone: 'Phone Verification',
    business: 'Business Verification'
  }
  return labels[type] || type
}

const formatDate = (date) => formatDateValue(date)

const getDocumentInfo = (request) => {
  const files = Array.isArray(request.submitted_documents) ? request.submitted_documents : []
  if (files.length > 0) {
    const file = files[0]
    const filename = file.filename || (file.path ? file.path.split('/').pop() : 'document')
    const url = file.path
      ? (file.path.startsWith('http') ? file.path : `/storage/${file.path}`)
      : null
    return { label: filename, url }
  }

  const path = request.document_front_path || request.document_back_path || request.selfie_path
  if (path) {
    const filename = path.split('/').pop()
    const url = path.startsWith('http') || path.startsWith('/storage') ? path : `/storage/${path}`
    return { label: filename, url }
  }

  return { label: 'Document', url: null }
}

const mapRequestToDoc = (request) => {
  const { label, url } = getDocumentInfo(request)
  const status = normalizeStatus(request.status)
  const createdAt = request.created_at || request.submitted_at
  const daysPending = status === 'pending' && createdAt
    ? Math.max(0, Math.floor((Date.now() - new Date(createdAt)) / 86400000))
    : 0

  return {
    id: request.id,
    photographerName: request.user?.name || request.full_name || 'Unknown',
    email: request.user?.email || '',
    status,
    type: request.request_type || request.type || 'unknown',
    submittedAt: createdAt,
    daysPending,
    documentFile: label,
    documentUrl: url
  }
}

const fetchDocuments = async () => {
  loading.value = true
  try {
    const params = {
      status: selectedStatus.value || undefined,
      type: selectedType.value || undefined,
      search: searchQuery.value || undefined,
      page: pagination.value.page,
      per_page: pagination.value.per_page
    }
    const { data } = await api.get('/verifications/pending', { params })
    const items = Array.isArray(data.data) ? data.data : []
    documents.value = items.map(mapRequestToDoc)
    if (data.pagination) {
      pagination.value = {
        ...pagination.value,
        page: data.pagination.current_page,
        per_page: data.pagination.per_page,
        total: data.pagination.total,
        total_pages: data.pagination.last_page
      }
    }
  } catch (error) {
    console.error('Failed to fetch verification requests:', error)
    toast.value = { show: true, message: 'Failed to load verification documents', type: 'error' }
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  pagination.value.page = 1
  fetchDocuments()
}

const changePage = (page) => {
  pagination.value.page = page
  fetchDocuments()
}

const viewDocument = async (documentId) => {
  const doc = documents.value.find((item) => item.id === documentId)
  if (doc?.documentUrl) {
    window.open(doc.documentUrl, '_blank')
    return
  }
  toast.value = { show: true, message: 'No document file available.', type: 'info' }
}

const approveDocument = async (documentId) => {
  const notes = window.prompt('Approval notes (optional):')
  try {
    await api.post(`/verifications/${documentId}/approve`, notes ? { notes } : {})
    toast.value = { show: true, message: 'Document approved', type: 'success' }
    fetchDocuments()
  } catch (error) {
    toast.value = { show: true, message: error.response?.data?.message || 'Approval failed', type: 'error' }
  }
}

const rejectDocument = async (documentId) => {
  const reason = window.prompt('Reason for rejection:')
  if (!reason) return
  try {
    await api.post(`/verifications/${documentId}/reject`, { reason })
    toast.value = { show: true, message: 'Document rejected', type: 'success' }
    fetchDocuments()
  } catch (error) {
    toast.value = { show: true, message: error.response?.data?.message || 'Rejection failed', type: 'error' }
  }
}

let searchTimeout = null
watch([selectedStatus, selectedType], applyFilters)
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 400)
})

onMounted(() => {
  fetchDocuments()
})
</script>
