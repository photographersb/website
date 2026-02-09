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
                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
              />
            </svg>
          </div>
          <div>
            <h1 class="text-5xl font-bold text-gray-900">
              Explore <span class="text-primary-700">Locations</span>
            </h1>
            <p class="text-lg text-primary-700 font-semibold mt-1">
              Discover photographers across Bangladesh
            </p>
          </div>
        </div>
        <div class="h-1 w-24 bg-gradient-to-r from-primary-500 to-primary-400 rounded-full" />
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">
              All Locations
            </h2>
            <p class="text-gray-600">
              Select a location to view available photographers.
            </p>
          </div>
          <div class="w-full md:w-80">
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search locations..."
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
            Loading locations...
          </p>
        </div>
      </div>

      <div v-else>
        <div
          v-if="filteredCities.length === 0"
          class="bg-white rounded-2xl shadow-lg p-12 border border-gray-100 text-center"
        >
          <div class="text-6xl mb-4">
            📍
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">
            No Locations Found
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
            v-for="city in filteredCities"
            :key="city.slug"
            :to="`/photographers/by-location?city=${city.slug}`"
            class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-2xl hover:border-primary-300 hover:-translate-y-2 transition-all text-left group"
          >
            <div class="text-5xl mb-4 group-hover:scale-125 transition-transform">
              📍
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">
              {{ city.name }}
            </h3>
            <p class="text-gray-600 mb-4">
              Browse photographers in {{ city.name }}
            </p>
            <div class="flex items-center justify-between">
              <span class="text-sm font-bold text-primary-700">{{ city.count }} photographers</span>
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
          to="/photographers/by-location"
          class="inline-flex items-center gap-2 px-6 py-3 bg-primary-700 text-white rounded-lg hover:bg-primary-800 transition-all font-semibold shadow-md"
        >
          View All Locations
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
const cities = ref([])
const searchTerm = ref('')

const filteredCities = computed(() => {
  const term = searchTerm.value.trim().toLowerCase()
  if (!term) return cities.value
  return cities.value.filter(city =>
    city.name.toLowerCase().includes(term) ||
    city.slug.toLowerCase().includes(term)
  )
})

const loadCities = async () => {
  try {
    const response = await api.get('/locations')
    const allCities = response.data.data || response.data || []
    const visibleLocations = allCities.filter(city => city.type !== 'division')
    cities.value = visibleLocations
      .map((city) => ({
        ...city,
        count: Number(city.photographers_count || city.count || 0)
      }))
      .filter(city => city.count > 0)
      .sort((a, b) => b.count - a.count)
  } catch (error) {
    console.error('Failed to load cities:', error)
    cities.value = []
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await loadCities()
})
</script>
