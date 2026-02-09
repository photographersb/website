<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Data Hub
        </h1>
        <p class="text-gray-600 mt-2">
          Centralized insights, exports, and data management
        </p>
      </div>
      
      <AdminQuickNav />

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Records
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ totals.records }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Exports This Month
            </p>
            <p class="text-2xl font-bold text-blue-600">
              {{ totals.exports }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Pending Jobs
            </p>
            <p class="text-2xl font-bold text-yellow-600">
              {{ totals.pendingJobs }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Failed Jobs
            </p>
            <p class="text-2xl font-bold text-red-600">
              {{ totals.failedJobs }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
              Quick Exports
            </h2>
            <div class="space-y-3">
              <button
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                @click="exportData('users')"
              >
                Export Users
              </button>
              <button
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                @click="exportData('bookings')"
              >
                Export Bookings
              </button>
              <button
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                @click="exportData('payments')"
              >
                Export Payments
              </button>
              <button
                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                @click="exportData('photographers')"
              >
                Export Photographers
              </button>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
              Data Cleanup
            </h2>
            <div class="space-y-3">
              <button
                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                @click="cleanup('logs')"
              >
                Purge Old Logs
              </button>
              <button
                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                @click="cleanup('drafts')"
              >
                Remove Draft Records
              </button>
              <button
                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                @click="cleanup('cache')"
              >
                Clear Cache
              </button>
            </div>
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
                    Job
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Created
                  </th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="job in jobs"
                  :key="job.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">
                      {{ job.name }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ job.file }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700 capitalize">
                    {{ job.type }}
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', job.status === 'completed' ? 'bg-green-500' : job.status === 'failed' ? 'bg-red-500' : 'bg-yellow-500']">
                      {{ job.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(job.createdAt) }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <button
                      v-if="job.status === 'completed'"
                      class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                      @click="download(job.id)"
                    >
                      Download
                    </button>
                    <button
                      v-else
                      class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
                      @click="retry(job.id)"
                    >
                      Retry
                    </button>
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
import { ref, onMounted } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDate as formatDateValue } from '../../utils/formatters'
const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })

const totals = ref({
  records: 154230,
  exports: 12,
  pendingJobs: 3,
  failedJobs: 1
})

const jobs = ref([
  { id: 1, name: 'Users Export', file: 'users_2026_02_05.csv', type: 'export', status: 'completed', createdAt: '2026-02-05' },
  { id: 2, name: 'Payments Export', file: 'payments_2026_02_04.csv', type: 'export', status: 'completed', createdAt: '2026-02-04' },
  { id: 3, name: 'Bookings Export', file: 'bookings_2026_02_03.csv', type: 'export', status: 'processing', createdAt: '2026-02-03' },
  { id: 4, name: 'Logs Cleanup', file: 'cleanup_logs_2026_02_02', type: 'cleanup', status: 'failed', createdAt: '2026-02-02' }
])

const formatDate = (date) => {
  return formatDateValue(date)
}

const exportData = (type) => {
  toast.value = { show: true, message: `Starting ${type} export...`, type: 'info' }
}

const cleanup = (type) => {
  toast.value = { show: true, message: `Running ${type} cleanup...`, type: 'info' }
}

const download = (id) => {
  toast.value = { show: true, message: `Downloading job ${id}...`, type: 'success' }
}

const retry = (id) => {
  toast.value = { show: true, message: `Retrying job ${id}...`, type: 'info' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 700)
})
</script>
