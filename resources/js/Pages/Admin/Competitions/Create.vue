<template>
  <div class="min-h-screen bg-gray-50">
    <AdminHeader
      title="Create Competition"
      subtitle="Set up a new photography competition"
    />

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
      <AdminQuickNav />

      <!-- Authorization Error Alert -->
      <div v-if="errors.auth" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-red-50 border border-red-300 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <div class="text-red-600 text-lg font-bold">⚠️</div>
            <div>
              <h3 class="text-red-900 font-semibold">Authorization Error</h3>
              <p class="text-red-700 text-sm mt-1">{{ errors.auth }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form @submit.prevent="submitForm" class="space-y-6">
          
          <!-- Basic Information -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input
                  v-model="form.title"
                  type="text"
                  placeholder="e.g., Spring Photography Contest 2026"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <span v-if="errors.title" class="text-red-600 text-sm">{{ errors.title }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input
                  v-model="form.slug"
                  type="text"
                  placeholder="Auto-generated if left blank"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <span v-if="errors.slug" class="text-red-600 text-sm">{{ errors.slug }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Theme *</label>
                <input
                  v-model="form.theme"
                  type="text"
                  placeholder="e.g., Nature & Wildlife"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <span v-if="errors.theme" class="text-red-600 text-sm">{{ errors.theme }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select
                  v-model="form.category_id"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                >
                  <option value="">-- Select Category --</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea
                  v-model="form.description"
                  placeholder="Tell photographers about this competition..."
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                ></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Image URL</label>
                <input
                  v-model="form.hero_image"
                  type="url"
                  placeholder="https://..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <span v-if="errors.hero_image" class="text-red-600 text-sm">{{ errors.hero_image }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image URL</label>
                <input
                  v-model="form.banner_image"
                  type="url"
                  placeholder="https://..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
                <span v-if="errors.banner_image" class="text-red-600 text-sm">{{ errors.banner_image }}</span>
              </div>
            </div>
          </div>

          <!-- Timeline -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Timeline</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Submission Deadline *</label>
                <input
                  v-model="form.submission_deadline"
                  type="datetime-local"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voting Start *</label>
                <input
                  v-model="form.voting_start_at"
                  type="datetime-local"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voting End *</label>
                <input
                  v-model="form.voting_end_at"
                  type="datetime-local"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judging Start</label>
                <input
                  v-model="form.judging_start_at"
                  type="datetime-local"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judging End</label>
                <input
                  v-model="form.judging_end_at"
                  type="datetime-local"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Results Announcement *</label>
                <input
                  v-model="form.results_announcement_date"
                  type="datetime-local"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>
            </div>
          </div>

          <!-- Details -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
              Details
              <span class="text-xs text-gray-400 ml-2">(v2.0 - Updated Feb 2)</span>
            </h2>

            <!-- Prize Pool Section -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-3">Total Prize Pool *</label>
              
              <!-- Input & Button Row -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <!-- Prize Pool Input -->
                <div class="md:col-span-2">
                  <div class="flex gap-2">
                    <input
                      v-model.number="form.total_prize_pool"
                      type="number"
                      min="0"
                      required
                      :class="[
                        'flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent transition',
                        errors.total_prize_pool
                          ? 'border-red-300 bg-red-50'
                          : 'border-gray-300 bg-white'
                      ]"
                    />
                    <button
                      v-if="cashPrizeTotal > 0"
                      type="button"
                      @click="form.total_prize_pool = cashPrizeTotal"
                      title="Sum all cash prizes below and set total prize pool"
                      class="px-4 py-2 bg-burgundy text-white text-sm rounded-lg hover:bg-burgundy-dark transition-all font-medium whitespace-nowrap flex items-center gap-2"
                    >
                      <span>⚡</span>
                      <span>Calculate</span>
                    </button>
                  </div>
                </div>

                <!-- Cash Total Info Card -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex flex-col justify-center">
                  <div class="text-xs text-blue-600 font-medium mb-1">Cash Prizes Total</div>
                  <div class="text-2xl font-bold text-blue-900">
                    ৳ {{ formatCurrency(cashPrizeTotal) }}
                  </div>
                </div>
              </div>

              <!-- Match Status Badge -->
              <div v-if="form.total_prize_pool > 0 || errors.total_prize_pool" class="mb-3">
                <div v-if="form.total_prize_pool === cashPrizeTotal" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 border border-emerald-300 rounded-lg">
                  <span class="text-emerald-700 text-sm font-medium">✅ Total matches cash prizes</span>
                </div>
                <div v-else-if="errors.total_prize_pool" class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 border border-red-300 rounded-lg">
                  <span class="text-red-700 text-sm font-medium">⚠️ Mismatch - Check prizes</span>
                </div>
              </div>

              <!-- Error Message -->
              <p v-if="errors.total_prize_pool" class="mt-2 text-sm text-red-600 font-medium">
                {{ errors.total_prize_pool }}
              </p>
              <p v-else class="mt-2 text-xs text-gray-500">
                Enter the total prize pool amount. Use Calculate to auto-fill from cash prizes below.
              </p>
            </div>

            <div class="mb-6">
              <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700">Prizes</label>
                <button
                  type="button"
                  @click="addPrize"
                  class="px-3 py-1 bg-burgundy text-white text-sm rounded-lg hover:bg-burgundy-dark transition-all font-medium"
                >
                  + Add Prize
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="(prize, index) in form.prizes"
                  :key="index"
                  class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm"
                >
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700"
                      >
                        🏆
                      </span>
                      <span class="font-semibold text-gray-900">{{ formatPrizeLabel(prize) }}</span>
                    </div>
                    <button
                      type="button"
                      @click="removePrize(index)"
                      class="text-sm text-red-600 hover:text-red-700"
                    >
                      Remove
                    </button>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">Rank</label>
                      <select
                        v-model="prize.rank"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      >
                        <option v-for="rank in prizeRanks" :key="rank" :value="rank">{{ rank }}</option>
                      </select>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">Reward Type</label>
                      <select
                        v-model="prize.type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      >
                        <option v-for="type in prizeTypes" :key="type" :value="type">{{ type }}</option>
                      </select>
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-xs font-medium text-gray-600 mb-1">Amount (must be > 1)</label>
                      <input
                        v-model.number="prize.amount"
                        type="number"
                        min="2"
                        step="1"
                        :disabled="prize.type !== 'Cash'"
                        placeholder="Cash amount"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      />
                    </div>
                    <div class="md:col-span-2" v-if="prize.type !== 'Cash'">
                      <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                      <input
                        v-model="prize.description"
                        type="text"
                        placeholder="e.g., Certificate, Gift Box, Trophy"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent text-sm"
                      />
                    </div>
                  </div>
                </div>

                <div v-if="form.prizes.length === 0" class="text-sm text-gray-500 text-center py-4 md:col-span-2">
                  No prizes added yet. Click "Add Prize" to create prize entries.
                </div>
              </div>

              <p v-if="errors.prizes" class="mt-2 text-sm text-red-600">{{ errors.prizes }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Winners *</label>
                <input
                  v-model.number="form.number_of_winners"
                  type="number"
                  min="1"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Submissions Per User</label>
                <input
                  v-model.number="form.max_submissions_per_user"
                  type="number"
                  min="1"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>
            </div>

            <div class="mt-4">
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Rules</label>
                <button
                  type="button"
                  @click="form.rules = defaultRules"
                  title="Fill with universal competition rules"
                  class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition"
                >
                  Use Default
                </button>
              </div>
              <textarea
                v-model="form.rules"
                placeholder="Enter competition rules..."
                rows="5"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              ></textarea>
              <p class="mt-1 text-xs text-gray-500">Tip: Click "Use Default" to populate universal rules, then customize as needed.</p>
            </div>

            <div class="mt-4">
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Terms & Conditions</label>
                <button
                  type="button"
                  @click="form.terms_and_conditions = defaultTerms"
                  title="Fill with universal terms and conditions"
                  class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition"
                >
                  Use Default
                </button>
              </div>
              <textarea
                v-model="form.terms_and_conditions"
                placeholder="Enter terms and conditions..."
                rows="5"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              ></textarea>
              <p class="mt-1 text-xs text-gray-500">Tip: Click "Use Default" to populate standard terms, then customize as needed.</p>
            </div>
          </div>

          <!-- Sponsors -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Sponsors</h2>

            <div class="mb-4">
              <input
                v-model="sponsorSearch"
                type="text"
                placeholder="Search sponsors..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
            </div>

            <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4 space-y-3">
              <div v-if="filteredSponsors.length === 0" class="text-gray-500 text-center py-8">
                No sponsors available
              </div>
              <label
                v-for="sponsor in filteredSponsors"
                :key="sponsor.id"
                class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer"
              >
                <input
                  type="checkbox"
                  :value="sponsor.id"
                  v-model="form.sponsor_ids"
                  class="h-4 w-4 rounded border-gray-300"
                />
                <span class="ml-3 font-medium">{{ sponsor.name }}</span>
              </label>
            </div>

            <p class="mt-2 text-sm text-gray-600">Selected: {{ form.sponsor_ids.length }}</p>
          </div>

          <!-- Judges -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Judges</h2>

            <div class="mb-4">
              <input
                v-model="judgeSearch"
                type="text"
                placeholder="Search judges..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              />
            </div>

            <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4 space-y-3">
              <div v-if="filteredJudges.length === 0" class="text-gray-500 text-center py-8">
                No judges available
              </div>
              <label
                v-for="judge in filteredJudges"
                :key="judge.id"
                class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer"
              >
                <input
                  type="checkbox"
                  :value="judge.id"
                  v-model="form.judge_ids"
                  class="h-4 w-4 rounded border-gray-300"
                />
                <span class="ml-3 font-medium">{{ judge.name }}</span>
              </label>
            </div>

            <p class="mt-2 text-sm text-gray-600">Selected: {{ form.judge_ids.length }}</p>
          </div>

          <!-- Status -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Status</h2>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                v-model="form.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
              >
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="judging">Judging</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="archived">Archived</option>
              </select>
            </div>

            <div class="mt-4">
              <label class="flex items-center">
                <input
                  v-model="form.is_featured"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">Featured Competition</span>
              </label>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="flex items-center">
                  <input
                    v-model="form.is_paid_competition"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300"
                  />
                  <span class="ml-2 text-sm font-medium text-gray-700">Paid Competition</span>
                </label>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Participation Fee</label>
                <input
                  v-model.number="form.participation_fee"
                  type="number"
                  min="0"
                  :disabled="!form.is_paid_competition"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy-500 focus:border-transparent"
                />
              </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
              <label class="flex items-center">
                <input
                  v-model="form.allow_public_voting"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">Allow Public Voting</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.allow_judge_scoring"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">Allow Judge Scoring</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.allow_watermark"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">Allow Watermark</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.require_watermark"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">Require Watermark</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="form.is_public"
                  type="checkbox"
                  class="h-4 w-4 rounded border-gray-300"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">Public Competition</span>
              </label>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end gap-4">
            <router-link
              to="/admin/competitions"
              class="px-6 py-2 border-2 border-burgundy text-burgundy rounded-lg font-medium hover:bg-gray-50 transition"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="loading"
              class="px-6 py-2 bg-burgundy text-white rounded-lg font-medium hover:bg-burgundy-dark disabled:opacity-50 transition"
            >
              {{ loading ? 'Creating...' : 'Create Competition' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import AdminHeader from '../../../components/AdminHeader.vue'
import AdminQuickNav from '../../../components/AdminQuickNav.vue'

export default {
  components: {
    AdminHeader,
    AdminQuickNav
  },

  data() {
    return {
      loading: false,
      errors: {},
      categories: [],
      judges: [],
      sponsors: [],
      sponsorSearch: '',
      judgeSearch: '',
      form: {
        title: '',
        slug: '',
        theme: '',
        description: '',
        category_id: '',
        submission_deadline: '',
        voting_start_at: '',
        voting_end_at: '',
        judging_start_at: '',
        judging_end_at: '',
        results_announcement_date: '',
        hero_image: '',
        banner_image: '',
        total_prize_pool: 2,
        number_of_winners: 1,
        max_submissions_per_user: 3,
        prizes: [{ rank: '1st', type: 'Cash', amount: 2, description: '' }],
        rules: '',
        terms_and_conditions: '',
        status: 'draft',
        is_public: true,
        is_featured: false,
        is_paid_competition: false,
        participation_fee: 0,
        allow_public_voting: true,
        allow_judge_scoring: true,
        allow_watermark: false,
        require_watermark: false,
        sponsor_ids: [],
        judge_ids: []
      },
      prizeRanks: ['1st', '2nd', '3rd', '4th', '5th', '6th', 'Grand Prize', 'Honorable Mention'],
      prizeTypes: ['Cash', 'Certificate', 'Gift Box', 'Trophy', 'Other'],
      defaultRules: `1. Eligibility
- Participants must be professional photographers or photography enthusiasts
- Participants must be 18 years or older
- Employees and family members of the organizing committee are not eligible

2. Submission Requirements
- All submissions must be original works by the participant
- Each submission must include a title and brief description
- Photos must be in JPEG or PNG format, minimum 2000x2000px resolution
- Maximum file size: 50 MB per image

3. Content Guidelines
- Submissions must not contain watermarks or signatures
- No explicitly offensive, discriminatory, or inappropriate content
- All content must comply with local and international copyright laws
- No digitally manipulated images (minor editing allowed)

4. Ownership & Usage Rights
- Participants retain full copyright of their work
- By submitting, you grant us permission to display entries during judging
- Winning entries may be used for promotional purposes with proper attribution
- Organizers are not responsible for any unsolicited or unauthorized use

5. Disqualification
- Plagiarized or previously published work
- Entries that violate copyright or intellectual property rights
- Inappropriate or offensive content
- Submissions exceeding the deadline
- Multiple entries from same participant exceeding the limit

6. Judging & Results
- Winners will be selected by a panel of professional judges
- Judging criteria: Composition, Creativity, Technical Quality, and Theme Relevance
- Results will be announced on the specified date
- Winners will be notified via email

7. Prize Claim
- Winners must claim their prizes within 30 days of announcement
- Prize transfer or cash equivalents are not available
- Prizes are non-transferable and non-refundable`,
      defaultTerms: `1. Agreement to Terms
By participating in this competition, you agree to comply with all rules, regulations, and terms outlined herein.

2. Participant Responsibilities
- You are responsible for obtaining all necessary permissions and rights to submit your work
- You confirm that your submission is original and does not infringe on any third-party rights
- You agree to use the Photographer SB platform in good faith and not engage in fraudulent activities

3. Intellectual Property Rights
- Participants retain all copyright and intellectual property rights to their submissions
- By submitting to the competition, you grant Photographer SB a non-exclusive license to use, display, and reproduce your work for competition purposes
- You consent to the use of your name and work in connection with competition announcements and promotional materials

4. Limitation of Liability
- Photographer SB is not responsible for lost, damaged, or corrupted submissions
- We are not liable for any technical issues, server errors, or connectivity problems
- Participants submit at their own risk

5. Privacy & Data Protection
- Your personal information will be used only for competition purposes
- We comply with all applicable data protection regulations
- Your data will not be shared with third parties without consent

6. Disqualification & Removal
- Photographer SB reserves the right to disqualify any participant or submission that violates these terms
- We may remove offensive or inappropriate content at our discretion
- Disqualified participants forfeit any prizes and may be banned from future competitions

7. Modification of Terms
- Photographer SB reserves the right to modify these terms and conditions at any time
- Continued participation implies acceptance of modified terms

8. Dispute Resolution
- Any disputes arising from this competition will be governed by local laws
- Participants agree to attempt resolution through our support team first
- In case of unresolved disputes, mediation or arbitration may be required

9. Entire Agreement
- These terms and conditions constitute the entire agreement between you and Photographer SB
- If any provision is found invalid, remaining provisions remain in effect

10. Contact Information
For questions or concerns regarding the competition, please contact our support team at support@photographersb.com`
    }
  },

  computed: {
    filteredSponsors() {
      if (!this.sponsorSearch) return this.sponsors;
      return this.sponsors.filter(s =>
        s.name.toLowerCase().includes(this.sponsorSearch.toLowerCase())
      );
    },
    filteredJudges() {
      if (!this.judgeSearch) return this.judges;
      return this.judges.filter(j =>
        j.name.toLowerCase().includes(this.judgeSearch.toLowerCase())
      );
    },
    cashPrizeTotal() {
      return this.form.prizes
        .filter(prize => prize.type === 'Cash')
        .reduce((sum, prize) => sum + (Number(prize.amount) || 0), 0);
    }
  },

  mounted() {
    this.loadCategories();
    this.loadSponsors();
    this.loadJudges();
  },

  methods: {
    async loadCategories() {
      try {
        const res = await fetch('/api/v1/categories');
        const data = await res.json();
        if (data.data) {
          this.categories = data.data;
        }
      } catch (error) {
        console.error('Error loading categories:', error);
      }
    },

    async loadSponsors() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch('/api/v1/admin/platform-sponsors', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        const data = await res.json();
        if (data.data) {
          this.sponsors = data.data.filter(s => s.status === 'active');
        }
      } catch (error) {
        console.error('Error loading sponsors:', error);
      }
    },

    async loadJudges() {
      try {
        const token = localStorage.getItem('auth_token');
        const res = await fetch('/api/v1/judges', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });
        const data = await res.json();
        if (data.data) {
          this.judges = data.data;
        }
      } catch (error) {
        console.error('Error loading judges:', error);
      }
    },

    async submitForm() {
      this.loading = true;
      this.errors = {};

      try {
        const token = localStorage.getItem('auth_token');

        const invalidCashPrize = this.form.prizes.some(
          prize => prize.type === 'Cash' && Number(prize.amount) <= 1
        );
        const invalidNonCashPrize = this.form.prizes.some(
          prize => prize.type !== 'Cash' && !(prize.description || '').trim()
        );

        if (invalidCashPrize) {
          this.errors.prizes = 'Cash prize amount must be greater than 1.';
          throw new Error(this.errors.prizes);
        }

        if (invalidNonCashPrize) {
          this.errors.prizes = 'Non-cash prizes require a description.';
          throw new Error(this.errors.prizes);
        }

        if (Number(this.form.total_prize_pool) !== this.cashPrizeTotal) {
          this.errors.total_prize_pool = 'Total Prize Pool must equal the sum of cash prizes.';
          throw new Error(this.errors.total_prize_pool);
        }

        const payload = {
          title: this.form.title,
          slug: this.form.slug || null,
          theme: this.form.theme,
          description: this.form.description,
          category_id: this.form.category_id || null,
          submission_deadline: this.form.submission_deadline + ':00',
          voting_start_at: this.form.voting_start_at + ':00',
          voting_end_at: this.form.voting_end_at + ':00',
          judging_start_at: this.form.judging_start_at ? this.form.judging_start_at + ':00' : null,
          judging_end_at: this.form.judging_end_at ? this.form.judging_end_at + ':00' : null,
          results_announcement_date: this.form.results_announcement_date + ':00',
          hero_image: this.form.hero_image || null,
          banner_image: this.form.banner_image || null,
          total_prize_pool: this.form.total_prize_pool,
          number_of_winners: this.form.number_of_winners,
          participation_fee: this.form.is_paid_competition ? this.form.participation_fee : 0,
          is_paid_competition: this.form.is_paid_competition,
          max_submissions_per_user: this.form.max_submissions_per_user,
          prizes: this.form.prizes.map(prize => {
            const prizeData = {
              position: prize.rank,
              amount: prize.amount || 0
            };
            if (prize.description && prize.description.trim()) {
              prizeData.description = prize.description;
            }
            return prizeData;
          }),
          rules: this.form.rules,
          terms_and_conditions: this.form.terms_and_conditions,
          status: this.form.status,
          is_featured: this.form.is_featured ? 1 : 0,
          is_public: this.form.is_public,
          allow_public_voting: this.form.allow_public_voting,
          allow_judge_scoring: this.form.allow_judge_scoring,
          allow_watermark: this.form.allow_watermark,
          require_watermark: this.form.require_watermark,
          sponsor_ids: this.form.sponsor_ids,
          judge_ids: this.form.judge_ids
        };

        const res = await fetch('/api/v1/admin/competitions', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (!res.ok) {
          // Handle authorization errors specifically
          if (res.status === 401) {
            this.errors.auth = 'Your session has expired. Please log in again.';
            setTimeout(() => this.$router.push('/login'), 2000);
          } else if (res.status === 403) {
            this.errors.auth = `Access denied. Your role (${data.user_role}) does not have permission to create competitions. Admin/Super Admin access required.`;
          } else {
            this.errors = data.errors || {};
          }
          throw new Error(data.message || 'Failed to create competition');
        }

        // Success
        this.$router.push('/admin/competitions');
      } catch (error) {
        console.error('Error creating competition:', error);
        alert(error.message || 'Error creating competition');
      } finally {
        this.loading = false;
      }
    },

    addPrize() {
      this.form.prizes.push({
        rank: '1st',
        type: 'Cash',
        amount: 2,
        description: ''
      });
    },

    removePrize(index) {
      this.form.prizes.splice(index, 1);
    },

    formatPrizeLabel(prize) {
      const rank = prize.rank || 'Prize';
      const type = prize.type || 'Reward';
      return `${rank} Place - ${type}`;
    },

    formatCurrency(value) {
      const num = parseFloat(value) || 0;
      return num.toLocaleString('bn-BD', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
    }
  }
}
</script>

<style scoped>
</style>
