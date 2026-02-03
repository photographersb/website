<template>
  <div class="text-center py-12 md:py-20">
    <!-- Icon -->
    <div class="inline-flex items-center justify-center w-20 h-20 md:w-24 md:h-24 rounded-full mb-6" :class="iconBgClass">
      <!-- Default Camera Icon -->
      <svg v-if="icon === 'camera'" class="w-10 h-10 md:w-12 md:h-12" :class="iconColorClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>

      <!-- Search Icon -->
      <svg v-else-if="icon === 'search'" class="w-10 h-10 md:w-12 md:h-12" :class="iconColorClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>

      <!-- Trophy Icon -->
      <svg v-else-if="icon === 'trophy'" class="w-10 h-10 md:w-12 md:h-12" :class="iconColorClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
      </svg>

      <!-- Calendar Icon -->
      <svg v-else-if="icon === 'calendar'" class="w-10 h-10 md:w-12 md:h-12" :class="iconColorClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>

      <!-- Inbox Icon -->
      <svg v-else-if="icon === 'inbox'" class="w-10 h-10 md:w-12 md:h-12" :class="iconColorClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
      </svg>

      <!-- Folder Icon -->
      <svg v-else-if="icon === 'folder'" class="w-10 h-10 md:w-12 md:h-12" :class="iconColorClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
      </svg>

      <!-- Custom Icon Slot -->
      <slot v-else name="icon">
        <svg class="w-10 h-10 md:w-12 md:h-12" :class="iconColorClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
      </slot>
    </div>

    <!-- Title -->
    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">
      {{ title || 'Nothing found' }}
    </h3>

    <!-- Description -->
    <p class="text-sm md:text-base text-gray-600 mb-6 max-w-md mx-auto px-4">
      {{ description || 'Try adjusting your filters or search terms' }}
    </p>

    <!-- Action Button -->
    <button
      v-if="actionText"
      @click="$emit('action')"
      class="px-6 py-3 bg-burgundy text-white rounded-xl hover:bg-burgundy-dark transition-all shadow-md hover:shadow-lg font-semibold"
    >
      {{ actionText }}
    </button>

    <!-- Custom Action Slot -->
    <slot name="action"></slot>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  icon: {
    type: String,
    default: 'search', // camera, search, trophy, calendar, inbox, folder
  },
  title: {
    type: String,
    default: '',
  },
  description: {
    type: String,
    default: '',
  },
  actionText: {
    type: String,
    default: '',
  },
  variant: {
    type: String,
    default: 'gray', // gray, burgundy, blue, green
  },
});

defineEmits(['action']);

const iconBgClass = computed(() => {
  switch (props.variant) {
    case 'burgundy':
      return 'bg-burgundy/10';
    case 'blue':
      return 'bg-blue-100';
    case 'green':
      return 'bg-green-100';
    default:
      return 'bg-gray-100';
  }
});

const iconColorClass = computed(() => {
  switch (props.variant) {
    case 'burgundy':
      return 'text-burgundy';
    case 'blue':
      return 'text-blue-500';
    case 'green':
      return 'text-green-500';
    default:
      return 'text-gray-400';
  }
});
</script>

<style scoped>
/* Additional styles if needed */
</style>
