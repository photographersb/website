<template>
  <Transition name="error">
    <div v-if="error" class="form-error">
      <svg class="error-icon" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
      </svg>
      <span class="error-text">{{ error }}</span>
    </div>
  </Transition>
</template>

<script setup>
const props = defineProps({
  error: {
    type: [String, Array],
    default: ''
  }
});

// If error is an array, join it
const errorMessage = computed(() => {
  if (Array.isArray(props.error)) {
    return props.error.join(', ');
  }
  return props.error;
});
</script>

<script>
import { computed } from 'vue';

export default {
  name: 'FormError'
};
</script>

<style scoped>
.form-error {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 10px 12px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 6px;
  margin-top: 6px;
  font-size: 13px;
  color: #dc2626;
  line-height: 1.5;
}

.error-icon {
  flex-shrink: 0;
  width: 16px;
  height: 16px;
  margin-top: 1px;
}

.error-text {
  flex: 1;
  min-width: 0;
  word-break: break-word;
}

/* Transition */
.error-enter-active,
.error-leave-active {
  transition: all 0.2s ease;
}

.error-enter-from {
  opacity: 0;
  transform: translateY(-4px);
}

.error-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
