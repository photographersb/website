<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Hashtag Management
        </h2>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-burgundy hover:bg-burgundy-dark"
          @click="showAddModal = true"
        >
          <svg
            class="-ml-1 mr-2 h-5 w-5"
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
          Add Hashtag
        </button>
      </div>
    </div>

    <!-- Filter -->
    <div class="mb-6 flex gap-4">
      <select
        v-model="filterCategory"
        class="block w-48 border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
        @change="filterHashtags"
      >
        <option value="">
          All Categories
        </option>
        <option
          v-for="cat in categories"
          :key="cat.id"
          :value="cat.id"
        >
          {{ cat.name }}
        </option>
      </select>
            
      <label class="flex items-center">
        <input
          v-model="filterFeatured"
          type="checkbox"
          class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
          @change="filterHashtags"
        >
        <span class="ml-2 text-sm text-gray-700">Featured Only</span>
      </label>
    </div>

    <!-- Hashtags Grid -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Hashtag
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Category
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Usage
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Featured
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="hashtag in hashtags"
            :key="hashtag.id"
          >
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                #{{ hashtag.name }}
              </div>
              <div
                v-if="hashtag.description"
                class="text-xs text-gray-500"
              >
                {{ hashtag.description }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                v-if="hashtag.category"
                class="badge badge-primary"
              >
                {{ hashtag.category.name }}
              </span>
              <span
                v-else
                class="text-sm text-gray-500"
              >-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ hashtag.usage_count || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                v-if="hashtag.is_featured"
                class="text-primary-600"
              >⭐</span>
              <span
                v-else
                class="text-gray-300"
              >☆</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                class="text-burgundy hover:text-burgundy-dark mr-3"
                @click="editHashtag(hashtag)"
              >
                Edit
              </button>
              <button
                class="text-red-600 hover:text-red-900"
                @click="deleteHashtag(hashtag.id)"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div
      v-if="showAddModal || showEditModal"
      class="fixed z-50 inset-0 overflow-y-auto"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ showEditModal ? 'Edit Hashtag' : 'Add New Hashtag' }}
            </h3>
                        
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Name * (without #)</label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="WeddingPhotography"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                >
              </div>
                            
              <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select
                  v-model="form.category_id"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                >
                  <option :value="null">
                    No Category
                  </option>
                  <option
                    v-for="cat in categories"
                    :key="cat.id"
                    :value="cat.id"
                  >
                    {{ cat.name }}
                  </option>
                </select>
              </div>
                            
              <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                  v-model="form.description"
                  rows="2"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                />
              </div>
                            
              <div class="flex items-center">
                <input
                  v-model="form.is_featured"
                  type="checkbox"
                  class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded"
                >
                <label class="ml-2 block text-sm text-gray-900">Featured Hashtag</label>
              </div>
            </div>
          </div>
                    
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
            <button
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-burgundy text-base font-medium text-white hover:bg-burgundy-dark sm:col-start-2 sm:text-sm"
              @click="saveHashtag"
            >
              {{ showEditModal ? 'Update' : 'Create' }}
            </button>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:col-start-1 sm:text-sm"
              @click="closeModal"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const hashtags = ref([]);
const categories = ref([]);
const showAddModal = ref(false);
const showEditModal = ref(false);
const filterCategory = ref('');
const filterFeatured = ref(false);
const form = ref({
    name: '',
    category_id: null,
    description: '',
    is_featured: false,
});
const editingId = ref(null);

onMounted(() => {
    fetchCategories();
    fetchHashtags();
});

const fetchCategories = async () => {
    try {
        const response = await api.get('/admin/photo-categories');
        categories.value = response.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

const fetchHashtags = async () => {
    try {
        const params = {};
        if (filterCategory.value) params.category_id = filterCategory.value;
        if (filterFeatured.value) params.is_featured = true;
        
        const response = await api.get('/admin/hashtags', { params });
        hashtags.value = response.data;
    } catch (error) {
        console.error('Error fetching hashtags:', error);
    }
};

const filterHashtags = () => {
    fetchHashtags();
};

const saveHashtag = async () => {
    try {
        if (showEditModal.value) {
            await api.put(`/admin/hashtags/${editingId.value}`, form.value);
        } else {
            await api.post('/admin/hashtags', form.value);
        }
        fetchHashtags();
        closeModal();
    } catch (error) {
        console.error('Error saving hashtag:', error);
        alert('Error saving hashtag');
    }
};

const editHashtag = (hashtag) => {
    form.value = { 
        name: hashtag.name,
        category_id: hashtag.category_id,
        description: hashtag.description,
        is_featured: hashtag.is_featured,
    };
    editingId.value = hashtag.id;
    showEditModal.value = true;
};

const deleteHashtag = async (id) => {
    if (confirm('Are you sure you want to delete this hashtag?')) {
        try {
            await api.delete(`/admin/hashtags/${id}`);
            fetchHashtags();
        } catch (error) {
            console.error('Error deleting hashtag:', error);
            alert('Error deleting hashtag');
        }
    }
};

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    form.value = {
        name: '',
        category_id: null,
        description: '',
        is_featured: false,
    };
    editingId.value = null;
};
</script>
