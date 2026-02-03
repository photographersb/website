<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Share Frame Generator</h1>
        <p class="text-gray-600 mt-1">Create branded frames for social media sharing</p>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Configuration Panel -->
      <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
          <!-- Frame Type Selection -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Frame Type</label>
            <select
              v-model="config.frameType"
              @change="updatePreview"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            >
              <option value="certificate">Certificate</option>
              <option value="winner">Winner Badge</option>
              <option value="finalist">Finalist Badge</option>
              <option value="achievement">Achievement</option>
            </select>
          </div>

          <!-- Title -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Title Text</label>
            <input
              v-model="config.title"
              @input="updatePreview"
              type="text"
              placeholder="e.g., Certificate of Excellence"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            />
          </div>

          <!-- Subtitle -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Subtitle</label>
            <input
              v-model="config.subtitle"
              @input="updatePreview"
              type="text"
              placeholder="e.g., Photography Excellence Awards 2026"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            />
          </div>

          <!-- Format Selection -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Format</label>
            <div class="space-y-2">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="config.format"
                  value="instagram-story"
                  @change="updatePreview"
                  type="radio"
                  class="w-4 h-4 text-orange-500"
                />
                <span class="text-sm">Instagram Story (1080×1920)</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="config.format"
                  value="instagram-post"
                  @change="updatePreview"
                  type="radio"
                  class="w-4 h-4 text-orange-500"
                />
                <span class="text-sm">Instagram Post (1080×1080)</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="config.format"
                  value="facebook"
                  @change="updatePreview"
                  type="radio"
                  class="w-4 h-4 text-orange-500"
                />
                <span class="text-sm">Facebook (1200×628)</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="config.format"
                  value="twitter"
                  @change="updatePreview"
                  type="radio"
                  class="w-4 h-4 text-orange-500"
                />
                <span class="text-sm">X/Twitter (1200×675)</span>
              </label>
            </div>
          </div>

          <!-- Color Scheme -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Color Scheme</label>
            <select
              v-model="config.colorScheme"
              @change="updatePreview"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            >
              <option value="burgundy">Burgundy (Brand)</option>
              <option value="gold">Gold Premium</option>
              <option value="blue">Blue Professional</option>
              <option value="green">Green Modern</option>
              <option value="purple">Purple Elegant</option>
            </select>
          </div>

          <!-- Background Style -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Background</label>
            <select
              v-model="config.backgroundStyle"
              @change="updatePreview"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            >
              <option value="solid">Solid Color</option>
              <option value="gradient">Gradient</option>
              <option value="pattern">Pattern</option>
            </select>
          </div>

          <!-- Include QR Code -->
          <div class="flex items-center gap-2">
            <input
              id="include_qr"
              v-model="config.includeQr"
              @change="updatePreview"
              type="checkbox"
              class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-2 focus:ring-orange-500"
            />
            <label for="include_qr" class="text-sm text-gray-700">Include QR Code</label>
          </div>

          <!-- Include Logo -->
          <div class="flex items-center gap-2">
            <input
              id="include_logo"
              v-model="config.includeLogo"
              @change="updatePreview"
              type="checkbox"
              class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-2 focus:ring-orange-500"
            />
            <label for="include_logo" class="text-sm text-gray-700">Include Logo</label>
          </div>

          <!-- Action Buttons -->
          <div class="pt-4 space-y-2">
            <button
              @click="generateFrame"
              :disabled="isGenerating"
              class="w-full px-4 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition"
            >
              {{ isGenerating ? 'Generating...' : 'Generate Frame' }}
            </button>
            <button
              @click="downloadFrame"
              :disabled="!frameGenerated"
              class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition"
            >
              Download PNG
            </button>
            <button
              @click="downloadJPEG"
              :disabled="!frameGenerated"
              class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition"
            >
              Download JPEG
            </button>
          </div>
        </div>
      </div>

      <!-- Preview Panel -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview</h3>
          
          <div class="flex justify-center items-center bg-gray-100 rounded-lg overflow-hidden" :style="previewContainerStyle">
            <!-- Frame Preview -->
            <div
              v-if="previewData"
              class="relative"
              :style="getFrameDimensions"
            >
              <!-- Background -->
              <div
                class="absolute inset-0"
                :style="getBackgroundStyle"
              ></div>

              <!-- Content -->
              <div class="relative h-full flex flex-col items-center justify-center p-8 text-center">
                <!-- Logo Area -->
                <div v-if="config.includeLogo" class="mb-6">
                  <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                    </svg>
                  </div>
                </div>

                <!-- Title -->
                <h2 class="text-4xl font-bold mb-3" :style="getTitleStyle">
                  {{ config.title || 'Frame Title' }}
                </h2>

                <!-- Subtitle -->
                <p v-if="config.subtitle" class="text-lg mb-6" :style="getSubtitleStyle">
                  {{ config.subtitle }}
                </p>

                <!-- Badge/Icon -->
                <div v-if="config.frameType !== 'achievement'" class="my-4">
                  <svg class="w-24 h-24" :style="getIconStyle" fill="currentColor" viewBox="0 0 24 24">
                    <path v-if="config.frameType === 'certificate'" d="M20 6h-2.15l1.46-1.46c.39-.39.39-1.02 0-1.41-.39-.39-1.02-.39-1.41 0L16.59 6H7.41L5.1 2.13c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.02 0 1.41L5.15 6H3c-1.66 0-3 1.34-3 3v11c0 1.66 1.34 3 3 3h17c1.66 0 3-1.34 3-3V9c0-1.66-1.34-3-3-3zm0 14H3V9h17v11z"/>
                    <path v-else-if="config.frameType === 'winner'" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                    <path v-else d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                  </svg>
                </div>

                <!-- QR Code Area -->
                <div v-if="config.includeQr" class="mt-6">
                  <div class="w-20 h-20 bg-white rounded p-1 shadow-lg">
                    <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center text-xs text-gray-600">
                      QR Code
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Loading State -->
            <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
              <svg class="w-12 h-12 mb-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M7.172 7.172A4 4 0 0112.828 12m5.656-5.656a4 4 0 010 5.656M7.172 7.172a4 4 0 015.656 0"/>
              </svg>
              <p>Frame preview will appear here</p>
            </div>
          </div>

          <!-- Format Info -->
          <div class="mt-4 text-xs text-gray-600 space-y-1">
            <p><strong>Current Format:</strong> {{ getFormatLabel }} ({{ getFormatDimensions }}px)</p>
            <p><strong>Color Scheme:</strong> {{ getColorSchemeLabel }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div 
      v-if="toastMessage"
      :class="[
        'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transition z-50',
        toastType === 'success' ? 'bg-green-600' : 'bg-red-600'
      ]"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const config = ref({
  frameType: 'certificate',
  title: 'Certificate of Excellence',
  subtitle: 'Photography Excellence Awards 2026',
  format: 'instagram-story',
  colorScheme: 'burgundy',
  backgroundStyle: 'gradient',
  includeQr: true,
  includeLogo: true
});

const isGenerating = ref(false);
const frameGenerated = ref(false);
const previewData = ref(null);
const toastMessage = ref('');
const toastType = ref('success');

const colorSchemes = {
  burgundy: { primary: '#8B0000', secondary: '#D4A574', accent: '#FFFFFF' },
  gold: { primary: '#FFD700', secondary: '#F4A460', accent: '#000000' },
  blue: { primary: '#0066CC', secondary: '#87CEEB', accent: '#FFFFFF' },
  green: { primary: '#2E7D32', secondary: '#81C784', accent: '#FFFFFF' },
  purple: { primary: '#6A0DAD', secondary: '#DA70D6', accent: '#FFFFFF' }
};

const formats = {
  'instagram-story': { width: 1080, height: 1920, label: 'Instagram Story' },
  'instagram-post': { width: 1080, height: 1080, label: 'Instagram Post' },
  'facebook': { width: 1200, height: 628, label: 'Facebook' },
  'twitter': { width: 1200, height: 675, label: 'X/Twitter' }
};

const getFormatDimensions = computed(() => {
  const fmt = formats[config.value.format];
  return `${fmt.width}×${fmt.height}`;
});

const getFormatLabel = computed(() => {
  return formats[config.value.format].label;
});

const getColorSchemeLabel = computed(() => {
  const labels = {
    burgundy: 'Burgundy (Brand)',
    gold: 'Gold Premium',
    blue: 'Blue Professional',
    green: 'Green Modern',
    purple: 'Purple Elegant'
  };
  return labels[config.value.colorScheme];
});

const getFrameDimensions = computed(() => {
  const fmt = formats[config.value.format];
  const scale = 0.15;
  return {
    width: `${fmt.width * scale}px`,
    height: `${fmt.height * scale}px`
  };
});

const previewContainerStyle = computed(() => {
  const fmt = formats[config.value.format];
  const scale = 0.15;
  return {
    minHeight: `${fmt.height * scale + 40}px`,
    minWidth: `${fmt.width * scale + 40}px`
  };
});

const getBackgroundStyle = computed(() => {
  const colors = colorSchemes[config.value.colorScheme];
  if (config.value.backgroundStyle === 'solid') {
    return { backgroundColor: colors.primary };
  } else if (config.value.backgroundStyle === 'gradient') {
    return {
      background: `linear-gradient(135deg, ${colors.primary} 0%, ${colors.secondary} 100%)`
    };
  }
  return {
    backgroundColor: colors.primary,
    backgroundImage: 'repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.1) 10px, rgba(255,255,255,.1) 20px)'
  };
});

const getTitleStyle = computed(() => {
  const colors = colorSchemes[config.value.colorScheme];
  return { color: colors.accent };
});

const getSubtitleStyle = computed(() => {
  const colors = colorSchemes[config.value.colorScheme];
  return { color: colors.accent, opacity: 0.9 };
});

const getIconStyle = computed(() => {
  const colors = colorSchemes[config.value.colorScheme];
  return { color: colors.secondary };
});

const updatePreview = () => {
  previewData.value = { updated: Date.now() };
};

const generateFrame = async () => {
  isGenerating.value = true;
  try {
    // Simulate frame generation
    await new Promise(resolve => setTimeout(resolve, 1500));
    frameGenerated.value = true;
    showToast('Frame generated successfully!', 'success');
  } catch (error) {
    console.error('Error generating frame:', error);
    showToast('Failed to generate frame', 'error');
  } finally {
    isGenerating.value = false;
  }
};

const downloadFrame = async () => {
  try {
    const canvas = await html2canvas(document.querySelector('[data-frame-canvas]'), {
      backgroundColor: null,
      scale: 2
    });
    const link = document.createElement('a');
    link.href = canvas.toDataURL('image/png');
    link.download = `frame-${Date.now()}.png`;
    link.click();
    showToast('Frame downloaded successfully!', 'success');
  } catch (error) {
    console.error('Error downloading frame:', error);
    showToast('Failed to download frame', 'error');
  }
};

const downloadJPEG = async () => {
  try {
    const canvas = await html2canvas(document.querySelector('[data-frame-canvas]'), {
      backgroundColor: '#ffffff',
      scale: 2
    });
    const link = document.createElement('a');
    link.href = canvas.toDataURL('image/jpeg', 0.95);
    link.download = `frame-${Date.now()}.jpg`;
    link.click();
    showToast('Frame downloaded successfully!', 'success');
  } catch (error) {
    console.error('Error downloading frame:', error);
    showToast('Failed to download frame', 'error');
  }
};

const showToast = (message, type) => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

updatePreview();
</script>

<style scoped>
</style>
