<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="Admin Profile"
      subtitle="Manage your profile and contact details"
    />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="bg-white shadow-sm rounded-xl p-6 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Profile overview</h2>
            <p class="text-sm text-gray-500">Review and update your admin profile information.</p>
          </div>
          <span class="text-xs font-semibold tracking-wide uppercase bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
            Admin
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="field-label">Full name</label>
            <input
              v-model="profile.name"
              type="text"
              class="form-input"
              placeholder="Admin name"
            >
          </div>
          <div>
            <label class="field-label">Email</label>
            <input
              v-model="profile.email"
              type="email"
              class="form-input is-readonly"
              placeholder="admin@photographersb.com"
              disabled
            >
            <p class="helper-text">Update email from Account Settings.</p>
          </div>
          <div>
            <label class="field-label">Phone</label>
            <input
              v-model="profile.phone"
              type="tel"
              class="form-input"
              placeholder="+880"
            >
          </div>
          <div>
            <label class="field-label">Role</label>
            <input
              v-model="profile.role"
              type="text"
              class="form-input is-readonly"
              disabled
            >
          </div>
        </div>

        <div class="pt-2 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm text-gray-500">
            Changes update your admin profile immediately.
              </p>
          <button
            class="btn-primary"
            type="button"
            @click="saveProfile"
          >
            Save changes
          </button>
        </div>
      </div>
    </div>

    <div
      v-if="showNotice"
      class="notice-toast"
      role="status"
    >
      {{ noticeMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'

const profile = ref({
  name: '',
  email: '',
  phone: '',
  role: 'Admin'
})

const showNotice = ref(false)
const noticeMessage = ref('')

const showToast = (message) => {
  noticeMessage.value = message
  showNotice.value = true
  setTimeout(() => {
    showNotice.value = false
  }, 2500)
}

const loadProfile = async () => {
  try {
    const response = await api.get('/admin/profile')
    const data = response.data?.data
    if (data) {
      profile.value = {
        name: data.name || '',
        email: data.email || '',
        phone: data.phone || '',
        role: data.role || 'Admin'
      }
    }
  } catch (error) {
    showToast('Unable to load profile.')
  }
}

const saveProfile = async () => {
  try {
    const payload = {
      name: profile.value.name,
      phone: profile.value.phone
    }
    await api.put('/admin/profile', payload)
    showToast('Profile updated.')
  } catch (error) {
    showToast('Unable to update profile.')
  }
}

onMounted(() => {
  loadProfile()
})
</script>

<style scoped>
.field-label {
  display: block;
  margin-bottom: 0.5rem;
  color: #374151;
  font-weight: 500;
  font-size: 0.875rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: var(--admin-brand-primary);
  box-shadow: 0 0 0 3px rgba(139, 21, 56, 0.12);
}

.form-input.is-readonly {
  background: #f9fafb;
  color: #6b7280;
}

.helper-text {
  margin-top: 0.35rem;
  font-size: 0.75rem;
  color: #6b7280;
}

.btn-primary {
  background: var(--admin-brand-primary);
  color: #ffffff;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: var(--admin-brand-primary-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(139, 21, 56, 0.2);
}

.notice-toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #111827;
  color: #ffffff;
  padding: 0.75rem 1.25rem;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
  font-size: 0.875rem;
}
</style>
