<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
      </div>

      <div class="container mx-auto px-4 py-12 md:py-16 relative z-10 text-center">
        <h1 class="text-3xl md:text-5xl font-bold mb-2 tracking-tight">
          Reset Your Password
        </h1>
        <p class="text-base md:text-lg text-gray-100">
          We'll help you get back into your account
        </p>
      </div>
    </section>

    <!-- Reset Form -->
    <div class="container mx-auto px-4 py-12 max-w-md">
      <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border border-gray-100">
        <!-- Step 1: Enter Email -->
        <div v-if="step === 1">
          <h2 class="text-2xl font-bold mb-2">
            Enter Your Email
          </h2>
          <p class="text-gray-600 mb-6 text-sm">
            We'll send you a link to reset your password
          </p>

          <form
            class="space-y-4"
            @submit.prevent="sendResetLink"
          >
            <div>
              <label class="block text-sm font-medium mb-1">Email Address</label>
              <input
                v-model="forgotForm.email"
                type="email"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
                placeholder="your@email.com"
              >
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] transition-colors disabled:opacity-50 font-semibold"
            >
              {{ loading ? 'Sending...' : 'Send Reset Link' }}
            </button>

            <p
              v-if="error"
              class="text-red-600 text-sm"
            >
              {{ error }}
            </p>

            <router-link
              to="/auth"
              class="block text-center text-burgundy hover:text-[#6F112D] font-medium text-sm transition-colors"
            >
              Back to Login
            </router-link>
          </form>
        </div>

        <!-- Step 2: Reset Password -->
        <div v-else-if="step === 2">
          <h2 class="text-2xl font-bold mb-2">
            Create New Password
          </h2>
          <p class="text-gray-600 mb-6 text-sm">
            Enter a strong password for your account
          </p>

          <form
            class="space-y-4"
            @submit.prevent="resetPassword"
          >
            <div>
              <label class="block text-sm font-medium mb-1">New Password</label>
              <input
                v-model="resetForm.password"
                type="password"
                required
                minlength="8"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
                placeholder="••••••••"
              >
              <p class="text-gray-500 text-xs mt-1">
                Minimum 8 characters
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Confirm Password</label>
              <input
                v-model="resetForm.password_confirmation"
                type="password"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
                placeholder="••••••••"
              >
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] transition-colors disabled:opacity-50 font-semibold"
            >
              {{ loading ? 'Resetting...' : 'Reset Password' }}
            </button>

            <p
              v-if="error"
              class="text-red-600 text-sm"
            >
              {{ error }}
            </p>

            <router-link
              to="/auth"
              class="block text-center text-burgundy hover:text-[#6F112D] font-medium text-sm transition-colors"
            >
              Back to Login
            </router-link>
          </form>
        </div>

        <!-- Step 3: Success -->
        <div
          v-else-if="step === 3"
          class="text-center"
        >
          <div class="mb-4">
            <svg
              class="w-16 h-16 mx-auto text-green-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
          </div>

          <h2 class="text-2xl font-bold mb-2">
            Password Reset Successfully!
          </h2>
          <p class="text-gray-600 mb-6">
            Your password has been reset. You can now login with your new password.
          </p>

          <router-link
            to="/auth"
            class="inline-block bg-burgundy text-white px-6 py-3 rounded-lg hover:bg-[#6F112D] transition-colors font-semibold"
          >
            Go to Login
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';

const router = useRouter();
const step = ref(1);
const loading = ref(false);
const error = ref('');
const resetToken = ref('');

const forgotForm = ref({
  email: '',
});

const resetForm = ref({
  password: '',
  password_confirmation: '',
});

const sendResetLink = async () => {
  loading.value = true;
  error.value = '';

  try {
    const { data } = await api.post('/auth/forgot-password', forgotForm.value);

    if (data.status === 'success') {
      // In a real app, user would receive email with token
      // For now, show a message
      error.value = '';
      step.value = 2;
      resetToken.value = data.token || '';
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to send reset link. Please check your email.';
  } finally {
    loading.value = false;
  }
};

const resetPassword = async () => {
  loading.value = true;
  error.value = '';

  if (resetForm.value.password !== resetForm.value.password_confirmation) {
    error.value = 'Passwords do not match';
    loading.value = false;
    return;
  }

  try {
    const { data } = await api.post('/auth/reset-password', {
      email: forgotForm.value.email,
      token: resetToken.value,
      password: resetForm.value.password,
      password_confirmation: resetForm.value.password_confirmation,
    });

    if (data.status === 'success') {
      step.value = 3;
      setTimeout(() => {
        router.push('/auth');
      }, 2000);
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to reset password. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>
