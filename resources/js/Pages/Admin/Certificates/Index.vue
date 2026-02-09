<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Header -->
    <AdminHeader 
      title="🎓 Certificate Management" 
      subtitle="Manage competition certificates and awards"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />
      
      <!-- Header with Action Button -->
      <div class="flex items-center justify-end">
        <Link 
          href="/admin/certificates/manual-issuance"
          class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-medium flex items-center gap-2"
        >
          <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            />
          </svg>
          Issue Certificate Manually
        </Link>
      </div>

      <!-- Tabs -->
      <div class="flex gap-4 border-b border-gray-200">
        <button
          :class="[
            'px-4 py-3 font-medium border-b-2 transition',
            activeTab === 'all'
              ? 'border-orange-500 text-orange-600'
              : 'border-transparent text-gray-600 hover:text-gray-900'
          ]"
          @click="activeTab = 'all'"
        >
          All Certificates ({{ totalCount }})
        </button>
        <button
          :class="[
            'px-4 py-3 font-medium border-b-2 transition',
            activeTab === 'pending'
              ? 'border-orange-500 text-orange-600'
              : 'border-transparent text-gray-600 hover:text-gray-900'
          ]"
          @click="activeTab = 'pending'"
        >
          Pending Generation ({{ pendingCount }})
        </button>
        <button
          :class="[
            'px-4 py-3 font-medium border-b-2 transition',
            activeTab === 'generated'
              ? 'border-orange-500 text-orange-600'
              : 'border-transparent text-gray-600 hover:text-gray-900'
          ]"
          @click="activeTab = 'generated'"
        >
          Generated ({{ generatedCount }})
        </button>
      </div>

      <!-- Search & Filter -->
      <div class="flex gap-4">
        <input 
          v-model="searchQuery"
          type="text"
          placeholder="Search by photographer name, competition..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
        >
        <select
          v-model="statusFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
        >
          <option value="">
            All Status
          </option>
          <option value="participation">
            Participation
          </option>
          <option value="finalist">
            Finalist
          </option>
          <option value="winner">
            Winner
          </option>
          <option value="merit">
            Merit
          </option>
        </select>
      </div>

      <!-- Certificates Table -->
      <div
        v-if="loading"
        class="bg-white rounded-lg shadow p-12 text-center"
      >
        <p class="text-gray-600">
          Loading certificates...
        </p>
      </div>

      <div
        v-else-if="filteredCertificates.length === 0"
        class="bg-white rounded-lg shadow p-12 text-center"
      >
        <svg
          class="w-16 h-16 text-gray-300 mx-auto mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z"
          />
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
          No Certificates Found
        </h3>
        <p class="text-gray-600">
          Try adjusting your search or filters
        </p>
      </div>

      <div
        v-else
        class="bg-white rounded-lg shadow overflow-hidden"
      >
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                Photographer
              </th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                Competition
              </th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                Type
              </th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                Status
              </th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                Issued Date
              </th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="cert in filteredCertificates"
              :key="cert.id"
              class="hover:bg-gray-50 transition"
            >
              <td class="px-6 py-4 text-sm">
                <div class="flex items-center gap-3">
                  <img 
                    :src="cert.photographer_photo || defaultAvatar" 
                    :alt="cert.photographer_name"
                    class="w-8 h-8 rounded-full object-cover"
                  >
                  <span class="font-medium text-gray-900">{{ cert.photographer_name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ cert.competition_title }}
              </td>
              <td class="px-6 py-4 text-sm">
                <span :class="['px-3 py-1 rounded-full text-xs font-medium', getCertificateTypeClass(cert.type)]">
                  {{ getCertificateTypeLabel(cert.type) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm">
                <span
                  v-if="cert.certificate_id"
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium"
                >
                  <svg
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Generated
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium"
                >
                  <svg
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  Pending
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ cert.certificate_generated_at ? formatDate(cert.certificate_generated_at) : '—' }}
              </td>
              <td class="px-6 py-4 text-sm space-x-2">
                <button 
                  v-if="cert.certificate_url"
                  class="px-3 py-1 text-orange-600 hover:bg-orange-50 rounded transition text-xs font-medium"
                  @click="downloadCertificate(cert)"
                >
                  Download
                </button>
                <button 
                  class="px-3 py-1 text-blue-600 hover:bg-blue-50 rounded transition text-xs font-medium"
                  @click="viewDetails(cert)"
                >
                  Details
                </button>
                <button 
                  v-if="!cert.certificate_id"
                  class="px-3 py-1 text-green-600 hover:bg-green-50 rounded transition text-xs font-medium"
                  @click="regenerateCertificate(cert)"
                >
                  Generate
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="totalPages > 1"
        class="flex items-center justify-between"
      >
        <p class="text-sm text-gray-600">
          Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, totalCount) }} of {{ totalCount }}
        </p>
        <div class="flex gap-2">
          <button
            :disabled="currentPage === 1"
            class="px-4 py-2 border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            @click="previousPage"
          >
            Previous
          </button>
          <button
            :disabled="currentPage === totalPages"
            class="px-4 py-2 border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            @click="nextPage"
          >
            Next
          </button>
        </div>
      </div>

      <!-- Toast -->
      <div 
        v-if="toastMessage"
        :class="[
          'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transition z-50',
          toastType === 'success' ? 'bg-green-600' : 'bg-red-600'
        ]"
      >
        {{ toastMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import api from '../../../api';
import AdminHeader from '../../../components/AdminHeader.vue';
import AdminQuickNav from '../../../components/AdminQuickNav.vue';
import { formatDate as formatDateValue } from '../../../utils/formatters';

const activeTab = ref('all');
const searchQuery = ref('');
const statusFilter = ref('');
const loading = ref(true);
const certificates = ref([]);
const currentPage = ref(1);
const perPage = ref(20);
const totalCount = ref(0);

const toastMessage = ref('');
const toastType = ref('success');

const defaultAvatar = 'https://ui-avatars.com/api/?name=Photographer';

const totalPages = computed(() => Math.ceil(totalCount.value / perPage.value));

const pendingCount = computed(() => 
  certificates.value.filter(c => !c.certificate_id).length
);

const generatedCount = computed(() => 
  certificates.value.filter(c => c.certificate_id).length
);

const filteredCertificates = computed(() => {
  let filtered = certificates.value;

  // Tab filter
  if (activeTab.value === 'pending') {
    filtered = filtered.filter(c => !c.certificate_id);
  } else if (activeTab.value === 'generated') {
    filtered = filtered.filter(c => c.certificate_id);
  }

  // Status filter
  if (statusFilter.value) {
    filtered = filtered.filter(c => c.type === statusFilter.value);
  }

  // Search filter
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(c => 
      c.photographer_name.toLowerCase().includes(q) ||
      c.competition_title.toLowerCase().includes(q)
    );
  }

  return filtered;
});

const loadCertificates = async () => {
  loading.value = true;
  try {
    const { data } = await api.get('/admin/certificates', {
      params: {
        page: currentPage.value,
        per_page: perPage.value
      }
    });
    if (data.status === 'success') {
      certificates.value = data.data;
      totalCount.value = data.meta?.total || data.data.length;
    }
  } catch (error) {
    console.error('Error loading certificates:', error);
    showToast('Failed to load certificates', 'error');
  } finally {
    loading.value = false;
  }
};

const downloadCertificate = (cert) => {
  if (cert.certificate_url) {
    window.open(cert.certificate_url, '_blank');
  }
};

const viewDetails = (cert) => {
  showToast(`Viewing details for ${cert.photographer_name}`, 'success');
};

const regenerateCertificate = async (cert) => {
  try {
    const { data } = await api.post(`/admin/certificates/${cert.id}/regenerate`);
    if (data.status === 'success') {
      showToast('Certificate regenerated successfully', 'success');
      loadCertificates();
    }
  } catch (error) {
    console.error('Error regenerating certificate:', error);
    showToast('Failed to regenerate certificate', 'error');
  }
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    loadCertificates();
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    loadCertificates();
  }
};

const showToast = (message, type) => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

const formatDate = (date) => {
  return formatDateValue(date);
};

const getCertificateTypeLabel = (type) => {
  const labels = {
    participation: 'Participation',
    finalist: 'Finalist',
    winner: 'Winner',
    merit: 'Merit'
  };
  return labels[type] || type;
};

const getCertificateTypeClass = (type) => {
  const classes = {
    participation: 'bg-blue-100 text-blue-800',
    finalist: 'bg-purple-100 text-purple-800',
    winner: 'bg-yellow-100 text-yellow-800',
    merit: 'bg-green-100 text-green-800'
  };
  return classes[type] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  loadCertificates();
});
</script>

<style scoped>
/* Smooth transitions */
</style>
