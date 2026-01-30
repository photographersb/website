<template>
  <div class="admin-photographers">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">📷 Photographer Management</h1>
        <p class="page-subtitle">Manage photographer profiles, verifications, and portfolios</p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card stat-blue">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Photographers</span>
          <span class="stat-value">{{ stats.total }}</span>
        </div>
      </div>

      <div class="stat-card stat-green">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Verified</span>
          <span class="stat-value">{{ stats.verified }}</span>
        </div>
      </div>

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

      <div class="stat-card stat-purple">
        <div class="stat-icon">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
          </svg>
        </div>
        <div class="stat-content">
          <span class="stat-label">Avg Rating</span>
          <span class="stat-value">{{ stats.avgRating }}</span>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="content-card">
      <div class="filters-bar">
        <div class="search-box">
          <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input 
            v-model="filters.search" 
            @input="debounceSearch"
            type="text" 
            placeholder="Search photographers by name..." 
            class="search-input"
          />
        </div>

        <select v-model="filters.verification" @change="fetchPhotographers" class="filter-select">
          <option value="">All Status</option>
          <option value="verified">Verified</option>
          <option value="pending">Pending</option>
          <option value="unverified">Unverified</option>
        </select>

        <select v-model="filters.rating" @change="fetchPhotographers" class="filter-select">
          <option value="">All Ratings</option>
          <option value="4.5">4.5+ Stars</option>
          <option value="4">4+ Stars</option>
          <option value="3">3+ Stars</option>
        </select>

        <button @click="exportPhotographers" class="btn-export">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Loading photographers...</p>
      </div>

      <!-- Photographers Grid -->
      <div v-else-if="photographers.length > 0" class="photographers-grid">
        <div v-for="photographer in photographers" :key="photographer.id" class="photographer-card">
          <div class="card-header-section">
            <div class="photographer-avatar">
              <img v-if="photographer.profile_picture" :src="photographer.profile_picture" :alt="photographer.business_name" />
              <span v-else>{{ photographer.business_name?.charAt(0).toUpperCase() || '📷' }}</span>
            </div>
            <span v-if="photographer.is_verified" class="verified-badge">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>

          <h3 class="photographer-name">{{ photographer.business_name }}</h3>
          <p class="photographer-user">{{ photographer.user?.name }}</p>
          
          <div class="photographer-categories">
            <span v-for="category in photographer.categories?.slice(0, 2)" :key="category.id" class="category-tag">
              {{ category.name }}
            </span>
          </div>

          <div class="photographer-stats">
            <div class="stat-item">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <span>{{ photographer.average_rating || 'N/A' }}</span>
            </div>
            <div class="stat-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
              </svg>
              <span>{{ photographer.total_reviews || 0 }} reviews</span>
            </div>
          </div>

          <div class="photographer-location">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>{{ photographer.city?.name || 'Not specified' }}</span>
          </div>

          <div class="card-actions">
            <button @click="viewPhotographer(photographer)" class="btn-action btn-view">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              View Profile
            </button>
            <button v-if="!photographer.is_verified" @click="verifyPhotographer(photographer)" class="btn-action btn-verify">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Verify
            </button>
            <button @click="editPhotographer(photographer)" class="btn-action btn-edit">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Edit
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">📷</div>
        <p class="empty-title">No photographers found</p>
        <p class="empty-subtitle">Try adjusting your filters</p>
      </div>

      <!-- Pagination -->
      <div v-if="meta.total > 0" class="pagination">
        <div class="pagination-info">
          Showing {{ photographers.length }} of {{ meta.total }} photographers
        </div>
        <div class="pagination-controls">
          <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="pagination-btn">
            Previous
          </button>
          <span class="pagination-current">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
          <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="pagination-btn">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal" class="modal-overlay" @click.self="showViewModal = false">
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>Photographer Details</h3>
          <button @click="showViewModal = false" class="modal-close">×</button>
        </div>
        <div class="modal-body" v-if="selectedPhotographer">
          <div class="profile-section">
            <div class="profile-avatar-large">
              <img v-if="selectedPhotographer.profile_picture" :src="selectedPhotographer.profile_picture" :alt="selectedPhotographer.business_name" />
              <span v-else>{{ selectedPhotographer.business_name?.charAt(0).toUpperCase() }}</span>
            </div>
            <div class="profile-info">
              <h2>{{ selectedPhotographer.business_name }}</h2>
              <p class="text-gray">{{ selectedPhotographer.user?.email }}</p>
              <div class="badge-group">
                <span v-if="selectedPhotographer.is_verified" class="badge badge-success">✓ Verified</span>
                <span v-else class="badge badge-warning">Pending Verification</span>
                <span v-if="selectedPhotographer.is_featured" class="badge badge-purple">⭐ Featured</span>
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
              <span class="detail-value">{{ selectedPhotographer.city?.name }}, {{ selectedPhotographer.city?.division }}</span>
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
              <span v-for="category in selectedPhotographer.categories" :key="category.id" class="category-tag">
                {{ category.name }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="showToast" class="toast">
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const photographers = ref([])
const loading = ref(false)
const showViewModal = ref(false)
const selectedPhotographer = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({
  search: '',
  verification: '',
  rating: ''
})

const meta = ref({
  total: 0,
  current_page: 1,
  last_page: 1,
  per_page: 30
})

const stats = computed(() => {
  const verified = photographers.value.filter(p => p.is_verified).length
  const pending = photographers.value.filter(p => !p.is_verified).length
  const avgRating = photographers.value.length > 0 
    ? (photographers.value.reduce((sum, p) => sum + (parseFloat(p.average_rating) || 0), 0) / photographers.value.length).toFixed(1)
    : '0.0'
  
  return {
    total: meta.value.total,
    verified,
    pending,
    avgRating
  }
})

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchPhotographers()
  }, 500)
}

const fetchPhotographers = async (page = 1) => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const params = new URLSearchParams()
    
    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.rating) params.append('rating', filters.value.rating)
    params.append('page', page)

    // For verification filter, we'll filter client-side since the API doesn't have this filter
    const response = await fetch(`/api/v1/photographers?${params}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    const data = await response.json()
    
    if (data.status === 'success') {
      let items = data.data
      
      // Apply verification filter client-side
      if (filters.value.verification === 'verified') {
        items = items.filter(p => p.is_verified)
      } else if (filters.value.verification === 'pending' || filters.value.verification === 'unverified') {
        items = items.filter(p => !p.is_verified)
      }
      
      photographers.value = items
      meta.value = {
        total: data.meta?.total || items.length,
        current_page: data.meta?.current_page || 1,
        last_page: data.meta?.last_page || 1,
        per_page: data.meta?.per_page || 30
      }
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

const verifyPhotographer = (photographer) => {
  if (confirm(`Verify ${photographer.business_name}?`)) {
    photographer.is_verified = true
    showToastMessage(`${photographer.business_name} verified successfully`)
  }
}

const editPhotographer = (photographer) => {
  showToastMessage('Edit feature coming soon')
}

const exportPhotographers = () => {
  showToastMessage('Export feature coming soon')
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
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

.stat-blue { border-color: #3b82f6; }
.stat-green { border-color: #10b981; }
.stat-yellow { border-color: #f59e0b; }
.stat-purple { border-color: #8b5cf6; }

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-blue .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-green .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-yellow .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stat-purple .stat-icon { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

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
  border-color: #6c0b1a;
  box-shadow: 0 0 0 3px rgba(108, 11, 26, 0.1);
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
  border-color: #6c0b1a;
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
  border-color: #6c0b1a;
  color: #6c0b1a;
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
  border-top-color: #6c0b1a;
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
  background: linear-gradient(135deg, #6c0b1a, #9d1429);
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
  background: #6c0b1a;
  border-color: #6c0b1a;
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
  border-color: #6c0b1a;
  color: #6c0b1a;
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
  background: linear-gradient(135deg, #6c0b1a, #9d1429);
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
</style>
