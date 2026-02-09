<template>
  <div class="achievement-grid">
    <!-- Header with Stats -->
    <div class="mb-6 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg p-6 text-white">
      <h2 class="text-2xl font-bold mb-2">
        🏆 Your Achievements
      </h2>
      <div class="flex items-center space-x-6">
        <div>
          <p class="text-3xl font-bold">
            {{ unlockedCount }}/{{ totalCount }}
          </p>
          <p class="text-sm text-purple-100">
            Achievements Unlocked
          </p>
        </div>
        <div>
          <p class="text-3xl font-bold">
            {{ completionPercentage }}%
          </p>
          <p class="text-sm text-purple-100">
            Completion Rate
          </p>
        </div>
        <div>
          <p class="text-3xl font-bold">
            {{ totalPoints }}
          </p>
          <p class="text-sm text-purple-100">
            Total Points
          </p>
        </div>
        <div>
          <p class="text-3xl font-bold">
            Level {{ currentLevel }}
          </p>
          <p class="text-sm text-purple-100">
            Current Level
          </p>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading"
      class="flex justify-center items-center py-12"
    >
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600" />
    </div>

    <!-- Error State -->
    <div
      v-else-if="error"
      class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700"
    >
      <p>{{ error }}</p>
      <button
        class="mt-2 text-sm underline"
        @click="loadAchievements"
      >
        Try again
      </button>
    </div>

    <!-- Achievements by Category -->
    <div v-else>
      <!-- Category Tabs -->
      <div class="flex space-x-2 mb-6 border-b border-gray-200">
        <button
          v-for="category in categories"
          :key="category"
          class="px-4 py-2 font-medium text-sm transition-colors border-b-2"
          :class="selectedCategory === category 
            ? 'text-purple-600 border-purple-600' 
            : 'text-gray-500 border-transparent hover:text-gray-700'"
          @click="selectedCategory = category"
        >
          {{ categoryLabels[category] }} ({{ getCategoryCount(category) }})
        </button>
      </div>

      <!-- Achievement Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <AchievementBadge
          v-for="achievement in filteredAchievements"
          :key="achievement.id"
          :achievement="achievement"
        />
      </div>

      <!-- Empty State -->
      <div
        v-if="filteredAchievements.length === 0"
        class="text-center py-12 text-gray-500"
      >
        <p class="text-lg">
          No achievements in this category yet.
        </p>
        <p class="text-sm mt-2">
          Keep working to unlock your first achievement!
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AchievementBadge from './AchievementBadge.vue';
import api from '../api';

const loading = ref(true);
const error = ref(null);
const achievements = ref({});
const stats = ref(null);
const unlockedCount = ref(0);
const totalCount = ref(0);
const completionPercentage = ref(0);
const totalPoints = ref(0);
const currentLevel = ref(1);
const pointsToNextLevel = ref(0);

const selectedCategory = ref('all');

const categories = ['all', 'bookings', 'reviews', 'portfolio', 'competitions', 'community'];

const categoryLabels = {
  'all': 'All Achievements',
  'bookings': '📸 Bookings',
  'reviews': '⭐ Reviews',
  'portfolio': '🎨 Portfolio',
  'competitions': '🏆 Competitions',
  'community': '🌟 Community'
};

const filteredAchievements = computed(() => {
  if (selectedCategory.value === 'all') {
    // Flatten all categories
    return Object.values(achievements.value).flat();
  }
  return achievements.value[selectedCategory.value] || [];
});

const getCategoryCount = (category) => {
  if (category === 'all') {
    return totalCount.value;
  }
  return (achievements.value[category] || []).length;
};

const loadAchievements = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await api.get('/photographer/achievements');
    
    if (response.data.status === 'success') {
      const data = response.data.data;
      
      achievements.value = data.achievements_by_category || {};
      stats.value = data.stats || null;
      unlockedCount.value = data.unlocked_achievements || 0;
      totalCount.value = data.total_achievements || 0;
      completionPercentage.value = data.completion_percentage || 0;
      pointsToNextLevel.value = data.points_to_next_level || 0;
      
      if (stats.value) {
        totalPoints.value = stats.value.total_points || 0;
        currentLevel.value = stats.value.level || 1;
      }
    }
  } catch (err) {
    console.error('Failed to load achievements:', err);
    error.value = 'Failed to load achievements. Please try again.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadAchievements();
});
</script>

<style scoped>
.achievement-grid {
  /* Additional styles if needed */
}
</style>
