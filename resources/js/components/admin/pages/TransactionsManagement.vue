<template>
  <AdminLayout 
    page-title="Transactions Management"
    page-description="View and manage all platform transactions"
    :show-breadcrumbs="true"
  >
    <BaseModal 
      :is-open="showModal"
      title="Transaction Details"
      :is-loading="false"
      @close="showModal = false"
    >
      <div class="space-y-3 text-sm">
        <p><strong>ID:</strong> {{ selectedTransaction?.transaction_id }}</p>
        <p><strong>User:</strong> {{ selectedTransaction?.user_name }}</p>
        <p><strong>Type:</strong> {{ selectedTransaction?.type }}</p>
        <p><strong>Amount:</strong> {{ formatCurrency(selectedTransaction?.amount) }}</p>
        <p><strong>Status:</strong> <span :class="`px-2 py-1 rounded text-xs font-medium ${getTxnStatusColor(selectedTransaction?.status)}`">{{ selectedTransaction?.status }}</span></p>
        <p><strong>Date:</strong> {{ formatDate(selectedTransaction?.created_at) }}</p>
      </div>
    </BaseModal>

    <div class="space-y-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by transaction ID or user..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
        <select
          v-model="typeFilter"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
        >
          <option value="">
            All Types
          </option>
          <option value="booking">
            Booking Payment
          </option>
          <option value="refund">
            Refund
          </option>
          <option value="withdrawal">
            Withdrawal
          </option>
        </select>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <div
          v-if="isLoading"
          class="p-8 text-center"
        >
          Loading transactions...
        </div>
        <div
          v-else-if="filteredTransactions.length === 0"
          class="p-12 text-center text-gray-500"
        >
          No transactions found
        </div>
        <table
          v-else
          class="w-full text-sm"
        >
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Transaction ID
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                User
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Type
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Amount
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Date
              </th>
              <th class="px-6 py-3 text-left font-medium text-gray-700">
                Status
              </th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="txn in filteredTransactions"
              :key="txn.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 font-mono text-xs">
                {{ txn.transaction_id }}
              </td>
              <td class="px-6 py-4">
                {{ txn.user_name }}
              </td>
              <td class="px-6 py-4">
                {{ txn.type }}
              </td>
              <td class="px-6 py-4 font-medium">
                {{ formatCurrency(txn.amount) }}
              </td>
              <td class="px-6 py-4">
                {{ formatDate(txn.created_at) }}
              </td>
              <td class="px-6 py-4">
                <span :class="`px-3 py-1 rounded-full text-xs font-medium ${getTxnStatusColor(txn.status)}`">
                  {{ txn.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import AdminLayout from '../../AdminLayout.vue';
import BaseModal from '../modals/BaseModal.vue';
import { formatDateTime as formatDateTimeValue } from '../../../utils/formatters';

const transactions = ref([]);
const isLoading = ref(false);
const searchQuery = ref('');
const typeFilter = ref('');
const showModal = ref(false);
const selectedTransaction = ref(null);
const addAlert = inject('addAlert', null);

const filteredTransactions = computed(() => {
  let filtered = transactions.value;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(t => t.transaction_id.toLowerCase().includes(q) || t.user_name.toLowerCase().includes(q));
  }
  if (typeFilter.value) filtered = filtered.filter(t => t.type === typeFilter.value);
  return filtered;
});

const formatDate = (date) => {
  if (!date) return '';
  return formatDateTimeValue(date);
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount || 0);
};

const getTxnStatusColor = (status) => {
  const colors = {
    success: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    failed: 'bg-red-100 text-red-800',
    refunded: 'bg-blue-100 text-blue-800'
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const fetchTransactions = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/v1/admin/transactions', {
      headers: {}
    });
    if (response.ok) {
      const data = await response.json();
      transactions.value = data.data || [];
    }
  } catch (error) {
    console.error('Error:', error);
    if (addAlert) addAlert('Failed to load transactions', 'error');
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchTransactions);
</script>
