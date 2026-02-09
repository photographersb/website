<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Platform Sponsors
          </h1>
          <p class="text-gray-600 mt-2">
            Manage platform sponsors and logo placements
          </p>
        </div>
        <button
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          @click="openCreate"
        >
          + New Sponsor
        </button>
      </div>

      <AdminQuickNav />

      <!-- Filters -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select
            v-model="selectedStatus"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
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
            placeholder="Search sponsors..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          >
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
          <p class="text-gray-600 text-sm">
            Total Sponsors
          </p>
          <p class="text-2xl font-bold text-gray-900">
            {{ sponsors.length }}
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
            Expiring Soon
          </p>
          <p class="text-2xl font-bold text-orange-600">
            {{ expiringSoon }}
          </p>
        </div>
      </div>

      <!-- Sponsors Table -->
      <div
        v-if="!loading"
        class="bg-white rounded-lg shadow overflow-hidden"
      >
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Sponsor</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Website</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Start</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">End</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Order</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-if="filteredSponsors.length === 0"
                class="hover:bg-gray-50"
              >
                <td
                  colspan="7"
                  class="px-6 py-8 text-center text-gray-500"
                >
                  No sponsors found.
                </td>
              </tr>
              <tr
                v-for="sponsor in filteredSponsors"
                :key="sponsor.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img
                      v-if="sponsor.logo"
                      :src="sponsor.logo"
                      :alt="sponsor.name"
                      class="w-10 h-10 rounded-full object-cover"
                    >
                    <div>
                      <div class="font-semibold text-gray-900">{{ sponsor.name }}</div>
                      <div class="text-xs text-gray-500">{{ sponsor.logo_credit_name || '—' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ sponsor.website || '—' }}
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="sponsor.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                    class="px-3 py-1 rounded-full text-xs font-medium"
                  >
                    {{ sponsor.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ formatDate(sponsor.start_date) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ formatDate(sponsor.end_date) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ sponsor.display_order }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end gap-2">
                    <button
                      class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                      @click="openEdit(sponsor)"
                    >
                      Edit
                    </button>
                    <button
                      class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                      @click="toggleStatus(sponsor)"
                    >
                      {{ sponsor.status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>
                    <button
                      class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                      @click="deleteSponsor(sponsor.id)"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Loading -->
      <div
        v-if="loading"
        class="flex justify-center py-12"
      >
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-lg max-w-lg w-full p-6 space-y-4">
        <h2 class="text-2xl font-bold text-gray-900">
          {{ editingId ? 'Edit Sponsor' : 'New Sponsor' }}
        </h2>

        <input
          v-model="formData.name"
          type="text"
          placeholder="Sponsor Name"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.website"
          type="url"
          placeholder="Website"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.logo"
          type="text"
          placeholder="Logo URL"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.logo_credit_name"
          type="text"
          placeholder="Logo Credit Name"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.logo_credit_url"
          type="url"
          placeholder="Logo Credit URL"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <textarea
          v-model="formData.description"
          rows="3"
          placeholder="Description"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        />

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label>
            <input
              v-model="formData.start_date"
              type="date"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">End Date</label>
            <input
              v-model="formData.end_date"
              type="date"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select
              v-model="formData.status"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="active">
                Active
              </option>
              <option value="inactive">
                Inactive
              </option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Display Order</label>
            <input
              v-model.number="formData.display_order"
              type="number"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input
            v-model="formData.is_featured"
            type="checkbox"
          >
          Featured
        </label>

        <div class="flex gap-3 pt-2">
          <button
            class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
            @click="closeModal"
          >
            Cancel
          </button>
          <button
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            @click="saveSponsor"
          >
            {{ editingId ? 'Update' : 'Create' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import { formatDate as formatDateValue } from '../../utils/formatters'
import api from '../../api'

const loading = ref(true)
const showModal = ref(false)
const editingId = ref(null)
const selectedStatus = ref('')
const searchQuery = ref('')

const sponsors = ref([])

const formData = ref({
  name: '',
  website: '',
  logo: '',
  logo_credit_name: '',
  logo_credit_url: '',
  description: '',
  status: 'active',
  display_order: 0,
  start_date: '',
  end_date: '',
  is_featured: false
})

const filteredSponsors = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  return sponsors.value.filter((sponsor) => {
    const statusMatch = !selectedStatus.value || sponsor.status === selectedStatus.value
    const searchMatch = !query || sponsor.name.toLowerCase().includes(query)
    return statusMatch && searchMatch
  })
})

const activeCount = computed(() => sponsors.value.filter((s) => s.status === 'active').length)
const inactiveCount = computed(() => sponsors.value.filter((s) => s.status === 'inactive').length)
const expiringSoon = computed(() => {
  const now = new Date()
  return sponsors.value.filter((s) => {
    if (!s.end_date) return false
    const endDate = new Date(s.end_date)
    const days = Math.ceil((endDate - now) / 86400000)
    return days >= 0 && days <= 30
  }).length
})

const formatDate = (date) => (date ? formatDateValue(date) : '—')

const fetchSponsors = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/platform-sponsors')
    sponsors.value = Array.isArray(data.data) ? data.data : []
  } catch (error) {
    console.error('Failed to load sponsors:', error)
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  formData.value = {
    name: '',
    website: '',
    logo: '',
    logo_credit_name: '',
    logo_credit_url: '',
    description: '',
    status: 'active',
    display_order: 0,
    start_date: '',
    end_date: '',
    is_featured: false
  }
  showModal.value = true
}

const openEdit = (sponsor) => {
  editingId.value = sponsor.id
  formData.value = {
    name: sponsor.name || '',
    website: sponsor.website || '',
    logo: sponsor.logo || '',
    logo_credit_name: sponsor.logo_credit_name || '',
    logo_credit_url: sponsor.logo_credit_url || '',
    description: sponsor.description || '',
    status: sponsor.status || 'active',
    display_order: sponsor.display_order ?? 0,
    start_date: sponsor.start_date || '',
    end_date: sponsor.end_date || '',
    is_featured: sponsor.is_featured || false
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveSponsor = async () => {
  try {
    if (editingId.value) {
      await api.put(`/admin/platform-sponsors/${editingId.value}`, formData.value)
    } else {
      await api.post('/admin/platform-sponsors', formData.value)
    }
    closeModal()
    fetchSponsors()
  } catch (error) {
    console.error('Failed to save sponsor:', error)
  }
}

const toggleStatus = async (sponsor) => {
  try {
    await api.put(`/admin/platform-sponsors/${sponsor.id}`, {
      status: sponsor.status === 'active' ? 'inactive' : 'active'
    })
    fetchSponsors()
  } catch (error) {
    console.error('Failed to toggle sponsor status:', error)
  }
}

const deleteSponsor = async (id) => {
  if (!confirm('Delete this sponsor?')) return
  try {
    await api.delete(`/admin/platform-sponsors/${id}`)
    fetchSponsors()
  } catch (error) {
    console.error('Failed to delete sponsor:', error)
  }
}

onMounted(() => {
  fetchSponsors()
})
</script>
