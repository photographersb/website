<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="closeToast"
    />
    <BaseModal
      v-if="isAdminRoute"
      :is-open="showConfirmModal"
      title="Confirm Winner Announcement"
      :is-loading="announcing"
      @close="showConfirmModal = false"
      @submit="confirmAnnounceWinners"
    >
      <p class="text-sm text-gray-600">
        Are you sure you want to announce winners? This action cannot be undone and will close the competition.
      </p>
    </BaseModal>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page Header -->
      <div class="mb-8">
        <router-link
          :to="backLink"
          class="text-red-600 hover:text-red-700 mb-4 inline-block"
        >
          ← {{ backLabel }}
        </router-link>
        <div
          v-if="isAdminRoute"
          class="inline-flex items-center gap-2 px-3 py-1.5 mb-4 rounded-full text-xs font-semibold bg-red-100 text-red-700"
        >
          Admin View
        </div>
        <div
          v-else
          class="inline-flex items-center gap-2 px-3 py-1.5 mb-4 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"
        >
          Public View
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 sm:p-8">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
              <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                {{ competition.title }}
              </h1>
              <p class="text-gray-600">
                Competition Winners & Results
              </p>
              <p
                v-if="competition.theme"
                class="text-sm text-gray-500 mt-2"
              >
                Theme: {{ competition.theme }}
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                {{ competition.status }}
              </span>
              <span
                v-if="competition.results_published"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700"
              >
                Results Published
              </span>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
            <div class="rounded-2xl border border-amber-100 bg-gradient-to-br from-white to-amber-50 px-4 py-4 shadow-sm">
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 text-amber-700">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4m6-13a4 4 0 11-8 0 4 4 0 018 0zm-3.5 5.5l-2.5 2-2.5-2" />
                  </svg>
                </span>
                <div>
                  <p class="text-xs uppercase tracking-wide text-amber-700 font-semibold">Winners</p>
                  <p class="text-2xl font-bold text-gray-900">{{ winnersTotal }}</p>
                </div>
              </div>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-gradient-to-br from-white to-rose-50 px-4 py-4 shadow-sm">
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-rose-100 text-rose-700">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                  </svg>
                </span>
                <div>
                  <p class="text-xs uppercase tracking-wide text-rose-700 font-semibold">Submissions</p>
                  <p class="text-2xl font-bold text-gray-900">{{ submissionsTotal }}</p>
                </div>
              </div>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-white to-emerald-50 px-4 py-4 shadow-sm">
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.314 0-6 1.79-6 4s2.686 4 6 4 6-1.79 6-4-2.686-4-6-4zm0 0V4m0 12v4" />
                  </svg>
                </span>
                <div>
                  <p class="text-xs uppercase tracking-wide text-emerald-700 font-semibold">Votes</p>
                  <p class="text-2xl font-bold text-gray-900">{{ votesTotal }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading"
        class="flex justify-center items-center py-20"
      >
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-red-600" />
      </div>

      <!-- Winners Section -->
      <div v-else>
        <!-- Admin Controls (if admin) -->
        <div
          v-if="isAdminRoute && competition.status !== 'closed'"
          class="bg-white rounded-lg shadow-md p-6 mb-8"
        >
          <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Admin Controls
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Vote Weight (%)</label>
              <input 
                v-model.number="config.voteWeight" 
                type="number" 
                min="0" 
                max="100" 
                step="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
              >
              <p class="text-xs text-gray-600 mt-1">
                Percentage weight for public votes (0-100)
              </p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Judge Weight (%)</label>
              <input 
                v-model.number="config.judgeWeight" 
                type="number" 
                min="0" 
                max="100" 
                step="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
              >
              <p class="text-xs text-gray-600 mt-1">
                Percentage weight for judge scores (0-100)
              </p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Number of Winners</label>
              <input 
                v-model.number="config.numberOfWinners" 
                type="number" 
                min="1" 
                max="10"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Honorable Mentions</label>
              <input 
                v-model.number="config.honorableMentions" 
                type="number" 
                min="0" 
                max="20"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
              >
            </div>
          </div>
          
          <div class="flex gap-4">
            <button 
              :disabled="calculating" 
              class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50"
              @click="previewWinners"
            >
              {{ calculating ? 'Calculating...' : 'Preview Winners' }}
            </button>
            
            <button 
              :disabled="!previewData || announcing" 
              class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
              @click="requestAnnounceWinners"
            >
              {{ announcing ? 'Announcing...' : 'Announce Winners (Final)' }}
            </button>
          </div>
          
          <!-- Preview Results -->
          <div
            v-if="previewData"
            class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg"
          >
            <p class="text-sm font-medium text-yellow-800 mb-2">
              Preview Results:
            </p>
            <ul class="text-sm text-yellow-700 space-y-1">
              <li>• Vote Weight: {{ (previewData.config.vote_weight * 100).toFixed(0) }}%</li>
              <li>• Judge Weight: {{ (previewData.config.judge_weight * 100).toFixed(0) }}%</li>
              <li>• Winners: {{ previewData.config.winners_count }}</li>
              <li>• Honorable Mentions: {{ previewData.config.honorable_mentions_count }}</li>
            </ul>
          </div>
        </div>

        <!-- Winners Display -->
        <div v-if="winners.length > 0">
          <!-- Top 3 Winners -->
          <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
              🏆 Winners
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div 
                v-for="winner in topWinners" 
                :key="winner.id"
                class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform hover:scale-105"
                :class="{
                  'md:order-2': getWinnerRank(winner) === 1,
                  'md:order-1': getWinnerRank(winner) === 2,
                  'md:order-3': getWinnerRank(winner) === 3
                }"
              >
                <!-- Award Badge -->
                <div 
                  class="py-4 text-center text-white font-bold text-xl"
                  :class="{
                    'bg-yellow-500': getWinnerRank(winner) === 1,
                    'bg-gray-400': getWinnerRank(winner) === 2,
                    'bg-orange-600': getWinnerRank(winner) === 3
                  }"
                >
                  <div class="text-4xl mb-2">
                    {{ getWinnerRank(winner) === 1 ? '🥇' : getWinnerRank(winner) === 2 ? '🥈' : '🥉' }}
                  </div>
                  {{ winner.award_type || formatWinnerTitle(getWinnerRank(winner)) }}
                </div>
                
                <!-- Image -->
                <div class="aspect-square overflow-hidden">
                  <img 
                    :src="winner.image_url" 
                    :alt="winner.title"
                    class="w-full h-full object-cover"
                  >
                </div>
                
                <!-- Details -->
                <div class="p-6">
                  <h3 class="text-xl font-bold text-gray-900 mb-2">
                    {{ winner.title }}
                  </h3>
                  <p class="text-gray-600 mb-4">
                    by {{ winner.photographer?.name || 'Unknown' }}
                  </p>
                  
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Final Score:</span>
                      <span class="font-bold text-red-600">{{ formatFixed(winner.final_score, 2, '0.00') }}/100</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Public Votes:</span>
                      <span class="font-medium">{{ winner.vote_count }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Judge Score:</span>
                      <span class="font-medium">{{ formatFixed(winner.judge_score, 2, '0.00') }}/50</span>
                    </div>
                    <div
                      v-if="winner.prize_amount"
                      class="flex justify-between pt-2 border-t"
                    >
                      <span class="text-gray-600">Prize:</span>
                      <span class="font-bold text-green-600">৳{{ formatNumber(winner.prize_amount) }}</span>
                    </div>
                  </div>
                  
                  <button 
                    class="w-full mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    @click="viewSubmission(winner)"
                  >
                    View Details
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Honorable Mentions -->
          <div
            v-if="honorableMentionsList.length > 0"
            class="mb-12"
          >
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
              Honorable Mentions
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
              <div 
                v-for="mention in honorableMentionsList" 
                :key="mention.id"
                class="bg-white rounded-lg shadow-md overflow-hidden cursor-pointer transform transition-transform hover:scale-105"
                @click="viewSubmission(mention)"
              >
                <div class="aspect-square overflow-hidden">
                  <img 
                    :src="mention.thumbnail_url || mention.image_url" 
                    :alt="mention.title"
                    class="w-full h-full object-cover"
                  >
                </div>
                <div class="p-3">
                  <p class="text-sm font-medium text-gray-900 truncate">
                    {{ mention.title }}
                  </p>
                  <p class="text-xs text-gray-600 truncate">
                    {{ mention.photographer?.name }}
                  </p>
                  <div class="flex justify-between items-center mt-2 text-xs">
                    <span class="text-gray-500">Rank: {{ getWinnerRank(mention) || '—' }}</span>
                    <span class="font-medium text-red-600">{{ formatFixed(mention.final_score, 1, '0.0') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Winners Yet -->
        <div
          v-else
          class="bg-white rounded-lg shadow-md p-12 text-center"
        >
          <div class="text-6xl mb-4">
            🏆
          </div>
          <h2 class="text-2xl font-bold text-gray-900 mb-2">
            Winners Not Yet Announced
          </h2>
          <p class="text-gray-600 mb-6">
            The competition is still in progress. Check back later for results!
          </p>
          <router-link 
            to="/competitions" 
            class="inline-block px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700"
          >
            View Other Competitions
          </router-link>
        </div>

        <!-- Full Leaderboard -->
        <div
          v-if="showLeaderboard"
          class="mt-12"
        >
          <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Full Leaderboard
          </h2>
          
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:hidden divide-y divide-gray-100">
              <div
                v-for="(submission, index) in leaderboard"
                :key="submission.id"
                class="p-4 flex items-center gap-4 hover:bg-gray-50 cursor-pointer"
                role="button"
                tabindex="0"
                :aria-label="`View submission ${submission.title || 'photo'}`"
                @click="viewSubmission(submission)"
                @keydown.enter="viewSubmission(submission)"
                @keydown.space.prevent="viewSubmission(submission)"
              >
                <div class="flex-shrink-0">
                  <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-white text-sm"
                    :class="{
                      'bg-yellow-500': index + 1 === 1,
                      'bg-gray-400': index + 1 === 2,
                      'bg-orange-600': index + 1 === 3,
                      'bg-gray-300 text-gray-700': index + 1 > 3
                    }"
                  >
                    {{ index + 1 }}
                  </span>
                </div>
                <img
                  :src="submission.thumbnail_url || submission.image_url"
                  :alt="submission.title"
                  class="w-16 h-16 object-cover rounded"
                >
                <div class="min-w-0 flex-1">
                  <p class="font-semibold text-gray-900 truncate">
                    {{ submission.title }}
                  </p>
                  <p class="text-sm text-gray-600 truncate">
                    {{ submission.photographer?.name || 'Unknown' }}
                  </p>
                  <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-600">
                    <span>Votes: {{ submission.vote_count }}</span>
                    <span>Judge: {{ submission.judge_score ? formatFixed(submission.judge_score, 2, '0.00') : 'N/A' }}</span>
                    <span class="font-semibold text-red-600">Final: {{ submission.final_score ? formatFixed(submission.final_score, 2, '0.00') : 'N/A' }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="hidden md:block overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Rank
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Photo
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Title
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Photographer
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Votes
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Judge
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Final
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="(submission, index) in leaderboard"
                  :key="submission.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4">
                    <span 
                      class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-white"
                      :class="{
                        'bg-yellow-500': index + 1 === 1,
                        'bg-gray-400': index + 1 === 2,
                        'bg-orange-600': index + 1 === 3,
                        'bg-gray-300 text-gray-700': index + 1 > 3
                      }"
                    >
                      {{ index + 1 }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <img 
                      :src="submission.thumbnail_url || submission.image_url" 
                      :alt="submission.title"
                      class="w-16 h-16 object-cover rounded"
                    >
                  </td>
                  <td class="px-6 py-4 font-medium text-gray-900">
                    {{ submission.title }}
                  </td>
                  <td class="px-6 py-4 text-gray-600">
                    {{ submission.photographer?.name }}
                  </td>
                  <td class="px-6 py-4 text-gray-600">
                    {{ submission.vote_count }}
                  </td>
                  <td class="px-6 py-4 text-gray-600">
                    {{ submission.judge_score ? formatFixed(submission.judge_score, 2, '0.00') : 'N/A' }}
                  </td>
                  <td class="px-6 py-4">
                    <span class="font-bold text-red-600">{{ submission.final_score ? formatFixed(submission.final_score, 2, '0.00') : 'N/A' }}</span>
                  </td>
                </tr>
              </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="mt-8 text-center">
          <button 
            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
            @click="showLeaderboard = !showLeaderboard"
          >
            {{ showLeaderboard ? 'Hide' : 'Show' }} Full Leaderboard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api'
import Toast from '../components/ui/Toast.vue'
import BaseModal from '../components/admin/modals/BaseModal.vue'
import { useApiError } from '../composables/useApiError'
import { useDevLogger } from '../composables/useDevLogger'
import { formatFixed, formatNumber } from '../utils/formatters'

const route = useRoute()
const competitionSlug = route.params.slug
const router = useRouter()

const loading = ref(true)
const calculating = ref(false)
const announcing = ref(false)
const competition = ref({})
const winners = ref([])
const honorableMentions = ref([])
const leaderboard = ref([])
const showLeaderboard = ref(false)
const previewData = ref(null)
const isAdminRoute = computed(() => route.meta?.requiresAdmin === true)
const backLink = computed(() => (isAdminRoute.value
  ? '/admin/dashboard'
  : `/competitions/${competitionSlug}`))
const backLabel = computed(() => (isAdminRoute.value
  ? 'Back to Admin Dashboard'
  : 'Back to Competition'))
const showConfirmModal = ref(false)

const { toastMessage, toastType, toastVisible, showToast, handleApiError, closeToast } = useApiError()
const { log } = useDevLogger()

// Admin configuration
const config = ref({
  voteWeight: 40, // 40%
  judgeWeight: 60, // 60%
  numberOfWinners: 3,
  honorableMentions: 5
})

const topWinners = computed(() => {
  return winners.value
    .filter(w => {
      const rank = getWinnerRank(w)
      return rank !== null && rank <= 3
    })
    .sort((a, b) => (getWinnerRank(a) || 0) - (getWinnerRank(b) || 0))
})

const honorableMentionsList = computed(() => {
  return honorableMentions.value
})

const winnersTotal = computed(() => winners.value.length)
const submissionsTotal = computed(() => Number(competition.value?.total_submissions) || 0)
const votesTotal = computed(() => Number(competition.value?.total_votes) || 0)

const getWinnerRank = (winner) => {
  const value = winner?.winner_position ?? winner?.rank
  return Number.isFinite(Number(value)) ? Number(value) : null
}

const formatWinnerTitle = (rank) => {
  if (rank === 1) return '1st Place'
  if (rank === 2) return '2nd Place'
  if (rank === 3) return '3rd Place'
  return 'Winner'
}

const fetchCompetition = async () => {
  try {
    const { data } = await api.get(`/competitions/${competitionSlug}`)
    competition.value = data.data
    
  } catch (error) {
    handleApiError(error, 'Failed to load competition')
  }
}

const fetchWinners = async () => {
  try {
    const { data } = await api.get(`/competitions/${competition.value.id}/winners`)
    winners.value = data.data.winners || []
    honorableMentions.value = data.data.honorable_mentions || []
  } catch (error) {
    handleApiError(error, 'Failed to load winners')
  }
}

const fetchLeaderboard = async () => {
  try {
    const { data } = await api.get(`/competitions/${competition.value.id}/full-leaderboard?limit=50`)
    leaderboard.value = data.data || []
  } catch (error) {
    handleApiError(error, 'Failed to load leaderboard')
  }
}

const previewWinners = async () => {
  if (!isAdminRoute.value) return
  calculating.value = true
  try {
    const { data } = await api.post(`/admin/competitions/${competition.value.id}/calculate-winners`, {
      vote_weight: config.value.voteWeight / 100,
      judge_weight: config.value.judgeWeight / 100,
      number_of_winners: config.value.numberOfWinners,
      honorable_mentions: config.value.honorableMentions
    })
    
    previewData.value = data.data
    showToast('Preview calculated! Review the results below.', 'success')
  } catch (error) {
    handleApiError(error, 'Failed to calculate winners')
  } finally {
    calculating.value = false
  }
}

const requestAnnounceWinners = () => {
  if (!isAdminRoute.value) return
  showConfirmModal.value = true
}

const confirmAnnounceWinners = async () => {
  if (!isAdminRoute.value) return
  announcing.value = true
  try {
    const { data } = await api.post(`/admin/competitions/${competition.value.id}/announce-winners`, {
      vote_weight: config.value.voteWeight / 100,
      judge_weight: config.value.judgeWeight / 100,
      number_of_winners: config.value.numberOfWinners,
      honorable_mentions: config.value.honorableMentions
    })
    
    showToast('Winners announced successfully!', 'success')
    previewData.value = null
    showConfirmModal.value = false
    await fetchCompetition()
    await fetchWinners()
    await fetchLeaderboard()
  } catch (error) {
    handleApiError(error, 'Failed to announce winners')
  } finally {
    announcing.value = false
  }
}

const viewSubmission = (submission) => {
  if (!submission?.id) return
  router.push(`/competitions/${competitionSlug}/submissions/${submission.id}`)
}

onMounted(async () => {
  await fetchCompetition()
  await fetchWinners()
  await fetchLeaderboard()
  loading.value = false
})
</script>
