<template>
  <div class="min-h-screen">
    <AdminHeader
      title="⚙️ Certificate Automation Rules"
      subtitle="Configure automatic certificate issuance rules"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex gap-2">
          <router-link to="/admin/certificates" class="btn-admin-secondary">← Back to Certificates</router-link>
          <router-link to="/admin/certificates/manual-issuance" class="btn-admin-secondary">Manual Issuance</router-link>
        </div>
      </div>

      <AdminQuickNav />

      <div class="panel space-y-4">
        <h2 class="text-lg font-semibold text-gray-900">{{ editingId ? 'Edit Rule' : 'Create Rule' }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="label">Rule Name</label>
            <input v-model="form.name" type="text" class="field" placeholder="Attendance auto issue">
          </div>

          <div>
            <label class="label">Trigger</label>
            <select v-model="form.trigger_type" class="field">
              <option value="event_attendance">Event attendance</option>
              <option value="workshop_completed">Workshop completed</option>
              <option value="competition_winners_announced">Competition winners announced</option>
              <option value="participation_confirmed">Participation confirmed</option>
            </select>
          </div>

          <div>
            <label class="label">Source Type</label>
            <select v-model="form.source_type" class="field">
              <option value="event">Event</option>
              <option value="workshop">Workshop</option>
              <option value="competition">Competition</option>
              <option value="award">Award</option>
              <option value="participation">Participation</option>
            </select>
          </div>

          <div>
            <label class="label">Source (optional)</label>
            <select v-model="form.source_id" class="field">
              <option value="">All sources</option>
              <option v-for="source in sourceOptions" :key="source.id" :value="source.id">{{ source.title }}</option>
            </select>
          </div>

          <div>
            <label class="label">Template</label>
            <select v-model="form.template_id" class="field">
              <option value="">Select template</option>
              <option v-for="template in options.templates" :key="template.id" :value="template.id">
                {{ template.title }} ({{ template.type }})
              </option>
            </select>
          </div>

          <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
              <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300">
              Active rule
            </label>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button class="btn-admin-primary" :disabled="saving" @click="saveRule">
            {{ saving ? 'Saving...' : (editingId ? 'Update Rule' : 'Create Rule') }}
          </button>
          <button v-if="editingId" class="btn-admin-secondary" @click="resetForm">Cancel edit</button>
        </div>
      </div>

      <div class="panel overflow-x-auto">
        <div v-if="loading" class="py-8 text-center text-gray-500">Loading rules...</div>
        <div v-else-if="rules.length === 0" class="py-8 text-center text-gray-500">No automation rules configured.</div>

        <table v-else class="w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-3">Name</th>
              <th class="py-3">Trigger</th>
              <th class="py-3">Source</th>
              <th class="py-3">Template</th>
              <th class="py-3">Status</th>
              <th class="py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="rule in rules" :key="rule.id" class="border-b last:border-b-0">
              <td class="py-3 font-medium text-gray-900">{{ rule.name }}</td>
              <td class="py-3">{{ prettify(rule.trigger_type) }}</td>
              <td class="py-3">{{ prettify(rule.source_type) }} <span v-if="rule.source_id" class="text-gray-500">#{{ rule.source_id }}</span></td>
              <td class="py-3">{{ rule.template?.title || '—' }}</td>
              <td class="py-3">
                <span :class="['badge', rule.is_active ? 'badge-ok' : 'badge-muted']">{{ rule.is_active ? 'Active' : 'Inactive' }}</span>
              </td>
              <td class="py-3 text-right">
                <button class="link" @click="editRule(rule)">Edit</button>
                <button class="link text-red-600" @click="deleteRule(rule)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="toast.message" :class="['fixed bottom-4 right-4 px-4 py-2 rounded text-white z-50', toast.type === 'error' ? 'bg-red-600' : 'bg-green-600']">
        {{ toast.message }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '../../../api'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

const rules = ref([])
const loading = ref(false)
const saving = ref(false)
const editingId = ref(null)
const options = ref({ events: [], competitions: [], templates: [] })
const toast = ref({ message: '', type: 'success' })

const form = ref({
  name: '',
  trigger_type: 'event_attendance',
  source_type: 'event',
  source_id: '',
  template_id: '',
  is_active: true,
})

const sourceOptions = computed(() => {
  if (form.value.source_type === 'competition') return options.value.competitions || []
  return options.value.events || []
})

const showToast = (message, type = 'success') => {
  toast.value = { message, type }
  setTimeout(() => {
    toast.value.message = ''
  }, 3500)
}

const prettify = (value) => (value || '').replaceAll('_', ' ')

const resetForm = () => {
  editingId.value = null
  form.value = {
    name: '',
    trigger_type: 'event_attendance',
    source_type: 'event',
    source_id: '',
    template_id: '',
    is_active: true,
  }
}

const loadOptions = async () => {
  try {
    const { data } = await api.get('/admin/certificates/options')
    if (data.status === 'success') {
      options.value = {
        events: data.data.events || [],
        competitions: data.data.competitions || [],
        templates: data.data.templates || [],
      }
    }
  } catch (error) {
    showToast('Failed to load options', 'error')
  }
}

const loadRules = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/certificate-automation-rules')
    if (data.status === 'success') {
      rules.value = data.data || []
    }
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to load automation rules', 'error')
  } finally {
    loading.value = false
  }
}

const saveRule = async () => {
  saving.value = true
  try {
    const payload = {
      name: form.value.name,
      trigger_type: form.value.trigger_type,
      source_type: form.value.source_type,
      source_id: form.value.source_id ? Number(form.value.source_id) : null,
      template_id: Number(form.value.template_id),
      is_active: !!form.value.is_active,
    }

    if (editingId.value) {
      await api.put(`/admin/certificate-automation-rules/${editingId.value}`, payload)
      showToast('Automation rule updated')
    } else {
      await api.post('/admin/certificate-automation-rules', payload)
      showToast('Automation rule created')
    }

    resetForm()
    await loadRules()
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to save automation rule', 'error')
  } finally {
    saving.value = false
  }
}

const editRule = (rule) => {
  editingId.value = rule.id
  form.value = {
    name: rule.name || '',
    trigger_type: rule.trigger_type || 'event_attendance',
    source_type: rule.source_type || 'event',
    source_id: rule.source_id || '',
    template_id: rule.template_id || '',
    is_active: !!rule.is_active,
  }
}

const deleteRule = async (rule) => {
  if (!window.confirm(`Delete automation rule "${rule.name}"?`)) return

  try {
    await api.delete(`/admin/certificate-automation-rules/${rule.id}`)
    showToast('Automation rule deleted')
    await loadRules()
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to delete rule', 'error')
  }
}

onMounted(async () => {
  await Promise.all([loadOptions(), loadRules()])
})
</script>

<style scoped>
.panel { background: #fff; border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1rem; padding: 1rem; box-shadow: 0 12px 28px rgba(18, 9, 6, 0.06); }
.field { width: 100%; border: 1px solid #d1d5db; border-radius: 0.75rem; padding: 0.6rem 0.8rem; }
.label { display: block; margin-bottom: 0.45rem; font-size: 0.85rem; font-weight: 600; color: #374151; }
.btn-admin-primary { background: #8e0e3f; color: #fff; padding: 0.625rem 1rem; border-radius: 0.75rem; border: none; font-weight: 600; }
.btn-admin-secondary { background: #fff; border: 1px solid #d1d5db; color: #111827; padding: 0.625rem 1rem; border-radius: 0.75rem; font-weight: 600; }
.badge { font-size: 0.75rem; border-radius: 9999px; padding: 0.2rem 0.65rem; }
.badge-ok { background: #dcfce7; color: #166534; }
.badge-muted { background: #f3f4f6; color: #4b5563; }
.link { color: #8e0e3f; font-weight: 600; background: none; border: none; cursor: pointer; margin-left: .75rem; }
</style>
