<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Header -->
    <AdminHeader 
      title="⭐ Featured Photographers Management" 
      subtitle="Manage featured listings and photographer promotions"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <AdminSectionHeader
        title="Featured Photographers"
        subtitle="Manage featured listings and photographer promotions."
        eyebrow="Admin / Featured Photographers"
      >
        <template #actions>
          <button
            class="btn-primary"
            @click="showAddModal = true"
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
                d="M12 4v16m8-8H4"
              />
            </svg>
            Add Featured Photographer
          </button>
        </template>
      </AdminSectionHeader>

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
              v-model="searchQuery"
              type="text"
              placeholder="Search by photographer name..."
              class="search-input"
            >
          </div>

          <div class="filter-group">
            <select
              v-model="filterStatus"
              class="filter-select"
            >
              <option value="">
                All Status
              </option>
              <option value="active">
                Active
              </option>
              <option value="expired">
                Expired
              </option>
              <option value="pending">
                Pending
              </option>
            </select>

            <select
              v-model="filterPackage"
              class="filter-select"
            >
              <option value="">
                All Packages
              </option>
              <option value="Starter">
                Starter (৳999)
              </option>
              <option value="Professional">
                Professional (৳2,499)
              </option>
              <option value="Enterprise">
                Enterprise (৳5,999)
              </option>
            </select>

            <select
              v-model="filterCategory"
              class="filter-select"
            >
              <option value="">
                All Categories
              </option>
              <option value="Wedding">
                Wedding
              </option>
              <option value="Portrait">
                Portrait
              </option>
              <option value="Event">
                Event
              </option>
              <option value="Product">
                Product
              </option>
              <option value="Landscape">
                Landscape
              </option>
              <option value="Street">
                Street
              </option>
              <option value="Fashion">
                Fashion
              </option>
              <option value="Corporate">
                Corporate
              </option>
            </select>
          </div>
        </AdminFilterBar>

        <!-- Featured Photographers Table -->
        <div class="table-container">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Photographer</th>
                <th>Package</th>
                <th>Category</th>
                <th>Location</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="featured in filteredFeatured"
                :key="featured.id"
                class="table-row"
              >
                <td>
                  <div class="user-cell">
                    <img
                      :src="featured.photographer?.profile_picture || '/images/placeholder.svg'"
                      :alt="featured.photographer?.user?.name || 'Photographer'"
                      class="avatar"
                    >
                    <div>
                      <div class="font-semibold">
                        {{ featured.photographer?.user?.name || 'Unknown Photographer' }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ featured.photographer?.user?.email || '' }}
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span
                    class="package-badge"
                    :class="getPackageClass(featured.package_tier)"
                  >
                    {{ featured.package_tier }}
                  </span>
                </td>
                <td>{{ featured.category || '-' }}</td>
                <td>{{ featured.location || '-' }}</td>
                <td>{{ formatDate(featured.start_date) }}</td>
                <td>{{ formatDate(featured.end_date) }}</td>
                <td>
                  <span
                    class="status-badge"
                    :class="getStatusClass(featured)"
                  >
                    {{ getStatus(featured) }}
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button
                      class="btn-icon btn-edit"
                      title="Edit"
                      @click="editFeatured(featured)"
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
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                      </svg>
                    </button>
                    <button
                      class="btn-icon"
                      :class="featured.active ? 'btn-success' : 'btn-warning'"
                      :title="featured.active ? 'Deactivate' : 'Activate'"
                      @click="toggleStatus(featured)"
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
                      class="btn-icon btn-danger"
                      title="Delete"
                      @click="deleteFeatured(featured.id)"
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
                </td>
              </tr>
              <tr
                v-if="filteredFeatured.length === 0"
                class="table-empty"
              >
                <td
                  colspan="8"
                  class="text-center py-8 text-gray-500"
                >
                  No featured photographers found
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div
          v-if="pagination.total > 0"
          class="mt-4 flex items-center justify-between"
        >
          <div class="text-sm text-gray-600">
            Showing {{ (pagination.page - 1) * pagination.per_page + 1 }} to
            {{ Math.min(pagination.page * pagination.per_page, pagination.total) }} of
            {{ pagination.total }} featured photographers
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
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div
      v-if="showAddModal"
      class="modal-overlay"
      @click.self="closeModal"
    >
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ editingId ? 'Edit Featured Photographer' : 'Add Featured Photographer' }}</h2>
          <button
            class="modal-close"
            @click="closeModal"
          >
            ✕
          </button>
        </div>

        <div class="modal-body">
          <form
            class="space-y-4"
            @submit.prevent="saveFeatured"
          >
            <!-- Photographer Selection -->
            <div class="form-group">
              <label class="form-label">Photographer *</label>
              <input 
                v-model="formData.photographer_search" 
                type="text" 
                placeholder="Search photographer..." 
                class="form-input" 
                @input="searchPhotographers"
                @focus="searchPhotographers"
              >
              <div
                v-if="photographerOptions.length > 0"
                class="dropdown-list"
              >
                <div 
                  v-for="photo in photographerOptions" 
                  :key="photo.id" 
                  class="dropdown-item"
                  @click="selectPhotographer(photo)"
                >
                  {{ photo.name }} ({{ photo.email }})
                </div>
              </div>
              <div
                v-if="formData.photographer_id"
                class="mt-2 text-sm text-green-600"
              >
                ✓ Selected: {{ selectedPhotographer.name }}
              </div>
            </div>

            <!-- Package Tier -->
            <div class="form-group">
              <label class="form-label">Package Tier *</label>
              <select
                v-model="formData.package_tier"
                class="form-input"
                required
              >
                <option value="">
                  Select Package
                </option>
                <option value="Starter">
                  Starter (৳999/month)
                </option>
                <option value="Professional">
                  Professional (৳2,499/month)
                </option>
                <option value="Enterprise">
                  Enterprise (৳5,999/month)
                </option>
              </select>
            </div>

            <!-- Category -->
            <div class="form-group">
              <label class="form-label">Category</label>
              <select
                v-model="formData.category"
                class="form-input"
              >
                <option value="">
                  Select Category (Optional)
                </option>
                <option value="Wedding">
                  Wedding
                </option>
                <option value="Portrait">
                  Portrait
                </option>
                <option value="Event">
                  Event
                </option>
                <option value="Product">
                  Product
                </option>
                <option value="Landscape">
                  Landscape
                </option>
                <option value="Street">
                  Street
                </option>
                <option value="Fashion">
                  Fashion
                </option>
                <option value="Corporate">
                  Corporate
                </option>
              </select>
            </div>

            <!-- Location -->
            <div class="form-group">
              <label class="form-label">Location</label>
              <select
                v-model="formData.location"
                class="form-input"
              >
                <option value="">
                  Select Location (Optional)
                </option>
                <option value="Dhaka">
                  Dhaka
                </option>
                <option value="Chittagong">
                  Chittagong
                </option>
                <option value="Sylhet">
                  Sylhet
                </option>
                <option value="Rajshahi">
                  Rajshahi
                </option>
                <option value="Khulna">
                  Khulna
                </option>
                <option value="Barisal">
                  Barisal
                </option>
                <option value="Mymensingh">
                  Mymensingh
                </option>
                <option value="Rangpur">
                  Rangpur
                </option>
              </select>
            </div>

            <!-- Start Date -->
            <div class="form-group">
              <label class="form-label">Start Date *</label>
              <input
                v-model="formData.start_date"
                type="date"
                class="form-input"
                required
              >
            </div>

            <!-- End Date -->
            <div class="form-group">
              <label class="form-label">End Date *</label>
              <input
                v-model="formData.end_date"
                type="date"
                class="form-input"
                required
              >
            </div>

            <!-- Active Status -->
            <div class="form-group">
              <label class="form-checkbox">
                <input
                  v-model="formData.active"
                  type="checkbox"
                >
                <span>Active</span>
              </label>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 pt-4">
              <button
                type="submit"
                class="btn-primary flex-1"
              >
                {{ editingId ? 'Update' : 'Create' }}
              </button>
              <button
                type="button"
                class="btn-secondary flex-1"
                @click="closeModal"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, h } from 'vue'
import AdminHeader from '@/components/AdminHeader.vue'
import AdminQuickNav from '@/components/AdminQuickNav.vue'
import AdminSectionHeader from '@/components/admin/ui/AdminSectionHeader.vue'
import AdminStatsStrip from '@/components/admin/ui/AdminStatsStrip.vue'
import AdminFilterBar from '@/components/admin/ui/AdminFilterBar.vue'
import api from '../../../api'
import { formatDate, formatNumber } from '@/utils/formatters'

// Data
const showAddModal = ref(false)
const editingId = ref(null)
const searchQuery = ref('')
const filterStatus = ref('')
const filterPackage = ref('')
const filterCategory = ref('')
const photographerOptions = ref([])
const selectedPhotographer = ref({})

const stats = ref({
  total: 0,
  active: 0,
  revenue: 0,
  premium: 0
})

const TotalIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z' })
])

const ActiveIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
])

const RevenueIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
])

const PremiumIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M13 10V3L4 14h7v7l9-11h-7z' })
])

const featured = ref([])
const pagination = ref({
  page: 1,
  per_page: 15,
  total: 0,
  total_pages: 1
})

const formData = ref({
  photographer_id: null,
  photographer_search: '',
  package_tier: '',
  category: '',
  location: '',
  start_date: '',
  end_date: '',
  active: true
})

// Computed
const filteredFeatured = computed(() => featured.value)

const statItems = computed(() => [
  {
    label: 'Total Featured',
    value: stats.value.total,
    meta: 'All tiers',
    icon: TotalIcon,
    tone: 'neutral',
  },
  {
    label: 'Active Now',
    value: stats.value.active,
    meta: 'Live listings',
    icon: ActiveIcon,
    tone: 'success',
  },
  {
    label: 'Total Revenue',
    value: `৳${formatNumber(stats.value.revenue)}`,
    meta: 'Lifetime',
    icon: RevenueIcon,
    tone: 'info',
  },
  {
    label: 'Premium Tier',
    value: stats.value.premium,
    meta: 'Top package',
    icon: PremiumIcon,
    tone: 'warning',
  }
])

// Methods
const fetchData = async () => {
  try {
    const params = {
      search: searchQuery.value || undefined,
      status: filterStatus.value || undefined,
      package: filterPackage.value || undefined,
      category: filterCategory.value || undefined,
      page: pagination.value.page,
      per_page: pagination.value.per_page
    }
    const response = await api.get('/admin/featured-photographers', { params })
    featured.value = Array.isArray(response.data.data) ? response.data.data : []
    stats.value = response.data.stats || stats.value
    if (response.data.pagination) {
      pagination.value = {
        ...pagination.value,
        page: response.data.pagination.current_page,
        per_page: response.data.pagination.per_page,
        total: response.data.pagination.total,
        total_pages: response.data.pagination.last_page
      }
    }
  } catch (error) {
    console.error('Error fetching featured photographers:', error)
  }
}

const searchPhotographers = async () => {
  const query = formData.value.photographer_search.trim()
  if (query.length > 0 && query.length < 2) {
    photographerOptions.value = []
    return
  }
  
  try {
    const response = await api.get('/admin/photographers/search', {
      params: { q: query }
    })
    photographerOptions.value = response.data
  } catch (error) {
    console.error('Error searching photographers:', error)
  }
}

const selectPhotographer = (photo) => {
  formData.value.photographer_id = photo.id
  selectedPhotographer.value = photo
  photographerOptions.value = []
}

const saveFeatured = async () => {
  if (!formData.value.photographer_id || !formData.value.package_tier) {
    alert('Please fill in all required fields')
    return
  }

  try {
    const url = editingId.value 
      ? `/admin/featured-photographers/${editingId.value}` 
      : '/admin/featured-photographers'
    
    const method = editingId.value ? 'put' : 'post'
    
    await api[method](url, formData.value)
    
    closeModal()
    fetchData()
  } catch (error) {
    console.error('Error saving featured photographer:', error)
    alert('Error saving featured photographer')
  }
}

const editFeatured = (item) => {
  editingId.value = item.id
  formData.value = {
    photographer_id: item.photographer_id,
    photographer_search: item.photographer?.user?.name || '',
    package_tier: item.package_tier,
    category: item.category || '',
    location: item.location || '',
    start_date: item.start_date,
    end_date: item.end_date,
    active: item.active
  }
  selectedPhotographer.value = item.photographer || {}
  showAddModal.value = true
}

const toggleStatus = async (item) => {
  try {
    await api.patch(`/admin/featured-photographers/${item.id}/toggle`)
    fetchData()
  } catch (error) {
    console.error('Error toggling status:', error)
  }
}

const deleteFeatured = async (id) => {
  if (!confirm('Are you sure you want to delete this featured listing?')) return
  
  try {
    await api.delete(`/admin/featured-photographers/${id}`)
    fetchData()
  } catch (error) {
    console.error('Error deleting featured photographer:', error)
  }
}

const closeModal = () => {
  showAddModal.value = false
  editingId.value = null
  formData.value = {
    photographer_id: null,
    photographer_search: '',
    package_tier: '',
    category: '',
    location: '',
    start_date: '',
    end_date: '',
    active: true
  }
  selectedPhotographer.value = {}
  photographerOptions.value = []
}

const applyFilters = () => {
  pagination.value.page = 1
  fetchData()
}

const changePage = (page) => {
  pagination.value.page = page
  fetchData()
}

const getStatus = (item) => {
  if (!item.active) return 'Inactive'
  const now = new Date()
  const endDate = new Date(item.end_date)
  return endDate < now ? 'Expired' : 'Active'
}

const getStatusClass = (item) => {
  const status = getStatus(item)
  return status === 'Active' ? 'status-active' : status === 'Expired' ? 'status-warning' : 'status-inactive'
}

const getPackageClass = (tier) => {
  const classes = {
    'Starter': 'package-starter',
    'Professional': 'package-pro',
    'Enterprise': 'package-enterprise'
  }
  return classes[tier] || ''
}

let searchTimeout = null
watch([filterStatus, filterPackage, filterCategory], applyFilters)
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 400)
})

// Lifecycle
fetchData()
</script>

<style scoped>
/* Reuse admin styles */
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
  z-index: 50;
}

.modal-content {
  background: white;
  border-radius: 0.5rem;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
  position: relative;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
}

.form-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.form-checkbox input {
  cursor: pointer;
}

.dropdown-list {
  position: absolute;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  max-height: 200px;
  overflow-y: auto;
  z-index: 60;
  width: calc(100% - 2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  padding: 0.5rem;
  cursor: pointer;
  border-bottom: 1px solid #e5e7eb;
}

.dropdown-item:hover {
  background: #f3f4f6;
}

.package-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
}

.package-starter {
  background: #dbeafe;
  color: #1e40af;
}

.package-pro {
  background: #fed7aa;
  color: #92400e;
}

.package-enterprise {
  background: #fce7f3;
  color: #831843;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
}

.status-active {
  background: #dcfce7;
  color: #166534;
}

.status-warning {
  background: #fed7aa;
  color: #92400e;
}

.status-inactive {
  background: #f3f4f6;
  color: #6b7280;
}

.user-cell {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  padding: 0.5rem;
  border: none;
  border-radius: 0.375rem;
  background: #e5e7eb;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-icon:hover {
  background: #d1d5db;
}

.btn-edit {
  background: #e0e7ff;
  color: #3b82f6;
}

.btn-edit:hover {
  background: #c7d2fe;
}

.btn-success {
  background: #dcfce7;
  color: #16a34a;
}

.btn-success:hover {
  background: #bbf7d0;
}

.btn-warning {
  background: #fed7aa;
  color: #ea580c;
}

.btn-warning:hover {
  background: #fdba74;
}

.btn-danger {
  background: #fee2e2;
  color: #dc2626;
}

.btn-danger:hover {
  background: #fecaca;
}

.content-card {
  background: white;
  border-radius: 0.5rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.search-box {
  flex: 1;
  min-width: 250px;
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  width: 1.25rem;
  height: 1.25rem;
  color: #9ca3af;
}

.search-input {
  width: 100%;
  padding: 0.5rem 0.75rem 0.5rem 2.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
}

.filter-group {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.filter-select {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background: white;
  cursor: pointer;
}

.table-container {
  overflow-x: auto;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
}

.admin-table thead {
  background: #f9fafb;
  border-bottom: 2px solid #e5e7eb;
}

.admin-table th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
}

.table-row {
  border-bottom: 1px solid #e5e7eb;
}

.table-row:hover {
  background: #f9fafb;
}

.admin-table td {
  padding: 1rem;
  vertical-align: middle;
}

.table-empty {
  background: #f9fafb;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.5rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  padding: 0.5rem 1.5rem;
  background: #e5e7eb;
  color: #374151;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-secondary:hover {
  background: #d1d5db;
}
</style>
