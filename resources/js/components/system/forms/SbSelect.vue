<template>
  <div class="sb-space-stack-xs">
    <label v-if="label" :for="inputId" class="sb-type-small font-medium text-gray-700">{{ label }}</label>
    <select
      :id="inputId"
      :value="modelValue"
      :class="['sb-ui-select', error ? 'sb-ui-select--error' : '']"
      v-bind="$attrs"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <slot />
    </select>
    <p v-if="helper && !error" class="sb-ui-form-help">{{ helper }}</p>
    <p v-if="error" class="sb-ui-form-error">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  helper: { type: String, default: '' },
  error: { type: String, default: '' },
  inputId: { type: String, default: undefined },
})

defineEmits(['update:modelValue'])
</script>
