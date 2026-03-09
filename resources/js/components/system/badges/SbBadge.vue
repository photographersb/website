<template>
  <span :class="['sb-ui-badge', `sb-ui-badge--${variant}`]">
    <slot />
  </span>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'neutral',
    validator: (value) => ['neutral', 'brand', 'success', 'warning', 'danger', 'info', 'verified', 'featured', 'category', 'status', 'free', 'paid', 'available'].includes(value),
  },
})
</script>
