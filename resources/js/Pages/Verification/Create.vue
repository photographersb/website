<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Submit Verification Request
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg p-8">
          <form @submit.prevent="submit" enctype="multipart/form-data">
            <!-- Verification Type -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Verification Type *</label>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="vtype in verificationTypes" :key="vtype.value" class="relative">
                  <input 
                    type="radio" 
                    :value="vtype.value" 
                    v-model="form.type"
                    :id="vtype.value"
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
              <div v-if="errors.type" class="text-red-600 text-sm mt-1">{{ errors.type }}</div>
            </div>

            <!-- Full Name -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Full Name *</label>
              <input 
                v-model="form.full_name" 
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600"
                :class="errors.full_name ? 'border-red-600' : ''"
              >
              <div v-if="errors.full_name" class="text-red-600 text-sm mt-1">{{ errors.full_name }}</div>
            </div>

            <!-- Phone -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Phone Number *</label>
              <input 
                v-model="form.phone" 
                type="tel"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600"
                :class="errors.phone ? 'border-red-600' : ''"
              >
              <div v-if="errors.phone" class="text-red-600 text-sm mt-1">{{ errors.phone }}</div>
            </div>

            <!-- NID Number -->
            <div v-if="form.type === 'nid'" class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">National ID Number</label>
              <input 
                v-model="form.nid_number" 
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600"
              >
            </div>

            <!-- Business Name -->
            <div v-if="form.type === 'business'" class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Business Name</label>
              <input 
                v-model="form.business_name" 
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600"
              >
            </div>

            <!-- Document Front -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Document Front (JPG/PNG/PDF)</label>
              <div class="flex items-center justify-center w-full">
                <label class="flex flex-col w-full h-32 border-2 border-gray-300 border-dashed rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                  <div class="flex flex-col items-center justify-center pt-7">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to upload</p>
                  </div>
                  <input 
                    type="file" 
                    class="hidden"
                    accept="image/jpeg,image/png,application/pdf"
                    @change="handleFileUpload($event, 'document_front_path')"
                  >
                </label>
              </div>
              <div v-if="form.document_front_path" class="mt-2 text-sm text-green-600">✓ File selected</div>
              <div v-if="errors.document_front_path" class="text-red-600 text-sm mt-1">{{ errors.document_front_path }}</div>
            </div>

            <!-- Document Back -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Document Back (JPG/PNG/PDF)</label>
              <div class="flex items-center justify-center w-full">
                <label class="flex flex-col w-full h-32 border-2 border-gray-300 border-dashed rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                  <div class="flex flex-col items-center justify-center pt-7">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to upload</p>
                  </div>
                  <input 
                    type="file" 
                    class="hidden"
                    accept="image/jpeg,image/png,application/pdf"
                    @change="handleFileUpload($event, 'document_back_path')"
                  >
                </label>
              </div>
              <div v-if="form.document_back_path" class="mt-2 text-sm text-green-600">✓ File selected</div>
            </div>

            <!-- Selfie -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Selfie with Document (JPG/PNG)</label>
              <div class="flex items-center justify-center w-full">
                <label class="flex flex-col w-full h-32 border-2 border-gray-300 border-dashed rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                  <div class="flex flex-col items-center justify-center pt-7">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to upload</p>
                  </div>
                  <input 
                    type="file" 
                    class="hidden"
                    accept="image/jpeg,image/png"
                    @change="handleFileUpload($event, 'selfie_path')"
                  >
                </label>
              </div>
              <div v-if="form.selfie_path" class="mt-2 text-sm text-green-600">✓ File selected</div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
              <label class="block text-gray-700 font-semibold mb-2">Additional Notes</label>
              <textarea 
                v-model="form.note"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600"
                placeholder="Any additional information..."
              ></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
              <button 
                type="submit" 
                :disabled="processing"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400"
              >
                {{ processing ? 'Submitting...' : 'Submit Verification Request' }}
              </button>
              <Link :href="route('verification.index')" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
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

const handleFileUpload = (event, fieldName) => {
  form[fieldName] = event.target.files[0]
}

const submit = () => {
  post(route('verification.store'))
}
</script>
