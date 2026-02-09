<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <section class="judge-hero">
        <div>
          <router-link
            to="/judge/dashboard"
            class="judge-back"
          >
            ← Back to Dashboard
          </router-link>
          <p class="judge-kicker">COMPETITION REVIEW</p>
          <h1 class="judge-title">
            {{ competition.title }}
          </h1>
          <p class="judge-subtitle">
            {{ competition.description }}
          </p>
          <div class="judge-chips">
            <span class="judge-chip judge-chip--neutral">
              Total: {{ scoringStats.total || 0 }}
            </span>
            <span class="judge-chip judge-chip--good">
              Completed: {{ scoringStats.completed || 0 }}
            </span>
            <span class="judge-chip judge-chip--warn">
              Pending: {{ scoringStats.pending || 0 }}
            </span>
          </div>
        </div>
        <div class="judge-hero__actions">
          <button
            type="button"
            class="judge-primary"
            :disabled="!nextUnscored"
            @click="goToNextUnscored"
          >
            Next Unscored
          </button>
          <select 
            v-model="filterStatus"
            class="judge-select"
          >
            <option value="">
              All Submissions
            </option>
            <option value="pending">
              Pending Score
            </option>
            <option value="scored">
              Already Scored
            </option>
          </select>
        </div>
      </section>

      <!-- Scoring Progress -->
      <div class="judge-card judge-progress">
        <div class="judge-progress__header">
          <h2>Scoring Progress</h2>
          <span>{{ progressPercent }}% complete</span>
        </div>
        <div class="judge-progress__stats">
          <div>
            <p>Total Submissions</p>
            <h3>{{ scoringStats.total || 0 }}</h3>
          </div>
          <div>
            <p>Completed Scores</p>
            <h3>{{ scoringStats.completed || 0 }}</h3>
          </div>
          <div>
            <p>Pending Scores</p>
            <h3>{{ scoringStats.pending || 0 }}</h3>
          </div>
        </div>
        <div class="judge-progress__bar">
          <div
            class="judge-progress__bar-fill"
            :style="{ width: progressPercent + '%' }"
          />
        </div>
      </div>

      <!-- Submissions List -->
      <div class="judge-card">
        <div class="judge-card__header">
          <h2>Submissions</h2>
          <div class="judge-card__meta">
            <span
              v-if="averageScoreTotal !== null"
              class="judge-card__stat"
            >
              Avg score: <strong>{{ formatFixed(averageScoreTotal, 1, '0.0') }}/50</strong>
            </span>
            <span>{{ displaySubmissions.length }} shown</span>
          </div>
        </div>

        <div
          v-if="loading"
          class="p-6"
        >
          <div class="space-y-4">
            <div
              v-for="i in 3"
              :key="i"
              class="h-20 bg-gray-200 rounded animate-pulse"
            />
          </div>
        </div>

        <div
          v-else-if="filteredSubmissions.length === 0"
          class="p-6 text-center"
        >
          <p class="text-gray-500">
            No submissions found
          </p>
        </div>

        <div
          v-else
          class="judge-submissions-grid"
        >
          <div 
            v-for="submission in displaySubmissions" 
            :key="submission.id"
            class="judge-submission-card"
            role="button"
            tabindex="0"
            @click="goToSubmission(submission.id)"
            @keydown.enter.prevent="goToSubmission(submission.id)"
            @keydown.space.prevent="goToSubmission(submission.id)"
          >
            <img 
              :src="submission.thumbnail_url || submission.image_url || submission.photo_url" 
              :alt="submission.title"
              class="judge-submission-thumb"
            >

            <div class="judge-submission-body">
              <div>
                <div class="judge-submission-heading">
                  <h3>{{ submission.title }}</h3>
                  <span
                    class="judge-status-pill"
                    :class="submission.is_scored ? 'judge-status-pill--scored' : 'judge-status-pill--pending'"
                  >
                    {{ submission.is_scored ? 'Scored' : 'Pending' }}
                  </span>
                </div>
                <p>{{ submission.description }}</p>
              </div>
              <div class="judge-submission-meta">
                <span>By: <strong>{{ submission.photographer.name }}</strong></span>
                <span
                  v-if="submission.is_scored && submission.my_score"
                  class="judge-status judge-status--scored"
                >
                  Score: {{ formatFixed(submission.my_score.total_score, 1, '0.0') }}/50
                </span>
                <span
                  v-else
                  class="judge-status judge-status--pending"
                >
                  Pending
                </span>
              </div>
            </div>

            <button 
              type="button"
              class="judge-secondary"
              @click.stop="goToSubmission(submission.id)"
            >
              {{ submission.is_scored ? 'Edit Score' : 'Score Now' }}
            </button>
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
import { formatFixed } from '../../utils/formatters';

const router = useRouter();
const route = useRoute();

const competition = ref({});
const submissions = ref([]);
const scoringStats = ref({ total: 0, completed: 0, pending: 0 });
const loading = ref(true);
const filterStatus = ref('');

const progressPercent = computed(() => {
  if (!scoringStats.value.total) return 0;
  return Math.round((scoringStats.value.completed / scoringStats.value.total) * 100);
});

const filteredSubmissions = computed(() => {
  if (filterStatus.value === 'pending') {
    return submissions.value.filter(s => !s.is_scored);
  } else if (filterStatus.value === 'scored') {
    return submissions.value.filter(s => s.is_scored);
  }
  return submissions.value;
});

const averageScoreTotal = computed(() => {
  const scored = submissions.value.filter((s) => s.is_scored && s.my_score);
  if (!scored.length) return null;
  const sum = scored.reduce((acc, item) => {
    const value = Number(item.my_score?.total_score || 0);
    return acc + (Number.isFinite(value) ? value : 0);
  }, 0);
  return sum / scored.length;
});

const displaySubmissions = computed(() => {
  if (filterStatus.value) return filteredSubmissions.value;
  return [...filteredSubmissions.value].sort((a, b) => Number(a.is_scored) - Number(b.is_scored));
});

const nextUnscored = computed(() => submissions.value.find((item) => !item.is_scored));

const goToNextUnscored = () => {
  if (!nextUnscored.value) return;
  router.push({
    name: 'judge.score-submission',
    params: { competitionId: route.params.competitionId, submissionId: nextUnscored.value.id }
  });
};

const goToSubmission = (submissionId) => {
  router.push({
    name: 'judge.score-submission',
    params: { competitionId: route.params.competitionId, submissionId }
  });
};

const loadData = async () => {
  loading.value = true;
  try {
    const competitionId = route.params.competitionId;

    const { data } = await api.get(`/judge/competitions/${competitionId}/submissions`);
    if (data.status === 'success') {
      const payload = data.data || {};
      competition.value = payload.competition || {};

      const paged = payload.submissions || {};
      const items = Array.isArray(paged.data) ? paged.data : Array.isArray(paged) ? paged : [];
      submissions.value = items.map((submission) => {
        const score = Array.isArray(submission.scores) ? submission.scores[0] : null;
        const isScored = score && score.status === 'completed';
        return {
          ...submission,
          is_scored: isScored,
          my_score: score,
        };
      });

      const completed = submissions.value.filter((s) => s.is_scored).length;
      scoringStats.value = {
        total: submissions.value.length,
        completed,
        pending: submissions.value.length - completed,
      };
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
  animation: judgeFadeIn 0.6s ease both;
}

.judge-back {
  display: inline-flex;
  align-items: center;
  color: #7c2d12;
  font-weight: 600;
  margin-bottom: 12px;
}

.judge-kicker {
  font-size: 12px;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: #6b4b3e;
  font-weight: 600;
}

.judge-title {
  font-size: 32px;
  font-weight: 700;
  color: #1f2937;
  font-family: Georgia, "Times New Roman", serif;
  margin-top: 8px;
}

.judge-subtitle {
  color: #6b7280;
}

.judge-chips {
  margin-top: 16px;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.judge-chip {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.judge-chip--neutral {
  background: #f4ede9;
  color: #6b4b3e;
}

.judge-chip--good {
  background: #dcfce7;
  color: #166534;
}

.judge-chip--warn {
  background: #fef3c7;
  color: #92400e;
}

.judge-hero__actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.judge-primary {
  background: linear-gradient(135deg, #7c2d12 0%, #9a3412 100%);
  color: #ffffff;
  padding: 8px 14px;
  border-radius: 999px;
  font-weight: 600;
  font-size: 13px;
  box-shadow: 0 10px 18px rgba(124, 45, 18, 0.22);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.judge-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.judge-primary:focus-visible,
.judge-secondary:focus-visible,
.judge-select:focus-visible,
.judge-back:focus-visible {
  outline: 2px solid #c2410c;
  outline-offset: 2px;
}

.judge-select {
  border: 1px solid #e5e7eb;
  border-radius: 999px;
  padding: 8px 14px;
  background: #ffffff;
  font-size: 14px;
}

.judge-card {
  background: #ffffff;
  border-radius: 18px;
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
  animation: judgeFadeIn 0.7s ease both;
}

.judge-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #efe7e3;
  font-weight: 600;
}

.judge-card__meta {
  display: flex;
  gap: 12px;
  align-items: center;
  color: #6b7280;
  font-size: 13px;
}

.judge-card__stat strong {
  color: #7c2d12;
  font-weight: 700;
}

.judge-progress {
  padding: 24px;
  display: grid;
  gap: 16px;
  margin-bottom: 20px;
}

.judge-progress__header {
  display: flex;
  justify-content: space-between;
  color: #6b7280;
  font-size: 14px;
  font-weight: 600;
}

.judge-progress__stats {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
}

.judge-progress__stats p {
  font-size: 12px;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.14em;
}

.judge-progress__stats h3 {
  font-size: 24px;
  font-weight: 700;
  color: #7c2d12;
}

.judge-progress__bar {
  height: 8px;
  background: #efe7e3;
  border-radius: 999px;
  overflow: hidden;
}

.judge-progress__bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #7c2d12 0%, #c2410c 100%);
}

.judge-submissions-grid {
  display: grid;
  gap: 22px;
  padding: 26px;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
}

.judge-submission-card {
  border: 1px solid #f1e7e1;
  border-radius: 16px;
  background: #ffffff;
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 14px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  animation: judgeRise 0.5s ease both;
}

.judge-submission-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 26px rgba(15, 23, 42, 0.08);
}

.judge-submission-card:hover .judge-submission-thumb {
  transform: scale(1.03);
}

.judge-submission-card:nth-child(3n + 1) {
  animation-delay: 0.04s;
}

.judge-submission-card:nth-child(3n + 2) {
  animation-delay: 0.08s;
}

.judge-submission-card:nth-child(3n) {
  animation-delay: 0.12s;
}

@keyframes judgeFadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes judgeRise {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.judge-submission-thumb {
  width: 100%;
  height: 170px;
  object-fit: cover;
  border-radius: 12px;
  transition: transform 0.25s ease;
}

.judge-submission-body {
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
}

.judge-submission-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.judge-status-pill {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
}

.judge-status-pill--scored {
  background: #dcfce7;
  color: #166534;
}

.judge-status-pill--pending {
  background: #fef3c7;
  color: #92400e;
}

.judge-submission-body h3 {
  font-size: 15px;
  font-weight: 700;
  color: #1f2937;
}

.judge-submission-body p {
  font-size: 13px;
  color: #6b7280;
  margin-top: 6px;
}

.judge-submission-meta {
  margin-top: 10px;
  display: flex;
  flex-wrap: wrap;
  gap: 8px 12px;
  font-size: 12px;
  color: #4b5563;
}

.judge-submission-meta strong {
  color: #1f2937;
}

.judge-status {
  padding: 4px 10px;
  border-radius: 999px;
  font-weight: 600;
  font-size: 11px;
}

.judge-status--scored {
  background: #dcfce7;
  color: #166534;
}

.judge-status--pending {
  background: #fef3c7;
  color: #92400e;
}

.judge-secondary {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid #e7d9d2;
  color: #7c2d12;
  font-weight: 600;
  background: #fff8f4;
  margin-top: auto;
}

@media (max-width: 768px) {
  .judge-hero {
    flex-direction: column;
    align-items: flex-start;
  }

  .judge-hero__actions {
    width: 100%;
  }

  .judge-primary,
  .judge-select {
    width: 100%;
  }
}

@media (prefers-reduced-motion: reduce) {
  .judge-hero,
  .judge-card,
  .judge-submission-card {
    animation: none;
  }

  .judge-submission-card {
    transition: none;
  }
}
</style>
