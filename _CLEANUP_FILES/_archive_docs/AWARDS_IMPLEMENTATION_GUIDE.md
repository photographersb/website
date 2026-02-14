# Awards & Achievements Implementation Guide

## Where to Add: `resources/js/components/PhotographerDashboard.vue`

### Step 1: Add Tab Button (Line ~127, after Events button)

```vue
<button
  @click="activeTab = 'awards'"
  :class="`pb-2 font-medium whitespace-nowrap text-sm sm:text-base min-h-[44px] px-2 ${activeTab === 'awards' ? 'text-burgundy border-b-2 border-burgundy' : 'text-gray-600'}`"
>
  🏆 Awards
</button>
```

### Step 2: Add Tab Content (Line ~903, after Events section closes)

Add this complete section:

```vue
<!-- Awards Tab -->
<div v-if="activeTab === 'awards'" class="p-4 sm:p-6">
  <div class="mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold">Awards & Achievements</h2>
        <p class="text-sm text-gray-600 mt-1">Showcase your awards, certifications, and achievements</p>
      </div>
      <button
        @click="showAddAwardModal = true"
        class="px-4 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Award
      </button>
    </div>
  </div>

  <!-- Loading State -->
  <div v-if="loadingAwards" class="text-center py-12">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-burgundy"></div>
    <p class="text-sm text-gray-600 mt-2">Loading awards...</p>
  </div>

  <!-- Empty State -->
  <div v-else-if="awards.length === 0" class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed">
    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
    </svg>
    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Awards Yet</h3>
    <p class="text-sm text-gray-600 mb-4">Start building your credibility by adding your awards and achievements</p>
    <button
      @click="showAddAwardModal = true"
      class="px-6 py-2 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark transition-colors"
    >
      Add Your First Award
    </button>
  </div>

  <!-- Awards List -->
  <div v-else class="space-y-4">
    <div
      v-for="award in awards"
      :key="award.id"
      class="bg-white border rounded-lg p-4 hover:shadow-md transition-shadow"
    >
      <div class="flex items-start gap-4">
        <!-- Award Icon -->
        <div class="flex-shrink-0">
          <div :class="`w-12 h-12 rounded-full flex items-center justify-center ${getAwardColor(award.type)}`">
            <span class="text-2xl">{{ getAwardIcon(award.type) }}</span>
          </div>
        </div>

        <!-- Award Content -->
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <div class="flex-1">
              <h3 class="font-semibold text-lg text-gray-900">{{ award.title }}</h3>
              <p v-if="award.organization" class="text-sm text-gray-600 mt-1">{{ award.organization }}</p>
            </div>
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 bg-burgundy/10 text-burgundy rounded-full text-sm font-medium">
                {{ award.year }}
              </span>
              <span :class="`px-3 py-1 rounded-full text-xs font-medium ${getTypeBadgeColor(award.type)}`">
                {{ award.type }}
              </span>
            </div>
          </div>

          <p v-if="award.description" class="text-sm text-gray-600 mt-2">{{ award.description }}</p>

          <!-- Certificate Link -->
          <a
            v-if="award.certificate_url"
            :href="award.certificate_url"
            target="_blank"
            class="inline-flex items-center gap-1 text-sm text-burgundy hover:underline mt-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            View Certificate
          </a>

          <!-- Actions -->
          <div class="flex gap-2 mt-3">
            <button
              @click="editAward(award)"
              class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
            >
              Edit
            </button>
            <button
              @click="deleteAward(award.id)"
              class="px-3 py-1 text-sm bg-red-50 text-red-600 rounded hover:bg-red-100"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
```

### Step 3: Add Modal for Create/Edit Award

Add after the awards list section:

```vue
<!-- Add/Edit Award Modal -->
<teleport to="body">
  <div
    v-if="showAddAwardModal"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    @click.self="closeAwardModal"
  >
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
        <h3 class="text-xl font-bold">{{ editingAward ? 'Edit Award' : 'Add New Award' }}</h3>
        <button @click="closeAwardModal" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="saveAward" class="p-6 space-y-4">
        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Award Title *
          </label>
          <input
            v-model="awardForm.title"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
            placeholder="Best Wedding Photographer 2024"
          />
          <p v-if="awardErrors.title" class="text-sm text-red-600 mt-1">{{ awardErrors.title[0] }}</p>
        </div>

        <!-- Organization -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Organization / Issuer
          </label>
          <input
            v-model="awardForm.organization"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
            placeholder="Bangladesh Photography Association"
          />
        </div>

        <!-- Year and Type -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Year *
            </label>
            <input
              v-model.number="awardForm.year"
              type="number"
              required
              min="1950"
              :max="currentYear + 1"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
            />
            <p v-if="awardErrors.year" class="text-sm text-red-600 mt-1">{{ awardErrors.year[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Type *
            </label>
            <select
              v-model="awardForm.type"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
            >
              <option value="award">🏆 Award</option>
              <option value="achievement">⭐ Achievement</option>
              <option value="recognition">🎖️ Recognition</option>
              <option value="certification">📜 Certification</option>
            </select>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Description
          </label>
          <textarea
            v-model="awardForm.description"
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
            placeholder="Brief description of the award and what you achieved..."
          ></textarea>
        </div>

        <!-- Certificate Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Certificate (JPG, PNG, or PDF - Max 5MB)
          </label>
          <input
            type="file"
            @change="handleCertificateUpload"
            accept="image/jpeg,image/jpg,image/png,application/pdf"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
          />
          <p v-if="awardErrors.certificate_file" class="text-sm text-red-600 mt-1">{{ awardErrors.certificate_file[0] }}</p>
          <p v-if="editingAward && editingAward.certificate_url" class="text-sm text-gray-600 mt-1">
            Current certificate: 
            <a :href="editingAward.certificate_url" target="_blank" class="text-burgundy hover:underline">View</a>
          </p>
        </div>

        <!-- Error Message -->
        <div v-if="awardErrorMessage" class="p-3 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-sm text-red-600">{{ awardErrorMessage }}</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4">
          <button
            type="submit"
            :disabled="savingAward"
            class="flex-1 px-6 py-3 bg-burgundy text-white rounded-lg hover:bg-burgundy-dark disabled:opacity-50 disabled:cursor-not-allowed font-medium"
          >
            {{ savingAward ? 'Saving...' : (editingAward ? 'Update Award' : 'Add Award') }}
          </button>
          <button
            type="button"
            @click="closeAwardModal"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</teleport>
```

### Step 4: Add JavaScript Functions (in `<script setup>` section)

Add these reactive variables and functions to your script section:

```javascript
// Awards data
const awards = ref([]);
const loadingAwards = ref(false);
const showAddAwardModal = ref(false);
const editingAward = ref(null);
const savingAward = ref(false);
const awardErrors = ref({});
const awardErrorMessage = ref('');
const currentYear = new Date().getFullYear();

const awardForm = ref({
  title: '',
  organization: '',
  year: currentYear,
  type: 'award',
  description: '',
});

const certificateFile = ref(null);

// Fetch awards
async function fetchAwards() {
  try {
    loadingAwards.value = true;
    const response = await api.get('/photographer/awards');
    awards.value = response.data.data;
  } catch (error) {
    console.error('Failed to fetch awards:', error);
  } finally {
    loadingAwards.value = false;
  }
}

// Award type styling
function getAwardIcon(type) {
  const icons = {
    award: '🏆',
    achievement: '⭐',
    recognition: '🎖️',
    certification: '📜'
  };
  return icons[type] || '🏆';
}

function getAwardColor(type) {
  const colors = {
    award: 'bg-yellow-100',
    achievement: 'bg-blue-100',
    recognition: 'bg-purple-100',
    certification: 'bg-green-100'
  };
  return colors[type] || 'bg-gray-100';
}

function getTypeBadgeColor(type) {
  const colors = {
    award: 'bg-yellow-100 text-yellow-800',
    achievement: 'bg-blue-100 text-blue-800',
    recognition: 'bg-purple-100 text-purple-800',
    certification: 'bg-green-100 text-green-800'
  };
  return colors[type] || 'bg-gray-100 text-gray-800';
}

// Handle certificate file upload
function handleCertificateUpload(event) {
  const file = event.target.files[0];
  if (file) {
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      awardErrors.value.certificate_file = ['Certificate file must not exceed 5MB'];
      event.target.value = '';
      return;
    }
    certificateFile.value = file;
    delete awardErrors.value.certificate_file;
  }
}

// Edit award
function editAward(award) {
  editingAward.value = award;
  awardForm.value = {
    title: award.title,
    organization: award.organization || '',
    year: award.year,
    type: award.type,
    description: award.description || '',
  };
  certificateFile.value = null;
  showAddAwardModal.value = true;
}

// Save award (create or update)
async function saveAward() {
  try {
    savingAward.value = true;
    awardErrors.value = {};
    awardErrorMessage.value = '';

    const formData = new FormData();
    Object.keys(awardForm.value).forEach(key => {
      if (awardForm.value[key]) {
        formData.append(key, awardForm.value[key]);
      }
    });

    if (certificateFile.value) {
      formData.append('certificate_file', certificateFile.value);
    }

    if (editingAward.value) {
      // Update existing award
      await api.post(`/photographer/awards/${editingAward.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
        params: { _method: 'PUT' }
      });
    } else {
      // Create new award
      await api.post('/photographer/awards', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }

    // Success
    closeAwardModal();
    fetchAwards();
    alert(editingAward.value ? 'Award updated successfully!' : 'Award added successfully!');
  } catch (error) {
    console.error('Failed to save award:', error);
    if (error.response?.status === 422) {
      awardErrors.value = error.response.data.errors || {};
      awardErrorMessage.value = error.response.data.message;
    } else {
      awardErrorMessage.value = 'Failed to save award. Please try again.';
    }
  } finally {
    savingAward.value = false;
  }
}

// Delete award
async function deleteAward(awardId) {
  if (!confirm('Are you sure you want to delete this award?')) {
    return;
  }

  try {
    await api.delete(`/photographer/awards/${awardId}`);
    awards.value = awards.value.filter(a => a.id !== awardId);
    alert('Award deleted successfully!');
  } catch (error) {
    console.error('Failed to delete award:', error);
    alert('Failed to delete award. Please try again.');
  }
}

// Close modal
function closeAwardModal() {
  showAddAwardModal.value = false;
  editingAward.value = null;
  awardForm.value = {
    title: '',
    organization: '',
    year: currentYear,
    type: 'award',
    description: '',
  };
  certificateFile.value = null;
  awardErrors.value = {};
  awardErrorMessage.value = '';
}

// Watch for activeTab changes to fetch awards when tab is opened
watch(activeTab, (newTab) => {
  if (newTab === 'awards' && awards.value.length === 0) {
    fetchAwards();
  }
});
```

### Step 5: Update the Quick Links Section (Optional)

You can also add an Awards quick link in the "Quick Links" section (around line 910):

```vue
<button @click="activeTab = 'awards'" class="flex flex-col items-center p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 active:bg-red-100 transition-all group min-h-[88px] sm:min-h-0">
  <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 group-hover:text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
  </svg>
  <span class="text-xs sm:text-sm font-medium text-center leading-tight">Awards</span>
</button>
```

## Summary

Your Awards section will have:
- ✅ List all awards with icons and styling by type
- ✅ Add new awards with form validation
- ✅ Edit existing awards
- ✅ Delete awards with confirmation
- ✅ Upload certificates (JPG, PNG, PDF)
- ✅ Beautiful UI matching your existing design
- ✅ Mobile responsive
- ✅ Error handling
- ✅ Loading states
- ✅ Empty states

The API is already working, so once you add this frontend code, everything will be functional!
