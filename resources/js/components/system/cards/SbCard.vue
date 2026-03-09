<template>
  <article :class="cardClasses">
    <slot name="image" />
    <div v-if="$slots.badges" class="sb-ui-card__badges p-4 pb-0">
      <slot name="badges" />
    </div>
    <div class="sb-ui-card__content">
      <slot />
    </div>
    <div v-if="$slots.meta" class="sb-ui-card__content pt-0">
      <div class="sb-ui-card__meta">
        <slot name="meta" />
      </div>
    </div>
    <div v-if="$slots.actions" class="sb-ui-card__content pt-0">
      <div class="sb-ui-card__actions">
        <slot name="actions" />
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  kind: {
    type: String,
    default: 'dashboard',
    validator: (value) => ['dashboard', 'photographer', 'event', 'competition'].includes(value),
  },
  interactive: { type: Boolean, default: false },
})

const cardClasses = computed(() => {
  const classes = ['sb-ui-card', `sb-ui-card--${props.kind}`]
  if (props.interactive) classes.push('sb-ui-card--interactive')
  return classes
})
</script>
