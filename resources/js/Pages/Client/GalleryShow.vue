<template>
  <div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-6xl mx-auto space-y-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
          <router-link
            to="/client/galleries"
            class="text-sm font-semibold text-burgundy-600 hover:text-burgundy-700"
          >
            Back to galleries
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ album?.name || 'Gallery' }}</h1>
          <p class="text-gray-600">
            {{ album?.photographer?.business_name || album?.photographer?.user?.name || 'Photographer' }}
            <span v-if="album?.photos?.length">• {{ album.photos.length }} photos</span>
          </p>
        </div>
        <div class="text-sm text-gray-600">
          Updated {{ formatDate(album?.updated_at) }}
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
        v-else-if="album?.photos?.length"
        class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3"
      >
        <div
          v-for="photo in album.photos"
          :key="photo.id"
          class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm"
        >
          <a
            :href="photo.image_url"
            target="_blank"
            rel="noopener"
          >
            <img
              :src="photo.thumbnail_url || photo.image_url"
              :alt="photo.title || album.name"
              class="w-full h-40 object-cover"
              loading="lazy"
            >
          </a>
          <div class="p-3">
            <p class="text-sm font-semibold text-gray-900 truncate">
              {{ photo.title || 'Photo' }}
            </p>
            <a
              :href="photo.image_url"
              target="_blank"
              rel="noopener"
              class="text-xs font-semibold text-burgundy-600 hover:text-burgundy-700"
            >
              Open full size
            </a>
          </div>
        </div>
      </div>

      <div
        v-else
        class="bg-white rounded-2xl border border-gray-200 p-10 text-center"
      >
        <p class="text-gray-600">No photos available yet in this album.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../api'
import { formatDate as formatDateValue } from '../../utils/formatters'

const route = useRoute()
const router = useRouter()
const album = ref(null)
const loading = ref(true)

onMounted(() => {
  loadAlbum()
})

const loadAlbum = async () => {
  loading.value = true
  try {
    const response = await api.get(`/client/albums/${route.params.albumId}`)
    album.value = response.data.data || null
  } catch (error) {
    if (error.response?.status === 401) {
      router.push('/auth')
      return
    }
    router.push('/client/galleries')
  } finally {
    loading.value = false
  }
}

const formatDate = (value) => {
  if (!value) return 'Unknown'
  return formatDateValue(value)
}
</script>
