<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <router-link 
          :to="{ name: 'judge.competition', params: { competitionId: competitionId } }"
          class="text-burgundy hover:text-burgundy-dark mb-4 inline-flex items-center"
        >
          ← Back to Submissions
        </router-link>
        <h1 class="text-3xl font-bold text-gray-900">Score Submission</h1>
      </div>

      <div v-if="loading" class="bg-white rounded-lg shadow p-6">
        <div class="space-y-4">
          <div class="h-32 bg-gray-200 rounded animate-pulse"></div>
          <div class="h-6 bg-gray-200 rounded animate-pulse"></div>
        </div>
      </div>

      <div v-else class="space-y-6">
        <!-- Photo Display -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <img 
            :src="submission.photo_url" 
            :alt="submission.title"
            class="w-full h-96 object-cover"
          />
          <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ submission.title }}</h2>
            <p class="text-gray-600 mt-2">{{ submission.description }}</p>
            <div class="mt-4 flex items-center gap-4 text-sm">
              <span class="text-gray-600">By: <strong>{{ submission.photographer.name }}</strong></span>
              <span class="text-gray-600">Camera: <strong>{{ submission.camera_make }} {{ submission.camera_model }}</strong></span>
              <span class="text-gray-600">ISO: <strong>{{ submission.iso }}</strong></span>
              <span class="text-gray-600">Shutter: <strong>{{ submission.shutter_speed }}</strong></span>
              <span class="text-gray-600">Aperture: <strong>{{ submission.aperture }}</strong></span>
            </div>
          </div>
        </div>

        <!-- Scoring Form -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-6">Your Evaluation</h3>

          <!-- Deadline Warning -->
          <div v-if="isPastDeadline" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-medium">Scoring deadline has passed. You cannot modify this score.</p>
          </div>

          <!-- Score Form -->
          <form @submit.prevent="submitScores" :disabled="isPastDeadline || submitting">
            <div class="space-y-8">
              <!-- Composition Criterion -->
              <div>
                <div class="flex items-center justify-between mb-3">
                  <label class="text-lg font-semibold text-gray-900">Composition</label>
                  <span class="text-2xl font-bold text-burgundy">{{ scores.composition }}/10</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">Framing, balance, and visual arrangement</p>
                <input 
                  v-model.number="scores.composition"
                  type="range"
                  min="0"
                  max="10"
                  step="0.5"
                  class="w-full"
                  :disabled="isPastDeadline"
                />
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>Poor</span>
                  <span>Excellent</span>
                </div>
              </div>

              <!-- Technical Criterion -->
              <div>
                <div class="flex items-center justify-between mb-3">
                  <label class="text-lg font-semibold text-gray-900">Technical Quality</label>
                  <span class="text-2xl font-bold text-burgundy">{{ scores.technical }}/10</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">Focus, exposure, color, and clarity</p>
                <input 
                  v-model.number="scores.technical"
                  type="range"
                  min="0"
                  max="10"
                  step="0.5"
                  class="w-full"
                  :disabled="isPastDeadline"
                />
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>Poor</span>
                  <span>Excellent</span>
                </div>
              </div>

              <!-- Creativity Criterion -->
              <div>
                <div class="flex items-center justify-between mb-3">
                  <label class="text-lg font-semibold text-gray-900">Creativity</label>
                  <span class="text-2xl font-bold text-burgundy">{{ scores.creativity }}/10</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">Originality and artistic vision</p>
                <input 
                  v-model.number="scores.creativity"
                  type="range"
                  min="0"
                  max="10"
                  step="0.5"
                  class="w-full"
                  :disabled="isPastDeadline"
                />
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>Poor</span>
                  <span>Excellent</span>
                </div>
              </div>

              <!-- Story Criterion -->
              <div>
                <div class="flex items-center justify-between mb-3">
                  <label class="text-lg font-semibold text-gray-900">Story/Impact</label>
                  <span class="text-2xl font-bold text-burgundy">{{ scores.story }}/10</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">Emotional connection and narrative depth</p>
                <input 
                  v-model.number="scores.story"
                  type="range"
                  min="0"
                  max="10"
                  step="0.5"
                  class="w-full"
                  :disabled="isPastDeadline"
                />
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>Poor</span>
                  <span>Excellent</span>
                </div>
              </div>

              <!-- Overall Criterion -->
              <div>
                <div class="flex items-center justify-between mb-3">
                  <label class="text-lg font-semibold text-gray-900">Overall Impression</label>
                  <span class="text-2xl font-bold text-burgundy">{{ scores.overall }}/10</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">Final assessment and recommendation</p>
                <input 
                  v-model.number="scores.overall"
                  type="range"
                  min="0"
                  max="10"
                  step="0.5"
                  class="w-full"
                  :disabled="isPastDeadline"
                />
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>Poor</span>
                  <span>Excellent</span>
                </div>
              </div>

              <!-- Average Score -->
              <div class="p-4 bg-burgundy-light rounded-lg">
                <div class="text-center">
                  <p class="text-sm text-gray-600 mb-2">Average Score</p>
                  <p class="text-4xl font-bold text-burgundy">{{ averageScore.toFixed(1) }}/10</p>
                </div>
              </div>

              <!-- Feedback -->
              <div>
                <label class="block text-lg font-semibold text-gray-900 mb-2">Feedback (Optional)</label>
                <p class="text-sm text-gray-600 mb-3">Provide constructive feedback to the photographer</p>
                <textarea 
                  v-model="feedback"
                  placeholder="Share your thoughts on the submission..."
                  rows="5"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                  :disabled="isPastDeadline"
                ></textarea>
              </div>

              <!-- Submit Button -->
              <div class="flex gap-4 pt-6 border-t">
                <router-link 
                  :to="{ name: 'judge.competition', params: { competitionId: competitionId } }"
                  class="flex-1 inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                >
                  Cancel
                </router-link>
                <button 
                  type="submit"
                  :disabled="isPastDeadline || submitting"
                  class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                >
                  <span v-if="!submitting">{{ submission.final_score ? 'Update Score' : 'Submit Score' }}</span>
                  <span v-else>Submitting...</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Toast Message -->
      <div 
        v-if="message"
        :class="[
          'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transition',
          messageType === 'success' ? 'bg-green-600' : 'bg-red-600'
        ]"
      >
        {{ message }}
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

const competitionId = route.params.competitionId;
const submissionId = route.params.submissionId;

const submission = ref({});
const deadline = ref(null);
const loading = ref(true);
const submitting = ref(false);
const message = ref('');
const messageType = ref('');

const scores = ref({
  composition: 5,
  technical: 5,
  creativity: 5,
  story: 5,
  overall: 5
});

const feedback = ref('');

const isPastDeadline = computed(() => {
  if (!deadline.value) return false;
  return new Date() > new Date(deadline.value);
});

const averageScore = computed(() => {
  const sum = Object.values(scores.value).reduce((a, b) => a + b, 0);
  return sum / Object.values(scores.value).length;
});

const loadData = async () => {
  loading.value = true;
  try {
    const { data } = await api.get(`/api/v1/admin/competitions/${competitionId}/submissions/${submissionId}`);
    if (data.status === 'success') {
      submission.value = data.data;
      
      // Load existing scores if any
      if (data.data.judge_scores && data.data.judge_scores.length > 0) {
        const currentJudgeId = await getCurrentJudgeId();
        const userScores = data.data.judge_scores.find(s => s.judge_id === currentJudgeId);
        if (userScores) {
          scores.value = {
            composition: userScores.composition,
            technical: userScores.technical,
            creativity: userScores.creativity,
            story: userScores.story,
            overall: userScores.overall
          };
          feedback.value = userScores.feedback || '';
        }
      }
    }

    const { data: compData } = await api.get(`/api/v1/admin/competitions/${competitionId}`);
    if (compData.status === 'success') {
      deadline.value = compData.data.voting_end_date;
    }
  } catch (error) {
    console.error('Error loading data:', error);
    showMessage('Failed to load submission', 'error');
  } finally {
    loading.value = false;
  }
};

const getCurrentJudgeId = async () => {
  const { data } = await api.get('/api/v1/me');
  return data.data.id;
};

const submitScores = async () => {
  submitting.value = true;
  try {
    const payload = {
      composition_score: scores.value.composition,
      technical_score: scores.value.technical,
      creativity_score: scores.value.creativity,
      story_score: scores.value.story,
      impact_score: scores.value.overall,
      feedback: feedback.value
    };

    const { data } = await api.post(
      `/api/v1/competitions/${competitionId}/submissions/${submissionId}/score`,
      payload
    );

    if (data.status === 'success') {
      showMessage('Score submitted successfully', 'success');
      setTimeout(() => {
        router.push({ name: 'judge.competition', params: { competitionId } });
      }, 2000);
    }
  } catch (error) {
    console.error('Error submitting scores:', error);
    showMessage('Failed to submit score. Please try again.', 'error');
  } finally {
    submitting.value = false;
  }
};

const showMessage = (msg, type) => {
  message.value = msg;
  messageType.value = type;
  setTimeout(() => {
    message.value = '';
  }, 4000);
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

.bg-burgundy-light {
  @apply bg-[#F5E6E6];
}

input[type="range"] {
  @apply accent-burgundy;
}

form:disabled input,
form:disabled textarea {
  @apply opacity-50 cursor-not-allowed;
}
</style>
