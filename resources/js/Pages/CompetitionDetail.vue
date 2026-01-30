<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Loading State -->
    <div v-if="loading" class="container mx-auto px-4 py-16 text-center">
      <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-4 border-burgundy"></div>
      <p class="text-gray-600 mt-6 text-lg">Loading competition details...</p>
    </div>

    <!-- Competition Content -->
    <div v-else-if="competition" class="pb-16">
      <!-- Hero Banner -->
      <div class="relative h-72 sm:h-96 md:h-[32rem] bg-gradient-to-br from-burgundy via-red-900 to-red-800 overflow-hidden">
        <img v-if="competition.hero_image || competition.banner_image" :src="competition.hero_image || competition.banner_image" :alt="competition.title" class="w-full h-full object-cover opacity-40" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-burgundy/20 rounded-full blur-3xl"></div>
        
        <div class="absolute inset-0 container mx-auto px-4 flex flex-col justify-end py-8 sm:py-12 md:py-16">
          <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
            <span :class="`px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-sm sm:text-base font-bold ${getStatusBadgeClass(competition.status)} shadow-lg`">
              {{ formatStatus(competition.status) }}
            </span>
            <span v-if="competition.is_featured" class="px-4 sm:px-5 py-2 sm:py-2.5 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-xl text-sm sm:text-base font-bold flex items-center gap-2 shadow-lg">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              Featured
            </span>
            <span v-if="competition.status === 'active'" class="px-4 sm:px-5 py-2 sm:py-2.5 bg-white/20 backdrop-blur-sm text-white rounded-xl text-sm sm:text-base font-semibold animate-pulse">
              ⏰ {{ getTimeRemaining(competition.submission_deadline) }}
            </span>
          </div>
          <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-3 sm:mb-4 leading-tight drop-shadow-2xl">
            {{ competition.title }}
          </h1>
          <p v-if="competition.theme" class="text-lg sm:text-xl md:text-2xl text-red-200 font-semibold mb-4 drop-shadow-lg">
            📷 {{ competition.theme }}
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 -mt-16 sm:-mt-20 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-gradient-to-br from-burgundy to-red-800 rounded-2xl shadow-xl p-5 text-center transform hover:scale-105 transition-transform">
                <div class="text-3xl font-black text-white mb-2">৳{{ formatNumber(competition.total_prize_pool) }}</div>
                <div class="text-sm text-white/90 font-medium">Prize Pool</div>
              </div>
              <div class="bg-white rounded-2xl shadow-xl p-5 text-center transform hover:scale-105 transition-transform">
                <div class="text-3xl font-black text-gray-900 mb-2">{{ competition.total_submissions }}</div>
                <div class="text-sm text-gray-600 font-medium">Submissions</div>
              </div>
              <div class="bg-white rounded-2xl shadow-xl p-5 text-center transform hover:scale-105 transition-transform">
                <div class="text-3xl font-black text-purple-600 mb-2">{{ competition.total_votes }}</div>
                <div class="text-sm text-gray-600 font-medium">Votes</div>
              </div>
              <div class="bg-white rounded-2xl shadow-xl p-5 text-center transform hover:scale-105 transition-transform">
                <div class="text-3xl font-black text-blue-600 mb-2">{{ competition.number_of_winners }}</div>
                <div class="text-sm text-gray-600 font-medium">Winners</div>
              </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
              <h2 class="text-2xl md:text-3xl font-bold mb-4 text-gray-900 flex items-center gap-3">
                <span class="w-1.5 h-8 bg-burgundy rounded-full"></span>
                About This Competition
              </h2>
              <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ competition.description }}</p>
              </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
              <h2 class="text-2xl md:text-3xl font-bold mb-6 text-gray-900 flex items-center gap-3">
                <span class="w-1.5 h-8 bg-burgundy rounded-full"></span>
                Competition Timeline
              </h2>
              <div class="space-y-4 sm:space-y-5">
                <div class="flex items-start gap-4">
                  <div :class="`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg ${isDatePassed(competition.submission_deadline) ? 'bg-gray-400' : 'bg-gradient-to-br from-burgundy to-red-700'}`">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-base sm:text-lg">Submission Deadline</h3>
                    <p class="text-sm sm:text-base text-gray-600 break-words">{{ formatDateTime(competition.submission_deadline) }}</p>
                    <p v-if="!isDatePassed(competition.submission_deadline)" class="text-sm text-red-600 font-medium mt-1">
                      {{ getTimeRemaining(competition.submission_deadline) }}
                    </p>
                  </div>
                </div>

                <div v-if="competition.voting_start_at" class="flex items-start gap-4">
                  <div :class="`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg ${isDatePassed(competition.voting_end_at) ? 'bg-gray-400' : isVotingActive ? 'bg-gradient-to-br from-purple-600 to-purple-800' : 'bg-gray-400'}`">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-base sm:text-lg">Voting Period</h3>
                    <p class="text-sm sm:text-base text-gray-600 break-words">{{ formatDateTime(competition.voting_start_at) }} - {{ formatDateTime(competition.voting_end_at) }}</p>
                    <p v-if="isVotingActive" class="text-sm text-purple-600 font-medium mt-1">
                      Voting is live now!
                    </p>
                  </div>
                </div>

                <div v-if="competition.results_announcement_date" class="flex items-start gap-4">
                  <div :class="`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg ${competition.results_published ? 'bg-gradient-to-br from-green-500 to-green-700' : 'bg-gray-400'}`">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-base sm:text-lg">Winner Announcement</h3>
                    <p class="text-sm sm:text-base text-gray-600 break-words">{{ formatDate(competition.results_announcement_date) }}</p>
                    <p v-if="competition.results_published" class="text-sm text-green-600 font-medium mt-1">
                      Results are now available!
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submissions / Leaderboard -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center gap-3">
                  <span class="w-1.5 h-8 bg-burgundy rounded-full"></span>
                  {{ competition.status === 'completed' ? 'Winners' : isVotingActive ? 'Leaderboard' : 'Top Submissions' }}
                </h2>
                <button v-if="competition.total_submissions > 0" @click="viewLeaderboard" 
                        class="bg-burgundy hover:bg-red-800 text-white font-semibold px-4 py-2 rounded-xl flex items-center gap-2 transition-colors shadow-lg">
                  View All
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>

              <div v-if="topSubmissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                <div v-for="(submission, index) in topSubmissions" :key="submission.id" 
                     class="relative group cursor-pointer rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-shadow">
                  <img :src="submission.image_url" :alt="submission.title" class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-300" />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-100 transition-opacity">
                    <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                      <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl md:text-3xl font-black">#{{ index + 1 }}</span>
                        <span class="bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-semibold">
                          {{ submission.vote_count }} votes
                        </span>
                      </div>
                      <h4 class="font-bold text-lg mb-1">{{ submission.title }}</h4>
                      <p class="text-sm text-gray-200">by {{ submission.photographer?.name || 'Anonymous' }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-lg font-semibold">No submissions yet</p>
                <p class="text-sm">Be the first to participate!</p>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1 space-y-5">
            <!-- Action Card -->
            <div class="bg-white rounded-2xl shadow-xl p-6 lg:sticky lg:top-6">
              <div class="space-y-5">
                <!-- Status Info -->
                <div class="p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-l-4 border-burgundy">
                  <h3 class="font-bold mb-4 text-lg text-gray-900">Competition Details</h3>
                  <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                      <span class="text-gray-600 font-medium">Entry Fee</span>
                      <span class="font-semibold">{{ competition.is_paid_competition ? `৳${competition.participation_fee}` : 'Free' }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Max Entries</span>
                      <span class="font-semibold">{{ competition.max_submissions_per_user }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Public Voting</span>
                      <span class="font-semibold">{{ competition.allow_public_voting ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Watermark</span>
                      <span class="font-semibold">{{ competition.require_watermark ? 'Required' : 'Optional' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div v-if="competition.status === 'active'" class="space-y-3">
                  <button v-if="isAuthenticated" @click="submitPhoto" class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                    Submit Your Photo
                  </button>
                  <button v-else @click="$router.push('/login')" class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                    Login to Participate
                  </button>
                  <button @click="viewGallery" class="w-full bg-gray-100 text-gray-800 py-3 rounded-lg font-semibold hover:bg-gray-200 transition-colors">
                    View Gallery
                  </button>
                  <button @click="viewLeaderboard" class="w-full border-2 border-red-600 text-red-600 py-3 rounded-lg font-semibold hover:bg-red-50 transition-colors">
                    View Leaderboard
                  </button>
                </div>

                <div v-else-if="competition.status === 'judging'" class="space-y-3">
                  <button @click="viewLeaderboard" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                    View Leaderboard
                  </button>
                  <div class="text-center text-sm text-gray-600">
                    <p class="font-semibold">Judging in Progress</p>
                    <p>Winners will be announced soon!</p>
                  </div>
                </div>

                <div v-else-if="competition.status === 'completed'" class="space-y-3">
                  <button @click="viewLeaderboard" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    View Winners
                  </button>
                  <div class="text-center text-sm text-gray-600">
                    <p class="font-semibold">Competition Ended</p>
                    <p>Check out the winners!</p>
                  </div>
                </div>

                <div v-else class="text-center p-4 bg-gray-50 rounded-lg">
                  <p class="text-gray-600 font-semibold">Coming Soon</p>
                  <p class="text-sm text-gray-500 mt-1">Submissions not yet open</p>
                </div>
              </div>

              <!-- Organizer Info -->
              <div v-if="competition.organizer" class="mt-6 pt-6 border-t">
                <h3 class="font-semibold mb-3">Organized By</h3>
                <div class="flex items-center gap-3">
                  <img v-if="competition.organizer.profile_photo_url" :src="competition.organizer.profile_photo_url" :alt="competition.organizer.business_name" class="w-12 h-12 rounded-full object-cover" />
                  <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold" v-else>
                    {{ competition.organizer.business_name?.charAt(0) }}
                  </div>
                  <div>
                    <p class="font-semibold">{{ competition.organizer.business_name }}</p>
                    <p class="text-sm text-gray-600">{{ competition.organizer.city }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Share Card -->
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-5 md:p-6">
              <h3 class="font-semibold mb-3 text-sm sm:text-base">Share Competition</h3>
              <div class="flex flex-wrap gap-2">
                <button @click="shareOnFacebook" class="flex-1 min-w-[100px] bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base">
                  Facebook
                </button>
                <button @click="shareOnTwitter" class="flex-1 min-w-[100px] bg-sky-500 text-white py-2 rounded-lg hover:bg-sky-600 transition-colors text-sm sm:text-base">
                  Twitter
                </button>
                <button @click="copyLink" class="px-3 sm:px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="container mx-auto px-4 py-16 text-center">
      <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">Competition Not Found</h3>
      <p class="text-gray-600 mb-6">The competition you're looking for doesn't exist or has been removed.</p>
      <button @click="$router.push('/competitions')" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors">
        Browse Competitions
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const competition = ref(null);
const topSubmissions = ref([]);
const loading = ref(true);
const isAuthenticated = ref(false);

const isVotingActive = computed(() => {
  if (!competition.value) return false;
  const now = new Date();
  const start = new Date(competition.value.voting_start_at);
  const end = new Date(competition.value.voting_end_at);
  return now >= start && now <= end;
});

const fetchCompetition = async () => {
  try {
    const slug = route.params.slug;
    const { data } = await axios.get(`/competitions/${slug}`);
    
    if (data.status === 'success') {
      competition.value = data.data;
      fetchTopSubmissions(slug);
    }
  } catch (error) {
    console.error('Error fetching competition:', error);
    competition.value = null;
  } finally {
    loading.value = false;
  }
};

const fetchTopSubmissions = async (slug) => {
  try {
    const competitionSlug = slug || competition.value?.slug || route.params.slug;
    const { data } = await axios.get(`/competitions/${competitionSlug}/leaderboard`);
    if (data.status === 'success') {
      topSubmissions.value = data.data.slice(0, 6);
    }
  } catch (error) {
    console.error('Error fetching submissions:', error);
  }
};

const checkAuth = () => {
  const token = localStorage.getItem('auth_token');
  isAuthenticated.value = !!token;
};

const submitPhoto = () => {
  router.push(`/competitions/${competition.value.slug}/submit`);
};

const viewGallery = () => {
  router.push(`/competitions/${competition.value.slug}/gallery`);
};

const viewLeaderboard = () => {
  router.push(`/competitions/${competition.value.slug}/leaderboard`);
};

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-green-100 text-green-800',
    judging: 'bg-purple-100 text-purple-800',
    completed: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
  });
};

const formatDateTime = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatNumber = (num) => {
  if (!num) return '0';
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

const isDatePassed = (date) => {
  return new Date(date) < new Date();
};

const getTimeRemaining = (deadline) => {
  const now = new Date();
  const end = new Date(deadline);
  const diff = end - now;
  
  if (diff < 0) return 'Deadline passed';
  
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  
  if (days > 0) return `${days} day${days > 1 ? 's' : ''} remaining`;
  if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} remaining`;
  return 'Less than an hour remaining';
};

const shareOnFacebook = () => {
  const url = encodeURIComponent(window.location.href);
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
};

const shareOnTwitter = () => {
  const url = encodeURIComponent(window.location.href);
  const text = encodeURIComponent(`Check out this photography competition: ${competition.value.title}`);
  window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard!');
  } catch (error) {
    console.error('Error copying link:', error);
  }
};

onMounted(() => {
  checkAuth();
  fetchCompetition();
});
</script>
