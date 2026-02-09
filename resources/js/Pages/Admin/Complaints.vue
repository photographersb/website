<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Complaints & Disputes
        </h1>
        <p class="text-gray-600 mt-2">
          Handle user complaints and resolution management
        </p>
      </div>
      
      <AdminQuickNav />

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
            <select
              v-model="selectedPriority"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Priority
              </option>
              <option value="critical">
                Critical
              </option>
              <option value="high">
                High
              </option>
              <option value="medium">
                Medium
              </option>
              <option value="low">
                Low
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="selectedStatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Status
              </option>
              <option value="open">
                Open
              </option>
              <option value="in-progress">
                In Progress
              </option>
              <option value="resolved">
                Resolved
              </option>
              <option value="closed">
                Closed
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="selectedCategory"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Categories
              </option>
              <option value="payment">
                Payment Issue
              </option>
              <option value="service">
                Service Quality
              </option>
              <option value="cancellation">
                Cancellation
              </option>
              <option value="other">
                Other
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search complaints..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Complaints
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ complaints.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Open
            </p>
            <p class="text-2xl font-bold text-red-600">
              {{ openCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              In Progress
            </p>
            <p class="text-2xl font-bold text-yellow-600">
              {{ inProgressCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Resolved
            </p>
            <p class="text-2xl font-bold text-green-600">
              {{ resolvedCount }}
            </p>
          </div>
        </div>

        <!-- Complaints List -->
        <div
          v-if="!loading"
          class="space-y-4"
        >
          <div
            v-for="complaint in filteredComplaints"
            :key="complaint.id"
            class="bg-white rounded-lg shadow-md overflow-hidden border-l-4"
            :class="getPriorityBorderClass(complaint.priority)"
          >
            <div class="p-6">
              <!-- Header -->
              <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="font-semibold text-gray-900 text-lg">
                      {{ complaint.subject }}
                    </h3>
                    <span :class="['px-2 py-1 rounded text-xs font-bold text-white', getPriorityBgClass(complaint.priority)]">
                      {{ complaint.priority }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600">
                    Complaint ID: {{ complaint.id }} • Filed by {{ complaint.filerName }}
                  </p>
                </div>
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold text-white', getStatusBgClass(complaint.status)]">
                  {{ complaint.status }}
                </span>
              </div>

              <!-- Details Grid -->
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 pb-4 border-b border-gray-200">
                <div>
                  <p class="text-xs text-gray-600">
                    Against
                  </p>
                  <p class="font-medium text-gray-900">
                    {{ complaint.targetName }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-600">
                    Category
                  </p>
                  <p class="font-medium text-gray-900 capitalize">
                    {{ complaint.category }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-600">
                    Filed Date
                  </p>
                  <p class="font-medium text-gray-900">
                    {{ formatDate(complaint.filedDate) }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-600">
                    Days Open
                  </p>
                  <p
                    class="font-medium"
                    :class="getDaysOpen(complaint.filedDate) > 14 ? 'text-red-600' : 'text-gray-900'"
                  >
                    {{ getDaysOpen(complaint.filedDate) }}
                  </p>
                </div>
              </div>

              <!-- Description -->
              <div class="mb-4">
                <p class="text-gray-700">
                  {{ complaint.description }}
                </p>
              </div>

              <!-- Actions -->
              <div class="flex flex-wrap gap-2">
                <button
                  v-if="complaint.status === 'open'"
                  class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition"
                  @click="assignComplaint(complaint.id)"
                >
                  Assign to Me
                </button>
                <button
                  v-if="complaint.status !== 'resolved'"
                  class="px-4 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition"
                  @click="updateStatus(complaint.id, 'in-progress')"
                >
                  In Progress
                </button>
                <button
                  v-if="complaint.status !== 'resolved'"
                  class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition"
                  @click="updateStatus(complaint.id, 'resolved')"
                >
                  Mark Resolved
                </button>
                <button
                  class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition"
                  @click="viewDetails(complaint.id)"
                >
                  View Full Details
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="!loading && filteredComplaints.length === 0"
          class="text-center py-12 bg-white rounded-lg"
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
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <p class="text-gray-600 text-lg">
            No complaints found
          </p>
        </div>

        <!-- Loading -->
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
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDate as formatDateValue } from '../../utils/formatters'
const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedPriority = ref('')
const selectedStatus = ref('')
const selectedCategory = ref('')
const searchQuery = ref('')

const complaints = ref([
  {
    id: 'CMP001',
    subject: 'Photographer failed to show up',
    filerName: 'Amanda Foster',
    targetName: 'Michael Zhang',
    priority: 'high',
    category: 'service',
    filedDate: '2026-01-15',
    description: 'Booked photographer for wedding. He never showed up and didn\'t respond to calls.',
    status: 'open'
  },
  {
    id: 'CMP002',
    subject: 'Incorrect charge on payment',
    filerName: 'Robert Evans',
    targetName: 'Platform',
    priority: 'critical',
    category: 'payment',
    filedDate: '2026-01-20',
    description: 'Charged twice for the same booking. Request refund immediately.',
    status: 'in-progress'
  },
  {
    id: 'CMP003',
    subject: 'Poor quality photos delivered',
    filerName: 'Victoria Martinez',
    targetName: 'Emma Davis',
    priority: 'medium',
    category: 'service',
    filedDate: '2026-01-10',
    description: 'Photos were out of focus and poorly edited. Not usable.',
    status: 'resolved'
  },
  {
    id: 'CMP004',
    subject: 'Unauthorized booking cancellation',
    filerName: 'James Wilson',
    targetName: 'Sarah Johnson',
    priority: 'high',
    category: 'cancellation',
    filedDate: '2026-01-18',
    description: 'Photographer cancelled 1 day before event with no reason.',
    status: 'open'
  },
  {
    id: 'CMP005',
    subject: 'Refund not received',
    filerName: 'Lisa Chen',
    targetName: 'Platform',
    priority: 'medium',
    category: 'payment',
    filedDate: '2026-01-12',
    description: 'Requested refund 3 weeks ago but still haven\'t received it.',
    status: 'in-progress'
  }
])

const filteredComplaints = computed(() => {
  return complaints.value.filter(complaint => {
    const priorityMatch = !selectedPriority.value || complaint.priority === selectedPriority.value
    const statusMatch = !selectedStatus.value || complaint.status === selectedStatus.value
    const categoryMatch = !selectedCategory.value || complaint.category === selectedCategory.value
    const searchMatch = !searchQuery.value || 
      complaint.subject.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      complaint.filerName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      complaint.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    return priorityMatch && statusMatch && categoryMatch && searchMatch
  })
})

const openCount = computed(() => {
  return complaints.value.filter(c => c.status === 'open').length
})

const inProgressCount = computed(() => {
  return complaints.value.filter(c => c.status === 'in-progress').length
})

const resolvedCount = computed(() => {
  return complaints.value.filter(c => c.status === 'resolved').length
})

const getPriorityBgClass = (priority) => {
  const classes = {
    critical: 'bg-red-600',
    high: 'bg-orange-600',
    medium: 'bg-yellow-600',
    low: 'bg-blue-600'
  }
  return classes[priority] || 'bg-gray-600'
}

const getPriorityBorderClass = (priority) => {
  const classes = {
    critical: 'border-red-400',
    high: 'border-orange-400',
    medium: 'border-yellow-400',
    low: 'border-blue-400'
  }
  return classes[priority] || 'border-gray-400'
}

const getStatusBgClass = (status) => {
  const classes = {
    open: 'bg-red-500',
    'in-progress': 'bg-yellow-500',
    resolved: 'bg-green-500',
    closed: 'bg-gray-500'
  }
  return classes[status] || 'bg-gray-500'
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const getDaysOpen = (filedDate) => {
  const today = new Date()
  const filed = new Date(filedDate)
  return Math.ceil((today - filed) / (1000 * 60 * 60 * 24))
}

const assignComplaint = (complaintId) => {
  const complaint = complaints.value.find(c => c.id === complaintId)
  if (complaint) {
    complaint.status = 'in-progress'
    toast.value = { show: true, message: `Complaint ${complaintId} assigned to you!`, type: 'success' }
  }
}

const updateStatus = (complaintId, newStatus) => {
  const complaint = complaints.value.find(c => c.id === complaintId)
  if (complaint) {
    complaint.status = newStatus
    toast.value = { show: true, message: `Complaint status updated to ${newStatus}!`, type: 'success' }
  }
}

const viewDetails = (complaintId) => {
  toast.value = { show: true, message: 'Loading complaint details...', type: 'info' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 800)
})
</script>
