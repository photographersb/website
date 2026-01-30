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
                <button @click="showAddModal = true" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-burgundy hover:bg-burgundy-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sponsor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="sponsor in sponsors" :key="sponsor.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div v-if="sponsor.logo" class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded object-contain" :src="sponsor.logo" :alt="sponsor.name">
                                </div>
                                <div :class="sponsor.logo ? 'ml-4' : ''">
                                    <div class="text-sm font-medium text-gray-900">{{ sponsor.name }}</div>
                                    <div v-if="sponsor.description" class="text-sm text-gray-500 line-clamp-1">{{ sponsor.description }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a v-if="sponsor.website" :href="sponsor.website" target="_blank" class="text-blue-600 hover:text-blue-900">Visit</a>
                            <span v-else>-</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="[sponsor.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800', 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full']">
                                {{ sponsor.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div v-if="sponsor.start_date">{{ formatDate(sponsor.start_date) }}</div>
                            <div v-if="sponsor.end_date" class="text-xs text-gray-400">to {{ formatDate(sponsor.end_date) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ sponsor.display_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="editSponsor(sponsor)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                            <button @click="deleteSponsor(sponsor.id)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showAddModal || showEditModal" class="fixed z-50 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            {{ showEditModal ? 'Edit Sponsor' : 'Add New Sponsor' }}
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name *</label>
                                <input v-model="form.name" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Logo URL</label>
                                <input v-model="form.logo" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Website</label>
                                <input v-model="form.website" type="url" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea v-model="form.description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"></textarea>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                                    <select v-model="form.status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Display Order</label>
                                    <input v-model.number="form.display_order" type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input v-model="form.start_date" type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input v-model="form.end_date" type="date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button @click="saveSponsor" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-burgundy text-base font-medium text-white hover:bg-burgundy-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy sm:col-start-2 sm:text-sm">
                            {{ showEditModal ? 'Update' : 'Create' }}
                        </button>
                        <button @click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-burgundy sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const sponsors = ref([]);
const showAddModal = ref(false);
const showEditModal = ref(false);
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

onMounted(() => {
    fetchSponsors();
});

const fetchSponsors = async () => {
    try {
        const response = await api.get('/admin/sponsors');
        sponsors.value = response.data;
    } catch (error) {
        console.error('Error fetching sponsors:', error);
    }
};

const saveSponsor = async () => {
    try {
        if (showEditModal.value) {
            await api.put(`/admin/sponsors/${editingId.value}`, form.value);
        } else {
            await api.post('/admin/sponsors', form.value);
        }
        fetchSponsors();
        closeModal();
    } catch (error) {
        console.error('Error saving sponsor:', error);
        alert('Error saving sponsor');
    }
};

const editSponsor = (sponsor) => {
    form.value = { ...sponsor };
    editingId.value = sponsor.id;
    showEditModal.value = true;
};

const deleteSponsor = async (id) => {
    if (confirm('Are you sure you want to delete this sponsor?')) {
        try {
            await api.delete(`/admin/sponsors/${id}`);
            fetchSponsors();
            alert('Sponsor deleted successfully');
        } catch (error) {
            console.error('Error deleting sponsor:', error);
            const errorMessage = error.response?.data?.message || error.message || 'Error deleting sponsor';
            alert(errorMessage);
        }
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
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
};
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
