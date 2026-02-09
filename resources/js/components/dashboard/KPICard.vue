<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
    <!-- Header with Icon and Title -->
    <div :class="['px-6 py-4 bg-gradient-to-r', colorClass]">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <p class="text-sm font-medium text-white opacity-90">
            {{ title }}
          </p>
          <div class="mt-2 flex items-baseline gap-2">
            <p class="text-2xl md:text-3xl font-bold text-white">
              {{ value }}
            </p>
            <span
              v-if="change !== undefined && change !== 0"
              :class="[
                'text-sm font-semibold',
                change >= 0 ? 'text-green-200' : 'text-red-200'
              ]"
            >
              {{ change > 0 ? '↑' : '↓' }} {{ Math.abs(change) }}%
            </span>
          </div>
        </div>
        <div class="text-4xl">
          {{ icon }}
        </div>
      </div>
    </div>

    <!-- Footer with Secondary Value -->
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
      <p class="text-xs text-gray-600">
        {{ secondaryValue }}
      </p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  change: {
    type: Number,
    default: 0
  },
  icon: {
    type: String,
    default: '📊'
  },
  colorClass: {
    type: String,
    default: 'from-blue-500 to-blue-600'
  },
  secondaryValue: {
    type: String,
    default: ''
  }
});
</script>

<style scoped>
.bg-gradient-to-r {
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
}
</style>
