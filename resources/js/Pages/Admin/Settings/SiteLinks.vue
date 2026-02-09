<template>
  <div class="min-h-screen bg-gray-50">
    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="closeToast"
    />
    <BaseModal
      :show="showDeleteModal"
      title="Delete Link"
      @close="cancelDelete"
    >
      <p class="text-gray-700 mb-6">
        Are you sure you want to delete "<strong>{{ linkToDelete?.title }}</strong>"? This action cannot be undone.
      </p>
      <div class="flex gap-3 justify-end">
        <button
          class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
          @click="cancelDelete"
        >
          Cancel
        </button>
        <button
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
          @click="confirmDelete"
        >
          Delete
        </button>
      </div>
    </BaseModal>

    <AdminHeader 
      title="Site Links" 
      subtitle="Manage footer links and site navigation"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loading -->
        <div
          v-if="loading"
          class="text-center py-12"
        >
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto" />
          <p class="mt-4 text-gray-600">
            Loading site links...
          </p>
        </div>

        <!-- Content -->
        <div
          v-else
          class="space-y-6"
        >
          <!-- Stats Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
              <div class="text-sm font-medium text-gray-500">
                Total Links
              </div>
              <div class="mt-2 text-3xl font-bold text-gray-900">
                {{ links.length }}
              </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
              <div class="text-sm font-medium text-gray-500">
                Active
              </div>
              <div class="mt-2 text-3xl font-bold text-green-600">
                {{ activeCount }}
              </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
              <div class="text-sm font-medium text-gray-500">
                Inactive
              </div>
              <div class="mt-2 text-3xl font-bold text-gray-600">
                {{ inactiveCount }}
              </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
              <div class="text-sm font-medium text-gray-500">
                Categories
              </div>
              <div class="mt-2 text-3xl font-bold text-blue-600">
                {{ categoryCount }}
              </div>
            </div>
          </div>

          <!-- Links Table -->
          <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">
                All Links
              </h2>
            </div>
            
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                      Title
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                      URL
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                      Category
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                      Order
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
                    v-for="link in sortedLinks"
                    :key="link.id"
                  >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ link.title }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                      <a
                        :href="link.url"
                        target="_blank"
                        class="text-blue-600 hover:underline"
                      >
                        {{ link.url }}
                      </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ link.category }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ link.order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <button 
                        :class="link.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer hover:opacity-80"
                        @click="toggleStatus(link)"
                      >
                        {{ link.is_active ? 'Active' : 'Inactive' }}
                      </button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <button 
                        class="text-blue-600 hover:text-blue-900 mr-3"
                        @click="editLink(link)"
                      >
                        Edit
                      </button>
                      <button 
                        class="text-red-600 hover:text-red-900"
                        @click="openDeleteModal(link)"
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../../api';
import AdminHeader from '../../../components/AdminHeader.vue';
import AdminQuickNav from '../../../components/AdminQuickNav.vue';
import Toast from '../../../components/ui/Toast.vue';
import BaseModal from '../../../components/admin/modals/BaseModal.vue';
import { useApiError } from '../../../composables/useApiError';
import { useDevLogger } from '../../../composables/useDevLogger';

const { toastMessage, toastType, toastVisible, showToast, closeToast, handleApiError } = useApiError();
const { log: devLog } = useDevLogger();

const links = ref([]);
const loading = ref(true);
const showDeleteModal = ref(false);
const linkToDelete = ref(null);

const activeCount = computed(() => links.value.filter(l => l.is_active).length);
const inactiveCount = computed(() => links.value.filter(l => !l.is_active).length);
const categoryCount = computed(() => {
  const categories = new Set(links.value.map(l => l.category));
  return categories.size;
});

const sortedLinks = computed(() => {
  return [...links.value].sort((a, b) => {
    if (a.category !== b.category) {
      return a.category.localeCompare(b.category);
    }
    return a.order - b.order;
  });
});

const loadLinks = async () => {
  loading.value = true;
  try {
    const response = await api.get('/site-links');
    links.value = response.data.data || [];
  } catch (error) {
    handleApiError(error, 'Failed to load site links');
  } finally {
    loading.value = false;
  }
};

const toggleStatus = async (link) => {
  try {
    const response = await api.patch(`/admin/site-links/${link.id}`, {
      is_active: !link.is_active
    });
    link.is_active = !link.is_active;
    showToast(link.is_active ? 'Link activated' : 'Link deactivated', 'success');
  } catch (error) {
    handleApiError(error, 'Failed to update link status');
  }
};

const editLink = (link) => {
  devLog('Edit link:', link);
  showToast('Edit functionality coming soon', 'info');
};

const openDeleteModal = (link) => {
  linkToDelete.value = link;
  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  if (!linkToDelete.value) return;
  
  try {
    await api.delete(`/admin/site-links/${linkToDelete.value.id}`);
    links.value = links.value.filter(l => l.id !== linkToDelete.value.id);
    showToast('Link deleted successfully', 'success');
    showDeleteModal.value = false;
    linkToDelete.value = null;
  } catch (error) {
    handleApiError(error, 'Failed to delete link');
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  linkToDelete.value = null;
};

onMounted(() => {
  loadLinks();
});
</script>
