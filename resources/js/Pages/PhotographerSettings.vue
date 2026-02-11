<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-rose-50 py-10 sm:py-12 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <p class="text-xs uppercase tracking-[0.3em] text-burgundy/70 mb-2">
          Photographer Settings
        </p>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
          Settings
        </h1>
        <p class="text-gray-600 max-w-2xl">
          Manage your photographer profile and preferences
        </p>
      </div>

      <!-- Settings Tabs -->
      <div class="flex flex-wrap gap-2 mb-6 sm:mb-8 bg-white/80 border border-gray-200/80 rounded-xl p-2 shadow-sm">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          :class="[
            'px-4 sm:px-5 py-2.5 text-sm sm:text-base font-semibold transition-all whitespace-nowrap rounded-lg',
            activeTab === tab.id
              ? 'text-burgundy bg-burgundy/10'
              : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
          ]"
          @click="activeTab = tab.id"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Settings Content -->
      <div class="bg-white/90 backdrop-blur rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
        <div
          v-if="successMessage"
          class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl"
        >
          ✓ {{ successMessage }}
        </div>
        <div
          v-if="errorMessage"
          class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl"
        >
          {{ errorMessage }}
        </div>
        <!-- Profile Settings Tab -->
        <div
          v-if="activeTab === 'profile'"
          class="space-y-6"
        >
          <div>
            <h2 class="text-2xl font-bold text-gray-900">
              Profile Information
            </h2>
            <p class="text-sm text-gray-600 mt-1">
              Make it easy for clients to understand your style and service area.
            </p>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
            <input
              v-model="form.username"
              type="text"
              placeholder="e.g. tanzim.photo"
              class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
            >
            <p class="text-xs text-gray-500 mt-1">
              This updates your public profile URL: /@username
            </p>
          </div>

          <!-- Bio -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Professional Bio</label>
            <textarea
              v-model="form.bio"
              placeholder="Tell clients about your photography style and experience..."
              rows="4"
              class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
            />
            <p class="text-xs text-gray-500 mt-1">
              {{ form.bio.length }}/500 characters
            </p>
          </div>

          <!-- Short Bio (Hero Section) -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Short Bio (Hero Section)</label>
            <textarea
              v-model="form.short_bio"
              placeholder="Brief summary shown in header (auto-truncates long bio if empty)..."
              rows="2"
              maxlength="200"
              class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
            />
            <p class="text-xs text-gray-500 mt-1">
              {{ form.short_bio.length }}/200 characters
            </p>
          </div>

          <!-- Profile Picture Upload -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Profile Picture</label>
            <div class="space-y-3">
              <!-- Image Preview -->
              <div
                v-if="profilePicturePreview || form.profile_picture"
                class="relative w-24 h-24 rounded-lg overflow-hidden border border-gray-200 bg-gray-50"
              >
                <img
                  :src="profilePicturePreview || profilePictureSrc"
                  alt="Profile preview"
                  class="w-full h-full object-cover"
                >
                <button
                  type="button"
                  class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                  @click="removeProfilePicture"
                >
                  ✕
                </button>
              </div>

              <!-- File Input -->
              <div class="relative">
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="onProfilePictureChange"
                >
                <button
                  type="button"
                  class="w-full px-4 py-2.5 text-sm font-medium text-burgundy bg-burgundy/10 border border-burgundy/30 rounded-lg hover:bg-burgundy/20 transition-colors"
                  @click="$refs.fileInput.click()"
                >
                  📷 Choose Image
                </button>
              </div>
              <p class="text-xs text-gray-500">
                JPG, PNG or WebP. Max 5MB.
              </p>
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <!-- Location -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Service Location</label>
              <select
                v-model="form.location"
                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
                <option value="">Select location</option>
                <option
                  v-for="location in locations"
                  :key="location.id || location.name"
                  :value="location.name"
                >
                  {{ location.name }}
                </option>
              </select>
            </div>

            <!-- City -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
              <select
                v-model="form.city_id"
                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
                <option :value="null">
                  Select city
                </option>
                <option
                  v-for="location in locations"
                  :key="location.id || location.name"
                  :value="location.id"
                >
                  {{ location.name }}
                </option>
              </select>
            </div>

            <!-- Experience -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Years of Experience</label>
              <input
                v-model.number="form.experience_years"
                type="number"
                min="0"
                max="60"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
            </div>
          </div>

          <!-- Categories -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Categories</label>
            <p class="text-sm text-gray-600 mb-3">
              Select the categories you shoot most often
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <label
                v-for="category in categories"
                :key="category.id || category.name"
                class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 transition-colors hover:border-burgundy/50"
              >
                <input
                  v-model="form.category_ids"
                  type="checkbox"
                  :value="category.id"
                  class="w-4 h-4 rounded border-gray-300"
                >
                <span>{{ category.name }}</span>
              </label>
            </div>
          </div>

          <!-- Specializations -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Specializations</label>
            <input
              v-model="form.specializations"
              type="text"
              placeholder="Portrait, Wedding, Fashion"
              class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
            >
            <p class="text-xs text-gray-500 mt-1">
              Comma-separated
            </p>
          </div>

          <!-- Favorite Hashtags -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Favorite Hashtags</label>
            <input
              v-model="form.favorite_hashtags"
              type="text"
              placeholder="#portrait, #wedding"
              class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
            >
            <p class="text-xs text-gray-500 mt-1">
              Comma-separated
            </p>
          </div>

          <!-- Service Area Radius -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Service Radius (km)</label>
            <input
              v-model.number="form.service_area_radius"
              type="number"
              min="0"
              max="500"
              class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
            >
            <p class="text-xs text-gray-500 mt-1">
              How far are you willing to travel for bookings?
            </p>
          </div>

          <!-- Save Button -->
          <button
            :disabled="savingProfile"
            class="w-full py-3 px-6 bg-burgundy text-white font-bold rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50"
            @click="saveSettings"
          >
            {{ savingProfile ? 'Saving...' : '✓ Save Profile Changes' }}
          </button>
        </div>

        <!-- Tip Settings Tab -->
        <div
          v-if="activeTab === 'tips'"
          class="space-y-6"
        >
          <div>
            <h2 class="text-2xl font-bold text-gray-900">
              ☕ Tip Settings
            </h2>
            <p class="text-sm text-gray-600 mt-1">
              Set up manual tip collection via mobile payment (bKash, Nagad, Rocket).
            </p>
          </div>

          <div class="flex items-center justify-between rounded-xl border border-amber-200/70 bg-amber-50 p-4">
            <div>
              <p class="text-sm font-semibold text-gray-900">Enable tips on your profile</p>
              <p class="text-xs text-gray-600">Turn this on to show the tip panel publicly.</p>
            </div>
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
              <input
                v-model="form.accept_tips"
                type="checkbox"
                class="h-5 w-5 rounded border-gray-300 text-burgundy focus:ring-burgundy/20"
              >
              <span>{{ form.accept_tips ? 'Enabled' : 'Disabled' }}</span>
            </label>
          </div>

          <!-- How It Works Info -->
          <div class="bg-blue-50 border border-blue-200/70 rounded-xl p-4">
            <p class="text-sm font-semibold text-gray-900 mb-2">How Manual Tips Work:</p>
            <ul class="text-xs text-gray-700 space-y-1.5 list-disc list-inside">
              <li>Add your bKash, Nagad, or Rocket numbers below</li>
              <li>Clients see the tip panel and choose a payment method</li>
              <li>They send money using the selected number</li>
              <li>They submit the transaction ID and their phone number</li>
              <li>You keep all the money they send - platform records only transaction data</li>
            </ul>
          </div>

          <!-- Payment Numbers -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">bKash Number</label>
              <div class="relative">
                <span class="absolute left-3 top-3 text-gray-500">💳</span>
                <input
                  v-model="form.bkash_number"
                  type="text"
                  placeholder="+880xxxxxxxxxx"
                  class="w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
                >
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nagad Number</label>
              <div class="relative">
                <span class="absolute left-3 top-3 text-gray-500">📱</span>
                <input
                  v-model="form.nagad_number"
                  type="text"
                  placeholder="+880xxxxxxxxxx"
                  class="w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
                >
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Rocket Number</label>
              <div class="relative">
                <span class="absolute left-3 top-3 text-gray-500">🚀</span>
                <input
                  v-model="form.rocket_number"
                  type="text"
                  placeholder="+880xxxxxxxxxx"
                  class="w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
                >
              </div>
            </div>
            <p class="text-xs text-gray-500">
              Format: +880XXXXXXXXXX (include country code) - Add one or more numbers
            </p>
          </div>

          <!-- Tip Message -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tip Message</label>
            <p class="text-xs text-gray-500 mb-2">
              Optional message shown to clients offering tip option
            </p>
            <textarea
              v-model="form.tip_message"
              placeholder="Your tip helps me keep creating, learning, and improving for you."
              rows="3"
              class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
            />
          </div>

          <!-- Preview Card -->
          <div class="bg-amber-50 border border-amber-200/70 rounded-xl p-4">
            <p class="text-sm font-semibold text-gray-700 mb-3">Preview (Client View):</p>
            <div class="bg-white rounded-lg p-4 space-y-3">
              <div v-if="!form.accept_tips" class="text-xs text-gray-500 text-center py-2">
                Tips are currently disabled for this photographer.
              </div>
              <div v-else class="text-sm text-gray-700">
                <strong>{{ tipPreviewMessage }}</strong>
              </div>
              <div v-if="form.accept_tips && hasAnyTipNumber" class="space-y-3 text-center">
                <p class="text-xs text-gray-600 mb-2">Send tip to:</p>
                <div class="grid gap-2 sm:grid-cols-3">
                  <div v-if="form.bkash_number" class="rounded-lg border border-burgundy/10 bg-burgundy/5 px-3 py-2">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">bKash</p>
                    <p class="font-mono text-sm text-burgundy font-bold">{{ form.bkash_number }}</p>
                  </div>
                  <div v-if="form.nagad_number" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Nagad</p>
                    <p class="font-mono text-sm text-amber-700 font-bold">{{ form.nagad_number }}</p>
                  </div>
                  <div v-if="form.rocket_number" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2">
                    <p class="text-[11px] uppercase tracking-wide text-gray-500">Rocket</p>
                    <p class="font-mono text-sm text-blue-700 font-bold">{{ form.rocket_number }}</p>
                  </div>
                </div>
                <button
                  type="button"
                  disabled
                  class="mt-3 w-full py-2 px-4 bg-burgundy/10 text-burgundy font-semibold rounded-lg cursor-not-allowed"
                >
                  💳 Send Tip (Clients click here)
                </button>
              </div>
              <div v-else-if="form.accept_tips" class="text-xs text-gray-500 text-center py-4">
                Add at least one payment number above to show tip options to clients
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <button
            :disabled="savingTips || (form.accept_tips && !hasAnyTipNumber)"
            class="w-full py-3 px-6 bg-burgundy text-white font-bold rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50"
            @click="saveTipSettings"
          >
            {{ savingTips ? 'Saving...' : '✓ Save Tip Settings' }}
          </button>
        </div>

        <!-- Social Media Tab -->
        <div
          v-if="activeTab === 'social'"
          class="space-y-6"
        >
          <div>
            <h2 class="text-2xl font-bold text-gray-900">
              Social Media Links
            </h2>
            <p class="text-sm text-gray-600 mt-1">
              Help clients connect with you across platforms.
            </p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <!-- Facebook -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">🔵 Facebook</label>
              <input
                v-model="form.facebook_url"
                type="url"
                placeholder="https://facebook.com/yourprofile"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
            </div>

            <!-- Instagram -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">📷 Instagram</label>
              <input
                v-model="form.instagram_url"
                type="url"
                placeholder="https://instagram.com/yourprofile"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
            </div>

            <!-- Twitter -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">🐦 Twitter</label>
              <input
                v-model="form.twitter_url"
                type="url"
                placeholder="https://twitter.com/yourprofile"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
            </div>

            <!-- LinkedIn -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">💼 LinkedIn</label>
              <input
                v-model="form.linkedin_url"
                type="url"
                placeholder="https://linkedin.com/in/yourprofile"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
            </div>

            <!-- YouTube -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">▶️ YouTube</label>
              <input
                v-model="form.youtube_url"
                type="url"
                placeholder="https://youtube.com/yourprofile"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
            </div>

            <!-- Pexels -->
            <div>
              <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                <img
                  src="/images/pexels.webp"
                  alt="Pexels"
                  class="w-4 h-4"
                  loading="lazy"
                >
                Pexels
              </label>
              <input
                v-model="form.pexels_url"
                type="url"
                placeholder="https://www.pexels.com/@yourprofile"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
            </div>

            <!-- Website -->
            <div class="sm:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">🌐 Website</label>
              <input
                v-model="form.website_url"
                type="url"
                placeholder="https://yourwebsite.com"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
            </div>
          </div>

          <!-- Save Button -->
          <button
            :disabled="savingSocial"
            class="w-full py-3 px-6 bg-burgundy text-white font-bold rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50"
            @click="saveSocialSettings"
          >
            {{ savingSocial ? 'Saving...' : '✓ Save Social Links' }}
          </button>
        </div>

        <!-- Availability Tab -->
        <div
          v-if="activeTab === 'availability'"
          class="space-y-6"
        >
          <div>
            <h2 class="text-2xl font-bold text-gray-900">
              Availability & Preferences
            </h2>
            <p class="text-sm text-gray-600 mt-1">
              Set expectations so clients know when you can respond.
            </p>
          </div>

          <!-- Availability Status -->
          <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200/70 rounded-xl">
            <div>
              <p class="font-semibold text-gray-900">
                Currently Available
              </p>
              <p class="text-sm text-gray-600">
                Show that you're open for bookings
              </p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                v-model="form.is_available"
                type="checkbox"
                class="sr-only peer"
              >
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-burgundy rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600" />
            </label>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <!-- Response Time Preference -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Average Response Time</label>
              <select
                v-model="form.response_time_preference"
                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
                <option value="">
                  Select response time
                </option>
                <option value="under_1_hour">
                  Under 1 hour
                </option>
                <option value="1_to_3_hours">
                  1-3 hours
                </option>
                <option value="3_to_24_hours">
                  3-24 hours
                </option>
                <option value="over_24_hours">
                  Over 24 hours
                </option>
              </select>
            </div>

            <!-- Booking Lead Time -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Booking Lead Time (days)</label>
              <input
                v-model.number="form.booking_lead_time"
                type="number"
                min="0"
                max="365"
                placeholder="e.g., 7"
                class="w-full px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-burgundy/10 focus:border-burgundy"
              >
              <p class="text-xs text-gray-500 mt-1">
                How many days advance notice do you need for bookings?
              </p>
            </div>
          </div>

          <!-- Save Button -->
          <button
            :disabled="savingAvailability"
            class="w-full py-3 px-6 bg-burgundy text-white font-bold rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50"
            @click="saveAvailabilitySettings"
          >
            {{ savingAvailability ? 'Saving...' : '✓ Save Availability' }}
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import api from '../api'
const defaultForm = () => ({
  username: '',
  bio: '',
  short_bio: '',
  location: '',
  experience_years: 0,
  category_ids: [],
  service_area_radius: 0,
  accept_tips: true,
  bkash_number: '',
  nagad_number: '',
  rocket_number: '',
  tip_message: '',
  facebook_url: '',
  instagram_url: '',
  twitter_url: '',
  linkedin_url: '',
  youtube_url: '',
  pexels_url: '',
  website_url: '',
  is_available: true,
  response_time_preference: '',
  booking_lead_time: 0,
});

export default {
  name: 'PhotographerSettings',

  data() {
    return {
      activeTab: 'profile',
      savingProfile: false,
      savingTips: false,
      savingSocial: false,
      savingAvailability: false,
      successMessage: null,
      errorMessage: null,
      profilePicturePreview: null,
      uploadingProfilePicture: false,
      removeProfilePictureFlag: false,
      defaultTipMessage: 'Your tip helps me keep creating, learning, and improving for you.',
      tabs: [
        { id: 'profile', label: 'Profile Info' },
        { id: 'tips', label: '☕ Tip Settings' },
        { id: 'social', label: 'Social Media' },
        { id: 'availability', label: 'Availability' },
      ],
      categories: [],
      locations: [],
      form: defaultForm(),
    };
  },

  computed: {
    hasAnyTipNumber() {
      return Boolean(this.form.bkash_number || this.form.nagad_number || this.form.rocket_number)
    },
    tipPreviewMessage() {
      return this.form.tip_message || this.defaultTipMessage
    },
    profilePictureSrc() {
      const value = this.form.profile_picture
      if (!value || typeof value !== 'string') return ''
      if (value.startsWith('data:') || value.startsWith('http') || value.startsWith('/storage/')) {
        return value
      }
      return `/storage/${value.replace(/^\/+/, '')}`
    },
  },

  mounted() {
    this.fetchCategories();
    this.fetchLocations();
    this.fetchPhotographerData();
  },

  methods: {
    notifyError(message) {
      if (this.$toast && typeof this.$toast.error === 'function') {
        this.$toast.error(message)
      }
      this.errorMessage = message
    },
    clearMessages() {
      this.successMessage = null
      this.errorMessage = null
    },
    async fetchCategories() {
      try {
        const response = await api.get('/categories');
        this.categories = response?.data?.data || [];
      } catch (error) {
        console.error('Error loading categories:', error);
      }
    },
    async fetchLocations() {
      try {
        const response = await api.get('/locations');
        this.locations = response?.data?.data || [];
      } catch (error) {
        console.error('Error loading locations:', error);
      }
    },
    async fetchPhotographerData() {
      try {
        const response = await api.get('/photographer/settings');
        const data = response?.data?.data || {};
        this.form = {
          ...defaultForm(),
          ...data,
          username: data.username || '',
          bio: data.bio || '',
          short_bio: data.short_bio || '',
          location: data.location || '',
          city_id: data.city_id ?? null,
          profile_picture: data.profile_picture || '',
          specializations: Array.isArray(data.specializations) ? data.specializations.join(', ') : (data.specializations || ''),
          favorite_hashtags: Array.isArray(data.favorite_hashtags) ? data.favorite_hashtags.join(', ') : (data.favorite_hashtags || ''),
          category_ids: Array.isArray(data.category_ids) ? data.category_ids : [],
          experience_years: Number.isFinite(Number(data.experience_years)) ? Number(data.experience_years) : 0,
          service_area_radius: Number.isFinite(Number(data.service_area_radius)) ? Number(data.service_area_radius) : 0,
          accept_tips: typeof data.accept_tips === 'boolean' ? data.accept_tips : true,
          bkash_number: data.bkash_number || data.tip_phone_number || '',
          nagad_number: data.nagad_number || '',
          rocket_number: data.rocket_number || '',
          tip_message: data.tip_message || '',
          facebook_url: data.facebook_url || '',
          instagram_url: data.instagram_url || '',
          twitter_url: data.twitter_url || '',
          linkedin_url: data.linkedin_url || '',
          youtube_url: data.youtube_url || '',
          pexels_url: data.pexels_url || '',
          website_url: data.website_url || '',
          is_available: typeof data.is_available === 'boolean' ? data.is_available : true,
          response_time_preference: data.response_time_preference || '',
          booking_lead_time: Number.isFinite(Number(data.booking_lead_time)) ? Number(data.booking_lead_time) : 0,
        };
        this.removeProfilePictureFlag = false;
      } catch (error) {
        console.error('Error fetching photographer data:', error);
        this.notifyError('Failed to load settings');
      }
    },

    async saveSettings() {
      this.savingProfile = true;
      this.clearMessages();
      try {
        // Check if a file is being uploaded
        if (this.form.profile_picture instanceof File) {
          // Use FormData for file upload
          const formData = new FormData();
          formData.append('_method', 'PUT');
          formData.append('username', this.form.username || '');
          formData.append('bio', this.form.bio || '');
          formData.append('short_bio', this.form.short_bio || '');
          formData.append('location', this.form.location || '');
          formData.append('city_id', this.form.city_id || '');
          formData.append('profile_picture', this.form.profile_picture);
          formData.append('experience_years', this.form.experience_years || 0);
          
          // Append array fields properly for FormData
          const specializations = this.normalizeList(this.form.specializations);
          specializations.forEach((spec, index) => {
            formData.append(`specializations[${index}]`, spec);
          });
          
          const hashtags = this.normalizeList(this.form.favorite_hashtags);
          hashtags.forEach((tag, index) => {
            formData.append(`favorite_hashtags[${index}]`, tag);
          });
          
          this.form.category_ids.forEach((id, index) => {
            formData.append(`category_ids[${index}]`, id);
          });
          
          formData.append('service_area_radius', this.form.service_area_radius || 0);
          if (this.removeProfilePictureFlag) {
            formData.append('remove_profile_picture', '1');
          }

          await api.post('/photographer/settings/profile', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
        } else {
          // Regular JSON request
          await api.put('/photographer/settings/profile', {
            username: this.form.username,
            bio: this.form.bio,
            short_bio: this.form.short_bio,
            location: this.form.location,
            city_id: this.form.city_id,
            experience_years: this.form.experience_years,
            specializations: this.normalizeList(this.form.specializations),
            favorite_hashtags: this.normalizeList(this.form.favorite_hashtags),
            category_ids: this.form.category_ids,
            service_area_radius: this.form.service_area_radius,
            remove_profile_picture: this.removeProfilePictureFlag,
          });
        }
        this.profilePicturePreview = null;
        this.removeProfilePictureFlag = false;
        this.showSuccess('Profile updated successfully!');
        await this.fetchPhotographerData();
      } catch (error) {
        this.notifyError(error.response?.data?.message || 'Failed to save profile');
      } finally {
        this.savingProfile = false;
      }
    },

    async saveTipSettings() {
      this.savingTips = true;
      this.clearMessages();
      try {
        await api.put('/photographer/settings/tips', {
          accept_tips: this.form.accept_tips,
          bkash_number: this.form.bkash_number,
          nagad_number: this.form.nagad_number,
          rocket_number: this.form.rocket_number,
          tip_message: this.form.tip_message,
        });
        this.showSuccess('Tip settings updated successfully!');
        await this.fetchPhotographerData();
      } catch (error) {
        this.notifyError(error.response?.data?.message || 'Failed to save tip settings');
      } finally {
        this.savingTips = false;
      }
    },

    async saveSocialSettings() {
      this.savingSocial = true;
      this.clearMessages();
      try {
        await api.put('/photographer/settings/social', {
          facebook_url: this.form.facebook_url,
          instagram_url: this.form.instagram_url,
          twitter_url: this.form.twitter_url,
          linkedin_url: this.form.linkedin_url,
          youtube_url: this.form.youtube_url,
          website_url: this.form.website_url,
          pexels_url: this.form.pexels_url,
        });
        this.showSuccess('Social links updated successfully!');
        await this.fetchPhotographerData();
      } catch (error) {
        this.notifyError(error.response?.data?.message || 'Failed to save social links');
      } finally {
        this.savingSocial = false;
      }
    },

    async saveAvailabilitySettings() {
      this.savingAvailability = true;
      this.clearMessages();
      try {
        await api.put('/photographer/settings/availability', {
          is_available: this.form.is_available,
          response_time_preference: this.form.response_time_preference,
          booking_lead_time: this.form.booking_lead_time,
        });
        this.showSuccess('Availability settings updated successfully!');
        await this.fetchPhotographerData();
      } catch (error) {
        this.notifyError(error.response?.data?.message || 'Failed to save availability');
      } finally {
        this.savingAvailability = false;
      }
    },

    showSuccess(message) {
      this.successMessage = message;
      this.errorMessage = null;
      setTimeout(() => {
        this.successMessage = null;
      }, 5000);
    },

    onProfilePictureChange(event) {
      const file = event.target.files?.[0];
      if (!file) return;

      // Validate file size (5MB max)
      const maxSize = 5242880; // 5MB in bytes
      if (file.size > maxSize) {
        this.notifyError('File size must be less than 5MB');
        this.$refs.fileInput.value = '';
        return;
      }

      // Validate file type
      const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
      if (!allowedTypes.includes(file.type)) {
        this.notifyError('Only JPG, PNG, and WebP images are allowed');
        this.$refs.fileInput.value = '';
        return;
      }

      // Create preview
      const reader = new FileReader();
      reader.onload = (e) => {
        this.profilePicturePreview = e.target.result;
      };
      reader.readAsDataURL(file);

      // Store the file in form
      this.form.profile_picture = file;
      this.removeProfilePictureFlag = false;
    },

    removeProfilePicture() {
      this.profilePicturePreview = null;
      this.form.profile_picture = null;
      this.removeProfilePictureFlag = true;
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = '';
      }
    },

    normalizeList(value) {
      if (Array.isArray(value)) {
        return value.map(item => String(item).trim()).filter(Boolean);
      }
      if (!value) {
        return [];
      }
      return String(value)
        .split(',')
        .map(item => item.trim())
        .filter(Boolean);
    },
  },
};
</script>

<style scoped>
textarea:focus,
input:focus,
select:focus {
  box-shadow: 0 0 0 3px rgba(128, 0, 32, 0.1);
}

/* Ensure proper text visibility on mobile */
input,
textarea,
select {
  color: #111827 !important;
  background-color: #ffffff !important;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

input::placeholder,
textarea::placeholder {
  color: #9ca3af !important;
}

/* Override iOS input styling */
input[type="text"],
input[type="email"],
input[type="url"],
input[type="number"],
input[type="tel"],
textarea,
select {
  font-size: 16px;
  font-family: inherit;
}
</style>
