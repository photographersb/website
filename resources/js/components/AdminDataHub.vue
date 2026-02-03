<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="🗂️ Data Hub" 
      subtitle="Manage Cities, Categories & Hashtags"
      :show-back="true"
    />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Tabs -->
      <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
          <nav class="flex -mb-px">
            <button
              @click="activeTab = 'cities'"
              :class="['px-6 py-4 text-sm font-medium border-b-2', activeTab === 'cities' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700']"
            >
              Cities ({{ cities.length }})
            </button>
            <button
              @click="activeTab = 'categories'"
              :class="['px-6 py-4 text-sm font-medium border-b-2', activeTab === 'categories' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700']"
            >
              Categories ({{ categories.length }})
            </button>
            <button
              @click="activeTab = 'hashtags'"
              :class="['px-6 py-4 text-sm font-medium border-b-2', activeTab === 'hashtags' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700']"
            >
              Hashtags ({{ hashtags.length }})
            </button>
          </nav>
        </div>

        <!-- Cities Tab -->
        <div v-if="activeTab === 'cities'" class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Cities Management</h2>
            <button @click="showAddCity = true" class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark">
              + Add City
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">State</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photographers</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="city in cities" :key="city.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ city.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.slug }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.state || '-' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.photographers_count || 0 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <button @click="editCity(city)" class="text-burgundy hover:text-burgundy-dark">Edit</button>
                    <button @click="deleteCity(city)" class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Categories Tab -->
        <div v-if="activeTab === 'categories'" class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Categories Management</h2>
            <button @click="showAddCategory = true" class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark">
              + Add Category
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photographers</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="category in categories" :key="category.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ category.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.slug }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.icon || '-' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.photographers_count || 0 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="category.is_active ? 'status-active' : 'status-inactive'">
                      {{ category.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <button @click="editCategory(category)" class="text-burgundy hover:text-burgundy-dark">Edit</button>
                    <button @click="deleteCategory(category)" class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Hashtags Tab -->
        <div v-if="activeTab === 'hashtags'" class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Hashtags Management</h2>
            <button @click="showAddHashtag = true" class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark">
              + Add Hashtag
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tag</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage Count</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Featured</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="hashtag in hashtags" :key="hashtag.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ hashtag.tag }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ hashtag.usage_count || 0 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="hashtag.is_featured ? 'status-featured' : 'badge bg-gray-100 text-gray-800'">
                      {{ hashtag.is_featured ? 'Featured' : 'Normal' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <button @click="editHashtag(hashtag)" class="text-burgundy hover:text-burgundy-dark">Edit</button>
                    <button @click="deleteHashtag(hashtag)" class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit City Modal -->
    <div v-if="showAddCity || editingCity" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">{{ editingCity ? 'Edit City' : 'Add New City' }}</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">City Name *</label>
            <input v-model="cityForm.name" type="text" class="w-full border rounded px-4 py-2" placeholder="e.g., Dhaka" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">State/Division</label>
            <input v-model="cityForm.state" type="text" class="w-full border rounded px-4 py-2" placeholder="e.g., Dhaka Division" />
          </div>
          <div class="flex gap-4">
            <button @click="saveCity" class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark">
              {{ editingCity ? 'Update' : 'Create' }}
            </button>
            <button @click="closeCityModal" class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div v-if="showAddCategory || editingCategory" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">{{ editingCategory ? 'Edit Category' : 'Add New Category' }}</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Category Name *</label>
            <input v-model="categoryForm.name" type="text" class="w-full border rounded px-4 py-2" placeholder="e.g., Wedding Photography" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea v-model="categoryForm.description" rows="3" class="w-full border rounded px-4 py-2"></textarea>
          </div>
          <div>
            <label class="flex items-center gap-2">
              <input v-model="categoryForm.is_active" type="checkbox" class="rounded" />
              <span class="text-sm font-medium">Active</span>
            </label>
          </div>
          <div class="flex gap-4">
            <button @click="saveCategory" class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark">
              {{ editingCategory ? 'Update' : 'Create' }}
            </button>
            <button @click="closeCategoryModal" class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Hashtag Modal -->
    <div v-if="showAddHashtag || editingHashtag" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">{{ editingHashtag ? 'Edit Hashtag' : 'Add New Hashtag' }}</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Hashtag *</label>
            <input v-model="hashtagForm.tag" type="text" class="w-full border rounded px-4 py-2" placeholder="e.g., weddingphotography" />
            <p class="text-xs text-gray-500 mt-1">Enter without the # symbol</p>
          </div>
          <div>
            <label class="flex items-center gap-2">
              <input v-model="hashtagForm.is_featured" type="checkbox" class="rounded" />
              <span class="text-sm font-medium">Featured</span>
            </label>
          </div>
          <div class="flex gap-4">
            <button @click="saveHashtag" class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark">
              {{ editingHashtag ? 'Update' : 'Create' }}
            </button>
            <button @click="closeHashtagModal" class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
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
import AdminHeader from './AdminHeader.vue';

const activeTab = ref('cities');
const cities = ref([]);
const categories = ref([]);
const hashtags = ref([]);

const showAddCity = ref(false);
const showAddCategory = ref(false);
const showAddHashtag = ref(false);

const editingCity = ref(null);
const editingCategory = ref(null);
const editingHashtag = ref(null);

const cityForm = ref({ name: '', state: '' });
const categoryForm = ref({ name: '', description: '', is_active: true });
const hashtagForm = ref({ tag: '', is_featured: false });

const fetchCities = async () => {
  try {
    const { data } = await api.get('/admin/cities');
    cities.value = data.data;
  } catch (error) {
    console.error('Error fetching cities:', error);
  }
};

const fetchCategories = async () => {
  try {
    const { data } = await api.get('/admin/categories');
    categories.value = data.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const fetchHashtags = async () => {
  try {
    const { data } = await api.get('/admin/hashtags');
    hashtags.value = data.data;
  } catch (error) {
    console.error('Error fetching hashtags:', error);
  }
};

const saveCity = async () => {
  try {
    if (editingCity.value) {
      await api.put(`/admin/cities/${editingCity.value.id}`, cityForm.value);
    } else {
      await api.post('/admin/cities', cityForm.value);
    }
    fetchCities();
    closeCityModal();
  } catch (error) {
    alert('Error saving city: ' + (error.response?.data?.message || error.message));
  }
};

const saveCategory = async () => {
  try {
    if (editingCategory.value) {
      await api.put(`/admin/categories/${editingCategory.value.id}`, categoryForm.value);
    } else {
      await api.post('/admin/categories', categoryForm.value);
    }
    fetchCategories();
    closeCategoryModal();
  } catch (error) {
    alert('Error saving category: ' + (error.response?.data?.message || error.message));
  }
};

const saveHashtag = async () => {
  try {
    if (editingHashtag.value) {
      await api.put(`/admin/hashtags/${editingHashtag.value.id}`, hashtagForm.value);
    } else {
      await api.post('/admin/hashtags', hashtagForm.value);
    }
    fetchHashtags();
    closeHashtagModal();
  } catch (error) {
    alert('Error saving hashtag: ' + (error.response?.data?.message || error.message));
  }
};

const editCity = (city) => {
  editingCity.value = city;
  cityForm.value = { name: city.name, state: city.state || '' };
};

const editCategory = (category) => {
  editingCategory.value = category;
  categoryForm.value = {
    name: category.name,
    description: category.description || '',
    is_active: category.is_active
  };
};

const editHashtag = (hashtag) => {
  editingHashtag.value = hashtag;
  hashtagForm.value = {
    tag: hashtag.tag,
    is_featured: hashtag.is_featured || false
  };
};

const deleteCity = async (city) => {
  if (!confirm(`Delete ${city.name}? This cannot be undone.`)) return;
  try {
    await api.delete(`/admin/cities/${city.id}`);
    fetchCities();
  } catch (error) {
    alert('Error: ' + (error.response?.data?.message || error.message));
  }
};

const deleteCategory = async (category) => {
  if (!confirm(`Delete ${category.name}? This cannot be undone.`)) return;
  try {
    await api.delete(`/admin/categories/${category.id}`);
    fetchCategories();
  } catch (error) {
    alert('Error: ' + (error.response?.data?.message || error.message));
  }
};

const deleteHashtag = async (hashtag) => {
  if (!confirm(`Delete #${hashtag.tag}? This cannot be undone.`)) return;
  try {
    await api.delete(`/admin/hashtags/${hashtag.id}`);
    fetchHashtags();
  } catch (error) {
    alert('Error: ' + (error.response?.data?.message || error.message));
  }
};

const closeCityModal = () => {
  showAddCity.value = false;
  editingCity.value = null;
  cityForm.value = { name: '', state: '' };
};

const closeCategoryModal = () => {
  showAddCategory.value = false;
  editingCategory.value = null;
  categoryForm.value = { name: '', description: '', is_active: true };
};

const closeHashtagModal = () => {
  showAddHashtag.value = false;
  editingHashtag.value = null;
  hashtagForm.value = { tag: '', is_featured: false };
};

onMounted(() => {
  fetchCities();
  fetchCategories();
  fetchHashtags();
});
</script>
