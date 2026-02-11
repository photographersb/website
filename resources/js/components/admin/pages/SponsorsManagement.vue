<template>
  <AdminLayout 
    page-title="Sponsors Management"
    page-description="Manage platform sponsors and sponsorships"
    :show-breadcrumbs="true"
  >
    <!-- Edit Modal -->
    <BaseModal 
      :is-open="showModal"
      :title="editingSponsor ? 'Edit Sponsor' : 'Add New Sponsor'"
      :is-loading="isSubmitting"
      @close="showModal = false"
      @submit="handleSaveSponsor"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor Name</label>
          <input
            v-model="formData.name"
            type="text"
            placeholder="Sponsor name"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
          <input
            v-model="formData.category"
            type="text"
            placeholder="e.g., Technology, Sports"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
          <input
            v-model="formData.contact_email"
            type="email"
            placeholder="contact@sponsor.com"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
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
            <option value="pending">
              Pending
            </option>
          </select>
        </div>
      </div>
    </BaseModal>

    <div class="space-y-6">
      <!-- FILTERS & ACTIONS -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search sponsors..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
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
        </select>
        <button
          class="px-6 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium"
          @click="openAddModal"
        >
          + Add Sponsor
        </button>
      </div>

      <!-- TABLE -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div
          v-if="isLoading"
          class="p-8 text-center text-gray-600"
        >
          Loading sponsors...
        </div>
        <div
          v-else-if="filteredSponsors.length === 0"
          class="p-12 text-center text-gray-500"
        >
          No sponsors found
        </div>
        <table
          v-else
          class="w-full"
        >
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                Sponsor
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                Category
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                Sponsorships
              </th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr
              v-for="sponsor in filteredSponsors"
              :key="sponsor.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 font-medium">
                {{ sponsor.name }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ sponsor.category }}
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    sponsor.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                  ]"
                >{{ sponsor.status }}</span>
              </td>
              <td class="px-6 py-4 text-sm">
                {{ sponsor.sponsorships_count || 0 }}
              </td>
              <td class="px-6 py-4 text-right">
                <button
                  class="text-blue-600 hover:text-blue-800 mr-3"
                  @click="editSponsor(sponsor)"
                >
                  Edit
                </button>
                <button
                  class="text-red-600 hover:text-red-800"
                  @click="deleteSponsor(sponsor)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from '../../AdminLayout.vue';
import BaseModal from '../modals/BaseModal.vue';

const sponsors = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const searchQuery = ref('');
const statusFilter = ref('');
const showModal = ref(false);
const editingSponsor = ref(null);
const formData = ref({
  name: '',
  category: '',
  contact_email: '',
  status: 'active',
});
const addAlert = inject('addAlert', null);

const filteredSponsors = computed(() => {
  let filtered = sponsors.value;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(s => s.name.toLowerCase().includes(q) || s.contact_email.toLowerCase().includes(q));
  }
  if (statusFilter.value) filtered = filtered.filter(s => s.status === statusFilter.value);
  return filtered;
});

const fetchSponsors = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/sponsors', {
      headers: {}
    });
    if (response.ok) {
      const data = await response.json();
      sponsors.value = data.data || [];
    }
  } catch (error) {
    console.error('Error fetching sponsors:', error);
    if (addAlert) addAlert('Failed to load sponsors', 'error');
  } finally {
    isLoading.value = false;
  }
};

const openAddModal = () => {
  editingSponsor.value = null;
  formData.value = { name: '', category: '', contact_email: '', status: 'active' };
  showModal.value = true;
};

const editSponsor = (sponsor) => {
  editingSponsor.value = sponsor;
  formData.value = { ...sponsor };
  showModal.value = true;
};

const handleSaveSponsor = async () => {
  isSubmitting.value = true;
  try {
    const url = editingSponsor.value 
      ? `/api/v1/admin/sponsors/${editingSponsor.value.id}`
      : '/api/v1/admin/sponsors';
    
    const method = editingSponsor.value ? 'PUT' : 'POST';
    
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData.value)
    });

    if (response.ok) {
      const data = await response.json();
      if (editingSponsor.value) {
        const index = sponsors.value.findIndex(s => s.id === editingSponsor.value.id);
        sponsors.value[index] = data.data;
        if (addAlert) addAlert('Sponsor updated successfully', 'success');
      } else {
        sponsors.value.unshift(data.data);
        if (addAlert) addAlert('Sponsor created successfully', 'success');
      }
      showModal.value = false;
    }
  } catch (error) {
    console.error('Error saving sponsor:', error);
    if (addAlert) addAlert('Failed to save sponsor', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteSponsor = async (sponsor) => {
  if (!confirm(`Delete ${sponsor.name}?`)) return;
  try {
    const response = await fetch(`/api/v1/admin/sponsors/${sponsor.id}`, {
      method: 'DELETE',
      headers: {}
    });
    if (response.ok) {
      sponsors.value = sponsors.value.filter(s => s.id !== sponsor.id);
      if (addAlert) addAlert('Sponsor deleted', 'success');
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to delete sponsor', 'error');
  }
};

onMounted(fetchSponsors);
</script>
