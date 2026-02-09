<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 mb-6">
      <div class="container mx-auto px-4 py-6 max-w-4xl">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">
              Account Settings
            </h1>
            <p class="text-gray-600 mt-1">
              Manage your account preferences and settings
            </p>
          </div>
          <router-link
            to="/"
            class="text-gray-600 hover:text-burgundy transition-colors"
          >
            Back to Home
          </router-link>
        </div>
      </div>
    </div>

    <!-- Settings Content -->
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <!-- Settings Sections -->
      <div class="space-y-6">
        <!-- Profile Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
            Profile Information
          </h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
              <input
                type="text"
                :value="user?.name"
                disabled
                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input
                type="email"
                :value="user?.email"
                disabled
                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
              >
            </div>
            <router-link
              to="/dashboard"
              class="inline-block px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-[#6F112D] transition-colors"
            >
              Edit Profile in Dashboard
            </router-link>
          </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
              />
            </svg>
            Notifications
          </h2>
          <div class="space-y-3">
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="notificationSettings.bookings"
                class="w-4 h-4 text-burgundy border-gray-300 rounded focus:ring-burgundy"
              >
              <span class="text-sm text-gray-700">Email notifications for bookings</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="notificationSettings.reviews"
                class="w-4 h-4 text-burgundy border-gray-300 rounded focus:ring-burgundy"
              >
              <span class="text-sm text-gray-700">Email notifications for reviews</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="notificationSettings.competitions"
                class="w-4 h-4 text-burgundy border-gray-300 rounded focus:ring-burgundy"
              >
              <span class="text-sm text-gray-700">Competition updates</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="notificationSettings.events"
                class="w-4 h-4 text-burgundy border-gray-300 rounded focus:ring-burgundy"
              >
              <span class="text-sm text-gray-700">Event announcements</span>
            </label>
          </div>
          <button
            class="mt-4 px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-[#6F112D] transition-colors"
            @click="saveNotifications"
          >
            Save Notification Settings
          </button>
        </div>

        <!-- Privacy Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
              />
            </svg>
            Privacy
          </h2>
          <div class="space-y-3">
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="privacySettings.profileVisible"
                class="w-4 h-4 text-burgundy border-gray-300 rounded focus:ring-burgundy"
              >
              <span class="text-sm text-gray-700">Make my profile visible to clients</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="privacySettings.showContact"
                class="w-4 h-4 text-burgundy border-gray-300 rounded focus:ring-burgundy"
              >
              <span class="text-sm text-gray-700">Show my contact information</span>
            </label>
          </div>
          <button
            class="mt-4 px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-[#6F112D] transition-colors"
            @click="savePrivacy"
          >
            Save Privacy Settings
          </button>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
          <h2 class="text-lg font-semibold text-red-900 mb-2">
            Danger Zone
          </h2>
          <p class="text-sm text-gray-600 mb-4">
            These actions are permanent and cannot be undone
          </p>
          <div class="space-y-3">
            <button
              class="px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors border border-red-200"
              disabled
            >
              Deactivate Account (Coming Soon)
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="saveMessage"
      class="container mx-auto px-4 pb-8 max-w-4xl"
    >
      <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        {{ saveMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const user = ref(null);
const saveMessage = ref('');
const notificationSettings = ref({
  bookings: true,
  reviews: true,
  competitions: true,
  events: true,
});
const privacySettings = ref({
  profileVisible: true,
  showContact: true,
});

const loadSettings = () => {
  const storedNotifications = localStorage.getItem('settings.notifications');
  const storedPrivacy = localStorage.getItem('settings.privacy');

  if (storedNotifications) {
    notificationSettings.value = JSON.parse(storedNotifications);
  }
  if (storedPrivacy) {
    privacySettings.value = JSON.parse(storedPrivacy);
  }
};

const setSaveMessage = (message) => {
  saveMessage.value = message;
  setTimeout(() => {
    saveMessage.value = '';
  }, 4000);
};

const saveNotifications = () => {
  localStorage.setItem('settings.notifications', JSON.stringify(notificationSettings.value));
  setSaveMessage('Notification preferences saved.');
};

const savePrivacy = () => {
  localStorage.setItem('settings.privacy', JSON.stringify(privacySettings.value));
  setSaveMessage('Privacy preferences saved.');
};

onMounted(() => {
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    user.value = JSON.parse(storedUser);
  }
  loadSettings();
});
</script>
