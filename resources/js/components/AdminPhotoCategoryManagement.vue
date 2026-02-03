<template>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Photo Categories
                </h2>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <button @click="showAddModal = true" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-burgundy hover:bg-burgundy-dark">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Category
                </button>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="category in categories" :key="category.id" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <span class="text-3xl mr-3">{{ category.icon || '📷' }}</span>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ category.name }}</h3>
                                <p class="text-sm text-gray-500">{{ category.hashtags_count || 0 }} hashtags</p>
                            </div>
                        </div>
                        <span :class="category.is_active ? 'status-active' : 'status-inactive'">
                            {{ category.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    
                    <p v-if="category.description" class="text-sm text-gray-600 mb-4 line-clamp-2">{{ category.description }}</p>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Order: {{ category.display_order }}</span>
                        <div>
                            <button @click="editCategory(category)" class="text-burgundy hover:text-burgundy-dark text-sm mr-3">Edit</button>
                            <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showAddModal || showEditModal" class="fixed z-50 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            {{ showEditModal ? 'Edit Category' : 'Add New Category' }}
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name *</label>
                                <input v-model="form.name" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Icon (Emoji)</label>
                                <input v-model="form.icon" type="text" maxlength="10" placeholder="📷" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea v-model="form.description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy"></textarea>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Display Order</label>
                                    <input v-model.number="form.display_order" type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-burgundy focus:border-burgundy">
                                </div>
                                
                                <div class="flex items-center mt-6">
                                    <input v-model="form.is_active" type="checkbox" class="h-4 w-4 text-burgundy focus:ring-burgundy border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button @click="saveCategory" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-burgundy text-base font-medium text-white hover:bg-burgundy-dark sm:col-start-2 sm:text-sm">
                            {{ showEditModal ? 'Update' : 'Create' }}
                        </button>
                        <button @click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:col-start-1 sm:text-sm">
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

const categories = ref([]);
const showAddModal = ref(false);
const showEditModal = ref(false);
const form = ref({
    name: '',
    icon: '',
    description: '',
    display_order: 0,
    is_active: true,
});
const editingId = ref(null);

onMounted(() => {
    fetchCategories();
});

const fetchCategories = async () => {
    try {
        const response = await api.get('/admin/photo-categories');
        categories.value = response.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

const saveCategory = async () => {
    try {
        if (showEditModal.value) {
            await api.put(`/admin/photo-categories/${editingId.value}`, form.value);
        } else {
            await api.post('/admin/photo-categories', form.value);
        }
        fetchCategories();
        closeModal();
    } catch (error) {
        console.error('Error saving category:', error);
        alert('Error saving category');
    }
};

const editCategory = (category) => {
    form.value = { ...category };
    editingId.value = category.id;
    showEditModal.value = true;
};

const deleteCategory = async (id) => {
    if (confirm('Are you sure you want to delete this category?')) {
        try {
            await api.delete(`/admin/photo-categories/${id}`);
            fetchCategories();
        } catch (error) {
            console.error('Error deleting category:', error);
            alert('Error deleting category');
        }
    }
};

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    form.value = {
        name: '',
        icon: '',
        description: '',
        display_order: 0,
        is_active: true,
    };
    editingId.value = null;
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
