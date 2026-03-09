<template>
  <component
    :is="as"
    :type="as === 'button' ? nativeType : undefined"
    :class="buttonClasses"
    v-bind="$attrs"
  >
    <slot name="icon-left" />
    <slot />
    <slot name="icon-right" />
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  as: { type: String, default: 'button' },
  nativeType: { type: String, default: 'button' },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'ghost', 'danger'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'icon'].includes(value),
  },
})

const buttonClasses = computed(() => {
  const classes = ['sb-ui-btn', `sb-ui-btn--${props.variant}`]
  if (props.size === 'sm') classes.push('sb-ui-btn--sm')
  if (props.size === 'lg') classes.push('sb-ui-btn--lg')
  if (props.size === 'icon') classes.push('sb-ui-btn--icon')
  return classes
})
</script>
