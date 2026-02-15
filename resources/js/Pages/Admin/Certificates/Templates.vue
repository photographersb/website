<template>
  <div class="min-h-screen">
    <AdminHeader
      title="🎨 Certificate Templates"
      subtitle="Design certificate templates for competitions and awards"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <section class="page-hero">
        <div class="hero-copy">
          <p class="hero-kicker">CERTIFICATE DESIGN</p>
          <h1 class="hero-title">Templates, standardized.</h1>
          <p class="hero-subtitle">Create reusable certificate designs for different award types and competitions.</p>
          <div class="hero-actions">
            <button
              class="btn-admin-primary"
              @click="showCreateModal = true"
            >
              + New Template
            </button>
            <button
              class="btn-admin-secondary"
              @click="loadTemplates"
            >
              Refresh
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Total</span>
            <span class="status-value">{{ templates.length }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Active</span>
            <span class="status-value">{{ templates.filter(t => !t.is_archived).length }}</span>
          </div>
          <div class="status-card">
            <span class="status-label">Default</span>
            <span class="status-value">{{ templates.filter(t => t.is_default).length }}</span>
          </div>
        </div>
      </section>

      <div class="page-topbar">
        <div class="status-chip">
          Certificates · Templates
        </div>
      </div>

      <AdminQuickNav />

      <!-- Template Gallery -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="i in 6"
          :key="i"
          class="bg-white rounded-lg shadow p-6 h-64 animate-pulse"
        >
          <div class="h-32 bg-gray-200 rounded mb-3" />
          <div class="h-4 bg-gray-200 rounded w-1/2" />
        </div>
      </div>

      <div v-else-if="templates.length === 0" class="panel text-center py-12">
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
          class="btn-admin-primary"
          @click="showCreateModal = true"
        >
          Create Template
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="template in templates"
          :key="template.id"
          class="card-container"
          @click="editTemplate(template)"
        >
          <!-- Template Preview -->
          <div class="template-preview">
            <div class="template-content">
              <p class="template-title">
                {{ template.title }}
              </p>
              <p class="template-type">
                {{ getTypeLabel(template.type) }}
              </p>
              <p class="template-size">
                {{ template.width }}×{{ template.height }}mm
              </p>
            </div>
            <!-- Overlay Actions -->
            <div class="template-overlay">
              <button
                class="btn-action btn-action-edit"
                @click.stop="editTemplate(template)"
              >
                Edit
              </button>
              <button
                class="btn-action btn-action-delete"
                @click.stop="deleteTemplate(template)"
              >
                Delete
              </button>
            </div>
          </div>

          <!-- Template Info -->
          <div class="template-info">
            <h3 class="template-info-title">
              {{ template.title }}
            </h3>
            <p class="template-info-description">
              {{ template.description }}
            </p>

            <div class="template-info-footer">
              <span
                :class="['badge', getTypeClass(template.type)]"
              >
                {{ getTypeLabel(template.type) }}
              </span>
              <span
                v-if="template.is_default"
                class="badge badge-default"
              >
                Default
              </span>
            </div>
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
import AdminHeader from '../../../components/AdminHeader.vue';
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
.page-hero {
  display: grid;
  grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
  gap: 1.5rem;
  padding: 1.75rem 2rem;
  border-radius: 1.5rem;
  border: 1px solid rgba(142, 14, 63, 0.2);
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(247, 239, 233, 0.82));
  box-shadow: 0 25px 55px rgba(24, 12, 8, 0.1);
  backdrop-filter: blur(6px);
}

.hero-copy { display: flex; flex-direction: column; gap: 0.85rem; }
.hero-kicker { font-size: 0.7rem; letter-spacing: 0.28em; text-transform: uppercase; color: var(--admin-text-secondary); font-weight: 700; }
.hero-title { font-size: 2rem; line-height: 1.1; color: var(--admin-text-primary); text-shadow: 0 2px 14px rgba(142, 14, 63, 0.18); }
.hero-subtitle { color: var(--admin-text-secondary); max-width: 520px; }
.hero-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.hero-status { display: grid; gap: 0.8rem; }
.status-card { background: rgba(255, 255, 255, 0.85); border: 1px solid rgba(142, 14, 63, 0.2); border-radius: 1rem; padding: 1rem 1.25rem; box-shadow: 0 16px 35px rgba(22, 12, 8, 0.08); display: flex; flex-direction: column; gap: 0.35rem; }
.status-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.2em; color: var(--admin-text-secondary); }
.status-value { font-size: 1.1rem; font-weight: 700; color: var(--admin-text-primary); }
.page-topbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.9rem 1.25rem; background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1.1rem; box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08); backdrop-filter: blur(8px); }
.status-chip { background: rgba(142, 14, 63, 0.12); color: var(--admin-text-primary); padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
.panel { background: #fff; border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1.2rem; padding: 1.5rem; box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08); }

.card-container { background: #fff; border: 1px solid rgba(140, 108, 95, 0.2); border-radius: 1rem; box-shadow: 0 12px 28px rgba(18, 9, 6, 0.06); overflow: hidden; cursor: pointer; transition: all 0.3s ease; }
.card-container:hover { box-shadow: 0 20px 40px rgba(18, 9, 6, 0.12); transform: translateY(-2px); }

.template-preview { position: relative; aspect-ratio: 16 / 9; background: linear-gradient(135deg, rgba(142, 14, 63, 0.08) 0%, rgba(247, 239, 233, 0.6) 100%); border-bottom: 1px solid rgba(140, 108, 95, 0.2); display: flex; align-items: center; justify-content: center; overflow: hidden; }
.template-content { text-align: center; padding: 1rem; z-index: 1; }
.template-title { font-size: 0.95rem; font-weight: 700; color: var(--admin-text-primary); font-family: Georgia, serif; }
.template-type { font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.25rem; }
.template-size { font-size: 0.7rem; color: #999; margin-top: 0.35rem; }

.template-overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; gap: 0.5rem; opacity: 0; transition: opacity 0.3s ease; }
.card-container:hover .template-overlay { opacity: 1; }

.btn-action { padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; }
.btn-action-edit { background: #3b82f6; color: white; }
.btn-action-edit:hover { background: #2563eb; }
.btn-action-delete { background: #ef4444; color: white; }
.btn-action-delete:hover { background: #dc2626; }

.template-info { padding: 1rem; }
.template-info-title { font-weight: 600; color: var(--admin-text-primary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.template-info-description { font-size: 0.85rem; color: var(--admin-text-secondary); margin-top: 0.35rem; overflow: hidden; height: 2.4rem; }
.template-info-footer { margin-top: 0.75rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }

.badge { display: inline-flex; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600; }
.badge-default { background: #dcfce7; color: #166534; }

.btn-admin-primary { background: #8e0e3f; color: white; padding: 0.625rem 1.25rem; border-radius: 0.75rem; border: none; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
.btn-admin-primary:hover { background: #6b0a30; box-shadow: 0 8px 16px rgba(142, 14, 63, 0.3); }

.btn-admin-secondary { background: rgba(142, 14, 63, 0.1); color: var(--admin-text-primary); padding: 0.625rem 1.25rem; border-radius: 0.75rem; border: 1px solid rgba(142, 14, 63, 0.2); font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; }
.btn-admin-secondary:hover { background: rgba(142, 14, 63, 0.15); border-color: rgba(142, 14, 63, 0.3); }

@media (max-width: 1024px) {
  .page-hero { grid-template-columns: 1fr; }
}
</style>
