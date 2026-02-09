<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Photographer Verification
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Success Alert -->
        <div
          v-if="$page.props.flash.success"
          class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4"
        >
          <p class="text-green-800">
            {{ $page.props.flash.success }}
          </p>
        </div>

        <!-- Error Alert -->
        <div
          v-if="$page.props.flash.error"
          class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4"
        >
          <p class="text-red-800">
            {{ $page.props.flash.error }}
          </p>
        </div>

        <!-- Verified Status -->
        <div
          v-if="verification && verification.is_verified"
          class="mb-6 bg-green-50 border-2 border-green-200 rounded-lg p-6"
        >
          <div class="flex items-center gap-4">
            <div class="flex-shrink-0">
              <svg
                class="h-8 w-8 text-green-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-green-800">
                You are verified!
              </h3>
              <p class="text-green-700">
                {{ verification.getLevelLabel }} • Verified {{ formatDate(verification.verified_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Pending Request -->
        <div
          v-else-if="latestRequest && latestRequest.isPending()"
          class="mb-6 bg-blue-50 border-2 border-blue-200 rounded-lg p-6"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex-shrink-0">
                <svg
                  class="h-8 w-8 text-blue-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-blue-800">
                  Verification Pending
                </h3>
                <p class="text-blue-700">
                  Your request is under review. We'll notify you within 2-3 business days.
                </p>
              </div>
            </div>
            <Link
              :href="route('verification.show', latestRequest.id)"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              View Details
            </Link>
          </div>
        </div>

        <!-- Rejected Request -->
        <div
          v-else-if="latestRequest && latestRequest.isRejected()"
          class="mb-6 bg-red-50 border-2 border-red-200 rounded-lg p-6"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex-shrink-0">
                <svg
                  class="h-8 w-8 text-red-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-red-800">
                  Verification Not Approved
                </h3>
                <p class="text-red-700">
                  {{ latestRequest.admin_note }}
                </p>
              </div>
            </div>
            <Link
              :href="route('verification.create')"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
            >
              Try Again
            </Link>
          </div>
        </div>

        <!-- No Request Yet -->
        <div
          v-else
          class="mb-6 bg-gray-50 border-2 border-gray-200 rounded-lg p-6"
        >
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-800">
                Get Verified
              </h3>
              <p class="text-gray-600">
                Boost your credibility with photographer verification
              </p>
            </div>
            <Link
              :href="route('verification.create')"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Start Verification
            </Link>
          </div>
        </div>

        <!-- Benefits Section -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="text-blue-600 text-2xl mb-2">
              ✓
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">
              Build Trust
            </h4>
            <p class="text-gray-600 text-sm">
              Show clients you're a verified professional photographer
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <div class="text-blue-600 text-2xl mb-2">
              ⭐
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">
              Higher Visibility
            </h4>
            <p class="text-gray-600 text-sm">
              Verified photographers appear higher in search results
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-6">
            <div class="text-blue-600 text-2xl mb-2">
              💼
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">
              Premium Features
            </h4>
            <p class="text-gray-600 text-sm">
              Access additional tools and premium booking features
            </p>
          </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Verification Types
          </h3>
          <div class="space-y-4">
            <div
              v-for="type in verificationTypes"
              :key="type"
              class="border-l-4 border-blue-600 pl-4"
            >
              <h4 class="font-semibold text-gray-800">
                {{ formatVerificationType(type) }}
              </h4>
              <p class="text-gray-600 text-sm">
                {{ getVerificationDescription(type) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'
import { formatDate as formatDateValue } from '@/utils/formatters'

const props = defineProps({
  verification: Object,
  latestRequest: Object,
  verificationTypes: Array
})

const formatDate = (date) => {
  return formatDateValue(date)
}

const formatVerificationType = (type) => {
  const types = { 'phone': 'Phone Verification', 'nid': 'National ID', 'business': 'Business Verification' }
  return types[type] || type
}

const getVerificationDescription = (type) => {
  const descriptions = {
    'phone': 'Quick phone number verification - takes 5 minutes',
    'nid': 'Government ID verification - most secure option',
    'business': 'Complete business verification including documents'
  }
  return descriptions[type] || 'Verification type'
}
</script>
