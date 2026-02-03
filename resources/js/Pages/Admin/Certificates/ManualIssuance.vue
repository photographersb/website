<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Manual Certificate Issuance</h2>
        <p class="text-gray-600 mt-1">Issue certificates to photographers for competitions</p>
      </div>
      <router-link 
        to="/admin/certificates"
        class="px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition"
      >
        ← Back
      </router-link>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left: Selection & Form -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Step 1: Select Competition -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 1: Select Competition</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Competition</label>
              <select 
                v-model="selectedCompetitionId"
                @change="loadSubmissions"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              >
                <option value="">Select a competition...</option>
                <option 
                  v-for="comp in competitions" 
                  :key="comp.id"
                  :value="comp.id"
                >
                  {{ comp.title }} ({{ comp.status }})
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Step 2: Select Submission -->
        <div v-if="selectedCompetitionId" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 2: Select Photographer/Submission</h3>
          
          <div v-if="loadingSubmissions" class="text-center py-8 text-gray-500">
            <p>Loading submissions...</p>
          </div>

          <div v-else-if="availableSubmissions.length === 0" class="text-center py-8 text-gray-500">
            <p>No submissions available for certificate issuance</p>
          </div>

          <div v-else class="space-y-3 max-h-96 overflow-y-auto">
            <div 
              v-for="submission in availableSubmissions"
              :key="submission.id"
              @click="selectSubmission(submission)"
              :class="[
                'p-4 border rounded-lg cursor-pointer transition',
                selectedSubmission?.id === submission.id
                  ? 'border-orange-500 bg-orange-50'
                  : 'border-gray-200 hover:border-orange-300'
              ]"
            >
              <div class="flex items-start gap-4">
                <img 
                  :src="submission.photo_url" 
                  :alt="submission.title"
                  class="w-16 h-16 rounded object-cover"
                />
                <div class="flex-1">
                  <h4 class="font-semibold text-gray-900">{{ submission.photographer_name }}</h4>
                  <p class="text-sm text-gray-600">{{ submission.title }}</p>
                  <div class="mt-2 flex items-center gap-2">
                    <span v-if="submission.certificate_id" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                      ✓ Already has certificate
                    </span>
                    <span v-if="submission.winner_position" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                      {{ getPositionText(submission.winner_position) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 3: Certificate Details -->
        <div v-if="selectedSubmission" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 3: Configure Certificate</h3>
          
          <div class="space-y-4">
            <!-- Photographer Info -->
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-600">Photographer</p>
              <p class="text-lg font-semibold text-gray-900">{{ selectedSubmission.photographer_name }}</p>
            </div>

            <!-- Certificate Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Certificate Type</label>
              <select 
                v-model="certificateForm.type"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              >
                <option value="participation">Participation Certificate</option>
                <option value="winner">Winner Certificate</option>
                <option value="finalist">Finalist Certificate</option>
                <option value="merit">Merit Certificate</option>
              </select>
            </div>

            <!-- Position (for winner certificates) -->
            <div v-if="certificateForm.type === 'winner'">
              <label class="block text-sm font-medium text-gray-700 mb-2">Prize Position</label>
              <select 
                v-model="certificateForm.position"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              >
                <option value="1st">1st Place</option>
                <option value="2nd">2nd Place</option>
                <option value="3rd">3rd Place</option>
              </select>
            </div>

            <!-- Issue Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Certificate Issue Date</label>
              <input 
                v-model="certificateForm.issueDate"
                type="date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              />
            </div>

            <!-- Admin Notes -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
              <textarea 
                v-model="certificateForm.notes"
                rows="3"
                placeholder="Any special notes or reasons for this certificate..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
              ></textarea>
            </div>

            <!-- Preview & Send Email -->
            <div class="space-y-3 pt-4 border-t">
              <label class="flex items-center gap-2 cursor-pointer">
                <input 
                  v-model="certificateForm.sendEmail"
                  type="checkbox"
                  class="rounded"
                />
                <span class="text-sm font-medium text-gray-700">Send certificate email to photographer</span>
              </label>

              <div v-if="certificateForm.sendEmail" class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                <p>The photographer will receive an email with:</p>
                <ul class="list-disc list-inside mt-1 space-y-1">
                  <li>Certificate details</li>
                  <li>Download link</li>
                  <li>Achievement badge</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div v-if="selectedSubmission" class="flex gap-3">
          <button
            @click="previewCertificate"
            class="flex-1 px-4 py-3 border-2 border-orange-500 text-orange-600 rounded-lg hover:bg-orange-50 transition font-medium"
          >
            Preview Certificate
          </button>
          <button
            @click="issueCertificate"
            :disabled="issuingCertificate"
            class="flex-1 px-4 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition font-medium flex items-center justify-center gap-2"
          >
            <svg v-if="!issuingCertificate" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span v-if="issuingCertificate">Issuing...</span>
            <span v-else>Issue Certificate</span>
          </button>
        </div>
      </div>

      <!-- Right: Preview Panel -->
      <div class="lg:col-span-1">
        <div class="sticky top-6 bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview</h3>
          
          <div v-if="!selectedSubmission" class="text-center py-12 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <p>Select a submission to preview</p>
          </div>

          <div v-else class="space-y-4">
            <!-- Certificate Preview -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50 aspect-video flex items-center justify-center">
              <div class="text-center">
                <p class="text-2xl font-serif font-bold text-orange-600 mb-2">Certificate</p>
                <p class="text-sm text-gray-600">of {{ certificateForm.type === 'winner' ? 'Achievement' : 'Participation' }}</p>
                <p class="text-xs text-gray-500 mt-3">{{ selectedSubmission.photographer_name }}</p>
              </div>
            </div>

            <!-- Details -->
            <div class="space-y-3 text-sm">
              <div>
                <p class="text-gray-600">Type</p>
                <p class="font-semibold text-gray-900">{{ getCertificateTypeLabel(certificateForm.type) }}</p>
              </div>

              <div v-if="certificateForm.position">
                <p class="text-gray-600">Prize Position</p>
                <p class="font-semibold text-gray-900">{{ certificateForm.position }}</p>
              </div>

              <div>
                <p class="text-gray-600">Issue Date</p>
                <p class="font-semibold text-gray-900">{{ formatDate(certificateForm.issueDate) }}</p>
              </div>

              <div class="pt-3 border-t">
                <p class="text-xs text-gray-500">Certificate ID will be generated upon issuance</p>
              </div>
            </div>

            <!-- Submission Details -->
            <div class="pt-4 border-t space-y-2 text-sm">
              <div>
                <p class="text-gray-600">Photographer</p>
                <p class="font-semibold text-gray-900">{{ selectedSubmission.photographer_name }}</p>
              </div>
              <div>
                <p class="text-gray-600">Submission</p>
                <p class="font-semibold text-gray-900">{{ selectedSubmission.title }}</p>
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
        'fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white transition z-50',
        toastType === 'success' ? 'bg-green-600' : 'bg-red-600'
      ]"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../../api';

const competitions = ref([]);
const selectedCompetitionId = ref('');
const submissionsList = ref([]);
const loadingSubmissions = ref(false);
const selectedSubmission = ref(null);
const issuingCertificate = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const certificateForm = ref({
  type: 'participation',
  position: '',
  issueDate: new Date().toISOString().split('T')[0],
  notes: '',
  sendEmail: true
});

const availableSubmissions = computed(() => {
  return submissionsList.value.filter(s => !s.certificate_id);
});

const loadCompetitions = async () => {
  try {
    const { data } = await api.get('/api/v1/admin/competitions?status=closed&per_page=100');
    if (data.status === 'success') {
      competitions.value = data.data;
    }
  } catch (error) {
    console.error('Error loading competitions:', error);
    showToast('Failed to load competitions', 'error');
  }
};

const loadSubmissions = async () => {
  if (!selectedCompetitionId.value) {
    submissionsList.value = [];
    selectedSubmission.value = null;
    return;
  }

  loadingSubmissions.value = true;
  try {
    const { data } = await api.get(
      `/api/v1/admin/competitions/${selectedCompetitionId.value}/submissions?per_page=100`
    );
    if (data.status === 'success') {
      submissionsList.value = data.data.map(s => ({
        ...s,
        photographer_name: s.photographer?.photographer?.user?.name || 'Unknown',
        photo_url: s.image_url
      }));
      selectedSubmission.value = null;
    }
  } catch (error) {
    console.error('Error loading submissions:', error);
    showToast('Failed to load submissions', 'error');
  } finally {
    loadingSubmissions.value = false;
  }
};

const selectSubmission = (submission) => {
  selectedSubmission.value = submission;
  // Auto-select position if it's a winner
  if (submission.winner_position) {
    certificateForm.value.position = submission.winner_position;
    certificateForm.value.type = 'winner';
  }
};

const previewCertificate = () => {
  // In a real implementation, this would open a PDF preview modal
  showToast('Certificate preview functionality coming soon', 'success');
};

const issueCertificate = async () => {
  if (!selectedSubmission.value) return;

  issuingCertificate.value = true;
  try {
    const payload = {
      submission_id: selectedSubmission.value.id,
      certificate_type: certificateForm.value.type,
      position: certificateForm.value.position || null,
      issue_date: certificateForm.value.issueDate,
      admin_notes: certificateForm.value.notes,
      send_email: certificateForm.value.sendEmail
    };

    const { data } = await api.post(
      `/api/v1/admin/competitions/${selectedCompetitionId.value}/issue-certificate`,
      payload
    );

    if (data.status === 'success') {
      showToast('Certificate issued successfully!', 'success');
      
      // Remove from available list
      submissionsList.value = submissionsList.value.map(s => 
        s.id === selectedSubmission.value.id 
          ? { ...s, certificate_id: data.data.certificate_id }
          : s
      );
      
      // Reset form
      selectedSubmission.value = null;
      certificateForm.value = {
        type: 'participation',
        position: '',
        issueDate: new Date().toISOString().split('T')[0],
        notes: '',
        sendEmail: true
      };
    }
  } catch (error) {
    console.error('Error issuing certificate:', error);
    showToast('Failed to issue certificate. Please try again.', 'error');
  } finally {
    issuingCertificate.value = false;
  }
};

const showToast = (message, type) => {
  toastMessage.value = message;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getCertificateTypeLabel = (type) => {
  const labels = {
    participation: 'Participation Certificate',
    winner: 'Winner Certificate',
    finalist: 'Finalist Certificate',
    merit: 'Merit Certificate'
  };
  return labels[type] || type;
};

const getPositionText = (position) => {
  const positions = {
    '1st': '🥇 1st Place',
    '2nd': '🥈 2nd Place',
    '3rd': '🥉 3rd Place'
  };
  return positions[position] || position;
};

onMounted(() => {
  loadCompetitions();
});
</script>

<style scoped>
/* Smooth transitions */
</style>
