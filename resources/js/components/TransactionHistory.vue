<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Transaction History</h1>
              <p class="mt-1 text-sm text-gray-600">View all your payment transactions</p>
            </div>

            <!-- Filters -->
            <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
              <select
                v-model="filters.status"
                @change="loadTransactions"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-burgundy"
              >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
                <option value="cancelled">Cancelled</option>
              </select>

              <select
                v-model="filters.paymentMethod"
                @change="loadTransactions"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-burgundy"
              >
                <option value="">All Methods</option>
                <option value="card">Card</option>
                <option value="bkash">bKash</option>
                <option value="nagad">Nagad</option>
                <option value="bank_transfer">Bank Transfer</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="py-12 text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-burgundy mx-auto"></div>
          <p class="text-gray-600 mt-4">Loading transactions...</p>
        </div>

        <!-- Transactions List -->
        <div v-else-if="transactions.length > 0" class="divide-y divide-gray-200">
          <div
            v-for="transaction in transactions"
            :key="transaction.id"
            class="px-6 py-5 hover:bg-gray-50 transition-colors"
          >
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
              <!-- Transaction Info -->
              <div class="flex-1">
                <div class="flex items-start gap-4">
                  <!-- Icon -->
                  <div
                    :class="[
                      'w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0',
                      getStatusColor(transaction.status).bg
                    ]"
                  >
                    <svg
                      class="w-6 h-6"
                      :class="getStatusColor(transaction.status).text"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      ></path>
                    </svg>
                  </div>

                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                      <h3 class="text-base font-semibold text-gray-900">
                        {{ formatPaymentMethod(transaction.payment_method) }}
                      </h3>
                      <span
                        :class="[
                          'px-3 py-1 rounded-full text-xs font-medium',
                          getStatusColor(transaction.status).badge
                        ]"
                      >
                        {{ transaction.status }}
                      </span>
                    </div>

                    <p class="text-sm text-gray-600 mb-1">
                      Transaction ID: {{ transaction.reference_id }}
                    </p>

                    <div v-if="transaction.booking" class="text-sm text-gray-500">
                      Booking with
                      <span class="font-medium text-gray-700">
                        {{ transaction.booking.photographer?.user?.name }}
                      </span>
                      •
                      {{ formatDate(transaction.booking.event_date) }}
                    </div>

                    <p class="text-xs text-gray-400 mt-1">
                      {{ formatDateTime(transaction.created_at) }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Amount & Actions -->
              <div class="flex items-center gap-6 lg:flex-shrink-0">
                <div class="text-right">
                  <p class="text-2xl font-bold text-gray-900">
                    ৳{{ Number(transaction.amount).toLocaleString() }}
                  </p>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ transaction.transaction_type }}
                  </p>
                </div>

                <router-link
                  :to="`/transaction/${transaction.reference_id}`"
                  class="px-4 py-2 text-burgundy border border-burgundy rounded-lg hover:bg-burgundy hover:text-white transition-colors text-sm font-medium"
                >
                  View Details
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-16 text-center">
          <svg
            class="w-16 h-16 text-gray-400 mx-auto mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
            ></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-1">No transactions found</h3>
          <p class="text-gray-600">You haven't made any payments yet.</p>
        </div>

        <!-- Pagination -->
        <div
          v-if="pagination.last_page > 1"
          class="px-6 py-4 border-t border-gray-200 flex items-center justify-between"
        >
          <p class="text-sm text-gray-600">
            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
          </p>

          <div class="flex gap-2">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              :class="[
                'px-4 py-2 border rounded-lg transition-colors',
                pagination.current_page === 1
                  ? 'border-gray-200 text-gray-400 cursor-not-allowed'
                  : 'border-burgundy text-burgundy hover:bg-burgundy hover:text-white'
              ]"
            >
              Previous
            </button>

            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              :class="[
                'px-4 py-2 border rounded-lg transition-colors',
                pagination.current_page === pagination.last_page
                  ? 'border-gray-200 text-gray-400 cursor-not-allowed'
                  : 'border-burgundy text-burgundy hover:bg-burgundy hover:text-white'
              ]"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import api from '../api';

const transactions = ref([]);
const loading = ref(true);

const filters = reactive({
  status: '',
  paymentMethod: '',
});

const pagination = ref({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0,
});

onMounted(() => {
  loadTransactions();
});

const loadTransactions = async (page = 1) => {
  loading.value = true;

  try {
    const params = {
      page,
      per_page: 10,
    };

    if (filters.status) params.status = filters.status;
    if (filters.paymentMethod) params.payment_method = filters.paymentMethod;

    const response = await api.get('/payments/transactions', { params });
    
    transactions.value = response.data.data.data;
    pagination.value = {
      current_page: response.data.data.current_page,
      last_page: response.data.data.last_page,
      from: response.data.data.from,
      to: response.data.data.to,
      total: response.data.data.total,
    };
  } catch (error) {
    console.error('Failed to load transactions:', error);
  } finally {
    loading.value = false;
  }
};

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadTransactions(page);
  }
};

const formatPaymentMethod = (method) => {
  const methods = {
    card: 'Credit/Debit Card (SSLCommerz)',
    bkash: 'bKash',
    nagad: 'Nagad',
    bank_transfer: 'Bank Transfer',
  };
  return methods[method] || method;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getStatusColor = (status) => {
  const colors = {
    pending: {
      bg: 'bg-yellow-100',
      text: 'text-yellow-600',
      badge: 'bg-yellow-100 text-yellow-800',
    },
    completed: {
      bg: 'bg-green-100',
      text: 'text-green-600',
      badge: 'bg-green-100 text-green-800',
    },
    failed: {
      bg: 'bg-red-100',
      text: 'text-red-600',
      badge: 'bg-red-100 text-red-800',
    },
    cancelled: {
      bg: 'bg-gray-100',
      text: 'text-gray-600',
      badge: 'bg-gray-100 text-gray-800',
    },
  };
  return colors[status] || colors.pending;
};
</script>
