<template>
  <div class="social-login">
    <div class="social-buttons-container">
      <!-- Google Login Button -->
      <button
        :disabled="loading"
        class="social-btn google-btn"
        type="button"
        @click="loginWith('google')"
      >
        <svg
          class="social-icon"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
            fill="#4285F4"
          />
          <path
            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
            fill="#34A853"
          />
          <path
            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
            fill="#FBBC05"
          />
          <path
            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
            fill="#EA4335"
          />
        </svg>
        <span v-if="loading && loadingProvider === 'google'">Redirecting...</span>
        <span v-else>Continue with Google</span>
      </button>

      <!-- Facebook Login Button -->
      <button
        :disabled="loading"
        class="social-btn facebook-btn"
        type="button"
        @click="loginWith('facebook')"
      >
        <svg
          class="social-icon"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
            fill="#1877F2"
          />
        </svg>
        <span v-if="loading && loadingProvider === 'facebook'">Redirecting...</span>
        <span v-else>Continue with Facebook</span>
      </button>

      <!-- Apple Login Button (Optional) -->
      <button
        v-if="showApple"
        :disabled="loading"
        class="social-btn apple-btn"
        type="button"
        @click="loginWith('apple')"
      >
        <svg
          class="social-icon"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"
            fill="#000000"
          />
        </svg>
        <span v-if="loading && loadingProvider === 'apple'">Redirecting...</span>
        <span v-else>Continue with Apple</span>
      </button>
    </div>

    <!-- Divider -->
    <div class="divider">
      <span>or</span>
    </div>

    <!-- Error Message -->
    <div
      v-if="error"
      class="error-message"
    >
      <svg
        class="error-icon"
        viewBox="0 0 20 20"
        fill="currentColor"
      >
        <path
          fill-rule="evenodd"
          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
          clip-rule="evenodd"
        />
      </svg>
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/api';

// Props
const props = defineProps({
  showApple: {
    type: Boolean,
    default: false
  }
});

// Emits
const emit = defineEmits(['success', 'error']);

// State
const loading = ref(false);
const loadingProvider = ref(null);
const error = ref(null);

/**
 * Initiate social login flow
 */
const loginWith = async (provider) => {
  try {
    loading.value = true;
    loadingProvider.value = provider;
    error.value = null;

    // Get redirect URL from backend
    const response = await api.get(`/auth/${provider}/redirect`);
    const redirectUrl = response.data.redirect_url;

    // Option 1: Open in popup (better UX)
    const popup = window.open(
      redirectUrl,
      `${provider}_oauth`,
      'width=600,height=700,scrollbars=yes'
    );

    // Listen for callback message
    window.addEventListener('message', handleOAuthCallback, false);

    // Option 2: Full redirect (simpler, but loses current page state)
    // window.location.href = redirectUrl;

  } catch (err) {
    console.error('Social login error:', err);
    error.value = err.response?.data?.error || 'Failed to initiate social login';
    emit('error', error.value);
  } finally {
    loading.value = false;
    loadingProvider.value = null;
  }
};

/**
 * Handle OAuth callback from popup
 */
const handleOAuthCallback = (event) => {
  // Verify origin for security
  if (event.origin !== window.location.origin) {
    return;
  }

  const { type, token, user, error: callbackError } = event.data;

  if (type === 'oauth_success' && token) {
    // Store token
    localStorage.setItem('auth_token', token);
    
    // Emit success event
    emit('success', { user, token });

    // Clean up
    window.removeEventListener('message', handleOAuthCallback);
  } else if (type === 'oauth_error') {
    error.value = callbackError || 'Authentication failed';
    emit('error', error.value);
    window.removeEventListener('message', handleOAuthCallback);
  }
};
</script>

<style scoped>
.social-login {
  width: 100%;
}

.social-buttons-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  width: 100%;
  padding: 12px 24px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: white;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.social-btn:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #d1d5db;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.social-btn:active:not(:disabled) {
  transform: translateY(0);
}

.social-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.social-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* Google Button */
.google-btn {
  border-color: #dadce0;
}

.google-btn:hover:not(:disabled) {
  background: #f8f9fa;
  box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
}

/* Facebook Button */
.facebook-btn {
  background: #1877F2;
  color: white;
  border-color: #1877F2;
}

.facebook-btn:hover:not(:disabled) {
  background: #166FE5;
  border-color: #166FE5;
}

/* Apple Button */
.apple-btn {
  background: #000;
  color: white;
  border-color: #000;
}

.apple-btn:hover:not(:disabled) {
  background: #1a1a1a;
  border-color: #1a1a1a;
}

.apple-btn .social-icon path {
  fill: white;
}

/* Divider */
.divider {
  position: relative;
  text-align: center;
  margin: 24px 0;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e5e7eb;
}

.divider span {
  position: relative;
  display: inline-block;
  padding: 0 16px;
  background: white;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
}

/* Error Message */
.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  color: #dc2626;
  font-size: 14px;
  margin-top: 16px;
}

.error-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 640px) {
  .social-btn {
    padding: 10px 20px;
    font-size: 14px;
  }

  .social-icon {
    width: 18px;
    height: 18px;
  }
}
</style>
