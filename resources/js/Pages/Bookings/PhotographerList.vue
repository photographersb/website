<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">
        My Booking Inbox
      </h1>

      <!-- Bookings Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Booking Code
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Client
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Event Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Budget
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                New Messages
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Action
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="booking in bookings.data"
              :key="booking.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                {{ booking.code }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ booking.client_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ booking.event_date }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ booking.budget }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="getStatusClass(booking.status)"
                  class="px-3 py-1 text-xs font-medium text-white rounded-full"
                >
                  {{ booking.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span
                  v-if="booking.unread_count > 0"
                  class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full"
                >
                  {{ booking.unread_count }}
                </span>
                <span
                  v-else
                  class="text-gray-400"
                >—</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <Link
                  :href="route('booking.show', booking.id)"
                  class="text-blue-600 hover:text-blue-900 font-medium"
                >
                  Review
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="bookings.links"
        class="mt-6 flex justify-center gap-2"
      >
        <Link
          v-for="link in bookings.links"
          :key="link.url"
          :href="link.url"
          :class="[
            'px-3 py-2 rounded-lg transition',
            link.active
              ? 'bg-blue-600 text-white'
              : link.url
                ? 'bg-white text-gray-900 hover:bg-gray-50 border border-gray-300'
                : 'bg-gray-100 text-gray-400 cursor-not-allowed',
          ]"
        >
          <span v-html="link.label"></span>
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  bookings: Object,
})

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
</script>
