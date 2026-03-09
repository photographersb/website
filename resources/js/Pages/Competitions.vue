<template>
  <div class="min-h-screen bg-[#f7f2ee] text-[#1d1014]">
    <!-- Hero Section with Parallax Effect -->
    <section class="relative overflow-hidden bg-[#1b0b12] text-white">
      <!-- Decorative Background Elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(245,158,11,0.28)_0,_transparent_55%)]" />
        <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-[#f3b35a]/30 blur-3xl" />
        <div class="absolute -bottom-28 -left-24 h-80 w-80 rounded-full bg-[#c46b7a]/20 blur-3xl" />
      </div>

      <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 max-w-7xl pt-10 pb-16 md:pt-10 md:pb-24 relative z-10">
        <!-- Logo/Brand Section -->
        <div class="text-center mt-5 mb-4 sm:mb-5 md:mb-6 lg:mb-8">
          <div class="inline-flex mb-2 sm:mb-3 px-3 sm:px-4 py-1 sm:py-1.5 bg-white bg-opacity-10 backdrop-blur-sm rounded-full border border-white border-opacity-20">
            <p class="text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-2 justify-center">
              <svg
                class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
              </svg>
              A Project by <a
                href="https://somogrobangladesh.com/"
                target="_blank"
                rel="noopener"
                class="underline hover:text-white/80 transition-colors"
              >Somogro Bangladesh</a>
            </p>
          </div>
        </div>

        <h1 class="text-3xl sm:text-4xl md:text-6xl font-semibold font-serif mb-4 text-center tracking-tight animate-fade-in">
          Photography Competitions
        </h1>
        <p class="text-lg md:text-xl text-gray-100 max-w-3xl mx-auto text-center leading-relaxed animate-fade-in-delay">
          Showcase your talent, compete with the best, win prizes!
        </p>

        <!-- Stats Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mt-10 animate-fade-in-delay-2">
          <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-4 sm:p-6 text-center border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold">
              {{ stats.active_competitions }}
            </div>
            <div class="text-sm md:text-base text-gray-200 mt-1">
              Active
            </div>
          </div>
          <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-4 sm:p-6 text-center border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold">
              ৳{{ stats.total_prize_pool }}
            </div>
            <div class="text-sm md:text-base text-gray-200 mt-1">
              Prize Pool
            </div>
          </div>
          <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-4 sm:p-6 text-center border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold">
              {{ stats.total_submissions }}
            </div>
            <div class="text-sm md:text-base text-gray-200 mt-1">
              Submissions
            </div>
          </div>
          <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-4 sm:p-6 text-center border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
            <div class="text-2xl sm:text-3xl md:text-4xl font-bold">
              {{ stats.total_participants }}
            </div>
            <div class="text-sm md:text-base text-gray-200 mt-1">
              Photographers
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 max-w-7xl py-12 mt-5">
      <!-- Filters -->
      <div class="sb-ui-card sb-ui-card--feature bg-white/90 backdrop-blur p-4 sm:p-5 md:p-6 mb-6 sm:mb-8">
        <div class="flex items-center gap-3 sm:hidden">
          <div class="relative flex-1">
            <input
              v-model="filters.theme"
              type="text"
              placeholder="Search competitions..."
              class="sb-ui-input pl-10 pr-4 py-2.5"
              @input="applyFilters"
            >
            <svg
              class="absolute left-3 top-3 w-5 h-5 text-[#7a1f2b]/60"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>
          <button
            class="sb-ui-btn sb-ui-btn--secondary px-4 py-2.5 text-[#1b0b12] font-medium"
            @click="showFilters = !showFilters"
          >
            Filters
          </button>
        </div>

        <div :class="[showFilters ? 'block' : 'hidden sm:block', 'mt-3 sm:mt-0']">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
            <div class="hidden sm:block">
              <label class="block text-sm font-medium text-[#1b0b12] mb-2">Search</label>
              <input
                v-model="filters.theme"
                type="text"
                placeholder="Search theme..."
                class="sb-ui-input px-4 py-2"
                @input="applyFilters"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-[#1b0b12] mb-2">Status</label>
              <select
                v-model="filters.status"
                class="sb-ui-select px-4 py-2"
                @change="applyFilters"
              >
                <option value="">
                  All Competitions
                </option>
                <option value="active">
                  Active
                </option>
                <option value="draft">
                  Upcoming
                </option>
                <option value="judging">
                  Judging
                </option>
                <option value="completed">
                  Completed
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-[#1b0b12] mb-2">Entry Fee</label>
              <select
                v-model="filters.is_paid"
                class="sb-ui-select px-4 py-2"
                @change="applyFilters"
              >
                <option value="">
                  All Types
                </option>
                <option value="0">
                  Free Entry
                </option>
                <option value="1">
                  Paid Entry
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-[#1b0b12] mb-2">Category</label>
              <select
                v-model="filters.category"
                class="sb-ui-select px-4 py-2"
                @change="applyFilters"
              >
                <option value="">
                  All Categories
                </option>
                <option
                  v-for="category in categoryOptions"
                  :key="category"
                  :value="category"
                >
                  {{ category }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-[#1b0b12] mb-2">Sort By</label>
              <select
                v-model="filters.sort"
                class="sb-ui-select px-4 py-2"
                @change="applyFilters"
              >
                <option value="deadline">
                  Deadline (Soon)
                </option>
                <option value="prize">
                  Prize Pool (High)
                </option>
                <option value="submissions">
                  Most Submissions
                </option>
                <option value="newest">
                  Newest First
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8"
        role="status"
        aria-live="polite"
      >
        <LoadingSkeleton
          v-for="n in 6"
          :key="n"
          type="card"
        />
      </div>

      <!-- Empty State -->
      <EmptyState
        v-else-if="competitionSections.length === 0"
        icon="trophy"
        title="No Competitions Found"
        description="Try adjusting your filters or check back later for new photography competitions."
        variant="burgundy"
      />

      <!-- Competitions Grid -->
      <div
        v-else
        class="space-y-8"
      >
        <section
          v-for="section in competitionSections"
          :key="section.key"
        >
          <div class="flex items-center justify-between gap-3 mb-4">
            <div>
              <h2 class="text-xl sm:text-2xl font-semibold text-[#1b0b12]">{{ section.title }}</h2>
              <p class="text-sm text-gray-600">{{ section.description }}</p>
            </div>
            <span class="sb-ui-badge sb-ui-badge--neutral">{{ section.items.length }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
            <div
              v-for="competition in section.items"
              :key="`${section.key}-${competition.id}`"
              class="sb-ui-card sb-ui-card--interactive bg-white/90 backdrop-blur border-[#eadfd7] hover:shadow-xl transition-all duration-300 overflow-hidden group cursor-pointer hover:-translate-y-1"
              role="button"
              tabindex="0"
              :aria-label="`Open competition ${competition.title}`"
              @click="viewCompetition(competition)"
              @keydown.enter="viewCompetition(competition)"
              @keydown.space.prevent="viewCompetition(competition)"
            >
          <!-- Image -->
          <div class="relative h-56 overflow-hidden">
            <img
              :src="competition.hero_image || competition.banner_image || 'https://placehold.co/400x250/8E0E3F/FFFFFF?text=Competition'"
              :alt="competition.title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
              @error="$event.target.src='https://placehold.co/400x250/8E0E3F/FFFFFF?text=No+Image'"
              loading="lazy"
              decoding="async"
            >
            
            <!-- Countdown Timer Overlay (if active and deadline within 7 days) -->
            <div
              v-if="competition.status === 'active' && isDeadlineSoon(competition.submission_deadline)"
              class="absolute bottom-3 right-3 bg-[#7a1f2b] text-white px-3 py-2 rounded-lg font-bold shadow-lg"
            >
              <CountdownTimer
                :deadline="competition.submission_deadline"
                :show-icon="true"
                format="short"
              />
            </div>
            
            <div class="absolute top-4 right-4">
              <span :class="`sb-ui-badge ${getStatusBadgeClass(competition.status)}`">
                {{ formatStatus(competition.status) }}
              </span>
            </div>
            <div
              v-if="competition.is_featured"
              class="absolute top-4 left-4"
            >
              <span class="sb-ui-badge bg-amber-400 text-[#1b0b12] flex items-center gap-1">
                <svg
                  class="w-4 h-4"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Featured
              </span>
            </div>
          </div>

          <!-- Content -->
          <div class="p-4 sm:p-5 md:p-6">
            <h3 class="text-lg sm:text-xl font-semibold font-serif text-[#1b0b12] mb-2 group-hover:text-[#7a1f2b] transition-colors">
              {{ competition.title }}
            </h3>
            <p
              v-if="competition.theme"
              class="text-sm text-[#7a1f2b] font-medium mb-2"
            >
              {{ competition.theme }}
            </p>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
              {{ competition.description }}
            </p>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 pb-4 border-b">
              <div>
                <div class="text-xl sm:text-2xl font-bold text-[#7a1f2b]">
                  ৳{{ formatNumber(competition.total_prize_pool) }}
                </div>
                <div class="text-xs sm:text-sm text-gray-500">
                  Prize Pool
                </div>
              </div>
              <div>
                <div class="text-xl sm:text-2xl font-bold text-gray-900">
                  {{ competition.total_submissions }}
                </div>
                <div class="text-xs sm:text-sm text-gray-500">
                  Submissions
                </div>
              </div>
            </div>

            <!-- Details -->
            <div class="space-y-2 text-xs sm:text-sm mb-5">
              <div class="flex items-center justify-between">
                <span class="text-gray-600 flex items-center gap-1">
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
                  Deadline
                </span>
                <span
                  class="font-semibold"
                  :class="isDeadlineSoon(competition.submission_deadline) ? 'text-red-600' : 'text-gray-900'"
                >
                  {{ formatDate(competition.submission_deadline) }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-600 flex items-center gap-1">
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
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  Entry Fee
                </span>
                <span class="font-semibold">
                  {{ competition.is_paid_competition ? `৳${competition.participation_fee}` : 'Free' }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-600 flex items-center gap-1">
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
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                    />
                  </svg>
                  Max Entries
                </span>
                <span class="font-semibold">{{ competition.max_submissions_per_user }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-gray-600 flex items-center gap-1">
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
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                  </svg>
                  Judges
                </span>
                <span class="font-semibold">{{ getJudgeCount(competition) }}</span>
              </div>
            </div>

            <!-- Action Button -->
            <button
              class="w-full sb-ui-btn justify-center !rounded-lg font-semibold transition-all duration-200"
              :class="getActionButtonClass(competition.status)"
              @click.stop="viewCompetition(competition)"
            >
              {{ getActionButtonText(competition.status) }}
            </button>
          </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Pagination -->
      <div
        v-if="totalPages > 1"
        class="flex flex-wrap justify-center items-center gap-2 mt-8 sm:mt-12"
      >
        <button
          :disabled="currentPage === 1"
          class="sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base"
          @click="goToPage(1)"
        >
          <span class="hidden sm:inline">First</span>
          <span class="sm:hidden">«</span>
        </button>
        <button
          :disabled="currentPage === 1"
          class="sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base"
          @click="previousPage"
        >
          <span class="hidden sm:inline">Previous</span>
          <span class="sm:hidden">‹</span>
        </button>
        <div class="flex gap-1 sm:gap-2">
          <button
            v-for="page in visiblePages"
            :key="page"
            :class="[
              'sb-ui-btn sb-ui-btn--sm px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base',
              page === currentPage ? 'sb-ui-btn--primary' : 'sb-ui-btn--secondary'
            ]"
            @click="goToPage(page)"
          >
            {{ page }}
          </button>
        </div>
        <button
          :disabled="currentPage === totalPages"
          class="sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base"
          @click="nextPage"
        >
          <span class="hidden sm:inline">Next</span>
          <span class="sm:hidden">›</span>
        </button>
        <button
          :disabled="currentPage === totalPages"
          class="sb-ui-btn sb-ui-btn--secondary sb-ui-btn--sm px-3 sm:px-4 py-2 rounded-lg text-sm sm:text-base"
          @click="goToPage(totalPages)"
        >
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
import api from '@/api';
import LoadingSkeleton from '../components/ui/LoadingSkeleton.vue';
import EmptyState from '../components/ui/EmptyState.vue';
import CountdownTimer from '../components/ui/CountdownTimer.vue';
import { formatDate as formatDateValue, formatNumber as formatNumberValue } from '../utils/formatters';

const router = useRouter();
const competitions = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const total = ref(0);
const showFilters = ref(false);

const stats = ref({
  active_competitions: 0,
  total_prize_pool: 0,
  total_submissions: 0,
  total_participants: 0,
});

const filters = ref({
  status: '',
  is_paid: '',
  category: '',
  theme: '',
  sort: 'deadline',
});

const normalizeCategory = (competition) => {
  return competition?.category?.name
    || competition?.category_name
    || competition?.category
    || competition?.genre
    || competition?.type
    || '';
};

const categoryOptions = computed(() => {
  const values = competitions.value
    .map((competition) => normalizeCategory(competition))
    .filter((value) => String(value || '').trim().length > 0);
  return [...new Set(values)].sort((left, right) => left.localeCompare(right));
});

const matchesCategory = (competition) => {
  if (!filters.value.category) return true;
  return normalizeCategory(competition) === filters.value.category;
};

const filteredCompetitions = computed(() => {
  return competitions.value.filter((competition) => matchesCategory(competition));
});

const activeCompetitions = computed(() => {
  return filteredCompetitions.value.filter((competition) => ['active', 'judging'].includes(String(competition.status || '').toLowerCase()));
});

const upcomingCompetitions = computed(() => {
  return filteredCompetitions.value.filter((competition) => {
    const status = String(competition.status || '').toLowerCase();
    return status === 'draft' || status === 'upcoming';
  });
});

const pastCompetitions = computed(() => {
  return filteredCompetitions.value.filter((competition) => ['completed', 'cancelled', 'archived'].includes(String(competition.status || '').toLowerCase()));
});

const competitionSections = computed(() => {
  const sections = [];
  if (activeCompetitions.value.length) {
    sections.push({
      key: 'active',
      title: 'Active Competitions',
      description: 'Open for submissions, voting, or judging right now.',
      items: activeCompetitions.value,
    });
  }
  if (upcomingCompetitions.value.length) {
    sections.push({
      key: 'upcoming',
      title: 'Upcoming Competitions',
      description: 'Prepare your entries before submission opens.',
      items: upcomingCompetitions.value,
    });
  }
  if (pastCompetitions.value.length) {
    sections.push({
      key: 'past',
      title: 'Past Competitions',
      description: 'Explore completed contests and results.',
      items: pastCompetitions.value,
    });
  }
  if (!sections.length && filteredCompetitions.value.length) {
    sections.push({
      key: 'all',
      title: 'Competitions',
      description: 'All competitions matching your filters.',
      items: filteredCompetitions.value,
    });
  }
  return sections;
});

const fetchStats = async () => {
  try {
    const { data } = await api.get('/competitions/stats');
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

    const { data } = await api.get(`/competitions?${params}`);

    if (data.status === 'success') {
      competitions.value = data.data;
      totalPages.value = data.pagination?.last_page || 1;
      currentPage.value = page;
      total.value = data.pagination?.total || 0;
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

const getJudgeCount = (competition) => {
  const direct = Number(competition?.judges_count ?? competition?.judge_count ?? competition?.total_judges);
  if (Number.isFinite(direct) && direct >= 0) return direct;
  if (Array.isArray(competition?.judges)) return competition.judges.length;
  if (Array.isArray(competition?.judgeProfiles)) return competition.judgeProfiles.length;
  if (Array.isArray(competition?.judge_profiles)) return competition.judge_profiles.length;
  return 0;
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
    draft: 'sb-ui-badge--neutral',
    active: 'sb-ui-badge--success',
    judging: 'sb-ui-badge--warning',
    completed: 'sb-ui-badge--info',
    cancelled: 'sb-ui-badge--danger',
  };
  return classes[status] || 'sb-ui-badge--neutral';
};

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};

const getActionButtonClass = (status) => {
  if (status === 'active') {
    return 'sb-ui-btn--primary';
  } else if (status === 'completed') {
    return 'bg-[#1b0b12] text-white hover:bg-[#14070d] border-transparent';
  } else if (status === 'judging') {
    return 'bg-amber-500 text-white hover:bg-amber-600 border-transparent';
  } else {
    return 'sb-ui-btn--secondary text-[#7a1f2b] cursor-not-allowed';
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
  return formatDateValue(date);
};

const formatNumber = (num) => {
  return formatNumberValue(num);
};

const isDeadlineSoon = (deadline) => {
  const days = Math.ceil((new Date(deadline) - new Date()) / (1000 * 60 * 60 * 24));
  return days <= 7 && days >= 0;
};

const setMetaTag = (attribute, key, content) => {
  if (!content) return;
  let tag = document.head.querySelector(`meta[${attribute}="${key}"]`);
  if (!tag) {
    tag = document.createElement('meta');
    tag.setAttribute(attribute, key);
    document.head.appendChild(tag);
  }
  tag.setAttribute('content', content);
};

const updatePageMeta = () => {
  if (typeof window === 'undefined') return;
  const title = 'Photography Competitions | Photographar SB';
  const description = 'Browse active, upcoming, and past photography competitions with prizes and public voting.';
  const canonical = `${window.location.origin}/competitions`;

  document.title = title;
  setMetaTag('name', 'description', description);
  setMetaTag('property', 'og:title', title);
  setMetaTag('property', 'og:description', description);
  setMetaTag('property', 'og:url', canonical);
  setMetaTag('property', 'og:type', 'website');
};

onMounted(() => {
  updatePageMeta();
  fetchStats();
  fetchCompetitions();
});
</script>
