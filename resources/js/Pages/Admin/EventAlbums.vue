<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader />
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Event Albums
        </h1>
        <p class="text-gray-600 mt-2">
          Manage photo albums created for events
        </p>
      </div>
      
      <AdminQuickNav />

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Event</label>
            <select
              v-model="selectedEvent"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Events
              </option>
              <option
                v-for="event in events"
                :key="event.id"
                :value="event.name"
              >
                {{ event.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="selectedStatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Status
              </option>
              <option value="draft">
                Draft
              </option>
              <option value="published">
                Published
              </option>
              <option value="archived">
                Archived
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
            <select
              v-model="selectedVisibility"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
              <option value="">
                All Visibility
              </option>
              <option value="public">
                Public
              </option>
              <option value="private">
                Private
              </option>
              <option value="unlisted">
                Unlisted
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search albums..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Albums
            </p>
            <p class="text-2xl font-bold text-gray-900">
              {{ albums.length }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Published
            </p>
            <p class="text-2xl font-bold text-green-600">
              {{ publishedCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Drafts
            </p>
            <p class="text-2xl font-bold text-yellow-600">
              {{ draftCount }}
            </p>
          </div>
          <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">
              Total Photos
            </p>
            <p class="text-2xl font-bold text-blue-600">
              {{ totalPhotos }}
            </p>
          </div>
        </div>

        <div
          v-if="!loading"
          class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        >
          <div
            v-for="album in filteredAlbums"
            :key="album.id"
            class="bg-white rounded-lg shadow-md overflow-hidden"
          >
            <div class="h-40 bg-gray-200 flex items-center justify-center text-gray-400">
              Cover Image
            </div>
            <div class="p-6">
              <div class="flex justify-between items-start mb-3">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ album.title }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ album.event }}
                  </p>
                </div>
                <div class="text-right">
                  <span :class="['px-2 py-1 rounded-full text-xs font-semibold text-white', getStatusClass(album.status)]">
                    {{ album.status }}
                  </span>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-4">
                <div>Photos: <span class="font-medium text-gray-900">{{ album.photos }}</span></div>
                <div>Visibility: <span class="font-medium text-gray-900 capitalize">{{ album.visibility }}</span></div>
                <div>Created: <span class="font-medium text-gray-900">{{ formatDate(album.createdAt) }}</span></div>
                <div>Updated: <span class="font-medium text-gray-900">{{ formatDate(album.updatedAt) }}</span></div>
              </div>
              <div class="flex gap-2">
                <button
                  class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"
                  @click="viewAlbum(album.id)"
                >
                  View
                </button>
                <button
                  class="flex-1 px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700"
                  @click="editAlbum(album.id)"
                >
                  Edit
                </button>
                <button
                  v-if="album.status !== 'published'"
                  class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700"
                  @click="publishAlbum(album.id)"
                >
                  Publish
                </button>
              </div>
            </div>
          </div>
        </div>

        <div
          v-if="!loading && filteredAlbums.length === 0"
          class="text-center py-12 bg-white rounded-lg"
        >
          <p class="text-gray-600 text-lg">
            No albums found
          </p>
        </div>

      <div
        v-if="loading"
        class="flex justify-center py-12"
      >
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
      </div>
    </div>
    <Toast
      v-if="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="toast.show = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminHeader from '../../components/AdminHeader.vue'
import AdminQuickNav from '../../components/AdminQuickNav.vue'
import Toast from '../../components/ui/Toast.vue'
import { formatDate as formatDateValue } from '../../utils/formatters'
const loading = ref(true)
const toast = ref({ show: false, message: '', type: 'success' })
const selectedEvent = ref('')
const selectedStatus = ref('')
const selectedVisibility = ref('')
const searchQuery = ref('')

const events = ref([
  { id: 1, name: 'Annual Photography Conference' },
  { id: 2, name: 'Wildlife Workshop' },
  { id: 3, name: 'Portrait Mastery Series' }
])

const albums = ref([
  {
    id: 1,
    title: 'Conference Highlights 2026',
    event: 'Annual Photography Conference',
    status: 'published',
    visibility: 'public',
    photos: 245,
    createdAt: '2026-01-20',
    updatedAt: '2026-02-01'
  },
  {
    id: 2,
    title: 'Wildlife Workshop Day 1',
    event: 'Wildlife Workshop',
    status: 'draft',
    visibility: 'private',
    photos: 83,
    createdAt: '2026-01-28',
    updatedAt: '2026-02-03'
  },
  {
    id: 3,
    title: 'Portrait Mastery - Week 1',
    event: 'Portrait Mastery Series',
    status: 'published',
    visibility: 'unlisted',
    photos: 132,
    createdAt: '2026-01-18',
    updatedAt: '2026-01-29'
  }
])

const filteredAlbums = computed(() => {
  return albums.value.filter(album => {
    const eventMatch = !selectedEvent.value || album.event === selectedEvent.value
    const statusMatch = !selectedStatus.value || album.status === selectedStatus.value
    const visibilityMatch = !selectedVisibility.value || album.visibility === selectedVisibility.value
    const searchMatch = !searchQuery.value || album.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    return eventMatch && statusMatch && visibilityMatch && searchMatch
  })
})

const publishedCount = computed(() => albums.value.filter(a => a.status === 'published').length)
const draftCount = computed(() => albums.value.filter(a => a.status === 'draft').length)
const totalPhotos = computed(() => albums.value.reduce((sum, a) => sum + a.photos, 0))

const getStatusClass = (status) => {
  const classes = { published: 'bg-green-500', draft: 'bg-yellow-500', archived: 'bg-gray-500' }
  return classes[status] || 'bg-gray-500'
}

const formatDate = (date) => {
  return formatDateValue(date)
}

const viewAlbum = (id) => {
  toast.value = { show: true, message: `Viewing album ${id}...`, type: 'info' }
}

const editAlbum = (id) => {
  toast.value = { show: true, message: `Editing album ${id}...`, type: 'info' }
}

const publishAlbum = (id) => {
  const album = albums.value.find(a => a.id === id)
  if (album) {
    album.status = 'published'
    toast.value = { show: true, message: `${album.title} published!`, type: 'success' }
  }
}

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 700)
})
</script>
