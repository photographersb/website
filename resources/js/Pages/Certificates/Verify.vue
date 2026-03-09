<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow border border-gray-100 p-6">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Certificate Verification</h1>
      <p class="text-sm text-gray-600 mb-6">{{ message }}</p>

      <div v-if="certificate?.platformLogo" class="mb-4 flex items-center gap-3">
        <img :src="certificate.platformLogo" alt="Platform Logo" class="w-10 h-10 object-contain">
        <span class="text-sm text-gray-700 font-semibold">Photographer SB</span>
      </div>

      <div v-if="!certificate" class="rounded-lg border border-red-200 bg-red-50 text-red-700 p-4">
        Certificate not found.
      </div>

      <div v-else class="space-y-4">
        <div :class="['rounded-lg p-3 text-sm', valid ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200']">
          {{ valid ? 'Valid Certificate' : 'Invalid or Revoked Certificate' }}
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-500">Certificate Code</dt>
            <dd class="font-medium text-gray-900">{{ certificate.code }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Certificate ID</dt>
            <dd class="font-medium text-gray-900">{{ certificate.certificateId || certificate.code }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Recipient</dt>
            <dd class="font-medium text-gray-900">{{ certificate.participantName }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Event / Competition</dt>
            <dd class="font-medium text-gray-900">{{ certificate.eventTitle || '—' }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Issue Date</dt>
            <dd class="font-medium text-gray-900">{{ certificate.issueDate || '—' }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Template</dt>
            <dd class="font-medium text-gray-900">{{ certificate.templateName || '—' }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Status</dt>
            <dd class="font-medium text-gray-900 capitalize">{{ certificate.status || '—' }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  certificate: {
    type: Object,
    default: null,
  },
  valid: {
    type: Boolean,
    default: false,
  },
  message: {
    type: String,
    default: '',
  },
})
</script>
