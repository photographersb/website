<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section with Branding -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white bg-opacity-5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white bg-opacity-5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
      </div>

      <div class="container mx-auto px-4 pt-10 pb-16 md:pt-10 md:pb-24 relative z-10 text-center">
        <div class="inline-flex mt-5 mb-2 sm:mb-3 px-3 sm:px-4 py-1 sm:py-1.5 bg-white bg-opacity-10 backdrop-blur-sm rounded-full border border-white border-opacity-20">
          <p class="text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-2 justify-center">
            <svg
              class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"
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
        <h1 class="text-3xl md:text-5xl font-bold mb-4 tracking-tight">
          Join Photographer SB
        </h1>
        <p class="text-base md:text-lg text-gray-100 mb-8">
          Connect with photographers or showcase your work
        </p>
      </div>
    </section>

    <!-- Auth Forms -->
    <div class="container mx-auto px-4 py-12 max-w-md mt-6 md:mt-8 mb-10 md:mb-12">
      <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border border-gray-100">
        <!-- Tabs -->
        <div class="flex gap-4 mb-8 border-b" role="tablist" aria-label="Authentication tabs">
          <button
            id="auth-tab-login"
            type="button"
            role="tab"
            :aria-selected="isLogin"
            aria-controls="auth-login-panel"
            :class="`pb-3 font-semibold transition-colors ${isLogin ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-500 hover:text-gray-700'}`"
            @click="isLogin = true"
          >
            Login
          </button>
          <button
            id="auth-tab-register"
            type="button"
            role="tab"
            :aria-selected="!isLogin"
            aria-controls="auth-register-panel"
            :class="`pb-3 font-semibold transition-colors ${!isLogin ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-500 hover:text-gray-700'}`"
            @click="isLogin = false"
          >
            Register
          </button>
        </div>

        <!-- Login Form -->
        <form
          v-if="isLogin"
          id="auth-login-panel"
          role="tabpanel"
          aria-labelledby="auth-tab-login"
          class="space-y-4"
          @submit.prevent="login"
        >
          <h2 class="text-2xl font-bold mb-6">
            Welcome Back
          </h2>

          <div>
            <label for="auth-login-email" class="block text-sm font-medium mb-1">Email or Username</label>
            <input
              id="auth-login-email"
              v-model="loginForm.email"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
              placeholder="email or username"
            >
          </div>

          <div>
            <label for="auth-login-password" class="block text-sm font-medium mb-1">Password</label>
            <div class="relative">
              <input
                id="auth-login-password"
                v-model="loginForm.password"
                :type="showLoginPassword ? 'text' : 'password'"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
                placeholder="••••••••"
              >
              <button
                type="button"
                :aria-label="showLoginPassword ? 'Hide password' : 'Show password'"
                :aria-pressed="showLoginPassword"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 p-1.5 rounded-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
                @click="showLoginPassword = !showLoginPassword"
              >
                <svg
                  v-if="!showLoginPassword"
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
          </div>

          <button
            type="submit"
            :disabled="loginLoading"
            class="w-full bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] transition-colors disabled:opacity-50 font-semibold"
          >
            {{ loginLoading ? 'Logging in...' : 'Login' }}
          </button>

          <div class="flex justify-between items-center text-sm">
            <router-link
              to="/forgot-password"
              class="text-burgundy hover:text-[#6F112D] font-medium transition-colors"
            >
              Forgot Password?
            </router-link>
          </div>

          <p
            v-if="loginError"
            class="text-red-600 text-sm"
          >
            {{ loginError }}
          </p>

          <!-- Always show Resend Verification Button -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
            <p class="text-sm text-gray-700 mb-3">
              📧 <strong>Email not verified?</strong> Check your inbox or spam folder.
            </p>
            <button
              type="button"
              :disabled="resendingVerification"
              class="w-full bg-burgundy text-white py-2 rounded-lg hover:bg-[#6F112D] transition-colors disabled:opacity-50 text-sm font-medium"
              @click="resendVerification"
            >
              {{ resendingVerification ? 'Sending...' : 'Resend Verification Email' }}
            </button>
          </div>

          <!-- Social Login Divider -->
          <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300" />
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
          </div>

          <!-- Social Login Buttons -->
          <div class="grid grid-cols-2 gap-3">
            <a
              href="/api/v1/auth/google/redirect"
              class="flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700"
            >
              <svg
                class="w-5 h-5"
                viewBox="0 0 24 24"
              >
                <path
                  fill="#4285F4"
                  d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                />
                <path
                  fill="#34A853"
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                />
                <path
                  fill="#FBBC05"
                  d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                />
                <path
                  fill="#EA4335"
                  d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                />
              </svg>
              <span class="text-sm">Google</span>
            </a>
            <a
              href="/api/v1/auth/facebook/redirect"
              class="flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700"
            >
              <svg
                class="w-5 h-5"
                fill="#1877F2"
                viewBox="0 0 24 24"
              >
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
              </svg>
              <span class="text-sm">Facebook</span>
            </a>
          </div>
        </form>

        <!-- Register Form -->
        <form
          v-else
          id="auth-register-panel"
          role="tabpanel"
          aria-labelledby="auth-tab-register"
          class="space-y-4"
          @submit.prevent="register"
        >
          <h2 class="text-2xl font-bold mb-6">
            Create Account
          </h2>

          <div>
            <label for="auth-register-name" class="block text-sm font-medium mb-1">Full Name</label>
            <input
              id="auth-register-name"
              v-model="registerForm.name"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
              placeholder="Your Name"
            >
          </div>

          <div>
            <label for="auth-register-email" class="block text-sm font-medium mb-1">Email</label>
            <input
              id="auth-register-email"
              v-model="registerForm.email"
              type="email"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
              placeholder="your@email.com"
            >
          </div>

          <div>
            <label for="auth-register-phone" class="block text-sm font-medium mb-1">Phone</label>
            <input
              id="auth-register-phone"
              v-model="registerForm.phone"
              type="tel"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
              placeholder="+880..."
            >
          </div>

          <div>
            <label for="auth-register-role" class="block text-sm font-medium mb-1">I am a</label>
            <select
              id="auth-register-role"
              v-model="registerForm.role"
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
            >
              <option value="client">
                Client (Looking for photographer)
              </option>
              <option value="photographer">
                Photographer
              </option>
            </select>
          </div>

          <div>
            <label for="auth-register-ref" class="block text-sm font-medium mb-1">Referral Code (optional)</label>
            <input
              id="auth-register-ref"
              v-model="registerForm.referral_code"
              type="text"
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
              placeholder="Enter referral code"
            >
          </div>

          <div>
            <label for="auth-register-password" class="block text-sm font-medium mb-1">Password</label>
            <div class="relative">
              <input
                id="auth-register-password"
                v-model="registerForm.password"
                :type="showRegisterPassword ? 'text' : 'password'"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
                placeholder="••••••••"
              >
              <button
                type="button"
                :aria-label="showRegisterPassword ? 'Hide password' : 'Show password'"
                :aria-pressed="showRegisterPassword"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 p-1.5 rounded-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
                @click="showRegisterPassword = !showRegisterPassword"
              >
                <svg
                  v-if="!showRegisterPassword"
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
          </div>

          <div>
            <label for="auth-register-password-confirm" class="block text-sm font-medium mb-1">Confirm Password</label>
            <div class="relative">
              <input
                id="auth-register-password-confirm"
                v-model="registerForm.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
                placeholder="••••••••"
              >
              <button
                type="button"
                :aria-label="showConfirmPassword ? 'Hide confirm password' : 'Show confirm password'"
                :aria-pressed="showConfirmPassword"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 p-1.5 rounded-md focus-visible:ring-2 focus-visible:ring-burgundy/30"
                @click="showConfirmPassword = !showConfirmPassword"
              >
                <svg
                  v-if="!showConfirmPassword"
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
          </div>

          <!-- Terms Agreement - REQUIRED -->
          <div class="bg-red-50 border-2 border-red-300 rounded-lg p-4 mt-4">
            <div class="flex items-start gap-3">
              <input
                id="register-terms"
                v-model="registerForm.accept_terms"
                type="checkbox"
                required
                class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-2 focus:ring-red-500 mt-1 cursor-pointer flex-shrink-0"
              >
              <div class="flex-1">
                <label
                  for="register-terms"
                  class="text-sm font-semibold text-gray-900 cursor-pointer block leading-relaxed"
                >
                  I agree to the
                  <router-link
                    to="/terms"
                    class="text-red-600 hover:text-red-700 font-bold underline transition-colors"
                  >Terms of Service</router-link>
                  and
                  <router-link
                    to="/privacy"
                    class="text-red-600 hover:text-red-700 font-bold underline transition-colors"
                  >Privacy Policy</router-link>
                  <span class="text-red-600 font-bold ml-1">*</span>
                </label>
                <p class="text-xs text-gray-600 mt-2">
                  You must check this box to create an account.
                </p>
              </div>
            </div>
          </div>

          <button
            type="submit"
            :disabled="registerLoading"
            class="w-full bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] transition-colors disabled:opacity-50 font-semibold"
          >
            {{ registerLoading ? 'Creating account...' : 'Create Account' }}
          </button>

          <p
            v-if="registerError"
            class="text-red-600 text-sm"
          >
            {{ registerError }}
          </p>

          <!-- Social Login Divider -->
          <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300" />
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">Or sign up with</span>
            </div>
          </div>

          <!-- Social Login Buttons -->
          <div class="grid grid-cols-2 gap-3">
            <a
              href="/api/v1/auth/google/redirect"
              class="flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700"
            >
              <svg
                class="w-5 h-5"
                viewBox="0 0 24 24"
              >
                <path
                  fill="#4285F4"
                  d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                />
                <path
                  fill="#34A853"
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                />
                <path
                  fill="#FBBC05"
                  d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                />
                <path
                  fill="#EA4335"
                  d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                />
              </svg>
              <span class="text-sm">Google</span>
            </a>
            <a
              href="/api/v1/auth/facebook/redirect"
              class="flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700"
            >
              <svg
                class="w-5 h-5"
                fill="#1877F2"
                viewBox="0 0 24 24"
              >
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
              </svg>
              <span class="text-sm">Facebook</span>
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api, { ensureCsrfCookie } from '../api';
import { notifySuccess, notifyWarning, notifyError } from '../utils/notifications';

const router = useRouter();
const route = useRoute();
const isLogin = ref(true);
const showLoginPassword = ref(false);
const showRegisterPassword = ref(false);
const showConfirmPassword = ref(false);

const loginForm = ref({
  email: '',
  password: '',
});

const registerForm = ref({
  name: '',
  email: '',
  phone: '',
  role: 'client',
  referral_code: '',
  password: '',
  password_confirmation: '',
  accept_terms: false,
});

const loginLoading = ref(false);
const registerLoading = ref(false);
const loginError = ref('');
const registerError = ref('');
const showResendVerification = ref(false);
const resendingVerification = ref(false);

const syncTabFromRoute = () => {
  if (route.path === '/register') {
    isLogin.value = false;
  }

  const tab = String(route.query?.tab || '').toLowerCase();
  if (tab === 'register') {
    isLogin.value = false;
  } else if (tab === 'login') {
    isLogin.value = true;
  }
  
  // Clear errors and resend button when switching tabs
  loginError.value = '';
  registerError.value = '';
  showResendVerification.value = false;
};

const normalizeRole = (role) => String(role || '').toLowerCase().replace(/[\s-]+/g, '_');

const resolvePostLoginRoute = (role) => {
  const normalizedRole = normalizeRole(role);

  if (['admin', 'super_admin', 'moderator'].includes(normalizedRole)) {
    return '/admin/dashboard';
  }

  if (normalizedRole === 'photographer' || normalizedRole === 'studio_owner') {
    return '/dashboard';
  }

  if (normalizedRole === 'judge') {
    return '/judge/dashboard';
  }

  if (normalizedRole === 'client') {
    return '/client/dashboard';
  }

  return '/';
};

const login = async () => {
  loginLoading.value = true;
  loginError.value = '';
  showResendVerification.value = false;

  try {
    if (import.meta.env.DEV) {
      console.log('Login attempt:', { email: loginForm.value.email, password: '***' });
    }
    await ensureCsrfCookie();
    const { data } = await api.post('/auth/login', loginForm.value);
    if (import.meta.env.DEV) {
      console.log('Login response:', data);
    }

    if (data.status === 'success') {
      // Check if email is verified
      if (!data.data.user.email_verified_at) {
        // Redirect to email verification page
        localStorage.setItem('pending_email', data.data.user.email);
        localStorage.setItem('pending_user_id', data.data.user.id);
        notifyWarning('Please check your email and verify your account before logging in.', 'Email Verification Required');
        // In production, redirect to /verify-email
        // router.push('/verify-email');
        return;
      }

      localStorage.setItem('user', JSON.stringify(data.data.user));
      localStorage.setItem('user_role', normalizeRole(data.data.user.role));
      localStorage.setItem('user_name', data.data.user.name);
      localStorage.setItem('user_email', data.data.user.email);
      localStorage.setItem('user_id', data.data.user.id);
      localStorage.setItem('token', data.data.token);
      
      notifySuccess('Welcome back!', 'Login Successful');
      
      const targetRoute = resolvePostLoginRoute(data.data.user.role);
      await router.replace(targetRoute);
    }
  } catch (error) {
    console.error('Login error:', error.response?.data || error);
    const message = error.response?.data?.message || error.response?.data?.errors?.email?.[0] || 'Login failed. Please check your credentials.';
    loginError.value = message;
    notifyError(message, 'Login Failed');
    
    // Show resend verification button if email not verified
    if (message.toLowerCase().includes('verify') || message.toLowerCase().includes('verification')) {
      showResendVerification.value = true;
    }
  } finally {
    loginLoading.value = false;
  }
};

const resendVerification = async () => {
  resendingVerification.value = true;
  
  try {
    await ensureCsrfCookie();
    const { data } = await api.post('/auth/resend-verification', { 
      email: loginForm.value.email 
    });
    
    if (data.status === 'success') {
      notifySuccess('Verification email sent! Please check your inbox and spam folder.', 'Email Sent');
      showResendVerification.value = false;
    }
  } catch (error) {
    const message = error.response?.data?.message || 'Failed to resend verification email. Please try again.';
    notifyError(message, 'Failed to Send');
  } finally {
    resendingVerification.value = false;
  }
};

const register = async () => {
  registerLoading.value = true;
  registerError.value = '';

  // Client-side validation for terms
  if (!registerForm.value.accept_terms) {
    registerError.value = 'You must agree to the Terms of Service and Privacy Policy to create an account.';
    registerLoading.value = false;
    return;
  }

  try {
    await ensureCsrfCookie();
    const { data } = await api.post('/auth/register', registerForm.value);

    if (data.status === 'success') {
      registerError.value = '';
      // Store pending verification info
      localStorage.setItem('pending_email', registerForm.value.email);
      localStorage.setItem('pending_user_id', data.data?.user_id);
      
      notifySuccess('Please check your email inbox for the verification link. Check spam folder if you don\'t see it.', 'Registration Successful!');
      // In production, redirect to /verify-email with token
      // router.push(`/verify-email?email=${registerForm.value.email}`);
      
      isLogin.value = true;
      loginForm.value.email = registerForm.value.email;
    }
  } catch (error) {
    const errors = error.response?.data?.errors;
    if (errors) {
      const firstError = Object.values(errors)[0]?.[0];
      registerError.value = firstError || error.response?.data?.message || 'Registration failed';
    } else {
      registerError.value = error.response?.data?.message || 'Registration failed. Please try again.';
    }
  } finally {
    registerLoading.value = false;
  }
};

onMounted(() => {
  syncTabFromRoute();

  const referral = typeof route.query?.ref === 'string' ? route.query.ref.trim() : '';
  if (referral) {
    isLogin.value = false;
    registerForm.value.referral_code = referral;
  }
  
  // Check for email verification status
  if (route.query.verified === 'success') {
    isLogin.value = true;
    notifySuccess('Email verified successfully! You can now login.', 'Verification Complete');
  } else if (route.query.verified === 'already') {
    isLogin.value = true;
    notifyWarning('Email already verified. You can login now.', 'Already Verified');
  } else if (route.query.error === 'invalid_verification_link') {
    notifyError('Invalid or expired verification link. Please request a new one.', 'Verification Failed');
  }
});

watch(
  () => route.query?.tab,
  () => syncTabFromRoute()
);
</script>
