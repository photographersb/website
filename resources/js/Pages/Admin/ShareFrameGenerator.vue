<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Header -->
    <AdminHeader 
      title="🎨 Share Frame Generator" 
      subtitle="Create branded frames for social media sharing"
    />

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <!-- Preview Panel (Top - Full Width) -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b border-orange-100">
          <h3 class="text-xl font-bold text-gray-900">📸 Live Preview</h3>
          <p class="text-sm text-gray-600 mt-1">Your frame will appear below</p>
        </div>
        <div class="p-8 flex justify-center items-center bg-gradient-to-b from-gray-50 to-gray-100 min-h-96" :style="previewContainerStyle">
          <!-- Frame Preview -->
          <div
            v-if="previewData"
            data-frame-canvas
            class="relative shadow-2xl rounded-xl overflow-hidden border-8 border-white"
            :style="getFrameDimensions"
          >
            <!-- Background -->
            <div
              class="absolute inset-0"
              :style="getBackgroundStyle"
            ></div>

            <!-- Content -->
            <div class="relative w-full h-full flex flex-col items-center justify-center px-6 py-8 text-center overflow-hidden">
              <!-- Logo Area -->
              <div v-if="config.includeLogo" class="mb-4 flex-shrink-0">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
                  <svg class="w-8 h-8 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                  </svg>
                </div>
              </div>

              <!-- Title -->
              <h2 class="font-bold leading-tight line-clamp-3" :style="{...getTitleStyle, fontSize: 'clamp(1.5rem, 8vw, 2.5rem)'}">
                {{ config.title || 'Frame Title' }}
              </h2>

              <!-- Subtitle -->
              <p v-if="config.subtitle" class="mt-2 leading-snug line-clamp-4" :style="{...getSubtitleStyle, fontSize: 'clamp(0.875rem, 5vw, 1.125rem)'}">
                {{ config.subtitle }}
              </p>

              <!-- Badge/Icon -->
              <div v-if="config.frameType !== 'achievement'" class="my-3 flex-shrink-0">
                <svg class="w-16 h-16" :style="getIconStyle" fill="currentColor" viewBox="0 0 24 24">
                  <path v-if="config.frameType === 'certificate'" d="M20 6h-2.15l1.46-1.46c.39-.39.39-1.02 0-1.41-.39-.39-1.02-.39-1.41 0L16.59 6H7.41L5.1 2.13c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.02 0 1.41L5.15 6H3c-1.66 0-3 1.34-3 3v11c0 1.66 1.34 3 3 3h17c1.66 0 3-1.34 3-3V9c0-1.66-1.34-3-3-3zm0 14H3V9h17v11z"/>
                  <path v-else-if="config.frameType === 'winner'" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                  <path v-else d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
              </div>

              <!-- QR Code Area -->
              <div v-if="config.includeQr" class="mt-4 flex-shrink-0">
                <div class="w-16 h-16 bg-white rounded p-1 shadow-lg">
                  <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center text-xs text-gray-600 font-mono">QR</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-else class="flex flex-col items-center justify-center py-20 text-gray-500">
            <svg class="w-16 h-16 mb-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M7.172 7.172A4 4 0 0112.828 12m5.656-5.656a4 4 0 010 5.656M7.172 7.172a4 4 0 015.656 0"/>
            </svg>
            <p class="text-lg font-medium">Generating preview...</p>
          </div>
        </div>

        <!-- Format Info -->
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-gray-600"><strong>Format:</strong> {{ getFormatLabel }} ({{ getFormatDimensions }}px)</span>
            </div>
            <div>
              <span class="text-gray-600"><strong>Color:</strong> {{ getColorSchemeLabel }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Configuration Panel (Bottom) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Configuration (2 columns) -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-lg p-8">
            <h3 class="text-lg font-bold text-gray-900 mb-6 pb-4 border-b-2 border-orange-200">⚙️ Frame Configuration</h3>
            
            <!-- Configuration Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Left Column: Steps 1-3 -->
              <div class="space-y-5">
                <!-- Step 1: Frame Type Selection -->
                <div class="group">
                  <label class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center text-xs font-bold shadow-md group-hover:scale-110 transition">1</span>
                    <span>Frame Type</span>
                  </label>
                  <select
                    v-model="config.frameType"
                    @change="updatePreview"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white hover:border-orange-300 transition"
                  >
                    <option value="certificate">🎓 Certificate</option>
                    <option value="winner">🏆 Winner Badge</option>
                    <option value="finalist">⭐ Finalist Badge</option>
                    <option value="achievement">✨ Achievement</option>
                  </select>
                </div>

                <!-- Step 2: Title -->
                <div class="group">
                  <label class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center text-xs font-bold shadow-md group-hover:scale-110 transition">2</span>
                    <span>Title Text</span>
                  </label>
                  <input
                    v-model="config.title"
                    @input="updatePreview"
                    type="text"
                    placeholder="e.g., Certificate of Excellence"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:border-orange-300 transition"
                  />
                </div>

                <!-- Step 3: Subtitle -->
                <div class="group">
                  <label class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center text-xs font-bold shadow-md group-hover:scale-110 transition">3</span>
                    <span>Subtitle</span>
                  </label>
                  <input
                    v-model="config.subtitle"
                    @input="updatePreview"
                    type="text"
                    placeholder="e.g., Photography Excellence Awards 2026"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent hover:border-orange-300 transition"
                  />
                </div>
              </div>

              <!-- Right Column: Steps 4-5 -->
              <div class="space-y-5">
                <!-- Step 4: Format Selection -->
                <div class="group">
                  <label class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center text-xs font-bold shadow-md group-hover:scale-110 transition">4</span>
                    <span>Format</span>
                  </label>
                  <select
                    v-model="config.format"
                    @change="updatePreview"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white hover:border-orange-300 transition"
                  >
                    <option value="instagram-story">📱 Instagram Story (1080×1920)</option>
                    <option value="instagram-post">📷 Instagram Post (1080×1080)</option>
                    <option value="facebook">👍 Facebook (1200×628)</option>
                    <option value="twitter">𝕏 X/Twitter (1200×675)</option>
                  </select>
                </div>

                <!-- Step 5: Color Scheme with Visual Buttons -->
                <div class="group">
                  <label class="block text-sm font-semibold text-gray-900 mb-3 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center text-xs font-bold shadow-md group-hover:scale-110 transition">5</span>
                    <span>Color Scheme</span>
                  </label>
                  <div class="grid grid-cols-5 gap-2">
                    <button
                      v-for="scheme in Object.keys(colorSchemes)"
                      :key="scheme"
                      @click="config.colorScheme = scheme; updatePreview()"
                      class="p-3 rounded-lg border-2 transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                      :class="config.colorScheme === scheme ? 'ring-2 ring-offset-2 ring-orange-500 border-orange-500 shadow-lg' : 'border-gray-300 hover:border-orange-300'"
                      :title="scheme.charAt(0).toUpperCase() + scheme.slice(1)"
                    >
                      <div class="flex gap-1 h-8">
                        <div class="flex-1 rounded" :style="{ backgroundColor: colorSchemes[scheme].primary }"></div>
                        <div class="flex-1 rounded" :style="{ backgroundColor: colorSchemes[scheme].secondary }"></div>
                      </div>
                    </button>
                  </div>
                </div>

                <!-- Background Style -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 mb-2">Background Style</label>
                  <select
                    v-model="config.backgroundStyle"
                    @change="updatePreview"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white hover:border-orange-300 transition"
                  >
                    <option value="gradient">🎨 Gradient</option>
                    <option value="solid">⬛ Solid Color</option>
                    <option value="pattern">🔲 Pattern</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Additional Options -->
            <div class="mt-8 pt-6 border-t border-gray-100 space-y-3">
              <h4 class="text-sm font-semibold text-gray-900 mb-3">✨ Additional Options</h4>
              <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded transition">
                <input
                  v-model="config.includeQr"
                  @change="updatePreview"
                  type="checkbox"
                  class="w-4 h-4 text-orange-500 rounded focus:ring-2 focus:ring-orange-500"
                />
                <span class="text-sm font-medium text-gray-700">📱 Include QR Code</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded transition">
                <input
                  v-model="config.includeLogo"
                  @change="updatePreview"
                  type="checkbox"
                  class="w-4 h-4 text-orange-500 rounded focus:ring-2 focus:ring-orange-500"
                />
                <span class="text-sm font-medium text-gray-700">🏢 Include Logo</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Action Panel (Sidebar) -->
        <div class="lg:col-span-1">
          <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl shadow-lg p-6 border border-orange-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b-2 border-orange-200">🚀 Actions</h3>
            
            <div class="space-y-3">
              <!-- Generate Button -->
              <button
                @click="generateFrame"
                :disabled="isGenerating"
                class="w-full px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-lg hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-105 active:scale-95"
              >
                <span v-if="!isGenerating">✨ Generate Frame</span>
                <span v-else class="flex items-center justify-center gap-2">
                  <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M7.172 7.172A4 4 0 0112.828 12m5.656-5.656a4 4 0 010 5.656M7.172 7.172a4 4 0 015.656 0"/>
                  </svg>
                  Generating...
                </span>
              </button>

              <!-- Download Options -->
              <div class="space-y-2 pt-2">
                <p class="text-xs font-semibold text-gray-600 uppercase">Download As:</p>
                <button
                  @click="downloadFrame"
                  :disabled="!frameGenerated"
                  class="w-full px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                  📥 PNG
                </button>
                <button
                  @click="downloadJPEG"
                  :disabled="!frameGenerated"
                  class="w-full px-4 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                  📥 JPEG
                </button>
              </div>

              <!-- Status Info -->
              <div class="mt-6 pt-6 border-t border-orange-200 bg-white rounded-lg p-4">
                <div class="space-y-2 text-sm">
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Frame:</span>
                    <span class="font-semibold text-gray-900">{{ config.frameType }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Format:</span>
                    <span class="font-semibold text-gray-900 text-right">{{ getFormatLabel }}</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span v-if="frameGenerated" class="text-green-600 font-semibold">✓ Ready</span>
                    <span v-else class="text-gray-500 font-semibold">Pending</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div 
      v-if="toastMessage"
      :class="[
        'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transition z-50 shadow-lg',
        toastType === 'success' ? 'bg-green-600' : 'bg-red-600'
      ]"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import html2canvas from 'html2canvas';
import AdminHeader from '../../components/AdminHeader.vue';

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
