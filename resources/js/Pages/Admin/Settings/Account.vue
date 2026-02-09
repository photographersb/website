<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="Account Settings"
      subtitle="Update your login and security preferences"
    />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <div class="bg-white shadow-sm rounded-xl p-6 space-y-6">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Login details</h2>
          <p class="text-sm text-gray-500">Manage your primary sign-in email and account security.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="field-label">Sign-in email</label>
            <input
              v-model="account.email"
              type="email"
              class="form-input"
              placeholder="admin@photographersb.com"
            >
          </div>
          <div>
            <label class="field-label">Two-factor authentication</label>
            <div class="flex items-center gap-2 text-sm text-gray-600">
              <input
                v-model="account.twoFactorEnabled"
                type="checkbox"
                class="toggle-input"
              >
              <span>Require a verification code at sign-in</span>
            </div>
          </div>
        </div>

        <div class="pt-2 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm text-gray-500">Changes apply to your admin account.</p>
          <button
            class="btn-primary"
            type="button"
            @click="saveAccount"
          >
            Save account changes
          </button>
        </div>
      </div>

      <div class="bg-white shadow-sm rounded-xl p-6 space-y-6">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Password</h2>
          <p class="text-sm text-gray-500">Change your password regularly to keep the account secure.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="field-label">Current password</label>
            <input
              v-model="password.current"
              type="password"
              class="form-input"
              placeholder="Current password"
            >
          </div>
          <div>
            <label class="field-label">New password</label>
            <input
              v-model="password.next"
              type="password"
              class="form-input"
              placeholder="New password"
            >
          </div>
          <div>
            <label class="field-label">Confirm new password</label>
            <input
              v-model="password.confirm"
              type="password"
              class="form-input"
              placeholder="Confirm new password"
            >
          </div>
        </div>

        <div class="pt-2 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm text-gray-500">Password changes take effect immediately.</p>
          <button
            class="btn-primary"
            type="button"
            @click="updatePassword"
          >
            Update password
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
import api from '../../../api'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

const account = ref({
  email: '',
  twoFactorEnabled: false
})

const password = ref({
  current: '',
  next: '',
  confirm: ''
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

const loadAccount = async () => {
  try {
    const response = await api.get('/admin/profile')
    const data = response.data?.data
    if (data) {
      account.value.email = data.email || ''
      account.value.twoFactorEnabled = !!data.two_factor_enabled
    }
  } catch (error) {
    showToast('Unable to load account settings.')
  }
}

const saveAccount = async () => {
  try {
    const payload = {
      email: account.value.email,
      two_factor_enabled: account.value.twoFactorEnabled
    }
    await api.put('/admin/account', payload)
    showToast('Account updated.')
  } catch (error) {
    showToast('Unable to update account settings.')
  }
}

const updatePassword = async () => {
  if (password.value.next !== password.value.confirm) {
    showToast('New passwords do not match.')
    return
  }

  try {
    const payload = {
      current_password: password.value.current,
      password: password.value.next,
      password_confirmation: password.value.confirm
    }
    await api.put('/admin/account/password', payload)
    password.value = { current: '', next: '', confirm: '' }
    showToast('Password updated.')
  } catch (error) {
    showToast('Unable to update password.')
  }
}

onMounted(() => {
  loadAccount()
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

.toggle-input {
  width: 1rem;
  height: 1rem;
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
