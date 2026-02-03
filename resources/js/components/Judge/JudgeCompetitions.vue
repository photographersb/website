<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8 flex items-center justify-between">
        <div>
          <router-link to="/judge/dashboard" class="text-burgundy hover:text-burgundy-dark mb-4">
            ← Back to Dashboard
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900">{{ competition.title }}</h1>
          <p class="mt-2 text-gray-600">{{ competition.description }}</p>
        </div>
      </div>

      <!-- Scoring Progress -->
      <div class="bg-white rounded-lg shadow mb-8 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Scoring Progress</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <div class="text-sm text-gray-600">Total Submissions</div>
            <div class="text-3xl font-bold text-gray-900">{{ scoringStats.total || 0 }}</div>
          </div>
          <div>
            <div class="text-sm text-gray-600">Completed Scores</div>
            <div class="text-3xl font-bold text-green-600">{{ scoringStats.completed || 0 }}</div>
          </div>
          <div>
            <div class="text-sm text-gray-600">Pending Scores</div>
            <div class="text-3xl font-bold text-orange-500">{{ scoringStats.pending || 0 }}</div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Overall Progress</span>
            <span class="text-sm font-semibold text-gray-900">{{ progressPercent }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div 
              class="bg-green-600 h-2 rounded-full transition-all duration-300"
              :style="{ width: progressPercent + '%' }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Submissions List -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">Submissions</h2>
          <div class="flex gap-2">
            <select 
              v-model="filterStatus"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
            >
              <option value="">All Submissions</option>
              <option value="pending">Pending Score</option>
              <option value="scored">Already Scored</option>
            </select>
          </div>
        </div>

        <div v-if="loading" class="p-6">
          <div class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-20 bg-gray-200 rounded animate-pulse"></div>
          </div>
        </div>

        <div v-else-if="filteredSubmissions.length === 0" class="p-6 text-center">
          <p class="text-gray-500">No submissions found</p>
        </div>

        <div v-else class="divide-y">
          <div 
            v-for="submission in filteredSubmissions" 
            :key="submission.id"
            class="p-6 hover:bg-gray-50 transition"
          >
            <div class="flex items-start gap-6">
              <!-- Thumbnail -->
              <div class="flex-shrink-0">
                <img 
                  :src="submission.photo_url" 
                  :alt="submission.title"
                  class="w-24 h-24 object-cover rounded"
                />
              </div>

              <!-- Info -->
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ submission.title }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ submission.description }}</p>
                
                <div class="mt-3 flex items-center gap-4 text-sm">
                  <span class="text-gray-600">
                    By: <strong>{{ submission.photographer.name }}</strong>
                  </span>
                  
                  <span v-if="submission.final_score" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-800">
                    Score: {{ submission.final_score.toFixed(1) }}/10
                  </span>
                  
                  <span v-else class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">
                    Pending
                  </span>
                </div>
              </div>

              <!-- Action -->
              <router-link 
                :to="{ name: 'judge.score-submission', params: { competitionId: route.params.competitionId, submissionId: submission.id } }"
                class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark transition"
              >
                {{ submission.final_score ? 'Edit Score' : 'Score Now' }}
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../../api';

const router = useRouter();
const route = useRoute();

const competition = ref({});
const submissions = ref([]);
const scoringStats = ref({});
const loading = ref(true);
const filterStatus = ref('');

const progressPercent = computed(() => {
  if (!scoringStats.value.total) return 0;
  return Math.round((scoringStats.value.completed / scoringStats.value.total) * 100);
});

const filteredSubmissions = computed(() => {
  if (filterStatus.value === 'pending') {
    return submissions.value.filter(s => !s.final_score);
  } else if (filterStatus.value === 'scored') {
    return submissions.value.filter(s => s.final_score);
  }
  return submissions.value;
});

const loadData = async () => {
  loading.value = true;
  try {
    const competitionId = route.params.competitionId;
    
    const { data } = await api.get(`/api/v1/admin/competitions/${competitionId}`);
    if (data.status === 'success') {
      competition.value = data.data;
    }

    // Get judge-assigned submissions
    const { data: subData } = await api.get(`/api/v1/competitions/${competitionId}/judge/submissions`);
    if (subData.status === 'success') {
      submissions.value = subData.data;
    }

    // Get scoring progress stats
    const { data: statsData } = await api.get(`/api/v1/competitions/${competitionId}/judge/progress`);
    if (statsData.status === 'success') {
      scoringStats.value = statsData.data;
    }
  } catch (error) {
    console.error('Error loading data:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadData();
});
</script>

<style scoped>
.bg-burgundy {
  @apply bg-[#8B0000];
}

.bg-burgundy-dark {
  @apply bg-[#5C0000];
}

.text-burgundy {
  @apply text-[#8B0000];
}
</style>
