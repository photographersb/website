<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page Header -->
      <div class="mb-8">
        <router-link to="/competitions" class="text-red-600 hover:text-red-700 mb-4 inline-block">
          ← Back to Competitions
        </router-link>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ competition.title }}</h1>
        <p class="text-gray-600">Competition Winners & Results</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-red-600"></div>
      </div>

      <!-- Winners Section -->
      <div v-else>
        <!-- Admin Controls (if admin) -->
        <div v-if="isAdmin && competition.status !== 'closed'" class="bg-white rounded-lg shadow-md p-6 mb-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Admin Controls</h2>
          
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
              />
              <p class="text-xs text-gray-600 mt-1">Percentage weight for public votes (0-100)</p>
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
              />
              <p class="text-xs text-gray-600 mt-1">Percentage weight for judge scores (0-100)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Number of Winners</label>
              <input 
                v-model.number="config.numberOfWinners" 
                type="number" 
                min="1" 
                max="10"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Honorable Mentions</label>
              <input 
                v-model.number="config.honorableMentions" 
                type="number" 
                min="0" 
                max="20"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
              />
            </div>
          </div>
          
          <div class="flex gap-4">
            <button 
              @click="previewWinners" 
              :disabled="calculating"
              class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50"
            >
              {{ calculating ? 'Calculating...' : 'Preview Winners' }}
            </button>
            
            <button 
              @click="announceWinners" 
              :disabled="!previewData || announcing"
              class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              {{ announcing ? 'Announcing...' : 'Announce Winners (Final)' }}
            </button>
          </div>
          
          <!-- Preview Results -->
          <div v-if="previewData" class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm font-medium text-yellow-800 mb-2">Preview Results:</p>
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
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">🏆 Winners</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div 
                v-for="winner in topWinners" 
                :key="winner.id"
                class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform hover:scale-105"
                :class="{
                  'md:order-2': winner.rank === 1,
                  'md:order-1': winner.rank === 2,
                  'md:order-3': winner.rank === 3
                }"
              >
                <!-- Award Badge -->
                <div 
                  class="py-4 text-center text-white font-bold text-xl"
                  :class="{
                    'bg-yellow-500': winner.rank === 1,
                    'bg-gray-400': winner.rank === 2,
                    'bg-orange-600': winner.rank === 3
                  }"
                >
                  <div class="text-4xl mb-2">
                    {{ winner.rank === 1 ? '🥇' : winner.rank === 2 ? '🥈' : '🥉' }}
                  </div>
                  {{ winner.award_type }}
                </div>
                
                <!-- Image -->
                <div class="aspect-square overflow-hidden">
                  <img 
                    :src="winner.image_url" 
                    :alt="winner.title"
                    class="w-full h-full object-cover"
                  />
                </div>
                
                <!-- Details -->
                <div class="p-6">
                  <h3 class="text-xl font-bold text-gray-900 mb-2">{{ winner.title }}</h3>
                  <p class="text-gray-600 mb-4">by {{ winner.photographer?.name || 'Unknown' }}</p>
                  
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Final Score:</span>
                      <span class="font-bold text-red-600">{{ winner.final_score?.toFixed(2) }}/100</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Public Votes:</span>
                      <span class="font-medium">{{ winner.vote_count }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Judge Score:</span>
                      <span class="font-medium">{{ winner.judge_score?.toFixed(2) }}/50</span>
                    </div>
                    <div v-if="winner.prize_amount" class="flex justify-between pt-2 border-t">
                      <span class="text-gray-600">Prize:</span>
                      <span class="font-bold text-green-600">৳{{ winner.prize_amount.toLocaleString() }}</span>
                    </div>
                  </div>
                  
                  <button 
                    @click="viewSubmission(winner)"
                    class="w-full mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                  >
                    View Details
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Honorable Mentions -->
          <div v-if="honorableMentionsList.length > 0" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Honorable Mentions</h2>
            
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
                  />
                </div>
                <div class="p-3">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ mention.title }}</p>
                  <p class="text-xs text-gray-600 truncate">{{ mention.photographer?.name }}</p>
                  <div class="flex justify-between items-center mt-2 text-xs">
                    <span class="text-gray-500">Rank: {{ mention.rank }}</span>
                    <span class="font-medium text-red-600">{{ mention.final_score?.toFixed(1) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Winners Yet -->
        <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
          <div class="text-6xl mb-4">🏆</div>
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Winners Not Yet Announced</h2>
          <p class="text-gray-600 mb-6">The competition is still in progress. Check back later for results!</p>
          <router-link 
            to="/competitions" 
            class="inline-block px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700"
          >
            View Other Competitions
          </router-link>
        </div>

        <!-- Full Leaderboard -->
        <div v-if="showLeaderboard" class="mt-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Full Leaderboard</h2>
          
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photo</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photographer</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Votes</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judge</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Final</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="submission in leaderboard" :key="submission.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4">
                    <span 
                      class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-white"
                      :class="{
                        'bg-yellow-500': submission.rank === 1,
                        'bg-gray-400': submission.rank === 2,
                        'bg-orange-600': submission.rank === 3,
                        'bg-gray-300 text-gray-700': submission.rank > 3
                      }"
                    >
                      {{ submission.rank }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <img 
                      :src="submission.thumbnail_url || submission.image_url" 
                      :alt="submission.title"
                      class="w-16 h-16 object-cover rounded"
                    />
                  </td>
                  <td class="px-6 py-4 font-medium text-gray-900">{{ submission.title }}</td>
                  <td class="px-6 py-4 text-gray-600">{{ submission.photographer?.name }}</td>
                  <td class="px-6 py-4 text-gray-600">{{ submission.vote_count }}</td>
                  <td class="px-6 py-4 text-gray-600">{{ submission.judge_score?.toFixed(2) || 'N/A' }}</td>
                  <td class="px-6 py-4">
                    <span class="font-bold text-red-600">{{ submission.final_score?.toFixed(2) || 'N/A' }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="mt-8 text-center">
          <button 
            @click="showLeaderboard = !showLeaderboard"
            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
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
import { useRoute } from 'vue-router'
import api from '../api'

const route = useRoute()
const competitionSlug = route.params.slug

const loading = ref(true)
const calculating = ref(false)
const announcing = ref(false)
const competition = ref({})
const winners = ref([])
const honorableMentions = ref([])
const leaderboard = ref([])
const showLeaderboard = ref(false)
const previewData = ref(null)
const isAdmin = ref(false) // TODO: Check user role from auth

// Admin configuration
const config = ref({
  voteWeight: 40, // 40%
  judgeWeight: 60, // 60%
  numberOfWinners: 3,
  honorableMentions: 5
})

const topWinners = computed(() => {
  return winners.value.filter(w => w.rank <= 3).sort((a, b) => a.rank - b.rank)
})

const honorableMentionsList = computed(() => {
  return honorableMentions.value
})

const fetchCompetition = async () => {
  try {
    const { data } = await api.get(`/competitions/${competitionSlug}`)
    competition.value = data.data
    
    // Check if user is admin (TODO: implement proper auth check)
    const user = JSON.parse(localStorage.getItem('user') || '{}')
    isAdmin.value = user.role === 'admin' || user.role === 'super_admin'
  } catch (error) {
    console.error('Error fetching competition:', error)
    alert('Failed to load competition')
  }
}

const fetchWinners = async () => {
  try {
    const { data } = await api.get(`/competitions/${competition.value.id}/winners`)
    winners.value = data.data.winners || []
    honorableMentions.value = data.data.honorable_mentions || []
  } catch (error) {
    console.error('Error fetching winners:', error)
  }
}

const fetchLeaderboard = async () => {
  try {
    const { data } = await api.get(`/competitions/${competition.value.id}/full-leaderboard?limit=50`)
    leaderboard.value = data.data || []
  } catch (error) {
    console.error('Error fetching leaderboard:', error)
  }
}

const previewWinners = async () => {
  calculating.value = true
  try {
    const { data } = await api.post(`/admin/competitions/${competition.value.id}/calculate-winners`, {
      vote_weight: config.value.voteWeight / 100,
      judge_weight: config.value.judgeWeight / 100,
      number_of_winners: config.value.numberOfWinners,
      honorable_mentions: config.value.honorableMentions
    })
    
    previewData.value = data.data
    alert('Preview calculated! Review the results below.')
  } catch (error) {
    console.error('Error calculating winners:', error)
    alert(error.response?.data?.message || 'Failed to calculate winners')
  } finally {
    calculating.value = false
  }
}

const announceWinners = async () => {
  if (!confirm('Are you sure you want to announce winners? This action cannot be undone and will close the competition.')) {
    return
  }
  
  announcing.value = true
  try {
    const { data } = await api.post(`/admin/competitions/${competition.value.id}/announce-winners`, {
      vote_weight: config.value.voteWeight / 100,
      judge_weight: config.value.judgeWeight / 100,
      number_of_winners: config.value.numberOfWinners,
      honorable_mentions: config.value.honorableMentions
    })
    
    alert('Winners announced successfully!')
    previewData.value = null
    await fetchCompetition()
    await fetchWinners()
    await fetchLeaderboard()
  } catch (error) {
    console.error('Error announcing winners:', error)
    alert(error.response?.data?.message || 'Failed to announce winners')
  } finally {
    announcing.value = false
  }
}

const viewSubmission = (submission) => {
  // TODO: Navigate to submission detail or show modal
  console.log('View submission:', submission)
}

onMounted(async () => {
  await fetchCompetition()
  await fetchWinners()
  await fetchLeaderboard()
  loading.value = false
})
</script>
