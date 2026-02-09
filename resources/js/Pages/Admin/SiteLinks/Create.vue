<template>
  <div>
    <AdminHeader 
      title="➕ Create Site Link" 
      subtitle="Add a new link to your site navigation" 
      :back-url="'/admin/settings/site-links'"
    />

    <AdminQuickNav />

    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
      <form
        class="space-y-6"
        @submit.prevent="submitForm"
      >
        <!-- Section -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Section *</label>
          <select
            v-model="form.section"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
          >
            <option value="">
              Select a section
            </option>
            <option
              v-for="(label, key) in sections"
              :key="key"
              :value="key"
            >
              {{ label }}
            </option>
          </select>
        </div>

        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
          <input
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
            placeholder="e.g., About Us"
          >
        </div>

        <!-- URL -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">URL *</label>
          <input
            v-model="form.url"
            type="url"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
            placeholder="https://example.com or /about"
          >
          <p class="mt-1 text-xs text-gray-500">
            Full URL (https://) or relative path (/about)
          </p>
        </div>

        <!-- Route Name (Optional) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Route Name (Optional)</label>
          <input
            v-model="form.route_name"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
            placeholder="e.g., about.index"
          >
          <p class="mt-1 text-xs text-gray-500">
            If provided, route will be used instead of URL
          </p>
        </div>

        <!-- Icon (for social links) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
          <input
            v-model="form.icon"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
            placeholder="e.g., facebook, instagram"
          >
        </div>

        <!-- Open in New Tab -->
        <div class="flex items-center gap-3">
          <input
            id="open_new_tab"
            v-model="form.open_in_new_tab"
            type="checkbox"
            class="w-4 h-4 text-burgundy border-gray-300 rounded focus:ring-burgundy"
          >
          <label
            for="open_new_tab"
            class="text-sm text-gray-700"
          >Open in new tab</label>
        </div>

        <!-- Sort Order -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
          <input
            v-model.number="form.sort_order"
            type="number"
            min="0"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
            placeholder="0"
          >
        </div>

        <!-- Visibility -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Visibility *</label>
          <select
            v-model="form.visibility"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
          >
            <option
              v-for="(label, key) in visibilityOptions"
              :key="key"
              :value="key"
            >
              {{ label }}
            </option>
          </select>
        </div>

        <!-- Active -->
        <div class="flex items-center gap-3">
          <input
            id="is_active"
            v-model="form.is_active"
            type="checkbox"
            class="w-4 h-4 text-burgundy border-gray-300 rounded focus:ring-burgundy"
          >
          <label
            for="is_active"
            class="text-sm text-gray-700"
          >Active (visible on site)</label>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-4 border-t">
          <button
            type="submit"
            class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
          >
            Create Link
          </button>
          <Link
            href="/admin/settings/site-links"
            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
          >
            Cancel
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'

const props = defineProps({
  sections: Object,
  visibilityOptions: Object,
})

const form = ref({
  section: '',
  title: '',
  url: '',
  route_name: '',
  icon: '',
  open_in_new_tab: true,
  sort_order: 0,
  visibility: 'public',
  is_active: true,
})

const submitForm = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    await api.post('/admin/settings/site-links', form.value, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    router.visit('/admin/settings/site-links')
  } catch (error) {
    console.error('Error creating link:', error)
    alert('Failed to create link: ' + (error.response?.data?.message || error.message))
  }
}
</script>
