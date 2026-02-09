<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">
          Certificate Templates
        </h1>
        <p class="text-gray-600 mt-1">
          Manage certificate designs for competitions and awards
        </p>
      </div>
      <button
        class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-medium flex items-center gap-2"
        @click="showCreateModal = true"
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
            d="M12 4v16m8-8H4"
          />
        </svg>
        New Template
      </button>
    </div>

    <AdminQuickNav />

    <!-- Template Gallery -->
    <div
      v-if="loading"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
    >
      <div
        v-for="i in 6"
        :key="i"
        class="bg-white rounded-lg shadow p-6 h-64 animate-pulse"
      >
        <div class="h-32 bg-gray-200 rounded mb-3" />
        <div class="h-4 bg-gray-200 rounded w-1/2" />
      </div>
    </div>

    <div
      v-else-if="templates.length === 0"
      class="bg-white rounded-lg shadow p-12 text-center"
    >
      <svg
        class="w-16 h-16 text-gray-300 mx-auto mb-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
        />
      </svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">
        No Templates Yet
      </h3>
      <p class="text-gray-600 mb-6">
        Create your first certificate template
      </p>
      <button
        class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-medium"
        @click="showCreateModal = true"
      >
        Create Template
      </button>
    </div>

    <div
      v-else
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
    >
      <div 
        v-for="template in templates"
        :key="template.id"
        class="bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer group"
        @click="editTemplate(template)"
      >
        <!-- Template Preview -->
        <div class="aspect-video bg-gradient-to-br from-orange-100 to-orange-50 relative overflow-hidden">
          <div class="w-full h-full flex items-center justify-center p-4 border-2 border-dashed border-orange-300">
            <div class="text-center">
              <p class="text-sm font-serif font-bold text-orange-600">
                {{ template.title }}
              </p>
              <p class="text-xs text-gray-600 mt-1">
                {{ template.type }}
              </p>
              <p class="text-xs text-gray-500 mt-2">
                {{ template.width }}×{{ template.height }}mm
              </p>
            </div>
          </div>
          <!-- Overlay Actions -->
          <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
            <button
              class="px-3 py-1 bg-blue-500 text-white rounded text-xs font-medium hover:bg-blue-600"
              @click.stop="editTemplate(template)"
            >
              Edit
            </button>
            <button
              class="px-3 py-1 bg-red-500 text-white rounded text-xs font-medium hover:bg-red-600"
              @click.stop="deleteTemplate(template)"
            >
              Delete
            </button>
          </div>
        </div>

        <!-- Template Info -->
        <div class="p-4">
          <h3 class="font-semibold text-gray-900 truncate">
            {{ template.title }}
          </h3>
          <p class="text-sm text-gray-600 mt-1">
            {{ template.description }}
          </p>
          
          <div class="mt-3 flex items-center justify-between">
            <span
              :class="[
                'px-2 py-1 rounded text-xs font-medium',
                getTypeClass(template.type)
              ]"
            >
              {{ getTypeLabel(template.type) }}
            </span>
            <span
              v-if="template.is_default"
              class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium"
            >
              Default
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <TemplateEditor
      v-if="showCreateModal || editingTemplate"
      :template="editingTemplate"
      :available-types="templateTypes"
      @save="saveTemplate"
      @cancel="closeModal"
    />

    <!-- Toast -->
    <div 
      v-if="toastMessage"
      :class="[
        'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transition z-50',
        toastType === 'success' ? 'bg-green-600' : 'bg-red-600'
      ]"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../api';
import TemplateEditor from './TemplateEditor.vue';
import AdminQuickNav from '../../../components/AdminQuickNav.vue';

const templates = ref([]);
const loading = ref(true);
const showCreateModal = ref(false);
const editingTemplate = ref(null);
const toastMessage = ref('');
const toastType = ref('success');

const templateTypes = [
  { value: 'participation', label: 'Participation Certificate' },
  { value: 'finalist', label: 'Finalist Certificate' },
  { value: 'winner', label: 'Winner Certificate' },
  { value: 'merit', label: 'Merit Certificate' }
];

const loadTemplates = async () => {
  loading.value = true;
  try {
    const { data } = await api.get('/admin/certificate-templates');
    if (data.status === 'success') {
      templates.value = data.data;
    }
  } catch (error) {
    console.error('Error loading templates:', error);
    showToast('Failed to load templates', 'error');
  } finally {
    loading.value = false;
  }
};

const editTemplate = (template) => {
  editingTemplate.value = { ...template };
  showCreateModal.value = false;
};

const saveTemplate = async (templateData) => {
  try {
    let response;
    if (editingTemplate.value?.id) {
      // Update existing
      response = await api.put(
        `/admin/certificate-templates/${editingTemplate.value.id}`,
        templateData
      );
    } else {
      // Create new
      response = await api.post(
        '/admin/certificate-templates',
        templateData
      );
    }

    if (response.data.status === 'success') {
      showToast(
        editingTemplate.value?.id 
          ? 'Template updated successfully' 
          : 'Template created successfully',
        'success'
      );
      closeModal();
      loadTemplates();
    }
  } catch (error) {
    console.error('Error saving template:', error);
    showToast('Failed to save template', 'error');
  }
};

const deleteTemplate = async (template) => {
  if (!confirm(`Delete template "${template.title}"?`)) return;

  try {
    const { data } = await api.delete(`/admin/certificate-templates/${template.id}`);
    if (data.status === 'success') {
      showToast('Template deleted successfully', 'success');
      loadTemplates();
    }
  } catch (error) {
    console.error('Error deleting template:', error);
    showToast('Failed to delete template', 'error');
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  editingTemplate.value = null;
};

const showToast = (message, type) => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

const getTypeLabel = (type) => {
  const template = templateTypes.find(t => t.value === type);
  return template?.label || type;
};

const getTypeClass = (type) => {
  const classes = {
    participation: 'bg-blue-100 text-blue-800',
    finalist: 'bg-purple-100 text-purple-800',
    winner: 'bg-yellow-100 text-yellow-800',
    merit: 'bg-green-100 text-green-800'
  };
  return classes[type] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  loadTemplates();
});
</script>

<style scoped>
/* Smooth transitions */
</style>
