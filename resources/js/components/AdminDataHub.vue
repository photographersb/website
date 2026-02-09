<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="🗂️ Data Hub" 
      subtitle="Manage Locations, Categories & Hashtags"
      :show-back="true"
    />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Tabs -->
      <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
          <nav class="flex -mb-px">
            <button
              :class="['px-6 py-4 text-sm font-medium border-b-2', activeTab === 'cities' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700']"
              @click="activeTab = 'cities'"
            >
              Locations ({{ cities.length }})
            </button>
            <button
              :class="['px-6 py-4 text-sm font-medium border-b-2', activeTab === 'categories' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700']"
              @click="activeTab = 'categories'"
            >
              Categories ({{ categories.length }})
            </button>
            <button
              :class="['px-6 py-4 text-sm font-medium border-b-2', activeTab === 'hashtags' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700']"
              @click="activeTab = 'hashtags'"
            >
              Hashtags ({{ hashtags.length }})
            </button>
          </nav>
        </div>

        <!-- Locations Tab -->
        <div
          v-if="activeTab === 'cities'"
          class="p-6"
        >
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">
              Locations Management
            </h2>
            <button
              class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark"
              @click="showAddCity = true"
            >
              + Add Location
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Name
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Slug
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Parent
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Photographers
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="city in cities"
                  :key="city.id"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ city.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ city.slug }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                    {{ city.type || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ city.parent?.name || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ city.photographers_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <button
                      class="text-burgundy hover:text-burgundy-dark"
                      @click="editCity(city)"
                    >
                      Edit
                    </button>
                    <button
                      class="text-red-600 hover:text-red-900"
                      @click="deleteCity(city)"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Categories Tab -->
        <div
          v-if="activeTab === 'categories'"
          class="p-6"
        >
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">
              Categories Management
            </h2>
            <button
              class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark"
              @click="showAddCategory = true"
            >
              + Add Category
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Name
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Slug
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Icon
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Photographers
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="category in categories"
                  :key="category.id"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ category.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ category.slug }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ category.icon || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ category.photographers_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="category.is_active ? 'status-active' : 'status-inactive'">
                      {{ category.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <button
                      class="text-burgundy hover:text-burgundy-dark"
                      @click="editCategory(category)"
                    >
                      Edit
                    </button>
                    <button
                      class="text-red-600 hover:text-red-900"
                      @click="deleteCategory(category)"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Hashtags Tab -->
        <div
          v-if="activeTab === 'hashtags'"
          class="p-6"
        >
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">
              Hashtags Management
            </h2>
            <button
              class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark"
              @click="showAddHashtag = true"
            >
              + Add Hashtag
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Tag
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Usage Count
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Featured
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="hashtag in hashtags"
                  :key="hashtag.id"
                >
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #{{ hashtag.tag }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ hashtag.usage_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="hashtag.is_featured ? 'status-featured' : 'badge bg-gray-100 text-gray-800'">
                      {{ hashtag.is_featured ? 'Featured' : 'Normal' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <button
                      class="text-burgundy hover:text-burgundy-dark"
                      @click="editHashtag(hashtag)"
                    >
                      Edit
                    </button>
                    <button
                      class="text-red-600 hover:text-red-900"
                      @click="deleteHashtag(hashtag)"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Location Modal -->
    <div
      v-if="showAddCity || editingCity"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">
          {{ editingCity ? 'Edit Location' : 'Add New Location' }}
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Location Name *</label>
            <input
              v-model="cityForm.name"
              type="text"
              class="w-full border rounded px-4 py-2"
              placeholder="e.g., Dhaka"
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Slug *</label>
            <input
              v-model="cityForm.slug"
              type="text"
              class="w-full border rounded px-4 py-2"
              placeholder="e.g., dhaka"
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Type *</label>
            <select
              v-model="cityForm.type"
              class="w-full border rounded px-4 py-2"
            >
              <option value="division">
                Division
              </option>
              <option value="district">
                District
              </option>
              <option value="upazila">
                Upazila
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Parent</label>
            <select
              v-model="cityForm.parent_id"
              class="w-full border rounded px-4 py-2"
              :disabled="availableParents.length === 0"
            >
              <option :value="null">
                None
              </option>
              <option
                v-for="parent in availableParents"
                :key="parent.id"
                :value="parent.id"
              >
                {{ parent.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="flex items-center gap-2">
              <input
                v-model="cityForm.is_active"
                type="checkbox"
                class="rounded"
              >
              <span class="text-sm font-medium">Active</span>
            </label>
          </div>
          <div class="flex gap-4">
            <button
              class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark"
              @click="saveCity"
            >
              {{ editingCity ? 'Update' : 'Create' }}
            </button>
            <button
              class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
              @click="closeCityModal"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div
      v-if="showAddCategory || editingCategory"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">
          {{ editingCategory ? 'Edit Category' : 'Add New Category' }}
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Category Name *</label>
            <input
              v-model="categoryForm.name"
              type="text"
              class="w-full border rounded px-4 py-2"
              placeholder="e.g., Wedding Photography"
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea
              v-model="categoryForm.description"
              rows="3"
              class="w-full border rounded px-4 py-2"
            />
          </div>
          <div>
            <label class="flex items-center gap-2">
              <input
                v-model="categoryForm.is_active"
                type="checkbox"
                class="rounded"
              >
              <span class="text-sm font-medium">Active</span>
            </label>
          </div>
          <div class="flex gap-4">
            <button
              class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark"
              @click="saveCategory"
            >
              {{ editingCategory ? 'Update' : 'Create' }}
            </button>
            <button
              class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
              @click="closeCategoryModal"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Hashtag Modal -->
    <div
      v-if="showAddHashtag || editingHashtag"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-4">
          {{ editingHashtag ? 'Edit Hashtag' : 'Add New Hashtag' }}
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">Hashtag *</label>
            <input
              v-model="hashtagForm.tag"
              type="text"
              class="w-full border rounded px-4 py-2"
              placeholder="e.g., weddingphotography"
            >
            <p class="text-xs text-gray-500 mt-1">
              Enter without the # symbol
            </p>
          </div>
          <div>
            <label class="flex items-center gap-2">
              <input
                v-model="hashtagForm.is_featured"
                type="checkbox"
                class="rounded"
              >
              <span class="text-sm font-medium">Featured</span>
            </label>
          </div>
          <div class="flex gap-4">
            <button
              class="flex-1 px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark"
              @click="saveHashtag"
            >
              {{ editingHashtag ? 'Update' : 'Create' }}
            </button>
            <button
              class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
              @click="closeHashtagModal"
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
import { ref, onMounted, computed, watch } from 'vue';
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

const cityForm = ref({
  name: '',
  slug: '',
  type: 'district',
  parent_id: null,
  sort_order: 0,
  is_active: true
});

const parentOptions = ref([]);

const availableParents = computed(() => {
  if (cityForm.value.type === 'district') {
    return parentOptions.value.filter(parent => parent.type === 'division');
  }
  if (cityForm.value.type === 'upazila') {
    return parentOptions.value.filter(parent => parent.type === 'district');
  }
  return [];
});
const categoryForm = ref({ name: '', description: '', is_active: true });
const hashtagForm = ref({ tag: '', is_featured: false });

const fetchCities = async () => {
  try {
    const { data } = await api.get('/admin/locations');
    cities.value = data.data.data || data.data || [];
  } catch (error) {
    console.error('Error fetching locations:', error);
  }
};

const fetchParentLocations = async () => {
  try {
    const { data } = await api.get('/admin/locations', { params: { minimal: true } });
    parentOptions.value = data.data || [];
  } catch (error) {
    console.error('Error fetching parent locations:', error);
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
      await api.put(`/admin/locations/${editingCity.value.id}`, {
        ...cityForm.value,
        parent_id: cityForm.value.parent_id || null
      });
    } else {
      await api.post('/admin/locations', {
        ...cityForm.value,
        parent_id: cityForm.value.parent_id || null
      });
    }
    fetchCities();
    closeCityModal();
  } catch (error) {
    alert('Error saving location: ' + (error.response?.data?.message || error.message));
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
  cityForm.value = {
    name: city.name,
    slug: city.slug || '',
    type: city.type || 'district',
    parent_id: city.parent_id || null,
    sort_order: city.sort_order ?? 0,
    is_active: city.is_active ?? true
  };
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
    await api.delete(`/admin/locations/${city.id}`);
    fetchCities();
  } catch (error) {
    alert('Error deleting location: ' + (error.response?.data?.message || error.message));
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
  cityForm.value = {
    name: '',
    slug: '',
    type: 'district',
    parent_id: null,
    sort_order: 0,
    is_active: true
  };
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

watch(
  () => cityForm.value.type,
  () => {
    if (availableParents.value.length === 0) {
      cityForm.value.parent_id = null;
      return;
    }

    const isValidParent = availableParents.value.some(parent => parent.id === cityForm.value.parent_id);
    if (!isValidParent) {
      cityForm.value.parent_id = null;
    }
  }
);

onMounted(() => {
  fetchCities();
  fetchParentLocations();
  fetchCategories();
  fetchHashtags();
});
</script>
