<template>
  <div
    v-if="tipInfo"
    class="relative mb-10 overflow-hidden rounded-3xl border border-amber-200/70 bg-[radial-gradient(circle_at_top,_rgba(255,196,120,0.28)_0,_transparent_60%)] p-6 sm:p-8"
  >
    <div class="pointer-events-none absolute -top-20 right-0 h-48 w-48 rounded-full bg-amber-200/40 blur-3xl" />
    <div class="pointer-events-none absolute -bottom-24 -left-16 h-60 w-60 rounded-full bg-orange-200/40 blur-3xl" />

    <div class="relative z-10 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
      <div>
        <p class="text-[11px] uppercase tracking-[0.45em] text-amber-700/80">
          Tip the artist
        </p>
        <h3 class="mt-3 text-2xl sm:text-3xl font-display font-semibold text-[#2a160a]">
          Support me if you love my work.
        </h3>
        <p class="mt-2 text-sm sm:text-base text-[#5f3c1f]">
          {{ tipInfo.tip_message || 'Your tip helps me keep creating, learning, and improving for you.' }}
        </p>
        <div class="mt-4 flex flex-wrap gap-3 text-xs font-semibold uppercase tracking-wide text-amber-800/80">
          <span class="rounded-full bg-white/70 px-3 py-1">{{ tipInfo.tip_count || 0 }} tips</span>
          <span class="rounded-full bg-white/70 px-3 py-1">৳{{ formatNumber(tipInfo.total_tips || 0) }} raised</span>
        </div>
      </div>

      <div class="flex flex-col justify-between gap-4 rounded-2xl border border-amber-200/60 bg-white/70 p-4 sm:p-5 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-2xl">
            ☕
          </div>
          <div>
            <p class="text-sm font-semibold text-[#2a160a]">Choose your amount</p>
            <p class="text-xs text-[#6f5136]">Minimum ৳50 • Secure & manual</p>
          </div>
        </div>
        <button
          class="inline-flex items-center justify-center gap-2 rounded-full border border-amber-300 bg-white px-4 py-2 text-sm font-semibold text-amber-800 transition hover:bg-amber-50"
          @click="showTipPanel = !showTipPanel"
        >
          {{ showTipPanel ? 'Hide tip options' : 'Show tip options' }}
          <svg
            class="h-4 w-4"
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
    </div>

    <div
      v-if="showTipPanel"
      class="relative z-10 mt-6 rounded-2xl border border-amber-200/70 bg-white/90 p-5 sm:p-6 shadow-lg"
    >
      <div
        v-if="loadingTipInfo"
        class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800"
      >
        Loading tip info...
      </div>
      <div
        v-else-if="tipInfoError"
        class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800"
      >
        Tip info is unavailable right now.
      </div>
      <div
        v-else-if="!tipInfo.accept_tips"
        class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800"
      >
        Tips are currently disabled for this photographer.
      </div>

      <div class="grid gap-4 md:grid-cols-[180px_1fr]">
        <div class="rounded-2xl border border-amber-200/70 bg-amber-50/70 p-4">
          <p class="text-[11px] uppercase tracking-[0.3em] text-amber-700">Amount</p>
          <input
            v-model.number="customTipAmount"
            type="number"
            min="50"
            max="100000"
            class="mt-3 w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-lg font-semibold text-amber-900 focus:border-amber-500 focus:outline-none"
          >
          <p class="mt-2 text-xs text-amber-700">Minimum ৳50</p>
        </div>

        <div>
          <label class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">Message (optional)</label>
          <textarea
            v-model="tipMessage"
            placeholder="Leave a kind note for the photographer"
            maxlength="500"
            rows="3"
            class="mt-2 w-full rounded-2xl border border-amber-200/60 bg-white px-4 py-3 text-sm text-gray-700 focus:border-amber-500 focus:outline-none"
          ></textarea>
          <p class="mt-2 text-xs text-gray-500">{{ tipMessage.length }}/500</p>
        </div>
      </div>

      <div class="mt-6 grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">Payment Method</p>
          <div class="mt-3 grid grid-cols-3 gap-3">
            <button
              v-for="method in paymentMethods"
              :key="method.id"
              :class="paymentButtonClass(method.id)"
              @click="selectedPaymentMethod = method.id"
            >
              {{ method.name }}
            </button>
          </div>
        </div>

        <div class="rounded-2xl border border-amber-200/60 bg-white p-4 shadow-sm">
          <p class="text-xs uppercase tracking-[0.25em] text-gray-500">Selected number</p>
          <div class="mt-3 flex items-center justify-between gap-3">
            <div>
              <p class="text-sm font-semibold text-gray-800">{{ selectedPaymentLabel }}</p>
              <p class="text-xs text-gray-500">Tap method to switch</p>
            </div>
            <p class="font-mono text-sm font-semibold text-amber-700">
              {{ selectedPaymentNumber || 'Not available' }}
            </p>
          </div>
        </div>
      </div>

      <button
        :disabled="processingTip || !finalTipAmount || !tipInfo.accept_tips"
        :class="[
          'mt-6 w-full rounded-full py-3 text-sm font-semibold tracking-wide transition',
          processingTip || !finalTipAmount || !tipInfo.accept_tips
            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
            : 'bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white shadow-lg hover:from-amber-700 hover:to-orange-700'
        ]"
        @click="showPaymentDetails"
      >
        {{ processingTip ? 'Preparing...' : 'Show payment details' }}
      </button>

      <div
        v-if="tipInfo.recent_tips.length > 0"
        class="mt-6 border-t border-amber-200/70 pt-4"
      >
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-gray-500">Recent tips</p>
        <div class="mt-3 space-y-3">
          <div
            v-for="(tip, idx) in tipInfo.recent_tips"
            :key="idx"
            class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-100 bg-white px-4 py-3 text-sm"
          >
            <div>
              <p class="font-semibold text-gray-900">{{ tip.donor_name }}</p>
              <p v-if="tip.message" class="text-xs text-gray-500">"{{ tip.message }}"</p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-amber-700">৳{{ formatNumber(tip.amount) }}</p>
              <p class="text-xs text-gray-500">{{ tip.paid_at }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showTrxModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="closeTrxModal"
    >
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <h4 class="text-lg font-semibold text-gray-900">Confirm your payment</h4>
        <p class="mt-2 text-sm text-gray-600">
          Send your tip to the number below and enter the transaction ID.
        </p>

        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
          <p class="text-xs uppercase tracking-wide text-amber-800">{{ trxDetails.label }}</p>
          <p class="text-lg font-semibold text-amber-900">
            {{ trxDetails.number || 'Not available' }}
          </p>
          <p class="text-xs text-amber-700 mt-1">
            Amount: ৳{{ formatNumber(finalTipAmount || 0) }}
          </p>
        </div>

        <div class="mt-4">
          <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Transaction ID</label>
          <input
            v-model.trim="trxId"
            type="text"
            placeholder="Enter TRX ID"
            class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-4 focus:ring-amber-200"
          >
        </div>

        <div class="mt-6 flex gap-3">
          <button
            class="flex-1 rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50"
            @click="closeTrxModal"
          >
            Cancel
          </button>
          <button
            :disabled="processingTip || !trxId"
            class="flex-1 rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 disabled:cursor-not-allowed disabled:bg-gray-300"
            @click="confirmTip"
          >
            {{ processingTip ? 'Saving...' : 'Confirm Tip' }}
          </button>
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
        tip_message: 'Your tip helps me keep creating, learning, and improving for you.',
        bkash_number: '',
        nagad_number: '',
        rocket_number: '',
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
    selectedPaymentLabel() {
      const found = this.paymentMethods.find((method) => method.id === this.selectedPaymentMethod)
      return found?.name || 'Payment'
    },
    selectedPaymentNumber() {
      if (!this.tipInfo) return ''
      const map = {
        bkash: this.tipInfo.bkash_number,
        nagad: this.tipInfo.nagad_number,
        rocket: this.tipInfo.rocket_number,
      }
      return map[this.selectedPaymentMethod]
        || this.tipInfo.tip_phone_number
        || ''
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

      if (!this.selectedPaymentNumber) {
        this.$toast?.error?.('Selected payment number is not available');
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

    paymentButtonClass(methodId) {
      const base = 'rounded-2xl border px-3 py-2 text-sm font-semibold transition-all'
      const active = {
        bkash: 'border-amber-500 bg-amber-600 text-white shadow-md',
        nagad: 'border-amber-400 bg-amber-500 text-white shadow-md',
        rocket: 'border-sky-500 bg-sky-600 text-white shadow-md',
      }
      const inactive = 'border-amber-200/70 bg-white/80 text-gray-700 hover:border-amber-400'
      const activeClass = active[methodId] || active.bkash
      return `${base} ${this.selectedPaymentMethod === methodId ? activeClass : inactive}`
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
