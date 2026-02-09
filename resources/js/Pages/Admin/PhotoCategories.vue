<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="mb-8 flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Photo Categories
            </h1>
            <p class="text-gray-600 mt-2">
              Manage photo category taxonomy and visibility
            </p>
          </div>
          <button
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            @click="openCreateModal"
          >
            + Add Category
          </button>
        </div>
        
        <AdminQuickNav />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="selectedStatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
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
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search categories..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort</label>
            <select
              v-model="sortBy"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="order">
                Display Order
              </option>
              <option value="alpha">
                Alphabetical
              </option>
              <option value="usage">
                Most Used
              </option>
              <option value="recent">
                Most Recent
              </option>
            </select>
          </div>
          
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Categories
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ categories.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Active
            </p>
            <p class="text-2xl font-bold text-green-600">
              {{ activeCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Inactive
            </p>
            <p class="text-2xl font-bold text-gray-600">
              {{ inactiveCount }}
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
                    Category
                  </th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                    Hashtags
                  </th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                    Order
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
                  v-for="category in filteredCategories"
                  :key="category.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">
                      {{ category.name }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ category.description }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                    {{ category.hashtags_count || 0 }}
                  </td>
                  <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                    {{ category.display_order || 0 }}
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', category.is_active ? 'bg-green-500' : 'bg-gray-500']">
                      {{ category.is_active ? 'active' : 'inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(category.updated_at) }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
                        @click="editCategory(category.id)"
                      >
                        Edit
                      </button>
                      <button
                        v-if="category.is_active"
                        class="px-3 py-1 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700"
                        @click="toggleCategory(category, false)"
                      >
                        Deactivate
                      </button>
                      <button
                        v-if="!category.is_active"
                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                        @click="toggleCategory(category, true)"
                      >
                        Activate
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div
            v-if="filteredCategories.length === 0"
            class="text-center py-12"
          >
            <p class="text-gray-600 text-lg">
              No categories found
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
          {{ isEditing ? 'Edit Category' : 'Add Category' }}
        </h2>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Category name"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.description"
          type="text"
          placeholder="Short description"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.icon"
          type="text"
          placeholder="Icon (optional)"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model.number="formData.display_order"
          type="number"
          min="0"
          placeholder="Display order"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input
            v-model="formData.is_active"
            type="checkbox"
            class="rounded text-blue-600"
          >
          Active
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
            @click="saveCategory"
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
const searchQuery = ref('')
const sortBy = ref('order')
const showCreateModal = ref(false)
const isEditing = ref(false)

const categories = ref([])
const editingId = ref(null)

const formData = ref({
  name: '',
  description: '',
  icon: '',
  display_order: 0,
  is_active: true
})

const filteredCategories = computed(() => {
  let result = categories.value.filter(category => {
    const statusMatch = !selectedStatus.value
      || (selectedStatus.value === 'active' && category.is_active)
      || (selectedStatus.value === 'inactive' && !category.is_active)
    const searchMatch = !searchQuery.value
      || category.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      || (category.description || '').toLowerCase().includes(searchQuery.value.toLowerCase())
    return statusMatch && searchMatch
  })

  if (sortBy.value === 'usage') {
    result = result.sort((a, b) => (b.hashtags_count || 0) - (a.hashtags_count || 0))
  } else if (sortBy.value === 'recent') {
    result = result.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))
  } else if (sortBy.value === 'alpha') {
    result = result.sort((a, b) => a.name.localeCompare(b.name))
  } else {
    result = result.sort((a, b) => (a.display_order || 0) - (b.display_order || 0))
  }

  return result
})

const activeCount = computed(() => categories.value.filter(c => c.is_active).length)
const inactiveCount = computed(() => categories.value.filter(c => !c.is_active).length)
const totalUsage = computed(() => categories.value.reduce((sum, c) => sum + (c.hashtags_count || 0), 0))

const formatDate = (date) => formatDateValue(date)

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
}

const resetForm = () => {
  formData.value = { name: '', description: '', icon: '', display_order: 0, is_active: true }
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
  loading.value = true
  try {
    const { data } = await api.get('/admin/photo-categories')
    categories.value = Array.isArray(data) ? data : []
  } catch (error) {
    console.error('Failed to load categories:', error)
    showToast('Failed to load categories.', 'error')
  } finally {
    loading.value = false
  }
}

const editCategory = (id) => {
  const category = categories.value.find(item => item.id === id)
  if (!category) return
  formData.value = {
    name: category.name,
    description: category.description || '',
    icon: category.icon || '',
    display_order: category.display_order || 0,
    is_active: !!category.is_active
  }
  editingId.value = id
  isEditing.value = true
  showCreateModal.value = true
}

const toggleCategory = async (category, isActive) => {
  try {
    await api.put(`/admin/photo-categories/${category.id}`, { is_active: isActive })
    await loadCategories()
    showToast(`Category ${isActive ? 'activated' : 'deactivated'} successfully.`)
  } catch (error) {
    console.error('Failed to update category:', error)
    showToast('Failed to update category.', 'error')
  }
}

const saveCategory = async () => {
  if (!formData.value.name) {
    showToast('Category name is required.', 'error')
    return
  }

  const payload = {
    name: formData.value.name,
    description: formData.value.description || null,
    icon: formData.value.icon || null,
    display_order: Number.isFinite(Number(formData.value.display_order)) ? Number(formData.value.display_order) : 0,
    is_active: !!formData.value.is_active
  }

  try {
    if (isEditing.value && editingId.value) {
      await api.put(`/admin/photo-categories/${editingId.value}`, payload)
      showToast('Category updated successfully!')
    } else {
      await api.post('/admin/photo-categories', payload)
      showToast('Category created successfully!')
    }
    showCreateModal.value = false
    resetForm()
    await loadCategories()
  } catch (error) {
    console.error('Failed to save category:', error)
    showToast('Failed to save category.', 'error')
  }
}

onMounted(() => {
  loadCategories()
})
</script>
