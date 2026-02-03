<template>
  <div class="stats-card bg-white rounded-lg shadow-md p-6">
    <!-- Card Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <div 
          class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"
          :class="iconBackgroundClass"
        >
          {{ icon }}
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
          <p class="text-sm text-gray-500">{{ subtitle }}</p>
        </div>
      </div>
      <div v-if="trend" class="flex items-center space-x-1">
        <svg 
          v-if="trend === 'up'" 
          class="w-5 h-5 text-green-500" 
          fill="currentColor" 
          viewBox="0 0 20 20"
        >
          <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
        <svg 
          v-else-if="trend === 'down'" 
          class="w-5 h-5 text-red-500" 
          fill="currentColor" 
          viewBox="0 0 20 20"
        >
          <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        <span 
          class="text-sm font-medium"
          :class="trend === 'up' ? 'text-green-600' : 'text-red-600'"
        >
          {{ trendValue }}
        </span>
      </div>
    </div>

    <!-- Main Value -->
    <div class="mb-4">
      <p class="text-4xl font-bold" :class="valueColorClass">{{ displayValue }}</p>
      <p v-if="secondaryValue" class="text-sm text-gray-500 mt-1">{{ secondaryValue }}</p>
    </div>

    <!-- Progress Bar (optional) -->
    <div v-if="showProgress && maxValue" class="mb-4">
      <div class="flex justify-between items-center mb-2">
        <span class="text-xs text-gray-600">{{ progressLabel }}</span>
        <span class="text-xs font-medium text-gray-700">{{ progressPercentage }}%</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div 
          class="h-2 rounded-full transition-all duration-500"
          :class="progressBarColor"
          :style="{ width: progressPercentage + '%' }"
        ></div>
      </div>
    </div>

    <!-- Footer Info -->
    <div v-if="footerText" class="pt-4 border-t border-gray-100">
      <p class="text-xs text-gray-500">{{ footerText }}</p>
    </div>

    <!-- Action Button (optional) -->
    <button 
      v-if="actionText"
      @click="$emit('action')"
      class="mt-4 w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors"
    >
      {{ actionText }}
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  secondaryValue: {
    type: String,
    default: ''
  },
  maxValue: {
    type: Number,
    default: null
  },
  trend: {
    type: String,
    default: null,
    validator: (value) => ['up', 'down', null].includes(value)
  },
  trendValue: {
    type: String,
    default: ''
  },
  showProgress: {
    type: Boolean,
    default: false
  },
  progressLabel: {
    type: String,
    default: 'Progress'
  },
  footerText: {
    type: String,
    default: ''
  },
  actionText: {
    type: String,
    default: ''
  },
  colorScheme: {
    type: String,
    default: 'purple',
    validator: (value) => ['purple', 'blue', 'green', 'yellow', 'red', 'gray'].includes(value)
  }
});

defineEmits(['action']);

const displayValue = computed(() => {
  if (typeof props.value === 'number') {
    return props.value.toLocaleString();
  }
  return props.value;
});

const progressPercentage = computed(() => {
  if (!props.maxValue) return 0;
  return Math.min(100, Math.round((props.value / props.maxValue) * 100));
});

const iconBackgroundClass = computed(() => {
  const colors = {
    'purple': 'bg-purple-100 text-purple-600',
    'blue': 'bg-blue-100 text-blue-600',
    'green': 'bg-green-100 text-green-600',
    'yellow': 'bg-yellow-100 text-yellow-600',
    'red': 'bg-red-100 text-red-600',
    'gray': 'bg-gray-100 text-gray-600'
  };
  return colors[props.colorScheme];
});

const valueColorClass = computed(() => {
  const colors = {
    'purple': 'text-purple-600',
    'blue': 'text-blue-600',
    'green': 'text-green-600',
    'yellow': 'text-yellow-600',
    'red': 'text-red-600',
    'gray': 'text-gray-900'
  };
  return colors[props.colorScheme];
});

const progressBarColor = computed(() => {
  const colors = {
    'purple': 'bg-purple-500',
    'blue': 'bg-blue-500',
    'green': 'bg-green-500',
    'yellow': 'bg-yellow-500',
    'red': 'bg-red-500',
    'gray': 'bg-gray-500'
  };
  return colors[props.colorScheme];
});
</script>
