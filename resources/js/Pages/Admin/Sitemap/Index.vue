<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader 
      title="Site Sitemap" 
      subtitle="Manage site structure and indexing"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <!-- Placeholder -->
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
          <div class="text-6xl mb-4">
            🗺️
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">
            Site Sitemap Management
          </h3>
          <p class="text-gray-600 mb-6">
            Manage and verify your site structure for search engines
          </p>
          <button 
            class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition"
            @click="router.visit('/admin/dashboard')"
          >
            Back to Dashboard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
</script>
