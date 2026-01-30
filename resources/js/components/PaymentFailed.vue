<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-3xl mx-auto">
      <!-- Failure Card -->
      <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Error Icon -->
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Failed</h1>
        <p class="text-gray-600 mb-8">
          We couldn't process your payment. Please try again or use a different payment method.
        </p>

        <!-- Transaction Details -->
        <div v-if="transaction" class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
          <h2 class="text-lg font-semibold mb-4">Transaction Details</h2>
          
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Transaction ID:</span>
              <span class="font-medium">{{ transaction.reference_id }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Amount:</span>
              <span class="font-medium">৳{{ Number(transaction.amount).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Payment Method:</span>
              <span class="font-medium capitalize">{{ formatPaymentMethod(transaction.payment_method) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Status:</span>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                Failed
              </span>
            </div>
          </div>
        </div>

        <!-- Common Reasons -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8 text-left">
          <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            Common Reasons for Payment Failure
          </h3>
          <ul class="space-y-2 text-sm text-gray-700">
            <li class="flex items-start gap-2">
              <span class="text-burgundy">•</span>
              <span>Insufficient funds in your account</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-burgundy">•</span>
              <span>Incorrect card details or PIN</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-burgundy">•</span>
              <span>Card expired or blocked</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-burgundy">•</span>
              <span>Network or connectivity issues</span>
            </li>
            <li class="flex items-start gap-2">
              <span class="text-burgundy">•</span>
              <span>Payment gateway temporarily unavailable</span>
            </li>
          </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button
            @click="retryPayment"
            class="px-6 py-3 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
          >
            Try Again
          </button>
          <router-link
            to="/"
            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-burgundy hover:text-burgundy transition-colors"
          >
            Return to Home
          </router-link>
        </div>

        <!-- Support Link -->
        <div class="mt-8 pt-8 border-t">
          <p class="text-sm text-gray-600 mb-2">Need help with your payment?</p>
          <a href="mailto:support@photographersb.com" class="text-burgundy hover:text-burgundy-dark font-medium">
            Contact Support
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';

const route = useRoute();
const router = useRouter();
const transaction = ref(null);

onMounted(async () => {
  const transactionId = route.query.transaction;
  
  if (transactionId) {
    try {
      const response = await api.get(`/payments/transactions/${transactionId}`);
      transaction.value = response.data.data;
    } catch (error) {
      console.error('Failed to load transaction:', error);
    }
  }
});

const formatPaymentMethod = (method) => {
  const methods = {
    'card': 'Credit/Debit Card (SSLCommerz)',
    'bkash': 'bKash',
    'nagad': 'Nagad',
    'bank_transfer': 'Bank Transfer'
  };
  return methods[method] || method;
};

const retryPayment = () => {
  if (transaction.value?.booking) {
    router.push(`/payment/${transaction.value.booking.id}`);
  } else {
    router.push('/');
  }
};
</script>
