<template>
  <div class="min-h-screen">
    <AdminHeader
      title="🎓 Manual Certificate Issuance"
      subtitle="Issue certificates for users, events, and competitions"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="flex items-center justify-between">
        <router-link to="/admin/certificates" class="btn-admin-secondary">← Back to Certificates</router-link>
      </div>

      <AdminQuickNav />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 panel space-y-4">
          <h2 class="text-lg font-semibold text-gray-900">Manual Issue</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
              <label class="label">Template</label>
              <select v-model="form.template_id" class="field">
                <option value="">Select template</option>
                <option v-for="template in options.templates" :key="template.id" :value="template.id">
                  {{ template.title }} ({{ template.type }})
                </option>
              </select>
            </div>

            <div>
              <label class="label">User (optional)</label>
              <select v-model="form.user_id" class="field" @change="syncUserInfo">
                <option value="">Select user</option>
                <option v-for="user in options.users" :key="user.id" :value="user.id">
                  {{ user.full_name || user.name }} · {{ user.email }}
                </option>
              </select>
            </div>

            <div v-if="form.source_type === 'event' || form.source_type === 'workshop'">
              <label class="label">Event</label>
              <select v-model="form.event_id" class="field" @change="form.competition_id = ''">
                <option value="">Select event</option>
                <option v-for="event in options.events" :key="event.id" :value="event.id">{{ event.title }}</option>
              </select>
            </div>

            <div v-if="form.source_type === 'competition'">
              <label class="label">Competition</label>
              <select v-model="form.competition_id" class="field" @change="form.event_id = ''">
                <option value="">Select competition</option>
                <option v-for="competition in options.competitions" :key="competition.id" :value="competition.id">{{ competition.title }}</option>
              </select>
            </div>

            <div>
              <label class="label">Recipient Name</label>
              <input v-model="form.recipient_name" type="text" class="field" placeholder="Recipient full name">
            </div>

            <div>
              <label class="label">Recipient Email</label>
              <input v-model="form.recipient_email" type="email" class="field" placeholder="Recipient email">
            </div>

            <div>
              <label class="label">Issue Date</label>
              <input v-model="form.issued_at" type="datetime-local" class="field">
            </div>

            <div class="md:col-span-2">
              <label class="label">Notes</label>
              <textarea v-model="form.notes" rows="3" class="field" placeholder="Optional internal notes" />
            </div>
          </div>

          <button class="btn-admin-primary" :disabled="issuing" @click="issueManually">
            {{ issuing ? 'Issuing...' : 'Issue Certificate' }}
          </button>
        </div>

        <div class="panel space-y-4">
          <h2 class="text-lg font-semibold text-gray-900">Auto Issue</h2>
          <p class="text-sm text-gray-600">Bulk issue certificates for event attendees or competition winners.</p>

          <div>
            <label class="label">Template</label>
            <select v-model="autoForm.template_id" class="field">
              <option value="">Select template</option>
              <option v-for="template in options.templates" :key="template.id" :value="template.id">
                {{ template.title }} ({{ template.type }})
              </option>
            </select>
          </div>

          <div>
            <label class="label">Source Type</label>
            <select v-model="autoForm.source_type" class="field">
              <option value="event">Event</option>
              <option value="competition">Competition</option>
            </select>
          </div>

          <div>
            <label class="label">Source</label>
            <select v-model="autoForm.source_id" class="field">
              <option value="">Select source</option>
              <option v-for="item in autoSourceOptions" :key="item.id" :value="item.id">{{ item.title }}</option>
            </select>
          </div>

          <button class="btn-admin-secondary" :disabled="autoIssuing" @click="issueAutomatically">
            {{ autoIssuing ? 'Running...' : 'Run Auto Issue' }}
          </button>
        </div>
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

const options = ref({ users: [], events: [], competitions: [], templates: [] })
const issuing = ref(false)
const autoIssuing = ref(false)
const toast = ref({ message: '', type: 'success' })

const form = ref({
  source_type: 'event',
  template_id: '',
  user_id: '',
  event_id: '',
  competition_id: '',
  recipient_name: '',
  recipient_email: '',
  issued_at: '',
  notes: '',
})

const autoForm = ref({
  template_id: '',
  source_type: 'event',
  source_id: '',
})

const autoSourceOptions = computed(() => (
  autoForm.value.source_type === 'event' ? options.value.events : options.value.competitions
))

const showToast = (message, type = 'success') => {
  toast.value = { message, type }
  setTimeout(() => {
    toast.value.message = ''
  }, 4000)
}

const loadOptions = async () => {
  try {
    const { data } = await api.get('/admin/certificates/options')
    if (data.status === 'success') {
      options.value = data.data
    }
  } catch (error) {
    showToast('Failed to load issuance options', 'error')
  }
}

const syncUserInfo = () => {
  const selectedUser = options.value.users.find((user) => user.id === Number(form.value.user_id))
  if (!selectedUser) return
  form.value.recipient_name = selectedUser.full_name || selectedUser.name
  form.value.recipient_email = selectedUser.email || ''
}

const issueManually = async () => {
  issuing.value = true
  try {
    const payload = {
      ...form.value,
      source_type: form.value.source_type,
      template_id: Number(form.value.template_id) || null,
      user_id: form.value.user_id ? Number(form.value.user_id) : null,
      event_id: (form.value.source_type === 'event' || form.value.source_type === 'workshop') && form.value.event_id ? Number(form.value.event_id) : null,
      competition_id: form.value.source_type === 'competition' && form.value.competition_id ? Number(form.value.competition_id) : null,
    }

    const { data } = await api.post('/admin/certificates/manual-issue', payload)
    if (data.status === 'success') {
      showToast('Certificate issued successfully')
      form.value.notes = ''
    }
  } catch (error) {
    showToast(error?.response?.data?.message || 'Manual issuance failed', 'error')
  } finally {
    issuing.value = false
  }
}

const issueAutomatically = async () => {
  autoIssuing.value = true
  try {
    const { data } = await api.post('/admin/certificates/auto-issue', {
      template_id: Number(autoForm.value.template_id),
      source_type: autoForm.value.source_type,
      source_id: Number(autoForm.value.source_id),
    })

    if (data.status === 'success') {
      showToast(`Auto-issue completed. Issued: ${data.data.issued_count}`)
    }
  } catch (error) {
    showToast(error?.response?.data?.message || 'Auto-issue failed', 'error')
  } finally {
    autoIssuing.value = false
  }
}

onMounted(() => {
  loadOptions()
})
</script>

<style scoped>
.panel { background: #fff; border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1rem; padding: 1rem; box-shadow: 0 12px 28px rgba(18, 9, 6, 0.06); }
.label { display: block; font-size: 0.85rem; margin-bottom: 0.35rem; font-weight: 600; color: #374151; }
.field { width: 100%; border: 1px solid #d1d5db; border-radius: 0.75rem; padding: 0.6rem 0.8rem; }
.btn-admin-primary { background: #8e0e3f; color: #fff; padding: 0.625rem 1rem; border-radius: 0.75rem; border: none; font-weight: 600; }
.btn-admin-primary:disabled { opacity: .6; cursor: not-allowed; }
.btn-admin-secondary { background: #fff; border: 1px solid #d1d5db; color: #111827; padding: 0.625rem 1rem; border-radius: 0.75rem; font-weight: 600; }
.btn-admin-secondary:disabled { opacity: .6; cursor: not-allowed; }
</style>
