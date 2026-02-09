<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Featured Hashtags
          </h1>
          <p class="text-gray-600 mt-2">
            Curate and highlight trending hashtags
          </p>
        </div>
        <button
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          @click="showCreateModal = true"
        >
          + Feature Hashtag
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
              <option value="active">
                Active
              </option>
              <option value="scheduled">
                Scheduled
              </option>
              <option value="expired">
                Expired
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
              <option value="style">
                Style
              </option>
              <option value="genre">
                Genre
              </option>
              <option value="event">
                Event
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
              <option value="recent">
                Most Recent
              </option>
              <option value="usage">
                Most Used
              </option>
              <option value="expires">
                Ending Soon
              </option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Featured
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ featuredTags.length }}
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
              Scheduled
            </p>
            <p class="text-2xl font-bold text-blue-600">
              {{ scheduledCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Expired
            </p>
            <p class="text-2xl font-bold text-gray-600">
              {{ expiredCount }}
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
                    End Date
                  </th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="tag in filteredTags"
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
                  <td class="px-6 py-4 text-sm text-gray-700 capitalize">
                    {{ tag.category }}
                  </td>
                  <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                    {{ tag.usage }}
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', getStatusClass(tag.status)]">
                      {{ tag.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(tag.endDate) }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
                        @click="editTag(tag.id)"
                      >
                        Edit
                      </button>
                      <button
                        v-if="tag.status === 'active'"
                        class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                        @click="endTag(tag.id)"
                      >
                        End
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div
            v-if="filteredTags.length === 0"
            class="text-center py-12"
          >
            <p class="text-gray-600 text-lg">
              No featured hashtags found
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
          Feature Hashtag
        </h2>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Hashtag name"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.description"
          type="text"
          placeholder="Short description"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <select
          v-model="formData.category"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="style">
            Style
          </option>
          <option value="genre">
            Genre
          </option>
          <option value="event">
            Event
          </option>
        </select>
        <div class="flex gap-3 pt-4">
          <button
            class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
            @click="showCreateModal = false"
          >
            Cancel
          </button>
          <button
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            @click="createTag"
          >
            Create
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
import { formatDate as formatDateValue } from '../../utils/formatters'
const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedStatus = ref('')
const selectedCategory = ref('')
const searchQuery = ref('')
const sortBy = ref('recent')
const showCreateModal = ref(false)

const featuredTags = ref([
  { id: 1, name: 'wedding', description: 'Wedding season highlights', category: 'event', usage: 1245, status: 'active', endDate: '2026-03-01' },
  { id: 2, name: 'portrait', description: 'Top portrait trends', category: 'style', usage: 980, status: 'scheduled', endDate: '2026-04-01' },
  { id: 3, name: 'street', description: 'Street photography week', category: 'genre', usage: 740, status: 'expired', endDate: '2026-01-15' }
])

const formData = ref({ name: '', description: '', category: 'style' })

const filteredTags = computed(() => {
  let result = featuredTags.value.filter(tag => {
    const statusMatch = !selectedStatus.value || tag.status === selectedStatus.value
    const categoryMatch = !selectedCategory.value || tag.category === selectedCategory.value
    const searchMatch = !searchQuery.value || tag.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return statusMatch && categoryMatch && searchMatch
  })

  if (sortBy.value === 'usage') {
    result = result.sort((a, b) => b.usage - a.usage)
  } else if (sortBy.value === 'expires') {
    result = result.sort((a, b) => new Date(a.endDate) - new Date(b.endDate))
  } else {
    result = result.sort((a, b) => new Date(b.endDate) - new Date(a.endDate))
  }

  return result
})

const activeCount = computed(() => featuredTags.value.filter(t => t.status === 'active').length)
const scheduledCount = computed(() => featuredTags.value.filter(t => t.status === 'scheduled').length)
const expiredCount = computed(() => featuredTags.value.filter(t => t.status === 'expired').length)

const getStatusClass = (status) => {
  const classes = { active: 'bg-green-500', scheduled: 'bg-blue-500', expired: 'bg-gray-500' }
  return classes[status] || 'bg-gray-500'
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const editTag = (id) => {
  toast.value = { show: true, message: `Editing featured hashtag ${id}...`, type: 'info' }
}

const endTag = (id) => {
  const tag = featuredTags.value.find(t => t.id === id)
  if (tag) {
    tag.status = 'expired'
    toast.value = { show: true, message: `#${tag.name} ended.`, type: 'success' }
  }
}

const createTag = () => {
  if (!formData.value.name) {
    toast.value = { show: true, message: 'Hashtag name is required.', type: 'error' }
    return
  }
  featuredTags.value.unshift({
    id: Math.max(...featuredTags.value.map(t => t.id)) + 1,
    name: formData.value.name.toLowerCase(),
    description: formData.value.description || 'Featured hashtag',
    category: formData.value.category,
    usage: 0,
    status: 'scheduled',
    endDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
  })
  formData.value = { name: '', description: '', category: 'style' }
  showCreateModal.value = false
  toast.value = { show: true, message: 'Featured hashtag scheduled.', type: 'success' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 700)
})
</script>
