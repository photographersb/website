<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="loading" class="container mx-auto px-4 py-16 text-center">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
      <p class="text-gray-600 mt-4">Loading submission...</p>
    </div>

    <!-- Submission Content -->
    <div v-else-if="submission" class="pb-16">
      <!-- Header -->
      <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-6">
          <router-link :to="`/competitions/${competition?.slug}/gallery`" class="text-red-600 hover:text-red-700 inline-flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Gallery
          </router-link>
        </div>
      </div>

      <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Image Section -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
              <!-- Winner Badge -->
              <div v-if="submission.is_winner" class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white px-6 py-3 flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="font-bold text-lg">{{ submission.winner_position }} Place Winner</span>
              </div>

              <!-- Image -->
              <div class="relative bg-black">
                <img 
                  :src="submission.image_url" 
                  :alt="submission.title"
                  class="w-full h-auto max-h-[80vh] object-contain mx-auto"
                />
              </div>

              <!-- Stats Bar -->
              <div class="px-6 py-4 border-t flex flex-wrap items-center justify-between gap-4 bg-gray-50">
                <div class="flex items-center gap-6">
                  <!-- Vote Button -->
                  <button 
                    @click="toggleVote"
                    :disabled="votingInProgress"
                    :class="[
                      'flex items-center gap-2 px-4 py-2 rounded-full font-semibold transition-all text-base',
                      hasVoted 
                        ? 'bg-red-600 text-white hover:bg-red-700' 
                        : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                    ]"
                  >
                    <svg class="w-5 h-5" :fill="hasVoted ? 'currentColor' : 'none'" :stroke="hasVoted ? 'none' : 'currentColor'" viewBox="0 0 20 20">
                      <path v-if="hasVoted" fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="font-semibold">{{ submission.vote_count }}</span>
                  </button>
                  
                  <div class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="font-semibold">{{ submission.view_count }}</span>
                  </div>
                </div>

                <!-- Share Buttons -->
                <div class="flex items-center gap-2">
                  <button 
                    @click="shareOnFacebook"
                    class="p-2 rounded-full hover:bg-gray-200 transition-colors"
                    title="Share on Facebook"
                  >
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                  </button>
                  <button 
                    @click="shareOnTwitter"
                    class="p-2 rounded-full hover:bg-gray-200 transition-colors"
                    title="Share on Twitter"
                  >
                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                  </button>
                  <button 
                    @click="copyLink"
                    class="p-2 rounded-full hover:bg-gray-200 transition-colors"
                    title="Copy Link"
                  >
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Info Sidebar -->
          <div class="space-y-6">
            <!-- Title & Description -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ submission.title }}</h1>
              
              <p v-if="submission.description" class="text-gray-700 mb-6 whitespace-pre-line">
                {{ submission.description }}
              </p>

              <!-- Photographer Info -->
              <div class="border-t pt-4">
                <div class="flex items-center gap-4 mb-4">
                  <img 
                    :src="submission.photographer?.profile_image || '/images/default-avatar.png'" 
                    :alt="submission.photographer?.name"
                    class="w-12 h-12 rounded-full object-cover"
                  />
                  <div>
                    <p class="font-semibold text-gray-900">{{ submission.photographer?.name }}</p>
                    <p class="text-sm text-gray-600">Photographer</p>
                  </div>
                </div>
                
                <router-link 
                  v-if="submission.photographer?.photographer"
                  :to="`/photographers/${submission.photographer.photographer.id}`"
                  class="block w-full text-center bg-gray-100 text-gray-700 py-2 rounded-lg font-medium hover:bg-gray-200 transition-colors"
                >
                  View Profile
                </router-link>
              </div>
            </div>

            <!-- Photo Details -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h2 class="font-bold text-gray-900 mb-4">Photo Details</h2>
              
              <div class="space-y-3 text-sm">
                <div v-if="submission.location" class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <div>
                    <p class="text-gray-600">Location</p>
                    <p class="text-gray-900 font-medium">{{ submission.location }}</p>
                  </div>
                </div>

                <div v-if="submission.date_taken" class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <div>
                    <p class="text-gray-600">Date Taken</p>
                    <p class="text-gray-900 font-medium">{{ formatDate(submission.date_taken) }}</p>
                  </div>
                </div>

                <div v-if="submission.camera_make || submission.camera_model" class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <div>
                    <p class="text-gray-600">Camera</p>
                    <p class="text-gray-900 font-medium">
                      {{ submission.camera_make }} {{ submission.camera_model }}
                    </p>
                  </div>
                </div>

                <div v-if="submission.camera_settings" class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <div>
                    <p class="text-gray-600">Settings</p>
                    <p class="text-gray-900 font-medium">{{ submission.camera_settings }}</p>
                  </div>
                </div>

                <div v-if="submission.hashtags" class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                  </svg>
                  <div>
                    <p class="text-gray-600">Tags</p>
                    <p class="text-gray-900 font-medium">{{ submission.hashtags }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Competition Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h2 class="font-bold text-gray-900 mb-4">Competition</h2>
              <router-link :to="`/competitions/${competition?.slug}`" class="block hover:bg-gray-50 transition-colors rounded-lg">
                <h3 class="font-semibold text-red-600 mb-1">{{ competition?.title }}</h3>
                <p v-if="competition?.theme" class="text-sm text-gray-600">{{ competition.theme }}</p>
              </router-link>
            </div>

            <!-- Sponsors Section -->
            <div v-if="competition?.sponsors && competition.sponsors.length > 0" class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-lg p-5 border border-gray-200">
              <h3 class="text-sm font-semibold text-gray-600 mb-4 text-center flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                SPONSORED BY
              </h3>
              <div class="flex flex-wrap items-center justify-center gap-3">
                <a 
                  v-for="sponsor in competition.sponsors.filter(s => s.is_active)"
                  :key="sponsor.id"
                  :href="sponsor.website_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  :class="[
                    'flex items-center justify-center p-3 rounded-lg transition-all hover:scale-105',
                    sponsor.tier === 'platinum' ? 'bg-gradient-to-br from-slate-100 to-slate-200 border-2 border-slate-300' :
                    sponsor.tier === 'gold' ? 'bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-yellow-300' :
                    sponsor.tier === 'silver' ? 'bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-300' :
                    'bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-300'
                  ]"
                  :title="sponsor.name"
                >
                  <img 
                    v-if="sponsor.logo_url"
                    :src="sponsor.logo_url" 
                    :alt="sponsor.name"
                    :class="[
                      'object-contain',
                      sponsor.tier === 'platinum' ? 'h-10 w-28' :
                      sponsor.tier === 'gold' ? 'h-9 w-24' :
                      sponsor.tier === 'silver' ? 'h-8 w-20' :
                      'h-7 w-18'
                    ]"
                  />
                  <span v-else class="text-gray-700 font-semibold text-xs">{{ sponsor.name }}</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';

const route = useRoute();
const router = useRouter();

const submission = ref(null);
const competition = ref(null);
const loading = ref(true);
const hasVoted = ref(false);
const votingInProgress = ref(false);

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const isAuthenticated = !!user.value;

onMounted(async () => {
  await fetchSubmission();
  if (isAuthenticated) {
    await checkVoteStatus();
  }
});

const fetchSubmission = async () => {
  try {
    // First get competition
    const compData = await api.get(`/competitions/${route.params.slug}`);
    competition.value = compData.data.data;

    // Then get submission
    const { data } = await api.get(
      `/competitions/${competition.value.id}/submissions/${route.params.submissionId}`
    );
    submission.value = data.data;
  } catch (error) {
    console.error('Error fetching submission:', error);
    alert('Submission not found');
    router.push(`/competitions/${route.params.slug}/gallery`);
  } finally {
    loading.value = false;
  }
};

const checkVoteStatus = async () => {
  try {
    const { data } = await api.get(
      `/competitions/${competition.value.id}/submissions/${route.params.submissionId}/vote-status`
    );
    hasVoted.value = data.data.has_voted;
  } catch (error) {
    console.error('Error checking vote status:', error);
  }
};

const toggleVote = async () => {
  if (!isAuthenticated) {
    alert('Please login to vote');
    router.push('/login');
    return;
  }
  
  votingInProgress.value = true;
  
  try {
    if (hasVoted.value) {
      // Unvote
      await api.delete(
        `/competitions/${competition.value.id}/submissions/${submission.value.id}/vote`
      );
      hasVoted.value = false;
      submission.value.vote_count--;
    } else {
      // Vote
      const { data } = await api.post(
        `/competitions/${competition.value.id}/submissions/${submission.value.id}/vote`
      );
      hasVoted.value = true;
      submission.value.vote_count = data.data.vote_count;
    }
  } catch (error) {
    console.error('Error voting:', error);
    alert(error.response?.data?.message || 'Failed to process vote');
  } finally {
    votingInProgress.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric'
  });
};

const shareOnFacebook = () => {
  const url = window.location.href;
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
};

const shareOnTwitter = () => {
  const url = window.location.href;
  const text = `Check out "${submission.value.title}" by ${submission.value.photographer?.name} in ${competition.value?.title}`;
  window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
};

const copyLink = () => {
  navigator.clipboard.writeText(window.location.href);
  alert('Link copied to clipboard!');
};
</script>
