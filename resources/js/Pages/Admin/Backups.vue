<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Database Backups
          </h1>
          <p class="text-gray-600 mt-2">
            Manage system backups and recovery
          </p>
        </div>
        
        <button
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2"
          @click="createBackup"
        >
          <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            />
          </svg>
          Create Backup Now
        </button>
      </div>

      <AdminQuickNav />

        <!-- Auto-Backup Settings -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Auto-Backup Settings
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Backup Frequency</label>
              <select
                v-model="backupSettings.frequency"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
              >
                <option value="hourly">
                  Hourly
                </option>
                <option value="daily">
                  Daily
                </option>
                <option value="weekly">
                  Weekly
                </option>
                <option value="monthly">
                  Monthly
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Retention Days</label>
              <input
                v-model.number="backupSettings.retentionDays"
                type="number"
                min="1"
                max="365"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Storage Location</label>
              <select
                v-model="backupSettings.storage"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
              >
                <option value="local">
                  Local Storage
                </option>
                <option value="aws">
                  AWS S3
                </option>
                <option value="azure">
                  Azure Storage
                </option>
              </select>
            </div>
          </div>
          <div class="mt-4 flex gap-2">
            <button
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
              @click="saveBackupSettings"
            >
              Save Settings
            </button>
            <button
              class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
              @click="testBackup"
            >
              Test Backup
            </button>
          </div>
        </div>

        <!-- Backup Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Backups
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ backups.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Last Backup
            </p>
            <p class="text-lg font-bold text-gray-900">
              {{ lastBackupTime }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Size
            </p>
            <p class="text-lg font-bold text-gray-900">
              {{ totalBackupSize }}GB
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Free Storage
            </p>
            <p class="text-lg font-bold text-gray-900">
              {{ freeStorage }}GB
            </p>
          </div>
        </div>

        <!-- Backups List -->
        <div
          v-if="!loading"
          class="bg-white rounded-lg shadow-md overflow-hidden"
        >
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Backup ID
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Created Date
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Size
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Duration
                  </th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="backup in backups"
                  :key="backup.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    {{ backup.id }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(backup.createdAt) }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                    {{ backup.size }}MB
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <span class="inline-flex px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                      {{ backup.type }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', backup.status === 'completed' ? 'bg-green-500' : backup.status === 'failed' ? 'bg-red-500' : 'bg-yellow-500']">
                      {{ backup.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ backup.duration }}s
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        v-if="backup.status === 'completed'"
                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                        title="Download"
                        @click="downloadBackup(backup.id)"
                      >
                        ⬇️
                      </button>
                      <button
                        v-if="backup.status === 'completed'"
                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition"
                        title="Restore"
                        @click="restoreBackup(backup.id)"
                      >
                        ↻
                      </button>
                      <button
                        class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition"
                        title="Delete"
                        @click="deleteBackup(backup.id)"
                      >
                        ✕
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Loading -->
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
import { formatDate as formatDateValue } from '../../utils/formatters'
const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })

const backupSettings = ref({
  frequency: 'daily',
  retentionDays: 30,
  storage: 'local'
})

const backups = ref([
  {
    id: 'BK-2026-01-04-001',
    createdAt: '2026-01-04 02:00:00',
    size: 2847,
    type: 'full',
    status: 'completed',
    duration: 45
  },
  {
    id: 'BK-2026-01-03-001',
    createdAt: '2026-01-03 02:00:00',
    size: 2812,
    type: 'full',
    status: 'completed',
    duration: 42
  },
  {
    id: 'BK-2026-01-02-001',
    createdAt: '2026-01-02 02:00:00',
    size: 2798,
    type: 'full',
    status: 'completed',
    duration: 44
  },
  {
    id: 'BK-2026-01-01-001',
    createdAt: '2026-01-01 02:00:00',
    size: 2756,
    type: 'full',
    status: 'completed',
    duration: 40
  },
  {
    id: 'BK-2025-12-31-001',
    createdAt: '2025-12-31 02:00:00',
    size: 2734,
    type: 'full',
    status: 'completed',
    duration: 43
  }
])

const lastBackupTime = computed(() => {
  if (backups.value.length === 0) return 'Never'
  const last = backups.value[0]
  const date = new Date(last.createdAt)
  const today = new Date()
  const diff = today - date
  const hours = Math.floor(diff / (1000 * 60 * 60))
  if (hours < 1) return 'Just now'
  if (hours < 24) return `${hours}h ago`
  return formatDate(last.createdAt)
})

const totalBackupSize = computed(() => {
  const total = backups.value.reduce((sum, b) => sum + b.size, 0)
  return (total / 1024).toFixed(2)
})

const freeStorage = computed(() => {
  return (500 - parseFloat(totalBackupSize.value)).toFixed(2)
})

const formatDate = (date) => {
  return formatDateValue(date)
}

const createBackup = () => {
  const newBackup = {
    id: `BK-${new Date().toISOString().split('T')[0]}-${String(backups.value.length + 1).padStart(3, '0')}`,
    createdAt: new Date().toISOString(),
    size: Math.floor(Math.random() * 500 + 2700),
    type: 'full',
    status: 'processing',
    duration: 0
  }
  backups.value.unshift(newBackup)
  setTimeout(() => {
    newBackup.status = 'completed'
    newBackup.duration = Math.floor(Math.random() * 10 + 35)
    toast.value = { show: true, message: 'Backup created successfully!', type: 'success' }
  }, 2000)
}

const downloadBackup = (backupId) => {
  toast.value = { show: true, message: `Downloading backup ${backupId}...`, type: 'info' }
}

const restoreBackup = (backupId) => {
  if (confirm(`Are you sure you want to restore from backup ${backupId}? This will overwrite current data.`)) {
    toast.value = { show: true, message: `Restoring from ${backupId}...`, type: 'warning' }
  }
}

const deleteBackup = (backupId) => {
  const index = backups.value.findIndex(b => b.id === backupId)
  if (index > -1) {
    backups.value.splice(index, 1)
    toast.value = { show: true, message: `Backup ${backupId} deleted!`, type: 'success' }
  }
}

const saveBackupSettings = () => {
  toast.value = { show: true, message: 'Backup settings saved!', type: 'success' }
}

const testBackup = () => {
  toast.value = { show: true, message: 'Testing backup connection...', type: 'info' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 800)
})
</script>
