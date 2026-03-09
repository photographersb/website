<template>
  <SbCard kind="competition" :interactive="interactive">
    <template v-if="$slots.image" #image><slot name="image" /></template>
    <template v-if="$slots.badges" #badges><slot name="badges" /></template>
    <slot />
    <template v-if="$slots.meta" #meta><slot name="meta" /></template>
    <template v-if="$slots.actions" #actions><slot name="actions" /></template>
  </SbCard>
</template>

<script setup>
import SbCard from './SbCard.vue'

defineProps({
  interactive: { type: Boolean, default: true },
})
</script>
