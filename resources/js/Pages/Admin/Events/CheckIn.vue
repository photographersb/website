<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="✅ Event Check-In"
      subtitle="Scan tickets and manage attendee check-ins"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="sb-ui-card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">
              {{ event?.title || 'Event' }}
            </h2>
            <p class="text-sm text-gray-500">
              {{ event?.location || 'Location not set' }} • {{ formatDate(event?.event_date) }}
            </p>
          </div>
          <div class="flex flex-wrap gap-2">
            <button
              class="sb-ui-btn sb-ui-btn--secondary"
              @click="downloadReport"
            >
              Export Report
            </button>
            <InertiaLink
              href="/admin/events"
              class="sb-ui-btn sb-ui-btn--primary"
            >
              Back to Events
            </InertiaLink>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="sb-ui-card p-4">
          <p class="text-sm text-gray-500">
            Confirmed
          </p>
          <p class="text-2xl font-semibold text-gray-900">
            {{ stats.total_confirmed || 0 }}
          </p>
        </div>
        <div class="sb-ui-card p-4">
          <p class="text-sm text-gray-500">
            Attended
          </p>
          <p class="text-2xl font-semibold text-gray-900">
            {{ stats.total_attended || 0 }}
          </p>
        </div>
        <div class="sb-ui-card p-4">
          <p class="text-sm text-gray-500">
            Pending
          </p>
          <p class="text-2xl font-semibold text-gray-900">
            {{ stats.pending_checkin || 0 }}
          </p>
        </div>
      </div>

      <div class="sb-ui-card p-6 space-y-4">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">
            Scan QR Code
          </h3>
          <p class="text-sm text-gray-500">
            Paste or scan the attendee QR token
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
          <input
            v-model="scanToken"
            type="text"
            placeholder="QR token"
            class="sb-ui-input flex-1"
          >
          <button
            class="sb-ui-btn sb-ui-btn--primary"
            @click="scanQr"
          >
            Check In
          </button>
        </div>
        <p
          v-if="scanMessage"
          :class="scanMessageClass"
          class="text-sm"
        >
          {{ scanMessage }}
        </p>
      </div>

      <div class="sb-ui-card p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">
              Registrations
            </h3>
            <p class="text-sm text-gray-500">
              Confirmed registrations eligible for check-in
            </p>
          </div>
          <div class="flex items-center gap-2">
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search name or email"
              class="sb-ui-input"
              @input="debounceSearch"
            >
          </div>
        </div>

        <div
          v-if="loading"
          class="py-10 text-center text-gray-500"
        >
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-burgundy" />
          <p class="mt-2">
            Loading registrations...
          </p>
        </div>

        <div
          v-else
          class="overflow-x-auto"
        >
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Attendee
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ticket
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Checked In At
                </th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="registrations.length === 0">
                <td
                  colspan="5"
                  class="px-4 py-6 text-center text-gray-500"
                >
                  No registrations found
                </td>
              </tr>
              <tr
                v-for="reg in registrations"
                :key="reg.id"
                class="hover:bg-gray-50"
              >
                <td class="px-4 py-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ reg.user?.name || 'Unknown' }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ reg.user?.email || '—' }}
                  </div>
                </td>
                <td class="px-4 py-4 text-sm text-gray-600">
                  {{ reg.ticket?.title || 'N/A' }}
                </td>
                <td class="px-4 py-4">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="reg.status === 'attended' ? 'badge-success' : 'badge-warning'"
                  >
                    {{ reg.status }}
                  </span>
                </td>
                <td class="px-4 py-4 text-sm text-gray-600">
                  {{ reg.attended_at ? formatDateTime(reg.attended_at) : '—' }}
                </td>
                <td class="px-4 py-4 text-right text-sm">
                  <button
                    v-if="reg.status !== 'attended'"
                    class="sb-ui-btn sb-ui-btn--primary sb-ui-btn--sm"
                    @click="manualCheckIn(reg.id)"
                  >
                    Check In
                  </button>
                  <button
                    v-else
                    class="sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm"
                    @click="undoCheckIn(reg.id)"
                  >
                    Undo
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div
          v-if="meta.total > 0"
          class="flex items-center justify-between text-sm text-gray-600"
        >
          <span>Showing {{ registrations.length }} of {{ meta.total }}</span>
          <div class="flex items-center gap-2">
            <button
              :disabled="meta.current_page <= 1"
              class="sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm disabled:opacity-50"
              @click="changePage(meta.current_page - 1)"
            >
              Previous
            </button>
            <span>Page {{ meta.current_page }} of {{ meta.last_page }}</span>
            <button
              :disabled="meta.current_page >= meta.last_page"
              class="sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm disabled:opacity-50"
              @click="changePage(meta.current_page + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/api'
import { Link as InertiaLink } from '@inertiajs/vue3'
import AdminHeader from '@/components/AdminHeader.vue'
import AdminQuickNav from '@/components/AdminQuickNav.vue'
import { formatDate as formatDateValue, formatDateTime as formatDateTimeValue } from '@/utils/formatters'

export default {
  name: 'AdminEventCheckIn',
  components: { AdminHeader, AdminQuickNav, InertiaLink },
  data() {
    return {
      event: null,
      stats: {},
      registrations: [],
      meta: { current_page: 1, last_page: 1, total: 0 },
      loading: false,
      scanToken: '',
      scanMessage: '',
      scanSuccess: false,
      filters: {
        search: '',
        page: 1,
      },
      searchTimeout: null,
    }
  },
  computed: {
    eventId() {
      const segments = window.location.pathname.split('/').filter(Boolean)
      const checkInIndex = segments.indexOf('check-in')
      if (checkInIndex > 0) {
        return segments[checkInIndex - 1]
      }
      return segments[segments.length - 1]
    },
    scanMessageClass() {
      return this.scanSuccess ? 'text-success-700' : 'text-danger-700'
    },
  },
  created() {
    this.loadEvent()
    this.fetchRegistrations()
  },
  methods: {
    async loadEvent() {
      try {
        const { data } = await api.get(`/admin/events/${this.eventId}/check-in`)
        this.event = data.event
        this.stats = data.stats || {}
      } catch (error) {
        this.scanMessage = error.response?.data?.message || 'Failed to load event details.'
        this.scanSuccess = false
      }
    },
    async fetchRegistrations(page = 1) {
      this.loading = true
      this.filters.page = page
      try {
        const { data } = await api.get(`/admin/events/${this.eventId}/check-in/registrations`, {
          params: {
            search: this.filters.search,
            page: this.filters.page,
          },
        })
        this.registrations = data.data || []
        this.meta = {
          current_page: data.current_page,
          last_page: data.last_page,
          total: data.total,
        }
      } catch (error) {
        this.scanMessage = error.response?.data?.message || 'Failed to load registrations.'
        this.scanSuccess = false
      } finally {
        this.loading = false
      }
    },
    debounceSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.fetchRegistrations(1)
      }, 400)
    },
    async scanQr() {
      if (!this.scanToken) {
        this.scanMessage = 'Please enter a QR token.'
        this.scanSuccess = false
        return
      }
      try {
        const { data } = await api.post(`/admin/events/${this.eventId}/check-in/scan`, {
          qr_token: this.scanToken,
        })
        this.scanMessage = data.message || 'Check-in successful.'
        this.scanSuccess = data.success !== false
        this.scanToken = ''
        await this.loadEvent()
        await this.fetchRegistrations(this.meta.current_page)
      } catch (error) {
        this.scanMessage = error.response?.data?.message || 'Check-in failed.'
        this.scanSuccess = false
      }
    },
    async manualCheckIn(registrationId) {
      try {
        const { data } = await api.post(`/admin/events/${this.eventId}/check-in/manual`, {
          registration_id: registrationId,
        })
        this.scanMessage = data.message || 'Check-in successful.'
        this.scanSuccess = true
        await this.loadEvent()
        await this.fetchRegistrations(this.meta.current_page)
      } catch (error) {
        this.scanMessage = error.response?.data?.error || 'Check-in failed.'
        this.scanSuccess = false
      }
    },
    async undoCheckIn(registrationId) {
      try {
        const { data } = await api.post(`/admin/registrations/${registrationId}/check-in/undo`)
        this.scanMessage = data.message || 'Check-in undone.'
        this.scanSuccess = true
        await this.loadEvent()
        await this.fetchRegistrations(this.meta.current_page)
      } catch (error) {
        this.scanMessage = error.response?.data?.error || 'Undo failed.'
        this.scanSuccess = false
      }
    },
    changePage(page) {
      if (page < 1 || page > this.meta.last_page) return
      this.fetchRegistrations(page)
    },
    downloadReport() {
      window.open(`/api/v1/admin/events/${this.eventId}/check-in/export`, '_blank')
    },
    formatDate(dateString) {
      if (!dateString) return '—'
      return formatDateValue(dateString)
    },
    formatDateTime(dateString) {
      if (!dateString) return '—'
      return formatDateTimeValue(dateString)
    },
  },
}
</script>
