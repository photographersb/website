<template>
  <div 
    class="achievement-badge relative flex flex-col items-center p-4 rounded-lg transition-all duration-300"
    :class="[
      isUnlocked ? 'bg-white shadow-md hover:shadow-lg' : 'bg-gray-50 opacity-60',
      badgeColorClass
    ]"
  >
    <!-- Badge Icon -->
    <div 
      class="relative mb-3"
      :class="isUnlocked ? 'scale-100' : 'scale-90'"
    >
      <div 
        class="w-16 h-16 rounded-full flex items-center justify-center text-3xl"
        :class="iconBackgroundClass"
      >
        {{ achievement.icon }}
      </div>
      
      <!-- Locked Overlay -->
      <div 
        v-if="!isUnlocked" 
        class="absolute inset-0 flex items-center justify-center bg-gray-800 bg-opacity-40 rounded-full"
      >
        <svg
          class="w-6 h-6 text-white"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
    </div>

    <!-- Badge Name -->
    <h3 
      class="text-sm font-semibold text-center mb-1"
      :class="isUnlocked ? 'text-gray-900' : 'text-gray-500'"
    >
      {{ achievement.name }}
    </h3>

    <!-- Badge Description -->
    <p 
      class="text-xs text-center mb-3 line-clamp-2 h-8"
      :class="isUnlocked ? 'text-gray-600' : 'text-gray-400'"
    >
      {{ achievement.description }}
    </p>

    <!-- Progress Bar (for locked achievements) -->
    <div
      v-if="!isUnlocked"
      class="w-full"
    >
      <div class="flex justify-between items-center mb-1">
        <span class="text-xs text-gray-500">Progress</span>
        <span class="text-xs font-medium text-gray-700">{{ progressPercentage }}%</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div 
          class="h-2 rounded-full transition-all duration-500"
          :class="progressBarColorClass"
          :style="{ width: progressPercentage + '%' }"
        />
      </div>
      <p class="text-xs text-gray-500 mt-1 text-center">
        {{ achievement.progress }} / {{ achievement.required_count }}
      </p>
    </div>

    <!-- Unlocked Info -->
    <div
      v-else
      class="w-full"
    >
      <div class="flex items-center justify-center space-x-1 text-xs text-green-600">
        <svg
          class="w-4 h-4"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
          />
        </svg>
        <span class="font-medium">Unlocked</span>
      </div>
      <p class="text-xs text-gray-500 mt-1 text-center">
        {{ formatDate(achievement.unlocked_at) }}
      </p>
    </div>

    <!-- Points Badge -->
    <div 
      class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-bold"
      :class="pointsBadgeClass"
    >
      +{{ achievement.points }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatDate as formatDateValue } from '../utils/formatters';

const props = defineProps({
  achievement: {
    type: Object,
    required: true
  }
});

const isUnlocked = computed(() => props.achievement.is_unlocked);
const progressPercentage = computed(() => props.achievement.progress_percentage || 0);

// Badge color classes based on badge_color
const badgeColorClass = computed(() => {
  if (!isUnlocked.value) return '';
  
  const colors = {
    'gray': 'border-l-4 border-gray-400',
    'bronze': 'border-l-4 border-orange-600',
    'silver': 'border-l-4 border-gray-400',
    'gold': 'border-l-4 border-yellow-500',
    'platinum': 'border-l-4 border-purple-600'
  };
  return colors[props.achievement.badge_color] || colors.gray;
});

const iconBackgroundClass = computed(() => {
  const colors = {
    'gray': isUnlocked.value ? 'bg-gray-100' : 'bg-gray-200',
    'bronze': isUnlocked.value ? 'bg-orange-100' : 'bg-gray-200',
    'silver': isUnlocked.value ? 'bg-gray-100' : 'bg-gray-200',
    'gold': isUnlocked.value ? 'bg-yellow-100' : 'bg-gray-200',
    'platinum': isUnlocked.value ? 'bg-purple-100' : 'bg-gray-200'
  };
  return colors[props.achievement.badge_color] || colors.gray;
});

const progressBarColorClass = computed(() => {
  const colors = {
    'gray': 'bg-gray-400',
    'bronze': 'bg-orange-500',
    'silver': 'bg-gray-400',
    'gold': 'bg-yellow-500',
    'platinum': 'bg-purple-500'
  };
  return colors[props.achievement.badge_color] || colors.gray;
});

const pointsBadgeClass = computed(() => {
  const colors = {
    'gray': 'bg-gray-100 text-gray-700',
    'bronze': 'bg-orange-100 text-orange-700',
    'silver': 'bg-gray-100 text-gray-700',
    'gold': 'bg-yellow-100 text-yellow-700',
    'platinum': 'bg-purple-100 text-purple-700'
  };
  return colors[props.achievement.badge_color] || colors.gray;
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  return formatDateValue(dateString);
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
