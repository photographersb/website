<template>
  <router-link 
    :to="href"
    class="block bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-amber-300 transition-all group"
  >
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-3xl">{{ icon }}</span>
          <div>
            <h4 class="font-semibold text-gray-900 group-hover:text-amber-600 transition-colors">
              {{ title }}
            </h4>
            <p class="text-xs text-gray-500">
              {{ description }}
            </p>
          </div>
        </div>
        <svg
          class="w-5 h-5 text-gray-400 group-hover:text-amber-500 transition-colors transform group-hover:translate-x-1"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5l7 7-7 7"
          />
        </svg>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-0 divide-x divide-y divide-gray-200">
      <div
        v-for="stat in stats"
        :key="stat.label"
        class="px-4 py-3"
      >
        <p class="text-xs text-gray-500 truncate">
          {{ stat.label }}
        </p>
        <p class="text-lg font-bold text-gray-900 mt-1">
          {{ formatStat(stat.value) }}
        </p>
      </div>
    </div>

    <!-- Footer Action -->
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-center">
      <span class="text-xs font-medium text-amber-600 group-hover:text-amber-700">
        Open Module →
      </span>
    </div>
  </router-link>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  },
  stats: {
    type: Array,
    default: () => []
  },
  href: {
    type: String,
    required: true
  }
});

const formatStat = (value) => {
  if (typeof value === 'string') return value;
  if (typeof value === 'number') {
    if (value > 1000000) return (value / 1000000).toFixed(1) + 'M';
    if (value > 1000) return (value / 1000).toFixed(1) + 'K';
    return value.toString();
  }
  return value;
};
</script>
