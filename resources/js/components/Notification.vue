<template>
  <transition name="notification">
    <div
      v-if="show"
      :class="notificationClasses"
      class="fixed top-4 right-4 z-50 max-w-md shadow-2xl rounded-xl overflow-hidden animate-slide-in"
    >
      <div class="p-4 flex items-start gap-3">
        <!-- Icon -->
        <div
          :class="iconBgClass"
          class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
        >
          <svg
            v-if="type === 'success'"
            class="w-6 h-6 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            />
          </svg>
          <svg
            v-else-if="type === 'error'"
            class="w-6 h-6 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
          <svg
            v-else-if="type === 'warning'"
            class="w-6 h-6 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
            />
          </svg>
          <svg
            v-else
            class="w-6 h-6 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>

        <!-- Content -->
        <div class="flex-1">
          <h4
            v-if="title"
            class="font-bold text-gray-900 mb-1"
          >
            {{ title }}
          </h4>
          <p class="text-sm text-gray-700">
            {{ message }}
          </p>
        </div>

        <!-- Close button -->
        <button
          class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"
          @click="close"
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
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- Progress bar -->
      <div
        v-if="duration"
        class="h-1 bg-gray-200"
      >
        <div
          :class="progressBarClass"
          class="h-full transition-all"
          :style="{ width: progressWidth + '%' }"
        />
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'info', // success, error, warning, info
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: String,
  message: {
    type: String,
    required: true
  },
  duration: {
    type: Number,
    default: 5000 // milliseconds
  }
})

const emit = defineEmits(['close'])

const show = ref(false)
const progressWidth = ref(100)
let progressInterval = null

const notificationClasses = computed(() => {
  const baseClasses = 'bg-white border-l-4'
  const borderColors = {
    success: 'border-green-500',
    error: 'border-red-500',
    warning: 'border-yellow-500',
    info: 'border-blue-500'
  }
  return `${baseClasses} ${borderColors[props.type]}`
})

const iconBgClass = computed(() => {
  const bgColors = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    warning: 'bg-yellow-500',
    info: 'bg-blue-500'
  }
  return bgColors[props.type]
})

const progressBarClass = computed(() => {
  const barColors = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    warning: 'bg-yellow-500',
    info: 'bg-blue-500'
  }
  return barColors[props.type]
})

const close = () => {
  show.value = false
  if (progressInterval) {
    clearInterval(progressInterval)
  }
  setTimeout(() => {
    emit('close')
  }, 300)
}

onMounted(() => {
  show.value = true
  
  if (props.duration) {
    const intervalTime = 50
    const step = (intervalTime / props.duration) * 100
    
    progressInterval = setInterval(() => {
      progressWidth.value -= step
      if (progressWidth.value <= 0) {
        close()
      }
    }, intervalTime)
  }
})
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.notification-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}
</style>
