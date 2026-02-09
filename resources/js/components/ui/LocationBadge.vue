<template>
  <router-link
    :to="`/photographers/by-location?city=${locationSlug}`"
    :class="[
      'inline-flex items-center gap-2 px-3 py-2 rounded-full font-semibold transition-all duration-200 cursor-pointer',
      sizeClasses[size],
      variantClasses[variant]
    ]"
    :title="`Browse photographers in ${locationName}`"
  >
    <span>📍</span>
    <span>{{ locationName }}</span>
    <svg
      v-if="showArrow"
      class="w-3 h-3"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M9 5l7 7-7 7"
      />
    </svg>
  </router-link>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  locationName: {
    type: String,
    required: true,
  },
  locationSlug: {
    type: String,
    required: true,
  },
  size: {
    type: String,
    default: 'md',
    validator: (val) => ['sm', 'md', 'lg'].includes(val),
  },
  variant: {
    type: String,
    default: 'soft',
    validator: (val) => ['solid', 'soft', 'outline'].includes(val),
  },
  showArrow: {
    type: Boolean,
    default: false,
  },
})

const sizeClasses = {
  sm: 'text-xs px-2 py-1',
  md: 'text-sm px-3 py-2',
  lg: 'text-base px-4 py-2',
}

const variantClasses = {
  solid: 'bg-primary-700 text-white hover:bg-primary-800 shadow-md hover:shadow-lg',
  soft: 'bg-primary-100 text-primary-700 hover:bg-primary-200 border-2 border-primary-200 hover:border-primary-300',
  outline: 'bg-white text-primary-700 border-2 border-primary-600 hover:bg-primary-50',
}
</script>
