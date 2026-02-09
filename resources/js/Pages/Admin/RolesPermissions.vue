<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Page Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            Roles & Permissions
          </h1>
          <p class="text-gray-600 mt-1">
            Manage user roles and access control
          </p>
        </div>
        <button
          class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 flex items-center space-x-2"
          @click="showCreateModal = true"
        >
          <span>➕</span>
          <span>Create Role</span>
        </button>
      </div>
      
      <AdminQuickNav />

        <!-- Loading State -->
      <div
        v-if="loading"
        class="flex items-center justify-center py-12"
      >
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-600" />
      </div>

        <!-- Roles Grid -->
      <div
        v-else
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
          <div
            v-for="role in roles"
            :key="role.id"
            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
          >
            <!-- Role Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                  <div :class="['w-10 h-10 rounded-lg flex items-center justify-center', role.colorClass]">
                    <span class="text-xl">{{ role.icon }}</span>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                      {{ role.name }}
                    </h3>
                    <p class="text-xs text-gray-500">
                      {{ role.userCount }} users
                    </p>
                  </div>
                </div>
                <p class="text-sm text-gray-600">
                  {{ role.description }}
                </p>
              </div>
              <div class="flex space-x-1">
                <button
                  class="p-2 text-gray-400 hover:text-amber-600 rounded-md hover:bg-gray-100"
                  @click="editRole(role)"
                >
                  <span>✏️</span>
                </button>
                <button
                  v-if="!role.isSystem"
                  class="p-2 text-gray-400 hover:text-red-600 rounded-md hover:bg-gray-100"
                  @click="deleteRole(role)"
                >
                  <span>🗑️</span>
                </button>
              </div>
            </div>

            <!-- Permissions List -->
            <div class="border-t border-gray-200 pt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">
                Permissions ({{ role.permissions.length }})
              </h4>
              <div class="space-y-1 max-h-40 overflow-y-auto">
                <div
                  v-for="permission in role.permissions.slice(0, 5)"
                  :key="permission"
                  class="flex items-center space-x-2"
                >
                  <span class="text-green-500 text-sm">✓</span>
                  <span class="text-sm text-gray-600">{{ formatPermission(permission) }}</span>
                </div>
                <button
                  v-if="role.permissions.length > 5"
                  class="text-sm text-amber-600 hover:text-amber-700"
                  @click="viewRole(role)"
                >
                  +{{ role.permissions.length - 5 }} more permissions
                </button>
              </div>
            </div>

            <!-- Role Actions -->
            <div class="mt-4 pt-4 border-t border-gray-200 flex space-x-2">
              <button
                class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm font-medium"
                @click="viewRole(role)"
              >
                View Details
              </button>
              <button
                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm"
                @click="duplicateRole(role)"
              >
                📋
              </button>
            </div>
          </div>
        </div>

        <!-- Create/Edit Modal -->
        <div
          v-if="showCreateModal || showEditModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
          <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-xl font-bold text-gray-900">
                {{ showCreateModal ? 'Create New Role' : 'Edit Role' }}
              </h2>
            </div>
            
            <div class="p-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                <input
                  v-model="formData.name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                  placeholder="e.g., Content Manager"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea
                  v-model="formData.description"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                  placeholder="Role description..."
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                <div class="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-4">
                  <label
                    v-for="permission in availablePermissions"
                    :key="permission.id"
                    class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded"
                  >
                    <input
                      v-model="formData.permissions"
                      type="checkbox"
                      :value="permission.id"
                      class="rounded text-amber-600 focus:ring-amber-500"
                    >
                    <span class="text-sm text-gray-700">{{ permission.label }}</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
              <button
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                @click="closeModal"
              >
                Cancel
              </button>
              <button
                class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700"
                @click="saveRole"
              >
                {{ showCreateModal ? 'Create Role' : 'Save Changes' }}
              </button>
            </div>
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
import { ref, onMounted } from 'vue';
import api from '../../api';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
import Toast from '../../components/ui/Toast.vue';
const loading = ref(true);
const showCreateModal = ref(false);
const showEditModal = ref(false);

const toast = ref({
  show: false,
  message: '',
  type: 'success'
});

const roles = ref([]);
const availablePermissions = ref([]);

const formData = ref({
  id: null,
  name: '',
  description: '',
  permissions: []
});

const loadRoles = async () => {
  loading.value = true;
  try {
    const response = await api.get('/admin/roles');
    roles.value = response.data.roles || [];
    availablePermissions.value = response.data.permissions || [];
  } catch (error) {
    console.error('Roles load error:', error);
    roles.value = [];
    availablePermissions.value = [];
  } finally {
    loading.value = false;
  }
};

const formatPermission = (permission) => {
  if (permission === '*') return 'All Permissions';
  return permission.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const editRole = (role) => {
  formData.value = {
    id: role.id,
    name: role.name,
    description: role.description,
    permissions: [...role.permissions]
  };
  showEditModal.value = true;
};

const viewRole = (role) => {
  toast.value = {
    show: true,
    message: `Viewing ${role.name} role details`,
    type: 'info'
  };
};

const duplicateRole = (role) => {
  formData.value = {
    id: null,
    name: `${role.name} (Copy)`,
    description: role.description,
    permissions: [...role.permissions]
  };
  showCreateModal.value = true;
};

const deleteRole = async (role) => {
  if (!confirm(`Delete role "${role.name}"?`)) return;
  
  try {
    await api.delete(`/admin/roles/${role.id}`);
    toast.value = {
      show: true,
      message: 'Role deleted successfully',
      type: 'success'
    };
    loadRoles();
  } catch (error) {
    toast.value = {
      show: true,
      message: 'Failed to delete role',
      type: 'error'
    };
  }
};

const saveRole = async () => {
  try {
    if (showCreateModal.value) {
      await api.post('/admin/roles', formData.value);
      toast.value = {
        show: true,
        message: 'Role created successfully',
        type: 'success'
      };
    } else {
      await api.put(`/admin/roles/${formData.value.id}`, formData.value);
      toast.value = {
        show: true,
        message: 'Role updated successfully',
        type: 'success'
      };
    }
    closeModal();
    loadRoles();
  } catch (error) {
    toast.value = {
      show: true,
      message: 'Failed to save role',
      type: 'error'
    };
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  showEditModal.value = false;
  formData.value = {
    id: null,
    name: '',
    description: '',
    permissions: []
  };
};

onMounted(() => {
  loadRoles();
});
</script>
