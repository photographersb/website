<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="mb-12 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          Upgrade Your Package
        </h1>
        <p class="text-lg text-gray-600">
          Get more features and reach more potential clients
        </p>
      </div>

      <!-- Current Package Info -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-12 border-l-4 border-blue-500">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div>
            <p class="text-gray-600 text-sm">
              Current Package
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ featured?.package_tier || 'Starter' }}
            </p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">
              Days Remaining
            </p>
            <p class="text-2xl font-bold text-blue-600">
              {{ daysRemaining }} days
            </p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">
              End Date
            </p>
            <p class="text-lg font-semibold text-gray-900">
              {{ formatDate(featured?.end_date) }}
            </p>
          </div>
          <div>
            <p class="text-gray-600 text-sm">
              Active Status
            </p>
            <span
              v-if="featured?.active"
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
            >
              ✓ Active
            </span>
            <span
              v-else
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"
            >
              ✗ Inactive
            </span>
          </div>
        </div>
      </div>

      <!-- Upgrade Options -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <div
          v-for="option in upgradeOptions"
          :key="option.package"
          :class="[
            'relative bg-white rounded-lg shadow-md overflow-hidden cursor-pointer transition-all duration-300 transform hover:shadow-xl hover:-translate-y-2',
            selectedPackage?.package === option.package ? 'ring-2 ring-blue-500 shadow-xl -translate-y-2' : ''
          ]"
          @click="selectPackage(option)"
        >
          <!-- Upgrade Badge -->
          <div
            v-if="option.is_upgrade"
            class="absolute top-0 right-0 bg-gradient-to-r from-green-400 to-green-600 text-white px-4 py-1 text-xs font-bold rounded-bl-lg"
          >
            UPGRADE
          </div>
          <div
            v-else
            class="absolute top-0 right-0 bg-gradient-to-r from-gray-400 to-gray-600 text-white px-4 py-1 text-xs font-bold rounded-bl-lg"
          >
            DOWNGRADE
          </div>

          <!-- Content -->
          <div class="p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">
              {{ option.package }}
            </h3>
            <p class="text-gray-600 text-sm mb-6">
              From {{ option.current_package }}
            </p>

            <!-- Price Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
              <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600 text-sm">New Cost ({{ option.price_details.days_remaining }} days)</span>
                <span class="font-semibold">৳{{ formatNumber(option.price_details.prorated_amount) }}</span>
              </div>
              <div
                v-if="option.price_details.credit_amount > 0"
                class="flex justify-between items-center mb-2 text-green-600"
              >
                <span class="text-sm">← Credit (current plan)</span>
                <span class="font-semibold">-৳{{ formatNumber(option.price_details.credit_amount) }}</span>
              </div>
              <div
                v-if="option.price_details.discount_amount > 0"
                class="flex justify-between items-center mb-3 text-green-600"
              >
                <span class="text-sm">🎁 Loyalty Discount (10%)</span>
                <span class="font-semibold">-৳{{ formatNumber(option.price_details.discount_amount) }}</span>
              </div>
              <div class="border-t pt-3 flex justify-between items-center">
                <span class="font-bold text-gray-900">You Pay</span>
                <span
                  :class="[
                    'text-2xl font-bold',
                    option.price_details.total_amount === 0 ? 'text-green-600' : 'text-blue-600'
                  ]"
                >
                  {{ option.price_details.total_amount === 0 ? 'FREE' : '৳' + formatNumber(option.price_details.total_amount) }}
                </span>
              </div>
            </div>

            <!-- Features List -->
            <div class="mb-6">
              <p class="text-sm font-semibold text-gray-900 mb-3">
                Features:
              </p>
              <ul class="space-y-2">
                <li
                  v-for="(feature, idx) in Array.isArray(option.features) ? option.features : [option.features + ' features']"
                  :key="idx"
                  class="flex items-center text-sm text-gray-700"
                >
                  <span class="text-blue-500 mr-2">✓</span>
                  {{ feature }}
                </li>
              </ul>
            </div>

            <!-- Selection Indicator -->
            <div
              :class="[
                'py-2 rounded-lg text-center font-semibold transition-all',
                selectedPackage?.package === option.package
                  ? 'bg-blue-500 text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              {{ selectedPackage?.package === option.package ? '✓ Selected' : 'Select Package' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Method Selection -->
      <div
        v-if="selectedPackage"
        class="bg-white rounded-lg shadow-md p-8 mb-12"
      >
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          Select Payment Method
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <div
            v-for="method in paymentMethods"
            :key="method.id"
            :class="[
              'relative p-6 border-2 rounded-lg cursor-pointer transition-all duration-300',
              selectedPaymentMethod === method.id
                ? 'border-blue-500 bg-blue-50'
                : 'border-gray-200 bg-white hover:border-gray-300'
            ]"
            @click="selectedPaymentMethod = method.id"
          >
            <div class="text-center">
              <div class="text-3xl mb-2">
                {{ method.icon }}
              </div>
              <h3 class="font-semibold text-gray-900 text-sm">
                {{ method.name }}
              </h3>
              <p
                v-if="method.description"
                class="text-xs text-gray-600 mt-1"
              >
                {{ method.description }}
              </p>
            </div>
            
            <!-- Selection Checkmark -->
            <div
              v-if="selectedPaymentMethod === method.id"
              class="absolute top-2 right-2 bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm"
            >
              ✓
            </div>
          </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start">
          <span class="text-blue-600 text-xl mr-3">🔒</span>
          <div>
            <p class="font-semibold text-blue-900 text-sm">
              Secure Payment
            </p>
            <p class="text-blue-800 text-xs mt-1">
              Your payment information is encrypted and processed securely with industry-standard security protocols.
            </p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-4 justify-center">
        <router-link
          :to="`/be-featured`"
          class="px-8 py-3 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors"
        >
          Cancel
        </router-link>
        
        <button
          v-if="selectedPackage"
          :disabled="processing"
          :class="[
            'px-8 py-3 rounded-lg bg-blue-600 text-white font-semibold transition-all',
            processing ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'
          ]"
          @click="proceedToPayment"
        >
          {{ processing ? 'Processing...' : `Proceed to Payment (৳${formatNumber(selectedPackage.price_details.total_amount)})` }}
        </button>
      </div>

      <!-- Summary Table -->
      <div
        v-if="selectedPackage"
        class="mt-12 bg-white rounded-lg shadow-md p-8"
      >
        <h2 class="text-xl font-bold text-gray-900 mb-6">
          Upgrade Summary
        </h2>
        
        <table class="w-full">
          <tbody class="space-y-3">
            <tr class="border-b">
              <td class="py-3 text-gray-700">
                Current Package
              </td>
              <td class="py-3 font-semibold text-gray-900 text-right">
                {{ selectedPackage.current_package }}
              </td>
            </tr>
            <tr class="border-b">
              <td class="py-3 text-gray-700">
                New Package
              </td>
              <td class="py-3 font-semibold text-gray-900 text-right">
                {{ selectedPackage.package }}
              </td>
            </tr>
            <tr class="border-b">
              <td class="py-3 text-gray-700">
                Days Remaining
              </td>
              <td class="py-3 font-semibold text-gray-900 text-right">
                {{ selectedPackage.price_details.days_remaining }} days
              </td>
            </tr>
            <tr class="border-b">
              <td class="py-3 text-gray-700">
                New Cost (for remaining days)
              </td>
              <td class="py-3 font-semibold text-gray-900 text-right">
                ৳{{ formatNumber(selectedPackage.price_details.prorated_amount) }}
              </td>
            </tr>
            <tr
              v-if="selectedPackage.price_details.credit_amount > 0"
              class="border-b bg-green-50"
            >
              <td class="py-3 text-green-700">
                Credit (current plan)
              </td>
              <td class="py-3 font-semibold text-green-700 text-right">
                -৳{{ formatNumber(selectedPackage.price_details.credit_amount) }}
              </td>
            </tr>
            <tr
              v-if="selectedPackage.price_details.discount_amount > 0"
              class="border-b bg-green-50"
            >
              <td class="py-3 text-green-700">
                Loyalty Discount (10%)
              </td>
              <td class="py-3 font-semibold text-green-700 text-right">
                -৳{{ formatNumber(selectedPackage.price_details.discount_amount) }}
              </td>
            </tr>
            <tr class="bg-blue-50">
              <td class="py-3 font-bold text-blue-900">
                Total Amount to Pay
              </td>
              <td class="py-3 font-bold text-blue-900 text-right text-xl">
                {{ selectedPackage.price_details.total_amount === 0 ? 'FREE' : '৳' + formatNumber(selectedPackage.price_details.total_amount) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- FAQ Section -->
      <div class="mt-12 bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          Frequently Asked Questions
        </h2>
        
        <div class="space-y-4">
          <div class="border-b pb-4">
            <p class="font-semibold text-gray-900 mb-2">
              What happens to my current credits?
            </p>
            <p class="text-gray-700 text-sm">
              Your unused credits will be applied to the new package price, reducing what you need to pay for the upgrade.
            </p>
          </div>
          
          <div class="border-b pb-4">
            <p class="font-semibold text-gray-900 mb-2">
              How is the prorated amount calculated?
            </p>
            <p class="text-gray-700 text-sm">
              We calculate the daily rate for both your current and new package, then charge only for the remaining days at the new rate, minus your credit.
            </p>
          </div>
          
          <div class="border-b pb-4">
            <p class="font-semibold text-gray-900 mb-2">
              Is there a discount for upgrading?
            </p>
            <p class="text-gray-700 text-sm">
              Yes! When upgrading to a higher tier, we apply a 10% loyalty discount on the prorated amount.
            </p>
          </div>
          
          <div>
            <p class="font-semibold text-gray-900 mb-2">
              What payment methods do you support?
            </p>
            <p class="text-gray-700 text-sm">
              We support bKash, Nagad, uPay, SSLCommerz, and Cash/Manual payment options.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/api'
import { formatDate as formatDateValue, formatNumber as formatNumberValue } from '../utils/formatters'

export default {
  name: 'FeaturedPhotographerUpgrade',
  
  data() {
    return {
      featured: null,
      daysRemaining: 0,
      upgradeOptions: [],
      selectedPackage: null,
      selectedPaymentMethod: 'bkash',
      processing: false,
      paymentMethods: [
        { id: 'bkash', name: 'bKash', icon: '📱', description: 'Mobile Money' },
        { id: 'nagad', name: 'Nagad', icon: '📲', description: 'Mobile Banking' },
        { id: 'upay', name: 'uPay', icon: '💳', description: 'Payment Gateway' },
        { id: 'sslcommerz', name: 'SSLCommerz', icon: '🛡️', description: 'Payment Gateway' },
        { id: 'cash', name: 'Cash', icon: '💵', description: 'Manual Payment' },
      ],
    };
  },

  mounted() {
    this.fetchUpgradeOptions();
  },

  methods: {
    async fetchUpgradeOptions() {
      try {
        const response = await api.get(`/featured-photographers/upgrade/options/${this.$route.params.id}`);
        this.featured = response.data.data.featured_photographer;
        this.upgradeOptions = response.data.data.upgrade_options;
        this.daysRemaining = response.data.data.days_remaining;
      } catch (error) {
        console.error('Error fetching upgrade options:', error);
        this.$toast.error('Failed to load upgrade options');
      }
    },

    selectPackage(option) {
      this.selectedPackage = option;
    },

    async proceedToPayment() {
      if (!this.selectedPackage || !this.selectedPaymentMethod) {
        this.$toast.error('Please select package and payment method');
        return;
      }

      this.processing = true;

      try {
        const response = await api.post(`/featured-photographers/upgrade/${this.$route.params.id}`, {
          to_package: this.selectedPackage.package,
          payment_method: this.selectedPaymentMethod,
        });

        const data = response.data.data;

        if (this.selectedPaymentMethod === 'cash') {
          this.$toast.success('Payment request submitted. Admin will verify and complete your upgrade.');
          this.$router.push(`/featured-photographer/analytics/${this.$route.params.id}`);
          return;
        }

        if (data.payment_url) {
          // Redirect to payment gateway
          window.location.href = data.payment_url;
          return;
        }

        this.$toast.success('Upgrade initiated');
        this.$router.push(`/featured-photographer/analytics/${this.$route.params.id}`);
      } catch (error) {
        console.error('Payment error:', error);
        this.$toast.error(error.response?.data?.message || 'Payment failed');
      } finally {
        this.processing = false;
      }
    },

    formatDate(date) {
      return formatDateValue(date)
    },

    formatNumber(num) {
      return formatNumberValue(num)
    },
  },
};
</script>

<style scoped>
button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
