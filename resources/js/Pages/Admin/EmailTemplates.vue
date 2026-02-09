<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Email Templates
          </h1>
          <p class="text-gray-600 mt-2">
            Manage transactional and marketing email templates
          </p>
        </div>
        <button
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          @click="openCreate"
        >
          + New Template
        </button>
      </div>
      
      <AdminQuickNav />

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="selectedCategory"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Categories
              </option>
              <option value="auth">
                Authentication
              </option>
              <option value="booking">
                Bookings
              </option>
              <option value="payments">
                Payments
              </option>
              <option value="marketing">
                Marketing
              </option>
              <option value="system">
                System
              </option>
            </select>
          </div>
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
              <option value="draft">
                Draft
              </option>
              <option value="archived">
                Archived
              </option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search template name or subject..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Templates
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ templates.length }}
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
              Drafts
            </p>
            <p class="text-2xl font-bold text-yellow-600">
              {{ draftCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Archived
            </p>
            <p class="text-2xl font-bold text-gray-600">
              {{ archivedCount }}
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
                    Template
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Subject
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Category
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Updated
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Status
                  </th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="template in filteredTemplates"
                  :key="template.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">
                      {{ template.name }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ template.code }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700">
                    {{ template.subject }}
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <span class="inline-flex px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium capitalize">
                      {{ template.category }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(template.updatedAt) }}
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', getStatusClass(template.status)]">
                      {{ template.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                        @click="previewTemplate(template.id)"
                      >
                        Preview
                      </button>
                      <button
                        class="px-3 py-1 text-sm bg-gray-600 text-white rounded hover:bg-gray-700"
                        @click="editTemplate(template.id)"
                      >
                        Edit
                      </button>
                      <button
                        v-if="template.status !== 'active'"
                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                        @click="activateTemplate(template.id)"
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
            v-if="filteredTemplates.length === 0"
            class="text-center py-12"
          >
            <p class="text-gray-600 text-lg">
              No templates found
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
          New Email Template
        </h2>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Template Name"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="formData.subject"
          type="text"
          placeholder="Email Subject"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <select
          v-model="formData.category"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="auth">
            Authentication
          </option>
          <option value="booking">
            Bookings
          </option>
          <option value="payments">
            Payments
          </option>
          <option value="marketing">
            Marketing
          </option>
          <option value="system">
            System
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
            @click="createTemplate"
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
const selectedCategory = ref('')
const selectedStatus = ref('')
const searchQuery = ref('')
const showCreateModal = ref(false)

const templates = ref([
  {
    id: 1,
    name: 'Welcome Email',
    code: 'welcome_email',
    subject: 'Welcome to Photographar!',
    category: 'auth',
    status: 'active',
    updatedAt: '2026-02-02'
  },
  {
    id: 2,
    name: 'Password Reset',
    code: 'password_reset',
    subject: 'Reset Your Password',
    category: 'auth',
    status: 'active',
    updatedAt: '2026-01-30'
  },
  {
    id: 3,
    name: 'Booking Confirmation',
    code: 'booking_confirmation',
    subject: 'Your Booking is Confirmed',
    category: 'booking',
    status: 'active',
    updatedAt: '2026-01-28'
  },
  {
    id: 4,
    name: 'Payment Receipt',
    code: 'payment_receipt',
    subject: 'Payment Receipt',
    category: 'payments',
    status: 'draft',
    updatedAt: '2026-01-25'
  },
  {
    id: 5,
    name: 'Monthly Newsletter',
    code: 'monthly_newsletter',
    subject: 'February Updates & Tips',
    category: 'marketing',
    status: 'archived',
    updatedAt: '2026-01-18'
  }
])

const formData = ref({
  name: '',
  subject: '',
  category: 'auth'
})

const filteredTemplates = computed(() => {
  return templates.value.filter(template => {
    const categoryMatch = !selectedCategory.value || template.category === selectedCategory.value
    const statusMatch = !selectedStatus.value || template.status === selectedStatus.value
    const searchMatch = !searchQuery.value ||
      template.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      template.subject.toLowerCase().includes(searchQuery.value.toLowerCase())

    return categoryMatch && statusMatch && searchMatch
  })
})

const activeCount = computed(() => templates.value.filter(t => t.status === 'active').length)
const draftCount = computed(() => templates.value.filter(t => t.status === 'draft').length)
const archivedCount = computed(() => templates.value.filter(t => t.status === 'archived').length)

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-500',
    draft: 'bg-yellow-500',
    archived: 'bg-gray-500'
  }
  return classes[status] || 'bg-gray-500'
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const previewTemplate = (templateId) => {
  toast.value = { show: true, message: `Previewing template ${templateId}...`, type: 'info' }
}

const editTemplate = (templateId) => {
  toast.value = { show: true, message: `Editing template ${templateId}...`, type: 'info' }
}

const activateTemplate = (templateId) => {
  const template = templates.value.find(t => t.id === templateId)
  if (template) {
    template.status = 'active'
    toast.value = { show: true, message: `${template.name} activated!`, type: 'success' }
  }
}

const openCreate = () => {
  showCreateModal.value = true
}

const createTemplate = () => {
  if (!formData.value.name || !formData.value.subject) {
    toast.value = { show: true, message: 'Name and subject are required.', type: 'error' }
    return
  }

  templates.value.unshift({
    id: Math.max(...templates.value.map(t => t.id)) + 1,
    name: formData.value.name,
    code: formData.value.name.toLowerCase().replace(/\s+/g, '_'),
    subject: formData.value.subject,
    category: formData.value.category,
    status: 'draft',
    updatedAt: new Date().toISOString().split('T')[0]
  })

  showCreateModal.value = false
  formData.value = { name: '', subject: '', category: 'auth' }
  toast.value = { show: true, message: 'Template created successfully!', type: 'success' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 700)
})
</script>
