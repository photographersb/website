<template>
  <Transition name="slide-up">
    <div v-if="showBanner" class="fixed bottom-0 left-0 right-0 z-50 bg-white shadow-2xl border-t border-gray-200">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Privacy & Cookie Settings</h3>
            <p class="text-gray-600 text-sm">
              We use cookies and analytics to understand how you use our site and to improve your experience. 
              By default, non-essential tracking is disabled. You can enable it to help us serve you better.
              <a href="/privacy" class="text-blue-600 hover:underline">Learn more</a>
            </p>
          </div>

          <div class="flex gap-3 flex-shrink-0">
            <button
              @click="denyAll"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
            >
              Decline All
            </button>
            <button
              @click="acceptAll"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
            >
              Accept All
            </button>
            <button
              @click="showSettings = true"
              class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors"
            >
              Settings
            </button>
          </div>
        </div>
      </div>

      <!-- Cookie Settings Modal -->
      <Teleport to="body">
        <Transition name="fade">
          <div v-if="showSettings" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-96 overflow-y-auto">
              <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Cookie Settings</h2>
                <button
                  @click="showSettings = false"
                  class="text-gray-400 hover:text-gray-600"
                >
                  ✕
                </button>
              </div>

              <div class="px-6 py-4 space-y-4">
                <!-- Essential Cookies -->
                <div class="flex items-start gap-3">
                  <input
                    type="checkbox"
                    id="essential"
                    v-model="consentSettings.essential"
                    disabled
                    class="mt-1"
                  />
                  <label for="essential" class="flex-1 cursor-pointer">
                    <span class="font-medium text-gray-900">Essential Cookies</span>
                    <p class="text-xs text-gray-600 mt-1">Required for site functionality. Always enabled.</p>
                  </label>
                </div>

                <!-- Analytics -->
                <div class="flex items-start gap-3">
                  <input
                    type="checkbox"
                    id="analytics"
                    v-model="consentSettings.analytics_storage"
                    class="mt-1"
                  />
                  <label for="analytics" class="flex-1 cursor-pointer">
                    <span class="font-medium text-gray-900">Analytics</span>
                    <p class="text-xs text-gray-600 mt-1">Help us understand how you use our site to improve it.</p>
                  </label>
                </div>

                <!-- Ad Storage -->
                <div class="flex items-start gap-3">
                  <input
                    type="checkbox"
                    id="ad_storage"
                    v-model="consentSettings.ad_storage"
                    class="mt-1"
                  />
                  <label for="ad_storage" class="flex-1 cursor-pointer">
                    <span class="font-medium text-gray-900">Advertising</span>
                    <p class="text-xs text-gray-600 mt-1">Personalized ads based on your activity.</p>
                  </label>
                </div>

                <!-- Ad Personalization -->
                <div class="flex items-start gap-3">
                  <input
                    type="checkbox"
                    id="ad_personalization"
                    v-model="consentSettings.ad_personalization"
                    class="mt-1"
                  />
                  <label for="ad_personalization" class="flex-1 cursor-pointer">
                    <span class="font-medium text-gray-900">Ad Personalization</span>
                    <p class="text-xs text-gray-600 mt-1">Show ads tailored to your interests.</p>
                  </label>
                </div>

                <!-- Ad User Data -->
                <div class="flex items-start gap-3">
                  <input
                    type="checkbox"
                    id="ad_user_data"
                    v-model="consentSettings.ad_user_data"
                    class="mt-1"
                  />
                  <label for="ad_user_data" class="flex-1 cursor-pointer">
                    <span class="font-medium text-gray-900">Ad User Data</span>
                    <p class="text-xs text-gray-600 mt-1">Send user data to Google for advertising purposes.</p>
                  </label>
                </div>
              </div>

              <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex gap-3 justify-end">
                <button
                  @click="showSettings = false"
                  class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                >
                  Cancel
                </button>
                <button
                  @click="saveSettings"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
                >
                  Save Settings
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </Teleport>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const showBanner = ref(false)
const showSettings = ref(false)
const consentSettings = ref({
  essential: true,
  analytics_storage: false,
  ad_storage: false,
  ad_personalization: false,
  ad_user_data: false,
})

onMounted(() => {
  // Check if user has already given consent
  const savedConsent = localStorage.getItem('cookieConsent')
  
  if (!savedConsent) {
    // First time - show banner
    showBanner.value = true
    // Set default consent to 'denied' for all tracking
    updateGAConsent({
      analytics_storage: 'denied',
      ad_storage: 'denied',
      ad_personalization: 'denied',
      ad_user_data: 'denied',
    })
  } else {
    // Load saved preferences
    try {
      const saved = JSON.parse(savedConsent)
      consentSettings.value = saved
    } catch (e) {
      // If invalid, show banner again
      showBanner.value = true
    }
  }
})

/**
 * Update Google Analytics consent settings
 */
function updateGAConsent(settings) {
  if (window.gtag && typeof window.gtag === 'function') {
    window.gtag('consent', 'update', settings)
  }
}

/**
 * Accept all cookies
 */
function acceptAll() {
  consentSettings.value = {
    essential: true,
    analytics_storage: true,
    ad_storage: true,
    ad_personalization: true,
    ad_user_data: true,
  }
  saveConsent()
}

/**
 * Deny all non-essential cookies
 */
function denyAll() {
  consentSettings.value = {
    essential: true,
    analytics_storage: false,
    ad_storage: false,
    ad_personalization: false,
    ad_user_data: false,
  }
  saveConsent()
}

/**
 * Save custom settings
 */
function saveSettings() {
  saveConsent()
  showSettings.value = false
}

/**
 * Save consent to localStorage and update GA
 */
function saveConsent() {
  localStorage.setItem('cookieConsent', JSON.stringify(consentSettings.value))
  
  // Update GA consent
  const gaSettings = {
    analytics_storage: consentSettings.value.analytics_storage ? 'granted' : 'denied',
    ad_storage: consentSettings.value.ad_storage ? 'granted' : 'denied',
    ad_personalization: consentSettings.value.ad_personalization ? 'granted' : 'denied',
    ad_user_data: consentSettings.value.ad_user_data ? 'granted' : 'denied',
  }
  
  updateGAConsent(gaSettings)
  showBanner.value = false
}

// Expose save consent for external access
defineExpose({
  saveConsent,
  updateGAConsent,
})
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  transform: translateY(100%);
  opacity: 0;
}

.slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
