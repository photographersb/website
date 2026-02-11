<template>
  <div class="min-h-screen">
    <AdminHeader
      title="📍 Locations"
      subtitle="Manage divisions, districts, and upazilas"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">LOCATION MAP</p>
          <h1 class="hero-title">Territories, clean and searchable.</h1>
          <p class="hero-subtitle">
            Maintain divisions, districts, and upazilas without gaps.
          </p>
          <div class="hero-actions">
            <button class="btn-admin-primary" @click="openCreate">
              Add Location
            </button>
            <button class="btn-admin-secondary" @click="fetchCities">
              Refresh List
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Total Locations</span>
            <span class="status-value">{{ meta.total || cities.length }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Active</span>
            <span class="status-value">{{ cities.filter(city => city.is_active).length }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Inactive</span>
            <span class="status-value">{{ cities.filter(city => !city.is_active).length }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Showing {{ cities.length }} of {{ meta.total || 0 }} locations
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
              placeholder="Search locations..."
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
          <p>Loading locations...</p>
        </div>

        <div
          v-else-if="cities.length > 0"
          class="table-wrapper"
        >
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Parent</th>
                <th>Sort Order</th>
                <th>Status</th>
                <th />
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="city in cities"
                :key="city.id"
              >
                <td>{{ city.name }}</td>
                <td class="capitalize">{{ city.type || '—' }}</td>
                <td>{{ city.parent?.name || '—' }}</td>
                <td>{{ city.sort_order ?? 0 }}</td>
                <td>
                  <span
                    :class="['status-pill', city.is_active ? 'status-active' : 'status-inactive']"
                  >
                    {{ city.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="actions">
                  <button
                    class="btn-action btn-edit"
                    @click="openEdit(city.id)"
                  >
                    Edit
                  </button>
                  <button
                    class="btn-action btn-delete"
                    @click="deleteCity(city.id)"
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
            🏙️
          </div>
          <p class="empty-title">
            No locations found
          </p>
        </div>

        <div
          v-if="meta.total > 0"
          class="pagination"
        >
          <div class="pagination-info">
            Showing {{ cities.length }} of {{ meta.total }} locations
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
          <h3>{{ editingId ? 'Edit Location' : 'Add Location' }}</h3>
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
            @submit.prevent="saveCity"
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
                <label>Type *</label>
                <select
                  v-model="form.type"
                  class="form-input"
                  required
                >
                  <option value="division">
                    Division
                  </option>
                  <option value="district">
                    District
                  </option>
                  <option value="upazila">
                    Upazila
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label>Parent</label>
                <select
                  v-model="form.parent_id"
                  class="form-input"
                  :disabled="availableParents.length === 0"
                >
                  <option :value="null">
                    None
                  </option>
                  <option
                    v-for="parent in availableParents"
                    :key="parent.id"
                    :value="parent.id"
                  >
                    {{ parent.name }}
                  </option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Sort Order</label>
                <input
                  v-model.number="form.sort_order"
                  type="number"
                  class="form-input"
                >
              </div>
              <div class="form-group">
                <label>Status</label>
                <label class="checkbox-row">
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
import { ref, onMounted, computed, watch } from 'vue'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'

const cities = ref([])
const loading = ref(false)
const showModal = ref(false)
const editingId = ref(null)
const saving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({ search: '' })
const meta = ref({ total: 0, current_page: 1, last_page: 1 })

const form = ref({
  name: '',
  slug: '',
  type: 'district',
  parent_id: null,
  sort_order: 0,
  is_active: true
})

const parentOptions = ref([])

const normalizeType = (value) => (value || '').toString().trim().toLowerCase()

const availableParents = computed(() => {
  const type = normalizeType(form.value.type)
  if (type === 'district') {
    return parentOptions.value.filter(parent => normalizeType(parent.type) === 'division')
  }
  if (type === 'upazila') {
    return parentOptions.value.filter(parent => normalizeType(parent.type) === 'district')
  }
  return []
})

let searchTimeout = null
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchCities(), 400)
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, 3000)
}

const fetchCities = async (page = 1) => {
  loading.value = true
  try {
    const params = { page }
    if (filters.value.search) params.search = filters.value.search

    const { data } = await api.get('/admin/locations', { params })
    if (data.status === 'success') {
      cities.value = data.data.data || data.data
      meta.value = {
        total: data.data.total || cities.value.length,
        current_page: data.data.current_page || 1,
        last_page: data.data.last_page || 1
      }
    }
  } catch (error) {
    console.error('Error fetching locations', error)
    showToastMessage('Error loading locations')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  form.value = {
    name: '',
    slug: '',
    type: 'district',
    parent_id: null,
    sort_order: 0,
    is_active: true
  }
  showModal.value = true
}

const openEdit = async (id) => {
  try {
    const { data } = await api.get(`/admin/locations/${id}`)
    if (data.status === 'success') {
      const city = data.data
      const normalizedType = normalizeType(city.type) || 'district'
      editingId.value = id
      form.value = {
        name: city.name,
        slug: city.slug,
        type: normalizedType,
        parent_id: city.parent_id || null,
        sort_order: city.sort_order ?? 0,
        is_active: city.is_active ?? true
      }
      showModal.value = true
    }
  } catch (error) {
    console.error('Error loading location', error)
    showToastMessage('Error loading location')
  }
}

const saveCity = async () => {
  saving.value = true
  try {
    const url = editingId.value ? `/admin/locations/${editingId.value}` : '/admin/locations'
    const payload = {
      ...form.value,
      type: normalizeType(form.value.type) || 'district',
      parent_id: form.value.parent_id || null
    }
    const request = editingId.value ? api.put(url, payload) : api.post(url, payload)

    const { data } = await request
    if (data.status === 'success') {
      showToastMessage(editingId.value ? 'Location updated' : 'Location created')
      showModal.value = false
      fetchCities(meta.value.current_page)
    } else {
      showToastMessage(data.message || 'Error saving location')
    }
  } catch (error) {
    console.error('Error saving location', error)
    showToastMessage('Error saving location')
  } finally {
    saving.value = false
  }
}

const deleteCity = async (id) => {
  if (!confirm('Delete this location?')) return
  try {
    const { data } = await api.delete(`/admin/locations/${id}`)
    if (data.status === 'success') {
      showToastMessage('Location deleted')
      fetchCities(meta.value.current_page)
    }
  } catch (error) {
    console.error('Error deleting location', error)
    showToastMessage('Error deleting location')
  }
}

const fetchParents = async () => {
  try {
    const { data } = await api.get('/admin/locations', { params: { minimal: true } })
    if (data.status === 'success') {
      parentOptions.value = data.data || []
    }
  } catch (error) {
    console.error('Error loading parent locations', error)
  }
}

const closeModal = () => { showModal.value = false }

const changePage = (page) => {
  if (page >= 1 && page <= meta.value.last_page) fetchCities(page)
}

watch(
  () => form.value.type,
  () => {
    if (availableParents.value.length === 0) {
      form.value.parent_id = null
      return
    }

    const isValidParent = availableParents.value.some(parent => parent.id === form.value.parent_id)
    if (!isValidParent) {
      form.value.parent_id = null
    }
  }
)

onMounted(() => {
  fetchCities()
  fetchParents()
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
.checkbox-row { display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #111827; }
.modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; }
.btn-cancel { padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white; }
.btn-save { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; background: var(--admin-brand-primary); color: white; }
.status-pill { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; }
.status-active { background: #dcfce7; color: #166534; }
.status-inactive { background: #fee2e2; color: #991b1b; }
.toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--admin-success-dark); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; z-index: 1001; }
.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
.pagination-btn { padding: 0.4rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
</style>
