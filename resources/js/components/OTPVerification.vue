<template>
  <div class="otp-verification-card">
    <div class="text-center mb-6">
      <div class="mx-auto w-16 h-16 bg-burgundy-100 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-burgundy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-800">Verify Your Phone</h2>
      <p class="text-gray-600 mt-2">Enter the 6-digit code sent to</p>
      <p class="text-burgundy-600 font-semibold">{{ formattedPhone }}</p>
    </div>

    <form @submit.prevent="verifyOTP">
      <!-- OTP Input Fields -->
      <div class="flex justify-center gap-2 mb-6">
        <input
          v-for="(digit, index) in otpDigits"
          :key="index"
          :ref="el => otpInputs[index] = el"
          v-model="otpDigits[index]"
          type="text"
          inputmode="numeric"
          maxlength="1"
          class="otp-input"
          @input="handleInput(index)"
          @keydown="handleKeydown($event, index)"
          @paste="handlePaste"
          :disabled="loading"
        />
      </div>

      <!-- Timer & Resend -->
      <div class="text-center mb-6">
        <p v-if="timeLeft > 0" class="text-sm text-gray-600">
          Code expires in <span class="font-semibold text-burgundy-600">{{ formatTime(timeLeft) }}</span>
        </p>
        <button
          v-else
          type="button"
          @click="resendOTP"
          :disabled="resending"
          class="text-sm text-burgundy-600 hover:text-burgundy-700 font-medium underline disabled:opacity-50"
        >
          {{ resending ? 'Sending...' : 'Resend Code' }}
        </button>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-700 text-center">{{ error }}</p>
      </div>

      <!-- Success Message -->
      <div v-if="success" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm text-green-700 text-center">✓ {{ success }}</p>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="loading || otpComplete"
        class="w-full py-3 bg-burgundy-600 text-white font-semibold rounded-lg hover:bg-burgundy-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="loading">Verifying...</span>
        <span v-else>Verify Phone Number</span>
      </button>

      <!-- Change Number -->
      <button
        type="button"
        @click="$emit('change-number')"
        class="w-full mt-3 text-sm text-gray-600 hover:text-gray-800"
      >
        Change phone number?
      </button>
    </form>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';

export default {
  name: 'OTPVerification',
  props: {
    phone: {
      type: String,
      required: true,
    },
  },
  emits: ['verified', 'change-number'],
  setup(props, { emit }) {
    const otpDigits = ref(['', '', '', '', '', '']);
    const otpInputs = ref([]);
    const loading = ref(false);
    const resending = ref(false);
    const error = ref('');
    const success = ref('');
    const timeLeft = ref(300); // 5 minutes in seconds
    let timer = null;

    const formattedPhone = computed(() => {
      // Format: 01XX-XXX-XXX
      const phone = props.phone.replace(/\D/g, '');
      if (phone.length === 11) {
        return `${phone.slice(0, 4)}-${phone.slice(4, 7)}-${phone.slice(7)}`;
      }
      return props.phone;
    });

    const otpComplete = computed(() => {
      return otpDigits.value.every(d => d !== '');
    });

    const formatTime = (seconds) => {
      const mins = Math.floor(seconds / 60);
      const secs = seconds % 60;
      return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    const handleInput = (index) => {
      // Only allow digits
      otpDigits.value[index] = otpDigits.value[index].replace(/\D/g, '');
      
      // Auto-focus next input
      if (otpDigits.value[index] && index < 5) {
        otpInputs.value[index + 1]?.focus();
      }

      // Auto-submit when all digits entered
      if (otpComplete.value) {
        verifyOTP();
      }
    };

    const handleKeydown = (event, index) => {
      // Backspace: clear current and focus previous
      if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
        otpInputs.value[index - 1]?.focus();
      }
      
      // Arrow keys navigation
      if (event.key === 'ArrowLeft' && index > 0) {
        otpInputs.value[index - 1]?.focus();
      }
      if (event.key === 'ArrowRight' && index < 5) {
        otpInputs.value[index + 1]?.focus();
      }
    };

    const handlePaste = (event) => {
      event.preventDefault();
      const paste = event.clipboardData.getData('text').replace(/\D/g, '');
      
      if (paste.length === 6) {
        otpDigits.value = paste.split('');
        otpInputs.value[5]?.focus();
        // Auto-submit
        setTimeout(verifyOTP, 100);
      }
    };

    const verifyOTP = async () => {
      const otp = otpDigits.value.join('');
      if (otp.length !== 6) return;

      loading.value = true;
      error.value = '';
      success.value = '';

      try {
        const response = await fetch('/api/v1/auth/verify-otp', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            phone: props.phone,
            otp: otp,
          }),
        });

        const data = await response.json();

        if (data.status === 'success') {
          success.value = 'Phone verified successfully!';
          setTimeout(() => {
            emit('verified', data.data);
          }, 1000);
        } else {
          error.value = data.message || 'Invalid OTP. Please try again.';
          // Clear OTP on error
          otpDigits.value = ['', '', '', '', '', ''];
          otpInputs.value[0]?.focus();
        }
      } catch (err) {
        error.value = 'Network error. Please try again.';
        console.error('OTP verification error:', err);
      } finally {
        loading.value = false;
      }
    };

    const resendOTP = async () => {
      resending.value = true;
      error.value = '';

      try {
        const response = await fetch('/api/v1/auth/resend-otp', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            phone: props.phone,
          }),
        });

        const data = await response.json();

        if (data.status === 'success') {
          success.value = 'New code sent!';
          timeLeft.value = 300;
          startTimer();
          setTimeout(() => {
            success.value = '';
          }, 3000);
        } else {
          error.value = data.message || 'Failed to resend code';
        }
      } catch (err) {
        error.value = 'Network error. Please try again.';
      } finally {
        resending.value = false;
      }
    };

    const startTimer = () => {
      if (timer) clearInterval(timer);
      
      timer = setInterval(() => {
        if (timeLeft.value > 0) {
          timeLeft.value--;
        } else {
          clearInterval(timer);
        }
      }, 1000);
    };

    onMounted(() => {
      startTimer();
      otpInputs.value[0]?.focus();
    });

    onUnmounted(() => {
      if (timer) clearInterval(timer);
    });

    return {
      otpDigits,
      otpInputs,
      loading,
      resending,
      error,
      success,
      timeLeft,
      formattedPhone,
      otpComplete,
      formatTime,
      handleInput,
      handleKeydown,
      handlePaste,
      verifyOTP,
      resendOTP,
    };
  },
};
</script>

<style scoped>
.otp-verification-card {
  @apply max-w-md mx-auto p-8 bg-white rounded-2xl shadow-lg;
}

.otp-input {
  @apply w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-burgundy-500 focus:ring-2 focus:ring-burgundy-200 focus:outline-none transition-all disabled:bg-gray-100;
}

.otp-input:focus {
  transform: scale(1.05);
}

@media (max-width: 640px) {
  .otp-input {
    @apply w-10 h-12 text-xl;
  }
}
</style>
