<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 flex items-center justify-between p-6 border-b bg-white">
          <h2 class="text-2xl font-bold text-gray-900">
            {{ template?.id ? 'Edit Template' : 'Create Certificate Template' }}
          </h2>
          <button
            @click="$emit('cancel')"
            class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
          >
            ×
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 grid grid-cols-2 gap-6">
          <!-- Left: Form -->
          <div class="space-y-4">
            <!-- Basic Info -->
            <div>
              <label class="block text-sm font-semibold text-gray-900 mb-2">Template Name</label>
              <input
                v-model="form.title"
                type="text"
                placeholder="e.g., Main Winner Certificate"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-900 mb-2">Description</label>
              <textarea
                v-model="form.description"
                placeholder="Brief description of this template"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent h-20"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-900 mb-2">Certificate Type</label>
              <select
                v-model="form.type"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              >
                <option value="">Select Type</option>
                <option v-for="type in availableTypes" :key="type.value" :value="type.value">
                  {{ type.label }}
                </option>
              </select>
            </div>

            <!-- Dimensions -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Width (mm)</label>
                <input
                  v-model.number="form.width"
                  type="number"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Height (mm)</label>
                <input
                  v-model.number="form.height"
                  type="number"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                />
              </div>
            </div>

            <!-- Design Elements -->
            <div>
              <label class="block text-sm font-semibold text-gray-900 mb-2">Design Elements</label>
              <div class="space-y-3">
                <!-- Background Color -->
                <div class="flex items-center gap-3">
                  <label class="text-sm text-gray-600">Background:</label>
                  <input
                    v-model="form.background_color"
                    type="color"
                    class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
                  />
                  <input
                    v-model="form.background_color"
                    type="text"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="#ffffff"
                  />
                </div>

                <!-- Accent Color -->
                <div class="flex items-center gap-3">
                  <label class="text-sm text-gray-600">Accent:</label>
                  <input
                    v-model="form.accent_color"
                    type="color"
                    class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
                  />
                  <input
                    v-model="form.accent_color"
                    type="text"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="#8B0000"
                  />
                </div>

                <!-- Text Color -->
                <div class="flex items-center gap-3">
                  <label class="text-sm text-gray-600">Text:</label>
                  <input
                    v-model="form.text_color"
                    type="color"
                    class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
                  />
                  <input
                    v-model="form.text_color"
                    type="text"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="#000000"
                  />
                </div>
              </div>
            </div>

            <!-- Font Settings -->
            <div>
              <label class="block text-sm font-semibold text-gray-900 mb-2">Title Font</label>
              <select
                v-model="form.title_font"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              >
                <option value="serif">Serif (Elegant)</option>
                <option value="sans-serif">Sans-Serif (Modern)</option>
                <option value="monospace">Monospace (Technical)</option>
              </select>
            </div>

            <!-- Placeholders -->
            <div>
              <label class="block text-sm font-semibold text-gray-900 mb-2">Available Placeholders</label>
              <div class="space-y-2 bg-gray-50 p-3 rounded-lg">
                <div 
                  v-for="ph in availablePlaceholders"
                  :key="ph.value"
                  @click="insertPlaceholder(ph.value)"
                  class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded cursor-pointer hover:bg-blue-200 transition"
                >
                  {{ ph.label }} <code class="text-xs">{{ ph.value }}</code>
                </div>
              </div>
            </div>

            <!-- Default Template -->
            <div class="flex items-center gap-2">
              <input
                id="is_default"
                v-model="form.is_default"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-2 focus:ring-orange-500"
              />
              <label for="is_default" class="text-sm text-gray-700">Set as default template</label>
            </div>
          </div>

          <!-- Right: Preview -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Preview</label>
            <div 
              class="border-4 border-gray-300 rounded-lg p-8 aspect-video flex flex-col items-center justify-center text-center overflow-hidden"
              :style="{
                backgroundColor: form.background_color,
                color: form.text_color,
                fontFamily: form.title_font
              }"
            >
              <div class="space-y-4">
                <!-- Decorative Top Border -->
                <div class="w-3/4 mx-auto" :style="{ borderTop: `3px solid ${form.accent_color}` }"></div>

                <!-- Title -->
                <h1 style="font-size: 2.5rem; font-weight: bold;">
                  {{ templateTypeLabel }}
                </h1>

                <!-- Decorative Line -->
                <div class="w-1/2 mx-auto my-4" :style="{ height: '2px', backgroundColor: form.accent_color }"></div>

                <!-- Recipient -->
                <p style="font-size: 1.25rem; font-style: italic;">This certifies that</p>
                <p class="px-4 pb-1" style="font-size: 1.5rem; font-weight: bold; border-bottom: 2px solid;">
                  [PHOTOGRAPHER_NAME]
                </p>

                <!-- Achievement Text -->
                <p style="font-size: 0.95rem; line-height: 1.6;">
                  has been awarded this certificate for exceptional achievement in competition
                </p>

                <!-- Decorative Bottom Border -->
                <div class="w-3/4 mx-auto" :style="{ borderTop: `3px solid ${form.accent_color}` }"></div>

                <!-- Date -->
                <p style="font-size: 0.85rem; margin-top: 1rem;">
                  [COMPETITION_DATE]
                </p>
              </div>
            </div>

            <!-- Preview Info -->
            <div class="mt-4 text-sm text-gray-600">
              <p><strong>Dimensions:</strong> {{ form.width }}×{{ form.height }}mm</p>
              <p><strong>Template:</strong> {{ templateTypeLabel }}</p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 flex items-center justify-end gap-3 p-6 border-t bg-gray-50">
          <button
            @click="$emit('cancel')"
            class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium"
          >
            Cancel
          </button>
          <button
            @click="submit"
            :disabled="isSaving || !isFormValid"
            class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition"
          >
            {{ isSaving ? 'Saving...' : (template?.id ? 'Update Template' : 'Create Template') }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  template: Object,
  availableTypes: Array
});

const emit = defineEmits(['save', 'cancel']);

const isSaving = ref(false);

const form = ref({
  title: '',
  description: '',
  type: '',
  width: 297,
  height: 210,
  background_color: '#ffffff',
  accent_color: '#8B0000',
  text_color: '#000000',
  title_font: 'serif',
  is_default: false,
  template_content: ''
});

const availablePlaceholders = [
  { label: 'Photographer Name', value: '[PHOTOGRAPHER_NAME]' },
  { label: 'Competition Name', value: '[COMPETITION_NAME]' },
  { label: 'Competition Date', value: '[COMPETITION_DATE]' },
  { label: 'Achievement Type', value: '[ACHIEVEMENT_TYPE]' },
  { label: 'Position/Rank', value: '[POSITION]' },
  { label: 'Award Date', value: '[AWARD_DATE]' },
  { label: 'Signature Line', value: '[SIGNATURE_LINE]' },
  { label: 'Certificate Number', value: '[CERTIFICATE_NUMBER]' }
];

const templateTypeLabel = computed(() => {
  const typeMap = {
    participation: 'Certificate of Participation',
    finalist: 'Finalist Certificate',
    winner: 'Winner Certificate',
    merit: 'Merit Certificate'
  };
  return typeMap[form.value.type] || 'Certificate';
});

const isFormValid = computed(() => {
  return form.value.title && form.value.type && form.value.width && form.value.height;
});

const insertPlaceholder = (placeholder) => {
  // This would typically insert into a content editor
  console.log('Insert placeholder:', placeholder);
};

const submit = async () => {
  if (!isFormValid.value) return;

  isSaving.value = true;
  try {
    emit('save', form.value);
  } finally {
    isSaving.value = false;
  }
};

watch(() => props.template, (newTemplate) => {
  if (newTemplate) {
    form.value = {
      ...newTemplate,
      template_content: newTemplate.template_content || ''
    };
  }
}, { immediate: true });
</script>

<style scoped>
</style>
