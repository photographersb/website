<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Cities
          </h1>
          <p class="text-gray-600 mt-2">
            Manage city coverage and availability
          </p>
        </div>
        <button
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          @click="showCreateModal = true"
        >
          + Add City
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
              <option value="paused">
                Paused
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
            <select
              v-model="selectedRegion"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Regions
              </option>
              <option value="north">
                North
              </option>
              <option value="south">
                South
              </option>
              <option value="east">
                East
              </option>
              <option value="west">
                West
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search cities..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort</label>
            <select
              v-model="sortBy"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="alpha">
                Alphabetical
              </option>
              <option value="photographers">
                Most Photographers
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
              Total Cities
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ cities.length }}
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
              Paused
            </p>
            <p class="text-2xl font-bold text-gray-600">
              {{ pausedCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Photographers
            </p>
            <p class="text-2xl font-bold text-blue-600">
              {{ totalPhotographers }}
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
                    City
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Region
                  </th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                    Photographers
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
                  v-for="city in filteredCities"
                  :key="city.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">
                      {{ city.name }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ city.state }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700 capitalize">
                    {{ city.region }}
                  </td>
                  <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                    {{ city.photographers }}
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', city.status === 'active' ? 'bg-green-500' : 'bg-gray-500']">
                      {{ city.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(city.updatedAt) }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
                        @click="editCity(city.id)"
                      >
                        Edit
                      </button>
                      <button
                        v-if="city.status === 'active'"
                        class="px-3 py-1 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700"
                        @click="pauseCity(city.id)"
                      >
                        Pause
                      </button>
                      <button
                        v-if="city.status !== 'active'"
                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                        @click="activateCity(city.id)"
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
            v-if="filteredCities.length === 0"
            class="text-center py-12"
          >
            <p class="text-gray-600 text-lg">
              No cities found
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
          Add City
        </h2>
        <input
          v-model="formData.name"
          type="text"
          placeholder="City name"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.state"
          type="text"
          placeholder="State/Province"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <select
          v-model="formData.region"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="north">
            North
          </option>
          <option value="south">
            South
          </option>
          <option value="east">
            East
          </option>
          <option value="west">
            West
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
            @click="createCity"
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
const selectedRegion = ref('')
const searchQuery = ref('')
const sortBy = ref('alpha')
const showCreateModal = ref(false)

const cities = ref([
  { id: 1, name: 'New York', state: 'NY', region: 'north', photographers: 124, status: 'active', updatedAt: '2026-02-03' },
  { id: 2, name: 'Los Angeles', state: 'CA', region: 'west', photographers: 98, status: 'active', updatedAt: '2026-02-02' },
  { id: 3, name: 'Chicago', state: 'IL', region: 'north', photographers: 76, status: 'paused', updatedAt: '2026-01-29' },
  { id: 4, name: 'Miami', state: 'FL', region: 'south', photographers: 54, status: 'active', updatedAt: '2026-01-25' }
])

const formData = ref({ name: '', state: '', region: 'north' })

const filteredCities = computed(() => {
  let result = cities.value.filter(city => {
    const statusMatch = !selectedStatus.value || city.status === selectedStatus.value
    const regionMatch = !selectedRegion.value || city.region === selectedRegion.value
    const searchMatch = !searchQuery.value || city.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return statusMatch && regionMatch && searchMatch
  })

  if (sortBy.value === 'photographers') {
    result = result.sort((a, b) => b.photographers - a.photographers)
  } else if (sortBy.value === 'recent') {
    result = result.sort((a, b) => new Date(b.updatedAt) - new Date(a.updatedAt))
  } else {
    result = result.sort((a, b) => a.name.localeCompare(b.name))
  }

  return result
})

const activeCount = computed(() => cities.value.filter(c => c.status === 'active').length)
const pausedCount = computed(() => cities.value.filter(c => c.status === 'paused').length)
const totalPhotographers = computed(() => cities.value.reduce((sum, c) => sum + c.photographers, 0))

const formatDate = (date) => {
  return formatDateValue(date)
}

const editCity = (id) => {
  toast.value = { show: true, message: `Editing city ${id}...`, type: 'info' }
}

const pauseCity = (id) => {
  const city = cities.value.find(c => c.id === id)
  if (city) {
    city.status = 'paused'
    toast.value = { show: true, message: `${city.name} paused.`, type: 'success' }
  }
}

const activateCity = (id) => {
  const city = cities.value.find(c => c.id === id)
  if (city) {
    city.status = 'active'
    toast.value = { show: true, message: `${city.name} activated.`, type: 'success' }
  }
}

const createCity = () => {
  if (!formData.value.name) {
    toast.value = { show: true, message: 'City name is required.', type: 'error' }
    return
  }
  cities.value.unshift({
    id: Math.max(...cities.value.map(c => c.id)) + 1,
    name: formData.value.name,
    state: formData.value.state || 'N/A',
    region: formData.value.region,
    photographers: 0,
    status: 'active',
    updatedAt: new Date().toISOString().split('T')[0]
  })
  formData.value = { name: '', state: '', region: 'north' }
  showCreateModal.value = false
  toast.value = { show: true, message: 'City created successfully!', type: 'success' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 700)
})
</script>
