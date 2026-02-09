<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Page Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            General Settings
          </h1>
          <p class="text-gray-600 mt-1">
            Configure platform general settings
          </p>
        </div>
        <button
          class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 flex items-center space-x-2"
          @click="saveSettings"
        >
          <span>💾</span>
          <span>Save Changes</span>
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

        <!-- Settings Form -->
      <div
        v-else
        class="space-y-6"
      >
          <!-- Site Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Site Information
            </h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                <input
                  v-model="settings.siteName"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                <input
                  v-model="settings.siteUrl"
                  type="url"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                <textarea
                  v-model="settings.siteDescription"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                <input
                  v-model="settings.adminEmail"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Support Email</label>
                <input
                  v-model="settings.supportEmail"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                >
              </div>
            </div>
          </div>

          <!-- Localization -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Localization
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Default Language</label>
                <select
                  v-model="settings.defaultLanguage"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                >
                  <option value="en">
                    English
                  </option>
                  <option value="es">
                    Spanish
                  </option>
                  <option value="fr">
                    French
                  </option>
                  <option value="de">
                    German
                  </option>
                  <option value="ja">
                    Japanese
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Default Timezone</label>
                <select
                  v-model="settings.defaultTimezone"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                >
                  <option value="UTC">
                    UTC
                  </option>
                  <option value="America/New_York">
                    Eastern Time
                  </option>
                  <option value="America/Chicago">
                    Central Time
                  </option>
                  <option value="America/Denver">
                    Mountain Time
                  </option>
                  <option value="America/Los_Angeles">
                    Pacific Time
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Features & Functionality -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Features & Functionality
            </h3>
            <div class="space-y-3">
              <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-md cursor-pointer hover:bg-gray-50">
                <input
                  v-model="settings.enableRegistration"
                  type="checkbox"
                  class="rounded text-amber-600 focus:ring-amber-500"
                >
                <span class="text-sm text-gray-700">Enable user registration</span>
              </label>

              <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-md cursor-pointer hover:bg-gray-50">
                <input
                  v-model="settings.enableBookings"
                  type="checkbox"
                  class="rounded text-amber-600 focus:ring-amber-500"
                >
                <span class="text-sm text-gray-700">Enable booking system</span>
              </label>

              <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-md cursor-pointer hover:bg-gray-50">
                <input
                  v-model="settings.enableCompetitions"
                  type="checkbox"
                  class="rounded text-amber-600 focus:ring-amber-500"
                >
                <span class="text-sm text-gray-700">Enable competitions</span>
              </label>

              <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-md cursor-pointer hover:bg-gray-50">
                <input
                  v-model="settings.enableEvents"
                  type="checkbox"
                  class="rounded text-amber-600 focus:ring-amber-500"
                >
                <span class="text-sm text-gray-700">Enable events</span>
              </label>

              <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-md cursor-pointer hover:bg-gray-50">
                <input
                  v-model="settings.enableReviews"
                  type="checkbox"
                  class="rounded text-amber-600 focus:ring-amber-500"
                >
                <span class="text-sm text-gray-700">Enable reviews</span>
              </label>
            </div>
          </div>

          <!-- Maintenance -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              Maintenance
            </h3>
            <div class="space-y-4">
              <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-md cursor-pointer hover:bg-gray-50">
                <input
                  v-model="settings.maintenanceMode"
                  type="checkbox"
                  class="rounded text-amber-600 focus:ring-amber-500"
                >
                <div>
                  <span class="text-sm text-gray-700 font-medium">Maintenance Mode</span>
                  <p class="text-xs text-gray-500">Disable public access temporarily</p>
                </div>
              </label>

              <div
                v-if="settings.maintenanceMode"
                class="p-3 bg-yellow-50 border border-yellow-200 rounded-md"
              >
                <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Message</label>
                <textarea
                  v-model="settings.maintenanceMessage"
                  rows="2"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500"
                  placeholder="Enter message to display during maintenance..."
                />
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end space-x-3">
            <button
              class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-medium"
              @click="resetForm"
            >
              Reset
            </button>
            <button
              class="px-6 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 font-medium"
              @click="saveSettings"
            >
              Save Settings
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
import { ref, onMounted } from 'vue';
import api from '../../api';
import AdminHeader from '../../components/AdminHeader.vue';
import AdminQuickNav from '../../components/AdminQuickNav.vue';
const loading = ref(true);

const toast = ref({
  show: false,
  message: '',
  type: 'success'
});

const settings = ref({
  siteName: 'Photographer SB',
  siteUrl: '',
  siteDescription: '',
  adminEmail: 'info@photographersb.com',
  supportEmail: '',
  defaultLanguage: 'en',
  defaultTimezone: 'Asia/Dhaka',
  enableRegistration: true,
  enableBookings: true,
  enableCompetitions: true,
  enableEvents: true,
  enableReviews: true,
  maintenanceMode: false,
  maintenanceMessage: ''
});

const originalSettings = ref({});

const settingsKeyMap = {
  siteName: 'site.name',
  siteUrl: 'site.url',
  siteDescription: 'site.description',
  adminEmail: 'site.email',
  supportEmail: 'site.support_email',
  defaultLanguage: 'site.language',
  defaultTimezone: 'site.timezone',
  enableRegistration: 'features.registration',
  enableBookings: 'features.bookings',
  enableCompetitions: 'features.competitions',
  enableEvents: 'features.events',
  enableReviews: 'features.reviews',
  maintenanceMode: 'system.maintenance_mode',
  maintenanceMessage: 'system.maintenance_message'
};

const parseBoolean = (value, fallback) => {
  if (value === undefined || value === null || value === '') return fallback;
  return value === true || value === 'true' || value === '1' || value === 1;
};

const applySettingsFromApi = (data) => {
  settings.value = {
    ...settings.value,
    siteName: data['site.name'] || settings.value.siteName,
    siteUrl: data['site.url'] || settings.value.siteUrl,
    siteDescription: data['site.description'] || settings.value.siteDescription,
    adminEmail: data['site.email'] || settings.value.adminEmail,
    supportEmail: data['site.support_email'] || settings.value.supportEmail,
    defaultLanguage: data['site.language'] || settings.value.defaultLanguage,
    defaultTimezone: data['site.timezone'] || settings.value.defaultTimezone,
    enableRegistration: parseBoolean(data['features.registration'], settings.value.enableRegistration),
    enableBookings: parseBoolean(data['features.bookings'], settings.value.enableBookings),
    enableCompetitions: parseBoolean(data['features.competitions'], settings.value.enableCompetitions),
    enableEvents: parseBoolean(data['features.events'], settings.value.enableEvents),
    enableReviews: parseBoolean(data['features.reviews'], settings.value.enableReviews),
    maintenanceMode: parseBoolean(data['system.maintenance_mode'], settings.value.maintenanceMode),
    maintenanceMessage: data['system.maintenance_message'] || settings.value.maintenanceMessage
  };
};

const loadSettings = async () => {
  loading.value = true;
  try {
    const response = await api.get('/admin/settings');
    applySettingsFromApi(response.data?.data || {});
    originalSettings.value = JSON.parse(JSON.stringify(settings.value));
  } catch (error) {
    console.error('Settings load error:', error);
    toast.value = {
      show: true,
      message: 'Failed to load settings',
      type: 'error'
    };
  } finally {
    loading.value = false;
  }
};

const saveSettings = async () => {
  try {
    const payload = Object.entries(settingsKeyMap).map(([localKey, remoteKey]) => {
      let value = settings.value[localKey];
      if (typeof value === 'boolean') {
        value = value ? 'true' : 'false';
      }
      return { key: remoteKey, value: String(value ?? '') };
    });

    await api.post('/admin/settings/bulk', { settings: payload });
    originalSettings.value = JSON.parse(JSON.stringify(settings.value));
    toast.value = {
      show: true,
      message: 'Settings saved successfully',
      type: 'success'
    };
  } catch (error) {
    toast.value = {
      show: true,
      message: 'Failed to save settings',
      type: 'error'
    };
  }
};

const resetForm = () => {
  settings.value = JSON.parse(JSON.stringify(originalSettings.value));
  toast.value = {
    show: true,
    message: 'Form reset to last saved state',
    type: 'info'
  };
};

onMounted(() => {
  loadSettings();
});
</script>
