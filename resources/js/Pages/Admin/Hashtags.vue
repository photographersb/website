<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="mb-8 flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Hashtags
            </h1>
            <p class="text-gray-600 mt-2">
              Manage platform hashtags and trending tags
            </p>
          </div>
          <button
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            @click="openCreateModal"
          >
            + Add Hashtag
          </button>
        </div>
        
        <AdminQuickNav />

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="selectedStatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Status
              </option>
              <option value="featured">
                Featured
              </option>
              <option value="standard">
                Standard
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="selectedCategory"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Categories
              </option>
              <option
                v-for="category in categories"
                :key="category.id"
                :value="String(category.id)"
              >
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search hashtags..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort</label>
            <select
              v-model="sortBy"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="usage">
                Most Used
              </option>
              <option value="recent">
                Most Recent
              </option>
              <option value="alpha">
                Alphabetical
              </option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Hashtags
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ hashtags.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Featured
            </p>
            <p class="text-2xl font-bold text-green-600">
              {{ featuredCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Standard
            </p>
            <p class="text-2xl font-bold text-gray-600">
              {{ standardCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Usage
            </p>
            <p class="text-2xl font-bold text-blue-600">
              {{ totalUsage }}
            </p>
          </div>
        </div>

        <div
          v-if="!loading"
          class="bg-white rounded-lg shadow-md overflow-hidden"
        >
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Hashtag
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Category
                  </th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                    Usage
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Updated
                  </th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="tag in filteredHashtags"
                  :key="tag.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">
                      #{{ tag.name }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ tag.description }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700">
                    {{ tag.category?.name || 'Uncategorized' }}
                  </td>
                  <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                    {{ tag.usage_count || 0 }}
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', tag.is_featured ? 'bg-green-500' : 'bg-gray-500']">
                      {{ tag.is_featured ? 'featured' : 'standard' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(tag.updated_at) }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
                        @click="editHashtag(tag.id)"
                      >
                        Edit
                      </button>
                      <button
                        v-if="!tag.is_featured"
                        class="px-3 py-1 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700"
                        @click="toggleFeatured(tag, true)"
                      >
                        Feature
                      </button>
                      <button
                        v-if="tag.is_featured"
                        class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
                        @click="toggleFeatured(tag, false)"
                      >
                        Unfeature
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div
            v-if="filteredHashtags.length === 0"
            class="text-center py-12"
          >
            <p class="text-gray-600 text-lg">
              No hashtags found
            </p>
          </div>
        </div>

        <div
          v-if="loading"
          class="flex justify-center py-12"
        >
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
        </div>
    </div>

    <div
      v-if="showCreateModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 space-y-4">
        <h2 class="text-xl font-bold text-gray-900">
          {{ isEditing ? 'Edit Hashtag' : 'Add Hashtag' }}
        </h2>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Hashtag name (no #)"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.description"
          type="text"
          placeholder="Short description"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <select
          v-model="formData.category_id"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="">
            Uncategorized
          </option>
          <option
            v-for="category in categories"
            :key="category.id"
            :value="String(category.id)"
          >
            {{ category.name }}
          </option>
        </select>
        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input
            v-model="formData.is_featured"
            type="checkbox"
            class="rounded text-blue-600"
          >
          Featured
        </label>
        <div class="flex gap-3 pt-4">
          <button
            class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
            @click="closeModal"
          >
            Cancel
          </button>
          <button
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            @click="saveHashtag"
          >
            {{ isEditing ? 'Save' : 'Create' }}
          </button>
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
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import api from '../../api'
import { formatDate as formatDateValue } from '../../utils/formatters'

const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedStatus = ref('')
const selectedCategory = ref('')
const searchQuery = ref('')
const sortBy = ref('usage')
const showCreateModal = ref(false)
const isEditing = ref(false)

const hashtags = ref([])
const categories = ref([])
const editingId = ref(null)

const formData = ref({
  name: '',
  description: '',
  category_id: '',
  is_featured: false
})

const filteredHashtags = computed(() => {
  let result = hashtags.value.filter(tag => {
    const statusMatch = !selectedStatus.value
      || (selectedStatus.value === 'featured' && tag.is_featured)
      || (selectedStatus.value === 'standard' && !tag.is_featured)
    const categoryMatch = !selectedCategory.value || String(tag.category_id || '') === selectedCategory.value
    const searchMatch = !searchQuery.value
      || tag.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      || (tag.description || '').toLowerCase().includes(searchQuery.value.toLowerCase())
    return statusMatch && categoryMatch && searchMatch
  })

  if (sortBy.value === 'alpha') {
    result = result.sort((a, b) => a.name.localeCompare(b.name))
  } else if (sortBy.value === 'recent') {
    result = result.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))
  } else {
    result = result.sort((a, b) => (b.usage_count || 0) - (a.usage_count || 0))
  }

  return result
})

const featuredCount = computed(() => hashtags.value.filter(h => h.is_featured).length)
const standardCount = computed(() => hashtags.value.filter(h => !h.is_featured).length)
const totalUsage = computed(() => hashtags.value.reduce((sum, h) => sum + (h.usage_count || 0), 0))

const formatDate = (date) => formatDateValue(date)

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
}

const resetForm = () => {
  formData.value = { name: '', description: '', category_id: '', is_featured: false }
  editingId.value = null
  isEditing.value = false
}

const openCreateModal = () => {
  resetForm()
  showCreateModal.value = true
}

const closeModal = () => {
  showCreateModal.value = false
  resetForm()
}

const loadCategories = async () => {
  try {
    const { data } = await api.get('/admin/photo-categories')
    categories.value = Array.isArray(data) ? data : []
  } catch (error) {
    console.error('Failed to load categories:', error)
    showToast('Failed to load categories.', 'error')
  }
}

const loadHashtags = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/hashtags')
    hashtags.value = Array.isArray(data) ? data : []
  } catch (error) {
    console.error('Failed to load hashtags:', error)
    showToast('Failed to load hashtags.', 'error')
  } finally {
    loading.value = false
  }
}

const editHashtag = (id) => {
  const tag = hashtags.value.find(item => item.id === id)
  if (!tag) return
  formData.value = {
    name: tag.name,
    description: tag.description || '',
    category_id: tag.category_id ? String(tag.category_id) : '',
    is_featured: !!tag.is_featured
  }
  editingId.value = id
  isEditing.value = true
  showCreateModal.value = true
}

const toggleFeatured = async (tag, makeFeatured) => {
  try {
    await api.put(`/admin/hashtags/${tag.id}`, { is_featured: makeFeatured })
    await loadHashtags()
    showToast(`Hashtag ${makeFeatured ? 'featured' : 'unfeatured'} successfully.`)
  } catch (error) {
    console.error('Failed to update hashtag:', error)
    showToast('Failed to update hashtag.', 'error')
  }
}

const saveHashtag = async () => {
  if (!formData.value.name) {
    showToast('Hashtag name is required.', 'error')
    return
  }

  const payload = {
    name: formData.value.name,
    description: formData.value.description || null,
    category_id: formData.value.category_id ? Number(formData.value.category_id) : null,
    is_featured: !!formData.value.is_featured
  }

  try {
    if (isEditing.value && editingId.value) {
      await api.put(`/admin/hashtags/${editingId.value}`, payload)
      showToast('Hashtag updated successfully!')
    } else {
      await api.post('/admin/hashtags', payload)
      showToast('Hashtag created successfully!')
    }
    showCreateModal.value = false
    resetForm()
    await loadHashtags()
  } catch (error) {
    console.error('Failed to save hashtag:', error)
    showToast('Failed to save hashtag.', 'error')
  }
}

onMounted(async () => {
  await Promise.all([loadCategories(), loadHashtags()])
})
</script>
