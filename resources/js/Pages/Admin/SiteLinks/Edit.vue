<template>
  <div>
    <AdminHeader 
      title="✏️ Edit Site Link" 
      :subtitle="`Editing: ${link.title}`"
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
          >
        </div>

        <!-- URL -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">URL *</label>
          <input
            v-model="form.url"
            type="url"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
          >
          <p class="mt-1 text-xs text-gray-500">
            Full URL (https://) or relative path (/about)
          </p>
        </div>

        <!-- Route Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Route Name (Optional)</label>
          <input
            v-model="form.route_name"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
          >
        </div>

        <!-- Icon -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
          <input
            v-model="form.icon"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy"
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

        <!-- Metadata -->
        <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-600 space-y-1">
          <div><strong>Created:</strong> {{ formatDateTime(link.created_at) }}</div>
          <div v-if="link.creator">
            <strong>Created by:</strong> {{ link.creator.name }}
          </div>
          <div><strong>Updated:</strong> {{ formatDateTime(link.updated_at) }}</div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-4 border-t">
          <button
            type="submit"
            class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
          >
            Update Link
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
import { ref, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'
import api from '../../../api'
import { formatDateTime } from '../../../utils/formatters'

const props = defineProps({
  link: Object,
  sections: Object,
  visibilityOptions: Object,
})

const form = ref({
  section: props.link.section,
  title: props.link.title,
  url: props.link.url,
  route_name: props.link.route_name || '',
  icon: props.link.icon || '',
  open_in_new_tab: props.link.open_in_new_tab,
  sort_order: props.link.sort_order,
  visibility: props.link.visibility,
  is_active: props.link.is_active,
})

const submitForm = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    await api.put(`/admin/settings/site-links/${props.link.id}`, form.value, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    router.visit('/admin/settings/site-links')
  } catch (error) {
    console.error('Error updating link:', error)
    alert('Failed to update link: ' + (error.response?.data?.message || error.message))
  }
}
</script>
