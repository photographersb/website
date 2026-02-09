<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Header -->
    <AdminHeader 
      title="💼 Platform Sponsors" 
      subtitle="Manage sponsor partnerships and sponsorship packages"
      :show-back="true"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Quick Navigation -->
      <AdminQuickNav />

      <!-- Stats Cards -->
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
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Sponsors</span>
            <span class="stat-value">{{ stats.total }}</span>
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
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Active</span>
            <span class="stat-value">{{ stats.active }}</span>
          </div>
        </div>

        <div class="stat-card stat-yellow">
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
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Inactive</span>
            <span class="stat-value">{{ stats.inactive }}</span>
          </div>
        </div>

        <div class="stat-card stat-purple">
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
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-content">
            <span class="stat-label">Featured</span>
            <span class="stat-value">{{ stats.featured }}</span>
          </div>
        </div>
      </div>

      <!-- Filters & Actions -->
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
              v-model="searchQuery" 
              type="text" 
              placeholder="Search sponsors..." 
              class="search-input"
            >
          </div>

          <select
            v-model="filterStatus"
            class="filter-select"
            @change="applyFilters"
          >
            <option value="">
              All Status
            </option>
            <option value="active">
              Active
            </option>
            <option value="inactive">
              Inactive
            </option>
          </select>

          <button
            class="btn-add"
            @click="showCreateModal = true"
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
            Add Sponsor
          </button>
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading sponsors...</p>
        </div>

        <!-- Sponsors Grid -->
        <div
          v-else-if="filteredSponsors.length > 0"
          class="sponsors-grid"
        >
          <div
            v-for="sponsor in filteredSponsors"
            :key="sponsor.id"
            class="sponsor-card"
          >
            <div class="sponsor-logo-container">
              <img
                v-if="sponsor.logo"
                :src="sponsor.logo"
                :alt="sponsor.name"
                class="sponsor-logo"
              >
              <div
                v-else
                class="sponsor-logo-placeholder"
              >
                <svg
                  class="w-12 h-12"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                  />
                </svg>
              </div>
              <span
                v-if="sponsor.is_featured"
                class="featured-badge"
              >
                <svg
                  class="w-4 h-4"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </span>
            </div>

            <h3 class="sponsor-name">
              {{ sponsor.name }}
            </h3>
            
            <div class="sponsor-meta">
              <span
                :class="getStatusClass(sponsor.status)"
                class="status-badge"
              >
                {{ sponsor.status }}
              </span>
              <span class="order-badge">Order: {{ sponsor.display_order || 'N/A' }}</span>
            </div>

            <p
              v-if="sponsor.description"
              class="sponsor-description"
            >
              {{ truncateText(sponsor.description, 100) }}
            </p>
            <p
              v-else
              class="sponsor-description text-gray-400"
            >
              No description provided
            </p>

            <div
              v-if="sponsor.website"
              class="sponsor-website"
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
                  d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"
                />
              </svg>
              <a
                :href="sponsor.website"
                target="_blank"
                class="website-link"
              >
                {{ formatWebsite(sponsor.website) }}
              </a>
            </div>

            <div class="card-actions">
              <button
                class="btn-action btn-edit"
                @click="editSponsor(sponsor)"
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
                Edit
              </button>
              <button
                class="btn-action btn-feature"
                @click="toggleFeatured(sponsor)"
              >
                <svg
                  class="w-4 h-4"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                {{ sponsor.is_featured ? 'Unfeature' : 'Feature' }}
              </button>
              <button
                class="btn-action btn-delete"
                @click="deleteSponsor(sponsor.id)"
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
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            💼
          </div>
          <p class="empty-title">
            No sponsors found
          </p>
          <p class="empty-subtitle">
            Add your first sponsor to get started
          </p>
          <button
            class="btn-add-empty"
            @click="showCreateModal = true"
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
            Add First Sponsor
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showCreateModal || showEditModal"
      class="modal-overlay"
      @click.self="closeModal"
    >
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>{{ editingId ? 'Edit Sponsor' : 'Add New Sponsor' }}</h3>
          <button
            class="modal-close"
            @click="closeModal"
          >
            &times;
          </button>
        </div>
        <div class="modal-body">
          <form
            class="form-grid"
            @submit.prevent="saveSponsor"
          >
            <!-- Name -->
            <div class="form-group">
              <label class="form-label">Sponsor Name *</label>
              <input 
                v-model="formData.name" 
                type="text" 
                required 
                class="form-input"
                placeholder="Enter sponsor name"
              >
            </div>

            <!-- Logo Upload -->
            <div class="form-group">
              <label class="form-label">Logo</label>
              <div class="logo-upload-section">
                <div
                  v-if="formData.logo"
                  class="logo-preview"
                >
                  <img
                    :src="formData.logo"
                    alt="Logo"
                  >
                  <button
                    type="button"
                    class="remove-logo-btn"
                    @click="formData.logo = ''"
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
                        d="M6 18L18 6M6 6l12 12"
                      />
                    </svg>
                  </button>
                </div>
                <input 
                  type="file" 
                  accept="image/*" 
                  class="file-input upload-input" 
                  @change="handleLogoUpload"
                >
                <p class="file-hint">
                  PNG, JPG up to 5MB. 600x300 px.
                </p>
              </div>
            </div>

            <!-- Website -->
            <div class="form-group">
              <label class="form-label">Website URL</label>
              <input 
                v-model="formData.website" 
                type="url" 
                class="form-input"
                placeholder="https://example.com"
              >
            </div>

            <!-- Description -->
            <div class="form-group full-width">
              <label class="form-label">Description</label>
              <textarea 
                v-model="formData.description" 
                rows="4" 
                class="form-input"
                placeholder="Enter sponsor description..."
              />
            </div>

            <!-- Status -->
            <div class="form-group">
              <label class="form-label">Status</label>
              <select
                v-model="formData.status"
                class="form-input"
              >
                <option value="active">
                  Active
                </option>
                <option value="inactive">
                  Inactive
                </option>
              </select>
            </div>

            <!-- Display Order -->
            <div class="form-group">
              <label class="form-label">Display Order</label>
              <input 
                v-model.number="formData.display_order" 
                type="number" 
                class="form-input"
                placeholder="1, 2, 3..."
              >
            </div>

            <!-- Is Featured -->
            <div class="form-group checkbox-group full-width">
              <label class="checkbox-label">
                <input 
                  v-model="formData.is_featured" 
                  type="checkbox" 
                  class="checkbox-input"
                >
                <span>Feature this sponsor (show prominently on homepage)</span>
              </label>
            </div>

            <!-- Form Actions -->
            <div class="modal-actions full-width">
              <button
                type="button"
                class="btn-cancel"
                @click="closeModal"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn-save"
                :disabled="saving"
              >
                <svg
                  v-if="saving"
                  class="w-5 h-5 mr-2 animate-spin"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
                <span>{{ saving ? 'Saving...' : (editingId ? 'Update Sponsor' : 'Add Sponsor') }}</span>
              </button>
            </div>
          </form>
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminHeader from './AdminHeader.vue'
import AdminQuickNav from './AdminQuickNav.vue'

const sponsors = ref([])
const loading = ref(false)
const saving = ref(false)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingId = ref(null)
const showToast = ref(false)
const toastMessage = ref('')

const searchQuery = ref('')
const filterStatus = ref('')

const formData = ref({
  name: '',
  logo: '',
  website: '',
  description: '',
  status: 'active',
  display_order: null,
  is_featured: false
})

const stats = computed(() => {
  const active = sponsors.value.filter(s => s.status === 'active').length
  const inactive = sponsors.value.filter(s => s.status === 'inactive').length
  const featured = sponsors.value.filter(s => s.is_featured).length
  
  return {
    total: sponsors.value.length,
    active,
    inactive,
    featured
  }
})

const filteredSponsors = computed(() => {
  let filtered = sponsors.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(s => 
      s.name.toLowerCase().includes(query) || 
      s.description?.toLowerCase().includes(query)
    )
  }

  if (filterStatus.value) {
    filtered = filtered.filter(s => s.status === filterStatus.value)
  }

  return filtered.sort((a, b) => (a.display_order || 999) - (b.display_order || 999))
})

onMounted(() => {
  fetchSponsors()
})

const fetchSponsors = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('/api/v1/admin/platform-sponsors', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      sponsors.value = data.data || []
    } else {
      showToastMessage('Error loading sponsors')
    }
  } catch (error) {
    console.error('Error fetching sponsors:', error)
    showToastMessage('Error loading sponsors')
  } finally {
    loading.value = false
  }
}

const editSponsor = (sponsor) => {
  editingId.value = sponsor.id
  formData.value = {
    name: sponsor.name,
    logo: sponsor.logo || '',
    website: sponsor.website || '',
    description: sponsor.description || '',
    status: sponsor.status,
    display_order: sponsor.display_order,
    is_featured: sponsor.is_featured || false
  }
  showEditModal.value = true
}

const saveSponsor = async () => {
  saving.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const url = editingId.value 
      ? `/api/v1/admin/platform-sponsors/${editingId.value}`
      : '/api/v1/admin/platform-sponsors'
    
    const response = await fetch(url, {
      method: editingId.value ? 'PUT' : 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formData.value)
    })
    
    if (response.ok) {
      showToastMessage(editingId.value ? 'Sponsor updated successfully' : 'Sponsor added successfully')
      closeModal()
      fetchSponsors()
    } else {
      showToastMessage('Error saving sponsor')
    }
  } catch (error) {
    console.error('Error saving sponsor:', error)
    showToastMessage('Error saving sponsor')
  } finally {
    saving.value = false
  }
}

const deleteSponsor = async (id) => {
  if (!confirm('Are you sure you want to delete this sponsor?')) return
  
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/platform-sponsors/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    
    if (response.ok) {
      showToastMessage('Sponsor deleted successfully')
      fetchSponsors()
    } else {
      showToastMessage('Error deleting sponsor')
    }
  } catch (error) {
    console.error('Error deleting sponsor:', error)
    showToastMessage('Error deleting sponsor')
  }
}

const toggleFeatured = async (sponsor) => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/platform-sponsors/${sponsor.id}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        ...sponsor,
        is_featured: !sponsor.is_featured
      })
    })
    
    if (response.ok) {
      showToastMessage(sponsor.is_featured ? 'Sponsor unfeatured' : 'Sponsor featured')
      fetchSponsors()
    }
  } catch (error) {
    console.error('Error toggling featured:', error)
  }
}

const handleLogoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      formData.value.logo = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingId.value = null
  formData.value = {
    name: '',
    logo: '',
    website: '',
    description: '',
    status: 'active',
    display_order: null,
    is_featured: false
  }
}

const applyFilters = () => {
  // Computed property will automatically handle this
}

const getStatusClass = (status) => {
  return status === 'active' ? 'badge-success' : 'badge-gray'
}

const formatWebsite = (url) => {
  try {
    return new URL(url).hostname.replace('www.', '')
  } catch {
    return url
  }
}

const truncateText = (text, length) => {
  return text?.length > length ? text.substring(0, length) + '...' : text
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}
</script>

<style scoped>
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

.btn-add {
  display: flex;
  align-items: center;
  background: #6c0b1a;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-add:hover {
  background: #9d1429;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(108, 11, 26, 0.3);
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

.sponsors-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.sponsor-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 1.5rem;
  transition: all 0.2s;
  display: flex;
  flex-direction: column;
}

.sponsor-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.sponsor-logo-container {
  position: relative;
  width: 100%;
  height: 120px;
  background: #f9fafb;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  overflow: hidden;
}

.sponsor-logo {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  padding: 1rem;
}

.sponsor-logo-placeholder {
  color: #d1d5db;
}

.featured-badge {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: #fbbf24;
  color: white;
  padding: 0.375rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(251, 191, 36, 0.3);
}

.sponsor-name {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.75rem 0;
}

.sponsor-meta {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.status-badge, .order-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-success { background: #d1fae5; color: #065f46; }
.badge-gray { background: #f3f4f6; color: #4b5563; }

.order-badge {
  background: #e0e7ff;
  color: #4338ca;
}

.sponsor-description {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.6;
  margin: 0 0 1rem 0;
  flex-grow: 1;
}

.sponsor-website {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 1rem;
  padding: 0.5rem;
  background: #f9fafb;
  border-radius: 0.375rem;
}

.sponsor-website svg {
  color: #9ca3af;
  flex-shrink: 0;
}

.website-link {
  color: #3b82f6;
  text-decoration: none;
  word-break: break-all;
}

.website-link:hover {
  text-decoration: underline;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn-action {
  flex: 1;
  min-width: max-content;
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

.btn-edit:hover {
  background: #6c0b1a;
  border-color: #6c0b1a;
  color: white;
}

.btn-feature {
  background: #fef3c7;
  border-color: #fbbf24;
  color: #92400e;
}

.btn-feature:hover {
  background: #fbbf24;
  color: white;
}

.btn-delete:hover {
  background: #fee2e2;
  border-color: #ef4444;
  color: #dc2626;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
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
  margin-bottom: 1.5rem;
}

.btn-add-empty {
  display: inline-flex;
  align-items: center;
  background: #6c0b1a;
  color: white;
  padding: 0.875rem 1.75rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-add-empty:hover {
  background: #9d1429;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(108, 11, 26, 0.3);
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
  max-width: 800px;
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
  position: sticky;
  top: 0;
  background: white;
  z-index: 10;
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

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: span 2;
}

.form-label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.form-input {
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #6c0b1a;
  box-shadow: 0 0 0 3px rgba(108, 11, 26, 0.1);
}

.logo-upload-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.logo-preview {
  position: relative;
  width: 200px;
  height: 100px;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  overflow: hidden;
  background: #f9fafb;
}

.logo-preview img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 0.5rem;
}

.remove-logo-btn {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: #ef4444;
  color: white;
  border: none;
  padding: 0.375rem;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s;
}

.remove-logo-btn:hover {
  background: #dc2626;
}

.file-input {
  font-size: 0.875rem;
}

.file-hint {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.checkbox-group {
  padding: 1rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
}

.checkbox-input {
  width: 1.25rem;
  height: 1.25rem;
  cursor: pointer;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-cancel {
  padding: 0.75rem 1.5rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background: #f9fafb;
}

.btn-save {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  background: #6c0b1a;
  color: white;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-save:hover:not(:disabled) {
  background: #9d1429;
}

.btn-save:disabled {
  opacity: 0.5;
  cursor: not-allowed;
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
.w-12 { width: 3rem; }
.h-12 { height: 3rem; }
.mr-2 { margin-right: 0.5rem; }
</style>
