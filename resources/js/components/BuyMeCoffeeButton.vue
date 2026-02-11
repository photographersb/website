<template>
  <!-- Buy Me a Coffee Section -->
  <div
    v-if="tipInfo"
    class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg border-2 border-amber-200 p-6 mb-8"
  >
    <div class="flex items-center justify-between gap-4">
      <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
          <div class="text-4xl">
            ☕
          </div>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900">
            Support me if you love my work.
          </h3>
          <p class="text-sm text-gray-600">
            {{ tipInfo.tip_message || 'Your tip helps me keep creating, learning, and improving for you.' }}
          </p>
        </div>
      </div>

      <button
        class="inline-flex items-center gap-2 rounded-full border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100"
        @click="showTipPanel = !showTipPanel"
      >
        {{ showTipPanel ? 'Hide tip options' : 'Show tip options' }}
        <svg
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            :d="showTipPanel ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"
          />
        </svg>
      </button>
    </div>

    <div
      v-if="showTipPanel"
      class="mt-6"
    >
        
        <div
          v-if="loadingTipInfo"
          class="mb-4 rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm text-amber-800"
        >
          Loading tip info...
        </div>
        <div
          v-else-if="tipInfoError"
          class="mb-4 rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm text-amber-800"
        >
          Tip info is unavailable right now.
        </div>
        <div
          v-else-if="!tipInfo.accept_tips"
          class="mb-4 rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm text-amber-800"
        >
          Tips are currently disabled for this photographer.
        </div>

        <!-- Amount Input -->
        <div class="mb-4">
          <input
            v-model.number="customTipAmount"
            type="number"
            min="50"
            max="100000"
            placeholder="Enter amount"
            class="w-full px-4 py-2 border-2 border-amber-300 rounded-lg focus:outline-none focus:border-amber-600"
          >
        </div>

        <!-- Message Input -->
        <div class="mb-4">
          <textarea
            v-model="tipMessage"
            placeholder="Add a message (optional)"
            maxlength="500"
            rows="3"
            class="w-full px-4 py-2 border-2 border-amber-300 rounded-lg focus:outline-none focus:border-amber-600"
          ></textarea>
          <p class="text-xs text-gray-600 mt-1">
            {{ tipMessage.length }}/500
          </p>
        </div>

        <!-- Payment Method Selection -->
        <div class="mb-4">
          <p class="text-sm font-semibold text-gray-700 mb-2">
            Payment Method
          </p>
          <div class="grid grid-cols-3 gap-2">
            <button
              v-for="method in paymentMethods"
              :key="method.id"
              :class="[
                'p-2 rounded-lg font-semibold transition-all text-sm',
                selectedPaymentMethod === method.id
                  ? 'bg-amber-600 text-white'
                  : 'bg-white text-gray-700 border-2 border-amber-200 hover:border-amber-400'
              ]"
              @click="selectedPaymentMethod = method.id"
            >
              {{ method.name }}
            </button>
          </div>
        </div>

        <!-- Show Payment Details Button -->
        <button
          :disabled="processingTip || !finalTipAmount || !tipInfo.accept_tips"
          :class="[
            'w-full py-3 rounded-lg font-bold text-white transition-all',
            processingTip || !finalTipAmount || !tipInfo.accept_tips
              ? 'bg-gray-400 cursor-not-allowed'
              : 'bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 shadow-lg'
          ]"
          @click="showPaymentDetails"
        >
          {{ processingTip ? 'Processing...' : 'Show payment details' }}
        </button>
        <div
          v-if="tipInfo.recent_tips.length > 0"
          class="mt-6 border-t border-amber-200 pt-4"
        >
          <p class="text-sm font-semibold text-gray-700 mb-3">
            Recent Tips
          </p>
          <div class="space-y-2">
            <div
              v-for="(tip, idx) in tipInfo.recent_tips"
              :key="idx"
              class="flex items-center justify-between text-sm bg-white rounded-lg p-2 border border-amber-100"
            >
              <div>
                <p class="font-semibold text-gray-900">
                  {{ tip.donor_name }}
          <div
            v-if="showTrxModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @click.self="closeTrxModal"
          >
            <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
              <h4 class="text-lg font-bold text-gray-900">
                Confirm your payment
              </h4>
              <p class="mt-2 text-sm text-gray-600">
                Send your tip to the number below and enter the transaction ID.
              </p>

              <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                <p class="text-xs uppercase text-amber-800">{{ trxDetails.label }}</p>
                <p class="text-lg font-bold text-amber-900">
                  {{ trxDetails.number || 'Not available' }}
                </p>
                <p class="text-xs text-amber-700 mt-1">
                  Amount: ৳{{ formatNumber(finalTipAmount || 0) }}
                </p>
              </div>

              <div class="mt-4">
                <label class="text-sm font-semibold text-gray-700">Transaction ID</label>
                <input
                  v-model.trim="trxId"
                  type="text"
                  placeholder="Enter TRX ID"
                  class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-4 focus:ring-amber-200"
                >
              </div>

              <div class="mt-5 flex gap-3">
                <button
                  class="flex-1 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                  @click="closeTrxModal"
                >
                  Cancel
                </button>
                <button
                  :disabled="processingTip || !trxId"
                  class="flex-1 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:cursor-not-allowed disabled:bg-gray-300"
                  @click="confirmTip"
                >
                  {{ processingTip ? 'Saving...' : 'Confirm Tip' }}
                </button>
              </div>
            </div>
          </div>

                </p>
                <p
                  v-if="tip.message"
                  class="text-xs text-gray-600"
                >
                  "{{ tip.message }}"
                </p>
              </div>
              <div class="text-right">
                <p class="font-bold text-amber-600">
                  ৳{{ formatNumber(tip.amount) }}
                </p>
                <p class="text-xs text-gray-500">
                  {{ tip.paid_at }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>

<script>
import api from '../api'
import { formatNumber as formatNumberValue } from '../utils/formatters'

export default {
  name: 'BuyMeCoffeeButton',
  
  props: {
    photographerId: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      tipInfo: {
        accept_tips: false,
        tip_message: 'Support your favorite photographer!',
        total_tips: 0,
        tip_count: 0,
        recent_tips: [],
      },
      showTipPanel: false,
      loadingTipInfo: true,
      tipInfoError: false,
      customTipAmount: 100,
      tipMessage: '',
      selectedPaymentMethod: 'bkash',
      showTrxModal: false,
      trxId: '',
      trxDetails: {
        label: 'bKash',
        number: '',
      },
      activeTipId: null,
      processingTip: false,
      paymentMethods: [
        { id: 'bkash', name: 'bKash' },
        { id: 'nagad', name: 'Nagad' },
        { id: 'rocket', name: 'Rocket' },
      ],
    };
  },

  computed: {
    finalTipAmount() {
      return this.customTipAmount;
    },
  },

  mounted() {
    this.fetchTipInfo();
  },

  methods: {
    async fetchTipInfo() {
      this.loadingTipInfo = true;
      this.tipInfoError = false;
      try {
        const response = await api.get(`/photographers/${this.photographerId}/tips/info`);
        this.tipInfo = response.data.data || this.tipInfo;
      } catch (error) {
        console.error('Error fetching tip info:', error);
        this.tipInfoError = true;
      } finally {
        this.loadingTipInfo = false;
      }
    },

    async showPaymentDetails() {
      if (!this.tipInfo?.accept_tips) {
        this.$toast?.error?.('Tips are currently disabled for this photographer');
        return;
      }

      if (!this.finalTipAmount || this.finalTipAmount < 50) {
        this.$toast?.error?.('Minimum tip amount is ৳50');
        return;
      }

      this.processingTip = true;

      try {
        const response = await api.post(`/photographers/${this.photographerId}/tips/initiate`, {
          amount: this.finalTipAmount,
          message: this.tipMessage,
          payment_method: this.selectedPaymentMethod,
        });

        const data = response.data.data || {};
        this.activeTipId = data.tip_id || null;
        this.trxDetails = {
          label: data.payment_method_label || this.selectedPaymentMethod,
          number: data.recipient_number || '',
        };
        this.showTrxModal = true;
      } catch (error) {
        console.error('Error sending tip:', error);
        this.$toast?.error?.(error.response?.data?.message || 'Failed to prepare payment details');
      } finally {
        this.processingTip = false;
      }
    },

    async confirmTip() {
      if (!this.trxId || !this.activeTipId) {
        this.$toast?.error?.('Please enter a valid transaction ID');
        return;
      }

      this.processingTip = true;
      try {
        await api.post(`/photographers/tips/${this.activeTipId}/confirm`, {
          transaction_id: this.trxId,
        });
        this.$toast?.success?.('Tip confirmed successfully! Thank you!');
        this.resetForm();
        this.fetchTipInfo();
        this.closeTrxModal();
      } catch (error) {
        console.error('Error confirming tip:', error);
        this.$toast?.error?.(error.response?.data?.message || 'Failed to confirm tip');
      } finally {
        this.processingTip = false;
      }
    },

    closeTrxModal() {
      this.showTrxModal = false;
      this.trxId = '';
    },

    resetForm() {
      this.customTipAmount = 100;
      this.tipMessage = '';
      this.selectedPaymentMethod = 'bkash';
      this.trxId = '';
      this.activeTipId = null;
    },

    formatNumber(num) {
      return formatNumberValue(num)
    },
  },
};
</script>

<style scoped>
input:focus,
textarea:focus {
  box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
}
</style>
