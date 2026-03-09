<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Submit Verification Request
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="sb-ui-card p-8">
          <form
            enctype="multipart/form-data"
            @submit.prevent="submit"
          >
            <!-- Verification Type -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Verification Type *</label>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                  v-for="vtype in verificationTypes"
                  :key="vtype.value"
                  class="relative"
                >
                  <input 
                    :id="vtype.value" 
                    v-model="form.type" 
                    type="radio"
                    :value="vtype.value"
                    class="sr-only"
                  >
                  <label 
                    :for="vtype.value" 
                    class="block cursor-pointer p-4 border-2 rounded-lg transition"
                    :class="form.type === vtype.value ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
                  >
                    <div class="font-semibold text-gray-800">{{ vtype.label }}</div>
                    <div class="text-sm text-gray-600">{{ vtype.description }}</div>
                  </label>
                </div>
              </div>
              <div
                v-if="errors.type"
                class="text-red-600 text-sm mt-1"
              >
                {{ errors.type }}
              </div>
            </div>

            <!-- Full Name -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Full Name *</label>
              <input 
                v-model="form.full_name" 
                type="text"
                :class="['sb-ui-input', errors.full_name ? 'sb-ui-input--error' : '']"
              >
              <div
                v-if="errors.full_name"
                class="text-red-600 text-sm mt-1"
              >
                {{ errors.full_name }}
              </div>
            </div>

            <!-- Phone -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Phone Number *</label>
              <input 
                v-model="form.phone" 
                type="tel"
                :class="['sb-ui-input', errors.phone ? 'sb-ui-input--error' : '']"
              >
              <div
                v-if="errors.phone"
                class="text-red-600 text-sm mt-1"
              >
                {{ errors.phone }}
              </div>
            </div>

            <!-- NID Number -->
            <div
              v-if="form.type === 'nid'"
              class="mb-6"
            >
              <label class="block text-gray-700 font-semibold mb-2">National ID Number</label>
              <input 
                v-model="form.nid_number" 
                type="text"
                class="sb-ui-input"
              >
            </div>

            <!-- Business Name -->
            <div
              v-if="form.type === 'business'"
              class="mb-6"
            >
              <label class="block text-gray-700 font-semibold mb-2">Business Name</label>
              <input 
                v-model="form.business_name" 
                type="text"
                class="sb-ui-input"
              >
            </div>

            <!-- Document Front -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Document Front (JPG/PNG/PDF)</label>
              <div class="flex items-center justify-center w-full">
                <label class="upload-drop flex flex-col w-full h-32 p-4 cursor-pointer">
                  <div class="flex flex-col items-center justify-center pt-7">
                    <svg
                      class="w-8 h-8 text-gray-400"
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
                    <p class="text-sm text-gray-500">Click to upload</p>
                    <p class="upload-hint mt-1">Images: 1600x1000 px recommended</p>
                  </div>
                  <input 
                    type="file" 
                    class="hidden"
                    accept="image/jpeg,image/png,application/pdf"
                    @change="handleFileUpload($event, 'document_front_path')"
                  >
                </label>
              </div>
              <div
                v-if="form.document_front_path"
                class="mt-2 text-sm text-green-600"
              >
                ✓ File selected
              </div>
              <div
                v-if="errors.document_front_path"
                class="text-red-600 text-sm mt-1"
              >
                {{ errors.document_front_path }}
              </div>
            </div>

            <!-- Document Back -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Document Back (JPG/PNG/PDF)</label>
              <div class="flex items-center justify-center w-full">
                <label class="upload-drop flex flex-col w-full h-32 p-4 cursor-pointer">
                  <div class="flex flex-col items-center justify-center pt-7">
                    <svg
                      class="w-8 h-8 text-gray-400"
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
                    <p class="text-sm text-gray-500">Click to upload</p>
                    <p class="upload-hint mt-1">Images: 1600x1000 px recommended</p>
                  </div>
                  <input 
                    type="file" 
                    class="hidden"
                    accept="image/jpeg,image/png,application/pdf"
                    @change="handleFileUpload($event, 'document_back_path')"
                  >
                </label>
              </div>
              <div
                v-if="form.document_back_path"
                class="mt-2 text-sm text-green-600"
              >
                ✓ File selected
              </div>
            </div>

            <!-- Selfie -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Selfie with Document (JPG/PNG)</label>
              <div class="flex items-center justify-center w-full">
                <label class="upload-drop flex flex-col w-full h-32 p-4 cursor-pointer">
                  <div class="flex flex-col items-center justify-center pt-7">
                    <svg
                      class="w-8 h-8 text-gray-400"
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
                    <p class="text-sm text-gray-500">Click to upload</p>
                    <p class="upload-hint mt-1">Selfie: 1000x1000 px recommended</p>
                  </div>
                  <input 
                    type="file" 
                    class="hidden"
                    accept="image/jpeg,image/png"
                    @change="handleFileUpload($event, 'selfie_path')"
                  >
                </label>
              </div>
              <div
                v-if="form.selfie_path"
                class="mt-2 text-sm text-green-600"
              >
                ✓ File selected
              </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Additional Notes</label>
              <textarea 
                v-model="form.note"
                rows="4"
                class="sb-ui-textarea"
                placeholder="Any additional information..."
              />
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
              <button 
                type="submit" 
                :disabled="processing"
                class="sb-ui-btn sb-ui-btn--primary disabled:bg-gray-400"
              >
                {{ processing ? 'Submitting...' : 'Submit Verification Request' }}
              </button>
              <Link
                :href="route('verification.index')"
                class="sb-ui-btn sb-ui-btn--secondary"
              >
                Cancel
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { validateUploadFile } from '../../utils/imageValidation'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  verificationTypes: Array
})

const { data: form, post, processing, errors } = useForm({
  type: '',
  full_name: '',
  phone: '',
  nid_number: '',
  business_name: '',
  document_front_path: null,
  document_back_path: null,
  selfie_path: null,
  note: ''
})

const handleFileUpload = async (event, fieldName) => {
  const file = event.target.files?.[0]
  if (!file) {
    form[fieldName] = null
    return
  }

  const rules = {
    document_front_path: { width: 1600, height: 1000 },
    document_back_path: { width: 1600, height: 1000 },
    selfie_path: { width: 1000, height: 1000 }
  }
  const rule = rules[fieldName] || {}
  const allowedTypes = fieldName === 'selfie_path'
    ? ['image/jpeg', 'image/png']
    : ['image/jpeg', 'image/png', 'application/pdf']

  const validation = await validateUploadFile(file, {
    label: 'File',
    maxBytes: 5 * 1024 * 1024,
    allowedTypes,
    imageWidth: rule.width,
    imageHeight: rule.height
  })

  if (!validation.ok) {
    alert(validation.message)
    form[fieldName] = null
    event.target.value = ''
    return
  }

  form[fieldName] = file
}

const submit = () => {
  post(route('verification.store'))
}
</script>
