<template>
  <div class="min-h-screen">
    <AdminHeader
      title="🎓 Certificate Management"
      subtitle="Manage competition certificates and awards"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">CERTIFICATE OPERATIONS</p>
          <h1 class="hero-title">Certificates, unified.</h1>
          <p class="hero-subtitle">Track awards, regenerate pending issues, and keep organizers aligned.</p>
          <div class="hero-actions">
            <router-link
              to="/admin/certificates/manual-issuance"
              class="btn-admin-primary"
            >
              + Issue Certificate
            </router-link>
            <button
              class="btn-admin-secondary"
              @click="loadCertificates"
            >
              Refresh
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Total</span>
            <span class="status-value">{{ totalCount }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Pending</span>
            <span class="status-value">{{ pendingCount }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Generated</span>
            <span class="status-value">{{ generatedCount }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Certificates · Awards
        </div>
      </div>

      <AdminQuickNav />

      <div class="tab-bar">
        <button
          class="tab-button"
          :class="{ active: activeTab === 'all' }"
          @click="activeTab = 'all'"
        >
          All Certificates ({{ totalCount }})
        </button>
        <button
          class="tab-button"
          :class="{ active: activeTab === 'pending' }"
          @click="activeTab = 'pending'"
        >
          Pending Generation ({{ pendingCount }})
        </button>
        <button
          class="tab-button"
          :class="{ active: activeTab === 'generated' }"
          @click="activeTab = 'generated'"
        >
          Generated ({{ generatedCount }})
        </button>
      </div>

      <div class="filter-grid">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by photographer name, competition..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
          <select
            v-model="statusFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="">All Status</option>
            <option value="participation">Participation</option>
            <option value="finalist">Finalist</option>
            <option value="winner">Winner</option>
            <option value="merit">Merit</option>
          </select>
        </div>
      </div>

      <div class="panel">
        <div v-if="loading" class="loading">Loading certificates...</div>

        <div v-else-if="filteredCertificates.length === 0" class="empty">
          No certificates found. Try adjusting your search or filters.
        </div>

        <div v-else class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Photographer</th>
                <th>Competition</th>
                <th>Type</th>
                <th>Status</th>
                <th>Issued Date</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cert in filteredCertificates" :key="cert.id">
                <td>
                  <div class="flex items-center gap-3">
                    <img
                      :src="cert.photographer_photo || defaultAvatar"
                      :alt="cert.photographer_name"
                      class="avatar"
                    >
                    <span class="font-medium text-gray-900">{{ cert.photographer_name }}</span>
                  </div>
                </td>
                <td class="text-sm text-gray-600">{{ cert.competition_title }}</td>
                <td>
                  <span :class="['badge', getCertificateTypeClass(cert.type)]">
                    {{ getCertificateTypeLabel(cert.type) }}
                  </span>
                </td>
                <td>
                  <span
                    v-if="cert.certificate_id"
                    class="badge badge-active"
                  >
                    Generated
                  </span>
                  <span
                    v-else
                    class="badge badge-pending"
                  >
                    Pending
                  </span>
                </td>
                <td class="text-sm text-gray-600">
                  {{ cert.certificate_generated_at ? formatDate(cert.certificate_generated_at) : '—' }}
                </td>
                <td class="text-right">
                  <button
                    v-if="cert.certificate_url"
                    class="link"
                    @click="downloadCertificate(cert)"
                  >
                    Download
                  </button>
                  <button
                    class="link"
                    @click="viewDetails(cert)"
                  >
                    Details
                  </button>
                  <button
                    v-if="!cert.certificate_id"
                    class="link"
                    @click="regenerateCertificate(cert)"
                  >
                    Generate
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
.page-hero { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr); gap: 1.5rem; padding: 1.75rem 2rem; border-radius: 1.5rem; border: 1px solid rgba(142, 14, 63, 0.2); background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(247, 239, 233, 0.82)), linear-gradient(90deg, rgba(142, 14, 63, 0.06), transparent 45%, rgba(109, 72, 56, 0.08)); box-shadow: 0 25px 55px rgba(24, 12, 8, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.6); backdrop-filter: blur(6px); }
.hero-copy { display: flex; flex-direction: column; gap: 0.85rem; }
.hero-kicker { font-size: 0.7rem; letter-spacing: 0.28em; text-transform: uppercase; color: var(--admin-text-secondary); font-weight: 700; }
.hero-title { font-size: 2rem; line-height: 1.1; color: var(--admin-text-primary); text-shadow: 0 2px 14px rgba(142, 14, 63, 0.18); }
.hero-subtitle { color: var(--admin-text-secondary); max-width: 520px; }
.hero-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.hero-status { display: grid; gap: 0.8rem; }
.status-card { background: rgba(255, 255, 255, 0.85); border: 1px solid rgba(142, 14, 63, 0.2); border-radius: 1rem; padding: 1rem 1.25rem; box-shadow: 0 16px 35px rgba(22, 12, 8, 0.08); display: flex; flex-direction: column; gap: 0.35rem; }
.status-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.2em; color: var(--admin-text-secondary); }
.status-value { font-size: 1.1rem; font-weight: 700; color: var(--admin-text-primary); }
.page-topbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.9rem 1.25rem; background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1.1rem; box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08); backdrop-filter: blur(8px); }
.status-chip { background: rgba(142, 14, 63, 0.12); color: var(--admin-text-primary); padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; }
.tab-bar { display: inline-flex; gap: 0.6rem; background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 999px; padding: 0.35rem; }
.tab-button { padding: 0.5rem 1rem; border-radius: 999px; font-weight: 600; font-size: 0.85rem; color: var(--admin-text-secondary); }
.tab-button.active { background: #8e0e3f; color: #fff; }
.panel { background: #fff; border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1.2rem; padding: 1.5rem; box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08); }
.table-wrap { overflow-x: auto; }
.table { width: 100%; border-collapse: collapse; }
.table th { text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.12em; color: #6b7280; padding: 0.75rem 0.5rem; border-bottom: 1px solid #e5e7eb; }
.table td { padding: 0.75rem 0.5rem; border-bottom: 1px solid #f1f1f1; vertical-align: middle; }
.avatar { width: 36px; height: 36px; border-radius: 999px; object-fit: cover; }
.badge { display: inline-flex; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
.badge-active { background: #dcfce7; color: #166534; }
.badge-pending { background: #fef9c3; color: #92400e; }
.link { color: #8e0e3f; margin-left: 0.75rem; font-weight: 600; }
.loading { padding: 2rem; text-align: center; color: #6b7280; }
.empty { padding: 2rem; text-align: center; color: #6b7280; }
@media (max-width: 1024px) { .page-hero { grid-template-columns: 1fr; } }
</style>
