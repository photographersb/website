<template>
  <div :class="['sb-ui-avatar', `sb-ui-avatar--${size}`]">
    <img
      v-if="src"
      :src="src"
      :alt="alt"
      class="h-full w-full object-cover"
    >
    <span v-else>{{ initials }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  src: { type: String, default: '' },
  alt: { type: String, default: 'Avatar' },
  name: { type: String, default: '' },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
  },
})

const initials = computed(() => {
  if (!props.name) return 'SB'
  const parts = props.name.trim().split(/\s+/)
  return parts.slice(0, 2).map((part) => part[0]?.toUpperCase() || '').join('')
})
</script>
