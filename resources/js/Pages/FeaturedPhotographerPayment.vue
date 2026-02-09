<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <button
          class="flex items-center gap-2 text-gray-600 hover:text-gray-900"
          @click="$router.back()"
        >
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
              d="M15 19l-7-7 7-7"
            />
          </svg>
          Back
        </button>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">
          Featured Photographer Payment
        </h1>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Order Summary -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
          Order Summary
        </h2>
        
        <div class="space-y-3 pb-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <span class="text-gray-700">Package Type:</span>
            <span class="font-semibold text-gray-900">{{ featured.package_tier }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-700">Category:</span>
            <span class="text-gray-900">{{ featured.category || 'All Categories' }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-700">Location:</span>
            <span class="text-gray-900">{{ featured.location || 'All Locations' }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-700">Duration:</span>
            <span class="text-gray-900">{{ formatDate(featured.start_date) }} - {{ formatDate(featured.end_date) }}</span>
          </div>
        </div>

        <div class="mt-4 pt-4">
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-700">Subtotal:</span>
            <span class="text-gray-900">৳{{ formatNumber(amount) }}</span>
          </div>
          <div class="flex justify-between items-center mb-4">
            <span class="text-gray-700">Tax/Fee:</span>
            <span class="text-gray-900">৳0</span>
          </div>
          <div class="flex justify-between items-center pt-4 border-t-2 border-gray-900">
            <span class="text-lg font-bold text-gray-900">Total Amount:</span>
            <span class="text-2xl font-bold text-burgundy-600">৳{{ formatNumber(amount) }}</span>
          </div>
        </div>
      </div>

      <!-- Payment Methods -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">
          Select Payment Method
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <!-- bKash Option -->
          <button
            :class="[
              'p-6 border-2 rounded-lg transition-all',
              selectedMethod === 'bkash'
                ? 'border-burgundy-600 bg-burgundy-50'
                : 'border-gray-200 bg-white hover:border-burgundy-300'
            ]"
            @click="selectPaymentMethod('bkash')"
          >
            <div class="flex items-start gap-3">
              <div
                class="w-6 h-6 mt-1 rounded border-2"
                :class="selectedMethod === 'bkash' ? 'border-burgundy-600 bg-burgundy-600' : 'border-gray-300'"
              >
                <svg
                  v-if="selectedMethod === 'bkash'"
                  class="w-4 h-4 text-white"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="text-left">
                <h3 class="font-bold text-gray-900">
                  bKash
                </h3>
                <p class="text-sm text-gray-600">
                  Mobile money payment
                </p>
                <p class="text-sm font-semibold text-burgundy-600 mt-1">
                  Instant Payment
                </p>
              </div>
            </div>
          </button>

        </div>

        <!-- Payment Info -->
        <div
          v-if="selectedMethod"
          class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg"
        >
          <p class="text-sm text-blue-800">
            <svg
              class="w-4 h-4 inline mr-2"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              />
            </svg>
            Your featured listing will be activated immediately after successful payment.
          </p>
        </div>

        <!-- Submit Button -->
        <button
          :disabled="!selectedMethod || processing"
          :class="[
            'w-full py-3 px-6 rounded-lg font-bold text-white transition-all',
            selectedMethod && !processing
              ? 'bg-gradient-to-r from-burgundy-600 to-burgundy-700 hover:from-burgundy-700 hover:to-burgundy-800 cursor-pointer'
              : 'bg-gray-400 cursor-not-allowed'
          ]"
          @click="processPayment"
        >
          <span v-if="!processing">Continue to bKash</span>
          <span
            v-else
            class="flex items-center justify-center gap-2"
          >
            <svg
              class="w-4 h-4 animate-spin"
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
            Processing...
          </span>
        </button>
      </div>

      <!-- Security Info -->
      <div class="mt-8 text-center text-sm text-gray-600">
        <svg
          class="w-5 h-5 inline mr-2 text-green-600"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
            clip-rule="evenodd"
          />
        </svg>
        Your payment information is secure and encrypted.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api'
import { formatDate as formatDateValue, formatNumber } from '@/utils/formatters'

const route = useRoute()
const selectedMethod = ref('')
const processing = ref(false)
const featured = ref(null)

const amount = computed(() => {
  if (!featured.value) return 0
  const prices = {
    'Starter': 999,
    'Professional': 2499,
    'Enterprise': 5999,
  }
  return prices[featured.value.package_tier] || 999
})

const selectPaymentMethod = (method) => {
  selectedMethod.value = method
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const processPayment = async () => {
  if (!selectedMethod.value || !featured.value) return

  processing.value = true
  try {
    const response = await api.post('/featured-photographers/payments/initiate', {
      featured_photographer_id: featured.value.id,
      payment_method: selectedMethod.value,
    })

    if (selectedMethod.value === 'bkash') {
      window.location.href = response.data.checkout_url
    }
  } catch (error) {
    console.error('Payment error:', error)
    alert('Error processing payment. Please try again.')
  } finally {
    processing.value = false
  }
}

onMounted(async () => {
  try {
    const id = route.params.id
    const response = await api.get(`/featured-photographers/${id}`)
    featured.value = response.data
  } catch (error) {
    console.error('Error loading featured photographer:', error)
    alert('Error loading featured photographer')
  }
})
</script>

<style scoped>
/* Add smooth transitions */
button {
  transition: all 0.3s ease;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
