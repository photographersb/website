<template>
  <div :class="['sb-ui-alert', `sb-ui-alert--${type}`]" role="alert">
    <slot />
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'warning', 'error'].includes(value),
  },
})
</script>
