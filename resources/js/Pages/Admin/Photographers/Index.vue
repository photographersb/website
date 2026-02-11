<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Header with Back Button & Notifications -->
    <AdminHeader 
      title="📷 Photographer Management" 
      subtitle="Manage photographer profiles, verifications, and portfolios"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <AdminSectionHeader
        title="Photographer Management"
        subtitle="Manage photographer profiles, verifications, and portfolios in one place."
        eyebrow="Admin / Photographers"
      />

      <AdminStatsStrip :stats="statItems" />

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
              placeholder="Search photographers by name..." 
              class="search-input" 
              @input="debounceSearch"
            >
          </div>

          <select
            v-model="filters.verification"
            class="filter-select"
            @change="fetchPhotographers"
          >
            <option value="">
              All Status
            </option>
            <option value="verified">
              Verified
            </option>
            <option value="pending">
              Pending
            </option>
            <option value="unverified">
              Unverified
            </option>
          </select>

          <select
            v-model="filters.city_id"
            class="filter-select"
            @change="fetchPhotographers"
          >
            <option value="">
              All Cities
            </option>
            <option
              v-for="city in cities"
              :key="city.id"
              :value="city.id"
            >
              {{ city.name }}
            </option>
          </select>

          <select
            v-model="filters.rating"
            class="filter-select"
            @change="fetchPhotographers"
          >
            <option value="">
              All Ratings
            </option>
            <option value="4.5">
              4.5+ Stars
            </option>
            <option value="4">
              4+ Stars
            </option>
            <option value="3">
              3+ Stars
            </option>
          </select>

          <template #actions>
            <button
              class="btn-export"
              @click="exportPhotographers"
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
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
              Export
            </button>
          </template>
        </AdminFilterBar>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading photographers...</p>
        </div>

        <div
          v-else-if="photographers.length > 0"
          class="photographers-table"
        >
          <div class="bulk-actions">
            <div class="bulk-left">
              <input
                type="checkbox"
                :checked="allSelected"
                :indeterminate.prop="someSelected"
                @change="toggleSelectAll"
              >
              <span>{{ selectedCount }} selected</span>
            </div>
            <div class="bulk-right">
              <button
                class="btn-action btn-verify"
                :disabled="!selectedCount || bulkVerifying"
                @click="bulkVerifySelected"
              >
                <span v-if="bulkVerifying" class="spinner-mini" aria-hidden="true" />
                {{ bulkVerifying ? 'Verifying...' : 'Verify Selected' }}
              </button>
              <button
                class="btn-export"
                :disabled="!selectedCount || bulkVerifying"
                @click="exportPhotographers"
              >
                Export Selected
              </button>
            </div>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>
                    <input
                      type="checkbox"
                      :checked="allSelected"
                      :indeterminate.prop="someSelected"
                      @change="toggleSelectAll"
                    >
                  </th>
                  <th>Photographer</th>
                  <th>Contact</th>
                  <th>Status</th>
                  <th>Rating</th>
                  <th>City</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="photographer in photographers"
                  :key="photographer.id"
                >
                  <td>
                    <input
                      type="checkbox"
                      :checked="isSelected(photographer.id)"
                      @change="toggleSelect(photographer.id)"
                    >
                  </td>
                  <td>
                    <div class="user-cell">
                      <div class="photographer-avatar">
                        <img
                          v-if="photographer.profile_picture"
                          :src="photographer.profile_picture"
                          :alt="photographer.business_name"
                        >
                        <span v-else>{{ photographer.business_name?.charAt(0).toUpperCase() || '📷' }}</span>
                      </div>
                      <div>
                        <div class="user-name">
                          {{ photographer.business_name || 'N/A' }}
                        </div>
                        <div class="user-subtitle">
                          {{ photographer.user?.name || 'N/A' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="user-subtitle">
                      {{ photographer.user?.email || 'N/A' }}
                    </div>
                  </td>
                  <td>
                    <span
                      v-if="photographer.is_verified"
                      class="status-pill status-pill--verified"
                    >
                      Verified
                    </span>
                    <span
                      v-else
                      class="status-pill status-pill--pending"
                    >
                      Pending
                    </span>
                  </td>
                  <td>
                    <span class="rating-pill">
                      ⭐ {{ photographer.average_rating || 'N/A' }}
                    </span>
                    <div class="user-subtitle">
                      {{ photographer.total_reviews || 0 }} reviews
                    </div>
                  </td>
                  <td>
                    <span class="user-subtitle">
                      {{ photographer.city?.name || 'Not specified' }}
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button
                        class="btn-action btn-view"
                        @click="viewPhotographer(photographer)"
                      >
                        View
                      </button>
                      <button
                        v-if="!photographer.is_verified"
                        class="btn-action btn-verify"
                        @click="verifyPhotographer(photographer)"
                      >
                        Verify
                      </button>
                      <button
                        class="btn-action btn-edit"
                        @click="editPhotographer(photographer)"
                      >
                        Edit
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            📷
          </div>
          <p class="empty-title">
            No photographers found
          </p>
          <p class="empty-subtitle">
            Try adjusting your filters
          </p>
        </div>

        <!-- Pagination -->
        <div
          v-if="meta.total > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ photographers.length }} of {{ meta.total }} photographers
          </div>
          <div class="pagination-controls">
            <button
              :disabled="meta.current_page <= 1"
              class="pagination-btn"
              @click="changePage(meta.current_page - 1)"
            >
              Previous
            </button>
            <span class="pagination-current">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
            <button
              :disabled="meta.current_page >= meta.last_page"
              class="pagination-btn"
              @click="changePage(meta.current_page + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div
        v-if="showEditModal"
        class="modal-overlay"
        @click.self="showEditModal = false"
      >
        <div class="modal modal-large">
          <div class="modal-header">
            <h3>Edit Photographer</h3>
            <button
              class="modal-close"
              @click="showEditModal = false"
            >
              ×
            </button>
          </div>
          <div
            v-if="selectedPhotographer"
            class="modal-body"
          >
            <form
              class="edit-form"
              @submit.prevent="savePhotographer"
            >
              <div class="form-row">
                <div class="form-group">
                  <label>Business Name</label>
                  <input
                    v-model="editForm.business_name"
                    type="text"
                    class="form-input"
                    required
                  >
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input
                    v-model="editForm.email"
                    type="email"
                    class="form-input"
                    required
                  >
                </div>
              </div>
    
              <div class="form-row">
                <div class="form-group">
                  <label>Phone</label>
                  <input
                    v-model="editForm.phone"
                    type="text"
                    class="form-input"
                  >
                </div>
                <div class="form-group">
                  <label>City</label>
                  <select
                    v-model="editForm.city_id"
                    class="form-input"
                  >
                    <option value="">
                      Select city
                    </option>
                    <option
                      v-for="city in cities"
                      :key="city.id"
                      :value="city.id"
                    >
                      {{ city.name }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                  <label>Bio</label>
                  <div>
                    <button
                      type="button"
                      class="btn-secondary"
                      style="padding: 6px 12px; font-size: 0.9em; margin-right: 8px;"
                      @click="generateBio"
                    >
                      🔄 Generate Bio
                    </button>
                    <button
                      type="button"
                      class="btn-secondary"
                      style="padding: 6px 12px; font-size: 0.9em;"
                      @click="clearBio"
                    >
                      🗑️ Clear
                    </button>
                  </div>
                </div>
                <textarea
                  v-model="editForm.bio"
                  class="form-input"
                  rows="4"
                  style="margin-bottom: 8px;"
                />
              
                <!-- Profile Data Preview -->
                <div
                  v-if="editForm.experience_years || editForm.service_area_radius || specializations"
                  style="background: #eff6ff; border: 1px solid #93c5fd; border-radius: 6px; padding: 12px; margin-bottom: 12px;"
                >
                  <div style="font-size: 0.85em; color: #1e40af; font-weight: 600; margin-bottom: 8px;">
                    📊 Profile Data Being Used:
                  </div>
                  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.85em;">
                    <div
                      v-if="editForm.experience_years"
                      style="color: #1e40af;"
                    >
                      <strong>🎯 Experience:</strong> {{ editForm.experience_years }} years
                    </div>
                    <div
                      v-if="specializations"
                      style="color: #1e40af;"
                    >
                      <strong>📸 Specializations:</strong> {{ specializations }}
                    </div>
                    <div
                      v-if="getLocationName"
                      style="color: #1e40af;"
                    >
                      <strong>📍 Location:</strong> {{ getLocationName }}
                    </div>
                    <div
                      v-if="editForm.service_area_radius"
                      style="color: #1e40af;"
                    >
                      <strong>📍 Radius:</strong> {{ editForm.service_area_radius }} km
                    </div>
                  </div>
                </div>
              
                <!-- Generated Bios - Multiple Options -->
                <div
                  v-if="generatedBios.length > 0"
                  style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; padding: 12px;"
                >
                  <div style="font-size: 0.85em; color: #22863a; font-weight: 600; margin-bottom: 12px;">
                    ✨ {{ generatedBios.length }} Generated Bio Options - Pick Your Favorite:
                  </div>
                  <div style="display: flex; flex-direction: column; gap: 10px; max-height: 400px; overflow-y: auto;">
                    <div
                      v-for="(bio, index) in generatedBios"
                      :key="index"
                      style="background: white; border: 1px solid #86efac; border-radius: 4px; padding: 10px; cursor: pointer; transition: all 0.2s;"
                      @mouseover="$event.target.parentElement.style.background='#dcfce7'"
                      @mouseleave="$event.target.parentElement.style.background='white'"
                      @click="() => useBio(bio)"
                    >
                      <div style="font-size: 0.75em; color: #65a30d; font-weight: 600; margin-bottom: 4px;">
                        Option {{ index + 1 }} - {{ bio.style }}
                      </div>
                      <div style="font-size: 0.9em; color: #166534; line-height: 1.5;">
                        {{ bio.text }}
                      </div>
                      <button
                        type="button"
                        class="btn-success"
                        style="margin-top: 6px; padding: 4px 10px; font-size: 0.8em;"
                        @click.stop="() => useBio(bio)"
                      >
                        ✅ Use This
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Experience (years)</label>
                  <input
                    v-model.number="editForm.experience_years"
                    type="number"
                    class="form-input"
                    min="0"
                  >
                </div>
                <div class="form-group">
                  <label>Service Area Radius (km)</label>
                  <input
                    v-model.number="editForm.service_area_radius"
                    type="number"
                    class="form-input"
                    min="0"
                  >
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label class="checkbox-label">
                    <input
                      v-model="editForm.is_verified"
                      type="checkbox"
                    >
                    <span>Verified</span>
                  </label>
                </div>
                <div class="form-group">
                  <label class="checkbox-label">
                    <input
                      v-model="editForm.is_featured"
                      type="checkbox"
                    >
                    <span>Featured</span>
                  </label>
                </div>
              </div>

              <div class="modal-actions">
                <button
                  type="button"
                  class="btn-cancel"
                  @click="showEditModal = false"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn-save"
                  :disabled="saving"
                >
                  {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- View Modal -->
      <div
        v-if="showViewModal"
        class="modal-overlay"
        @click.self="showViewModal = false"
      >
        <div class="modal modal-large">
          <div class="modal-header">
            <h3>Photographer Details</h3>
            <button
              class="modal-close"
              @click="showViewModal = false"
            >
              ×
            </button>
          </div>
          <div
            v-if="selectedPhotographer"
            class="modal-body"
          >
            <div class="profile-section">
              <div class="profile-avatar-large">
                <img
                  v-if="selectedPhotographer.profile_picture"
                  :src="selectedPhotographer.profile_picture"
                  :alt="selectedPhotographer.business_name"
                >
                <span v-else>{{ selectedPhotographer.business_name?.charAt(0).toUpperCase() }}</span>
              </div>
              <div class="profile-info">
                <h2>{{ selectedPhotographer.business_name }}</h2>
                <p class="text-gray">
                  {{ selectedPhotographer.user?.email }}
                </p>
                <div class="badge-group">
                  <span
                    v-if="selectedPhotographer.is_verified"
                    class="badge badge-success"
                  >✓ Verified</span>
                  <span
                    v-else
                    class="badge badge-warning"
                  >Pending Verification</span>
                  <span
                    v-if="selectedPhotographer.is_featured"
                    class="badge badge-purple"
                  >⭐ Featured</span>
                </div>
              </div>
            </div>

            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ selectedPhotographer.phone || 'N/A' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Experience:</span>
                <span class="detail-value">{{ selectedPhotographer.experience_years }} years</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Price Range:</span>
                <span class="detail-value">৳{{ selectedPhotographer.price_range_min }} - ৳{{ selectedPhotographer.price_range_max }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Rating:</span>
                <span class="detail-value">⭐ {{ selectedPhotographer.average_rating || 'N/A' }} ({{ selectedPhotographer.total_reviews }} reviews)</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Location:</span>
                <span class="detail-value">
                  {{ selectedPhotographer.city?.name || 'N/A' }}
                  <span v-if="selectedPhotographer.city?.parent?.name">, {{ selectedPhotographer.city.parent.name }}</span>
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Joined:</span>
                <span class="detail-value">{{ formatDate(selectedPhotographer.created_at) }}</span>
              </div>
            </div>

            <div class="bio-section">
              <h4>Bio</h4>
              <p>{{ selectedPhotographer.bio || 'No bio provided' }}</p>
            </div>

            <div class="categories-section">
              <h4>Categories</h4>
              <div class="category-list">
                <span
                  v-for="category in selectedPhotographer.categories"
                  :key="category.id"
                  class="category-tag"
                >
                  {{ category.name }}
                </span>
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
import { ref, computed, onMounted, h } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import AdminSectionHeader from '../../../components/admin/ui/AdminSectionHeader.vue'
import AdminStatsStrip from '../../../components/admin/ui/AdminStatsStrip.vue'
import AdminFilterBar from '../../../components/admin/ui/AdminFilterBar.vue'
import api from '../../../api'

const photographers = ref([])
const cities = ref([])
const loading = ref(false)
const showViewModal = ref(false)
const selectedPhotographer = ref(null)
const showToast = ref(false)
const toastMessage = ref('')
const showEditModal = ref(false)
const editForm = ref({})
const saving = ref(false)
const generatedBios = ref([])
const specializations = ref('')
const selectedIds = ref([])
const bulkVerifying = ref(false)

const filters = ref({
  search: '',
  verification: '',
  city_id: '',
  rating: ''
})

const meta = ref({
  total: 0,
  current_page: 1,
  last_page: 1,
  per_page: 30
})

const stats = ref({
  total: 0,
  verified: 0,
  pending: 0,
  avgRating: '0.0'
})

const CameraIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z' })
])

const VerifiedIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })
])

const PendingIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
])

const RatingIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' })
])

let searchTimeout = null

// Computed property for location name
const getLocationName = computed(() => {
  if (!editForm.value.city_id) return ''
  const city = cities.value.find(c => c.id == editForm.value.city_id)
  return city ? city.name : ''
})

const statItems = computed(() => [
  {
    label: 'Total Photographers',
    value: stats.value.total,
    meta: `${stats.value.verified} verified`,
    icon: CameraIcon,
    tone: 'neutral',
  },
  {
    label: 'Verified',
    value: stats.value.verified,
    meta: 'Approved profiles',
    icon: VerifiedIcon,
    tone: 'success',
  },
  {
    label: 'Pending',
    value: stats.value.pending,
    meta: 'Awaiting review',
    icon: PendingIcon,
    tone: 'warning',
  },
  {
    label: 'Avg Rating',
    value: stats.value.avgRating,
    meta: 'Across all reviews',
    icon: RatingIcon,
    tone: 'info',
  }
])

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchPhotographers()
  }, 500)
}

const selectedCount = computed(() => selectedIds.value.length)

const allSelected = computed(() => {
  return photographers.value.length > 0 && selectedIds.value.length === photographers.value.length
})

const someSelected = computed(() => {
  return selectedIds.value.length > 0 && !allSelected.value
})

const isSelected = (id) => selectedIds.value.includes(id)

const toggleSelect = (id) => {
  if (isSelected(id)) {
    selectedIds.value = selectedIds.value.filter(item => item !== id)
  } else {
    selectedIds.value = [...selectedIds.value, id]
  }
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedIds.value = []
    return
  }
  selectedIds.value = photographers.value.map((photographer) => photographer.id)
}

const fetchPhotographers = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: meta.value.per_page
    }

    if (filters.value.search) params.search = filters.value.search
    if (filters.value.verification) params.verification = filters.value.verification
    if (filters.value.rating) params.rating = filters.value.rating
    if (filters.value.city_id) params.city_id = filters.value.city_id

    const { data } = await api.get('/admin/photographers', { params })
    
    if (data.status === 'success') {
      photographers.value = data.data.photographers
      selectedIds.value = []
      
      // Update meta from backend - safely handle meta structure
      if (data.meta) {
        meta.value = {
          total: data.meta.total || 0,
          current_page: data.meta.current_page || 1,
          last_page: data.meta.last_page || 1,
          per_page: data.meta.per_page || 20
        }
      } else if (data.data.meta) {
        meta.value = {
          total: data.data.meta.total || 0,
          current_page: data.data.meta.current_page || 1,
          last_page: data.data.meta.last_page || 1,
          per_page: data.data.meta.per_page || 20
        }
      }

      // Use backend-calculated stats (accounts for filters)
      if (data.data.stats) {
        stats.value.total = data.data.stats.total
        stats.value.verified = data.data.stats.verified
        stats.value.pending = data.data.stats.pending
        stats.value.avgRating = data.data.stats.avgRating
      }
    } else {
      console.error('API error:', data)
      showToastMessage('Error: ' + (data.message || 'Failed to load photographers'))
    }
  } catch (error) {
    console.error('Error fetching photographers:', error)
    showToastMessage('Error loading photographers')
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= meta.value.last_page) {
    fetchPhotographers(page)
  }
}

const viewPhotographer = (photographer) => {
  selectedPhotographer.value = photographer
  showViewModal.value = true
}

const verifyPhotographer = async (photographer) => {
  if (!confirm(`Verify ${photographer.business_name}?`)) return

  try {
    const { data } = await api.post(`/admin/photographers/${photographer.id}/verify`)
    if (data.status === 'success') {
      photographer.is_verified = true
      showToastMessage(data.message || `${photographer.business_name} verified successfully`)
    } else {
      showToastMessage(data.message || 'Error verifying photographer')
    }
  } catch (error) {
    console.error('Error verifying photographer:', error)
    showToastMessage('Error verifying photographer')
  }
}

const bulkVerifySelected = async () => {
  if (!selectedIds.value.length) return
  if (!confirm(`Verify ${selectedIds.value.length} photographers?`)) return

  const selectedPhotographers = photographers.value.filter((photographer) => selectedIds.value.includes(photographer.id))
  let successCount = 0

  bulkVerifying.value = true

  for (const photographer of selectedPhotographers) {
    if (photographer.is_verified) continue
    try {
      const { data } = await api.post(`/admin/photographers/${photographer.id}/verify`)
      if (data.status === 'success') {
        photographer.is_verified = true
        successCount += 1
      }
    } catch (error) {
      console.error('Error verifying photographer:', error)
    }
  }

  bulkVerifying.value = false

  if (successCount) {
    showToastMessage(`Verified ${successCount} photographers`)
  } else {
    showToastMessage('No photographers were verified')
  }
  selectedIds.value = []
}

const editPhotographer = (photographer) => {
  selectedPhotographer.value = photographer
  editForm.value = {
    business_name: photographer.business_name || photographer.user?.name,
    email: photographer.user?.email,
    phone: photographer.user?.phone || '',
    city_id: photographer.city_id || '',
    bio: photographer.bio || '',
    experience_years: photographer.experience_years || 0,
    service_area_radius: photographer.service_area_radius || 50,
    is_verified: photographer.is_verified || false,
    is_featured: photographer.is_featured || false
  }
  specializations.value = (photographer.specializations || []).join(', ')
  generatedBios.value = []
  showEditModal.value = true
}

const savePhotographer = async () => {
  saving.value = true
  try {
    const { data } = await api.put(`/admin/photographers/${selectedPhotographer.value.id}`, editForm.value)
    
    if (data.status === 'success') {
      showToastMessage('Photographer updated successfully')
      showEditModal.value = false
      fetchPhotographers(meta.value.current_page)
    } else {
      showToastMessage(data.message || 'Error updating photographer')
    }
  } catch (error) {
    console.error('Error updating photographer:', error)
    showToastMessage('Error updating photographer')
  } finally {
    saving.value = false
  }
}

const exportPhotographers = () => {
  showToastMessage('Export feature coming soon')
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}-${month}-${year}` // DD-MM-YYYY for Bangladesh
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

const fetchCities = async () => {
  try {
    const { data } = await api.get('/locations', {
      params: {
        type: 'district'
      }
    })
    cities.value = data.data || []
  } catch (error) {
    console.error('Error fetching cities:', error)
  }
}

// Auto-generate bio from photographer profile data
const generateBio = () => {
  const exp = editForm.value.experience_years
  const specs = specializations.value
    .split(',')
    .map(s => s.trim())
    .filter(s => s.length > 0)
  const locationName = getLocationName.value
  const radius = editForm.value.service_area_radius

  const bios = []

  // STYLE 1: Professional & Formal
  if (specs.length && exp && locationName) {
    bios.push({
      style: 'Professional',
      text: `A seasoned ${specs[0]} photographer with ${exp} years of professional experience. Serving ${locationName} and surrounding areas within ${radius}km radius. Dedicated to delivering exceptional results with meticulous attention to detail.`
    })
  }

  // STYLE 2: Creative & Passionate
  if (specs.length && exp) {
    bios.push({
      style: 'Passionate',
      text: `Creative ${specs.join(' & ')} photographer with ${exp} years of passion-driven experience. Specializing in capturing genuine moments and transforming them into timeless visual stories.`
    })
  }

  // STYLE 3: Artistic & Unique
  if (specs.length) {
    bios.push({
      style: 'Artistic',
      text: `Artistic lens specialist in ${specs.join(', ')}. Bringing creativity, innovation, and technical expertise to every frame. Every photo tells a story worth capturing.`
    })
  }

  // STYLE 4: Friendly & Approachable
  if (specs.length && locationName) {
    bios.push({
      style: 'Friendly',
      text: `Your ${specs[0]} photographer in ${locationName}! With years of experience, I love capturing the moments that matter most. Let's create something beautiful together.`
    })
  }

  // STYLE 5: Client-Focused
  if (specs.length && exp && locationName) {
    bios.push({
      style: 'Client-Focused',
      text: `${specs[0]} photographer committed to bringing your vision to life. ${exp} years of experience ensuring your special moments are captured beautifully in ${locationName}.`
    })
  }

  // STYLE 6: Innovative & Modern
  if (specs.length) {
    bios.push({
      style: 'Innovative',
      text: `Modern ${specs.join(' + ')} photographer blending traditional techniques with contemporary aesthetics. Creating stunning visual content that stands out.`
    })
  }

  // STYLE 7: Premium & Luxury
  if (specs.length && exp && locationName) {
    bios.push({
      style: 'Premium',
      text: `Premium ${specs[0]} services with ${exp} years of excellence. Based in ${locationName}, serving discerning clients across ${radius}km radius seeking sophisticated visual storytelling.`
    })
  }

  // STYLE 8: Storyteller
  if (specs.length && exp) {
    bios.push({
      style: 'Storyteller',
      text: `${specs.join(' and ')} storyteller. ${exp} years of capturing authentic narratives through the lens, preserving your most precious memories in stunning detail.`
    })
  }

  // STYLE 9: Technical Expert
  if (specs.length && exp) {
    bios.push({
      style: 'Technical Expert',
      text: `Expert ${specs[0]} photographer with ${exp}+ years mastering composition, lighting, and post-production. Technical precision meets artistic vision in every shot.`
    })
  }

  // STYLE 10: Enthusiast Professional
  if (specs.length && locationName) {
    bios.push({
      style: 'Enthusiast',
      text: `Enthusiastic ${specs[0]} professional based in ${locationName}. Combining technical skill with genuine passion to deliver photos you'll treasure forever.`
    })
  }

  // STYLE 11: Minimalist & Direct
  if (specs.length && exp) {
    bios.push({
      style: 'Direct',
      text: `${specs.join(', ')} photographer. ${exp} years. Exceptional results. That's what you get.`
    })
  }

  // STYLE 12: Vision-Driven
  if (specs.length && exp && locationName) {
    bios.push({
      style: 'Vision-Driven',
      text: `Visionary ${specs[0]} photographer in ${locationName} with ${exp} years dedicated to capturing moments exactly as you imagine them. Local roots, global vision.`
    })
  }

  // FALLBACK if minimal data
  if (bios.length === 0) {
    bios.push({
      style: 'Classic',
      text: 'Professional photographer dedicated to capturing beautiful moments and creating lasting visual memories.'
    })
  }

  generatedBios.value = bios
}

const useBio = (bio) => {
  if (bio && bio.text) {
    editForm.value.bio = bio.text
    generatedBios.value = []
    showToastMessage(`✅ Bio added! "${bio.style}" style applied.`)
  }
}

const clearBio = () => {
  editForm.value.bio = ''
  generatedBios.value = []
  showToastMessage('Bio cleared')
}

onMounted(() => {
  const params = new URLSearchParams(window.location.search)
  const verification = params.get('verification')
  if (verification) {
    filters.value.verification = verification
  }
  fetchCities()
  fetchPhotographers()
})
</script>

<style scoped>
.admin-photographers {
  padding: 2rem;
  min-height: 100vh;
  background: #f9fafb;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.page-subtitle {
  color: #6b7280;
  margin: 0.5rem 0 0 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

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

.stat-blue { border-color: var(--admin-brand-primary); }
.stat-green { border-color: var(--admin-brand-primary); }
.stat-yellow { border-color: var(--admin-brand-primary); }
.stat-purple { border-color: var(--admin-brand-primary); }

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-blue .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-green .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-yellow .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }
.stat-purple .stat-icon { background: var(--admin-brand-primary-soft); color: var(--admin-brand-primary); }

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

.filters-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.search-box {
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
  box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.12);
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

.photographers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.photographer-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s;
}

.photographer-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.card-header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.photographer-avatar {
  width: 4rem;
  height: 4rem;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--admin-brand-primary), var(--admin-brand-primary-dark));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: 600;
  overflow: hidden;
}

.photographer-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.verified-badge {
  background: #d1fae5;
  color: #065f46;
  padding: 0.375rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.photographer-name {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.photographer-user {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0 0 1rem 0;
}

.photographer-categories {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.category-tag {
  background: #f3f4f6;
  color: #4b5563;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.photographer-stats {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.stat-item svg {
  color: #f59e0b;
}

.photographer-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
}

.photographers-table {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.bulk-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  gap: 1rem;
}

.bulk-left {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #6b7280;
  font-size: 0.9rem;
}

.bulk-right {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 0.9rem;
  background: #f9fafb;
  color: #6b7280;
  font-weight: 600;
  font-size: 0.85rem;
  border-bottom: 1px solid #e5e7eb;
}

.data-table td {
  padding: 0.9rem;
  border-bottom: 1px solid #f3f4f6;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-name {
  font-weight: 600;
  color: #1f2937;
}

.user-subtitle {
  font-size: 0.8rem;
  color: #6b7280;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.25rem 0.65rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-pill--verified {
  background: #d1fae5;
  color: #065f46;
}

.status-pill--pending {
  background: #fef3c7;
  color: #92400e;
}

.rating-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-weight: 600;
  color: #1f2937;
}

.action-buttons {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.spinner-mini {
  width: 0.85rem;
  height: 0.85rem;
  border-radius: 999px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #ffffff;
  display: inline-block;
  margin-right: 0.4rem;
  animation: spin 0.8s linear infinite;
}

.btn-action {
  flex: 1;
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

.btn-verify {
  background: #d1fae5;
  border-color: #10b981;
  color: #065f46;
}

.btn-verify:hover {
  background: #10b981;
  color: white;
}

.btn-edit:hover {
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

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.pagination-info {
  color: #6b7280;
  font-size: 0.875rem;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.pagination-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
  background: #f9fafb;
  border-color: var(--admin-brand-primary);
  color: var(--admin-brand-primary);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-current {
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
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

.profile-section {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.profile-avatar-large {
  width: 6rem;
  height: 6rem;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--admin-brand-primary), var(--admin-brand-primary-dark));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 600;
  overflow: hidden;
  flex-shrink: 0;
}

.profile-avatar-large img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-info h2 {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
  color: #1f2937;
}

.text-gray {
  color: #6b7280;
  margin: 0 0 1rem 0;
}

.badge-group {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.badge {
  padding: 0.375rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-success { background: #d1fae5; color: #065f46; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-purple { background: #e9d5ff; color: #6b21a8; }

.detail-grid {
  display: grid;
  gap: 1rem;
  margin-bottom: 2rem;
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

.bio-section, .categories-section {
  margin-bottom: 2rem;
}

.bio-section h4, .categories-section h4 {
  margin: 0 0 1rem 0;
  font-size: 1.125rem;
  color: #1f2937;
}

.bio-section p {
  color: #6b7280;
  line-height: 1.6;
}

.category-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
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

/* Edit Form Styles */
.edit-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.form-input {
  padding: 0.625rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: var(--admin-brand-primary);
  box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.1);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-weight: 500;
}

.checkbox-label input[type="checkbox"] {
  width: 1.125rem;
  height: 1.125rem;
  cursor: pointer;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-cancel {
  padding: 0.625rem 1.25rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background: #f3f4f6;
}

.btn-save {
  padding: 0.625rem 1.25rem;
  background: var(--admin-brand-primary);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-save:hover:not(:disabled) {
  background: var(--admin-brand-primary-hover);
}

.btn-save:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
