<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Competition</h1>
            <p class="mt-1 text-sm text-gray-600">Update competition details</p>
          </div>
          <a
            href="/admin/competitions"
            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
          </a>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
              <input
                v-model="form.title"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
              <input
                v-model="form.slug"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from title</p>
              <p v-if="errors.slug" class="mt-1 text-sm text-red-600">{{ errors.slug }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Theme *</label>
              <input
                v-model="form.theme"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.theme" class="mt-1 text-sm text-red-600">{{ errors.theme }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              ></textarea>
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
            </div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Competition Timeline</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Submission Start Date *</label>
              <input
                v-model="form.submission_start_date"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.submission_start_date" class="mt-1 text-sm text-red-600">{{ errors.submission_start_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Submission End Date *</label>
              <input
                v-model="form.submission_end_date"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.submission_end_date" class="mt-1 text-sm text-red-600">{{ errors.submission_end_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Voting Start Date *</label>
              <input
                v-model="form.voting_start_date"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.voting_start_date" class="mt-1 text-sm text-red-600">{{ errors.voting_start_date }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Voting End Date *</label>
              <input
                v-model="form.voting_end_date"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.voting_end_date" class="mt-1 text-sm text-red-600">{{ errors.voting_end_date }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Winner Announcement Date *</label>
              <input
                v-model="form.announcement_date"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.announcement_date" class="mt-1 text-sm text-red-600">{{ errors.announcement_date }}</p>
            </div>
          </div>
        </div>

        <!-- Competition Details -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Competition Details</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Prize Pool (৳)</label>
              <input
                v-model.number="form.prize_pool"
                type="number"
                min="0"
                step="100"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.prize_pool" class="mt-1 text-sm text-red-600">{{ errors.prize_pool }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Max Submissions Per User</label>
              <input
                v-model.number="form.max_submissions_per_user"
                type="number"
                min="1"
                max="10"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.max_submissions_per_user" class="mt-1 text-sm text-red-600">{{ errors.max_submissions_per_user }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Rules</label>
              <textarea
                v-model="form.rules"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              ></textarea>
              <p v-if="errors.rules" class="mt-1 text-sm text-red-600">{{ errors.rules }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
              <textarea
                v-model="form.terms"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              ></textarea>
              <p v-if="errors.terms" class="mt-1 text-sm text-red-600">{{ errors.terms }}</p>
            </div>
          </div>
        </div>

        <!-- Status & Settings -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Status & Settings</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
              <select
                v-model="form.status"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="draft">Draft (Not visible to public)</option>
                <option value="published">Published (Visible to public)</option>
                <option value="closed">Closed (No new submissions)</option>
              </select>
              <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
            </div>

            <div class="flex items-center">
              <input
                v-model="form.featured"
                type="checkbox"
                class="h-4 w-4 text-burgundy-600 focus:ring-burgundy-500 border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                Featured Competition (Show prominently on homepage)
              </label>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4">
          <a
            href="/admin/competitions"
            class="px-6 py-3 bg-white text-burgundy-600 border-2 border-burgundy-600 rounded-lg font-medium hover:bg-burgundy-50 transition-all"
          >
            Cancel
          </a>
          <button
            type="submit"
            :disabled="processing"
            class="px-6 py-3 bg-burgundy-600 text-white rounded-lg font-medium hover:bg-burgundy-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ processing ? 'Updating...' : 'Update Competition' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    competition: {
      type: Object,
      default: () => ({})
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      processing: false,
      form: {
        title: this.competition?.title || '',
        slug: this.competition?.slug || '',
        theme: this.competition?.theme || '',
        description: this.competition?.description || '',
        submission_start_date: this.formatDateForInput(this.competition?.submission_start_date),
        submission_end_date: this.formatDateForInput(this.competition?.submission_end_date),
        voting_start_date: this.formatDateForInput(this.competition?.voting_start_date),
        voting_end_date: this.formatDateForInput(this.competition?.voting_end_date),
        announcement_date: this.formatDateForInput(this.competition?.announcement_date),
        prize_pool: this.competition?.prize_pool || 0,
        max_submissions_per_user: this.competition?.max_submissions_per_user || 3,
        rules: this.competition?.rules || '',
        terms: this.competition?.terms || '',
        status: this.competition?.status || 'draft',
        featured: this.competition?.featured || false,
      },
    };
  },

  methods: {
    submitForm() {
      this.processing = true;
      // TODO: Implement API call to update competition
      console.log('Update competition:', this.form);
      setTimeout(() => {
        this.processing = false;
        alert('Competition update will be implemented with the API');
      }, 1000);
    },

    formatDateForInput(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${year}-${month}-${day}T${hours}:${minutes}`;
    },
  },
};
</script>
