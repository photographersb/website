<template>
  <div class="min-h-screen">
    <AdminHeader
      title="🏷️ Categories"
      subtitle="Manage photography categories"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">CATEGORY CONTROL</p>
          <h1 class="hero-title">Taxonomy, balanced and on brand.</h1>
          <p class="hero-subtitle">
            Shape discoverability and keep the marketplace organized.
          </p>
          <div class="hero-actions">
            <button class="btn-admin-primary" @click="openCreate">
              Add Category
            </button>
            <button class="btn-admin-secondary" @click="fetchCategories">
              Refresh List
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Current</span>
            <span class="status-value">{{ categories.length }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Max Allowed</span>
            <span class="status-value">{{ MAX_CATEGORIES }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Remaining</span>
            <span class="status-value">{{ MAX_CATEGORIES - categories.length }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Capacity used: {{ Math.round(categories.length / MAX_CATEGORIES * 100) }}%
        </div>
      </div>

      <!-- Stats Section -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-value">
            {{ categories.length }}
          </div>
          <div class="stat-label">
            Current Categories
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-value">
            {{ MAX_CATEGORIES }}
          </div>
          <div class="stat-label">
            Maximum Allowed
          </div>
        </div>
        <div
          class="stat-card"
          :class="{ 'stat-warning': categories.length >= MAX_CATEGORIES }"
        >
          <div class="stat-value">
            {{ MAX_CATEGORIES - categories.length }}
          </div>
          <div class="stat-label">
            Remaining Slots
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-progress">
            <div
              class="progress-bar"
              :style="{ width: (categories.length / MAX_CATEGORIES * 100) + '%' }"
            />
          </div>
          <div class="stat-label">
            Capacity Used
          </div>
          <div class="stat-percentage">
            {{ Math.round(categories.length / MAX_CATEGORIES * 100) }}%
          </div>
        </div>
      </div>

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
              placeholder="Search categories..."
              class="search-input"
              @input="debounceSearch"
            >
          </div>

        </div>

        <div
          v-if="loading"
          class="loading-state"
        >
          <div class="spinner" />
          <p>Loading categories...</p>
        </div>

        <div
          v-else-if="categories.length > 0"
          class="table-wrapper"
        >
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Icon</th>
                <th>Display Order</th>
                <th>Active</th>
                <th />
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="category in categories"
                :key="category.id"
              >
                <td>{{ category.name }}</td>
                <td>{{ category.slug }}</td>
                <td>{{ category.icon || '—' }}</td>
                <td>{{ category.display_order }}</td>
                <td>{{ category.is_active ? 'Yes' : 'No' }}</td>
                <td class="actions">
                  <button
                    class="btn-action btn-edit"
                    @click="openEdit(category.id)"
                  >
                    Edit
                  </button>
                  <button
                    class="btn-action btn-delete"
                    @click="deleteCategory(category.id)"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div
          v-else
          class="empty-state"
        >
          <div class="empty-icon">
            🏷️
          </div>
          <p class="empty-title">
            No categories found
          </p>
        </div>

        <div
          v-if="meta.total > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ categories.length }} of {{ meta.total }} categories
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
    </div>

    <div
      v-if="showModal"
      class="modal-overlay"
      @click.self="closeModal"
    >
      <div class="modal modal-large">
        <div class="modal-header">
          <h3>{{ editingId ? 'Edit Category' : 'Add Category' }}</h3>
          <button
            class="modal-close"
            @click="closeModal"
          >
            ×
          </button>
        </div>
        <div class="modal-body">
          <form
            class="edit-form"
            @submit.prevent="saveCategory"
          >
            <div class="form-row">
              <div class="form-group">
                <label>Name *</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-input"
                  required
                >
              </div>
              <div class="form-group">
                <label>Slug *</label>
                <input
                  v-model="form.slug"
                  type="text"
                  class="form-input"
                  required
                >
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Icon</label>
                <input
                  v-model="form.icon"
                  type="text"
                  class="form-input"
                  placeholder="e.g. 📷"
                >
              </div>
              <div class="form-group">
                <label>Display Order</label>
                <input
                  v-model.number="form.display_order"
                  type="number"
                  class="form-input"
                >
              </div>
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="form-input"
              />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="checkbox-label">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                  >
                  <span>Active</span>
                </label>
              </div>
            </div>

            <div class="modal-actions">
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
                {{ saving ? 'Saving...' : (editingId ? 'Update' : 'Create') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      v-if="showToast"
      class="toast"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'

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
    const params = { page }
    if (filters.value.search) params.search = filters.value.search

    const { data } = await api.get('/admin/categories', { params })
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
    const { data } = await api.get(`/admin/categories/${id}`)
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
    const url = editingId.value ? `/admin/categories/${editingId.value}` : '/admin/categories'
    const request = editingId.value ? api.put(url, form.value) : api.post(url, form.value)

    const { data } = await request
    if (data.status === 'success') {
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
    const { data } = await api.delete(`/admin/categories/${id}`)
    if (data.status === 'success') {
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
