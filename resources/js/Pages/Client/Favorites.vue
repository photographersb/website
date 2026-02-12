<template>
  <div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-6xl mx-auto space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Favorite Photographers</h1>
          <p class="text-gray-600">Saved photographers you may want to book again.</p>
        </div>
        <router-link
          to="/client/dashboard"
          class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200"
        >
          Back to Dashboard
        </router-link>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="flex-1">
            <input
              v-model.trim="search"
              type="text"
              placeholder="Search by photographer name"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-burgundy-200 focus:border-burgundy-400"
            >
          </div>
          <button
            class="px-4 py-2.5 rounded-lg bg-burgundy text-white font-semibold hover:bg-[#6F112D]"
            @click="loadFavorites"
          >
            Refresh
          </button>
        </div>
      </div>

      <div
        v-if="loading"
        class="flex items-center justify-center py-12"
      >
        <div
          class="animate-spin rounded-full h-10 w-10 border-b-2"
          style="border-bottom-color: var(--admin-brand-primary);"
        />
      </div>

      <div
        v-else-if="filteredFavorites.length"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
      >
        <div
          v-for="favorite in filteredFavorites"
          :key="favorite.id"
          class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-full bg-gray-100 overflow-hidden">
              <img
                v-if="favorite.photographer?.profile_picture"
                :src="favorite.photographer.profile_picture"
                :alt="favorite.photographer?.business_name || 'Photographer'"
                class="w-full h-full object-cover"
              >
              <div
                v-else
                class="w-full h-full flex items-center justify-center text-sm font-semibold text-gray-400"
              >
                PSB
              </div>
            </div>
            <div class="flex-1">
              <p class="text-lg font-semibold text-gray-900">
                {{ favorite.photographer?.business_name || favorite.photographer?.user?.name || 'Photographer' }}
              </p>
              <p class="text-sm text-gray-600">
                Saved {{ formatDate(favorite.created_at) }}
              </p>
            </div>
          </div>

          <div class="mt-4 flex items-center gap-2">
            <router-link
              :to="getPhotographerLink(favorite.photographer)"
              class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-lg bg-burgundy text-white text-sm font-semibold hover:bg-[#6F112D]"
            >
              View Profile
            </router-link>
            <button
              class="px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200"
              @click="removeFavorite(favorite.photographer_id)"
            >
              Remove
            </button>
          </div>
        </div>
      </div>

      <div
        v-else
        class="bg-white rounded-2xl border border-gray-200 p-10 text-center"
      >
        <p class="text-gray-600">No favorites yet. Save photographers to access them quickly.</p>
        <router-link
          to="/"
          class="inline-flex items-center justify-center mt-4 px-5 py-2.5 rounded-lg bg-burgundy text-white font-semibold hover:bg-[#6F112D]"
        >
          Find Photographers
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../api'
import { formatDate as formatDateValue } from '../../utils/formatters'

const router = useRouter()
const favorites = ref([])
const loading = ref(true)
const search = ref('')

const filteredFavorites = computed(() => {
  if (!search.value) {
    return favorites.value
  }
  const term = search.value.toLowerCase()
  return favorites.value.filter((favorite) => {
    const name = String(favorite.photographer?.business_name || favorite.photographer?.user?.name || '').toLowerCase()
    return name.includes(term)
  })
})

onMounted(() => {
  loadFavorites()
})

const loadFavorites = async () => {
  loading.value = true
  try {
    const response = await api.get('/client/favorites')
    favorites.value = response.data.data || []
  } catch (error) {
    if (error.response?.status === 401) {
      router.push('/auth')
      return
    }
    console.error('Failed to load favorites:', error)
  } finally {
    loading.value = false
  }
}

const removeFavorite = async (photographerId) => {
  try {
    await api.delete(`/client/favorites/${photographerId}`)
    favorites.value = favorites.value.filter((favorite) => favorite.photographer_id !== photographerId)
  } catch (error) {
    console.error('Failed to remove favorite:', error)
  }
}

const getPhotographerLink = (photographer) => {
  if (!photographer) return '/'
  if (photographer.user?.username) {
    return `/@${photographer.user.username}`
  }
  return `/photographer/${photographer.slug || photographer.id}`
}

const formatDate = (value) => formatDateValue(value)
</script>
