<template>
  <div class="sb-space-stack-xs">
    <label v-if="label" :for="inputId" class="sb-type-small font-medium text-gray-700">{{ label }}</label>
    <textarea
      :id="inputId"
      :value="modelValue"
      :rows="rows"
      :placeholder="placeholder"
      :class="['sb-ui-textarea', error ? 'sb-ui-textarea--error' : '']"
      v-bind="$attrs"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <p v-if="helper && !error" class="sb-ui-form-help">{{ helper }}</p>
    <p v-if="error" class="sb-ui-form-error">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  rows: { type: [String, Number], default: 4 },
  placeholder: { type: String, default: '' },
  helper: { type: String, default: '' },
  error: { type: String, default: '' },
  inputId: { type: String, default: undefined },
})

defineEmits(['update:modelValue'])
</script>
