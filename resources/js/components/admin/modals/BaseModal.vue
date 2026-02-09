<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
  >
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-bold">
          {{ title }}
        </h2>
        <button
          class="text-gray-500 hover:text-gray-700 text-2xl"
          @click="$emit('close')"
        >
          &times;
        </button>
      </div>
      
      <form
        class="p-6 space-y-4"
        @submit.prevent="handleSubmit"
      >
        <slot />
        
        <div class="flex gap-3 pt-4">
          <button
            type="submit"
            :disabled="isLoading"
            class="flex-1 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 disabled:bg-gray-400 font-medium"
          >
            {{ isLoading ? 'Saving...' : 'Save' }}
          </button>
          <button
            type="button"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium"
            @click="$emit('close')"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['close', 'submit']);

const handleSubmit = () => {
  emit('submit');
};
</script>
