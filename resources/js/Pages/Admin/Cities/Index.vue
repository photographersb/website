<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader title="🏙️ Cities" subtitle="Manage cities and districts" />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="content-card">
        <div class="filters-bar">
          <div class="search-box">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input v-model="filters.search" @input="debounceSearch" type="text" placeholder="Search cities..." class="search-input" />
          </div>

          <button @click="openCreate" class="btn-add">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add City
          </button>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading cities...</p>
        </div>

        <div v-else-if="cities.length > 0" class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>State</th>
                <th>Division</th>
                <th>Display Order</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="city in cities" :key="city.id">
                <td>{{ city.name }}</td>
                <td>{{ city.state || '—' }}</td>
                <td>{{ city.division || '—' }}</td>
                <td>{{ city.display_order }}</td>
                <td class="actions">
                  <button @click="openEdit(city.id)" class="btn-action btn-edit">Edit</button>
                  <button @click="deleteCity(city.id)" class="btn-action btn-delete">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">🏙️</div>
          <p class="empty-title">No cities found</p>
        </div>

        <div v-if="meta.total > 0" class="pagination">
          <div class="pagination-info">Showing {{ cities.length }} of {{ meta.total }} cities</div>
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
          <h3>{{ editingId ? 'Edit City' : 'Add City' }}</h3>
          <button @click="closeModal" class="modal-close">×</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveCity" class="edit-form">
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
                <label>State</label>
                <input v-model="form.state" type="text" class="form-input" placeholder="e.g., Dhaka" />
              </div>
              <div class="form-group">
                <label>Division</label>
                <input v-model="form.division" type="text" class="form-input" placeholder="e.g., Dhaka Division" />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Display Order</label>
                <input v-model.number="form.display_order" type="number" class="form-input" />
              </div>
              <div class="form-group">
                <!-- Empty for grid layout -->
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

const cities = ref([])
const loading = ref(false)
const showModal = ref(false)
const editingId = ref(null)
const saving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

const filters = ref({ search: '' })
const meta = ref({ total: 0, current_page: 1, last_page: 1 })

const form = ref({ name: '', slug: '', state: '', division: '', display_order: 0 })

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
    const token = localStorage.getItem('auth_token')
    const params = new URLSearchParams()
    if (filters.value.search) params.append('search', filters.value.search)
    params.append('page', page)

    const response = await fetch(`/api/v1/admin/cities?${params}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      cities.value = data.data.data || data.data
      meta.value = {
        total: data.data.total || cities.value.length,
        current_page: data.data.current_page || 1,
        last_page: data.data.last_page || 1
      }
    }
  } catch (error) {
    console.error('Error fetching cities', error)
    showToastMessage('Error loading cities')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  form.value = { name: '', slug: '', state: '', division: '', display_order: 0 }
  showModal.value = true
}

const openEdit = async (id) => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/cities/${id}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      const city = data.data
      editingId.value = id
      form.value = {
        name: city.name,
        slug: city.slug,
        state: city.state || '',
        division: city.division || '',
        display_order: city.display_order || 0
      }
      showModal.value = true
    }
  } catch (error) {
    console.error('Error loading city', error)
    showToastMessage('Error loading city')
  }
}

const saveCity = async () => {
  saving.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const url = editingId.value ? `/api/v1/admin/cities/${editingId.value}` : '/api/v1/admin/cities'
    const method = editingId.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(form.value)
    })

    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage(editingId.value ? 'City updated' : 'City created')
      showModal.value = false
      fetchCities(meta.value.current_page)
    } else {
      showToastMessage(data.message || 'Error saving city')
    }
  } catch (error) {
    console.error('Error saving city', error)
    showToastMessage('Error saving city')
  } finally {
    saving.value = false
  }
}

const deleteCity = async (id) => {
  if (!confirm('Delete this city?')) return
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/cities/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage('City deleted')
      fetchCities(meta.value.current_page)
    }
  } catch (error) {
    console.error('Error deleting city', error)
    showToastMessage('Error deleting city')
  }
}

const closeModal = () => { showModal.value = false }

const changePage = (page) => {
  if (page >= 1 && page <= meta.value.last_page) fetchCities(page)
}

onMounted(() => {
  fetchCities()
})
</script>

<style scoped>
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
.modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; }
.btn-cancel { padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white; }
.btn-save { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; background: var(--admin-brand-primary); color: white; }
.toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--admin-success-dark); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; z-index: 1001; }
.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
.pagination-btn { padding: 0.4rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; }
</style>
