<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-semibold text-gray-900">{{ template?.id ? 'Edit Template' : 'Create Template' }}</h2>
          <button class="text-gray-500" @click="$emit('cancel')">✕</button>
        </div>

        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="label">Title</label>
            <input v-model="form.title" class="field" type="text" placeholder="Template title">
          </div>

          <div class="md:col-span-2">
            <label class="label">Description</label>
            <textarea v-model="form.description" class="field" rows="2" placeholder="Short template description" />
          </div>

          <div>
            <label class="label">Type</label>
            <select v-model="form.type" class="field">
              <option value="">Select type</option>
              <option v-for="type in availableTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
            </select>
          </div>

          <div>
            <label class="label">Title Font</label>
            <select v-model="form.font_family" class="field">
              <option value="serif">Serif</option>
              <option value="sans-serif">Sans-serif</option>
              <option value="monospace">Monospace</option>
            </select>
          </div>

          <div>
            <label class="label">Font Size</label>
            <input v-model.number="form.font_size" class="field" type="number" min="10" max="120">
          </div>

          <div>
            <label class="label">Width (mm)</label>
            <input v-model.number="form.width" class="field" type="number">
          </div>

          <div>
            <label class="label">Height (mm)</label>
            <input v-model.number="form.height" class="field" type="number">
          </div>

          <div>
            <label class="label">Background Color</label>
            <input v-model="form.background_color" class="field" type="color">
          </div>

          <div>
            <label class="label">Background Image URL</label>
            <input v-model="form.background_image" class="field" type="text" placeholder="https://.../background.jpg">
          </div>

          <div>
            <label class="label">Accent Color</label>
            <input v-model="form.accent_color" class="field" type="color">
          </div>

          <div class="md:col-span-2">
            <label class="label">Text Color</label>
            <input v-model="form.text_color" class="field" type="color">
          </div>

          <div class="md:col-span-2">
            <label class="label">Template Content (HTML supported)</label>
            <textarea
              v-model="form.template_content"
              class="field"
              rows="6"
              placeholder="Use placeholders: {{name}}, {{event}}, {{date}}, {{certificate_id}}"
            />
          </div>

          <div class="md:col-span-2 flex items-center gap-2">
            <input id="is_default" v-model="form.is_default" type="checkbox">
            <label for="is_default" class="text-sm text-gray-700">Set as default for this type</label>
          </div>
        </div>

        <div class="p-4 border-t flex items-center justify-end gap-2">
          <button class="btn-secondary" @click="$emit('cancel')">Cancel</button>
          <button class="btn-primary" :disabled="saving || !isValid" @click="submit">
            {{ saving ? 'Saving...' : (template?.id ? 'Update Template' : 'Create Template') }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  template: { type: Object, default: null },
  availableTypes: { type: Array, default: () => [] },
})

const emit = defineEmits(['save', 'cancel'])

const saving = ref(false)
const form = ref({
  title: '',
  description: '',
  type: '',
  width: 297,
  height: 210,
  background_color: '#ffffff',
  background_image: '',
  accent_color: '#8e0e3f',
  text_color: '#111827',
  font_family: 'serif',
  font_size: 42,
  title_font: 'serif',
  is_default: false,
  template_content: 'This certifies that <strong>{{name}}</strong> completed <strong>{{event_name}}</strong> on {{date}}. Award: {{award_title}}. Certificate ID: {{certificate_code}}. Platform: {{platform_name}}',
})

const isValid = computed(() => {
  return Boolean(form.value.title && form.value.type && form.value.width && form.value.height)
})

const submit = async () => {
  if (!isValid.value) return
  saving.value = true
  try {
    emit('save', { ...form.value, title_font: form.value.font_family || form.value.title_font || 'serif' })
  } finally {
    saving.value = false
  }
}

watch(
  () => props.template,
  (value) => {
    if (!value) return
    form.value = {
      ...form.value,
      ...value,
      is_default: Boolean(value.is_default),
      template_content: value.template_content || form.value.template_content,
    }
  },
  { immediate: true }
)
</script>

<style scoped>
.label { display: block; font-size: .85rem; margin-bottom: .35rem; font-weight: 600; color: #374151; }
.field { width: 100%; border: 1px solid #d1d5db; border-radius: .75rem; padding: .6rem .8rem; }
.btn-primary { background: #8e0e3f; color: #fff; border: none; padding: .55rem 1rem; border-radius: .6rem; font-weight: 600; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.btn-secondary { background: #fff; border: 1px solid #d1d5db; color: #111827; padding: .55rem 1rem; border-radius: .6rem; font-weight: 600; }
</style>
