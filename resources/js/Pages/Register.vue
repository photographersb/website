<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section with Branding -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 text-white">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
      </div>

      <div class="container mx-auto px-4 py-12 md:py-16 relative z-10 text-center">
        <div class="inline-block mb-4 px-6 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
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
              class="underline hover:text-white/80 transition-colors"
            >Somogro Bangladesh</a>
          </p>
        </div>
        <h1 class="text-3xl md:text-5xl font-bold mb-2 tracking-tight">
          Join Photographer SB
        </h1>
        <p class="text-base md:text-lg text-gray-100">
          Create your account and get started
        </p>
      </div>
    </section>

    <!-- Registration Form -->
    <div class="container mx-auto px-4 py-12 max-w-md">
      <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border border-gray-100">
        <h2 class="text-2xl font-bold mb-6">
          Create Account
        </h2>

        <form
          class="space-y-4"
          @submit.prevent="register"
        >
          <div>
            <label class="block text-sm font-medium mb-1">Full Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent"
              placeholder="Your Full Name"
            >
            <p
              v-if="errors.name"
              class="text-red-600 text-sm mt-1"
            >
              {{ errors.name[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Email Address</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent"
              placeholder="your@email.com"
            >
            <p
              v-if="errors.email"
              class="text-red-600 text-sm mt-1"
            >
              {{ errors.email[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Phone Number</label>
            <input
              v-model="form.phone"
              type="tel"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent"
              placeholder="+880..."
            >
            <p
              v-if="errors.phone"
              class="text-red-600 text-sm mt-1"
            >
              {{ errors.phone[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">I am a</label>
            <select
              v-model="form.role"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent"
            >
              <option value="">
                Select your role
              </option>
              <option value="client">
                Client (Looking for photographer)
              </option>
              <option value="photographer">
                Photographer
              </option>
              <option value="studio_owner">
                Studio Owner
              </option>
            </select>
            <p
              v-if="errors.role"
              class="text-red-600 text-sm mt-1"
            >
              {{ errors.role[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                minlength="8"
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
            <p class="text-gray-500 text-xs mt-1">
              Minimum 8 characters
            </p>
            <p
              v-if="errors.password"
              class="text-red-600 text-sm mt-1"
            >
              {{ errors.password[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Confirm Password</label>
            <div class="relative">
              <input
                v-model="form.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent"
                placeholder="••••••••"
              >
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
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
            <p
              v-if="errors.password_confirmation"
              class="text-red-600 text-sm mt-1"
            >
              {{ errors.password_confirmation[0] }}
            </p>
          </div>

          <!-- Terms Agreement - REQUIRED -->
          <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
              <input
                id="terms"
                v-model="acceptTerms"
                type="checkbox"
                required
                class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-2 focus:ring-red-500 mt-1 cursor-pointer flex-shrink-0"
              >
              <div class="flex-1">
                <label
                  for="terms"
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
            <p
              v-if="errors.accept_terms"
              class="text-red-600 text-sm mt-3 font-semibold"
            >
              ⚠️ {{ errors.accept_terms[0] || 'You must accept the Terms of Service and Privacy Policy' }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50 font-semibold"
          >
            {{ loading ? 'Creating Account...' : 'Create Account' }}
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
            Already have an account?
            <router-link
              to="/login"
              class="text-primary-600 hover:text-primary-700 font-medium transition-colors"
            >
              Login here
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api, { ensureCsrfCookie } from '../api';

const router = useRouter();
const loading = ref(false);
const error = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const acceptTerms = ref(false);
const errors = ref({});

const form = ref({
  name: '',
  email: '',
  phone: '',
  role: '',
  password: '',
  password_confirmation: '',
});

const register = async () => {
  if (!acceptTerms.value) {
    errors.value.accept_terms = ['You must check the box to accept the Terms of Service and Privacy Policy'];
    error.value = '⚠️ Please check the Terms of Service and Privacy Policy checkbox above to continue';
    return;
  }

  loading.value = true;
  error.value = '';
  errors.value = {};

  try {
    const payload = {
      ...form.value,
      accept_terms: acceptTerms.value
    };

    await ensureCsrfCookie();
    const { data } = await api.post('/auth/register', payload);

    if (data?.status === 'success' || data?.data?.user_id) {
      error.value = '';
      router.push('/login');
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
      // Check if terms acceptance error
      if (errors.value.accept_terms) {
        error.value = '⚠️ TERMS ACCEPTANCE REQUIRED - Please check the Terms of Service and Privacy Policy box above';
      } else {
        error.value = 'Please check the errors below';
      }
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'Registration failed. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>
