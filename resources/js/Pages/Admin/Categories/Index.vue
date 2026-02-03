<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader title="🏷️ Categories" subtitle="Manage photography categories" />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <!-- Stats Section -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-value">{{ categories.length }}</div>
          <div class="stat-label">Current Categories</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ MAX_CATEGORIES }}</div>
          <div class="stat-label">Maximum Allowed</div>
        </div>
        <div class="stat-card" :class="{ 'stat-warning': categories.length >= MAX_CATEGORIES }">
          <div class="stat-value">{{ MAX_CATEGORIES - categories.length }}</div>
          <div class="stat-label">Remaining Slots</div>
        </div>
        <div class="stat-card">
          <div class="stat-progress">
            <div class="progress-bar" :style="{ width: (categories.length / MAX_CATEGORIES * 100) + '%' }"></div>
          </div>
          <div class="stat-label">Capacity Used</div>
          <div class="stat-percentage">{{ Math.round(categories.length / MAX_CATEGORIES * 100) }}%</div>
        </div>
      </div>

      <div class="content-card">
        <div class="filters-bar">
          <div class="search-box">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input v-model="filters.search" @input="debounceSearch" type="text" placeholder="Search categories..." class="search-input" />
          </div>

          <button @click="openCreate" class="btn-add">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Category
          </button>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading categories...</p>
        </div>

        <div v-else-if="categories.length > 0" class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Icon</th>
                <th>Display Order</th>
                <th>Active</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in categories" :key="category.id">
                <td>{{ category.name }}</td>
                <td>{{ category.slug }}</td>
                <td>{{ category.icon || '—' }}</td>
                <td>{{ category.display_order }}</td>
                <td>{{ category.is_active ? 'Yes' : 'No' }}</td>
                <td class="actions">
                  <button @click="openEdit(category.id)" class="btn-action btn-edit">Edit</button>
                  <button @click="deleteCategory(category.id)" class="btn-action btn-delete">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">🏷️</div>
          <p class="empty-title">No categories found</p>
        </div>

        <div v-if="meta.total > 0" class="pagination">
          <div class="pagination-info">Showing {{ categories.length }} of {{ meta.total }} categories</div>
          <div class="pagination-controls">
            <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="pagination-btn">Previous</button>
            <span class="pagination-current">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
            <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="pagination-btn">Next</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>{{ editingId ? 'Edit Category' : 'Add Category' }}</h3>
          <button @click="closeModal" class="modal-close">×</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveCategory" class="edit-form">
            <div class="form-row">
              <div class="form-group">
                <label>Name *</label>
                <input v-model="form.name" type="text" class="form-input" required />
              </div>
              <div class="form-group">
                <label>Slug *</label>
                <input v-model="form.slug" type="text" class="form-input" required />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Icon</label>
                <input v-model="form.icon" type="text" class="form-input" placeholder="e.g. 📷" />
              </div>
              <div class="form-group">
                <label>Display Order</label>
                <input v-model.number="form.display_order" type="number" class="form-input" />
              </div>
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea v-model="form.description" rows="3" class="form-input"></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="checkbox-label">
                  <input v-model="form.is_active" type="checkbox" />
                  <span>Active</span>
                </label>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeModal" class="btn-cancel">Cancel</button>
              <button type="submit" class="btn-save" :disabled="saving">
                {{ saving ? 'Saving...' : (editingId ? 'Update' : 'Create') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div v-if="showToast" class="toast">{{ toastMessage }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

const MAX_CATEGORIES = 50 // Maximum allowed categories

const categories = ref([])
const loading = ref(false)
const showModal = ref(false)
const editingId = ref(null)
const saving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({ search: '' })
const meta = ref({ total: 0, current_page: 1, last_page: 1 })

const form = ref({ name: '', slug: '', description: '', icon: '', display_order: 0, is_active: true })

let searchTimeout = null
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchCategories(), 400)
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, 3000)
}

const fetchCategories = async (page = 1) => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const params = new URLSearchParams()
    if (filters.value.search) params.append('search', filters.value.search)
    params.append('page', page)

    const response = await fetch(`/api/v1/admin/categories?${params}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      categories.value = data.data.data || data.data
      meta.value = {
        total: data.data.total || categories.value.length,
        current_page: data.data.current_page || 1,
        last_page: data.data.last_page || 1
      }
    }
  } catch (error) {
    console.error('Error fetching categories', error)
    showToastMessage('Error loading categories')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  form.value = { name: '', slug: '', description: '', icon: '', display_order: 0, is_active: true }
  showModal.value = true
}

const openEdit = async (id) => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/categories/${id}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      const category = data.data
      editingId.value = id
      form.value = {
        name: category.name,
        slug: category.slug,
        description: category.description || '',
        icon: category.icon || '',
        display_order: category.display_order || 0,
        is_active: !!category.is_active
      }
      showModal.value = true
    }
  } catch (error) {
    console.error('Error loading category', error)
    showToastMessage('Error loading category')
  }
}

const saveCategory = async () => {
  saving.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const url = editingId.value ? `/api/v1/admin/categories/${editingId.value}` : '/api/v1/admin/categories'
    const method = editingId.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(form.value)
    })

    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage(editingId.value ? 'Category updated' : 'Category created')
      showModal.value = false
      fetchCategories(meta.value.current_page)
    } else {
      showToastMessage(data.message || 'Error saving category')
    }
  } catch (error) {
    console.error('Error saving category', error)
    showToastMessage('Error saving category')
  } finally {
    saving.value = false
  }
}

const deleteCategory = async (id) => {
  if (!confirm('Delete this category?')) return
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/categories/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage('Category deleted')
      fetchCategories(meta.value.current_page)
    }
  } catch (error) {
    console.error('Error deleting category', error)
    showToastMessage('Error deleting category')
  }
}

const closeModal = () => { showModal.value = false }

const changePage = (page) => {
  if (page >= 1 && page <= meta.value.last_page) fetchCategories(page)
}

onMounted(() => {
  fetchCategories()
})
</script>

<style scoped>
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; }
.stat-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; border-left: 4px solid var(--admin-brand-primary); }
.stat-card.stat-warning { border-left-color: #f59e0b; }
.stat-value { font-size: 1.875rem; font-weight: 700; color: var(--admin-brand-primary); }
.stat-card.stat-warning .stat-value { color: #f59e0b; }
.stat-label { font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem; }
.stat-percentage { font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem; }
.stat-progress { width: 100%; height: 8px; background: #e5e7eb; border-radius: 9999px; overflow: hidden; margin: 0.75rem 0; }
.progress-bar { height: 100%; background: linear-gradient(to right, var(--admin-brand-primary), #0ea5e9); border-radius: 9999px; transition: width 0.3s ease; }

.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.filters-bar { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin-bottom: 1.5rem; }
.search-box { position: relative; flex: 1; min-width: 220px; }
.search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; width: 1.25rem; height: 1.25rem; }
.search-input { width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; }
.btn-add { display: inline-flex; align-items: center; background: var(--admin-brand-primary); color: white; padding: 0.625rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 600; }
.table-wrapper { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 0.75rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
.actions { display: flex; gap: 0.5rem; }
.btn-action { padding: 0.35rem 0.6rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
.btn-edit { color: var(--admin-brand-primary); }
.btn-delete { color: var(--admin-danger); }
.empty-state { text-align: center; padding: 2rem 0; }
.empty-icon { font-size: 2rem; }
.loading-state { display: flex; flex-direction: column; align-items: center; padding: 2rem 0; }
.spinner { width: 2rem; height: 2rem; border: 3px solid #e5e7eb; border-top: 3px solid var(--admin-brand-primary); border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal { background: white; border-radius: 1rem; width: 100%; max-width: 720px; overflow: hidden; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
.modal-body { padding: 1.5rem; }
.edit-form { display: flex; flex-direction: column; gap: 1rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.5rem; }
.form-input { padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; }
.checkbox-label { display: flex; align-items: center; gap: 0.5rem; }
.modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; }
.btn-cancel { padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white; }
.btn-save { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; background: var(--admin-brand-primary); color: white; }
.toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--admin-success-dark); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; z-index: 1001; }
.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
.pagination-btn { padding: 0.4rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
</style>
