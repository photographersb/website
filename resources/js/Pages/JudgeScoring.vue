<template>
  <div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Judge Scoring Dashboard</h1>
        <p class="text-gray-600">{{ competition?.title }}</p>
      </div>

      <!-- Progress Card -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900">Your Progress</h2>
          <span class="text-3xl font-bold text-red-600">{{ progress.progress_percentage }}%</span>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
          <div 
            class="bg-red-600 h-4 rounded-full transition-all duration-500"
            :style="{ width: progress.progress_percentage + '%' }"
          ></div>
        </div>
        
        <div class="grid grid-cols-3 gap-4 text-center">
          <div>
            <p class="text-2xl font-bold text-gray-900">{{ progress.total }}</p>
            <p class="text-sm text-gray-600">Total Submissions</p>
          </div>
          <div>
            <p class="text-2xl font-bold text-green-600">{{ progress.scored }}</p>
            <p class="text-sm text-gray-600">Scored</p>
          </div>
          <div>
            <p class="text-2xl font-bold text-yellow-600">{{ progress.pending }}</p>
            <p class="text-sm text-gray-600">Pending</p>
          </div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex gap-4">
          <button 
            @click="filter = 'all'"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-colors',
              filter === 'all' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            All ({{ submissions.length }})
          </button>
          <button 
            @click="filter = 'pending'"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-colors',
              filter === 'pending' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Pending ({{ pendingCount }})
          </button>
          <button 
            @click="filter = 'scored'"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-colors',
              filter === 'scored' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Scored ({{ scoredCount }})
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
        <p class="mt-4 text-gray-600">Loading submissions...</p>
      </div>

      <!-- Submissions Grid -->
      <div v-else-if="filteredSubmissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="submission in filteredSubmissions" 
          :key="submission.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow cursor-pointer"
          @click="openScoringModal(submission)"
        >
          <!-- Image -->
          <div class="aspect-square bg-gray-200">
            <img 
              :src="submission.thumbnail_url || submission.image_url" 
              :alt="submission.title"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Info -->
          <div class="p-4">
            <div class="flex items-start justify-between mb-2">
              <h3 class="font-bold text-gray-900 flex-1 line-clamp-1">{{ submission.title }}</h3>
              <span 
                v-if="submission.is_scored"
                class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded"
              >
                ✓ Scored
              </span>
              <span 
                v-else
                class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded"
              >
                Pending
              </span>
            </div>
            <p class="text-sm text-gray-600 mb-2">by {{ submission.photographer?.name }}</p>
            
            <!-- Current Score (if scored) -->
            <div v-if="submission.is_scored && submission.my_score" class="mt-3 pt-3 border-t">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Your Score:</span>
                <span class="text-xl font-bold text-red-600">{{ submission.my_score.total_score }}/50</span>
              </div>
            </div>
            
            <!-- Call to Action -->
            <button 
              class="mt-3 w-full py-2 rounded-lg font-medium transition-colors"
              :class="submission.is_scored 
                ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' 
                : 'bg-red-600 text-white hover:bg-red-700'"
            >
              {{ submission.is_scored ? 'Review Score' : 'Score Now' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-white rounded-lg shadow">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No submissions found</h3>
        <p class="text-gray-600">There are no submissions matching your filter.</p>
      </div>
    </div>

    <!-- Scoring Modal -->
    <div v-if="showScoringModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
      <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between z-10">
          <h2 class="text-2xl font-bold text-gray-900">Score Submission</h2>
          <button @click="closeScoringModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Image & Details -->
            <div>
              <img 
                :src="selectedSubmission?.image_url" 
                :alt="selectedSubmission?.title"
                class="w-full rounded-lg mb-4"
              />
              <h3 class="text-xl font-bold text-gray-900 mb-2">{{ selectedSubmission?.title }}</h3>
              <p class="text-gray-600 mb-4">by {{ selectedSubmission?.photographer?.name }}</p>
              <p v-if="selectedSubmission?.description" class="text-gray-700 mb-4">{{ selectedSubmission.description }}</p>
              
              <!-- Metadata -->
              <div class="space-y-2 text-sm text-gray-600">
                <p v-if="selectedSubmission?.location"><strong>Location:</strong> {{ selectedSubmission.location }}</p>
                <p v-if="selectedSubmission?.camera_make"><strong>Camera:</strong> {{ selectedSubmission.camera_make }} {{ selectedSubmission.camera_model }}</p>
                <p v-if="selectedSubmission?.camera_settings"><strong>Settings:</strong> {{ selectedSubmission.camera_settings }}</p>
              </div>
            </div>

            <!-- Right: Scoring Form -->
            <div>
              <form @submit.prevent="submitScore">
                <!-- Scoring Criteria -->
                <div class="space-y-6">
                  <!-- Composition -->
                  <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                      Composition (0-10)
                      <span class="float-right text-2xl text-red-600">{{ scoreForm.composition_score }}</span>
                    </label>
                    <input 
                      v-model.number="scoreForm.composition_score"
                      type="range" 
                      min="0" 
                      max="10" 
                      step="0.5"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    />
                    <p class="text-xs text-gray-600 mt-1">Rule of thirds, balance, framing</p>
                  </div>

                  <!-- Technical Quality -->
                  <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                      Technical Quality (0-10)
                      <span class="float-right text-2xl text-red-600">{{ scoreForm.technical_score }}</span>
                    </label>
                    <input 
                      v-model.number="scoreForm.technical_score"
                      type="range" 
                      min="0" 
                      max="10" 
                      step="0.5"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    />
                    <p class="text-xs text-gray-600 mt-1">Focus, exposure, lighting, post-processing</p>
                  </div>

                  <!-- Creativity -->
                  <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                      Creativity (0-10)
                      <span class="float-right text-2xl text-red-600">{{ scoreForm.creativity_score }}</span>
                    </label>
                    <input 
                      v-model.number="scoreForm.creativity_score"
                      type="range" 
                      min="0" 
                      max="10" 
                      step="0.5"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    />
                    <p class="text-xs text-gray-600 mt-1">Originality, unique perspective, innovation</p>
                  </div>

                  <!-- Story/Message -->
                  <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                      Story/Message (0-10)
                      <span class="float-right text-2xl text-red-600">{{ scoreForm.story_score }}</span>
                    </label>
                    <input 
                      v-model.number="scoreForm.story_score"
                      type="range" 
                      min="0" 
                      max="10" 
                      step="0.5"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    />
                    <p class="text-xs text-gray-600 mt-1">Narrative, emotion, context</p>
                  </div>

                  <!-- Impact -->
                  <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                      Impact (0-10)
                      <span class="float-right text-2xl text-red-600">{{ scoreForm.impact_score }}</span>
                    </label>
                    <input 
                      v-model.number="scoreForm.impact_score"
                      type="range" 
                      min="0" 
                      max="10" 
                      step="0.5"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    />
                    <p class="text-xs text-gray-600 mt-1">Visual impact, memorability, wow factor</p>
                  </div>
                </div>

                <!-- Total Score -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center justify-between">
                    <span class="text-lg font-bold text-gray-900">Total Score:</span>
                    <span class="text-3xl font-bold text-red-600">{{ totalScore }}/50</span>
                  </div>
                </div>

                <!-- Feedback -->
                <div class="mt-6">
                  <label class="block text-sm font-bold text-gray-900 mb-2">Feedback (Optional)</label>
                  <textarea 
                    v-model="scoreForm.feedback"
                    rows="3"
                    placeholder="Provide constructive feedback..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  ></textarea>
                </div>

                <!-- Strengths -->
                <div class="mt-4">
                  <label class="block text-sm font-bold text-gray-900 mb-2">Strengths (Optional)</label>
                  <textarea 
                    v-model="scoreForm.strengths"
                    rows="2"
                    placeholder="What worked well..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  ></textarea>
                </div>

                <!-- Areas for Improvement -->
                <div class="mt-4">
                  <label class="block text-sm font-bold text-gray-900 mb-2">Areas for Improvement (Optional)</label>
                  <textarea 
                    v-model="scoreForm.improvements"
                    rows="2"
                    placeholder="What could be improved..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  ></textarea>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex gap-3">
                  <button 
                    type="button"
                    @click="closeScoringModal"
                    class="flex-1 px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium"
                  >
                    Cancel
                  </button>
                  <button 
                    type="submit"
                    :disabled="submitting || totalScore === 0"
                    class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                  >
                    {{ submitting ? 'Submitting...' : 'Submit Score' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api';

const route = useRoute();

const competition = ref(null);
const submissions = ref([]);
const progress = ref({ total: 0, scored: 0, pending: 0, progress_percentage: 0 });
const loading = ref(true);
const filter = ref('all');

const showScoringModal = ref(false);
const selectedSubmission = ref(null);
const submitting = ref(false);

const scoreForm = ref({
  composition_score: 0,
  technical_score: 0,
  creativity_score: 0,
  story_score: 0,
  impact_score: 0,
  feedback: '',
  strengths: '',
  improvements: ''
});

const totalScore = computed(() => {
  return (
    scoreForm.value.composition_score +
    scoreForm.value.technical_score +
    scoreForm.value.creativity_score +
    scoreForm.value.story_score +
    scoreForm.value.impact_score
  ).toFixed(1);
});

const filteredSubmissions = computed(() => {
  if (filter.value === 'pending') {
    return submissions.value.filter(s => !s.is_scored);
  } else if (filter.value === 'scored') {
    return submissions.value.filter(s => s.is_scored);
  }
  return submissions.value;
});

const pendingCount = computed(() => submissions.value.filter(s => !s.is_scored).length);
const scoredCount = computed(() => submissions.value.filter(s => s.is_scored).length);

onMounted(async () => {
  await fetchCompetition();
  await fetchProgress();
  await fetchSubmissions();
});

const fetchCompetition = async () => {
  try {
    const { data } = await api.get(`/competitions/${route.params.slug}`);
    competition.value = data.data;
  } catch (error) {
    console.error('Error fetching competition:', error);
  }
};

const fetchProgress = async () => {
  try {
    const { data } = await api.get(`/competitions/${competition.value.id}/judge/progress`);
    progress.value = data.data;
  } catch (error) {
    console.error('Error fetching progress:', error);
  }
};

const fetchSubmissions = async () => {
  loading.value = true;
  try {
    const { data } = await api.get(`/competitions/${competition.value.id}/judge/submissions`);
    submissions.value = data.data;
  } catch (error) {
    console.error('Error fetching submissions:', error);
    alert(error.response?.data?.message || 'Failed to load submissions');
  } finally {
    loading.value = false;
  }
};

const openScoringModal = (submission) => {
  selectedSubmission.value = submission;
  
  // Load existing score if available
  if (submission.my_score) {
    scoreForm.value = {
      composition_score: submission.my_score.composition_score || 0,
      technical_score: submission.my_score.technical_score || 0,
      creativity_score: submission.my_score.creativity_score || 0,
      story_score: submission.my_score.story_score || 0,
      impact_score: submission.my_score.impact_score || 0,
      feedback: submission.my_score.feedback || '',
      strengths: submission.my_score.strengths || '',
      improvements: submission.my_score.improvements || ''
    };
  } else {
    scoreForm.value = {
      composition_score: 0,
      technical_score: 0,
      creativity_score: 0,
      story_score: 0,
      impact_score: 0,
      feedback: '',
      strengths: '',
      improvements: ''
    };
  }
  
  showScoringModal.value = true;
};

const closeScoringModal = () => {
  showScoringModal.value = false;
  selectedSubmission.value = null;
};

const submitScore = async () => {
  submitting.value = true;
  try {
    await api.post(
      `/competitions/${competition.value.id}/submissions/${selectedSubmission.value.id}/score`,
      scoreForm.value
    );
    
    alert('Score submitted successfully!');
    closeScoringModal();
    await fetchProgress();
    await fetchSubmissions();
  } catch (error) {
    console.error('Error submitting score:', error);
    alert(error.response?.data?.message || 'Failed to submit score');
  } finally {
    submitting.value = false;
  }
};
</script>
