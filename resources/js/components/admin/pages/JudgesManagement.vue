<template>
  <AdminLayout 
    page-title="Judges Management"
    page-description="Manage competition judges and their assignments"
    :show-breadcrumbs="true"
  >
    <!-- Edit Modal -->
    <BaseModal 
      :is-open="showModal"
      :title="editingJudge ? 'Edit Judge' : 'Add New Judge'"
      :is-loading="isSubmitting"
      @close="showModal = false"
      @submit="handleSaveJudge"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
          <input
            v-model="formData.name"
            type="text"
            placeholder="Judge name"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Expertise Categories</label>
          <textarea
            v-model="formData.expertise_categories"
            placeholder="Photography, Lighting, Composition (comma-separated)"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
            rows="2"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
          <textarea
            v-model="formData.bio"
            placeholder="Judge biography"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
            rows="3"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="formData.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
            <option value="active">
              Active
            </option>
            <option value="inactive">
              Inactive
            </option>
          </select>
        </div>
      </div>
    </BaseModal>

    <div class="space-y-6">
      <!-- SECTION 1: FILTERS & ACTIONS -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="flex flex-col sm:flex-row gap-4 flex-1">
            <!-- Search -->
            <div class="flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search judges by name or email..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
              >
            </div>

            <!-- Status Filter -->
            <select
              v-model="statusFilter"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
            >
              <option value="">
                All Status
              </option>
              <option value="active">
                Active
              </option>
              <option value="inactive">
                Inactive
              </option>
              <option value="pending">
                Pending
              </option>
            </select>

            <!-- Competition Filter -->
            <select
              v-model="competitionFilter"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
            >
              <option value="">
                All Competitions
              </option>
              <option
                v-for="comp in competitions"
                :key="comp.id"
                :value="comp.id"
              >
                {{ comp.name }}
              </option>
            </select>
          </div>

          <button
            class="px-6 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium whitespace-nowrap transition-colors"
            @click="openAddModal"
          >
            + Add Judge
          </button>
        </div>
      </div>

      <!-- SECTION 2: JUDGES TABLE -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Loading State -->
        <div
          v-if="isLoading"
          class="p-8 text-center"
        >
          <svg
            class="animate-spin h-8 w-8 mx-auto text-amber-500"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
          </svg>
          <p class="mt-4 text-gray-600">
            Loading judges...
          </p>
        </div>

        <!-- Table -->
        <div
          v-else-if="filteredJudges.length > 0"
          class="overflow-x-auto"
        >
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Judge
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Expertise
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Competitions
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Joined
                </th>
                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="judge in filteredJudges"
                :key="judge.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <!-- Name -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                      {{ judge.user?.name?.charAt(0) || 'J' }}
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">
                        {{ judge.user?.name }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ judge.user?.email }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- Expertise -->
                <td class="px-6 py-4 text-sm">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="cat in judge.expertise_categories?.slice(0, 2)"
                      :key="cat"
                      class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium"
                    >
                      {{ cat }}
                    </span>
                    <span
                      v-if="judge.expertise_categories?.length > 2"
                      class="text-xs text-gray-500"
                    >
                      +{{ judge.expertise_categories.length - 2 }}
                    </span>
                  </div>
                </td>

                <!-- Status -->
                <td class="px-6 py-4">
                  <span :class="getStatusBadge(judge.status)">
                    {{ judge.status }}
                  </span>
                </td>

                <!-- Competitions -->
                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                  {{ judge.assigned_competitions || 0 }}
                </td>

                <!-- Joined Date -->
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ formatDate(judge.created_at) }}
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                      title="Edit judge"
                      @click="editJudge(judge)"
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
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                      </svg>
                    </button>

                    <button
                      class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                      title="Assign competitions"
                      @click="assignCompetitions(judge)"
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
                          d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                        />
                      </svg>
                    </button>

                    <button
                      :class="[
                        'p-2 rounded-lg transition-colors',
                        judge.status === 'active'
                          ? 'text-orange-600 hover:bg-orange-50'
                          : 'text-green-600 hover:bg-green-50'
                      ]"
                      :title="judge.status === 'active' ? 'Deactivate judge' : 'Activate judge'"
                      @click="toggleStatus(judge)"
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
                          d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"
                        />
                      </svg>
                    </button>

                    <button
                      class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      title="Delete judge"
                      @click="deleteJudge(judge)"
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
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="p-12 text-center"
        >
          <svg
            class="w-12 h-12 mx-auto text-gray-400 mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17.657 18.657L13.414 14.414m4.243 4.243l-6.364-6.364m6.364 6.364l4.243 4.243m-6.364-6.364l-4.243-4.243"
            />
          </svg>
          <p class="text-gray-600 font-medium">
            No judges found
          </p>
          <p class="text-sm text-gray-500 mt-1">
            Create your first judge by clicking the "Add Judge" button above.
          </p>
        </div>
      </div>

      <!-- SECTION 3: PAGINATION -->
      <div
        v-if="filteredJudges.length > itemsPerPage"
        class="flex items-center justify-between"
      >
        <p class="text-sm text-gray-600">
          Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredJudges.length) }} of {{ filteredJudges.length }}
        </p>
        <div class="flex gap-2">
          <button
            :disabled="currentPage === 1"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="currentPage--"
          >
            Previous
          </button>
          <button
            :disabled="currentPage * itemsPerPage >= filteredJudges.length"
            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            @click="currentPage++"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from '../../AdminLayout.vue';
import BaseModal from '../modals/BaseModal.vue';
import { formatDate as formatDateValue } from '../../../utils/formatters';

// State
const judges = ref([]);
const competitions = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const searchQuery = ref('');
const statusFilter = ref('');
const competitionFilter = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

// Modal state
const showModal = ref(false);
const editingJudge = ref(null);
const formData = ref({
  name: '',
  expertise_categories: '',
  bio: '',
  status: 'active',
});

// Injected methods
const addAlert = inject('addAlert', null);

// Computed - Filtered judges
const filteredJudges = computed(() => {
  let filtered = judges.value;

  // Search filter
  if (searchQuery.value) {
    filtered = filtered.filter(j =>
      j.user?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      j.user?.email?.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  // Status filter
  if (statusFilter.value) {
    filtered = filtered.filter(j => j.status === statusFilter.value);
  }

  // Competition filter
  if (competitionFilter.value) {
    filtered = filtered.filter(j =>
      j.assigned_competitions > 0
    );
  }

  // Pagination
  const start = (currentPage.value - 1) * itemsPerPage;
  return filtered.slice(start, start + itemsPerPage);
});

// Fetch judges
const fetchJudges = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/judges', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });

    if (response.ok) {
      const data = await response.json();
      judges.value = data.data || [];
    }
  } catch (error) {
    console.error('Error fetching judges:', error);
    if (addAlert) addAlert('Failed to load judges', 'error');
  } finally {
    isLoading.value = false;
  }
};

// Fetch competitions
const fetchCompetitions = async () => {
  try {
    const response = await fetch('/api/v1/admin/competitions', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });

    if (response.ok) {
      const data = await response.json();
      competitions.value = data.data?.slice(0, 10) || [];
    }
  } catch (error) {
    console.error('Error fetching competitions:', error);
  }
};

// Format date
const formatDate = (date) => {
  if (!date) return '';
  return formatDateValue(date);
};

// Get status badge
const getStatusBadge = (status) => {
  const classes = {
    active: 'px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold',
    inactive: 'px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold',
    pending: 'px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold'
  };
  return classes[status] || classes.inactive;
};

// Actions
const openAddModal = () => {
  editingJudge.value = null;
  formData.value = { name: '', expertise_categories: '', bio: '', status: 'active' };
  showModal.value = true;
};

const editJudge = (judge) => {
  editingJudge.value = judge;
  formData.value = {
    name: judge.user?.name || '',
    expertise_categories: (judge.expertise_categories || []).join(', '),
    bio: judge.bio || '',
    status: judge.status || 'active',
  };
  showModal.value = true;
};

const handleSaveJudge = async () => {
  isSubmitting.value = true;
  try {
    const url = editingJudge.value 
      ? `/api/v1/admin/judges/${editingJudge.value.id}`
      : '/api/v1/admin/judges';
    
    const method = editingJudge.value ? 'PUT' : 'POST';
    
    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        expertise_categories: formData.value.expertise_categories.split(',').map(c => c.trim()),
        bio: formData.value.bio,
        status: formData.value.status,
      })
    });

    if (response.ok) {
      const data = await response.json();
      if (editingJudge.value) {
        const index = judges.value.findIndex(j => j.id === editingJudge.value.id);
        judges.value[index] = data.data;
        if (addAlert) addAlert('Judge updated successfully', 'success');
      } else {
        judges.value.unshift(data.data);
        if (addAlert) addAlert('Judge created successfully', 'success');
      }
      showModal.value = false;
    }
  } catch (error) {
    console.error('Error saving judge:', error);
    if (addAlert) addAlert('Failed to save judge', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

const assignCompetitions = (judge) => {
  console.log('Assign competitions to:', judge);
  if (addAlert) addAlert(`Assign competitions to ${judge.user?.name} - Coming soon`, 'info');
};

const toggleStatus = async (judge) => {
  const newStatus = judge.status === 'active' ? 'inactive' : 'active';
  try {
    const response = await fetch(`/api/v1/admin/judges/${judge.id}/status`, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ status: newStatus })
    });

    if (response.ok) {
      judge.status = newStatus;
      if (addAlert) addAlert(`Judge ${newStatus} successfully`, 'success');
    }
  } catch (error) {
    console.error('Error updating judge status:', error);
    if (addAlert) addAlert('Failed to update judge status', 'error');
  }
};

const deleteJudge = async (judge) => {
  if (!confirm(`Are you sure you want to delete ${judge.user?.name}?`)) return;

  try {
    const response = await fetch(`/api/v1/admin/judges/${judge.id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });

    if (response.ok) {
      judges.value = judges.value.filter(j => j.id !== judge.id);
      if (addAlert) addAlert('Judge deleted successfully', 'success');
    }
  } catch (error) {
    console.error('Error deleting judge:', error);
    if (addAlert) addAlert('Failed to delete judge', 'error');
  }
};

// Lifecycle
onMounted(() => {
  fetchJudges();
  fetchCompetitions();
});
</script>
