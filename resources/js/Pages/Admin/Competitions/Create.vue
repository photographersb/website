<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Competition</h1>
            <p class="mt-1 text-sm text-gray-600">Set up a new photography competition</p>
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
                placeholder="Summer Photography Contest 2026"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Slug (optional)</label>
              <input
                v-model="form.slug"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="summer-photography-contest-2026"
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
                placeholder="Nature & Wildlife"
              />
              <p v-if="errors.theme" class="mt-1 text-sm text-red-600">{{ errors.theme[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
              <select
                v-model="form.category_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option :value="null">Select a category (optional)</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{ errors.category_id[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Detailed description of the competition..."
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
                v-model="form.submission_start"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.submission_start" class="mt-1 text-sm text-red-600">{{ errors.submission_start[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Submission Deadline *</label>
              <input
                v-model="form.submission_deadline"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.submission_deadline" class="mt-1 text-sm text-red-600">{{ errors.submission_deadline[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Voting Start Date *</label>
              <input
                v-model="form.voting_start"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.voting_start" class="mt-1 text-sm text-red-600">{{ errors.voting_start[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Voting End Date *</label>
              <input
                v-model="form.voting_end"
                type="datetime-local"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
              <p v-if="errors.voting_end" class="mt-1 text-sm text-red-600">{{ errors.voting_end[0] }}</p>
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
              <label class="block text-sm font-medium text-gray-700 mb-2">Total Prize Pool (৳)</label>
              <input
                v-model.number="form.total_prize_pool"
                type="number"
                min="0"
                step="100"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="50000"
              />
              <p v-if="errors.total_prize_pool" class="mt-1 text-sm text-red-600">{{ errors.total_prize_pool[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Max Submissions Per User</label>
              <input
                v-model.number="form.max_submissions_per_user"
                type="number"
                min="1"
                max="10"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="3"
              />
              <p class="mt-1 text-sm text-gray-500">Maximum 10 submissions per user</p>
              <p v-if="errors.max_submissions_per_user" class="mt-1 text-sm text-red-600">{{ errors.max_submissions_per_user }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Rules</label>
              <textarea
                v-model="form.rules"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Competition rules and guidelines..."
              ></textarea>
              <p v-if="errors.rules" class="mt-1 text-sm text-red-600">{{ errors.rules }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
              <textarea
                v-model="form.terms_and_conditions"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                placeholder="Terms and conditions..."
              ></textarea>
              <p v-if="errors.terms_and_conditions" class="mt-1 text-sm text-red-600">{{ errors.terms_and_conditions[0] }}</p>
            </div>
          </div>
        </div>

        <!-- Prizes Section -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Prizes</h2>
            <button
              type="button"
              @click="addPrize"
              class="inline-flex items-center px-4 py-2 bg-[#8B1538] text-white rounded-lg hover:bg-[#6F112D] transition-all text-sm font-semibold shadow-md"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Add Prize
            </button>
          </div>

          <div v-if="form.prizes.length === 0" class="text-center py-8 text-gray-500">
            <p>No prizes added yet. Click "Add Prize" to create prize tiers.</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="(prize, index) in form.prizes"
              :key="index"
              class="border border-gray-300 rounded-lg p-4"
            >
              <div class="flex items-start justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Prize {{ index + 1 }}</h3>
                <button
                  type="button"
                  @click="removePrize(index)"
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                >
                  Remove
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Rank *</label>
                  <input
                    v-model="prize.rank"
                    type="text"
                    required
                    placeholder="1st, 2nd, 3rd..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                  <input
                    v-model="prize.title"
                    type="text"
                    required
                    placeholder="Grand Prize, First Place..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Cash Amount (৳)</label>
                  <input
                    v-model.number="prize.cash_amount"
                    type="number"
                    min="0"
                    step="100"
                    placeholder="10000"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                  <input
                    v-model.number="prize.display_order"
                    type="number"
                    min="0"
                    placeholder="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea
                    v-model="prize.description"
                    rows="2"
                    placeholder="Describe this prize..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  ></textarea>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Physical Prizes</label>
                  <textarea
                    v-model="prize.physical_prizes"
                    rows="2"
                    placeholder="Trophy, Camera Gear, etc..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sponsors Section -->
        <div class="bg-white rounded-lg shadow-card p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Sponsors</h2>
            <button
              type="button"
              @click="addSponsor"
              class="inline-flex items-center px-4 py-2 bg-[#8B1538] text-white rounded-lg hover:bg-[#6F112D] transition-all text-sm font-semibold shadow-md"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Add Sponsor
            </button>
          </div>

          <div v-if="form.sponsors.length === 0" class="text-center py-8 text-gray-500">
            <p>No sponsors added yet. Click "Add Sponsor" to add competition sponsors.</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="(sponsor, index) in form.sponsors"
              :key="index"
              class="border border-gray-300 rounded-lg p-4"
            >
              <div class="flex items-start justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Sponsor {{ index + 1 }}</h3>
                <button
                  type="button"
                  @click="removeSponsor(index)"
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                >
                  Remove
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                  <input
                    v-model="sponsor.name"
                    type="text"
                    required
                    placeholder="Sponsor Name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Tier</label>
                  <select
                    v-model="sponsor.tier"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  >
                    <option value="platinum">Platinum</option>
                    <option value="gold">Gold</option>
                    <option value="silver">Silver</option>
                    <option value="bronze">Bronze</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Logo URL</label>
                  <input
                    v-model="sponsor.logo_url"
                    type="url"
                    placeholder="https://example.com/logo.png"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                  <input
                    v-model="sponsor.website_url"
                    type="url"
                    placeholder="https://example.com"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Contribution Amount (৳)</label>
                  <input
                    v-model.number="sponsor.contribution_amount"
                    type="number"
                    min="0"
                    step="100"
                    placeholder="5000"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                  <input
                    v-model.number="sponsor.display_order"
                    type="number"
                    min="0"
                    placeholder="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  />
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea
                    v-model="sponsor.description"
                    rows="2"
                    placeholder="Brief description about the sponsor..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                  ></textarea>
                </div>
              </div>
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
                v-model="form.is_featured"
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
            {{ processing ? 'Creating...' : 'Create Competition' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" :class="['toast', toastType]">{{ toastMessage }}</div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      processing: false,
      errors: {},
      categories: [],
      form: {
        title: '',
        slug: '',
        theme: '',
        description: '',
        category_id: null,
        submission_start: '',
        submission_deadline: '',
        voting_start: '',
        voting_end: '',
        announcement_date: '',
        total_prize_pool: null,
        max_submissions_per_user: 3,
        rules: '',
        terms_and_conditions: '',
        status: 'draft',
        is_featured: false,
        prizes: [],
        sponsors: [],
      },
      showToast: false,
      toastMessage: '',
      toastType: 'success'
    };
  },

  mounted() {
    this.fetchCategories();
  },

  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get('/api/v1/categories');
        if (response.data.status === 'success') {
          this.categories = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    addPrize() {
      this.form.prizes.push({
        rank: `${this.form.prizes.length + 1}${this.getOrdinalSuffix(this.form.prizes.length + 1)}`,
        title: '',
        description: '',
        cash_amount: null,
        physical_prizes: '',
        display_order: this.form.prizes.length,
      });
    },

    removePrize(index) {
      this.form.prizes.splice(index, 1);
    },

    addSponsor() {
      this.form.sponsors.push({
        name: '',
        tier: 'bronze',
        logo_url: '',
        website_url: '',
        description: '',
        contribution_amount: null,
        display_order: this.form.sponsors.length,
      });
    },

    removeSponsor(index) {
      this.form.sponsors.splice(index, 1);
    },

    getOrdinalSuffix(num) {
      const j = num % 10;
      const k = num % 100;
      if (j === 1 && k !== 11) return 'st';
      if (j === 2 && k !== 12) return 'nd';
      if (j === 3 && k !== 13) return 'rd';
      return 'th';
    },

    async submitForm() {
      this.processing = true;
      this.errors = {};

      try {
        const token = localStorage.getItem('auth_token');
        
        // Prepare form data
        const formData = {
          title: this.form.title,
          slug: this.form.slug || null,
          theme: this.form.theme,
          description: this.form.description,
          category_id: this.form.category_id,
          submission_start: this.form.submission_start,
          submission_deadline: this.form.submission_deadline,
          voting_start: this.form.voting_start,
          voting_end: this.form.voting_end,
          announcement_date: this.form.announcement_date,
          total_prize_pool: this.form.total_prize_pool || 0,
          max_submissions_per_user: this.form.max_submissions_per_user || 3,
          rules: this.form.rules,
          terms_and_conditions: this.form.terms_and_conditions,
          status: this.form.status,
          is_featured: this.form.is_featured ? 1 : 0,
          prizes: this.form.prizes,
          sponsors: this.form.sponsors,
        };

        const response = await axios.post('/admin/competitions', formData, {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });

        if (response.data.status === 'success') {
          this.showToastMessage('Competition created successfully!', 'success');
          
          // Redirect after short delay
          setTimeout(() => {
            window.location.href = '/admin/competitions';
          }, 1500);
        }
      } catch (error) {
        console.error('Error creating competition:', error);
        
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            this.errors = error.response.data.errors;
          }
          this.showToastMessage(error.response.data.message || 'Error creating competition', 'error');
        } else {
          this.showToastMessage('Error creating competition. Please try again.', 'error');
        }
      } finally {
        this.processing = false;
      }
    },

    showToastMessage(message, type = 'success') {
      this.toastMessage = message;
      this.toastType = type;
      this.showToast = true;
      setTimeout(() => {
        this.showToast = false;
      }, 3000);
    }
  },
};
</script>

<style scoped>
.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  animation: slideIn 0.3s ease-out;
  z-index: 1000;
  font-weight: 600;
}

.toast.success {
  background: #10b981;
  color: white;
}

.toast.error {
  background: #ef4444;
  color: white;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style>
