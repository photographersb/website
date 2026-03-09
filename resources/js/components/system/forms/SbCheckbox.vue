<template>
  <label class="inline-flex items-center gap-2">
    <input
      type="checkbox"
      :checked="modelValue"
      class="h-4 w-4 rounded border-gray-300 text-burgundy focus:ring-burgundy"
      v-bind="$attrs"
      @change="$emit('update:modelValue', $event.target.checked)"
    >
    <span class="sb-type-small text-gray-700"><slot /></span>
  </label>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
})

defineEmits(['update:modelValue'])
</script>
