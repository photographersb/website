<template>
  <div class="album-photo-manager">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold">{{ album.name }}</h2>
        <p class="text-gray-600">{{ album.photo_count || 0 }} photos</p>
      </div>
      <div class="flex gap-2">
        <button
          @click="showPexelsSearch = true"
          class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
        >
          🔍 Add from Pexels
        </button>
        <button
          @click="$emit('close')"
          class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
        >
          Close
        </button>
      </div>
    </div>

    <!-- Photo Grid -->
    <div v-if="photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
      <div v-for="photo in photos" :key="photo.id" class="relative group">
        <img
          :src="photo.thumbnail_url"
          :alt="photo.title || 'Photo'"
          class="w-full h-48 object-cover rounded-lg"
        />
        <div
          class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2"
        >
          <button
            @click="deletePhoto(photo.id)"
            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
          >
            Delete
          </button>
        </div>
        <div v-if="photo.title" class="mt-1 text-sm font-medium truncate">
          {{ photo.title }}
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12 text-gray-500">
      <p class="mb-4">No photos in this album yet</p>
      <button
        @click="showPexelsSearch = true"
        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700"
      >
        🔍 Add Photos from Pexels
      </button>
    </div>

    <!-- Pexels Search Modal -->
    <div
      v-if="showPexelsSearch"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      @click.self="showPexelsSearch = false"
    >
      <div class="bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Search Header -->
        <div class="p-6 border-b">
          <h3 class="text-2xl font-bold mb-4">Search Pexels Photos</h3>
          <div class="flex gap-2">
            <input
              v-model="pexelsQuery"
              @keyup.enter="searchPexels"
              type="text"
              placeholder="Search for wedding, portrait, landscape..."
              class="flex-1 border rounded px-4 py-2 focus:ring-2 focus:ring-purple-600"
            />
            <button
              @click="searchPexels"
              :disabled="searching || !pexelsQuery"
              class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50"
            >
              {{ searching ? 'Searching...' : 'Search' }}
            </button>
          </div>
          <div class="mt-2 flex flex-wrap gap-2">
            <button
              v-for="tag in quickTags"
              :key="tag"
              @click="quickSearch(tag)"
              class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded"
            >
              {{ tag }}
            </button>
          </div>
        </div>

        <!-- Results Grid -->
        <div class="flex-1 overflow-y-auto p-6">
          <div v-if="searching" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
            <p class="mt-4 text-gray-600">Searching Pexels...</p>
          </div>

          <div v-else-if="pexelsResults.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="photo in pexelsResults"
              :key="photo.id"
              class="relative group cursor-pointer"
              @click="togglePhotoSelection(photo)"
            >
              <img
                :src="photo.thumbnail_url"
                :alt="'Photo by ' + photo.photographer"
                class="w-full h-48 object-cover rounded-lg"
              />
              <div
                v-if="isPhotoSelected(photo)"
                class="absolute inset-0 bg-purple-600/30 rounded-lg flex items-center justify-center"
              >
                <div class="bg-white rounded-full p-2">
                  ✓
                </div>
              </div>
              <div class="mt-1 text-xs text-gray-600 truncate">
                By {{ photo.photographer }}
              </div>
            </div>
          </div>

          <div v-else-if="pexelsQuery" class="text-center py-12 text-gray-500">
            No results found. Try different keywords.
          </div>

          <div v-else class="text-center py-12 text-gray-500">
            Enter a search term to find photos
          </div>
        </div>

        <!-- Footer with Selection -->
        <div v-if="selectedPhotos.length > 0" class="p-6 border-t bg-gray-50">
          <div class="flex justify-between items-center">
            <div>
              <span class="font-medium">{{ selectedPhotos.length }} photo(s) selected</span>
            </div>
            <div class="flex gap-2">
              <button
                @click="selectedPhotos = []"
                class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100"
              >
                Clear Selection
              </button>
              <button
                @click="addSelectedPhotos"
                :disabled="adding"
                class="px-6 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50"
              >
                {{ adding ? 'Adding...' : 'Add to Album' }}
              </button>
            </div>
          </div>
        </div>

        <div v-else class="p-6 border-t bg-gray-50 text-center text-gray-600">
          Click on photos to select them, then add to your album
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const props = defineProps({
  album: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'updated']);

const photos = ref([]);
const showPexelsSearch = ref(false);
const pexelsQuery = ref('');
const pexelsResults = ref([]);
const selectedPhotos = ref([]);
const searching = ref(false);
const adding = ref(false);

const quickTags = ['wedding', 'portrait', 'landscape', 'nature', 'sunset', 'architecture', 'fashion', 'food'];

onMounted(() => {
  fetchPhotos();
});

const fetchPhotos = async () => {
  try {
    const response = await api.get(`/photographer/albums/${props.album.id}`);
    photos.value = response.data.data.photos || [];
  } catch (error) {
    console.error('Error fetching photos:', error);
  }
};

const searchPexels = async () => {
  if (!pexelsQuery.value) return;

  searching.value = true;
  try {
    const response = await api.get('/photographer/photos/search-pexels', {
      params: {
        query: pexelsQuery.value,
        per_page: 20,
      },
    });
    pexelsResults.value = response.data.data || [];
  } catch (error) {
    console.error('Error searching Pexels:', error);
    alert('Failed to search photos. Please try again.');
  } finally {
    searching.value = false;
  }
};

const quickSearch = (tag) => {
  pexelsQuery.value = tag;
  searchPexels();
};

const togglePhotoSelection = (photo) => {
  const index = selectedPhotos.value.findIndex((p) => p.id === photo.id);
  if (index > -1) {
    selectedPhotos.value.splice(index, 1);
  } else {
    selectedPhotos.value.push(photo);
  }
};

const isPhotoSelected = (photo) => {
  return selectedPhotos.value.some((p) => p.id === photo.id);
};

const addSelectedPhotos = async () => {
  if (selectedPhotos.value.length === 0) return;

  adding.value = true;
  try {
    const photosData = selectedPhotos.value.map((photo) => ({
      image_url: photo.image_url,
      thumbnail_url: photo.thumbnail_url,
      title: `Photo by ${photo.photographer}`,
    }));

    await api.post(`/photographer/albums/${props.album.id}/photos`, {
      photos: photosData,
    });

    alert(`${selectedPhotos.value.length} photo(s) added successfully!`);
    selectedPhotos.value = [];
    pexelsResults.value = [];
    showPexelsSearch.value = false;
    await fetchPhotos();
    emit('updated');
  } catch (error) {
    console.error('Error adding photos:', error);
    alert(error.response?.data?.message || 'Failed to add photos');
  } finally {
    adding.value = false;
  }
};

const deletePhoto = async (photoId) => {
  if (!confirm('Are you sure you want to delete this photo?')) return;

  try {
    await api.delete(`/photographer/photos/${photoId}`);
    photos.value = photos.value.filter((p) => p.id !== photoId);
    emit('updated');
    alert('Photo deleted successfully');
  } catch (error) {
    console.error('Error deleting photo:', error);
    alert('Failed to delete photo');
  }
};
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
