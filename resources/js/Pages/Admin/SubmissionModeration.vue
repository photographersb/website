<template>
  <div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Submission Moderation</h1>
            <p class="mt-1 text-sm text-gray-600">Review and moderate competition submissions</p>
          </div>
          <router-link 
            to="/admin/competitions" 
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium"
          >
            Back to Competitions
          </router-link>
        </div>

        <!-- Competition Info -->
        <div v-if="competition" class="bg-white rounded-lg shadow-md p-4">
          <h2 class="font-bold text-xl text-gray-900">{{ competition.title }}</h2>
          <p class="text-sm text-gray-600">{{ competition.description }}</p>
        </div>
      </div>

      <!-- Statistics -->
      <div v-if="stats" class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-600">Total</p>
          <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4 border-2 border-yellow-200">
          <p class="text-sm text-yellow-700">Pending</p>
          <p class="text-2xl font-bold text-yellow-800">{{ stats.pending }}</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4">
          <p class="text-sm text-green-700">Approved</p>
          <p class="text-2xl font-bold text-green-800">{{ stats.approved }}</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-4">
          <p class="text-sm text-red-700">Rejected</p>
          <p class="text-2xl font-bold text-red-800">{{ stats.rejected }}</p>
        </div>
        <div class="bg-gray-50 rounded-lg shadow p-4">
          <p class="text-sm text-gray-700">Disqualified</p>
          <p class="text-2xl font-bold text-gray-800">{{ stats.disqualified }}</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Status Filter -->
          <div class="flex-1">
            <select 
              v-model="statusFilter"
              @change="fetchSubmissions"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
              <option value="">All Status</option>
              <option value="pending">Pending Review</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="disqualified">Disqualified</option>
            </select>
          </div>

          <!-- Search -->
          <div class="flex-1">
            <input 
              v-model="searchQuery"
              @input="debouncedSearch"
              type="text" 
              placeholder="Search by title or photographer..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
        <p class="mt-4 text-gray-600">Loading submissions...</p>
      </div>

      <!-- Submissions List -->
      <div v-else-if="submissions.length > 0" class="space-y-4">
        <div 
          v-for="submission in submissions" 
          :key="submission.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
        >
          <div class="flex flex-col md:flex-row">
            <!-- Image -->
            <div class="md:w-64 h-64 md:h-auto bg-gray-200 flex-shrink-0">
              <img 
                :src="submission.thumbnail_url || submission.image_url" 
                :alt="submission.title"
                class="w-full h-full object-cover cursor-pointer hover:opacity-75 transition-opacity"
                @click="viewFullImage(submission)"
              />
            </div>

            <!-- Content -->
            <div class="flex-1 p-6">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <h3 class="text-xl font-bold text-gray-900 mb-2">{{ submission.title }}</h3>
                  <p class="text-sm text-gray-600 mb-2">
                    by <span class="font-medium">{{ submission.photographer?.name || 'Unknown' }}</span>
                  </p>
                  <p v-if="submission.description" class="text-gray-700 mb-3">{{ submission.description }}</p>
                  
                  <!-- Metadata -->
                  <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                    <span v-if="submission.location">📍 {{ submission.location }}</span>
                    <span v-if="submission.camera_make">📷 {{ submission.camera_make }} {{ submission.camera_model }}</span>
                    <span>👁 {{ submission.view_count }} views</span>
                    <span>♥ {{ submission.vote_count }} votes</span>
                  </div>

                  <!-- Rejection Reason -->
                  <div v-if="submission.rejection_reason" class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                    <p class="text-sm font-medium text-red-800">Rejection Reason:</p>
                    <p class="text-sm text-red-700">{{ submission.rejection_reason }}</p>
                  </div>

                  <!-- Submitted Date -->
                  <p class="text-xs text-gray-500">
                    Submitted {{ formatDate(submission.created_at) }}
                  </p>
                </div>

                <!-- Status Badge -->
                <span :class="getStatusClass(submission.status)" class="px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap ml-4">
                  {{ formatStatus(submission.status) }}
                </span>
              </div>

              <!-- Actions -->
              <div class="flex flex-wrap gap-2">
                <button 
                  v-if="submission.status === 'pending_review'"
                  @click="approveSubmission(submission)"
                  :disabled="processing"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm"
                >
                  ✓ Approve
                </button>
                
                <button 
                  v-if="submission.status === 'pending_review'"
                  @click="openRejectModal(submission)"
                  :disabled="processing"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm"
                >
                  ✗ Reject
                </button>

                <button 
                  v-if="submission.status === 'approved'"
                  @click="openDisqualifyModal(submission)"
                  :disabled="processing"
                  class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm"
                >
                  Disqualify
                </button>

                <button 
                  @click="viewFullImage(submission)"
                  class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm"
                >
                  View Full Image
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-white rounded-lg shadow">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No submissions found</h3>
        <p class="text-gray-600">There are no submissions matching your filters.</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-6 flex justify-center">
        <div class="flex gap-2">
          <button 
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          
          <div class="flex gap-1">
            <button 
              v-for="page in visiblePages" 
              :key="page"
              @click="changePage(page)"
              :class="[
                'px-4 py-2 border rounded-lg',
                page === pagination.current_page 
                  ? 'bg-red-600 text-white border-red-600' 
                  : 'border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </div>

          <button 
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Submission</h3>
        <p class="text-gray-600 mb-4">Please provide a reason for rejecting this submission:</p>
        
        <textarea 
          v-model="rejectReason"
          rows="4"
          placeholder="Enter rejection reason..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent mb-4"
        ></textarea>
        
        <div class="flex gap-3 justify-end">
          <button 
            @click="closeRejectModal"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancel
          </button>
          <button 
            @click="confirmReject"
            :disabled="!rejectReason || processing"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Confirm Reject
          </button>
        </div>
      </div>
    </div>

    <!-- Disqualify Modal -->
    <div v-if="showDisqualifyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Disqualify Submission</h3>
        <p class="text-gray-600 mb-4">Please provide a reason for disqualifying this submission:</p>
        
        <textarea 
          v-model="disqualifyReason"
          rows="4"
          placeholder="Enter disqualification reason..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent mb-4"
        ></textarea>
        
        <div class="flex gap-3 justify-end">
          <button 
            @click="closeDisqualifyModal"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancel
          </button>
          <button 
            @click="confirmDisqualify"
            :disabled="!disqualifyReason || processing"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Confirm Disqualify
          </button>
        </div>
      </div>
    </div>

    <!-- Full Image Modal -->
    <div v-if="showImageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4" @click="closeImageModal">
      <div class="max-w-6xl max-h-full">
        <img 
          :src="selectedSubmission?.image_url" 
          :alt="selectedSubmission?.title"
          class="max-w-full max-h-[90vh] object-contain"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';

const route = useRoute();

const competition = ref(null);
const submissions = ref([]);
const stats = ref(null);
const loading = ref(true);
const processing = ref(false);
const statusFilter = ref('');
const searchQuery = ref('');
const pagination = ref(null);

const showRejectModal = ref(false);
const showDisqualifyModal = ref(false);
const showImageModal = ref(false);
const selectedSubmission = ref(null);
const rejectReason = ref('');
const disqualifyReason = ref('');

let searchTimeout = null;

onMounted(() => {
  fetchCompetition();
  fetchStats();
  fetchSubmissions();
});

const fetchCompetition = async () => {
  // Skip fetching competition if no ID is provided (viewing all submissions)
  if (!route.params.id) {
    return;
  }
  
  try {
    const { data } = await api.get(`/competitions/${route.params.id}`);
    competition.value = data.data;
  } catch (error) {
    console.error('Error fetching competition:', error);
    alert('Failed to load competition details');
  }
};

const fetchStats = async () => {
  try {
    // Use different endpoint based on whether viewing specific competition or all submissions
    const endpoint = route.params.id 
      ? `/admin/competitions/${route.params.id}/submissions/stats`
      : `/admin/submissions/stats`;
    const { data } = await api.get(endpoint);
    stats.value = data.data;
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const fetchSubmissions = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
      status: statusFilter.value || undefined,
      search: searchQuery.value || undefined
    };

    // Use different endpoint based on whether viewing specific competition or all submissions
    const endpoint = route.params.id 
      ? `/admin/competitions/${route.params.id}/submissions`
      : `/admin/submissions`;
    const { data } = await api.get(endpoint, { params });
    
    submissions.value = data.data.data || data.data;
    pagination.value = {
      current_page: data.data.current_page,
      last_page: data.data.last_page,
      per_page: data.data.per_page,
      total: data.data.total
    };
  } catch (error) {
    console.error('Error fetching submissions:', error);
    alert('Failed to load submissions');
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchSubmissions();
  }, 500);
};

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchSubmissions(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const visiblePages = computed(() => {
  if (!pagination.value) return [];
  
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const delta = 2;
  const range = [];
  
  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i);
  }
  
  range.unshift(1);
  if (last > 1) range.push(last);
  
  return range.filter((v, i, a) => a.indexOf(v) === i);
});

const approveSubmission = async (submission) => {
  if (!confirm(`Approve "${submission.title}"?`)) return;
  
  processing.value = true;
  try {
    await api.post(`/admin/competitions/${route.params.id}/submissions/${submission.id}/approve`);
    alert('Submission approved successfully!');
    await fetchStats();
    await fetchSubmissions(pagination.value.current_page);
  } catch (error) {
    console.error('Error approving submission:', error);
    alert(error.response?.data?.message || 'Failed to approve submission');
  } finally {
    processing.value = false;
  }
};

const openRejectModal = (submission) => {
  selectedSubmission.value = submission;
  rejectReason.value = '';
  showRejectModal.value = true;
};

const closeRejectModal = () => {
  showRejectModal.value = false;
  selectedSubmission.value = null;
  rejectReason.value = '';
};

const confirmReject = async () => {
  processing.value = true;
  try {
    await api.post(
      `/admin/competitions/${route.params.id}/submissions/${selectedSubmission.value.id}/reject`,
      { reason: rejectReason.value }
    );
    alert('Submission rejected successfully!');
    closeRejectModal();
    await fetchStats();
    await fetchSubmissions(pagination.value.current_page);
  } catch (error) {
    console.error('Error rejecting submission:', error);
    alert(error.response?.data?.message || 'Failed to reject submission');
  } finally {
    processing.value = false;
  }
};

const openDisqualifyModal = (submission) => {
  selectedSubmission.value = submission;
  disqualifyReason.value = '';
  showDisqualifyModal.value = true;
};

const closeDisqualifyModal = () => {
  showDisqualifyModal.value = false;
  selectedSubmission.value = null;
  disqualifyReason.value = '';
};

const confirmDisqualify = async () => {
  processing.value = true;
  try {
    await api.post(
      `/admin/competitions/${route.params.id}/submissions/${selectedSubmission.value.id}/disqualify`,
      { reason: disqualifyReason.value }
    );
    alert('Submission disqualified successfully!');
    closeDisqualifyModal();
    await fetchStats();
    await fetchSubmissions(pagination.value.current_page);
  } catch (error) {
    console.error('Error disqualifying submission:', error);
    alert(error.response?.data?.message || 'Failed to disqualify submission');
  } finally {
    processing.value = false;
  }
};

const viewFullImage = (submission) => {
  selectedSubmission.value = submission;
  showImageModal.value = true;
};

const closeImageModal = () => {
  showImageModal.value = false;
};

const getStatusClass = (status) => {
  const classes = {
    pending_review: 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    approved: 'bg-green-100 text-green-800 border border-green-300',
    rejected: 'bg-red-100 text-red-800 border border-red-300',
    disqualified: 'bg-gray-100 text-gray-800 border border-gray-300'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatStatus = (status) => {
  const formats = {
    pending_review: 'Pending Review',
    approved: 'Approved',
    rejected: 'Rejected',
    disqualified: 'Disqualified'
  };
  return formats[status] || status;
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now - date);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) return 'today';
  if (diffDays === 1) return 'yesterday';
  if (diffDays < 7) return `${diffDays} days ago`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
  
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>
