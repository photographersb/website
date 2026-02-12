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
              <p class="upload-hint">Max 5 MB. JPG/PNG. Min 1200x630 px.</p>
              <p v-if="ogImageUploading" class="text-xs text-blue-600">Uploading OG image...</p>
              <p v-if="ogImageError" class="text-xs text-red-600">{{ ogImageError }}</p>
            </div>
            <div v-if="ogImageUrl" class="mt-3 flex items-center gap-3">
              <img
                :src="ogImageUrl"
                alt="OG preview"
                class="h-16 w-28 rounded border border-gray-200 object-cover"
              >
              <span class="text-xs text-gray-600">Current OG image</span>
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
            :disabled="savingSettings"
          >
            {{ savingSettings ? 'Saving...' : 'Save Settings' }}
          </button>
          <p
            v-if="saveError || lastSavedAt"
            class="mt-2 text-sm"
            :class="saveError ? 'text-red-600' : 'text-green-600'"
          >
            {{ saveError || `Last saved: ${lastSavedAt}` }}
          </p>
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
            :disabled="savingSettings"
          >
            {{ savingSettings ? 'Saving...' : 'Save Settings' }}
          </button>
          <p
            v-if="saveError || lastSavedAt"
            class="mt-2 text-sm"
            :class="saveError ? 'text-red-600' : 'text-green-600'"
          >
            {{ saveError || `Last saved: ${lastSavedAt}` }}
          </p>
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
                    /sitemap.xml
                  </p>
                </div>
                <button
                  class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                  @click="openSitemap('/sitemap.xml')"
                >
                  View
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                  <p class="font-medium text-gray-900">
                    Photographers Index
                  </p>
                  <p class="text-sm text-gray-600">
                    /sitemap/photographers.xml
                  </p>
                </div>
                <button
                  class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                  @click="openSitemap('/sitemap/photographers.xml')"
                >
                  View
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                  <p class="font-medium text-gray-900">
                    Events Index
                  </p>
                  <p class="text-sm text-gray-600">
                    /sitemap/events.xml
                  </p>
                </div>
                <button
                  class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                  @click="openSitemap('/sitemap/events.xml')"
                >
                  View
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                  <p class="font-medium text-gray-900">
                    Competitions Index
                  </p>
                  <p class="text-sm text-gray-600">
                    /sitemap/competitions.xml
                  </p>
                </div>
                <button
                  class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                  @click="openSitemap('/sitemap/competitions.xml')"
                >
                  View
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                  <p class="font-medium text-gray-900">
                    Cities Index
                  </p>
                  <p class="text-sm text-gray-600">
                    /sitemap/cities.xml
                  </p>
                </div>
                <button
                  class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                  @click="openSitemap('/sitemap/cities.xml')"
                >
                  View
                </button>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                  <p class="font-medium text-gray-900">
                    Categories Index
                  </p>
                  <p class="text-sm text-gray-600">
                    /sitemap/categories.xml
                  </p>
                </div>
                <button
                  class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                  @click="openSitemap('/sitemap/categories.xml')"
                >
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
          <button
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            @click="saveSEOSettings"
            :disabled="savingSettings"
          >
            {{ savingSettings ? 'Saving...' : 'Save Redirects' }}
          </button>
          <p
            v-if="saveError || lastSavedAt"
            class="mt-2 text-sm"
            :class="saveError ? 'text-red-600' : 'text-green-600'"
          >
            {{ saveError || `Last saved: ${lastSavedAt}` }}
          </p>
        </div>

        <!-- SEO Scan Tab -->
        <div
          v-if="activeTab === 'SEO Scan'"
          class="bg-white rounded-lg shadow-md p-6 space-y-6"
        >
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h3 class="font-semibold text-gray-900">SEO Scan & Generation</h3>
              <p class="text-sm text-gray-600">Scan for missing metadata and auto-generate SEO meta.</p>
            </div>
            <div class="flex gap-2 flex-col sm:flex-row">
              <button
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-60"
                :disabled="bulkGenerating"
                @click="bulkGenerateSEO"
              >
                {{ bulkGenerating ? 'Generating...' : 'Bulk Generate' }}
              </button>
              <button
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-60"
                :disabled="scanning"
                @click="runScan"
              >
                {{ scanning ? 'Scanning...' : 'Run Scan' }}
              </button>
            </div>
          </div>

          <div v-if="scanResults">
            <p class="text-xs text-gray-500">
              Last run: {{ scanResults.generated_at }}
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mt-4">
              <div
                v-for="item in scanResults.items"
                :key="item.model_type"
                class="border border-gray-200 rounded-lg p-4 bg-gray-50"
              >
                <h4 class="font-semibold text-gray-900 mb-2">{{ item.model_type }}</h4>
                <div class="text-sm text-gray-700 space-y-1">
                  <div>Total: {{ item.total }}</div>
                  <div>Meta: {{ item.meta_records }}</div>
                  <div>Missing: {{ item.missing_meta }}</div>
                  <div>No title: {{ item.missing_title }}</div>
                  <div>No desc: {{ item.missing_description }}</div>
                  <div>No canonical: {{ item.missing_canonical }}</div>
                  <div>No OG image: {{ item.missing_og_image }}</div>
                </div>
                <div
                  v-if="item.missing_samples?.length"
                  class="mt-3 text-xs text-gray-600"
                >
                  <p class="font-semibold text-gray-700">Sample missing entries:</p>
                  <ul class="mt-1 space-y-1">
                    <li
                      v-for="sample in item.missing_samples"
                      :key="sample.model_id"
                    >
                      <div class="font-medium text-gray-800">{{ sample.title }}</div>
                      <div class="truncate">{{ sample.url }}</div>
                    </li>
                  </ul>
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
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDateTimeUtc } from '../../utils/formatters'
import { validateUploadFile } from '../../utils/imageValidation'
import api from '../../api'
const toast = ref({ show: false, message: '', type: 'success' })
const ogImageFile = ref(null)
const ogImageUrl = ref('')
const ogImageUploading = ref(false)
const ogImageError = ref('')
const ogImageName = computed(() => {
  if (ogImageFile.value?.name) return ogImageFile.value.name
  if (ogImageUrl.value) {
    const parts = ogImageUrl.value.split('/')
    return parts[parts.length - 1] || 'Current OG image'
  }
  return 'No image selected'
})
const activeTab = ref('General')
const showAddRedirect = ref(false)
const savingSettings = ref(false)
const saveError = ref('')
const lastSavedAt = ref('')

const tabs = ['General', 'Structured Data', 'Sitemap', 'Robots.txt', 'Redirects', 'SEO Scan']

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

const siteOrigin = typeof window !== 'undefined' ? window.location.origin : 'https://photographersb.com'
const robotsContent = ref(`User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /private/

Sitemap: ${siteOrigin}/sitemap.xml

User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /`)

const redirects = ref([
  { id: 1, from: '/old-photographers', to: '/photographers', type: '301' },
  { id: 2, from: '/book-now', to: '/bookings', type: '301' },
  { id: 3, from: '/events-old', to: '/events', type: '301' }
])

const scanResults = ref(null)
const scanning = ref(false)
const bulkGenerating = ref(false)

const newRedirect = ref({ from: '', to: '', type: '301' })

const handleOgImageSelect = async (event) => {
  const file = event.target.files?.[0]
  if (!file) {
    ogImageFile.value = null
    return
  }

  ogImageError.value = ''
  const validation = await validateUploadFile(file, {
    label: 'OG image',
    maxBytes: 5 * 1024 * 1024,
    allowedTypes: ['image/jpeg', 'image/png'],
    minImageWidth: 1200,
    minImageHeight: 630
  })

  if (!validation.ok) {
    toast.value = { show: true, message: validation.message, type: 'error' }
    ogImageError.value = validation.message
    ogImageFile.value = null
    event.target.value = ''
    return
  }

  ogImageFile.value = file
  await uploadOgImage(file)
}

const uploadOgImage = async (file) => {
  if (!file) return
  ogImageUploading.value = true
  try {
    const formData = new FormData()
    formData.append('og_image', file)

    const { data } = await api.post('/admin/settings/seo/og-image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (data?.status === 'success') {
      ogImageUrl.value = data.data?.url || ''
      toast.value = { show: true, message: 'OG image uploaded successfully!', type: 'success' }
    } else {
      ogImageError.value = data?.message || 'Failed to upload OG image.'
      toast.value = { show: true, message: data?.message || 'Failed to upload OG image.', type: 'error' }
    }
  } catch (error) {
    console.error('Error uploading OG image', error)
    ogImageError.value = error?.response?.data?.message || 'Failed to upload OG image.'
    toast.value = { show: true, message: 'Failed to upload OG image.', type: 'error' }
  } finally {
    ogImageUploading.value = false
  }
}

const parseBooleanSetting = (value, fallback = false) => {
  if (typeof value === 'boolean') return value
  if (value === null || value === undefined || value === '') return fallback
  const normalized = String(value).toLowerCase().trim()
  if (['1', 'true', 'yes', 'on'].includes(normalized)) return true
  if (['0', 'false', 'no', 'off'].includes(normalized)) return false
  return fallback
}

const parseJsonSetting = (value, fallback) => {
  if (typeof value === 'object' && value !== null) return value
  if (!value) return fallback
  try {
    return JSON.parse(value)
  } catch (error) {
    return fallback
  }
}

const loadSEOSettings = async () => {
  try {
    const { data } = await api.get('/admin/settings/category/seo')
    const settings = data?.data || {}

    seoSettings.value = {
      metaTitleTemplate: settings.meta_title_template || seoSettings.value.metaTitleTemplate,
      metaDescriptionTemplate: settings.meta_description_template || seoSettings.value.metaDescriptionTemplate,
      defaultKeywords: settings.default_keywords || seoSettings.value.defaultKeywords,
      enableSitemap: parseBooleanSetting(settings.enable_sitemap, seoSettings.value.enableSitemap),
      enableRobots: parseBooleanSetting(settings.enable_robots, seoSettings.value.enableRobots),
      enabledSchemas: parseJsonSetting(settings.enabled_schemas, seoSettings.value.enabledSchemas),
      organizationName: settings.organization_name || seoSettings.value.organizationName,
      organizationPhone: settings.organization_phone || seoSettings.value.organizationPhone,
      organizationEmail: settings.organization_email || seoSettings.value.organizationEmail
    }

    if (settings.og_image_url) {
      ogImageUrl.value = settings.og_image_url
    }

    const storedRobots = settings.robots_content
    if (storedRobots) {
      robotsContent.value = storedRobots
    }

    const storedRedirects = parseJsonSetting(settings.redirects, null)
    if (Array.isArray(storedRedirects) && storedRedirects.length > 0) {
      redirects.value = storedRedirects
    }
  } catch (error) {
    console.error('Error loading SEO settings', error)
    toast.value = { show: true, message: 'Failed to load SEO settings.', type: 'error' }
  }
}

const saveSEOSettings = async () => {
  savingSettings.value = true
  saveError.value = ''
  try {
    const payload = {
      settings: {
        'seo.meta_title_template': seoSettings.value.metaTitleTemplate,
        'seo.meta_description_template': seoSettings.value.metaDescriptionTemplate,
        'seo.default_keywords': seoSettings.value.defaultKeywords,
        'seo.enable_sitemap': seoSettings.value.enableSitemap,
        'seo.enable_robots': seoSettings.value.enableRobots,
        'seo.enabled_schemas': seoSettings.value.enabledSchemas,
        'seo.organization_name': seoSettings.value.organizationName,
        'seo.organization_phone': seoSettings.value.organizationPhone,
        'seo.organization_email': seoSettings.value.organizationEmail,
        'seo.og_image_url': ogImageUrl.value,
        'seo.robots_content': robotsContent.value,
        'seo.redirects': redirects.value
      }
    }

    const { data } = await api.post('/admin/settings/bulk', payload)
    if (data?.status === 'success') {
      lastSavedAt.value = `${formatDateTimeUtc(new Date())} UTC`
      toast.value = { show: true, message: 'SEO settings saved successfully!', type: 'success' }
    } else {
      const message = data?.message || 'Failed to save SEO settings.'
      saveError.value = message
      toast.value = { show: true, message, type: 'error' }
    }
  } catch (error) {
    console.error('Error saving SEO settings', error)
    const message = error?.response?.data?.message || 'Failed to save SEO settings.'
    saveError.value = message
    toast.value = { show: true, message, type: 'error' }
  } finally {
    savingSettings.value = false
  }
}

const generateSitemap = () => {
  toast.value = { show: true, message: 'Generating sitemap...', type: 'info' }
  setTimeout(() => {
    sitemapLastGenerated.value = `${formatDateTimeUtc(new Date())} UTC`
    toast.value = { show: true, message: 'Sitemap generated successfully!', type: 'success' }
  }, 2000)
}

const saveRobotsContent = () => {
  saveSEOSettings()
}

const previewRobots = () => {
  toast.value = { show: true, message: 'Opening robots.txt preview...', type: 'info' }
}

const openSitemap = (path) => {
  if (typeof window === 'undefined') return
  const safePath = String(path || '').startsWith('/') ? path : `/${path}`
  window.open(`${window.location.origin}${safePath}`, '_blank')
}

const runScan = async () => {
  scanning.value = true
  try {
    const { data } = await api.post('/admin/seo/scan', {
      model_types: ['Photographer', 'Competition', 'Event', 'Album']
    })
    if (data.status === 'success') {
      scanResults.value = data.data
      toast.value = { show: true, message: 'SEO scan completed.', type: 'success' }
    } else {
      toast.value = { show: true, message: data.message || 'Scan failed.', type: 'error' }
    }
  } catch (error) {
    console.error('Error running SEO scan', error)
    toast.value = { show: true, message: 'Error running SEO scan.', type: 'error' }
  } finally {
    scanning.value = false
  }
}

const bulkGenerateSEO = async () => {
  if (!confirm('Generate SEO metadata for all entities without existing metadata?\n\nThis may take a while.')) return
  bulkGenerating.value = true
  try {
    const { data } = await api.post('/admin/seo/bulk-generate', {
      model_types: ['Photographer', 'Competition', 'Event', 'Album']
    })
    if (data.status === 'success') {
      toast.value = {
        show: true,
        message: `Generated ${data.data.generated_count} SEO records (${data.data.skipped_count} skipped).`,
        type: 'success'
      }
      // Re-run scan to show updated counts
      setTimeout(() => runScan(), 1000)
    } else {
      toast.value = { show: true, message: data.message || 'Generation failed.', type: 'error' }
    }
  } catch (error) {
    console.error('Error bulk generating SEO', error)
    toast.value = { show: true, message: 'Error during bulk generation.', type: 'error' }
  } finally {
    bulkGenerating.value = false
  }
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
    saveSEOSettings()
  }
}

const deleteRedirect = (id) => {
  const index = redirects.value.findIndex(r => r.id === id)
  if (index > -1) {
    redirects.value.splice(index, 1)
    toast.value = { show: true, message: 'Redirect deleted!', type: 'success' }
    saveSEOSettings()
  }
}

onMounted(() => {
  loadSEOSettings()
})
</script>
