<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="🔎 SEO Center"
      subtitle="Manage SEO metadata for platform entities"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="content-card">
        <h3 class="section-title">🔎 SEO Meta Management</h3>
        <p class="section-desc">Manage search engine optimization metadata for platform entities</p>
        
        <div class="form-row">
          <div class="form-group">
            <label>Entity Type *</label>
            <select v-model="modelType" @change="loadEntities" class="form-input">
              <option value="">Select entity type...</option>
              <option value="User">Photographer</option>
              <option value="Competition">Competition</option>
              <option value="Event">Event</option>
              <option value="Album">Album</option>
            </select>
          </div>
          <div class="form-group">
            <label>Entity *</label>
            <select v-model.number="modelId" @change="fetchMeta" class="form-input" :disabled="!modelType || loadingEntities">
              <option value="">{{ loadingEntities ? 'Loading...' : 'Select entity...' }}</option>
              <option v-for="entity in entities" :key="entity.id" :value="entity.id">
                {{ entity.name || entity.title || `ID: ${entity.id}` }}
              </option>
            </select>
          </div>
        </div>

        <div class="actions-row">
          <button @click="fetchMeta" class="btn-secondary">Load</button>
          <button @click="generateMeta" class="btn-secondary">Auto-Generate</button>
          <button @click="previewMeta" class="btn-secondary">Preview</button>
          <button @click="deleteMeta" class="btn-danger">Delete</button>
        </div>

        <div v-if="preview" class="preview-card">
          <h4>Preview</h4>
          <p class="preview-title">{{ preview.title }}</p>
          <p class="preview-url">{{ preview.url }}</p>
          <p class="preview-desc">{{ preview.description }}</p>
        </div>

        <form @submit.prevent="saveMeta" class="edit-form">
          <div class="form-row">
            <div class="form-group">
              <label>Meta Title</label>
              <input v-model="form.meta_title" type="text" class="form-input" />
            </div>
            <div class="form-group">
              <label>Meta Keywords</label>
              <input v-model="form.meta_keywords" type="text" class="form-input" />
            </div>
          </div>

          <div class="form-group">
            <label>Meta Description</label>
            <textarea v-model="form.meta_description" rows="3" class="form-input"></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Canonical URL</label>
              <input v-model="form.canonical_url" type="url" class="form-input" />
            </div>
            <div class="form-group">
              <label>OG Image</label>
              <input v-model="form.og_image" type="url" class="form-input" />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Twitter Card</label>
              <select v-model="form.twitter_card" class="form-input">
                <option value="summary">summary</option>
                <option value="summary_large_image">summary_large_image</option>
                <option value="app">app</option>
                <option value="player">player</option>
              </select>
            </div>
            <div class="form-group">
              <label>OG Title</label>
              <input v-model="form.og_title" type="text" class="form-input" />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>OG Description</label>
              <textarea v-model="form.og_description" rows="2" class="form-input"></textarea>
            </div>
            <div class="form-group">
              <label>Twitter Description</label>
              <textarea v-model="form.twitter_description" rows="2" class="form-input"></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Twitter Image</label>
              <input v-model="form.twitter_image" type="url" class="form-input" />
            </div>
            <div class="form-group">
              <label>Robots Snippet</label>
              <input v-model="form.robots_snippet" type="text" class="form-input" />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="checkbox-label">
                <input v-model="form.robots_index" type="checkbox" />
                <span>Robots Index</span>
              </label>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input v-model="form.robots_follow" type="checkbox" />
                <span>Robots Follow</span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label>Schema JSON</label>
            <textarea v-model="form.schema_json" rows="4" class="form-input"></textarea>
          </div>

          <div class="modal-actions">
            <button type="submit" class="btn-save" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save SEO Meta' }}
            </button>
          </div>
        </form>
      </div>

      <div class="content-card">
        <h3 class="section-title">📋 Existing SEO Meta Records</h3>
        <div class="table-actions">
          <button @click="loadAllMeta" class="btn-secondary" :disabled="loadingAll">
            {{ loadingAll ? 'Loading...' : 'Refresh List' }}
          </button>
        </div>
        
        <div v-if="loadingAll" class="loading-state">
          <div class="spinner"></div>
          <p>Loading SEO meta records...</p>
        </div>

        <div v-else-if="allMeta.length > 0" class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>Entity Type</th>
                <th>Entity ID</th>
                <th>Meta Title</th>
                <th>Status</th>
                <th>Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="meta in allMeta" :key="meta.id">
                <td>{{ meta.model_type }}</td>
                <td>{{ meta.model_id }}</td>
                <td>{{ meta.meta_title || '(No title)' }}</td>
                <td>
                  <span :class="['status-badge', meta.is_auto_generated ? 'status-auto' : 'status-manual']">
                    {{ meta.is_auto_generated ? 'Auto' : 'Manual' }}
                  </span>
                </td>
                <td>{{ formatDate(meta.updated_at) }}</td>
                <td class="actions">
                  <button @click="loadMetaRecord(meta)" class="btn-action btn-edit">Edit</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="empty-state">
          <div class="empty-icon">📝</div>
          <p class="empty-title">No SEO Meta Records Yet</p>
          <p class="empty-desc">Create SEO metadata using the form above</p>
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

const modelType = ref('')
const modelId = ref(null)
const preview = ref(null)
const saving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const entities = ref([])
const loadingEntities = ref(false)
const allMeta = ref([])
const loadingAll = ref(false)

const form = ref({
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  canonical_url: '',
  og_title: '',
  og_description: '',
  og_image: '',
  twitter_card: 'summary',
  twitter_title: '',
  twitter_description: '',
  twitter_image: '',
  robots_index: true,
  robots_follow: true,
  robots_snippet: '',
  schema_json: ''
})

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, 3000)
}

const loadEntities = async () => {
  if (!modelType.value) return
  loadingEntities.value = true
  entities.value = []
  modelId.value = null
  try {
    const token = localStorage.getItem('auth_token')
    let endpoint = ''
    if (modelType.value === 'User') endpoint = '/api/v1/admin/photographers'
    else if (modelType.value === 'Competition') endpoint = '/api/v1/admin/competitions'
    else if (modelType.value === 'Event') endpoint = '/api/v1/admin/events'
    else if (modelType.value === 'Album') endpoint = '/api/v1/albums'
    
    if (!endpoint) return
    
    const response = await fetch(endpoint, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      entities.value = data.data?.data || data.data || []
    }
  } catch (error) {
    console.error('Error loading entities', error)
  } finally {
    loadingEntities.value = false
  }
}

const loadAllMeta = async () => {
  loadingAll.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('/api/v1/admin/seo/all', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      allMeta.value = data.data || []
    }
  } catch (error) {
    console.error('Error loading all meta', error)
    showToastMessage('Error loading SEO records')
  } finally {
    loadingAll.value = false
  }
}

const loadMetaRecord = (meta) => {
  modelType.value = meta.model_type
  modelId.value = meta.model_id
  Object.assign(form.value, meta)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

const fetchMeta = async () => {
  if (!modelType.value || !modelId.value) return showToastMessage('Model type and ID required')
  try {
    const token = localStorage.getItem('auth_token')
    const params = new URLSearchParams({ model_type: modelType.value, model_id: modelId.value })
    const response = await fetch(`/api/v1/admin/seo?${params}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      if (data.data) {
        Object.assign(form.value, data.data)
      }
      showToastMessage(data.message || 'SEO meta loaded')
    }
  } catch (error) {
    console.error('Error loading SEO meta', error)
    showToastMessage('Error loading SEO meta')
  }
}

const generateMeta = async () => {
  if (!modelType.value || !modelId.value) return showToastMessage('Model type and ID required')
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('/api/v1/admin/seo/generate', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ model_type: modelType.value, model_id: modelId.value })
    })
    const data = await response.json()
    if (data.status === 'success') {
      Object.assign(form.value, data.data || {})
      showToastMessage('SEO meta generated')
    } else {
      showToastMessage(data.message || 'Error generating SEO meta')
    }
  } catch (error) {
    console.error('Error generating SEO meta', error)
    showToastMessage('Error generating SEO meta')
  }
}

const previewMeta = async () => {
  if (!modelType.value || !modelId.value) return showToastMessage('Model type and ID required')
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('/api/v1/admin/seo/preview', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ model_type: modelType.value, model_id: modelId.value })
    })
    const data = await response.json()
    if (data.status === 'success') preview.value = data.data
  } catch (error) {
    console.error('Error previewing SEO meta', error)
    showToastMessage('Error previewing SEO meta')
  }
}

const saveMeta = async () => {
  if (!modelType.value || !modelId.value) return showToastMessage('Model type and ID required')
  saving.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const payload = { model_type: modelType.value, model_id: modelId.value, ...form.value }
    const response = await fetch('/api/v1/admin/seo', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage('SEO meta saved')
    } else {
      showToastMessage(data.message || 'Error saving SEO meta')
    }
  } catch (error) {
    console.error('Error saving SEO meta', error)
    showToastMessage('Error saving SEO meta')
  } finally {
    saving.value = false
  }
}

const deleteMeta = async () => {
  if (!modelType.value || !modelId.value) return showToastMessage('Model type and ID required')
  if (!confirm('Delete SEO meta for this entity?')) return
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('/api/v1/admin/seo', {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ model_type: modelType.value, model_id: modelId.value })
    })
    const data = await response.json()
    if (response.ok && data.status === 'success') {
      showToastMessage('SEO meta deleted')
    } else {
      showToastMessage(data.message || 'Error deleting SEO meta')
    }
  } catch (error) {
    console.error('Error deleting SEO meta', error)
    showToastMessage('Error deleting SEO meta')
  }
}

onMounted(() => {
  loadAllMeta()
})
</script>

<style scoped>
.content-card { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.5rem; }
.form-input { padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; }
.checkbox-label { display: flex; align-items: center; gap: 0.5rem; }
.actions-row { display: flex; gap: 0.5rem; margin: 1rem 0; flex-wrap: wrap; }
.btn-secondary { padding: 0.5rem 0.75rem; border: none; border-radius: 0.5rem; background: var(--admin-brand-primary); color: white; cursor: pointer; transition: opacity 0.2s; }
.btn-secondary:hover:not(:disabled) { opacity: 0.9; }
.btn-secondary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-danger { padding: 0.5rem 0.75rem; border: 1px solid var(--admin-danger); border-radius: 0.5rem; color: var(--admin-danger); background: white; cursor: pointer; }
.btn-danger:hover { background: var(--admin-danger); color: white; }
.btn-save { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; background: var(--admin-brand-primary); color: white; }
.preview-card { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; background: #f9fafb; }
.preview-title { font-weight: 700; }
.preview-url { color: var(--admin-brand-primary); }
.preview-desc { color: #4b5563; }
.toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--admin-success-dark); color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; z-index: 1001; }
.section-title { font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; }
.section-desc { color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem; }
.table-actions { margin-bottom: 1rem; }
.table-wrapper { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 0.75rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
.data-table th { background: #f9fafb; font-weight: 600; }
.actions { display: flex; gap: 0.5rem; }
.btn-action { padding: 0.35rem 0.6rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; cursor: pointer; }
.btn-edit { color: var(--admin-brand-primary); }
.btn-edit:hover { background: var(--admin-brand-primary); color: white; }
.status-badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; }
.status-auto { background: #dbeafe; color: #1e40af; }
.status-manual { background: #d1fae5; color: #065f46; }
.empty-state { text-align: center; padding: 2rem 0; }
.empty-icon { font-size: 2rem; }
.empty-title { font-weight: 600; margin-top: 0.5rem; }
.empty-desc { color: #6b7280; font-size: 0.875rem; }
.loading-state { display: flex; flex-direction: column; align-items: center; padding: 2rem 0; }
.spinner { width: 2rem; height: 2rem; border: 3px solid #e5e7eb; border-top: 3px solid var(--admin-brand-primary); border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
