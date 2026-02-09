<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4">
    <div class="max-w-7xl mx-auto">
      <div class="mb-12">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-xl">
            <svg
              class="w-8 h-8 text-white"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
          </div>
          <div>
            <h1 class="text-5xl font-bold text-gray-900">
              Browse <span class="text-primary-700">Photography Categories</span>
            </h1>
            <p class="text-lg text-primary-700 font-semibold mt-1">
              Find photographers by specialty and style
            </p>
          </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-primary-500 to-primary-400 rounded-full" />
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">
              All Categories
            </h2>
            <p class="text-gray-600">
              Select a category to view available photographers.
            </p>
          </div>
          <div class="w-full md:w-80">
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search categories..."
              class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-primary-500 focus:ring-primary-200 outline-none transition"
            >
          </div>
        </div>
      </div>

      <div
        v-if="loading"
        class="flex justify-center items-center py-20"
      >
        <div class="text-center">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600 mx-auto mb-4" />
          <p class="text-primary-700 font-semibold">
            Loading categories...
          </p>
        </div>
      </div>

      <div v-else>
        <div
          v-if="filteredCategories.length === 0"
          class="bg-white rounded-2xl shadow-lg p-12 border border-gray-100 text-center"
        >
          <div class="text-6xl mb-4">
            📷
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">
            No Categories Found
          </h3>
          <p class="text-gray-600 mb-6">
            Try a different search term.
          </p>
          <button
            class="px-6 py-3 bg-gradient-to-r from-primary-700 to-primary-800 text-white rounded-lg hover:from-primary-800 hover:to-primary-900 transition-all font-semibold"
            @click="searchTerm = ''"
          >
            Clear Search
          </button>
        </div>

        <div
          v-else
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <router-link
            v-for="category in filteredCategories"
            :key="category.id"
            :to="`/photographers/by-category?category=${category.slug}`"
            class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl hover:border-primary-300 hover:-translate-y-2 transition-all text-left group"
          >
            <div class="text-5xl mb-4 group-hover:scale-125 transition-transform">
              {{ category.icon }}
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">
              {{ category.name }}
            </h3>
            <p class="text-gray-600 mb-4">
              {{ category.description }}
            </p>
            <div class="flex items-center justify-between">
              <span class="text-sm font-bold text-primary-700">{{ category.count }} photographers</span>
              <svg
                class="w-5 h-5 text-primary-600 group-hover:translate-x-2 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </div>
          </router-link>
        </div>
      </div>

      <div class="mt-12 text-center">
        <router-link
          to="/photographers/by-category"
          class="inline-flex items-center gap-2 px-6 py-3 bg-primary-700 text-white rounded-lg hover:bg-primary-800 transition-all font-semibold shadow-md"
        >
          View All Categories
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
              d="M9 5l7 7-7 7"
            />
          </svg>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../api'

const loading = ref(true)
const categories = ref([])
const searchTerm = ref('')

const categoryIcons = {
  'wedding-photography': '💒',
  'portrait-photography': '👤',
  'event-photography': '🎉',
  'product-photography': '📦',
  'corporate-photography': '🏢',
  'fashion-model-photography': '👗',
  'default': '📷'
}

const getIconForCategory = (slug) => categoryIcons[slug] || categoryIcons.default

const filteredCategories = computed(() => {
  const term = searchTerm.value.trim().toLowerCase()
  if (!term) return categories.value
  return categories.value.filter(category =>
    category.name.toLowerCase().includes(term) ||
    category.slug.toLowerCase().includes(term)
  )
})

const loadCategories = async () => {
  try {
    const response = await api.get('/categories')
    const allCategories = response.data.data || response.data || []
    categories.value = allCategories
      .map((cat) => ({
        ...cat,
        count: Number(cat.photographers_count || cat.count || 0),
        icon: cat.icon || getIconForCategory(cat.slug),
        description: cat.description || `Browse ${cat.name} photographers`
      }))
      .filter(category => category.count > 0)
      .sort((a, b) => b.count - a.count)
  } catch (error) {
    console.error('Failed to load categories:', error)
    categories.value = []
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await loadCategories()
})
</script>
