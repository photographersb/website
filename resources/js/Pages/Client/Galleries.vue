<template>
  <div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-6xl mx-auto space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Photo Galleries</h1>
          <p class="text-gray-600">Albums shared by photographers you have booked.</p>
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
              placeholder="Search by album or photographer"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-burgundy-200 focus:border-burgundy-400"
            >
          </div>
          <button
            class="px-4 py-2.5 rounded-lg bg-burgundy text-white font-semibold hover:bg-[#6F112D]"
            @click="loadAlbums"
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
        v-else-if="filteredAlbums.length"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
      >
        <div
          v-for="album in filteredAlbums"
          :key="album.id"
          class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow"
        >
          <button
            class="w-full text-left"
            @click="openAlbum(album.id)"
          >
            <div class="h-44 bg-gray-100 overflow-hidden">
              <img
                v-if="album.cover_photo_url"
                :src="album.cover_photo_url"
                :alt="album.name"
                class="h-full w-full object-cover"
              >
              <div
                v-else
                class="h-full w-full flex items-center justify-center text-sm font-semibold text-gray-400"
              >
                No cover photo
              </div>
            </div>
            <div class="p-4 space-y-2">
              <div>
                <p class="text-lg font-semibold text-gray-900">{{ album.name }}</p>
                <p class="text-sm text-gray-600">
                  {{ album.photographer?.business_name || album.photographer?.user?.name || 'Photographer' }}
                </p>
              </div>
              <div class="flex items-center justify-between text-sm text-gray-600">
                <span>{{ album.photos_count || 0 }} photos</span>
                <span>{{ formatDate(album.updated_at) }}</span>
              </div>
            </div>
          </button>
        </div>
      </div>

      <div
        v-else
        class="bg-white rounded-2xl border border-gray-200 p-10 text-center"
      >
        <p class="text-gray-600">No galleries available yet. Your photographers will share albums after delivery.</p>
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
const albums = ref([])
const loading = ref(true)
const search = ref('')

const filteredAlbums = computed(() => {
  if (!search.value) {
    return albums.value
  }
  const term = search.value.toLowerCase()
  return albums.value.filter((album) => {
    const name = String(album.name || '').toLowerCase()
    const photographerName = String(album.photographer?.business_name || album.photographer?.user?.name || '').toLowerCase()
    return name.includes(term) || photographerName.includes(term)
  })
})

onMounted(() => {
  loadAlbums()
})

const loadAlbums = async () => {
  loading.value = true
  try {
    const response = await api.get('/client/albums')
    albums.value = response.data.data || []
  } catch (error) {
    if (error.response?.status === 401) {
      router.push('/auth')
      return
    }
    console.error('Failed to load galleries:', error)
  } finally {
    loading.value = false
  }
}

const openAlbum = (albumId) => {
  router.push(`/client/galleries/${albumId}`)
}

const formatDate = (value) => formatDateValue(value)
</script>
