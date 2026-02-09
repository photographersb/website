<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="Judge Details" 
      :subtitle="judge.name"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="max-w-4xl mx-auto">
        <div
          v-if="loading"
          class="bg-white rounded-lg shadow p-12 text-center"
        >
          <div
            class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 mx-auto mb-4"
            style="border-color: var(--admin-brand-primary);"
          />
          <p class="text-gray-600">
            Loading judge details...
          </p>
        </div>

        <div
          v-else-if="judge.id"
          class="bg-white rounded-lg shadow overflow-hidden"
        >
          <!-- Header Section -->
          <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-6 border-b">
            <div class="flex items-start gap-6">
              <div
                v-if="judge.profile_image_url"
                class="flex-shrink-0"
              >
                <img
                  :src="judge.profile_image_url"
                  :alt="judge.name"
                  class="h-32 w-32 rounded-lg object-cover border-4 border-white shadow-lg"
                >
              </div>
              <div class="flex-1">
                <div class="flex items-start justify-between">
                  <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                      {{ judge.name }}
                    </h1>
                    <p
                      v-if="judge.title"
                      class="text-lg text-gray-600 mt-1"
                    >
                      {{ judge.title }}
                    </p>
                    <p
                      v-if="judge.organization"
                      class="text-gray-500 mt-1"
                    >
                      {{ judge.organization }}
                    </p>
                  </div>
                  <span
                    class="px-4 py-2 rounded-full text-sm font-semibold" 
                    :class="judge.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  >
                    {{ judge.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Content Sections -->
          <div class="p-6 space-y-6">
            <!-- Bio Section -->
            <div
              v-if="judge.bio"
              class="border-b pb-6"
            >
              <h2 class="text-xl font-semibold text-gray-900 mb-3">
                Biography
              </h2>
              <p class="text-gray-700 whitespace-pre-line">
                {{ judge.bio }}
              </p>
            </div>

            <!-- Contact Information -->
            <div class="grid md:grid-cols-2 gap-6 border-b pb-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                  Contact Information
                </h3>
                <div class="space-y-2">
                  <div class="flex items-center gap-2 text-gray-700">
                    <svg
                      class="w-5 h-5 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                      />
                    </svg>
                    <span>{{ judge.email || 'No email' }}</span>
                  </div>
                </div>
              </div>

              <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                  System Information
                </h3>
                <div class="space-y-2">
                  <div class="text-gray-700">
                    <span class="font-medium">Linked User:</span> 
                    <span v-if="judge.user">{{ judge.user.name }} ({{ judge.user.email }})</span>
                    <span
                      v-else
                      class="text-gray-400"
                    >Not linked</span>
                  </div>
                  <div class="text-gray-700">
                    <span class="font-medium">Sort Order:</span> {{ judge.sort_order || 0 }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Social Media -->
            <div
              v-if="judge.facebook_url || judge.instagram_url || judge.website_url"
              class="border-b pb-6"
            >
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Social Media & Web
              </h3>
              <div class="flex flex-wrap gap-3">
                <a
                  v-if="judge.facebook_url"
                  :href="judge.facebook_url"
                  target="_blank" 
                  class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100"
                >
                  <svg
                    class="w-5 h-5"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                  </svg>
                  Facebook
                </a>
                <a
                  v-if="judge.instagram_url"
                  :href="judge.instagram_url"
                  target="_blank" 
                  class="inline-flex items-center gap-2 px-4 py-2 bg-pink-50 text-pink-700 rounded-lg hover:bg-pink-100"
                >
                  <svg
                    class="w-5 h-5"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                  </svg>
                  Instagram
                </a>
                <a
                  v-if="judge.website_url"
                  :href="judge.website_url"
                  target="_blank" 
                  class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100"
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
                      d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"
                    />
                  </svg>
                  Website
                </a>
              </div>
            </div>

            <!-- Timestamps -->
            <div class="text-sm text-gray-500 space-y-1">
              <p><span class="font-medium">Created:</span> {{ formatDate(judge.created_at) }}</p>
              <p><span class="font-medium">Last Updated:</span> {{ formatDate(judge.updated_at) }}</p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t">
            <button
              class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
              @click="router.push('/admin/judges')"
            >
              ← Back to List
            </button>
            <div class="flex gap-3">
              <button
                class="px-4 py-2 text-white rounded-lg hover:opacity-90" 
                style="background-color: var(--admin-brand-primary);" 
                @click="router.push(`/admin/judges/${judge.id}/edit`)"
              >
                Edit Judge
              </button>
              <a
                href="/judge/dashboard"
                target="_blank"
                rel="noopener"
                class="px-4 py-2 text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100"
              >
                Judge Portal
              </a>
              <button
                class="px-4 py-2 border rounded-lg" 
                :class="judge.is_active ? 'text-orange-700 bg-orange-50 border-orange-200 hover:bg-orange-100' : 'text-green-700 bg-green-50 border-green-200 hover:bg-green-100'"
                @click="toggleStatus"
              >
                {{ judge.is_active ? 'Deactivate' : 'Activate' }}
              </button>
              <button
                class="px-4 py-2 text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100"
                @click="deleteJudge"
              >
                Delete
              </button>
            </div>
          </div>
        </div>

        <div
          v-else
          class="bg-white rounded-lg shadow p-12 text-center"
        >
          <p class="text-gray-600">
            Judge not found
          </p>
        </div>
      </div>
    </div>

    <div
      v-if="showToast"
      class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import { formatDateTime as formatDateTimeValue } from '../../../utils/formatters'
import api from '../../../api'

const router = useRouter()
const route = useRoute()
const judge = ref({})
const loading = ref(true)
const showToast = ref(false)
const toastMessage = ref('')
const judgeId = ref(null)

const resolveJudgeId = () => {
  const paramId = route.params.id
  if (paramId) return paramId

  const segments = window.location.pathname.split('/').filter(Boolean)
  const last = segments[segments.length - 1]
  if (last === 'edit' && segments.length > 1) {
    return segments[segments.length - 2]
  }
  return last
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return formatDateTimeValue(dateString) || 'N/A'
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => { showToast.value = false }, 3000)
}

const fetchJudge = async () => {
  try {
    const { data } = await api.get(`/admin/judges/${judgeId.value}`)

    if (data.status === 'success' && data.data) {
      judge.value = data.data
    } else {
      showToastMessage('Judge not found')
    }
  } catch (error) {
    console.error('Error fetching judge', error)
    showToastMessage('Error loading judge')
  } finally {
    loading.value = false
  }
}

const toggleStatus = async () => {
  try {
    const { data } = await api.post(`/admin/judges/${judgeId.value}/toggle-status`)
    if (data.status === 'success') {
      judge.value.is_active = data.data.is_active
      showToastMessage('Status updated successfully')
    }
  } catch (error) {
    console.error('Error toggling status', error)
    showToastMessage('Error updating status')
  }
}

const deleteJudge = async () => {
  if (!confirm('Are you sure you want to delete this judge? This action cannot be undone.')) return
  
  try {
    const { data } = await api.delete(`/admin/judges/${judgeId.value}`)
    if (data.status === 'success') {
      showToastMessage('Judge deleted successfully')
      setTimeout(() => {
        router.push('/admin/judges')
      }, 1500)
    }
  } catch (error) {
    console.error('Error deleting judge', error)
    showToastMessage('Error deleting judge')
  }
}

onMounted(() => {
  judgeId.value = resolveJudgeId()
  fetchJudge()
})
</script>

<style scoped>
/* Toast animation */
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

.fixed {
  animation: slideIn 0.3s ease-out;
}
</style>
