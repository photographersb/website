<template>
  <div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-5xl mx-auto space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <router-link
            to="/client/payments"
            class="text-sm font-semibold text-burgundy-600 hover:text-burgundy-700"
          >
            Back to Payments
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900 mt-2">Payment Details</h1>
          <p class="text-gray-600">Transaction ID: {{ transaction?.reference_id || transaction?.transaction_id || route.params.transactionId }}</p>
        </div>
        <span
          v-if="transaction"
          :class="[
            'px-3 py-1 rounded-full text-xs font-semibold',
            getStatusColor(transaction.status).badge
          ]"
        >
          {{ transaction.status }}
        </span>
      </div>

      <div
        v-if="loading"
        class="flex items-center justify-center py-12"
      >
        <div
          class="animate-spin rounded-full h-10 w-10 border-b-2"
          style="border-bottom-color: var(--admin-brand-primary);"
        />
      </div>

      <div
        v-else-if="transaction"
        class="grid grid-cols-1 lg:grid-cols-3 gap-6"
      >
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Summary</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-gray-500">Amount</p>
                <p class="text-xl font-bold text-gray-900">৳{{ formatNumber(transaction.amount) }}</p>
              </div>
              <div>
                <p class="text-gray-500">Payment Method</p>
                <p class="text-base font-semibold text-gray-900">{{ formatPaymentMethod(transaction.payment_method) }}</p>
              </div>
              <div>
                <p class="text-gray-500">Status</p>
                <p class="text-base font-semibold text-gray-900">{{ transaction.status }}</p>
              </div>
              <div>
                <p class="text-gray-500">Paid On</p>
                <p class="text-base font-semibold text-gray-900">{{ formatDateTime(transaction.created_at) }}</p>
              </div>
            </div>
          </div>

          <div
            v-if="transaction.booking"
            class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6"
          >
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking</h2>
            <div class="space-y-2 text-sm text-gray-700">
              <p>
                Photographer:
                <span class="font-semibold text-gray-900">
                  {{ transaction.booking.photographer?.user?.name || transaction.booking.photographer?.business_name || 'Photographer' }}
                </span>
              </p>
              <p>
                Event Date:
                <span class="font-semibold text-gray-900">{{ formatDate(transaction.booking.event_date) }}</span>
              </p>
              <p>
                Status:
                <span class="font-semibold text-gray-900">{{ transaction.booking.status }}</span>
              </p>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Reference</h2>
            <div class="space-y-2 text-sm text-gray-700">
              <p>Reference ID: <span class="font-semibold text-gray-900">{{ transaction.reference_id }}</span></p>
              <p>Gateway ID: <span class="font-semibold text-gray-900">{{ transaction.transaction_id || 'N/A' }}</span></p>
              <p>Type: <span class="font-semibold text-gray-900">{{ transaction.transaction_type }}</span></p>
            </div>
          </div>
        </div>
      </div>

      <div
        v-else
        class="bg-white rounded-2xl border border-gray-200 p-10 text-center"
      >
        <p class="text-gray-600">Transaction not found.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../api'
import {
  formatDate as formatDateValue,
  formatDateTime as formatDateTimeValue,
  formatNumber
} from '../../utils/formatters'

const route = useRoute()
const router = useRouter()
const transaction = ref(null)
const loading = ref(true)

onMounted(() => {
  loadTransaction()
})

const loadTransaction = async () => {
  loading.value = true
  try {
    const response = await api.get(`/payments/transactions/${route.params.transactionId}`)
    transaction.value = response.data.data || null
  } catch (error) {
    if (error.response?.status === 401) {
      router.push('/auth')
      return
    }
  } finally {
    loading.value = false
  }
}

const formatPaymentMethod = (method) => {
  const methods = {
    card: 'Credit/Debit Card (SSLCommerz)',
    bkash: 'bKash',
    nagad: 'Nagad',
    bank_transfer: 'Bank Transfer',
  }
  return methods[method] || method
}

const formatDate = (value) => formatDateValue(value)

const formatDateTime = (value) => formatDateTimeValue(value)

const getStatusColor = (status) => {
  const colors = {
    pending: {
      badge: 'bg-yellow-100 text-yellow-800',
    },
    completed: {
      badge: 'bg-green-100 text-green-800',
    },
    failed: {
      badge: 'bg-red-100 text-red-800',
    },
    cancelled: {
      badge: 'bg-gray-100 text-gray-800',
    },
  }
  return colors[status] || colors.pending
}
</script>
