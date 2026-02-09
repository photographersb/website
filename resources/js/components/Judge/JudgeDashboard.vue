<template>
  <div class="judge-shell min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <section class="judge-hero">
        <div>
          <p class="judge-kicker">JUDGE PANEL</p>
          <h1 class="judge-title">Score with clarity.</h1>
          <p class="judge-subtitle">
            Keep momentum high with focused submissions and a streamlined scoring flow.
          </p>
        </div>
        <button
          type="button"
          class="judge-action"
          @click="loadCompetitions"
        >
          Refresh
        </button>
      </section>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="judge-kpi">
          <p>Assigned Competitions</p>
          <h3>{{ competitions.length }}</h3>
        </div>
        <div class="judge-kpi judge-kpi--warm">
          <p>Pending Scores</p>
          <h3>{{ pendingScores }}</h3>
        </div>
        <div class="judge-kpi judge-kpi--cool">
          <p>Completed Scores</p>
          <h3>{{ completedScores }}</h3>
        </div>
        <div class="judge-kpi judge-kpi--accent">
          <p>Accuracy</p>
          <h3>{{ accuracy }}%</h3>
        </div>
      </div>

      <!-- Competitions List -->
      <div class="judge-card">
        <div class="px-6 py-5 border-b flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">
            Your Assigned Competitions
          </h2>
          <span class="judge-count">{{ competitions.length }} total</span>
        </div>

        <div
          v-if="loading"
          class="p-6"
        >
          <div class="space-y-4">
            <div
              v-for="i in 3"
              :key="i"
              class="h-24 bg-gray-200 rounded animate-pulse"
            />
          </div>
        </div>

        <div
          v-else-if="competitions.length === 0"
          class="p-6 text-center"
        >
          <p class="text-gray-500">
            No competitions assigned yet
          </p>
        </div>

        <div
          v-else
          class="judge-competitions-grid"
        >
          <div 
            v-for="competition in competitions" 
            :key="competition.id"
            class="judge-competition-card"
          >
            <div>
              <h3 class="judge-competition-title">
                {{ competition.title }}
              </h3>
              <p class="judge-competition-desc">
                {{ competition.description }}
              </p>

              <div class="judge-competition-meta">
                <span
                  class="judge-pill"
                  :class="getStatusClass(competition.status)"
                >
                  {{ formatStatus(competition.status) }}
                </span>
                <span class="judge-meta">
                  Submissions: <strong>{{ competition.total_submissions ?? competition.submission_count ?? 0 }}</strong>
                </span>
                <span
                  v-if="competition.deadline"
                  class="judge-meta"
                >
                  Deadline: <strong>{{ formatDate(competition.voting_end_date) }}</strong>
                </span>
              </div>
            </div>

            <div class="judge-action-col">
              <router-link 
                :to="{ name: 'judge.competition', params: { competitionId: competition.id } }"
                class="judge-primary"
              >
                Score Submissions
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
import { formatDate as formatDateValue } from '../../utils/formatters';

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
    'draft': 'judge-pill--draft',
    'published': 'judge-pill--published',
    'closed': 'judge-pill--closed',
    'results_published': 'judge-pill--results'
  };
  return classMap[status] || 'judge-pill--draft';
};

const formatDate = (date) => {
  if (!date) return '';
  return formatDateValue(date);
};

const loadCompetitions = async () => {
  loading.value = true;
  try {
    const [dashboardRes, competitionsRes] = await Promise.all([
      api.get('/judge/dashboard'),
      api.get('/judge/competitions')
    ]);

    if (dashboardRes.data?.status === 'success') {
      const stats = dashboardRes.data.data?.stats || {};
      completedScores.value = stats.completed_scores || 0;
      pendingScores.value = stats.pending_scores || 0;
    }

    if (competitionsRes.data?.status === 'success') {
      const payload = competitionsRes.data.data || {};
      competitions.value = Array.isArray(payload.data) ? payload.data : [];
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

.judge-shell {
  background: linear-gradient(180deg, #f9f5f2 0%, #f5f1ee 50%, #ffffff 100%);
}

.judge-hero {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 24px;
  padding: 32px;
  border-radius: 20px;
  background: radial-gradient(circle at top left, #ffffff 0%, #f3ebe7 60%, #efe7e3 100%);
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
  margin-bottom: 32px;
}

.judge-kicker {
  font-size: 12px;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: #6b4b3e;
  font-weight: 600;
}

.judge-title {
  font-size: 36px;
  font-weight: 700;
  color: #1f2937;
  font-family: Georgia, "Times New Roman", serif;
  margin-top: 8px;
}

.judge-subtitle {
  color: #6b7280;
  margin-top: 8px;
}

.judge-action {
  padding: 10px 18px;
  border-radius: 999px;
  border: 1px solid #d6c6be;
  background: #ffffff;
  font-weight: 600;
  color: #5a3a2f;
}

.judge-kpi {
  background: #ffffff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 16px 30px rgba(15, 23, 42, 0.06);
  border: 1px solid #f1e7e1;
}

.judge-kpi p {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #6b7280;
}

.judge-kpi h3 {
  margin-top: 8px;
  font-size: 28px;
  color: #7c2d12;
  font-weight: 700;
}

.judge-kpi--warm h3 {
  color: #c2410c;
}

.judge-kpi--cool h3 {
  color: #0f766e;
}

.judge-kpi--accent h3 {
  color: #1d4ed8;
}

.judge-card {
  background: #ffffff;
  border-radius: 18px;
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
}

.judge-competitions-grid {
  display: grid;
  gap: 20px;
  padding: 24px;
}

.judge-competition-card {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 20px;
  padding: 20px 22px;
  border-radius: 16px;
  border: 1px solid #f1e7e1;
  background: linear-gradient(135deg, #ffffff 0%, #fdf9f6 100%);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
  position: relative;
  overflow: hidden;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  animation: judgeCardRise 0.55s ease both;
}

.judge-competition-card::after {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top right, rgba(244, 237, 233, 0.9), rgba(255, 255, 255, 0));
  opacity: 0;
  transition: opacity 0.25s ease;
  pointer-events: none;
}

.judge-competition-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 18px 32px rgba(15, 23, 42, 0.1);
  border-color: #e9d8cf;
}

.judge-competition-card:hover::after {
  opacity: 1;
}

.judge-competition-card:focus-within {
  outline: 2px solid #c2410c;
  outline-offset: 3px;
}

.judge-competition-title {
  font-size: 18px;
  font-weight: 700;
  color: #1f2937;
  transition: color 0.2s ease;
}

.judge-competition-card:hover .judge-competition-title {
  color: #7c2d12;
}

.judge-competition-desc {
  font-size: 14px;
  color: #6b7280;
  margin-top: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.judge-competition-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 16px;
  align-items: center;
}

.judge-pill {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.judge-pill::before {
  content: "";
  width: 6px;
  height: 6px;
  border-radius: 999px;
  background: currentColor;
  opacity: 0.8;
}

.judge-pill--draft {
  background: #f3f4f6;
  color: #374151;
}

.judge-pill--published {
  background: #dbeafe;
  color: #1d4ed8;
}

.judge-pill--closed {
  background: #fef3c7;
  color: #92400e;
}

.judge-pill--results {
  background: #dcfce7;
  color: #166534;
}

.judge-meta {
  font-size: 13px;
  color: #4b5563;
}

.judge-action-col {
  display: flex;
  align-items: center;
}

.judge-count {
  font-size: 12px;
  color: #6b7280;
  padding: 6px 12px;
  border-radius: 999px;
  background: #f4ede9;
}

.judge-primary {
  background: linear-gradient(135deg, #7c2d12 0%, #9a3412 100%);
  color: #ffffff;
  padding: 10px 18px;
  border-radius: 999px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 10px 18px rgba(124, 45, 18, 0.22);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.judge-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 14px 24px rgba(124, 45, 18, 0.3);
}

.judge-competition-card:nth-child(3n + 1) {
  animation-delay: 0.04s;
}

.judge-competition-card:nth-child(3n + 2) {
  animation-delay: 0.08s;
}

.judge-competition-card:nth-child(3n) {
  animation-delay: 0.12s;
}

@keyframes judgeCardRise {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (prefers-reduced-motion: reduce) {
  .judge-competition-card {
    animation: none;
    transition: none;
  }
}
</style>
