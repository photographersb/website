<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">
            Photographer Payouts
          </h1>
          <p class="text-gray-600 mt-2">
            Manage photographer payments and payout history
          </p>
        </div>
        
        <AdminQuickNav />

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="selectedStatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Status
              </option>
              <option value="pending">
                Pending
              </option>
              <option value="processing">
                Processing
              </option>
              <option value="completed">
                Completed
              </option>
              <option value="failed">
                Failed
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
            <select
              v-model="selectedMethod"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Methods
              </option>
              <option value="bank">
                Bank Transfer
              </option>
              <option value="wallet">
                Platform Wallet
              </option>
              <option value="paypal">
                PayPal
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
            <select
              v-model="selectedPeriod"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Time
              </option>
              <option value="this-month">
                This Month
              </option>
              <option value="last-month">
                Last Month
              </option>
              <option value="this-year">
                This Year
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by name..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Payouts (All Time)
            </p>
            <p class="text-2xl font-bold text-gray-900">
              ${{ totalPayouts }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Pending Payouts
            </p>
            <p class="text-2xl font-bold text-yellow-600">
              ${{ pendingAmount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              This Month
            </p>
            <p class="text-2xl font-bold text-blue-600">
              ${{ thisMonthAmount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Failed Payouts
            </p>
            <p class="text-2xl font-bold text-red-600">
              {{ failedCount }}
            </p>
          </div>
        </div>

        <!-- Payouts Table -->
        <div
          v-if="!loading"
          class="bg-white rounded-lg shadow-md overflow-hidden"
        >
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Photographer
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Email
                  </th>
                  <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                    Amount
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Method
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Date
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">
                    Status
                  </th>
                  <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="payout in filteredPayouts"
                  :key="payout.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">
                      {{ payout.photographerName }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ payout.email }}
                  </td>
                  <td class="px-6 py-4 text-right">
                    <span class="font-semibold text-gray-900">${{ payout.amount }}</span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    <span class="inline-flex px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                      {{ getMethodLabel(payout.method) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ formatDate(payout.date) }}
                  </td>
                  <td class="px-6 py-4">
                    <span :class="['inline-flex px-3 py-1 rounded-full text-xs font-semibold text-white', getStatusBgClass(payout.status)]">
                      {{ payout.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                      <button
                        v-if="payout.status === 'pending'"
                        class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition"
                        @click="processPayout(payout.id)"
                      >
                        Process
                      </button>
                      <button
                        v-if="payout.status === 'failed'"
                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                        @click="retrPayout(payout.id)"
                      >
                        Retry
                      </button>
                      <button
                        class="px-3 py-1 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition"
                        @click="viewDetails(payout.id)"
                      >
                        Details
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div
            v-if="filteredPayouts.length === 0"
            class="text-center py-12"
          >
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
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <p class="text-gray-600 text-lg">
              No payouts found
            </p>
          </div>
        </div>

        <!-- Loading -->
        <div
          v-if="loading"
          class="flex justify-center py-12"
        >
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
        </div>

        <!-- Payout Summary by Period -->
        <div
          v-if="!loading"
          class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6"
        >
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
              This Month
            </h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Total Payouts:</span>
                <span class="font-semibold">${{ thisMonthAmount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Count:</span>
                <span class="font-semibold">{{ thisMonthCount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Average:</span>
                <span class="font-semibold">${{ thisMonthAvg }}</span>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
              By Status
            </h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-green-600">Completed:</span>
                <span class="font-semibold">${{ completedAmount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-yellow-600">Processing:</span>
                <span class="font-semibold">${{ processingAmount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-red-600">Failed:</span>
                <span class="font-semibold">${{ failedAmount }}</span>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
              By Method
            </h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Bank Transfer:</span>
                <span class="font-semibold">${{ bankAmount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Wallet:</span>
                <span class="font-semibold">${{ walletAmount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Other:</span>
                <span class="font-semibold">${{ otherAmount }}</span>
              </div>
            </div>
          </div>
        </div>
    </div>
    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDate as formatDateValue } from '../../utils/formatters'
const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedStatus = ref('')
const selectedMethod = ref('')
const selectedPeriod = ref('')
const searchQuery = ref('')

const payouts = ref([
  {
    id: 1,
    photographerName: 'Sarah Johnson',
    email: 'sarah@email.com',
    amount: 1500,
    method: 'bank',
    date: '2026-01-20',
    status: 'completed'
  },
  {
    id: 2,
    photographerName: 'Michael Chen',
    email: 'michael@email.com',
    amount: 2300,
    method: 'bank',
    date: '2026-01-19',
    status: 'completed'
  },
  {
    id: 3,
    photographerName: 'Emma Davis',
    email: 'emma@email.com',
    amount: 950,
    method: 'wallet',
    date: '2026-01-21',
    status: 'pending'
  },
  {
    id: 4,
    photographerName: 'James Wilson',
    email: 'james@email.com',
    amount: 1200,
    method: 'bank',
    date: '2026-01-15',
    status: 'failed'
  },
  {
    id: 5,
    photographerName: 'Lisa Garcia',
    email: 'lisa@email.com',
    amount: 1750,
    method: 'bank',
    date: '2026-01-18',
    status: 'processing'
  },
  {
    id: 6,
    photographerName: 'Robert Kim',
    email: 'robert@email.com',
    amount: 2100,
    method: 'paypal',
    date: '2026-01-17',
    status: 'completed'
  }
])

const filteredPayouts = computed(() => {
  return payouts.value.filter(payout => {
    const statusMatch = !selectedStatus.value || payout.status === selectedStatus.value
    const methodMatch = !selectedMethod.value || payout.method === selectedMethod.value
    const searchMatch = !searchQuery.value || 
      payout.photographerName.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    return statusMatch && methodMatch && searchMatch
  })
})

const totalPayouts = computed(() => {
  return payouts.value.reduce((sum, p) => sum + p.amount, 0)
})

const pendingAmount = computed(() => {
  return payouts.value
    .filter(p => p.status === 'pending')
    .reduce((sum, p) => sum + p.amount, 0)
})

const thisMonthAmount = computed(() => {
  return payouts.value
    .filter(p => new Date(p.date).getMonth() === new Date().getMonth())
    .reduce((sum, p) => sum + p.amount, 0)
})

const failedCount = computed(() => {
  return payouts.value.filter(p => p.status === 'failed').length
})

const thisMonthCount = computed(() => {
  return payouts.value.filter(p => new Date(p.date).getMonth() === new Date().getMonth()).length
})

const thisMonthAvg = computed(() => {
  if (thisMonthCount.value === 0) return 0
  return Math.round(thisMonthAmount.value / thisMonthCount.value)
})

const completedAmount = computed(() => {
  return payouts.value
    .filter(p => p.status === 'completed')
    .reduce((sum, p) => sum + p.amount, 0)
})

const processingAmount = computed(() => {
  return payouts.value
    .filter(p => p.status === 'processing')
    .reduce((sum, p) => sum + p.amount, 0)
})

const failedAmount = computed(() => {
  return payouts.value
    .filter(p => p.status === 'failed')
    .reduce((sum, p) => sum + p.amount, 0)
})

const bankAmount = computed(() => {
  return payouts.value
    .filter(p => p.method === 'bank')
    .reduce((sum, p) => sum + p.amount, 0)
})

const walletAmount = computed(() => {
  return payouts.value
    .filter(p => p.method === 'wallet')
    .reduce((sum, p) => sum + p.amount, 0)
})

const otherAmount = computed(() => {
  return payouts.value
    .filter(p => p.method !== 'bank' && p.method !== 'wallet')
    .reduce((sum, p) => sum + p.amount, 0)
})

const getStatusBgClass = (status) => {
  const classes = {
    pending: 'bg-yellow-500',
    processing: 'bg-blue-500',
    completed: 'bg-green-500',
    failed: 'bg-red-500'
  }
  return classes[status] || 'bg-gray-500'
}

const getMethodLabel = (method) => {
  const labels = {
    bank: 'Bank Transfer',
    wallet: 'Platform Wallet',
    paypal: 'PayPal'
  }
  return labels[method] || method
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const processPayout = (payoutId) => {
  const payout = payouts.value.find(p => p.id === payoutId)
  if (payout) {
    payout.status = 'processing'
    toast.value = { show: true, message: `Payout to ${payout.photographerName} is now processing!`, type: 'success' }
  }
}

const retrPayout = (payoutId) => {
  const payout = payouts.value.find(p => p.id === payoutId)
  if (payout) {
    payout.status = 'processing'
    toast.value = { show: true, message: `Retrying payout to ${payout.photographerName}...`, type: 'info' }
  }
}

const viewDetails = (payoutId) => {
  toast.value = { show: true, message: 'Loading payout details...', type: 'info' }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 800)
})
</script>
