<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="Edit Mentor" 
      subtitle="Update mentor profile"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2" style="border-color: var(--admin-brand-primary);"></div>
          </div>

          <form v-else @submit.prevent="saveMentor" class="space-y-6">
            <!-- Basic Information -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                  <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required placeholder="Mentor Name" />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                  <input v-model="form.title" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g., Professional Photographer" />
                </div>
              </div>
            </div>

            <!-- Organization & Location -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Organization & Location</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Organization</label>
                  <input v-model="form.organization" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Organization Name" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                  <input v-model="form.country" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Bangladesh" />
                </div>
              </div>
            </div>

            <!-- Link to User -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Link to User (Optional)</h3>
              <label class="block text-sm font-medium text-gray-700 mb-2">Promote Existing User to Mentor</label>
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
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                  <input v-model="form.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="mentor@example.com" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                  <input v-model="form.phone" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="+880 1700 000000" />
                </div>
              </div>
            </div>

            <!-- Bio -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
              <textarea v-model="form.bio" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Tell us about the mentor..."></textarea>
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
              <div v-if="form.profile_image_url" class="mb-2">
                <img :src="form.profile_image_url" :alt="form.name" class="h-24 w-24 object-cover rounded-lg" />
              </div>
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
              <button type="button" @click="$router.back()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Cancel
              </button>
              <button type="submit" :disabled="saving" class="px-4 py-2 text-white bg-var(--admin-brand-primary) rounded-lg hover:bg-opacity-90" style="background-color: var(--admin-brand-primary);">
                {{ saving ? 'Updating...' : 'Update Mentor' }}
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
import { useRouter, useRoute } from 'vue-router'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

const router = useRouter()
const route = useRoute()

const form = ref({
  name: '',
  title: '',
  organization: '',
  bio: '',
  email: '',
  phone: '',
  facebook_url: '',
  instagram_url: '',
  website_url: '',
  country: 'Bangladesh',
  city: '',
  is_active: true,
  sort_order: 0,
  user_id: null,
  profile_image_url: ''
})

const errors = ref({})
const saving = ref(false)
const loading = ref(true)
const showToast = ref(false)
const toastMessage = ref('')
const generalError = ref('')
const selectedFile = ref(null)
const availableUsers = ref([])
const mentorId = ref(null)

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

const fetchMentor = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`/api/v1/admin/mentors/${mentorId.value}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    const data = await response.json()

    if (data.status === 'success' && data.data) {
      const mentor = data.data
      form.value = {
        name: mentor.name || '',
        title: mentor.title || '',
        organization: mentor.organization || '',
        bio: mentor.bio || '',
        email: mentor.email || '',
        phone: mentor.phone || '',
        facebook_url: mentor.facebook_url || '',
        instagram_url: mentor.instagram_url || '',
        website_url: mentor.website_url || '',
        country: mentor.country || 'Bangladesh',
        city: mentor.city || '',
        is_active: mentor.is_active === 1 || mentor.is_active === true,
        sort_order: mentor.sort_order || 0,
        user_id: mentor.user_id || null,
        profile_image_url: mentor.profile_image_url || ''
      }
    }
  } catch (error) {
    console.error('Error fetching mentor', error)
    generalError.value = 'Error loading mentor data'
  } finally {
    loading.value = false
  }
}

const saveMentor = async () => {
  saving.value = true
  errors.value = {}
  generalError.value = ''

  try {
    const token = localStorage.getItem('auth_token')
    const formData = new FormData()

    Object.entries(form.value).forEach(([key, value]) => {
      if (key === 'profile_image_url') {
        return
      }
      if (key === 'is_active') {
        formData.append(key, value ? '1' : '0')
      } else if (value !== null && value !== undefined && value !== '') {
        formData.append(key, value)
      }
    })

    if (selectedFile.value) {
      formData.append('profile_image', selectedFile.value)
    }

    const response = await fetch(`/api/v1/admin/mentors/${mentorId.value}`, {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
      body: formData
    })

    const data = await response.json()

    if (response.ok && data.status === 'success') {
      toastMessage.value = 'Mentor updated successfully!'
      showToast.value = true
      setTimeout(() => {
        router.push('/admin/mentors')
      }, 1500)
    } else {
      if (data.errors) {
        errors.value = data.errors
      }
      generalError.value = data.message || 'Error updating mentor'
    }
  } catch (error) {
    console.error('Error saving mentor', error)
    generalError.value = 'Error updating mentor. Please try again.'
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  mentorId.value = route.params.id
  fetchAvailableUsers()
  fetchMentor()
})
</script>

<style scoped>
button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
