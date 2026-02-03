<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <!-- Header -->
        <div :class="['px-6 py-4 border-b', headerClass]">
          <h3 class="text-lg font-semibold" :class="headerTextClass">
            {{ actionTitle }}
          </h3>
        </div>

        <!-- Content -->
        <div class="px-6 py-4">
          <!-- Booking Details Summary -->
          <div class="mb-4 p-3 bg-gray-50 rounded-lg text-sm">
            <p class="font-semibold text-gray-900">{{ booking.event_type }}</p>
            <p class="text-gray-600">{{ formatDate(booking.event_date) }}</p>
            <p class="text-gray-600">Client: {{ booking.client.name }}</p>
            <p class="font-semibold text-gray-900 mt-2">{{ formatCurrency(booking.total_amount) }}</p>
          </div>

          <!-- Confirmation Message -->
          <p class="text-gray-700 mb-4">{{ confirmationMessage }}</p>

          <!-- Reason/Notes Textarea -->
          <textarea
            v-model="userInput"
            :placeholder="inputPlaceholder"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent resize-none"
          ></textarea>

          <!-- Additional Info -->
          <div v-if="action === 'decline'" class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg text-sm">
            <p class="text-orange-800">
              <strong>Note:</strong> The client will be notified of your decline with the reason provided.
            </p>
          </div>

          <div v-if="action === 'accept'" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg text-sm">
            <p class="text-green-800">
              <strong>Note:</strong> The client will receive a confirmation. Make sure you're available on the scheduled date!
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t bg-gray-50 flex gap-3">
          <button
            @click="$emit('cancel')"
            :disabled="loading"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Cancel
          </button>
          <button
            @click="handleConfirm"
            :disabled="loading || !userInput.trim()"
            :class="[
              'flex-1 px-4 py-2 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition',
              action === 'accept' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'
            ]"
          >
            <span v-if="!loading">{{ actionButtonText }}</span>
            <span v-else>Processing...</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  booking: {
    type: Object,
    required: true
  },
  action: {
    type: String,
    enum: ['accept', 'decline'],
    required: true
  }
});

const emit = defineEmits(['confirm', 'cancel']);

const userInput = ref('');
const loading = ref(false);

const actionTitle = computed(() => {
  return props.action === 'accept' 
    ? 'Accept Booking?' 
    : 'Decline Booking?';
});

const confirmationMessage = computed(() => {
  return props.action === 'accept'
    ? 'You are about to accept this booking request. Please confirm you are available for this date and time.'
    : 'You are about to decline this booking. The client will be notified with the reason you provide.';
});

const inputPlaceholder = computed(() => {
  return props.action === 'accept'
    ? 'Any notes for the client (optional)...'
    : 'Tell the client why you\'re declining... (required)';
});

const actionButtonText = computed(() => {
  return props.action === 'accept' ? 'Accept Booking' : 'Decline Booking';
});

const headerClass = computed(() => {
  if (props.action === 'accept') {
    return 'bg-green-50 border-green-200';
  }
  return 'bg-red-50 border-red-200';
});

const headerTextClass = computed(() => {
  if (props.action === 'accept') {
    return 'text-green-900';
  }
  return 'text-red-900';
});

const handleConfirm = async () => {
  loading.value = true;
  try {
    await emit('confirm', userInput.value);
  } finally {
    loading.value = false;
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
};
</script>

<style scoped>
/* Scoped styles if needed */
</style>
