<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-8 text-center">
      <!-- Icon -->
      <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-red-100 rounded-full">
        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16H5m13 0h3m2-7a9 9 0 11-18 0 9 9 0 0118 0zM9 9h.01M15 9h.01" />
        </svg>
      </div>

      <!-- Title -->
      <h3 class="text-2xl font-bold text-gray-900 mb-2">Service Unavailable</h3>

      <!-- Message -->
      <p class="text-gray-600 mb-4">
        {{ message }}
      </p>

      <!-- Sub-message -->
      <p class="text-sm text-gray-500 mb-6">
        {{ subMessage }}
      </p>

      <!-- Actions -->
      <div class="space-y-3">
        <button
          @click="retry"
          class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2"
          :disabled="isRetrying"
        >
          <svg v-if="!isRetrying" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span v-if="isRetrying">Retrying...</span>
          <span v-else>Try Again</span>
        </button>

        <button
          @click="goHome"
          class="w-full px-4 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors"
        >
          Go to Home
        </button>
      </div>

      <!-- Status Indicator -->
      <div class="mt-6 pt-6 border-t border-gray-200">
        <p class="text-xs text-gray-500">
          Status: <span class="font-semibold text-red-600">{{ statusText }}</span>
        </p>
        <p class="text-xs text-gray-400 mt-1">
          {{ lastCheckText }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  message: {
    type: String,
    default: 'Our servers are currently offline. We\'re working to fix it as soon as possible.',
  },
  subMessage: {
    type: String,
    default: 'Please try again in a few moments or report this to our admin for a faster fix.',
  },
  statusText: {
    type: String,
    default: 'Offline - API unavailable',
  },
  onRetry: {
    type: Function,
    default: null,
  },
})

const emit = defineEmits(['retry', 'dismissed'])
const router = useRouter()
const isRetrying = ref(false)

const lastCheckText = computed(() => {
  const now = new Date()
  return `Last checked: ${now.toLocaleTimeString()}`
})

async function retry() {
  isRetrying.value = true
  try {
    if (props.onRetry) {
      await props.onRetry()
    }
    emit('retry')
  } finally {
    isRetrying.value = false
  }
}

function goHome() {
  emit('dismissed')
  router.push('/')
}
</script>
