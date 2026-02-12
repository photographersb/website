<template>
  <div>
    <AdminHeader
      title="🔗 Site Links Management"
      subtitle="Control all public site links from one place"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />
    
      <!-- Top Actions -->
      <div class="flex flex-wrap justify-between items-center gap-4 mb-6 bg-white p-4 rounded-lg shadow">
        <!-- Section Filter -->
      <div class="flex items-center gap-2">
        <label class="text-sm font-medium text-gray-700">Section:</label>
        <select
          v-model="selectedSection"
          class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-burgundy focus:border-burgundy"
          @change="filterBySection"
        >
          <option value="">
            All Sections
          </option>
          <option
            v-for="(label, key) in sections"
            :key="key"
            :value="key"
          >
            {{ label }}
          </option>
        </select>
      </div>

      <!-- Actions -->
      <div class="flex gap-2">
        <button
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
          @click="clearCache"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          Clear Cache
        </button>
        <Link
          href="/admin/settings/site-links/preview"
          class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
            />
          </svg>
          Preview
        </Link>
        <Link
          href="/admin/settings/site-links/create"
          class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors flex items-center gap-2"
        >
          <svg
            class="w-4 h-4"
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
          Add New Link
        </Link>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-sm text-gray-600 mb-1">
          Total Links
        </div>
        <div class="text-2xl font-bold text-gray-900">
          {{ links.total }}
        </div>
      </div>
      <div class="bg-green-50 p-4 rounded-lg shadow">
        <div class="text-sm text-green-700 mb-1">
          Active
        </div>
        <div class="text-2xl font-bold text-green-700">
          {{ activeCount }}
        </div>
      </div>
      <div class="bg-red-50 p-4 rounded-lg shadow">
        <div class="text-sm text-red-700 mb-1">
          Disabled
        </div>
        <div class="text-2xl font-bold text-red-700">
          {{ inactiveCount }}
        </div>
      </div>
      <div class="bg-blue-50 p-4 rounded-lg shadow">
        <div class="text-sm text-blue-700 mb-1">
          Sections
        </div>
        <div class="text-2xl font-bold text-blue-700">
          {{ Object.keys(sections).length }}
        </div>
      </div>
    </div>

    <!-- Links Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Section
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Title
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                URL
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Visibility
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Sort
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="link in links.data"
              :key="link.id"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 text-xs font-medium rounded-full"
                  :class="getSectionBadgeClass(link.section)"
                >
                  {{ sections[link.section] }}
                </span>
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center gap-2">
                  <svg
                    v-if="link.icon"
                    class="w-4 h-4 text-gray-400"
                    :class="getIconClass(link.icon)"
                  >
                    <!-- Icon placeholder -->
                  </svg>
                  <span class="text-sm font-medium text-gray-900">{{ link.title }}</span>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm text-gray-600 max-w-md truncate">
                  {{ link.route_name || link.url }}
                </div>
                <div
                  v-if="link.route_name"
                  class="text-xs text-gray-400"
                >
                  Route: {{ link.route_name }}
                </div>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 text-xs rounded-full"
                  :class="getVisibilityBadgeClass(link.visibility)"
                >
                  {{ visibilityOptions[link.visibility] }}
                </span>
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                #{{ link.sort_order }}
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <button
                  class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium transition-colors"
                  :class="link.is_active 
                    ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
                  @click="toggleActive(link)"
                >
                  <span
                    class="w-2 h-2 rounded-full"
                    :class="link.is_active ? 'bg-green-600' : 'bg-gray-400'"
                  />
                  {{ link.is_active ? 'Active' : 'Disabled' }}
                </button>
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-2">
                  <Link
                    :href="`/admin/settings/site-links/${link.id}/edit`"
                    class="text-burgundy hover:text-burgundy-dark"
                    title="Edit"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                  </Link>
                  <button
                    class="text-red-600 hover:text-red-800"
                    title="Delete"
                    @click="deleteLink(link)"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="links.last_page > 1"
        class="bg-gray-50 px-4 py-3 border-t border-gray-200"
      >
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-700">
            Showing {{ links.from }} to {{ links.to }} of {{ links.total }} links
          </div>
          <div class="flex gap-2">
            <button
              v-for="page in links.last_page"
              :key="page"
              class="px-3 py-1 rounded transition-colors"
              :class="page === links.current_page 
                ? 'bg-burgundy text-white' 
                : 'bg-white text-gray-700 hover:bg-gray-100'"
              @click="goToPage(page)"
            >
              {{ page }}
            </button>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'

const props = defineProps({
  links: Object,
  sections: Object,
  visibilityOptions: Object,
  currentSection: String,
})

const selectedSection = ref(props.currentSection || '')

const activeCount = computed(() => {
  return props.links.data.filter(link => link.is_active).length
})

const inactiveCount = computed(() => {
  return props.links.data.filter(link => !link.is_active).length
})

const filterBySection = () => {
  const url = selectedSection.value 
    ? `/admin/settings/site-links?section=${selectedSection.value}`
    : '/admin/settings/site-links'
  router.visit(url)
}

const toggleActive = async (link) => {
  try {
    const response = await api.post(`/admin/settings/site-links/${link.id}/toggle-active`)
    
    if (response.data.success) {
      link.is_active = response.data.is_active
    }
  } catch (error) {
    console.error('Error toggling link status:', error)
    alert('Failed to toggle link status')
  }
}

const deleteLink = async (link) => {
  if (!confirm(`Are you sure you want to delete "${link.title}"?`)) {
    return
  }

  try {
    await api.delete(`/admin/settings/site-links/${link.id}`)
    
    router.visit('/admin/settings/site-links')
  } catch (error) {
    console.error('Error deleting link:', error)
    alert('Failed to delete link')
  }
}

const clearCache = async () => {
  try {
    await api.post('/admin/settings/site-links/clear-cache')
    
    alert('Cache cleared successfully!')
  } catch (error) {
    console.error('Error clearing cache:', error)
    alert('Failed to clear cache')
  }
}

const goToPage = (page) => {
  const url = selectedSection.value 
    ? `/admin/settings/site-links?section=${selectedSection.value}&page=${page}`
    : `/admin/settings/site-links?page=${page}`
  router.visit(url)
}

const getSectionBadgeClass = (section) => {
  const classes = {
    navbar: 'bg-blue-100 text-blue-800',
    footer_company: 'bg-green-100 text-green-800',
    footer_legal: 'bg-yellow-100 text-yellow-800',
    footer_useful: 'bg-purple-100 text-purple-800',
    social: 'bg-pink-100 text-pink-800',
    cta: 'bg-orange-100 text-orange-800',
  }
  return classes[section] || 'bg-gray-100 text-gray-800'
}

const getVisibilityBadgeClass = (visibility) => {
  const classes = {
    public: 'bg-green-100 text-green-800',
    guest_only: 'bg-blue-100 text-blue-800',
    auth_only: 'bg-purple-100 text-purple-800',
  }
  return classes[visibility] || 'bg-gray-100 text-gray-800'
}

const getIconClass = (icon) => {
  return `icon-${icon}`
}
</script>

<style scoped>
.hover\:bg-gray-50:hover {
  background-color: #f9fafb;
}
</style>
