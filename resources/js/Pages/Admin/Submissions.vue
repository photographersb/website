<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">
            Competition Submissions
          </h1>
          <p class="text-gray-600 mt-2">
            Review and moderate competition submissions
          </p>
        </div>
        
        <AdminQuickNav />

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="selectedStatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
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
              <option value="disqualified">
                Disqualified
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Competition</label>
            <select
              v-model="selectedCompetition"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
              <option value="">
                All Competitions
              </option>
              <option
                v-for="competition in competitionOptions"
                :key="competition.id"
                :value="competition.id"
              >
                {{ competition.title }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Score Range</label>
            <select
              v-model="selectedScore"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
              <option value="">
                All Scores
              </option>
              <option value="high">
                High (8-10)
              </option>
              <option value="medium">
                Medium (5-7)
              </option>
              <option value="low">
                Low (0-4)
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search submissions..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
          </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Pending Review
            </p>
            <p class="text-2xl font-bold text-yellow-600">
              {{ stats.pending }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Approved
            </p>
            <p class="text-2xl font-bold text-green-600">
              {{ stats.approved }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Rejected
            </p>
            <p class="text-2xl font-bold text-red-600">
              {{ stats.rejected }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Disqualified
            </p>
            <p class="text-2xl font-bold text-gray-600">
              {{ stats.disqualified || 0 }}
            </p>
          </div>
        </div>

        <!-- Submissions Grid -->
        <div
          v-if="!loading"
          class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        >
          <div
            v-for="submission in filteredSubmissions"
            :key="submission.id"
            class="bg-white rounded-lg shadow-md overflow-hidden border-t-4"
            :class="getStatusBorderClass(submission.status)"
          >
            <!-- Image Preview -->
            <div class="relative h-48 bg-gray-200 overflow-hidden group">
              <div class="absolute inset-0 bg-gray-300 flex items-center justify-center">
                <svg
                  class="w-16 h-16 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 12m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </div>
              <div class="absolute top-3 right-3">
                <span :class="['px-3 py-1 rounded-full text-xs font-bold text-white', getStatusBgClass(submission.status)]">
                  {{ formatStatus(submission.status) }}
                </span>
              </div>
            </div>

            <div class="p-6">
              <!-- Submission Info -->
              <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                  {{ submission.title }}
                </h3>
                <p class="text-sm text-gray-600">
                  by <span class="font-medium">{{ submission.photographer?.name || 'Unknown' }}</span>
                </p>
              </div>

              <!-- Competition & Date -->
              <div class="space-y-2 mb-4 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-600">Competition:</span>
                  <span class="font-medium">{{ submission.competition?.title || 'Unknown' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Submitted:</span>
                  <span class="text-gray-900">{{ formatDate(submission.submitted_at || submission.created_at) }}</span>
                </div>
              </div>

              <!-- Scoring -->
              <div
                v-if="getScoreValue(submission) !== null"
                class="mb-4"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-medium text-gray-700">Score:</span>
                  <span class="text-2xl font-bold text-blue-600">{{ getScoreValue(submission) }}/10</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    class="bg-blue-600 h-2 rounded-full"
                    :style="{ width: getScorePercent(submission) + '%' }"
                  />
                </div>
              </div>

              <!-- Feedback -->
              <div
                v-if="submission.rejection_reason"
                class="mb-4 p-3 bg-gray-50 rounded-lg"
              >
                <p class="text-sm text-gray-700">
                  {{ submission.rejection_reason }}
                </p>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-2">
                <button
                  class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition"
                  @click="reviewSubmission(submission.id)"
                >
                  Review
                </button>
                <button
                  v-if="isPendingStatus(submission.status)"
                  class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition"
                  @click="approveSubmission(submission.id)"
                >
                  Approve
                </button>
                <button
                  v-if="isPendingStatus(submission.status)"
                  class="flex-1 px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition"
                  @click="rejectSubmission(submission.id)"
                >
                  Reject
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="!loading && filteredSubmissions.length === 0"
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
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 12m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
          <p class="text-gray-600 text-lg">
            No submissions found
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
            {{ pagination.total }} submissions
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
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../api'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDate as formatDateValue } from '../../utils/formatters'

const router = useRouter()
const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedStatus = ref('')
const selectedCompetition = ref('')
const selectedScore = ref('')
const searchQuery = ref('')

const submissions = ref([])
const stats = ref({ pending: 0, approved: 0, rejected: 0, disqualified: 0 })
const pagination = ref({
  page: 1,
  per_page: 20,
  total: 0,
  total_pages: 1
})

const competitionOptions = computed(() => {
  const map = new Map()
  submissions.value.forEach((submission) => {
    const competition = submission.competition
    if (competition?.id && !map.has(competition.id)) {
      map.set(competition.id, { id: competition.id, title: competition.title })
    }
  })
  return Array.from(map.values()).sort((a, b) => a.title.localeCompare(b.title))
})

const normalizeStatus = (status) => {
  if (!status) return ''
  if (status === 'pending') return 'pending'
  return status
}

const getScoreValue = (submission) => {
  const raw = submission.final_score ?? submission.judge_score
  const score = Number(raw)
  return Number.isFinite(score) ? score : null
}

const getScorePercent = (submission) => {
  const score = getScoreValue(submission)
  return score === null ? 0 : Math.min(100, Math.max(0, score * 10))
}

const formatStatus = (status) => {
  const labels = {
    pending_review: 'Pending Review',
    payment_pending: 'Payment Pending',
    pending: 'Pending Review',
    approved: 'Approved',
    rejected: 'Rejected',
    disqualified: 'Disqualified'
  }
  return labels[status] || status
}

const isPendingStatus = (status) => ['pending', 'pending_review', 'payment_pending'].includes(status)

const filteredSubmissions = computed(() => submissions.value)

const getStatusBgClass = (status) => {
  const classes = {
    pending_review: 'bg-yellow-500',
    pending: 'bg-yellow-500',
    payment_pending: 'bg-yellow-500',
    approved: 'bg-green-500',
    rejected: 'bg-red-500',
    disqualified: 'bg-gray-600'
  }
  return classes[status] || 'bg-gray-500'
}

const getStatusBorderClass = (status) => {
  const classes = {
    pending_review: 'border-yellow-400',
    pending: 'border-yellow-400',
    payment_pending: 'border-yellow-400',
    approved: 'border-green-400',
    rejected: 'border-red-400',
    disqualified: 'border-gray-400'
  }
  return classes[status] || 'border-gray-400'
}

const formatDate = (date) => formatDateValue(date)

const fetchStats = async () => {
  try {
    const { data } = await api.get('/admin/submissions/stats')
    stats.value = data.data || stats.value
  } catch (error) {
    console.error('Failed to load submission stats:', error)
  }
}

const fetchSubmissions = async () => {
  loading.value = true
  try {
    const params = {
      status: selectedStatus.value || undefined,
      competition_id: selectedCompetition.value || undefined,
      score_range: selectedScore.value || undefined,
      search: searchQuery.value || undefined,
      page: pagination.value.page,
      per_page: pagination.value.per_page
    }
    const { data } = await api.get('/admin/submissions', { params })
    submissions.value = Array.isArray(data.data) ? data.data : []
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
    console.error('Failed to load submissions:', error)
    toast.value = { show: true, message: 'Failed to load submissions.', type: 'error' }
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  pagination.value.page = 1
  fetchSubmissions()
}

const changePage = (page) => {
  pagination.value.page = page
  fetchSubmissions()
}

const reviewSubmission = (submissionId) => {
  const submission = submissions.value.find((item) => item.id === submissionId)
  const competitionId = submission?.competition?.id || submission?.competition_id
  if (!competitionId) {
    toast.value = { show: true, message: 'Competition not found for this submission.', type: 'error' }
    return
  }
  router.push(`/admin/competitions/${competitionId}/submissions`)
}

const approveSubmission = async (submissionId) => {
  const submission = submissions.value.find((item) => item.id === submissionId)
  const competitionId = submission?.competition?.id || submission?.competition_id
  if (!competitionId) return
  try {
    await api.post(`/admin/competitions/${competitionId}/submissions/${submissionId}/approve`)
    toast.value = { show: true, message: `${submission.title} approved.`, type: 'success' }
    await fetchStats()
    await fetchSubmissions()
  } catch (error) {
    toast.value = { show: true, message: error.response?.data?.message || 'Approval failed.', type: 'error' }
  }
}

const rejectSubmission = async (submissionId) => {
  const submission = submissions.value.find((item) => item.id === submissionId)
  const competitionId = submission?.competition?.id || submission?.competition_id
  if (!competitionId) return
  const reason = window.prompt('Provide a rejection reason:')
  if (!reason) return
  try {
    await api.post(`/admin/competitions/${competitionId}/submissions/${submissionId}/reject`, { reason })
    toast.value = { show: true, message: `${submission.title} rejected.`, type: 'error' }
    await fetchStats()
    await fetchSubmissions()
  } catch (error) {
    toast.value = { show: true, message: error.response?.data?.message || 'Rejection failed.', type: 'error' }
  }
}

onMounted(async () => {
  await fetchStats()
  await fetchSubmissions()
})

let searchTimeout = null
watch([selectedStatus, selectedCompetition, selectedScore], applyFilters)
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 400)
})
</script>
