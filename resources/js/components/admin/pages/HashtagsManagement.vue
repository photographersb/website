<template>
  <AdminLayout 
    page-title="Hashtags Management"
    page-description="Manage trending hashtags and tags"
    :show-breadcrumbs="true"
  >
    <BaseModal 
      :is-open="showModal"
      :title="editingHashtag ? 'Edit Hashtag' : 'Add Hashtag'"
      :is-loading="isSubmitting"
      @close="showModal = false"
      @submit="handleSaveHashtag"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Hashtag Name</label>
          <input
            v-model="formData.name"
            type="text"
            placeholder="photography"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea
            v-model="formData.description"
            placeholder="Describe this hashtag"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm"
            rows="2"
          />
        </div>
      </div>
    </BaseModal>

    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search hashtags..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
        <select
          v-model="sortBy"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="trending">
            Trending
          </option>
          <option value="recent">
            Recent
          </option>
          <option value="popular">
            Most Used
          </option>
        </select>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-if="isLoading"
          class="col-span-full p-8 text-center"
        >
          Loading hashtags...
        </div>
        <div
          v-else-if="filteredHashtags.length === 0"
          class="col-span-full p-12 text-center text-gray-500"
        >
          No hashtags found
        </div>
        <div
          v-for="hashtag in filteredHashtags"
          :key="hashtag.id"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition"
        >
          <div class="flex items-start justify-between mb-2">
            <div>
              <p class="font-medium text-lg">
                #{{ hashtag.name }}
              </p>
              <p class="text-sm text-gray-500">
                {{ hashtag.use_count }} posts
              </p>
            </div>
            <span :class="`px-2 py-1 rounded-full text-xs font-medium ${getTrendingBadge(hashtag.trend_score)}`">
              {{ hashtag.trend_score }}%
            </span>
          </div>
          <p
            v-if="hashtag.description"
            class="text-sm text-gray-600 mb-3"
          >
            {{ hashtag.description }}
          </p>
          <div class="flex gap-2">
            <button
              class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-sm font-medium"
              @click="editHashtag(hashtag)"
            >
              Edit
            </button>
            <button
              class="flex-1 px-3 py-2 bg-red-50 text-red-600 rounded hover:bg-red-100 text-sm font-medium"
              @click="deleteHashtag(hashtag)"
            >
              Remove
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from '../../AdminLayout.vue';
import BaseModal from '../modals/BaseModal.vue';

const hashtags = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const searchQuery = ref('');
const sortBy = ref('trending');
const showModal = ref(false);
const editingHashtag = ref(null);
const formData = ref({ name: '', description: '' });
const addAlert = inject('addAlert', null);

const filteredHashtags = computed(() => {
  let filtered = hashtags.value;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(h => h.name.toLowerCase().includes(q));
  }
  
  if (sortBy.value === 'trending') {
    filtered.sort((a, b) => (b.trend_score || 0) - (a.trend_score || 0));
  } else if (sortBy.value === 'recent') {
    filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  } else if (sortBy.value === 'popular') {
    filtered.sort((a, b) => (b.use_count || 0) - (a.use_count || 0));
  }
  
  return filtered;
});

const getTrendingBadge = (score) => {
  if (score >= 80) return 'bg-red-100 text-red-800';
  if (score >= 60) return 'bg-orange-100 text-orange-800';
  if (score >= 40) return 'bg-yellow-100 text-yellow-800';
  return 'bg-gray-100 text-gray-800';
};

const fetchHashtags = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/hashtags', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (response.ok) {
      const data = await response.json();
      hashtags.value = data.data || [];
    }
  } catch (error) {
    console.error('Error:', error);
    if (addAlert) addAlert('Failed to load hashtags', 'error');
  } finally {
    isLoading.value = false;
  }
};

const openAddModal = () => {
  editingHashtag.value = null;
  formData.value = { name: '', description: '' };
  showModal.value = true;
};

const editHashtag = (hashtag) => {
  editingHashtag.value = hashtag;
  formData.value = { name: hashtag.name, description: hashtag.description || '' };
  showModal.value = true;
};

const handleSaveHashtag = async () => {
  isSubmitting.value = true;
  try {
    const url = editingHashtag.value 
      ? `/api/v1/admin/hashtags/${editingHashtag.value.id}`
      : '/api/v1/admin/hashtags';
    
    const method = editingHashtag.value ? 'PUT' : 'POST';
    
    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData.value)
    });

    if (response.ok) {
      const data = await response.json();
      if (editingHashtag.value) {
        const index = hashtags.value.findIndex(h => h.id === editingHashtag.value.id);
        hashtags.value[index] = data.data;
        if (addAlert) addAlert('Hashtag updated', 'success');
      } else {
        hashtags.value.unshift(data.data);
        if (addAlert) addAlert('Hashtag created', 'success');
      }
      showModal.value = false;
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to save hashtag', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteHashtag = async (hashtag) => {
  if (!confirm(`Remove #${hashtag.name}?`)) return;
  try {
    const response = await fetch(`/api/v1/admin/hashtags/${hashtag.id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    if (response.ok) {
      hashtags.value = hashtags.value.filter(h => h.id !== hashtag.id);
      if (addAlert) addAlert('Hashtag removed', 'success');
    }
  } catch (error) {
    if (addAlert) addAlert('Failed to delete hashtag', 'error');
  }
};

onMounted(fetchHashtags);
</script>
