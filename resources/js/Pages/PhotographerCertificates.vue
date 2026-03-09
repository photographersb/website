<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">My Certificates</h1>
          <p class="text-sm text-gray-600">Download, verify, and share your issued certificates.</p>
        </div>
        <router-link to="/dashboard" class="btn-secondary">Back to Dashboard</router-link>
      </div>

      <div class="panel">
        <div v-if="loading" class="py-10 text-center text-gray-500">Loading certificates...</div>
        <div v-else-if="certificates.length === 0" class="py-10 text-center text-gray-500">No certificates issued yet.</div>

        <div v-else class="space-y-4">
          <div
            v-for="certificate in certificates"
            :key="certificate.id"
            class="border border-gray-200 rounded-xl p-4 sm:p-5 bg-white"
          >
            <div class="flex flex-col sm:flex-row gap-4 sm:items-start sm:justify-between">
              <div class="flex items-start gap-4 min-w-0">
                <img
                  v-if="certificate.preview_png_url"
                  :src="certificate.preview_png_url"
                  :alt="certificate.title"
                  class="w-16 h-16 rounded-lg object-cover border"
                >
                <div v-else class="w-16 h-16 rounded-lg bg-gray-100 border flex items-center justify-center text-gray-500">🎓</div>

                <div class="min-w-0">
                  <h2 class="text-lg font-semibold text-gray-900 truncate">{{ certificate.title }}</h2>
                  <p class="text-sm text-gray-600 truncate">{{ certificate.source || 'Photographer SB' }}</p>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ certificate.certificate_code }} · {{ formatDate(certificate.issued_at) }}
                  </p>
                </div>
              </div>

              <div class="flex flex-wrap gap-2">
                <a :href="certificate.download_pdf_url" target="_blank" class="btn-primary">PDF</a>
                <a :href="certificate.download_png_url" target="_blank" class="btn-secondary">PNG</a>
                <a :href="certificate.verify_url" target="_blank" class="btn-secondary">Verify</a>
                <button class="btn-secondary" @click="openShare(certificate, 'instagram_post')">Share</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="meta.total > meta.per_page" class="flex items-center justify-between">
        <span class="text-sm text-gray-600">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
        <div class="flex gap-2">
          <button class="btn-secondary" :disabled="meta.current_page <= 1" @click="load(meta.current_page - 1)">Prev</button>
          <button class="btn-secondary" :disabled="meta.current_page >= meta.last_page" @click="load(meta.current_page + 1)">Next</button>
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
import api from '../api'
import { formatDate as formatDateValue } from '../utils/formatters'

const loading = ref(false)
const certificates = ref([])
const meta = ref({ total: 0, per_page: 20, current_page: 1, last_page: 1 })
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

const load = async (page = 1) => {
  loading.value = true
  try {
    const { data } = await api.get('/my-certificates', {
      params: {
        page,
        per_page: meta.value.per_page,
      },
    })

    if (data.status === 'success') {
      certificates.value = data.data || []
      meta.value = data.meta || meta.value
    }
  } catch (error) {
    showToast(error?.response?.data?.message || 'Failed to load certificates', 'error')
  } finally {
    loading.value = false
  }
}

const openShare = async (certificate, size) => {
  try {
    const { data } = await api.get(`/my-certificates/${certificate.id}/share/${size}`)
    if (data.status === 'success' && data.data?.url) {
      window.open(data.data.url, '_blank')
      showToast('Share image opened in a new tab')
    }
  } catch (error) {
    showToast(error?.response?.data?.message || 'Share image is not available yet', 'error')
  }
}

onMounted(() => {
  load()
})
</script>

<style scoped>
.panel { background: #fff; border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1rem; padding: 1rem; box-shadow: 0 12px 28px rgba(18, 9, 6, 0.06); }
.btn-primary { background: #8e0e3f; color: #fff; padding: 0.5rem 0.85rem; border-radius: 0.65rem; border: none; font-weight: 600; font-size: 0.85rem; }
.btn-secondary { background: #fff; border: 1px solid #d1d5db; color: #111827; padding: 0.5rem 0.85rem; border-radius: 0.65rem; font-weight: 600; font-size: 0.85rem; }
button:disabled { opacity: .6; cursor: not-allowed; }
</style>
