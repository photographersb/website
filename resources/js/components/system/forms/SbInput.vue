<template>
  <div class="sb-space-stack-xs">
    <label v-if="label" :for="inputId" class="sb-type-small font-medium text-gray-700">{{ label }}</label>
    <input
      :id="inputId"
      :value="modelValue"
      :type="type"
      :placeholder="placeholder"
      :class="['sb-ui-input', error ? 'sb-ui-input--error' : '']"
      v-bind="$attrs"
      @input="$emit('update:modelValue', $event.target.value)"
    >
    <p v-if="helper && !error" class="sb-ui-form-help">{{ helper }}</p>
    <p v-if="error" class="sb-ui-form-error">{{ error }}</p>
    <p v-if="counter !== null" class="sb-ui-form-counter">{{ counter }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  helper: { type: String, default: '' },
  error: { type: String, default: '' },
  counter: { type: [String, Number], default: null },
  inputId: { type: String, default: undefined },
})

defineEmits(['update:modelValue'])
</script>
