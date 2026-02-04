<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="Share Frame Template"
      :subtitle="`Configure social share frames for ${competition.name}`"
    />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <AdminQuickNav />

      <!-- Success/Error Messages -->
      <div v-if="$page.props.flash?.success" class="mb-6">
        <div class="bg-green-50 border border-green-300 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <div class="text-green-600 text-lg font-bold">✓</div>
            <div>
              <h3 class="text-green-900 font-semibold">Success</h3>
              <p class="text-green-700 text-sm mt-1">{{ $page.props.flash.success }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Configuration Form -->
        <div class="space-y-6">
          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Colors -->
            <div class="bg-white rounded-lg shadow-card p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Colors</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Background Color</label>
                  <div class="flex gap-3">
                    <input
                      v-model="form.background_color"
                      type="color"
                      class="h-10 w-20 rounded border border-gray-300"
                    />
                    <input
                      v-model="form.background_color"
                      type="text"
                      class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                      placeholder="#1a1a1a"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Text Color</label>
                  <div class="flex gap-3">
                    <input
                      v-model="form.text_color"
                      type="color"
                      class="h-10 w-20 rounded border border-gray-300"
                    />
                    <input
                      v-model="form.text_color"
                      type="text"
                      class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                      placeholder="#ffffff"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Accent Color</label>
                  <div class="flex gap-3">
                    <input
                      v-model="form.accent_color"
                      type="color"
                      class="h-10 w-20 rounded border border-gray-300"
                    />
                    <input
                      v-model="form.accent_color"
                      type="text"
                      class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                      placeholder="#3b82f6"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Typography -->
            <div class="bg-white rounded-lg shadow-card p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Typography</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Primary Font</label>
                  <select
                    v-model="form.primary_font"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                  >
                    <option value="Inter">Inter</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Open Sans">Open Sans</option>
                    <option value="Lato">Lato</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Poppins">Poppins</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Font</label>
                  <select
                    v-model="form.secondary_font"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                  >
                    <option value="Inter">Inter</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Open Sans">Open Sans</option>
                    <option value="Lato">Lato</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Poppins">Poppins</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Call to Action Message</label>
                  <input
                    v-model="form.cta_message"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                    placeholder="Scan to vote for my photo!"
                  />
                </div>
              </div>
            </div>

            <!-- Watermark Settings -->
            <div class="bg-white rounded-lg shadow-card p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Watermark</h2>
              
              <div class="space-y-4">
                <div class="flex items-center gap-3">
                  <input
                    v-model="form.watermark_enabled"
                    type="checkbox"
                    class="h-4 w-4 text-burgundy-600 rounded focus:ring-burgundy-500"
                  />
                  <label class="text-sm font-medium text-gray-700">Enable Watermark</label>
                </div>

                <div v-if="form.watermark_enabled">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Watermark Text</label>
                  <input
                    v-model="form.watermark_text"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                    placeholder="Photographar"
                  />
                </div>

                <div v-if="form.watermark_enabled">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Watermark Opacity ({{ form.watermark_opacity }}%)
                  </label>
                  <input
                    v-model.number="form.watermark_opacity"
                    type="range"
                    min="0"
                    max="100"
                    class="w-full"
                  />
                </div>
              </div>
            </div>

            <!-- QR Code Settings -->
            <div class="bg-white rounded-lg shadow-card p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">QR Code</h2>
              
              <div class="space-y-4">
                <div class="flex items-center gap-3">
                  <input
                    v-model="form.qr_code_enabled"
                    type="checkbox"
                    class="h-4 w-4 text-burgundy-600 rounded focus:ring-burgundy-500"
                  />
                  <label class="text-sm font-medium text-gray-700">Enable QR Code</label>
                </div>

                <div v-if="form.qr_code_enabled">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    QR Code Size ({{ form.qr_code_size }}px)
                  </label>
                  <input
                    v-model.number="form.qr_code_size"
                    type="range"
                    min="100"
                    max="400"
                    step="10"
                    class="w-full"
                  />
                </div>
              </div>
            </div>

            <!-- Layout Settings -->
            <div class="bg-white rounded-lg shadow-card p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Layout</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Image Fit Strategy</label>
                  <select
                    v-model="form.fit_strategy"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                  >
                    <option value="contain">Contain (Show entire image)</option>
                    <option value="cover">Cover (Fill frame, may crop)</option>
                  </select>
                  <p class="mt-1 text-sm text-gray-500">
                    <span v-if="form.fit_strategy === 'contain'">
                      Entire image will be visible with padding if needed
                    </span>
                    <span v-else>
                      Frame will be completely filled, edges may be cropped
                    </span>
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Padding ({{ form.padding }}px)
                  </label>
                  <input
                    v-model.number="form.padding"
                    type="range"
                    min="0"
                    max="200"
                    step="5"
                    class="w-full"
                  />
                </div>
              </div>
            </div>

            <!-- Background Image (Optional) -->
            <div class="bg-white rounded-lg shadow-card p-6">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Background Image (Optional)</h2>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Background</label>
                <input
                  type="file"
                  accept="image/*"
                  @change="handleBackgroundUpload"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500"
                />
                <p class="mt-1 text-sm text-gray-500">Optional: Use a custom background image instead of solid color</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
              <button
                type="submit"
                :disabled="form.processing"
                class="flex-1 bg-burgundy-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-burgundy-700 transition disabled:opacity-50"
              >
                {{ form.processing ? 'Saving...' : 'Save Template' }}
              </button>
              
              <Link
                :href="`/admin/competitions/${competition.id}/edit`"
                class="px-6 py-3 border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition"
              >
                Cancel
              </Link>
            </div>
          </form>
        </div>

        <!-- Right: Live Preview -->
        <div class="space-y-6">
          <div class="bg-white rounded-lg shadow-card p-6 sticky top-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Preview</h2>
            
            <div class="space-y-4">
              <!-- Format Selector -->
              <div class="flex gap-2">
                <button
                  v-for="format in previewFormats"
                  :key="format.value"
                  @click="selectedFormat = format.value"
                  :class="[
                    'flex-1 px-3 py-2 rounded-lg text-sm font-medium transition',
                    selectedFormat === format.value
                      ? 'bg-burgundy-600 text-white'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  {{ format.label }}
                </button>
              </div>

              <!-- Preview Canvas -->
              <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center min-h-[400px]">
                <div
                  :style="{
                    backgroundColor: form.background_color,
                    aspectRatio: getAspectRatio(selectedFormat),
                    maxWidth: '100%',
                    maxHeight: '500px',
                  }"
                  class="relative rounded-lg shadow-lg overflow-hidden"
                >
                  <!-- Simulated Content -->
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div :style="{ color: form.text_color }" class="text-center p-4">
                      <p class="text-sm opacity-70">Sample Photo</p>
                      <p class="text-xs opacity-50 mt-2">{{ competition.name }}</p>
                    </div>
                  </div>

                  <!-- Text Overlay Preview -->
                  <div
                    class="absolute bottom-0 left-0 right-0 p-4"
                    :style="{
                      background: `linear-gradient(to top, rgba(0,0,0,0.7), transparent)`,
                      padding: `${form.padding}px`,
                    }"
                  >
                    <p :style="{ color: form.text_color }" class="text-sm font-bold mb-1">
                      {{ competition.name }}
                    </p>
                    <p :style="{ color: form.text_color }" class="text-xs opacity-90">
                      by Sample Photographer
                    </p>
                    <p :style="{ color: form.accent_color }" class="text-xs mt-2">
                      {{ form.cta_message }}
                    </p>
                  </div>

                  <!-- QR Code Preview -->
                  <div
                    v-if="form.qr_code_enabled"
                    class="absolute bottom-4 right-4 bg-white p-2 rounded"
                    :style="{ width: `${form.qr_code_size / 4}px`, height: `${form.qr_code_size / 4}px` }"
                  >
                    <div class="w-full h-full bg-gray-300 flex items-center justify-center text-xs text-gray-500">
                      QR
                    </div>
                  </div>

                  <!-- Watermark Preview -->
                  <div
                    v-if="form.watermark_enabled"
                    class="absolute top-4 right-4"
                    :style="{
                      color: form.text_color,
                      opacity: form.watermark_opacity / 100,
                    }"
                  >
                    <p class="text-xs font-light">{{ form.watermark_text }}</p>
                  </div>
                </div>
              </div>

              <!-- Format Info -->
              <div class="text-xs text-gray-500 text-center">
                {{ getFormatInfo(selectedFormat) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminHeader from '@/Components/Admin/AdminHeader.vue';
import AdminQuickNav from '@/Components/Admin/AdminQuickNav.vue';

const props = defineProps({
  competition: Object,
  template: Object,
});

const form = useForm({
  background_color: props.template.background_color || '#1a1a1a',
  text_color: props.template.text_color || '#ffffff',
  accent_color: props.template.accent_color || '#3b82f6',
  primary_font: props.template.primary_font || 'Inter',
  secondary_font: props.template.secondary_font || 'Inter',
  cta_message: props.template.cta_message || 'Scan to vote for my photo!',
  watermark_enabled: props.template.watermark_enabled ?? true,
  watermark_text: props.template.watermark_text || 'Photographar',
  watermark_opacity: props.template.watermark_opacity || 30,
  qr_code_enabled: props.template.qr_code_enabled ?? true,
  qr_code_size: props.template.qr_code_size || 250,
  padding: props.template.padding || 40,
  fit_strategy: props.template.fit_strategy || 'contain',
  background_image: null,
  is_active: props.template.is_active ?? true,
});

const selectedFormat = ref('post');

const previewFormats = [
  { value: 'story', label: 'Story' },
  { value: 'post', label: 'Post' },
  { value: 'portrait', label: 'Portrait' },
  { value: 'landscape', label: 'Landscape' },
];

const getAspectRatio = (format) => {
  const ratios = {
    story: '9/16',
    post: '1/1',
    portrait: '4/5',
    landscape: '16/9',
  };
  return ratios[format];
};

const getFormatInfo = (format) => {
  const info = {
    story: '1080×1920 - Instagram Story',
    post: '1080×1080 - Instagram Post',
    portrait: '1080×1350 - Instagram Portrait',
    landscape: '1200×675 - Facebook/Twitter',
  };
  return info[format];
};

const handleBackgroundUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.background_image = file;
  }
};

const submitForm = () => {
  form.put(`/admin/competitions/${props.competition.id}/share-frame-template`, {
    preserveScroll: true,
  });
};
</script>
