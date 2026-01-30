<template>
  <div class="admin-settings">
    <div class="page-header">
      <h1 class="page-title">⚙️ System Settings</h1>
      <p class="page-subtitle">Configure platform settings and preferences</p>
    </div>

    <div class="settings-layout">
      <div class="settings-nav">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="['nav-item', { active: activeTab === tab.id }]"
        >
          {{ tab.icon }} {{ tab.name }}
        </button>
      </div>

      <div class="settings-content">
        <!-- General Settings -->
        <div v-if="activeTab === 'general'" class="settings-section">
          <h2>General Settings</h2>
          
          <div class="setting-group">
            <label>Platform Name</label>
            <input 
              v-model="settings.platform_name" 
              type="text" 
              class="form-input" 
              placeholder="Photographar"
            />
          </div>

          <div class="setting-group">
            <label>Platform Email</label>
            <input 
              v-model="settings.platform_email" 
              type="email" 
              class="form-input" 
              placeholder="admin@photographersb.com"
            />
          </div>

          <div class="setting-group">
            <label>Default Currency</label>
            <select v-model="settings.currency" class="form-input">
              <option value="BDT">BDT (৳)</option>
              <option value="USD">USD ($)</option>
              <option value="EUR">EUR (€)</option>
            </select>
          </div>

          <div class="setting-group">
            <label>Timezone</label>
            <select v-model="settings.timezone" class="form-input">
              <option value="Asia/Dhaka">Asia/Dhaka (GMT+6)</option>
              <option value="UTC">UTC</option>
              <option value="America/New_York">America/New_York (EST)</option>
            </select>
          </div>

          <div class="setting-group">
            <label>Date Format</label>
            <select v-model="settings.date_format" class="form-input">
              <option value="Y-m-d">YYYY-MM-DD</option>
              <option value="d/m/Y">DD/MM/YYYY</option>
              <option value="m/d/Y">MM/DD/YYYY</option>
            </select>
          </div>

          <button @click="saveSettings" class="btn-save">💾 Save Settings</button>
        </div>

        <!-- Email Settings -->
        <div v-if="activeTab === 'email'" class="settings-section">
          <h2>Email Configuration</h2>
          
          <div class="setting-group">
            <label>SMTP Host</label>
            <input 
              v-model="settings.smtp_host" 
              type="text" 
              class="form-input" 
              placeholder="smtp.gmail.com"
            />
          </div>

          <div class="setting-group">
            <label>SMTP Port</label>
            <input 
              v-model="settings.smtp_port" 
              type="number" 
              class="form-input" 
              placeholder="587"
            />
          </div>

          <div class="setting-group">
            <label>SMTP Username</label>
            <input 
              v-model="settings.smtp_username" 
              type="text" 
              class="form-input" 
              placeholder="your-email@gmail.com"
            />
          </div>

          <div class="setting-group">
            <label>SMTP Password</label>
            <input 
              v-model="settings.smtp_password" 
              type="password" 
              class="form-input" 
              placeholder="••••••••"
            />
          </div>

          <div class="setting-group">
            <label>From Name</label>
            <input 
              v-model="settings.mail_from_name" 
              type="text" 
              class="form-input" 
              placeholder="Photographar"
            />
          </div>

          <button @click="saveSettings" class="btn-save">💾 Save Email Settings</button>
        </div>

        <!-- Payment Settings -->
        <div v-if="activeTab === 'payment'" class="settings-section">
          <h2>Payment Gateway Settings</h2>
          
          <div class="gateway-section">
            <h3>💳 Card Payment</h3>
            <div class="setting-group">
              <label class="flex items-center">
                <input v-model="settings.card_enabled" type="checkbox" class="mr-2" />
                Enable Card Payments
              </label>
            </div>
          </div>

          <div class="gateway-section">
            <h3>📱 bKash</h3>
            <div class="setting-group">
              <label class="flex items-center">
                <input v-model="settings.bkash_enabled" type="checkbox" class="mr-2" />
                Enable bKash
              </label>
            </div>
            <div class="setting-group">
              <label>Merchant Number</label>
              <input v-model="settings.bkash_merchant" type="text" class="form-input" />
            </div>
          </div>

          <div class="gateway-section">
            <h3>📲 Nagad</h3>
            <div class="setting-group">
              <label class="flex items-center">
                <input v-model="settings.nagad_enabled" type="checkbox" class="mr-2" />
                Enable Nagad
              </label>
            </div>
          </div>

          <button @click="saveSettings" class="btn-save">💾 Save Payment Settings</button>
        </div>

        <!-- Security Settings -->
        <div v-if="activeTab === 'security'" class="settings-section">
          <h2>Security Settings</h2>
          
          <div class="setting-group">
            <label class="flex items-center">
              <input v-model="settings.two_factor_enabled" type="checkbox" class="mr-2" />
              Enable Two-Factor Authentication
            </label>
          </div>

          <div class="setting-group">
            <label>Session Timeout (minutes)</label>
            <input v-model="settings.session_timeout" type="number" class="form-input" placeholder="120" />
          </div>

          <div class="setting-group">
            <label>Password Minimum Length</label>
            <input v-model="settings.password_min_length" type="number" class="form-input" placeholder="8" />
          </div>

          <button @click="saveSettings" class="btn-save">💾 Save Security Settings</button>
        </div>

        <!-- Notifications Settings -->
        <div v-if="activeTab === 'notifications'" class="settings-section">
          <h2>Notification Settings</h2>
          
          <div class="setting-group">
            <label class="flex items-center">
              <input v-model="settings.email_notifications" type="checkbox" class="mr-2" />
              Enable Email Notifications
            </label>
          </div>

          <div class="setting-group">
            <label class="flex items-center">
              <input v-model="settings.booking_notifications" type="checkbox" class="mr-2" />
              Notify on New Bookings
            </label>
          </div>

          <div class="setting-group">
            <label class="flex items-center">
              <input v-model="settings.review_notifications" type="checkbox" class="mr-2" />
              Notify on New Reviews
            </label>
          </div>

          <button @click="saveSettings" class="btn-save">💾 Save Notification Settings</button>
        </div>

        <!-- Advanced Settings -->
        <div v-if="activeTab === 'advanced'" class="settings-section">
          <h2>Advanced Settings</h2>
          
          <div class="setting-group">
            <label>Maintenance Mode</label>
            <select v-model="settings.maintenance_mode" class="form-input">
              <option value="0">Disabled</option>
              <option value="1">Enabled</option>
            </select>
          </div>

          <div class="setting-group">
            <label>Debug Mode</label>
            <select v-model="settings.debug_mode" class="form-input">
              <option value="0">Disabled</option>
              <option value="1">Enabled</option>
            </select>
            <p class="text-sm text-gray-500 mt-1">⚠️ Disable in production</p>
          </div>

          <div class="setting-group">
            <label>Cache Duration (minutes)</label>
            <input v-model="settings.cache_duration" type="number" class="form-input" placeholder="60" />
          </div>

          <button @click="saveSettings" class="btn-save">💾 Save Advanced Settings</button>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <div v-if="showSuccess" class="success-toast">
      ✅ Settings saved successfully!
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const activeTab = ref('general')
const showSuccess = ref(false)

const tabs = [
  { id: 'general', name: 'General', icon: '⚙️' },
  { id: 'email', name: 'Email', icon: '📧' },
  { id: 'payment', name: 'Payment', icon: '💳' },
  { id: 'security', name: 'Security', icon: '🔒' },
  { id: 'notifications', name: 'Notifications', icon: '🔔' },
  { id: 'advanced', name: 'Advanced', icon: '🔧' }
]

const settings = ref({
  platform_name: 'Photographer SB',
  platform_email: 'admin@photographersb.com',
  currency: 'BDT',
  timezone: 'Asia/Dhaka',
  date_format: 'Y-m-d',
  smtp_host: '',
  smtp_port: 587,
  smtp_username: '',
  smtp_password: '',
  mail_from_name: 'Photographer SB',
  card_enabled: true,
  bkash_enabled: true,
  bkash_merchant: '',
  nagad_enabled: true,
  two_factor_enabled: false,
  session_timeout: 120,
  password_min_length: 8,
  email_notifications: true,
  booking_notifications: true,
  review_notifications: true,
  maintenance_mode: '0',
  debug_mode: '0',
  cache_duration: 60
})

const saveSettings = () => {
  showSuccess.value = true
  setTimeout(() => {
    showSuccess.value = false
  }, 3000)
  console.log('Settings saved:', settings.value)
}

onMounted(() => {
  console.log('Settings page loaded')
})
</script>

<style scoped>
.admin-settings { padding: 2rem; max-width: 1400px; margin: 0 auto; }
.page-header { margin-bottom: 2rem; }
.page-title { font-size: 2rem; color: #1f2937; margin-bottom: 0.5rem; }
.page-subtitle { color: #6b7280; }
.settings-layout { display: grid; grid-template-columns: 250px 1fr; gap: 2rem; }
.settings-nav { background: white; border-radius: 1rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); height: fit-content; position: sticky; top: 2rem; }
.nav-item { width: 100%; text-align: left; padding: 0.75rem 1rem; background: none; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 500; color: #6b7280; margin-bottom: 0.5rem; transition: all 0.2s; }
.nav-item:hover { background: #f3f4f6; }
.nav-item.active { background: #6c0b1a; color: white; }
.settings-content { background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); min-height: 500px; }
.settings-section h2 { margin-bottom: 2rem; color: #1f2937; font-size: 1.5rem; font-weight: 700; }
.settings-section h3 { margin: 1.5rem 0 1rem; color: #374151; font-size: 1.125rem; font-weight: 600; }
.gateway-section { padding: 1.5rem; background: #f9fafb; border-radius: 0.5rem; margin-bottom: 1.5rem; }
.setting-group { margin-bottom: 1.5rem; }
.setting-group label { display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 500; font-size: 0.875rem; }
.form-input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; transition: border-color 0.2s; }
.form-input:focus { outline: none; border-color: #6c0b1a; box-shadow: 0 0 0 3px rgba(108, 11, 26, 0.1); }
.btn-save { background: #6c0b1a; color: white; padding: 0.75rem 2rem; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600; margin-top: 1rem; transition: all 0.2s; }
.btn-save:hover { background: #4a070f; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(108, 11, 26, 0.2); }
.success-toast { position: fixed; bottom: 2rem; right: 2rem; background: #10b981; color: white; padding: 1rem 2rem; border-radius: 0.5rem; box-shadow: 0 10px 15px rgba(0,0,0,0.1); font-weight: 600; animation: slideIn 0.3s ease-out; }
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.text-sm { font-size: 0.875rem; }
.text-gray-500 { color: #6b7280; }
.mt-1 { margin-top: 0.25rem; }
.mr-2 { margin-right: 0.5rem; }
.flex { display: flex; }
.items-center { align-items: center; }

@media (max-width: 768px) {
  .settings-layout { grid-template-columns: 1fr; }
  .settings-nav { position: static; }
}
</style>
