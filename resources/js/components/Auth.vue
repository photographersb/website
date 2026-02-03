<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section with Branding -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
      </div>

      <div class="container mx-auto px-4 py-12 md:py-16 relative z-10 text-center">
        <div class="inline-block mb-4 px-6 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
          <p class="text-sm md:text-base font-medium flex items-center gap-2 justify-center">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
            </svg>
            A Project by <a href="https://somogrobangladesh.com/" target="_blank" rel="noopener" class="underline hover:text-white/80 transition-colors">Somogro Bangladesh</a>
          </p>
        </div>
        <h1 class="text-3xl md:text-5xl font-bold mb-2 tracking-tight">Join Photographer SB</h1>
        <p class="text-base md:text-lg text-gray-100">Connect with photographers or showcase your work</p>
      </div>
    </section>

    <!-- Auth Forms -->
    <div class="container mx-auto px-4 py-12 max-w-md">
      <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border border-gray-100">
        <!-- Tabs -->
        <div class="flex gap-4 mb-8 border-b">
          <button
            @click="isLogin = true"
            :class="`pb-3 font-semibold transition-colors ${isLogin ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-500 hover:text-gray-700'}`"
          >
            Login
          </button>
          <button
            @click="isLogin = false"
            :class="`pb-3 font-semibold transition-colors ${!isLogin ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-500 hover:text-gray-700'}`"
          >
            Register
          </button>
        </div>

      <!-- Login Form -->
      <form v-if="isLogin" @submit.prevent="login" class="space-y-4">
        <h2 class="text-2xl font-bold mb-6">Welcome Back</h2>

        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input
            v-model="loginForm.email"
            type="email"
            required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
            placeholder="your@email.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <div class="relative">
            <input
              v-model="loginForm.password"
              :type="showLoginPassword ? 'text' : 'password'"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
              placeholder="••••••••"
            />
            <button
              type="button"
              @click="showLoginPassword = !showLoginPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
            >
              <svg v-if="!showLoginPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M9.172 9.172L21 21m0 0l-18-18m18 18l-18-18"></path>
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
          <router-link to="/forgot-password" class="text-burgundy hover:text-[#6F112D] font-medium transition-colors">
            Forgot Password?
          </router-link>
        </div>

        <p v-if="loginError" class="text-red-600 text-sm">{{ loginError }}</p>
      </form>

      <!-- Register Form -->
      <form v-else @submit.prevent="register" class="space-y-4">
        <h2 class="text-2xl font-bold mb-6">Create Account</h2>

        <div>
          <label class="block text-sm font-medium mb-1">Full Name</label>
          <input
            v-model="registerForm.name"
            type="text"
            required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
            placeholder="Your Name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input
            v-model="registerForm.email"
            type="email"
            required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
            placeholder="your@email.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Phone</label>
          <input
            v-model="registerForm.phone"
            type="tel"
            required
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
            placeholder="+880..."
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">I am a</label>
          <select
            v-model="registerForm.role"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
          >
            <option value="client">Client (Looking for photographer)</option>
            <option value="photographer">Photographer</option>
            <option value="studio_owner">Studio Owner</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <div class="relative">
            <input
              v-model="registerForm.password"
              :type="showRegisterPassword ? 'text' : 'password'"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
              placeholder="••••••••"
            />
            <button
              type="button"
              @click="showRegisterPassword = !showRegisterPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
            >
              <svg v-if="!showRegisterPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M9.172 9.172L21 21m0 0l-18-18m18 18l-18-18"></path>
              </svg>
            </button>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Confirm Password</label>
          <div class="relative">
            <input
              v-model="registerForm.password_confirmation"
              :type="showConfirmPassword ? 'text' : 'password'"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-burgundy focus:border-transparent"
              placeholder="••••••••"
            />
            <button
              type="button"
              @click="showConfirmPassword = !showConfirmPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
            >
              <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M9.172 9.172L21 21m0 0l-18-18m18 18l-18-18"></path>
              </svg>
            </button>
          </div>
        </div>

        <button
          type="submit"
          :disabled="registerLoading"
          class="w-full bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] transition-colors disabled:opacity-50 font-semibold"
        >
          {{ registerLoading ? 'Creating account...' : 'Create Account' }}
        </button>

        <p v-if="registerError" class="text-red-600 text-sm">{{ registerError }}</p>
      </form>
    </div>
  </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { notifySuccess, notifyWarning, notifyError } from '../utils/notifications';

const router = useRouter();
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
  password: '',
  password_confirmation: '',
});

const loginLoading = ref(false);
const registerLoading = ref(false);
const loginError = ref('');
const registerError = ref('');

const login = async () => {
  loginLoading.value = true;
  loginError.value = '';

  try {
    console.log('Login attempt:', { email: loginForm.value.email, password: '***' });
    const { data } = await api.post('/auth/login', loginForm.value);
    console.log('Login response:', data);

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

      localStorage.setItem('auth_token', data.data.token);
      localStorage.setItem('user', JSON.stringify(data.data.user));
      localStorage.setItem('user_role', data.data.user.role);
      localStorage.setItem('user_name', data.data.user.name);
      localStorage.setItem('user_email', data.data.user.email);
      localStorage.setItem('user_id', data.data.user.id);
      
      notifySuccess('Welcome back!', 'Login Successful');
      
      // Redirect based on user role
      const userRole = data.data.user.role;
      if (userRole === 'admin' || userRole === 'super_admin') {
        router.push('/admin/dashboard');
      } else if (userRole === 'photographer') {
        router.push('/dashboard');
      } else {
        router.push('/');
      }
    }
  } catch (error) {
    console.error('Login error:', error.response?.data || error);
    const message = error.response?.data?.message || error.response?.data?.errors?.email?.[0] || 'Login failed. Please check your credentials.';
    loginError.value = message;
    notifyError(message, 'Login Failed');
  } finally {
    loginLoading.value = false;
  }
};

const register = async () => {
  registerLoading.value = true;
  registerError.value = '';

  try {
    const { data } = await api.post('/auth/register', registerForm.value);

    if (data.status === 'success') {
      registerError.value = '';
      // Store pending verification info
      localStorage.setItem('pending_email', registerForm.value.email);
      localStorage.setItem('pending_user_id', data.data?.user_id);
      
      notifySuccess('Please check your email inbox for the verification link. Check spam folder if you don\'t see it.', 'Registration Successful! 🎉');
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
</script>
