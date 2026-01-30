<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b">
      <div class="container mx-auto px-4 py-6">
        <router-link :to="`/competitions/${competition?.slug}`" class="text-red-600 hover:text-red-700 inline-flex items-center gap-2 mb-4">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Competition
        </router-link>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ competition?.title }} - Gallery</h1>
        <p class="text-gray-600">{{ totalSubmissions }} {{ totalSubmissions === 1 ? 'Submission' : 'Submissions' }}</p>
      </div>
    </div>

    <!-- Filters & Sort -->
    <div class="bg-white border-b sticky top-0 z-10 shadow-sm">
      <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Search -->
          <div class="flex-1">
            <div class="relative">
              <input 
                v-model="searchQuery"
                @input="debouncedSearch"
                type="text" 
                placeholder="Search by title or photographer..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Sort -->
          <div class="flex gap-2">
            <select 
              v-model="sortBy"
              @change="fetchSubmissions"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white"
            >
              <option value="created_at">Recently Added</option>
              <option value="most_voted">Most Voted</option>
              <option value="trending">Trending</option>
              <option value="random">Random</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="container mx-auto px-4 py-16 text-center">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
      <p class="text-gray-600 mt-4">Loading submissions...</p>
    </div>

    <!-- Content after loading -->
    <div v-else>
      <!-- Sponsors Section -->
      <div v-if="competition?.sponsors && competition.sponsors.length > 0" class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 py-6">
          <div class="text-center mb-4">
            <h3 class="text-sm font-semibold text-gray-600 inline-flex items-center gap-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              PROUDLY SPONSORED BY
            </h3>
          </div>
          <div class="flex flex-wrap items-center justify-center gap-6">
            <a 
              v-for="sponsor in competition.sponsors.filter(s => s.is_active)"
              :key="sponsor.id"
              :href="sponsor.website_url"
              target="_blank"
              rel="noopener noreferrer"
              :class="[
                'flex items-center justify-center p-4 rounded-xl transition-all hover:scale-105 shadow-md hover:shadow-lg',
                sponsor.tier === 'platinum' ? 'bg-gradient-to-br from-slate-100 to-slate-200 border-2 border-slate-400' :
                sponsor.tier === 'gold' ? 'bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-yellow-400' :
                sponsor.tier === 'silver' ? 'bg-gradient-to-br from-gray-100 to-gray-200 border-2 border-gray-400' :
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
                  sponsor.tier === 'platinum' ? 'h-14 w-36' :
                  sponsor.tier === 'gold' ? 'h-12 w-32' :
                  sponsor.tier === 'silver' ? 'h-10 w-28' :
                  'h-9 w-24'
                ]"
              />
              <span v-else class="text-gray-700 font-semibold text-sm">{{ sponsor.name }}</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Gallery Grid -->
      <div class="container mx-auto px-4 py-8">
      <!-- Empty State -->
      <div v-if="submissions.length === 0" class="text-center py-16">
        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Submissions Yet</h3>
        <p class="text-gray-600 mb-6">Be the first to submit a photo to this competition!</p>
        <router-link 
          v-if="isAuthenticated"
          :to="`/competitions/${competition?.slug}/submit`" 
          class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors"
        >
          Submit Your Photo
        </router-link>
      </div>

      <!-- Submissions Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div 
          v-for="submission in submissions" 
          :key="submission.id"
          @click="viewSubmission(submission)"
          class="group cursor-pointer bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow"
        >
          <!-- Image -->
          <div class="relative aspect-square overflow-hidden bg-gray-200">
            <img 
              :src="submission.thumbnail_url || submission.image_url" 
              :alt="submission.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
              loading="lazy"
            />
            
            <!-- Overlay on hover -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
              <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                <div class="flex items-center gap-4 text-sm">
                  <div class="flex items-center gap-1">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                    {{ submission.vote_count }}
                  </div>
                  <div class="flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ submission.view_count }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Winner Badge -->
            <div v-if="submission.is_winner" class="absolute top-3 right-3">
              <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                {{ submission.winner_position }}
              </span>
            </div>
          </div>

          <!-- Info -->
          <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-red-600 transition-colors">
              {{ submission.title }}
            </h3>
            <p class="text-sm text-gray-600 mb-2 line-clamp-1">
              by {{ submission.photographer?.name || 'Anonymous' }}
            </p>
            <div class="flex items-center justify-between text-sm">
              <div class="flex items-center gap-3 text-gray-500">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  {{ submission.view_count }}
                </span>
              </div>
              
              <!-- Vote Button -->
              <button 
                @click.stop="toggleVote(submission)"
                :disabled="votingInProgress[submission.id]"
                :class="[
                  'flex items-center gap-1 px-3 py-1.5 rounded-full font-medium transition-all',
                  submission.has_voted 
                    ? 'bg-red-600 text-white hover:bg-red-700' 
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                <svg class="w-4 h-4" :fill="submission.has_voted ? 'currentColor' : 'none'" :stroke="submission.has_voted ? 'none' : 'currentColor'" viewBox="0 0 20 20">
                  <path v-if="submission.has_voted" fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                {{ submission.vote_count }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex justify-center">
        <div class="flex gap-2">
          <button 
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          
          <div class="flex gap-1">
            <button 
              v-for="page in visiblePages" 
              :key="page"
              @click="changePage(page)"
              :class="[
                'px-4 py-2 border rounded-lg',
                page === pagination.current_page 
                  ? 'bg-red-600 text-white border-red-600' 
                  : 'border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </div>

          <button 
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';

const route = useRoute();
const router = useRouter();

const competition = ref(null);
const submissions = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const sortBy = ref('created_at');
const pagination = ref(null);
const totalSubmissions = ref(0);
const votingInProgress = ref({});

const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const isAuthenticated = computed(() => !!user.value);

let searchTimeout = null;

onMounted(async () => {
  await fetchCompetition();
  await fetchSubmissions();
});

const fetchCompetition = async () => {
  try {
    const { data } = await api.get(`/competitions/${route.params.slug}`);
    competition.value = data.data;
  } catch (error) {
    console.error('Error fetching competition:', error);
    router.push('/competitions');
  }
};

const fetchSubmissions = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
      sort_by: sortBy.value,
      search: searchQuery.value || undefined
    };

    const { data } = await api.get(`/competitions/${competition.value.id}/submissions`, { params });
    
    submissions.value = data.data.data || data.data;
    
    // Check vote status for each submission if authenticated
    if (isAuthenticated.value) {
      await checkVoteStatus();
    }
    
    pagination.value = {
      current_page: data.data.current_page,
      last_page: data.data.last_page,
      per_page: data.data.per_page,
      total: data.data.total
    };
    totalSubmissions.value = pagination.value.total;
  } catch (error) {
    console.error('Error fetching submissions:', error);
  } finally {
    loading.value = false;
  }
};

const checkVoteStatus = async () => {
  try {
    await Promise.all(
      submissions.value.map(async (submission) => {
        const { data } = await api.get(
          `/competitions/${competition.value.id}/submissions/${submission.id}/vote-status`
        );
        submission.has_voted = data.data.has_voted;
      })
    );
  } catch (error) {
    console.error('Error checking vote status:', error);
  }
};

const toggleVote = async (submission) => {
  if (!isAuthenticated.value) {
    alert('Please login to vote');
    router.push('/login');
    return;
  }
  
  votingInProgress.value[submission.id] = true;
  
  try {
    if (submission.has_voted) {
      // Unvote
      await api.delete(`/competitions/${competition.value.id}/submissions/${submission.id}/vote`);
      submission.has_voted = false;
      submission.vote_count--;
    } else {
      // Vote
      const { data } = await api.post(
        `/competitions/${competition.value.id}/submissions/${submission.id}/vote`
      );
      submission.has_voted = true;
      submission.vote_count = data.data.vote_count;
    }
  } catch (error) {
    console.error('Error voting:', error);
    alert(error.response?.data?.message || 'Failed to process vote');
  } finally {
    votingInProgress.value[submission.id] = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchSubmissions();
  }, 500);
};

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchSubmissions(page);
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const visiblePages = computed(() => {
  if (!pagination.value) return [];
  
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const delta = 2;
  const range = [];
  
  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i);
  }
  
  if (current - delta > 2) {
    range.unshift('...');
  }
  if (current + delta < last - 1) {
    range.push('...');
  }
  
  range.unshift(1);
  if (last > 1) {
    range.push(last);
  }
  
  return range.filter((v, i, a) => a.indexOf(v) === i && v !== '...' || v === '...');
});

const viewSubmission = (submission) => {
  router.push(`/competitions/${competition.value.slug}/submissions/${submission.id}`);
};
</script>
