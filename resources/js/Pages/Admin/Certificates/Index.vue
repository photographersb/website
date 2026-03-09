<template>
  <div class="min-h-screen">
    <AdminHeader
      title="🎓 Certificate Management"
      subtitle="Issue, verify, and manage platform certificates"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900">Certificates</h1>
        <div class="flex gap-2">
          <router-link to="/admin/certificates/manual-issuance" class="btn-admin-primary">Manual Issue</router-link>
          <router-link to="/admin/certificates/templates" class="btn-admin-secondary">Templates</router-link>
          <router-link to="/admin/certificates/automation-rules" class="btn-admin-secondary">Automation Rules</router-link>
        </div>
      </div>

      <AdminQuickNav />

      <div class="panel grid grid-cols-1 md:grid-cols-5 gap-3">
        <input v-model="filters.search" type="text" placeholder="Search code, recipient, event..." class="field md:col-span-2">
        <select v-model="filters.status" class="field">
          <option value="">All Status</option>
          <option value="issued">Issued</option>
          <option value="revoked">Revoked</option>
        </select>
        <select v-model="filters.type" class="field">
          <option value="">All Types</option>
          <option value="event">Event</option>
          <option value="competition">Competition</option>
          <option value="award">Award</option>
        </select>
        <button class="btn-admin-secondary" @click="loadCertificates">Apply</button>
      </div>

      <div class="panel overflow-x-auto">
        <div v-if="loading" class="py-10 text-center text-gray-500">Loading certificates...</div>
        <div v-else-if="certificates.length === 0" class="py-10 text-center text-gray-500">No certificates found.</div>
        <table v-else class="w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-3">Code</th>
              <th class="py-3">Recipient</th>
              <th class="py-3">Source</th>
              <th class="py-3">Template</th>
              <th class="py-3">Issued</th>
              <th class="py-3">Status</th>
              <th class="py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="certificate in certificates" :key="certificate.id" class="border-b last:border-b-0">
              <td class="py-3 font-medium text-gray-900">{{ certificate.certificate_code }}</td>
              <td class="py-3">{{ certificate.recipient_name }}</td>
              <td class="py-3">{{ certificate.source_title }}</td>
              <td class="py-3">{{ certificate.template_title || '—' }}</td>
              <td class="py-3">{{ formatDate(certificate.issued_at) }}</td>
              <td class="py-3">
                <span :class="['badge', certificate.status === 'issued' ? 'badge-ok' : 'badge-alert']">{{ certificate.status }}</span>
              </td>
              <td class="py-3 text-right">
                <div class="inline-flex items-center gap-3">
                  <button class="link" :disabled="!certificate.has_file" @click="download(certificate)">Download</button>
                  <button class="link" @click="regenerate(certificate)">Regenerate</button>
                  <button class="link" @click="openVerify(certificate)">Verify</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="meta.total > meta.per_page" class="flex items-center justify-between">
        <span class="text-sm text-gray-600">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
        <div class="flex gap-2">
          <button class="btn-admin-secondary" :disabled="meta.current_page <= 1" @click="goTo(meta.current_page - 1)">Prev</button>
          <button class="btn-admin-secondary" :disabled="meta.current_page >= meta.last_page" @click="goTo(meta.current_page + 1)">Next</button>
        </div>
      </div>

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
import { formatDate as formatDateValue } from '../../../utils/formatters'

const loading = ref(false)
const certificates = ref([])
const meta = ref({ total: 0, per_page: 20, current_page: 1, last_page: 1 })
const filters = ref({ search: '', status: '', type: '' })
const toast = ref({ message: '', type: 'success' })

const showToast = (message, type = 'success') => {
  toast.value = { message, type }
  setTimeout(() => {
    toast.value.message = ''
  }, 3500)
}

const formatDate = (value) => {
  if (!value) return '—'
  return formatDateValue(value)
}

const loadCertificates = async (page = 1) => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/certificates', {
      params: {
        page,
        per_page: meta.value.per_page,
        search: filters.value.search || undefined,
        status: filters.value.status || undefined,
        type: filters.value.type || undefined,
      },
    })

    if (data.status === 'success') {
      certificates.value = data.data
      meta.value = data.meta
    }
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to load certificates', 'error')
  } finally {
    loading.value = false
  }
}

const goTo = (page) => {
  loadCertificates(page)
}

const download = (certificate) => {
  window.open(`/api/v1/admin/certificates/${certificate.id}/download`, '_blank')
}

const openVerify = (certificate) => {
  if (certificate.verification_url) {
    window.open(certificate.verification_url, '_blank')
  }
}

const regenerate = async (certificate) => {
  try {
    const { data } = await api.post(`/admin/certificates/${certificate.id}/regenerate`)
    if (data.status === 'success') {
      showToast('Certificate regenerated successfully')
      await loadCertificates(meta.value.current_page)
    }
  } catch (error) {
    showToast(error?.response?.data?.message || 'Regeneration failed', 'error')
  }
}

onMounted(() => {
  loadCertificates()
})
</script>

<style scoped>
.panel { background: #fff; border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1rem; padding: 1rem; box-shadow: 0 12px 28px rgba(18, 9, 6, 0.06); }
.field { width: 100%; border: 1px solid #d1d5db; border-radius: 0.75rem; padding: 0.6rem 0.8rem; }
.btn-admin-primary { background: #8e0e3f; color: #fff; padding: 0.625rem 1rem; border-radius: 0.75rem; border: none; font-weight: 600; }
.btn-admin-secondary { background: #fff; border: 1px solid #d1d5db; color: #111827; padding: 0.625rem 1rem; border-radius: 0.75rem; font-weight: 600; }
.badge { font-size: 0.75rem; border-radius: 9999px; padding: 0.2rem 0.65rem; text-transform: capitalize; }
.badge-ok { background: #dcfce7; color: #166534; }
.badge-alert { background: #fee2e2; color: #991b1b; }
.link { color: #8e0e3f; font-weight: 600; background: none; border: none; cursor: pointer; }
.link:disabled { color: #9ca3af; cursor: not-allowed; }
</style>
