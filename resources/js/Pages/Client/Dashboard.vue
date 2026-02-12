<template>
  <div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-6xl mx-auto space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Client Dashboard</h1>
          <p class="text-gray-600">Manage your bookings and stay on top of your photography projects.</p>
        </div>
        <router-link
          to="/bookings"
          class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-burgundy text-white font-semibold hover:bg-[#6F112D]"
        >
          View All Bookings
        </router-link>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <p class="text-xs font-semibold uppercase text-gray-500">Total Bookings</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ totalBookings }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <p class="text-xs font-semibold uppercase text-gray-500">Pending</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ pendingBookings }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <p class="text-xs font-semibold uppercase text-gray-500">Active</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ activeBookings }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <p class="text-xs font-semibold uppercase text-gray-500">Completed</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ completedBookings }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <router-link
          to="/client/galleries"
          class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <p class="text-sm font-semibold text-gray-900">Photo Galleries</p>
          <p class="text-sm text-gray-600 mt-1">Access delivered albums from your bookings.</p>
        </router-link>
        <router-link
          to="/client/favorites"
          class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <p class="text-sm font-semibold text-gray-900">Favorite Photographers</p>
          <p class="text-sm text-gray-600 mt-1">Keep a shortlist for future shoots.</p>
        </router-link>
        <router-link
          to="/client/payments"
          class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <p class="text-sm font-semibold text-gray-900">Payments</p>
          <p class="text-sm text-gray-600 mt-1">Review your transaction history.</p>
        </router-link>
        <router-link
          to="/client/notifications"
          class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <p class="text-sm font-semibold text-gray-900">Notifications</p>
          <p class="text-sm text-gray-600 mt-1">Stay updated on bookings and updates.</p>
        </router-link>
        <router-link
          to="/"
          class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <p class="text-sm font-semibold text-gray-900">Find Photographers</p>
          <p class="text-sm text-gray-600 mt-1">Browse new portfolios and request bookings.</p>
        </router-link>
        <router-link
          to="/competitions"
          class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <p class="text-sm font-semibold text-gray-900">Competitions</p>
          <p class="text-sm text-gray-600 mt-1">Vote and follow your favorite entries.</p>
        </router-link>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Recent Bookings</h2>
          <router-link
            to="/bookings"
            class="text-sm font-semibold text-burgundy-600 hover:text-burgundy-700"
          >
            Manage bookings
          </router-link>
        </div>

        <div
          v-if="loading"
          class="flex items-center justify-center py-12"
        >
          <div
            class="animate-spin rounded-full h-10 w-10 border-b-2"
            style="border-bottom-color: var(--admin-brand-primary);"
          />
        </div>

        <div
          v-else-if="recentBookings.length"
          class="divide-y divide-gray-200"
        >
          <div
            v-for="booking in recentBookings"
            :key="booking.id"
            class="px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3"
          >
            <div>
              <p class="font-semibold text-gray-900">
                {{ booking.photographer?.business_name || booking.photographer?.user?.name || 'Photographer' }}
              </p>
              <p class="text-sm text-gray-600">
                {{ formatDate(booking.event_date) }} • {{ booking.event_location || 'Location TBD' }}
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-start md:justify-end">
              <span
                :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold',
                  getStatusClass(booking.status)
                ]"
              >
                {{ capitalizeFirst(booking.status) }}
              </span>
              <span class="text-sm font-semibold text-gray-900">৳{{ formatNumber(booking.total_price) }}</span>
              <router-link
                :to="`/bookings/${booking.id}/messages`"
                class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-burgundy-100 text-burgundy-700 hover:bg-burgundy-200"
              >
                Messages
              </router-link>
              <button
                v-if="booking.status === 'completed' && !booking.review"
                class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200"
                @click="writeReview(booking)"
              >
                Write Review
              </button>
              <button
                v-if="booking.status === 'pending'"
                class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-red-100 text-red-700 hover:bg-red-200"
                @click="cancelBooking(booking)"
              >
                Cancel
              </button>
              <button
                class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200"
                @click="viewDetails(booking)"
              >
                Details
              </button>
            </div>
          </div>
        </div>

        <div
          v-else
          class="px-5 py-12 text-center"
        >
          <p class="text-gray-600">No bookings yet. When you book a photographer, they will appear here.</p>
          <router-link
            to="/"
            class="inline-flex items-center justify-center mt-4 px-5 py-2.5 rounded-lg bg-burgundy text-white font-semibold hover:bg-[#6F112D]"
          >
            Start Browsing
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../api'
import { formatDate as formatDateValue, formatNumber } from '../../utils/formatters'

const router = useRouter()
const bookings = ref([])
const loading = ref(true)

const totalBookings = computed(() => bookings.value.length)
const pendingBookings = computed(() => bookings.value.filter((booking) => booking.status === 'pending').length)
const activeBookings = computed(() => bookings.value.filter((booking) => booking.status === 'confirmed').length)
const completedBookings = computed(() => bookings.value.filter((booking) => booking.status === 'completed').length)

const recentBookings = computed(() => {
  const sorted = [...bookings.value].sort((a, b) => {
    const aDate = getSortDate(a)
    const bDate = getSortDate(b)
    return bDate - aDate
  })
  return sorted.slice(0, 5)
})

onMounted(() => {
  loadBookings()
})

const loadBookings = async () => {
  loading.value = true
  try {
    const response = await api.get('/bookings')
    bookings.value = response.data.data || []
  } catch (error) {
    if (error.response?.status === 401) {
      router.push('/auth')
      return
    }
    console.error('Failed to load bookings:', error)
  } finally {
    loading.value = false
  }
}

const getSortDate = (booking) => {
  const dateValue = booking?.created_at || booking?.event_date || 0
  return new Date(dateValue).getTime() || 0
}

const formatDate = (dateString) => formatDateValue(dateString)

const capitalizeFirst = (value) => {
  if (!value) return ''
  return value.charAt(0).toUpperCase() + value.slice(1)
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    rejected: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const writeReview = (booking) => {
  router.push(`/review/${booking.photographer_id}`)
}

const cancelBooking = async (booking) => {
  if (!confirm('Are you sure you want to cancel this booking?')) {
    return
  }

  try {
    await api.patch(`/bookings/${booking.id}/cancel`)
    alert('Booking cancelled successfully')
    loadBookings()
  } catch (error) {
    console.error('Failed to cancel booking:', error)
    alert('Failed to cancel booking. Please try again.')
  }
}

const viewDetails = (booking) => {
  alert(`Booking Details:\n\nID: ${booking.id}\nPhotographer: ${booking.photographer?.business_name || 'N/A'}\nDate: ${formatDate(booking.event_date)}\nLocation: ${booking.event_location}\nPrice: ৳${formatNumber(booking.total_price)}\nStatus: ${capitalizeFirst(booking.status)}`)
}
</script>
