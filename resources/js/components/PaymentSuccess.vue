<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-3xl mx-auto">
      <!-- Success Card -->
      <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Success Icon -->
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
        <p class="text-gray-600 mb-8">Thank you for your payment. Your booking has been confirmed.</p>

        <!-- Transaction Details -->
        <div v-if="transaction" class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
          <h2 class="text-lg font-semibold mb-4">Transaction Details</h2>
          
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Transaction ID:</span>
              <span class="font-medium">{{ transaction.reference_id }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Amount Paid:</span>
              <span class="font-medium">৳{{ Number(transaction.amount).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Payment Method:</span>
              <span class="font-medium capitalize">{{ formatPaymentMethod(transaction.payment_method) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Date:</span>
              <span class="font-medium">{{ formatDate(transaction.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Status:</span>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                {{ transaction.status }}
              </span>
            </div>
          </div>

          <!-- Booking Info -->
          <div v-if="transaction.booking" class="mt-6 pt-6 border-t">
            <h3 class="font-semibold mb-3">Booking Information</h3>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">Photographer:</span>
                <span class="font-medium">{{ transaction.booking.photographer?.user?.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Event Date:</span>
                <span class="font-medium">{{ formatDate(transaction.booking.event_date) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Location:</span>
                <span class="font-medium">{{ transaction.booking.location }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading transaction details...</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            to="/bookings"
            class="px-6 py-3 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
          >
            View My Bookings
          </router-link>
          <router-link
            to="/"
            class="px-6 py-3 border-2 border-burgundy text-burgundy rounded-lg hover:bg-burgundy hover:text-white transition-colors"
          >
            Return to Home
          </router-link>
        </div>

        <!-- Receipt Download -->
        <button
          v-if="transaction"
          @click="downloadReceipt"
          class="mt-4 text-burgundy hover:text-burgundy-dark transition-colors"
        >
          <span class="flex items-center gap-2 justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download Receipt
          </span>
        </button>
      </div>

      <!-- Email Notification -->
      <div class="mt-6 text-center text-sm text-gray-600">
        <p>A confirmation email has been sent to your registered email address.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api';

const route = useRoute();
const transaction = ref(null);
const loading = ref(true);

onMounted(async () => {
  const transactionId = route.query.transaction;
  
  if (transactionId) {
    try {
      const response = await api.get(`/payments/transactions/${transactionId}`);
      transaction.value = response.data.data;
    } catch (error) {
      console.error('Failed to load transaction:', error);
    } finally {
      loading.value = false;
    }
  } else {
    loading.value = false;
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
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const downloadReceipt = () => {
  // Generate a simple receipt (in production, this should be a PDF from backend)
  const receiptData = `
PHOTOGRAPHER SB - PAYMENT RECEIPT
=================================

Transaction ID: ${transaction.value.reference_id}
Date: ${formatDate(transaction.value.created_at)}
Amount: ৳${Number(transaction.value.amount).toLocaleString()}
Payment Method: ${formatPaymentMethod(transaction.value.payment_method)}
Status: ${transaction.value.status.toUpperCase()}

Photographer: ${transaction.value.booking?.photographer?.user?.name || 'N/A'}
Event Date: ${transaction.value.booking?.event_date ? formatDate(transaction.value.booking.event_date) : 'N/A'}
Location: ${transaction.value.booking?.location || 'N/A'}

=================================
Thank you for choosing Photographer SB!
Across Somogro Bangladesh
  `.trim();

  const blob = new Blob([receiptData], { type: 'text/plain' });
  const url = window.URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `receipt-${transaction.value.reference_id}.txt`;
  link.click();
  window.URL.revokeObjectURL(url);
};
</script>
