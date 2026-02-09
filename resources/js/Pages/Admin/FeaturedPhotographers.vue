<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Featured Photographers
          </h1>
          <p class="text-gray-600 mt-2">
            Manage featured listings and promotions
          </p>
        </div>
        <button
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          @click="showCreateModal = true"
        >
          + Feature Photographer
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Tier</label>
            <select
              v-model="selectedTier"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Tiers
              </option>
              <option value="gold">
                Gold
              </option>
              <option value="silver">
                Silver
              </option>
              <option value="bronze">
                Bronze
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search photographers..."
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
              <option value="expires">
                Ending Soon
              </option>
              <option value="rating">
                Highest Rated
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
              {{ featured.length }}
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
          class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        >
          <div
            v-for="item in filteredFeatured"
            :key="item.id"
            class="bg-white rounded-lg shadow-md p-6"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">
                  {{ item.name }}
                </h3>
                <p class="text-sm text-gray-600">
                  {{ item.location }}
                </p>
              </div>
              <div class="text-right">
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold text-white', getStatusClass(item.status)]">
                  {{ item.status }}
                </span>
                <span class="block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-900">
                  {{ item.tier }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-4">
              <div>Rating: <span class="font-medium text-gray-900">{{ item.rating }}</span></div>
              <div>Bookings: <span class="font-medium text-gray-900">{{ item.bookings }}</span></div>
              <div>Start: <span class="font-medium text-gray-900">{{ formatDate(item.startDate) }}</span></div>
              <div>End: <span class="font-medium text-gray-900">{{ formatDate(item.endDate) }}</span></div>
            </div>

            <div class="flex gap-2">
              <button
                class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"
                @click="viewProfile(item.id)"
              >
                View
              </button>
              <button
                class="flex-1 px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700"
                @click="editFeature(item.id)"
              >
                Edit
              </button>
              <button
                v-if="item.status === 'active'"
                class="flex-1 px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700"
                @click="endFeature(item.id)"
              >
                End
              </button>
            </div>
          </div>
        </div>

        <div
          v-if="!loading && filteredFeatured.length === 0"
          class="text-center py-12 bg-white rounded-lg"
        >
          <p class="text-gray-600 text-lg">
            No featured photographers found
          </p>
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
          Feature Photographer
        </h2>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Photographer Name"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.location"
          type="text"
          placeholder="Location"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <select
          v-model="formData.tier"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="gold">
            Gold
          </option>
          <option value="silver">
            Silver
          </option>
          <option value="bronze">
            Bronze
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
            @click="createFeature"
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
const selectedTier = ref('')
const searchQuery = ref('')
const sortBy = ref('recent')
const showCreateModal = ref(false)

const featured = ref([
  { id: 1, name: 'Emma Johnson', location: 'New York, NY', rating: 4.9, bookings: 124, tier: 'gold', status: 'active', startDate: '2026-01-10', endDate: '2026-03-10' },
  { id: 2, name: 'Liam Carter', location: 'Los Angeles, CA', rating: 4.7, bookings: 98, tier: 'silver', status: 'scheduled', startDate: '2026-02-20', endDate: '2026-04-20' },
  { id: 3, name: 'Sophia Lee', location: 'Chicago, IL', rating: 4.6, bookings: 72, tier: 'bronze', status: 'expired', startDate: '2025-11-01', endDate: '2026-01-01' }
])

const formData = ref({ name: '', location: '', tier: 'gold' })

const filteredFeatured = computed(() => {
  let result = featured.value.filter(item => {
    const statusMatch = !selectedStatus.value || item.status === selectedStatus.value
    const tierMatch = !selectedTier.value || item.tier === selectedTier.value
    const searchMatch = !searchQuery.value || item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return statusMatch && tierMatch && searchMatch
  })

  if (sortBy.value === 'expires') {
    result = result.sort((a, b) => new Date(a.endDate) - new Date(b.endDate))
  } else if (sortBy.value === 'rating') {
    result = result.sort((a, b) => b.rating - a.rating)
  } else {
    result = result.sort((a, b) => new Date(b.startDate) - new Date(a.startDate))
  }

  return result
})

const activeCount = computed(() => featured.value.filter(f => f.status === 'active').length)
const scheduledCount = computed(() => featured.value.filter(f => f.status === 'scheduled').length)
const expiredCount = computed(() => featured.value.filter(f => f.status === 'expired').length)

const getStatusClass = (status) => {
  const classes = { active: 'bg-green-500', scheduled: 'bg-blue-500', expired: 'bg-gray-500' }
  return classes[status] || 'bg-gray-500'
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const viewProfile = (id) => {
  toast.value = { show: true, message: `Viewing photographer ${id}...`, type: 'info' }
}

const editFeature = (id) => {
  toast.value = { show: true, message: `Editing feature ${id}...`, type: 'info' }
}

const endFeature = (id) => {
  const item = featured.value.find(f => f.id === id)
  if (item) {
    item.status = 'expired'
    toast.value = { show: true, message: `${item.name} feature ended.`, type: 'success' }
  }
}

const createFeature = () => {
  if (!formData.value.name) {
    toast.value = { show: true, message: 'Photographer name is required.', type: 'error' }
    return
  }
  featured.value.unshift({
    id: Math.max(...featured.value.map(f => f.id)) + 1,
    name: formData.value.name,
    location: formData.value.location || 'Unknown',
    rating: 4.5,
    bookings: 0,
    tier: formData.value.tier,
    status: 'scheduled',
    startDate: new Date().toISOString().split('T')[0],
    endDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
  })
  formData.value = { name: '', location: '', tier: 'gold' }
  showCreateModal.value = false
  toast.value = { show: true, message: 'Featured photographer scheduled.', type: 'success' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 700)
})
</script>
