<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section with Parallax Effect -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <!-- Decorative Background Elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-white/3 rounded-full blur-2xl"></div>
      </div>

      <div class="container mx-auto px-4 py-16 md:py-24 relative z-10">
        <!-- Logo/Brand Section -->
        <div class="text-center mb-8">
          <div class="inline-block mb-4 px-6 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
            <p class="text-sm md:text-base font-medium flex items-center gap-2 justify-center">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
              </svg>
              A Project by <a href="https://somogrobangladesh.com/" target="_blank" rel="noopener" class="underline hover:text-white/80 transition-colors">Somogro Bangladesh</a>
            </p>
          </div>
        </div>

        <h1 class="text-4xl md:text-6xl font-bold mb-4 text-center tracking-tight animate-fade-in">
          Photography Competitions
        </h1>
        <p class="text-lg md:text-xl text-gray-100 max-w-3xl mx-auto text-center leading-relaxed animate-fade-in-delay">
          Showcase your talent, compete with the best, win prizes!
        </p>

        <!-- Stats Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mt-10 animate-fade-in-delay-2">
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/15 transition-all">
            <div class="text-3xl md:text-4xl font-bold">{{ stats.active_competitions }}</div>
            <div class="text-sm md:text-base text-gray-200 mt-1">Active</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/15 transition-all">
            <div class="text-3xl md:text-4xl font-bold">৳{{ stats.total_prize_pool }}</div>
            <div class="text-sm md:text-base text-gray-200 mt-1">Prize Pool</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/15 transition-all">
            <div class="text-3xl md:text-4xl font-bold">{{ stats.total_submissions }}</div>
            <div class="text-sm md:text-base text-gray-200 mt-1">Submissions</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20 hover:bg-white/15 transition-all">
            <div class="text-3xl md:text-4xl font-bold">{{ stats.total_participants }}</div>
            <div class="text-sm md:text-base text-gray-200 mt-1">Photographers</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-12">
      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-4 sm:p-5 md:p-6 mb-6 sm:mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select v-model="filters.status" @change="applyFilters" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
              <option value="">All Competitions</option>
              <option value="active">Active</option>
              <option value="draft">Upcoming</option>
              <option value="judging">Judging</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Entry Fee</label>
            <select v-model="filters.is_paid" @change="applyFilters" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
              <option value="">All Types</option>
              <option value="0">Free Entry</option>
              <option value="1">Paid Entry</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
            <input v-model="filters.theme" @input="applyFilters" type="text" placeholder="Search theme..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
            <select v-model="filters.sort" @change="applyFilters" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
              <option value="deadline">Deadline (Soon)</option>
              <option value="prize">Prize Pool (High)</option>
              <option value="submissions">Most Submissions</option>
              <option value="newest">Newest First</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
        <LoadingSkeleton v-for="n in 6" :key="n" type="card" />
      </div>

      <!-- Empty State -->
      <EmptyState
        v-else-if="competitions.length === 0"
        icon="trophy"
        title="No Competitions Found"
        description="Try adjusting your filters or check back later for new photography competitions."
        variant="burgundy"
      />

      <!-- Competitions Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
        <div v-for="competition in competitions" :key="competition.id" class="bg-white rounded-lg shadow hover:shadow-xl transition-all duration-300 overflow-hidden group cursor-pointer" @click="viewCompetition(competition)">
          <!-- Image -->
          <div class="relative h-56 overflow-hidden">
            <img :src="competition.hero_image || competition.banner_image || 'https://placehold.co/400x250/8E0E3F/FFFFFF?text=Competition'" :alt="competition.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" @error="$event.target.src='https://placehold.co/400x250/8E0E3F/FFFFFF?text=No+Image'" />
            
            <!-- Countdown Timer Overlay (if active and deadline within 7 days) -->
            <div v-if="competition.status === 'active' && isDeadlineSoon(competition.submission_deadline)" class="absolute bottom-3 right-3 bg-red-500 text-white px-3 py-2 rounded-lg font-bold shadow-lg">
              <CountdownTimer :deadline="competition.submission_deadline" :showIcon="true" format="short" />
            </div>
            
            <div class="absolute top-4 right-4">
              <span :class="`px-3 py-1 rounded-full text-xs font-semibold ${getStatusBadgeClass(competition.status)}`">
                {{ formatStatus(competition.status) }}
              </span>
            </div>
            <div v-if="competition.is_featured" class="absolute top-4 left-4">
              <span class="px-3 py-1 bg-yellow-500 text-white rounded-full text-xs font-semibold flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Featured
              </span>
            </div>
          </div>

          <!-- Content -->
          <div class="p-4 sm:p-5 md:p-6">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 group-hover:text-red-600 transition-colors">
              {{ competition.title }}
            </h3>
            <p v-if="competition.theme" class="text-sm text-red-600 font-medium mb-2">{{ competition.theme }}</p>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ competition.description }}</p>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 pb-4 border-b">
              <div>
                <div class="text-xl sm:text-2xl font-bold text-red-600">৳{{ formatNumber(competition.total_prize_pool) }}</div>
                <div class="text-xs sm:text-sm text-gray-500">Prize Pool</div>
              </div>
              <div>
                <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ competition.total_submissions }}</div>
                <div class="text-xs sm:text-sm text-gray-500">Submissions</div>
              </div>
            </div>

            <!-- Details -->
            <div class="space-y-2 text-xs sm:text-sm mb-4">
              <div class="flex items-center justify-between">
                <span class="text-gray-600 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Deadline
                </span>
                <span class="font-semibold" :class="isDeadlineSoon(competition.submission_deadline) ? 'text-red-600' : 'text-gray-900'">
                  {{ formatDate(competition.submission_deadline) }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-600 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Entry Fee
                </span>
                <span class="font-semibold">
                  {{ competition.is_paid_competition ? `৳${competition.participation_fee}` : 'Free' }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-600 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  Max Entries
                </span>
                <span class="font-semibold">{{ competition.max_submissions_per_user }}</span>
              </div>
            </div>

            <!-- Action Button -->
            <button @click.stop="viewCompetition(competition)" class="w-full py-3 rounded-lg font-semibold transition-all duration-200" :class="getActionButtonClass(competition.status)">
              {{ getActionButtonText(competition.status) }}
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex flex-wrap justify-center items-center gap-2 mt-8 sm:mt-12">
        <button @click="goToPage(1)" :disabled="currentPage === 1" class="px-3 sm:px-4 py-2 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
          <span class="hidden sm:inline">First</span>
          <span class="sm:hidden">«</span>
        </button>
        <button @click="previousPage" :disabled="currentPage === 1" class="px-3 sm:px-4 py-2 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
          <span class="hidden sm:inline">Previous</span>
          <span class="sm:hidden">‹</span>
        </button>
        <div class="flex gap-1 sm:gap-2">
          <button v-for="page in visiblePages" :key="page" @click="goToPage(page)" :class="`px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base ${page === currentPage ? 'bg-red-600 text-white' : 'border hover:bg-gray-100'}`">
            {{ page }}
          </button>
        </div>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 sm:px-4 py-2 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
          <span class="hidden sm:inline">Next</span>
          <span class="sm:hidden">›</span>
        </button>
        <button @click="goToPage(totalPages)" :disabled="currentPage === totalPages" class="px-3 sm:px-4 py-2 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
          <span class="hidden sm:inline">Last</span>
          <span class="sm:hidden">»</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../bootstrap';
import LoadingSkeleton from '../components/ui/LoadingSkeleton.vue';
import EmptyState from '../components/ui/EmptyState.vue';
import CountdownTimer from '../components/ui/CountdownTimer.vue';

const router = useRouter();
const competitions = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const total = ref(0);

const stats = ref({
  active_competitions: 0,
  total_prize_pool: 0,
  total_submissions: 0,
  total_participants: 0,
});

const filters = ref({
  status: '',
  is_paid: '',
  theme: '',
  sort: 'deadline',
});

const fetchStats = async () => {
  try {
    const { data } = await axios.get('/competitions/stats');
    if (data.status === 'success') {
      stats.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const fetchCompetitions = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', page);
    
    if (filters.value.status) params.append('status', filters.value.status);
    if (filters.value.is_paid !== '') params.append('is_paid', filters.value.is_paid);
    if (filters.value.theme) params.append('theme', filters.value.theme);
    if (filters.value.sort) params.append('sort', filters.value.sort);

    const { data } = await axios.get(`/competitions?${params}`);

    if (data.status === 'success') {
      competitions.value = data.data;
      totalPages.value = Math.ceil(data.meta.total / data.meta.per_page);
      currentPage.value = page;
      total.value = data.meta.total;
    }
  } catch (error) {
    console.error('Error fetching competitions:', error);
    competitions.value = [];
  } finally {
    loading.value = false;
  }
};

const applyFilters = () => {
  fetchCompetitions(1);
};

const viewCompetition = (competition) => {
  router.push(`/competitions/${competition.slug}`);
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    fetchCompetitions(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const previousPage = () => {
  if (currentPage.value > 1) {
    goToPage(currentPage.value - 1);
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    goToPage(currentPage.value + 1);
  }
};

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});

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

const getActionButtonClass = (status) => {
  if (status === 'active') {
    return 'bg-red-600 text-white hover:bg-red-700';
  } else if (status === 'completed') {
    return 'bg-blue-600 text-white hover:bg-blue-700';
  } else if (status === 'judging') {
    return 'bg-purple-600 text-white hover:bg-purple-700';
  } else {
    return 'bg-gray-300 text-gray-600 cursor-not-allowed';
  }
};

const getActionButtonText = (status) => {
  if (status === 'active') {
    return 'Submit Entry';
  } else if (status === 'completed') {
    return 'View Winners';
  } else if (status === 'judging') {
    return 'View Leaderboard';
  } else {
    return 'Coming Soon';
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const formatNumber = (num) => {
  if (!num) return '0';
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

const isDeadlineSoon = (deadline) => {
  const days = Math.ceil((new Date(deadline) - new Date()) / (1000 * 60 * 60 * 24));
  return days <= 7 && days >= 0;
};

onMounted(() => {
  fetchStats();
  fetchCompetitions();
});
</script>
