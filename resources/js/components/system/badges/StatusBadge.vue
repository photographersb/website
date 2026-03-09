<template>
  <SbBadge :variant="mappedVariant"><slot>{{ label }}</slot></SbBadge>
</template>

<script setup>
import { computed } from 'vue'
import SbBadge from './SbBadge.vue'

const props = defineProps({
  status: { type: String, default: 'status' },
  label: { type: String, default: '' },
})

const mappedVariant = computed(() => {
  const normalized = (props.status || '').toLowerCase()
  if (['verified', 'approved', 'active', 'available'].includes(normalized)) return 'success'
  if (['featured'].includes(normalized)) return 'featured'
  if (['pending', 'draft'].includes(normalized)) return 'warning'
  if (['paid'].includes(normalized)) return 'paid'
  if (['free'].includes(normalized)) return 'free'
  if (['rejected', 'failed', 'cancelled'].includes(normalized)) return 'danger'
  return 'status'
})
</script>
