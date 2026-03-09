<template>
  <transition name="fade">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-end sm:items-center justify-center"
    >
      <div
        class="absolute inset-0 bg-black bg-opacity-50"
        @click="close"
      />
      <transition name="slide-up">
        <div v-if="modelValue" class="relative w-full sm:max-w-lg bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">
              {{ title }}
            </h3>
            <button
              class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center"
              aria-label="Close filters"
              @click="close"
            >
              <svg
                class="w-5 h-5 text-gray-700"
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

          <div class="max-h-[70vh] overflow-y-auto px-5 py-4">
            <slot />
          </div>

          <div class="flex gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50">
            <button
              class="flex-1 px-4 py-3 rounded-lg bg-white border-2 border-gray-200 text-gray-700 font-semibold hover:border-gray-300"
              @click="onReset"
            >
              Reset
            </button>
            <button
              class="flex-1 px-4 py-3 rounded-lg bg-primary-700 text-white font-semibold hover:bg-primary-800"
              @click="onApply"
            >
              Apply
            </button>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: 'Filters' },
  onApply: { type: Function, default: () => {} },
  onReset: { type: Function, default: () => {} }
})

const emit = defineEmits(['update:modelValue'])

const close = () => {
  emit('update:modelValue', false)
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.25s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}
</style>
