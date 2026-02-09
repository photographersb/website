<template>
  <div
    v-if="visible"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    @click.self="close"
  >
    <div class="w-full max-w-6xl overflow-hidden rounded-2xl bg-white shadow-2xl">
      <div class="flex items-center justify-between border-b px-6 py-4">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">
            {{ title }}
          </h3>
          <p class="text-xs text-gray-500">
            Target size: {{ targetWidth }}x{{ targetHeight }} px
          </p>
        </div>
        <button
          type="button"
          class="rounded-full border px-3 py-1 text-sm text-gray-600 hover:bg-gray-50"
          @click="close"
        >
          Close
        </button>
      </div>

      <div class="grid gap-6 px-6 py-5 md:grid-cols-[1.2fr_1fr]">
        <div>
          <div class="flex flex-wrap items-center gap-2">
            <input
              v-model="query"
              type="text"
              class="w-full flex-1 rounded-lg border px-4 py-2 text-sm focus:ring-2 focus:ring-burgundy"
              placeholder="Search Pexels photos..."
              @keyup.enter="search"
            >
            <button
              type="button"
              class="rounded-lg bg-burgundy px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800 disabled:opacity-60"
              :disabled="searching || !query"
              @click="search"
            >
              {{ searching ? 'Searching...' : 'Search' }}
            </button>
          </div>
          <div class="mt-3 flex flex-wrap gap-2">
            <button
              v-for="tag in quickTags"
              :key="tag"
              type="button"
              class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-200"
              @click="quickSearch(tag)"
            >
              {{ tag }}
            </button>
          </div>

          <div class="mt-5">
            <div
              v-if="searching"
              class="flex items-center justify-center rounded-xl border border-dashed py-16 text-sm text-gray-500"
            >
              Searching Pexels...
            </div>
            <div
              v-else-if="results.length"
              class="grid grid-cols-2 gap-3 md:grid-cols-3"
            >
              <button
                v-for="photo in results"
                :key="photo.id"
                type="button"
                class="group relative overflow-hidden rounded-xl border border-gray-200"
                @click="selectPhoto(photo)"
              >
                <img
                  :src="photo.thumbnail_url"
                  :alt="`Photo by ${photo.photographer}`"
                  class="h-32 w-full object-cover transition-transform duration-300 group-hover:scale-105"
                >
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2 text-xs text-white">
                  {{ photo.photographer }}
                </div>
              </button>
            </div>
            <div
              v-else
              class="rounded-xl border border-dashed py-16 text-center text-sm text-gray-500"
            >
              Search for a photo to begin.
            </div>
          </div>
        </div>

        <div>
          <div class="rounded-2xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-semibold text-gray-800">
                Crop Preview
              </h4>
              <span
                v-if="selectedPhoto"
                class="text-xs text-gray-500"
              >
                {{ selectedPhoto.width }}x{{ selectedPhoto.height }} px
              </span>
            </div>

            <div class="mt-4 flex items-center justify-center rounded-xl bg-gray-100 p-3">
              <canvas
                ref="canvasRef"
                :width="targetWidth"
                :height="targetHeight"
                class="pexels-canvas max-h-64 w-full rounded-lg border border-gray-200 bg-white"
                :style="canvasStyle"
              />
            </div>

            <div class="mt-4 space-y-3 text-xs text-gray-600">
              <div class="flex items-center gap-3">
                <span class="w-16 font-medium">Zoom</span>
                <input
                  v-model.number="zoom"
                  type="range"
                  min="1"
                  max="3"
                  step="0.05"
                  class="w-full"
                >
              </div>
              <div class="flex items-center gap-3">
                <span class="w-16 font-medium">Move X</span>
                <input
                  v-model.number="panX"
                  type="range"
                  min="0"
                  max="1"
                  step="0.01"
                  class="w-full"
                >
              </div>
              <div class="flex items-center gap-3">
                <span class="w-16 font-medium">Move Y</span>
                <input
                  v-model.number="panY"
                  type="range"
                  min="0"
                  max="1"
                  step="0.01"
                  class="w-full"
                >
              </div>
            </div>

            <div
              v-if="selectedPhoto"
              class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-600"
            >
              Photo by
              <a
                :href="creditUrl"
                target="_blank"
                rel="noopener"
                class="font-semibold text-burgundy underline"
              >
                {{ selectedPhoto.photographer }}
              </a>
              on Pexels.
            </div>

            <div class="mt-4 flex items-center gap-2">
              <button
                type="button"
                class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                @click="resetCrop"
              >
                Reset
              </button>
              <button
                type="button"
                class="flex-1 rounded-lg bg-burgundy px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800 disabled:opacity-60"
                :disabled="!selectedPhoto || exporting"
                @click="exportCrop"
              >
                {{ exporting ? 'Preparing...' : 'Use Cropped Photo' }}
              </button>
            </div>

            <p
              v-if="error"
              class="mt-3 text-xs text-red-600"
            >
              {{ error }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import api from '../api';

const props = defineProps({
  visible: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Choose a Pexels Photo',
  },
  targetWidth: {
    type: Number,
    required: true,
  },
  targetHeight: {
    type: Number,
    required: true,
  },
  orientation: {
    type: String,
    default: 'landscape',
  },
});

const emit = defineEmits(['close', 'select']);

const query = ref('');
const results = ref([]);
const searching = ref(false);
const selectedPhoto = ref(null);
const canvasRef = ref(null);
const loadedImage = ref(null);
const zoom = ref(1);
const panX = ref(0.5);
const panY = ref(0.5);
const exporting = ref(false);
const error = ref('');

const quickTags = ['workshop', 'portrait', 'landscape', 'meetup', 'exhibition', 'travel', 'city'];

const canvasStyle = computed(() => ({
  aspectRatio: `${props.targetWidth} / ${props.targetHeight}`,
}));

const close = () => {
  emit('close');
};

const search = async () => {
  if (!query.value) return;
  searching.value = true;
  error.value = '';
  try {
    const response = await api.get('/photographer/photos/search-pexels', {
      params: {
        query: query.value,
        per_page: 18,
        orientation: props.orientation,
      },
    });
    results.value = response.data.data || [];
  } catch (err) {
    console.error('Pexels search failed', err);
    error.value = err.response?.data?.message || 'Failed to search Pexels.';
  } finally {
    searching.value = false;
  }
};

const quickSearch = (tag) => {
  query.value = tag;
  search();
};

const selectPhoto = async (photo) => {
  selectedPhoto.value = photo;
  zoom.value = 1;
  panX.value = 0.5;
  panY.value = 0.5;
  error.value = '';
  await loadImage(photo.image_url);
};

const loadImage = async (url) => {
  try {
    const response = await fetch(url, { mode: 'cors' });
    if (!response.ok) {
      throw new Error('Failed to load image');
    }
    const blob = await response.blob();
    const objectUrl = URL.createObjectURL(blob);
    const img = new Image();
    img.onload = () => {
      loadedImage.value = img;
      URL.revokeObjectURL(objectUrl);
      drawCanvas();
    };
    img.onerror = () => {
      URL.revokeObjectURL(objectUrl);
      error.value = 'Unable to load the selected photo.';
    };
    img.src = objectUrl;
  } catch (err) {
    console.error('Load image failed', err);
    error.value = 'Unable to load the selected photo.';
  }
};

const drawCanvas = () => {
  const canvas = canvasRef.value;
  const img = loadedImage.value;
  if (!canvas || !img) return;

  const ctx = canvas.getContext('2d');
  if (!ctx) return;

  ctx.imageSmoothingEnabled = true;
  ctx.imageSmoothingQuality = 'high';

  const targetWidth = props.targetWidth;
  const targetHeight = props.targetHeight;

  const baseScale = Math.max(targetWidth / img.width, targetHeight / img.height);
  const scale = baseScale * zoom.value;

  const scaledWidth = img.width * scale;
  const scaledHeight = img.height * scale;

  const maxOffsetX = Math.max(0, scaledWidth - targetWidth);
  const maxOffsetY = Math.max(0, scaledHeight - targetHeight);

  const offsetX = -maxOffsetX * panX.value;
  const offsetY = -maxOffsetY * panY.value;

  ctx.clearRect(0, 0, targetWidth, targetHeight);
  ctx.drawImage(img, offsetX, offsetY, scaledWidth, scaledHeight);
};

const resetCrop = () => {
  zoom.value = 1;
  panX.value = 0.5;
  panY.value = 0.5;
  drawCanvas();
};

const exportCrop = async () => {
  if (!canvasRef.value || !selectedPhoto.value) return;
  exporting.value = true;
  error.value = '';

  canvasRef.value.toBlob((blob) => {
    if (!blob) {
      exporting.value = false;
      error.value = 'Unable to export cropped image.';
      return;
    }
    const filename = `pexels-${selectedPhoto.value.id}.jpg`;
    const file = new File([blob], filename, { type: 'image/jpeg' });
    emit('select', {
      file,
      credit: {
        name: selectedPhoto.value.photographer,
        url: creditUrl.value,
      },
      source: selectedPhoto.value,
    });
    exporting.value = false;
  }, 'image/jpeg', 0.92);
};

const creditUrl = ref('');

watch(selectedPhoto, (photo) => {
  creditUrl.value = photo?.photographer_url || photo?.url || 'https://www.pexels.com';
});

watch([zoom, panX, panY, loadedImage], () => {
  drawCanvas();
});
</script>

<style scoped>
.pexels-canvas {
  width: 100%;
  height: auto;
  display: block;
}
</style>
