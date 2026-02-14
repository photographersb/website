<template>
  <div class="min-h-screen judge-shell py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <section class="judge-hero">
        <div>
          <router-link 
            :to="{ name: 'judge.competition', params: { competitionId: competitionId } }"
            class="judge-back"
          >
            ← Back to Submissions
          </router-link>
          <p class="judge-kicker">SUBMISSION REVIEW</p>
          <h1 class="judge-title">
            Score Submission
          </h1>
          <p class="judge-subtitle">
            {{ currentIndex >= 0 ? currentIndex + 1 : 0 }} of {{ totalSubmissions }}
          </p>
        </div>
        <div class="judge-hero__actions">
          <div class="judge-hero__row">
            <button
              type="button"
              class="judge-ghost"
              :disabled="!hasPrev"
              @click="goToAdjacent(-1)"
            >
              Prev
            </button>
            <button
              type="button"
              class="judge-ghost"
              :disabled="!hasNext"
              @click="goToAdjacent(1)"
            >
              Next
            </button>
          </div>
          <button
            type="button"
            class="judge-primary"
            :disabled="!hasNextUnscored"
            @click="goToNextUnscored"
          >
            Next Unscored
          </button>
          <span class="judge-shortcut">Shortcuts: ← →, N</span>
        </div>
      </section>

      <div
        v-if="loading"
        class="judge-card p-6"
      >
        <div class="space-y-4">
          <div class="h-32 bg-gray-200 rounded animate-pulse" />
          <div class="h-6 bg-gray-200 rounded animate-pulse" />
        </div>
      </div>

      <div
        v-else
        class="space-y-6"
      >
        <div class="judge-card judge-summary">
          <div class="judge-summary__item">
            <p class="judge-summary__label">Status</p>
            <span
              class="judge-status-chip"
              :class="hasExistingScore ? 'judge-status-chip--scored' : 'judge-status-chip--pending'"
            >
              {{ hasExistingScore ? 'Scored' : 'Pending' }}
            </span>
          </div>
          <div class="judge-summary__item">
            <p class="judge-summary__label">Pending</p>
            <p class="judge-summary__value">{{ pendingCount }}</p>
          </div>
          <div class="judge-summary__item">
            <p class="judge-summary__label">Deadline</p>
            <p class="judge-summary__value">{{ deadlineLabel }}</p>
          </div>
        </div>

        <div class="score-stage">
          <!-- Photo Display -->
          <div class="judge-card judge-photo overflow-hidden">
            <div class="relative">
              <img 
                :src="submission.image_url || submission.photo_url" 
                :alt="submission.title"
                class="w-full h-[28rem] lg:h-[34rem] object-cover cursor-zoom-in"
                @click="showLightbox = true"
              >
              <button
                type="button"
                class="absolute bottom-4 right-4 px-3 py-1 text-xs font-semibold text-white bg-black/70 rounded-full"
                @click="showLightbox = true"
              >
                View Full
              </button>
            </div>
            <div class="p-6">
              <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                <span class="px-2 py-1 bg-gray-100 rounded-full">{{ submission.photographer?.name || 'Unknown' }}</span>
                <span class="px-2 py-1 bg-gray-100 rounded-full">{{ cameraLabel }}</span>
                <span class="px-2 py-1 bg-gray-100 rounded-full">ISO {{ isoValue }}</span>
                <span class="px-2 py-1 bg-gray-100 rounded-full">Shutter {{ shutterValue }}</span>
                <span class="px-2 py-1 bg-gray-100 rounded-full">Aperture {{ apertureValue }}</span>
              </div>
              <h2 class="text-2xl font-bold text-gray-900 mt-4">
                {{ submission.title }}
              </h2>
              <p class="text-gray-600 mt-2">
                {{ submission.description }}
              </p>
            </div>
          </div>

          <!-- Scoring Form -->
          <div class="judge-card p-6 score-overlay">
          <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <h3 class="text-xl font-bold text-gray-900">
              Your Evaluation
            </h3>
            <div class="px-4 py-2 bg-burgundy-light rounded-lg">
              <span class="text-xs text-gray-600">Average</span>
              <span class="ml-2 text-lg font-bold text-burgundy">{{ averageScore.toFixed(1) }}/10</span>
            </div>
          </div>

          <!-- Deadline Warning -->
          <div
            v-if="isPastDeadline"
            class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg"
          >
            <p class="text-red-800 font-medium">
              Scoring deadline has passed. You cannot modify this score.
            </p>
          </div>

          <!-- Score Form -->
          <form
            :disabled="isPastDeadline || submitting"
            @submit.prevent="submitScores"
          >
            <div class="space-y-4">
              <div class="score-section">
                <button
                  type="button"
                  class="score-toggle"
                  @click="toggleSection('composition')"
                >
                  <span>Composition</span>
                  <span class="text-burgundy font-semibold">{{ scores.composition }}/10</span>
                </button>
                <div
                  v-show="openSection === 'composition'"
                  class="score-body"
                >
                  <p class="text-sm text-gray-600 mb-4">
                    Framing, balance, and visual arrangement
                  </p>
                  <input 
                    v-model.number="scores.composition"
                    type="range"
                    min="0"
                    max="10"
                    step="0.5"
                    class="w-full"
                    :disabled="isPastDeadline"
                    @change="handleScoreChange('composition')"
                  >
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Poor</span>
                    <span>Excellent</span>
                  </div>
                </div>
              </div>

              <div class="score-section">
                <button
                  type="button"
                  class="score-toggle"
                  @click="toggleSection('technical')"
                >
                  <span>Technical Quality</span>
                  <span class="text-burgundy font-semibold">{{ scores.technical }}/10</span>
                </button>
                <div
                  v-show="openSection === 'technical'"
                  class="score-body"
                >
                  <p class="text-sm text-gray-600 mb-4">
                    Focus, exposure, color, and clarity
                  </p>
                  <input 
                    v-model.number="scores.technical"
                    type="range"
                    min="0"
                    max="10"
                    step="0.5"
                    class="w-full"
                    :disabled="isPastDeadline"
                    @change="handleScoreChange('technical')"
                  >
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Poor</span>
                    <span>Excellent</span>
                  </div>
                </div>
              </div>

              <div class="score-section">
                <button
                  type="button"
                  class="score-toggle"
                  @click="toggleSection('creativity')"
                >
                  <span>Creativity</span>
                  <span class="text-burgundy font-semibold">{{ scores.creativity }}/10</span>
                </button>
                <div
                  v-show="openSection === 'creativity'"
                  class="score-body"
                >
                  <p class="text-sm text-gray-600 mb-4">
                    Originality and artistic vision
                  </p>
                  <input 
                    v-model.number="scores.creativity"
                    type="range"
                    min="0"
                    max="10"
                    step="0.5"
                    class="w-full"
                    :disabled="isPastDeadline"
                    @change="handleScoreChange('creativity')"
                  >
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Poor</span>
                    <span>Excellent</span>
                  </div>
                </div>
              </div>

              <div class="score-section">
                <button
                  type="button"
                  class="score-toggle"
                  @click="toggleSection('story')"
                >
                  <span>Story/Impact</span>
                  <span class="text-burgundy font-semibold">{{ scores.story }}/10</span>
                </button>
                <div
                  v-show="openSection === 'story'"
                  class="score-body"
                >
                  <p class="text-sm text-gray-600 mb-4">
                    Emotional connection and narrative depth
                  </p>
                  <input 
                    v-model.number="scores.story"
                    type="range"
                    min="0"
                    max="10"
                    step="0.5"
                    class="w-full"
                    :disabled="isPastDeadline"
                    @change="handleScoreChange('story')"
                  >
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Poor</span>
                    <span>Excellent</span>
                  </div>
                </div>
              </div>

              <div class="score-section">
                <button
                  type="button"
                  class="score-toggle"
                  @click="toggleSection('overall')"
                >
                  <span>Overall Impression</span>
                  <span class="text-burgundy font-semibold">{{ scores.overall }}/10</span>
                </button>
                <div
                  v-show="openSection === 'overall'"
                  class="score-body"
                >
                  <p class="text-sm text-gray-600 mb-4">
                    Final assessment and recommendation
                  </p>
                  <input 
                    v-model.number="scores.overall"
                    type="range"
                    min="0"
                    max="10"
                    step="0.5"
                    class="w-full"
                    :disabled="isPastDeadline"
                    @change="handleScoreChange('overall')"
                  >
                  <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>Poor</span>
                    <span>Excellent</span>
                  </div>
                </div>
              </div>

              <div class="score-section">
                <button
                  type="button"
                  class="score-toggle"
                  @click="toggleSection('feedback')"
                >
                  <span>Feedback (Optional)</span>
                  <span class="text-xs text-gray-500">Notes</span>
                </button>
                <div
                  v-show="openSection === 'feedback'"
                  class="score-body"
                >
                  <p class="text-sm text-gray-600 mb-3">
                    Provide constructive feedback to the photographer
                  </p>
                  <textarea 
                    v-model="feedback"
                    placeholder="Share your thoughts on the submission..."
                    rows="5"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                    :disabled="isPastDeadline"
                    @focus="openSection = 'feedback'"
                  />
                </div>
              </div>

              <!-- Submit Button -->
              <div class="flex gap-4 pt-6 border-t">
                <router-link 
                  :to="{ name: 'judge.competition', params: { competitionId: competitionId } }"
                  class="flex-1 judge-secondary"
                >
                  Cancel
                </router-link>
                <button 
                  type="submit"
                  :disabled="isPastDeadline || submitting"
                  class="flex-1 judge-primary"
                >
                  <span v-if="!submitting">{{ hasExistingScore ? 'Update Score' : 'Submit Score' }}</span>
                  <span v-else>Submitting...</span>
                </button>
              </div>
            </div>
          </form>
          </div>
        </div>
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

    <div
      v-if="showLightbox"
      class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-6"
      @click="showLightbox = false"
    >
      <button
        class="absolute top-6 right-6 text-white text-3xl"
        @click="showLightbox = false"
      >
        &times;
      </button>
      <button
        v-if="hasPrev"
        class="absolute left-6 top-1/2 -translate-y-1/2 text-white text-4xl px-3 py-2 rounded-full bg-white hover:bg-white bg-opacity-10 hover:bg-opacity-20"
        @click.stop="goToAdjacent(-1)"
      >
        ‹
      </button>
      <button
        v-if="hasNext"
        class="absolute right-6 top-1/2 -translate-y-1/2 text-white text-4xl px-3 py-2 rounded-full bg-white hover:bg-white bg-opacity-10 hover:bg-opacity-20"
        @click.stop="goToAdjacent(1)"
      >
        ›
      </button>
      <div
        v-if="totalSubmissions > 0 && currentIndex >= 0"
        class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white text-sm bg-white/10 px-3 py-1 rounded-full"
      >
        {{ currentIndex + 1 }} / {{ totalSubmissions }}
      </div>
      <img
        :src="submission.image_url || submission.photo_url"
        :alt="submission.title"
        class="max-h-[90vh] max-w-[90vw] object-contain"
        @click.stop
      >
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../../api';

const router = useRouter();
const route = useRoute();

const competitionId = computed(() => route.params.competitionId);
const submissionId = computed(() => route.params.submissionId);

const submission = ref({});
const deadline = ref(null);
const showLightbox = ref(false);
const loading = ref(true);
const submitting = ref(false);
const message = ref('');
const messageType = ref('');
const submissionsList = ref([]);
const currentIndex = ref(-1);
const competitionInfo = ref({});

const scores = ref({
  composition: 5,
  technical: 5,
  creativity: 5,
  story: 5,
  overall: 5
});

const feedback = ref('');
const openSection = ref('composition');
const sectionOrder = ['composition', 'technical', 'creativity', 'story', 'overall', 'feedback'];

const isPastDeadline = computed(() => {
  if (!deadline.value) return false;
  return new Date() > new Date(deadline.value);
});

const averageScore = computed(() => {
  const sum = Object.values(scores.value).reduce((a, b) => a + b, 0);
  return sum / Object.values(scores.value).length;
});

const scoredCount = computed(() => submissionsList.value.filter(isSubmissionScored).length);
const pendingCount = computed(() => Math.max(totalSubmissions.value - scoredCount.value, 0));

const deadlineLabel = computed(() => {
  if (!deadline.value) return 'No deadline';
  try {
    return new Intl.DateTimeFormat('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    }).format(new Date(deadline.value));
  } catch (error) {
    return deadline.value;
  }
});

const cameraSettings = computed(() => {
  const raw = submission.value?.camera_settings;
  const exif = submission.value?.files?.[0]?.exif_json || null;
  return normalizeCameraSettings(raw, exif);
});

const cameraLabel = computed(() => {
  const make = submission.value?.camera_make || '';
  const model = submission.value?.camera_model || '';
  const label = `${make} ${model}`.trim();
  return label || 'Camera Unknown';
});

const isoValue = computed(() => formatIsoValue(cameraSettings.value.iso || submission.value?.iso));
const shutterValue = computed(() => formatShutterValue(cameraSettings.value.shutter_speed || submission.value?.shutter_speed));
const apertureValue = computed(() => formatApertureValue(cameraSettings.value.aperture || submission.value?.aperture));

function normalizeCameraSettings(raw, exif) {
  const parsed = parseCameraSettingsRaw(raw);
  if (Object.keys(parsed).length) {
    return parsed;
  }
  return parseExifSettings(exif);
}

function parseCameraSettingsRaw(raw) {
  if (!raw) return {};
  if (typeof raw === 'object') {
    if (typeof raw.raw === 'string' && raw.raw.trim().length > 0) {
      return parseSettingsString(raw.raw);
    }
    return raw;
  }

  if (typeof raw === 'string') {
    try {
      const parsed = JSON.parse(raw);
      if (parsed && typeof parsed === 'object') {
        if (typeof parsed.raw === 'string' && parsed.raw.trim().length > 0) {
          return parseSettingsString(parsed.raw);
        }
        return parsed;
      }
    } catch (error) {
      return parseSettingsString(raw);
    }
  }

  return {};
}

function parseSettingsString(value) {
  const parts = String(value)
    .split(',')
    .map((part) => part.trim())
    .filter(Boolean);

  const settings = {};

  parts.forEach((part) => {
    const lower = part.toLowerCase();
    if (lower.includes('iso')) {
      const match = part.match(/iso\s*([\d.]+)/i);
      settings.iso = match ? match[1] : part.replace(/iso\s*/i, '').trim();
      return;
    }
    if (lower.startsWith('f/') || lower.includes('aperture')) {
      settings.aperture = part.replace(/aperture\s*/i, '').trim();
      return;
    }
    if (lower.includes('sec') || lower.includes('s') || part.includes('/')) {
      settings.shutter_speed = part.replace(/shutter\s*/i, '').trim();
      return;
    }
  });

  return settings;
}

function parseExifSettings(exif) {
  if (!exif || typeof exif !== 'object') return {};

  const iso = exif.iso ?? exif.ISO ?? exif.ISOSpeedRatings ?? exif.EXIF?.ISOSpeedRatings;
  const aperture = exif.aperture ?? exif.FNumber ?? exif.EXIF?.FNumber ?? exif.ApertureValue ?? exif.EXIF?.ApertureValue;
  const shutter = exif.shutter_speed ?? exif.ExposureTime ?? exif.EXIF?.ExposureTime;

  return {
    iso: iso ?? undefined,
    aperture: aperture ?? undefined,
    shutter_speed: shutter ?? undefined,
  };
}

function formatIsoValue(value) {
  if (!value) return '—';
  const match = String(value).match(/(\d+(?:\.\d+)?)/);
  return match ? match[1] : String(value).replace(/iso\s*/i, '').trim();
}

function formatShutterValue(value) {
  if (!value) return '—';
  const text = String(value).trim();
  if (text.includes('s')) return text;
  if (text.includes('/')) return `${text}s`;
  return `${text}s`;
}

function formatApertureValue(value) {
  if (!value) return '—';
  const text = String(value).trim();
  if (text.toLowerCase().startsWith('f/')) return text;
  return `f/${text}`;
}

const hasExistingScore = computed(() => Array.isArray(submission.value?.scores) && submission.value.scores.length > 0);
const totalSubmissions = computed(() => submissionsList.value.length);
const hasPrev = computed(() => currentIndex.value > 0);
const hasNext = computed(() => currentIndex.value >= 0 && currentIndex.value < submissionsList.value.length - 1);
const hasNextUnscored = computed(() => Boolean(nextUnscoredSubmission.value));

const toggleSection = (sectionKey) => {
  openSection.value = openSection.value === sectionKey ? '' : sectionKey;
};

const openNextSection = (sectionKey) => {
  const index = sectionOrder.indexOf(sectionKey);
  const next = sectionOrder[index + 1];
  if (next) {
    openSection.value = next;
  }
};

const handleScoreChange = (sectionKey) => {
  if (isPastDeadline.value) return;
  openNextSection(sectionKey);
};

const updateCurrentIndex = () => {
  const id = String(submissionId.value || '');
  currentIndex.value = submissionsList.value.findIndex((item) => String(item.id) === id);
};

const isSubmissionScored = (item) => {
  const score = Array.isArray(item?.scores) ? item.scores[0] : null;
  return Boolean(score && score.status === 'completed');
};

const nextUnscoredSubmission = computed(() => {
  if (!submissionsList.value.length) return null;
  const start = currentIndex.value >= 0 ? currentIndex.value + 1 : 0;
  for (let i = start; i < submissionsList.value.length; i += 1) {
    if (!isSubmissionScored(submissionsList.value[i])) return submissionsList.value[i];
  }
  for (let i = 0; i < start; i += 1) {
    if (!isSubmissionScored(submissionsList.value[i])) return submissionsList.value[i];
  }
  return null;
});

const loadSubmissionsList = async () => {
  try {
    const { data } = await api.get(`/judge/competitions/${competitionId.value}/submissions`);
    if (data.status === 'success') {
      const payload = data.data || {};
      const paged = payload.submissions || {};
      const items = Array.isArray(paged.data) ? paged.data : Array.isArray(paged) ? paged : [];
      submissionsList.value = items;
      updateCurrentIndex();
    }
  } catch (error) {
    console.error('Error loading submissions list:', error);
  }
};

const goToAdjacent = (offset) => {
  if (!submissionsList.value.length || currentIndex.value < 0) return;
  const target = submissionsList.value[currentIndex.value + offset];
  if (!target) return;
  router.push({
    name: 'judge.score-submission',
    params: { competitionId: competitionId.value, submissionId: target.id }
  });
};

const goToNextUnscored = () => {
  if (!nextUnscoredSubmission.value) return;
  router.push({
    name: 'judge.score-submission',
    params: { competitionId: competitionId.value, submissionId: nextUnscoredSubmission.value.id }
  });
};

const handleKeydown = (event) => {
  const target = event.target;
  const tagName = target?.tagName || '';
  if (tagName === 'INPUT' || tagName === 'TEXTAREA') return;
  if (event.key === 'ArrowLeft') {
    event.preventDefault();
    goToAdjacent(-1);
  }
  if (event.key === 'ArrowRight') {
    event.preventDefault();
    goToAdjacent(1);
  }
  if (event.key?.toLowerCase() === 'n') {
    event.preventDefault();
    goToNextUnscored();
  }
};

const loadData = async () => {
  loading.value = true;
  try {
    const { data } = await api.get(`/judge/competitions/${competitionId.value}/submissions/${submissionId.value}`);
    if (data.status === 'success') {
      submission.value = data.data?.submission || {};
      competitionInfo.value = data.data?.competition || {};
      deadline.value = competitionInfo.value?.judging_end_at || null;

      const score = Array.isArray(submission.value.scores) ? submission.value.scores[0] : null;
      if (score) {
        scores.value = {
          composition: Number(score.composition_score) || 0,
          technical: Number(score.technical_score) || 0,
          creativity: Number(score.creativity_score) || 0,
          story: Number(score.story_score) || 0,
          overall: Number(score.impact_score) || 0,
        };
        feedback.value = score.feedback || '';
      }
    }
  } catch (error) {
    console.error('Error loading data:', error);
    showMessage('Failed to load submission', 'error');
  } finally {
    loading.value = false;
  }
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
      `/judge/competitions/${competitionId.value}/submissions/${submissionId.value}/score`,
      payload
    );

    if (data.status === 'success') {
      showMessage('Score submitted successfully', 'success');
      setTimeout(() => {
        router.push({ name: 'judge.competition', params: { competitionId: competitionId.value } });
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
  loadSubmissionsList();
  loadData();
  window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown);
});

watch(() => submissionId.value, () => {
  loadData();
  updateCurrentIndex();
});

watch(() => competitionId.value, () => {
  loadSubmissionsList();
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

.score-stage {
  position: relative;
  padding-right: 420px;
}

.score-overlay {
  position: absolute;
  top: 0;
  right: 0;
  width: 380px;
  max-height: calc(100% - 8px);
  overflow: auto;
  border: 1px solid #efe7e3;
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
  margin-top: 4px;
}

.judge-hero__actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.judge-hero__row {
  display: flex;
  gap: 8px;
}

.judge-shortcut {
  font-size: 12px;
  color: #6b7280;
}

.judge-primary {
  background: linear-gradient(135deg, #7c2d12 0%, #9a3412 100%);
  color: #ffffff;
  padding: 10px 18px;
  border-radius: 999px;
  font-weight: 600;
  font-size: 13px;
  box-shadow: 0 10px 18px rgba(124, 45, 18, 0.22);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.judge-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.judge-primary:focus-visible,
.judge-secondary:focus-visible,
.judge-ghost:focus-visible,
.judge-back:focus-visible {
  outline: 2px solid #c2410c;
  outline-offset: 2px;
}

.judge-secondary {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  padding: 10px 18px;
  border-radius: 999px;
  border: 1px solid #e7d9d2;
  color: #7c2d12;
  font-weight: 600;
  background: #fff8f4;
}

.judge-ghost {
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid #e7d9d2;
  color: #7c2d12;
  font-weight: 600;
  font-size: 13px;
  background: #ffffff;
}

.judge-ghost:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.judge-card {
  background: #ffffff;
  border-radius: 18px;
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
  animation: judgeFadeIn 0.7s ease both;
}

.judge-summary {
  margin-bottom: 24px;
  padding: 18px 22px;
  display: grid;
  gap: 18px;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  align-items: center;
  animation: judgeFadeIn 0.7s ease both;
}

.judge-summary__item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.judge-summary__label {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #6b4b3e;
  font-weight: 600;
}

.judge-summary__value {
  font-size: 18px;
  font-weight: 700;
  color: #1f2937;
}

.judge-status-chip {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  width: fit-content;
}

.judge-status-chip--scored {
  background: #dcfce7;
  color: #166534;
}

.judge-status-chip--pending {
  background: #fef3c7;
  color: #92400e;
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

.judge-photo img {
  filter: saturate(1.05) contrast(1.03);
}

.score-section {
  border: 1px solid #efe7e3;
  border-radius: 12px;
  padding: 12px 16px;
  background: #ffffff;
}

.score-toggle {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.score-toggle__meta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.score-toggle__chevron {
  color: #6b7280;
  font-size: 12px;
}

.score-body {
  margin-top: 12px;
}

@media (max-width: 1024px) {
  .score-stage {
    padding-right: 0;
  }

  .score-overlay {
    position: static;
    width: auto;
    margin-top: 24px;
  }

  .judge-hero {
    flex-direction: column;
    align-items: flex-start;
  }

  .judge-hero__actions {
    width: 100%;
  }

  .judge-primary,
  .judge-secondary {
    width: 100%;
  }


@media (prefers-reduced-motion: reduce) {
  .judge-hero,
  .judge-card,
  .judge-summary {
    animation: none;
  }
}
  .judge-summary {
    grid-template-columns: 1fr;
  }
}
</style>
