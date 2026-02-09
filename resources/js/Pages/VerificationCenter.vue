<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 pt-8 sm:pt-10 lg:pt-12 pb-24 md:pb-12 px-3 sm:px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="mb-8 sm:mb-12">
        <div class="flex items-start sm:items-center gap-3 sm:gap-4 mb-4">
          <div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 flex items-center gap-3">
              <svg
                class="w-8 h-8 sm:w-10 sm:h-10 text-burgundy-600"
                fill="currentColor"
                viewBox="0 0 24 24"
              >
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg>
              Verification <span class="text-burgundy-600">Center</span>
            </h1>
            <p class="text-sm sm:text-base lg:text-lg text-burgundy-600 font-semibold mt-1">
              Build trust with verified credentials
            </p>
          </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-burgundy-500 to-burgundy-400 rounded-full" />
      </div>

      <div
        v-if="loading"
        class="flex justify-center items-center py-20"
      >
        <div class="text-center">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-burgundy-200 border-t-burgundy-600 mx-auto mb-4" />
          <p class="text-burgundy-600 font-semibold">
            Loading your verification status...
          </p>
        </div>
      </div>

      <div v-else>
        <!-- Not Photographer Alert -->
        <div
          v-if="!isPhotographer"
          class="mb-6 sm:mb-8 bg-white rounded-2xl shadow-lg p-5 sm:p-8 border-l-4 border-burgundy-600"
        >
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-burgundy-100 flex items-center justify-center flex-shrink-0">
              <svg
                class="w-7 h-7 text-burgundy-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-xl font-bold text-burgundy-700 mb-2">
                Photographer Account Required
              </h3>
              <p class="text-gray-600 mb-4">
                Verification is exclusively for photographer accounts. To get started with verification, please create a photographer profile first.
              </p>
              <router-link
                to="/photographer/onboarding"
                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-burgundy-600 to-burgundy-700 text-white rounded-lg hover:from-burgundy-700 hover:to-burgundy-800 transition-all font-semibold shadow-md hover:shadow-lg"
              >
                Create Photographer Profile
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                  />
                </svg>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div
          v-else
          class="space-y-8"
        >
          <!-- Status Overview -->
          <div
            v-if="statusItems.length"
            class="bg-white rounded-2xl shadow-xl p-5 sm:p-8 border border-gray-100"
          >
            <div class="flex items-center gap-3 mb-6 sm:mb-8">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-burgundy-100 to-burgundy-200 flex items-center justify-center">
                <svg
                  class="w-6 h-6 text-burgundy-700"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.757a1 1 0 01-.940 1.017 48.412 48.412 0 01-7.125 0 1 1 0 01-.94-1.017v-6.757a3.066 3.066 0 012.812-3.062zM9 12a1 1 0 100-2 1 1 0 000 2z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-burgundy-700 bg-clip-text text-transparent">
                Your Verification Status
              </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
              <div
                v-for="item in statusItems"
                :key="item.type"
                :class="[
                  'p-6 rounded-xl border-2 transition-all hover:shadow-lg',
                  item.status === 'approved' && !isExpired(item.expires_at)
                    ? 'bg-green-50 border-green-300 hover:border-green-400'
                    : item.status === 'approved' && isExpired(item.expires_at)
                      ? 'bg-orange-50 border-orange-300 hover:border-orange-400'
                      : item.status === 'rejected'
                        ? 'bg-red-50 border-red-300 hover:border-red-400'
                        : 'bg-amber-50 border-amber-300 hover:border-amber-400'
                ]"
              >
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <p class="text-sm font-bold text-gray-700 uppercase tracking-wide">
                      {{ formatType(item.type) }}
                    </p>
                    <span
                      :class="[
                        'inline-block mt-3 px-4 py-2 rounded-full text-xs font-bold',
                        item.status === 'approved'
                          ? isExpired(item.expires_at)
                            ? 'bg-orange-200 text-orange-900'
                            : 'bg-green-200 text-green-900'
                          : item.status === 'rejected'
                            ? 'bg-red-200 text-red-900'
                            : 'bg-amber-200 text-amber-900'
                      ]"
                    >
                      {{ isExpired(item.expires_at) && item.status === 'approved' ? '⚠️ EXPIRED' : item.status.toUpperCase() }}
                    </span>
                  </div>
                  <div
                    :class="[
                      'w-10 h-10 rounded-full flex items-center justify-center text-xl',
                      item.status === 'approved' && !isExpired(item.expires_at)
                        ? 'bg-green-200 text-green-700'
                        : item.status === 'approved' && isExpired(item.expires_at)
                          ? 'bg-orange-200 text-orange-700'
                          : item.status === 'rejected'
                            ? 'bg-red-200 text-red-700'
                            : 'bg-amber-200 text-amber-700'
                    ]"
                  >
                    {{ item.status === 'approved' && !isExpired(item.expires_at) ? '✓' : '!' }}
                  </div>
                </div>

                <div class="space-y-3 text-sm text-gray-700 border-t border-gray-200 pt-4">
                  <p v-if="item.verified_at">
                    <span class="font-semibold text-burgundy-700">✓ Verified:</span>
                    {{ formatDate(item.verified_at) }}
                  </p>
                  <p
                    v-if="item.expires_at"
                    :class="isExpired(item.expires_at) ? 'text-orange-700 font-bold' : ''"
                  >
                    <span
                      class="font-semibold"
                      :class="isExpired(item.expires_at) ? 'text-orange-700' : 'text-burgundy-700'"
                    >📅 Expires:</span>
                    {{ formatDate(item.expires_at) }}
                  </p>
                </div>

                <button
                  v-if="item.status === 'approved' && isExpired(item.expires_at)"
                  :disabled="renewingId === item.type"
                  class="w-full mt-4 px-4 py-3 text-sm font-bold bg-gradient-to-r from-burgundy-600 to-burgundy-700 text-white rounded-lg hover:from-burgundy-700 hover:to-burgundy-800 transition-all disabled:opacity-60 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
                  @click="renewVerification(item)"
                >
                  {{ renewingId === item.type ? 'Renewing...' : '⟳ Renew Now' }}
                </button>
              </div>
            </div>

            <div
              v-if="pendingRequests"
              class="mt-8 p-5 bg-gradient-to-r from-burgundy-50 to-rose-50 border-l-4 border-burgundy-600 rounded-lg"
            >
              <p class="text-burgundy-900 font-semibold">
                ⏳ <span class="text-burgundy-700">{{ pendingRequests }}</span> verification request{{ pendingRequests !== 1 ? 's' : '' }} pending admin review.
              </p>
            </div>
          </div>

          <!-- No Verifications -->
          <div
            v-else
            class="bg-gradient-to-br from-burgundy-50 via-rose-50 to-burgundy-50 rounded-2xl shadow-lg p-6 sm:p-10 border-2 border-burgundy-200 text-center"
          >
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-burgundy-100 to-burgundy-200 flex items-center justify-center mx-auto mb-5 shadow-md">
              <svg
                class="w-10 h-10 text-burgundy-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4"
                />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-burgundy-900 mb-3">
              No Verifications Yet
            </h3>
            <p class="text-burgundy-700 font-medium mb-4">
              Start building credibility by submitting verification requests below.
            </p>
          </div>

          <!-- Submit Form -->
          <div class="bg-white rounded-2xl shadow-2xl p-5 sm:p-8 border border-gray-100">
            <div class="flex items-start sm:items-center gap-4 mb-6 sm:mb-8 pb-5 sm:pb-6 border-b-2 border-gray-100">
              <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-burgundy-500 to-burgundy-700 flex items-center justify-center shadow-lg flex-shrink-0">
                <svg
                  class="w-7 h-7 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                  />
                </svg>
              </div>
              <div>
                <h2 class="text-2xl sm:text-4xl font-bold">
                  <span class="text-gray-900">Submit New </span>
                  <span class="bg-gradient-to-r from-burgundy-600 to-burgundy-700 bg-clip-text text-transparent">Verification</span>
                </h2>
                <p class="text-burgundy-700 text-sm mt-2 font-semibold">
                  Add verified credentials to strengthen your profile
                </p>
              </div>
            </div>

            <p class="text-gray-700 mb-8 text-base font-medium leading-relaxed">
              Choose a verification type and submit supporting documents. Our team will review your submission within <span class="text-burgundy-700 font-bold">24-48 hours</span>.
            </p>

            <form
              id="verificationForm"
              class="space-y-6 sm:space-y-8"
              @submit.prevent="submitRequest"
            >
              <div>
                <label class="block text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                  <span class="text-2xl">📋</span>
                  <span>Verification Type</span>
                </label>
                <select
                  v-model="form.request_type"
                  class="w-full bg-white border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-900 font-medium focus:outline-none focus:border-burgundy-600 focus:ring-2 focus:ring-burgundy-500/30 transition-all hover:border-gray-300"
                  required
                >
                  <option value="">
                    🔍 Select verification type...
                  </option>
                  <option value="nid">
                    🪪 National ID / Passport
                  </option>
                  <option value="business_license">
                    📜 Business License
                  </option>
                  <option value="tax_certificate">
                    📋 Tax Certificate
                  </option>
                  <option value="studio_address">
                    🏢 Studio Address Proof
                  </option>
                </select>
                <p class="text-sm text-burgundy-700 mt-2 flex items-center gap-2">
                  <span>💡</span>
                  <span>Submit verified documents to increase your credibility and appear higher in search results.</span>
                </p>
              </div>

              <div>
                <label class="block text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                  <span class="text-2xl">📸</span>
                  <span>Supporting Documents</span>
                </label>
                <div
                  :class="[
                    'upload-drop relative p-6 sm:p-10 text-center transition-all cursor-pointer',
                    dragActive
                      ? 'border-burgundy-600 bg-burgundy-50'
                      : 'border-burgundy-300 hover:border-burgundy-500 hover:bg-burgundy-100/50'
                  ]"
                  @dragover.prevent="dragActive = true"
                  @dragleave.prevent="dragActive = false"
                  @drop.prevent="handleDrop"
                >
                  <input
                    ref="fileInput"
                    type="file"
                    multiple
                    class="hidden"
                    accept="image/*,application/pdf"
                    @change="handleFiles"
                  >
                  <div
                    class="cursor-pointer"
                    @click="$refs.fileInput?.click()"
                  >
                    <div class="text-4xl sm:text-5xl mb-4">
                      📁
                    </div>
                    <p class="text-lg sm:text-xl font-extrabold text-burgundy-900 mb-2">
                      Click to upload or drag and drop
                    </p>
                    <p class="text-sm sm:text-base text-burgundy-800 font-bold mt-2 bg-white/60 inline-block px-4 py-2 rounded-lg">
                      JPG, PNG, PDF (Max 5 files, 10MB each)
                    </p>
                    <p class="upload-hint mt-2">
                      Images: 1600x1000 px recommended
                    </p>
                  </div>
                </div>

                <!-- File Preview -->
                <div
                  v-if="files.length"
                  class="mt-6 space-y-3"
                >
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-bold text-gray-900 flex items-center gap-2">
                      <span class="text-xl">✅</span>
                      <span>Selected Files (<span class="text-burgundy-700">{{ files.length }}/5</span>)</span>
                    </p>
                  </div>
                  <div class="space-y-2 max-h-56 overflow-y-auto">
                    <div
                      v-for="(file, index) in files"
                      :key="index"
                      class="flex items-center justify-between p-4 bg-gradient-to-r from-burgundy-50 to-rose-50 border-2 border-burgundy-200 rounded-xl hover:border-burgundy-400 transition-all hover:shadow-md"
                    >
                      <div class="flex items-center gap-4 flex-1 min-w-0">
                        <span class="text-2xl flex-shrink-0">📄</span>
                        <div class="flex-1 min-w-0">
                          <p class="text-sm font-semibold text-gray-900 truncate">
                            {{ file.name }}
                          </p>
                          <p class="text-xs text-burgundy-700 font-medium">
                            {{ (file.size / 1024).toFixed(1) }} KB
                          </p>
                        </div>
                      </div>
                      <button
                        type="button"
                        class="text-burgundy-600 hover:text-burgundy-800 hover:bg-burgundy-200 p-2 rounded-lg transition-all flex-shrink-0 font-bold"
                        @click="removeFile(index)"
                      >
                        <svg
                          class="w-5 h-5"
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div>
                <button
                  type="submit"
                  :disabled="submitting || !form.request_type"
                  class="w-full px-6 py-4 bg-gradient-to-r from-burgundy-600 to-burgundy-700 hover:from-burgundy-700 hover:to-burgundy-800 text-white font-bold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl hover:shadow-burgundy-600/40 text-lg uppercase tracking-wide"
                >
                  <span
                    v-if="!submitting"
                    class="flex items-center justify-center gap-2"
                  >
                    <span>✓</span>
                    <span>Submit Verification Request</span>
                  </span>
                  <span
                    v-else
                    class="flex items-center justify-center gap-2"
                  >
                    <svg
                      class="w-5 h-5 animate-spin"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                      />
                    </svg>
                    <span>Submitting...</span>
                  </span>
                </button>
              </div>

              <!-- Alerts -->
              <transition name="fade">
                <div
                  v-if="message"
                  class="p-5 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-600 rounded-xl"
                >
                  <div class="flex items-start gap-3">
                    <svg
                      class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <p class="text-sm text-green-900 font-semibold">
                      {{ message }}
                    </p>
                  </div>
                </div>
              </transition>

              <transition name="fade">
                <div
                  v-if="error"
                  class="p-5 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-600 rounded-xl"
                >
                  <div class="flex items-start gap-3">
                    <svg
                      class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <p class="text-sm text-red-900 font-semibold">
                      {{ error }}
                    </p>
                  </div>
                </div>
              </transition>
            </form>

            <!-- Benefits Box -->
            <div class="mt-8 sm:mt-10 p-5 sm:p-7 bg-gradient-to-br from-burgundy-50 via-rose-50 to-burgundy-50 border-2 border-burgundy-300 rounded-2xl">
              <p class="text-base font-bold text-burgundy-900 mb-5 flex items-center gap-2">
                <span class="text-2xl">✨</span>
                <span>Why Get Verified?</span>
              </p>
              <ul class="text-sm text-gray-800 space-y-4">
                <li class="flex gap-4">
                  <span class="text-2xl flex-shrink-0">📈</span>
                  <span><span class="font-bold text-burgundy-700">Higher Visibility</span> - Verified photographers appear first in searches</span>
                </li>
                <li class="flex gap-4">
                  <span class="text-2xl flex-shrink-0">🤝</span>
                  <span><span class="font-bold text-burgundy-700">Build Trust</span> - Clients are 3x more likely to book verified photographers</span>
                </li>
                <li class="flex gap-4">
                  <span class="text-2xl flex-shrink-0">⭐</span>
                  <span><span class="font-bold text-burgundy-700">Badge Recognition</span> - Display verified badges on your profile</span>
                </li>
              </ul>
            </div>
          </div>

          <div
            v-if="isPhotographer"
            class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur border-t border-gray-200 px-4 py-3 z-40"
          >
            <button
              form="verificationForm"
              type="submit"
              :disabled="submitting || !form.request_type"
              class="w-full px-6 py-3 bg-gradient-to-r from-burgundy-600 to-burgundy-700 hover:from-burgundy-700 hover:to-burgundy-800 text-white font-bold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ submitting ? 'Submitting...' : 'Submit Verification' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../api'
import { validateUploadFile } from '../utils/imageValidation'
import { formatDate as formatDateValue } from '../utils/formatters'

const loading = ref(true)
const submitting = ref(false)
const renewingId = ref(null)
const statusItems = ref([])
const pendingRequests = ref(0)
const message = ref('')
const error = ref('')
const photographerId = ref(null)
const dragActive = ref(false)
const fileInput = ref(null)

const form = ref({
  request_type: ''
})
const files = ref([])

const isPhotographer = computed(() => !!photographerId.value)

const fetchCurrentUser = async () => {
  try {
    const response = await api.get('/auth/me')
    const user = response.data.data || response.data
    photographerId.value = user?.photographer?.slug || null
  } catch (e) {
    if (import.meta.env.DEV) console.error('Failed to fetch user:', e)
  }
}

const fetchStatus = async () => {
  if (!photographerId.value) return
  try {
    const response = await api.get(`/verifications/status/${photographerId.value}`)
    const payload = response.data.data || {}
    statusItems.value = payload.verifications || []
    pendingRequests.value = payload.pending_requests || 0
  } catch (e) {
    if (import.meta.env.DEV) console.error('Failed to fetch verification status:', e)
  }
}

const submitRequest = async () => {
  error.value = ''
  message.value = ''

  if (!form.value.request_type) {
    error.value = 'Please select a verification type.'
    return
  }

  submitting.value = true
  try {
    const formData = new FormData()
    formData.append('request_type', form.value.request_type)
    files.value.forEach((file) => formData.append('submitted_documents[]', file))

    await api.post('/verifications/submit', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    message.value = '✓ Verification request submitted successfully! Our team will review it shortly.'
    form.value.request_type = ''
    files.value = []
    
    // Auto-clear success message after 5 seconds
    setTimeout(() => {
      message.value = ''
    }, 5000)

    await fetchStatus()
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to submit request. Please try again.'
  } finally {
    submitting.value = false
  }
}

const handleFiles = async (event) => {
  const newFiles = Array.from(event.target.files || [])
  await addFiles(newFiles)
}

const handleDrop = async (event) => {
  dragActive.value = false
  const newFiles = Array.from(event.dataTransfer.files || [])
  await addFiles(newFiles)
}

const addFiles = async (newFiles) => {
  error.value = ''
  const validated = []

  for (const file of newFiles) {
    const validation = await validateUploadFile(file, {
      label: 'Document',
      maxBytes: 10 * 1024 * 1024,
      allowedTypes: ['image/jpeg', 'image/png', 'application/pdf'],
      imageWidth: 1600,
      imageHeight: 1000
    })

    if (!validation.ok) {
      error.value = validation.message
      continue
    }

    validated.push(file)
  }

  files.value = [...files.value, ...validated].slice(0, 5)
}

const removeFile = (index) => {
  files.value.splice(index, 1)
}

const formatType = (type) => {
  const labels = {
    nid: 'National ID / Passport',
    business_license: 'Business License',
    tax_certificate: 'Tax Certificate',
    studio_address: 'Studio Address Proof'
  }
  return labels[type] || type
}

const formatDate = (date) => {
  const formatted = formatDateValue(date)
  return formatted || 'N/A'
}

const isExpired = (expiryDate) => {
  if (!expiryDate) return false
  return new Date(expiryDate) < new Date()
}

const renewVerification = async (item) => {
  renewingId.value = item.type
  error.value = ''
  message.value = ''

  try {
    await api.post(`/verifications/renew`, {
      verification_type: item.type
    })

    message.value = '✓ Renewal request submitted! We will process it soon.'
    
    setTimeout(() => {
      message.value = ''
    }, 5000)

    await fetchStatus()
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to renew verification. Please try again.'
  } finally {
    renewingId.value = null
  }
}

onMounted(async () => {
  try {
    await fetchCurrentUser()
    await fetchStatus()
  } catch (e) {
    if (import.meta.env.DEV) console.error('Failed to initialize verification center:', e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
