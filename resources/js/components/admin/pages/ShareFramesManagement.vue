<template>
  <AdminLayout 
    page-title="Share Frames"
    page-description="Manage competition share frame templates"
    :show-breadcrumbs="true"
  >
    <BaseModal 
      :is-open="showModal"
      :title="editingFrame ? 'Edit Share Frame' : 'Add Share Frame'"
      :is-loading="isSubmitting"
      @close="showModal = false"
      @submit="handleSaveFrame"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Competition</label>
          <select
            v-model="formData.competition_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
            <option value="">
              Select competition
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
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
          <input
            v-model="formData.name"
            type="text"
            placeholder="e.g., Standard 16:9"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Width (px)</label>
          <input
            v-model.number="formData.width"
            type="number"
            placeholder="1920"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Height (px)</label>
          <input
            v-model.number="formData.height"
            type="number"
            placeholder="1080"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
        </div>
      </div>
    </BaseModal>

    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search templates..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
        <select
          v-model="competitionFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
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
        <button
          class="px-6 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium"
          @click="openAddModal"
        >
          + Add Template
        </button>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <div
          v-if="isLoading"
          class="p-8 text-center"
        >
          Loading templates...
        </div>
        <div
          v-else-if="filteredFrames.length === 0"
          class="p-12 text-center text-gray-500"
        >
          No templates found
        </div>
        <table
          v-else
          class="w-full text-sm"
        >
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Template
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Competition
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Dimensions
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Created
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="frame in filteredFrames"
              :key="frame.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 font-medium">
                {{ frame.name }}
              </td>
              <td class="px-6 py-4">
                {{ frame.competition?.name }}
              </td>
              <td class="px-6 py-4">
                {{ frame.width }}x{{ frame.height }}
              </td>
              <td class="px-6 py-4 text-gray-600">
                {{ formatDate(frame.created_at) }}
              </td>
              <td class="px-6 py-4">
                <button
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-3"
                  @click="editFrame(frame)"
                >
                  Edit
                </button>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="deleteFrame(frame)"
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
import { formatDate as formatDateValue } from '../../../utils/formatters';

const frames = ref([]);
const competitions = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const searchQuery = ref('');
const competitionFilter = ref('');
const showModal = ref(false);
const editingFrame = ref(null);
const formData = ref({
  competition_id: '',
  name: '',
  width: 1920,
  height: 1080,
});
const addAlert = inject('addAlert', null);

const filteredFrames = computed(() => {
  let filtered = frames.value;
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(f => f.name.toLowerCase().includes(q));
  }
  
  if (competitionFilter.value) {
    filtered = filtered.filter(f => f.competition_id === competitionFilter.value);
  }
  
  return filtered;
});

const formatDate = (date) => {
  if (!date) return '';
  return formatDateValue(date);
};

const fetchFrames = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/share-frames', {
      headers: {}
    });
    if (response.ok) {
      const data = await response.json();
      frames.value = data.data || [];
    }
  } catch (error) {
    console.error('Error:', error);
    if (addAlert) addAlert('Failed to load templates', 'error');
  } finally {
    isLoading.value = false;
  }
};

const fetchCompetitions = async () => {
  try {
    const response = await fetch('/api/v1/admin/competitions', {
      headers: {}
    });
    if (response.ok) {
      const data = await response.json();
      competitions.value = data.data?.slice(0, 20) || [];
    }
  } catch (error) {
    console.error('Error fetching competitions:', error);
  }
};

const openAddModal = () => {
  editingFrame.value = null;
  formData.value = { competition_id: '', name: '', width: 1920, height: 1080 };
  showModal.value = true;
};

const editFrame = (frame) => {
  editingFrame.value = frame;
  formData.value = { ...frame };
  showModal.value = true;
};

const handleSaveFrame = async () => {
  isSubmitting.value = true;
  try {
    const url = editingFrame.value 
      ? `/api/v1/admin/share-frames/${editingFrame.value.id}`
      : '/api/v1/admin/share-frames';
    
    const method = editingFrame.value ? 'PUT' : 'POST';
    
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData.value)
    });

    if (response.ok) {
      const data = await response.json();
      if (editingFrame.value) {
        const index = frames.value.findIndex(f => f.id === editingFrame.value.id);
        frames.value[index] = data.data;
        if (addAlert) addAlert('Template updated', 'success');
      } else {
        frames.value.unshift(data.data);
        if (addAlert) addAlert('Template created', 'success');
      }
      showModal.value = false;
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to save template', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteFrame = async (frame) => {
  if (!confirm(`Delete ${frame.name}?`)) return;
  
  try {
    const response = await fetch(`/api/v1/admin/share-frames/${frame.id}`, {
      method: 'DELETE',
      headers: {}
    });
    if (response.ok) {
      frames.value = frames.value.filter(f => f.id !== frame.id);
      if (addAlert) addAlert('Template deleted', 'success');
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to delete template', 'error');
  }
};

onMounted(() => {
  fetchFrames();
  fetchCompetitions();
});
</script>
