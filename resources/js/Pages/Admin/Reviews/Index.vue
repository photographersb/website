<template>
  <div class="min-h-screen">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="⭐ Review Management" 
      subtitle="Monitor and moderate platform reviews"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">REVIEW CONTROL</p>
          <h1 class="hero-title">Reviews, quality and trust aligned.</h1>
          <p class="hero-subtitle">
            Moderate feedback, monitor ratings, and resolve flags quickly.
          </p>
          <div class="hero-actions">
            <button
              class="btn-admin-primary"
              @click="exportReviews"
            >
              Export
            </button>
            <button
              class="btn-admin-secondary"
              @click="fetchReviews"
            >
              Refresh List
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Total Reviews</span>
            <span class="status-value">{{ stats.total || 0 }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Avg Rating</span>
            <span class="status-value">{{ stats.avg_rating || 0 }} ⭐</span>
          </div>
          <div class="status-card">
            <span class="status-label">Flagged</span>
            <span class="status-value">{{ stats.reported || 0 }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Published: {{ stats.published || 0 }}
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid">
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
                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Reviews</span>
            <span class="stat-value">{{ stats.total }}</span>
          </div>
        </div>

        <div class="stat-card stat-yellow">
          <div class="stat-icon">
            <svg
              class="w-8 h-8"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Average Rating</span>
            <span class="stat-value">{{ stats.avg_rating }} ⭐</span>
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
                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Published</span>
            <span class="stat-value">{{ stats.published }}</span>
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
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Flagged</span>
            <span class="stat-value">{{ stats.reported }}</span>
          </div>
        </div>
      </div>

      <!-- Filters & Search -->
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
              v-model="filters.search" 
              type="text"
              placeholder="Search by photographer or client..." 
              class="search-input" 
              @input="debounceSearch"
            >
          </div>

          <select
            v-model="filters.rating"
            class="filter-select"
            @change="fetchReviews"
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

          <select
            v-model="filters.status"
            class="filter-select"
            @change="fetchReviews"
          >
            <option value="">
              All Status
            </option>
            <option value="published">
              Published
            </option>
            <option value="pending">
              Pending
            </option>
            <option value="flagged">
              Flagged
            </option>
            <option value="hidden">
              Hidden
            </option>
          </select>
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading reviews...</p>
        </div>

        <!-- Reviews List -->
        <div
          v-else-if="reviews.length > 0"
          class="reviews-container"
        >
          <div
            v-for="review in reviews"
            :key="review.id"
            class="review-card"
          >
            <div class="review-header">
              <div class="reviewer-info">
                <div class="reviewer-avatar">
                  {{ review.reviewer?.name?.charAt(0).toUpperCase() || 'C' }}
                </div>
                <div>
                  <div class="reviewer-name">
                    {{ review.reviewer?.name || 'Anonymous' }}
                    <span
                      v-if="review.is_anonymous"
                      class="ml-2 badge badge-info"
                      title="This review is displayed as anonymous to the public"
                    >
                      🔒 Public: Anonymous
                    </span>
                  </div>
                  <div class="review-date">
                    {{ formatDate(review.created_at) }}
                  </div>
                </div>
              </div>
              <div class="review-actions">
                <button
                  v-if="review.status === 'flagged'"
                  class="btn-action btn-success"
                  title="Unflag"
                  @click="unflagReview(review)"
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
                  v-else
                  class="btn-action btn-warning"
                  title="Flag"
                  @click="flagReview(review)"
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
                      d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"
                    />
                  </svg>
                </button>
                <button
                  class="btn-action"
                  title="View Details"
                  @click="viewReview(review)"
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
                <button
                  class="btn-action btn-danger"
                  title="Delete"
                  @click="deleteReview(review)"
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
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                </button>
              </div>
            </div>

            <div class="review-body">
              <div class="photographer-reviewed">
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
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                  />
                </svg>
                <span>{{ review.photographer?.business_name || review.photographer?.user?.name || 'N/A' }}</span>
              </div>

              <div class="rating-stars">
                <span
                  v-for="i in 5"
                  :key="i"
                  :class="i <= review.rating ? 'star-filled' : 'star-empty'"
                >★</span>
                <span class="rating-number">{{ review.rating }}/5</span>
              </div>

              <p class="review-comment">
                {{ review.comment || 'No comment provided' }}
              </p>

              <div
                v-if="review.status === 'flagged'"
                class="flagged-badge"
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
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                  />
                </svg>
                Flagged for Review
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
            ⭐
          </div>
          <p class="empty-title">
            No reviews found
          </p>
          <p class="empty-subtitle">
            Reviews will appear here as they are submitted
          </p>
        </div>

        <!-- Pagination -->
        <div
          v-if="reviews.length > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ reviews.length }} reviews
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
            <h3>Review Details</h3>
            <button
              class="modal-close"
              @click="showViewModal = false"
            >
              ×
            </button>
          </div>
          <div
            v-if="selectedReview"
            class="modal-body"
          >
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Client:</span>
                <span class="detail-value">
                  {{ selectedReview.reviewer?.name }}
                  <span
                    v-if="selectedReview.is_anonymous"
                    class="ml-2 badge badge-info"
                  >
                    🔒 Public: Anonymous
                  </span>
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Photographer:</span>
                <span class="detail-value">{{ selectedReview.photographer?.business_name || selectedReview.photographer?.user?.name }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Rating:</span>
                <div class="rating-stars">
                  <span
                    v-for="i in 5"
                    :key="i"
                    :class="i <= selectedReview.rating ? 'star-filled' : 'star-empty'"
                  >★</span>
                  <span class="rating-number">{{ selectedReview.rating }}/5</span>
                </div>
              </div>
              <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span
                  v-if="selectedReview.status === 'flagged'"
                  class="badge badge-warning"
                >Flagged</span>
                <span
                  v-else
                  class="badge badge-success"
                >{{ selectedReview.status || 'active' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ formatDate(selectedReview.created_at) }}</span>
              </div>
            </div>

            <div class="comment-section">
              <h4>Comment</h4>
              <p class="review-comment-full">
                {{ selectedReview.comment || 'No comment provided' }}
              </p>
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

const reviews = ref([])
const loading = ref(false)
const showViewModal = ref(false)
const selectedReview = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({
  search: '',
  rating: '',
  status: ''
})

const stats = ref({
  total: 0,
  pending: 0,
  published: 0,
  rejected: 0,
  reported: 0,
  avg_rating: 0
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchReviews()
  }, 500)
}

const fetchReviews = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.status) params.status = filters.value.status
    if (filters.value.rating) params.rating = filters.value.rating

    const { data } = await api.get('/admin/reviews', { params })
    if (data.status === 'success') {
      reviews.value = data.data?.reviews || []
      if (data.data?.stats) {
        stats.value = data.data.stats
      }
    } else {
      showToastMessage(data.message || 'Error loading reviews')
    }
  } catch (error) {
    console.error('Error fetching reviews:', error)
    showToastMessage('Error loading reviews')
  } finally {
    loading.value = false
  }
}

const viewReview = (review) => {
  selectedReview.value = review
  showViewModal.value = true
}

const flagReview = async (review) => {
  if (!confirm('Flag this review for moderation?')) return

  try {
    const reason = prompt('Optional flag reason (leave blank if none):')
    const { data } = await api.post(`/admin/reviews/${review.id}/report`, {
      reason: reason || undefined
    })
    if (data.status === 'success') {
      review.status = 'flagged'
      showToastMessage('Review flagged successfully')
    } else {
      showToastMessage(data.message || 'Failed to flag review')
    }
  } catch (error) {
    console.error('Error flagging review:', error)
    showToastMessage('Error flagging review')
  }
}

const unflagReview = async (review) => {
  if (!confirm('Remove flag from this review?')) return

  try {
    const { data } = await api.put(`/admin/reviews/${review.id}/status`, {
      status: 'published'
    })
    if (data.status === 'success') {
      review.status = 'published'
      showToastMessage('Review unflagged successfully')
    } else {
      showToastMessage(data.message || 'Failed to unflag review')
    }
  } catch (error) {
    console.error('Error unflagging review:', error)
    showToastMessage('Error unflagging review')
  }
}

const deleteReview = async (review) => {
  if (!confirm('Are you sure you want to delete this review? This action cannot be undone.')) return

  try {
    const { data } = await api.delete(`/admin/reviews/${review.id}`)
    if (data.status === 'success' || data.message) {
      const index = reviews.value.findIndex(r => r.id === review.id)
      if (index > -1) {
        reviews.value.splice(index, 1)
      }
      showToastMessage('Review deleted successfully')
    } else {
      showToastMessage(data.message || 'Error deleting review')
    }
  } catch (error) {
    console.error('Error deleting review:', error)
    showToastMessage('Error deleting review')
  }
}

const exportReviews = () => {
  showToastMessage('Export feature coming soon')
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
  fetchReviews()
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
.admin-reviews { padding: 2rem; min-height: 100vh; background: var(--admin-bg-page); }
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
.stat-content { display: flex; flex-direction: column; }
.stat-label { color: #6b7280; font-size: 0.875rem; margin-bottom: 0.25rem; }
.stat-value { font-size: 2rem; font-weight: 700; color: #1f2937; }

.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.filters-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.search-box { position: relative; flex: 1; min-width: 300px; }
.search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: #9ca3af; }
.search-input { width: 100%; padding: 0.75rem 1rem 0.75rem 3rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; }
.search-input:focus { outline: none; border-color: var(--admin-brand-primary); box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.12); }
.filter-select { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer; }
.filter-select:focus { outline: none; border-color: var(--admin-brand-primary); }

.loading-state { text-align: center; padding: 3rem; color: #6b7280; }
.spinner { width: 3rem; height: 3rem; border: 3px solid #e5e7eb; border-top-color: var(--admin-brand-primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
@keyframes spin { to { transform: rotate(360deg); } }

.reviews-container { display: grid; gap: 1.5rem; }
.review-card { background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; transition: all 0.2s; }
.review-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

.review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.reviewer-info { display: flex; align-items: center; gap: 0.75rem; }
.reviewer-avatar { width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--admin-brand-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1rem; }
.reviewer-name { font-weight: 600; color: #1f2937; }
.review-date { font-size: 0.875rem; color: #6b7280; }

.review-actions { display: flex; gap: 0.5rem; }
.btn-action { width: 2rem; height: 2rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.375rem; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; transition: all 0.2s; }
.btn-action:hover { background: #f9fafb; border-color: var(--admin-brand-primary); color: var(--admin-brand-primary); }
.btn-success:hover { background: var(--admin-success-light); border-color: var(--admin-success); color: var(--admin-success-text); }
.btn-warning:hover { background: var(--admin-warning-light); border-color: var(--admin-warning); color: var(--admin-warning-text); }
.btn-danger:hover { background: var(--admin-danger-light); border-color: var(--admin-danger); color: var(--admin-danger-text); }

.review-body { display: flex; flex-direction: column; gap: 1rem; }
.photographer-reviewed { display: flex; align-items: center; gap: 0.5rem; color: #6b7280; font-size: 0.875rem; }
.rating-stars { display: flex; align-items: center; gap: 0.25rem; }
.star-filled { color: var(--admin-brand-primary); font-size: 1.25rem; }
.star-empty { color: #d1d5db; font-size: 1.25rem; }
.rating-number { margin-left: 0.5rem; color: #6b7280; font-size: 0.875rem; font-weight: 600; }
.review-comment { color: #1f2937; line-height: 1.6; margin: 0; }
.review-comment-full { color: #1f2937; line-height: 1.6; background: #f9fafb; padding: 1rem; border-radius: 0.5rem; margin: 0; }

.flagged-badge { display: inline-flex; align-items: center; gap: 0.5rem; background: var(--admin-warning-light); color: var(--admin-warning-text); padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; }

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

.detail-grid { display: grid; gap: 1rem; margin-bottom: 2rem; }
.detail-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; }
.detail-label { font-weight: 600; color: #6b7280; }
.detail-value { color: #1f2937; }

.comment-section h4 { margin: 0 0 1rem 0; font-size: 1.125rem; color: #1f2937; }

.badge { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-warning { background: #fef3c7; color: #92400e; }

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
