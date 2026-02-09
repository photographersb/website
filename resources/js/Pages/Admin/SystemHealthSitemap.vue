<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">
            Sitemap Health
          </h1>
          <p class="text-gray-600 mt-2">
            Monitor sitemap generation status and submission health
          </p>
        </div>
        
        <AdminQuickNav />

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Sitemaps Active
            </p>
            <p class="text-2xl font-bold text-green-600">
              {{ sitemaps.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Last Generated
            </p>
            <p class="text-lg font-bold text-gray-900">
              {{ lastGenerated }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total URLs
            </p>
            <p class="text-2xl font-bold text-blue-600">
              {{ totalUrls }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Errors
            </p>
            <p
              class="text-2xl font-bold"
              :class="totalErrors > 0 ? 'text-red-600' : 'text-green-600'"
            >
              {{ totalErrors }}
            </p>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Sitemap Actions
          </h2>
          <div class="flex flex-wrap gap-3">
            <button
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              @click="generateSitemap"
            >
              Regenerate All
            </button>
            <button
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
              @click="submitSitemaps"
            >
              Submit to Search Engines
            </button>
            <button
              class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
              @click="checkStatus"
            >
              Check Status
            </button>
          </div>
        </div>

        <div
          v-if="!loading"
          class="bg-white rounded-lg shadow-md overflow-hidden"
        >
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Sitemap
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    URL
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    URLs
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Last Generated
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Status
                  </th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="map in sitemaps"
                  :key="map.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">
                      {{ map.name }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ map.type }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-blue-600">
                    {{ map.url }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                    {{ map.urls }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(map.generated_at) }}
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', map.status === 'healthy' ? 'bg-green-500' : map.status === 'warning' ? 'bg-yellow-500' : 'bg-red-500']">
                      {{ map.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                        @click="viewSitemap(map.id)"
                      >
                        View
                      </button>
                      <button
                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                        @click="regenerateMap(map.id)"
                      >
                        Regenerate
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div
          v-if="loading"
          class="flex justify-center py-12"
        >
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
        </div>
    </div>
    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDateTime } from '../../utils/formatters'
import api from '../../api'

const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const sitemaps = ref([])
const lastGenerated = ref('N/A')

const totalUrls = computed(() => sitemaps.value.reduce((sum, m) => sum + (m.urls || 0), 0))
const totalErrors = computed(() => sitemaps.value.filter(m => m.status !== 'healthy').length)

const formatDate = (date) => (date ? formatDateTime(date) : 'N/A')

const fetchHealth = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/sitemaps/health')
    if (data.status === 'success') {
      sitemaps.value = data.data?.sitemaps || []
      lastGenerated.value = data.data?.stats?.last_generated
        ? formatDate(data.data.stats.last_generated)
        : 'N/A'
    }
  } catch (error) {
    console.error('Failed to load sitemap health:', error)
    toast.value = { show: true, message: 'Failed to load sitemap health', type: 'error' }
  } finally {
    loading.value = false
  }
}

const generateSitemap = () => {
  toast.value = { show: true, message: 'Regenerating all sitemaps...', type: 'info' }
}

const submitSitemaps = () => {
  toast.value = { show: true, message: 'Submitting sitemaps to search engines...', type: 'success' }
}

const checkStatus = () => {
  toast.value = { show: true, message: 'Refreshing sitemap status...', type: 'info' }
  fetchHealth()
}

const viewSitemap = (id) => {
  const map = sitemaps.value.find((item) => item.id === id)
  if (map?.url) {
    window.open(map.url, '_blank')
    return
  }
  toast.value = { show: true, message: `Opening sitemap ${id}...`, type: 'info' }
}

const regenerateMap = (id) => {
  toast.value = { show: true, message: `Regenerating sitemap ${id}...`, type: 'success' }
}

onMounted(() => {
  fetchHealth()
})
</script>
