<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
      <h1 class="text-3xl font-bold mb-8">Photography Competitions</h1>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Status</label>
            <select v-model="filters.status" class="w-full border rounded px-3 py-2">
              <option value="">All Competitions</option>
              <option value="upcoming">Upcoming</option>
              <option value="active">Active</option>
              <option value="voting">Voting Phase</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Category</label>
            <select v-model="filters.category" class="w-full border rounded px-3 py-2">
              <option value="">All Categories</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                {{ cat.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Sort By</label>
            <select v-model="filters.sort" class="w-full border rounded px-3 py-2">
              <option value="newest">Newest First</option>
              <option value="prize">Highest Prize</option>
              <option value="entries">Most Entries</option>
            </select>
          </div>
        </div>
        <button
          @click="fetchCompetitions"
          class="mt-4 bg-burgundy text-white px-6 py-2 rounded hover:bg-[#6F112D]"
        >
          Apply Filters
        </button>
      </div>

      <!-- Competitions Grid -->
      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-600">Loading competitions...</p>
      </div>

      <div v-else-if="competitions.length === 0" class="text-center py-12">
        <p class="text-gray-600">No competitions found</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="competition in competitions"
          :key="competition.id"
          class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition cursor-pointer"
          @click="viewCompetition(competition)"
        >
          <img
            :src="competition.cover_image_url || 'https://placehold.co/400x250/8E0E3F/FFFFFF?text=Competition'"
            :alt="competition.title"
            class="w-full h-48 object-cover"
          />
          <div class="p-6">
            <div class="flex justify-between items-start mb-2">
              <h3 class="text-xl font-bold">{{ competition.title }}</h3>
              <span
                :class="`px-3 py-1 rounded-full text-xs ${getStatusBadgeClass(competition.status)}`"
              >
                {{ competition.status }}
              </span>
            </div>
            <p class="text-gray-600 text-sm mb-4">{{ competition.description }}</p>

            <div class="space-y-2 mb-4 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Prize Pool</span>
                <span class="font-bold text-burgundy">৳{{ competition.prize_pool }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Submissions</span>
                <span class="font-semibold">{{ competition.submission_count }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Deadline</span>
                <span class="font-semibold">{{ formatDate(competition.submission_deadline) }}</span>
              </div>
            </div>

            <button
              @click.stop="participateInCompetition(competition)"
              class="w-full bg-burgundy text-white py-2 rounded hover:bg-[#6F112D]"
              :disabled="competition.status === 'completed'"
            >
              {{ competition.status === 'completed' ? 'Ended' : 'View Details' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center gap-2 mt-12">
        <button
          v-if="currentPage > 1"
          @click="previousPage"
          class="px-4 py-2 border rounded hover:bg-gray-100"
        >
          Previous
        </button>
        <span class="px-4 py-2">Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          v-if="currentPage < totalPages"
          @click="nextPage"
          class="px-4 py-2 border rounded hover:bg-gray-100"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';

const router = useRouter();
const competitions = ref([]);
const categories = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);

const filters = ref({
  status: '',
  category: '',
  sort: 'newest',
});

const fetchCategories = async () => {
  try {
    const { data } = await api.get('/categories');
    if (data.status === 'success') {
      categories.value = data.data;
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const fetchCompetitions = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', currentPage.value);
    
    if (filters.value.status) params.append('status', filters.value.status);
    if (filters.value.category) params.append('category', filters.value.category);
    if (filters.value.sort) params.append('sort', filters.value.sort);

    const { data } = await api.get(`/competitions?${params}`);

    if (data.status === 'success') {
      competitions.value = data.data;
      totalPages.value = data.meta?.last_page || 1;
      currentPage.value = data.meta?.current_page || 1;
    }
  } catch (error) {
    console.error('Error fetching competitions:', error);
    competitions.value = [];
  } finally {
    loading.value = false;
  }
};

const viewCompetition = (competition) => {
  router.push(`/competitions/${competition.slug}`);
};

const participateInCompetition = (competition) => {
  if (competition.status === 'completed') return;
  router.push(`/competitions/${competition.slug}`);
};

const getStatusBadgeClass = (status) => {
  const classes = {
    upcoming: 'bg-blue-100 text-blue-800',
    active: 'bg-green-100 text-green-800',
    voting: 'bg-purple-100 text-purple-800',
    completed: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    fetchCompetitions();
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    fetchCompetitions();
  }
};

onMounted(() => {
  fetchCategories();
  fetchCompetitions();
});
</script>
