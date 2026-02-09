<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
      <h1 class="text-3xl font-bold mb-8">
        Payment Checkout
      </h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Booking Summary -->
        <div class="md:col-span-2">
          <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">
              Booking Details
            </h2>
            
            <div
              v-if="booking"
              class="space-y-4"
            >
              <div class="flex items-center gap-4 pb-4 border-b">
                <img
                  :src="booking.photographer.avatar || '/images/placeholder.svg'"
                  :alt="booking.photographer.user?.name || 'Photographer'"
                  class="w-16 h-16 rounded-full object-cover"
                >
                <div>
                  <h3 class="font-bold">
                    {{ booking.photographer.user?.name || 'Unknown' }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ booking.photographer.bio }}
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <p class="text-gray-600">
                    Event Date
                  </p>
                  <p class="font-semibold">
                    {{ formatDate(booking.event_date) }}
                  </p>
                </div>
                <div>
                  <p class="text-gray-600">
                    Location
                  </p>
                  <p class="font-semibold">
                    {{ booking.event_location }}
                  </p>
                </div>
                <div>
                  <p class="text-gray-600">
                    Guest Count
                  </p>
                  <p class="font-semibold">
                    {{ booking.guest_count }} guests
                  </p>
                </div>
                <div v-if="booking.package">
                  <p class="text-gray-600">
                    Package
                  </p>
                  <p class="font-semibold">
                    {{ booking.package?.name || 'N/A' }}
                  </p>
                </div>
              </div>
            </div>

            <div
              v-else
              class="text-center py-8 text-gray-600"
            >
              Loading booking details...
            </div>
          </div>

          <!-- Payment Method Selection -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">
              Payment Method
            </h2>

            <div class="space-y-3">
              <!-- SSLCommerz Card Payment -->
              <label
                class="flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-burgundy transition"
                :class="paymentMethod === 'sslcommerz' ? 'border-burgundy bg-[#F9E5EA]' : 'border-gray-200'"
              >
                <input
                  v-model="paymentMethod"
                  type="radio"
                  value="sslcommerz"
                  class="w-5 h-5 text-burgundy"
                >
                <div class="flex-1">
                  <p class="font-semibold">Credit/Debit Card</p>
                  <p class="text-sm text-gray-600">Visa, Mastercard, American Express</p>
                </div>
                <div class="flex gap-2">
                  <img
                    src="/images/placeholder.svg"
                    alt="Visa"
                    class="h-6"
                  >
                  <img
                    src="/images/placeholder.svg"
                    alt="Mastercard"
                    class="h-6"
                  >
                </div>
              </label>

              <!-- bKash -->
              <label
                class="flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-burgundy transition"
                :class="paymentMethod === 'bkash' ? 'border-burgundy bg-[#F9E5EA]' : 'border-gray-200'"
              >
                <input
                  v-model="paymentMethod"
                  type="radio"
                  value="bkash"
                  class="w-5 h-5 text-burgundy"
                >
                <div class="flex-1">
                  <p class="font-semibold">bKash</p>
                  <p class="text-sm text-gray-600">Pay with bKash mobile wallet</p>
                </div>
                <div class="bg-[#E2136E] text-white px-3 py-1 rounded font-bold text-sm">
                  bKash
                </div>
              </label>

              <!-- Nagad -->
              <label
                class="flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-burgundy transition"
                :class="paymentMethod === 'nagad' ? 'border-burgundy bg-[#F9E5EA]' : 'border-gray-200'"
              >
                <input
                  v-model="paymentMethod"
                  type="radio"
                  value="nagad"
                  class="w-5 h-5 text-burgundy"
                >
                <div class="flex-1">
                  <p class="font-semibold">Nagad</p>
                  <p class="text-sm text-gray-600">Pay with Nagad mobile wallet</p>
                </div>
                <div class="bg-[#ED1C24] text-white px-3 py-1 rounded font-bold text-sm">
                  Nagad
                </div>
              </label>

              <!-- Bank Transfer -->
              <label
                class="flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-burgundy transition"
                :class="paymentMethod === 'bank' ? 'border-burgundy bg-[#F9E5EA]' : 'border-gray-200'"
              >
                <input
                  v-model="paymentMethod"
                  type="radio"
                  value="bank"
                  class="w-5 h-5 text-burgundy"
                >
                <div class="flex-1">
                  <p class="font-semibold">Bank Transfer</p>
                  <p class="text-sm text-gray-600">Direct bank transfer</p>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="md:col-span-1">
          <div class="bg-white rounded-lg shadow p-6 sticky top-4">
            <h2 class="text-xl font-bold mb-4">
              Order Summary
            </h2>

            <div class="space-y-3 mb-4 pb-4 border-b">
              <div class="flex justify-between">
                <span class="text-gray-600">Service Fee</span>
                <span class="font-semibold">৳{{ serviceFee }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Advance Payment (30%)</span>
                <span class="font-semibold">৳{{ advanceAmount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Platform Fee</span>
                <span class="font-semibold">৳{{ platformFee }}</span>
              </div>
            </div>

            <div class="flex justify-between text-lg font-bold mb-6">
              <span>Total Amount</span>
              <span class="text-burgundy">৳{{ totalAmount }}</span>
            </div>

            <button
              :disabled="!paymentMethod || processing"
              class="w-full bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] disabled:opacity-50 disabled:cursor-not-allowed"
              @click="processPayment"
            >
              {{ processing ? 'Processing...' : 'Proceed to Payment' }}
            </button>

            <p class="text-xs text-gray-500 mt-4 text-center">
              🔒 Secure payment powered by SSLCommerz
            </p>

            <!-- Remaining Balance -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
              <p class="text-sm font-semibold text-blue-900">
                Remaining Balance
              </p>
              <p class="text-2xl font-bold text-blue-700">
                ৳{{ remainingAmount }}
              </p>
              <p class="text-xs text-blue-600 mt-1">
                To be paid after service completion
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div
        v-if="error"
        class="mt-6 p-4 bg-red-100 text-red-700 rounded-lg"
      >
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import { formatDate as formatDateValue } from '../utils/formatters';

const route = useRoute();
const router = useRouter();

const booking = ref(null);
const paymentMethod = ref('sslcommerz');
const processing = ref(false);
const error = ref('');

const serviceFee = computed(() => {
  if (!booking.value) return 0;
  return booking.value.package?.base_price || booking.value.budget_max || 0;
});

const advanceAmount = computed(() => {
  return Math.round(serviceFee.value * 0.3); // 30% advance
});

const platformFee = computed(() => {
  return Math.round(serviceFee.value * 0.05); // 5% platform fee
});

const totalAmount = computed(() => {
  return advanceAmount.value + platformFee.value;
});

const remainingAmount = computed(() => {
  return serviceFee.value - advanceAmount.value;
});

const fetchBooking = async () => {
  try {
    const { data } = await api.get(`/bookings/${route.params.bookingId}`);
    if (data.status === 'success') {
      booking.value = data.data;
    }
  } catch (err) {
    error.value = 'Failed to load booking details';
    console.error(err);
  }
};

const processPayment = async () => {
  processing.value = true;
  error.value = '';

  try {
    const { data } = await api.post('/payments/initiate', {
      booking_id: booking.value.id,
      payment_method: paymentMethod.value,
      amount: totalAmount.value,
    });

    if (data.status === 'success') {
      // Redirect to payment gateway
      if (data.data.gateway_url) {
        window.location.href = data.data.gateway_url;
      } else {
        // For bank transfer, show instructions
        router.push(`/payment/${data.data.transaction_id}/instructions`);
      }
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Payment initiation failed';
    processing.value = false;
  }
};

const formatDate = (date) => {
  return formatDateValue(date);
};

onMounted(() => {
  fetchBooking();
});
</script>
