<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-3xl mx-auto">
      <!-- Cancelled Card -->
      <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Cancel Icon -->
        <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Cancelled</h1>
        <p class="text-gray-600 mb-8">
          You have cancelled the payment process. Your booking is still pending.
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
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                Cancelled
              </span>
            </div>
          </div>

          <div v-if="transaction.booking" class="mt-6 pt-6 border-t">
            <h3 class="font-semibold mb-3">Booking Information</h3>
            <p class="text-sm text-gray-600 mb-3">
              Your booking is still reserved. Complete the payment to confirm your reservation.
            </p>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Photographer:</span>
                <span class="font-medium">{{ transaction.booking.photographer?.user?.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Event Date:</span>
                <span class="font-medium">{{ formatDate(transaction.booking.event_date) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-left">
          <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            What happens next?
          </h3>
          <p class="text-sm text-gray-700">
            Your booking reservation will be held for a limited time. Please complete the payment to confirm your booking before the reservation expires.
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button
            @click="retryPayment"
            class="px-6 py-3 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
          >
            Complete Payment
          </button>
          <router-link
            to="/bookings"
            class="px-6 py-3 border-2 border-burgundy text-burgundy rounded-lg hover:bg-burgundy hover:text-white transition-colors"
          >
            View My Bookings
          </router-link>
          <router-link
            to="/"
            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:border-burgundy hover:text-burgundy transition-colors"
          >
            Return to Home
          </router-link>
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const retryPayment = () => {
  if (transaction.value?.booking) {
    router.push(`/payment/${transaction.value.booking.id}`);
  } else {
    router.push('/');
  }
};
</script>
