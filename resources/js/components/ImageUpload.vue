<template>
  <div>
    <label class="block text-sm font-medium mb-2">
      {{ label }}
    </label>
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-burgundy transition">
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        :multiple="multiple"
        @change="handleFileSelect"
        class="hidden"
      />
      
      <!-- Preview -->
      <div v-if="preview" class="mb-4">
        <img
          :src="preview"
          alt="Preview"
          class="max-h-48 mx-auto rounded"
        />
      </div>

      <!-- Upload Area -->
      <div>
        <svg
          class="mx-auto h-12 w-12 text-gray-400"
          stroke="currentColor"
          fill="none"
          viewBox="0 0 48 48"
        >
          <path
            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
        <p class="mt-2 text-sm text-gray-600">
          {{ placeholder }}
        </p>
        <button
          type="button"
          @click="$refs.fileInput.click()"
          class="mt-4 px-4 py-2 bg-burgundy text-white rounded hover:bg-[#6F112D]"
        >
          Choose {{ multiple ? 'Files' : 'File' }}
        </button>
        <p class="mt-2 text-xs text-gray-500">
          {{ hint }}
        </p>
      </div>

      <!-- Progress -->
      <div v-if="uploading" class="mt-4">
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div
            class="bg-burgundy h-2 rounded-full transition-all"
            :style="{ width: `${uploadProgress}%` }"
          ></div>
        </div>
        <p class="text-sm text-gray-600 mt-2">Uploading... {{ uploadProgress }}%</p>
      </div>

      <!-- Error -->
      <div v-if="error" class="mt-4 p-3 bg-red-100 text-red-700 rounded text-sm">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  label: {
    type: String,
    default: 'Upload Image',
  },
  placeholder: {
    type: String,
    default: 'Click to upload or drag and drop',
  },
  hint: {
    type: String,
    default: 'PNG, JPG up to 5MB',
  },
  accept: {
    type: String,
    default: 'image/*',
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  maxSize: {
    type: Number,
    default: 5 * 1024 * 1024, // 5MB
  },
});

const emit = defineEmits(['upload', 'error']);

const fileInput = ref(null);
const preview = ref(null);
const uploading = ref(false);
const uploadProgress = ref(0);
const error = ref('');

const handleFileSelect = (event) => {
  const files = event.target.files;
  if (!files || files.length === 0) return;

  error.value = '';
  
  // Validate file size
  for (let file of files) {
    if (file.size > props.maxSize) {
      error.value = `File ${file.name} exceeds maximum size of ${props.maxSize / (1024 * 1024)}MB`;
      return;
    }
  }

  // Create preview for single image
  if (!props.multiple && files[0].type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.value = e.target.result;
    };
    reader.readAsDataURL(files[0]);
  }

  // Emit files for parent to handle upload
  emit('upload', props.multiple ? Array.from(files) : files[0]);
};

// Simulate upload progress (parent should call this)
const startUpload = () => {
  uploading.value = true;
  uploadProgress.value = 0;
};

const updateProgress = (progress) => {
  uploadProgress.value = progress;
};

const completeUpload = () => {
  uploading.value = false;
  uploadProgress.value = 100;
};

const setError = (message) => {
  error.value = message;
  uploading.value = false;
};

defineExpose({
  startUpload,
  updateProgress,
  completeUpload,
  setError,
});
</script>
