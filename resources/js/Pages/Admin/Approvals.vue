<template>
  <div class="admin-shell approvals-shell">
    <AdminHeader />

    <div class="admin-shell__content approvals-content px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <section class="approvals-hero">
        <div class="approvals-hero__copy">
          <p class="approvals-hero__kicker">
            ACCESS CONTROL
          </p>
          <h1 class="approvals-hero__title">
            Approvals that feel decisive.
          </h1>
          <p class="approvals-hero__subtitle">
            Review new registrations, verify intent, and keep the platform clean.
          </p>
          <div class="approvals-hero__actions">
            <button
              class="btn-admin-primary"
              :disabled="loading"
              @click="loadApprovals"
            >
              Refresh Queue
            </button>
            <button
              class="btn-admin-secondary"
              @click="resetFilters"
            >
              Clear Filters
            </button>
          </div>
        </div>
        <div class="approvals-hero__stats">
          <div class="approvals-stat">
            <span class="approvals-stat__label">Pending</span>
            <span class="approvals-stat__value">{{ stats.pending }}</span>
          </div>
          <div class="approvals-stat">
            <span class="approvals-stat__label">Approved</span>
            <span class="approvals-stat__value">{{ stats.approved }}</span>
          </div>
          <div class="approvals-stat">
            <span class="approvals-stat__label">Rejected</span>
            <span class="approvals-stat__value">{{ stats.rejected }}</span>
          </div>
          <div class="approvals-stat">
            <span class="approvals-stat__label">Total</span>
            <span class="approvals-stat__value">{{ stats.total }}</span>
          </div>
        </div>
      </section>

      <AdminQuickNav />

      <section class="approvals-panel">
        <div class="approvals-panel__header">
          <div>
            <h2 class="approvals-panel__title">
              Review Pipeline
            </h2>
            <p class="approvals-panel__subtitle">
              Filter by status, user type, and quick search.
            </p>
            <div class="approvals-status">
              <button
                :class="['status-pill', selectedStatus === '' && 'status-pill--active']"
                @click="setStatusFilter('')"
              >
                All ({{ stats.total }})
              </button>
              <button
                :class="['status-pill', selectedStatus === 'pending' && 'status-pill--active']"
                @click="setStatusFilter('pending')"
              >
                Pending ({{ stats.pending }})
              </button>
              <button
                :class="['status-pill', selectedStatus === 'approved' && 'status-pill--active']"
                @click="setStatusFilter('approved')"
              >
                Approved ({{ stats.approved }})
              </button>
              <button
                :class="['status-pill', selectedStatus === 'rejected' && 'status-pill--active']"
                @click="setStatusFilter('rejected')"
              >
                Rejected ({{ stats.rejected }})
              </button>
            </div>
          </div>
          <div class="approvals-panel__actions">
            <button
              class="btn-admin-secondary"
              :disabled="loading"
              @click="loadApprovals"
            >
              Apply Filters
            </button>
          </div>
        </div>

        <div class="approvals-filters">
          <label class="approvals-field">
            <span>Search</span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Name, email, phone"
              @keyup.enter="loadApprovals"
            >
          </label>
          <label class="approvals-field">
            <span>Status</span>
            <select v-model="selectedStatus">
              <option value="">
                All Statuses
              </option>
              <option value="pending">
                Pending
              </option>
              <option value="approved">
                Approved
              </option>
              <option value="rejected">
                Rejected
              </option>
            </select>
          </label>
          <label class="approvals-field">
            <span>User Type</span>
            <select v-model="selectedType">
              <option value="">
                All Types
              </option>
              <option value="client">
                Client
              </option>
              <option value="photographer">
                Photographer
              </option>
            </select>
          </label>
        </div>

        <div class="approvals-bulk">
          <label class="bulk-select">
            <input
              type="checkbox"
              :checked="allSelected"
              :disabled="approvals.length === 0"
              @change="toggleSelectAll"
            >
            <span>Select all</span>
          </label>
          <div class="bulk-actions">
            <button
              class="btn-admin-outline"
              :disabled="selectedIds.length === 0"
              @click="clearSelection"
            >
              Clear
            </button>
            <button
              class="btn-admin-primary"
              :disabled="selectedIds.length === 0"
              @click="bulkApprove"
            >
              Approve Selected ({{ selectedIds.length }})
            </button>
            <button
              class="btn-admin-danger"
              :disabled="selectedIds.length === 0"
              @click="bulkReject"
            >
              Reject Selected
            </button>
          </div>
        </div>
      </section>

      <section class="approvals-list">
        <div
          v-if="loading"
          class="approvals-loading"
        >
          <div class="admin-loader animate-spin rounded-full h-12 w-12" />
          <p>Loading approvals...</p>
        </div>

        <div
          v-else-if="approvals.length === 0"
          class="approvals-empty"
        >
          <div class="approvals-empty__icon">
            ✓
          </div>
          <h3>No pending approvals</h3>
          <p>All applications have been reviewed.</p>
        </div>

        <div
          v-else
          class="approvals-listing"
        >
          <article
            v-for="approval in approvals"
            :key="approval.id"
            class="approval-row"
            @click="viewDetails(approval)"
          >
            <label class="approval-select" @click.stop>
              <input
                type="checkbox"
                :checked="isSelected(approval.id)"
                @change="toggleSelection(approval.id)"
              >
            </label>
            <div class="approval-avatar">
              {{ approval.name.charAt(0) }}
            </div>
            <div class="approval-info">
              <h3>{{ approval.name }}</h3>
              <p>{{ approval.email }}</p>
              <div class="approval-badges">
                <span :class="['badge', statusBadge(approval.status)]">
                  {{ approval.status }}
                </span>
                <span :class="['badge', typeBadge(approval.type)]">
                  {{ approval.type }}
                </span>
              </div>
            </div>
            <div class="approval-meta">
              <span class="approval-meta__label">Applied</span>
              <strong>{{ formatDate(approval.appliedAt) }}</strong>
            </div>
          </article>
        </div>
      </section>
    </div>

    <div
      v-if="selectedApproval"
      class="approval-modal__overlay"
      @click="closeDetails"
    >
      <div class="approval-modal" @click.stop>
        <header class="approval-modal__header">
          <div>
            <p class="approval-modal__kicker">Applicant</p>
            <h3>{{ selectedApproval.name }}</h3>
            <p>{{ selectedApproval.email }}</p>
          </div>
          <button class="approval-modal__close" @click="closeDetails">✕</button>
        </header>
        <div class="approval-modal__body">
          <div class="approval-modal__grid">
            <div>
              <span>Type</span>
              <p>{{ selectedApproval.type }}</p>
            </div>
            <div>
              <span>Status</span>
              <p>{{ selectedApproval.status }}</p>
            </div>
            <div>
              <span>Phone</span>
              <p>{{ selectedApproval.phone || 'N/A' }}</p>
            </div>
            <div>
              <span>Location</span>
              <p>{{ selectedApproval.location }}</p>
            </div>
            <div>
              <span>Applied</span>
              <p>{{ formatDate(selectedApproval.appliedAt) }}</p>
            </div>
          </div>
          <div
            v-if="selectedApproval.type === 'photographer'"
            class="approval-modal__note"
          >
            <div>
              <strong>Portfolio</strong>
              <p>{{ selectedApproval.portfolio || 'Not provided' }}</p>
            </div>
            <div>
              <strong>Experience</strong>
              <p>{{ selectedApproval.experience || 'N/A' }} years</p>
            </div>
          </div>
          <div class="approval-modal__actions">
            <button class="btn-admin-primary" @click="approveUser(selectedApproval)">
              Approve
            </button>
            <button class="btn-admin-danger" @click="rejectUser(selectedApproval)">
              Reject
            </button>
          </div>
        </div>
      </div>
    </div>

    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../../api';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import Toast from '../../components/ui/Toast.vue';
import { formatDate as formatDateValue } from '../../utils/formatters';
const loading = ref(true);
const selectedStatus = ref('');
const selectedType = ref('');
const searchQuery = ref('');
const stats = ref({
  pending: 0,
  approved: 0,
  rejected: 0,
  total: 0
});
const selectedIds = ref([]);
const selectedApproval = ref(null);

const toast = ref({
  show: false,
  message: '',
  type: 'success'
});

const approvals = ref([]);

const loadStats = async () => {
  try {
    const response = await api.get('/admin/approval-stats');
    stats.value = {
      pending: response.data?.pending || 0,
      approved: response.data?.approved || 0,
      rejected: response.data?.rejected || 0,
      total: response.data?.total || 0,
    };
  } catch (error) {
    stats.value = { pending: 0, approved: 0, rejected: 0, total: 0 };
  }
};

const loadApprovals = async () => {
  loading.value = true;
  try {
    const response = await api.get('/admin/pending-users', {
      params: {
        status: selectedStatus.value || undefined,
        role: selectedType.value || undefined,
        search: searchQuery.value || undefined
      }
    });

    const users = response.data || [];
    approvals.value = users.map((user) => {
      const photographer = user.photographer || {};
      return {
        id: user.id,
        name: user.name,
        email: user.email,
        phone: user.phone,
        location: photographer.location || photographer.city?.name || 'N/A',
        type: user.role,
        status: user.approval_status,
        appliedAt: user.created_at,
        portfolio: photographer.website_url || null,
        experience: photographer.experience_years || null
      };
    });
    selectedIds.value = [];
    selectedApproval.value = null;
  } catch (error) {
    console.error('Approvals load error:', error);
    approvals.value = [];
    toast.value = {
      show: true,
      message: 'Failed to load approvals',
      type: 'error'
    };
  } finally {
    loading.value = false;
    await loadStats();
  }
};

const resetFilters = () => {
  selectedStatus.value = '';
  selectedType.value = '';
  searchQuery.value = '';
  loadApprovals();
};

const statusBadge = (status) => {
  const badges = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
  };
  return badges[status] || 'bg-gray-100 text-gray-800';
};

const typeBadge = (type) => {
  const badges = {
    photographer: 'bg-pink-100 text-pink-800',
    client: 'bg-blue-100 text-blue-800',
  };
  return badges[type] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date) => {
  return formatDateValue(date);
};

const viewDetails = (approval) => {
  selectedApproval.value = approval;
};

const closeDetails = () => {
  selectedApproval.value = null;
};

const isSelected = (id) => selectedIds.value.includes(id);

const toggleSelection = (id) => {
  if (isSelected(id)) {
    selectedIds.value = selectedIds.value.filter((item) => item !== id);
  } else {
    selectedIds.value = [...selectedIds.value, id];
  }
};

const allSelected = computed(() => approvals.value.length > 0 && selectedIds.value.length === approvals.value.length);

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedIds.value = [];
  } else {
    selectedIds.value = approvals.value.map((approval) => approval.id);
  }
};

const clearSelection = () => {
  selectedIds.value = [];
};

const bulkApprove = async () => {
  if (selectedIds.value.length === 0) return;
  if (!confirm(`Approve ${selectedIds.value.length} users?`)) return;
  try {
    await api.post('/admin/users/bulk-approve', { user_ids: selectedIds.value });
    toast.value = {
      show: true,
      message: 'Selected users approved',
      type: 'success'
    };
    await loadApprovals();
  } catch (error) {
    toast.value = {
      show: true,
      message: error.response?.data?.message || 'Failed to approve selected users',
      type: 'error'
    };
  }
};

const bulkReject = async () => {
  if (selectedIds.value.length === 0) return;
  if (!confirm(`Reject ${selectedIds.value.length} users? This action cannot be undone.`)) return;
  const reason = window.prompt('Reason for rejection:');
  if (!reason) return;

  try {
    const requests = selectedIds.value.map((id) => api.post(`/admin/users/${id}/reject`, { reason }));
    await Promise.all(requests);
    toast.value = {
      show: true,
      message: 'Selected users rejected',
      type: 'success'
    };
    await loadApprovals();
  } catch (error) {
    toast.value = {
      show: true,
      message: error.response?.data?.message || 'Failed to reject selected users',
      type: 'error'
    };
  }
};

const setStatusFilter = (status) => {
  selectedStatus.value = status;
  loadApprovals();
};

const approveUser = async (approval) => {
  if (!confirm(`Approve ${approval.name}?`)) return;
  
  try {
    await api.post(`/admin/users/${approval.id}/approve`);
    toast.value = {
      show: true,
      message: `${approval.name} has been approved`,
      type: 'success'
    };
    await loadApprovals();
    closeDetails();
  } catch (error) {
    toast.value = {
      show: true,
      message: error.response?.data?.message || 'Failed to approve user',
      type: 'error'
    };
  }
};

const rejectUser = async (approval) => {
  if (!confirm(`Reject ${approval.name}? This action cannot be undone.`)) return;
  
  const reason = window.prompt('Reason for rejection:')
  if (!reason) return
  try {
    await api.post(`/admin/users/${approval.id}/reject`, { reason });
    toast.value = {
      show: true,
      message: `${approval.name} has been rejected`,
      type: 'success'
    };
    await loadApprovals();
    closeDetails();
  } catch (error) {
    toast.value = {
      show: true,
      message: error.response?.data?.message || 'Failed to reject user',
      type: 'error'
    };
  }
};

onMounted(() => {
  loadApprovals();
  loadStats();
});
</script>

<style scoped>
.approvals-shell {
  --approvals-bg: #f6f4ef;
  --approvals-ink: #1f2937;
  --approvals-accent: #b45309;
  --approvals-accent-soft: #fef3c7;
  font-family: "Space Grotesk", "IBM Plex Sans", sans-serif;
  background: var(--approvals-bg);
}

.approvals-content {
  color: var(--approvals-ink);
}

.approvals-hero {
  background: linear-gradient(120deg, #fff7ed 0%, #fde68a 45%, #fbbf24 100%);
  border-radius: 1.5rem;
  padding: 2rem;
  display: grid;
  gap: 2rem;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  position: relative;
  overflow: hidden;
  border: 1px solid #fcd34d;
}

.approvals-hero::after {
  content: "";
  position: absolute;
  inset: auto -10% -35% auto;
  width: 320px;
  height: 320px;
  background: radial-gradient(circle, rgba(217, 119, 6, 0.25), transparent 70%);
}

.approvals-hero__copy {
  position: relative;
  z-index: 1;
}

.approvals-hero__kicker {
  font-size: 0.75rem;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #92400e;
  font-weight: 600;
  margin-bottom: 0.75rem;
}

.approvals-hero__title {
  font-size: clamp(2rem, 3.2vw, 3rem);
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.75rem;
}

.approvals-hero__subtitle {
  color: #6b4b16;
  max-width: 520px;
}

.approvals-hero__actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

.approvals-hero__stats {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  align-content: center;
  z-index: 1;
}

.approvals-stat {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 1rem;
  padding: 1rem;
  border: 1px solid rgba(217, 119, 6, 0.25);
}

.approvals-stat__label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #9a3412;
}

.approvals-stat__value {
  display: block;
  font-size: 1.6rem;
  font-weight: 700;
  margin-top: 0.25rem;
}

.approvals-panel {
  background: #ffffff;
  border-radius: 1.25rem;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
}

.approvals-panel__header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}

.approvals-panel__title {
  font-size: 1.25rem;
  font-weight: 700;
}

.approvals-panel__subtitle {
  color: #6b7280;
}

.approvals-status {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.status-pill {
  border-radius: 999px;
  padding: 0.3rem 0.8rem;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
  color: #6b7280;
}

.status-pill--active {
  background: #111827;
  color: #ffffff;
  border-color: #111827;
}

.approvals-filters {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}

.approvals-bulk {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 1rem;
  align-items: center;
}

.bulk-select {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #374151;
}

.bulk-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.approvals-field {
  display: grid;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #374151;
}

.approvals-field input,
.approvals-field select {
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  padding: 0.6rem 0.75rem;
  background: #f9fafb;
}

.approvals-field input:focus,
.approvals-field select:focus {
  outline: none;
  border-color: #f59e0b;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
}

.approvals-list {
  min-height: 200px;
}

.approvals-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 3rem 0;
  color: #6b7280;
}

.approvals-empty {
  text-align: center;
  padding: 3rem;
  background: #ffffff;
  border-radius: 1.25rem;
  border: 1px dashed #d1d5db;
}

.approvals-empty__icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.approvals-listing {
  display: grid;
  gap: 0.85rem;
}

.approval-row {
  background: #ffffff;
  border-radius: 1rem;
  padding: 0.85rem 1rem;
  border: 1px solid #e5e7eb;
  display: grid;
  grid-template-columns: auto auto 1fr auto;
  gap: 0.75rem;
  align-items: center;
  cursor: pointer;
  transition: all 0.2s;
}

.approval-row:hover {
  border-color: #f59e0b;
  box-shadow: 0 10px 20px rgba(245, 158, 11, 0.1);
}

.approval-select {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.7rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.approval-avatar {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 1rem;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: #fff;
  display: grid;
  place-items: center;
  font-size: 1rem;
  font-weight: 700;
}

.approval-info h3 {
  font-size: 0.95rem;
  font-weight: 700;
}

.approval-info p {
  font-size: 0.75rem;
  color: #6b7280;
  word-break: break-all;
}

.approval-meta {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  align-items: flex-end;
}

.approval-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.badge {
  border-radius: 999px;
  padding: 0.25rem 0.6rem;
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}


.approval-meta__label {
  font-size: 0.6rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #9ca3af;
}

.approval-modal__overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  display: grid;
  place-items: center;
  z-index: 50;
  padding: 1.5rem;
}

.approval-modal {
  width: min(640px, 92vw);
  max-height: 85vh;
  background: #ffffff;
  border-radius: 1.5rem;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  overflow: hidden;
}

.approval-modal__header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
}

.approval-modal__kicker {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: #9a3412;
  margin-bottom: 0.4rem;
}

.approval-modal__close {
  border: none;
  background: #f3f4f6;
  color: #6b7280;
  width: 2rem;
  height: 2rem;
  border-radius: 999px;
}

.approval-modal__body {
  display: grid;
  gap: 1rem;
  overflow-y: auto;
  padding-right: 0.25rem;
}

.approval-modal__grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 0.75rem;
  font-size: 0.85rem;
}

.approval-modal__grid span {
  display: block;
  font-size: 0.65rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #9ca3af;
}

.approval-modal__note {
  background: #fff7ed;
  border-radius: 0.75rem;
  padding: 0.75rem 1rem;
  border: 1px solid #fed7aa;
  display: grid;
  gap: 0.5rem;
  font-size: 0.85rem;
}

.approval-modal__actions {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.approval-drawer__overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  display: flex;
  justify-content: flex-end;
  z-index: 50;
}

.approval-drawer {
  width: min(420px, 90vw);
  height: 100%;
  background: #ffffff;
  padding: 1.5rem;
  box-shadow: -10px 0 30px rgba(15, 23, 42, 0.2);
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.approval-drawer__header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
}

.approval-drawer__kicker {
  font-size: 0.7rem;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #9a3412;
  margin-bottom: 0.5rem;
}

.approval-drawer__close {
  border: none;
  background: #f3f4f6;
  color: #6b7280;
  width: 2rem;
  height: 2rem;
  border-radius: 999px;
}

.approval-drawer__body {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  overflow-y: auto;
}

.drawer-section h4 {
  font-size: 0.9rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
}

.drawer-grid {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  font-size: 0.85rem;
}

.drawer-grid span {
  display: block;
  font-size: 0.65rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #9ca3af;
}

.drawer-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.btn-admin-danger {
  background: #dc2626;
  color: #ffffff;
}

.btn-admin-danger:hover {
  background: #b91c1c;
}

@media (max-width: 768px) {
  .approval-row {
    grid-template-columns: auto 1fr;
  }

  .approval-meta {
    align-items: flex-start;
  }

  .approval-modal__actions {
    grid-template-columns: 1fr;
  }
}
</style>
