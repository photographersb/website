<template>
  <div class="level-progress bg-gradient-to-br from-purple-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-sm font-medium text-purple-100">Your Level</h3>
        <p class="text-4xl font-bold">Level {{ currentLevel }}</p>
      </div>
      <div class="text-right">
        <p class="text-sm text-purple-100">Total Points</p>
        <p class="text-2xl font-bold">{{ totalPoints.toLocaleString() }}</p>
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="mb-4">
      <div class="flex justify-between items-center mb-2">
        <span class="text-sm text-purple-100">Progress to Level {{ currentLevel + 1 }}</span>
        <span class="text-sm font-medium">{{ progressPercentage }}%</span>
      </div>
      <div class="w-full bg-purple-800 bg-opacity-30 rounded-full h-3">
        <div 
          class="bg-white h-3 rounded-full transition-all duration-500 relative overflow-hidden"
          :style="{ width: progressPercentage + '%' }"
        >
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-50 animate-shimmer"></div>
        </div>
      </div>
      <p class="text-xs text-purple-100 mt-2">
        {{ pointsToNextLevel.toLocaleString() }} points to next level
      </p>
    </div>

    <!-- Level Milestones -->
    <div class="grid grid-cols-3 gap-3">
      <div class="text-center">
        <div class="w-10 h-10 mx-auto mb-1 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
        </div>
        <p class="text-xs text-purple-100">{{ unlockedAchievements }}</p>
        <p class="text-xs text-purple-200 font-medium">Achievements</p>
      </div>
      
      <div class="text-center">
        <div class="w-10 h-10 mx-auto mb-1 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
          </svg>
        </div>
        <p class="text-xs text-purple-100">{{ streakDays || 0 }}</p>
        <p class="text-xs text-purple-200 font-medium">Day Streak</p>
      </div>
      
      <div class="text-center">
        <div class="w-10 h-10 mx-auto mb-1 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
          </svg>
        </div>
        <p class="text-xs text-purple-100">{{ completionRate }}%</p>
        <p class="text-xs text-purple-200 font-medium">Completion</p>
      </div>
    </div>

    <!-- Next Achievement Hint -->
    <div v-if="nextAchievement" class="mt-4 pt-4 border-t border-white border-opacity-20">
      <p class="text-xs text-purple-100 mb-1">Next Achievement</p>
      <div class="flex items-center space-x-2">
        <span class="text-xl">{{ nextAchievement.icon }}</span>
        <div class="flex-1">
          <p class="text-sm font-medium">{{ nextAchievement.name }}</p>
          <p class="text-xs text-purple-200">{{ nextAchievement.progress }} / {{ nextAchievement.required_count }}</p>
        </div>
        <span class="text-xs font-bold bg-white bg-opacity-20 px-2 py-1 rounded">
          +{{ nextAchievement.points }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  currentLevel: {
    type: Number,
    required: true
  },
  totalPoints: {
    type: Number,
    required: true
  },
  pointsToNextLevel: {
    type: Number,
    required: true
  },
  unlockedAchievements: {
    type: Number,
    default: 0
  },
  totalAchievements: {
    type: Number,
    default: 0
  },
  nextAchievement: {
    type: Object,
    default: null
  },
  streakDays: {
    type: Number,
    default: 0
  }
});

const progressPercentage = computed(() => {
  // Calculate based on points in current level
  // Level formula: every 100 points = 1 level
  const pointsInCurrentLevel = props.totalPoints % 100;
  return Math.round((pointsInCurrentLevel / 100) * 100);
});

const completionRate = computed(() => {
  if (props.totalAchievements === 0) return 0;
  return Math.round((props.unlockedAchievements / props.totalAchievements) * 100);
});
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-shimmer {
  animation: shimmer 2s infinite;
}
</style>
