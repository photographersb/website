<template>
  <nav class="sb-ui-breadcrumb" aria-label="Breadcrumb">
    <template v-for="(item, index) in items" :key="`${item.label}-${index}`">
      <component
        :is="item.to || item.href ? 'a' : 'span'"
        :href="item.href || item.to || undefined"
        :class="index === items.length - 1 ? 'sb-ui-breadcrumb__current' : ''"
      >
        {{ item.label }}
      </component>
      <span v-if="index < items.length - 1">/</span>
    </template>
  </nav>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})
</script>
