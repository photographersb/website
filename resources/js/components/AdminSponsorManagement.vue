<template>
  <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Sponsor Management
        </h2>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-burgundy hover:bg-burgundy-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy"
          @click="showAddModal = true"
        >
          <svg
            class="-ml-1 mr-2 h-5 w-5"
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
          Add Sponsor
        </button>
      </div>
    </div>

    <!-- Sponsors Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Sponsor
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Website
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Duration
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Order
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="sponsor in sponsors"
            :key="sponsor.id"
          >
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div
                  v-if="sponsor.logo"
                  class="flex-shrink-0 h-16 w-32"
                >
                  <img
                    class="h-16 w-32 rounded object-contain bg-gray-50"
                    :src="sponsor.logo"
                    :alt="sponsor.name"
                  >
                </div>
                <div :class="sponsor.logo ? 'ml-4' : ''">
                  <div class="text-sm font-medium text-gray-900">
                    {{ sponsor.name }}
                  </div>
                  <div
                    v-if="sponsor.description"
                    class="text-sm text-gray-500 line-clamp-1"
                  >
                    {{ sponsor.description }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <a
                v-if="sponsor.website"
                :href="sponsor.website"
                target="_blank"
                class="text-burgundy hover:text-burgundy-dark"
              >Visit</a>
              <span v-else>-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="sponsor.status === 'active' ? 'status-active' : 'status-inactive'">
                {{ sponsor.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div v-if="sponsor.start_date">
                {{ formatSponsorDate(sponsor.start_date) }}
              </div>
              <div
                v-if="sponsor.end_date"
                class="text-xs text-gray-400"
              >
                to {{ formatSponsorDate(sponsor.end_date) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ sponsor.display_order }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                class="text-burgundy hover:text-burgundy-dark mr-3"
                @click="editSponsor(sponsor)"
              >
                Edit
              </button>
              <button
                class="text-red-600 hover:text-red-900"
                @click="confirmDeleteSponsor(sponsor)"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div
      v-if="showAddModal || showEditModal"
      class="fixed z-50 inset-0 overflow-y-auto"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              {{ showEditModal ? 'Edit Sponsor' : 'Add New Sponsor' }}
            </h3>
                        
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                >
              </div>
                            
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sponsor Logo *</label>
                <div class="mt-1 flex items-center space-x-4">
                  <div
                    v-if="form.logo"
                    class="relative"
                  >
                    <img
                      :src="form.logo"
                      alt="Logo preview"
                      class="h-24 w-48 object-contain border border-gray-300 rounded p-2 bg-gray-50"
                    >
                    <button
                      type="button"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                      @click="form.logo = ''"
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
                          d="M6 18L18 6M6 6l12 12"
                        />
                      </svg>
                    </button>
                  </div>
                  <div class="flex-1">
                    <input 
                      type="file" 
                      accept="image/*" 
                      class="upload-input block text-sm" 
                      @change="handleLogoUpload"
                    >
                    <p class="mt-1 text-xs text-gray-500">
                      PNG, JPG up to 5MB. 600x300 px.
                    </p>
                  </div>
                </div>
              </div>
                            
              <div>
                <label class="block text-sm font-medium text-gray-700">Website</label>
                <input
                  v-model="form.website"
                  type="url"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                >
              </div>
                            
              <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                />
              </div>
                            
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Status *</label>
                  <select
                    v-model="form.status"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
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
                  <label class="block text-sm font-medium text-gray-700">Display Order</label>
                  <input
                    v-model.number="form.display_order"
                    type="number"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                  >
                </div>
              </div>
                            
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Start Date</label>
                  <input
                    v-model="form.start_date"
                    type="date"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                  >
                </div>
                                
                <div>
                  <label class="block text-sm font-medium text-gray-700">End Date</label>
                  <input
                    v-model="form.end_date"
                    type="date"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"
                  >
                </div>
              </div>
            </div>
          </div>
                    
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
            <button
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-burgundy text-base font-medium text-white hover:bg-burgundy-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy sm:col-start-2 sm:text-sm"
              @click="saveSponsor"
            >
              {{ showEditModal ? 'Update' : 'Create' }}
            </button>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy sm:mt-0 sm:col-start-1 sm:text-sm"
              @click="closeModal"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed z-50 inset-0 overflow-y-auto"
    >
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          @click="showDeleteModal = false"
        />
                
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg
                class="h-6 w-6 text-red-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Delete Sponsor
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to delete <span class="font-semibold text-gray-900">{{ deletingSponsorName }}</span>? This action cannot be undone.
                </p>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
              @click="deleteSponsor"
            >
              Delete
            </button>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy sm:mt-0 sm:w-auto sm:text-sm"
              @click="showDeleteModal = false"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div
      v-if="showToast"
      class="toast"
      :class="toastType"
    >
      <div class="toast-content">
        <svg
          v-if="toastType === 'error'"
          class="toast-icon"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <svg
          v-else
          class="toast-icon"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <div class="toast-message">
          <span>{{ toastMessage }}</span>
        </div>
        <button
          class="toast-copy"
          @click="copyToClipboard(toastMessage)"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';
import { formatDate } from '../utils/formatters';

const sponsors = ref([]);
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const deletingId = ref(null);
const deletingSponsorName = ref('');
const form = ref({
    name: '',
    logo: '',
    website: '',
    description: '',
    status: 'active',
    display_order: 0,
    start_date: '',
    end_date: '',
});
const editingId = ref(null);
const logoFile = ref(null);

// Toast notifications
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const showToastMessage = (message, type = 'success') => {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, type === 'error' ? 5000 : 3000);
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        const originalMessage = toastMessage.value;
        toastMessage.value = 'Copied to clipboard!';
        setTimeout(() => {
            toastMessage.value = originalMessage;
        }, 1000);
    });
};

onMounted(() => {
    fetchSponsors();
});

const fetchSponsors = async () => {
    try {
        const response = await api.get('/admin/platform-sponsors');
        sponsors.value = response.data;
    } catch (error) {
        console.error('Error fetching sponsors:', error);
    }
};

const handleLogoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            showToastMessage('Logo file must be less than 2MB', 'error');
            event.target.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showToastMessage('Please select an image file', 'error');
            event.target.value = '';
            return;
        }
        
        logoFile.value = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            form.value.logo = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const saveSponsor = async () => {
    try {
        let logoUrl = form.value.logo;
        
        // Upload logo if a new file was selected
        if (logoFile.value) {
            const formData = new FormData();
            formData.append('logo', logoFile.value);
            
            try {
                const uploadResponse = await api.post('/admin/upload-logo', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                logoUrl = uploadResponse.data.url;
            } catch (uploadError) {
                console.error('Error uploading logo:', uploadError);
                showToastMessage('Error uploading logo. Proceeding without logo.', 'error');
                logoUrl = '';
            }
        }
        
        const sponsorData = {
            ...form.value,
            logo: logoUrl
        };
        
        if (showEditModal.value) {
            await api.put(`/admin/platform-sponsors/${editingId.value}`, sponsorData);
            showToastMessage('Sponsor updated successfully', 'success');
        } else {
            await api.post('/admin/platform-sponsors', sponsorData);
            showToastMessage('Sponsor created successfully', 'success');
        }
        fetchSponsors();
        closeModal();
    } catch (error) {
        console.error('Error saving sponsor:', error);
        const errorMessage = error.response?.data?.message || error.message || 'Error saving sponsor';
        showToastMessage(errorMessage, 'error');
    }
};

const editSponsor = (sponsor) => {
    form.value = { ...sponsor };
    editingId.value = sponsor.id;
    showEditModal.value = true;
};

const confirmDeleteSponsor = (sponsor) => {
    deletingId.value = sponsor.id;
    deletingSponsorName.value = sponsor.name;
    showDeleteModal.value = true;
};

const deleteSponsor = async () => {
    try {
        await api.delete(`/admin/platform-sponsors/${deletingId.value}`);
        showDeleteModal.value = false;
        fetchSponsors();
        showToastMessage('Sponsor deleted successfully', 'success');
    } catch (error) {
        console.error('Error deleting sponsor:', error);
        showDeleteModal.value = false;
        const errorMessage = error.response?.data?.message || error.message || 'Error deleting sponsor. This sponsor may have related data.';
        showToastMessage(errorMessage, 'error');
    } finally {
        deletingId.value = null;
        deletingSponsorName.value = '';
    }
};

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    form.value = {
        name: '',
        logo: '',
        website: '',
        description: '',
        status: 'active',
        display_order: 0,
        start_date: '',
        end_date: '',
    };
    editingId.value = null;
    logoFile.value = null;
};

const formatSponsorDate = (date) => {
  return formatDate(date);
};
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Toast Notifications */
.toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    min-width: 300px;
    max-width: 500px;
    padding: 16px;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    z-index: 9999;
    animation: slideIn 0.3s ease-out;
}

.toast.success {
    background-color: #10b981;
    color: white;
}

.toast.error {
    background-color: #ef4444;
    color: white;
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.toast-icon {
    width: 24px;
    height: 24px;
    flex-shrink: 0;
}

.toast-message {
    flex: 1;
    user-select: text;
    cursor: text;
}

.toast-message span {
    display: block;
    word-break: break-word;
}

.toast-copy {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 4px;
    padding: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}

.toast-copy:hover {
    background: rgba(255, 255, 255, 0.3);
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
