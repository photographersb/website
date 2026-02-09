<template>
  <Teleport to="body">
    <transition name="slide-up">
      <div 
        v-if="!consentGiven && !hidden"
        class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl rounded-t-lg border-t border-gray-200 z-50"
      >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <!-- Message -->
            <div class="flex-1 pr-4">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">
                Cookie Preferences
              </h3>
              <p class="text-sm text-gray-600">
                We use cookies to enhance your experience, analyze site traffic, and show personalized content. 
                <router-link
                  to="/privacy"
                  class="text-burgundy hover:text-burgundy-dark underline"
                >
                  Learn more
                </router-link>
                about our cookie policy.
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
              <button 
                class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-medium whitespace-nowrap"
                @click="acceptRequired"
              >
                Required Only
              </button>
              <button 
                class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition text-sm font-medium whitespace-nowrap"
                @click="acceptAll"
              >
                Accept All Cookies
              </button>
            </div>

            <!-- Close Button -->
            <button 
              class="text-gray-400 hover:text-gray-600 transition"
              aria-label="Close"
              @click="hidden = true"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <!-- Expandable Details (Optional) -->
          <div class="mt-4 pt-4 border-t border-gray-200">
            <button 
              class="text-sm text-burgundy hover:text-burgundy-dark font-medium flex items-center gap-1"
              @click="showDetails = !showDetails"
            >
              {{ showDetails ? 'Hide' : 'Show' }} cookie details
              <svg
                class="w-4 h-4 transition"
                :class="{ 'rotate-180': showDetails }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 14l-7 7m0 0l-7-7m7 7V3"
                />
              </svg>
            </button>

            <!-- Cookie Details -->
            <transition name="expand">
              <div
                v-if="showDetails"
                class="mt-4 space-y-3 text-sm"
              >
                <div class="space-y-2">
                  <div class="flex items-start gap-3">
                    <input 
                      v-model="cookiePreferences.necessary"
                      type="checkbox"
                      disabled
                      class="mt-1"
                    >
                    <div>
                      <p class="font-medium text-gray-900">
                        Necessary Cookies (Always Active)
                      </p>
                      <p class="text-gray-600 mt-1">
                        Required for basic site functionality, user authentication, and security. Cannot be disabled.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-start gap-3">
                    <input 
                      v-model="cookiePreferences.analytics"
                      type="checkbox"
                      class="mt-1"
                    >
                    <div>
                      <p class="font-medium text-gray-900">
                        Analytics Cookies
                      </p>
                      <p class="text-gray-600 mt-1">
                        Help us understand how you use our site so we can improve your experience. Includes Google Analytics and similar tools.
                      </p>
                    </div>
                  </div>

                  <div class="flex items-start gap-3">
                    <input 
                      v-model="cookiePreferences.marketing"
                      type="checkbox"
                      class="mt-1"
                    >
                    <div>
                      <p class="font-medium text-gray-900">
                        Marketing Cookies
                      </p>
                      <p class="text-gray-600 mt-1">
                        Used for personalized ads and remarketing. Helps us show you relevant content and offers.
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Save Preferences Button -->
                <div class="pt-3 border-t border-gray-200">
                  <button 
                    class="px-4 py-2 bg-burgundy text-white rounded hover:bg-burgundy-dark transition text-sm font-medium"
                    @click="savePreferences"
                  >
                    Save Preferences
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const consentGiven = ref(false);
const hidden = ref(false);
const showDetails = ref(false);

const cookiePreferences = ref({
  necessary: true, // Always required
  analytics: false,
  marketing: false,
});

// Check if user has already given consent
const checkExistingConsent = () => {
  const consent = localStorage.getItem('cookie_consent');
  if (consent) {
    consentGiven.value = true;
    const preferences = JSON.parse(consent);
    cookiePreferences.value = preferences;
    
    // Apply saved preferences
    applyPreferences(preferences);
  }
};

// Apply cookie preferences
const applyPreferences = (preferences) => {
  // Load Analytics script if enabled
  if (preferences.analytics) {
    loadGoogleAnalytics();
  }

  // Load Marketing script if enabled  
  if (preferences.marketing) {
    loadMarketingPixels();
  }

  // Always load necessary tracking (auth tokens, session, etc.)
  // This is done by default in app bootstrap
};

// Load Google Analytics
const loadGoogleAnalytics = () => {
  if (window.gtag) return; // Already loaded

  const script = document.createElement('script');
  script.async = true;
  script.src = `https://www.googletagmanager.com/gtag/js?id=${import.meta.env.VITE_GOOGLE_ANALYTICS_ID || ''}`;
  document.head.appendChild(script);

  window.dataLayer = window.dataLayer || [];
  function gtag() {
    dataLayer.push(arguments);
  }
  window.gtag = gtag;
  gtag('js', new Date());
  gtag('config', import.meta.env.VITE_GOOGLE_ANALYTICS_ID || '');
};

// Load Marketing pixels (Facebook, etc.)
const loadMarketingPixels = () => {
  // Facebook Pixel
  if (import.meta.env.VITE_FACEBOOK_PIXEL_ID) {
    const script = document.createElement('script');
    script.innerHTML = `
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '${import.meta.env.VITE_FACEBOOK_PIXEL_ID}');
      fbq('track', 'PageView');
    `;
    document.head.appendChild(script);
  }
};

// Accept all cookies
const acceptAll = () => {
  cookiePreferences.value = {
    necessary: true,
    analytics: true,
    marketing: true,
  };
  saveCookies();
};

// Accept only required cookies
const acceptRequired = () => {
  cookiePreferences.value = {
    necessary: true,
    analytics: false,
    marketing: false,
  };
  saveCookies();
};

// Save custom preferences
const savePreferences = () => {
  saveCookies();
  hidden.value = true;
};

// Persist cookies
const saveCookies = () => {
  localStorage.setItem('cookie_consent', JSON.stringify(cookiePreferences.value));
  localStorage.setItem('cookie_consent_timestamp', new Date().toISOString());
  consentGiven.value = true;
  
  // Apply the selected preferences
  applyPreferences(cookiePreferences.value);
};

onMounted(() => {
  checkExistingConsent();
});
</script>

<style scoped>
.bg-burgundy {
  @apply bg-[#8B0000];
}

.bg-burgundy-dark {
  @apply bg-[#5C0000];
}

.text-burgundy {
  @apply text-[#8B0000];
}

.slide-up-enter-active,
.slide-up-leave-active {
  @apply transition-all duration-300;
}

.slide-up-enter-from {
  @apply translate-y-full opacity-0;
}

.slide-up-leave-to {
  @apply translate-y-full opacity-0;
}

.expand-enter-active,
.expand-leave-active {
  @apply transition-all duration-200;
}

.expand-enter-from {
  @apply max-h-0 opacity-0;
}

.expand-leave-to {
  @apply max-h-0 opacity-0;
}
</style>
