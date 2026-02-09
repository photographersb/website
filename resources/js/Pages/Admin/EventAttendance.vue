<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Event Attendance
        </h1>
        <p class="text-gray-600 mt-2">
          Track and manage participant attendance for events
        </p>
      </div>
      
      <AdminQuickNav />

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">
              Total Events
            </p>
            <p class="text-3xl font-bold text-gray-900">
              {{ events.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">
              Checked In
            </p>
            <p class="text-3xl font-bold text-green-600">
              {{ totalCheckedIn }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">
              No-Show Rate
            </p>
            <p class="text-3xl font-bold text-red-600">
              {{ noShowRate }}%
            </p>
          </div>
        </div>

        <!-- Events List -->
        <div
          v-if="!loading"
          class="space-y-6"
        >
          <div
            v-for="event in events"
            :key="event.id"
            class="bg-white rounded-lg shadow-md overflow-hidden"
          >
            <!-- Event Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="text-xl font-bold">
                    {{ event.name }}
                  </h3>
                  <p class="text-blue-100 mt-1">
                    {{ formatDate(event.date) }} • {{ event.location }}
                  </p>
                </div>
                <span :class="['px-4 py-2 rounded-full text-sm font-semibold', event.status === 'ongoing' ? 'bg-green-500' : event.status === 'upcoming' ? 'bg-yellow-500' : 'bg-gray-500']">
                  {{ event.status }}
                </span>
              </div>
            </div>

            <!-- Attendance Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6 border-b border-gray-200">
              <div>
                <p class="text-gray-600 text-sm">
                  Registered
                </p>
                <p class="text-2xl font-bold text-gray-900">
                  {{ event.registered }}
                </p>
              </div>
              <div>
                <p class="text-gray-600 text-sm">
                  Checked In
                </p>
                <p class="text-2xl font-bold text-green-600">
                  {{ event.checkedIn }}
                </p>
              </div>
              <div>
                <p class="text-gray-600 text-sm">
                  Pending
                </p>
                <p class="text-2xl font-bold text-yellow-600">
                  {{ event.pending }}
                </p>
              </div>
              <div>
                <p class="text-gray-600 text-sm">
                  No-Show
                </p>
                <p class="text-2xl font-bold text-red-600">
                  {{ event.noShow }}
                </p>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="px-6 py-4 bg-gray-50">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-sm text-gray-600">Attendance Rate:</span>
                <span class="font-semibold">{{ Math.round((event.checkedIn / event.registered) * 100) }}%</span>
              </div>
              <div class="w-full bg-gray-300 rounded-full h-2">
                <div
                  class="bg-green-500 h-2 rounded-full"
                  :style="{ width: Math.round((event.checkedIn / event.registered) * 100) + '%' }"
                />
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-6 flex gap-3">
              <button
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                @click="viewAttendance(event.id)"
              >
                View Attendance
              </button>
              <button
                v-if="event.status === 'ongoing'"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                @click="markCheckIn(event.id)"
              >
                Quick Check-In
              </button>
              <button
                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
                @click="downloadReport(event.id)"
              >
                Download Report
              </button>
            </div>
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

const events = ref([
  {
    id: 1,
    name: 'Annual Photography Conference 2026',
    date: '2026-02-15',
    location: 'Convention Center, Downtown',
    status: 'ongoing',
    registered: 245,
    checkedIn: 198,
    pending: 32,
    noShow: 15
  },
  {
    id: 2,
    name: 'Wildlife Photography Workshop',
    date: '2026-02-22',
    location: 'Nature Reserve',
    status: 'upcoming',
    registered: 89,
    checkedIn: 0,
    pending: 89,
    noShow: 0
  },
  {
    id: 3,
    name: 'Portrait Mastery Series - Week 1',
    date: '2026-01-30',
    location: 'Studio A',
    status: 'completed',
    registered: 156,
    checkedIn: 142,
    pending: 0,
    noShow: 14
  },
  {
    id: 4,
    name: 'Lighting Techniques Masterclass',
    date: '2026-03-05',
    location: 'Technical Building',
    status: 'upcoming',
    registered: 203,
    checkedIn: 0,
    pending: 203,
    noShow: 0
  }
])

const totalRegistrations = computed(() => {
  return events.value.reduce((sum, event) => sum + event.registered, 0)
})

const totalCheckedIn = computed(() => {
  return events.value.reduce((sum, event) => sum + event.checkedIn, 0)
})

const noShowRate = computed(() => {
  const total = totalRegistrations.value
  const noShows = events.value.reduce((sum, event) => sum + event.noShow, 0)
  return total > 0 ? Math.round((noShows / total) * 100) : 0
})

const formatDate = (date) => {
  return formatDateValue(date)
}

const viewAttendance = (eventId) => {
  const event = events.value.find(e => e.id === eventId)
  toast.value = { show: true, message: `Loading attendance list for ${event.name}...`, type: 'info' }
}

const markCheckIn = (eventId) => {
  const event = events.value.find(e => e.id === eventId)
  toast.value = { show: true, message: 'Check-in mode enabled. Scan QR codes to mark attendance.', type: 'info' }
}

const downloadReport = (eventId) => {
  const event = events.value.find(e => e.id === eventId)
  toast.value = { show: true, message: `Downloading report for ${event.name}...`, type: 'success' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 800)
})
</script>
