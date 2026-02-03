<template>
  <div class="min-h-screen bg-gray-50 py-6 px-4">
    <!-- Document Preview Modal -->
    <div v-if="previewModal.isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[80vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b px-6 py-4">
          <h2 class="text-lg font-semibold text-gray-900">{{ previewModal.filename }}</h2>
          <button
            @click="closePreviewModal"
            class="text-gray-500 hover:text-gray-700"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto p-6 flex items-center justify-center">
          <!-- Image Preview -->
          <img
            v-if="isImageFile(previewModal.filename)"
            :src="previewModal.path"
            :alt="previewModal.filename"
            class="max-w-full max-h-full object-contain"
          />

          <!-- PDF Icon / Non-Previewable Files -->
          <div v-else class="flex flex-col items-center justify-center text-center">
            <svg class="w-16 h-16 text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
              <path d="M4 3a2 2 0 012-2h6a1 1 0 01.707.293l4 4a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V3z" fill="currentColor"/>
            </svg>
            <p class="text-gray-700 font-medium">{{ previewModal.filename }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ formatFileSize(previewModal.size) }}</p>
            <p class="text-sm text-gray-500 mt-4">This file type cannot be previewed in the browser</p>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t px-6 py-4 flex justify-end gap-3">
          <a
            :href="previewModal.path"
            download
            class="px-4 py-2 bg-burgundy-600 text-white rounded-lg hover:bg-burgundy-700 transition-colors inline-flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download
          </a>
          <button
            @click="closePreviewModal"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <div class="max-w-5xl mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Booking Messages</h1>
          <p class="text-gray-600" v-if="booking">Booking #{{ booking.id }} • {{ booking.status }}</p>
        </div>
        <div class="flex gap-2">
          <router-link
            to="/bookings"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
          >
            Back to Bookings
          </router-link>
          <button
            @click="markAllRead"
            :disabled="markingAll"
            class="px-4 py-2 bg-burgundy-600 text-white rounded-lg hover:bg-burgundy-700 transition-colors disabled:opacity-60"
          >
            Mark All Read
          </button>
        </div>
      </div>

      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2" style="border-bottom-color: var(--admin-brand-primary);"></div>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Booking Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Booking Summary</h3>
            <div v-if="booking" class="space-y-2 text-sm text-gray-700">
              <div class="flex items-center gap-2">
                <span class="text-gray-500">Photographer:</span>
                <span class="font-medium">{{ booking.photographer?.business_name || booking.photographer?.user?.name || 'Photographer' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-500">Date:</span>
                <span>{{ formatDate(booking.event_date) }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-500">Location:</span>
                <span>{{ booking.event_location || 'N/A' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-500">Status:</span>
                <span class="font-medium">{{ booking.status }}</span>
              </div>
            </div>
            <div v-else class="text-sm text-gray-500">Booking details unavailable.</div>
          </div>
        </div>

        <!-- Messages Panel -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-sm p-5 flex flex-col h-[70vh]">
            <!-- Message Filter -->
            <div class="mb-4 flex flex-col sm:flex-row gap-2">
              <input
                v-model="filterText"
                type="text"
                placeholder="Search messages..."
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-400"
              />
              <select
                v-model="filterSender"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-400"
              >
                <option value="">All senders</option>
                <option value="me">My messages</option>
                <option value="other">Other messages</option>
              </select>
              <button
                @click="clearFilters"
                class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Clear
              </button>
            </div>

            <div class="flex-1 overflow-y-auto space-y-4 pr-2">
              <div v-if="filteredMessages.length === 0" class="text-center text-gray-500 py-12">
                {{ messages.length === 0 ? 'No messages yet. Start the conversation.' : 'No messages match your filters.' }}
              </div>

              <div
                v-for="message in filteredMessages"
                :key="message.id"
                class="flex"
                :class="isOwnMessage(message) ? 'justify-end' : 'justify-start'"
              >
                <div
                  class="max-w-[80%] rounded-lg px-4 py-3 shadow-sm"
                  :class="isOwnMessage(message) ? 'bg-burgundy-600 text-white' : 'bg-gray-100 text-gray-800'"
                >
                  <div class="text-xs opacity-80 mb-1">
                    {{ message.sender?.name || 'User' }} • {{ formatDateTime(message.created_at) }}
                  </div>
                  <div class="whitespace-pre-wrap text-sm">{{ message.message }}</div>

                  <div v-if="message.attachments && message.attachments.length" class="mt-3 space-y-1">
                    <div
                      v-for="(file, index) in message.attachments"
                      :key="index"
                      class="text-xs"
                    >
                      <button
                        @click="openPreviewModal(file)"
                        class="underline hover:no-underline font-medium text-inherit"
                        :class="isOwnMessage(message) ? 'text-blue-200 hover:text-white' : 'text-blue-600 hover:text-blue-800'"
                      >
                        {{ file.filename }} ({{ formatFileSize(file.size) }})
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Message Composer -->
            <form @submit.prevent="sendMessage" class="mt-4 border-t pt-4">
              <div class="flex flex-col gap-3">
                <textarea
                  v-model="newMessage"
                  rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-burgundy-400"
                  placeholder="Write a message..."
                  required
                ></textarea>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                  <input
                    type="file"
                    multiple
                    @change="handleFileChange"
                    class="text-sm text-gray-600"
                    accept="image/*,application/pdf"
                  />
                  <button
                    type="submit"
                    :disabled="sending"
                    class="px-5 py-2 bg-burgundy-600 text-white rounded-lg hover:bg-burgundy-700 transition-colors disabled:opacity-60"
                  >
                    {{ sending ? 'Sending...' : 'Send Message' }}
                  </button>
                </div>
                <p class="text-xs text-gray-500">Max 5 files, 10MB each. Images or PDF.</p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api'

const route = useRoute()
const bookingId = computed(() => route.params.bookingId)

const booking = ref(null)
const messages = ref([])
const loading = ref(true)
const sending = ref(false)
const markingAll = ref(false)
const newMessage = ref('')
const attachments = ref([])
const previewModal = ref({
  isOpen: false,
  path: '',
  filename: '',
  size: 0
})
const filterText = ref('')
const filterSender = ref('')

const userId = computed(() => {
  const stored = localStorage.getItem('user')
  return stored ? JSON.parse(stored).id : null
})

const filteredMessages = computed(() => {
  return messages.value.filter((message) => {
    // Filter by sender
    if (filterSender.value === 'me' && !isOwnMessage(message)) {
      return false
    }
    if (filterSender.value === 'other' && isOwnMessage(message)) {
      return false
    }

    // Filter by text content (searches message text and filenames)
    if (filterText.value) {
      const searchTerm = filterText.value.toLowerCase()
      const messageMatch = message.message.toLowerCase().includes(searchTerm)
      const fileMatch = message.attachments?.some(file =>
        file.filename.toLowerCase().includes(searchTerm)
      )
      return messageMatch || fileMatch
    }

    return true
  })
})

const fetchBooking = async () => {
  const response = await api.get(`/bookings/${bookingId.value}`)
  booking.value = response.data.data || response.data
}

const fetchMessages = async () => {
  const response = await api.get(`/bookings/${bookingId.value}/messages`)
  const payload = response.data.data || []
  messages.value = Array.isArray(payload) ? payload : payload.data || []
}

const markAllRead = async () => {
  markingAll.value = true
  try {
    await api.post(`/bookings/${bookingId.value}/messages/mark-all-read`)
    await fetchMessages()
  } finally {
    markingAll.value = false
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim()) return
  sending.value = true
  try {
    const form = new FormData()
    form.append('message', newMessage.value)
    attachments.value.forEach((file) => form.append('attachments[]', file))

    await api.post(`/bookings/${bookingId.value}/messages`, form, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    newMessage.value = ''
    attachments.value = []
    await fetchMessages()
  } finally {
    sending.value = false
  }
}

const handleFileChange = (event) => {
  attachments.value = Array.from(event.target.files || []).slice(0, 5)
}

const isOwnMessage = (message) => message.sender_id === userId.value

const isImageFile = (filename) => {
  const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg']
  return imageExtensions.some(ext => filename.toLowerCase().endsWith(ext))
}

const openPreviewModal = (file) => {
  previewModal.value = {
    isOpen: true,
    path: `/storage/${file.path}`,
    filename: file.filename,
    size: file.size
  }
}

const closePreviewModal = () => {
  previewModal.value.isOpen = false
}

const clearFilters = () => {
  filterText.value = ''
  filterSender.value = ''
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString()
}

const formatDateTime = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleString()
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0B'
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return `${(bytes / Math.pow(1024, i)).toFixed(1)}${sizes[i]}`
}

onMounted(async () => {
  try {
    await Promise.all([fetchBooking(), fetchMessages()])
    await markAllRead()
  } finally {
    loading.value = false
  }
})
</script>
