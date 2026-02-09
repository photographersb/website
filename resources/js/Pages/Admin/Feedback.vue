<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="mb-8 flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Reviews & Feedback
            </h1>
            <p class="text-gray-600 mt-2">
              Manage user reviews and feedback submissions
            </p>
          </div>
          <div class="flex gap-2">
            <select
              v-model="sortBy"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm"
            >
              <option value="recent">
                Most Recent
              </option>
              <option value="rating">
                Highest Rated
              </option>
              <option value="oldest">
                Oldest First
              </option>
            </select>
          </div>
        </div>
        
        <AdminQuickNav />

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
            <select
              v-model="selectedRating"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Ratings
              </option>
              <option value="5">
                5 Stars
              </option>
              <option value="4">
                4 Stars
              </option>
              <option value="3">
                3 Stars
              </option>
              <option value="2">
                2 Stars
              </option>
              <option value="1">
                1 Star
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
            <select
              v-model="selectedType"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Types
              </option>
              <option value="photographer">
                Photographer
              </option>
              <option value="booking">
                Booking
              </option>
              <option value="event">
                Event
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
              <option value="published">
                Published
              </option>
              <option value="pending">
                Pending Review
              </option>
              <option value="flagged">
                Flagged
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search reviews..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Reviews
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ reviews.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Average Rating
            </p>
            <p class="text-2xl font-bold text-yellow-600">
              {{ formatFixed(avgRating, 1, '0.0') }} ⭐
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Pending Review
            </p>
            <p class="text-2xl font-bold text-orange-600">
              {{ pendingCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Flagged
            </p>
            <p class="text-2xl font-bold text-red-600">
              {{ flaggedCount }}
            </p>
          </div>
        </div>

        <!-- Reviews List -->
        <div
          v-if="!loading"
          class="space-y-4"
        >
          <div
            v-for="review in filteredReviews"
            :key="review.id"
            class="bg-white rounded-lg shadow-md p-6"
          >
            <div class="flex justify-between items-start mb-4">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <h3 class="font-semibold text-gray-900">
                    {{ review.reviewerName }}
                  </h3>
                  <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ review.type }}</span>
                </div>
                <p class="text-sm text-gray-600">
                  Reviewed <strong>{{ review.reviewedItem }}</strong>
                </p>
              </div>
              <div class="text-right">
                <div class="flex items-center justify-end gap-1 mb-2">
                  <span
                    v-for="i in 5"
                    :key="i"
                    class="text-lg"
                    :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
                  >★</span>
                </div>
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold text-white', review.status === 'published' ? 'bg-green-500' : review.status === 'pending' ? 'bg-yellow-500' : 'bg-red-500']">
                  {{ review.status }}
                </span>
              </div>
            </div>

            <!-- Review Content -->
            <p class="text-gray-700 mb-4">
              {{ review.content }}
            </p>

            <!-- Reviewer Info -->
            <div class="flex justify-between items-center text-sm text-gray-600 mb-4">
              <span>{{ formatDate(review.createdAt) }}</span>
              <span
                v-if="review.replies > 0"
                class="text-blue-600 font-medium"
              >{{ review.replies }} {{ review.replies === 1 ? 'reply' : 'replies' }}</span>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
              <button
                v-if="review.status === 'pending'"
                class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition"
                @click="approveReview(review.id)"
              >
                Approve
              </button>
              <button
                v-if="review.status !== 'flagged'"
                class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition"
                @click="flagReview(review.id)"
              >
                Flag
              </button>
              <button
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition"
                @click="viewDetails(review.id)"
              >
                View Details
              </button>
              <button
                class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition"
                @click="deleteReview(review.id)"
              >
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="!loading && filteredReviews.length === 0"
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
              d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2h-3l-4 4z"
            />
          </svg>
          <p class="text-gray-600 text-lg">
            No reviews found
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
import { formatDate as formatDateValue, formatFixed } from '../../utils/formatters'
const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedRating = ref('')
const selectedType = ref('')
const selectedStatus = ref('')
const searchQuery = ref('')
const sortBy = ref('recent')

const reviews = ref([
  {
    id: 1,
    reviewerName: 'Jessica Thompson',
    reviewedItem: 'Emma Johnson (Photographer)',
    type: 'photographer',
    rating: 5,
    content: 'Excellent photographer! Professional, punctual, and delivered amazing photos. Highly recommended!',
    status: 'published',
    createdAt: '2026-01-20',
    replies: 1
  },
  {
    id: 2,
    reviewerName: 'Michael Chen',
    reviewedItem: 'Wedding Photography Session',
    type: 'booking',
    rating: 4,
    content: 'Great service overall. Photos turned out beautiful. Would have appreciated faster turnaround.',
    status: 'published',
    createdAt: '2026-01-19',
    replies: 0
  },
  {
    id: 3,
    reviewerName: 'Sarah Lee',
    reviewedItem: 'Annual Photography Conference',
    type: 'event',
    rating: 3,
    content: 'Good event, but networking could have been better organized.',
    status: 'pending',
    createdAt: '2026-01-21',
    replies: 0
  },
  {
    id: 4,
    reviewerName: 'David Martinez',
    reviewedItem: 'Portrait Session',
    type: 'booking',
    rating: 2,
    content: 'Session was canceled last minute with minimal notice. Not happy.',
    status: 'flagged',
    createdAt: '2026-01-15',
    replies: 2
  },
  {
    id: 5,
    reviewerName: 'Lisa Wong',
    reviewedItem: 'James Park (Photographer)',
    type: 'photographer',
    rating: 5,
    content: 'Absolutely fantastic work. One of the best photographers I\'ve worked with!',
    status: 'published',
    createdAt: '2026-01-18',
    replies: 0
  }
])

const filteredReviews = computed(() => {
  let result = reviews.value.filter(review => {
    const ratingMatch = !selectedRating.value || review.rating.toString() === selectedRating.value
    const typeMatch = !selectedType.value || review.type === selectedType.value
    const statusMatch = !selectedStatus.value || review.status === selectedStatus.value
    const searchMatch = !searchQuery.value || 
      review.reviewerName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      review.reviewedItem.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      review.content.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    return ratingMatch && typeMatch && statusMatch && searchMatch
  })

  // Sort
  if (sortBy.value === 'rating') {
    result.sort((a, b) => b.rating - a.rating)
  } else if (sortBy.value === 'oldest') {
    result.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt))
  } else {
    result.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
  }

  return result
})

const avgRating = computed(() => {
  if (reviews.value.length === 0) return 0
  const sum = reviews.value.reduce((acc, r) => acc + r.rating, 0)
  return sum / reviews.value.length
})

const pendingCount = computed(() => {
  return reviews.value.filter(r => r.status === 'pending').length
})

const flaggedCount = computed(() => {
  return reviews.value.filter(r => r.status === 'flagged').length
})

const formatDate = (date) => {
  return formatDateValue(date)
}

const approveReview = (reviewId) => {
  const review = reviews.value.find(r => r.id === reviewId)
  if (review) {
    review.status = 'published'
    toast.value = { show: true, message: 'Review approved and published!', type: 'success' }
  }
}

const flagReview = (reviewId) => {
  const review = reviews.value.find(r => r.id === reviewId)
  if (review) {
    review.status = 'flagged'
    toast.value = { show: true, message: 'Review flagged for moderation!', type: 'warning' }
  }
}

const deleteReview = (reviewId) => {
  const index = reviews.value.findIndex(r => r.id === reviewId)
  if (index > -1) {
    reviews.value.splice(index, 1)
    toast.value = { show: true, message: 'Review deleted!', type: 'success' }
  }
}

const viewDetails = (reviewId) => {
  toast.value = { show: true, message: 'Loading review details...', type: 'info' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 800)
})
</script>
