<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Judge Dashboard</h1>
        <p class="mt-2 text-gray-600">Review and score competition submissions</p>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-sm text-gray-600">Assigned Competitions</div>
          <div class="text-2xl font-bold text-burgundy">{{ competitions.length }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-sm text-gray-600">Pending Scores</div>
          <div class="text-2xl font-bold text-orange-500">{{ pendingScores }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-sm text-gray-600">Completed Scores</div>
          <div class="text-2xl font-bold text-green-600">{{ completedScores }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-sm text-gray-600">Accuracy</div>
          <div class="text-2xl font-bold text-blue-600">{{ accuracy }}%</div>
        </div>
      </div>

      <!-- Competitions List -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
          <h2 class="text-lg font-semibold text-gray-900">Your Assigned Competitions</h2>
        </div>

        <div v-if="loading" class="p-6">
          <div class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-24 bg-gray-200 rounded animate-pulse"></div>
          </div>
        </div>

        <div v-else-if="competitions.length === 0" class="p-6 text-center">
          <p class="text-gray-500">No competitions assigned yet</p>
        </div>

        <div v-else class="divide-y">
          <div 
            v-for="competition in competitions" 
            :key="competition.id"
            class="p-6 hover:bg-gray-50 transition"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ competition.title }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ competition.description }}</p>
                
                <div class="mt-4 flex items-center gap-4">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                    :class="getStatusClass(competition.status)">
                    {{ formatStatus(competition.status) }}
                  </span>
                  
                  <span class="text-sm text-gray-600">
                    Submissions: <strong>{{ competition.submission_count || 0 }}</strong>
                  </span>
                  
                  <span class="text-sm text-gray-600" v-if="competition.deadline">
                    Deadline: <strong>{{ formatDate(competition.voting_end_date) }}</strong>
                  </span>
                </div>
              </div>

              <router-link 
                :to="{ name: 'judge.competition', params: { competitionId: competition.id } }"
                class="ml-4 inline-flex items-center px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark transition"
              >
                Score Submissions
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
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
import { useRouter } from 'vue-router';
import api from '../../api';

const router = useRouter();
const competitions = ref([]);
const loading = ref(true);
const completedScores = ref(0);
const pendingScores = ref(0);

const accuracy = computed(() => {
  const total = completedScores.value + pendingScores.value;
  if (total === 0) return 0;
  return Math.round((completedScores.value / total) * 100);
});

const formatStatus = (status) => {
  const statusMap = {
    'draft': 'Draft',
    'published': 'Active',
    'closed': 'Closed',
    'results_published': 'Results Published'
  };
  return statusMap[status] || status;
};

const getStatusClass = (status) => {
  const classMap = {
    'draft': 'bg-gray-100 text-gray-800',
    'published': 'bg-blue-100 text-blue-800',
    'closed': 'bg-yellow-100 text-yellow-800',
    'results_published': 'bg-green-100 text-green-800'
  };
  return classMap[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric', 
    year: 'numeric' 
  });
};

const loadCompetitions = async () => {
  loading.value = true;
  try {
    const { data } = await api.get('/api/v1/judge/assignments');
    if (data.status === 'success') {
      competitions.value = data.data;
      
      // Calculate stats
      let pending = 0;
      let completed = 0;
      
      for (const comp of competitions.value) {
        if (comp.scoring_stats) {
          pending += comp.scoring_stats.pending || 0;
          completed += comp.scoring_stats.completed || 0;
        }
      }
      
      pendingScores.value = pending;
      completedScores.value = completed;
    }
  } catch (error) {
    console.error('Error loading competitions:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadCompetitions();
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
