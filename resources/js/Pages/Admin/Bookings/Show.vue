<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Booking Header -->
      <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Booking #{{ booking.code }}</h1>
            <p class="text-gray-600 mt-2">{{ booking.event_date }}</p>
          </div>
          <span :class="getStatusClass(booking.status)" class="px-4 py-2 rounded-full text-white font-medium">
            {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
          </span>
        </div>

        <!-- Key Info -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div>
            <p class="text-gray-600 text-sm">Duration</p>
            <p class="text-lg font-semibold">{{ booking.duration_hours }} hours</p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">Budget</p>
            <p class="text-lg font-semibold">৳{{ booking.budget_min }} - ৳{{ booking.budget_max }}</p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">Category</p>
            <p class="text-lg font-semibold">{{ booking.category?.name || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">Created</p>
            <p class="text-lg font-semibold">{{ booking.timestamps.created_at }}</p>
          </div>
        </div>

        <!-- Timeline -->
        <div class="border-t pt-4 grid grid-cols-5 gap-2 text-center text-sm">
          <div :class="[booking.timestamps.created_at ? 'text-green-600' : 'text-gray-400']">
            <p class="font-semibold">Created</p>
            <p class="text-xs">{{ booking.timestamps.created_at }}</p>
          </div>
          <div :class="[booking.timestamps.accepted_at ? 'text-green-600' : 'text-gray-400']">
            <p class="font-semibold">Accepted</p>
            <p class="text-xs">{{ booking.timestamps.accepted_at || '—' }}</p>
          </div>
          <div :class="[booking.timestamps.declined_at ? 'text-red-600' : 'text-gray-400']">
            <p class="font-semibold">Declined</p>
            <p class="text-xs">{{ booking.timestamps.declined_at || '—' }}</p>
          </div>
          <div :class="[booking.timestamps.cancelled_at ? 'text-yellow-600' : 'text-gray-400']">
            <p class="font-semibold">Cancelled</p>
            <p class="text-xs">{{ booking.timestamps.cancelled_at || '—' }}</p>
          </div>
          <div :class="[booking.timestamps.completed_at ? 'text-blue-600' : 'text-gray-400']">
            <p class="font-semibold">Completed</p>
            <p class="text-xs">{{ booking.timestamps.completed_at || '—' }}</p>
          </div>
        </div>
      </div>

      <!-- Client & Photographer Info -->
      <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Client</h3>
          <p class="font-semibold">{{ booking.client.name }}</p>
          <p class="text-gray-600">{{ booking.client.email }}</p>
          <p class="text-gray-600">{{ booking.client.phone }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Photographer</h3>
          <p class="font-semibold">{{ booking.photographer.name }}</p>
          <p class="text-gray-600">{{ booking.photographer.email }}</p>
        </div>
      </div>

      <!-- Booking Details -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Booking Details</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-gray-600 font-medium">Venue Address</p>
            <p>{{ booking.venue_address || 'Not specified' }}</p>
          </div>
          <div>
            <p class="text-gray-600 font-medium">Event Time</p>
            <p>{{ booking.event_time || 'Not specified' }}</p>
          </div>
          <div class="col-span-2">
            <p class="text-gray-600 font-medium">Notes</p>
            <p>{{ booking.notes || 'No notes' }}</p>
          </div>
        </div>
      </div>

      <!-- Messages -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Messages ({{ booking.messages.length }})</h3>
        <div class="space-y-4 max-h-96 overflow-y-auto">
          <div v-for="msg in booking.messages" :key="msg.id" class="flex gap-4 p-3 bg-gray-50 rounded">
            <div class="flex-1">
              <p class="font-semibold text-gray-900">{{ msg.sender.name }}</p>
              <p class="text-xs text-gray-500">{{ msg.created_at }}</p>
              <p class="mt-2 text-gray-800">{{ msg.message }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Status Logs -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Status History</h3>
        <div class="space-y-4">
          <div v-for="log in booking.status_logs" :key="log.created_at" class="flex gap-4 pb-4 border-b last:border-b-0">
            <div class="flex-shrink-0 w-20 text-sm font-mono">{{ log.created_at }}</div>
            <div class="flex-1">
              <p class="font-semibold">
                <span class="text-gray-500">{{ log.old_status || 'START' }}</span>
                → <span class="text-blue-600">{{ log.new_status }}</span>
              </p>
              <p class="text-sm text-gray-600 mt-1">{{ log.note }}</p>
              <p class="text-xs text-gray-400">by {{ log.changed_by }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Admin Actions -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Admin Actions</h3>
        <div class="flex gap-4 flex-wrap">
          <button
            v-if="booking.status !== 'cancelled' && booking.status !== 'completed'"
            @click="cancelBooking"
            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition"
          >
            Cancel Booking
          </button>
          <button
            @click="openDispute"
            class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-6 rounded-lg transition"
          >
            Flag as Dispute
          </button>
        </div>
      </div>
    </div>

    <!-- Dispute Modal -->
    <div v-if="showDisputeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Flag as Dispute</h3>
        <textarea
          v-model="disputeReason"
          placeholder="Reason for flagging..."
          rows="4"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 mb-4"
        ></textarea>
        <div class="flex gap-4">
          <button
            @click="submitDispute"
            class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition"
          >
            Submit
          </button>
          <button
            @click="showDisputeModal = false"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 px-4 rounded-lg transition"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  booking: Object,
})

const showDisputeModal = ref(false)
const disputeReason = ref('')

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-500',
    accepted: 'bg-green-500',
    declined: 'bg-red-500',
    cancelled: 'bg-gray-500',
    completed: 'bg-blue-500',
  }
  return classes[status] || 'bg-gray-500'
}

const cancelBooking = () => {
  const reason = prompt('Reason for admin cancellation:')
  if (reason !== null) {
    router.post(route('admin.booking.cancel', props.booking.id), {
      reason: reason || 'Admin cancelled',
    }, {
      onSuccess: () => window.location.reload(),
    })
  }
}

const openDispute = () => {
  showDisputeModal.value = true
}

const submitDispute = () => {
  if (!disputeReason.value.trim()) {
    alert('Please enter a reason.')
    return
  }
  router.post(route('admin.booking.dispute', props.booking.id), {
    reason: disputeReason.value,
  }, {
    onSuccess: () => {
      showDisputeModal.value = false
      window.location.reload()
    },
  })
}
</script>
