<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="Add Judge" 
      subtitle="Create a new competition judge"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
          <form @submit.prevent="saveJudge" class="space-y-6">
            <!-- Basic Information -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                  <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required placeholder="Judge Name" />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                  <input v-model="form.title" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g., Photography Judge" />
                </div>
              </div>
            </div>

            <!-- Organization -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Organization</h3>
              <label class="block text-sm font-medium text-gray-700 mb-2">Organization</label>
              <input v-model="form.organization" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Organization Name" />
            </div>

            <!-- Link to User -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Link to User (Optional)</h3>
              <label class="block text-sm font-medium text-gray-700 mb-2">Promote Existing User to Judge</label>
              <select v-model.number="form.user_id" @change="onUserSelected" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option :value="null">-- Select a user or leave blank --</option>
                <option v-for="user in availableUsers" :key="user.id" :value="user.id">
                  {{ user.name }} ({{ user.email }})
                </option>
              </select>
              <small v-if="form.user_id" class="text-gray-500">Selected user data will auto-populate above</small>
            </div>

            <!-- Contact Information -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input v-model="form.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="judge@example.com" />
            </div>

            <!-- Bio -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
              <textarea v-model="form.bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Tell us about the judge..."></textarea>
            </div>

            <!-- Social Media -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Media & Web</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                  <input v-model="form.facebook_url" type="url" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="https://facebook.com/..." />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                  <input v-model="form.instagram_url" type="url" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="https://instagram.com/..." />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                  <input v-model="form.website_url" type="url" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="https://..." />
                </div>
              </div>
            </div>

            <!-- Profile Image -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
              <input @change="handleFileChange" type="file" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>

            <!-- Additional Settings -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Settings</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                  <input v-model.number="form.sort_order" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="0" />
                </div>
                <div class="flex items-end">
                  <label class="flex items-center gap-2">
                    <input v-model="form.is_active" type="checkbox" class="w-4 h-4" />
                    <span class="text-sm font-medium text-gray-700">Active</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t">
              <button type="button" @click="router.back()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Cancel
              </button>
              <button type="submit" :disabled="saving" class="px-4 py-2 text-white rounded-lg hover:bg-opacity-90" style="background-color: var(--admin-brand-primary);">
                {{ saving ? 'Creating...' : 'Create Judge' }}
              </button>
            </div>
          </form>

          <div v-if="showToast" class="mt-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ toastMessage }}
          </div>
          <div v-if="generalError" class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ generalError }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

const router = useRouter()

const form = ref({
  name: '',
  title: '',
  organization: '',
  bio: '',
  email: '',
  facebook_url: '',
  instagram_url: '',
  website_url: '',
  is_active: true,
  sort_order: 0,
  user_id: null
})

const errors = ref({})
const saving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const generalError = ref('')
const selectedFile = ref(null)
const availableUsers = ref([])

const handleFileChange = (event) => {
  selectedFile.value = event.target.files[0] || null
}

const onUserSelected = () => {
  if (form.value.user_id) {
    const selectedUser = availableUsers.value.find(u => u.id === form.value.user_id)
    if (selectedUser) {
      form.value.name = selectedUser.name || form.value.name
      form.value.email = selectedUser.email || form.value.email
    }
  }
}

const fetchAvailableUsers = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/users?limit=500`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()
    if (data.status === 'success') {
      availableUsers.value = data.data.data || data.data || []
    }
  } catch (error) {
    console.error('Error fetching users', error)
  }
}

const saveJudge = async () => {
  saving.value = true
  errors.value = {}
  generalError.value = ''

  try {
    const token = localStorage.getItem('auth_token')
    const formData = new FormData()

    Object.entries(form.value).forEach(([key, value]) => {
      if (key === 'is_active') {
        formData.append(key, value ? '1' : '0')
      } else if (value !== null && value !== undefined && value !== '') {
        formData.append(key, value)
      }
    })

    if (selectedFile.value) {
      formData.append('profile_image', selectedFile.value)
    }

    const response = await fetch('/api/v1/admin/judges', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
      body: formData
    })

    const data = await response.json()

    if (response.ok && data.status === 'success') {
      toastMessage.value = 'Judge created successfully!'
      showToast.value = true
      setTimeout(() => {
        router.push('/admin/judges')
      }, 1500)
    } else {
      if (data.errors) {
        errors.value = data.errors
      }
      generalError.value = data.message || 'Error creating judge'
    }
  } catch (error) {
    console.error('Error saving judge', error)
    generalError.value = 'Error creating judge. Please try again.'
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchAvailableUsers()
})
</script>

<style scoped>
button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
