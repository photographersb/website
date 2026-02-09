<template>
  <div class="quote-send-modal">
    <div
      class="modal-overlay"
      @click="$emit('close')"
    />
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="text-2xl font-bold text-gray-800">
          Send Custom Quote
        </h2>
        <button
          class="close-btn"
          @click="$emit('close')"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <form
        class="modal-body"
        @submit.prevent="submitQuote"
      >
        <!-- Client Info -->
        <div class="client-info-card">
          <h3 class="font-semibold text-lg mb-2">
            Client: {{ inquiry.user?.name || 'Unknown' }}
          </h3>
          <p class="text-sm text-gray-600">
            {{ inquiry.event_type }} • {{ inquiry.event_date }}
          </p>
          <p class="text-sm text-gray-600 mt-1">
            {{ inquiry.message }}
          </p>
        </div>

        <!-- Package Selection (Optional) -->
        <div class="form-group">
          <label>Select Package (Optional)</label>
          <select
            v-model="form.package_id"
            class="form-control"
          >
            <option :value="null">
              Custom Quote (No Package)
            </option>
            <option
              v-for="pkg in packages"
              :key="pkg.id"
              :value="pkg.id"
            >
              {{ pkg.name }} - ৳{{ formatNumber(pkg.price) }}
            </option>
          </select>
        </div>

        <!-- Amount -->
        <div class="form-group">
          <label>Quote Amount (৳) <span class="text-red-500">*</span></label>
          <input 
            v-model="form.amount" 
            type="number" 
            class="form-control"
            placeholder="Enter amount in BDT"
            min="100"
            max="1000000"
            required
          >
          <p class="text-sm text-gray-500 mt-1">
            Client will pay 30% advance: ৳{{ formatNumber(advanceAmount) }}
          </p>
        </div>

        <!-- Description -->
        <div class="form-group">
          <label>Quote Description <span class="text-red-500">*</span></label>
          <textarea 
            v-model="form.description" 
            class="form-control"
            rows="4"
            placeholder="Describe what's included in this quote..."
            minlength="20"
            maxlength="2000"
            required
          />
          <p class="text-sm text-gray-500 mt-1">
            {{ form.description.length }}/2000 characters
          </p>
        </div>

        <!-- Deliverables -->
        <div class="form-group">
          <label>Deliverables</label>
          <div
            v-for="(deliverable, index) in form.deliverables"
            :key="index"
            class="flex gap-2 mb-2"
          >
            <input 
              v-model="deliverable.item" 
              type="text" 
              class="form-control flex-1"
              placeholder="e.g., High-res edited photos"
            >
            <input 
              v-model="deliverable.quantity" 
              type="number" 
              class="form-control w-20"
              placeholder="Qty"
              min="1"
            >
            <button
              type="button"
              class="btn-danger"
              @click="removeDeliverable(index)"
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
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
          <button
            type="button"
            class="btn-secondary mt-2"
            @click="addDeliverable"
          >
            + Add Deliverable
          </button>
        </div>

        <!-- Validity -->
        <div class="form-group">
          <label>Quote Valid For (Days) <span class="text-red-500">*</span></label>
          <select
            v-model="form.validity_days"
            class="form-control"
            required
          >
            <option :value="3">
              3 Days
            </option>
            <option :value="7">
              1 Week
            </option>
            <option :value="14">
              2 Weeks
            </option>
            <option :value="30">
              1 Month
            </option>
          </select>
          <p class="text-sm text-gray-500 mt-1">
            Expires on: {{ expiryDate }}
          </p>
        </div>

        <!-- Terms & Conditions -->
        <div class="form-group">
          <label>Terms & Conditions (Optional)</label>
          <textarea 
            v-model="form.terms_conditions" 
            class="form-control"
            rows="3"
            placeholder="Add any special terms, cancellation policy, etc."
            maxlength="5000"
          />
        </div>

        <!-- Error Message -->
        <div
          v-if="error"
          class="alert alert-error"
        >
          {{ error }}
        </div>

        <!-- Submit Button -->
        <div class="flex gap-3 mt-6">
          <button
            type="button"
            class="btn-secondary flex-1"
            @click="$emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="btn-primary flex-1"
          >
            <span v-if="loading">Sending...</span>
            <span v-else>Send Quote</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { formatDate as formatDateValue, formatNumber } from '../utils/formatters';

export default {
  name: 'QuoteSendModal',
  props: {
    inquiry: {
      type: Object,
      required: true,
    },
    packages: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['close', 'sent'],
  setup(props, { emit }) {
    const loading = ref(false);
    const error = ref('');

    const form = ref({
      package_id: null,
      amount: '',
      description: '',
      validity_days: 7,
      terms_conditions: '',
      deliverables: [
        { item: '', quantity: 1 },
      ],
    });

    const advanceAmount = computed(() => {
      return Math.round(form.value.amount * 0.3);
    });

    const expiryDate = computed(() => {
      const date = new Date();
      date.setDate(date.getDate() + parseInt(form.value.validity_days));
      return formatDateValue(date);
    });

    const addDeliverable = () => {
      form.value.deliverables.push({ item: '', quantity: 1 });
    };

    const removeDeliverable = (index) => {
      form.value.deliverables.splice(index, 1);
    };

    const submitQuote = async () => {
      loading.value = true;
      error.value = '';

      try {
        // Filter out empty deliverables
        const deliverables = form.value.deliverables.filter(d => d.item.trim() !== '');

        const response = await fetch(`/api/v1/photographer/quotes/${props.inquiry.id}/send`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
          },
          body: JSON.stringify({
            ...form.value,
            deliverables: deliverables.length > 0 ? deliverables : null,
          }),
        });

        const data = await response.json();

        if (data.status === 'success') {
          emit('sent', data.data);
          emit('close');
        } else {
          error.value = data.message || 'Failed to send quote';
        }
      } catch (err) {
        error.value = 'Network error. Please try again.';
        console.error('Quote send error:', err);
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      loading,
      error,
      advanceAmount,
      expiryDate,
      addDeliverable,
      removeDeliverable,
      submitQuote,
      formatNumber,
    };
  },
};
</script>

<style scoped>
.quote-send-modal {
  @apply fixed inset-0 z-50 flex items-center justify-center p-4;
}

.modal-overlay {
  @apply absolute inset-0 bg-black bg-opacity-50;
}

.modal-content {
  @apply relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden z-10;
}

.modal-header {
  @apply flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-burgundy-50 to-white;
}

.close-btn {
  @apply text-gray-500 hover:text-gray-700 transition-colors;
}

.modal-body {
  @apply p-6 overflow-y-auto max-h-[calc(90vh-80px)];
}

.client-info-card {
  @apply bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6;
}

.form-group {
  @apply mb-4;
}

.form-group label {
  @apply block text-sm font-medium text-gray-700 mb-2;
}

.form-control {
  @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent;
}

.btn-primary {
  @apply px-6 py-3 bg-burgundy-600 text-white font-semibold rounded-lg hover:bg-burgundy-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed;
}

.btn-secondary {
  @apply px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors;
}

.btn-danger {
  @apply p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors;
}

.alert-error {
  @apply bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg;
}
</style>
