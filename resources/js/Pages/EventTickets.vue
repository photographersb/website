<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="mb-6">
        <button
          class="text-sm text-burgundy hover:text-rose-800 font-medium"
          @click="router.push(`/events/${route.params.slug}`)"
        >
          ← Back to event
        </button>
      </div>

      <div v-if="loading" class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Loading tickets...</p>
      </div>

      <div v-else-if="!event" class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Event not found.</p>
      </div>

      <div v-else class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                {{ event.title }}
              </h1>
              <p class="text-sm text-gray-600 mt-1">
                Select your ticket and choose a payment method.
              </p>
              <p v-if="displayStatusMessage" class="mt-2 text-sm font-medium" :class="displayStatusTone">
                {{ displayStatusMessage }}
              </p>
            </div>
            <div class="rounded-lg border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800">
              <p class="font-semibold">Secure checkout</p>
              <p class="text-xs">Manual payments are reviewed by admins.</p>
            </div>
          </div>
        </div>

          <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
          <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Available Tickets
              </h2>

              <div v-if="tickets.length === 0" class="text-gray-600">
                No tickets are configured for this event yet.
              </div>

              <div v-else class="space-y-4">
                <label
                  v-for="ticket in tickets"
                  :key="ticket.id"
                  class="flex items-start gap-4 border rounded-lg p-4 cursor-pointer"
                  :class="selectedTicketId === ticket.id ? 'border-burgundy bg-rose-50/40' : 'border-gray-200'"
                >
                  <input
                    type="radio"
                    class="mt-1"
                    :value="ticket.id"
                    v-model="selectedTicketId"
                    :disabled="!isTicketSelectable(ticket)"
                  >
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <p class="font-semibold text-gray-900">
                        {{ ticket.title }}
                      </p>
                      <p class="text-burgundy font-semibold">
                        {{ formatPrice(ticket.price, event.currency || 'BDT') }}
                      </p>
                    </div>
                    <p v-if="ticket.description" class="text-sm text-gray-600 mt-1">
                      {{ ticket.description }}
                    </p>
                    <p class="text-xs mt-2" :class="ticketStatusTone(ticket)">
                      {{ ticketStatusLabel(ticket) }}
                    </p>
                  </div>
                </label>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Payment Method
              </h2>

              <div class="grid gap-3 sm:grid-cols-2">
                <label
                  v-for="method in paymentMethods"
                  :key="method.value"
                  class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer"
                  :class="paymentMethod === method.value ? 'border-burgundy bg-rose-50/40' : 'border-gray-200'"
                >
                  <input
                    type="radio"
                    class="mt-1"
                    :value="method.value"
                    v-model="paymentMethod"
                  >
                  <div>
                    <p class="font-semibold text-gray-900">
                      {{ method.label }}
                    </p>
                    <p class="text-xs text-gray-600">
                      {{ method.note }}
                    </p>
                  </div>
                </label>
              </div>

              <div v-if="isProofMethod" class="mt-5 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sender Number</label>
                  <input
                    v-model="senderNumber"
                    type="text"
                    placeholder="e.g., 01XXXXXXXXX"
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
                  <input
                    v-model="transactionId"
                    type="text"
                    placeholder="Transaction reference"
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Payment Screenshot</label>
                  <input
                    type="file"
                    accept="image/*"
                    @change="handleScreenshot"
                    class="w-full text-sm"
                  >
                </div>
              </div>

              <div v-else-if="isManualSimple" class="mt-5 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sender Number</label>
                  <input
                    v-model="senderNumber"
                    type="text"
                    placeholder="e.g., 01XXXXXXXXX"
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Reference (optional)</label>
                  <input
                    v-model="referenceNote"
                    type="text"
                    placeholder="Any reference note"
                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                  >
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Order Summary
              </h2>
              <div class="space-y-4 text-sm text-gray-700">
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Ticket</span>
                  <span class="font-medium">{{ selectedTicket?.title || 'Select a ticket' }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Quantity</span>
                  <div class="flex items-center gap-3">
                    <button
                      class="h-9 w-9 rounded-lg border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                      :disabled="quantity <= 1"
                      @click="decrementQuantity"
                    >
                      −
                    </button>
                    <span class="text-lg font-semibold w-8 text-center">{{ quantity }}</span>
                    <button
                      class="h-9 w-9 rounded-lg border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                      :disabled="quantity >= maxQty"
                      @click="incrementQuantity"
                    >
                      +
                    </button>
                  </div>
                </div>

                <div class="border-t pt-4">
                  <div class="flex items-center justify-between">
                    <span class="text-base font-semibold">Total</span>
                    <span class="text-xl font-bold text-burgundy">{{ formatPrice(totalAmount, event.currency || 'BDT') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
              <button
                class="w-full px-6 py-3 rounded-lg font-semibold text-white bg-burgundy hover:bg-rose-800 disabled:bg-gray-300 disabled:text-gray-500 transition-colors"
                :disabled="!canSubmit || submitting"
                @click="submitPayment"
              >
                {{ submitting ? 'Submitting...' : 'Proceed to Payment' }}
              </button>
              <p class="mt-3 text-xs text-gray-500">
                You will receive a confirmation after payment verification.
              </p>
              <p v-if="submitDisabledReason" class="mt-2 text-xs text-amber-700">
                {{ submitDisabledReason }}
              </p>
              <p v-if="isRegistrationClosed" class="mt-3 text-xs text-amber-700">
                Registration deadline has passed.
              </p>
            </div>

            <div v-if="paymentGateway" class="bg-white rounded-lg shadow p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-3">
                SSLCommerz Response
              </h2>
              <p class="text-sm text-gray-600 mb-4">
                Click continue to open SSLCommerz checkout. If the redirect URL is missing, check your SSLCommerz credentials.
              </p>
              <div class="rounded-lg bg-gray-50 border border-gray-200 p-3 text-xs text-gray-700 break-all">
                <pre class="whitespace-pre-wrap">{{ JSON.stringify(paymentGateway, null, 2) }}</pre>
              </div>
              <button
                v-if="paymentGateway?.redirect_url"
                class="mt-4 w-full px-4 py-2 rounded-lg font-semibold text-white bg-burgundy hover:bg-rose-800"
                @click="openGateway(paymentGateway.redirect_url)"
              >
                Continue to SSLCommerz
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Toast
      v-if="toastVisible"
      :message="toastMessage"
      :type="toastType"
      @close="closeToast"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import Toast from '../components/ui/Toast.vue';
import { useApiError } from '../composables/useApiError';
import { formatNumber } from '../utils/formatters';

const route = useRoute();
const router = useRouter();

const {
  toastMessage,
  toastType,
  toastVisible,
  showToast,
  closeToast,
  handleApiError,
} = useApiError();

const event = ref(null);
const loading = ref(true);
const submitting = ref(false);
const lastSubmitAt = ref(0);
const selectedTicketId = ref(null);
const quantity = ref(1);
const paymentMethod = ref('card');
const senderNumber = ref('');
const transactionId = ref('');
const referenceNote = ref('');
const screenshotFile = ref(null);
const paymentGateway = ref(null);
const paymentStatusMessage = computed(() => {
  const status = String(route.query.status || '').toLowerCase();
  if (status === 'success') return 'Payment completed successfully.';
  if (status === 'failed') return 'Payment failed. Please try again.';
  if (status === 'cancelled') return 'Payment was cancelled.';
  return '';
});

const paymentStatusTone = computed(() => {
  const status = String(route.query.status || '').toLowerCase();
  if (status === 'success') return 'text-green-700';
  if (status === 'failed') return 'text-red-600';
  if (status === 'cancelled') return 'text-amber-700';
  return 'text-gray-700';
});

const submissionMessage = ref('');
const submissionTone = ref('text-green-700');
const displayStatusMessage = computed(() => submissionMessage.value || paymentStatusMessage.value);
const displayStatusTone = computed(() => submissionMessage.value ? submissionTone.value : paymentStatusTone.value);

const tickets = computed(() => Array.isArray(event.value?.tickets) ? event.value.tickets : []);
const selectedTicket = computed(() => tickets.value.find((ticket) => ticket.id === selectedTicketId.value));
const totalAmount = computed(() => {
  const unit = Number(selectedTicket.value?.price || 0);
  const qty = Number(quantity.value || 0);
  return Number.isFinite(unit * qty) ? unit * qty : 0;
});
const maxQty = computed(() => {
  if (!selectedTicket.value) return 1;
  const available = Number(selectedTicket.value.available_quantity ?? selectedTicket.value.quantity ?? 0);
  const limit = Number(event.value?.max_tickets_per_user || available || 1);
  const safeAvailable = Number.isFinite(available) ? Math.max(available, 1) : 1;
  const safeLimit = Number.isFinite(limit) ? Math.max(limit, 1) : safeAvailable;
  return Math.min(safeAvailable, safeLimit);
});
const maxTicketsLabel = computed(() => {
  if (!event.value?.max_tickets_per_user) return 'No limit';
  return `${event.value.max_tickets_per_user} per user`;
});
const isProofMethod = computed(() => ['bkash', 'nagad', 'rocket'].includes(paymentMethod.value));
const isManualSimple = computed(() => paymentMethod.value === 'manual');
const isManualMethod = computed(() => isProofMethod.value || isManualSimple.value);
const isRegistrationClosed = computed(() => {
  if (!event.value?.registration_deadline) return false;
  const deadline = new Date(event.value.registration_deadline);
  if (Number.isNaN(deadline.getTime())) return false;
  return deadline.getTime() < Date.now();
});
const canSubmit = computed(() => {
  if (!selectedTicketId.value || quantity.value < 1) return false;
  if (!isTicketSelectable(selectedTicket.value)) return false;
  if (isRegistrationClosed.value) return false;
  if (!isManualMethod.value) return true;
  if (isProofMethod.value) {
    return Boolean(senderNumber.value && transactionId.value && screenshotFile.value);
  }
  return Boolean(senderNumber.value);
});
const submitDisabledReason = computed(() => {
  if (submitting.value) return '';
  if (isRegistrationClosed.value) return 'Registration is closed for this event.';
  if (!selectedTicketId.value) return 'Select a ticket to continue.';
  if (!isTicketSelectable(selectedTicket.value)) return 'Selected ticket is not available.';
  if (quantity.value < 1) return 'Quantity must be at least 1.';
  if (isProofMethod.value && (!senderNumber.value || !transactionId.value || !screenshotFile.value)) {
    return 'Provide sender number, transaction ID, and screenshot.';
  }
  if (isManualSimple.value && !senderNumber.value) {
    return 'Provide sender number to continue.';
  }
  return '';
});

const paymentMethods = [
  { value: 'card', label: 'Card / SSLCommerz', note: 'Pay online with card or supported gateways.' },
  { value: 'bkash', label: 'bKash', note: 'Requires sender number, transaction ID, and screenshot.' },
  { value: 'nagad', label: 'Nagad', note: 'Requires sender number, transaction ID, and screenshot.' },
  { value: 'rocket', label: 'Rocket', note: 'Requires sender number, transaction ID, and screenshot.' },
  { value: 'manual', label: 'Manual Transfer', note: 'Sender number only. Reference is optional.' },
];

const formatPrice = (amount, currency) => {
  const value = Number(amount || 0);
  if (!Number.isFinite(value)) return `${currency} 0`;
  return `${currency} ${formatNumber(value)}`;
};

const ticketStatusLabel = (ticket) => {
  if (!ticket) return 'Availability unknown';
  if (ticket.is_sold_out) return 'Sold out';
  if (ticket.is_on_sale === false) return 'Sales closed';
  if (ticket.is_active === false) return 'Unavailable';
  if (typeof ticket.available_quantity === 'number') {
    return `${ticket.available_quantity} available`;
  }
  return 'On sale';
};

const ticketStatusTone = (ticket) => {
  if (!ticket) return 'text-gray-500';
  if (ticket.is_sold_out) return 'text-red-600';
  if (ticket.is_on_sale === false) return 'text-amber-700';
  if (ticket.is_active === false) return 'text-gray-500';
  return 'text-green-700';
};

const isTicketSelectable = (ticket) => {
  if (!ticket) return false;
  if (ticket.is_active === false) return false;
  if (ticket.is_sold_out) return false;
  if (ticket.is_on_sale === false) return false;
  return true;
};

const fetchEvent = async () => {
  loading.value = true;
  try {
    const { data } = await api.get(`/events/${route.params.slug}`);
    event.value = data.data || data.event || null;
    if (!selectedTicketId.value) {
      const firstAvailable = tickets.value.find((ticket) => isTicketSelectable(ticket));
      if (firstAvailable) {
        selectedTicketId.value = firstAvailable.id;
      }
    }
  } catch (error) {
    handleApiError(error, 'Failed to load event tickets');
    event.value = null;
  } finally {
    loading.value = false;
  }
};

watch([selectedTicketId, maxQty], () => {
  if (quantity.value < 1) quantity.value = 1;
  if (quantity.value > maxQty.value) quantity.value = maxQty.value;
});

const openGateway = (url) => {
  if (!url) return;
  window.location.href = url;
};

const handleScreenshot = (event) => {
  const file = event.target.files?.[0] || null;
  screenshotFile.value = file;
};

const incrementQuantity = () => {
  if (quantity.value < maxQty.value) {
    quantity.value++;
  }
};

const decrementQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const initiatePayment = async () => {
  if (submitting.value) return;
  if (!localStorage.getItem('user')) {
    router.push('/auth');
    return;
  }

  if (!event.value) {
    showToast('Event not loaded. Please refresh and try again.', 'error');
    return;
  }

  if (!selectedTicketId.value) {
    showToast('Please select a ticket before proceeding.', 'error');
    return;
  }

  submitting.value = true;
  try {
    const payload = {
      ticket_id: selectedTicketId.value,
      qty: quantity.value,
    };

    const { data } = await api.post(`/events/${event.value.id}/payments/initiate`, payload);

    if (data?.status === 'success' || data?.success) {
      paymentGateway.value = data?.payment_gateway || null;
      showToast('Payment initiated. Complete payment using the gateway details.', 'success');
      submissionMessage.value = 'Payment initiated. Complete payment using the gateway details.';
      submissionTone.value = 'text-green-700';
    } else {
      showToast('Unable to start payment. Please try again.', 'error');
    }
  } catch (error) {
    console.error('Payment initiation error:', error.response?.data || error.message);
    handleApiError(error, 'Failed to start payment');
  } finally {
    submitting.value = false;
  }
};

const submitManualPayment = async () => {
  if (submitting.value) return;
  if (!localStorage.getItem('user')) {
    router.push('/auth');
    return;
  }

  if (!event.value) return;

  // Validate sender number
  if (!senderNumber.value || senderNumber.value.trim() === '') {
    showToast('Please enter a sender number before submitting.', 'error');
    return;
  }

  submitting.value = true;
  try {
    const formData = new FormData();
    formData.append('ticket_id', selectedTicketId.value);
    formData.append('qty', quantity.value);
    formData.append('method', paymentMethod.value);
    formData.append('sender_number', senderNumber.value.trim());
    
    if (isProofMethod.value && transactionId.value) {
      formData.append('trx_id', transactionId.value.trim());
    }
    if (isManualSimple.value && referenceNote.value) {
      formData.append('trx_id', referenceNote.value.trim());
    }
    if (isProofMethod.value && screenshotFile.value) {
      formData.append('screenshot', screenshotFile.value);
    }

    const { data } = await api.post(`/events/${event.value.id}/payments/manual`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (data?.status === 'success' || data?.success) {
      const successMessage = 'Payment submitted successfully. Your payment is under review. You will receive confirmation after admin verification.';
      showToast(`✅ ${successMessage}`, 'success');
      submissionMessage.value = successMessage;
      submissionTone.value = 'text-green-700';
      // Reset form
      senderNumber.value = '';
      transactionId.value = '';
      referenceNote.value = '';
      screenshotFile.value = null;
      quantity.value = 1;
    } else {
      showToast('Unable to submit payment. Please try again.', 'error');
    }
  } catch (error) {
    console.error('Manual payment error:', error.response?.data || error.message);
    
    const errorMsg = error.response?.data?.message || error.response?.data?.error || 'Failed to submit payment';
    showToast(errorMsg, 'error');
    submissionMessage.value = errorMsg;
    submissionTone.value = 'text-red-600';
  } finally {
    submitting.value = false;
  }
};

const submitPayment = () => {
  const now = Date.now();
  if (now - lastSubmitAt.value < 1500) {
    showToast('Please wait a moment before trying again.', 'error');
    return;
  }
  lastSubmitAt.value = now;
  paymentGateway.value = null;
  if (isManualMethod.value) {
    submitManualPayment();
  } else {
    initiatePayment();
  }
};

onMounted(fetchEvent);
</script>
