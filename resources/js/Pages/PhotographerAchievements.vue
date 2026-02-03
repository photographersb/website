<template>
  <div class="achievements-page max-w-7xl mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Achievements & Growth</h1>
      <p class="text-gray-600">Track your progress, unlock achievements, and level up your photography career</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Loading your achievements...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
      <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      <p class="text-red-700 font-medium mb-2">Failed to load achievements</p>
      <p class="text-red-600 text-sm mb-4">{{ error }}</p>
      <button 
        @click="loadAchievements" 
        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
      >
        Try Again
      </button>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Level Progress Card -->
      <div class="mb-8">
        <LevelProgress
          :current-level="stats.level"
          :total-points="stats.total_points"
          :points-to-next-level="pointsToNextLevel"
          :unlocked-achievements="unlockedCount"
          :total-achievements="totalCount"
          :next-achievement="nextAchievement"
          :streak-days="0"
        />
      </div>

      <!-- Stats Overview Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatsCard
          title="Profile Views"
          subtitle="This month"
          icon="👀"
          :value="stats.profile_views_this_month"
          :secondary-value="`${stats.profile_views.toLocaleString()} total views`"
          color-scheme="blue"
        />
        
        <StatsCard
          title="Conversion Rate"
          subtitle="Views to bookings"
          icon="📈"
          :value="Number.isFinite(Number(stats.conversion_rate)) ? Number(stats.conversion_rate).toFixed(1) + '%' : '0%'"
          secondary-value="Improve your profile!"
          color-scheme="green"
        />
        
        <StatsCard
          title="Portfolio Score"
          subtitle="Profile completeness"
          icon="✨"
          :value="stats.portfolio_completeness"
          :max-value="100"
          :show-progress="true"
          progress-label="Completeness"
          color-scheme="yellow"
          footer-text="Complete your profile to reach 100%"
        />
        
        <StatsCard
          title="Response Rate"
          subtitle="Client inquiries"
          icon="⚡"
          :value="stats.response_rate ? stats.response_rate.toFixed(0) + '%' : '0%'"
          :secondary-value="stats.average_response_time ? `Avg: ${stats.average_response_time}h` : ''"
          color-scheme="purple"
        />
      </div>

      <!-- Achievement Grid -->
      <AchievementGrid />

      <!-- Helpful Tips -->
      <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start space-x-3">
          <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
          <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">💡 Tips to Unlock More Achievements</h3>
            <ul class="space-y-2 text-sm text-blue-800">
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span><strong>Complete your profile:</strong> Add bio, photos, packages, and social links to unlock portfolio achievements</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span><strong>Respond quickly:</strong> Maintain fast response times to earn the "Fast Responder" badge</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span><strong>Deliver excellence:</strong> Earn 5-star reviews to unlock quality achievements</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span><strong>Join competitions:</strong> Participate in photography competitions for special badges</span>
              </li>
              <li class="flex items-start">
                <span class="mr-2">•</span>
                <span><strong>Build relationships:</strong> Create repeat clients to earn the "Client Favorite" achievement</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import LevelProgress from '../components/LevelProgress.vue';
import StatsCard from '../components/StatsCard.vue';
import AchievementGrid from '../components/AchievementGrid.vue';
import api from '../api';

const loading = ref(true);
const error = ref(null);
const stats = ref({
  profile_views: 0,
  profile_views_this_month: 0,
  total_points: 0,
  level: 1,
  conversion_rate: 0,
  response_rate: 0,
  average_response_time: 0,
  portfolio_completeness: 0
});
const unlockedCount = ref(0);
const totalCount = ref(0);
const pointsToNextLevel = ref(0);
const achievements = ref([]);

const nextAchievement = computed(() => {
  // Find the closest locked achievement with the highest progress
  const locked = achievements.value
    .filter(a => !a.is_unlocked && a.progress > 0)
    .sort((a, b) => b.progress_percentage - a.progress_percentage);
  
  return locked[0] || null;
});

const loadAchievements = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await api.get('/photographer/achievements');
    
    if (response.data.status === 'success') {
      const data = response.data.data;
      
      stats.value = data.stats || stats.value;
      unlockedCount.value = data.unlocked_achievements || 0;
      totalCount.value = data.total_achievements || 0;
      pointsToNextLevel.value = data.points_to_next_level || 0;
      
      // Flatten achievements for next achievement calculation
      if (data.achievements_by_category) {
        achievements.value = Object.values(data.achievements_by_category).flat();
      }
    } else {
      error.value = response.data.message || 'Unknown error occurred';
    }
  } catch (err) {
    console.error('Failed to load achievements:', err);
    error.value = err.response?.data?.message || 'Failed to load achievements. Please try again.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadAchievements();
});
</script>
