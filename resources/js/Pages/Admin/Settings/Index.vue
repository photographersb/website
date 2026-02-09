<template>
  <div class="settings-page">
    <AdminHeader
      title="System Settings"
      subtitle="Control the platform, payments, security, and brand settings"
    />

    <div class="settings-shell">
      <AdminQuickNav />

      <section class="settings-hero">
        <div class="hero-copy">
          <p class="hero-kicker">SYSTEM CONTROL CENTER</p>
          <h1 class="hero-title">Settings, tuned for performance.</h1>
          <p class="hero-subtitle">
            Keep the platform fast, trusted, and on-brand. Save changes in bulk and track every update.
          </p>
          <div class="hero-actions">
            <button class="btn btn-primary" @click="saveSettings" :disabled="saving">
              {{ saving ? 'Saving...' : 'Save All' }}
            </button>
            <button class="btn btn-outline" @click="resetToDefaults" :disabled="saving">
              Reset Defaults
            </button>
          </div>
        </div>
        <div class="hero-status">
          <div class="status-card">
            <span class="status-label">Environment</span>
            <span class="status-value">Production</span>
          </div>
          <div class="status-card">
            <span class="status-label">Current State</span>
            <span class="status-value" :class="isDirty ? 'status-warn' : 'status-ok'">
              {{ isDirty ? 'Unsaved' : 'Synced' }}
            </span>
          </div>
          <div class="status-card">
            <span class="status-label">Brand</span>
            <span class="status-value">{{ settings.platform_name }}</span>
          </div>
        </div>
      </section>

      <div class="settings-topbar">
        <div class="topbar-left">
          <div class="search-box">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search settings"
              class="search-input"
            >
          </div>
          <div class="status-chip" :class="isDirty ? 'status-chip--warn' : 'status-chip--ok'">
            {{ isDirty ? 'Unsaved changes' : 'All changes saved' }}
          </div>
        </div>
        <div class="topbar-actions">
          <button class="btn btn-ghost" @click="goToChangeTracking">
            Change Tracking
          </button>
          <button class="btn btn-ghost" @click="goToSiteLinks">
            Site Links
          </button>
          <button class="btn btn-outline" @click="resetToDefaults" :disabled="saving">
            Reset Defaults
          </button>
          <button class="btn btn-primary" @click="saveSettings" :disabled="saving">
            {{ saving ? 'Saving...' : 'Save All' }}
          </button>
        </div>
      </div>

      <div class="settings-grid">
        <aside class="settings-rail">
          <div class="rail-title">Sections</div>
          <button
            v-for="section in filteredSections"
            :key="section.id"
            :class="['rail-item', { active: activeSection === section.id }]"
            @click="activeSection = section.id"
          >
            <span class="rail-name">{{ section.name }}</span>
            <span class="rail-note">{{ section.note }}</span>
          </button>
        </aside>

        <section class="settings-panel">
          <div class="panel-header">
            <div>
              <h2 class="panel-title">{{ activeSectionMeta.name }}</h2>
              <p class="panel-subtitle">{{ activeSectionMeta.description }}</p>
            </div>
          </div>

          <div v-if="activeSection === 'general'" class="panel-body">
            <div class="field-grid">
              <div v-if="matches('Platform Name')" class="field">
                <label>Platform Name</label>
                <input v-model="settings.platform_name" type="text" placeholder="Photographer SB">
              </div>
              <div v-if="matches('Platform Email')" class="field">
                <label>Platform Email</label>
                <input v-model="settings.platform_email" type="email" placeholder="admin@photographersb.com">
              </div>
              <div v-if="matches('Support Email')" class="field">
                <label>Support Email</label>
                <input v-model="settings.support_email" type="email" placeholder="support@photographersb.com">
              </div>
              <div v-if="matches('Phone')" class="field">
                <label>Contact Phone</label>
                <input v-model="settings.platform_phone" type="text" placeholder="+880 1700 000000">
              </div>
              <div v-if="matches('Address')" class="field field--full">
                <label>Business Address</label>
                <textarea v-model="settings.platform_address" rows="2" placeholder="Street, City, Country" />
              </div>
              <div v-if="matches('Currency')" class="field">
                <label>Default Currency</label>
                <select v-model="settings.currency">
                  <option value="BDT">BDT</option>
                  <option value="USD">USD</option>
                </select>
              </div>
              <div v-if="matches('Timezone')" class="field">
                <label>Timezone</label>
                <select v-model="settings.timezone">
                  <option value="Asia/Dhaka">Asia/Dhaka</option>
                  <option value="UTC">UTC</option>
                  <option value="America/New_York">America/New_York</option>
                </select>
              </div>
              <div v-if="matches('Date Format')" class="field">
                <label>Date Format</label>
                <select v-model="settings.date_format">
                  <option value="d-m-Y">DD-MM-YYYY</option>
                  <option value="Y-m-d">YYYY-MM-DD</option>
                </select>
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'branding'" class="panel-body">
            <div class="field-grid">
              <div v-if="matches('Logo')" class="field">
                <label>Logo URL</label>
                <input v-model="settings.brand_logo" type="url" placeholder="https://...">
                <input
                  type="file"
                  accept="image/*"
                  class="upload-input mt-2 block text-sm"
                  @change="handleImageUpload('brand_logo', $event)"
                >
                <div class="mt-2 flex flex-wrap gap-2">
                  <button
                    type="button"
                    class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                    @click="openPexelsPicker('brand_logo', 600, 300)"
                  >
                    Choose from Pexels
                  </button>
                </div>
                <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 600x300 px.</p>
                <p
                  v-if="uploadingImages.brand_logo"
                  class="mt-1 text-xs text-gray-500"
                >
                  Uploading...
                </p>
                <p
                  v-if="settings.brand_logo_credit_name"
                  class="mt-1 text-xs text-gray-500"
                >
                  Pexels credit:
                  <a
                    :href="settings.brand_logo_credit_url || 'https://www.pexels.com'"
                    target="_blank"
                    rel="noopener"
                    class="font-semibold text-burgundy underline"
                  >
                    {{ settings.brand_logo_credit_name }}
                  </a>
                </p>
              </div>
              <div v-if="matches('Favicon')" class="field">
                <label>Favicon URL</label>
                <input v-model="settings.brand_favicon" type="url" placeholder="https://...">
                <input
                  type="file"
                  accept="image/*"
                  class="upload-input mt-2 block text-sm"
                  @change="handleImageUpload('brand_favicon', $event)"
                >
                <div class="mt-2 flex flex-wrap gap-2">
                  <button
                    type="button"
                    class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                    @click="openPexelsPicker('brand_favicon', 512, 512)"
                  >
                    Choose from Pexels
                  </button>
                </div>
                <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 512x512 px.</p>
                <p
                  v-if="uploadingImages.brand_favicon"
                  class="mt-1 text-xs text-gray-500"
                >
                  Uploading...
                </p>
                <p
                  v-if="settings.brand_favicon_credit_name"
                  class="mt-1 text-xs text-gray-500"
                >
                  Pexels credit:
                  <a
                    :href="settings.brand_favicon_credit_url || 'https://www.pexels.com'"
                    target="_blank"
                    rel="noopener"
                    class="font-semibold text-burgundy underline"
                  >
                    {{ settings.brand_favicon_credit_name }}
                  </a>
                </p>
              </div>
              <div v-if="matches('OG Image')" class="field">
                <label>Default OG Image</label>
                <input v-model="settings.brand_og_image" type="url" placeholder="https://...">
                <input
                  type="file"
                  accept="image/*"
                  class="upload-input mt-2 block text-sm"
                  @change="handleImageUpload('brand_og_image', $event)"
                >
                <div class="mt-2 flex flex-wrap gap-2">
                  <button
                    type="button"
                    class="rounded-full border border-burgundy px-4 py-1 text-xs font-semibold text-burgundy hover:bg-burgundy hover:text-white"
                    @click="openPexelsPicker('brand_og_image', 1200, 630)"
                  >
                    Choose from Pexels
                  </button>
                </div>
                <p class="mt-1 upload-hint">Max 5 MB. JPG/PNG. 1200x630 px.</p>
                <p
                  v-if="uploadingImages.brand_og_image"
                  class="mt-1 text-xs text-gray-500"
                >
                  Uploading...
                </p>
                <p
                  v-if="settings.brand_og_image_credit_name"
                  class="mt-1 text-xs text-gray-500"
                >
                  Pexels credit:
                  <a
                    :href="settings.brand_og_image_credit_url || 'https://www.pexels.com'"
                    target="_blank"
                    rel="noopener"
                    class="font-semibold text-burgundy underline"
                  >
                    {{ settings.brand_og_image_credit_name }}
                  </a>
                </p>
              </div>
              <div v-if="matches('Tagline')" class="field">
                <label>Tagline</label>
                <input v-model="settings.brand_tagline" type="text" placeholder="Bangladesh Photographer Network">
              </div>
              <div v-if="matches('Primary Color')" class="field">
                <label>Primary Color</label>
                <input v-model="settings.brand_primary_color" type="text" placeholder="#8E0E3F">
              </div>
              <div v-if="matches('Secondary Color')" class="field">
                <label>Secondary Color</label>
                <input v-model="settings.brand_secondary_color" type="text" placeholder="#1F2937">
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'email'" class="panel-body">
            <div class="field-grid">
              <div v-if="matches('SMTP Host')" class="field">
                <label>SMTP Host</label>
                <input v-model="settings.smtp_host" type="text" placeholder="smtp.gmail.com">
              </div>
              <div v-if="matches('SMTP Port')" class="field">
                <label>SMTP Port</label>
                <input v-model.number="settings.smtp_port" type="number" placeholder="587">
              </div>
              <div v-if="matches('SMTP Username')" class="field">
                <label>SMTP Username</label>
                <input v-model="settings.smtp_username" type="text" placeholder="email@example.com">
              </div>
              <div v-if="matches('SMTP Password')" class="field">
                <label>SMTP Password</label>
                <input v-model="settings.smtp_password" type="password" placeholder="Update password">
              </div>
              <div v-if="matches('From Name')" class="field">
                <label>From Name</label>
                <input v-model="settings.mail_from_name" type="text" placeholder="Photographer SB">
              </div>
              <div v-if="matches('From Address')" class="field">
                <label>From Address</label>
                <input v-model="settings.mail_from_address" type="email" placeholder="no-reply@photographersb.com">
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'payments'" class="panel-body">
            <div class="field-grid">
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.card_enabled" type="checkbox">
                  <span>Enable Card Payments</span>
                </label>
              </div>
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.bkash_enabled" type="checkbox">
                  <span>Enable bKash</span>
                </label>
              </div>
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.nagad_enabled" type="checkbox">
                  <span>Enable Nagad</span>
                </label>
              </div>
              <div v-if="matches('Commission Rate')" class="field">
                <label>Commission Rate (%)</label>
                <input v-model.number="settings.commission_rate" type="number" min="0" max="100" placeholder="15">
              </div>
              <div v-if="matches('Merchant Number')" class="field">
                <label>bKash Merchant Number</label>
                <input v-model="settings.bkash_merchant" type="text" placeholder="01700 000000">
              </div>
              <div v-if="matches('SSL')" class="field">
                <label class="toggle">
                  <input v-model="settings.ssl_enabled" type="checkbox">
                  <span>Enable SSL Commerz</span>
                </label>
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'bookings'" class="panel-body">
            <div class="field-grid">
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.booking_auto_confirm" type="checkbox">
                  <span>Auto-confirm bookings</span>
                </label>
              </div>
              <div class="field">
                <label>Cancellation Window (hours)</label>
                <input v-model.number="settings.booking_cancel_window" type="number" min="0" placeholder="48">
              </div>
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.booking_deposit_required" type="checkbox">
                  <span>Require deposit</span>
                </label>
              </div>
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.review_auto_publish" type="checkbox">
                  <span>Auto-publish reviews</span>
                </label>
              </div>
              <div class="field">
                <label>Minimum Stars to Display</label>
                <input v-model.number="settings.review_min_stars" type="number" min="1" max="5" placeholder="3">
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'security'" class="panel-body">
            <div class="field-grid">
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.two_factor_enabled" type="checkbox">
                  <span>Enable two-factor authentication</span>
                </label>
              </div>
              <div class="field">
                <label>Session Timeout (minutes)</label>
                <input v-model.number="settings.session_timeout" type="number" placeholder="120">
              </div>
              <div class="field">
                <label>Password Minimum Length</label>
                <input v-model.number="settings.password_min_length" type="number" placeholder="8">
              </div>
              <div class="field">
                <label>reCAPTCHA Site Key</label>
                <input v-model="settings.recaptcha_site_key" type="text" placeholder="Site key">
              </div>
              <div class="field">
                <label>reCAPTCHA Secret Key</label>
                <input v-model="settings.recaptcha_secret_key" type="password" placeholder="Secret key">
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'notifications'" class="panel-body">
            <div class="field-grid">
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.email_notifications" type="checkbox">
                  <span>Email notifications</span>
                </label>
              </div>
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.booking_notifications" type="checkbox">
                  <span>Booking notifications</span>
                </label>
              </div>
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.review_notifications" type="checkbox">
                  <span>Review notifications</span>
                </label>
              </div>
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.sms_enabled" type="checkbox">
                  <span>SMS alerts</span>
                </label>
              </div>
              <div class="field">
                <label>Admin Alerts Email</label>
                <input v-model="settings.admin_alerts_email" type="email" placeholder="alerts@photographersb.com">
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'seo'" class="panel-body">
            <div class="field-grid">
              <div class="field">
                <label>Default Site Title</label>
                <input v-model="settings.seo_site_title" type="text" placeholder="Photographer SB">
              </div>
              <div class="field field--full">
                <label>Default Description</label>
                <textarea v-model="settings.seo_site_description" rows="2" placeholder="Professional photography marketplace" />
              </div>
              <div class="field">
                <label>Robots</label>
                <input v-model="settings.seo_robots" type="text" placeholder="index,follow">
              </div>
              <div class="field">
                <label>Default OG Title</label>
                <input v-model="settings.seo_og_title" type="text" placeholder="Photographer SB">
              </div>
              <div class="field field--full">
                <label>Default OG Description</label>
                <textarea v-model="settings.seo_og_description" rows="2" placeholder="Share preview summary" />
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'tracking'" class="panel-body">
            <div class="field-grid">
              <div class="field">
                <label class="toggle">
                  <input v-model="settings.tracking_enabled" type="checkbox">
                  <span>Enable Analytics & Pixels</span>
                </label>
              </div>
              <div class="field">
                <label>GA4 Measurement ID</label>
                <input v-model="settings.ga4_measurement_id" type="text" placeholder="G-XXXXXXXXXX">
              </div>
              <div class="field">
                <label>Google Tag Manager ID</label>
                <input v-model="settings.gtm_id" type="text" placeholder="GTM-XXXXXXX">
              </div>
              <div class="field">
                <label>Meta Pixel ID</label>
                <input v-model="settings.fb_pixel_id" type="text" placeholder="1234567890">
              </div>
              <div class="field field--full">
                <label>Google Search Console Verification</label>
                <input v-model="settings.gsc_verification" type="text" placeholder="verification_token">
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'system'" class="panel-body">
            <div class="field-grid">
              <div class="field">
                <label>Maintenance Mode</label>
                <select v-model="settings.maintenance_mode">
                  <option value="0">Disabled</option>
                  <option value="1">Enabled</option>
                </select>
              </div>
              <div class="field field--full">
                <label>Maintenance Message</label>
                <textarea v-model="settings.maintenance_message" rows="2" placeholder="We are updating the platform." />
              </div>
              <div class="field">
                <label>Debug Mode</label>
                <select v-model="settings.debug_mode">
                  <option value="0">Disabled</option>
                  <option value="1">Enabled</option>
                </select>
              </div>
              <div class="field">
                <label>Cache Duration (minutes)</label>
                <input v-model.number="settings.cache_duration" type="number" placeholder="60">
              </div>
            </div>
          </div>

          <div v-if="activeSection === 'storage'" class="panel-body">
            <div class="field-grid">
              <div class="field">
                <label>Storage Driver</label>
                <select v-model="settings.storage_driver">
                  <option value="local">Local</option>
                  <option value="s3">S3</option>
                </select>
              </div>
              <div class="field">
                <label>S3 Bucket</label>
                <input v-model="settings.s3_bucket" type="text" placeholder="bucket-name">
              </div>
              <div class="field">
                <label>S3 Region</label>
                <input v-model="settings.s3_region" type="text" placeholder="ap-south-1">
              </div>
              <div class="field">
                <label>S3 Base URL</label>
                <input v-model="settings.s3_url" type="text" placeholder="https://...">
              </div>
              <div class="field">
                <label>Max Upload (MB)</label>
                <input v-model.number="settings.max_upload_mb" type="number" placeholder="20">
              </div>
              <div class="field">
                <label>Image Quality (%)</label>
                <input v-model.number="settings.image_quality" type="number" min="40" max="100" placeholder="80">
              </div>
            </div>
          </div>
        </section>
      </div>

      <div v-if="showToast" class="toast" :class="toastType === 'error' ? 'toast--error' : 'toast--success'">
        {{ toastMessage }}
      </div>

      <PexelsPickerModal
        :visible="pexelsPickerOpen"
        :target-width="pexelsTarget.width"
        :target-height="pexelsTarget.height"
        @close="closePexelsPicker"
        @select="handlePexelsSelect"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import PexelsPickerModal from '../../../components/PexelsPickerModal.vue'
import api from '../../../api'
import { validateUploadFile } from '../../../utils/imageValidation'

const router = useRouter()
const activeSection = ref('general')
const saving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
const searchQuery = ref('')
const initialSnapshot = ref('')
const uploadingImages = ref({
  brand_logo: false,
  brand_favicon: false,
  brand_og_image: false,
})

const pexelsPickerOpen = ref(false)
const pexelsTarget = ref({
  field: 'brand_logo',
  width: 600,
  height: 300,
})

const sections = [
  { id: 'general', name: 'General', note: 'Identity and region', description: 'Platform name, contact, and localization.' },
  { id: 'branding', name: 'Branding', note: 'Logo and colors', description: 'Assets and visual identity defaults.' },
  { id: 'email', name: 'Email', note: 'SMTP and sender', description: 'Mail server and sender configuration.' },
  { id: 'payments', name: 'Payments', note: 'Gateways and fees', description: 'Payment switches and commission controls.' },
  { id: 'bookings', name: 'Bookings & Reviews', note: 'Operations rules', description: 'Booking workflow and review rules.' },
  { id: 'security', name: 'Security', note: 'Access control', description: 'Auth, session, and captcha settings.' },
  { id: 'notifications', name: 'Notifications', note: 'Alerts and channels', description: 'Email, SMS, and admin alerts.' },
  { id: 'seo', name: 'SEO', note: 'Metadata defaults', description: 'Site title, description, and robots.' },
  { id: 'tracking', name: 'Tracking', note: 'Pixels and analytics', description: 'GA4, Meta Pixel, and Search Console.' },
  { id: 'system', name: 'System', note: 'Runtime mode', description: 'Maintenance, debug, and cache.' },
  { id: 'storage', name: 'Storage', note: 'Files and media', description: 'Storage driver and upload limits.' }
]

const filteredSections = computed(() => {
  const term = (searchQuery.value || '').toLowerCase()
  if (!term) return sections
  return sections.filter(section => (
    section.name.toLowerCase().includes(term) ||
    section.note.toLowerCase().includes(term) ||
    section.description.toLowerCase().includes(term)
  ))
})

const activeSectionMeta = computed(() => {
  return sections.find(section => section.id === activeSection.value) || sections[0]
})

const matches = (label) => {
  const term = (searchQuery.value || '').trim().toLowerCase()
  if (!term) return true
  return label.toLowerCase().includes(term)
}

const settings = ref({
  platform_name: 'Photographer SB',
  platform_email: 'admin@photographersb.com',
  support_email: 'support@photographersb.com',
  platform_phone: '',
  platform_address: '',
  currency: 'BDT',
  timezone: 'Asia/Dhaka',
  date_format: 'd-m-Y',
  brand_logo: '',
  brand_logo_credit_name: '',
  brand_logo_credit_url: '',
  brand_favicon: '',
  brand_favicon_credit_name: '',
  brand_favicon_credit_url: '',
  brand_og_image: '',
  brand_og_image_credit_name: '',
  brand_og_image_credit_url: '',
  brand_tagline: '',
  brand_primary_color: '#8E0E3F',
  brand_secondary_color: '#1F2937',
  smtp_host: '',
  smtp_port: 587,
  smtp_username: '',
  smtp_password: '',
  mail_from_name: 'Photographer SB',
  mail_from_address: '',
  card_enabled: true,
  bkash_enabled: true,
  bkash_merchant: '',
  nagad_enabled: true,
  ssl_enabled: false,
  commission_rate: 15,
  booking_auto_confirm: false,
  booking_cancel_window: 48,
  booking_deposit_required: false,
  review_auto_publish: true,
  review_min_stars: 3,
  two_factor_enabled: false,
  session_timeout: 120,
  password_min_length: 8,
  recaptcha_site_key: '',
  recaptcha_secret_key: '',
  email_notifications: true,
  booking_notifications: true,
  review_notifications: true,
  sms_enabled: false,
  admin_alerts_email: '',
  seo_site_title: 'Photographer SB',
  seo_site_description: '',
  seo_robots: 'index,follow',
  seo_og_title: '',
  seo_og_description: '',
  tracking_enabled: true,
  ga4_measurement_id: '',
  gtm_id: '',
  fb_pixel_id: '',
  gsc_verification: '',
  maintenance_mode: '0',
  maintenance_message: '',
  debug_mode: '0',
  cache_duration: 60,
  storage_driver: 'local',
  s3_bucket: '',
  s3_region: '',
  s3_url: '',
  max_upload_mb: 20,
  image_quality: 80
})

const settingsKeyMap = {
  platform_name: 'site.name',
  platform_email: 'site.email',
  support_email: 'site.support_email',
  platform_phone: 'site.phone',
  platform_address: 'site.address',
  currency: 'site.currency',
  timezone: 'site.timezone',
  date_format: 'site.date_format',
  brand_logo: 'branding.logo_url',
  brand_logo_credit_name: 'branding.logo_credit_name',
  brand_logo_credit_url: 'branding.logo_credit_url',
  brand_favicon: 'branding.favicon_url',
  brand_favicon_credit_name: 'branding.favicon_credit_name',
  brand_favicon_credit_url: 'branding.favicon_credit_url',
  brand_og_image: 'branding.og_image',
  brand_og_image_credit_name: 'branding.og_image_credit_name',
  brand_og_image_credit_url: 'branding.og_image_credit_url',
  brand_tagline: 'branding.tagline',
  brand_primary_color: 'branding.primary_color',
  brand_secondary_color: 'branding.secondary_color',
  smtp_host: 'email.smtp_host',
  smtp_port: 'email.smtp_port',
  smtp_username: 'email.smtp_username',
  smtp_password: 'email.smtp_password',
  mail_from_name: 'email.mail_from_name',
  mail_from_address: 'email.mail_from_address',
  card_enabled: 'payment.card_enabled',
  bkash_enabled: 'payment.bkash_enabled',
  bkash_merchant: 'payment.bkash_merchant',
  nagad_enabled: 'payment.nagad_enabled',
  ssl_enabled: 'payment.ssl_enabled',
  commission_rate: 'payment.commission_rate',
  booking_auto_confirm: 'booking.auto_confirm',
  booking_cancel_window: 'booking.cancellation_window_hours',
  booking_deposit_required: 'booking.deposit_required',
  review_auto_publish: 'review.auto_publish',
  review_min_stars: 'review.min_stars_to_display',
  two_factor_enabled: 'security.two_factor_enabled',
  session_timeout: 'security.session_timeout',
  password_min_length: 'security.password_min_length',
  recaptcha_site_key: 'security.recaptcha_site_key',
  recaptcha_secret_key: 'security.recaptcha_secret_key',
  email_notifications: 'notification.email_notifications',
  booking_notifications: 'notification.booking_notifications',
  review_notifications: 'notification.review_notifications',
  sms_enabled: 'notification.sms_enabled',
  admin_alerts_email: 'notification.admin_alerts_email',
  seo_site_title: 'seo.site_title',
  seo_site_description: 'seo.site_description',
  seo_robots: 'seo.robots',
  seo_og_title: 'seo.og_title',
  seo_og_description: 'seo.og_description',
  tracking_enabled: 'tracking.enable',
  ga4_measurement_id: 'tracking.ga4_measurement_id',
  gtm_id: 'tracking.gtm_id',
  fb_pixel_id: 'tracking.fb_pixel_id',
  gsc_verification: 'tracking.gsc_verification',
  maintenance_mode: 'system.maintenance_mode',
  maintenance_message: 'system.maintenance_message',
  debug_mode: 'system.debug_mode',
  cache_duration: 'system.cache_duration',
  storage_driver: 'storage.driver',
  s3_bucket: 'storage.s3_bucket',
  s3_region: 'storage.s3_region',
  s3_url: 'storage.s3_url',
  max_upload_mb: 'media.max_upload_mb',
  image_quality: 'media.image_quality'
}

const parseBoolean = (value) => value === true || value === 'true' || value === '1' || value === 1
const parseNumber = (value, fallback) => {
  const parsed = Number(value)
  return Number.isFinite(parsed) ? parsed : fallback
}

const applySettingsFromApi = (data) => {
  settings.value = {
    ...settings.value,
    platform_name: data['site.name'] || settings.value.platform_name,
    platform_email: data['site.email'] || settings.value.platform_email,
    support_email: data['site.support_email'] || settings.value.support_email,
    platform_phone: data['site.phone'] || settings.value.platform_phone,
    platform_address: data['site.address'] || settings.value.platform_address,
    currency: data['site.currency'] || settings.value.currency,
    timezone: data['site.timezone'] || settings.value.timezone,
    date_format: data['site.date_format'] || settings.value.date_format,
    brand_logo: data['branding.logo_url'] || settings.value.brand_logo,
    brand_logo_credit_name: data['branding.logo_credit_name'] || settings.value.brand_logo_credit_name,
    brand_logo_credit_url: data['branding.logo_credit_url'] || settings.value.brand_logo_credit_url,
    brand_favicon: data['branding.favicon_url'] || settings.value.brand_favicon,
    brand_favicon_credit_name: data['branding.favicon_credit_name'] || settings.value.brand_favicon_credit_name,
    brand_favicon_credit_url: data['branding.favicon_credit_url'] || settings.value.brand_favicon_credit_url,
    brand_og_image: data['branding.og_image'] || settings.value.brand_og_image,
    brand_og_image_credit_name: data['branding.og_image_credit_name'] || settings.value.brand_og_image_credit_name,
    brand_og_image_credit_url: data['branding.og_image_credit_url'] || settings.value.brand_og_image_credit_url,
    brand_tagline: data['branding.tagline'] || settings.value.brand_tagline,
    brand_primary_color: data['branding.primary_color'] || settings.value.brand_primary_color,
    brand_secondary_color: data['branding.secondary_color'] || settings.value.brand_secondary_color,
    smtp_host: data['email.smtp_host'] || settings.value.smtp_host,
    smtp_port: parseNumber(data['email.smtp_port'], settings.value.smtp_port),
    smtp_username: data['email.smtp_username'] || settings.value.smtp_username,
    smtp_password: data['email.smtp_password'] || settings.value.smtp_password,
    mail_from_name: data['email.mail_from_name'] || settings.value.mail_from_name,
    mail_from_address: data['email.mail_from_address'] || settings.value.mail_from_address,
    card_enabled: parseBoolean(data['payment.card_enabled'] ?? settings.value.card_enabled),
    bkash_enabled: parseBoolean(data['payment.bkash_enabled'] ?? settings.value.bkash_enabled),
    bkash_merchant: data['payment.bkash_merchant'] || settings.value.bkash_merchant,
    nagad_enabled: parseBoolean(data['payment.nagad_enabled'] ?? settings.value.nagad_enabled),
    ssl_enabled: parseBoolean(data['payment.ssl_enabled'] ?? settings.value.ssl_enabled),
    commission_rate: parseNumber(data['payment.commission_rate'], settings.value.commission_rate),
    booking_auto_confirm: parseBoolean(data['booking.auto_confirm'] ?? settings.value.booking_auto_confirm),
    booking_cancel_window: parseNumber(data['booking.cancellation_window_hours'], settings.value.booking_cancel_window),
    booking_deposit_required: parseBoolean(data['booking.deposit_required'] ?? settings.value.booking_deposit_required),
    review_auto_publish: parseBoolean(data['review.auto_publish'] ?? settings.value.review_auto_publish),
    review_min_stars: parseNumber(data['review.min_stars_to_display'], settings.value.review_min_stars),
    two_factor_enabled: parseBoolean(data['security.two_factor_enabled'] ?? settings.value.two_factor_enabled),
    session_timeout: parseNumber(data['security.session_timeout'], settings.value.session_timeout),
    password_min_length: parseNumber(data['security.password_min_length'], settings.value.password_min_length),
    recaptcha_site_key: data['security.recaptcha_site_key'] || settings.value.recaptcha_site_key,
    recaptcha_secret_key: data['security.recaptcha_secret_key'] || settings.value.recaptcha_secret_key,
    email_notifications: parseBoolean(data['notification.email_notifications'] ?? settings.value.email_notifications),
    booking_notifications: parseBoolean(data['notification.booking_notifications'] ?? settings.value.booking_notifications),
    review_notifications: parseBoolean(data['notification.review_notifications'] ?? settings.value.review_notifications),
    sms_enabled: parseBoolean(data['notification.sms_enabled'] ?? settings.value.sms_enabled),
    admin_alerts_email: data['notification.admin_alerts_email'] || settings.value.admin_alerts_email,
    seo_site_title: data['seo.site_title'] || settings.value.seo_site_title,
    seo_site_description: data['seo.site_description'] || settings.value.seo_site_description,
    seo_robots: data['seo.robots'] || settings.value.seo_robots,
    seo_og_title: data['seo.og_title'] || settings.value.seo_og_title,
    seo_og_description: data['seo.og_description'] || settings.value.seo_og_description,
    tracking_enabled: parseBoolean(data['tracking.enable'] ?? settings.value.tracking_enabled),
    ga4_measurement_id: data['tracking.ga4_measurement_id'] || settings.value.ga4_measurement_id,
    gtm_id: data['tracking.gtm_id'] || settings.value.gtm_id,
    fb_pixel_id: data['tracking.fb_pixel_id'] || settings.value.fb_pixel_id,
    gsc_verification: data['tracking.gsc_verification'] || settings.value.gsc_verification,
    maintenance_mode: data['system.maintenance_mode'] ?? settings.value.maintenance_mode,
    maintenance_message: data['system.maintenance_message'] || settings.value.maintenance_message,
    debug_mode: data['system.debug_mode'] ?? settings.value.debug_mode,
    cache_duration: parseNumber(data['system.cache_duration'], settings.value.cache_duration),
    storage_driver: data['storage.driver'] || settings.value.storage_driver,
    s3_bucket: data['storage.s3_bucket'] || settings.value.s3_bucket,
    s3_region: data['storage.s3_region'] || settings.value.s3_region,
    s3_url: data['storage.s3_url'] || settings.value.s3_url,
    max_upload_mb: parseNumber(data['media.max_upload_mb'], settings.value.max_upload_mb),
    image_quality: parseNumber(data['media.image_quality'], settings.value.image_quality)
  }
}

const snapshotSettings = () => {
  initialSnapshot.value = JSON.stringify(settings.value)
}

const isDirty = computed(() => JSON.stringify(settings.value) !== initialSnapshot.value)

const showToastMessage = (message, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 2500)
}

const handleImageUpload = async (field, event) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (field === 'brand_logo') {
    settings.value.brand_logo_credit_name = ''
    settings.value.brand_logo_credit_url = ''
  }
  if (field === 'brand_favicon') {
    settings.value.brand_favicon_credit_name = ''
    settings.value.brand_favicon_credit_url = ''
  }
  if (field === 'brand_og_image') {
    settings.value.brand_og_image_credit_name = ''
    settings.value.brand_og_image_credit_url = ''
  }

  const rules = {
    brand_logo: { width: 600, height: 300 },
    brand_favicon: { width: 512, height: 512 },
    brand_og_image: { width: 1200, height: 630 }
  }
  const rule = rules[field] || {}
  const validation = await validateUploadFile(file, {
    label: 'Image',
    maxBytes: 5 * 1024 * 1024,
    allowedTypes: ['image/jpeg', 'image/png'],
    imageWidth: rule.width,
    imageHeight: rule.height
  })

  if (!validation.ok) {
    showToastMessage(validation.message, 'error')
    event.target.value = ''
    return
  }

  uploadingImages.value[field] = true
  try {
    const formData = new FormData()
    formData.append('image', file)
    formData.append('folder', 'branding')

    const response = await api.post('/admin/media/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (response.data?.status === 'success' && response.data.data?.url) {
      settings.value[field] = response.data.data.url
    } else {
      showToastMessage(response.data?.message || 'Image upload failed.', 'error')
    }
  } catch (error) {
    showToastMessage(error.response?.data?.message || 'Image upload failed.', 'error')
  } finally {
    uploadingImages.value[field] = false
    event.target.value = ''
  }
}

const openPexelsPicker = (field, width, height) => {
  pexelsTarget.value = { field, width, height }
  pexelsPickerOpen.value = true
}

const closePexelsPicker = () => {
  pexelsPickerOpen.value = false
}

const applyPexelsCredit = (field, credit) => {
  if (field === 'brand_logo') {
    settings.value.brand_logo_credit_name = credit?.name || ''
    settings.value.brand_logo_credit_url = credit?.url || ''
  }
  if (field === 'brand_favicon') {
    settings.value.brand_favicon_credit_name = credit?.name || ''
    settings.value.brand_favicon_credit_url = credit?.url || ''
  }
  if (field === 'brand_og_image') {
    settings.value.brand_og_image_credit_name = credit?.name || ''
    settings.value.brand_og_image_credit_url = credit?.url || ''
  }
}

const handlePexelsSelect = async ({ file, credit }) => {
  const field = pexelsTarget.value.field
  uploadingImages.value[field] = true
  try {
    const formData = new FormData()
    formData.append('image', file)
    formData.append('folder', 'branding')

    const response = await api.post('/admin/media/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (response.data?.status === 'success' && response.data.data?.url) {
      settings.value[field] = response.data.data.url
      applyPexelsCredit(field, credit)
    } else {
      showToastMessage(response.data?.message || 'Image upload failed.', 'error')
    }
  } catch (error) {
    showToastMessage(error.response?.data?.message || 'Image upload failed.', 'error')
  } finally {
    uploadingImages.value[field] = false
    closePexelsPicker()
  }
}

const loadSettings = async () => {
  try {
    const { data } = await api.get('/admin/settings')
    applySettingsFromApi(data.data || {})
    snapshotSettings()
  } catch (error) {
    showToastMessage('Failed to load settings', 'error')
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    const payload = Object.entries(settingsKeyMap).map(([localKey, remoteKey]) => {
      let value = settings.value[localKey]
      if (typeof value === 'boolean') {
        value = value ? 'true' : 'false'
      }
      return { key: remoteKey, value: String(value ?? '') }
    })

    await api.post('/admin/settings/bulk', { settings: payload })
    snapshotSettings()
    showToastMessage('Settings saved successfully', 'success')
  } catch (error) {
    showToastMessage('Failed to save settings', 'error')
  } finally {
    saving.value = false
  }
}

const resetToDefaults = async () => {
  if (!window.confirm('Reset all settings to defaults?')) return
  try {
    saving.value = true
    await api.post('/admin/settings/reset')
    await loadSettings()
    showToastMessage('Settings reset to defaults', 'success')
  } catch (error) {
    showToastMessage('Failed to reset settings', 'error')
  } finally {
    saving.value = false
  }
}

const goToChangeTracking = () => {
  router.push('/admin/settings/changes')
}

const goToSiteLinks = () => {
  router.push('/admin/settings/site-links')
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
.settings-page {
  --settings-ink: #2c1a16;
  --settings-muted: #7b6a5f;
  --settings-accent: #8e0e3f;
  --settings-accent-strong: #6e0c2c;
  --settings-border: rgba(140, 108, 95, 0.25);
  --settings-glow: rgba(142, 14, 63, 0.12);
  --settings-grid: rgba(140, 108, 95, 0.12);
  min-height: 100vh;
  background:
    repeating-linear-gradient(90deg, transparent 0 54px, var(--settings-grid) 54px 55px),
    repeating-linear-gradient(0deg, transparent 0 54px, var(--settings-grid) 54px 55px),
    radial-gradient(circle at 12% 18%, rgba(233, 207, 191, 0.65), transparent 40%),
    radial-gradient(circle at 88% 12%, rgba(233, 199, 214, 0.55), transparent 35%),
    linear-gradient(135deg, #f9f4ef 0%, #f3ece6 52%, #f8f2ef 100%);
  font-family: "Space Grotesk", "DM Sans", sans-serif;
  color: var(--settings-ink);
  position: relative;
  overflow: hidden;
}

.settings-page::before,
.settings-page::after {
  content: "";
  position: absolute;
  inset: -10% 8% auto auto;
  width: 360px;
  height: 360px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(142, 14, 63, 0.18), transparent 60%);
  filter: blur(0.2px);
  pointer-events: none;
  animation: orbitGlow 16s linear infinite;
}

.settings-page::after {
  inset: auto auto -12% -8%;
  width: 440px;
  height: 440px;
  background: radial-gradient(circle, rgba(108, 68, 52, 0.14), transparent 65%);
  animation: orbitGlow 20s linear infinite reverse;
}

.settings-shell {
  max-width: 1440px;
  margin: 0 auto;
  padding: 1.5rem 1.5rem 4rem;
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  position: relative;
  z-index: 1;
}

.settings-hero {
  display: grid;
  grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr);
  gap: 1.5rem;
  padding: 1.75rem 2rem;
  border-radius: 1.5rem;
  border: 1px solid rgba(142, 14, 63, 0.2);
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.92), rgba(247, 239, 233, 0.82)),
    linear-gradient(90deg, rgba(142, 14, 63, 0.06), transparent 45%, rgba(109, 72, 56, 0.08));
  box-shadow: 0 25px 55px rgba(24, 12, 8, 0.1), inset 0 0 0 1px rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(6px);
  animation: heroReveal 0.6s ease-out;
  position: relative;
  overflow: hidden;
}

.settings-hero::after {
  content: "";
  position: absolute;
  inset: auto 0 0 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, rgba(142, 14, 63, 0.6), transparent);
  animation: scanLine 6s ease-in-out infinite;
}

.hero-copy {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.hero-kicker {
  font-size: 0.7rem;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: var(--settings-muted);
  font-weight: 700;
}

.hero-title {
  font-size: 2.2rem;
  line-height: 1.1;
  color: var(--settings-ink);
  text-shadow: 0 2px 14px rgba(142, 14, 63, 0.18);
}

.hero-subtitle {
  color: var(--settings-muted);
  max-width: 480px;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.hero-status {
  display: grid;
  gap: 0.8rem;
}

.status-card {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(142, 14, 63, 0.2);
  border-radius: 1rem;
  padding: 1rem 1.25rem;
  box-shadow: 0 16px 35px rgba(22, 12, 8, 0.08);
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.status-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  color: var(--settings-muted);
}

.status-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--settings-ink);
}

.status-ok {
  color: #1f6a49;
}

.status-warn {
  color: #9a5b00;
}

.settings-topbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid var(--settings-border);
  border-radius: 1.1rem;
  box-shadow: 0 18px 35px rgba(18, 9, 6, 0.08);
  backdrop-filter: blur(8px);
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.search-box {
  min-width: 220px;
}

.search-input {
  width: 100%;
  border: 1px solid rgba(142, 14, 63, 0.18);
  border-radius: 0.75rem;
  padding: 0.6rem 0.9rem;
  background: rgba(255, 255, 255, 0.7);
  font-size: 0.95rem;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.6);
}

.status-chip {
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-chip--ok {
  background: #e6f2ea;
  color: #1d5c3b;
}

.status-chip--warn {
  background: #fff1d8;
  color: #9a5b00;
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.btn {
  border-radius: 0.8rem;
  padding: 0.55rem 1rem;
  font-weight: 600;
  border: 1px solid transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-ghost {
  background: #f6f2ee;
  color: #5d4a42;
  border-color: #eadfd6;
}

.btn-outline {
  background: white;
  border-color: #dbcfc5;
  color: #6b4a40;
}

.btn-primary {
  background: linear-gradient(135deg, var(--settings-accent), var(--settings-accent-strong));
  color: white;
  box-shadow: 0 8px 18px rgba(142, 14, 63, 0.25);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.settings-grid {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 1.5rem;
}

.settings-rail {
  background: rgba(255, 255, 255, 0.85);
  border-radius: 1.2rem;
  padding: 1rem;
  border: 1px solid rgba(142, 14, 63, 0.18);
  box-shadow: 0 24px 50px rgba(20, 12, 8, 0.1);
  position: sticky;
  top: 1.5rem;
  height: fit-content;
  backdrop-filter: blur(6px);
}

.rail-title {
  text-transform: uppercase;
  font-size: 0.72rem;
  letter-spacing: 0.16em;
  color: #9b8b80;
  margin-bottom: 1rem;
}

.rail-item {
  width: 100%;
  text-align: left;
  border-radius: 0.85rem;
  padding: 0.7rem 0.85rem;
  margin-bottom: 0.6rem;
  border: 1px solid transparent;
  background: rgba(249, 244, 239, 0.8);
  transition: all 0.2s ease;
}

.rail-item.active {
  background: rgba(255, 242, 235, 0.9);
  border-color: rgba(142, 14, 63, 0.2);
  box-shadow: 0 8px 18px var(--settings-glow);
}

.rail-name {
  display: block;
  font-weight: 600;
  color: #3f2a24;
}

.rail-note {
  display: block;
  font-size: 0.75rem;
  color: #9b8b80;
  margin-top: 0.15rem;
}

.settings-panel {
  background: rgba(255, 255, 255, 0.92);
  border-radius: 1.4rem;
  padding: 1.5rem;
  border: 1px solid rgba(142, 14, 63, 0.18);
  box-shadow: 0 28px 55px rgba(18, 9, 6, 0.1);
  backdrop-filter: blur(6px);
  animation: panelFade 0.4s ease-out;
}

.panel-header {
  border-bottom: 1px solid #efe8e1;
  padding-bottom: 1rem;
  margin-bottom: 1.5rem;
}

.panel-title {
  font-size: 1.6rem;
  color: #2f1d18;
}

.panel-subtitle {
  color: #7c6d62;
  margin-top: 0.25rem;
}

.field-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem 1.5rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.field--full {
  grid-column: span 2;
}

.field label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #4a3129;
}

.field input,
.field select,
.field textarea {
  border: 1px solid rgba(142, 14, 63, 0.22);
  border-radius: 0.8rem;
  padding: 0.6rem 0.8rem;
  font-size: 0.95rem;
  background: rgba(255, 255, 255, 0.8);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.5);
}

.toggle {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  font-weight: 600;
  color: #4a3129;
}

.toast {
  position: fixed;
  bottom: 1.5rem;
  right: 1.5rem;
  padding: 0.85rem 1.2rem;
  border-radius: 0.8rem;
  color: white;
  font-weight: 600;
  box-shadow: 0 10px 30px rgba(20, 20, 20, 0.2);
}

.toast--success {
  background: linear-gradient(135deg, #1d6b4f, #139164);
}

.toast--error {
  background: linear-gradient(135deg, #9e2b2b, #c13a3a);
}

@keyframes scanLine {
  0% {
    transform: translateX(-40%);
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    transform: translateX(40%);
    opacity: 0;
  }
}

@keyframes heroReveal {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes panelFade {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes orbitGlow {
  from {
    transform: translate3d(0, 0, 0) scale(1);
  }
  50% {
    transform: translate3d(12px, 8px, 0) scale(1.03);
  }
  to {
    transform: translate3d(0, 0, 0) scale(1);
  }
}

@media (max-width: 1024px) {
  .settings-hero {
    grid-template-columns: 1fr;
  }

  .settings-grid {
    grid-template-columns: 1fr;
  }

  .settings-rail {
    position: static;
  }
}

@media (max-width: 768px) {
  .field-grid {
    grid-template-columns: 1fr;
  }

  .field--full {
    grid-column: span 1;
  }
}

/* Admin header + quick nav overrides for this page */
:deep(.admin-header) {
  background: rgba(255, 255, 255, 0.82);
  border-bottom: 1px solid rgba(142, 14, 63, 0.2);
  box-shadow: 0 18px 40px rgba(18, 9, 6, 0.08);
  backdrop-filter: blur(10px);
}

:deep(.admin-header__bg) {
  background: linear-gradient(135deg, rgba(142, 14, 63, 0.12), transparent 60%);
  opacity: 0.8;
}

:deep(.admin-page-title) {
  color: var(--settings-ink);
  letter-spacing: 0.04em;
}

:deep(.admin-icon-btn),
:deep(.btn-admin-secondary) {
  background: rgba(255, 255, 255, 0.8);
  border-color: rgba(142, 14, 63, 0.2);
  color: var(--settings-ink);
}

:deep(.admin-quicknav-bar) {
  background: rgba(255, 255, 255, 0.86);
  border: 1px solid rgba(142, 14, 63, 0.2);
  box-shadow: 0 20px 40px rgba(18, 9, 6, 0.08);
  backdrop-filter: blur(8px);
}

:deep(.admin-nav-link) {
  background: rgba(255, 255, 255, 0.75);
  border-color: rgba(142, 14, 63, 0.18);
  color: var(--settings-muted);
}

:deep(.admin-nav-link:hover) {
  color: var(--settings-accent);
  border-color: rgba(142, 14, 63, 0.35);
}

:deep(.admin-nav-link.router-link-active) {
  background: linear-gradient(135deg, rgba(142, 14, 63, 0.85), rgba(110, 12, 44, 0.85));
  border-color: rgba(142, 14, 63, 0.6);
}
</style>
