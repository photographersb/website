<template>
  <div class="min-h-screen">
    <AdminHeader
      title="🎨 Certificate Templates"
      subtitle="Create and manage certificate templates"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Templates</h1>
        <button class="btn-admin-primary" @click="openCreate">+ New Template</button>
      </div>

      <AdminQuickNav />

      <div class="panel overflow-x-auto">
        <div v-if="loading" class="py-8 text-center text-gray-500">Loading templates...</div>
        <div v-else-if="templates.length === 0" class="py-8 text-center text-gray-500">No templates available.</div>
        <table v-else class="w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-3">Title</th>
              <th class="py-3">Type</th>
              <th class="py-3">Size</th>
              <th class="py-3">Default</th>
              <th class="py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="template in templates" :key="template.id" class="border-b last:border-b-0">
              <td class="py-3">
                <div class="font-medium text-gray-900">{{ template.title }}</div>
                <div class="text-xs text-gray-500">{{ template.description || '—' }}</div>
              </td>
              <td class="py-3 capitalize">{{ template.type }}</td>
              <td class="py-3">{{ template.width }} × {{ template.height }} mm</td>
              <td class="py-3">
                <span v-if="template.is_default" class="badge">Default</span>
                <button v-else class="link" @click="setDefault(template)">Set default</button>
              </td>
              <td class="py-3 text-right">
                <button class="link" @click="edit(template)">Edit</button>
                <button class="link text-red-600" @click="remove(template)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <TemplateEditor
        v-if="showEditor"
        :template="editingTemplate"
        :available-types="types"
        @save="save"
        @cancel="closeEditor"
      />

      <div v-if="toast.message" :class="['fixed bottom-4 right-4 px-4 py-2 rounded text-white z-50', toast.type === 'error' ? 'bg-red-600' : 'bg-green-600']">
        {{ toast.message }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '../../../api'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import TemplateEditor from './TemplateEditor.vue'

const templates = ref([])
const loading = ref(false)
const showEditor = ref(false)
const editingTemplate = ref(null)
const toast = ref({ message: '', type: 'success' })

const types = [
  { value: 'event', label: 'Event' },
  { value: 'workshop', label: 'Workshop' },
  { value: 'competition', label: 'Competition' },
  { value: 'award', label: 'Award' },
  { value: 'participation', label: 'Participation' },
]

const showToast = (message, type = 'success') => {
  toast.value = { message, type }
  setTimeout(() => {
    toast.value.message = ''
  }, 3500)
}

const loadTemplates = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/certificate-templates')
    if (data.status === 'success') {
      templates.value = data.data
    }
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to load templates', 'error')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  editingTemplate.value = null
  showEditor.value = true
}

const edit = (template) => {
  editingTemplate.value = { ...template }
  showEditor.value = true
}

const closeEditor = () => {
  showEditor.value = false
  editingTemplate.value = null
}

const save = async (payload) => {
  try {
    if (editingTemplate.value?.id) {
      await api.put(`/admin/certificate-templates/${editingTemplate.value.id}`, payload)
      showToast('Template updated successfully')
    } else {
      await api.post('/admin/certificate-templates', payload)
      showToast('Template created successfully')
    }

    closeEditor()
    loadTemplates()
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to save template', 'error')
  }
}

const setDefault = async (template) => {
  try {
    await api.put(`/admin/certificate-templates/${template.id}`, {
      ...template,
      is_default: true,
    })
    showToast('Default template updated')
    loadTemplates()
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to update default', 'error')
  }
}

const remove = async (template) => {
  if (!window.confirm(`Delete template "${template.title}"?`)) return

  try {
    await api.delete(`/admin/certificate-templates/${template.id}`)
    showToast('Template deleted')
    loadTemplates()
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to delete template', 'error')
  }
}

onMounted(() => {
  loadTemplates()
})
</script>

<style scoped>
.panel { background: #fff; border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1rem; padding: 1rem; box-shadow: 0 12px 28px rgba(18, 9, 6, 0.06); }
.btn-admin-primary { background: #8e0e3f; color: #fff; padding: 0.625rem 1rem; border-radius: 0.75rem; border: none; font-weight: 600; }
.badge { background: #dcfce7; color: #166534; font-size: .75rem; border-radius: 999px; padding: .2rem .6rem; }
.link { color: #8e0e3f; font-weight: 600; background: none; border: none; cursor: pointer; margin-left: .75rem; }
</style>
