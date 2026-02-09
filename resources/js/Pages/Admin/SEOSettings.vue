<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          SEO Settings
        </h1>
        <p class="text-gray-600 mt-2">
          Configure search engine optimization for your platform
        </p>
      </div>
      
      <AdminQuickNav />

        <!-- Tabs -->
        <div class="flex gap-4 mb-6 border-b border-gray-200">
          <button
            v-for="tab in tabs"
            :key="tab"
            :class="['px-4 py-2 font-medium border-b-2 transition', activeTab === tab ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900']"
            @click="activeTab = tab"
          >
            {{ tab }}
          </button>
        </div>

        <!-- General Settings Tab -->
        <div
          v-if="activeTab === 'General'"
          class="bg-white rounded-lg shadow-md p-6 space-y-6"
        >
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title Template</label>
            <input
              v-model="seoSettings.metaTitleTemplate"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="e.g., {title} | Photographar"
            >
            <p class="text-xs text-gray-500 mt-1">
              Use {title} as a placeholder for page-specific title
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description Template</label>
            <textarea
              v-model="seoSettings.metaDescriptionTemplate"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="Default meta description..."
            />
            <p class="text-xs text-gray-500 mt-1">
              Recommended length: 120-160 characters
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Default Keywords</label>
            <input
              v-model="seoSettings.defaultKeywords"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              placeholder="photography, photographers, booking..."
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">OG Image (for social sharing)</label>
            <div class="space-y-2">
              <input
                type="text"
                readonly
                :value="ogImageName"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                placeholder="No image selected"
              >
              <input
                type="file"
                accept="image/*"
                class="upload-input text-sm"
                @change="handleOgImageSelect"
              >
              <p class="upload-hint">Max 5 MB. JPG/PNG. 1200x630 px.</p>
            </div>
          </div>

          <div class="flex gap-2">
            <input
              v-model="seoSettings.enableSitemap"
              type="checkbox"
              class="rounded"
            >
            <label class="text-sm text-gray-700">Enable XML Sitemap</label>
          </div>

          <div class="flex gap-2">
            <input
              v-model="seoSettings.enableRobots"
              type="checkbox"
              class="rounded"
            >
            <label class="text-sm text-gray-700">Enable Robots.txt Management</label>
          </div>

          <button
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            @click="saveSEOSettings"
          >
            Save Settings
          </button>
        </div>

        <!-- Structured Data Tab -->
        <div
          v-if="activeTab === 'Structured Data'"
          class="bg-white rounded-lg shadow-md p-6 space-y-6"
        >
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">
                Schema Types
              </h3>
              <div class="space-y-2">
                <label
                  v-for="schema in availableSchemas"
                  :key="schema"
                  class="flex items-center gap-2"
                >
                  <input
                    v-model="seoSettings.enabledSchemas"
                    type="checkbox"
                    :value="schema"
                    class="rounded"
                  >
                  <span class="text-sm text-gray-700">{{ schema }}</span>
                </label>
              </div>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">
                Organization Info
              </h3>
              <div class="space-y-3">
                <input
                  v-model="seoSettings.organizationName"
                  type="text"
                  placeholder="Organization Name"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                >
                <input
                  v-model="seoSettings.organizationPhone"
                  type="tel"
                  placeholder="Phone Number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                >
                <input
                  v-model="seoSettings.organizationEmail"
                  type="email"
                  placeholder="Email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                >
              </div>
            </div>
          </div>
          <button
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            @click="saveSEOSettings"
          >
            Save Settings
          </button>
        </div>

        <!-- Sitemap Tab -->
        <div
          v-if="activeTab === 'Sitemap'"
          class="bg-white rounded-lg shadow-md p-6"
        >
          <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">
              Sitemap Configuration
            </h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                  <p class="font-medium text-gray-900">
                    Main Sitemap
                  </p>
                  <p class="text-sm text-gray-600">
                    http://photographar.local/sitemap.xml
                  </p>
                </div>
                <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                  View
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                  <p class="font-medium text-gray-900">
                    Photographers Index
                  </p>
                  <p class="text-sm text-gray-600">
                    http://photographar.local/sitemap-photographers.xml
                  </p>
                </div>
                <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                  View
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                  <p class="font-medium text-gray-900">
                    Events Index
                  </p>
                  <p class="text-sm text-gray-600">
                    http://photographar.local/sitemap-events.xml
                  </p>
                </div>
                <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                  View
                </button>
              </div>
            </div>
          </div>

          <div>
            <h3 class="font-semibold text-gray-900 mb-4">
              Sitemap Generation
            </h3>
            <div class="space-y-2">
              <p class="text-sm text-gray-600">
                Last generated: {{ sitemapLastGenerated }}
              </p>
              <button
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                @click="generateSitemap"
              >
                Regenerate Sitemap Now
              </button>
            </div>
          </div>
        </div>

        <!-- Robots.txt Tab -->
        <div
          v-if="activeTab === 'Robots.txt'"
          class="bg-white rounded-lg shadow-md p-6"
        >
          <h3 class="font-semibold text-gray-900 mb-4">
            Robots.txt Content
          </h3>
          <textarea
            v-model="robotsContent"
            rows="12"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm focus:ring-2 focus:ring-blue-500"
          />
          <div class="mt-4 flex gap-2">
            <button
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
              @click="saveRobotsContent"
            >
              Save Robots.txt
            </button>
            <button
              class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition"
              @click="previewRobots"
            >
              Preview
            </button>
          </div>
        </div>

        <!-- Redirects Tab -->
        <div
          v-if="activeTab === 'Redirects'"
          class="bg-white rounded-lg shadow-md p-6"
        >
          <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">
              URL Redirects
            </h3>
            <button
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition mb-4"
              @click="showAddRedirect = true"
            >
              + Add Redirect
            </button>

            <div class="space-y-2">
              <div
                v-for="redirect in redirects"
                :key="redirect.id"
                class="flex justify-between items-center p-3 bg-gray-50 rounded"
              >
                <div>
                  <p class="font-medium text-gray-900">
                    {{ redirect.from }}
                  </p>
                  <p class="text-sm text-gray-600">
                    → {{ redirect.to }}
                  </p>
                </div>
                <div class="flex gap-2">
                  <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded font-medium">{{ redirect.type }}</span>
                  <button
                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                    @click="deleteRedirect(redirect.id)"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

    <!-- Add Redirect Modal -->
    <div
      v-if="showAddRedirect"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-6 space-y-4">
        <h2 class="text-xl font-bold text-gray-900">
          Add Redirect
        </h2>
        <input
          v-model="newRedirect.from"
          type="text"
          placeholder="From URL (old path)"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <input
          v-model="newRedirect.to"
          type="text"
          placeholder="To URL (new path)"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
        <select
          v-model="newRedirect.type"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="301">
            301 (Permanent)
          </option>
          <option value="302">
            302 (Temporary)
          </option>
        </select>
        <div class="flex gap-3 pt-4">
          <button
            class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
            @click="showAddRedirect = false"
          >
            Cancel
          </button>
          <button
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            @click="addRedirect"
          >
            Add
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
import { ref, computed } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDateTimeUtc } from '../../utils/formatters'
import { validateUploadFile } from '../../utils/imageValidation'
const toast = ref({ show: false, message: '', type: 'success' })
const ogImageFile = ref(null)
const ogImageName = computed(() => ogImageFile.value?.name || 'No image selected')
const activeTab = ref('General')
const showAddRedirect = ref(false)

const tabs = ['General', 'Structured Data', 'Sitemap', 'Robots.txt', 'Redirects']

const seoSettings = ref({
  metaTitleTemplate: 'Photographar | {title}',
  metaDescriptionTemplate: 'Discover talented photographers and book professional photography services on Photographar.',
  defaultKeywords: 'photography, photographers, booking, events, professional',
  enableSitemap: true,
  enableRobots: true,
  enabledSchemas: ['Organization', 'LocalBusiness'],
  organizationName: 'Photographar',
  organizationPhone: '+1 (555) 000-0000',
  organizationEmail: 'info@photographar.local'
})

const availableSchemas = ['Organization', 'LocalBusiness', 'Event', 'Person', 'BreadcrumbList', 'FAQPage']

const sitemapLastGenerated = ref('2026-02-04 at 14:32 UTC')

const robotsContent = ref(`User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /private/

Sitemap: http://photographar.local/sitemap.xml

User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /`)

const redirects = ref([
  { id: 1, from: '/old-photographers', to: '/photographers', type: '301' },
  { id: 2, from: '/book-now', to: '/bookings', type: '301' },
  { id: 3, from: '/events-old', to: '/events', type: '301' }
])

const newRedirect = ref({ from: '', to: '', type: '301' })

const handleOgImageSelect = async (event) => {
  const file = event.target.files?.[0]
  if (!file) {
    ogImageFile.value = null
    return
  }

  const validation = await validateUploadFile(file, {
    label: 'OG image',
    maxBytes: 5 * 1024 * 1024,
    allowedTypes: ['image/jpeg', 'image/png'],
    imageWidth: 1200,
    imageHeight: 630
  })

  if (!validation.ok) {
    toast.value = { show: true, message: validation.message, type: 'error' }
    ogImageFile.value = null
    event.target.value = ''
    return
  }

  ogImageFile.value = file
}

const saveSEOSettings = () => {
  toast.value = { show: true, message: 'SEO settings saved successfully!', type: 'success' }
}

const generateSitemap = () => {
  toast.value = { show: true, message: 'Generating sitemap...', type: 'info' }
  setTimeout(() => {
    sitemapLastGenerated.value = `${formatDateTimeUtc(new Date())} UTC`
    toast.value = { show: true, message: 'Sitemap generated successfully!', type: 'success' }
  }, 2000)
}

const saveRobotsContent = () => {
  toast.value = { show: true, message: 'Robots.txt updated!', type: 'success' }
}

const previewRobots = () => {
  toast.value = { show: true, message: 'Opening robots.txt preview...', type: 'info' }
}

const addRedirect = () => {
  if (newRedirect.value.from && newRedirect.value.to) {
    redirects.value.push({
      id: Math.max(...redirects.value.map(r => r.id)) + 1,
      from: newRedirect.value.from,
      to: newRedirect.value.to,
      type: newRedirect.value.type
    })
    newRedirect.value = { from: '', to: '', type: '301' }
    showAddRedirect.value = false
    toast.value = { show: true, message: 'Redirect added successfully!', type: 'success' }
  }
}

const deleteRedirect = (id) => {
  const index = redirects.value.findIndex(r => r.id === id)
  if (index > -1) {
    redirects.value.splice(index, 1)
    toast.value = { show: true, message: 'Redirect deleted!', type: 'success' }
  }
}
</script>
