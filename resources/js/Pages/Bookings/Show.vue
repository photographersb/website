<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Booking Header -->
      <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Booking #{{ booking.code }}
            </h1>
            <p class="text-gray-600 mt-2">
              {{ booking.event_date }}
            </p>
          </div>
          <span
            :class="getStatusClass(booking.status)"
            class="px-4 py-2 rounded-full text-white font-medium"
          >
            {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
          </span>
        </div>

        <!-- Booking Details Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div>
            <p class="text-gray-600 text-sm">
              Duration
            </p>
            <p class="text-lg font-semibold">
              {{ booking.duration_hours }} hours
            </p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">
              Budget
            </p>
            <p class="text-lg font-semibold">
              ৳{{ booking.budget_min }} - ৳{{ booking.budget_max }}
            </p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">
              Venue
            </p>
            <p class="text-lg font-semibold">
              {{ booking.venue_address || 'N/A' }}
            </p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">
              Time
            </p>
            <p class="text-lg font-semibold">
              {{ booking.event_time || 'Not set' }}
            </p>
          </div>
        </div>

        <!-- Parties -->
        <div class="grid grid-cols-2 gap-4 border-t pt-4">
          <div>
            <p class="text-gray-600 text-sm font-medium mb-2">
              Client
            </p>
            <p class="font-semibold">
              {{ booking.client.name }}
            </p>
          </div>
          <div>
            <p class="text-gray-600 text-sm font-medium mb-2">
              Photographer
            </p>
            <p class="font-semibold">
              {{ booking.photographer.name }}
            </p>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200 flex">
          <button
            v-for="tab in tabs"
            :key="tab"
            :class="[
              'px-4 py-3 font-medium text-sm transition',
              activeTab === tab
                ? 'text-blue-600 border-b-2 border-blue-600'
                : 'text-gray-600 hover:text-gray-900'
            ]"
            @click="activeTab = tab"
          >
            {{ tab === 'messages' ? 'Messages' : 'Timeline' }}
          </button>
        </div>

        <!-- Messages Tab -->
        <div
          v-if="activeTab === 'messages'"
          class="p-6"
        >
          <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
            <div
              v-for="msg in booking.messages"
              :key="msg.id"
              class="flex gap-4"
            >
              <div class="flex-1 bg-gray-50 p-4 rounded-lg">
                <p class="font-semibold text-gray-900">
                  {{ msg.sender.name }}
                </p>
                <p class="text-gray-600 text-sm">
                  {{ msg.created_at }}
                </p>
                <p class="mt-2 text-gray-800">
                  {{ msg.message }}
                </p>
              </div>
            </div>
          </div>

          <!-- Message Form -->
          <form
            v-if="canMessage"
            class="space-y-4"
            @submit.prevent="sendMessage"
          >
            <textarea
              v-model="newMessage"
              placeholder="Type your message..."
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            />
            <button
              type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
            >
              Send Message
            </button>
          </form>
        </div>

        <!-- Timeline Tab -->
        <div
          v-if="activeTab === 'timeline'"
          class="p-6"
        >
          <div class="space-y-4">
            <div
              v-for="log in booking.status_logs"
              :key="log.created_at"
              class="flex gap-4"
            >
              <div class="flex flex-col items-center">
                <div class="w-4 h-4 bg-blue-600 rounded-full" />
                <div class="w-0.5 h-12 bg-gray-300 mt-2" />
              </div>
              <div class="flex-1 pb-4">
                <p class="font-semibold text-gray-900">
                  {{ log.new_status }}
                </p>
                <p class="text-gray-600 text-sm">
                  {{ log.created_at }}
                </p>
                <p class="text-gray-800">
                  {{ log.note }}
                </p>
                <p class="text-gray-600 text-sm mt-1">
                  by {{ log.changed_by }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-6 flex gap-4 justify-end">
        <button
          v-if="booking.can_accept"
          class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition"
          @click="acceptBooking"
        >
          Accept Booking
        </button>
        <button
          v-if="booking.can_decline"
          class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition"
          @click="declineBooking"
        >
          Decline
        </button>
        <button
          v-if="booking.can_cancel"
          class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-6 rounded-lg transition"
          @click="cancelBooking"
        >
          Cancel
        </button>
        <button
          v-if="booking.can_complete"
          class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition"
          @click="completeBooking"
        >
          Mark Complete
        </button>
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

const activeTab = ref('messages')
const tabs = ['messages', 'timeline']
const newMessage = ref('')
const canMessage = ref(true)

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

const sendMessage = () => {
  if (!newMessage.value.trim()) return
  router.post(route('booking.message.store', props.booking.id), {
    message: newMessage.value,
  }, {
    onSuccess: () => {
      newMessage.value = ''
      location.reload()
    },
  })
}

const acceptBooking = () => {
  router.post(route('booking.accept', props.booking.id), {}, {
    onSuccess: () => location.reload(),
  })
}

const declineBooking = () => {
  const reason = prompt('Reason for declining (optional):')
  if (reason !== null) {
    router.post(route('booking.decline', props.booking.id), {
      reason: reason || '',
    }, {
      onSuccess: () => location.reload(),
    })
  }
}

const cancelBooking = () => {
  const reason = prompt('Reason for cancelling (optional):')
  if (reason !== null) {
    router.post(route('booking.cancel', props.booking.id), {
      reason: reason || '',
    }, {
      onSuccess: () => location.reload(),
    })
  }
}

const completeBooking = () => {
  if (confirm('Mark this booking as completed?')) {
    router.post(route('booking.complete', props.booking.id), {}, {
      onSuccess: () => location.reload(),
    })
  }
}
</script>
