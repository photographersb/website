<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 py-12 px-4 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-12 text-center">
      <!-- Success Icon -->
      <div class="mb-6">
        <div class="mx-auto w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-4xl">
          ✓
        </div>
      </div>

      <!-- Success Message -->
      <h1 class="text-3xl font-bold text-gray-900 mb-2">
        Upgrade Successful!
      </h1>
      <p class="text-lg text-gray-600 mb-8">
        Your package has been upgraded successfully.
      </p>

      <!-- Details Card -->
      <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <div class="mb-4">
          <p class="text-sm text-gray-600 mb-1">
            New Package
          </p>
          <p class="text-2xl font-bold text-blue-600">
            {{ upgrade?.to_package }}
          </p>
        </div>
        <div class="mb-4">
          <p class="text-sm text-gray-600 mb-1">
            Amount Paid
          </p>
          <p class="text-lg font-semibold text-gray-900">
            ৳{{ formatNumber(upgrade?.total_amount) }}
          </p>
        </div>
        <div>
          <p class="text-sm text-gray-600 mb-1">
            Active Until
          </p>
          <p class="text-lg font-semibold text-gray-900">
            {{ formatDate(featured?.end_date) }}
          </p>
        </div>
      </div>

      <!-- Features List -->
      <div class="text-left mb-8">
        <p class="font-semibold text-gray-900 mb-3">
          Your new package includes:
        </p>
        <ul class="space-y-2">
          <li
            v-for="feature in newPackageFeatures"
            :key="feature"
            class="flex items-center text-gray-700"
          >
            <span class="text-green-500 mr-2 text-lg">✓</span>
            {{ feature }}
          </li>
        </ul>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <router-link
          :to="`/featured-photographer/analytics/${featured?.id}`"
          class="block w-full py-3 px-4 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors"
        >
          View Analytics
        </router-link>
        <router-link
          :to="`/be-featured`"
          class="block w-full py-3 px-4 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors"
        >
          Back to Featured
        </router-link>
      </div>

      <!-- Additional Info -->
      <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-900">
          Thank you for upgrading! Your featured photographer listing will receive enhanced visibility with your new tier.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDate as formatDateValue, formatNumber as formatNumberValue } from '../utils/formatters'

export default {
  name: 'FeaturedPhotographerUpgradeSuccess',

  data() {
    return {
      upgrade: null,
      featured: null,
      newPackageFeatures: [],
    };
  },

  mounted() {
    this.fetchUpgradeDetails();
  },

  methods: {
    async fetchUpgradeDetails() {
      try {
        const upgradeId = this.$route.params.upgradeId;
        // In a real app, fetch from API
        
        // Mock data for now
        this.upgrade = {
          id: upgradeId,
          from_package: 'Starter',
          to_package: 'Professional',
          total_amount: 1499.99,
        };

        this.featured = {
          id: 1,
          end_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000),
        };

        this.setFeaturesForPackage(this.upgrade.to_package);
      } catch (error) {
        console.error('Error fetching upgrade details:', error);
      }
    },

    setFeaturesForPackage(packageName) {
      const features = {
        Starter: [
          '5 featured listings',
          'Basic analytics',
          'Email support',
          '30-day listings',
        ],
        Professional: [
          '15 featured listings',
          'Advanced analytics with charts',
          'Priority email & chat support',
          '30-day listings with analytics',
          'Custom categories',
          'Portfolio showcase',
        ],
        Enterprise: [
          'Unlimited featured listings',
          'Real-time analytics dashboard',
          '24/7 premium support',
          '30-day listings',
          'API access',
          'Custom features & integrations',
          'Dedicated account manager',
        ],
      };

      this.newPackageFeatures = features[packageName] || [];
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
