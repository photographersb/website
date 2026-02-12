<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 pt-6">
      <router-link
        to="/client/dashboard"
        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200"
      >
        Back to Dashboard
      </router-link>
    </div>
    <NotificationsInbox />
  </div>
</template>

<script setup>
import NotificationsInbox from '../../components/NotificationsInbox.vue'
</script>
