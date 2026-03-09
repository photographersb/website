<template>
  <div class="sb-space-stack-xs">
    <label v-if="label" class="sb-type-small font-medium text-gray-700">{{ label }}</label>
    <input
      type="file"
      class="upload-input"
      v-bind="$attrs"
      @change="$emit('change', $event)"
    >
    <div v-if="$slots.preview" class="sb-ui-upload-preview">
      <slot name="preview" />
    </div>
    <p v-if="helper" class="sb-ui-form-help">{{ helper }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, default: '' },
  helper: { type: String, default: '' },
})

defineEmits(['change'])
</script>
