<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      :title="competition.title || 'Competition Details'"
      subtitle="View competition information"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <!-- Loading State -->
      <div v-if="loading" class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-card p-8 text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy-600 mx-auto"></div>
          <p class="mt-4 text-gray-600">Loading competition details...</p>
        </div>
      </div>

      <!-- Competition Details -->
      <div v-else class="max-w-6xl mx-auto space-y-6">
        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow-card p-4 md:p-6">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
            <router-link 
              to="/admin/competitions" 
              class="w-full sm:w-auto inline-flex items-center justify-center sm:justify-start gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Back to Competitions
            </router-link>
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
              <router-link 
                :to="`/admin/competitions/${competition.id}/submissions`"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Submissions ({{ competition.submissions_count || 0 }})
              </router-link>
              <router-link 
                :to="`/admin/competitions/${competition.id}/edit`"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Competition
              </router-link>
              <button 
                @click="deleteCompetition"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4v2h16V7h-3z" />
                </svg>
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Competition Header -->
        <div class="bg-white rounded-lg shadow-card overflow-hidden">
          <div v-if="competition.hero_image" class="h-64 bg-gray-200">
            <img :src="competition.hero_image" :alt="competition.title" class="w-full h-full object-cover" />
          </div>
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ competition.title }}</h1>
                <p class="text-gray-600 mt-2">{{ competition.theme }}</p>
              </div>
              <span :class="['px-4 py-2 rounded-lg font-semibold text-sm', statusClass(competition.status)]">
                {{ competition.status }}
              </span>
            </div>
            <p v-if="competition.description" class="text-gray-700 mt-4">{{ competition.description }}</p>
          </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-6">
          <div class="md:col-span-2 bg-gradient-to-br from-burgundy via-red-700 to-red-800 rounded-2xl shadow-xl p-6 md:p-8 text-white">
            <div class="text-3xl md:text-4xl font-black mb-2">{{ formatNumber(Math.floor(competition.total_prize_pool)) }}</div>
            <div class="text-xs md:text-sm text-white/90 font-medium">Prize Pool</div>
          </div>
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 md:p-8 text-white">
            <div class="text-2xl md:text-3xl font-black mb-2">{{ competition.submissions_count || 0 }}</div>
            <div class="text-xs md:text-sm text-white/90 font-medium">Submissions</div>
          </div>
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 md:p-8 text-white">
            <div class="text-2xl md:text-3xl font-black mb-2">{{ formatNumber(competition.entry_fee) }}</div>
            <div class="text-xs md:text-sm text-white/90 font-medium">Entry Fee</div>
          </div>
        </div>

        <!-- Dates -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Important Dates</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="text-sm font-medium text-gray-600">Submission Opens</div>
              <div class="text-gray-900 mt-1">{{ formatDate(competition.submission_start_date) }}</div>
            </div>
            <div>
              <div class="text-sm font-medium text-gray-600">Submission Deadline</div>
              <div class="text-gray-900 mt-1">{{ formatDate(competition.submission_deadline) }}</div>
            </div>
            <div>
              <div class="text-sm font-medium text-gray-600">Voting Starts</div>
              <div class="text-gray-900 mt-1">{{ formatDate(competition.voting_start_date) }}</div>
            </div>
            <div>
              <div class="text-sm font-medium text-gray-600">Voting Ends</div>
              <div class="text-gray-900 mt-1">{{ formatDate(competition.voting_end_date) }}</div>
            </div>
          </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Eligibility & Rules -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Eligibility & Rules</h2>
            <div class="space-y-3 text-sm">
              <div class="flex items-center gap-2">
                <span class="text-gray-600 font-medium">Age Requirement:</span>
                <span class="text-gray-900">{{ competition.age_requirement || 'None' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-600 font-medium">Region:</span>
                <span class="text-gray-900">{{ competition.region_restriction || 'Global' }}</span>
              </div>
              <div v-if="competition.rules" class="mt-4">
                <div class="text-gray-600 font-medium mb-2">Rules:</div>
                <div class="text-gray-700 whitespace-pre-wrap">{{ competition.rules }}</div>
              </div>
            </div>
          </div>

          <!-- Judging Criteria -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Judging Information</h2>
            <div class="space-y-3 text-sm">
              <div class="flex items-center gap-2">
                <span class="text-gray-600 font-medium">Judging Type:</span>
                <span class="text-gray-900">{{ competition.judging_type || 'N/A' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-600 font-medium">Public Voting Weight:</span>
                <span class="text-gray-900">{{ competition.public_voting_weight || 0 }}%</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-gray-600 font-medium">Judge Score Weight:</span>
                <span class="text-gray-900">{{ competition.judge_score_weight || 0 }}%</span>
              </div>
              <div v-if="competition.judging_criteria" class="mt-4">
                <div class="text-gray-600 font-medium mb-2">Criteria:</div>
                <div class="text-gray-700 whitespace-pre-wrap">{{ competition.judging_criteria }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Metadata -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">System Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <div class="text-gray-600 font-medium">Competition ID</div>
              <div class="text-gray-900 mt-1">{{ competition.id }}</div>
            </div>
            <div>
              <div class="text-gray-600 font-medium">Slug</div>
              <div class="text-gray-900 mt-1">{{ competition.slug }}</div>
            </div>
            <div>
              <div class="text-gray-600 font-medium">Created</div>
              <div class="text-gray-900 mt-1">{{ formatDate(competition.created_at) }}</div>
            </div>
          </div>
        </div>

        <!-- Judges -->
        <div v-if="competition.judgeProfiles && competition.judgeProfiles.length > 0" class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Judges</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="judge in competition.judgeProfiles" :key="judge.id" class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-center gap-3">
                <img v-if="judge.profile_photo_url" :src="judge.profile_photo_url" :alt="judge.name" class="w-12 h-12 rounded-full object-cover" />
                <div v-else class="w-12 h-12 rounded-full bg-burgundy-100 flex items-center justify-center font-bold text-burgundy-600">
                  {{ judge.name.charAt(0) }}
                </div>
                <div>
                  <div class="font-semibold text-gray-900">{{ judge.name }}</div>
                  <div v-if="judge.expertise" class="text-xs text-burgundy-600">{{ judge.expertise }}</div>
                </div>
              </div>
              <p v-if="judge.bio" class="text-sm text-gray-600 mt-2">{{ judge.bio }}</p>
            </div>
          </div>
        </div>

        <!-- Sponsors -->
        <div v-if="competition.sponsors && competition.sponsors.length > 0" class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Sponsors</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="sponsor in competition.sponsors" :key="sponsor.id" class="border border-gray-200 rounded-lg p-4 flex items-start gap-4">
              <img v-if="sponsor.logo_url" :src="sponsor.logo_url" :alt="sponsor.name" class="w-16 h-16 object-contain" />
              <div v-else class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-600 font-bold">
                {{ sponsor.name.substring(0, 2).toUpperCase() }}
              </div>
              <div class="flex-1">
                <div class="font-semibold text-gray-900">{{ sponsor.name }}</div>
                <p v-if="sponsor.description" class="text-sm text-gray-600 mt-1">{{ sponsor.description }}</p>
                <a v-if="sponsor.website" :href="sponsor.website" target="_blank" rel="noopener" class="text-sm text-burgundy-600 hover:underline">
                  Visit Website →
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Mentors -->
        <div v-if="competition.mentors && competition.mentors.length > 0" class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Mentors</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="mentor in competition.mentors" :key="mentor.id" class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-center gap-3">
                <img v-if="mentor.profile_photo_url" :src="mentor.profile_photo_url" :alt="mentor.name" class="w-12 h-12 rounded-full object-cover" />
                <div v-else class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-600">
                  {{ mentor.name.charAt(0) }}
                </div>
                <div>
                  <div class="font-semibold text-gray-900">{{ mentor.name }}</div>
                  <div v-if="mentor.expertise" class="text-xs text-blue-600">{{ mentor.expertise }}</div>
                </div>
              </div>
              <p v-if="mentor.bio" class="text-sm text-gray-600 mt-2">{{ mentor.bio }}</p>
            </div>
          </div>
        </div>

        <!-- Terms & Conditions -->
        <div v-if="competition.terms_and_conditions" class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Terms & Conditions</h2>
          <div class="text-gray-700 whitespace-pre-wrap text-sm">{{ competition.terms_and_conditions }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AdminHeader from '../../../Components/AdminHeader.vue';
import AdminQuickNav from '../../../Components/AdminQuickNav.vue';

export default {
  name: 'AdminCompetitionShow',
  components: {
    AdminHeader,
    AdminQuickNav,
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const competition = ref({});
    const loading = ref(true);

    const fetchCompetition = async () => {
      try {
        loading.value = true;
        const token = localStorage.getItem('auth_token');
        const response = await axios.get(`/admin/competitions/${route.params.id}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          competition.value = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching competition:', error);
        // Redirect to competitions list if error
        router.push('/admin/competitions');
      } finally {
        loading.value = false;
      }
    };

    const formatNumber = (num) => {
      return new Intl.NumberFormat('en-US').format(num || 0);
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'Not set';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    const statusClass = (status) => {
      const classes = {
        draft: 'bg-gray-100 text-gray-800',
        upcoming: 'bg-blue-100 text-blue-800',
        active: 'bg-green-100 text-green-800',
        judging: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-purple-100 text-purple-800',
        cancelled: 'bg-red-100 text-red-800',
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    };

    const deleteCompetition = async () => {
      if (!confirm('Are you sure you want to delete this competition? This action cannot be undone.')) {
        return;
      }

      try {
        const token = localStorage.getItem('auth_token');
        const response = await axios.delete(`/admin/competitions/${route.params.id}`, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          alert('Competition deleted successfully');
          router.push('/admin/competitions');
        }
      } catch (error) {
        console.error('Error deleting competition:', error);
        alert('Failed to delete competition: ' + (error.response?.data?.message || error.message));
      }
    };

    onMounted(() => {
      fetchCompetition();
    });

    return {
      competition,
      loading,
      formatNumber,
      formatDate,
      statusClass,
      deleteCompetition,
    };
  },
};
</script>

<style scoped>
.shadow-card {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
</style>
