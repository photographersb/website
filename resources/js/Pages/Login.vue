<!--
  DEPRECATED: This component is deprecated. Use Auth.vue (components/Auth.vue) instead.
  
  Reason: Auth.vue provides both login and registration in a unified interface.
  
  Migration: If you need to use this page, import Auth component instead:
  const Auth = () => import('../components/Auth.vue')
  
  This file will be removed in a future version.
-->
<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section with Branding -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white bg-opacity-5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white bg-opacity-5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
      </div>

      <div class="container mx-auto px-4 py-12 md:py-16 relative z-10 text-center">
        <div class="inline-block mb-4 px-6 py-2 bg-white bg-opacity-10 backdrop-blur-sm rounded-full border border-white border-opacity-20">
          <p class="text-sm md:text-base font-medium flex items-center gap-2 justify-center">
            <svg
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
            </svg>
            A Project by <a
              href="https://somogrobangladesh.com/"
              target="_blank"
              rel="noopener"
              class="underline hover:text-white hover:text-opacity-80 transition-colors"
            >Somogro Bangladesh</a>
          </p>
        </div>
        <h1 class="text-3xl md:text-5xl font-bold mb-2 tracking-tight">
          Welcome Back
        </h1>
        <p class="text-base md:text-lg text-gray-100">
          Login to Photographer SB
        </p>
      </div>
    </section>

    <!-- Login Form -->
    <div class="container mx-auto px-4 py-12 max-w-md">
      <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border border-gray-100">
        <h2 class="text-2xl font-bold mb-6">
          Sign In
        </h2>

        <form
          class="space-y-4"
          @submit.prevent="login"
        >
          <div>
            <label class="block text-sm font-medium mb-1">Email or Username</label>
            <input
              v-model="form.email"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent"
              placeholder="email or username"
            >
            <p
              v-if="errors.email"
              class="text-red-600 text-sm mt-1"
            >
              {{ errors.email[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent"
                placeholder="••••••••"
              >
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                @click="showPassword = !showPassword"
              >
                <svg
                  v-if="!showPassword"
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 3l18 18M9.172 9.172L21 21m0 0l-18-18m18 18l-18-18"
                  />
                </svg>
              </button>
            </div>
            <p
              v-if="errors.password"
              class="text-red-600 text-sm mt-1"
            >
              {{ errors.password[0] }}
            </p>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input
                v-model="form.remember_me"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600"
              >
              <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
            <router-link
              to="/forgot-password"
              class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors"
            >
              Forgot Password?
            </router-link>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50 font-semibold"
          >
            {{ loading ? 'Signing In...' : 'Sign In' }}
          </button>

          <p
            v-if="error"
            class="text-red-600 text-sm text-center"
          >
            {{ error }}
          </p>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-200">
          <p class="text-center text-gray-600 text-sm">
            Don't have an account?
            <router-link
              to="/register"
              class="text-primary-600 hover:text-primary-700 font-medium transition-colors"
            >
              Register here
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api, { ensureCsrfCookie } from '../api';

const router = useRouter();
const loading = ref(false);
const error = ref('');
const showPassword = ref(false);
const errors = ref({});

const form = ref({
  email: '',
  password: '',
  remember_me: false,
});

const normalizeRole = (role) => String(role || '').toLowerCase().replace(/[\s-]+/g, '_');

const login = async () => {
  loading.value = true;
  error.value = '';
  errors.value = {};

  try {
    await ensureCsrfCookie();
    const { data } = await api.post('/auth/login', form.value);

    if (data?.status === 'success' && data?.data?.user) {
      const { user } = data.data;

      localStorage.setItem('user', JSON.stringify(user));
      localStorage.setItem('user_role', normalizeRole(user?.role));

      if (user?.role === 'admin' || user?.role === 'super_admin' || user?.role === 'moderator') {
        router.push('/admin/dashboard');
      } else if (user?.role === 'photographer') {
        router.push('/photographer/dashboard');
      } else {
        router.push('/dashboard');
      }
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
      error.value = 'Please check the errors below';
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'Login failed. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  try {
    await ensureCsrfCookie();
    const { data } = await api.get('/auth/me');
    const user = data?.data || data?.user || data;
    if (user?.role) {
      localStorage.setItem('user', JSON.stringify(user));
      localStorage.setItem('user_role', normalizeRole(user.role));
      if (['admin', 'super_admin', 'moderator'].includes(user.role)) {
        router.push('/admin/dashboard');
      } else if (user.role === 'photographer') {
        router.push('/photographer/dashboard');
      } else {
        router.push('/dashboard');
      }
      return;
    }
  } catch (error) {
    // Fall back to login page if token is invalid or user lookup fails.
  }
});
</script>
