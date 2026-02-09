<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b">
      <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold text-gray-900">
          Judge Dashboard
        </h1>
        <p class="text-gray-600">
          Welcome back, {{ user?.name }}
        </p>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 mb-2">
            Assigned Competitions
          </p>
          <p class="text-3xl font-bold text-burgundy">
            {{ stats.total_competitions || 0 }}
          </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 mb-2">
            Total Submissions
          </p>
          <p class="text-3xl font-bold text-blue-600">
            {{ stats.total_submissions || 0 }}
          </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 mb-2">
            Scored
          </p>
          <p class="text-3xl font-bold text-green-600">
            {{ stats.scored || 0 }}
          </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 mb-2">
            Overall Progress
          </p>
          <p class="text-3xl font-bold text-burgundy">
            {{ overallProgress }}%
          </p>
        </div>
      </div>

      <!-- Active Competitions -->
      <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b">
          <h2 class="text-xl font-bold text-gray-900">
            My Judging Assignments
          </h2>
        </div>
        
        <div
          v-if="loading"
          class="p-12 text-center"
        >
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy mx-auto" />
          <p class="text-gray-600 mt-4">
            Loading assignments...
          </p>
        </div>

        <div
          v-else-if="competitions.length === 0"
          class="p-12 text-center text-gray-600"
        >
          <svg
            class="w-16 h-16 mx-auto text-gray-400 mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
          <p class="text-lg font-medium mb-2">
            No Judging Assignments
          </p>
          <p class="text-sm">
            You haven't been assigned to any competitions yet.
          </p>
        </div>

        <div
          v-else
          class="divide-y"
        >
          <div
            v-for="competition in competitions"
            :key="competition.id"
            class="p-6 hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-start justify-between gap-4">
              <!-- Competition Info -->
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-lg font-bold text-gray-900">
                    {{ competition.title }}
                  </h3>
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-medium',
                      getStatusClass(competition.status)
                    ]"
                  >
                    {{ capitalizeFirst(competition.status) }}
                  </span>
                </div>

                <p class="text-sm text-gray-600 mb-3">
                  {{ competition.description }}
                </p>

                <!-- Progress Bar -->
                <div class="mb-3">
                  <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Scoring Progress</span>
                    <span class="font-medium text-burgundy">
                      {{ competition.scored_count || 0 }}/{{ competition.total_submissions || 0 }} 
                      ({{ getProgressPercent(competition) }}%)
                    </span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-burgundy h-2 rounded-full transition-all"
                      :style="{ width: getProgressPercent(competition) + '%' }"
                    />
                  </div>
                </div>

                <!-- Deadlines -->
                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                  <div class="flex items-center gap-2">
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                      />
                    </svg>
                    <span>Submission Deadline: {{ formatDate(competition.submission_deadline) }}</span>
                  </div>
                  <div
                    v-if="competition.judging_deadline"
                    class="flex items-center gap-2"
                  >
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    <span class="font-medium">Judging Due: {{ formatDate(competition.judging_deadline) }}</span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex flex-col gap-2 min-w-[180px]">
                <router-link
                  :to="`/competitions/${competition.slug}/judge`"
                  class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors text-center font-medium"
                >
                  {{ competition.scored_count === 0 ? 'Start Scoring' : 'Continue Scoring' }}
                </router-link>
                <router-link
                  :to="`/competitions/${competition.slug}`"
                  class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center"
                >
                  View Competition
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">
          Quick Links
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
          <router-link
            to="/competitions"
            class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-red-50 transition-all group"
          >
            <svg
              class="w-8 h-8 text-gray-600 group-hover:text-burgundy mb-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
              />
            </svg>
            <span class="text-sm font-medium text-center">Browse Competitions</span>
          </router-link>

          <router-link
            to="/notifications"
            class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-red-50 transition-all group"
          >
            <svg
              class="w-8 h-8 text-gray-600 group-hover:text-burgundy mb-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
              />
            </svg>
            <span class="text-sm font-medium text-center">Notifications</span>
          </router-link>

          <router-link
            to="/help"
            class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-red-50 transition-all group"
          >
            <svg
              class="w-8 h-8 text-gray-600 group-hover:text-burgundy mb-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <span class="text-sm font-medium text-center">Judging Guidelines</span>
          </router-link>

          <button
            class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-red-50 transition-all group"
            @click="$router.push('/settings')"
          >
            <svg
              class="w-8 h-8 text-gray-600 group-hover:text-burgundy mb-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
            </svg>
            <span class="text-sm font-medium text-center">Settings</span>
          </button>

          <button
            class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-burgundy hover:bg-red-50 transition-all group"
            @click="$router.push('/')"
          >
            <svg
              class="w-8 h-8 text-gray-600 group-hover:text-burgundy mb-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
              />
            </svg>
            <span class="text-sm font-medium text-center">Home</span>
          </button>

          <button
            class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all group"
            @click="logout"
          >
            <svg
              class="w-8 h-8 text-gray-600 group-hover:text-red-600 mb-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
              />
            </svg>
            <span class="text-sm font-medium text-center">Logout</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { formatDate as formatDateValue } from '../utils/formatters';

const router = useRouter();
const user = ref(null);
const competitions = ref([]);
const stats = ref({
  total_competitions: 0,
  total_submissions: 0,
  scored: 0,
});
const loading = ref(true);

const overallProgress = computed(() => {
  if (stats.value.total_submissions === 0) return 0;
  return Math.round((stats.value.scored / stats.value.total_submissions) * 100);
});

onMounted(() => {
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    user.value = JSON.parse(storedUser);
  }
  fetchJudgingAssignments();
});

const fetchJudgingAssignments = async () => {
  loading.value = true;
  try {
    const { data } = await api.get('/judge/assignments');
    competitions.value = data.data.competitions || [];
    stats.value = data.data.stats || stats.value;
  } catch (error) {
    console.error('Error fetching judging assignments:', error);
  } finally {
    loading.value = false;
  }
};

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-blue-100 text-blue-800',
    judging: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const capitalizeFirst = (str) => {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return formatDateValue(dateString) || 'N/A';
};

const getProgressPercent = (competition) => {
  if (!competition.total_submissions || competition.total_submissions === 0) return 0;
  return Math.round((competition.scored_count / competition.total_submissions) * 100);
};

const logout = () => {
  localStorage.removeItem('user');
  localStorage.removeItem('token');
  router.push('/');
};
</script>
